<?php

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Settings\Settings;

class ServiceTest extends CIUnitTestCase
{
	public function testServiceReturnsLibrary()
	{
		$result = service('settings');

		$this->assertInstanceOf(Settings::class, $result);
	}
}
