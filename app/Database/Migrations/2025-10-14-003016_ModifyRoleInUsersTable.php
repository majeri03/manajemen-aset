<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyRoleInUsersTable extends Migration
{
    public function up()
    {
        // Langkah 1: Ubah semua 'manager' yang ada menjadi 'admin' terlebih dahulu
        $this->db->query("UPDATE users SET role = 'admin' WHERE role = 'manager'");

        // Langkah 2: Ubah definisi kolom ENUM
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['super_admin', 'admin', 'staff'],
                'default'    => 'staff',
            ],
        ]);
    }

    public function down()
    {
        // Untuk membatalkan, kita kembalikan definisinya
        // Perhatian: Ini akan mengubah 'super_admin' kembali menjadi 'admin',
        // karena kita tidak bisa tahu mana yang sebelumnya 'manager'.
        $this->db->query("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
        
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'manager', 'staff'],
                'default'    => 'staff',
            ],
        ]);
    }
}