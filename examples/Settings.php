<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Settings.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
***/

use CodeIgniter\Config\BaseConfig;
use Tatter\Settings\Config\Settings as BaseSettings;

class Settings extends BaseSettings;
{
	/**
	 * A key in $_SESSION that contains
	 * the integer ID of a logged in user.
	 *
	 * @var string
	 *
	 * @deprecated The next version will use user_id()
	 * @see https://codeigniter4.github.io/CodeIgniter4/extending/authentication.html
	 */
	public $sessionUserId = "logged_in";
	
	/**
	 * Number of seconds to cache Settings.
	 * 0 disables caching (not recommended except for testing)
	 *
	 * @var int
	 *
	 * @deprecated The next version will use $ttl instead
	 */
	public $cacheDuration = 300;
	
	/**
	 * Whether to continue instead of throwing exceptions
	 *
	 * @var bool
	 *
	 * @deprecated The next version will always throw exceptions
	 */
	public $silent = false;
}
