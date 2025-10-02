<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\KategoriModel; // Tambahkan ini
use App\Models\LokasiModel;

class StockOpnameController extends BaseController
{
    /**
     * Menampilkan halaman riwayat stock opname dengan filter.
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $lokasiModel = new LokasiModel();
        $asetModel = new AsetModel();

        $activeCycle = $db->table('stock_opname_cycles')->where('status', 'active')->orderBy('id', 'DESC')->get()->getRow();

        // --- Data Statistik untuk Dasbor Siklus ---
        $totalAset    = $asetModel->countAllResults();
        $sudahDicek   = $asetModel->where('status_verifikasi', 'Sudah Dicek')->countAllResults();
        $belumDicek   = $totalAset - $sudahDicek;
        $progress     = ($totalAset > 0) ? ($sudahDicek / $totalAset) * 100 : 0;

        // --- Logika Filter ---
        $filters = [
            'lokasi_id'         => $this->request->getGet('lokasi_id'),
            'status_verifikasi' => $this->request->getGet('status_verifikasi'),
            'kode'              => $this->request->getGet('kode'),
        ];

        $builder = $db->table('aset a')
            ->select('a.id, a.kode, a.status_verifikasi, l.nama_lokasi, sk.nama_sub_kategori, m.nama_merk')
            ->join('lokasi l', 'l.id = a.lokasi_id', 'left')
            ->join('sub_kategori sk', 'sk.id = a.sub_kategori_id', 'left')
            ->join('merk m', 'm.id = a.merk_id', 'left')
            ->where('a.deleted_at', null); // Hanya aset aktif

        if (!empty($filters['lokasi_id'])) {
            $builder->where('a.lokasi_id', $filters['lokasi_id']);
        }
        if (!empty($filters['status_verifikasi'])) {
            $builder->where('a.status_verifikasi', $filters['status_verifikasi']);
        }
        if (!empty($filters['kode'])) {
            $builder->like('a.kode', $filters['kode']); 
        }
        $asetList = $builder->orderBy('a.kode', 'ASC')->get()->getResultArray();

        $data = [
            'title'             => 'Dasbor Stock Opname',
            'activeCycle'       => $activeCycle,
            'lokasi_list'       => $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'totalAset'         => $totalAset,
            'sudahDicek'        => $sudahDicek,
            'belumDicek'        => $belumDicek,
            'progress'          => $progress,
            'asetList'          => $asetList, // Daftar aset untuk ditampilkan
            'filters'           => $filters,
        ];

        return view('stock_opname/index', $data);
    }

    /**
     * Menampilkan form untuk verifikasi/stock opname aset.
     * URL: /stockopname/aset/{id}
     */
    public function view($asetId)
    {
        $db = \Config\Database::connect();
        $userModel = new \App\Models\UserModel(); // Panggil UserModel

        // Cek 1: Apakah mode SO aktif secara global?
        $setting = $db->table('settings')->where('setting_key', 'stock_opname_mode')->get()->getRow();
        if (!$setting || $setting->setting_value !== 'on') {
            return view('stock_opname/inactive', ['title' => 'Akses Ditolak']);
        }

        // Cek 2: Apakah pengguna yang login punya izin?
        $currentUser = $userModel->find(session()->get('user_id'));
        if (!$currentUser || !$currentUser->can_perform_so) {
            return view('stock_opname/inactive', [
                'title' => 'Akses Ditolak',
                'message' => 'Anda tidak memiliki izin untuk melakukan Stock Opname. Silakan hubungi administrator.'
            ]);
        }

        // --- Jika semua pengecekan lolos, lanjutkan seperti biasa ---
        $asetModel = new AsetModel();

        $aset = $asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left')
            ->find($asetId);

        if (!$aset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Aset dengan ID ' . $asetId . ' tidak ditemukan.');
        }

        $lokasiModel = new LokasiModel();

        $data = [
            'title'       => 'Verifikasi Aset: ' . esc($aset['kode']),
            'aset'        => $aset,
            'lokasi_list' => $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
        ];

        return view('stock_opname/form', $data);
    }

    /**
     * Memproses data dari form stock opname.
     */
    public function process($asetId)
    {
        $asetModel = new AsetModel();
        $db = \Config\Database::connect();

        $asetAsli = $asetModel->find($asetId);
        if (!$asetAsli) {
            return redirect()->to('/dashboard')->with('error', 'Aset tidak ditemukan.');
        }

        $dataForm = [
            'lokasi_id'  => $this->request->getPost('lokasi_id'),
            'status'     => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        $perubahan = [];
        if ($asetAsli['lokasi_id'] != $dataForm['lokasi_id']) {
            $perubahan['lokasi_id'] = $dataForm['lokasi_id'];
        }
        if ($asetAsli['status'] != $dataForm['status']) {
            $perubahan['status'] = $dataForm['status'];
        }
        if ($asetAsli['keterangan'] != $dataForm['keterangan']) {
            $perubahan['keterangan'] = $dataForm['keterangan'];
        }
        
        $adaPerubahan = !empty($perubahan);

        $historyData = [
            'aset_id'       => $asetId,
            'user_id'       => session()->get('user_id'),
            'opname_at'     => date('Y-m-d H:i:s'),
            'catatan'       => $dataForm['keterangan'],
            'ada_perubahan' => $adaPerubahan,
        ];
        
        if ($adaPerubahan) {
            $historyData['data_sebelum'] = json_encode([
                'lokasi_id'  => $asetAsli['lokasi_id'],
                'status'     => $asetAsli['status'],
                'keterangan' => $asetAsli['keterangan'],
            ]);
            $historyData['data_sesudah'] = json_encode($perubahan);
        }

        $db->table('stock_opname_history')->insert($historyData);

        if ($adaPerubahan) {
            $requestData = [
                'aset_id'       => $asetId,
                'user_id'       => session()->get('user_id'),
                'proposed_data' => json_encode($perubahan),
                'status'        => 'pending',
                'created_at'    => date('Y-m-d H:i:s'),
            ];
            $db->table('aset_update_requests')->insert($requestData);
        }

        $pesan = $adaPerubahan 
            ? 'Verifikasi berhasil. Usulan perubahan Anda telah diajukan untuk persetujuan.'
            : 'Aset berhasil diverifikasi tanpa ada perubahan.';

        return redirect()->to('/dashboard')->with('success', $pesan);
    }
    public function export()
    {
        $db = \Config\Database::connect();

        $selectedKategori = $this->request->getGet('kategori_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $builder = $db->table('stock_opname_history as soh')
            ->select('
                a.kode as kode_aset, 
                sk.nama_sub_kategori, 
                m.nama_merk,
                t.nama_tipe,
                l.nama_lokasi,
                u.full_name, 
                soh.opname_at, 
                soh.ada_perubahan, 
                soh.catatan
            ')
            ->join('users as u', 'u.id = soh.user_id')
            ->join('aset as a', 'a.id = soh.aset_id')
            ->join('sub_kategori as sk', 'sk.id = a.sub_kategori_id', 'left')
            ->join('merk as m', 'm.id = a.merk_id', 'left')
            ->join('tipe as t', 't.id = a.tipe_id', 'left')
            ->join('lokasi as l', 'l.id = a.lokasi_id', 'left')
            ->orderBy('soh.opname_at', 'DESC');

        if ($selectedKategori) {
            $builder->where('a.kategori_id', $selectedKategori);
        }
        if ($startDate && $endDate) {
            $builder->where('soh.opname_at >=', $startDate . ' 00:00:00');
            $builder->where('soh.opname_at <=', $endDate . ' 23:59:59');
        }

        $history = $builder->get()->getResultArray();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Kode Aset');
        $sheet->setCellValue('B1', 'Sub Kategori');
        $sheet->setCellValue('C1', 'Merk');
        $sheet->setCellValue('D1', 'Tipe');
        $sheet->setCellValue('E1', 'Lokasi Terakhir');
        $sheet->setCellValue('F1', 'Diverifikasi Oleh');
        $sheet->setCellValue('G1', 'Tanggal Verifikasi');
        $sheet->setCellValue('H1', 'Status Verifikasi');
        $sheet->setCellValue('I1', 'Catatan');

        $row = 2;
        foreach ($history as $item) {
            $sheet->setCellValue('A' . $row, $item['kode_aset']);
            $sheet->setCellValue('B' . $row, $item['nama_sub_kategori']);
            $sheet->setCellValue('C' . $row, $item['nama_merk']);
            $sheet->setCellValue('D' . $row, $item['nama_tipe']);
            $sheet->setCellValue('E' . $row, $item['nama_lokasi']);
            $sheet->setCellValue('F' . $row, $item['full_name']);
            $sheet->setCellValue('G' . $row, date('d M Y H:i', strtotime($item['opname_at'])));
            $sheet->setCellValue('H' . $row, $item['ada_perubahan'] ? 'Ada Usulan Perubahan' : 'Data Sesuai');
            $sheet->setCellValue('I' . $row, $item['catatan']);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan_stock_opname_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    /**
     * Menampilkan halaman Scan Cepat.
     */
    public function scanCepat()
    {
        $db = \Config\Database::connect();
        $userModel = new \App\Models\UserModel();

        // Cek 1: Apakah mode SO aktif secara global?
        $setting = $db->table('settings')->where('setting_key', 'stock_opname_mode')->get()->getRow();
        if (!$setting || $setting->setting_value !== 'on') {
            return view('stock_opname/inactive', ['title' => 'Akses Ditolak']);
        }

        // Cek 2: Apakah pengguna yang login punya izin?
        $currentUser = $userModel->find(session()->get('user_id'));
        if (!$currentUser || !$currentUser->can_perform_so) {
            return view('stock_opname/inactive', [
                'title' => 'Akses Ditolak',
                'message' => 'Anda tidak memiliki izin untuk melakukan Stock Opname. Silakan hubungi administrator.'
            ]);
        }
        $lokasiModel = new \App\Models\LokasiModel();

        $data = [
            'title'       => 'Scan Cepat Stock Opname',
            'lokasi_list' => $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(), // [BARU] Kirim daftar lokasi
        ];
        return view('stock_opname/scan_cepat', $data);
    }

    /**
     * API Endpoint untuk mendapatkan info verifikasi terakhir.
     * --- FUNGSI INI DIPERBARUI & DISEMPURNAKAN ---
     */
    public function getLastVerificationInfo($asetId = null)
    {
        if ($asetId === null) {
            return $this->response->setStatusCode(400, 'Aset ID tidak disediakan.');
        }

        try {
            $db = \Config\Database::connect();
            $asetModel = new \App\Models\AsetModel();

            // 1. Ambil detail dasar aset
            $aset = $asetModel
                ->select('aset.kode, aset.status_verifikasi, sk.nama_sub_kategori, m.nama_merk, l.nama_lokasi')
                ->join('sub_kategori as sk', 'sk.id = aset.sub_kategori_id', 'left')
                ->join('merk as m', 'm.id = aset.merk_id', 'left')
                ->join('lokasi as l', 'l.id = aset.lokasi_id', 'left')
                ->where('aset.id', $asetId)
                ->first();                  

            if (!$aset) {
                return $this->response->setJSON(['status' => 'asset_not_found']);
            }


            // 3. Ambil riwayat verifikasi TERAKHIR (tidak peduli kapan)
            $history = $db->table('stock_opname_history as soh')
                      ->select('soh.opname_at, u.full_name')
                      ->join('users as u', 'u.id = soh.user_id')
                      ->where('soh.aset_id', $asetId)
                      ->orderBy('soh.opname_at', 'DESC')
                      ->limit(1)
                      ->get()
                      ->getRow();
            
            // Siapkan data yang akan dikirim
            $dataToSend = [
                'kode'              => $aset['kode'],
                'nama_sub_kategori' => $aset['nama_sub_kategori'],
                'nama_merk'         => $aset['nama_merk'],
                'nama_lokasi'       => $aset['nama_lokasi'],
                'status_verifikasi' => $aset['status_verifikasi'],
                'opname_at'         => null, // Default
                'full_name'         => null, // Default
            ];

            $responseStatus = 'no_history';

            if ($history) {
                // Jika ADA riwayat, isi datanya
                $responseStatus = 'has_history';
                $dataToSend['opname_at'] = date('d M Y H:i', strtotime($history->opname_at));
                $dataToSend['full_name'] = $history->full_name;
            }
            
            // Kirim respons
            return $this->response->setJSON([
                'status' => $responseStatus,
                'data' => $dataToSend
            ]);

        } catch (\Throwable $e) {
            log_message('error', '[SCAN_CEPAT_ERROR] ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'server_error',
                'message' => 'Terjadi kesalahan internal pada server.'
            ]);
        }
    }

    /**
     * Memproses hasil scan cepat yang dikirim dari form.
     */
    public function processScan()
    {
        $assetIds = $this->request->getPost('asset_ids');

        if (empty($assetIds)) {
            return redirect()->to('/scan-cepat')->with('error', 'Tidak ada aset yang dipindai untuk diverifikasi.');
        }

        $db = \Config\Database::connect();
        $asetModel = new AsetModel(); // Panggil model Aset
        $processedCount = 0;
        $skippedCount = 0;
        $userId = session()->get('user_id');
        $now = date('Y-m-d H:i:s');

        foreach ($assetIds as $asetId) {
            // AMBIL DATA ASET TERBARU DARI DATABASE
            $aset = $asetModel->find($asetId);

            // LEWATI JIKA ASET TIDAK DITEMUKAN ATAU SUDAH DICEK
            if (!$aset || $aset['status_verifikasi'] === 'Sudah Dicek') {
                $skippedCount++;
                continue;
            }

            // UPDATE status verifikasi aset menjadi 'Sudah Dicek'
            $db->table('aset')->where('id', $asetId)->update(['status_verifikasi' => 'Sudah Dicek']);

            // Catat ke riwayat
            $historyData = [
                'aset_id'       => $asetId,
                'user_id'       => $userId,
                'opname_at'     => $now,
                'catatan'       => 'Diverifikasi via Scan Cepat.',
                'ada_perubahan' => false,
            ];

            $db->table('stock_opname_history')->insert($historyData);
            $processedCount++;
        }

        $message = $processedCount . ' aset berhasil diverifikasi.';
        if ($skippedCount > 0) {
            $message .= ' ' . $skippedCount . ' aset dilewati karena sudah diverifikasi sebelumnya dalam siklus ini.';
        }

        return redirect()->to('/dashboard')->with('success', $message);
    }
    /**
     * API Endpoint untuk mengambil daftar aset berdasarkan lokasi
     * untuk halaman Scan Cepat.
     */
    public function getAsetByLocation($locationId = null)
    {
        if ($locationId === null) {
            return $this->response->setStatusCode(400, 'ID Lokasi dibutuhkan.');
        }

        $asetModel = new \App\Models\AsetModel();

        // Ambil semua aset di lokasi tersebut
        $asets = $asetModel
            ->select('id, kode, status_verifikasi')
            ->where('lokasi_id', $locationId)
            ->where('deleted_at IS NULL') // Hanya aset yang aktif
            ->orderBy('kode', 'ASC')
            ->findAll();

        return $this->response->setJSON($asets);
    }

    /**
     * [KHUSUS ADMIN] Memulai siklus stock opname baru dengan mereset status
     * verifikasi semua aset menjadi 'Belum Dicek'.
     */
    public function startCycle()
    {
        $db = \Config\Database::connect();
        $userId = session()->get('user_id');

        $db->table('stock_opname_cycles')
           ->where('status', 'active')
           ->set(['status' => 'completed', 'end_date' => date('Y-m-d H:i:s')])
           ->update();

        $db->table('stock_opname_cycles')->insert([
            'start_date'         => date('Y-m-d H:i:s'),
            'started_by_user_id' => $userId,
            'status'             => 'active',
        ]);

        $db->table('aset')->update(['status_verifikasi' => 'Belum Dicek']);

        return redirect()->to('/stockopname')->with('success', 'Siklus Stock Opname baru telah dimulai!');
    }
}