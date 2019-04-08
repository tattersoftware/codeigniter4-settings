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

		// prioritizes user config in app/Config if found
		if (empty($config)):
			if (class_exists('\Config\Settings')):
				$config = new \Config\Settings();
			else:
				$config = new \Tatter\Settings\Config\Settings();
			endif;
		endif;

		return new \Tatter\Settings\Settings($config);
	}
}
