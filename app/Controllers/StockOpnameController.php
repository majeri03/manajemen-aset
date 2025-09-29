<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsetModel;
use App\Models\LokasiModel;

class StockOpnameController extends BaseController
{
    /**
     * Menampilkan form untuk verifikasi/stock opname aset.
     * URL: /stockopname/aset/{id}
     */
    public function view($asetId)
    {
        $asetModel = new AsetModel();
        
        $aset = $asetModel
            ->select('aset.*, k.nama_kategori, sk.nama_sub_kategori, l.nama_lokasi, m.nama_merk, t.nama_tipe')
            ->join('kategori k', 'k.id = aset.kategori_id', 'left')
            ->join('sub_kategori sk', 'sk.id = aset.sub_kategori_id', 'left')
            ->join('lokasi l', 'l.id = aset.lokasi_id', 'left')
            ->join('merk m', 'm.id = aset.merk_id', 'left')
            ->join('tipe t', 't.id = aset.tipe_id', 'left')
            ->find($asetId);

        if (!$aset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Aset dengan ID ' . $asetId . ' tidak ditemukan.');
        }

        $lokasiModel = new LokasiModel();

        $data = [
            'title'       => 'Verifikasi Aset: ' . esc($aset['kode']),
            'aset'        => $aset,
            'lokasi_list' => $lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
        ];

        return view('stock_opname/form', $data);
    }

    /**
     * Memproses data dari form stock opname.
     */
    public function process($asetId)
    {
        $asetModel = new AsetModel();
        $db = \Config\Database::connect();

        // 1. Ambil data aset asli dari database
        $asetAsli = $asetModel->find($asetId);
        if (!$asetAsli) {
            return redirect()->to('/dashboard')->with('error', 'Aset tidak ditemukan.');
        }

        // 2. Ambil data yang dikirim dari form
        $dataForm = [
            'lokasi_id'  => $this->request->getPost('lokasi_id'),
            'status'     => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        // 3. Bandingkan data asli dengan data form untuk mencari perubahan
        $perubahan = [];
        if ($asetAsli['lokasi_id'] != $dataForm['lokasi_id']) {
            $perubahan['lokasi_id'] = $dataForm['lokasi_id'];
        }
        if ($asetAsli['status'] != $dataForm['status']) {
            $perubahan['status'] = $dataForm['status'];
        }
        if ($asetAsli['keterangan'] != $dataForm['keterangan']) {
            $perubahan['keterangan'] = $dataForm['keterangan'];
        }
        
        $adaPerubahan = !empty($perubahan);

        // 4. Simpan ke tabel riwayat stock opname
        $historyData = [
            'aset_id'       => $asetId,
            'user_id'       => session()->get('user_id'),
            'opname_at'     => date('Y-m-d H:i:s'),
            'catatan'       => $dataForm['keterangan'],
            'ada_perubahan' => $adaPerubahan,
        ];
        
        if ($adaPerubahan) {
            // Simpan data sebelum dan sesudah HANYA jika ada perubahan
            $historyData['data_sebelum'] = json_encode([
                'lokasi_id'  => $asetAsli['lokasi_id'],
                'status'     => $asetAsli['status'],
                'keterangan' => $asetAsli['keterangan'],
            ]);
            $historyData['data_sesudah'] = json_encode($perubahan);
        }

        $db->table('stock_opname_history')->insert($historyData);

        // 5. Jika ada perubahan, simpan juga ke tabel request
        if ($adaPerubahan) {
            $requestData = [
                'aset_id'       => $asetId,
                'user_id'       => session()->get('user_id'),
                'proposed_data' => json_encode($perubahan),
                'status'        => 'pending',
                'created_at'    => date('Y-m-d H:i:s'),
            ];
            $db->table('aset_update_requests')->insert($requestData);
        }

        // 6. Beri notifikasi ke pengguna
        $pesan = $adaPerubahan 
            ? 'Verifikasi berhasil. Usulan perubahan Anda telah diajukan untuk persetujuan.'
            : 'Aset berhasil diverifikasi tanpa ada perubahan.';

        return redirect()->to('/dashboard')->with('success', $pesan);
    }
}