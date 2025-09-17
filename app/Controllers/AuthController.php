<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function register()
    {
        return view('auth/register');
    }

    // --- TAMBAHKAN FUNGSI DI BAWAH INI ---
    public function processRegister()
    {
        // 1. Validasi
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembali ke form dengan error dan input lama
            return redirect()->to('/register')->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Simpan ke Database
        $userModel = new UserModel();
        $userModel->save([
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'department' => $this->request->getPost('department'),
            'password' => $this->request->getPost('password'), // akan di-hash oleh Model
        ]);

        // 3. Redirect ke halaman login dengan pesan sukses
        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function login()
    {
        return view('auth/login');
    }

    public function processLogin()
    {
        // 1. Validasi
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Cek User
        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user->password_hash)) {
            return redirect()->to('/login')->withInput()->with('error', 'Email atau password salah.');
        }

        // 3. Buat Session
        $session = session();
        $sessionData = [
            'user_id'       => $user->id,
            'full_name'     => $user->full_name,
            'email'         => $user->email,
            'role'          => $user->role,
            'isLoggedIn'    => true,
        ];
        $session->set($sessionData);

        // Redirect ke dashboard (halaman ini akan kita buat nanti)
        return redirect()->to('/dashboard');
    }
}