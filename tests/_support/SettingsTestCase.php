<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Settings\Database\Seeds\SettingSeeder;
use Tatter\Settings\Models\SettingModel;

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

	/**
	 * Initializes required helpers.
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		helper(['auth']);
	}

	protected function setUp(): void
	{
		parent::setUp();

		model(SettingModel::class)->clearTemplates();
	}
}
