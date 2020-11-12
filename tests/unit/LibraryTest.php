<?php

use Tatter\Settings\Config\Settings as SettingsConfig;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Settings;
use Tests\Support\SettingsTestCase;

class LibraryTest extends SettingsTestCase
{
	/**
	 * @var Settings
	 */
	protected $settings;

	protected function setUp(): void
	{
		parent::setUp();

		$config = new SettingsConfig();
		$config->silent = false;

		$this->settings = new Settings(
			$config,
			model(SettingModel::class),
			service('session')
		);
	}

	public function testGetReturnsDefaultValue()
	{
		$result = $this->settings->get('currencyScale');

		$this->assertEquals(100, $result);
	}
}
