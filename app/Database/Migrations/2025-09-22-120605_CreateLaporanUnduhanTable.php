<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanUnduhanTable extends Migration
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
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'path_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'diunduh_oleh_id' => [ // Menyimpan ID user yang mengunduh
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('diunduh_oleh_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('laporan_unduhan');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_unduhan');
    }
}