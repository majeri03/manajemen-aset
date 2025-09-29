<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel;
use App\Models\TipeModel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ImportController extends BaseController
{
    public function index()
    {
        $data = [
            'title'        => 'Import Data Aset',
            'import_data'  => session()->get('import_data'),
            'kategori'     => (new KategoriModel())->orderBy('nama_kategori', 'ASC')->findAll(),
            'subkategori'  => (new SubKategoriModel())->orderBy('nama_sub_kategori', 'ASC')->findAll(),
            'lokasi'       => (new LokasiModel())->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merk'         => (new MerkModel())->orderBy('nama_merk', 'ASC')->findAll(),
        ];

        return view('import/index', $data);
    }

public function upload()
    {
     $file = $this->request->getFile('excel_file');

        if ($file->isValid() && !$file->hasMoved()) {
            $asetModel = new AsetModel();
            $reader = new Xlsx();
            $spreadsheet = $reader->load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $existingSerialsDB = array_map('strtoupper', array_filter(array_column($asetModel->select('serial_number')->findAll(), 'serial_number')));
            
            $dataToImport = [];
            $serialsInFile = [];

            // Kumpulkan serial number dari file
            for ($i = 2; $i <= count($sheet); $i++) {
                $sn = strtoupper(trim($sheet[$i]['E'])); // Kolom Serial Number
                if (!empty($sn)) {
                    $serialsInFile[] = $sn;
                }
            }
            
            $duplicatesInFile = array_keys(array_filter(array_count_values($serialsInFile), fn($count) => $count > 1));
            
            // Proses setiap baris
            for ($i = 2; $i <= count($sheet); $i++) {
                $serialNumber = strtoupper(trim($sheet[$i]['E']));
                $isDuplicate = !empty($serialNumber) && (in_array($serialNumber, $existingSerialsDB) || in_array($serialNumber, $duplicatesInFile));
                
                $statusExcel = strtolower(trim($sheet[$i]['K'])); // Kolom status sekarang di 'K'
                $statusFinal = 'Baik Terpakai'; // Default
                if (str_contains($statusExcel, 'tidak terpakai')) {
                    $statusFinal = 'Baik Tidak Terpakai';
                } elseif (str_contains($statusExcel, 'rusak')) {
                    $statusFinal = 'Rusak';
                }

                $rowData = [
                    'kategori'          => strtoupper(trim($sheet[$i]['A'])),
                    'sub_kategori'      => strtoupper(trim($sheet[$i]['B'])),
                    'merk'              => strtoupper(trim($sheet[$i]['C'])),
                    'tipe'              => strtoupper(trim($sheet[$i]['D'])),
                    'serial_number'     => $serialNumber,
                    'entitas_pembelian' => strtoupper(trim($sheet[$i]['F'])), // Kolom Entitas 'F'
                    'tahun'             => trim($sheet[$i]['G']),             // Kolom Tahun 'G'
                    'harga_beli'        => trim($sheet[$i]['H']),             // Kolom Harga Beli 'H'
                    'penanggung_jawab'  => strtoupper(trim($sheet[$i]['I'])), // Kolom Penanggung Jawab 'I'
                    'lokasi'            => strtoupper(trim($sheet[$i]['J'])), // Kolom Lokasi 'J'
                    'status'            => $statusFinal,
                    'keterangan'        => strtoupper(trim($sheet[$i]['L'])), // Kolom Keterangan 'L'
                    'is_duplicate'      => $isDuplicate,
                ];

                // Hanya impor baris yang memiliki data
                if (implode('', array_slice($rowData, 0, 5)) !== '') {
                    $dataToImport[] = $rowData;
                }
            }

            session()->set('import_data', $dataToImport);
        } else {
            return redirect()->to('/import')->with('error', 'Gagal mengunggah file. Pastikan file valid.');
        }

        return redirect()->to('/import')->with('success', 'File berhasil diunggah. Silakan validasi data di bawah.');
    }
    
    private function generateUniqueAssetCode($entitas, $tahun, $subKategoriId, $merkId)
    {
        $asetModel = new AsetModel();
        $subKategoriModel = new SubKategoriModel();
        $merkModel = new MerkModel();

        $lastAsset = $asetModel
            ->where('entitas_pembelian', $entitas)
            ->where('sub_kategori_id', $subKategoriId)
            ->where('tahun', $tahun)
            ->orderBy('id', 'DESC')
            ->first();
        
        $nextUniqueNumber = 1;

        if ($lastAsset) {
            $parts = explode('/', $lastAsset['kode']);
            $lastUniqueNumber = (int)end($parts);
            $nextUniqueNumber = $lastUniqueNumber + 1;
        }

        $formattedUniqueNumber = str_pad($nextUniqueNumber, 2, '0', STR_PAD_LEFT);

        $subKategoriInfo = $subKategoriModel->find($subKategoriId);
        $merkInfo = $merkModel->find($merkId);

        $subKategoriCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $subKategoriInfo['nama_sub_kategori'] ?? 'SUB'), 0, 5));
        $merkCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $merkInfo['nama_merk'] ?? 'MRK'), 0, 3));
        $entitasCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $entitas), 0, 5));
        
        $newCode = "BTR/{$entitasCode}/{$tahun}/{$subKategoriCode}/{$merkCode}/{$formattedUniqueNumber}";
        return $newCode;
    }

    public function save()
{
    $importedData = $this->request->getPost('aset');
    $asetModel = new AsetModel();
    $allRowsAreValid = true; // Anggap semua data valid pada awalnya
    $validatedData = [];   // Untuk menampung data yang sudah divalidasi
    $originalDataWithErrors = []; // Untuk menampung data asli jika ada error

    if (empty($importedData)) {
        return redirect()->to('/import')->with('error', 'Tidak ada data untuk disimpan.');
    }

    // =============================================================
    // TAHAP 1: VALIDASI SEMUA DATA TANPA MENYIMPAN
    // =============================================================
    foreach ($importedData as $index => $data) {
        // Lewati baris kosong
        if (count(array_filter($data)) === 0) {
            $originalDataWithErrors[] = session()->get('import_data')[$index]; // tetap bawa baris kosong
            continue;
        }

        $errors = [];
        $requiredFields = [
            'kategori_id' => 'Kategori', 'sub_kategori_id' => 'Sub Kategori',
            'merk_id' => 'Merk', 'tipe_id' => 'Tipe', 'tahun' => 'Tahun',
            'entitas_pembelian' => 'Entitas Pembelian', 'lokasi_id' => 'Lokasi', 'status' => 'Status'
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = "$label wajib diisi.";
            }
        }
        
        if (!empty($data['serial_number'])) {
            $existing = $asetModel->where('serial_number', $data['serial_number'])->first();
            if ($existing) {
                $errors[] = "Serial Number sudah ada di database.";
            }
        }

        // Ambil data asli dari sesi untuk ditampilkan kembali jika ada error
        $dataFromSession = session()->get('import_data')[$index];

        if (!empty($errors)) {
            $allRowsAreValid = false; // Tandai bahwa ada data yang tidak valid
            $dataFromSession['errors'] = $errors;
        }
        
        $originalDataWithErrors[] = $dataFromSession;
        $validatedData[] = $data; // Kumpulkan data yang akan disimpan nanti
    }

    // =============================================================
    // TAHAP 2: KEPUTUSAN BERDASARKAN HASIL VALIDASI
    // =============================================================
    if (!$allRowsAreValid) {
        // Jika ada SATU SAJA data yang tidak valid, batalkan seluruh proses.
        // Kembalikan semua data ke halaman import dengan pesan error.
        session()->set('import_data', $originalDataWithErrors);
        return redirect()->to('/import')->with('error', 'Beberapa data perlu diperbaiki sebelum dapat disimpan.');
    }

    // =============================================================
    // TAHAP 3: SIMPAN SEMUA DATA & BUAT QR CODE (Hanya jika semua valid)
    // =============================================================
    $newlyCreatedAssets = [];
    foreach ($validatedData as $data) {
        $kode = $this->generateUniqueAssetCode(
            $data['entitas_pembelian'], $data['tahun'],
            $data['sub_kategori_id'], $data['merk_id']
        );

        if ($asetModel->save([
            'kode'              => $kode,
            'kategori_id'       => $data['kategori_id'],
            'sub_kategori_id'   => $data['sub_kategori_id'],
            'merk_id'           => $data['merk_id'],
            'tipe_id'           => $data['tipe_id'],
            'serial_number'     => $data['serial_number'],
            'tahun'             => $data['tahun'],
            'harga_beli'        => $data['harga_beli'],
            'entitas_pembelian' => $data['entitas_pembelian'],
            'penanggung_jawab'  => $data['penanggung_jawab'],
            'lokasi_id'         => $data['lokasi_id'],
            'status'            => $data['status'],
            'keterangan'        => $data['keterangan'],
        ])) {
            $newAsetId = $asetModel->getInsertID();

            $url = base_url('stockopname/aset/' . $newAsetId);
            if (!is_dir(FCPATH . 'qrcodes')) {
                mkdir(FCPATH . 'qrcodes', 0777, true);
            }
            
            $qrCode = QrCode::create($url);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodePath = 'qrcodes/aset-' . $newAsetId . '.png';
            $result->saveToFile(FCPATH . $qrCodePath);

            $asetModel->update($newAsetId, ['qrcode' => $qrCodePath]);
            
            $newlyCreatedAssets[] = $asetModel
                ->select('aset.*, sub_kategori.nama_sub_kategori')
                ->join('sub_kategori', 'sub_kategori.id = aset.sub_kategori_id', 'left')
                ->find($newAsetId);
        }
    }

    // Hapus sesi dan arahkan ke halaman cetak label
    session()->remove('import_data');
    return redirect()->to('/import/print-labels')
                     ->with('new_assets', $newlyCreatedAssets)
                     ->with('success', count($newlyCreatedAssets) . ' label aset baru siap dicetak.');
}

    public function printLabels()
    {
        $newAssets = session()->getFlashdata('new_assets');

        if (empty($newAssets)) {
            return redirect()->to('/aset');
        }

        $data = [
            'title' => 'Cetak Label Aset Baru',
            'asets' => $newAssets,
        ];

        return view('import/print_labels', $data);
    }



    public function cancel()
    {
        session()->remove('import_data');
        return redirect()->to('/import');
    }

    public function addMasterData()
    {
        if ($this->request->isAJAX()) {
            $type = $this->request->getPost('type');
            $name = strtoupper($this->request->getPost('name'));
            $parentId = $this->request->getPost('parent_id');

            $model = null; $data = [];

            switch ($type) {
                case 'kategori': $model = new KategoriModel(); $data = ['nama_kategori' => $name]; break;
                case 'subkategori': $model = new SubKategoriModel(); $data = ['kategori_id' => $parentId, 'nama_sub_kategori' => $name]; break;
                case 'lokasi': $model = new LokasiModel(); $data = ['nama_lokasi' => $name]; break;
                case 'merk': $model = new MerkModel(); $data = ['nama_merk' => $name]; break;
                case 'tipe': $model = new TipeModel(); $data = ['merk_id' => $parentId, 'nama_tipe' => $name]; break;
            }

            if ($model && $model->save($data)) {
                $id = $model->getInsertID();
                return $this->response->setJSON(['status' => 'success', 'id' => $id, 'text' => $name]);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan data. Mungkin sudah ada.']);
        }
    }
    
    public function updateSessionData()
    {
        if ($this->request->isAJAX()) {
            $rowIndex    = $this->request->getPost('rowIndex');
            $fieldName   = $this->request->getPost('fieldName'); // misal: 'kategori_id' atau 'serial_number'
            $value       = $this->request->getPost('value');     // Teks yang diketik/dipilih
            $id          = $this->request->getPost('id');         // ID dari master data (jika ada)

            $importData = session()->get('import_data');

            if (isset($importData[$rowIndex])) {
                $baseFieldName = str_replace('_id', '', $fieldName);

                // Selalu simpan teks yang ditampilkan di kolom utama
                $importData[$rowIndex][$baseFieldName] = $value;

                // Jika ada ID yang dikirim (saat memilih dari autocomplete), simpan ID tersebut.
                // Jika tidak, hapus ID lama karena teks sudah tidak cocok.
                $importData[$rowIndex][$baseFieldName . '_id'] = $id;

                session()->set('import_data', $importData);
                return $this->response->setJSON(['status' => 'success']);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Row index not found.']);
        }
    }

    // ... (di dalam class ImportController)

public function deleteMasterData()
    {
        // Pastikan ini adalah request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $type = $this->request->getPost('type');
        $id   = $this->request->getPost('id');
        $model = null;
        $db = \Config\Database::connect();
        $isUsed = false;

        // Tentukan model dan cek apakah data sedang digunakan
        switch ($type) {
            case 'kategori':
                $model = new KategoriModel();
                $isUsed = $db->table('aset')->where('kategori_id', $id)->countAllResults() > 0;
                break;
            case 'subkategori':
                $model = new SubKategoriModel();
                $isUsed = $db->table('aset')->where('sub_kategori_id', $id)->countAllResults() > 0;
                break;
            case 'lokasi':
                $model = new LokasiModel();
                $isUsed = $db->table('aset')->where('lokasi_id', $id)->countAllResults() > 0;
                break;
            case 'merk':
                $model = new MerkModel();
                $isUsed = $db->table('aset')->where('merk_id', $id)->countAllResults() > 0;
                break;
            case 'tipe':
                $model = new TipeModel();
                $isUsed = $db->table('aset')->where('tipe_id', $id)->countAllResults() > 0;
                break;
        }

        if ($isUsed) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data ini sudah terpakai dan tidak bisa dihapus dari halaman ini.']);
        }

        if ($model && $model->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Item berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data.']);
    }
}