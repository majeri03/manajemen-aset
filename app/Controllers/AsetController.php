<?php

namespace App\Controllers;

use App\Models\AsetModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    // Ambil nilai filter dari URL (method GET)
    $filters = [
        'kategori' => $this->request->getGet('kategori'),
        'status'   => $this->request->getGet('status'),
        'keyword'  => $this->request->getGet('keyword'),
    ];

    // Mulai query builder
    $query = $this->asetModel;

    // Terapkan filter jika ada nilainya
    if (!empty($filters['kategori'])) {
        $query->where('kategori', $filters['kategori']);
    }
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    if (!empty($filters['keyword'])) {
        $query->groupStart()
              ->like('kode', $filters['keyword'])
              ->orLike('merk', $filters['keyword'])
              ->orLike('serial_number', $filters['keyword'])
              ->orLike('lokasi', $filters['keyword'])
              ->groupEnd();
    }
    
    // Ambil daftar kategori unik untuk dropdown filter
    $kategori_list = $this->asetModel->distinct()->select('kategori')->findAll();

    $data = [
        'title'         => 'Data Aset',
        'asets'         => $query->orderBy('updated_at', 'DESC')->findAll(),
        'kategori_list' => $kategori_list, // Variabel ini yang dibutuhkan view
        'filters'       => $filters 
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
    $data = [
        'kode'          => $this->request->getPost('kode'),
        'kategori'      => $this->request->getPost('kategori'),
        'merk'          => $this->request->getPost('merk'),
        'serial_number' => $this->request->getPost('serial_number'),
        'tahun'         => $this->request->getPost('tahun'),
        'lokasi'        => $this->request->getPost('lokasi'),
        'status'        => $this->request->getPost('status'),
        'keterangan'    => $this->request->getPost('keterangan'),
    ];

    if ($this->asetModel->save($data)) {
        $newAsetId = $this->asetModel->getInsertID();

        // Logika generate QR Code
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

        // Tentukan halaman tujuan redirect berdasarkan input tersembunyi
        $redirectPage = $this->request->getPost('redirect_to');
        $redirectTo = ($redirectPage === 'dashboard') ? '/dashboard' : '/aset';
        
        // Ambil data aset baru untuk dikirim ke session (untuk popup QR code)
        $newAset = $this->asetModel->find($newAsetId);

        // Redirect ke halaman yang benar DAN selalu kirim data 'new_aset'
        return redirect()->to($redirectTo)
                         ->with('success', 'Aset baru berhasil ditambahkan!')
                         ->with('new_aset', $newAset);

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

        // Nonaktifkan Debug Toolbar untuk halaman publik ini
        \Config\Services::toolbar()->disable();

        return view('aset/public_detail', $data);
    }

public function export()
{
    // Logika filter tetap sama
    $filters = [
        'kategori' => $this->request->getGet('kategori'),
        'status'   => $this->request->getGet('status'),
        'keyword'  => $this->request->getGet('keyword'),
    ];
    
    $query = $this->asetModel;

    if (!empty($filters['kategori'])) {
        $query->where('kategori', $filters['kategori']);
    }
    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    if (!empty($filters['keyword'])) {
        $query->groupStart()
              ->like('kode', $filters['keyword'])
              ->orLike('merk', $filters['keyword'])
              ->orLike('lokasi', $filters['keyword'])
              ->groupEnd();
    }

    $asets = $query->orderBy('updated_at', 'DESC')->findAll();

    // Buat objek Spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Tulis header kolom
    $sheet->setCellValue('A1', 'Kode Aset');
    $sheet->setCellValue('B1', 'Kategori');
    $sheet->setCellValue('C1', 'Merk');
    $sheet->setCellValue('D1', 'Serial Number');
    $sheet->setCellValue('E1', 'Tahun');
    $sheet->setCellValue('F1', 'Lokasi');
    $sheet->setCellValue('G1', 'Status');
    $sheet->setCellValue('H1', 'Keterangan');
    $sheet->setCellValue('I1', 'Terakhir Diperbarui');
    
    // Styling untuk header (opsional)
    $styleArray = [
        'font' => ['bold' => true],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0C0C0']]
    ];
    $sheet->getStyle('A1:I1')->applyFromArray($styleArray);

    // Tulis data aset ke dalam baris
    $row = 2;
    foreach ($asets as $aset) {
        $sheet->setCellValue('A' . $row, $aset['kode']);
        $sheet->setCellValue('B' . $row, $aset['kategori']);
        $sheet->setCellValue('C' . $row, $aset['merk']);
        $sheet->setCellValue('D' . $row, $aset['serial_number']);
        $sheet->setCellValue('E' . $row, $aset['tahun']);
        $sheet->setCellValue('F' . $row, $aset['lokasi']);
        $sheet->setCellValue('G' . $row, $aset['status']);
        $sheet->setCellValue('H' . $row, $aset['keterangan']);
        $sheet->setCellValue('I' . $row, $aset['updated_at']);
        $row++;
    }
    
    // Atur lebar kolom agar otomatis (opsional)
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Buat writer untuk file .xlsx
    $writer = new Xlsx($spreadsheet);
    $filename = 'laporan_aset_' . date('Y-m-d') . '.xlsx';

    // Set header untuk download file
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Tulis file ke output
    $writer->save('php://output');
    exit();
}
    
    

}
