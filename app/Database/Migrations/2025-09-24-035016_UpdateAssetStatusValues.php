<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAssetStatusValues extends Migration
{
    public function up()
    {
        // Mengubah nilai status yang ada di database
        $this->db->query("UPDATE aset SET status = 'Baik Terpakai' WHERE status = 'Baik'");
        $this->db->query("UPDATE aset SET status = 'Baik Tidak Terpakai' WHERE status = 'Tidak terpakai'");
    }

    public function down()
    {
        // Mengembalikan nilai jika migrasi di-rollback
        $this->db->query("UPDATE aset SET status = 'Baik' WHERE status = 'Baik Terpakai'");
        $this->db->query("UPDATE aset SET status = 'Tidak terpakai' WHERE status = 'Baik Tidak Terpakai'");
    }
}
