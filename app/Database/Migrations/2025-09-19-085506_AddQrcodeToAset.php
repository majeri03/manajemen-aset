<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrcodeToAset extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aset', [
            'qrcode' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'status', // Posisi kolom setelah kolom status
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('aset', 'qrcode');
    }
}