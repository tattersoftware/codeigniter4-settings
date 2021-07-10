<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Settings\Database\Seeds\SettingSeeder;
use Tatter\Settings\Models\SettingModel;

/**
 * @internal
 */
final class SettingsTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * @var bool
     */
    protected $refresh = true;

    /**
     * @var array|string|null
     */
    protected $namespace;

    /**
     * @var array|string
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
