<?php

namespace Tatter\Settings\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_settings extends Migration
{
    public function up()
    {
        // Settings
        $fields = [
            'name'       => ['type' => 'varchar', 'constraint' => 63, 'unique' => true],
            'scope'      => ['type' => 'varchar', 'constraint' => 15, 'default' => ''],
            'summary'    => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'content'    => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'protected'  => ['type' => 'boolean', 'default' => 1],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('created_at');

        $this->forge->createTable('settings');

        // Settings<->Users
        $fields = [
            'setting_id' => ['type' => 'int', 'unsigned' => true],
            'user_id'    => ['type' => 'int', 'unsigned' => true],
            'content'    => ['type' => 'varchar', 'constraint' => 255, 'default' => ''],
            'created_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey(['setting_id', 'user_id']);
        $this->forge->addKey(['user_id', 'setting_id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('settings_users');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
        $this->forge->dropTable('settings_users');
    }
}
