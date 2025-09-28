<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenanggungJawabToAset extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aset', [
            'penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'harga_beli', // Sesuaikan posisi jika perlu
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('aset', 'penanggung_jawab');
    }
}