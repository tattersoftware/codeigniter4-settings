<?php namespace Tatter\Settings\Config;

use CodeIgniter\Config\BaseConfig;

class Settings extends BaseConfig
{
	// key in $_SESSION that contains the integer ID of a logged in user
	public $sessionUserId = "logged_in";
	
	// number of seconds to cache a setting
	// 0 disables caching (not recommended except for testing)
	public $cacheDuration = 300;
	
	// whether to continue instead of throwing exceptions
	public $silent = false;
}
