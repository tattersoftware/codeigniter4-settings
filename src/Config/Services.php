<?php

namespace Tatter\Settings\Config;

use Config\Services as BaseService;
use Tatter\Settings\Settings;

class Services extends BaseService
{
	/**
	 * @param bool $getShared
	 */
	public static function settings(bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('settings');
		}

		return new Settings();
	}
}
