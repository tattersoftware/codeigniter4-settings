<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Settings\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Settings\Models\SettingModel;

class SettingsList extends BaseCommand
{
    protected $group = 'Settings';

    protected $name = 'settings:list';

    protected $description = 'Lists setting templates from the database.';

    public function run(array $params)
    {
        CLI::write(' SETTING TEMPLATES ', 'white', 'black');

        // get all settings
        $rows = model(SettingModel::class)->builder()->select('name, summary, content, protected, created_at')
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'asc')
            ->get()->getResultArray();

        if (empty($rows)) {
            CLI::write('No settings templates.', 'yellow');
        } else {
            $thead = ['Name', 'Summary', 'Content', 'Protected?', 'Created'];
            CLI::table($rows, $thead);
        }
    }
}
