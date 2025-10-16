<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDokumenPerbaikanTable extends Migration
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
            'user_pemohon_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'penyetuju_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'keterangan_kerusakan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'estimasi_biaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('aset_id', 'aset', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_pemohon_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('dokumen_perbaikan');
    }

    public function down()
    {
        $this->forge->dropTable('dokumen_perbaikan');
    }
}