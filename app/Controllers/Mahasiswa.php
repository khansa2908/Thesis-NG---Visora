<?php



namespace App\Controllers;


use App\Models\SkripsiModel;
use CodeIgniter\I18n\Time;
use Exception;



class Mahasiswa extends BaseController
{

    protected $mahasiswaModel;

    protected $prodiModel;

    protected $proposalModel;

    protected $dosenModel;

    protected $bidangModel;

    protected $semproModel;

    protected $pembimbingModel;

    protected $catatanBimbinganModel;

    protected $skripsiModel;

    protected $pengajuanPrasidangModel;

    protected $pengajuanSidangModel;

    protected $seminarPrasidangModel;

    protected $sidangSkripsiModel;

    protected $penilaianSidangModel;

    protected $makalahModel;

    protected $semesterModel;

    protected $akunModel;

    protected $modeSemproModel;

    protected $penilaianSidangNewModel;

    protected $penilaianSidangParamModel;

    protected $pengajuanHardcoverModel;

    public function __construct()
    {

        $this->mahasiswaModel = new \App\Models\MahasiswaModel();

        $this->prodiModel = new \App\Models\ProgramStudiModel();

        $this->proposalModel = new \App\Models\ProposalModel();

        $this->dosenModel = new \App\Models\DosenModel();

        $this->bidangModel = new \App\Models\BidangModel();

        $this->semproModel = new \App\Models\SeminarProposalModel();

        $this->pembimbingModel = new \App\Models\PembimbingModel();

        $this->catatanBimbinganModel = new \App\Models\CatatanBimbinganModel();

        $this->skripsiModel = new \App\Models\SkripsiModel();

        $this->pengajuanPrasidangModel = new \App\Models\PengajuanPrasidangModel();

        $this->pengajuanSidangModel = new \App\Models\PengajuanSidangModel();

        $this->seminarPrasidangModel = new \App\Models\SeminarPrasidangModel();

        $this->sidangSkripsiModel = new \App\Models\SidangSkripsiModel();

        $this->penilaianSidangModel = new \App\Models\PenilaianSidangModel();

        $this->makalahModel = new \App\Models\MakalahModel();

        $this->semesterModel = new \App\Models\SemesterModel();

        $this->akunModel = new \App\Models\AkunModel();

        $this->modeSemproModel = new \App\Models\ModeSemproModel();

        $this->penilaianSidangNewModel = new \App\Models\PenilaianSidangNewModel();

        $this->penilaianSidangParamModel = new \App\Models\PenilaianSidangParamModel();

        $this->pengajuanHardcoverModel = new \App\Models\PengajuanHardcoverModel();
    }



    public function index()
    {
        // 1. Autentikasi
        if (!$this->authenticate(["mahasiswa"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 2. Ambil NPM dari session (pastikan saat login NPM sudah disimpan di session)
        $npm = session()->get('npm'); 

        // 3. Panggil fungsi progres dari SkripsiModel yang sudah kita buat
        $progresData = $this->skripsiModel->getThesisProgress($npm);

        // 4. Siapkan data untuk dikirim ke view
        $data = [
            'title'        => 'Dashboard Mahasiswa',
            'skripsi'      => $progresData ? $progresData['detail'] : null,
            'current_step' => $progresData ? $progresData['current_step'] : 0,
            'tahapan'      => [
                "Penyusunan", "Draft Proposal", "Jadwal Sempro", 
                "Judul Final", "Draft Prasidang", "Pra Sidang", 
                "Draft Sidang", "Jadwal Sidang", "Sidang Akhir"
            ]
        ];

        // 5. Tampilkan view dashboard mahasiswa (Pastikan file ini ada di app/Views/mahasiswa/dashboard.php)
        return view('mahasiswa/dashboard', $data);
    }







    public function pengajuanPenulisanSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $dosen = $this->dosenModel->getDosenByProdi($mahasiswa['id_prodi']);



        $data = [

            'title' => 'Pengajuan Penulisan Skripsi',

            'mahasiswa' => $mahasiswa,

            'dosen' => $dosen,

        ];



        return view("mahasiswa/pengajuan_penulisan_skripsi", $data);

    }



    public function insertPengajuanPenulisanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $mahasiswa = $this->mahasiswaModel->find($npm);



        $fileKhsLama = $this->request->getFile("inputKhs");

        $fileKhsBaru = $mahasiswa['file_khs'];

        if ($fileKhsLama != null) {

            $fileKhsBaru = "KHS_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileKhsLama->getClientExtension();

            $fileKhsLama->move("folderKHS", $fileKhsBaru);

        }



        $fileKrsLama = $this->request->getFile("inputKrs");

        $fileKrsBaru = $mahasiswa['file_krs'];

        if ($fileKrsLama != null) {

            $fileKrsBaru = "KRS_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileKrsLama->getClientExtension();

            $fileKrsLama->move("folderKRS", $fileKrsBaru);

        }



        $filePengajuanSkripsiLama = $this->request->getFile("inputPersetujuanSkripsi");

        $filePengajuanSkripsiBaru = $mahasiswa['file_persetujuan_skripsi'];

        if ($filePengajuanSkripsiLama != null) {

            $filePengajuanSkripsiBaru = "PersetujuanSkripsi_" . $npm . "_" . $this->generateTimeStamp() . "." . $filePengajuanSkripsiLama->getClientExtension();

            $filePengajuanSkripsiLama->move("folderPersetujuanSkripsi", $filePengajuanSkripsiBaru);

        }



        $this->mahasiswaModel->update($npm, [

            "file_khs" => $fileKhsBaru,

            "file_krs" => $fileKrsBaru,

            "file_persetujuan_skripsi" => $filePengajuanSkripsiBaru,

            "sks_lulus" => $this->request->getPost("sks_lulus", FILTER_SANITIZE_SPECIAL_CHARS),

            "pembimbing_akademik" => $this->request->getPost("pembimbing_akademik"),

            "mk_sedang_diambil" => $this->request->getPost("mk_sedang_diambil", FILTER_SANITIZE_SPECIAL_CHARS),

            "mk_akan_diambil" => $this->request->getPost("mk_akan_diambil", FILTER_SANITIZE_SPECIAL_CHARS),

            "status_persetujuan_skripsi" => null,

            'tanggal_pengajuan_skripsi' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Upload Data Berhasil", "text" => "Data Pengajuan Penulisan Skripsi Berhasil diunggah"]);

        return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

    }













    public function proposal()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        // [BARU] Cek Jadwal Sempro DISINI
        $cek = $this->checkJadwal('sempro');

        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $proposal = $this->proposalModel->getProposalMahasiswa($mahasiswa['npm']);

        $lastProposal = $this->proposalModel->getMahasiswaLastProposal($mahasiswa['npm']);

        $dosen = $this->dosenModel->getDosenByProdi($mahasiswa['id_prodi']);

        $bidang = $this->bidangModel->getBidangByProdi($mahasiswa['id_prodi']);

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);

        for ($i = 0; $i < count($proposal); $i++) {

            $proposal[$i]['editable'] = $this->proposalModel->isEditable($proposal[$i]['id']);

        }

        $currentSemester = $this->semesterModel->getActiveSemester();

        $modeSempro = $this->modeSemproModel->getModeSempro($currentSemester['kode'], $prodi['id']);



        $data = [

            'title' => "Kelola Proposal Skripsi",

            "mahasiswa" => $mahasiswa,

            "proposal" => $proposal,

            "dosen" => $dosen,

            "prodi" => $prodi,

            "bidang" => $bidang,

            "lastProposal" => $lastProposal,

            "lastSkripsi" => $lastSkripsi,

            "currentSemester" => $currentSemester,

            "modeSempro" => $modeSempro,

            // [BARU] Kirim status jadwal ke view
            'is_open' => $cek['status'],
            'deadline' => ($cek['data']) ? $cek['data']['tanggal_selesai'] : null,
            'pesan_tutup' => $cek['msg'] // Pesan jika tutup
        ];

        return view('mahasiswa/proposal', $data);

    }



    public function detailProposal($idProposal)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $detailProposal = $this->proposalModel->getDetailProposalById($idProposal);



        if ($detailProposal == null || $dataAkun == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        if ($detailProposal['npm'] != $dataAkun['npm']) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }

        $bidang = $this->bidangModel->getBidangByProdi($dataAkun['id_prodi']);

        $dosen = $this->dosenModel->getDosenByProdi($dataAkun['id_prodi']);



        $data = [

            'title' => 'Detail Proposal Skripsi',

            'detailProposal' => $detailProposal,

            'bidang' => $bidang,

            'dosen' => $dosen,

        ];



        return view("mahasiswa/detail_proposal", $data);

    }



    public function insertProposal()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        // --- CEK JADWAL DULU ---
        $cek = $this->checkJadwal('sempro'); // Parameter: 'sempro', 'prasidang', atau 'sidang'
        
        if ($cek['status'] === false) {
            // Blokir Aksi
            session()->setFlashdata("message", ["icon" => "error", "title" => "Akses Ditolak", "text" => $cek['msg']]);
            return redirect()->back();
        }
        
        // Jika lolos, kirim warning (jika ada) ke view atau flashdata info
        if (!empty($cek['msg'])) {
            session()->setFlashdata("warning_deadline", $cek['msg']);
        }

        // [BARU] Cegat proses insert jika jadwal tutup
        $cek = $this->checkJadwal('sempro');
        if ($cek['status'] === false) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Jadwal Pengajuan Proposal ditutup."]);
            return redirect()->back();
        }

        $validationRules = [

            'file_proposal' => [

                'rules' => 'uploaded[file_proposal]|mime_in[file_proposal,application/pdf]|ext_in[file_proposal,pdf]|max_size[file_proposal,10000]',

                'errors' => [

                    'uploaded' => 'Pilih File Proposal terlebih dahulu',

                    'mime_in' => 'File Proposal harus berupa PDF',

                    'ext_in' => 'File Proposal harus berekstensi .pdf',

                    'max_size' => 'Ukuran File Proposal tidak boleh lebih dari 10MB'

                ]

            ]

        ];



        if (!$this->validate($validationRules)) {

            $validation = \Config\Services::validation();

            session()->setFlashdata("message", ["icon" => "error", "title" => "Upload File Proposal Gagal", "text" => $validation->getError("file_proposal")]);

            return redirect()->to(base_url("mahasiswa/proposal"))->withInput();

        }



        $npm = $this->request->getPost("npm", FILTER_SANITIZE_SPECIAL_CHARS);

        $fileProposal = $this->request->getFile("file_proposal");

        $newName = "Proposal_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileProposal->getClientExtension();

        $fileProposal->move("folderProposal", $newName);



        $this->proposalModel->save([

            'judul' => $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS),

            'id_bidang' => $this->request->getPost("bidang", FILTER_SANITIZE_SPECIAL_CHARS),

            'sifat' => $this->request->getPost("sifat", FILTER_SANITIZE_SPECIAL_CHARS),

            'sumber' => $this->request->getPost("sumber", FILTER_SANITIZE_SPECIAL_CHARS),

            'file_proposal' => $newName,

            'npm' => $npm,

            'tanggal_upload' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            'dosen_usulan1' => $this->request->getPost("dosen_usulan1", FILTER_SANITIZE_SPECIAL_CHARS),

            'dosen_usulan2' => $this->request->getPost("dosen_usulan2", FILTER_SANITIZE_SPECIAL_CHARS),

            'kode_semester' => $this->semesterModel->getActiveSemester()['kode'],

            'status' => 'TERTUNDA'

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Tambah Proposal Berhasil", "text" => "Proposal berhasil ditambahkan"]);

        return redirect()->to(base_url("mahasiswa/proposal"));

    }



    public function updateProposal($idProposal)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $proposal = $this->proposalModel->find($idProposal);

        $npm = $this->request->getPost("npm");



        $fileProposal = $this->request->getFile("file_proposal");

        $fileProposalBaru = $proposal['file_proposal'];

        if ($fileProposal != null) {

            $fileProposalBaru = "Proposal_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileProposal->getClientExtension();

            $fileProposal->move("folderProposal", $fileProposalBaru);

        }



        $this->proposalModel->update($idProposal, [

            'judul' => $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS),

            'id_bidang' => $this->request->getPost("bidang", FILTER_SANITIZE_SPECIAL_CHARS),

            'sifat' => $this->request->getPost("sifat", FILTER_SANITIZE_SPECIAL_CHARS),

            'sumber' => $this->request->getPost("sumber", FILTER_SANITIZE_SPECIAL_CHARS),

            'tanggal_upload' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            'file_proposal' => $fileProposalBaru,

            'dosen_usulan1' => $this->request->getPost("dosen_usulan1", FILTER_SANITIZE_SPECIAL_CHARS),

            'dosen_usulan2' => $this->request->getPost("dosen_usulan2", FILTER_SANITIZE_SPECIAL_CHARS),

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Proposal Berhasil", "text" => "Proposal berhasil diubah"]);

        return redirect()->back();

    }













    public function seminarProposal()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $proposal = $this->proposalModel->getMahasiswaLastProposal($mahasiswa['npm']);

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $jadwalSempro = $this->semproModel->getJadwalByNPM($mahasiswa['npm']);

        $currentSemester = $this->semesterModel->getActiveSemester();

        $modeSempro = $this->modeSemproModel->getModeSempro($currentSemester['kode'], $prodi['id']);



        if ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') {

            for ($i = 0; $i < count($jadwalSempro); $i++) {

                $jadwalSempro[$i]['editable'] = $this->semproModel->isEditable($jadwalSempro[$i]['id']);

            }

            $data = [

                'title' => "Seminar Proposal",

                'mahasiswa' => $mahasiswa,

                'proposal' => $proposal,

                'modeSempro' => $modeSempro,

                'prodi' => $prodi,

                'jadwalSempro' => $jadwalSempro,

            ];



            return view("mahasiswa/seminar_proposal_asinkronus", $data);

        } elseif ($modeSempro != null && ($modeSempro['mode'] == 'Sinkronus Daring' || $modeSempro['mode'] == 'Sinkronus Luring')) {

            $dosen = $this->dosenModel->getDosenByProdi($prodi['id']);

            $data = [

                'title' => "Seminar Proposal",

                'mahasiswa' => $mahasiswa,

                'proposal' => $proposal,

                'prodi' => $prodi,

                'modeSempro' => $modeSempro,

                'jadwalSempro' => $jadwalSempro,

                'dosen' => $dosen,

            ];



            return view("mahasiswa/seminar_proposal_sinkronus", $data);

        } elseif ($modeSempro == null) {

            $data = [

                'title' => 'Seminar Proposal',

                'prodi' => $prodi,

            ];

            return view("mahasiswa/seminar_proposal_default", $data);

        }



    }



    public function insertVideoSempro()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $id_proposal = $this->request->getPost("id_proposal", FILTER_SANITIZE_SPECIAL_CHARS);

        $link_video = $this->request->getPost("link_video", FILTER_SANITIZE_URL);



        $this->semproModel->insert([

            'id_proposal' => $id_proposal,

            'link_video' => $link_video,

            'tanggal' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengumpulan Video Seminar Proposal Berhasil", "text" => "Link Video Seminar Proposal berhasil dikumpulkan"]);

        return redirect()->to(base_url("mahasiswa/seminarProposal"));

    }



    public function updateVideoSempro($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $id_proposal = $this->request->getPost("id_proposal", FILTER_SANITIZE_SPECIAL_CHARS);

        $link_video = $this->request->getPost("link_video", FILTER_SANITIZE_URL);



        $this->semproModel->update($id, [

            'id_proposal' => $id_proposal,

            'link_video' => $link_video,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengubahan Video Seminar Proposal Berhasil", "text" => "Link Video Seminar Proposal berhasil diubah"]);

        return redirect()->to(base_url("mahasiswa/seminarProposal"));

    }















    public function pembimbing()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);

        $dosenPembimbing = array();

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $skripsi = [];
        if ($currentSemester != null) {
            if ($lastSkripsi != null) {
                $dosenPembimbing = $this->pembimbingModel->getAllPembimbingByIdSkripsiAndKodeSemester($lastSkripsi['id'], $currentSemester['kode']);
            }

            $hasilBimbingan = $this->catatanBimbinganModel->getAllCatatanByNpmAndKodeSemester($mahasiswa['npm'], $currentSemester['kode']);
        }
        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => "Catatan Bimbingan Skripsi",

            'mahasiswa' => $mahasiswa,

            'dosenPembimbing' => $dosenPembimbing,

            'hasilBimbingan' => $hasilBimbingan,

            'semesterList' => $allSemester,

            'currentSemester' => $currentSemester,

        ];



        return view("mahasiswa/pembimbing", $data);

    }



    public function insertHasilBimbingan()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $idPembimbing = $this->request->getPost("pembimbing");

        $materi = $this->request->getPost("materi", FILTER_SANITIZE_SPECIAL_CHARS);

        $tanggal = date_create($this->request->getPost("tanggalBimbingan"));

        $status = 'TERTUNDA';



        $this->catatanBimbinganModel->insert([

            'id_pembimbing' => $idPembimbing,

            'hasil_bimbingan' => $materi,

            'tanggal_bimbingan' => date_format($tanggal, 'Y-m-d'),

            'status' => $status,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pembuatan Catatan Hasil Bimbingan Berhasil", "text" => "Catatan Hasil Bimbingan telah berhasil disimpan"]);

        return redirect()->to(base_url("mahasiswa/pembimbing"));

    }



    public function updateHasilBimbingan($idCatatan)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $idPembimbing = $this->request->getPost("pembimbing");

        $materi = $this->request->getPost("materi", FILTER_SANITIZE_SPECIAL_CHARS);

        $tanggal = date_create($this->request->getPost("tanggalBimbingan"));

        $status = 'TERTUNDA';



        $this->catatanBimbinganModel->update($idCatatan, [

            'id_pembimbing' => $idPembimbing,

            'hasil_bimbingan' => $materi,

            'tanggal_bimbingan' => date_format($tanggal, 'Y-m-d'),

            'status' => $status,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengubahan Catatan Hasil Bimbingan Berhasil", "text" => "Catatan Hasil Bimbingan telah berhasil diubah"]);

        return redirect()->to(base_url("mahasiswa/pembimbing"));

    }



    public function deleteHasilBimbingan($idCatatan)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $this->catatanBimbinganModel->delete($idCatatan);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Penghapusan Catatan Hasil Bimbingan Berhasil", "text" => "Catatan Hasil Bimbingan telah berhasil dihapus"]);

        return redirect()->to(base_url("mahasiswa/pembimbing"));

    }



    public function cetakFormBimbingan($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);

        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);

        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);

        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);

        $hasilBimbinganIlmu1 = $this->catatanBimbinganModel->getWhere(['id_pembimbing' => $pembimbingIlmu1[0]['id']])->getResultArray();

        $hasilBimbinganIlmu2 = $this->catatanBimbinganModel->getWhere(['id_pembimbing' => $pembimbingIlmu2[0]['id']])->getResultArray();

        $hasilBimbinganAgama = $this->catatanBimbinganModel->getWhere(['id_pembimbing' => $pembimbingAgama[0]['id']])->getResultArray();

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $data = [

            'mahasiswa' => $mahasiswa,

            'lastSkripsi' => $lastSkripsi,

            'pembimbingIlmu1' => $pembimbingIlmu1,

            'pembimbingIlmu2' => $pembimbingIlmu2,

            'pembimbingAgama' => $pembimbingAgama,

            'hasilBimbinganIlmu1' => $hasilBimbinganIlmu1,

            'hasilBimbinganIlmu2' => $hasilBimbinganIlmu2,

            'hasilBimbinganAgama' => $hasilBimbinganAgama,

            'prodi' => $prodi,

        ];



        return view("cetakBimbingan", $data);

    }















    public function skripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastProposal = $this->proposalModel->getMahasiswaLastProposal($mahasiswa['npm']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);

        $bidang = $this->bidangModel->getBidangByProdi($mahasiswa['id_prodi']);

        $skripsi = $this->skripsiModel->getSkripsiByNpm($mahasiswa['npm']);

        $currentSemester = $this->semesterModel->getActiveSemester();

        $data = [

            'title' => 'Kelola Skripsi',

            'lastProposal' => $lastProposal,

            'lastSkripsi' => $lastSkripsi,

            'mahasiswa' => $mahasiswa,

            'bidang' => $bidang,

            'skripsi' => $skripsi,

            'currentSemester' => $currentSemester,

        ];



        return view("mahasiswa/skripsi", $data);

    }



    public function detailSkripsi($idSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $detailSkripsi = $this->skripsiModel->getDetailSkripsiById($idSkripsi);



        if ($detailSkripsi == null || $dataAkun == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        if ($detailSkripsi['npm'] != $dataAkun['npm']) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $bidang = $this->bidangModel->getBidangByProdi($dataAkun['id_prodi']);



        $data = [

            'title' => 'Detail Skripsi',

            'detailSkripsi' => $detailSkripsi,

            'bidang' => $bidang,

        ];



        return view("mahasiswa/detail_skripsi", $data);

    }



    public function insertSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        $npm = $this->request->getPost("npm", FILTER_SANITIZE_SPECIAL_CHARS);

        $klien = $this->request->getPost("klien", FILTER_SANITIZE_SPECIAL_CHARS);

        $jenis = $this->request->getPost("jenis_skripsi");

        $this->skripsiModel->insert([

            'judul' => $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS),

            'sifat' => $this->request->getPost("sifat", FILTER_SANITIZE_SPECIAL_CHARS),

            'sumber' => $this->request->getPost("sumber", FILTER_SANITIZE_SPECIAL_CHARS),

            'npm' => $npm,

            'id_bidang' => $this->request->getPost("bidang"),

            'tanggal_skripsi' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            'kode_semester' => $this->semesterModel->getActiveSemester()['kode'],

            'jenis' => $jenis,

            'klien' => $klien,

            'status' => 'Dalam Pengerjaan',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Tambah Skripsi Berhasil", "text" => "Skripsi berhasil ditambahkan!"]);

        return redirect()->to(base_url("mahasiswa/skripsi"));

    }



    public function updateSkripsi($idSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $skripsi = $this->skripsiModel->find($idSkripsi);

        $npm = $this->request->getPost("npm");



        $fileSkripsi = $this->request->getFile("file_skripsi");

        $fileSkripsiBaru = $skripsi['file_skripsi'];

        if ($fileSkripsi != null) {

            $fileSkripsiBaru = "Skripsi_" . $npm . "_" . $idSkripsi . "_" . $this->generateTimeStamp() . "." . $fileSkripsi->getClientExtension();

            $fileSkripsi->move("folderSkripsi", $fileSkripsiBaru);

        }

        $jenis = $this->request->getPost("jenis");
        $klien = $this->request->getPost("klien", FILTER_SANITIZE_SPECIAL_CHARS);

        $this->skripsiModel->update($idSkripsi, [

            'file_skripsi' => $fileSkripsiBaru,

            'tanggal_selesai_skripsi' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            'judul' => $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS),

            'sifat' => $this->request->getPost("sifat", FILTER_SANITIZE_SPECIAL_CHARS),

            'sumber' => $this->request->getPost("sumber", FILTER_SANITIZE_SPECIAL_CHARS),

            'jenis' => $jenis,

            'klien' => $jenis == "SKRIPSI REGULER" ? NULL : $klien,

            'id_bidang' => $this->request->getPost("bidang"),

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Skripsi Berhasil", "text" => "Skripsi berhasil diubah!"]);

        return redirect()->back();

    }

















    public function pengajuanPraSidang()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        // [BARU] Cek Jadwal Prasidang
        $cek = $this->checkJadwal('prasidang');

        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);

        $pembimbing = [];
        $pengajuanPrasidang = [];
        $seminarPrasidang = [];

        if ($lastSkripsi != null) {

            $lastSkripsi['nama_bidang'] = $this->bidangModel->find($lastSkripsi['id_bidang'])['nama'];

            $pembimbing = $this->pembimbingModel->getAllPembimbingByIdSkripsi($lastSkripsi['id']);

            $pengajuanPrasidang = $this->pengajuanPrasidangModel->getPengajuanPrasidangByIdSkripsi($lastSkripsi['id']);

            $seminarPrasidang = $this->seminarPrasidangModel->getJadwalByIdSkripsi($lastSkripsi['id']);
        }



        $data = [

            'title' => 'Pengajuan Seminar Prasidang',

            'mahasiswa' => $mahasiswa,

            'lastSkripsi' => $lastSkripsi,

            'pengajuanPrasidang' => $pengajuanPrasidang,

            'seminarPrasidang' => $seminarPrasidang,

            'pembimbing' => $pembimbing,

            // [BARU] Kirim status ke View
            'is_open' => $cek['status'],
            'deadline' => ($cek['data']) ? $cek['data']['tanggal_selesai'] : null,
            'pesan_tutup' => $cek['msg']
        ];

        return view("mahasiswa/pengajuan_pra_sidang", $data);

    }



    public function detailPengajuanPraSidang($idPengajuanPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $detailPengajuan = $this->pengajuanPrasidangModel->getDetailPengajuanById($idPengajuanPrasidang);



        if ($detailPengajuan == null || $dataAkun == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $skripsi = $this->skripsiModel->find($detailPengajuan['id_skripsi']);

        if ($skripsi == null || $skripsi['npm'] != $dataAkun['npm']) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $data = [

            'title' => 'Detail Pengajuan Seminar Prasidang',

            'detailPengajuan' => $detailPengajuan,

        ];



        return view("mahasiswa/detail_pengajuan_prasidang", $data);

    }



    public function insertPengajuanPraSidang()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        // [BARU] Cegat jika jadwal tutup
        $cek = $this->checkJadwal('prasidang');
        if ($cek['status'] === false) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Jadwal Seminar Pra-Sidang ditutup."]);
            return redirect()->back();
        }

        $id_skripsi = $this->request->getPost("id_skripsi");

        $npm = $this->request->getPost("npm");



        $fileDraft = $this->request->getFile("file_draft");

        $fileDraftBaru = "Draft_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $fileDraft->getClientExtension();

        $fileDraft->move("folderDraft", $fileDraftBaru);



        $lembarPersetujuan = $this->request->getFile("lembar_persetujuan");

        $lembarPersetujuanBaru = "LembarPersetujuanPrasidang_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $lembarPersetujuan->getClientExtension();

        $lembarPersetujuan->move("folderLembarPersetujuanPrasidang", $lembarPersetujuanBaru);



        $this->pengajuanPrasidangModel->insert([

            "id_skripsi" => $id_skripsi,

            "file_draft" => $fileDraftBaru,

            "lembar_persetujuan" => $lembarPersetujuanBaru,

            "tanggal_pengajuan" => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            "status" => "TERTUNDA",

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Upload Pengajuan Prasidang Berhasil", "text" => "Pengajuan Seminar Prasidang Berhasil diunggah"]);

        return redirect()->to(base_url("mahasiswa/pengajuanPraSidang"));

    }



    public function updatePengajuanPrasidang($idPengajuanPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $pengajuanPrasidang = $this->pengajuanPrasidangModel->find($idPengajuanPrasidang);

        $npm = $this->request->getPost("npm");

        $id_skripsi = $this->request->getPost("id_skripsi");



        $fileDraft = $this->request->getFile("file_draft");

        $fileDraftBaru = $pengajuanPrasidang['file_draft'];

        if ($fileDraft != null) {

            $fileDraftBaru = "Draft_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $fileDraft->getClientExtension();

            $fileDraft->move("folderDraft", $fileDraftBaru);

        }



        $lembarPersetujuan = $this->request->getFile("lembar_persetujuan");

        $lembarPersetujuanBaru = $pengajuanPrasidang['lembar_persetujuan'];

        if ($lembarPersetujuan != null) {

            $lembarPersetujuanBaru = "LembarPersetujuanPrasidang_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $lembarPersetujuan->getClientExtension();

            $lembarPersetujuan->move("folderLembarPersetujuanPrasidang", $lembarPersetujuanBaru);

        }



        $this->pengajuanPrasidangModel->update($idPengajuanPrasidang, [

            "file_draft" => $fileDraftBaru,

            "lembar_persetujuan" => $lembarPersetujuanBaru,

            "status" => 'TERTUNDA'

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Perbaruan Pengajuan Seminar Prasidang Berhasil", "text" => "Data Pengajuan Seminar Prasidang Berhasil diperbarui"]);

        return redirect()->back();

    }

















    public function seminarPrasidang()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($dataAkun['npm']);

        $pengajuan = null;

        if ($lastSkripsi != null) {

            $pengajuan = $this->pengajuanPrasidangModel->where('id_skripsi', $lastSkripsi['id'])->orderBy('tanggal_pengajuan', 'DESC')->limit(1)->findAll();

        }



        if ($pengajuan != null) {

            $pengajuan = $pengajuan[0];

        }



        $jadwalSeminarPrasidang = $this->seminarPrasidangModel->getJadwalByNPM($dataAkun['npm']);



        $data = [

            'title' => "Jadwal Seminar Prasidang",

            'jadwalSeminarPrasidang' => $jadwalSeminarPrasidang,

            'lastSkripsi' => $lastSkripsi,

            'pengajuan' => $pengajuan,

        ];



        return view("mahasiswa/seminar_prasidang", $data);

    }



    public function pengajuanSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        // [BARU] Cek Jadwal Sidang
        $cek = $this->checkJadwal('sidang');

        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($dataAkun['npm']);

        if ($lastSkripsi != null) {

            $lastSkripsi['nama_bidang'] = $this->bidangModel->find($lastSkripsi['id_bidang'])['nama'];

            $lastSkripsi['jumlah_bimbingan_ilmu'] = 0;

            $lastSkripsi['jumlah_bimbingan_agama'] = 0;

            foreach ($this->catatanBimbinganModel->getAllCatatanByLastSkripsi($lastSkripsi['id']) as $catatan) {

                if (($catatan['role'] == 'Pembimbing Ilmu 1' || $catatan['role'] == 'Pembimbing Ilmu 2') && $catatan['status'] == 'DISETUJUI') {

                    $lastSkripsi['jumlah_bimbingan_ilmu']++;

                }



                if ($catatan['role'] == 'Pembimbing Agama' && $catatan['status'] == 'DISETUJUI') {

                    $lastSkripsi['jumlah_bimbingan_agama']++;

                }

            }

        }



        $seminarPrasidang = null;

        if ($lastSkripsi != null) {

            $seminarPrasidang = $this->seminarPrasidangModel->where('id_skripsi', $lastSkripsi['id'])->orderBy('tanggal', 'DESC')->limit(1)->find();



            if (count($seminarPrasidang) == 0) {

                $seminarPrasidang = null;

            } else {

                $seminarPrasidang = $seminarPrasidang[0];

            }

        }



        $pengajuanSidangSkripsi = $this->pengajuanSidangModel->getPengajuanSidangByNpm($dataAkun['npm']);



        $data = [

            'title' => 'Pengajuan Sidang Skripsi',

            'dataAkun' => $dataAkun,

            'lastSkripsi' => $lastSkripsi,

            'seminarPrasidang' => $seminarPrasidang,

            'pengajuanSidangSkripsi' => $pengajuanSidangSkripsi,

            // [BARU] Kirim status ke View
            'is_open' => $cek['status'],
            'deadline' => ($cek['data']) ? $cek['data']['tanggal_selesai'] : null,
            'pesan_tutup' => $cek['msg']
        ];



        return view("mahasiswa/pengajuan_sidang_skripsi", $data);

    }



    public function detailPengajuanSidangSkripsi($idpengajuanSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $detailPengajuan = $this->pengajuanSidangModel->getDetailPengajuanById($idpengajuanSidangSkripsi);

        if ($detailPengajuan == null || $dataAkun == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $skripsi = $this->skripsiModel->find($detailPengajuan['id_skripsi']);

        if ($skripsi == null || $skripsi['npm'] != $dataAkun['npm']) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }





        $data = [

            'title' => 'Detail Pengajuan Sidang Skripsi',

            'detailPengajuan' => $detailPengajuan,

        ];



        return view("mahasiswa/detail_pengajuan_sidang_skripsi", $data);

    }



    public function insertPengajuanSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        // [BARU] Cegat jika jadwal tutup
        $cek = $this->checkJadwal('sidang');
        if ($cek['status'] === false) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Jadwal Sidang Skripsi ditutup."]);
            return redirect()->back();
        }

        $id_skripsi = $this->request->getPost("id_skripsi");

        $npm = $this->request->getPost("npm");



        $fileDraftFinal = $this->request->getFile("file_draft_final");

        $fileDraftFinalBaru = "Draft_Final_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $fileDraftFinal->getClientExtension();

        $fileDraftFinal->move("folderDraftFinal", $fileDraftFinalBaru);



        $formBimbingan = $this->request->getFile("file_form_bimbingan");

        $formBimbinganBaru = "Form_Bimbingan_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $formBimbingan->getClientExtension();

        $formBimbingan->move("folderFormBimbingan", $formBimbinganBaru);



        $persyaratanSidang = $this->request->getFile("file_persyaratan_sidang");

        $persyaratanSidangBaru = "Persyaratan_Sidang_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $persyaratanSidang->getClientExtension();

        $persyaratanSidang->move("folderPersyaratanSidang", $persyaratanSidangBaru);



        $this->pengajuanSidangModel->insert([

            "id_skripsi" => $id_skripsi,

            "file_draft_final" => $fileDraftFinalBaru,

            "file_form_bimbingan" => $formBimbinganBaru,

            "file_persyaratan_sidang" => $persyaratanSidangBaru,

            "tanggal_pengajuan" => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            "status" => "TERTUNDA",

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Upload Pengajuan Sidang Berhasil", "text" => "Pengajuan Sidang Skripsi Berhasil diunggah"]);

        return redirect()->to(base_url("mahasiswa/pengajuanSidangSkripsi"));

    }



    public function updatePengajuanSidangSkripsi($idpengajuanSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $pengajuanSidang = $this->pengajuanSidangModel->find($idpengajuanSidangSkripsi);

        $npm = $this->request->getPost("npm");

        $id_skripsi = $this->request->getPost("id_skripsi");



        $fileDraftFinal = $this->request->getFile("file_draft_final");

        $fileDraftFinalBaru = $pengajuanSidang['file_draft_final'];

        if ($fileDraftFinal != null) {

            // if ($fileDraftFinalBaru != null) {

            //     unlink("folderDraftFinal/".$fileDraftFinalBaru);

            // }

            $fileDraftFinalBaru = "Draft_Final_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $fileDraftFinal->getClientExtension();

            $fileDraftFinal->move("folderDraftFinal", $fileDraftFinalBaru);

        }



        $formBimbingan = $this->request->getFile("file_form_bimbingan");

        $formBimbinganBaru = $pengajuanSidang['file_form_bimbingan'];

        if ($formBimbingan != null) {

            // if ($formBimbinganBaru != null) {

            //     unlink("folderFormBimbingan/".$formBimbinganBaru);

            // }

            $formBimbinganBaru = "Form_Bimbingan_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $formBimbingan->getClientExtension();

            $formBimbingan->move("folderFormBimbingan", $formBimbinganBaru);

        }



        $persyaratanSidang = $this->request->getFile("file_persyaratan_sidang");

        $persyaratanSidangBaru = $pengajuanSidang['file_persyaratan_sidang'];

        if ($persyaratanSidang != null) {

            // if ($persyaratanSidangBaru != null) {

            //     unlink("folderFormBimbingan/".$persyaratanSidangBaru);

            // }

            $persyaratanSidangBaru = "Persyaratan_Sidang_" . $npm . "_" . $id_skripsi . "_" . $this->generateTimeStamp() . "." . $persyaratanSidang->getClientExtension();

            $persyaratanSidang->move("folderPersyaratanSidang", $persyaratanSidangBaru);

        }



        $this->pengajuanSidangModel->update($idpengajuanSidangSkripsi, [

            "file_draft_final" => $fileDraftFinalBaru,

            "file_form_bimbingan" => $formBimbinganBaru,

            "file_persyaratan_sidang" => $persyaratanSidangBaru,

            "status" => 'TERTUNDA'

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Update Pengajuan Sidang Skripsi Berhasil", "text" => "Pengajuan Sidang Skripsi Berhasil diubah"]);

        return redirect()->back();

    }













    public function sidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($dataAkun['npm']);

        $pengajuan = null;

        if ($lastSkripsi != null) {

            $pengajuan = $this->pengajuanSidangModel->getWhere(['id_skripsi' => $lastSkripsi['id']])->getResultArray();

        }



        if ($pengajuan != null && count($pengajuan) == 1) {

            $pengajuan = $pengajuan[0];

        }



        $jadwalSidangSkripsi = $this->sidangSkripsiModel->getJadwalByNPM($dataAkun['npm']);



        $data = [

            'title' => "Jadwal Sidang Skripsi",

            'jadwalSidangSkripsi' => $jadwalSidangSkripsi,

            'lastSkripsi' => $lastSkripsi,

            'pengajuan' => $pengajuan,

        ];



        return view("mahasiswa/sidang_skripsi", $data);

    }


    public function hasilSidangSkripsi($idSidangSkripsi)
    {
        //autentikasi
        if (!$this->authenticate(["mahasiswa"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $sidangSkripsi = $this->sidangSkripsiModel->find($idSidangSkripsi);
        if ($sidangSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lastSkripsi = $this->skripsiModel->find($sidangSkripsi['id_skripsi']);

        if ($lastSkripsi == null || $lastSkripsi['npm'] != $dataAkun['npm']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $tahunAkademik = explode("-", $lastSkripsi['kode_semester'])[0];
        $semester = explode("-", $lastSkripsi['kode_semester'])[1];

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->hasilSidangSkripsiOld($lastSkripsi, $sidangSkripsi);
        } else {
            return $this->hasilSidangSkripsiNew($lastSkripsi, $sidangSkripsi);
        }
    }
    private function hasilSidangSkripsiOld($lastSkripsi, $sidangSkripsi)
    {
        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);

        $idSidangSkripsi = $sidangSkripsi['id'];
        $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();
        $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();
        $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();
        $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi['dosen_penguji']])->getResultArray();

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'lastSkripsi' => $lastSkripsi,
            'sidangSkripsi' => $sidangSkripsi,
            'pembimbingIlmu1' => $pembimbingIlmu1,
            'pembimbingIlmu2' => $pembimbingIlmu2,
            'pembimbingAgama' => $pembimbingAgama,
            'penguji' => $penguji,
            'nilaiPembimbing1' => $nilaiPembimbing1,
            'nilaiPembimbing2' => $nilaiPembimbing2,
            'nilaiPembimbingAgama' => $nilaiPembimbingAgama,
            'nilaiPenguji' => $nilaiPenguji,
        ];

        return view("mahasiswa/hasil_sidang_skripsi", $data);
    }

    private function hasilSidangSkripsiNew($lastSkripsi, $sidangSkripsi)
    {
        $idSidangSkripsi = $sidangSkripsi['id'];
        $detailSidangSkripsi = $this->sidangSkripsiModel->getDetailSidangSkripsiById($idSidangSkripsi);

        if (count($detailSidangSkripsi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        if ($lastSkripsi['jenis'] == JENIS_SKRIPSI_REGULER) {
            return $this->hasilSidangSkripsiNonProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi);
        } else {
            return $this->hasilSidangSkripsiProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi);
        }

    }


    private function hasilSidangSkripsiNonProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi)
    {
        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['dosen_penguji'], 'Dosen Penguji', $sidangSkripsi["id"]);
        $nilaiPembimbing1 = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing1'], 'Pembimbing Ilmu 1', $sidangSkripsi["id"]);
        $nilaiPembimbing2 = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing2'], 'Pembimbing Ilmu 2', $sidangSkripsi["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing_agama'], 'Pembimbing Agama', $sidangSkripsi["id"]);

        $rubrikNilai = $this->penilaianSidangParamModel->getRubrikPdsi2023();

        $rataRataNilai = $this->penilaianSidangNewModel->getAverageByIdSidang($lastSkripsi['id'], $idSidangSkripsi,
            $detailSidangSkripsi[0]['dosen_penguji'], $detailSidangSkripsi[0]['id_pembimbing1'],
            $detailSidangSkripsi[0]['id_pembimbing2'], $detailSidangSkripsi[0]['id_pembimbing_agama']);

        $data = [

            'title' => 'Hasil Sidang Skripsi',

            'lastSkripsi' => $lastSkripsi,

            'sidangSkripsi' => $sidangSkripsi,

            'detailSidangSkripsi' => $detailSidangSkripsi[0],

            'nilaiPembimbing1' => $nilaiPembimbing1,

            'nilaiPembimbing2' => $nilaiPembimbing2,

            'nilaiPembimbingAgama' => $nilaiPembimbingAgama,

            'nilaiPenguji' => $nilaiPenguji,

            'rubrikNilai' => $rubrikNilai,

            'avgNilai' => $rataRataNilai,

        ];



        return view("mahasiswa/hasil_sidang_skripsi_non_proyek", $data);
    }

    private function hasilSidangSkripsiProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi)
    {
        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['dosen_penguji'], 'Penguji', $sidangSkripsi["id"]);
        $nilaiPembimbing = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing1'], 'Pembimbing Ilmu', $sidangSkripsi["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing_agama'], 'Pembimbing Agama', $sidangSkripsi["id"]);
        $nilaiKlien = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_klien'], 'Klien', $sidangSkripsi["id"]);

        $rubrikPenguji = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Penguji');
        $rubrikPembimbing = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Pembimbing Ilmu');
        $rubrikPembimbingAgama = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Pembimbing Agama');
        $rubrikKlien = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Klien');

        $totalBobot = $this->penilaianSidangParamModel->getAllSumBobot();
        $totalNilai = $this->penilaianSidangNewModel->getTotalProyekByIdSidang($lastSkripsi['id'], $idSidangSkripsi, $lastSkripsi['jenis']);

        $data = [

            'title' => 'Hasil Sidang Skripsi',

            'lastSkripsi' => $lastSkripsi,

            'sidangSkripsi' => $sidangSkripsi,

            'detailSidangSkripsi' => $detailSidangSkripsi[0],

            'nilaiPembimbing' => $nilaiPembimbing,

            'nilaiPembimbingAgama' => $nilaiPembimbingAgama,

            'nilaiPenguji' => $nilaiPenguji,

            'nilaiKlien' => $nilaiKlien,

            'rubrikPenguji' => $rubrikPenguji,

            'rubrikPembimbing' => $rubrikPembimbing,

            'rubrikPembimbingAgama' => $rubrikPembimbingAgama,

            'rubrikKlien' => $rubrikKlien,

            'totalBobot' => $totalBobot,

            'totalNilai' => $totalNilai,
        ];



        return view("mahasiswa/hasil_sidang_skripsi_proyek", $data);
    }

















    public function makalah()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->mahasiswaModel->find(session()->get("user_session")['id']);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($dataAkun['npm']);

        $bidang = $this->bidangModel->getBidangByProdi($dataAkun['id_prodi']);

        $makalah = $this->makalahModel->getWhere(['npm' => $dataAkun['npm']])->getResultArray();





        $data = [

            'title' => 'Kelola Makalah',

            'dataAkun' => $dataAkun,

            'lastSkripsi' => $lastSkripsi,

            'bidang' => $bidang,

            'makalah' => $makalah,

            'activeSemester' => $this->semesterModel->getActiveSemester(),

        ];



        return view("mahasiswa/makalah", $data);

    }



    public function insertMakalah()
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $validationRules = [

            'file_makalah' => [

                'rules' => 'uploaded[file_makalah]|mime_in[file_makalah,application/pdf]|ext_in[file_makalah,pdf]|max_size[file_makalah,10000]',

                'errors' => [

                    'uploaded' => 'Pilih File Makalah terlebih dahulu',

                    'mime_in' => 'File Makalah harus berupa PDF',

                    'ext_in' => 'File Makalah harus berekstensi .pdf',

                    'max_size' => 'Ukuran File Makalah tidak boleh lebih dari 10MB'

                ]

            ]

        ];



        if (!$this->validate($validationRules)) {

            $validation = \Config\Services::validation();

            session()->setFlashdata("message", ["icon" => "error", "title" => "Unggah File Makalah Gagal", "text" => $validation->getError("file_makalah")]);

            return redirect()->to(base_url("mahasiswa/makalah"))->withInput();

        }



        $npm = $this->request->getPost("npm");

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);
        if ($lastSkripsi['status'] != STATUS_SKRIPSI_LULUS) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Unggah Makalah Gagal", "text" => "Anda belum bisa mengumpulkan makalah karena belum dinyatakan lulus"]);
            return redirect()->to(base_url("mahasiswa/makalah"));
        }

        $judul = $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS);

        $deskripsi = $this->request->getPost("deskripsi", FILTER_SANITIZE_SPECIAL_CHARS);

        $kata_kunci = $this->request->getPost("kata_kunci", FILTER_SANITIZE_SPECIAL_CHARS);

        $id_bidang = $this->request->getPost("bidang");



        $fileMakalah = $this->request->getFile("file_makalah");

        $fileMakalahBaru = "Makalah_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileMakalah->getClientExtension();

        $fileMakalah->move("folderMakalah", $fileMakalahBaru);



        $this->makalahModel->insert([

            "judul" => $judul,

            "deskripsi" => $deskripsi,

            "kata_kunci" => $kata_kunci,

            "file_makalah" => $fileMakalahBaru,

            "npm" => $npm,

            "tanggal_upload" => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),

            "id_bidang" => $id_bidang,

            "kode_semester" => $lastSkripsi['kode_semester'],

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Unggah Makalah Berhasil", "text" => "Makalah Anda telah berhasil diunggah"]);

        return redirect()->to(base_url("mahasiswa/makalah"));

    }



    public function updateMakalah($idMakalah)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $makalah = $this->makalahModel->find($idMakalah);

        $npm = $this->request->getPost("npm");



        $fileMakalah = $this->request->getFile("file_makalah");

        $fileMakalahBaru = $makalah['file_makalah'];

        if ($fileMakalah != null) {

            $fileMakalahBaru = "Makalah_" . $npm . "_" . $this->generateTimeStamp() . "." . $fileMakalah->getClientExtension();

            $fileMakalah->move("folderMakalah", $fileMakalahBaru);

        }



        $judul = $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS);

        $deskripsi = $this->request->getPost("deskripsi", FILTER_SANITIZE_SPECIAL_CHARS);

        $kata_kunci = $this->request->getPost("kata_kunci", FILTER_SANITIZE_SPECIAL_CHARS);

        $id_bidang = $this->request->getPost("bidang");



        $this->makalahModel->update($idMakalah, [

            "file_makalah" => $fileMakalahBaru,

            "judul" => $judul,

            "deskripsi" => $deskripsi,

            "kata_kunci" => $kata_kunci,

            "id_bidang" => $id_bidang,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Perbarui Makalah Berhasil", "text" => "Makalah Anda telah berhasil diperbarui"]);

        return redirect()->to(base_url("mahasiswa/makalah"));

    }













    public function downloadKhs($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileKhs = $this->mahasiswaModel->find($npm);

        if ($namaFileKhs == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File KHS Gagal", "text" => "File KHS tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }



        redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        return $this->response->download("folderKHS/" . $namaFileKhs['file_khs'], null);

    }



    public function downloadKrs($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileKrs = $this->mahasiswaModel->find($npm);

        if ($namaFileKrs == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File KRS Gagal", "text" => "File KRS tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }



        redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        return $this->response->download("folderKRS/" . $namaFileKrs['file_krs'], null);

    }



    public function downloadPersetujuanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePengajuan = $this->mahasiswaModel->find($npm);

        if ($namaFilePengajuan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Persetujuan Skripsi Gagal", "text" => "File Persetujuan Skripsi tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }



        redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        return $this->response->download("folderPersetujuanSkripsi/" . $namaFilePengajuan['file_persetujuan_skripsi'], null);

    }



    public function downloadProposal($idProposal)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileProposal = $this->proposalModel->find($idProposal)['file_proposal'];

        if ($namaFileProposal == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Proposal Gagal", "text" => "File Proposal tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/proposal"));

        }



        redirect()->to(base_url("mahasiswa/proposal"));

        try {
            return $this->response->download("folderProposal/" . $namaFileProposal, null);
        } catch (Exception $e) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Proposal Gagal", "text" => "Terjadi kesalahan pada saat mengunduh"]);

            return redirect()->to(base_url("mahasiswa/proposal"));
        }


    }



    public function downloadDraft($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePengajuan = $this->pengajuanPrasidangModel->find($id);

        if ($namaFilePengajuan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Unduh File Draft Skripsi Gagal", "text" => "File Draft Skripsi tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderDraft/" . $namaFilePengajuan['file_draft'], null);

    }



    public function downloadLembarPersetujuan($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePengajuan = $this->pengajuanPrasidangModel->find($id);

        if ($namaFilePengajuan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Unduh Lembar Persetujuan Seminar Prasidang Gagal", "text" => "Lembar Persetujuan Seminar Prasidang tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderLembarPersetujuanPrasidang/" . $namaFilePengajuan['lembar_persetujuan'], null);

    }



    public function downloadDraftFinal($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileDraftFinal = $this->pengajuanSidangModel->find($id);

        if ($namaFileDraftFinal == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Draft Final Skripsi Gagal", "text" => "File Draft Final Skripsi tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderDraftFinal/" . $namaFileDraftFinal['file_draft_final'], null);

    }



    public function downloadFormBimbingan($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFormBimbingan = $this->pengajuanSidangModel->find($id);

        if ($namaFormBimbingan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Form Bimbingan Skripsi Gagal", "text" => "File Form Bimbingan Skripsi tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderFormBimbingan/" . $namaFormBimbingan['file_form_bimbingan'], null);

    }



    public function downloadPersyaratanSidang($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePersyaratanSidang = $this->pengajuanSidangModel->find($id);

        if ($namaFilePersyaratanSidang == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Download File Persyaratan Sidang Skripsi Gagal", "text" => "File Persyaratan Sidang Skripsi tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderPersyaratanSidang/" . $namaFilePersyaratanSidang['file_persyaratan_sidang'], null);

    }



    public function downloadSkripsi($idSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa", "tendik", "kaprodi", "fakultas", "dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $skripsi = $this->skripsiModel->find($idSkripsi);

        if ($skripsi == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Unduh File Skripsi Gagal", "text" => "File Skripsi tidak ditemukan"]);

            return redirect()->back();

        }



        redirect()->back();

        return $this->response->download("folderSkripsi/" . $skripsi['file_skripsi'], null);

    }

















    public function deleteKhs($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileKhs = $this->mahasiswaModel->find($npm);

        if ($namaFileKhs == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File KHS Gagal", "text" => "File KHS tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }

        unlink("folderKHS/" . $namaFileKhs['file_khs']);

        $this->mahasiswaModel->update($npm, [

            "file_khs" => null,

            "status_persetujuan_skripsi" => null,

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File KHS Berhasil", "text" => "File KHS berhasil dihapus"]);

        return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

    }



    public function deleteKrs($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileKrs = $this->mahasiswaModel->find($npm);

        if ($namaFileKrs == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File KRS Gagal", "text" => "File KRS tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }

        unlink("folderKRS/" . $namaFileKrs['file_krs']);

        $this->mahasiswaModel->update($npm, [

            "file_krs" => null,

            "status_persetujuan_skripsi" => null,

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File KRS Berhasil", "text" => "File KRS berhasil dihapus"]);

        return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

    }



    public function deletePersetujuanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePengajuan = $this->mahasiswaModel->find($npm);

        if ($namaFilePengajuan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Persetujuan Skripsi Gagal", "text" => "File Persetujuan Skripsi tidak ditemukan"]);

            return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

        }

        unlink("folderPersetujuanSkripsi/" . $namaFilePengajuan['file_persetujuan_skripsi']);

        $this->mahasiswaModel->update($npm, [

            "file_persetujuan_skripsi" => null,

            "status_persetujuan_skripsi" => null,

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Persetujuan Skripsi Berhasil", "text" => "File Persetujuan Skripsi berhasil dihapus"]);

        return redirect()->to(base_url("mahasiswa/pengajuanPenulisanSkripsi"));

    }



    public function deleteProposal($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileProposal = $this->proposalModel->find($id);

        if ($namaFileProposal == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Proposal Gagal", "text" => "File Proposal tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderProposal/" . $namaFileProposal['file_proposal']);

        $this->proposalModel->update($id, [

            "file_proposal" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Proposal Berhasil", "text" => "File Proposal berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteDraft($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileDraft = $this->pengajuanPrasidangModel->find($id);

        if ($namaFileDraft == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Draft Skripsi Gagal", "text" => "File Draft Skripsi tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderDraft/" . $namaFileDraft['file_draft']);

        $this->pengajuanPrasidangModel->update($id, [

            "file_draft" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Draft Skripsi Berhasil", "text" => "File Draft Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteLembarPersetujuan($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileDraft = $this->pengajuanPrasidangModel->find($id);

        if ($namaFileDraft == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Lembar Persetujuan Seminar Prasidang Gagal", "text" => "File Lembar Persetujuan Seminar Prasidang tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderLembarPersetujuanPrasidang/" . $namaFileDraft['lembar_persetujuan']);

        $this->pengajuanPrasidangModel->update($id, [

            "lembar_persetujuan" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Lembar Persetujuan Seminar Prasidang Berhasil", "text" => "File Lembar Persetujuan Seminar Prasidang berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteDraftFinal($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileDraftFinal = $this->pengajuanSidangModel->find($id);

        if ($namaFileDraftFinal == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Draft Final Skripsi Gagal", "text" => "File Draft Final Skripsi tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderDraftFinal/" . $namaFileDraftFinal['file_draft_final']);

        $this->pengajuanSidangModel->update($id, [

            "file_draft_final" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Draft Final Skripsi Berhasil", "text" => "File Draft Final Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteFormBimbingan($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileFormBimbingan = $this->pengajuanSidangModel->find($id);

        if ($namaFileFormBimbingan == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Form Bimbingan Skripsi Gagal", "text" => "File Form Bimbingan Skripsi tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderFormBimbingan/" . $namaFileFormBimbingan['file_form_bimbingan']);

        $this->pengajuanSidangModel->update($id, [

            "file_form_bimbingan" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Form Bimbingan Skripsi Berhasil", "text" => "File Form Bimbingan Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function deletePersyaratanSidang($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFilePersyaratanSidang = $this->pengajuanSidangModel->find($id);

        if ($namaFilePersyaratanSidang == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Persyaratan Sidang Skripsi Gagal", "text" => "File Persyaratan Sidang Skripsi tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderPersyaratanSidang/" . $namaFilePersyaratanSidang['file_persyaratan_sidang']);

        $this->pengajuanSidangModel->update($id, [

            "file_persyaratan_sidang" => null,

            "status" => 'TERTUNDA',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Persyaratan Sidang Skripsi Berhasil", "text" => "File Persyaratan Sidang Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteSkripsi($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileSkripsi = $this->skripsiModel->find($id);

        if ($namaFileSkripsi == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Skripsi Gagal", "text" => "File Skripsi tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderSkripsi/" . $namaFileSkripsi['file_skripsi']);

        $this->skripsiModel->update($id, [

            "file_skripsi" => null,

            "tanggal_selesai_skripsi" => null,

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Skripsi Berhasil", "text" => "File Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function deleteMakalah($id)
    {

        //autentikasi

        if (!$this->authenticate(["mahasiswa"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $namaFileMakalah = $this->makalahModel->find($id);

        if ($namaFileMakalah == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Hapus File Makalah Gagal", "text" => "File Makalah tidak ditemukan"]);

            return redirect()->back();

        }

        unlink("folderMakalah/" . $namaFileMakalah['file_makalah']);

        $this->makalahModel->update($id, [

            "file_makalah" => null,

            "tanggal_upload" => null,

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus File Skripsi Berhasil", "text" => "File Skripsi berhasil dihapus"]);

        return redirect()->back();

    }



    public function profile()
    {
        //autentikasi
        if (!$this->authenticate(["mahasiswa"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->mahasiswaModel->getMahasiswaInfo(session()->get("user_session")['id']);
        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Ubah Data Diri',
            'dataDiri' => $dataAkun,
        ];

        return view("mahasiswa/profile", $data);
    }


    public function saveProfile()
    {
        //autentikasi
        if (!$this->authenticate(["mahasiswa"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $npm = $this->request->getPost("npm");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $dataMahasiswa = $this->mahasiswaModel->getMahasiswaInfo($npm);
        if ($dataMahasiswa == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->akunModel->isEmailAlreadyUsed($dataMahasiswa['id_akun'], $email)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Email telah digunakan!"]);
            return redirect()->to(base_url("Mahasiswa/profile"));
        }

        $this->akunModel->update($dataMahasiswa['id_akun'], [
            "email" => $email,
        ]);

        $this->mahasiswaModel->update($npm, [
            "nama" => $nama,
            "email" => $email,
        ]);


        session()->set("user_session", [
            'id' => $dataMahasiswa['npm'],
            'nama' => $nama,
            'email' => $email,
            'prodi' => $dataMahasiswa['inisial_prodi'],
            'roles' => ['mahasiswa'],
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Diri Berhasil", "text" => "Data diri berhasil diubah!"]);
        return redirect()->to(base_url("Mahasiswa/profile"));
    }


    private function generateTimeStamp()
    {
        return date_format(Time::now('Asia/Jakarta', 'en_us'), 'YmdHis');
    }


    private function authenticate($roles)
    {

        $userSession = session("user_session");

        if ($userSession == null) {

            return false;

        }



        $userRoles = $userSession['roles'];

        for ($i = 0; $i < count($roles); $i++) {

            if (in_array($roles[$i], $userRoles)) {

                return true;

            }

        }



        return false;

    }
public function statusProgres()
    {
        $npm = session()->get('npm'); 
        $progresData = $this->skripsiModel->getThesisProgress($npm);

        $data = [
            'title'        => 'Status Progres Skripsi',
            'skripsi'      => $progresData ? $progresData['detail'] : null,
            'current_step' => $progresData ? $progresData['current_step'] : 0,
            'tahapan'      => [
                "Penyusunan", "Draft Proposal", "Jadwal Sempro", 
                "Judul Final", "Draft Prasidang", "Pra Sidang", 
                "Draft Sidang", "Jadwal Sidang", "Sidang Akhir"
            ]
        ];

        return view('mahasiswa/status_progres', $data);
    }

    // --- [BARU] Helper Function untuk Cek Jadwal ---
    private function checkJadwal($jenisKegiatan)
    {
        $session = session('user_session');
        if (!isset($session['id'])) return ['status' => false, 'data' => null, 'msg' => 'Sesi login tidak valid.'];

        // Ambil Data Mahasiswa & Semester Aktif
        $mhs = $this->mahasiswaModel->find($session['id']);
        $semesterAktif = $this->semesterModel->where('status', 'AKTIF')->first();

        if (!$semesterAktif) {
            return ['status' => false, 'data' => null, 'msg' => 'Tidak ada Semester Aktif saat ini.'];
        }

        // Cek Kalender
        $kalenderModel = new \App\Models\KalenderSkripsiModel();
        $jadwal = $kalenderModel->getJadwalAktif($jenisKegiatan, $mhs['id_prodi'], $semesterAktif['kode']);

        if ($jadwal) {
            return ['status' => true, 'data' => $jadwal, 'msg' => null]; // BUKA (Pesan kosong)
        } else {
            return ['status' => false, 'data' => null, 'msg' => 'Periode pendaftaran belum dibuka atau sudah berakhir.']; // TUTUP (Ada Pesan)
        }
    }


    // --- FITUR KELOLA HARD COVER (MAHASISWA) ---

    public function pengajuanHardcover()
    {
        if (!$this->authenticate(["mahasiswa"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $mahasiswa = $this->mahasiswaModel->find(session()->get("user_session")['id']);
        
        // 1. Ambil Data Skripsi Terakhir (Harus LULUS)
        $skripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);
        
        // 2. Ambil List Hard Cover yang sudah diupload (Jika ada)
        // Kita pakai findAll() supaya strukturnya sama kayak tabel proposal (bisa list history kalau perlu)
        // Tapi logic-nya biasanya cuma 1 file final.
        $hardcoverList = [];
        if ($skripsi) {
            $hardcoverList = $this->pengajuanHardcoverModel
                            ->where('id_skripsi', $skripsi['id'])
                            ->orderBy('tanggal_pengajuan', 'DESC')
                            ->findAll();
        }

        $data = [
            'title' => "Kelola Hard Cover",
            'mahasiswa' => $mahasiswa,
            'skripsi' => $skripsi,
            'hardcoverList' => $hardcoverList
        ];

        return view('mahasiswa/pengajuan_hardcover', $data);
    }

    public function insertHardcover()
    {
        if (!$this->authenticate(["mahasiswa"])) return redirect()->to(base_url("unauthorized.php"));
        if ($this->request->getMethod() != 'post') return redirect()->back();

        // 1. Validasi
        $validationRules = [
            'file_hardcover' => [
                'rules' => 'uploaded[file_hardcover]|mime_in[file_hardcover,application/pdf]|ext_in[file_hardcover,pdf]|max_size[file_hardcover,10000]',
                'errors' => [
                    'uploaded' => 'Pilih File Hard Cover terlebih dahulu',
                    'mime_in' => 'File harus berupa PDF',
                    'ext_in' => 'File harus berekstensi .pdf',
                    'max_size' => 'Ukuran File tidak boleh lebih dari 10MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            $validation = \Config\Services::validation();
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => $validation->getError("file_hardcover")]);
            return redirect()->back()->withInput();
        }

        // 2. Proses Upload
        $idSkripsi = $this->request->getPost('id_skripsi');
        $npm = $this->request->getPost('npm');
        
        $file = $this->request->getFile('file_hardcover');
        $namaFile = "Hardcover_" . $npm . "_" . $this->generateTimeStamp() . "." . $file->getClientExtension();
        $file->move("folderHardcover", $namaFile);

        // 3. Simpan Database (Logika: Jika ditolak/baru -> Insert baru sebagai history atau Update status? 
        // Agar sesuai style 'Kelola Proposal' yang bentuknya list, kita Insert data baru saja biar ada history revisi)
        
        // Cek apakah ada data sebelumnya untuk di-update (Opsional: overwrite atau insert baru)
        // Disini saya buat UPDATE jika ada, agar tidak menumpuk sampah file, tapi status direset jadi TERTUNDA.
        $cekAda = $this->pengajuanHardcoverModel->where('id_skripsi', $idSkripsi)->first();

        if($cekAda) {
            // Update data lama (Revisi)
            $this->pengajuanHardcoverModel->update($cekAda['id'], [
                'file_hardcover' => $namaFile,
                'tanggal_pengajuan' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),
                'status' => 'TERTUNDA',
                'komentar_penolakan' => null // Reset komentar
            ]);
        } else {
            // Insert baru
            $this->pengajuanHardcoverModel->insert([
                'id_skripsi' => $idSkripsi,
                'file_hardcover' => $namaFile,
                'tanggal_pengajuan' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),
                'status' => 'TERTUNDA'
            ]);
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Hard Cover berhasil diunggah!"]);
        return redirect()->to(base_url("Mahasiswa/pengajuanHardcover"));
    }
}