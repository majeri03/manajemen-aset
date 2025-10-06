<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTahunColumn extends Migration
{
    public function up()
    {
        $fields = [
            'tahun' => [
                'name' => 'tahun_beli',
                'type' => 'YEAR',
            ],
        ];
        $this->forge->modifyColumn('aset', $fields);
    }

    public function down()
    {
        // Perintah untuk mengembalikan jika migrasi dibatalkan
        $fields = [
            'tahun_beli' => [
                'name' => 'tahun',
                'type' => 'YEAR',
            ],
        ];
        $this->forge->modifyColumn('aset', $fields);
    }
}