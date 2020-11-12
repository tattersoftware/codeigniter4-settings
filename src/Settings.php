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
* 	>= PHP 7.2
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

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Session\SessionInterface;
use Config\Services;
use Tatter\Settings\Config\Settings as SettingsConfig;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Exceptions\SettingsException;

/*** CLASS ***/
class Settings
{
	/**
	 * Our configuration instance.
	 *
	 * @var SettingsConfig
	 */
	protected $config;
	
	/**
	 * The model for fetching templates.
	 *
	 * @var SettingModel
	 */
	protected $model;
	
	/**
	 * The Session Handler
	 *
	 * @var SessionInterface
	 */
	protected $session;

	/**
	 * Builder for the `settings_users` table, derived from $model
	 *
	 * @var BaseBuilder
	 */
	private $builder;
		
	/**
	 * Stores dependencies
	 *
	 * @param SettingsConfig $config
	 * @param SettingModel $model
	 * @param SessionInterface $session
	 */
	public function __construct(SettingsConfig $config, SettingModel $model, SessionInterface $session)
	{
		$this->config  = $config;
		$this->model   = $model;
		$this->session = $session;

		$this->builder = $this->model->builder('settings_users');
	}
	
	/**
	 * Checks for a logged in user
	 *
	 * @return int The user ID, 0 for "not logged in", -1 for CLI
	 */
	protected function sessionUserId(): int
	{
		if (is_cli())
		{
			return -1;
		}
		return $this->session->get($this->config->sessionUserId) ?? 0;
	}
	
	/**
	 * Fetches the setting template from the settings table and handles errors
	 *
	 * @param string $name
	 *
	 * @return object|null
	 *
	 * @throws SettingsException
	 */
	public function getTemplate(string $name): ?object
	{
		if (empty($name))
		{
			if ($this->config->silent)
			{
				return null;
			}
			else
			{
				throw SettingsException::forMissingName();
			}
		}
		
		// Check the cache
		if ($setting = cache("settings:templates:{$name}"))
		{
			return $setting;
		}
		
		// Query the database
		$setting = $this->model->where('name', $name)->first();
		if (empty($setting))
		{
			if ($this->config->silent)
			{
				return null;
			}
			else
			{
				throw SettingsException::forUnmatchedName($name);
			}
		}
		
		$this->cache("settings:templates:{$name}", $setting);
		return $setting;
	}
	
	/**
	 * Tries to cache a Setting
	 *
	 * @param string $key
	 * @param mixed $content
	 *
	 * @return mixed
	 */
	protected function cache($key, $content)
	{
		if ($content === null)
		{
			return cache()->delete($key);
		}

		if ($duration = $this->config->cacheDuration)
		{
			cache()->save($key, $content, $duration);
		}

		return $content;
	}

	/**
	 * Magic getter for a setting
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get(string $name)
	{
		return $this->get($name);
	}
	
	/**
	 * Gets a setting - checks session, then user, then global
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function get(string $name)
	{
		$setting = $this->getTemplate($name);
		if (empty($setting))
		{
			return null;
		}

		// check for a cached version
		$userId = $this->sessionUserId();
		$ident  = $userId ?: md5(session_id());
		$cacheKey = "settings:contents:{$setting->name}:{$ident}";
		$content = cache($cacheKey);
		if ($content !== null)
		{
			return $content;
		}
		
		// global settings cannot be overridden
		if ($setting->scope=="global")
		{
			return $this->cache($cacheKey, $setting->content);
		}
		
		// check if there's a setting for this session
		$content = $this->getSession($setting);
		if ($content!==null)
		{
			return $this->cache($cacheKey, $content);
		}

		// check if there's a user-defined setting
		$content = $this->getUser($setting, $userId);
		if ($content!==null)
		{
			return $this->cache($cacheKey, $content);
		}

		// fall back to template setting
		return $this->cache($cacheKey, $setting->content);
	}
    
	/**
	 * Checks if there is a $_SESSION entry
	 *
	 * @param object $setting
	 *
	 * @return mixed|null
	 */
	protected function getSession($setting)
	{
		// prefix to avoid collision
		return $this->session->get('settings:contents:' . $setting->name) ?? null;
	}
	
	/**
	 * Checks the database for a user-defined setting
	 *
	 * @param object $setting
	 * @param int|null $userId
	 *
	 * @return mixed|null
	 */
	protected function getUser($setting, int $userId = null)
	{
		// if no user is provided try to get the current user ID
		if (! is_numeric($userId))
		{
			$userId = $this->sessionUserId();
		}
			
		// look for a user-defined setting
		$result = $this->builder
			->where('setting_id', $setting->id)
			->where('user_id', $userId)
			->limit(1)->get()->getResult();

		if (empty($result))
		{
			return null;
		}
		return reset($result)->content;
	}

	/**
	 * Magic setter for changing a setting
	 *
	 * @param string $name
	 * @param mixed|null $content
	 *
	 * @return bool
	 */
	public function __set(string $name, $content): bool
	{
		return $this->set($name, $content);
	}

	/**
	 * Changes or removes a setting
	 *
	 * @param string $name
	 * @param mixed|null $content Null to remove
	 *
	 * @return bool
	 */
	public function set(string $name, $content): bool
	{
		$setting = $this->getTemplate($name);
		if (empty($setting))
		{
			return false;
		}
		
		$userId = $this->sessionUserId();
		$ident  = $userId ?: md5(session_id());
		$cacheKey = "settings:contents:{$setting->name}:{$ident}";
		
		switch ($setting->scope)
		{
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
		}
	
		return true;
	}

	/**
	 * Changes a global setting template (updates content in settings table)
	 *
	 * @param object $setting
	 * @param mixed|null $content
	 *
	 * @return bool|null
	 */
	protected function setGlobal($setting, $content = null): ?bool
	{
		// don't alter protected templates
		if ($setting->protected)
		{
			if ($this->config->silent)
			{
				return null;
			}
			else
			{
				throw SettingsException::forProtectionViolation($setting->name);
			}
		}
		
		// check for a removal request
		if ($content === null)
		{
			$this->model->delete($setting->id);
			cache()->delete("settings:templates:{$setting->name}");
			return true;
		}
		
		// update the setting template
		$setting->content = $content;
		$this->model->save($setting);

		return true;
	}
	
	/**
	 * Changes a session setting
	 *
	 * @param object $setting
	 * @param mixed|null $content
	 *
	 * @return bool
	 */
	protected function setSession($setting, $content = null): bool
	{
		if ($content === null)
		{
			$this->session->remove('settings:contents:' . $setting->name);
		}
		else
		{
			$this->session->set('settings:contents:' . $setting->name, $content);
		}
		return true;
	}

	/**
	 * Changes a user setting
	 *
	 * @param object $setting
	 * @param mixed|null $content
	 * @param int|null $userId
	 *
	 * @return bool
	 */
	protected function setUser($setting, $content = null, int $userId = null): bool
	{
		// if no user is provided try to get the current user ID
		if (! is_numeric($userId))
		{
			$userId = $this->sessionUserId();
		}
			
		// remove any existing setting
		$this->builder
			->where('user_id', $userId)
			->where('setting_id', $setting->id)
			->delete();
			
		// if this was a removal request, we're done
		if ($content === null)
		{
			return true;
		}
			
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
