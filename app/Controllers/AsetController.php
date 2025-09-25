<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel; // TAMBAHKAN
use App\Models\TipeModel; // TAMBAHKAN
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */
class AsetController extends ResourceController
{
    protected $asetModel;
    protected $kategoriModel;
    protected $subKategoriModel;
    protected $lokasiModel; // Deklarasikan
    protected $merkModel; // TAMBAHKAN
    protected $tipeModel; // TAMBAHKAN
    protected $modelName = 'App\Models\AsetModel';
    
    public function __construct()
    {
        $this->asetModel = new AsetModel();
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->lokasiModel = new LokasiModel();
        $this->merkModel = new MerkModel(); // TAMBAHKAN
        $this->tipeModel = new TipeModel(); // TAMBAHKAN
    }

    private function generateUniqueAssetCode($entitas, $tahun, $subKategoriId, $merkId)
    {
        // 1. Ambil data terakhir berdasarkan kata kunci
        $lastAsset = $this->asetModel
            ->where('entitas_pembelian', $entitas)
            ->where('sub_kategori_id', $subKategoriId)
            ->where('tahun', $tahun)
            ->orderBy('kode', 'DESC')
            ->first();
    
        $nextUniqueNumber = 1;
    
        // 2. Jika data ada, ambil nomor terakhir dan increment
        if ($lastAsset) {
            $parts = explode('/', $lastAsset['kode']);
            $lastUniqueNumber = (int)end($parts);
            $nextUniqueNumber = $lastUniqueNumber + 1;
        }
    
        // 3. Format nomor unik menjadi dua digit (e.g., 01, 02)
        $formattedUniqueNumber = str_pad($nextUniqueNumber, 2, '0', STR_PAD_LEFT);
    
        // Ambil singkatan dari master data
        $subKategoriInfo = $this->subKategoriModel->find($subKategoriId);
        $merkInfo = $this->merkModel->find($merkId);
        $subKategoriCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $subKategoriInfo['nama_sub_kategori']), 0, 5));
        $merkCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $merkInfo['nama_merk']), 0, 3));
        $entitasCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $entitas), 0, 5));
    
    
        // 4. Gabungkan menjadi kode aset final
        $newCode = "BTR/{$entitasCode}/{$tahun}/{$subKategoriCode}/{$merkCode}/{$formattedUniqueNumber}";
    
        return $newCode;
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status'      => $this->request->getGet('status'),
            'keyword'     => $this->request->getGet('keyword'),
        ];
    
        $query = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left');
    
        if (!empty($filters['kategori_id'])) {
            $query = $query->where('aset.kategori_id', $filters['kategori_id']);
        }
        if (!empty($filters['status'])) {
            $query = $query->where('aset.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $query = $query->groupStart()
                ->like('aset.kode', $filters['keyword'])
                ->orLike('aset.merk', $filters['keyword'])
                ->orLike('aset.serial_number', $filters['keyword'])
                ->orLike('l.nama_lokasi', $filters['keyword']) // Pencarian berdasarkan nama lokasi
                ->groupEnd();
        }
        
        $asets_data = $query->orderBy('aset.updated_at', 'DESC')->findAll();
    
        $data = [
            'title'            => 'Data Aset',
            'asets'            => $asets_data,
            'kategori_list'    => $this->kategoriModel->findAll(),
            'subkategori_list' => $this->subKategoriModel->findAll(),
            'lokasi_list'      => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk_list'        => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(), // TAMBAHKAN
            'filters'          => $filters
        ];
    
        return view('aset/index', $data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
public function show($id = null)
{
    $aset = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left') // PASTIKAN JOIN INI ADA
            ->join('tipe t', 't.id = aset.tipe_id', 'left') // PASTIKAN JOIN INI ADA
            ->find($id);

        if ($aset) {
            $aset['updated_at'] = date('d F Y H:i:s', strtotime($aset['updated_at']));
            return $this->response->setJSON($aset);
        }
        
        return $this->response->setStatusCode(404, 'Aset tidak ditemukan');
}

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $serialNumber = $this->request->getPost('serial_number');

        if (!empty($serialNumber)) {
            $existingAset = $this->asetModel->where('serial_number', $serialNumber)->first();
            if ($existingAset) {
                $redirectPage = $this->request->getPost('redirect_to');
                $redirectTo = ($redirectPage === 'dashboard') ? '/dashboard' : '/aset';

                return redirect()->to($redirectTo)
                                 ->with('error', 'Gagal! Serial Number sudah terdaftar pada aset lain.')
                                 ->with('conflicting_asset_id', $existingAset['id']);
            }
        }
        
        $entitas = $this->request->getPost('entitas_pembelian');
        $tahun = $this->request->getPost('tahun');
        $subKategoriId = $this->request->getPost('sub_kategori_id');
        $merkId = $this->request->getPost('merk_id');
    
        // Hasilkan kode aset baru di sisi server
        $newAssetCode = $this->generateUniqueAssetCode($entitas, $tahun, $subKategoriId, $merkId);

        $data = [
            'kode'            => $newAssetCode,
            'kategori_id'     => $this->request->getPost('kategori_id'),
            'sub_kategori_id' => $subKategoriId,
            'merk_id'         => $merkId,
            'tipe_id'         => $this->request->getPost('tipe_id'),
            'serial_number'   => $this->request->getPost('serial_number'),
            'tahun'           => $tahun,
            'lokasi_id'       => $this->request->getPost('lokasi_id'), 
            'status'          => $this->request->getPost('status'),
            'keterangan'      => $this->request->getPost('keterangan'),
            'harga_beli'      => $this->request->getPost('harga_beli'),
            'entitas_pembelian' => strtoupper($entitas),
        ];

        if ($this->asetModel->save($data)) {
            $newAsetId = $this->asetModel->getInsertID();

            $url = base_url('tracking/aset/' . $newAsetId);
            if (!is_dir(FCPATH . 'qrcodes')) {
                mkdir(FCPATH . 'qrcodes', 0777, true);
            }
            $qrCode = QrCode::create($url);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodePath = 'qrcodes/aset-' . $newAsetId . '.png';
            $result->saveToFile(FCPATH . $qrCodePath);
            $this->asetModel->update($newAsetId, ['qrcode' => $qrCodePath]);

            $redirectPage = $this->request->getPost('redirect_to');
            $redirectTo = ($redirectPage === 'dashboard') ? '/dashboard' : '/aset';
            
            // AMBIL DETAIL ASET YANG LENGKAP DENGAN JOIN
            $newAset = $this->asetModel
                ->select('aset.*, sk.nama_sub_kategori')
                ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                ->find($newAsetId);

            return redirect()->to($redirectTo)
                             ->with('success', 'Aset baru berhasil ditambahkan!')
                             ->with('new_aset', $newAset);
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan aset.');
        }
    }

    public function getDetail($id = null)
    {
        $aset = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->find($id);

        if ($aset) {
            $aset['updated_at'] = date('d F Y H:i:s', strtotime($aset['updated_at']));
            return $this->response->setJSON($aset);
        }
        
        return $this->response->setStatusCode(404, 'Aset tidak ditemukan');
    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        $aset = $this->asetModel->find($id);
        if (!$aset) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan.');
        }

        $data = [
            'title'            => 'Edit Aset',
            'aset'             => $aset,
            'kategori_list'    => $this->kategoriModel->findAll(),
            'subkategori_list' => $this->subKategoriModel->where('kategori_id', $aset['kategori_id'])->findAll(),
            'lokasi_list'      => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk_list'        => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(),
            'tipe_list'        => $this->tipeModel->where('merk_id', $aset['merk_id'])->findAll(),
        ];

        return view('aset/edit', $data);
    }    

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $allowedFields = [
            'kategori_id',
            'sub_kategori_id',
            'merk_id',
            'tipe_id',
            'tahun',
            'lokasi_id', 
            'status',
            'keterangan',
            'harga_beli',
            'entitas_pembelian',
        ];

        $data = $this->request->getPost($allowedFields);

        if (!empty($data['entitas_pembelian'])) {
            $data['entitas_pembelian'] = strtoupper($data['entitas_pembelian']);
        }

        if ($this->asetModel->update($id, $data)) {
            return redirect()->to('/aset')->with('success', 'Data aset berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data aset.');

    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        if ($this->asetModel->delete($id)) {
            return redirect()->to('/aset')->with('success', 'Aset berhasil dihapus.');
        } else {
            return redirect()->to('/aset')->with('error', 'Gagal menghapus aset.');
        }
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        
        $query = $this->asetModel
                        ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
                        ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                        ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left');

        if ($keyword) {
            $query = $query->groupStart()
                        ->like('aset.kode', $keyword)
                        ->orLike('k.nama_kategori', $keyword)
                        ->orLike('sk.nama_sub_kategori', $keyword)
                        ->orLike('aset.merk', $keyword)
                        ->orLike('aset.serial_number', $keyword)
                        ->orLike('aset.lokasi', $keyword)
                        ->groupEnd();
        }

        $results = $query->findAll();

        return $this->response->setJSON($results);
    }

    public function publicDetail($id)
    {
        $data = [
            'title' => 'Detail Aset',
            'aset'  => $this->asetModel
                                ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
                                ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                                ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                                ->find($id),
        ];

        return view('aset/public_detail', $data);
    }

    public function export()
    {
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status'      => $this->request->getGet('status'),
            'keyword'     => $this->request->getGet('keyword'),
        ];
        
        $query = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left');

        if (!empty($filters['kategori_id'])) {
            $query->where('aset.kategori_id', $filters['kategori_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('aset.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $query->groupStart()
                  ->like('aset.kode', $filters['keyword'])
                  ->orLike('aset.merk', $filters['keyword'])
                  ->orLike('aset.lokasi', $filters['keyword'])
                  ->orLike('k.nama_kategori', $filters['keyword'])
                  ->orLike('sk.nama_sub_kategori', $filters['keyword'])
                  ->groupEnd();
        }

        $asets = $query->orderBy('aset.updated_at', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Kode Aset');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Sub Kategori');
        $sheet->setCellValue('D1', 'Merk');
        $sheet->setCellValue('E1', 'Type');
        $sheet->setCellValue('F1', 'Serial Number');
        $sheet->setCellValue('G1', 'Tahun');
        $sheet->setCellValue('H1', 'Lokasi');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Keterangan');
        $sheet->setCellValue('K1', 'Harga Beli');
        $sheet->setCellValue('L1', 'Entitas Pembelian');
        $sheet->setCellValue('M1', 'Terakhir Diperbarui');
        
        $styleArray = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']]
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);

        $row = 2;
        foreach ($asets as $aset) {
            $sheet->setCellValue('A' . $row, $aset['kode']);
            $sheet->setCellValue('B' . $row, $aset['nama_kategori']);
            $sheet->setCellValue('C' . $row, $aset['nama_sub_kategori']);
            $sheet->setCellValue('D' . $row, $aset['merk']);
            $sheet->setCellValue('E' . $row, $aset['type']);
            $sheet->setCellValue('F' . $row, $aset['serial_number']);
            $sheet->setCellValue('G' . $row, $aset['tahun']);
            $sheet->setCellValue('H' . $row, $aset['lokasi']);
            $sheet->setCellValue('I' . $row, $aset['status']);
            $sheet->setCellValue('J' . $row, $aset['keterangan']);
            $sheet->setCellValue('K' . $row, $aset['harga_beli']);
            $sheet->setCellValue('L' . $row, $aset['entitas_pembelian']);
            $sheet->setCellValue('M' . $row, $aset['updated_at']);
            $row++;
        }
        
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_aset_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    public function getHistory($asetId)
    {
        $db = \Config\Database::connect();
        $history = $db->table('aset_update_requests as aur')
                        ->select('aur.created_at, aur.proposed_data, u.full_name')
                        ->join('users as u', 'u.id = aur.user_id')
                        ->where('aur.aset_id', $asetId)
                        ->where('aur.status', 'approved')
                        ->orderBy('aur.created_at', 'DESC')
                        ->get()->getResultArray();

        return $this->response->setJSON($history);
    }
    
    public function exportBulanan($bulan)
    {
        if ($bulan < 1 || $bulan > 12) {
            return redirect()->to('/dashboard')->with('error', 'Bulan yang dipilih tidak valid.');
        }

        $tahunIni = date('Y');

        $asets = $this->asetModel
                        ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori')
                        ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                        ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                        ->where('MONTH(aset.updated_at)', $bulan)
                        ->where('YEAR(aset.updated_at)', $tahunIni)
                        ->orderBy('aset.updated_at', 'DESC')
                        ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Kode Aset');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Sub Kategori');
        $sheet->setCellValue('D1', 'Merk');
        $sheet->setCellValue('E1', 'Type');
        $sheet->setCellValue('F1', 'Serial Number');
        $sheet->setCellValue('G1', 'Tahun');
        $sheet->setCellValue('H1', 'Lokasi');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'Keterangan');
        $sheet->setCellValue('K1', 'Harga Beli');
        $sheet->setCellValue('L1', 'Entitas Pembelian');
        $sheet->setCellValue('M1', 'Terakhir Diperbarui');
        
        $styleArray = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']]
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($styleArray);

        $row = 2;
        foreach ($asets as $aset) {
            $sheet->setCellValue('A' . $row, $aset['kode']);
            $sheet->setCellValue('B' . $row, $aset['nama_kategori']);
            $sheet->setCellValue('C' . $row, $aset['nama_sub_kategori']);
            $sheet->setCellValue('D' . $row, $aset['merk']);
            $sheet->setCellValue('E' . $row, $aset['type']);
            $sheet->setCellValue('F' . $row, $aset['serial_number']);
            $sheet->setCellValue('G' . $row, $aset['tahun']);
            $sheet->setCellValue('H' . $row, $aset['lokasi']);
            $sheet->setCellValue('I' . $row, $aset['status']);
            $sheet->setCellValue('J' . $row, $aset['keterangan']);
            $sheet->setCellValue('K' . $row, $aset['harga_beli']);
            $sheet->setCellValue('L' . $row, $aset['entitas_pembelian']);
            $sheet->setCellValue('M' . $row, $aset['updated_at']);
            $row++;
        }
        
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        $namaBulan = date('F', mktime(0, 0, 0, $bulan, 10));
        $filename = 'laporan_aset_' . strtolower($namaBulan) . '_' . $tahunIni . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function getTipesByMerk($merkId)
    {
        $tipes = $this->tipeModel->where('merk_id', $merkId)->orderBy('nama_tipe', 'ASC')->findAll();
        return $this->response->setJSON($tipes);
    }
}