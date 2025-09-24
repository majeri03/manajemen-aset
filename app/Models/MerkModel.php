<?php

namespace App\Models;

use CodeIgniter\Model;

class MerkModel extends Model
{
    protected $table            = 'merk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_merk'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_merk' => 'required|is_unique[merk.nama_merk,id,{id}]',
    ];
    protected $validationMessages   = [
        'nama_merk' => [
            'required'  => 'Nama merk harus diisi.',
            'is_unique' => 'Merk ini sudah terdaftar.',
        ],
    ];
}