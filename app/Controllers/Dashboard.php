<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel;

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
        $distribusiAset = $asetModel->select('k.nama_kategori as kategori, COUNT(aset.id) as jumlah')
                                    ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                    ->groupBy('k.nama_kategori')
                                    ->findAll();
        $labelsKategori = array_column($distribusiAset, 'kategori');
        $dataKategori = array_column($distribusiAset, 'jumlah');

        // Bar Chart Status
        $semuaStatus = ['Baik Terpakai', 'Baik Tidak Terpakai', 'Rusak', 'Perbaikan'];
        $hasilStatus = array_fill_keys($semuaStatus, 0);
        $statusDariDB = $asetModel->select('status, COUNT(id) as jumlah')->groupBy('status')->findAll();
        foreach ($statusDariDB as $status) {
            if (array_key_exists($status['status'], $hasilStatus)) {
                $hasilStatus[$status['status']] = (int)$status['jumlah'];
            }
        }
        $labelsStatus = array_keys($hasilStatus);
        $dataStatus = array_values($hasilStatus);


    // Langkah 1: Dapatkan ID lokasi yang sudah ada aktivitas SO
$activeLokasiIds = $db->table('aset')
    ->distinct()
    ->select('lokasi_id')
    ->where('status_verifikasi', 'Sudah Dicek')
    ->where('deleted_at IS NULL')
    ->get()
    ->getResultArray();

// Ubah array hasil menjadi array ID sederhana
$lokasiIds = array_column($activeLokasiIds, 'lokasi_id');

$stockOpnamePerLokasi = [];
// Lanjutkan hanya jika ada lokasi yang sudah diproses
if (!empty($lokasiIds)) {
    // Langkah 2: Ambil data progres hanya untuk lokasi-lokasi tersebut
    $stockOpnamePerLokasi = $db->table('lokasi')
        ->select('lokasi.id as id_lokasi, lokasi.nama_lokasi, COUNT(a.id) as total_aset, COUNT(CASE WHEN a.status_verifikasi = "Sudah Dicek" THEN 1 ELSE NULL END) as sudah_dicek')
        ->join('aset a', 'a.lokasi_id = lokasi.id', 'left')
        ->whereIn('lokasi.id', $lokasiIds)
        ->where('a.deleted_at IS NULL')
        ->groupBy('lokasi.id, lokasi.nama_lokasi')
        ->orderBy('lokasi.nama_lokasi', 'ASC')
        ->get()
        ->getResultArray();
}
// Bagian ini bertugas menghitung dan menambahkan 'persentase' ke setiap lokasi
foreach ($stockOpnamePerLokasi as &$lokasi) { // Gunakan referensi '&'
    if ($lokasi['total_aset'] > 0) {
        $lokasi['persentase'] = ($lokasi['sudah_dicek'] / $lokasi['total_aset']) * 100;
    } else {
        $lokasi['persentase'] = 0;
    }
}
unset($lokasi); // Hapus referensi setelah loop selesai



   // --- DATA UNTUK DAFTAR PENANGGUNG JAWAB ---
    $daftarPenanggungJawab = $db->table('aset')
        ->select('user_pengguna, COUNT(id) as jumlah_aset')
        ->where('deleted_at', null) // Tetap filter aset yang aktif
        ->where('user_pengguna IS NOT NULL')
        ->where('user_pengguna !=', '')
        ->groupBy('user_pengguna')
        ->orderBy('jumlah_aset', 'DESC')
        ->get()
        ->getResultArray();

    $nilaiPerLokasi = $db->table('aset a')
        ->select('l.nama_lokasi, SUM(a.harga_beli) as total_nilai')
        ->join('lokasi l', 'l.id = a.lokasi_id', 'left')
        ->where('a.deleted_at', null)
        ->groupBy('l.nama_lokasi')
        ->orderBy('total_nilai', 'DESC')
        ->limit(7)
        ->get()
        ->getResultArray();
        
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i months"));
            $tahun = date('Y', strtotime("-$i months"));
            $namaBulan = date('M Y', strtotime("-$i months"));
            
            $jumlah = $asetModel->where('MONTH(created_at)', $bulan)
                                ->where('YEAR(created_at)', $tahun)
                                ->countAllResults();

    $trendData['labels'][] = $namaBulan;
    $trendData['data'][] = $jumlah;
}

        // --- 4. DATA UNTUK MODAL TAMBAH ASET ---
        $kategoriModel = new KategoriModel();
        $subKategoriModel = new SubKategoriModel();
        $lokasiModel = new LokasiModel();
        $merkModel = new MerkModel();

        // --- 5. KUMPULKAN SEMUA DATA UNTUK VIEW ---
        $data = [
            'title'                   => 'Dashboard',
            'user'                    => session()->get('full_name') ?: 'Guest',
            'asets'                   => $asetModel->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
                                    ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                    ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                                    ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
                                    ->join('merk m', 'm.id = aset.merk_id', 'left')
                                    ->join('tipe t', 't.id = aset.tipe_id', 'left')
                                    ->orderBy('aset.created_at', 'DESC')
                                    ->limit(5)
                                    ->findAll(),
            'total_aset'              => $totalAset,
            'aset_rusak'              => $asetRusak,
            'total_nilai_aset'        => $totalNilaiAset,
            'nilai_aset_rusak'        => $nilaiAsetRusak,
            'chartLabels'             => $labelsKategori,
            'chartData'               => $dataKategori,
            'statusLabels'            => $labelsStatus,
            'statusData'              => $dataStatus,
            'pending_requests'        => $pendingRequests,
            'kategori_list'           => $kategoriModel->findAll(),
            'subkategori_list'        => $subKategoriModel->findAll(),
            'lokasi_list'             => $lokasiModel->findAll(),
            'merk_list'               => $merkModel->orderBy('nama_merk', 'ASC')->findAll(),
            'stock_opname_per_lokasi' => $stockOpnamePerLokasi,
            'daftar_penanggung_jawab' => $daftarPenanggungJawab,
            'lokasiLabels'            => array_column($nilaiPerLokasi, 'nama_lokasi'),
            'lokasiData'              => array_column($nilaiPerLokasi, 'total_nilai'),
            'trendLabels'             => $trendData['labels'],
            'trendData'               => $trendData['data'],
        ];

        return view('Dashboard/index', $data);
    }

    public function laporanLokasiDetail($lokasi_id)
{
    $db = \Config\Database::connect();
    $lokasiModel = new \App\Models\LokasiModel();
    $lokasi = $lokasiModel->find($lokasi_id);

    if (!$lokasi) {
        return redirect()->to('/dashboard')->with('error', 'Lokasi tidak ditemukan.');
    }

    // 1. Aset yang seharusnya ada di lokasi ini
    $asetSeharusnya = $db->table('aset a')
        ->select('a.kode, sk.nama_sub_kategori, m.nama_merk, a.status_verifikasi')
        ->join('sub_kategori sk', 'sk.id = a.sub_kategori_id', 'left')
        ->join('merk m', 'm.id = a.merk_id', 'left')
        ->where('a.lokasi_id', $lokasi_id)
        ->where('a.deleted_at IS NULL')
        ->orderBy('a.kode', 'ASC')
        ->get()->getResultArray();

    // Pisahkan antara yang sudah dan belum dicek
    $asetDitemukan = array_filter($asetSeharusnya, fn($aset) => $aset['status_verifikasi'] === 'Sudah Dicek');
    $asetBelumDitemukan = array_filter($asetSeharusnya, fn($aset) => $aset['status_verifikasi'] === 'Belum Dicek');

    // 2. Aset salah tempat yang ditemukan di lokasi ini
    // Ambil semua aset yang riwayat scan terakhirnya ada di lokasi ini, tapi seharusnya tidak di sini
    $asetSalahTempat = $db->table('stock_opname_history soh')
        ->select('a.kode, sk.nama_sub_kategori, m.nama_merk, l.nama_lokasi as lokasi_seharusnya')
        ->join('aset a', 'a.id = soh.aset_id')
        ->join('sub_kategori sk', 'sk.id = a.sub_kategori_id', 'left')
        ->join('merk m', 'm.id = a.merk_id', 'left')
        ->join('lokasi l', 'l.id = a.lokasi_id', 'left')
        ->where('soh.lokasi_scan_id', $lokasi_id)
        ->where('a.lokasi_id !=', $lokasi_id)
        ->where('a.deleted_at IS NULL')
        ->groupBy('a.id')
        ->orderBy('a.kode', 'ASC')
        ->get()->getResultArray();

    $data = [
        'title'              => 'Detail Stock Opname: ' . $lokasi['nama_lokasi'],
        'lokasi'             => $lokasi,
        'asetDitemukan'      => $asetDitemukan,
        'asetBelumDitemukan' => $asetBelumDitemukan,
        'asetSalahTempat'    => $asetSalahTempat,
    ];

    return view('laporan/detail_lokasi', $data);
}
    
}