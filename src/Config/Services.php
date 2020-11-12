<?php namespace Tatter\Settings\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\ConnectionInterface;
use Tatter\Settings\Config\Settings as SettingsConfig;
use Tatter\Settings\Settings;

class Services extends BaseService
{
	/**
	 * @param SettingsConfig|null $config
	 * @param bool $getShared
	 */
	public static function settings(SettingsConfig $config = null, bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('settings', $config);
		}

		$config = $config ?? config('Settings');

		return new Settings($config);
	}
}
