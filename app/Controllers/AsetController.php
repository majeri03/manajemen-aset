<?php

namespace App\Controllers;

use App\Models\AsetModel;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class AsetController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
            $data = [
            'title' => 'Manajemen Aset',
            'asets' => $this->asetModel->findAll(), // Ambil semua data aset
        ];

        // Anda akan memuat view tabel aset di sini
        // Contoh: return view('aset/index', $data);
        // Untuk sementara, kita siapkan datanya dulu
        return $this->response->setJSON($data); // Contoh output JSON
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
        //
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
        // Ambil data dari form (method POST)
        $data = [
            'kode'          => $this->request->getPost('kode'),
            'kategori'      => $this->request->getPost('kategori'),
            'merk'          => $this->request->getPost('merk'),
            'serial_number' => $this->request->getPost('serial_number'),
            'tahun'         => $this->request->getPost('tahun'),
            'status'        => $this->request->getPost('status'),
            'lokasi'        => $this->request->getPost('lokasi'),
            'keterangan'    => $this->request->getPost('keterangan'),
        ];

    // Simpan data ke database melalui model
        if ($this->asetModel->save($data)) {
            // Jika berhasil, kembalikan ke dashboard dengan pesan sukses
            return redirect()->to('/dashboard')->with('success', 'Aset baru berhasil ditambahkan.');
        } else {
            // Jika gagal, kembalikan dengan pesan error
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan aset.');
        }
    }

    public function getDetail($id = null)
    {
        $aset = $this->asetModel->find($id);

        if ($aset) {
            // Format tanggal agar lebih mudah dibaca
            $aset['updated_at'] = date('d F Y H:i:s', strtotime($aset['updated_at']));
            return $this->response->setJSON($aset);
        }
        
        // Jika aset tidak ditemukan, kirim response error
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
        //
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
        //
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
        //
    }

    public function __construct()
    {
        // Inisialisasi model di constructor
        $this->asetModel = new AsetModel();
    }

     public function search()
    {
        // Ambil kata kunci dari request GET, misalnya: /aset/search?q=epson
        $keyword = $this->request->getGet('q');
        
        if ($keyword) {
            // Gunakan metode 'like' untuk mencari di beberapa kolom
            $results = $this->asetModel->like('kode', $keyword)
                                       ->orLike('kategori', $keyword)
                                       ->orLike('merk', $keyword)
                                       ->orLike('serial_number', $keyword)
                                       ->orLike('lokasi', $keyword)
                                       ->findAll();
        } else {
            // Jika tidak ada kata kunci, kembalikan semua data
            $results = $this->asetModel->findAll();
        }

        // Kembalikan hasil sebagai JSON
        return $this->response->setJSON($results);
    }

}
