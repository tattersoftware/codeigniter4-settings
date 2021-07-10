<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Settings\Settings;

/**
 * @internal
 */
final class ServiceTest extends CIUnitTestCase
{
    public function testServiceReturnsLibrary()
    {
        $result = service('settings');

        $this->assertInstanceOf(Settings::class, $result);
    }
}
