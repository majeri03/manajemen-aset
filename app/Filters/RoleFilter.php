<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Jika tidak ada peran yang diizinkan yang ditentukan, tolak akses.
        if (empty($arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        // Ambil role dari session
        $userRole = $session->get('role');

        // Anggap 'super_admin' sebagai 'admin' untuk keperluan pengecekan hak akses
        $effectiveRole = ($userRole === 'super_admin') ? 'admin' : $userRole;

        // Periksa apakah peran efektif pengguna ada dalam daftar peran yang diizinkan.
        if (!$session->has('role') || !in_array($effectiveRole, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan yang diperlukan setelah request.
    }
}