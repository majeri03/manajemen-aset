<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoPermissionToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'can_perform_so' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'after'      => 'role', // Posisi setelah kolom role
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'can_perform_so');
    }
}