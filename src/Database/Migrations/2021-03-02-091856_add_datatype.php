<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Settings\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatatype extends Migration
{
    public function up()
    {
        $this->forge->addColumn('settings', [
            'datatype' => ['type' => 'varchar', 'constraint' => 31, 'after' => 'name', 'default' => 'string'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('settings', 'datatype');
    }
}
