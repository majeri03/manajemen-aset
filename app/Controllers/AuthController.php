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
    // public function register()
    // {
    //     return view('auth/register');
    // }

    // public function processRegister()
    // {
    //     $rules = [
    //         //'employee_id'      => 'required',
    //         'full_name' => 'required|min_length[3]',
    //         'email' => 'required|is_unique[users.email]',
    //         'password' => 'required|min_length[8]',
    //         'password_confirm' => 'required|matches[password]',
    //         'terms' => 'required'
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->to('/register')->withInput()->with('errors', $this->validator->getErrors());
    //     }

    //     // $db = \Config\Database::connect();
    //     // $employeeId = $this->request->getPost('employee_id');

    //     // $employee = $db->table('employee_ids')->where('employee_id', $employeeId)->get()->getRow();

    //     // if (!$employee) {
    //     //     return redirect()->to('/register')->withInput()->with('error', 'ID Karyawan tidak valid atau tidak terdaftar.');
    //     // }

    //     // if ($employee->is_registered) {
    //     //     return redirect()->to('/register')->withInput()->with('error', 'ID Karyawan ini sudah digunakan untuk mendaftar.');
        
    //     $userModel = new UserModel();
    //     $userModel->save([
    //         'full_name' => $this->request->getPost('full_name'),
    //         'email' => $this->request->getPost('email'),
    //         'phone_number' => $this->request->getPost('phone_number'),
    //         'department' => $this->request->getPost('department'),
    //         'password' => $this->request->getPost('password'), // akan di-hash oleh Model
    //     ]);
    //     //$db->table('employee_ids')->where('employee_id', $employeeId)->update(['is_registered' => true]);
    //     return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    // }

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

    // FUNGSI-FUNGSI BARU UNTUK LUPA PASSWORD
    
    public function forgotPassword()
    {
        return view('auth/forgot');
    }

    public function processForgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Alamat email tidak ditemukan di sistem kami.');
        }

        // Buat token unik
        $token = bin2hex(random_bytes(20));

        $db = \Config\Database::connect();
        $db->table('password_reset_tokens')->where('email', $user->email)->delete(); // Hapus token lama jika ada
        $db->table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // --- AWAL PERUBAHAN ---
        // Konfigurasi email secara eksplisit di dalam controller
        $config = [
            'protocol'   => getenv('email.protocol'),
            'SMTPHost'   => getenv('email.SMTPHost'),
            'SMTPPort'   => (int) getenv('email.SMTPPort'),
            'SMTPUser'   => getenv('email.SMTPUser'),
            'SMTPPass'   => getenv('email.SMTPPass'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            'mailType'   => getenv('email.mailType'),
            'charset'    => getenv('email.charset'),
            'newline'    => "\r\n", // Wajib diset secara eksplisit
            'CRLF'       => "\r\n", // Wajib diset secara eksplisit
        ];
        
        $emailService = \Config\Services::email();
        $emailService->initialize($config);
        
        // Atur pengirim setelah inisialisasi
        $emailService->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
        // --- AKHIR PERUBAHAN ---

        $emailService->setTo($user->email);
        $emailService->setSubject('Reset Password Akun Anda');

        $resetLink = base_url('reset-password/' . $token);
        $message = "Halo " . esc($user->full_name) . ",<br><br>"
                 . "Anda menerima email ini karena ada permintaan untuk mereset password akun Anda pada Sistem Manajemen Aset.<br>"
                 . "Silakan klik tautan di bawah ini untuk melanjutkan:<br>"
                 . "<a href='" . $resetLink . "'>" . $resetLink . "</a><br><br>"
                 . "Tautan ini berlaku selama 1 jam.<br><br>"
                 . "Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.";

        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Tautan reset password telah dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam.');
        } else {
            $debug = $emailService->printDebugger(['headers']);
            return redirect()->back()->with('error', 'Gagal mengirim email. Debug: ' . esc($debug));
        }
    }

    public function resetPassword($token = null)
    {
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Token tidak valid atau tidak ada.');
        }

        $db = \Config\Database::connect();
        $resetData = $db->table('password_reset_tokens')->where('token', $token)->get()->getRow();

        // Cek apakah token ada dan belum lebih dari 1 jam
        if (!$resetData || (time() - strtotime($resetData->created_at)) > 3600) {
            $db->table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
        }

        return view('auth/reset', ['token' => $token]);
    }

    public function processResetPassword($token = null)
    {
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Aksi tidak diizinkan.');
        }
        
        $db = \Config\Database::connect();
        $resetData = $db->table('password_reset_tokens')->where('token', $token)->get()->getRow();

        if (!$resetData || (time() - strtotime($resetData->created_at)) > 3600) {
            $db->table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
        }

        $rules = [
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $resetData->email)->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Pengguna tidak ditemukan.');
        }

        $userModel->update($user->id, ['password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)]);

        // Hapus token setelah berhasil digunakan
        $db->table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->to('/login')->with('success', 'Password Anda telah berhasil direset! Silakan login dengan password baru.');
    }
}

