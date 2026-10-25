<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AkunModel;

class Admin extends BaseController
{
    protected $akunModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
    }
    
    // Helper Cek Admin
    private function isAdmin()
    {
        $session = session()->get('user_session');
        return ($session && isset($session['role']) && $session['role'] === 'Admin');
    }

    // 1. DASHBOARD
    public function dashboard()
    {
        $session = session()->get('user_session');
        $roles = $session['roles'] ?? []; // Ambil daftar role user

        // Allow Admin and Tendik to view Admin dashboard. Other roles are redirected to home.
        if (!in_array('tendik', $roles) && !in_array('admin', $roles)) {
            return redirect()->to(base_url('home')); 
        }

        $data = [
            'title'      => 'Dashboard',
            'total_user' => $this->akunModel->countAllResults(),
            'username'   => $session['username'] ?? 'User'
        ];
        
        return view('admin/dashboard', $data);
    }

    // 2. HALAMAN KELOLA PENGGUNA
    public function manageUsers()
    {
        if (!$this->isAdmin()) return redirect()->to(base_url('auth/login'));

        $users = $this->akunModel->findAll();

        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $users
        ];

        return view('admin/manage_users', $data);
    }

    // 3. PROSES GANTI ROLE (INI YANG SAYA PERBAIKI)
    public function changeRole()
    {
        // Cek Admin
        if (!$this->isAdmin()) return redirect()->to(base_url('auth/login'));

        $id = $this->request->getPost('id');
        $roleInput = $this->request->getPost('role'); // Isinya: "Admin", "Dosen", dll.

        // --- MAPPING TULISAN KE ANGKA (ID_ROLE) ---
        // Kita terjemahkan dulu karena database butuh Angka, bukan Tulisan
        $id_role_baru = 1; // Default ke Mahasiswa kalau error

        switch ($roleInput) {
            case 'Admin':     $id_role_baru = 8;  break;
            case 'Dosen':     $id_role_baru = 2;  break;
            case 'Mahasiswa': $id_role_baru = 1;  break;
            case 'Tendik':    $id_role_baru = 4;  break;
            case 'Kaprodi':   $id_role_baru = 3;  break;
            case 'Klien':     $id_role_baru = 7;  break;
            case 'Fakultas':  $id_role_baru = 5;  break;
            case 'Tamu':      $id_role_baru = 99; break;
            default:          $id_role_baru = 1;  break;
        }

        // --- UPDATE KE DATABASE PAKAI 'id_role' ---
        $this->akunModel->update($id, [
            'id_role' => $id_role_baru 
        ]);

        return redirect()->back()->with('message', [
            "icon" => "success",
            "title" => "Berhasil",
            "text" => "Role pengguna berhasil diubah menjadi " . $roleInput
        ]);
    }

    // 4. PROSES FORCE RESET PASSWORD
    public function forceResetPassword()
    {
        if (!$this->isAdmin()) {
            return redirect()->to(base_url('auth/login'))->with('message', [
                "icon" => "error",
                "title" => "Akses Ditolak",
                "text" => "Hanya Admin yang boleh melakukan ini."
            ]);
        }

        $id          = $this->request->getPost('id');
        $newPass     = $this->request->getPost('password');
        $confirmPass = $this->request->getPost('confirm_password');

        if ($newPass !== $confirmPass) {
            return redirect()->back()->with('message', [
                "icon" => "error",
                "title" => "Gagal",
                "text" => "Konfirmasi password tidak sama."
            ]);
        }

        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
        
        $this->akunModel->update($id, ['password' => $hashedPassword]);

        return redirect()->back()->with('message', [
            "icon" => "success",
            "title" => "Berhasil",
            "text" => "Password pengguna ID " . $id . " berhasil direset paksa."
        ]);
    }
 
}