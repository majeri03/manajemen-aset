<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAsetTableForMerkAndTipe extends Migration
{
    public function up()
    {
        // 1. Pindahkan data Merk yang ada ke tabel baru
        $this->db->query('INSERT INTO merk (nama_merk, created_at, updated_at) SELECT DISTINCT merk, NOW(), NOW() FROM aset WHERE merk IS NOT NULL AND merk != ""');

        // 2. Tambahkan kolom baru (merk_id & tipe_id) ke tabel aset
        $this->forge->addColumn('aset', [
            'merk_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'sub_kategori_id'],
            'tipe_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'merk_id'],
        ]);

        // 3. Update kolom merk_id di tabel aset berdasarkan data yang sudah ada
        $this->db->query('UPDATE aset a JOIN merk m ON a.merk = m.nama_merk SET a.merk_id = m.id');

        // 4. Pindahkan data Tipe yang ada ke tabel baru, hubungkan dengan merk_id
        $this->db->query('INSERT INTO tipe (merk_id, nama_tipe, created_at, updated_at) SELECT DISTINCT a.merk_id, a.type, NOW(), NOW() FROM aset a WHERE a.type IS NOT NULL AND a.type != "" AND a.merk_id IS NOT NULL');
        
        // 5. Update kolom tipe_id di tabel aset
        $this->db->query('UPDATE aset a JOIN tipe t ON a.type = t.nama_tipe AND a.merk_id = t.merk_id SET a.tipe_id = t.id');

        // 6. Hapus kolom lama
        $this->forge->dropColumn('aset', 'merk');
        $this->forge->dropColumn('aset', 'type');

        // 7. Tambahkan foreign key
        $this->forge->addForeignKey('merk_id', 'merk', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('tipe_id', 'tipe', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Logika untuk mengembalikan perubahan jika diperlukan (opsional untuk diimplementasikan)
    }
}