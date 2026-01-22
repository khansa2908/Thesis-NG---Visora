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
        $userData = $session->get('user_session');

        // 1. Cek Login
        if (!$userData) {
            return redirect()->to(base_url('auth'))->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. AMBIL ROLE (Sesuaikan dengan session Anda yang berbentuk array 'roles')
        $userRoles = $userData['roles'] ?? []; 

        // 3. CEK AKSES
        if ($arguments) {
            // Kita cek apakah ada salah satu role user yang diizinkan di $arguments
            $hasAccess = false;
            foreach ($arguments as $allowedRole) {
                if (in_array($allowedRole, $userRoles)) {
                    $hasAccess = true;
                    break;
                }
            }

            if (!$hasAccess) {
                // Jika tidak punya akses, arahkan ke base_url agar tidak 404
                return redirect()->to(base_url('/'))->with('error', 'Akses Terbatas!');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}