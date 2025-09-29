<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'lokasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_lokasi'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_lokasi' => 'required|is_unique[lokasi.nama_lokasi,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_lokasi' => [
            'required'  => 'Nama lokasi harus diisi.',
            'is_unique' => 'Lokasi ini sudah terdaftar.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}

