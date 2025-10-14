<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel; 
use App\Models\TipeModel; 
use App\Models\DokumentasiAsetModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

use Dompdf\Dompdf;
use App\Models\KaryawanModel;



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
    protected $dokumentasiAsetModel;
    protected $modelName = 'App\Models\AsetModel';
    
    public function __construct()
    {
        $this->asetModel = new AsetModel();
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->lokasiModel = new LokasiModel();
        $this->merkModel = new MerkModel(); // TAMBAHKAN
        $this->tipeModel = new TipeModel(); // TAMBAHKAN
        $this->dokumentasiAsetModel = new DokumentasiAsetModel();
    }

    private function generateUniqueAssetCode($tahunBeli, $subKategoriId, $merkId)
    {
        $subKategoriModel = new \App\Models\SubKategoriModel();
        $merkModel = new \App\Models\MerkModel();

        $subKategori = $subKategoriModel->find($subKategoriId);
        $merk = $merkModel->find($merkId);

        $kodeSubKategori = strtoupper(substr($subKategori['nama_sub_kategori'], 0, 5));
        $kodeMerk = strtoupper(substr($merk['nama_merk'], 0, 3));
        $kodeSubKategoriMerk = $kodeSubKategori . '_' . $kodeMerk;

        // --- PERUBAHAN LOGIKA UTAMA ADA DI SINI ---
        // Mencari aset terakhir berdasarkan kombinasi TAHUN, SUB KATEGORI, dan MERK
        // Ini adalah kunci baru untuk menentukan nomor urut.
        $lastAsset = $this->asetModel
            ->where('tahun_beli', $tahunBeli)
            ->where('sub_kategori_id', $subKategoriId)
            ->where('merk_id', $merkId)
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = 1;
        if ($lastAsset) {
            $parts = explode('/', $lastAsset['kode_aset']);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber + 1;
        }

        $nextNumberFormatted = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return 'BTR/' . $tahunBeli . '/' . $kodeSubKategoriMerk . '/' . $nextNumberFormatted;
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

        // [BAGIAN QUERY ANDA]
        // Kita tambahkan join ke tabel karyawan untuk mendapatkan nama, bukan hanya ID
        $query = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe, karyawan.nama_karyawan')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left')
            ->join('karyawan', 'karyawan.id = aset.user_pengguna', 'left'); // Join ke tabel karyawan

        if (!empty($filters['kategori_id'])) {
            $query = $query->where('aset.kategori_id', $filters['kategori_id']);
        }
        if (!empty($filters['status'])) {
            $query = $query->where('aset.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $query = $query->groupStart()
                ->like('aset.kode', $filters['keyword'])
                ->orLike('m.nama_merk', $filters['keyword'])
                ->orLike('aset.serial_number', $filters['keyword'])
                ->orLike('l.nama_lokasi', $filters['keyword'])
                ->orLike('karyawan.nama_karyawan', $filters['keyword']) // Pencarian berdasarkan nama karyawan
                ->orLike('aset.entitas_pembelian', $filters['keyword'])
                ->groupEnd();
        }
        
        // [PENGAMBILAN DATA BARU] Menggunakan paginate() untuk performa
        $asets_data = $query->orderBy('aset.updated_at', 'DESC')->findAll();

        // ======================= PENINGKATAN DIMULAI DI SINI =======================
        // Jika ada aset yang ditemukan di halaman ini, ambil dokumennya
        if (!empty($asets_data)) {
            // 1. Kumpulkan semua ID aset dari hasil paginasi
            $asetIds = array_column($asets_data, 'id');

            // 2. Ambil semua dokumen yang terkait dalam SATU query
            $dokumentasiModel = new DokumentasiAsetModel();
            // Disini kita pastikan menggunakan nama kolom yang benar yaitu 'aset_id'
            $allDokumen = $dokumentasiModel->whereIn('aset_id', $asetIds)->findAll();

            // 3. Buat "peta" dokumen agar mudah dicari
            $dokumenMap = [];
            foreach ($allDokumen as $dokumen) {
                // Disini juga, gunakan 'aset_id' sebagai key
                $dokumenMap[$dokumen['aset_id']][] = $dokumen;
            }

            // 4. Lampirkan array dokumen ke setiap aset
            foreach ($asets_data as &$aset) { // tanda '&' penting
                $aset['dokumen'] = $dokumenMap[$aset['id']] ?? [];
            }
            unset($aset); // Hapus referensi setelah loop selesai
        }
        // ======================== PENINGKATAN SELESAI ========================

        $data = [
            'title'            => 'Data Aset',
            'asets'            => $asets_data, // Sekarang sudah ada sub-array 'dokumen'
            'kategori_list'    => $this->kategoriModel->findAll(),
            'subkategori_list' => $this->subKategoriModel->findAll(), // Anda menggunakan ini di view asli
            'lokasi_list'      => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk_list'        => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(),
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
                ->join('merk m', 'm.id = aset.merk_id', 'left') 
                ->join('tipe t', 't.id = aset.tipe_id', 'left') 
                ->find($id);

        if ($aset) {
            $dokumentasi = $this->dokumentasiAsetModel->where('aset_id', $id)->findAll();
            $aset['dokumentasi'] = $dokumentasi;
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
        
        // Variabel-variabel yang dibutuhkan untuk generate kode
        $tahun_beli = $this->request->getPost('tahun_beli');
        $subKategoriId = $this->request->getPost('sub_kategori_id');
        $merkId = $this->request->getPost('merk_id');

        // --- PERUBAHAN DI SINI ---
        // Panggil fungsi generateUniqueAssetCode TANPA menyertakan entitas pembelian.
        $newAssetCode = $this->generateUniqueAssetCode($tahun_beli, $subKategoriId, $merkId);

        // Variabel entitas tetap diambil untuk disimpan ke database, tapi tidak dipakai untuk generate kode.
        $entitas = $this->request->getPost('entitas_pembelian');

        $data = [
            'kode'                => $newAssetCode, // Gunakan kode aset yang baru
            'kategori_id'         => $this->request->getPost('kategori_id'),
            'sub_kategori_id'     => $subKategoriId,
            'merk_id'             => $merkId,
            'tipe_id'             => $this->request->getPost('tipe_id'),
            'serial_number'       => !empty($this->request->getPost('serial_number')) ? $this->request->getPost('serial_number') : null,
            'tahun_beli'          => $tahun_beli,
            'lokasi_id'           => $this->request->getPost('lokasi_id'), 
            'status'              => $this->request->getPost('status'),
            'keterangan'          => $this->request->getPost('keterangan'),
            'harga_beli'          => $this->request->getPost('harga_beli'),
            'entitas_pembelian'   => !empty($entitas) ? strtoupper($entitas) : null,
            'user_pengguna'       => !empty($this->request->getPost('user_pengguna')) ? strtoupper($this->request->getPost('user_pengguna')) : null
        ];

        if ($this->asetModel->save($data)) {
            $newAsetId = $this->asetModel->getInsertID();
            // --- AWAL KODE BARU UNTUK PROSES UPLOAD ---
            $files = $this->request->getFiles('bukti_aset');
            $uploadedFilesCount = 0; // Menghitung file yang berhasil diupload

            if (isset($files['bukti_aset'])) {
                foreach ($files['bukti_aset'] as $file) {
                    if ($file->isValid() && !$file->hasMoved() && $uploadedFilesCount < 2) {
                        // Validasi ukuran dan tipe
                        if ($file->getSize() <= 2048000 && in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf'])) {
                            $newName = $file->getRandomName();
                            $file->move(WRITEPATH . 'uploads/aset_bukti', $newName);

                            $this->dokumentasiAsetModel->save([
                                'aset_id'        => $newAsetId,
                                'path_file'      => $newName, // Hanya simpan nama file
                                'nama_asli_file' => $file->getClientName(),
                                'tipe_file'      => $file->getClientMimeType(),
                            ]);
                            $uploadedFilesCount++;
                        }
                    }
                }
            }
            $url = base_url('aset/info/' . $newAsetId);
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

        $karyawanModel = new KaryawanModel();

        $dokumentasi = $this->dokumentasiAsetModel->where('aset_id', $id)->findAll();
        $data = [
            'title'            => 'Edit Aset',
            'aset'             => $aset,
            'kategori_list'    => $this->kategoriModel->findAll(),
            'subkategori_list' => $this->subKategoriModel->where('kategori_id', $aset['kategori_id'])->findAll(),
            'lokasi_list'      => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk_list'        => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(),
            'tipe_list'        => $this->tipeModel->where('merk_id', $aset['merk_id'])->findAll(),
            'dokumentasi'      => $dokumentasi,
            'karyawan_list'    => $karyawanModel->orderBy('nama_karyawan', 'ASC')->findAll(), 
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
            'tahun_beli',
            'lokasi_id', 
            'status',
            'keterangan',
            'harga_beli',
            'entitas_pembelian',
            'user_pengguna',
        ];

        $data = $this->request->getPost($allowedFields);
        // --- AWAL KODE BARU UNTUK PROSES UPLOAD SAAT UPDATE ---
        $existingDocsCount = $this->dokumentasiAsetModel->where('aset_id', $id)->countAllResults();
        $files = $this->request->getFiles('bukti_aset');
        $uploadedFilesCount = 0;

        if (isset($files['bukti_aset'])) {
            foreach ($files['bukti_aset'] as $file) {
                if ($file->isValid() && !$file->hasMoved() && ($existingDocsCount + $uploadedFilesCount) < 2) {
                    if ($file->getSize() <= 2048000 && in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf'])) {
                        $newName = $file->getRandomName();
                        $file->move(WRITEPATH . 'uploads/aset_bukti', $newName);

                        $this->dokumentasiAsetModel->save([
                            'aset_id'        => $id,
                            'path_file'      => $newName, // Hanya simpan nama file
                            'nama_asli_file' => $file->getClientName(),
                            'tipe_file'      => $file->getClientMimeType(),
                        ]);
                        $uploadedFilesCount++;
                    }
                }
            }
        }
        if (!empty($data['entitas_pembelian'])) {
            $data['entitas_pembelian'] = strtoupper($data['entitas_pembelian']);
        }
        

    // Ambil data aset sebelum diubah untuk perbandingan
    $asetSebelumnya = $this->asetModel->find($id);
    $statusSebelumnya = $asetSebelumnya['status'] ?? null;
    $statusSekarang = $data['status'] ?? null;

    // Ambil ID pihak kedua dari form
    $pihakKeduaId = $this->request->getPost('pihak_kedua_id');

    // --- LOGIKA BARU UNTUK UPDATE USER PENGGUNA ---
    // Cek jika status berubah dari 'Baik Tidak Terpakai' ke 'Baik Terpakai' DAN pihak kedua dipilih
    if ($statusSebelumnya === 'Baik Tidak Terpakai' && $statusSekarang === 'Baik Terpakai' && !empty($pihakKeduaId)) {
        $karyawanModel = new KaryawanModel();
        $pihakKedua = $karyawanModel->find($pihakKeduaId);

        if ($pihakKedua) {
            // Ganti nilai 'user_pengguna' di data yang akan disimpan
            $data['user_pengguna'] = $pihakKedua['nama_karyawan'];
        }
    }
    // --- AKHIR LOGIKA BARU ---

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
                        ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, m.nama_merk, l.nama_lokasi')
                        ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                        ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                        ->join('merk m', 'm.id = aset.merk_id', 'left')
                        ->join('lokasi l', 'l.id = aset.lokasi_id', 'left');

        if ($keyword) {
            $query = $query->groupStart()
                        ->like('aset.kode', $keyword)
                        ->orLike('k.nama_kategori', $keyword)
                        ->orLike('sk.nama_sub_kategori', $keyword)
                        ->orLike('m.nama_merk', $keyword)       
                        ->orLike('aset.serial_number', $keyword)
                        ->orLike('l.nama_lokasi', $keyword)    
                        ->groupEnd();
        }

        $results = $query->findAll();

        return $this->response->setJSON($results);
    }

    public function publicDetail($id)
    {
        $asetData = $this->asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left')
            ->find($id);

        $data = [
            'title' => 'Detail Aset',
            'aset'  => $asetData,
        ];

        // Cek jika aset tidak ditemukan
        if (empty($data['aset'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Aset dengan ID ' . $id . ' tidak ditemukan.');
        }

        return view('aset/public_detail', $data);
    }

    // app/Controllers/AsetController.php

public function export()
    {
        $filters = [
            'kategori_id' => $this->request->getGet('kategori_id'),
            'status'      => $this->request->getGet('status'),
            'keyword'     => $this->request->getGet('keyword'),
        ];
        
        // Query yang sudah diperbaiki dengan join ke semua tabel yang relevan
        $query = $this->asetModel
            ->select('
                aset.kode, aset.serial_number, aset.tahun_beli, aset.status, aset.keterangan,
                aset.harga_beli, aset.entitas_pembelian, aset.user_pengguna, aset.updated_at,
                k.nama_kategori,
                sk.nama_sub_kategori,
                m.nama_merk,
                t.nama_tipe,
                l.nama_lokasi
            ')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left');

        if (!empty($filters['kategori_id'])) {
            $query->where('aset.kategori_id', $filters['kategori_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('aset.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $query->groupStart()
                ->like('aset.kode', $filters['keyword'])
                ->orLike('m.nama_merk', $filters['keyword'])
                ->orLike('l.nama_lokasi', $filters['keyword'])
                ->orLike('sk.nama_sub_kategori', $filters['keyword'])
                ->orLike('aset.user_pengguna', $filters['keyword'])
                ->groupEnd();
        }

        $asets = $query->orderBy('aset.updated_at', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menyesuaikan header dengan data yang diambil
        $sheet->setCellValue('A1', 'Kode Aset');
        $sheet->setCellValue('B1', 'Kategori');
        $sheet->setCellValue('C1', 'Sub Kategori');
        $sheet->setCellValue('D1', 'Merk');
        $sheet->setCellValue('E1', 'Tipe');
        $sheet->setCellValue('F1', 'Serial Number');
        $sheet->setCellValue('G1', 'Tahun');
        $sheet->setCellValue('H1', 'Lokasi');
        $sheet->setCellValue('I1', 'Status');
        $sheet->setCellValue('J1', 'User Pengguna');
        $sheet->setCellValue('K1', 'Harga Beli');
        $sheet->setCellValue('L1', 'Entitas Pembelian');
        $sheet->setCellValue('M1', 'Keterangan');
        $sheet->setCellValue('N1', 'Terakhir Diperbarui');
        
        $styleArray = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']]
        ];
        $sheet->getStyle('A1:N1')->applyFromArray($styleArray);

        $row = 2;
        foreach ($asets as $aset) {
            $sheet->setCellValue('A' . $row, $aset['kode']);
            $sheet->setCellValue('B' . $row, $aset['nama_kategori']);
            $sheet->setCellValue('C' . $row, $aset['nama_sub_kategori']);
            $sheet->setCellValue('D' . $row, $aset['nama_merk']);
            $sheet->setCellValue('E' . $row, $aset['nama_tipe']);
            $sheet->setCellValue('F' . $row, $aset['serial_number']);
            $sheet->setCellValue('G' . $row, $aset['tahun_beli']);
            $sheet->setCellValue('H' . $row, $aset['nama_lokasi']);
            $sheet->setCellValue('I' . $row, $aset['status']);
            $sheet->setCellValue('J' . $row, $aset['user_pengguna']);
            $sheet->setCellValue('K' . $row, $aset['harga_beli']);
            $sheet->setCellValue('L' . $row, $aset['entitas_pembelian']);
            $sheet->setCellValue('M' . $row, $aset['keterangan']);
            $sheet->setCellValue('N' . $row, $aset['updated_at']);
            $row++;
        }
        
        foreach (range('A', 'N') as $col) {
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
        $lokasiModel = new LokasiModel(); // Inisialisasi model lokasi

        $history = $db->table('aset_update_requests as aur')
                        ->select('aur.created_at, aur.proposed_data, u.full_name')
                        ->join('users as u', 'u.id = aur.user_id')
                        ->where('aur.aset_id', $asetId)
                        ->where('aur.status', 'approved')
                        ->orderBy('aur.created_at', 'DESC')
                        ->get()->getResultArray();

        // Proses data untuk mengubah ID menjadi nama
        $processedHistory = [];
        foreach ($history as $item) {
            $proposed = json_decode($item['proposed_data'], true);
            $newProposed = [];

            foreach ($proposed as $key => $value) {
                if ($key === 'lokasi_id') {
                    // Cari nama lokasi berdasarkan ID
                    $lokasi = $lokasiModel->find($value);
                    $newProposed['Lokasi'] = $lokasi ? $lokasi['nama_lokasi'] : 'ID: ' . $value;
                } else {
                    // Untuk field lain, cukup rapikan namanya
                    $newProposed[ucfirst(str_replace('_', ' ', $key))] = $value;
                }
            }
            $item['proposed_data'] = json_encode($newProposed);
            $processedHistory[] = $item;
        }

        return $this->response->setJSON($processedHistory);
    }
    
    public function exportBulanan($bulan)
{
    if ($bulan < 1 || $bulan > 12) {
        return redirect()->to('/dashboard')->with('error', 'Bulan yang dipilih tidak valid.');
    }

    $tahunIni = date('Y');

    // ======================================================================
    // 1. PERBAIKAN PADA QUERY DATABASE
    // Menambahkan join ke tabel merk, tipe, dan lokasi
    // ======================================================================
    $asets = $this->asetModel
                    ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, m.nama_merk, t.nama_tipe, l.nama_lokasi')
                    ->join('kategori k', 'k.id = aset.kategori_id', 'left')
                    ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
                    ->join('merk m', 'm.id = aset.merk_id', 'left')
                    ->join('tipe t', 't.id = aset.tipe_id', 'left')
                    ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
                    ->where('MONTH(aset.updated_at)', $bulan)
                    ->where('YEAR(aset.updated_at)', $tahunIni)
                    ->orderBy('aset.updated_at', 'DESC')
                    ->findAll();

    // ======================================================================
    // 2. TAMBAHAN VALIDASI DATA
    // Mencegah download jika tidak ada data yang ditemukan
    // ======================================================================
    if (empty($asets)) {
        return redirect()->to('/dashboard')->with('error', 'Tidak ada data aset untuk diekspor pada bulan yang dipilih.');
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Kode Aset');
    $sheet->setCellValue('B1', 'Kategori');
    $sheet->setCellValue('C1', 'Sub Kategori');
    $sheet->setCellValue('D1', 'Merk');
    $sheet->setCellValue('E1', 'Tipe');
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
        // Menggunakan nama_merk, nama_tipe, dan nama_lokasi dari hasil join
        $sheet->setCellValue('D' . $row, $aset['nama_merk']);
        $sheet->setCellValue('E' . $row, $aset['nama_tipe']);
        $sheet->setCellValue('F' . $row, $aset['serial_number']);
        $sheet->setCellValue('G' . $row, $aset['tahun_beli']);
        $sheet->setCellValue('H' . $row, $aset['nama_lokasi']);
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

    // stockOpname
    public function getStockOpnameHistory($asetId)
    {
        $db = \Config\Database::connect();
        $history = $db->table('stock_opname_history as soh')
                        ->select('soh.opname_at, soh.catatan, u.full_name, soh.ada_perubahan')
                        ->join('users as u', 'u.id = soh.user_id')
                        ->where('soh.aset_id', $asetId)
                        ->orderBy('soh.opname_at', 'DESC')
                        ->get()->getResultArray();

        return $this->response->setJSON($history);
    }


public function barcodes()
{
    // Muat semua model yang dibutuhkan untuk filter
    $lokasiModel = new LokasiModel();
    $kategoriModel = new KategoriModel();
    $merkModel = new MerkModel();

    // Ambil nilai filter dari URL (GET request)
    $filters = [
        'kategori_id'       => $this->request->getGet('kategori_id'),
        'lokasi_id'         => $this->request->getGet('lokasi_id'),
        'merk_id'           => $this->request->getGet('merk_id'),
        'tahun_beli'             => $this->request->getGet('tahun_beli'),
        'user_pengguna'  => $this->request->getGet('user_pengguna'),
        'keyword'           => $this->request->getGet('keyword'),
    ];

    // ===== MEMBANGUN QUERY SECARA LANGSUNG (LEBIH EKSPLISIT) =====
    $db = db_connect();
    $builder = $db->table('aset');

    $builder->select('
        aset.kode, aset.qrcode, aset.tahun_beli, aset.user_pengguna, aset.entitas_pembelian, 
        k.nama_kategori, 
        sk.nama_sub_kategori, 
        l.nama_lokasi, 
        m.nama_merk, 
        t.nama_tipe
    ');
    $builder->join('kategori k', 'k.id = aset.kategori_id', 'left');
    $builder->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left');
    $builder->join('lokasi l', 'l.id = aset.lokasi_id', 'left');
    $builder->join('merk m', 'm.id = aset.merk_id', 'left');
    $builder->join('tipe t', 't.id = aset.tipe_id', 'left');
    $builder->where('aset.qrcode IS NOT NULL');
    $builder->where('aset.qrcode !=', '');
    $builder->where('aset.deleted_at IS NULL'); // Hanya ambil aset yang tidak dihapus

    // Terapkan semua filter
    if (!empty($filters['kategori_id'])) {
        $builder->where('aset.kategori_id', $filters['kategori_id']);
    }
    if (!empty($filters['lokasi_id'])) {
        $builder->where('aset.lokasi_id', $filters['lokasi_id']);
    }
    if (!empty($filters['merk_id'])) {
        $builder->where('aset.merk_id', $filters['merk_id']);
    }
    if (!empty($filters['tahun_beli'])) {
        $builder->where('aset.tahun_beli', $filters['tahun_beli']);
    }
    if (!empty($filters['user_pengguna'])) {
    $builder->where('aset.user_pengguna', $filters['user_pengguna']);
    }
    if (!empty($filters['keyword'])) {
        $keyword = strtoupper($filters['keyword']);
        $builder->groupStart()
              ->like('UPPER(aset.kode)', $keyword)
              ->orLike('UPPER(sk.nama_sub_kategori)', $keyword)
              ->orLike('UPPER(m.nama_merk)', $keyword)
              ->orLike('UPPER(t.nama_tipe)', $keyword)
              ->groupEnd();
    }
    
    // Eksekusi query
    $asets = $builder->orderBy('aset.kode', 'ASC')->get()->getResultArray();

    // Ambil data untuk dropdown filter
    $penanggungJawabList = $this->asetModel->distinct()->select('user_pengguna')->where('user_pengguna IS NOT NULL AND user_pengguna != ""')->orderBy('user_pengguna', 'ASC')->findAll();

    $data = [
        'title'               => 'Cetak Barcode Aset',
        'asets'               => $asets,
        'kategori_list'       => $kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
        'lokasi_list'         => $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
        'merk_list'           => $merkModel->orderBy('nama_merk', 'ASC')->findAll(),
        'penanggung_jawab_list' => $penanggungJawabList,
        'filters'             => $filters
    ];

    return view('aset/barcodes', $data);
}
    public function deleteDocument($docId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $doc = $this->dokumentasiAsetModel->find($docId);

        if ($doc) {
            // Hapus file fisik dari server
            if (file_exists(FCPATH . $doc['path_file'])) {
                unlink(FCPATH . $doc['path_file']);
            }
            // Hapus record dari database
            $this->dokumentasiAsetModel->delete($docId);
            
            return $this->response->setJSON(['success' => true]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }


    public function generateSerahTerimaPdf($id = null, $pihakKeduaId = null)
    {
        $karyawanModel = new KaryawanModel();

        // Ambil data Pihak Pertama (user_pengguna saat ini)
        $pihakPertama = $this->asetModel->find($id);

        // Ambil data Pihak Kedua dari master data
        $pihakKedua = $karyawanModel->find($pihakKeduaId);

        // Ambil data lengkap aset untuk detail
        $asetData = $this->asetModel
            ->select('aset.*, sk.nama_sub_kategori, m.nama_merk, t.nama_tipe')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left')
            ->find($id);

        if (!$asetData || !$pihakKedua) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data aset atau karyawan tidak ditemukan.');
        }

        $filename = 'Berita_Acara_Serah_Terima_' . str_replace('/', '_', $asetData['kode']) . '.pdf';

        $dompdf = new Dompdf();

        // Kirim data Pihak Pertama dan Pihak Kedua ke view
        $viewData = [
            'aset' => $asetData,
            'pihak_pertama' => $pihakPertama,
            'pihak_kedua' => $pihakKedua,
        ];
        $dompdf->loadHtml(view('aset/serah_terima_pdf', $viewData));

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename);

        return;
    }


    public function serveDocument($fileName = null)
    {
        if (empty($fileName)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Tentukan path file di dalam folder writable
        $path = WRITEPATH . 'uploads/aset_bukti/' . $fileName;

        if (!file_exists($path) || !is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Baca file dan kirimkan ke browser
        $fileContent = file_get_contents($path);
        
        // Dapatkan tipe mime dari database untuk akurasi
        $docInfo = $this->dokumentasiAsetModel->where('path_file', $fileName)->first();
        $mimeType = $docInfo['tipe_file'] ?? mime_content_type($path);

        return $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            ->setBody($fileContent)
            ->setHeader('Content-Disposition', 'inline; filename="' . $docInfo['nama_asli_file'] . '"'); // Menampilkan file, bukan download paksa
    }

    public function getAsetWithDetails($filters = [])
    {
        // Memulai query builder dengan join yang sudah ada
        $builder = $this->select('aset.*, lokasi.nama_lokasi, kategori.nama_kategori, karyawan.nama_karyawan')
                        ->join('lokasi', 'lokasi.id = aset.id_lokasi', 'left')
                        ->join('kategori', 'kategori.id = aset.id_kategori', 'left')
                        ->join('karyawan', 'karyawan.id = aset.user_pengguna', 'left');

        // Menerapkan filter yang sudah ada dari URL
        if (!empty($filters['keyword'])) {
            $builder->groupStart()
                    ->like('nama_aset', $filters['keyword'])
                    ->orLike('kode_aset', $filters['keyword'])
                    ->orLike('serial_number', $filters['keyword'])
                    ->groupEnd();
        }
        // Anda bisa tambahkan filter lain di sini jika diperlukan
        // Contoh:
        // if (!empty($filters['id_lokasi'])) {
        //     $builder->where('aset.id_lokasi', $filters['id_lokasi']);
        // }

        // Dapatkan hasil query aset yang sudah difilter
        // PENTING: Kita akan gunakan paginate() agar halaman tidak berat
        $asets = $builder->where('aset.deleted_at', null)->paginate(10, 'aset');

        // Jika tidak ada aset sama sekali, kembalikan array kosong
        if (empty($asets)) {
            return [];
        }

        // --- Bagian Baru: Mengambil Dokumen Secara Efisien ---
        // 1. Kumpulkan semua ID aset dari hasil paginasi
        $asetIds = array_column($asets, 'id');

        // 2. Ambil semua dokumen yang terkait dengan ID tersebut dalam SATU query
        $dokumentasiModel = new \App\Models\DokumentasiAsetModel();
        $allDokumen = $dokumentasiModel->whereIn('id_aset', $asetIds)->findAll();

        // 3. Buat "peta" dokumen agar mudah dicari berdasarkan id_aset
        $dokumenMap = [];
        foreach ($allDokumen as $dokumen) {
            $dokumenMap[$dokumen['id_aset']][] = $dokumen;
        }

        // 4. Lampirkan array dokumen ke setiap aset
        foreach ($asets as &$aset) { // tanda '&' penting untuk mengubah array aslinya
            $aset['dokumen'] = isset($dokumenMap[$aset['id']]) ? $dokumenMap[$aset['id']] : [];
        }
        // --------------------------------------------------

        return $asets;
    }

    public function infoPublik($id)
{
    // Ambil detail lengkap aset
    $aset = $this->asetModel->getAsetDetail($id);

    // Jika aset tidak ditemukan, tampilkan error 404
    if (empty($aset)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Informasi Aset tidak ditemukan.');
    }

    // Ambil data dokumen terkait aset ini
    $dokumentasiModel = new \App\Models\DokumentasiAsetModel();
    $dokumen = $dokumentasiModel->where('aset_id', $id)->findAll();

    $data = [
        'title'   => 'Informasi Aset: ' . esc($aset['kode']),
        'aset'    => $aset,
        'dokumen' => $dokumen,
    ];

    // Tampilkan view yang sudah kita rename tadi
    return view('aset/info_publik', $data);
}


}