<?php

namespace App\Controllers;

use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel; 
use App\Models\TipeModel;

use App\Models\DokumentasiAsetModel;
use App\Models\DokumenPerbaikanModel;

use App\Models\BerkasAsetModel;

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
    // Ambil filter dari request
    $filters = [
        'kategori_id' => $this->request->getGet('kategori_id'),
        'status' => $this->request->getGet('status'),
        'keyword' => $this->request->getGet('keyword'),
    ];

    // Mulai build query dengan join yang diperlukan
    $this->asetModel
        ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
        ->join('kategori k', 'k.id = sk.kategori_id', 'left')
        ->join('merk m', 'm.id = aset.merk_id', 'left')
        ->join('tipe t', 't.id = aset.tipe_id', 'left')
        ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
        // --- PERBAIKAN DI BARIS INI ---
        ->join('karyawan kry', 'kry.id = aset.user_pengguna', 'left');

    // Terapkan filter jika ada
    if (!empty($filters['kategori_id'])) {
        $this->asetModel->where('k.id', $filters['kategori_id']);
    }
    if (!empty($filters['status'])) {
        $this->asetModel->where('aset.status', $filters['status']);
    }
    if (!empty($filters['keyword'])) {
        $this->asetModel->groupStart()
            ->like('aset.kode', $filters['keyword'])
            ->orLike('m.nama_merk', $filters['keyword'])
            ->orLike('t.nama_tipe', $filters['keyword'])
            ->orLike('aset.serial_number', $filters['keyword'])
            ->orLike('l.nama_lokasi', $filters['keyword'])
            ->orLike('aset.user_pengguna', $filters['keyword'])
            ->groupEnd();
    }

    // Tambahkan select statement setelah semua join dan filter diterapkan
    $asets = $this->asetModel->select('
        aset.*, 
        k.nama_kategori, 
        sk.nama_sub_kategori, 
        m.nama_merk, 
        t.nama_tipe, 
        l.nama_lokasi, 
        kry.nama_karyawan,
        (SELECT MAX(soh.opname_at) FROM stock_opname_history soh WHERE soh.aset_id = aset.id) as last_so_date
    ')->findAll();

    // Mengambil semua dokumentasi terkait dalam satu query untuk efisiensi
    $asetIds = array_column($asets, 'id');
    $allDokumentasi = [];
    if (!empty($asetIds)) {
        $dokumentasiModel = new \App\Models\DokumentasiAsetModel();
        $allDokumentasi = $dokumentasiModel->whereIn('aset_id', $asetIds)->findAll();
    }

    // Membuat map dokumentasi untuk akses cepat
    $dokumentasiMap = [];
    foreach ($allDokumentasi as $doc) {
        $dokumentasiMap[$doc['aset_id']][] = $doc;
    }

    // Menggabungkan dokumentasi ke dalam data aset
    foreach ($asets as &$aset) {
        $aset['dokumen'] = $dokumentasiMap[$aset['id']] ?? [];
    }
    unset($aset); // Membersihkan referensi

    // Siapkan data untuk view
    $data = [
        'asets' => $asets,
        'kategori_list' => (new \App\Models\KategoriModel())->findAll(),
        'subkategori_list' => (new \App\Models\SubKategoriModel())->findAll(),
        'merk_list' => (new \App\Models\MerkModel())->findAll(),
        'tipe_list' => (new \App\Models\TipeModel())->findAll(),
        'lokasi_list' => (new \App\Models\LokasiModel())->findAll(),
        'filters' => $filters,
        'pager' => $this->asetModel->pager,
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
                            $file->move(FCPATH . 'uploads/aset_bukti', $newName);

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
        $aset = $this->asetModel->getAsetDetail($id);
        if (!$aset) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan.');
        }

        $karyawanModel = new KaryawanModel();
        
        // Ini untuk mengambil data FOTO FISIK (tetap ada)
        $dokumentasi = $this->dokumentasiAsetModel->where('aset_id', $id)->findAll();

        // ▼▼▼ TAMBAHKAN DUA BARIS INI ▼▼▼
        $berkasModel = new \App\Models\BerkasAsetModel();
        $berkas_list = $berkasModel->where('aset_id', $id)->findAll();
        // ▲▲▲ ------------------------- ▲▲▲

        $data = [
            'title'           => 'Edit Aset',
            'aset'            => $aset,
            'kategori_list'   => $this->kategoriModel->findAll(),
            'subkategori_list' => $this->subKategoriModel->where('kategori_id', $aset['kategori_id'])->findAll(),
            'lokasi_list'     => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk_list'       => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(),
            'tipe_list'       => $this->tipeModel->where('merk_id', $aset['merk_id'])->findAll(),
            'dokumentasi'     => $dokumentasi,
            'karyawan_list'   => $karyawanModel->orderBy('nama_karyawan', 'ASC')->findAll(),
            'berkas_list'     => $berkas_list, // <-- Kirim data berkas ke view
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
        $asetSebelumnya = $this->model->getAsetDetail($id);
        if (!$asetSebelumnya) {
            return redirect()->to('/aset')->with('error', 'Aset tidak ditemukan.');
        }

        $data = $this->request->getPost();
        $statusSebelumnya = $asetSebelumnya['status'];
        $statusSekarang = $data['status'] ?? $statusSebelumnya;
        
        $isSerahTerima = ($statusSebelumnya === 'Baik Tidak Terpakai' && $statusSekarang === 'Baik Terpakai');
        $isPerbaikan = ($statusSekarang === 'Perbaikan');

        // Validasi
        if ($isSerahTerima && empty($this->request->getPost('pihak_kedua_id'))) {
            return redirect()->back()->withInput()->with('error', 'Untuk serah terima, Anda wajib memilih Pihak Kedua (Penerima).');
        }
        if ($isPerbaikan && (empty($data['penyetuju_nama']) || empty($data['keterangan_kerusakan']) || empty($data['estimasi_biaya']))) {
            return redirect()->back()->withInput()->with('error', 'Untuk status "Perbaikan", semua detail permohonan harus diisi.');
        }
        
        // Proses update user pengguna jika ada serah terima
        if ($isSerahTerima) {
            $penerima = (new KaryawanModel())->find($this->request->getPost('pihak_kedua_id'));
            if ($penerima) {
                $data['user_pengguna'] = $penerima['id'];
            }
        }

        // LAKUKAN UPDATE DATA UTAMA
        if ($this->model->update($id, $data)) {
            // PROSES PDF SERAH TERIMA (JIKA ADA)
            if ($isSerahTerima) {
                $pihakPertamaData = [
                    'nama_karyawan' => $asetSebelumnya['nama_karyawan'] ?? $asetSebelumnya['user_pengguna'] ?? 'Gudang',
                    'jabatan'       => $asetSebelumnya['jabatan'] ?? 'HCGA'
                ];
                $this->buatDanSimpanPdfSerahTerima($id, $pihakPertamaData, (new KaryawanModel())->find($this->request->getPost('pihak_kedua_id')));
            }

            // PROSES PDF PERBAIKAN (JIKA ADA)
            if ($isPerbaikan) {
                $this->buatDanSimpanPdfPerbaikan($id, $data);
            }

            return redirect()->to('/aset')->with('success', 'Data aset berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data aset.');
        }
    }

    private function buatDanSimpanPdfSerahTerima($asetId, $pihakPertama, $pihakKedua)
    {
        $asetDetail = $this->model->getAsetDetail($asetId);
        if (!$asetDetail || !$pihakKedua) return;

        $pdfData = [
            'aset'           => $asetDetail,
            'pihak_pertama'  => $pihakPertama,
            'pihak_kedua'    => $pihakKedua,
            'tanggal'        => date('l, d F Y'),
        ];
        
        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml(view('aset/serah_terima_pdf', $pdfData));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $filename = 'serah_terima_' . str_replace('/', '-', $asetDetail['kode']) . '_' . time() . '.pdf';
        $path = FCPATH . 'uploads/aset_bukti/'; 

        if (file_put_contents($path . $filename, $pdfOutput)) {
            (new BerkasAsetModel())->save([
                'aset_id'     => $asetId, 'path_file'   => $filename, 'nama_berkas' => 'BUKTI SERAH TERIMA',
                'tipe_file'   => 'application/pdf', 'ukuran_file' => strlen($pdfOutput)
            ]);
        }
    }

    private function buatDanSimpanPdfPerbaikan($asetId, $dataPermohonan)
    {
        $dokumenPerbaikanModel = new DokumenPerbaikanModel();
        $dokumenPerbaikanModel->save([
            'aset_id'              => $asetId,
            'user_pemohon_id'      => session()->get('user_id'),
            'penyetuju_nama'       => $dataPermohonan['penyetuju_nama'],
            'keterangan_kerusakan' => $dataPermohonan['keterangan_kerusakan'],
            'estimasi_biaya'       => $dataPermohonan['estimasi_biaya'],
        ]);
        $dokumenPerbaikanId = $dokumenPerbaikanModel->getInsertID();

        $asetDetail = $this->model->getAsetDetail($asetId);
        $karyawanModel = new KaryawanModel();
            // Logika yang sama seperti di atas: prioritaskan data dari user_pengguna aset
            $pemohon = $karyawanModel->find($asetDetail['user_pengguna']);

            if (!$pemohon) {
                $pemohon = [
                    'nama_karyawan' => $asetDetail['nama_karyawan'] ?: ($asetDetail['user_pengguna'] ?: 'Tidak Ada'),
                    'jabatan'       => $asetDetail['jabatan'] ?? 'Pengguna Aset'
                ];
            }

        $pdfData = [
            'aset'           => $asetDetail,
            'pemohon'        => $pemohon, // <-- DATA PEMOHON SEKARANG SUDAH BENAR
            'penyetuju'      => ['nama' => $dataPermohonan['penyetuju_nama'], 'jabatan' => 'HCGA'],
            'perbaikan'      => [ // Menggunakan key 'perbaikan' agar konsisten dengan template
                'keterangan_kerusakan' => $dataPermohonan['keterangan_kerusakan'],
                'estimasi_biaya' => $dataPermohonan['estimasi_biaya']
            ]
        ];

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml(view('aset/permohonan_perbaikan_pdf', $pdfData));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        $filename = 'permohonan_perbaikan_' . str_replace('/', '-', $asetDetail['kode']) . '_' . time() . '.pdf';
        $path = FCPATH . 'uploads/aset_bukti/';

        if (file_put_contents($path . $filename, $pdfOutput)) {
            (new BerkasAsetModel())->save([
                'aset_id'     => $asetId, 'path_file'   => $filename, 'nama_berkas' => 'PERMOHONAN DANA PERBAIKAN',
                'tipe_file'   => 'application/pdf', 'ukuran_file' => strlen($pdfOutput)
            ]);
            $dokumenPerbaikanModel->update($dokumenPerbaikanId, ['nama_file' => $filename]);
        }
    }

    public function generateSerahTerimaPdf($id = null, $pihakKeduaId = null)
    {
        $asetSaatIni = $this->model->getAsetDetail($id);
        $pihakKedua = (new KaryawanModel())->find($pihakKeduaId);

        if (!$asetSaatIni || !$pihakKedua) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data aset atau karyawan tidak ditemukan.');
        }

        $pihakPertamaData = [
            'nama_karyawan' => $asetSaatIni['nama_karyawan'] ?? $asetSaatIni['user_pengguna'] ?? 'Gudang',
            'jabatan'       => $asetSaatIni['jabatan'] ?? 'HCGA'
        ];

        $filename = 'Berita_Acara_Serah_Terima_' . str_replace('/', '_', $asetSaatIni['kode']) . '.pdf';
        
        $pdfData = [
            'aset'           => $asetSaatIni,
            'pihak_pertama'  => $pihakPertamaData,
            'pihak_kedua'    => $pihakKedua,
            'tanggal'        => date('l, d F Y'),
        ];

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml(view('aset/serah_terima_pdf', $pdfData));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename);
    }
    public function generatePerbaikanPdf($id = null)
{
    // Ambil data dari query string di URL
    $penyetujuNama = $this->request->getGet('penyetuju');
    $kerusakan = $this->request->getGet('kerusakan');
    $biaya = $this->request->getGet('biaya');

    // Ambil detail lengkap aset untuk memastikan kita punya 'nama_karyawan' jika ada
    $asetDetail = $this->model->getAsetDetail($id);
    $karyawanModel = new \App\Models\KaryawanModel();

    // Prioritaskan mencari data karyawan berdasarkan ID 'user_pengguna'
    $pemohon = $karyawanModel->find($asetDetail['user_pengguna']);

    // Jika tidak ada di tabel karyawan (misal 'user_pengguna' hanya teks biasa),
    // gunakan data dari detail aset itu sendiri.
    if (!$pemohon) {
        $pemohon = [
            'nama_karyawan' => $asetDetail['nama_karyawan'] ?: ($asetDetail['user_pengguna'] ?: 'Tidak Ada'),
            'jabatan'       => $asetDetail['jabatan'] ?? 'Pengguna Aset'
        ];
    }

    // Validasi menggunakan variabel $asetDetail
    if (!$asetDetail || !$penyetujuNama || !$kerusakan || !$biaya) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak lengkap untuk membuat PDF permohonan perbaikan.');
    }

    // Membuat nama file menggunakan variabel $asetDetail
    $filename = 'Permohonan_Perbaikan_' . str_replace('/', '_', $asetDetail['kode']) . '.pdf';

    $pdfData = [
        'aset'           => $asetDetail, // Mengirim data ke view dengan nama 'aset'
        'pemohon'        => $pemohon,
        'penyetuju'      => ['nama' => $penyetujuNama, 'jabatan' => 'HCGA'],
        'kerusakan'      => $kerusakan,
        'estimasi_biaya' => $biaya,
    ];

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml(view('aset/permohonan_perbaikan_pdf', $pdfData));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream($filename);
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

     public function serveDocument($fileName = null)
    {
        if (empty($fileName)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Tentukan path file di dalam folder public/uploads
        $path = FCPATH . 'uploads/aset_bukti/' . $fileName;

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
            ->setHeader('Content-Disposition', 'inline; filename="' . ($docInfo['nama_asli_file'] ?? $fileName) . '"');
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

// Fungsi baru untuk menangani unggahan FOTO FISIK
public function add_dokumentasi($id)
{
    // Pastikan ini adalah request AJAX
    if ($this->request->isAJAX()) {
        $files = $this->request->getFiles();

        if (empty($files) || !isset($files['bukti_aset'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada file yang dipilih.']);
        }
        
        $isSuccess = false;
        foreach ($files['bukti_aset'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                if ($file->getSize() > 2048000) continue; 
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) continue;

                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/aset_bukti', $newName); 

                $dokumentasiModel = new \App\Models\DokumentasiAsetModel();
                $dokumentasiModel->save([
                    'aset_id'        => $id,
                    'path_file'      => $newName,
                    'nama_asli_file' => $file->getClientName(),
                    'tipe_file'      => $file->getClientMimeType(),
                    'ukuran_file'    => $file->getSize(),
                ]);
                $isSuccess = true;
            }
        }

        if ($isSuccess) {
            return $this->response->setJSON(['success' => true, 'message' => 'Dokumentasi fisik berhasil diunggah.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid. Pastikan file adalah gambar (jpg/png) dan ukurannya di bawah 2MB.']);
        }
    }
    
    return redirect()->back()->with('error', 'Akses tidak diizinkan.');
}
// Fungsi baru untuk menangani unggahan BERKAS LEGAL
public function add_berkas($id)
{
    // Pastikan ini adalah request AJAX
    if ($this->request->isAJAX()) {
        $file = $this->request->getFile('file_berkas');
        $namaBerkas = $this->request->getPost('nama_berkas');

        // Validasi sederhana
        if (empty($namaBerkas) || !$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama berkas dan file tidak boleh kosong.']);
        }

        if ($file->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File ini sepertinya sudah pernah diunggah.']);
        }

        // Pindahkan file ke folder public
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/aset_bukti', $newName);

        // Simpan ke database menggunakan model yang benar
        $berkasModel = new \App\Models\BerkasAsetModel();
        $berkasModel->save([
            'aset_id'       => $id,
            'path_file'     => $newName,
            'nama_berkas'   => $namaBerkas,
            'tipe_file'     => $file->getClientMimeType(),
            'ukuran_file'   => $file->getSize()
        ]);

        // Kirim respons sukses dalam format JSON
        return $this->response->setJSON(['success' => true, 'message' => 'Berkas legal berhasil diunggah.']);
    }

    // Jika diakses secara langsung, redirect saja
    return redirect()->back()->with('error', 'Akses tidak diizinkan.');
}
public function delete_document($id = null, $type = null)
{
    if ($this->request->isAJAX()) {
        $model = null;
        if ($type === 'dokumentasi') {
            $model = new \App\Models\DokumentasiAsetModel();
        } elseif ($type === 'berkas') {
            $model = new \App\Models\BerkasAsetModel();
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Tipe tidak valid.']);
        }

        $doc = $model->find($id);

        if ($doc) {
            // Hapus file fisik dari server
            $filePath = FCPATH . 'uploads/aset_bukti/' . $doc['path_file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Hapus record dari database
            $model->delete($id);

            return $this->response->setJSON(['success' => true, 'message' => 'File berhasil dihapus.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'File tidak ditemukan.']);
    }
    return redirect()->back()->with('error', 'Akses tidak diizinkan.');
}






}