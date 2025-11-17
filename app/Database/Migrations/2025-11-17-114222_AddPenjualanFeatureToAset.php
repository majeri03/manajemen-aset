<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenjualanFeatureToAset extends Migration
{
    public function up()
    {
        // Add harga_penjualan column
        $this->forge->addColumn('aset', [
            'harga_penjualan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'harga_beli',
            ],
        ]);

        // Update status ENUM to include "Penjualan"
        $this->db->query("ALTER TABLE aset MODIFY status ENUM('Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak', 'Perbaikan', 'Penjualan') DEFAULT 'Baik Terpakai'");
    }

    public function down()
    {
        // Remove harga_penjualan column
        $this->forge->dropColumn('aset', 'harga_penjualan');

        // Revert status ENUM to previous values
        $this->db->query("ALTER TABLE aset MODIFY status ENUM('Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak', 'Perbaikan') DEFAULT 'Baik Terpakai'");
    }
}
