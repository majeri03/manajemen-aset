<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetUpdateRequestsTable extends Migration
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
            'user_id' => [ // ID pengguna yang mengajukan
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'proposed_data' => [ // Data baru dalam format JSON
                'type' => 'TEXT',
            ],
            'status' => [ // Status permintaan
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('aset_id', 'aset', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('aset_update_requests');
    }

    public function down()
    {
        $this->forge->dropTable('aset_update_requests');
    }
}