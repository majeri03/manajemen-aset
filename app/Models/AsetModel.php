<?php

namespace App\Models;

use CodeIgniter\Model;

class AsetModel extends Model
{
    protected $table            = 'aset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;


    protected $allowedFields    = [
        'kode',
        'kategori_id',
        'sub_kategori_id',
        'merk_id',
        'tipe_id',
        'serial_number',
        'tahun_beli',
        'lokasi_id',
        'keterangan',
        'status',
        'harga_beli',
        'entitas_pembelian',
        'user_pengguna',
        'qrcode',
    ];
   

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';


    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ↓↓↓ FUNGSI BARU DITAMBAHKAN DI SINI ↓↓↓
    public function getAsetDetail($id)
    {
        return $this->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe, kar.nama_karyawan')
                    ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                    ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                    ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
                    ->join('merk m', 'm.id = aset.merk_id', 'left')
                    ->join('tipe t', 't.id = aset.tipe_id', 'left')
                    ->join('karyawan kar', 'kar.id = aset.user_pengguna', 'left')
                    ->find($id);
    }
}