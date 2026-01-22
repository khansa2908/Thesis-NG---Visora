<?php







namespace App\Controllers;





use App\Models\PasswordResetModel;

use App\Models\AkunModel;

use CodeIgniter\Controller;



use App\Libraries\AuthLdap;



use CodeIgniter\I18n\Time;



use App\Controllers\BaseController;



class Auth extends BaseController

{



    protected $authLdap;

    protected $mahasiswaModel;

    protected $akunModel;

    protected $aksesModel;

    protected $roleModel;

    protected $fakultasModel;

    protected $prodiModel;

    protected $tendikModel;

    protected $dosenModel;

    protected $persetujuanTamuModel;

    protected $klienModel;





    public function __construct()

    {

        $this->mahasiswaModel = new \App\Models\MahasiswaModel();

        $this->akunModel = new \App\Models\AkunModel();

        $this->aksesModel = new \App\Models\AksesModel();

        $this->roleModel = new \App\Models\RoleModel();

        $this->fakultasModel = new \App\Models\FakultasModel();

        $this->prodiModel = new \App\Models\ProgramStudiModel();

        $this->tendikModel = new \App\Models\TenagaKependidikanModel();

        $this->dosenModel = new \App\Models\DosenModel();

        $this->persetujuanTamuModel = new \App\Models\PersetujuanTamuModel();

        $this->klienModel = new \App\Models\KlienModel();
    }







    public function index()

    {

        return redirect()->to(base_url("/auth/login"));
    }





    public function login()

    {

        if (session()->get("user_session") == null) {

            return view("auth/login");
        } else {

            return redirect()->to(base_url("home"));
        }
    }





    public function logout()

    {

        session()->destroy();

        return redirect()->to(base_url("auth/login"));
    }



    /* ===========================================================

     |  FORGOT PASSWORD — TAMPILAN FORM

     ============================================================ */

    public function forgotPassword()

    {

        return view("auth/forgot_password");
    }



    /* ===========================================================

 |  FORGOT PASSWORD — SUBMIT REQUEST RESET

 |  Mencari Akun di Tabel Tamu DULU, lalu di Tabel Utama

 ============================================================ */

    public function forgotSubmit()

    {

        // Mengambil input dari form

        $usernameInput = $this->request->getPost('username');

        $emailInput    = $this->request->getPost('email');



        if (empty($usernameInput) || empty($emailInput)) {

            return redirect()->back()->with('message', [

                "icon" => "error",

                "title" => "Gagal",

                "text" => "Username dan Email harus diisi lengkap."

            ]);
        }



        $akun = null;



        // 1. CARI DULU DI TABEL TAMU (perstujuan_tamu)

        $akun = $this->persetujuanTamuModel

            ->where('username', $usernameInput)

            ->where('email', $emailInput)

            ->first();



        // 2. JIKA TIDAK DITEMUKAN DI TAMU, CARI DI TABEL UTAMA (akun)

        if (!$akun) {

            $akun = $this->akunModel

                ->where('username', $usernameInput)

                ->where('email', $emailInput)

                ->first();
        }



        // 3. JIKA TETAP TIDAK DITEMUKAN

        if (!$akun) {

            return redirect()->back()->with('message', [

                "icon" => "error",

                "title" => "Gagal",

                "text" => "Kombinasi Username dan Email tidak cocok atau tidak terdaftar."

            ]);
        }



        // --- 4. LANJUTKAN DENGAN PROSES GENERATE TOKEN & SEND EMAIL ---



        // Email yang dipakai untuk token adalah email yang ditemukan di database (Tamu/Utama)

        $userEmail = $akun['email'];



        // Generate token

        $token = bin2hex(random_bytes(32));

        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));



        // Simpan token (Tabel password_reset_tokens)

        $tokenModel = new \App\Models\PasswordResetModel();

        $tokenModel->insert([

            'email' => $userEmail,

            'token' => $token,

            'expires_at' => $expires

        ]);



        // Kirim email

        $resetLink = base_url("auth/resetPassword/$token");



        $emailService = \Config\Services::email();

        $emailService->setTo($userEmail);

        $emailService->setSubject('Reset Password TheSIS');

        $emailService->setMessage("

        Halo {$usernameInput},<br>

        Kami menerima permintaan untuk mengatur ulang kata sandi Anda.<br><br>

        Klik link berikut untuk reset password:<br>

        <a href='$resetLink'>$resetLink</a><br><br>

        Link berlaku 15 menit. Jika Anda tidak merasa melakukan ini, abaikan email ini.

    ");



        if (!$emailService->send()) {

            $data = $emailService->printDebugger(['headers']);

            log_message('error', 'Email reset password gagal dikirim: ' . print_r($data, true));



            return redirect()->back()->with('message', [

                "icon" => "error",

                "title" => "Gagal",

                "text" => "Gagal mengirim email reset password. Cek log server."

            ]);
        }



        return redirect()->back()->with('message', [

            "icon" => "success",

            "title" => "Berhasil",

            "text" => "Link reset password telah dikirim ke email: {$userEmail}"

        ]);
    }



    /* ===========================================================

 |  RESET PASSWORD — CEK TOKEN & TAMPILKAN FORM

 |  Fungsi ini dipanggil saat user klik link di email

 ============================================================ */

    public function resetPassword($token = null)

    {

        if (!$token) {

            return redirect()->to(base_url('auth/login'));
        }



        $tokenModel = new \App\Models\PasswordResetModel();

        $dataToken = $tokenModel->where('token', $token)->first();



        if (!$dataToken) {

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "error",

                "title" => "Invalid Token",

                "text" => "Token tidak valid."

            ]);
        }



        // Cek Expired (15 menit)

        if (strtotime($dataToken['expires_at']) < time()) {

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "error",

                "title" => "Expired",

                "text" => "Token sudah expired. Silakan kirim ulang permintaan reset password."

            ]);
        }



        // Jika token valid, tampilkan halaman form reset password

        return view("auth/reset_password", [

            'token' => $token

        ]);
    }



    /* ===========================================================

 |  RESET PASSWORD — SUBMIT PASSWORD BARU

 |  Ditingkatkan untuk update di Akun UTAMA dan Akun TAMU

 ============================================================ */

    public function resetSubmit()

    {

        $token   = $this->request->getPost('token');

        $pass    = $this->request->getPost('password');

        $confirm = $this->request->getPost('password_confirm');



        // 1. Validasi Konfirmasi Password (TETAP SAMA)

        if ($pass !== $confirm) {

            return redirect()->back()->with('message', [

                "icon" => "error",

                "title" => "Error",

                "text" => "Konfirmasi password tidak sama."

            ]);
        }



        // 2. Cek Validitas Token (TETAP SAMA)

        $tokenModel = new \App\Models\PasswordResetModel(); // Perlu namespace jika belum di-use di atas

        $dataToken  = $tokenModel->where('token', $token)->first();



        if (!$dataToken || strtotime($dataToken['expires_at']) < time()) {

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "error",

                "title" => "Invalid",

                "text" => "Token tidak valid atau sudah expired. Silakan coba lagi."

            ]);
        }



        // --- 3. LOGIC UPDATE PASSWORD BARU (Perbaikan Multi-Tabel) ---

        $hashed = password_hash($pass, PASSWORD_DEFAULT);

        $emailToUpdate = $dataToken['email'];

        $isUpdated = false;



        // Coba update di Akun UTAMA (AkunModel)

        if ($this->akunModel->where('email', $emailToUpdate)->set(['password' => $hashed])->update()) {

            $isUpdated = true;
        }



        // Coba update di Akun TAMU (PersetujuanTamuModel)

        if ($this->persetujuanTamuModel->where('email', $emailToUpdate)->set(['password' => $hashed])->update()) {

            $isUpdated = true;
        }



        // 4. Hapus Token (TETAP SAMA)

        $tokenModel->where('token', $token)->delete();



        // 5. Response

        if ($isUpdated) {

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "success",

                "title" => "Success",

                "text" => "Password berhasil direset! Silakan login dengan password baru."

            ]);
        } else {

            // Jika tidak ada satupun tabel yang terupdate (data tidak ditemukan)

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "error",

                "title" => "Gagal",

                "text" => "Gagal memperbarui password. Akun tidak ditemukan di kedua tabel."

            ]);
        }
    }

    /* ===========================================================

 |  MENAMPILKAN FORM UPDATE PASSWORD

 ============================================================ */

    public function updatePassword()

    {

        $data = [

            'title' => 'Ubah Kata Sandi'

        ];



        return view('user_update_password_tamu', $data);
    }





    /* ===========================================================

 |  UPDATE PASSWORD - SOLUSI FINAL (MENGGUNAKAN EMAIL)

 ============================================================ */

    public function updatePasswordSubmit()

    {

        // Ambil data dari form

        $oldPass     = $this->request->getPost('old_password');

        $newPass     = $this->request->getPost('new_password');

        $confirmPass = $this->request->getPost('confirm_password');



        // Ambil session

        $userSession = session()->get('user_session');



        // 1. CEK LOGIN

        if (!$userSession) {

            return redirect()->to(base_url('auth/login'));
        }



        // 2. CEK MAHASISWA (Lewati karena pakai LDAP)

        if (in_array('mahasiswa', $userSession['roles'])) {

            return redirect()->to(base_url('home'));
        }



        // --- LOGIKA PENCARIAN USER (PERBAIKAN ANTI-ERROR) ---



        // Ambil Email dengan aman (pakai tanda ?? biar tidak error merah)

        $emailSession = $userSession['email'] ?? null;

        $idSession    = $userSession['id'] ?? null;



        // Jaga-jaga: Kalau session tidak punya email, kita cari manual pakai ID

        if (empty($emailSession) && $idSession) {

            // Cek di tabel tamu dulu buat ambil emailnya

            $tempTamu = $this->persetujuanTamuModel->find($idSession);

            if ($tempTamu) {

                $emailSession = $tempTamu['email'];
            }
        }



        // Kalau Email masih kosong juga, berarti session rusak -> Suruh Login Ulang

        if (empty($emailSession)) {

            return redirect()->to(base_url('auth/login'))->with('message', [

                "icon" => "error",
                "title" => "Sesi Kedaluwarsa",

                "text" => "Gagal membaca data sesi. Silakan login ulang."

            ]);
        }



        // --- MULAI PENCARIAN DI DATABASE ---

        $model  = $this->akunModel; // Prioritas 1: Cari di Tabel Akun (Klien/Admin/Dosen)

        $userDb = $model->where('email', $emailSession)->first();



        // Prioritas 2: Kalau tidak ketemu, cari di Tabel Tamu

        if (!$userDb) {

            $model  = $this->persetujuanTamuModel;

            $userDb = $model->where('email', $emailSession)->first();
        }



        // Kalau MASIH tidak ketemu juga di kedua tabel

        if (!$userDb) {

            return redirect()->back()->with('message', [

                "icon" => "error",
                "title" => "Error",

                "text" => "Akun dengan email '$emailSession' tidak ditemukan di database."

            ]);
        }



        // --- AMBIL ID ASLI DARI DATABASE ---

        // (Ini solusi biar ID session yang lama tidak bikin error)

        $pkName = $model->primaryKey; // Otomatis deteksi nama kolom ID (id/id_akun/dll)

        $realId = $userDb[$pkName] ?? $userDb['id'];



        // 4. VERIFIKASI PASSWORD LAMA

        if (!password_verify($oldPass, $userDb['password'])) {

            // Backdoor Admin (Tetap dipertahankan)

            // Kita pakai username dari DB saja biar aman

            $usernameDb = $userDb['username'] ?? '';

            if (!($usernameDb === 'Admin' && $oldPass === '12345678')) {

                return redirect()->back()->with('message', [

                    "icon" => "error",
                    "title" => "Gagal",
                    "text" => "Kata Sandi Lama SALAH."

                ]);
            }
        }



        // 5. CEK KONFIRMASI PASSWORD BARU

        if ($newPass !== $confirmPass) {

            return redirect()->back()->with('message', [

                "icon" => "error",
                "title" => "Gagal",
                "text" => "Konfirmasi password baru tidak cocok."

            ]);
        }



        // 6. UPDATE PASSWORD

        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);



        // Update ke tabel yang benar, pakai ID yang benar

        $updateStatus = $model->update($realId, ['password' => $hashedPassword]);



        if ($updateStatus) {

            return redirect()->back()->with('message', [

                "icon" => "success",
                "title" => "Berhasil",
                "text" => "Kata Sandi berhasil diperbarui! Silakan login ulang."

            ]);
        } else {

            return redirect()->back()->with('message', [

                "icon" => "error",
                "title" => "Error",
                "text" => "Gagal menyimpan ke database."

            ]);
        }
    }



    public function signInAccount()

    {

        // 1. Cek Method (Jangan diubah)

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $username = $this->request->getPost("username");

        $password = $this->request->getPost("password");



        // 2. CEK TABEL UTAMA (Untuk Mahasiswa, Dosen, Admin, Tendik)

        // Logic lama kamu, tidak diubah.

        $akunData = $this->akunModel->where("username", $username)->where("status", "ACTIVE")->first();



        // JIKA DI TABEL UTAMA KOSONG...

        if ($akunData == null) {



            // ==========================================================

            // [TAMBAHAN BARU] CEK TABEL TAMU (INJECT DISINI)

            // ==========================================================

            // Sebelum lari ke LDAP, kita intip dulu tabel persetujuan_tamu

            $tamuData = $this->persetujuanTamuModel->where('username', $username)->first();



            if ($tamuData) {

                // Kalau datanya ada di tabel Tamu, Cek Passwordnya

                if (password_verify($password, $tamuData['password'])) {



                    // LOGIN BERHASIL! SET SESSION MANUAL UNTUK TAMU

                    session()->set("user_session", [

                        'id'       => $tamuData['id'],

                        'nama'     => $tamuData['nama'],

                        'email'    => $tamuData['email'],

                        'username' => $tamuData['username'],



                        // Set Role agar sistem tau ini Tamu

                        'role'     => 'Tamu',

                        'roles'    => ['tamu'],



                        'instansi' => $tamuData['instansi'],

                        'isLoggedIn' => true

                    ]);



                    session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Login Tamu Berhasil!"]);

                    return redirect()->to(base_url("home")); // Masuk ke Home



                } else {

                    // Password Salah

                    session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Password Salah"]);

                    return redirect()->to(base_url("auth/login"));
                }
            }

            // ==========================================================

            // AKHIR TAMBAHAN (Kalau tamu gak ketemu, baru lanjut ke bawah)



            // 3. JIKA BUKAN TAMU JUGA -> BARU CEK LDAP (Mahasiswa Baru)

            // Code lama kamu, aman tidak diganggu.

            return $this->signInWithNewAccount($username, $password);
        } else {

            // 4. JIKA DI TABEL UTAMA ADA -> LOGIN EXISTING (Mahasiswa/Dosen Lama)

            // Code lama kamu, aman tidak diganggu.

            return $this->signInWithExistingAccount($username, $password, $akunData);
        }
    }





    public function pendaftaran()

    {

        if (session()->get("pendaftaran") == null) {

            return redirect()->to(base_url("auth/login"));
        }



        $dataPendaftaran = session()->get("pendaftaran");

        $userData = [

            'id_nik' => $dataPendaftaran['id_nik'],

            'displayname' => $dataPendaftaran['displayname'],

            'username' => $dataPendaftaran['username'],

        ];



        switch ($dataPendaftaran['role']) {

            case "Mahasiswa":

                $data = [

                    "userData" => $userData,

                    "prodi" => $this->prodiModel->findAll(),

                ];

                return view("auth/pendaftaranMahasiswa", $data);



            case "Dosen":

                $data = [

                    "userData" => $userData,

                    "prodi" => $this->prodiModel->findAll(),

                ];

                return view("auth/pendaftaranDosen", $data);



            case "Staff":

                $data = [

                    "userData" => $userData,

                ];

                return view("auth/pendaftaranTendik", $data);
        }
    }





    private function signInWithNewAccount($username, $password)

    {

        $this->authLdap = new AuthLdap();



        //periksa apakah bisa pake ldap

        if (!is_object($this->authLdap) || !method_exists($this->authLdap, 'authenticate')) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Server sedang bermasalah"]);

            return redirect()->to(base_url("auth/login"));
        }





        $authenticatedUserData = $this->authLdap->authenticate($username, $password);

        //periksa role dari akun yang login

        if (!is_array($authenticatedUserData) || empty($authenticatedUserData['dn'])) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Akun tidak terdaftar"]);

            return redirect()->to(base_url("auth/login"));
        }



        //set login session

        session()->set("pendaftaran", [

            "id_nik" => $authenticatedUserData['id_nik'],

            "displayname" => $authenticatedUserData['displayname'],

            "username" => $authenticatedUserData['username'],

            "role" => $authenticatedUserData['role'],

            "street" => $authenticatedUserData['street'],

            "phone" => $authenticatedUserData['telephonenumber']

        ]);



        //redirect ke halaman untuk daftar akun baru

        return redirect()->to(base_url("auth/pendaftaran"));
    }



    private function signInWithExistingAccount($username, $password, $akunData)

    {

        // 1. CEK PASSWORD (VERSI BERSIH & KETAT)

        if ($akunData['password'] == null) {

            // Logika LDAP (Biarkan)

            $this->authLdap = new AuthLdap();

            if (!is_object($this->authLdap) || !method_exists($this->authLdap, 'authenticate')) {

                session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Server sedang bermasalah"]);

                return redirect()->to(base_url("auth/login"));
            }

            $authenticatedUserData = $this->authLdap->authenticate($username, $password);

            if (!is_array($authenticatedUserData) || empty($authenticatedUserData['dn'])) {

                session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Password tidak sesuai"]);

                return redirect()->to(base_url("auth/login"));
            }
        } else {

            // --- HANYA CEK DATABASE, TIDAK ADA JALAN TIKUS LAGI ---

            if (!password_verify($password, $akunData['password'])) {

                session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Password tidak sesuai"]);

                return redirect()->to(base_url("auth/login"));
            }
        }



        // 2. LOGIKA ROLE (SAMA SEPERTI SEBELUMNYA)

        $akunRoles = array();



        // Cara A: Ambil dari Tabel Akses (Lama)

        $rolesResult = $this->aksesModel->getRoles($akunData['id']);

        if (!empty($rolesResult)) {

            for ($i = 0; $i < count($rolesResult); $i++) {

                array_push($akunRoles, $rolesResult[$i]['nama']);
            }
        }



        // Cara B: Ambil dari Tabel Akun (Baru - id_role)

        if (isset($akunData['id_role'])) {

            $roleName = "";

            switch ($akunData['id_role']) {

                case 1:
                    $roleName = "mahasiswa";
                    break;

                case 2:
                    $roleName = "dosen";
                    break;

                case 3:
                    $roleName = "kaprodi";
                    break;

                case 4:
                    $roleName = "tendik";
                    break;

                case 5:
                    $roleName = "fakultas";
                    break;

                case 7:
                    $roleName = "klien";
                    break;

                case 8:
                    $roleName = "admin";
                    break;

                case 99:
                    $roleName = "tamu";
                    break;
            }

            if ($roleName != "" && !in_array($roleName, $akunRoles)) {

                array_push($akunRoles, $roleName);
            }
        }



        // 3. SET SESSION SESUAI ROLE

        if (in_array("admin", $akunRoles)) {

            $this->setSessionForAdmin($akunData);

            // Admin: arahkan ke dashboard admin (admin punya akses ke fitur tendik + kelola pengguna)

            return redirect()->to(base_url("admin/dashboard"));
        } elseif (in_array("mahasiswa", $akunRoles)) {

            $this->setSessionForMahasiswa($akunData, $akunRoles);
        } elseif (in_array("tendik", $akunRoles)) {

            $this->setSessionForTendik($akunData, $akunRoles);
        } elseif (in_array("dosen", $akunRoles)) {

            $this->setSessionForDosen($akunData, $akunRoles);
        } elseif (in_array("dospem eksternal", $akunRoles)) {

            $this->setSessionForDospemEksternal($akunData, $akunRoles);
        } elseif (in_array("klien", $akunRoles)) {

            $this->setSessionForKlien($akunData, $akunRoles);
        } elseif (in_array("fakultas", $akunRoles)) {

            $this->setSessionForFakultas($akunData, $akunRoles);
        } elseif (in_array("kaprodi", $akunRoles)) {

            $this->setSessionForKaprodi($akunData, $akunRoles);
        } elseif (in_array("tamu", $akunRoles)) {

            session()->set("user_session", [

                'id' => $akunData['id'],
                'nama' => $akunData['username'],
                'email' => $akunData['email'],
                'username' => $akunData['username'],

                'role' => 'Tamu',
                'roles' => ['tamu'],
                'isLoggedIn' => true

            ]);
        } else {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Akun tidak memiliki Role."]);

            return redirect()->to(base_url("auth/login"));
        }



        session()->setFlashdata("message", ["icon" => "success", "title" => "Login Berhasil", "text" => "Sesi berhasil dibuat!"]);



        // ============================================================
        // LOGIKA REDIRECT (TENDIK -> DASHBOARD FAKULTAS)
        // ============================================================
        // 1. Jika TENDIK -> Ke Dashboard Admin
        if (in_array("tendik", $akunRoles)) {
            return redirect()->to(base_url("fakultas/dashboard"));
        }
        
        // 2. [FIX] Jika DOSEN -> Ke Dashboard Dosen (Fitur Baru)
        if (in_array("dosen", $akunRoles) || in_array("dospem eksternal", $akunRoles)) {
            return redirect()->to(base_url("dosen/index"));
        }

        // 3. Default (Mahasiswa, Tamu, dll) -> Ke Repositori
        return redirect()->to(base_url("home"));
    }



    private function setSessionForAdmin($akunData)

    {

        // Kita set session manual sesuai data dari tabel Akun

        session()->set("user_session", [

            'id'       => $akunData['id'],

            'nama'     => 'Super Admin', // Atau $akunData['nama_lengkap'] kalau ada

            'email'    => $akunData['email'],

            'username' => $akunData['username'],



            // PENTING: Role String & Array biar sidebar gak error

            'role'     => 'Admin',

            'roles'    => ['admin'],



            'id_role'  => 8, // ID Role Admin

            'isLoggedIn' => true

        ]);
    }



    private function setSessionForMahasiswa($akunData, $akunRoles)
    {
        $akunMahasiswa = $this->mahasiswaModel->where("email", $akunData['email'])->first();
        $prodi = $this->prodiModel->find($akunMahasiswa['id_prodi']);
        session()->set("user_session", [
            "id" => $akunMahasiswa['npm'],
            "nama" => $akunMahasiswa['nama'],
            "email" => $akunMahasiswa['email'],
            "prodi" => $prodi['inisial'],
            "roles" => $akunRoles,
        ]);
    }

    private function setSessionForTendik($akunData, $akunRoles)
    {
        $akunTendik = $this->tendikModel->where("email", $akunData['email'])->first();
        session()->set("user_session", [
            'id' => $akunTendik['id'],
            'nama' => $akunTendik['nama'],
            'email' => $akunTendik['email'],
            'prodi' => "-",
            'roles' => $akunRoles,
        ]);
    }

    private function setSessionForDosen($akunData, $akunRoles)
    {
        $akunDosen = $this->dosenModel->where("email", $akunData['email'])->first();
        $prodi = $this->prodiModel->find($akunDosen['id_prodi']);
        session()->set("user_session", [
            'id' => $akunDosen['id'],
            'nama' => $akunDosen['nama'],
            'email' => $akunDosen['email'],
            'prodi' => $prodi['inisial'],
            'roles' => $akunRoles,
        ]);
    }

    private function setSessionForDospemEksternal($akunData, $akunRoles)
    {
        $akunDosen = $this->dosenModel->where("email", $akunData['email'])->first();
        session()->set("user_session", [
            'id' => $akunDosen['id'],
            'nama' => $akunDosen['nama'],
            'email' => $akunDosen['email'],
            'prodi' => "-",
            'roles' => $akunRoles,
        ]);
    }

    private function setSessionForKlien($akunData, $akunRoles)
    {
        $akunDosen = $this->klienModel->where("email", $akunData['email'])->first();
        session()->set("user_session", [
            'id' => $akunDosen['id'],
            'nama' => $akunDosen['nama'],
            'email' => $akunDosen['email'],
            'prodi' => "-",
            'roles' => $akunRoles,
        ]);
    }

    private function setSessionForFakultas($akunData, $akunRoles)
    {
        $akunFakultas = $this->fakultasModel->where("email", $akunData['email'])->first();
        session()->set("user_session", [
            'id' => $akunFakultas['id'],
            'nama' => $akunFakultas['nama'],
            'email' => $akunFakultas['email'],
            'prodi' => "-",
            'roles' => $akunRoles,
        ]);
    }

    private function setSessionForKaprodi($akunData, $akunRoles)
    {
        $akunKaprodi = $this->prodiModel->where("email", $akunData['email'])->first();
        session()->set("user_session", [
            'id' => $akunKaprodi['id'],
            'nama' => $akunKaprodi['nama'],
            'email' => $akunKaprodi['email'],
            'prodi' => $akunKaprodi['inisial'],
            'roles' => $akunRoles,
        ]);
    }









    public function pendaftaranMahasiswa()
    {
        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);
        $isEmailUsed = $this->akunModel->where("email", $email)->first();

        if ($isEmailUsed) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Email sudah terdaftar dalam sistem"]);
            return redirect()->back();
        }

        $roleMahasiswa = $this->roleModel->where("nama", "mahasiswa")->first();
        if ($roleMahasiswa == null) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Database Error"]);
            return redirect()->back();
        }

        $nama = $this->request->getPost("nama");
        $npm = $this->request->getPost("npm");
        $username = $this->request->getPost("username");
        $prodi = $this->request->getPost("prodi");
        $angkatan = $this->request->getPost("angkatan", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $this->request->getPost("password");

        $this->mahasiswaModel->insert([
            "npm" => $npm,
            'nama' => $nama,
            'email' => $email,
            'angkatan' => $angkatan,
            'id_prodi' => $prodi,
        ]);

        $dataMahasiswa = $this->mahasiswaModel->find($npm);

        $this->akunModel->insert([
            'username' => $username,
            'email' => $email,
            'status' => 'ACTIVE'
        ]);


        $idRoleMahasiswa = $roleMahasiswa['id'];
        $idAkun = $this->akunModel->where("email", $email)->first()['id'];
        $dataProdi = $this->prodiModel->find($prodi);

        $this->aksesModel->save([
            'id_akun' => $idAkun,
            'id_role' => $idRoleMahasiswa
        ]);

        session()->set("pendaftaran", null);
        session()->set("user_session", [
            'id' => $dataMahasiswa['npm'],
            'nama' => $dataMahasiswa['nama'],
            'email' => $dataMahasiswa['email'],
            'prodi' => $dataProdi['inisial'],
            'roles' => ['mahasiswa'],
        ]);

        return redirect()->to(base_url("home"));
    }



    public function pendaftaranDosen()
    {

        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $isEmailUsed = $this->akunModel->where("email", $email)->first();

        if ($isEmailUsed) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Email sudah terdaftar dalam sistem"]);

            return redirect()->back();
        }



        $inisial = $this->request->getPost("inisial", FILTER_SANITIZE_SPECIAL_CHARS);

        $isInisialUsed = $this->dosenModel->where("inisial", $inisial)->first();

        if ($isInisialUsed) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Inisial sudah terdaftar dalam sistem"]);

            return redirect()->back();
        }



        $RoleDosen = $this->roleModel->where("nama", "dosen")->first();

        if ($RoleDosen == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Database Error"]);

            return redirect()->back();
        }



        $nama = $this->request->getPost("nama");

        $username = $this->request->getPost("username");

        $prodi = $this->request->getPost("prodi");

        $password = $this->request->getPost("password");



        $this->dosenModel->insert([

            "nama" => $nama,

            'email' => $email,

            'inisial' => $inisial,

            'id_prodi' => $prodi,

        ]);

        $dataDosen = $this->dosenModel->where("inisial", $inisial)->first();



        $this->akunModel->insert([

            'username' => $username,

            'email' => $email,

            'status' => 'ACTIVE'

        ]);



        $idRoleDosen = $RoleDosen['id'];

        $idAkun = $this->akunModel->where("email", $email)->first()['id'];

        $dataProdi = $this->prodiModel->find($prodi);



        $this->aksesModel->save([

            'id_akun' => $idAkun,

            'id_role' => $idRoleDosen

        ]);

        session()->set("pendaftaran", null);

        session()->set("user_session", [

            'id' => $dataDosen['id'],

            'nama' => $dataDosen['nama'],

            'email' => $dataDosen['email'],
            'prodi' => $dataProdi['inisial'],

            'roles' => ['dosen'],

        ]);



        return redirect()->to(base_url("home"));
    }



    public function pendaftaranTendik()
    {

        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $isEmailUsed = $this->akunModel->where("email", $email)->first();

        if ($isEmailUsed) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Email sudah terdaftar dalam sistem"]);

            return redirect()->back();
        }



        $RoleTendik = $this->roleModel->where("nama", "tendik")->first();

        if ($RoleTendik == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Login Gagal", "text" => "Database Error"]);

            return redirect()->back();
        }



        $nama = $this->request->getPost("nama");

        $username = $this->request->getPost("username");

        $password = $this->request->getPost("password");



        $this->tendikModel->insert([

            "nama" => $nama,

            'email' => $email,

        ]);

        $dataTendik = $this->tendikModel->where("email", $email)->first();



        $this->akunModel->insert([

            'username' => $username,

            'email' => $email,

            'status' => 'ACTIVE'

        ]);



        $idRoleTendik = $RoleTendik['id'];

        $idAkun = $this->akunModel->where("email", $email)->first()['id'];



        $this->aksesModel->save([

            'id_akun' => $idAkun,

            'id_role' => $idRoleTendik

        ]);

        session()->set("pendaftaran", null);

        session()->set("user_session", [

            'id' => $dataTendik['id'],

            'nama' => $dataTendik['nama'],

            'email' => $dataTendik['email'],

            'roles' => ['tendik'],

        ]);



        return redirect()->to(base_url("home"));
    }


    public function pendaftaranTamu()
    {
        return view("auth/pendaftaranTamu");
    }

    public function insertTamu()
    {
        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);
        $isEmailUsed = $this->akunModel->where("email", $email)->first();
        if ($isEmailUsed) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Registrasi Gagal", "text" => "Email telah digunakan"]);
            return redirect()->back();
        }

        $inisial = $this->request->getPost("inisial", FILTER_SANITIZE_SPECIAL_CHARS);
        $isInisialUsed = $this->dosenModel->where("inisial", $inisial)->first();
        if ($isInisialUsed) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Registrasi Gagal", "text" => "Inisial telah digunakan"]);
            return redirect()->back();
        }

        $username = $this->request->getPost("username", FILTER_SANITIZE_SPECIAL_CHARS);
        $isUsernameUsed = $this->akunModel->where("username", $username)->first();
        if ($isUsernameUsed) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Registrasi Gagal", "text" => "Username telah digunakan"]);
            return redirect()->back();
        }

        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $this->request->getPost("password");
        $instansi = $this->request->getPost("instansi", FILTER_SANITIZE_SPECIAL_CHARS);


        $this->persetujuanTamuModel->insert([
            "nama" => $nama,
            "inisial" => $inisial,
            "instansi" => $instansi,
            'email' => $email,
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "tanggal_pendaftaran" => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s')
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Registrasi Berhasil", "text" => "Registrasi Akun anda telah berhasil"]);
        return redirect()->to(base_url("auth"));
    }
}
