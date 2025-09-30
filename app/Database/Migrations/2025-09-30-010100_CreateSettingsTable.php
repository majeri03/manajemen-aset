<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');

        // Langsung isi dengan nilai default untuk mode stock opname
        $this->db->table('settings')->insert([
            'setting_key'   => 'stock_opname_mode',
            'setting_value' => 'off'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}