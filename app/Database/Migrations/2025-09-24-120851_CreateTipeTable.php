<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipeTable extends Migration
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
            'merk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_tipe' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('merk_id', 'merk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tipe');
    }

    public function down()
    {
        $this->forge->dropTable('tipe');
    }
}