<?php namespace Tatter\Settings\Config;

use CodeIgniter\Config\BaseConfig;

class Settings extends BaseConfig
{
	/**
	 * Number of seconds to cache Settings.
	 *  - Use 0 for indefinite
	 *  - Use null to disable caching (not recommended)
	 *
	 * @var int|null
	 */
	public $ttl = 5 * MINUTE;
}
