<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PopulateLokasiFromAset extends Migration
{
    public function up()
    {
        // Nonaktifkan foreign key check sementara untuk kelancaran proses
        $this->db->disableForeignKeyChecks();

        // 1. Ambil semua data lokasi yang unik (tidak ada duplikat) dari tabel aset
        $builder = $this->db->table('aset');
        $locations = $builder->select('lokasi')->distinct()->get()->getResultArray();

        if (!empty($locations)) {
            $lokasiData = [];
            foreach ($locations as $loc) {
                // Pastikan kita tidak memasukkan data lokasi yang kosong
                if (!empty(trim($loc['lokasi']))) {
                    $lokasiData[] = ['nama_lokasi' => trim($loc['lokasi'])];
                }
            }

            // 2. Masukkan semua data lokasi unik tersebut ke tabel 'lokasi' yang baru
            if (!empty($lokasiData)) {
                $this->db->table('lokasi')->insertBatch($lokasiData);
            }
        }
        
        // Aktifkan kembali foreign key check
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // Jika migrasi ini di-rollback, kosongkan kembali tabel lokasi
        $this->db->table('lokasi')->truncate();
    }
}

