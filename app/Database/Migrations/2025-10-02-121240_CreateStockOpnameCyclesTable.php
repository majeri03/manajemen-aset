<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockOpnameCyclesTable extends Migration
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
            'start_date' => [
                'type' => 'DATETIME',
            ],
            'end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'started_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'completed'],
                'default'    => 'active',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('started_by_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_opname_cycles');
    }

    public function down()
    {
        $this->forge->dropTable('stock_opname_cycles');
    }
}
