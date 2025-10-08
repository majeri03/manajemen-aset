<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeEntitasPembelianNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('aset', [
            'entitas_pembelian' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true, // Mengizinkan entitas_pembelian menjadi NULL
            ],
            'user_pengguna' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true, // Mengizinkan user_pengguna menjadi NULL
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true, // Mengizinkan serial_number menjadi NULL
            ],
        ]);
    }

    public function down()
    {
        // Fungsi down untuk mengembalikan perubahan jika diperlukan
        $this->forge->modifyColumn('aset', [
            'entitas_pembelian' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'user_pengguna' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'serial_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
        ]);
    }
}