<?php namespace Tatter\Settings\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Session\SessionInterface;
use Tatter\Settings\Config\Settings as SettingsConfig;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Settings;

class Services extends BaseService
{
	/**
	 * @param SettingsConfig|null $config
	 * @param SettingModel|null $model
	 * @param SessionInterface|null $session
	 * @param bool $getShared
	 */
	public static function settings(SettingsConfig $config = null, SettingModel $model = null, SessionInterface $session = null, bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('settings', $model, $session, $config);
		}

		return new Settings(
			$config ?? config('Settings'),
			$model ?? model(SettingModel::class),
			$session ?? service('session')
		);
	}
}
