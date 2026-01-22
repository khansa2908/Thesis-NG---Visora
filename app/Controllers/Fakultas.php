<?php

namespace App\Controllers;

class Fakultas extends BaseController
{
    protected $kalenderSkripsiModel;
    protected $skripsiModel;
    protected $proposalModel;
    protected $dosenModel;
    protected $bidangModel;
    protected $makalahModel;
    protected $mahasiswaModel;
    protected $prodiModel;
    protected $sidangSkripsiModel;
    protected $pembimbingModel;
    protected $penilaianSidangModel;
    protected $semesterModel;
    protected $akunModel;
    protected $fakultasModel;
    protected $penilaianSidangNewModel;
    protected $penilaianSidangParamModel;
    protected $dashboardModel;

    public function __construct()
    {
        $this->kalenderSkripsiModel = new \App\Models\KalenderSkripsiModel();
        $this->skripsiModel = new \App\Models\SkripsiModel();
        $this->proposalModel = new \App\Models\ProposalModel();
        $this->dosenModel = new \App\Models\DosenModel();
        $this->bidangModel = new \App\Models\BidangModel();
        $this->makalahModel = new \App\Models\MakalahModel();
        $this->mahasiswaModel = new \App\Models\MahasiswaModel();
        $this->prodiModel = new \App\Models\ProgramStudiModel();
        $this->sidangSkripsiModel = new \App\Models\SidangSkripsiModel();
        $this->pembimbingModel = new \App\Models\PembimbingModel();
        $this->penilaianSidangModel = new \App\Models\PenilaianSidangModel();
        $this->semesterModel = new \App\Models\SemesterModel();
        $this->akunModel = new \App\Models\AkunModel();
        $this->fakultasModel = new \App\Models\FakultasModel();
        $this->penilaianSidangNewModel = new \App\Models\PenilaianSidangNewModel();
        $this->penilaianSidangParamModel = new \App\Models\PenilaianSidangParamModel();
        $this->dashboardModel = new \App\Models\DashboardModel();
    }

    public function dashboard()

    {

        $db = \Config\Database::connect();
        $id_ti = 1;
        $id_pdsi = 2;

        // ==========================================================
        // A. LOGIKA DROPDOWN SEMESTER
        // ==========================================================
        $list_semester = $db->table('semester')->orderBy('kode', 'DESC')->get()->getResultArray();
        $selected_semester = $this->request->getVar('filter_semester');

        // Default semester aktif jika belum dipilih
        if (empty($selected_semester)) {
            $sem_aktif = $db->table('semester')->where('status', 'AKTIF')->get()->getRow();
            $selected_semester = ($sem_aktif) ? $sem_aktif->kode : ($list_semester[0]['kode'] ?? null);
        }

        // ==========================================================
        // FUNGSI BANTUAN: SORTING A-Z (INISIAL/NAMA)
        // ==========================================================
        // Kita taruh sini biar bisa dipake sama semua grafik di bawah
        $sort_az = function ($a, $b) {
            $nameA = !empty($a['inisial']) ? $a['inisial'] : $a['nama'];
            $nameB = !empty($b['inisial']) ? $b['inisial'] : $b['nama'];
            return strcasecmp($nameA, $nameB);
        };

        // ==========================================================
        // B. DATA PIE CHART  - Status TA)
        // ==========================================================

        // --- 1. PRODI TI ---
        $builderTiSempro = $db->table('seminar_proposal')
            ->join('proposal', 'proposal.id = seminar_proposal.id_proposal')
            ->join('mahasiswa', 'mahasiswa.npm = proposal.npm')
            ->where('mahasiswa.id_prodi', $id_ti);
        if ($selected_semester) $builderTiSempro->where('proposal.kode_semester', $selected_semester);
        $ti_sempro = $builderTiSempro->countAllResults();

        $builderTiPrasidang = $db->table('seminar_prasidang')
            ->join('skripsi', 'skripsi.id = seminar_prasidang.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->where('mahasiswa.id_prodi', $id_ti);
        if ($selected_semester) $builderTiPrasidang->where('skripsi.kode_semester', $selected_semester);
        $ti_prasidang = $builderTiPrasidang->countAllResults();

        $builderTiSidang = $db->table('sidang_skripsi')
            ->join('skripsi', 'skripsi.id = sidang_skripsi.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->where('mahasiswa.id_prodi', $id_ti);
        if ($selected_semester) $builderTiSidang->where('skripsi.kode_semester', $selected_semester);
        $ti_sidang = $builderTiSidang->countAllResults();

        // --- 2. PRODI PdSI ---
        $builderPdsiSempro = $db->table('seminar_proposal')->join('proposal', 'proposal.id = seminar_proposal.id_proposal')->join('mahasiswa', 'mahasiswa.npm = proposal.npm')->where('mahasiswa.id_prodi', $id_pdsi);
        if ($selected_semester) $builderPdsiSempro->where('proposal.kode_semester', $selected_semester);
        $pdsi_sempro = $builderPdsiSempro->countAllResults();

        $builderPdsiPrasidang = $db->table('seminar_prasidang')->join('skripsi', 'skripsi.id = seminar_prasidang.id_skripsi')->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')->where('mahasiswa.id_prodi', $id_pdsi);
        if ($selected_semester) $builderPdsiPrasidang->where('skripsi.kode_semester', $selected_semester);
        $pdsi_prasidang = $builderPdsiPrasidang->countAllResults();

        $builderPdsiSidang = $db->table('sidang_skripsi')->join('skripsi', 'skripsi.id = sidang_skripsi.id_skripsi')->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')->where('mahasiswa.id_prodi', $id_pdsi);
        if ($selected_semester) $builderPdsiSidang->where('skripsi.kode_semester', $selected_semester);
        $pdsi_sidang = $builderPdsiSidang->countAllResults();


        // ==========================================================
        // C. DATA BAR CHART  - Beban Pembimbing)
        // ==========================================================

        // --- 1. Beban Dosen TI ---
        $queryBarTI = $db->table('dosen')
            ->select('dosen.inisial, dosen.nama, COUNT(pembimbing.id) as jumlah_bimbingan')
            ->join('pembimbing', 'pembimbing.id_dosen = dosen.id')
            ->join('skripsi', 'skripsi.id = pembimbing.id_skripsi')
            ->where('dosen.id_prodi', $id_ti)
            ->where('skripsi.kode_semester', $selected_semester)
            ->groupBy('dosen.id')
            // ->orderBy('jumlah_bimbingan', 'DESC') // <-- HAPUS INI (Karna minta urut Abjad)
            ->orderBy('dosen.inisial', 'ASC')        // <-- GANTI JADI INI
            ->get()->getResultArray();

        // URUTKAN LAGI PAKAI PHP BIAR PASTI (Inisial/Nama)
        usort($queryBarTI, $sort_az);

        $label_dosen_ti = [];
        $value_dosen_ti = [];
        foreach ($queryBarTI as $row) {
            $label_dosen_ti[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_dosen_ti[] = $row['jumlah_bimbingan'];
        }

        // --- 2. Beban Dosen PdSI ---
        $queryBarPdSI = $db->table('dosen')
            ->select('dosen.inisial, dosen.nama, COUNT(pembimbing.id) as jumlah_bimbingan')
            ->join('pembimbing', 'pembimbing.id_dosen = dosen.id')
            ->join('skripsi', 'skripsi.id = pembimbing.id_skripsi')
            ->where('dosen.id_prodi', $id_pdsi)
            ->where('skripsi.kode_semester', $selected_semester)
            ->groupBy('dosen.id')
            // ->orderBy('jumlah_bimbingan', 'DESC') // <-- HAPUS INI
            ->orderBy('dosen.inisial', 'ASC')        // <-- GANTI JADI INI
            ->get()->getResultArray();

        // URUTKAN LAGI PAKAI PHP BIAR PASTI
        usort($queryBarPdSI, $sort_az);

        $label_dosen_pdsi = [];
        $value_dosen_pdsi = [];
        foreach ($queryBarPdSI as $row) {
            $label_dosen_pdsi[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_dosen_pdsi[] = $row['jumlah_bimbingan'];
        }

        // ==========================================================
        // D. DATA BAR CHART  - Beban Reviewer Sempro)
        // ==========================================================

        // --- 1. Reviewer Sempro TI ---
        $data_reviewer_ti = $this->dashboardModel->getBebanReviewerSempro($selected_semester, $id_ti);
        usort($data_reviewer_ti, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_reviewer_ti = [];
        $value_reviewer_ti = [];
        foreach ($data_reviewer_ti as $row) {
            $label_reviewer_ti[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_reviewer_ti[] = $row['jumlah_uji'];
        }

        // --- 2. Reviewer Sempro PdSI ---
        $data_reviewer_pdsi = $this->dashboardModel->getBebanReviewerSempro($selected_semester, $id_pdsi);
        usort($data_reviewer_pdsi, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_reviewer_pdsi = [];
        $value_reviewer_pdsi = [];
        foreach ($data_reviewer_pdsi as $row) {
            $label_reviewer_pdsi[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_reviewer_pdsi[] = $row['jumlah_uji'];
        }

        // ==========================================================
        // E. DATA BAR CHART  - Beban Reviewer Pra-Sidang)
        // ==========================================================

        // --- 1. Reviewer Pra-Sidang TI ---
        $data_pra_ti = $this->dashboardModel->getBebanReviewerPrasidang($selected_semester, $id_ti);
        usort($data_pra_ti, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_pra_ti = [];
        $value_pra_ti = [];
        foreach ($data_pra_ti as $row) {
            $label_pra_ti[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_pra_ti[] = $row['jumlah_uji'];
        }

        // --- 2. Reviewer Pra-Sidang PdSI ---
        $data_pra_pdsi = $this->dashboardModel->getBebanReviewerPrasidang($selected_semester, $id_pdsi);
        usort($data_pra_pdsi, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_pra_pdsi = [];
        $value_pra_pdsi = [];
        foreach ($data_pra_pdsi as $row) {
            $label_pra_pdsi[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_pra_pdsi[] = $row['jumlah_uji'];
        }

        // ==========================================================
        // F. DATA BAR CHART  - Beban Penguji Sidang Akhir)
        // ==========================================================

        // --- 1. Penguji Sidang TI ---
        $data_sidang_ti = $this->dashboardModel->getBebanPengujiSidang($selected_semester, $id_ti);
        usort($data_sidang_ti, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_sidang_ti = [];
        $value_sidang_ti = [];
        foreach ($data_sidang_ti as $row) {
            $label_sidang_ti[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_sidang_ti[] = $row['jumlah_uji'];
        }

        // --- 2. Penguji Sidang PdSI ---
        $data_sidang_pdsi = $this->dashboardModel->getBebanPengujiSidang($selected_semester, $id_pdsi);
        usort($data_sidang_pdsi, $sort_az); // <--- TAMBAHAN: Sorting A-Z

        $label_sidang_pdsi = [];
        $value_sidang_pdsi = [];
        foreach ($data_sidang_pdsi as $row) {
            $label_sidang_pdsi[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_sidang_pdsi[] = $row['jumlah_uji'];
        }


        // ==========================================================
        // KIRIM DATA KE VIEW
        // ==========================================================
        $data = [
            'title' => 'Dasbor Akademik',
            'list_semester'     => $list_semester,
            'selected_semester' => $selected_semester,

            // Pie Chart Data (US 33)
            'ti_sempro'    => $ti_sempro,
            'ti_prasidang' => $ti_prasidang,
            'ti_sidang'    => $ti_sidang,
            'pdsi_sempro'    => $pdsi_sempro,
            'pdsi_prasidang' => $pdsi_prasidang,
            'pdsi_sidang'    => $pdsi_sidang,

            // Bar Chart Pembimbing (US 34)
            'label_dosen_ti' => json_encode($label_dosen_ti),
            'value_dosen_ti' => json_encode($value_dosen_ti),
            'label_dosen_pdsi' => json_encode($label_dosen_pdsi),
            'value_dosen_pdsi' => json_encode($value_dosen_pdsi),

            // Bar Chart Reviewer Sempro (US 35)
            'label_reviewer_ti' => json_encode($label_reviewer_ti),
            'value_reviewer_ti' => json_encode($value_reviewer_ti),
            'label_reviewer_pdsi' => json_encode($label_reviewer_pdsi),
            'value_reviewer_pdsi' => json_encode($value_reviewer_pdsi),

            // Bar Chart Reviewer Pra-Sidang (US 36)
            'label_pra_ti' => json_encode($label_pra_ti),
            'value_pra_ti' => json_encode($value_pra_ti),
            'label_pra_pdsi' => json_encode($label_pra_pdsi),
            'value_pra_pdsi' => json_encode($value_pra_pdsi),

            // Bar Chart Penguji Sidang (US 37)
            'label_sidang_ti' => json_encode($label_sidang_ti),
            'value_sidang_ti' => json_encode($value_sidang_ti),
            'label_sidang_pdsi' => json_encode($label_sidang_pdsi),
            'value_sidang_pdsi' => json_encode($value_sidang_pdsi),
        ];

        return view('fakultas/dashboard', $data);
    }

    public function index()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        return redirect()->to(base_url('fakultas/dashboard'));
    }

    public function semester()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $data = [
            "title" => "Kelola Semester",
            "semester" => $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray()
        ];

        return view("fakultas/semester", $data);
    }



    public function insertSemester()
    {
        //autentikasi

        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $tahunAkademik = $this->request->getPost("tahunAkademik");
        $semester = $this->request->getPost("semester");
        $kodeSemester = "$tahunAkademik-$semester";

        //periksa apakah semester udah pernah dibuat sebelomnya
        if ($this->semesterModel->find($kodeSemester)) {
            session()->setFlashdata(
                "message",
                [
                    "icon" => "error",
                    "title" => "Penambahan Semester Gagal",
                    "text" => "Semester dan Tahun akademik tersebut telah ada!"
                ]
            );
            return redirect()->to(base_url("fakultas/semester"));
        }

        $namaSemester = "Tahun Akademik " . $tahunAkademik . "/" . ($tahunAkademik + 1) . " Semester " . ($semester == 1 ? "Ganjil" : "Genap");
        $this->semesterModel->insert([
            'kode' => $kodeSemester,
            'nama' => $namaSemester,
            'status' => 'NON-AKTIF'
        ]);

        session()->setFlashdata(
            "message",
            [
                "icon" => "success",
                "title" => "Penambahan Semester Berhasil",
                "text" => "Semester berhasil ditambahkan!"
            ]
        );
        return redirect()->to(base_url("fakultas/semester"));
    }

    public function deleteSemester($kode)
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        //periksa apakah kode semester beneran ada atau gak
        $dataSemester = $this->semesterModel->find($kode);
        if (!$dataSemester) {
            session()->setFlashdata("message", [
                "icon" => "error",
                "title" => "Hapus Semester Gagal",
                "text" => "Kode Semester tidak ditemukan"
            ]);
            return redirect()->to(base_url("fakultas/semester"));
        }

        //kalo semester statusnya aktif, gak boleh diapus
        if ($dataSemester['status'] == 'AKTIF') {
            session()->setFlashdata("message", [
                "icon" => "error",
                "title" => "Hapus Semester Gagal",
                "text" => "Tidak dapat menghapus semester yang sedang aktif"
            ]);
            return redirect()->to(base_url("fakultas/semester"));
        }

        //kalo semester udah jadi FK ditempat lain, gak boleh diapus
        if (!$this->semesterModel->isSemesterDeleteable($kode)) {
            session()->setFlashdata("message", [
                "icon" => "error",
                "title" => "Hapus Semester Gagal",
                "text" => "Semester ini sudah digunakan"
            ]);
            return redirect()->to(base_url("fakultas/semester"));
        }

        $this->semesterModel->delete($kode);

        session()->setFlashdata("message", [
            "icon" => "success",
            "title" => "Hapus Semester Berhasil",
            "text" => "Semester Berhasil dihapus"
        ]);

        return redirect()->to(base_url("fakultas/semester"));
    }


    public function activateSemester($kode)
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        if (count($this->semesterModel->where("status", 'AKTIF')->get()->getResultArray()) > 0) {
            session()->setFlashdata("message", [
                "icon" => "error",
                "title" => "Aktivasi Semester Gagal",
                "text" => "Hanya boleh ada satu semester yang aktif dalam satu waktu!"
            ]);
            return redirect()->to(base_url("fakultas/semester"));
        }

        $this->semesterModel->update($kode, ['status' => 'AKTIF']);

        session()->setFlashdata("message", [
            "icon" => "success",
            "title" => "Aktivasi Semester Berhasil",
            "text" => "Semester Berhasil diaktifkan"
        ]);

        return redirect()->to(base_url("fakultas/semester"));
    }


    public function nonactivateSemester($kode)
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $this->semesterModel->update($kode, ['status' => 'NON-AKTIF']);

        session()->setFlashdata("message", [
            "icon" => "success",
            "title" => "Non-Aktivasi Semester Berhasil",
            "text" => "Semester Berhasil dinonaktifkan"
        ]);

        return redirect()->to(base_url("fakultas/semester"));
    }


    public function proposal()
    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }


        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $proposal = [];
        if ($currentSemester != null)
            $proposal = $this->proposalModel->getAllProposalMahasiswaByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();


        $dosen = $this->dosenModel->findAll();

        $bidang = $this->bidangModel->findAll();

        $data = [

            "title" => "Proposal Mahasiswa FTI",

            "proposal" => $proposal,

            "bidang" => $bidang,

            "dosen" => $dosen,

            "semesterList" => $allSemester

        ];

        return view("fakultas/proposal", $data);
    }



    public function skripsi()
    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $skripsi = [];
        if ($currentSemester != null)
            $skripsi = $this->skripsiModel->getAllSkripsiMahasiswaByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            "title" => "Skripsi Mahasiswa FTI",

            "skripsi" => $skripsi,

            "semesterList" => $allSemester

        ];

        return view("fakultas/skripsi", $data);
    }



    public function makalah()
    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $makalah = [];
        if ($currentSemester != null)
            $makalah = $this->makalahModel->getAllMakalahByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            "title" => "Makalah Mahasiswa FTI",

            "makalah" => $makalah,

            "semesterList" => $allSemester,

        ];

        return view("fakultas/makalah", $data);
    }



    public function pembimbing()
    {

        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $pembimbing = [];
        if ($currentSemester != null)
            $pembimbing = $this->mahasiswaModel->getAllPembimbingMahasiswaByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [
            "title" => "Pembimbing Mahasiswa FTI",
            "pembimbing" => $pembimbing,
            "semesterList" => $allSemester,
        ];

        return view("fakultas/pembimbing", $data);
    }



    public function hasilSidangSkripsi($idSkripsi)
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }


        $lastSkripsi = $this->skripsiModel->find($idSkripsi);
        if ($lastSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        $mahasiswa = $this->mahasiswaModel->find($lastSkripsi['npm']);
        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $sidangSkripsi = $this->sidangSkripsiModel->getWhere(['id_skripsi' => $idSkripsi])->getResultArray();
        if (count($sidangSkripsi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $sidangSkripsi = $this->sidangSkripsiModel->getWhere(['id_skripsi' => $idSkripsi])->getResultArray()[0];

        $tahunAkademik = explode("-", $lastSkripsi['kode_semester'])[0];
        $semester = explode("-", $lastSkripsi['kode_semester'])[1];

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->hasilSidangSkripsiOld($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi);
        } else {
            return $this->hasilSidangSkripsiNew($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi);
        }
    }

    private function hasilSidangSkripsiOld($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi)
    {
        $idSidangSkripsi = $sidangSkripsi['id'];

        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);


        $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();
        $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();
        $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();
        $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi['dosen_penguji']])->getResultArray();


        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
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

        return view("fakultas/hasil_sidang_skripsi", $data);
    }

    private function hasilSidangSkripsiNew($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi)
    {
        $idSidangSkripsi = $sidangSkripsi['id'];
        $detailSidangSkripsi = $this->sidangSkripsiModel->getDetailSidangSkripsiById($idSidangSkripsi);

        if (count($detailSidangSkripsi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($lastSkripsi['jenis'] == JENIS_SKRIPSI_REGULER) {
            return $this->hasilSidangSkripsiNonProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi, $mahasiswa, $prodi);
        } else {
            return $this->hasilSidangSkripsiProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi, $mahasiswa, $prodi);
        }
    }

    private function hasilSidangSkripsiNonProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi, $mahasiswa, $prodi)
    {
        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['dosen_penguji'], 'Dosen Penguji', $sidangSkripsi["id"]);
        $nilaiPembimbing1 = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing1'], 'Pembimbing Ilmu 1', $sidangSkripsi["id"]);
        $nilaiPembimbing2 = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing2'], 'Pembimbing Ilmu 2', $sidangSkripsi["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($detailSidangSkripsi[0]['id_pembimbing_agama'], 'Pembimbing Agama', $sidangSkripsi["id"]);

        $rubrikNilai = $this->penilaianSidangParamModel->getRubrikPdsi2023();

        $rataRataNilai = $this->penilaianSidangNewModel->getAverageByIdSidang(
            $lastSkripsi['id'],
            $idSidangSkripsi,
            $detailSidangSkripsi[0]['dosen_penguji'],
            $detailSidangSkripsi[0]['id_pembimbing1'],
            $detailSidangSkripsi[0]['id_pembimbing2'],
            $detailSidangSkripsi[0]['id_pembimbing_agama']
        );

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
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

        return view("fakultas/hasil_sidang_skripsi_non_proyek", $data);
    }

    private function hasilSidangSkripsiProyek($idSidangSkripsi, $sidangSkripsi, $lastSkripsi, $detailSidangSkripsi, $mahasiswa, $prodi)
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
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
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

        return view("fakultas/hasil_sidang_skripsi_proyek", $data);
    }


    public function kalender()

    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        // Ambil Semester Aktif
        $semesterAktif = $this->semesterModel->where('status', 'AKTIF')->first();
        
        $jadwalGrouped = []; // Array untuk menampung data yang sudah digabung

       if ($semesterAktif) {
            // 1. Ambil data mentah
            $rawJadwal = $this->kalenderSkripsiModel
                            ->select('kalender_skripsi.*, program_studi.nama as nama_prodi')
                            ->join('program_studi', 'program_studi.id = kalender_skripsi.id_prodi')
                            ->where('kalender_skripsi.kode_semester', $semesterAktif['kode'])
                            ->orderBy('tanggal_mulai', 'DESC')
                            ->findAll();

            // 2. Lakukan Grouping Logic
            foreach ($rawJadwal as $row) {
                // Buat unique key berdasarkan atribut kegiatan (selain ID dan Prodi)
                // Kita anggap kegiatan sama jika Nama, Jenis, Mulai, dan Selesai-nya SAMA.
                $key = md5($row['nama_kegiatan'] . $row['jenis_kegiatan'] . $row['tanggal_mulai'] . $row['tanggal_selesai']);

                if (!isset($jadwalGrouped[$key])) {
                    // Jika belum ada, buat entry baru
                    $jadwalGrouped[$key] = [
                        'id_utama' => $row['id'], // ID ini dipakai sebagai referensi update
                        'nama_kegiatan' => $row['nama_kegiatan'],
                        'jenis_kegiatan' => $row['jenis_kegiatan'],
                        'tanggal_mulai' => $row['tanggal_mulai'],
                        'tanggal_selesai' => $row['tanggal_selesai'],
                        'prodi_list' => [], // Array nama prodi
                        'prodi_ids' => []   // Array ID prodi (untuk checkbox)
                    ];
                }

                // Masukkan data prodi ke entry tersebut
                $jadwalGrouped[$key]['prodi_list'][] = $row['nama_prodi'];
                $jadwalGrouped[$key]['prodi_ids'][] = $row['id_prodi'];
            }
        }

        $data = [
            "title" => "Kelola Kalender Skripsi (" . ($semesterAktif['nama'] ?? 'Tidak Ada Semester Aktif') . ")",
            "kegiatanSkripsi" => $jadwalGrouped, // Kirim data yang sudah di-group
            "listProdi" => $this->prodiModel->findAll(),
            "semesterAktif" => $semesterAktif
        ];

        return view("fakultas/kalender", $data);
    }



    public function insertKalender()
    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }

// 1. Ambil Semester Aktif
        $semesterAktif = $this->semesterModel->where('status', 'AKTIF')->first();
        
        if (!$semesterAktif) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Tidak ada Semester Aktif! Harap aktifkan semester terlebih dahulu."]);
            return redirect()->to(base_url("fakultas/kalender"));
        }

        // Ambil Data Input
        $nama       = $this->request->getPost("namaKegiatan", FILTER_SANITIZE_SPECIAL_CHARS);
        $tglMulai   = $this->request->getPost("tanggalMulai");
        $tglSelesai = $this->request->getPost("tanggalSelesai");
        $prodiDipilih = $this->request->getPost("prodi"); 
        
        $jenis = $this->request->getPost("jenis_kegiatan");
        if (empty($jenis)) $jenis = null;

        if (!$prodiDipilih || count($prodiDipilih) == 0) {
             session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Pilih minimal satu Prodi!"]);
             return redirect()->to(base_url("fakultas/kalender"));
        }

        // 2. Simpan dengan KODE SEMESTER AKTIF
        foreach ($prodiDipilih as $idProdi) {
            $this->kalenderSkripsiModel->save([
                'nama_kegiatan'   => $nama,
                'jenis_kegiatan'  => $jenis,
                'id_prodi'        => $idProdi,
                'kode_semester'   => $semesterAktif['kode'], // Simpan kode semester
                'tanggal_mulai'   => $tglMulai,
                'tanggal_selesai' => $tglSelesai
            ]);
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Jadwal Berhasil ditambahkan pada Semester Aktif"]);
        return redirect()->to(base_url("fakultas/kalender"));

    }



    public function updateKalender($id)
    {

        if (!$this->authenticate(["fakultas"])) return redirect()->to(base_url("unauthorized.php"));
        if ($this->request->getMethod() != 'post') return redirect()->back();

        // 1. Ambil Data Input
        $idRef      = $this->request->getPost('id'); // ID salah satu record (referensi)
        $namaBaru   = $this->request->getPost("namaKegiatan", FILTER_SANITIZE_SPECIAL_CHARS);
        $jenisBaru  = $this->request->getPost("jenis_kegiatan");
        $mulaiBaru  = $this->request->getPost("tanggalMulai");
        $selesaiBaru= $this->request->getPost("tanggalSelesai");
        $prodiInput = $this->request->getPost("prodi"); // Array ID Prodi [1, 2]

        if (!$prodiInput || count($prodiInput) == 0) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Minimal satu prodi harus dipilih!"]);
            return redirect()->back();
        }

        // 2. Ambil Data Referensi Lama dari Database
        $oldData = $this->kalenderSkripsiModel->find($idRef);
        if (!$oldData) {
            return redirect()->back();
        }

        // 3. Cari Semua Record "Saudara" (Satu Group)
        // Kita cari semua record yang punya Nama, Jenis, Waktu SAMA dengan data lama
        // Gunanya untuk tahu record mana saja yang sedang kita edit "batch"-nya
        $siblings = $this->kalenderSkripsiModel
                         ->where('nama_kegiatan', $oldData['nama_kegiatan'])
                         ->where('jenis_kegiatan', $oldData['jenis_kegiatan'])
                         ->where('tanggal_mulai', $oldData['tanggal_mulai'])
                         ->where('tanggal_selesai', $oldData['tanggal_selesai'])
                         ->where('kode_semester', $oldData['kode_semester'])
                         ->findAll();

        // Siapkan array ID record yang ada di database berdasarkan ID Prodinya
        // Format: [ id_prodi => id_record_kalender ]
        $existingRecordsMap = [];
        foreach ($siblings as $sib) {
            $existingRecordsMap[$sib['id_prodi']] = $sib['id'];
        }

        // 4. LOGIC SINKRONISASI (Insert/Update/Delete)

        // LOOP A: Cek Input User vs Existing DB
        foreach ($prodiInput as $idProdi) {
            if (array_key_exists($idProdi, $existingRecordsMap)) {
                // KASUS 1: Prodi dicentang user DAN sudah ada di DB.
                // Action: UPDATE datanya (siapa tahu nama/tanggal berubah)
                $idToUpdate = $existingRecordsMap[$idProdi];
                $this->kalenderSkripsiModel->update($idToUpdate, [
                    'nama_kegiatan'   => $namaBaru,
                    'jenis_kegiatan'  => $jenisBaru,
                    'tanggal_mulai'   => $mulaiBaru,
                    'tanggal_selesai' => $selesaiBaru
                ]);
                
                // Hapus dari map agar sisa map nanti adalah yang perlu dihapus
                unset($existingRecordsMap[$idProdi]); 
            } else {
                // KASUS 2: Prodi dicentang user TAPI belum ada di DB.
                // Action: INSERT baru
                $this->kalenderSkripsiModel->insert([
                    'nama_kegiatan'   => $namaBaru,
                    'jenis_kegiatan'  => $jenisBaru,
                    'id_prodi'        => $idProdi,
                    'kode_semester'   => $oldData['kode_semester'], // Ikut semester lama
                    'tanggal_mulai'   => $mulaiBaru,
                    'tanggal_selesai' => $selesaiBaru
                ]);
            }
        }

        // LOOP B: Sisanya di Map (Ada di DB, tapi TIDAK dicentang user)
        // KASUS 3: Prodi di-uncheck user.
        // Action: DELETE
        if (!empty($existingRecordsMap)) {
            foreach ($existingRecordsMap as $idProdi => $idToDelete) {
                $this->kalenderSkripsiModel->delete($idToDelete);
            }
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Jadwal kegiatan berhasil diperbarui!"]);
        return redirect()->to(base_url("fakultas/kalender"));
    }



    public function deleteKalender($id)
    {

        //autentikasi

        if (!$this->authenticate(["fakultas"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->kalenderSkripsiModel->delete($id);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Kegiatan Berhasil", "text" => "Kegiatan Berhasil dihapus"]);

        return redirect()->to(base_url("fakultas/kalender"));// 1. Ambil data referensi
        $target = $this->kalenderSkripsiModel->find($id);
        
        if ($target) {
            // 2. Hapus BATCH (Hapus semua yang nama, jenis, dan tanggalnya sama di semester ini)
            $this->kalenderSkripsiModel
                 ->where('nama_kegiatan', $target['nama_kegiatan'])
                 ->where('jenis_kegiatan', $target['jenis_kegiatan'])
                 ->where('tanggal_mulai', $target['tanggal_mulai'])
                 ->where('tanggal_selesai', $target['tanggal_selesai'])
                 ->where('kode_semester', $target['kode_semester'])
                 ->delete();
        }
        
        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Kegiatan Berhasil", "text" => "Kegiatan Berhasil dihapus"]);

        return redirect()->to(base_url("fakultas/kalender"));

    }



    public function dosen()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $listDosen = $this->dosenModel->getAllDosenInfo();
        $listProdi = $this->prodiModel->findAll();

        $data = [
            "title" => "Kelola Dosen FTI",
            "listDosen" => $listDosen,
            "listProdi" => $listProdi,
        ];

        return view("fakultas/dosen", $data);
    }

    public function updateDosen()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dosenId = $this->request->getPost("idDosen");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);
        $inisial = $this->request->getPost("inisial", FILTER_SANITIZE_SPECIAL_CHARS);
        $prodiId = $this->request->getPost("prodi", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->dosenModel->isInitialAlreadyUsed($dosenId, $inisial)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Dosen Gagal", "text" => "Inisial telah digunakan!"]);
            return redirect()->to(base_url("Fakultas/dosen"));
        }

        $dataDosen = $this->dosenModel->getDosenInfo($dosenId);
        if ($dataDosen == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataDosen['id_akun'], [
            "email" => $email,
        ]);

        $this->dosenModel->update($dosenId, [
            "nama" => $nama,
            "email" => $email,
            "inisial" => $inisial,
            "id_prodi" => $prodiId,
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Dosen Berhasil", "text" => "Data dosen berhasil diubah!"]);
        return redirect()->to(base_url("Fakultas/dosen"));
    }



    public function profile()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->fakultasModel->getFakultasInfo(session()->get("user_session")['id']);
        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Ubah Data Diri',
            'dataDiri' => $dataAkun,
        ];

        return view("Fakultas/profile", $data);
    }


    public function saveProfile()
    {
        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $idFakultas = $this->request->getPost("id");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $dataFakultas = $this->fakultasModel->getFakultasInfo($idFakultas);
        if ($dataFakultas == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->akunModel->isEmailAlreadyUsed($dataFakultas['id_akun'], $email)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Email telah digunakan!"]);
            return redirect()->to(base_url("Fakultas/profile"));
        }

        $this->akunModel->update($dataFakultas['id_akun'], [
            "email" => $email,
        ]);

        $this->fakultasModel->update($idFakultas, [
            "nama" => $nama,
            "email" => $email,
        ]);


        session()->set("user_session", [
            'id' => $dataFakultas['id'],
            'nama' => $nama,
            'email' => $email,
            'prodi' => "-",
            'roles' => ['fakultas'],
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Diri Berhasil", "text" => "Data diri berhasil diubah!"]);
        return redirect()->to(base_url("Fakultas/profile"));
    }


    public function rekapDosenPembimbing()
    {

        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $pembimbing = [];
        if ($currentSemester != null)
            $pembimbing = $this->dosenModel->getDosenWithCountMahasiswaBimbinganByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [
            "title" => "Rekap Dosen Pembimbing FTI",
            "pembimbing" => $pembimbing,
            "semesterList" => $allSemester,
        ];

        return view("fakultas/rekap_dosen_pembimbing", $data);
    }


    public function detailRekapDospem($id_dosen, $kode_semester)
    {

        //autentikasi
        if (!$this->authenticate(["fakultas"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dosen = $this->dosenModel->find($id_dosen);
        if ($dosen == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $semester = $this->semesterModel->find($kode_semester);
        if ($semester == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mahasiswaBimbingan = $this->dosenModel->getMahasiswaBimbinganByIdDosenAndKodeSemester($dosen['id'], $kode_semester);

        $data = [
            "title" => "Detail Mahasiswa Bimbingan",
            "mahasiswaBimbingan" => $mahasiswaBimbingan,
            "dosen" => $dosen,
            "semester" => $semester,
        ];

        return view("fakultas/detail_rekap_dospem", $data);
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
}
