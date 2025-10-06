<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenamePenanggungJawabColumn extends Migration
{
    public function up()
    {
        $fields = [
            'penanggung_jawab' => [
                'name' => 'user_pengguna',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('aset', $fields);
    }

    public function down()
    {
        // Fungsi ini untuk mengembalikan jika migrasi dibatalkan
        $fields = [
            'user_pengguna' => [
                'name' => 'penanggung_jawab',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('aset', $fields);
    }
}