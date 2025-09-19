<?php

namespace App\Controllers;

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
