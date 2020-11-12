<?php namespace Tests\Support;

use CodeIgniter\Test\CIDatabaseTestCase;

class SettingsTestCase extends CIDatabaseTestCase
{
	/**
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * @var string|array|null
	 */
    protected $namespace = null;

	/**
	 * @var string|array
	 */
	protected $seed = 'Tatter\Settings\Database\Seeds\SettingSeeder';
}
