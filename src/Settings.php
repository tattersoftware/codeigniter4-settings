<?php namespace Tatter\Settings;

/***
* Name: Settings
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-04-07
*
* Description:  Lightweight settings management for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.1
* 	>= CodeIgniter 4.0
*	Preconfigured, autoloaded Database
*	`settings` and `settings_users` tables (run migrations)
*
* Configuration:
* 	Use Config/Settings.php to override default behavior
* 	Run migrations to update database tables:
* 		> php spark migrate:latest -all
*
* Description:
* Settings exist at three tiered levels: Session, User, Global
* 	Global settings cannot be overriden by users
* 	User settings automatically write back to the database to persist between sessions
* 	Session settings only last as long as a session
* Gets return NULL for missing/unmatched settings
* Sets use NULL to remove values

* @package CodeIgniter4-Settings
* @author Matthew Gatner
* @link https://github.com/tattersoftware/codeigniter4-settings
*
***/

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Exceptions\SettingsException;

/*** CLASS ***/
class Settings
{
	/**
	 * Our configuration instance.
	 *
	 * @var \Tatter\Settings\Config\Settings
	 */
	protected $config;

	/**
	 * Database connection for the `settings_users` table
	 *
	 * @var ConnectionInterface
	 */
	protected $builder;

	/**
	 * The active user session.
	 *
	 * @var \CodeIgniter\Session\Session
	 */
	protected $session;
	
	/**
	 * The setting model used to fetch Settings templates.
	 *
	 * @var \Tatter\Settings\Models\SettingModel
	 */
	protected $model;
		
	// initiate library, check for existing session
	public function __construct(BaseConfig $config, ConnectionInterface $db = null)
	{		
		// save configuration
		$this->config = $config;

		// initiate the Session library
		$this->session = Services::session();
		
		// If no db connection passed in, use the default database group.
		$db = db_connect($db);
		$this->builder = $db->table('settings_users');
		
		// initiate the model
		$this->model = new SettingModel();
	}
	
	// checks for a logged in user based on config
	// returns user ID, 0 for "not logged in", -1 for CLI
	protected function sessionUserId(): int
	{
		if (is_cli())
			return -1;
		return $this->session->get($this->config->sessionUserId) ?? 0;
	}
	
	// fetches the setting template from the settings table and handles errors
	public function getTemplate(string $name)
	{
		if (empty($name)):
			if ($this->config->silent):
				return null;
			else:
				throw SettingsException::forMissingName();
			endif;
		endif;
		
		// check cache
		if ($setting = cache("settings:templates:{$name}"))
			return $setting;
		
		// fetch from the database
		$setting = $this->model->where('name', $name)->first();
		if (empty($setting)):
			if ($this->config->silent):
				return null;
			else:
				throw SettingsException::forUnmatchedName($name);
			endif;
		endif;
		
		cache("settings:templates:{$name}", $setting);
		return $setting;
	}
	
	// try to cache a setting and pass it back
	protected function cache($key, $content)
	{
		if ($content === null)
			return cache()->delete($key);

		if ($duration = $this->config->cacheDuration)
			cache()->save($key, $content, $duration);
		return $content;
	}

	// magic wrapper for getting a setting
    public function __get(string $name)
    {
		return $this->get($name);
    }
	
	// get a setting - checks session, then user, then global
	public function get(string $name)
	{
		$setting = $this->getTemplate($name);
		if (empty($setting))
			return null;

		// check for a cached version
		$userId = $this->sessionUserId();
		$ident  = $userId ?: md5(session_id());
		$cacheKey = "settings:contents:{$setting->name}:{$ident}";
		$content = cache($cacheKey);
		if ($content !== null)
			return $content;
		
		// global settings cannot be overridden
		if ($setting->scope=="global")
			return $this->cache($cacheKey, $setting->content);
		
		// check if there's a setting for this session
		$content = $this->getSession($setting);
		if ($content!==null)
			return $this->cache($cacheKey, $content);

		// check if there's a user-defined setting
		$content = $this->getUser($setting, $userId);
		if ($content!==null)
			return $this->cache($cacheKey, $content);

		// fall back to template setting
		return $this->cache($cacheKey, $setting->content);
    }
    
	// check if there is a $_SESSION entry
	protected function getSession($setting)
	{
		// prefix to avoid collision
		return $this->session->get('settings:contents:' . $setting->name) ?? null;
	}
	
	// checks the database for a user-defined setting
	protected function getUser($setting, int $userId = null)
	{
		// if no user is provided try to get the current user ID
		if (! is_numeric($userId))
			$userId = $this->sessionUserId();
			
		// look for a user-defined setting
		$result = $this->builder
			->where('setting_id', $setting->id)
			->where('user_id', $userId)
			->limit(1)->get()->getResult();

		if (empty($result))
			return null;
		return reset($result)->content;
	}

	// magic wrapper for changing a setting
    public function __set(string $name, $content): bool
    {
		return $this->set($name, $content);
    }

	// change a setting, null removes
    public function set(string $name, $content): bool
    {
		$setting = $this->getTemplate($name);
		if (empty($setting))
			return false;
		
		$userId = $this->sessionUserId();
		$ident  = $userId ?: md5(session_id());
		$cacheKey = "settings:contents:{$setting->name}:{$ident}";
		
		switch ($setting->scope):
			// global scope changes the template in the settings table
			case "global":
				$this->setGlobal($setting, $content);
				$this->cache($cacheKey, $content);
			break;
		
			// user scope changes the session and writes back to the database
			case "user":
				$this->setSession($setting, $content);
				$this->setUser($setting, $content);
				$this->cache($cacheKey, $content);
			break;
		
			case "session":
				$this->setSession($setting,$content);
				$this->cache($cacheKey, $content);
			break;
		
			// something borked
			default:
				return false;
		endswitch;
	
		return true;
    }

	// change a global setting template (updates content in settings table)
	protected function setGlobal($setting, $content = null): ?bool
	{
		// don't alter protected templates
		if ($setting->protected):
			if ($this->config->silent):
				return null;
			else:
				throw SettingsException::forProtectionViolation($setting->name);
			endif;
		endif;
		
		// check for a removal request
		if ($content === null):
			$this->model->delete($setting->id);
			cache()->delete("settings:templates:{$setting->name}");
			return true;
		endif;
		
		// update the setting template
		$setting->content = $content;
		$this->model->save($setting);

		return true;
	}
	
	// change a session setting
	protected function setSession($setting, $content = null): bool
	{
		if ($content === null)
			$this->session->remove('settings:contents:' . $setting->name);
		else
			$this->session->set('settings:contents:' . $setting->name, $content);
		return true;
	}

	// change a user setting
	protected function setUser($setting, $content = null, int $userId = null): bool
	{
		// if no user is provided try to get the current user ID
		if (! is_numeric($userId))
			$userId = $this->sessionUserId();
			
		// remove any existing setting
		$this->builder
			->where('user_id', $userId)
			->where('setting_id', $setting->id)
			->delete();
			
		// if this was a removal request, we're done
		if ($content === null)
			return true;
			
		// build the row
		$row = [
			'setting_id'  => $setting->id,
			'user_id'     => $userId,
			'content'     => $content,
			'created_at'  => date('Y-m-d H:i:s'),
		];
		$this->builder->insert($row);
		
		return true;
	}
}
