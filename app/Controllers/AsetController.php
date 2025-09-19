<?php

namespace App\Controllers;

use App\Models\AsetModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

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
            'title' => 'Data Aset',
            'asets' => $this->asetModel->orderBy('updated_at', 'DESC')->findAll(),
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

        $data = [
        'kode' => $this->request->getPost('kode'),
        // ... data lainnya
    ];

    // Simpan data aset terlebih dahulu
    if ($this->asetModel->save($data)) {
        // Ambil ID dari aset yang baru saja disimpan
        $newAsetId = $this->asetModel->getInsertID();

        // Buat URL unik untuk halaman detail aset
        $url = base_url('tracking/aset/' . $newAsetId);

        // Generate QR Code
        $qrCode = QrCode::create($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Simpan gambar QR code ke folder public
        $qrCodePath = 'qrcodes/aset-' . $newAsetId . '.png';
        $result->saveToFile(FCPATH . $qrCodePath);

        // Update database dengan path gambar QR code
        $this->asetModel->update($newAsetId, ['qrcode' => $qrCodePath]);

        return redirect()->to('/dashboard')->with('success', 'Aset baru berhasil ditambahkan.');
    } else {
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
        if ($this->asetModel->delete($id)) {
            return redirect()->to('/aset')->with('success', 'Aset berhasil dihapus.');
        } else {
            return redirect()->to('/aset')->with('error', 'Gagal menghapus aset.');
        }
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

    public function publicDetail($id)
    {
        $data = [
            'title' => 'Detail Aset',
            'aset'  => $this->asetModel->find($id),
        ];

        // Buat view baru untuk halaman ini, misal: 'aset/public_detail'
        return view('aset/public_detail', $data);
    }

}
