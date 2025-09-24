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
        $asetModel = new AsetModel();
        $kategoriModel = new KategoriModel();
        $subKategoriModel = new SubKategoriModel();
        $lokasiModel = new LokasiModel(); // 2. Buat instance dari LokasiModel
        $merkModel = new MerkModel(); // <-- TAMBAHKAN INI

        $totalAset = $asetModel->countAllResults();
        $asetRusak = (new AsetModel())->where('status', 'Rusak')->countAllResults();

        // --- Data untuk Pie Chart Kategori ---
        $distribusiAset = $asetModel->select('k.nama_kategori as kategori, COUNT(aset.id) as jumlah')
                                     ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                     ->groupBy('k.nama_kategori')
                                     ->findAll();
        $labelsKategori = array_column($distribusiAset, 'kategori');
        $dataKategori = array_column($distribusiAset, 'jumlah');

        // --- Logika untuk Bar Chart Status ---
        $semuaStatus = ['Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak'];
        $hasilStatus = array_fill_keys($semuaStatus, 0);
        $statusDariDB = (new AsetModel())->select('status, COUNT(id) as jumlah')
                                     ->groupBy('status')
                                     ->findAll();
        foreach ($statusDariDB as $status) {
            if (array_key_exists($status['status'], $hasilStatus)) {
                $hasilStatus[$status['status']] = (int)$status['jumlah'];
            }
        }
        $labelsStatus = array_keys($hasilStatus);
        $dataStatus = array_values($hasilStatus);

        // --- Hitung Permintaan Pending ---
        $db = \Config\Database::connect();
        $pendingRequests = $db->table('aset_update_requests')
                              ->where('status', 'pending')
                              ->countAllResults();

        // --- Kirim SEMUA data ke View dalam SATU array $data ---
        $data = [
            'title'              => 'Dashboard',
            'user'               => session()->get('full_name') ?: 'Guest',
            'asets'              => $asetModel->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
                                 ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                 ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                                 ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
                                 ->join('merk m', 'm.id = aset.merk_id', 'left') // <-- JOIN ke tabel merk
                                 ->join('tipe t', 't.id = aset.tipe_id', 'left') // <-- JOIN ke tabel tipe
                                 ->orderBy('aset.created_at', 'DESC') // Urutkan berdasarkan data terbaru
                                 ->limit(5) // Batasi hanya 5 aset terbaru
                                 ->findAll(),
            'total_aset'         => $totalAset,
            'aset_rusak'         => $asetRusak,
            'chartLabels'        => $labelsKategori,
            'chartData'          => $dataKategori,
            'statusLabels'       => $labelsStatus,
            'statusData'         => $dataStatus,
            'pending_requests'   => $pendingRequests,
            'kategori_list'      => $kategoriModel->findAll(),
            'subkategori_list'   => $subKategoriModel->findAll(),
            'lokasi_list'        => $lokasiModel->findAll(),
            'merk_list'          => $merkModel->orderBy('nama_merk', 'ASC')->findAll(), // <-- TAMBAHKAN INI
        ];

        return view('Dashboard/index', $data);
    }

    
}

