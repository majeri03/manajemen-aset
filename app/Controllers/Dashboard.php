<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;

class Dashboard extends BaseController
{
    public function index()
{
    $asetModel = new AsetModel();

    $totalAset = $asetModel->countAllResults();
    $asetRusak = $asetModel->where('status', 'Rusak')->countAllResults();


    // --- Data untuk Pie Chart Kategori ---
    $distribusiAset = $asetModel->select('kategori, COUNT(id) as jumlah')
                                ->groupBy('kategori')
                                ->findAll();
    $labelsKategori = array_column($distribusiAset, 'kategori');
    $dataKategori = array_column($distribusiAset, 'jumlah');

    // --- Logika untuk Bar Chart Status ---
    $semuaStatus = ['Baik', 'Rusak', 'Tidak terpakai'];
    $hasilStatus = array_fill_keys($semuaStatus, 0);
    $statusDariDB = $asetModel->select('status, COUNT(id) as jumlah')
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
        'title'            => 'Dashboard',
        'user'             => session()->get('full_name') ?: 'Guest',
        'asets'            => $asetModel->orderBy('updated_at', 'DESC')->findAll(),
        'total_aset'       => $totalAset, // Tambahkan ini
        'aset_rusak'       => $asetRusak, // Tambahkan ini
        'chartLabels'      => $labelsKategori,
        'chartData'        => $dataKategori,
        'statusLabels'     => $labelsStatus,
        'statusData'       => $dataStatus,
        'pending_requests' => $pendingRequests
    ];

    return view('dashboard/index', $data);
}
}
