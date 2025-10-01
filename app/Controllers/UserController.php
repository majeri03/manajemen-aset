<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
public function index()
{
    $userModel = new UserModel();
    $db = \Config\Database::connect();

    $keyword = $this->request->getGet('keyword');
    $role = $this->request->getGet('role');

    $query = $userModel; // Mulai query builder

    if ($keyword) {
        $query->groupStart();
        $query->like('full_name', $keyword)
              ->orLike('email', $keyword);
        $query->groupEnd();
    }

    if ($role) {
        $query->where('role', $role);
    }
    
    $users = $query->findAll();

    // ===== TAMBAHKAN LOGIKA STATISTIK INI =====
    $totalUsers = count($userModel->findAll()); // Hitung total sebelum filter
    $roleCounts = [
        'admin'   => $userModel->where('role', 'admin')->countAllResults(),
        'manager' => $userModel->where('role', 'manager')->countAllResults(),
        'staff'   => $userModel->where('role', 'staff')->countAllResults(),
    ];
    $statusCounts = [
        'active'   => $userModel->where('status', 'active')->countAllResults(),
        'inactive' => $userModel->where('status', 'inactive')->countAllResults(),
    ];
    // ===========================================

    $soModeSetting = $db->table('settings')->where('setting_key', 'stock_opname_mode')->get()->getRow();

    $data = [
        'title'        => 'Manajemen Pengguna',
        'users'        => $users,
        'so_mode'      => $soModeSetting ? $soModeSetting->setting_value : 'off',
        'filters'      => ['keyword' => $keyword, 'role' => $role],
        'totalUsers'   => $totalUsers,
        'roleCounts'   => $roleCounts,
        'statusCounts' => $statusCounts,
    ];

    return view('user/index', $data);
}
    public function toggleSoMode()
    {
        $db = \Config\Database::connect();
        $newStatus = $this->request->getPost('status');

        if (in_array($newStatus, ['on', 'off'])) {
            $db->table('settings')->where('setting_key', 'stock_opname_mode')->update(['setting_value' => $newStatus]);
            return $this->response->setJSON(['success' => true, 'message' => 'Mode Stock Opname berhasil diubah.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Status tidak valid.']);
    }

    public function toggleUserSoPermission($userId)
    {
        $userModel = new UserModel();
        $permission = $this->request->getPost('permission'); // Akan bernilai 'true' atau 'false' dari JS

        $data = [
            'can_perform_so' => ($permission === 'true') ? 1 : 0
        ];

        if ($userModel->update($userId, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Izin pengguna berhasil diubah.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah izin pengguna.']);
    }
    public function create()
    {
        $data = [
            'title' => 'Tambah Pengguna Baru',
        ];
        return view('user/create', $data);
    }

    public function store()
    {
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/user/create')->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->save([
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
            'status' => 'active' // Status default saat dibuat
        ]);

        return redirect()->to('/user')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $userModel = new UserModel();
        $data = [
            'title' => 'Edit Pengguna',
            'user' => $userModel->find($id),
        ];
        return view('user/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'role' => 'required',
            'status' => 'required|in_list[active,inactive]',
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/user/edit/' . $id)->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        if ($this->request->getPost('password')) {
            $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);

        return redirect()->to('/user')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function activate($id)
    {
        $userModel = new UserModel();
        $userModel->update($id, ['status' => 'active']);

        return redirect()->to('/user')->with('success', 'Pengguna berhasil diaktifkan kembali.');
    }

    public function resetPassword($id)
    {
        $userModel = new UserModel();
        // Buat password acak 8 karakter
        $newPassword = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(8/strlen($x)) )),1,8);

        $data = [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ];

        if ($userModel->update($id, $data)) {
            return redirect()->to('/user')->with('success', 'Password berhasil direset! Password baru: <strong>' . $newPassword . '</strong>');
        } else {
            return redirect()->to('/user')->with('error', 'Gagal mereset password.');
        }
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        // Ubah ini menjadi nonaktif, bukan hapus
        $userModel->update($id, ['status' => 'inactive']);

        return redirect()->to('/user')->with('success', 'Pengguna telah dinonaktifkan.');
    }
}