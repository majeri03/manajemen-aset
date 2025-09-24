<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $session;
    protected $helpers = ['form', 'url']; 
    public function __construct()
        {
            $this->session = service('session');
        }
    public function register()
    {
        return view('auth/register');
    }

    public function processRegister()
    {
        $rules = [
            //'employee_id'      => 'required',
            'full_name' => 'required|min_length[3]',
            'email' => 'required|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/register')->withInput()->with('errors', $this->validator->getErrors());
        }

        // $db = \Config\Database::connect();
        // $employeeId = $this->request->getPost('employee_id');

        // $employee = $db->table('employee_ids')->where('employee_id', $employeeId)->get()->getRow();

        // if (!$employee) {
        //     return redirect()->to('/register')->withInput()->with('error', 'ID Karyawan tidak valid atau tidak terdaftar.');
        // }

        // if ($employee->is_registered) {
        //     return redirect()->to('/register')->withInput()->with('error', 'ID Karyawan ini sudah digunakan untuk mendaftar.');
        
        $userModel = new UserModel();
        $userModel->save([
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'department' => $this->request->getPost('department'),
            'password' => $this->request->getPost('password'), // akan di-hash oleh Model
        ]);
        //$db->table('employee_ids')->where('employee_id', $employeeId)->update(['is_registered' => true]);
        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function login()
    {
        return view('auth/login');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function processLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];
        if (!$this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user->password_hash)) {
            return redirect()->to('/login')->withInput()->with('error', 'Email atau password salah.');
        }

        $session = session();
        $sessionData = [
            'user_id'       => $user->id,
            'full_name'     => $user->full_name,
            'email'         => $user->email,
            'role'          => $user->role,
            'isLoggedIn'    => true,
        ];
        $session->set($sessionData);

        if ($redirectUrl = session()->get('redirect_url')) {
            // Hapus session agar tidak dipakai lagi
            session()->remove('redirect_url');
            // Arahkan ke URL tujuan
            return redirect()->to($redirectUrl);
        }

        return redirect()->to('/dashboard');
    }
}