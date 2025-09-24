<?php

namespace App\Models;

use CodeIgniter\Model;

class TipeModel extends Model
{
    protected $table            = 'tipe';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['merk_id', 'nama_tipe'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'merk_id'   => 'required|is_natural_no_zero',
        'nama_tipe' => 'required',
    ];
    protected $validationMessages   = [
        'merk_id' => [
            'required' => 'Merk harus dipilih.',
        ],
        'nama_tipe' => [
            'required' => 'Nama tipe harus diisi.',
        ],
    ];
}