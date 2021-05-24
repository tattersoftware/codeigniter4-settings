<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Settings\Database\Seeds\SettingSeeder;

class SettingsTestCase extends CIUnitTestCase
{
	use DatabaseTestTrait;

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
	protected $seed = SettingSeeder::class;
}
