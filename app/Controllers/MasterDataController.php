<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel; // <-- TAMBAHKAN
use App\Models\TipeModel; // <-- TAMBAHKAN
use App\Models\KaryawanModel;


class MasterDataController extends BaseController
{
    protected $kategoriModel;
    protected $subKategoriModel;
    protected $lokasiModel;
    protected $merkModel; // <-- TAMBAHKAN
    protected $tipeModel; // <-- TAMBAHKAN
    protected $karyawanModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->lokasiModel = new LokasiModel();
        $this->merkModel = new MerkModel(); // <-- TAMBAHKAN
        $this->tipeModel = new TipeModel(); // <-- TAMBAHKAN
         $this->karyawanModel = new KaryawanModel();
    }

    /**
     * Menampilkan halaman utama Data Master dengan tab Kategori dan Lokasi.
     */
    public function index()
    {
        $data = [
            'title'      => 'Manajemen Data Master',
            'kategoris'  => $this->kategoriModel->findAll(),
            'lokasis'    => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'merks'      => $this->merkModel->orderBy('nama_merk', 'ASC')->findAll(), // <-- TAMBAHKAN
            'karyawan_list'   => $this->karyawanModel->orderBy('nama_karyawan', 'ASC')->findAll(),
            'validation' => \Config\Services::validation(),
        ];
        
        return view('master/index', $data);
    }
    
    //--------------------------------------------------------------------
    // Metode untuk CRUD Kategori
    //--------------------------------------------------------------------

    public function createKategori()
    {
        if (!$this->validate(['nama_kategori' => 'required|is_unique[kategori.nama_kategori]'])) {
            return redirect()->to('/master-data')->withInput()->with('errors_kategori', $this->validator->getErrors());
        }

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);

        return redirect()->to('/master-data')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateKategori($id)
    {
        $validationRule = "required|is_unique[kategori.nama_kategori,id,{$id}]";
        if (!$this->validate(['nama_kategori' => $validationRule])) {
            return redirect()->to('/master-data?tab=kategori')->withInput()->with('error', $this->validator->getError('nama_kategori'));
        }

        $this->kategoriModel->update($id, ['nama_kategori' => $this->request->getPost('nama_kategori')]);
        return redirect()->to('/master-data?tab=kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteKategori($id)
    {
        $relatedSubKategoris = $this->subKategoriModel->where('kategori_id', $id)->first();
        if ($relatedSubKategoris) {
            return redirect()->to('/master-data')->with('error', 'Gagal menghapus! Kategori ini masih memiliki sub-kategori terkait.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->to('/master-data')->with('success', 'Kategori berhasil dihapus.');
    }
    

    //--------------------------------------------------------------------
    // Metode untuk CRUD Sub Kategori
    //--------------------------------------------------------------------

    public function createSubKategori()
    {
        $kategoriId = $this->request->getPost('kategori_id');
        $subKategoriNames = $this->request->getPost('nama_sub_kategori');
        
        $rules = [
            'kategori_id' => 'required|is_natural_no_zero',
            'nama_sub_kategori.*' => 'required',
        ];

        if (!$this->validate($rules)) {
             return redirect()->to('/master-data')->withInput()->with('errors_kategori', $this->validator->getErrors());
        }
        
        foreach ($subKategoriNames as $name) {
            if (!empty($name)) {
                $this->subKategoriModel->save([
                    'kategori_id' => $kategoriId,
                    'nama_sub_kategori' => $name,
                ]);
            }
        }
    
        return redirect()->to('/master-data')->with('success', 'Sub-kategori berhasil ditambahkan.');
    }

    public function updateSubKategori($id)
    {
        // Untuk sub-kategori, kita hanya perlu memastikan namanya tidak kosong.
        // Validasi is_unique bisa lebih kompleks (unik per kategori induk), jadi kita sederhanakan.
        if (!$this->validate(['nama_sub_kategori' => 'required'])) {
            return redirect()->to('/master-data?tab=kategori')->withInput()->with('error', $this->validator->getError('nama_sub_kategori'));
        }

        $this->subKategoriModel->update($id, ['nama_sub_kategori' => $this->request->getPost('nama_sub_kategori')]);
        return redirect()->to('/master-data?tab=kategori')->with('success', 'Sub-Kategori berhasil diperbarui.');
    }

    public function deleteSubKategori($id)
    {
        $db = \Config\Database::connect();
        $relatedAset = $db->table('aset')->where('sub_kategori_id', $id)->get()->getRow();
        if ($relatedAset) {
            return redirect()->to('/master-data')->with('error', 'Gagal menghapus! Sub-kategori ini masih digunakan oleh data aset.');
        }

        $this->subKategoriModel->delete($id);
        return redirect()->to('/master-data')->with('success', 'Sub-kategori berhasil dihapus.');
    }
    
    //--------------------------------------------------------------------
    // Metode untuk CRUD Lokasi
    //--------------------------------------------------------------------

    public function createLokasi()
    {
        if (!$this->validate(['nama_lokasi' => 'required|is_unique[lokasi.nama_lokasi]'])) {
            return redirect()->to('/master-data?tab=lokasi')->withInput()->with('errors_lokasi', $this->validator->getErrors());
        }

        $this->lokasiModel->save([
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
        ]);

        return redirect()->to('/master-data?tab=lokasi')->with('success', 'Lokasi baru berhasil ditambahkan.');
    }
    // app/Controllers/MasterDataController.php

    public function updateLokasi($id)
    {
        // Aturan validasi: nama harus diisi dan harus unik, kecuali untuk data ID saat ini
        $validationRule = "required|is_unique[lokasi.nama_lokasi,id,{$id}]";

        if (!$this->validate(['nama_lokasi' => $validationRule])) {
            return redirect()->to('/master-data?tab=lokasi')->withInput()->with('error', $this->validator->getError('nama_lokasi'));
        }

        $this->lokasiModel->update($id, [
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
        ]);

        return redirect()->to('/master-data?tab=lokasi')->with('success', 'Nama lokasi berhasil diperbarui.');
    }
    
    //--------------------------------------------------------------------
    // Merk & Tipe
    //--------------------------------------------------------------------

    public function createMerk()
    {
        if (!$this->validate(['nama_merk' => 'required|is_unique[merk.nama_merk]'])) {
            return redirect()->to('/master-data?tab=merk')->withInput()->with('errors_merk', $this->validator->getErrors());
        }
        $this->merkModel->save(['nama_merk' => $this->request->getPost('nama_merk')]);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Merk baru berhasil ditambahkan.');
    }

    public function updateMerk($id)
    {
        $validationRule = "required|is_unique[merk.nama_merk,id,{$id}]";
        if (!$this->validate(['nama_merk' => $validationRule])) {
            return redirect()->to('/master-data?tab=merk')->withInput()->with('error', $this->validator->getError('nama_merk'));
        }

        $this->merkModel->update($id, ['nama_merk' => $this->request->getPost('nama_merk')]);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Merk berhasil diperbarui.');
    }

    public function deleteMerk($id)
    {
        if ($this->tipeModel->where('merk_id', $id)->first()) {
            return redirect()->to('/master-data?tab=merk')->with('error', 'Gagal menghapus! Merk ini masih memiliki tipe terkait.');
        }
        $this->merkModel->delete($id);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Merk berhasil dihapus.');
    }

    public function createTipe()
    {
        $rules = [
            'merk_id' => 'required|is_natural_no_zero',
            'nama_tipe.*' => 'required',
        ];
        if (!$this->validate($rules)) {
             return redirect()->to('/master-data?tab=merk')->withInput()->with('errors_merk', $this->validator->getErrors());
        }
        
        $merkId = $this->request->getPost('merk_id');
        $tipeNames = $this->request->getPost('nama_tipe');
        foreach ($tipeNames as $name) {
            if (!empty($name)) {
                $this->tipeModel->save(['merk_id' => $merkId, 'nama_tipe' => $name]);
            }
        }
        return redirect()->to('/master-data?tab=merk')->with('success', 'Tipe berhasil ditambahkan.');
    }
    
    public function updateTipe($id)
    {
        if (!$this->validate(['nama_tipe' => 'required'])) {
            return redirect()->to('/master-data?tab=merk')->withInput()->with('error', $this->validator->getError('nama_tipe'));
        }

        $this->tipeModel->update($id, ['nama_tipe' => $this->request->getPost('nama_tipe')]);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Tipe berhasil diperbarui.');
    }
    
    // [BARU] Fungsi untuk menghapus Tipe
    public function deleteTipe($id)
    {
        $db = \Config\Database::connect();
        if ($db->table('aset')->where('tipe_id', $id)->get()->getRow()) {
            return redirect()->to('/master-data?tab=merk')->with('error', 'Gagal menghapus! Tipe ini masih digunakan oleh data aset.');
        }
        $this->tipeModel->delete($id);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Tipe berhasil dihapus.');
    }

    public function deleteLokasi($id)
    {
        // Hubungkan ke database untuk memeriksa relasi
        $db = \Config\Database::connect();
        $isUsed = $db->table('aset')->where('lokasi_id', $id)->get()->getRow();

        // Jika lokasi sudah digunakan oleh aset, batalkan penghapusan
        if ($isUsed) {
            return redirect()->to('/master-data?tab=lokasi')->with('error', 'Gagal menghapus! Lokasi ini masih digunakan oleh data aset.');
        }

        // Jika aman, lanjutkan penghapusan
        $this->lokasiModel->delete($id);
        return redirect()->to('/master-data?tab=lokasi')->with('success', 'Lokasi berhasil dihapus.');
    }

    //--------------------------------------------------------------------
    // nama karyawan & jabatan
    //--------------------------------------------------------------------

    public function createKaryawan()
    {
        if (!$this->validate($this->karyawanModel->getValidationRules())) {
            return redirect()->to('/master-data?tab=karyawan')->withInput()->with('errors_karyawan', $this->validator->getErrors());
        }

        $this->karyawanModel->save([
            'nama_karyawan' => $this->request->getPost('nama_karyawan'),
            'jabatan'       => $this->request->getPost('jabatan'),
        ]);

        return redirect()->to('/master-data?tab=karyawan')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function deleteKaryawan($id)
    {
        // Di masa depan, Anda bisa menambahkan pengecekan apakah karyawan ini terkait dengan data lain
        $this->karyawanModel->delete($id);
        return redirect()->to('/master-data?tab=karyawan')->with('success', 'Data karyawan berhasil dihapus.');
    }
}

