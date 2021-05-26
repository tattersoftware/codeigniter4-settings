<?php

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tatter\Settings\Models\SettingModel;
use Tests\Support\SettingsTestCase;

/**
 * @see https://github.com/codeigniter4/CodeIgniter4/blob/develop/tests/system/Commands/HelpCommandTest.php
 */
final class CommandsTest extends SettingsTestCase
{
	use DatabaseTestTrait;

	/**
	 * @var resource
	 */
	private $streamFilter;

	protected function setUp(): void
	{
		parent::setUp();

		CITestStreamFilter::$buffer = '';

		$this->streamFilter = stream_filter_append(STDOUT, 'CITestStreamFilter');
		$this->streamFilter = stream_filter_append(STDERR, 'CITestStreamFilter');
	}

	protected function tearDown(): void
	{
		stream_filter_remove($this->streamFilter);
	}

	private function getBuffer()
	{
		return CITestStreamFilter::$buffer;
	}

	//--------------------------------------------------------------------

	public function testListCommand()
	{
		command('settings:list');

		$this->assertStringContainsString('Your organization phone', $this->getBuffer());
	}

	public function testListCommandEmpty()
	{
		model(SettingModel::class)->builder()->truncate();

		command('settings:list');

		$this->assertStringContainsString('No settings templates', $this->getBuffer());
	}

	public function testAddCommand()
	{
		command('settings:add fruits string "Favorite fruits" bananas 1');

		$this->assertSame('bananas', service('settings')->fruits);
		$this->assertStringContainsString('fruits', $this->getBuffer());
	}

	public function testAddCommandInvalidName()
	{
		command('settings:add illegal:character string "Favorite fruits" bananas 1');

		$this->assertStringContainsString('key contains reserved characters', $this->getBuffer());
	}

	public function testAddCommandInvalidDatatype()
	{
		command('settings:add fruits ThisDatatypeIsFarTooLongToBeAllowed "Favorite fruits" bananas 1');

		$this->assertStringContainsString('The datatype field cannot exceed 31 characters in length', $this->getBuffer());
	}
}
