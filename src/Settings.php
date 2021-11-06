<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Settings;

use CodeIgniter\Cache\Handlers\BaseHandler;
use InvalidArgumentException;
use Tatter\Settings\Entities\Setting;
use Tatter\Settings\Models\SettingModel;

/**
 * Settings Library.
 */
class Settings
{
	/**
	 * @var SettingModel
	 */
	protected $model;

    public function __construct(?SettingModel $model = null)
    {
    	$this->model = $model ?? model(SettingModel::class); // @phpstan-ignore-line
    }

    /**
     * Magic getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    //--------------------------------------------------------------------

    /**
     * Magic setter for changing a setting.
     *
     * @param string     $name
     * @param mixed|null $content
     *
     * @return void
     */
    public function __set(string $name, $content): void
    {
        $this->set($name, $content);
    }

    /**
     * Validates a Setting name.
     * Since this library relies on Cache
     * and it is fairly restrictive we use
     * its validation.
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return string The validated name
     */
    public static function validate(string $name): string
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

        $templates = $this->model->getTemplates();

        return $templates[$name] ?? null;
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
        if (session()->has('settings-' . $name)) {
            return session('settings-' . $name);
        }

        // Bail if there is not a template
        if (! $setting = $this->getTemplate($name)) {
            return null;
        }

        // If this is a global or there is no user then use the Setting default
        if ($setting->protected || ! function_exists('user_id') || null === $userId = user_id()) {
            return $this->setSession($name, $setting->content);
        }

        // Check if this user has overrides
        if ($overrides = $this->model->getOverrides($userId)) {
            // Match the Setting to its potential override
            if (array_key_exists($setting->id, $overrides)) {
                $setting->content = $overrides[$setting->id];
            }
        }

        return $this->setSession($name, $setting->content);
    }

    /**
     * Updates a Setting value.
     *
     * @param string $name
     * @param mixed  $content Null to remove
     *
     * @return $this
     */
    public function set(string $name, $content): self
    {
        self::validate($name);

        // If there is not a template then set session and quit
        if (! ($setting = $this->getTemplate($name))) {
            $this->setSession($name, $content);

            return $this;
        }

        // Globals may not be overriden - use the Setting default value
        if ($setting->protected) {
            $this->setSession($name, $setting->content);

            return $this;
        }

        // If there is a user then create an override
        if (function_exists('user_id') && null !== $userId = user_id()) {
            $this->model->setOverride($setting->id, $userId, $content);
        }

        // Update the session
        $this->setSession($name, $content);

        return $this;
    }

    /**
     * Stores Setting content in session and returns the value.
     * Always called when retrieving a value to improve chances
     * at a hit next time.
     *
     * @param string $name
     * @param mixed  $content
     *
     * @return mixed
     */
    protected function setSession(string $name, $content)
    {
        session()->set('settings-' . $name, $content);

        return $content;
    }
}
