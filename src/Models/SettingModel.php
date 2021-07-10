<?php

namespace Tatter\Settings\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use Faker\Generator;
use Tatter\Settings\Entities\Setting;

class SettingModel extends Model
{
	protected $table = 'settings';

	protected $primaryKey = 'id';

	protected $returnType = Setting::class;

	protected $useTimestamps = true;

	protected $useSoftDeletes = true;

	protected $skipValidation = false;

	protected $allowedFields = [
		'name',
		'datatype',
		'summary',
		'content',
		'protected',
	];

	protected $validationRules = [
		'name'      => 'required|max_length[63]',
		'datatype'  => 'required|max_length[31]',
		'summary'   => 'permit_empty|max_length[255]',
		'content'   => 'permit_empty|max_length[255]',
		'protected' => 'in_list[0,1]',
	];

	protected $afterInsert = ['clearTemplates'];

	protected $afterUpdate = ['clearTemplates'];

	protected $afterDelete = ['clearTemplates'];

	/**
	 * Store of loaded Setting templates,
	 * optimized for performance.
	 *
	 * @var array<string,Setting>|null
	 */
	private static $templates;

	/**
	 * Store of overrides by user ID.
	 *
	 * @var array<int,array<int,mixed>> [userId => [settingId => contents]]
	 */
	private static $overrides = [];

	/**
	 * Removes stored and cached templates.
	 *
	 * @return void
	 */
	public function clearTemplates()
	{
		self::$templates = null;

		cache()->delete('settings-templates');
	}

	/**
	 * Retrieves available Settings from the
	 * store, cache, or database; skips the
	 * summary field to improve performance.
	 *
	 * @return array<string,Setting>
	 */
	public function getTemplates(): array
	{
		if (isset(self::$templates))
		{
			return self::$templates;
		}

		$ttl = config('Cache')->ttl ?? 5 * MINUTE;

		// If caching is disabled or no cache is matched...
		if (is_null($ttl) || null === $templates = cache('settings-templates'))
		{
			// ... then have to load from the database instead
			$templates = $this->builder()
			    ->select(['id', 'name', 'datatype', 'content', 'protected'])
			    ->get()->getResultArray();

			if (isset($ttl))
			{
				cache()->save('settings-templates', $templates, $ttl);
			}
		}

		// Convert the arrays to Setting entities and index by name
		self::$templates = [];
		foreach ($templates as $template)
		{
			self::$templates[$template['name']] = (new Setting())->setContentCast($template['datatype'])->setAttributes($template);
		}

		return self::$templates;
	}

	/**
	 * Retrieves all of a user's overrides.
	 *
	 * @param int $userId
	 *
	 * @return array<int,mixed>
	 */
	public function getOverrides(int $userId): array
	{
		if (isset(self::$overrides[$userId]))
		{
			return self::$overrides[$userId];
		}

		// Load from the database
		self::$overrides[$userId] = [];
		foreach ($this->builder('settings_users')->where('user_id', $userId)->get()->getResultArray() as $override)
		{
			self::$overrides[$userId][$override['setting_id']] = $override['content'];
		}

		return self::$overrides[$userId];
	}

	/**
	 * Sets a user override for a Setting.
	 *
	 * @param int   $settingId
	 * @param int   $userId
	 * @param mixed $content
	 *
	 * @return void
	 */
	public function setOverride(int $settingId, int $userId, $content): void
	{
		$builder = $this->builder('settings_users');

		// Remove any existing overrides
		$builder->where('user_id', $userId)
		    ->where('setting_id', $settingId)
		    ->delete();

		// Add the new row
		$builder->insert([
			'setting_id' => $settingId,
			'user_id'    => $userId,
			'content'    => $content,
			'created_at' => Time::now()->toDateTimeString(),
		]);

		if (! isset(self::$overrides[$userId]))
		{
			self::$overrides[$userId] = [];
		}

		self::$overrides[$userId][$settingId] = $content;
	}

	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Setting
	 */
	public function fake(Generator &$faker): Setting
	{
		return new Setting([
			'name'      => $faker->word,
			'datatype'  => 'string',
			'summary'   => $faker->sentence,
			'content'   => $faker->lexify,
			'protected' => ! (bool) mt_rand(0, 3),
		]);
	}
}
