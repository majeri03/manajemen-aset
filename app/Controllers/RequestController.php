<?php

namespace App\Controllers;
use App\Models\AsetModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class RequestController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        // Hubungkan ke database
        $db = \Config\Database::connect();

        // Ambil data permintaan yang masih 'pending'
        $pending = $db->table('aset_update_requests as aur')
                      ->select('aur.id, aur.proposed_data, aur.created_at, u.full_name, a.kode')
                      ->join('users as u', 'u.id = aur.user_id')
                      ->join('aset as a', 'a.id = aur.aset_id')
                      ->where('aur.status', 'pending')
                      ->get()->getResultArray();

        // Ambil data riwayat (yang sudah di-approve atau di-reject)
        $history = $db->table('aset_update_requests as aur')
                       ->select('aur.id, aur.proposed_data, aur.status, aur.created_at, u.full_name, a.kode')
                       ->join('users as u', 'u.id = aur.user_id')
                       ->join('aset as a', 'a.id = aur.aset_id')
                       ->whereIn('aur.status', ['approved', 'rejected'])
                       ->orderBy('aur.created_at', 'DESC')
                       ->get()->getResultArray();

        $data = [
            'title'   => 'Permintaan Perubahan Aset',
            'pending' => $pending,
            'history' => $history,
        ];

        return view('requests/index', $data);
    }
    public function newRequest($asetId)
    {
        $asetModel = new AsetModel();
        $aset = $asetModel->find($asetId);

        if (!$aset) {
            return redirect()->back()->with('error', 'Aset tidak ditemukan.');
        }

        $data = [
            'title' => 'Ajukan Perubahan Aset',
            'aset'  => $aset,
        ];

        return view('requests/new', $data);
    }
    public function store()
    {
        $proposedData = service('request')->getVar('proposed_data');
        $asetId = service('request')->getVar('aset_id');
        $userId = session()->get('user_id'); 

        $filteredData = array_filter($proposedData, static fn ($value) => $value !== '' && $value !== null);

        if (empty($filteredData)) {
            return redirect()->back()->withInput()->with('error', 'Anda harus mengisi setidaknya satu kolom perubahan.');
        }

        $dataToStore = [
            'aset_id'       => $asetId,
            'user_id'       => $userId,
            'proposed_data' => json_encode($filteredData),
            'status'        => 'pending',
        ];

        $db = \Config\Database::connect();
        if ($db->table('aset_update_requests')->insert($dataToStore)) {
            return redirect()->to('/dashboard')->with('success', 'Permintaan perubahan berhasil diajukan dan sedang menunggu persetujuan.');
        }

        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan permintaan.');
    }
    public function approve($requestId)
    {
        $db = \Config\Database::connect();
        $request = $db->table('aset_update_requests')->where('id', $requestId)->get()->getRow();

        if ($request && $request->status === 'pending') {
            // 1. Ambil data yang diajukan
            $proposedData = json_decode($request->proposed_data, true);

            // 2. Update data aset utama
            $asetModel = new AsetModel();
            $asetModel->update($request->aset_id, $proposedData);

            // 3. Update status permintaan menjadi 'approved'
            $db->table('aset_update_requests')->where('id', $requestId)->update(['status' => 'approved']);

            return redirect()->to('admin/requests')->with('success', 'Permintaan berhasil disetujui.');
        }
        return redirect()->to('admin/requests')->with('error', 'Permintaan tidak valid.');
    }

    public function reject($requestId)
    {
        $db = \Config\Database::connect();
        // Update status permintaan menjadi 'rejected'
        $db->table('aset_update_requests')->where('id', $requestId)->update(['status' => 'rejected']);

        return redirect()->to('admin/requests')->with('success', 'Permintaan telah ditolak.');
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
        //
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
}
