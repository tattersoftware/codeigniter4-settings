<?php

namespace Tatter\Settings\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;
use Tatter\Settings\Models\SettingModel;
use Tatter\Settings\Settings;

class SettingsAdd extends BaseCommand
{
	protected $group = 'Settings';

	protected $name = 'settings:add';

	protected $description = 'Adds a setting template to the database.';

	protected $usage = 'settings:add [name] [datatype] [summary] [content] [protected]';

	protected $arguments = [
		'name'      => 'The name of the setting (e.g. "timezone")',
		'summary'   => "A brief summary of this setting's purpose",
		'content'   => 'The default value for the setting',
		'protected' => 'Whether to prevent the setting from being overriden; 0 or 1',
	];

	public function run(array $params = [])
	{
		$row = [];

		// Consume or prompt for each parameter
		$row['name']     = array_shift($params) ?: CLI::prompt('Name of the setting', null, 'required');
		$row['datatype'] = array_shift($params) ?: CLI::prompt('Data type', 'string');
		$row['summary']  = array_shift($params) ?: CLI::prompt('Brief description (optional)');
		$row['content']  = array_shift($params) ?: CLI::prompt('Content value');
		$protected       = array_shift($params) ?: CLI::prompt('Protected?', ['y', 'n']);

		$row['protected'] = (int) ($protected === 'y');

		// Validate the name
		try {
			Settings::validate($row['name']);
		}
		catch (Exception $e)
		{
			$this->showError($e);

			return;
		}

		if (model(SettingModel::class)->insert($row))
		{
			return $this->call('settings:list');
		}

		CLI::write(implode('. ', model(SettingModel::class)->errors()), 'red');
	}
}
