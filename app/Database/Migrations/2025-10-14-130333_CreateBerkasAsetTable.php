<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBerkasAsetTable extends Migration
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
            'nama_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Contoh: BUKTI SERAH TERIMA, FAKTUR PEMBELIAN',
            ],
            'path_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tipe_file' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'ukuran_file' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('aset_id', 'aset', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('berkas_aset');
    }

    public function down()
    {
        $this->forge->dropTable('berkas_aset');
    }
}