<?php

 namespace App\Models;

 use CodeIgniter\Model;

 class DokumenPerbaikanModel extends Model
 {
     protected $table            = 'dokumen_perbaikan';
     protected $primaryKey       = 'id';
     protected $useAutoIncrement = true;
     protected $returnType       = 'array';
     protected $useSoftDeletes   = false;
     protected $allowedFields    = [
         'aset_id', 
         'user_pemohon_id', 
         'penyetuju_nama', 
         'keterangan_kerusakan', 
         'estimasi_biaya', 
         'nama_file'
     ];
     protected $useTimestamps = true;
 }