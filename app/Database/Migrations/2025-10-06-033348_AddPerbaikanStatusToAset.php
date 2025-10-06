<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerbaikanStatusToAset extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE aset MODIFY status ENUM('Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak', 'Perbaikan') DEFAULT 'Baik Terpakai'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE aset MODIFY status ENUM('Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak') DEFAULT 'Baik Terpakai'");
    }
}