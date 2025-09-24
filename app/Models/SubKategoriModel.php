<?php

namespace App\Models;

use CodeIgniter\Model;

class SubKategoriModel extends Model
{
    protected $table = 'sub_kategori';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = ['kategori_id', 'nama_sub_kategori'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'kategori_id' => 'required|is_natural_no_zero',
        'nama_sub_kategori' => 'required',
    ];
    protected $validationMessages = [
        'kategori_id' => [
            'required' => 'Kategori harus dipilih.',
            'is_natural_no_zero' => 'Kategori tidak valid.',
        ],
        'nama_sub_kategori' => [
            'required' => 'Nama sub kategori harus diisi.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
