<?php

use Tatter\Settings\Entities\Setting;
use Tatter\Settings\Models\SettingModel;
use Tests\Support\SettingsTestCase;

final class ModelTest extends SettingsTestCase
{
	public function testGetTemplatesDoesNotCastDatabaseValues()
	{
		// Insert using the model (e.g., admin dashboard)
		model(SettingModel::class)->insert([
			'name'      => 'fruits',
			'datatype'  => 'json-array',
			'summary'   => 'Yummy fruits',
			'content'   => '{"a":"Bananas","b":"Oranges"}',
			'protected' => 1,
		]);

		$array = [
			'a' => 'Bananas',
			'b' => 'Oranges',
		];

		$model = model(SettingModel::class);
		$model->getTemplates();
		$setting = $this->getPrivateProperty($model, 'templates')['fruits'];

		$check = $this->getPrivateProperty($setting, 'attributes')['content'];
		$this->assertEquals('{"a":"Bananas","b":"Oranges"}', $check);

		$this->assertSame($array, config('Settings')->fruits);
	}

}
