<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeHargaBeliNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('aset', [
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true, // Mengizinkan nilai NULL
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('aset', [
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false, // Mengembalikan ke aturan semula (tidak boleh null)
                'default' => 0.00,
            ],
        ]);
    }
}