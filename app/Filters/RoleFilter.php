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

        // Jika pengguna adalah super_admin, izinkan akses ke mana saja.
        if ($userRole === 'super_admin') {
            return;
        }

        // Periksa apakah peran pengguna ada dalam daftar peran yang diizinkan.
        if (!$session->has('role') || !in_array($userRole, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada tindakan yang diperlukan setelah request.
    }
}