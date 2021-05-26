<?php

use Tatter\Settings\Entities\Setting;
use Tatter\Settings\Models\SettingModel;
use Tests\Support\SettingsTestCase;

final class EntityTest extends SettingsTestCase
{
	public function testContentCastDefaultsToString()
	{
		$setting = new Setting(['content' => 42]);

		$this->assertSame('42', $setting->content);
	}

	public function testContentCastsToDatatype()
	{
		$setting = new Setting([
			'datatype' => 'int',
			'content'  => '12.34',
		]);

		$this->assertSame(12, $setting->content);
	}

	public function testFaked()
	{
		$setting = fake(SettingModel::class, null, false);

		$this->assertInstanceOf(Setting::class, $setting);
	}
}
