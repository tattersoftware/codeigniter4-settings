<?php

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
