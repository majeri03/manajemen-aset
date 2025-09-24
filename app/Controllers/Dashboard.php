<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $asetModel = new AsetModel();
        $kategoriModel = new KategoriModel();
        $subKategoriModel = new SubKategoriModel();

        $totalAset = $asetModel->countAllResults();
        // Cloning the model to reset the query builder for the next query
        $asetRusak = (new AsetModel())->where('status', 'Rusak')->countAllResults();

        // --- Data untuk Pie Chart Kategori ---
        $distribusiAset = $asetModel->select('k.nama_kategori as kategori, COUNT(aset.id) as jumlah')
                                     ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                     ->groupBy('k.nama_kategori')
                                     ->findAll();
        $labelsKategori = array_column($distribusiAset, 'kategori');
        $dataKategori = array_column($distribusiAset, 'jumlah');

        // --- Logika untuk Bar Chart Status ---
        $semuaStatus = ['Baik', 'Rusak', 'Tidak terpakai'];
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
            // [FIXED] Added JOIN to fetch category and sub-category names for the table
            'asets'              => $asetModel->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
                                             ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                             ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                                             ->orderBy('aset.updated_at', 'DESC')
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
        ];

        return view('dashboard/index', $data);
    }
}
