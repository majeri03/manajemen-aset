<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_karyawan', 'jabatan'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'nama_karyawan' => 'required',
        'jabatan'       => 'required',
    ];
    protected $validationMessages   = [
        'nama_karyawan' => ['required' => 'Nama karyawan harus diisi.'],
        'jabatan'       => ['required' => 'Jabatan harus diisi.'],
    ];
}