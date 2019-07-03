<?php namespace Tatter\Settings\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\ConnectionInterface;

class Services extends BaseService
{
    public static function settings(BaseConfig $config = null, bool $getShared = true)
    {
		if ($getShared):
			return static::getSharedInstance('settings', $config);
		endif;

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
			$config = config('Settings');

		return new \Tatter\Settings\Settings($config);
	}
}
