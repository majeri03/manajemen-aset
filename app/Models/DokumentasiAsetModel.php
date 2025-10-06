<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumentasiAsetModel extends Model
{
    protected $table            = 'dokumentasi_aset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'aset_id',
        'path_file',
        'nama_asli_file',
        'tipe_file',
        'keterangan',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}