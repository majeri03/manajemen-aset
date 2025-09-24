<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAsetTableForCategories extends Migration
{
    public function up()
    {
        // Tambahkan kolom-kolom baru
        $fields = [
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'merk',
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'updated_at',
            ],
            'entitas_pembelian' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'harga_beli',
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kode',
            ],
            'sub_kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kategori_id',
            ],
        ];

        $this->forge->addColumn('aset', $fields);

        // Tambahkan foreign key setelah kolom dibuat
        $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('sub_kategori_id', 'sub_kategori', 'id', 'CASCADE', 'SET NULL');
        
        // Cek dan hapus kolom lama hanya jika ada
        if ($this->db->fieldExists('kategori', 'aset')) {
            $this->forge->dropColumn('aset', 'kategori');
        }
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
        $this->forge->dropForeignKey('aset', 'aset_kategori_id_foreign');
        $this->forge->dropForeignKey('aset', 'aset_sub_kategori_id_foreign');

        // Hapus kolom-kolom baru
        $this->forge->dropColumn('aset', 'type');
        $this->forge->dropColumn('aset', 'harga_beli');
        $this->forge->dropColumn('aset', 'entitas_pembelian');
        $this->forge->dropColumn('aset', 'kategori_id');
        $this->forge->dropColumn('aset', 'sub_kategori_id');

        // Tambahkan kembali kolom lama
        $this->forge->addColumn('aset', [
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
        ]);
    }
}