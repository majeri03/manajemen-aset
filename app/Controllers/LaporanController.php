<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;

class LaporanController extends BaseController
{
    public function index()
    {
        $asetModel = new AsetModel();
        $db = \Config\Database::connect();

        // --- Statistik Utama ---
        $totalAset = $asetModel->countAllResults();
        $asetBaik = $asetModel->where('status', 'Baik')->countAllResults();
        $asetRusak = $asetModel->where('status', 'Rusak')->countAllResults();
        $asetTidakTerpakai = $asetModel->where('status', 'Tidak terpakai')->countAllResults();

        // --- Data untuk Peta Lokasi ---
        $lokasiAset = $asetModel->select('lokasi, COUNT(id) as jumlah')
                                ->groupBy('lokasi')
                                ->orderBy('jumlah', 'DESC')
                                ->limit(5) // Ambil 5 lokasi teratas
                                ->get()->getResultArray();

        // --- Data untuk Chart Tren Bulanan (Contoh 6 bulan terakhir) ---
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('m', strtotime("-$i months"));
            $tahun = date('Y', strtotime("-$i months"));
            $namaBulan = date('M', strtotime("-$i months"));
            
            $jumlah = $asetModel->where('MONTH(created_at)', $bulan)
                                ->where('YEAR(created_at)', $tahun)
                                ->countAllResults();
            $trendData['labels'][] = $namaBulan;
            $trendData['data'][] = $jumlah;
        }
        
        // --- Riwayat Unduhan ---
        $riwayatUnduhan = $db->table('laporan_unduhan as lu')
                            ->select('lu.id, lu.nama_file, lu.created_at, u.full_name')
                            ->join('users as u', 'u.id = lu.diunduh_oleh_id')
                            ->orderBy('lu.created_at', 'DESC')
                            ->get()->getResultArray();

        $data = [
            'title'             => 'Asset Intelligence Hub',
            'totalAset'         => $totalAset,
            'asetBaik'          => $asetBaik,
            'asetRusak'         => $asetRusak,
            'asetTidakTerpakai' => $asetTidakTerpakai,
            'lokasiAset'        => $lokasiAset,
            'trendLabels'       => $trendData['labels'],
            'trendData'         => $trendData['data'],
            'riwayat'           => $riwayatUnduhan,
        ];

        return view('laporan/index', $data);
    }

    public function download($id = null)
    {
        $db = \Config\Database::connect();
        $laporan = $db->table('laporan_unduhan')->where('id', $id)->get()->getRow();

        if ($laporan && file_exists($laporan->path_file)) {
            return $this->response->download($laporan->path_file, null)->setFileName($laporan->nama_file);
        }

        return redirect()->to('/laporan')->with('error', 'File laporan tidak ditemukan atau telah dihapus.');
    }
}