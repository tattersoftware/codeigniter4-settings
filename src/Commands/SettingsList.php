<?php namespace Tatter\Settings\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SettingsList extends BaseCommand
{
    protected $group       = 'Settings';
    protected $name        = 'settings:list';
    protected $description = 'Lists setting templates from the database.';

    public function run(array $params)
    {
		$db = db_connect();
		
		CLI::write(" SETTING TEMPLATES ", 'white', 'black');
		
		// get all settings
		$rows = $db->table('settings')->select('name, scope, content, summary, protected, created_at')
			->where('deleted', 0)
			->orderBy('name', 'asc')
			->get()->getResultArray();

		if (empty($rows)):
			CLI::write( CLI::color("No settings templates.", 'yellow') );
		else:
			$thead = ['Name', 'Scope', 'Content', 'Notes', 'Protected?', 'Created'];
			CLI::table($rows, $thead);
		endif;
	}
}
