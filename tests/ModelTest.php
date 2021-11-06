<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Tatter\Settings\Models\SettingModel;
use Tests\Support\SettingsTestCase;

/**
 * @internal
 */
final class ModelTest extends SettingsTestCase
{
    public function testGetTemplatesDoesNotCastDatabaseValues()
    {
        // Insert using the model (e.g., admin dashboard)
        $this->model->insert([
            'name'      => 'fruits',
            'datatype'  => 'json-array',
            'summary'   => 'Yummy fruits',
            'content'   => '{"a":"Bananas","b":"Oranges"}',
            'protected' => 1,
        ]);

        // Load templates from the database into the model
        $this->model->getTemplates();

        // The attibute of the entity should be encoded
        $setting   = $this->getPrivateProperty($this->model, 'templates')['fruits'];
        $attribute = $this->getPrivateProperty($setting, 'attributes')['content'];

        $this->assertSame('{"a":"Bananas","b":"Oranges"}', $attribute);

        // The value returned from the getter should be decoded
        $array = [
            'a' => 'Bananas',
            'b' => 'Oranges',
        ];

        $this->assertSame($array, config('Settings')->fruits);
    }
}
