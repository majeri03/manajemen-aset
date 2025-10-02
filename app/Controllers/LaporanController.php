<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    // ======================================================================
    // FUNGSI BARU 1: Menampilkan halaman daftar siklus laporan
    // ======================================================================
    public function stockOpnameReport()
    {
        $db = \Config\Database::connect();
        $completedCycles = $db->table('stock_opname_cycles')
            ->select('stock_opname_cycles.*, u.full_name as started_by')
            ->join('users u', 'u.id = stock_opname_cycles.started_by_user_id')
            ->where('stock_opname_cycles.status', 'completed')
            ->orderBy('start_date', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title'   => 'Laporan Siklus Stock Opname',
            'cycles'  => $completedCycles,
        ];

        return view('laporan/stock_opname_report', $data);
    }

    // ======================================================================
    // FUNGSI BARU 2: Membuat dan mengekspor file Excel per siklus
    // ======================================================================
    public function exportStockOpnameReport($cycle_id)
    {
        $db = \Config\Database::connect();
        $cycle = $db->table('stock_opname_cycles')->where('id', $cycle_id)->get()->getRow();

        if (!$cycle) {
            return redirect()->to('laporan/stockopname')->with('error', 'Siklus laporan tidak ditemukan.');
        }

        // Query super lengkap untuk mendapatkan semua data
        $query = "
            SELECT 
                a.kode, a.serial_number, a.tahun, a.harga_beli, a.entitas_pembelian, a.penanggung_jawab, a.keterangan,
                k.nama_kategori, sk.nama_sub_kategori, m.nama_merk, t.nama_tipe, l.nama_lokasi,
                CASE 
                    WHEN soh.opname_at IS NOT NULL THEN 'Sudah Dicek'
                    ELSE 'Belum Dicek'
                END as status_verifikasi,
                soh.opname_at as tanggal_verifikasi,
                u.full_name as diverifikasi_oleh
            FROM aset a
            LEFT JOIN kategori k ON k.id = a.kategori_id
            LEFT JOIN sub_kategori sk ON sk.id = a.sub_kategori_id
            LEFT JOIN merk m ON m.id = a.merk_id
            LEFT JOIN tipe t ON t.id = a.tipe_id
            LEFT JOIN lokasi l ON l.id = a.lokasi_id
            LEFT JOIN (
                SELECT aset_id, opname_at, user_id FROM stock_opname_history 
                WHERE opname_at >= ? AND opname_at <= ?
            ) soh ON a.id = soh.aset_id
            LEFT JOIN users u ON u.id = soh.user_id
            WHERE a.deleted_at IS NULL
            ORDER BY a.kode ASC
        ";
        
        $asets = $db->query($query, [$cycle->start_date, $cycle->end_date])->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Stock Opname');

        // Header Tabel
        $headers = [
            'Kode Aset', 'Kategori', 'Sub Kategori', 'Merk', 'Tipe', 'Serial Number', 'Tahun', 'Lokasi', 'Penanggung Jawab', 
            'Harga Beli', 'Entitas Pembelian', 'Keterangan', 'Status Verifikasi', 'Tanggal Verifikasi', 'Diverifikasi Oleh'
        ];
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

        // Isi Tabel
        $row = 2;
        foreach ($asets as $aset) {
            $sheet->fromArray([
                $aset['kode'], $aset['nama_kategori'], $aset['nama_sub_kategori'], $aset['nama_merk'], $aset['nama_tipe'],
                $aset['serial_number'], $aset['tahun'], $aset['nama_lokasi'], $aset['penanggung_jawab'], $aset['harga_beli'],
                $aset['entitas_pembelian'], $aset['keterangan'], $aset['status_verifikasi'], $aset['tanggal_verifikasi'],
                $aset['diverifikasi_oleh']
            ], NULL, 'A' . $row);
            $row++;
        }
        
        // Atur lebar kolom otomatis
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Proses download file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_SO_' . date('Ymd_His', strtotime($cycle->start_date)) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
}