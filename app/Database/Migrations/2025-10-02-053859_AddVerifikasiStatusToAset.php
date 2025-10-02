<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVerifikasiStatusToAset extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aset', [
            'status_verifikasi' => [
                'type'       => 'ENUM',
                'constraint' => ['Belum Dicek', 'Sudah Dicek'],
                'default'    => 'Belum Dicek',
                'after'      => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('aset', 'status_verifikasi');
    }
}