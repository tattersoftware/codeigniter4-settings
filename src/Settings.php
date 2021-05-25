<?php namespace Tatter\Settings;

use CodeIgniter\Cache\Handlers\BaseHandler;
use Tatter\Settings\Config\Settings as SettingsConfig;
use Tatter\Settings\Entities\Setting;
use Tatter\Settings\Models\SettingModel;
use InvalidArgumentException;

/**
 * Settings Library
 */
class Settings
{
	/**
	 * Validates a Setting name.
	 * Since this library relies on Cache
	 * and it is fairly restrictive we use
	 * its validation.
	 *
	 * @param string $name
	 *
	 * @return string The validated name
	 *
	 * @throws InvalidArgumentException
	 */
	private static function validate(string $name): string
	{
		BaseHandler::validateKey($name);

		return $name;
	}

 	//--------------------------------------------------------------------

	/**
	 * Gets a Setting template, if it exists.
	 *
	 * @param string $name
	 *
	 * @return Setting|null
	 */
	public function getTemplate(string $name): ?Setting
	{
		self::validate($name);

		$templates = model(SettingModel::class)->getTemplates();

		return $templates[$name] ?? null;
	}

	/**
	 * Stores Setting content in session and returns the value.
	 * Always called when retrieving a value to improve chances
	 * at a hit next time.
	 *
	 * @param string $name
	 * @param mixed $content
	 *
	 * @return mixed
	 */
	protected function setSession(string $name, $content)
	{
		session()->set('settings-' . $name, $content);

		return $content;
	}

 	//--------------------------------------------------------------------

	/**
	 * Magic getter
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
	 * Retrieves Setting content by its name.
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function get(string $name)
	{
		self::validate($name);

		// Check session first
		if (session()->has('settings-' . $name))
		{
			return session('settings-' . $name);
		}

		// Bail if there is not a template
		if (! $setting = $this->getTemplate($name))
		{
			return null;
		}

		// If this is a global or there is no user then use the Setting default
		if ($setting->protected || ! function_exists('user_id') || null === $userId = user_id())
		{
			return $this->setSession($name, $setting->content);
		}

		// Check if this user has overrides
		if ($overrides = model(SettingModel::class)->getOverrides($userId))
		{
			// Match the Setting to its potential override
			if (array_key_exists($setting->id, $overrides))
			{
				$setting->content = $overrides[$setting->id];
			}
		}

		return $this->setSession($name, $setting->content);
	}

 	//--------------------------------------------------------------------

	/**
	 * Magic setter for changing a setting
	 *
	 * @param string $name
	 * @param mixed|null $content
	 *
	 * @return void
	 */
	public function __set(string $name, $content): void
	{
		$this->set($name, $content);
	}

	/**
	 * Updates a Setting value
	 *
	 * @param string $name
	 * @param mixed $content Null to remove
	 *
	 * @return $this
	 */
	public function set(string $name, $content): self
	{
		self::validate($name);

		// If there is not a template or there is no user then set session and quit
		if (! ($setting = $this->getTemplate($name)) || ! function_exists('user_id') || null === $userId = user_id())
		{
			$this->setSession($name, $content);

			return $this;
		}

		// Globals may not be overriden - use the Setting default value
		if ($setting->protected)
		{
			$this->setSession($name, $setting->content);

			return $this;
		}

		// Update the database and set the session
		model(SettingModel::class)->setOverride($setting->id, $userId, $content);
		$this->setSession($name, $content);

		return $this;
	}
}
