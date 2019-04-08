<?php namespace Tatter\Settings\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_settings extends Migration
{
	public function up()
	{
		// settings
		$fields = [
			'name'         => ['type' => 'VARCHAR', 'constraint' => 63, 'unique' => true],
			'scope'        => ['type' => 'VARCHAR', 'constraint' => 15],
			'content'      => ['type' => 'VARCHAR', 'constraint' => 255],
			'summary'      => ['type' => 'VARCHAR', 'constraint' => 255],
			'protected'    => ['type' => 'BOOLEAN', 'default' => 1],
			'deleted'      => ['type' => 'BOOLEAN', 'default' => 0],
			'created_at'   => ['type' => 'DATETIME', 'null' => true],
			'updated_at'   => ['type' => 'DATETIME', 'null' => true],
		];
		
		$this->forge->addField('id');
		$this->forge->addField($fields);

		$this->forge->addUniqueKey('name');
		$this->forge->addKey('created_at');
		
		$this->forge->createTable('settings');
		
		// users override settings
		$fields = [
			'setting_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
			'user_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
			'content'      => ['type' => 'VARCHAR', 'constraint' => 255],
			'created_at'  => ['type' => 'DATETIME', 'null' => true],
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
