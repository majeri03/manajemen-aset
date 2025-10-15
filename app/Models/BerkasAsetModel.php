<?php

namespace App\Models;

use CodeIgniter\Model;

class BerkasAsetModel extends Model
{
    protected $table            = 'berkas_aset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['aset_id', 'nama_berkas', 'path_file', 'tipe_file', 'ukuran_file'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Tidak ada updated_at
}