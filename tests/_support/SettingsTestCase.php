<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Settings\Database\Seeds\SettingSeeder;
use Tatter\Settings\Models\SettingModel;

abstract class SettingsTestCase extends CIUnitTestCase
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
	 * @var SettingModel
	 */
	protected $model;

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

		/** @var SettingModel $model */
		$model = model(SettingModel::class);
		$model->clearTemplates();

    	$this->model = $model;
    }
}
