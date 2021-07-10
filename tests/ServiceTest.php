<?php

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
