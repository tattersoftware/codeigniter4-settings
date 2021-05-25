<?php

use CodeIgniter\Test\DatabaseTestTrait;
use Myth\Auth\Test\AuthTestTrait;
use Tatter\Settings\Entities\Setting;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Settings;
use Tests\Support\SettingsTestCase;

final class LibraryTest extends SettingsTestCase
{
	use AuthTestTrait, DatabaseTestTrait;

	protected $migrateOnce = true;
	protected $seedOnce    = true;

	protected function tearDown(): void
	{
		parent::tearDown();

		$this->resetAuthServices();
		model(SettingModel::class)->clearTemplates();
	}

	public function testInvalidNameThrowsException()
	{
		$this->expectException('InvalidArgumentException');
		$this->expectExceptionMessage('Cache key contains reserved characters {}()/\@:');

		(new Settings)->get('colons:not:allow');
	}

	public function testGetTemplate()
	{
		$result = (new Settings)->getTemplate('currencyScale');

		$this->assertInstanceOf(Setting::class, $result);
		$this->assertSame(100, $result->content);
		$this->assertSame(null, $result->summary); // Summaries are dropped to improve performance
	}

	public function testSetSession()
	{
		$settings = new Settings();
		$method   = $this->getPrivateMethodInvoker($settings, 'setSession');

		$result = $method('foo', 'bar');

		$this->assertSame('bar', $result);
		$this->assertSame(['settings-foo' => 'bar'], $_SESSION);
	}

	public function testGetUsesSession()
	{
		$_SESSION['settings-fruit'] = 'banana';

		$result = (new Settings)->get('fruit');

		$this->assertSame('banana', $result);
	}

	public function testGetReturnsNull()
	{
		$result = (new Settings)->get('does not exist');

		$this->assertNull($result);
	}

	public function testGetReturnsDefault()
	{
		$result = (new Settings)->get('currencyScale');

		$this->assertSame(100, $result);
		$this->assertSame(['settings-currencyScale' => 100], $_SESSION);
	}

	public function testGetUsesOverride()
	{
		$user     = $this->createAuthUser();
		$settings = new Settings();
		$setting  = $settings->getTemplate('perPage');

		$this->hasInDatabase('settings_users', [
			'setting_id' => $setting->id,
			'user_id'    => $user->id,
			'content'    => '42',
		]);

		$result = $settings->get('perPage');

		$this->assertSame(42, $result);
		$this->assertSame(42, $_SESSION['settings-perPage']);
	}

	public function testGetProtectedIgnoresOverride()
	{
		$user     = $this->createAuthUser();
		$settings = new Settings();
		$setting  = $settings->getTemplate('siteVersion');

		$this->hasInDatabase('settings_users', [
			'setting_id' => $setting->id,
			'user_id'    => $user->id,
			'content'    => '1.2.3',
		]);

		$result = (new Settings)->get('siteVersion');

		$this->assertSame('1.0.0', $result);
		$this->assertSame('1.0.0', $_SESSION['settings-siteVersion']);
	}

	public function testMagicGet()
	{
		$result = (new Settings)->currencyScale;

		$this->assertSame(100, $result);
		$this->assertSame(100, $_SESSION['settings-currencyScale']);
	}

	public function testSetAlwaysSetsSession()
	{
		$result = (new Settings)->set('goblins', 'blaart');

		$this->assertInstanceOf(Settings::class, $result);
		$this->assertSame(['settings-goblins' => 'blaart'], $_SESSION);
	}

	public function testSetIgnoresProtected()
	{
		$settings = new Settings();

		$result = $settings->set('serverTimezone', 'Australia/Darwin');

		$this->assertSame('America/New_York', $settings->serverTimezone);
		$this->assertSame('America/New_York', $_SESSION['settings-serverTimezone']);
	}

	public function testSetNoUser()
	{
		$settings = new Settings();
		$setting  = $settings->getTemplate('theme');
		$result   = $settings->set('theme', 77);

		$this->assertSame(['settings-theme' => 77], $_SESSION);
		$this->dontSeeInDatabase('settings_users', ['setting_id' => $setting->id]);
	}

	public function testSetCreatesOverride()
	{
		$user     = $this->createAuthUser();
		$settings = new Settings();
		$setting  = $settings->getTemplate('timezone');
		$result   = $settings->set('timezone', 'foobar/bam');

		$this->assertSame('foobar/bam', $_SESSION['settings-timezone']);
		$this->seeInDatabase('settings_users', [
			'setting_id' => $setting->id,
			'user_id'    => $user->id,
			'content'    => 'foobar/bam',
		]);
	}

	public function testStoreOverrides()
	{
		$user     = $this->createAuthUser();
		$settings = new Settings();
		$result   = $settings->set('theme', 99);

		// Remove records from the cache and database so
		// we are certain this is coming from the model storage
		model(SettingModel::class)->builder('settings_users')->truncate();
		unset($_SESSION['settings-theme']);
		cache()->clean();

		$this->assertSame(99, $settings->theme);
	}

	public function testMagicSet()
	{
		$settings = new Settings();

		$result = $settings->winnie = 'pooh';

		$this->assertSame('pooh', $settings->get('winnie'));
	}
}
