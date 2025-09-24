<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\SubKategoriModel;

class CategoryController extends BaseController
{
    protected $kategoriModel;
    protected $subKategoriModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Manajemen Kategori Aset',
            'kategoris'  => $this->kategoriModel->findAll(),
            'validation' => \Config\Services::validation(),
        ];
        
        return view('category/index', $data);
    }
    
    // Metode untuk CRUD Kategori
    public function createKategori()
    {
        if (!$this->request->is('post') || !$this->validate(['nama_kategori' => 'required|is_unique[kategori.nama_kategori]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateKategori($id)
    {
        if (!$this->request->is('post') || !$this->validate(['nama_kategori' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteKategori($id)
    {
        // Cek apakah ada sub-kategori yang terkait
        $relatedSubKategoris = $this->subKategoriModel->where('kategori_id', $id)->first();
        if ($relatedSubKategoris) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus kategori. Masih ada sub-kategori yang terkait.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }

    // Metode untuk CRUD Sub Kategori
    public function createSubKategori()
    {
        if (!$this->request->is('post') || !$this->validate(['kategori_id' => 'required|is_natural_no_zero', 'nama_sub_kategori' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    
        $this->subKategoriModel->save([
            'kategori_id' => $this->request->getPost('kategori_id'),
            'nama_sub_kategori' => $this->request->getPost('nama_sub_kategori'),
        ]);
    
        return redirect()->back()->with('success', 'Sub Kategori berhasil ditambahkan.');
    }

    public function updateSubKategori($id)
    {
        if (!$this->request->is('post') || !$this->validate(['nama_sub_kategori' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->subKategoriModel->update($id, [
            'nama_sub_kategori' => $this->request->getPost('nama_sub_kategori'),
        ]);

        return redirect()->back()->with('success', 'Sub Kategori berhasil diperbarui.');
    }

    public function deleteSubKategori($id)
    {
        // Cek apakah ada aset yang terkait
        $db = \Config\Database::connect();
        $relatedAset = $db->table('aset')->where('sub_kategori_id', $id)->first();
        if ($relatedAset) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus sub-kategori. Masih ada aset yang terkait.');
        }

        $this->subKategoriModel->delete($id);
        return redirect()->back()->with('success', 'Sub Kategori berhasil dihapus.');
    }
}