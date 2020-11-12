<?php namespace Tatter\Settings\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Settings\Models\SettingModel;

class SettingsAdd extends BaseCommand
{
	protected $group       = 'Settings';
	protected $name        = 'settings:add';
	protected $description = "Adds a setting template to the database.";
    
	protected $usage     = "settings:add [name] [scope] [content] [protected] [notes]";
	protected $arguments = [
		'name'       => "The name of the setting (e.g. 'timezone')",
		'scope'      => "The scope of the setting ('session', 'user', or 'global') *See docs*",
		'content'    => "The default/global value of the setting",
		'protected'  => "Whether to prevent template from being changed or deleted; 0 or 1",
	];

	public function run(array $params = [])
	{
		$settings = new SettingModel();
		
		// consume or prompt for a setting name
		$name = array_shift($params);
		if (empty($name))
		{
			$name = CLI::prompt('Name of the setting', null, 'required');
		}
		
		// consume or prompt for the scope
		$scope = array_shift($params);
		if (empty($scope))
		{
			$scope = CLI::prompt('Scope', ['global', 'user', 'session']);
		}
				
		// consume or prompt for content
		$content = array_shift($params);
		if (empty($content))
		{
			$content = CLI::prompt('Default content');
		}

		// consume or prompt for the protection status
		$protected = array_shift($params);
		if (empty($protected))
		{
			$protected = CLI::prompt('Protected?', ['y', 'n']);
		}

		// consume or prompt for summary
		$summary = array_shift($params);
		if (empty($summary))
		{
			$summary = CLI::prompt('Brief description (optional)');
		}
		
		// build the row
		$setting = [
			'name'       => $name,
			'scope'      => $scope,
			'content'    => $content,
			'protected'  => ($protected=='y'),
			'summary'    => $summary,
		];
				
		try
		{
			$settings->save($setting);
		}
		catch (\Exception $e)
		{
			$this->showError($e);
		}
		
		$this->call('settings:list');
	}
}
