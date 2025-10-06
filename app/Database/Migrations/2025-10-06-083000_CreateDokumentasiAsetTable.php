<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumentasiAsetTable extends Migration
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
            'aset_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'path_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama_asli_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tipe_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('aset_id', 'aset', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumentasi_aset');
    }

    public function down()
    {
        $this->forge->dropTable('dokumentasi_aset');
    }
}