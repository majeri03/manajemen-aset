<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockOpnameHistoryTable extends Migration
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
            'user_id' => [ // Pengguna yang melakukan verifikasi
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'opname_at' => [ // Waktu verifikasi
                'type' => 'DATETIME',
                'null' => true,
            ],
            'catatan' => [ // Catatan opsional dari pengguna
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_sebelum' => [ // JSON data aset sebelum ada perubahan
                'type' => 'TEXT',
                'null' => true,
            ],
            'data_sesudah' => [ // JSON data yang diusulkan jika ada perubahan
                'type' => 'TEXT',
                'null' => true,
            ],
            'ada_perubahan' => [ // Flag untuk menandai apakah ada usulan perubahan
                'type' => 'BOOLEAN',
                'default' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('aset_id', 'aset', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_opname_history');
    }

    public function down()
    {
        $this->forge->dropTable('stock_opname_history');
    }
}