<?php

use CodeIgniter\Test\CIDatabaseTestCase;
use Tatter\Settings\Settings;

class ServiceTest extends CIDatabaseTestCase
{
	public function testServiceReturnsLibrary()
	{
		$result = service('settings');

		$this->assertInstanceOf(Settings::class, $result);
	}
}
