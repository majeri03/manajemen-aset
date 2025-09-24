<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = ['nama_kategori'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_kategori' => 'required|is_unique[kategori.nama_kategori,id,{id}]',
    ];
    protected $validationMessages = [
        'nama_kategori' => [
            'required' => 'Nama kategori harus diisi.',
            'is_unique' => 'Kategori ini sudah ada.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
