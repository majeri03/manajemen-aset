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
        
        // app/Controllers/Dashboard.php

    // --- 3. DATA BARU: LAPORAN KEJANGGALAN DARI STOCK OPNAME ---
    $laporan_kejanggalan = $db->table('stock_opname_history soh')
        ->select('soh.opname_at, soh.data_sesudah, a.id as aset_id, a.kode, u.full_name')
        ->join('aset a', 'a.id = soh.aset_id')
        ->join('users u', 'u.id = soh.user_id')
        ->where('soh.ada_perubahan', 1)
        ->where('a.deleted_at', null)
        ->orderBy('soh.opname_at', 'DESC')
        ->limit(5)
        ->get()
        ->getResultArray();

    $lokasiModel = new \App\Models\LokasiModel();
    foreach ($laporan_kejanggalan as &$laporan) { // Tanda '&' penting
        $perubahan = json_decode($laporan['data_sesudah'], true);
        if (!is_array($perubahan)) continue;

        $detail_perubahan = [];
        foreach ($perubahan as $field => $value) {
            $namaField = ucfirst(str_replace(['_id', '_'], ['', ' '], $field));
            
            $nilaiBaru = esc($value);

            if ($field === 'lokasi_id') {
                $lokasi = $lokasiModel->find($value);
                $nilaiBaru = $lokasi ? esc($lokasi['nama_lokasi']) : 'ID: ' . esc($value);
            }
            
            $detail_perubahan[$namaField] = $nilaiBaru;
        }
        $laporan['detail_perubahan'] = $detail_perubahan; // <- Ini yang membuat variabelnya ada di view
    }
    unset($laporan); // Hapus referensi
    // =================================================================




   // --- [FINAL] DATA UNTUK DAFTAR PENANGGUNG JAWAB ---
    $daftarPenanggungJawab = $db->table('aset')
        ->select('penanggung_jawab, COUNT(id) as jumlah_aset')
        ->where('deleted_at', null) // Tetap filter aset yang aktif
        ->where('penanggung_jawab IS NOT NULL')
        ->where('penanggung_jawab !=', '')
        ->groupBy('penanggung_jawab')
        ->orderBy('jumlah_aset', 'DESC')
        ->get()
        ->getResultArray();

    $nilaiPerLokasi = $db->table('aset a')
        ->select('l.nama_lokasi, SUM(a.harga_beli) as total_nilai')
        ->join('lokasi l', 'l.id = a.lokasi_id', 'left')
        ->where('a.deleted_at', null)
        ->groupBy('l.nama_lokasi')
        ->orderBy('total_nilai', 'DESC')
        ->limit(7) // Ambil 7 lokasi teratas
        ->get()
        ->getResultArray();
        
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i months"));
            $tahun = date('Y', strtotime("-$i months"));
            $namaBulan = date('M Y', strtotime("-$i months")); // Format: Jan 2024
            
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
            'laporan_kejanggalan'     => $laporan_kejanggalan,
            'daftar_penanggung_jawab' => $daftarPenanggungJawab,
            'lokasiLabels'            => array_column($nilaiPerLokasi, 'nama_lokasi'),
            'lokasiData'              => array_column($nilaiPerLokasi, 'total_nilai'),
            'trendLabels'             => $trendData['labels'],
            'trendData'               => $trendData['data'],
        ];

        return view('Dashboard/index', $data);
    }
    
}