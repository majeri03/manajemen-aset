<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;
use App\Models\LokasiModel;
use App\Models\MerkModel; // <-- TAMBAHKAN
use App\Models\TipeModel; // <-- TAMBAHKAN


class MasterDataController extends BaseController
{
    protected $kategoriModel;
    protected $subKategoriModel;
    protected $lokasiModel;
    protected $merkModel; // <-- TAMBAHKAN
    protected $tipeModel; // <-- TAMBAHKAN
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->lokasiModel = new LokasiModel();
        $this->merkModel = new MerkModel(); // <-- TAMBAHKAN
        $this->tipeModel = new TipeModel(); // <-- TAMBAHKAN
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
    
    //--------------------------------------------------------------------
    // [BARU] Metode untuk CRUD Merk
    //--------------------------------------------------------------------

    public function createMerk()
    {
        if (!$this->validate(['nama_merk' => 'required|is_unique[merk.nama_merk]'])) {
            return redirect()->to('/master-data?tab=merk')->withInput()->with('errors_merk', $this->validator->getErrors());
        }

        $this->merkModel->save([
            'nama_merk' => $this->request->getPost('nama_merk'),
        ]);

        return redirect()->to('/master-data?tab=merk')->with('success', 'Merk baru berhasil ditambahkan.');
    }

    public function deleteMerk($id)
    {
        // Cek relasi dengan tipe
        $relatedTipes = $this->tipeModel->where('merk_id', $id)->first();
        if ($relatedTipes) {
            return redirect()->to('/master-data?tab=merk')->with('error', 'Gagal menghapus! Merk ini masih memiliki tipe terkait.');
        }

        $this->merkModel->delete($id);
        return redirect()->to('/master-data?tab=merk')->with('success', 'Merk berhasil dihapus.');
    }

    //--------------------------------------------------------------------
    // [BARU] Metode untuk CRUD Tipe
    //--------------------------------------------------------------------

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
                $this->tipeModel->save([
                    'merk_id' => $merkId,
                    'nama_tipe' => $name,
                ]);
            }
        }
    
        return redirect()->to('/master-data?tab=merk')->with('success', 'Tipe berhasil ditambahkan.');
    }
    
    public function deleteLokasi($id)
    {
        $db = \Config\Database::connect();
        $relatedAset = $db->table('aset')->where('lokasi_id', $id)->get()->getRow();
        if ($relatedAset) {
            return redirect()->to('/master-data?tab=lokasi')->with('error', 'Gagal menghapus! Lokasi ini masih digunakan oleh data aset.');
        }

        $this->lokasiModel->delete($id);
        return redirect()->to('/master-data?tab=lokasi')->with('success', 'Lokasi berhasil dihapus.');
    }
}

