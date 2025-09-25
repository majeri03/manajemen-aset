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

            // 1. Ambil semua serial number dari database (yang tidak kosong/null dan sudah kapital)
            $existingSerialsDB = array_map('strtoupper', array_filter(array_column($asetModel->select('serial_number')->findAll(), 'serial_number')));
            
            $dataToImport = [];
            $serialsInFile = [];

            // 2. Loop pertama: Kumpulkan semua serial number dari file Excel
            for ($i = 2; $i <= count($sheet); $i++) {
                $sn = strtoupper(trim($sheet[$i]['E']));
                if (!empty($sn)) {
                    $serialsInFile[] = $sn;
                }
            }
            
            // 3. Cari duplikat di dalam file Excel itu sendiri
            $duplicatesInFile = array_keys(array_filter(array_count_values($serialsInFile), fn($count) => $count > 1));
            
            // 4. Loop kedua: Proses setiap baris, buat data lengkap, dan tandai duplikat
            for ($i = 2; $i <= count($sheet); $i++) {
                $serialNumber = strtoupper(trim($sheet[$i]['E']));
                $isDuplicate = false;

                if (!empty($serialNumber) && (in_array($serialNumber, $existingSerialsDB) || in_array($serialNumber, $duplicatesInFile))) {
                    $isDuplicate = true;
                }
                
                $statusExcel = strtolower(trim($sheet[$i]['J']));
                $statusFinal = 'Baik Terpakai';
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
                    'tahun'             => trim($sheet[$i]['F']),
                    'harga_beli'        => trim($sheet[$i]['G']),
                    'entitas_pembelian' => strtoupper(trim($sheet[$i]['H'])),
                    'lokasi'            => strtoupper(trim($sheet[$i]['I'])),
                    'status'            => $statusFinal,
                    'keterangan'        => strtoupper(trim($sheet[$i]['K'])),
                    'is_duplicate'      => $isDuplicate,
                ];

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
        $savedCount = 0;

        if (empty($importedData)) {
            return redirect()->to('/import')->with('error', 'Tidak ada data untuk disimpan.');
        }

        foreach ($importedData as $data) {
            $isRowEmpty = true;
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    $isRowEmpty = false;
                    break;
                }
            }
            if ($isRowEmpty) continue;

            $requiredFields = [
                'kategori_id', 'sub_kategori_id', 'merk_id', 'tipe_id', 
                'tahun', 'entitas_pembelian', 'lokasi_id', 'status'
            ];
            $isValid = true;
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $isValid = false;
                    break;
                }
            }
            if (!$isValid) continue;

            $kode = $this->generateUniqueAssetCode(
                 $data['entitas_pembelian'], $data['tahun'],
                 $data['sub_kategori_id'], $data['merk_id']
            );

             $asetModel->save([
                'kode'              => $kode,
                'kategori_id'       => $data['kategori_id'],
                'sub_kategori_id'   => $data['sub_kategori_id'],
                'merk_id'           => $data['merk_id'],
                'tipe_id'           => $data['tipe_id'],
                'serial_number'     => $data['serial_number'],
                'tahun'             => $data['tahun'],
                'harga_beli'        => $data['harga_beli'],
                'entitas_pembelian' => $data['entitas_pembelian'],
                'lokasi_id'         => $data['lokasi_id'],
                'status'            => $data['status'],
                'keterangan'        => $data['keterangan'],
             ]);
             $savedCount++;
        }
        
        session()->remove('import_data');
        if ($savedCount > 0) {
            return redirect()->to('/aset')->with('success', $savedCount . ' baris data aset berhasil diimpor.');
        } else {
            return redirect()->to('/import')->with('error', 'Tidak ada data valid yang dapat disimpan. Pastikan semua kolom wajib diisi pada baris yang ingin Anda simpan.');
        }
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
            $rowIndex = $this->request->getPost('rowIndex');
            $fieldName = $this->request->getPost('fieldName');
            $value = $this->request->getPost('value');

            $importData = session()->get('import_data');

            if (isset($importData[$rowIndex])) {
                $sessionFieldName = str_replace(['_id'], '', $fieldName);
                $importData[$rowIndex][$sessionFieldName] = $value;
                
                session()->set('import_data', $importData);
                return $this->response->setJSON(['status' => 'success']);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Row index not found.']);
        }
    }
}