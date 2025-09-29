<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel; // 1. Tambahkan LokasiModel
use App\Models\MerkModel; // <-- TAMBAHKAN INI

class Dashboard extends BaseController
{
    public function index()
{
    // Inisialisasi model dan database di awal
    $asetModel = new AsetModel();
    $db = \Config\Database::connect(); // Definisikan $db di sini

    // --- 1. DATA UNTUK KARTU RINGKASAN ---
    $totalAset = $asetModel->countAllResults();
    $asetRusak = $asetModel->where('status', 'Rusak')->countAllResults();
    // Gunakan variabel $db yang sudah dibuat
    $pendingRequests = $db->table('aset_update_requests')->where('status', 'pending')->countAllResults();

    // Data Finansial
    $totalNilaiAset = $asetModel->selectSum('harga_beli')->first()['harga_beli'] ?? 0;
    $nilaiAsetRusak = $asetModel->selectSum('harga_beli')->where('status', 'Rusak')->first()['harga_beli'] ?? 0;

    // --- 2. DATA UNTUK CHART ---
    // Pie Chart Kategori
    $distribusiAset = $asetModel->select('k.nama_kategori as kategori, COUNT(aset.id) as jumlah')
                                 ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                 ->groupBy('k.nama_kategori')
                                 ->findAll();
    $labelsKategori = array_column($distribusiAset, 'kategori');
    $dataKategori = array_column($distribusiAset, 'jumlah');

    // Bar Chart Status
    $semuaStatus = ['Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak'];
    $hasilStatus = array_fill_keys($semuaStatus, 0);
    $statusDariDB = $asetModel->select('status, COUNT(id) as jumlah')->groupBy('status')->findAll();
    foreach ($statusDariDB as $status) {
        if (array_key_exists($status['status'], $hasilStatus)) {
            $hasilStatus[$status['status']] = (int)$status['jumlah'];
        }
    }
    $labelsStatus = array_keys($hasilStatus);
    $dataStatus = array_values($hasilStatus);
    
    // --- 3. DATA UNTUK WIDGET LAINNYA ---
    // (Anda bisa tambahkan query untuk widget lain di sini nanti)

    // --- 4. DATA UNTUK MODAL TAMBAH ASET ---
    $kategoriModel = new KategoriModel();
    $subKategoriModel = new SubKategoriModel();
    $lokasiModel = new LokasiModel();
    $merkModel = new MerkModel();

    // --- 5. KUMPULKAN SEMUA DATA UNTUK VIEW ---
    $data = [
        'title'              => 'Dashboard',
        'user'               => session()->get('full_name') ?: 'Guest',
        'asets'              => $asetModel->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
                             ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                             ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                             ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
                             ->join('merk m', 'm.id = aset.merk_id', 'left')
                             ->join('tipe t', 't.id = aset.tipe_id', 'left')
                             ->orderBy('aset.created_at', 'DESC')
                             ->limit(5)
                             ->findAll(),
        'total_aset'         => $totalAset,
        'aset_rusak'         => $asetRusak,
        'total_nilai_aset'   => $totalNilaiAset,
        'nilai_aset_rusak'   => $nilaiAsetRusak,
        'chartLabels'        => $labelsKategori,
        'chartData'          => $dataKategori,
        'statusLabels'       => $labelsStatus,
        'statusData'         => $dataStatus,
        'pending_requests'   => $pendingRequests,
        'kategori_list'      => $kategoriModel->findAll(),
        'subkategori_list'   => $subKategoriModel->findAll(),
        'lokasi_list'        => $lokasiModel->findAll(),
        'merk_list'          => $merkModel->orderBy('nama_merk', 'ASC')->findAll(),
    ];

    return view('Dashboard/index', $data);
}
    

    
}

