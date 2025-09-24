<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAsetTableForLokasi extends Migration
{
    public function up()
    {
        // Nonaktifkan foreign key check sementara
        $this->db->disableForeignKeyChecks();

        // 1. Tambahkan kolom baru 'lokasi_id' ke tabel 'aset'
        $this->forge->addColumn('aset', [
            'lokasi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tahun', // Posisi kolom setelah kolom 'tahun'
            ],
        ]);

        // 2. Isi kolom 'lokasi_id' dengan ID yang sesuai dari tabel 'lokasi'
        // Mencocokkan berdasarkan nama lokasi yang sama
        $this->db->query('
            UPDATE aset a
            JOIN lokasi l ON a.lokasi = l.nama_lokasi
            SET a.lokasi_id = l.id
        ');

        // 3. Hapus kolom 'lokasi' yang lama karena sudah tidak diperlukan
        $this->forge->dropColumn('aset', 'lokasi');
        
        // 4. Buat relasi (foreign key) antara 'lokasi_id' di tabel 'aset'
        // dengan 'id' di tabel 'lokasi'
        $this->forge->addForeignKey('lokasi_id', 'lokasi', 'id', 'CASCADE', 'SET NULL');
        
        // Aktifkan kembali foreign key check
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // Bagian ini untuk membatalkan migrasi (rollback) jika diperlukan
        $this->db->disableForeignKeyChecks();

        // 1. Buat kembali kolom 'lokasi' yang lama
        $this->forge->addColumn('aset', [
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);

        // 2. Kembalikan data nama lokasi dari tabel 'lokasi' ke tabel 'aset'
         $this->db->query('
            UPDATE aset a
            JOIN lokasi l ON a.lokasi_id = l.id
            SET a.lokasi = l.nama_lokasi
        ');

        // 3. Hapus foreign key dan kolom 'lokasi_id'
        $this->forge->dropForeignKey('aset', 'aset_lokasi_id_foreign');
        $this->forge->dropColumn('aset', 'lokasi_id');
        
        $this->db->enableForeignKeyChecks();
    }
}

