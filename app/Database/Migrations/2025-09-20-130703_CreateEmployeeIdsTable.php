<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeIdsTable extends Migration
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
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true, // Pastikan setiap ID unik
            ],
            'is_registered' => [
                'type'    => 'BOOLEAN',
                'default' => false, // Default-nya false, akan jadi true jika sudah dipakai daftar
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employee_ids');
    }

    public function down()
    {
        $this->forge->dropTable('employee_ids');
    }
}