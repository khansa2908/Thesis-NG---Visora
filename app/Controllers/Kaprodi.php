<?php


namespace App\Controllers;


use CodeIgniter\I18n\Time;


class Kaprodi extends BaseController

{

    protected $proposalModel;

    protected $dosenModel;

    protected $mahasiswaModel;

    protected $bidangModel;

    protected $prodiModel;

    protected $semproModel;

    protected $pembimbingModel;

    protected $skripsiModel;

    protected $seminarPrasidangModel;

    protected $pengajuanPrasidangModel;

    protected $pengajuanSidangModel;

    protected $sidangSkripsiModel;

    protected $penilaianSidangModel;

    protected $makalahModel;

    protected $semesterModel;

    protected $akunModel;

    protected $modeSemproModel;

    protected $klienModel;

    protected $penilaianSidangNewModel;

    protected $penilaianSidangParamModel;

    protected $dashboardModel;

    protected $sidangAkhirModel;

    public function __construct()
    {

        $this->proposalModel = new \App\Models\ProposalModel();

        $this->dosenModel = new \App\Models\DosenModel();

        $this->mahasiswaModel = new \App\Models\MahasiswaModel();

        $this->bidangModel = new \App\Models\BidangModel();

        $this->prodiModel = new \App\Models\ProgramStudiModel();

        $this->semproModel = new \App\Models\SeminarProposalModel();

        $this->pembimbingModel = new \App\Models\PembimbingModel();

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

        $this->klienModel = new \App\Models\KlienModel();

        $this->penilaianSidangNewModel = new \App\Models\PenilaianSidangNewModel();

        $this->penilaianSidangParamModel = new \App\Models\PenilaianSidangParamModel();

        $this->dashboardModel = new \App\Models\DashboardModel();

        $this->sidangAkhirModel = model('App\Models\SidangAkhirModel');
    }



    public function index()
    {

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        return redirect()->to(base_url('kaprodi/dashboard'));
    }




    public function dashboard()
    {
        $db = \Config\Database::connect();

        // ==========================================================
        // 1. DETEKSI "JALUR KHUSUS" (SOLUSI FINAL)
        // ==========================================================
        // Ambil email dari session
        $sess_data   = session()->get('user_session');
        $email_login = $sess_data['email'] ?? '';

        // LOGIKA PENENTUAN ID PRODI (HARDCODE BIAR PASTI BENAR)
        if ($email_login == 'pdsi.yarsi@gmail.com') {
            // Kalau email ini yg login, LANGSUNG SET KE 2 (PdSI)
            $id_prodi = 2;
        } elseif ($email_login == 'teknik.informatika@gmail.com') {
            // Kalau email ini yg login, LANGSUNG SET KE 1 (TI)
            $id_prodi = 1;
        } else {
            // Kalau bukan dua email sakti di atas, baru cari di database
            // (Siapa tau Kaprodi beneran yg login pake NIDN)
            $dosen = $db->table('dosen')->where('email', $email_login)->get()->getRowArray();
            $id_prodi = ($dosen && !empty($dosen['id_prodi'])) ? $dosen['id_prodi'] : 1;
        }

        // Judul Dinamis
        $nama_prodi = ($id_prodi == 1) ? 'Teknik Informatika' : 'Perpustakaan & Sains Informasi';


        // ==========================================================
        // 2. LOGIKA SEMESTER (JANGAN DIHAPUS, BIAR GAK ERROR)
        // ==========================================================
        $list_semester = $db->table('semester')->orderBy('kode', 'DESC')->get()->getResultArray();
        $selected_semester = $this->request->getVar('filter_semester');

        if (empty($selected_semester)) {
            $sem_aktif = $db->table('semester')->where('status', 'AKTIF')->get()->getRow();
            $selected_semester = ($sem_aktif) ? $sem_aktif->kode : ($list_semester[0]['kode'] ?? null);
        }


        // ==========================================================
        // 3. QUERY DATA PIE CHART (Sesuai ID Prodi yg sudah dipaksa tadi)
        // ==========================================================

        // Sempro
        $builderSempro = $db->table('seminar_proposal')
            ->join('proposal', 'proposal.id = seminar_proposal.id_proposal')
            ->join('mahasiswa', 'mahasiswa.npm = proposal.npm')
            ->where('mahasiswa.id_prodi', $id_prodi);
        if ($selected_semester) $builderSempro->where('proposal.kode_semester', $selected_semester);
        $jumlah_sempro = $builderSempro->countAllResults();

        // Prasidang
        $builderPrasidang = $db->table('seminar_prasidang')
            ->join('skripsi', 'skripsi.id = seminar_prasidang.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->where('mahasiswa.id_prodi', $id_prodi);
        if ($selected_semester) $builderPrasidang->where('skripsi.kode_semester', $selected_semester);
        $jumlah_prasidang = $builderPrasidang->countAllResults();

        // Sidang
        $builderSidang = $db->table('sidang_skripsi')
            ->join('skripsi', 'skripsi.id = sidang_skripsi.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->where('mahasiswa.id_prodi', $id_prodi);
        if ($selected_semester) $builderSidang->where('skripsi.kode_semester', $selected_semester);
        $jumlah_sidang = $builderSidang->countAllResults();


        // ==========================================================
        // 4. QUERY BAR CHART PEMBIMBING (URUTKAN ALFABET A-Z)
        // ==========================================================
        $queryBar = $db->table('dosen')
            ->select('dosen.inisial, dosen.nama, COUNT(pembimbing.id) as jumlah_bimbingan')
            ->join('pembimbing', 'pembimbing.id_dosen = dosen.id')
            ->join('skripsi', 'skripsi.id = pembimbing.id_skripsi')
            ->where('dosen.id_prodi', $id_prodi)
            ->where('skripsi.kode_semester', $selected_semester)
            ->groupBy('dosen.id')
            ->orderBy('dosen.inisial', 'ASC') // <--- UBAH INI (Biar urut A-Z)
            ->get()->getResultArray();

        $label_pembimbing = [];
        $value_pembimbing = [];
        foreach ($queryBar as $row) {
            $label_pembimbing[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $value_pembimbing[] = $row['jumlah_bimbingan'];
        }


        // ==========================================================
        // 5. QUERY SISANYA (URUTKAN ALFABET MANUAL DI PHP)
        // ==========================================================
        
        // Fungsi pembantu buat ngurutin Array berdasarkan Inisial/Nama
        $sort_by_name = function($a, $b) {
            $nameA = !empty($a['inisial']) ? $a['inisial'] : $a['nama'];
            $nameB = !empty($b['inisial']) ? $b['inisial'] : $b['nama'];
            return strcasecmp($nameA, $nameB); // A-Z sorting
        };

        // --- A. Reviewer Sempro ---
        $data_rev_sempro = $this->dashboardModel->getBebanReviewerSempro($selected_semester, $id_prodi);
        usort($data_rev_sempro, $sort_by_name); // <--- Urutkan A-Z
        
        $lbl_rev_sempro = []; $val_rev_sempro = [];
        foreach ($data_rev_sempro as $row) {
            $lbl_rev_sempro[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $val_rev_sempro[] = $row['jumlah_uji'];
        }

        // --- B. Reviewer Pra-Sidang ---
        $data_rev_pra = $this->dashboardModel->getBebanReviewerPrasidang($selected_semester, $id_prodi);
        usort($data_rev_pra, $sort_by_name); // <--- Urutkan A-Z

        $lbl_rev_pra = []; $val_rev_pra = [];
        foreach ($data_rev_pra as $row) {
            $lbl_rev_pra[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $val_rev_pra[] = $row['jumlah_uji'];
        }

        // --- C. Penguji Sidang ---
        $data_sidang_akhir = $this->dashboardModel->getBebanPengujiSidang($selected_semester, $id_prodi);
        usort($data_sidang_akhir, $sort_by_name); // <--- Urutkan A-Z

        $lbl_sidang_akhir = []; $val_sidang_akhir = [];
        foreach ($data_sidang_akhir as $row) {
            $lbl_sidang_akhir[] = !empty($row['inisial']) ? $row['inisial'] : $row['nama'];
            $val_sidang_akhir[] = $row['jumlah_uji'];
        }


        // ==========================================================
        // 6. PACKING DATA
        // ==========================================================
        $data = [
            'title'             => 'Dasbor Prodi ' . $nama_prodi,
            'list_semester'     => $list_semester,      
            'selected_semester' => $selected_semester,  

            'pie_sempro'    => $jumlah_sempro,
            'pie_prasidang' => $jumlah_prasidang,
            'pie_sidang'    => $jumlah_sidang,

            'lbl_pembimbing' => json_encode($label_pembimbing), 
            'val_pembimbing' => json_encode($value_pembimbing), 

            'lbl_sempro'     => json_encode($lbl_rev_sempro), 
            'val_sempro'     => json_encode($val_rev_sempro),

            'lbl_pra'        => json_encode($lbl_rev_pra),    
            'val_pra'        => json_encode($val_rev_pra),

            'lbl_sidang'     => json_encode($lbl_sidang_akhir),
            'val_sidang'     => json_encode($val_sidang_akhir),
        ];

        return view('kaprodi/dashboard', $data);
    }



    public function proposal()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $proposal = [];
        if ($currentSemester != null)
            $proposal = $this->proposalModel->getProposalMahasiswaByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $bidang = $this->bidangModel->findAll();

        $data = [

            "title" => "Proposal Mahasiswa " . $prodi['inisial'],

            "proposal" => $proposal,

            "bidang" => $bidang,

            "dosen" => $dosen,

            "semesterList" => $allSemester,

        ];

        return view("kaprodi/proposal", $data);
    }



    public function skripsi()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $skripsi = [];
        if ($currentSemester != null)
            $skripsi = $this->skripsiModel->getSkripsiMahasiswaByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            "title" => "Skripsi Mahasiswa " . $prodi['inisial'],

            "skripsi" => $skripsi,

            "semesterList" => $allSemester,

        ];

        return view("kaprodi/skripsi", $data);
    }

    public function ubahStatusSkripsi()
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $id = $this->request->getPost("id", FILTER_SANITIZE_SPECIAL_CHARS);

        $skripsi = $this->skripsiModel->find($id);
        if ($skripsi == null) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Status Skripsi Gagal", "text" => "Data Skripsi tidak ditemukan"]);
            return redirect()->to(base_url("Kaprodi/skripsi"));
        }

        $status = $this->request->getPost("status", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($status != STATUS_SKRIPSI_DALAMPENGERJAAN && $status != STATUS_SKRIPSI_TIDAKLULUS && $status != STATUS_SKRIPSI_LULUS) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Status Skripsi Gagal", "text" => "Status tidak sesuai"]);
            return redirect()->to(base_url("Kaprodi/skripsi"));
        }

        $this->skripsiModel->update($id, [
            'status' => $status,
            'tanggal_selesai_skripsi' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Status Skripsi Berhasil", "text" => "Status Skripsi milik " . $skripsi['npm'] . " berhasil diubah menjadi $status"]);
        return redirect()->back();
    }



    public function makalah()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //$dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $makalah = [];
        if ($currentSemester != null)
            $makalah = $this->makalahModel->getAllMakalahByIdProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            "title" => "Makalah Mahasiswa " . $prodi['inisial'],

            "makalah" => $makalah,

            "semesterList" => $allSemester,

        ];

        return view("kaprodi/makalah", $data);
    }





    public function rekapDosenPembimbing()
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $pembimbing = [];
        if ($currentSemester != null)
            $pembimbing = $this->dosenModel->getDosenWithCountMahasiswaBimbinganByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [
            "title" => "Rekap Dosen Pembimbing " . $prodi['inisial'],
            "pembimbing" => $pembimbing,
            "semesterList" => $allSemester,
        ];

        return view("kaprodi/rekap_dosen_pembimbing", $data);
    }



    public function detailRekapDospem($id_dosen, $kode_semester)
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
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

        return view("kaprodi/detail_rekap_dospem", $data);
    }



    public function penilaianSidang()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $mahasiswa = [];
        if ($currentSemester != null)
            $mahasiswa = $this->sidangSkripsiModel->getAllSidangSkripsiByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Penilaian Sidang Mahasiswa',

            'mahasiswa' => $mahasiswa,

            'semesterList' => $allSemester,

        ];

        return view("kaprodi/penilaian_sidang", $data);
    }



    public function hasilSidangSkripsi($idSkripsi)
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $lastSkripsi = $this->skripsiModel->find($idSkripsi);
        if ($lastSkripsi == null || $idSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mahasiswa = $this->mahasiswaModel->find($lastSkripsi['npm']);

        $sidangSkripsi = $this->sidangSkripsiModel->where(['id_skripsi' => $idSkripsi])->find();
        if ($sidangSkripsi == null || $idSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $tahunAkademik = explode("-", $lastSkripsi['kode_semester'])[0];
        $semester = explode("-", $lastSkripsi['kode_semester'])[1];

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->hasilSidangSkripsiOld($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }

        if ($lastSkripsi['jenis'] == JENIS_SKRIPSI_REGULER) {
            return $this->hasilSidangSkripsiNonProyek($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }

        if (in_array($lastSkripsi['jenis'], [JENIS_SKRIPSI_PROYEK_MANDIRI, JENIS_SKRIPSI_PROYEK_TIM])) {
            return $this->hasilSidangSkripsiProyek($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }
    }


    private function hasilSidangSkripsiOld($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $idSidangSkripsi = $sidangSkripsi[0]['id'];

        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi[0]['dosen_penguji']);

        $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();

        $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();

        $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();

        $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi[0]['dosen_penguji']])->getResultArray();

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
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
        return view("kaprodi/hasil_sidang_skripsi", $data);
    }

    private function hasilSidangSkripsiNonProyek($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $sidangSkripsi[0]['dosen_penguji'] == null ? null : $this->dosenModel->find($sidangSkripsi[0]['dosen_penguji']);

        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($sidangSkripsi[0]['dosen_penguji'], 'Dosen Penguji', $sidangSkripsi[0]['id']);
        $nilaiPembimbing1 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu1[0]['id_dosen'], 'Pembimbing Ilmu 1', $sidangSkripsi[0]["id"]);
        $nilaiPembimbing2 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu2[0]['id_dosen'], 'Pembimbing Ilmu 2', $sidangSkripsi[0]["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingAgama[0]['id_dosen'], 'Pembimbing Agama', $sidangSkripsi[0]["id"]);

        $rubrikNilai = $this->penilaianSidangParamModel->getRubrikPdsi2023();

        $rataRataNilai = $this->penilaianSidangNewModel->getAverageByIdSidang(
            $lastSkripsi['id'],
            $sidangSkripsi[0]['id'],
            $sidangSkripsi[0]['dosen_penguji'],
            $pembimbingIlmu1[0]['id_dosen'],
            $pembimbingIlmu2[0]['id_dosen'],
            $pembimbingAgama[0]['id_dosen']
        );

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
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
            'rubrikNilai' => $rubrikNilai,
            'avgNilai' => $rataRataNilai,
        ];
        return view("kaprodi/hasil_sidang_skripsi_non_proyek", $data);
    }

    private function hasilSidangSkripsiProyek($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $pembimbingIlmu = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $klien = $sidangSkripsi[0]['klien'] == null ? null : $this->klienModel->find($sidangSkripsi[0]['klien']);
        $penguji = $sidangSkripsi[0]['dosen_penguji'] == null ? null : $this->dosenModel->find($sidangSkripsi[0]['dosen_penguji']);

        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($sidangSkripsi[0]['dosen_penguji'], 'Penguji', $sidangSkripsi[0]["id"]);
        $nilaiPembimbing = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu[0]['id_dosen'], 'Pembimbing Ilmu', $sidangSkripsi[0]["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingAgama[0]['id_dosen'], 'Pembimbing Agama', $sidangSkripsi[0]["id"]);
        $nilaiKlien = $this->penilaianSidangNewModel->getPenilaianSidang($sidangSkripsi[0]['klien'], 'Klien', $sidangSkripsi[0]["id"]);

        $rubrikPenguji = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Penguji');
        $rubrikPembimbing = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Pembimbing Ilmu');
        $rubrikPembimbingAgama = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Pembimbing Agama');
        $rubrikKlien = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan('Klien');

        $totalBobot = $this->penilaianSidangParamModel->getAllSumBobot();
        $totalNilai = $this->penilaianSidangNewModel->getTotalProyekByIdSidang($lastSkripsi['id'], $sidangSkripsi[0]["id"], $lastSkripsi['jenis']);

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
            'lastSkripsi' => $lastSkripsi,
            'sidangSkripsi' => $sidangSkripsi,
            'pembimbingIlmu' => $pembimbingIlmu,
            'pembimbingAgama' => $pembimbingAgama,
            'klien' => $klien,
            'penguji' => $penguji,
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
        return view("kaprodi/hasil_sidang_skripsi_proyek", $data);
    }


    public function cetakBeritaAcara($id_skripsi)
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $lastSkripsi = $this->skripsiModel->find($id_skripsi);
        if ($lastSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mahasiswa = $this->mahasiswaModel->find($lastSkripsi['npm']);

        $sidangSkripsi = $this->sidangSkripsiModel->getWhere(['id_skripsi' => $id_skripsi])->getResultArray();
        if (count($sidangSkripsi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $sidangSkripsi = $this->sidangSkripsiModel->getWhere(['id_skripsi' => $id_skripsi])->getResultArray()[0];

        $tahunAkademik = explode("-", $lastSkripsi['kode_semester'])[0];
        $semester = explode("-", $lastSkripsi['kode_semester'])[1];

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->cetakBeritaAcaraOld($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }

        if ($lastSkripsi['jenis'] == JENIS_SKRIPSI_REGULER) {
            return $this->cetakBeritaAcaraNonProyek($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }

        if (in_array($lastSkripsi['jenis'], [JENIS_SKRIPSI_PROYEK_MANDIRI, JENIS_SKRIPSI_PROYEK_TIM])) {
            return $this->cetakBeritaAcaraProyek($lastSkripsi, $mahasiswa, $sidangSkripsi);
        }
    }

    private function cetakBeritaAcaraOld($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $idSidangSkripsi = $sidangSkripsi['id'];

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);

        $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();

        $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();

        $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();

        $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi['dosen_penguji']])->getResultArray();


        $arrBeritaAcara = [];

        array_push($arrBeritaAcara, [
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
        ]);

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'arrBeritaAcara' => $arrBeritaAcara,
        ];

        return view("kaprodi/berita_acara", $data);
    }

    private function cetakBeritaAcaraNonProyek($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $sidangSkripsi['dosen_penguji'] == null ? null : $this->dosenModel->find($sidangSkripsi['dosen_penguji']);

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($sidangSkripsi['dosen_penguji'], 'Dosen Penguji', $sidangSkripsi['id']);
        $nilaiPembimbing1 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu1[0]['id_dosen'], 'Pembimbing Ilmu 1', $sidangSkripsi["id"]);
        $nilaiPembimbing2 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu2[0]['id_dosen'], 'Pembimbing Ilmu 2', $sidangSkripsi["id"]);
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingAgama[0]['id_dosen'], 'Pembimbing Agama', $sidangSkripsi["id"]);

        $rubrikNilai = $this->penilaianSidangParamModel->getRubrikPdsi2023();

        $rataRataNilai = $this->penilaianSidangNewModel->getAverageByIdSidang(
            $lastSkripsi['id'],
            $sidangSkripsi['id'],
            $sidangSkripsi['dosen_penguji'],
            $pembimbingIlmu1[0]['id_dosen'],
            $pembimbingIlmu2[0]['id_dosen'],
            $pembimbingAgama[0]['id_dosen']
        );

        $arrBeritaAcara = [];

        array_push($arrBeritaAcara, [
            'title' => 'Hasil Sidang Skripsi',
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
            'lastSkripsi' => $lastSkripsi,
            'sidangSkripsi' => $sidangSkripsi,
            'pembimbingIlmu1' => $pembimbingIlmu1[0],
            'pembimbingIlmu2' => $pembimbingIlmu2[0]['id_dosen'] == null ? null : $pembimbingIlmu2[0],
            'pembimbingAgama' => $pembimbingAgama[0],
            'penguji' => $penguji,
            'nilaiPembimbing1' => $nilaiPembimbing1,
            'nilaiPembimbing2' => $nilaiPembimbing2,
            'nilaiPembimbingAgama' => $nilaiPembimbingAgama,
            'nilaiPenguji' => $nilaiPenguji,
            'rubrikNilai' => $rubrikNilai,
            'avgNilai' => $rataRataNilai,
        ]);

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'arrBeritaAcara' => $arrBeritaAcara,
        ];

        return view("kaprodi/berita_acara_new", $data);
    }

    private function cetakBeritaAcaraProyek($lastSkripsi, $mahasiswa, $sidangSkripsi)
    {
        $pembimbingIlmu = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);
        $klien = $this->klienModel->find($sidangSkripsi['klien']);

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

        $idSidangSkripsi = $sidangSkripsi['id'];

        $rubrikPenguji = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PENGUJI");
        $rubrikKlien = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("KLIEN");
        $rubrikPembimbingIlmu = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PEMBIMBING ILMU");
        $rubrikPembimbingAgama = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PEMBIMBING AGAMA");

        $nilaiPenguji = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $penguji['id'], "PENGUJI");
        $nilaiKlien = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $klien['id'], "KLIEN");
        $nilaiPembimbingIlmu = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $pembimbingIlmu[0]['id_dosen'], "PEMBIMBING ILMU");
        $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $pembimbingAgama[0]['id_dosen'], "PEMBIMBING AGAMA");

        $nilaiAkhir = $this->penilaianSidangNewModel->getTotalProyekByIdSidang($lastSkripsi['id'], $idSidangSkripsi, $lastSkripsi['jenis']);

        $arrBeritaAcara = [];

        array_push($arrBeritaAcara, [
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
            'lastSkripsi' => $lastSkripsi,
            'sidangSkripsi' => $sidangSkripsi,
            'pembimbingIlmu' => $pembimbingIlmu,
            'pembimbingAgama' => $pembimbingAgama,
            'klien' => $klien,
            'penguji' => $penguji,
            'nilaiPembimbingIlmu' => $nilaiPembimbingIlmu,
            'nilaiPembimbingAgama' => $nilaiPembimbingAgama,
            'nilaiPenguji' => $nilaiPenguji,
            'nilaiKlien' => $nilaiKlien,
            'rubrikPenguji' => $rubrikPenguji,
            'rubrikKlien' => $rubrikKlien,
            'rubrikPembimbingIlmu' => $rubrikPembimbingIlmu,
            'rubrikPembimbingAgama' => $rubrikPembimbingAgama,
            'nilaiAkhir' => $nilaiAkhir,
        ]);

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'arrBeritaAcara' => $arrBeritaAcara,
        ];

        return view("kaprodi/berita_acara_new", $data);
    }

    public function cetakBeritaAcaraBulk()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $semester = $this->semesterModel->where(['status' => 'AKTIF'])->first();
        $kodeSemester = $semester['kode'];

        $tahunAkademik = explode("-", $kodeSemester)[0];
        $semester = explode("-", $kodeSemester)[1];

        $listSkripsi = $this->skripsiModel->getWhere(['status' => 'LULUS', 'kode_semester' => $kodeSemester])->getResultArray();

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->printBeritaAcaraBulkOld($listSkripsi);
        } else {
            return $this->printBeritaAcaraBulkNew($listSkripsi);
        }
    }

    private function printBeritaAcaraBulkOld($listSkripsi)
    {
        $arrBeritaAcara = [];

        foreach ($listSkripsi as $skripsi) {

            $mahasiswa = $this->mahasiswaModel->find($skripsi['npm']);
            $listSidangSkripsi = $this->sidangSkripsiModel->getWhere(["id_skripsi" => $skripsi['id']])->getResultArray();

            if ($mahasiswa['id_prodi'] != session()->get("user_session")['id']) {
                continue;
            }

            if ($listSidangSkripsi == null || count($listSidangSkripsi) == 0) {
                continue;
            }

            $sidangSkripsi = $listSidangSkripsi[0];
            $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);


            $idSidangSkripsi = $sidangSkripsi['id'];

            $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($skripsi['id']);

            $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($skripsi['id']);

            $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($skripsi['id']);

            $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);



            $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();

            $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();

            $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();

            $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi['dosen_penguji']])->getResultArray();



            array_push($arrBeritaAcara, [
                'mahasiswa' => $mahasiswa,

                'prodi' => $prodi,

                'lastSkripsi' => $skripsi,

                'sidangSkripsi' => $sidangSkripsi,

                'pembimbingIlmu1' => $pembimbingIlmu1,

                'pembimbingIlmu2' => $pembimbingIlmu2,

                'pembimbingAgama' => $pembimbingAgama,

                'penguji' => $penguji,

                'nilaiPembimbing1' => $nilaiPembimbing1,

                'nilaiPembimbing2' => $nilaiPembimbing2,

                'nilaiPembimbingAgama' => $nilaiPembimbingAgama,

                'nilaiPenguji' => $nilaiPenguji
            ]);
        }



        $data = [

            'title' => 'Hasil Sidang Skripsi',

            'arrBeritaAcara' => $arrBeritaAcara,

        ];



        return view("kaprodi/berita_acara", $data);
    }


    private function printBeritaAcaraBulkNew($listSkripsi)
    {
        $arrBeritaAcara = [];

        foreach ($listSkripsi as $skripsi) {
            $mahasiswa = $this->mahasiswaModel->find($skripsi['npm']);
            $listSidangSkripsi = $this->sidangSkripsiModel->getWhere(["id_skripsi" => $skripsi['id']])->getResultArray();

            if ($mahasiswa['id_prodi'] != session()->get("user_session")['id']) {
                continue;
            }

            if ($listSidangSkripsi == null || count($listSidangSkripsi) == 0) {
                continue;
            }

            $sidangSkripsi = $listSidangSkripsi[0];
            $idSidangSkripsi = $sidangSkripsi['id'];

            $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);

            if ($skripsi['jenis'] == JENIS_SKRIPSI_PROYEK_MANDIRI || $skripsi['jenis'] == JENIS_SKRIPSI_PROYEK_TIM) {
                $pembimbingIlmu = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($skripsi['id']);
                $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($skripsi['id']);
                $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);
                $klien = $this->klienModel->find($sidangSkripsi['klien']);

                $rubrikPenguji = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PENGUJI");
                $rubrikKlien = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("KLIEN");
                $rubrikPembimbingIlmu = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PEMBIMBING ILMU");
                $rubrikPembimbingAgama = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan("PEMBIMBING AGAMA");

                $nilaiPenguji = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $penguji['id'], "PENGUJI");
                $nilaiKlien = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $klien['id'], "KLIEN");
                $nilaiPembimbingIlmu = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $pembimbingIlmu[0]['id_dosen'], "PEMBIMBING ILMU");
                $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $pembimbingAgama[0]['id_dosen'], "PEMBIMBING AGAMA");

                $nilaiAkhir = $this->penilaianSidangNewModel->getTotalProyekByIdSidang($skripsi['id'], $idSidangSkripsi, $skripsi['jenis']);

                array_push($arrBeritaAcara, [
                    'mahasiswa' => $mahasiswa,
                    'prodi' => $prodi,
                    'lastSkripsi' => $skripsi,
                    'sidangSkripsi' => $sidangSkripsi,
                    'pembimbingIlmu' => $pembimbingIlmu,
                    'pembimbingAgama' => $pembimbingAgama,
                    'klien' => $klien,
                    'penguji' => $penguji,
                    'nilaiPembimbingIlmu' => $nilaiPembimbingIlmu,
                    'nilaiPembimbingAgama' => $nilaiPembimbingAgama,
                    'nilaiPenguji' => $nilaiPenguji,
                    'nilaiKlien' => $nilaiKlien,
                    'rubrikPenguji' => $rubrikPenguji,
                    'rubrikKlien' => $rubrikKlien,
                    'rubrikPembimbingIlmu' => $rubrikPembimbingIlmu,
                    'rubrikPembimbingAgama' => $rubrikPembimbingAgama,
                    'nilaiAkhir' => $nilaiAkhir,
                ]);
            } else if ($skripsi['jenis'] == JENIS_SKRIPSI_REGULER) {
                $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($skripsi['id']);
                $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($skripsi['id']);
                $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($skripsi['id']);
                $penguji = $sidangSkripsi['dosen_penguji'] == null ? null : $this->dosenModel->find($sidangSkripsi['dosen_penguji']);

                $nilaiPenguji = $this->penilaianSidangNewModel->getPenilaianSidang($sidangSkripsi['dosen_penguji'], 'Dosen Penguji', $sidangSkripsi['id']);
                $nilaiPembimbing1 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu1[0]['id_dosen'], 'Pembimbing Ilmu 1', $sidangSkripsi["id"]);
                $nilaiPembimbing2 = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingIlmu2[0]['id_dosen'], 'Pembimbing Ilmu 2', $sidangSkripsi["id"]);
                $nilaiPembimbingAgama = $this->penilaianSidangNewModel->getPenilaianSidang($pembimbingAgama[0]['id_dosen'], 'Pembimbing Agama', $sidangSkripsi["id"]);

                $rubrikNilai = $this->penilaianSidangParamModel->getRubrikPdsi2023();

                $rataRataNilai = $this->penilaianSidangNewModel->getAverageByIdSidang(
                    $skripsi['id'],
                    $sidangSkripsi['id'],
                    $sidangSkripsi['dosen_penguji'],
                    $pembimbingIlmu1[0]['id_dosen'],
                    $pembimbingIlmu2[0]['id_dosen'],
                    $pembimbingAgama[0]['id_dosen']
                );

                array_push($arrBeritaAcara, [
                    'title' => 'Hasil Sidang Skripsi',
                    'mahasiswa' => $mahasiswa,
                    'prodi' => $prodi,
                    'lastSkripsi' => $skripsi,
                    'sidangSkripsi' => $sidangSkripsi,
                    'pembimbingIlmu1' => $pembimbingIlmu1[0],
                    'pembimbingIlmu2' => $pembimbingIlmu2[0]['id_dosen'] == null ? null : $pembimbingIlmu2[0],
                    'pembimbingAgama' => $pembimbingAgama[0],
                    'penguji' => $penguji,
                    'nilaiPembimbing1' => $nilaiPembimbing1,
                    'nilaiPembimbing2' => $nilaiPembimbing2,
                    'nilaiPembimbingAgama' => $nilaiPembimbingAgama,
                    'nilaiPenguji' => $nilaiPenguji,
                    'rubrikNilai' => $rubrikNilai,
                    'avgNilai' => $rataRataNilai,
                ]);
            }
        }

        $data = [
            'title' => 'Hasil Sidang Skripsi',
            'arrBeritaAcara' => $arrBeritaAcara,
        ];

        return view("tenagaKependidikan/berita_acara_new", $data);
    }













    public function seminarProposal()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $seminarProposal = [];
        if ($currentSemester != null)
            $seminarProposal = $this->semproModel->getSemproByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $mahasiswa = $this->mahasiswaModel->getMahasiswaBelumDapatSemproByProdi($prodi['id']);

        $dosen = $this->dosenModel->getDosenByProdi($prodi['id']);

        $modeSempro = $this->modeSemproModel->getModeSempro($currentSemester['kode'], $prodi['id']);

        $data = [

            "title" => "Seminar Proposal Mahasiswa " . $prodi['inisial'],

            "prodi" => $prodi,

            "modeSempro" => $modeSempro,

            "seminarProposal" => $seminarProposal,

            "mahasiswa" => $mahasiswa,

            "dosen" => $dosen,

            "semesterList" => $allSemester,

            "currentSemester" => $currentSemester,
        ];



        return view("kaprodi/seminar_proposal", $data);
    }



    public function updateModeSempro($idProdi, $kodeSemester)
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $mode_sempro = $this->request->getPost("mode_sempro", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->modeSemproModel->getModeSempro($kodeSemester, $idProdi) == null) {
            $this->modeSemproModel->insert([
                "mode" => $mode_sempro,
                "kode_semester" => $kodeSemester,
                "id_prodi" => $idProdi
            ]);
        } else {
            $this->modeSemproModel->updateModeSempro($mode_sempro, $kodeSemester, $idProdi);
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Atur Mode Seminar Proposal Berhasil", "text" => "Seminar Proposal diatur menjadi " . $mode_sempro]);
        return redirect()->to(base_url("Kaprodi/seminarProposal"));
    }



    public function insertJadwalSempro()
{
    // autentikasi
    if (!$this->authenticate(["kaprodi"])) {
        return redirect()->to(base_url("unauthorized.php"));
    }

    // buat mencegah access langsung dari link
    if ($this->request->getMethod() != 'post') {
        return redirect()->back();
    }

    $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);
    $proposal = $this->proposalModel->getMahasiswaLastProposal($npm);

    $tanggal_seminar = date_format(date_create($this->request->getPost("tanggal")), 'Y-m-d');
    $jam_seminar = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);

    $mode_sempro = $this->request->getPost("mode_sempro", FILTER_SANITIZE_SPECIAL_CHARS);

    // 1. Proses Simpan Data
    if ($mode_sempro == 'Sinkronus Daring') {
        $this->semproModel->save([
            'id_proposal' => $proposal['id'],
            'tanggal' => $tanggal_seminar . " " . $jam_seminar,
            'link_konferensi' => $this->request->getPost("link_konferensi", FILTER_SANITIZE_URL),
            'dosen_penguji1' => $this->request->getPost("dosen_penguji1"),
            'dosen_penguji2' => $this->request->getPost("dosen_penguji2"),
        ]);
    } elseif ($mode_sempro == 'Sinkronus Luring') {
        $this->semproModel->save([
            'id_proposal' => $proposal['id'],
            'tanggal' => $tanggal_seminar . " " . $jam_seminar,
            'ruangan' => $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS),
            'dosen_penguji1' => $this->request->getPost("dosen_penguji1"),
            'dosen_penguji2' => $this->request->getPost("dosen_penguji2"),
        ]);
    }

    // 2. AMBIL ID YANG BARU SAJA DISIMPAN
    $idBaru = $this->semproModel->getInsertID();

    // 3. PANGGIL FUNGSI KIRIM EMAIL (PENTING!)
    // Ini yang tadi hilang di kode Anda
    $this->kirimEmailReviewer($idBaru);

    session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Seminar Proposal Berhasil dibuat", "text" => "Jadwal seminar telah dibuat & Email terkirim ke Reviewer"]);

    return redirect()->to(base_url("Kaprodi/seminarProposal"));
}


    public function updateJadwalSempro($idSempro)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);

        $proposal = $this->proposalModel->getMahasiswaLastProposal($npm);



        $tanggal_seminar = date_format(date_create($this->request->getPost("tanggal")), 'Y-m-d');

        $jam_seminar = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);



        $mode_sempro = $this->request->getPost("mode_sempro", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($mode_sempro == 'Sinkronus Daring') {

            $this->semproModel->update($idSempro, [

                'tanggal' => $tanggal_seminar . " " . $jam_seminar,

                'link_konferensi' => $this->request->getPost("link_konferensi", FILTER_SANITIZE_URL),

                'dosen_penguji1' => $this->request->getPost("dosen_penguji1"),

                'dosen_penguji2' => $this->request->getPost("dosen_penguji2"),

            ]);
            $this->kirimEmailReviewer($idSempro);
        } elseif ($mode_sempro == 'Sinkronus Luring') {

            $this->semproModel->update($idSempro, [

                'tanggal' => $tanggal_seminar . " " . $jam_seminar,

                'ruangan' => $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS),

                'dosen_penguji1' => $this->request->getPost("dosen_penguji1"),

                'dosen_penguji2' => $this->request->getPost("dosen_penguji2"),

            ]);
           $this->kirimEmailReviewer($idSempro);

        }



        session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Seminar Proposal Berhasil diperbarui", "text" => "Jadwal seminar telah diperbarui"]);

        return redirect()->to(base_url("Kaprodi/seminarProposal"));
    }



    public function deleteJadwalSempro($idSempro)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->semproModel->delete($idSempro);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Jadwal SemPro Berhasil", "text" => "Jadwal Seminar Proposal Berhasil dihapus"]);

        return redirect()->to(base_url("Kaprodi/seminarProposal"));
    }



    public function insertJadwalSemproBatch()
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }


        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }


        //$dataAkun = $this->dosenModel->find(session()->get('user_session')['id']);
        $dataAkun = $this->prodiModel->find(session()->get("user_session")['id']);
        $validationRules = [
            'fileJadwal' => [
                'rules' => 'uploaded[fileJadwal]|mime_in[fileJadwal,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|ext_in[fileJadwal,xls,xlsx]|max_size[fileJadwal,10000]',
                'errors' => [
                    'uploaded' => 'Pilih File Jadwal Seminar Proposal terlebih dahulu',
                    'mime_in' => 'File Jadwal Seminar Proposal harus berupa Excel',
                    'ext_in' => 'File Jadwal Seminar Proposal harus berekstensi .xls atau .xlsx',
                    'max_size' => 'Ukuran File Jadwal Seminar Proposal tidak boleh lebih dari 10MB'
                ]
            ]
        ];


        if (!$this->validate($validationRules)) {
            $validation = \Config\Services::validation();
            session()->setFlashdata("message", ["icon" => "error", "title" => "Upload File Jadwal Seminar Proposal Gagal", "text" => $validation->getError("fileJadwal")]);
            return redirect()->to(base_url("Kaprodi/seminarProposal"))->withInput();
        }


        $file = $this->request->getFile("fileJadwal");
        $reader = '';
        if ($file->getExtension() == 'xls') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        } else if ($file->getExtension() == 'xlsx') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        }


        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file->getTempName());


        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();


        $mode_sempro = $this->request->getPost("mode_sempro", FILTER_SANITIZE_SPECIAL_CHARS);

        $isAllSuccess = true;
        $htmlErrorMessage = '<table class="table table-bordered">';
        $htmlErrorMessage .= "<thead><tr><td>NPM</td><td>Pesan Error</td></tr></thead>";
        $htmlErrorMessage .= "<tbody>";
        for ($row = 2; $row <= $highestRow; ++$row) {
            $strNPM = strval($worksheet->getCell("A$row")->getValue());

            $mahasiswa = $this->mahasiswaModel->find($strNPM);
            if ($mahasiswa == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak terdaftar</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $lastProposal = $this->proposalModel->getMahasiswaLastProposal($mahasiswa['npm']);
            if ($lastProposal == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki proposal</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($lastProposal['status'] != 'TERTUNDA') {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki proposal dengan status 'TERTUNDA'</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $id_sempro = null;

            $id_proposal = $lastProposal['id'];

            $jadwalSemproByIdProposal = $this->semproModel->getWhere(['id_proposal' => $id_proposal])->getResultArray();

            $isJadwalExist = count($jadwalSemproByIdProposal) != 0;
            if ($isJadwalExist) {
                $id_sempro = $jadwalSemproByIdProposal[0]['id'];
            }


            $kolomTanggalSeminar = $worksheet->getCell("B$row")->getValue();
            if ($kolomTanggalSeminar == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Tanggal Seminar' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4} ([0-1][0-9]|2[0-3]):([0-5][0-9])$/", $kolomTanggalSeminar)) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Format 'Tanggal Seminar' tidak sesuai</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $tanggal = date_format(date_create_from_format('d-m-Y H:i', $kolomTanggalSeminar), 'Y-m-d H:i');

            if ($worksheet->getCell("C$row")->getValue() == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Ruangan'/'Link Konferensi' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $ruangan = "";
            $link_konferensi = "";
            if ($mode_sempro == 'Sinkronus Daring') {
                $link_konferensi = $worksheet->getCell("C$row")->getValue();
            } elseif ($mode_sempro == 'Sinkronus Luring') {
                $ruangan = $worksheet->getCell("C$row")->getValue();
            }


            $penguji1 = $this->dosenModel->getDosenByInisial($worksheet->getCell("D$row")->getValue());
            $penguji2 = $this->dosenModel->getDosenByInisial($worksheet->getCell("E$row")->getValue());

            if ($penguji1 == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Penguji 1 untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($penguji2 == null && $worksheet->getCell("E$row")->getValue() != "-") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Penguji 2 untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($penguji1['id_prodi'] != $dataAkun['id']) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Penguji 1 berasal dari prodi yang berbeda dengan Mahasiswa</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($penguji2 != null && $penguji2['id_prodi'] != $dataAkun['id']) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Penguji 2 berasal dari prodi yang berbeda dengan Mahasiswa</td></tr>";
                $isAllSuccess = false;
                continue;
            }


            $dosen_penguji1 = $penguji1['id'];
            $dosen_penguji2 = null;
            if ($penguji2 != null) {
                $dosen_penguji2 = $penguji2['id'];
            }


            $jadwal = [
                'id_proposal' => $id_proposal,
                'tanggal' => $tanggal,
                'dosen_penguji1' => $dosen_penguji1,
                'dosen_penguji2' => $dosen_penguji2,
            ];


            if ($mode_sempro == 'Sinkronus Daring') {
                $jadwal['link_konferensi'] =  $link_konferensi;
            } elseif ($mode_sempro == 'Sinkronus Luring') {
                $jadwal['ruangan'] = $ruangan;
            }


            if ($id_sempro == null) {
                $this->semproModel->insert($jadwal);
            } else {
                $this->semproModel->update($id_sempro, $jadwal);
            }
        }

        $htmlErrorMessage .= "</tbody>";
        $htmlErrorMessage .= "</table>";

        $htmlErrorMessage = str_replace("<", "&lt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace(">", "&gt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace("/", "&#47;", $htmlErrorMessage);

        if ($isAllSuccess) {
            session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Seminar Proposal Berhasil dibuat", "text" => "Jadwal Seminar Proposal berhasil dibuat sepenuhnya"]);
        } else {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Jadwal Seminar Proposal Gagal dibuat", "html" => $htmlErrorMessage]);
        }

        return redirect()->to(base_url("Kaprodi/seminarProposal"));
    }



    public function downloadFormatJadwalSempro($mode_sempro)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        redirect()->to(base_url("Kaprodi/seminarProposal"));

        if ($mode_sempro == 'Sinkronus Daring') {

            return $this->response->download("folderResource/Format_Jadwal_SemPro_Sinkronus_Daring.xlsx", null);
        } else if ($mode_sempro == 'Sinkronus Luring') {

            return $this->response->download("folderResource/Format_Jadwal_SemPro_Sinkronus_Luring.xlsx", null);
        }
    }















    public function seminarPrasidang()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //$dataAkun = $this->dosenModel->find(session()->get('user_session')['id']);

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $mahasiswa = $this->mahasiswaModel->getMahasiswaBelumDapatSempraByProdi($prodi['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $seminarPrasidang = [];
        if ($currentSemester != null)
            $seminarPrasidang = $this->seminarPrasidangModel->getPrasidangByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $data = [

            "title" => "Seminar Prasidang Mahasiswa " . $prodi['inisial'],

            "prodi" => $prodi,

            "seminarPrasidang" => $seminarPrasidang,

            "mahasiswa" => $mahasiswa,

            "dosen" => $dosen,

            "semesterList" => $allSemester,

            "currentSemester" => $currentSemester,

        ];



        return view("kaprodi/seminar_prasidang", $data);
    }



  public function insertJadwalSeminarPrasidang()
{
    if (!$this->authenticate(["kaprodi"])) return redirect()->to(base_url("unauthorized.php"));
    if ($this->request->getMethod() != 'post') return redirect()->back();

    // Ambil data dasar
    $prodi = $this->prodiModel->find(session()->get("user_session")['id']);
    $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);
    $skripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);
    $mahasiswa = $this->mahasiswaModel->where('npm', $npm)->first(); 

    // Konversi tanggal ke String agar tidak error DateTime
    $input_tanggal = $this->request->getPost("tanggal");
    $tanggal_seminar = date("Y-m-d", strtotime((string)$input_tanggal)); 
    
    $jam_seminar = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);
    $ruangan = $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS);
    $link_seminar = $this->request->getPost("link_seminar", FILTER_SANITIZE_URL); 

    $reviewer = $this->dosenModel->find($this->request->getPost("dosen_reviewer"));

    $undangan = [
        'title'         => 'Undangan Review Seminar Prasidang',
        'nama_dosen'    => $reviewer['nama'],
        'nama_prodi'    => $prodi['nama'],
        'inisial_prodi' => $prodi['inisial'],
        'nama_kegiatan' => 'Seminar Prasidang',
        'selaku'        => 'Reviewer Seminar Prasidang',
        'nama_mhs'      => $mahasiswa['nama'], 
        'npm_mhs'       => $npm,
        'judul_skripsi' => $skripsi['judul'], 
        'tanggal'       => $tanggal_seminar,
        'jam'           => $jam_seminar,
        'ruangan'       => $ruangan,
        'link_seminar'  => $link_seminar 
    ];

    // Simpan ke Database DULU (agar data aman jika email lambat)
    $this->seminarPrasidangModel->insert([
        'id_skripsi'     => $skripsi['id'],
        'tanggal'        => $tanggal_seminar . " " . $jam_seminar,
        'ruangan'        => $ruangan,
        'link_seminar'   => $link_seminar,
        'dosen_reviewer' => $this->request->getPost("dosen_reviewer"),
    ]);

    // Baru Kirim Email
    $this->sendEmail(null, $reviewer['email'], "Undangan Prasidang", view("undangan_email", $undangan));

    session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Jadwal dibuat & Email terkirim"]);
    return redirect()->to(base_url("Kaprodi/seminarPrasidang"));
}
    public function updateJadwalSeminarPrasidang($idSeminarPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);

        $skripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);



        $tanggal_seminar = date_format(date_create($this->request->getPost("tanggal")), 'Y-m-d');

        $jam_seminar = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);



        $this->seminarPrasidangModel->update($idSeminarPrasidang, [

            'tanggal' => $tanggal_seminar . " " . $jam_seminar,

            'ruangan' => $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS),

            'dosen_reviewer' => $this->request->getPost("dosen_reviewer")

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Seminar Prasidang Berhasil diperbarui", "text" => "Jadwal seminar prasidang telah diperbarui"]);

        return redirect()->to(base_url("Kaprodi/seminarPrasidang"));
    }



    public function deleteJadwalSeminarPrasidang($idSeminarPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->seminarPrasidangModel->delete($idSeminarPrasidang);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Jadwal Seminar Prasidang Berhasil", "text" => "Jadwal Seminar Prasidang Berhasil dihapus"]);

        return redirect()->to(base_url("Kaprodi/seminarPrasidang"));
    }



    public function downloadFormatJadwalSeminarPrasidang()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        redirect()->to(base_url("Kaprodi/seminarPrasidang"));

        return $this->response->download("folderResource/Format_Jadwal_Seminar_Prasidang.xlsx", null);
    }



    public function insertJadwalSeminarPrasidangBatch()
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }


        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }


        //$dataAkun = $this->dosenModel->find(session()->get('user_session')['id']);
        $dataAkun = $this->prodiModel->find(session()->get("user_session")['id']);
        $validationRules = [
            'fileJadwal' => [
                'rules' => 'uploaded[fileJadwal]|mime_in[fileJadwal,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|ext_in[fileJadwal,xls,xlsx]|max_size[fileJadwal,10000]',
                'errors' => [
                    'uploaded' => 'Pilih File Jadwal Seminar Prasidang terlebih dahulu',
                    'mime_in' => 'File Jadwal Seminar Prasidang harus berupa Excel',
                    'ext_in' => 'File Jadwal Seminar Prasidang harus berekstensi .xls atau .xlsx',
                    'max_size' => 'Ukuran File Jadwal Seminar Prasidang tidak boleh lebih dari 10MB'
                ]
            ]
        ];


        if (!$this->validate($validationRules)) {
            $validation = \Config\Services::validation();
            session()->setFlashdata("message", ["icon" => "error", "title" => "Upload File Jadwal Seminar Prasidang Gagal", "text" => $validation->getError("fileJadwal")]);
            return redirect()->to(base_url("Kaprodi/seminarPrasidang"))->withInput();
        }


        $file = $this->request->getFile("fileJadwal");
        $reader = '';
        if ($file->getExtension() == 'xls') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        } else if ($file->getExtension() == 'xlsx') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        }


        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file->getTempName());


        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();


        $isAllSuccess = true;
        $htmlErrorMessage = '<table class="table table-bordered">';
        $htmlErrorMessage .= "<thead><tr><td>NPM</td><td>Pesan Error</td></tr></thead>";
        $htmlErrorMessage .= "<tbody>";
        for ($row = 2; $row <= $highestRow; ++$row) {
            if ($worksheet->getCell("A$row")->getValue() == null || $worksheet->getCell("A$row")->getValue() == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'NPM' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $strNPM = strval(trim($worksheet->getCell("A$row")->getValue()));

            $mahasiswa = $this->mahasiswaModel->find($strNPM);
            if ($mahasiswa == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak terdaftar</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);
            if ($lastSkripsi == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki skripsi</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($lastSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki skripsi dengan status 'Dalam Pengerjaan'</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $pengajuanPrasidang = $this->pengajuanPrasidangModel->where("id_skripsi", $lastSkripsi['id'])->orderBy("tanggal_pengajuan", "desc")->findAll();
            if (count($pengajuanPrasidang) == 0) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut belum melakukan pengajuan seminar prasidang</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $jadwalSempraByIdSkripsi = $this->seminarPrasidangModel->where('id_skripsi', $lastSkripsi['id'])->orderBy('tanggal', 'desc')->findAll();

            $id_sempra = null;

            if ($lastSkripsi['jumlah_penolakan_sidang'] == 0) {
                if ($pengajuanPrasidang[0]['status'] != 'DISETUJUI') {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Status Pengajuan Seminar Prasidang bukan 'DISETUJUI'</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }

                //belom pernah ketolak sempra tapi jadwalnya pengen diubah tapi statusnya bukan tertunda
                if (count($jadwalSempraByIdSkripsi) != 0 && $jadwalSempraByIdSkripsi[0]['status'] != 'TERTUNDA') {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Jadwal Seminar Prasidang tidak bisa ubah karena statusnya bukan 'TERTUNDA'</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            } else {
                if ($pengajuanPrasidang[0]['status'] != 'DISETUJUI') {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Status Pengajuan Seminar Prasidang bukan 'DISETUJUI'</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }

                //udah pernah ketolak, tapi belum buat pengajuan baru
                if ($jadwalSempraByIdSkripsi[0]['status'] == 'TIDAK LAYAK SIDANG' && $jadwalSempraByIdSkripsi[0]['tanggal'] > $pengajuanPrasidang[0]['tanggal_pengajuan']) {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut belum membuat Pengajuan Seminar Prasidang</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }

                //UDAH BUAT PENGAJUAN TAPI SEMPRANYA MAU DIEDIT TAPI STATUSNYA BUKAN TERTUNDA
                if ($jadwalSempraByIdSkripsi[0]['status'] != 'TERTUNDA' && $jadwalSempraByIdSkripsi[0]['tanggal'] > $pengajuanPrasidang[0]['tanggal_pengajuan']) {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Jadwal Seminar Prasidang tidak bisa ubah karena statusnya bukan 'TERTUNDA'</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            }

            if (count($jadwalSempraByIdSkripsi) != 0 && $jadwalSempraByIdSkripsi[0]['tanggal'] > $pengajuanPrasidang[0]['tanggal_pengajuan']) {
                $id_sempra = $jadwalSempraByIdSkripsi[0]['id'];
            }

            $id_skripsi = $lastSkripsi['id'];

            $kolomTanggalSeminar = $worksheet->getCell("B$row")->getValue();
            if ($kolomTanggalSeminar == null || trim($kolomTanggalSeminar) == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Tanggal Seminar' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4} ([0-1][0-9]|2[0-3]):([0-5][0-9])$/", trim($kolomTanggalSeminar))) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Format 'Tanggal Seminar' tidak sesuai</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $tanggal = date_format(date_create_from_format('d-m-Y H:i', trim($kolomTanggalSeminar)), 'Y-m-d H:i');

            if ($worksheet->getCell("C$row")->getValue() == null || trim($worksheet->getCell("C$row")->getValue()) == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Ruangan' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }
            $ruangan = trim($worksheet->getCell("C$row")->getValue());


            if ($worksheet->getCell("D$row")->getValue() == null || trim($worksheet->getCell("D$row")->getValue()) == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Inisial Dosen Reviewer' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $reviewer = $this->dosenModel->getDosenByInisial(trim($worksheet->getCell("D$row")->getValue()));
            if ($reviewer == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Inisial Reviewer untuk NPM ini tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $jadwal = [
                'id_skripsi' => $id_skripsi,
                'tanggal' => $tanggal,
                'ruangan' => $ruangan,
                'dosen_reviewer' => $reviewer['id'],
            ];

            if ($id_sempra == null) {
                $this->seminarPrasidangModel->insert($jadwal);
            } else {
                $this->seminarPrasidangModel->update($id_sempra, $jadwal);
            }
        }

        $htmlErrorMessage .= "</tbody>";
        $htmlErrorMessage .= "</table>";

        $htmlErrorMessage = str_replace("<", "&lt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace(">", "&gt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace("/", "&#47;", $htmlErrorMessage);

        if ($isAllSuccess) {
            session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Seminar Prasidang Berhasil dibuat", "text" => "Jadwal Seminar Prasidang berhasil dibuat sepenuhnya"]);
        } else {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Jadwal Seminar Prasidang Gagal dibuat", "html" => $htmlErrorMessage]);
        }

        return redirect()->to(base_url("Kaprodi/seminarPrasidang"));
    }















    public function sidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //$dataAkun = $this->dosenModel->find(session()->get('user_session')['id']);

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);

        $mahasiswa = $this->mahasiswaModel->getMahasiswaBelumDapatSidangByProdi($prodi['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $sidangSkripsi = [];
        if ($currentSemester != null)
            $sidangSkripsi = $this->sidangSkripsiModel->getSidangSkripsiByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $listKlien = $this->klienModel->findAll();

        $data = [

            "title" => "Sidang Skripsi",

            "prodi" => $prodi,

            "sidangSkripsi" => $sidangSkripsi,

            "mahasiswa" => $mahasiswa,

            "dosen" => $dosen,

            "listKlien" => $listKlien,

            "semesterList" => $allSemester,

            "currentSemester" => $currentSemester,

        ];



        return view("kaprodi/sidang_skripsi", $data);
    }



    public function insertJadwalSidangSkripsi()
    {
        // 1. Autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        // 2. Ambil Data Dasar
        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);
        $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Ambil data skripsi & mahasiswa untuk keperluan Email
        $skripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);
        $mahasiswa = $this->mahasiswaModel->where('npm', $npm)->first();

        // 3. Ambil Input Jadwal
        $tanggal_sidang = date_format(date_create($this->request->getPost("tanggal", FILTER_SANITIZE_SPECIAL_CHARS)), 'Y-m-d');
        $jam_sidang = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);
        $ruangan = $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS);
        
        // ID Dosen
        $dosen_penguji = $this->request->getPost("dosen_penguji");
        $ketua_sidang = $this->request->getPost("ketua_sidang");
        $klien = $this->request->getPost("klien");

        // Gabung Waktu
        $waktu_lengkap = $tanggal_sidang . " " . $jam_sidang;

        // 4. [FITUR UTAMA] Cek Bentrok Jadwal
        // Fungsi ini ada di SidangSkripsiModel yang sudah kita update
        $statusBentrok = $this->sidangSkripsiModel->isJadwalBentrok($waktu_lengkap, $ruangan, $dosen_penguji, $ketua_sidang, $skripsi['id']);

        if ($statusBentrok !== false) {
            session()->setFlashdata("message", [
                "icon" => "error", 
                "title" => "Gagal Menambahkan Jadwal", 
                "text" => $statusBentrok 
            ]);
            return redirect()->back()->withInput();
        }

        // 5. Persiapan Data Email (Ambil Nama Dosen)
        $dataPenguji = $this->dosenModel->find($dosen_penguji);
        $dataKetua = $this->dosenModel->find($ketua_sidang);
        
        // Jaga-jaga kalau judul di tabel skripsi kosong, ambil dari tabel lain/set default
        $judulFix = $skripsi['judul'] ?? 'Judul Skripsi Mahasiswa';

        // Template Data Email
        $emailData = [
            'nama_prodi'    => $prodi['nama'],
            'inisial_prodi' => $prodi['inisial'],
            'nama_mhs'      => $mahasiswa['nama'] ?? $npm, // Fallback ke NPM kalau nama ga ketemu
            'npm_mhs'       => $npm,
            'judul_skripsi' => $judulFix,
            'tanggal'       => date('d F Y', strtotime($tanggal_sidang)),
            'jam'           => $jam_sidang,
            'ruangan'       => $ruangan,
        ];

        // --- KIRIM EMAIL 1: KE PENGUJI ---
        $emailPenguji = $emailData;
        $emailPenguji['title'] = "Undangan Penguji Sidang";
        $emailPenguji['nama_dosen'] = $dataPenguji['nama'];
        $emailPenguji['selaku'] = "Penguji Sidang Skripsi";
        
        // Pastikan file view 'undangan' atau 'undangan_sidang_email' ada di folder Views
        // Kita pakai 'undangan' sesuai code kamu, tapi isinya nanti sesuaikan
        $this->sendEmail("", $dataPenguji['email'], "Undangan Penguji Sidang - " . $emailData['nama_mhs'], view("undangan", $emailPenguji));

        // --- KIRIM EMAIL 2: KE KETUA SIDANG (Fitur Baru) ---
        if ($dataKetua) {
            $emailKetua = $emailData;
            $emailKetua['title'] = "Undangan Ketua Sidang";
            $emailKetua['nama_dosen'] = $dataKetua['nama'];
            $emailKetua['selaku'] = "Ketua Komisi Sidang";
            
            $this->sendEmail("", $dataKetua['email'], "Undangan Ketua Sidang - " . $emailData['nama_mhs'], view("undangan", $emailKetua));
        }

        // 6. Insert ke Database
        $this->sidangSkripsiModel->insert([
            'id_skripsi'    => $skripsi['id'],
            'tanggal'       => $waktu_lengkap,
            'ruangan'       => $ruangan,
            'dosen_penguji' => $dosen_penguji,
            'ketua_sidang'  => $ketua_sidang,
            'klien'         => empty($klien) ? null : $klien, // Cek kosong biar aman
            'status'        => 'DAFTAR', // Default status
            'total_nilai'   => 0
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Jadwal sidang berhasil dibuat dan undangan telah dikirim."]);
        return redirect()->to(base_url("Kaprodi/sidangSkripsi"));
    }

    public function updateJadwalSidangSkripsi($idSidangSkripsi)
    {

        // //autentikasi
        // if (!$this->authenticate(["kaprodi"])) {
        //     return redirect()->to(base_url("unauthorized.php"));
        // }

        // //buat mencegah access langsung dari link
        // if ($this->request->getMethod() != 'post') {
        //     return redirect()->back();
        // }

        // $npm = $this->request->getPost("mahasiswa", FILTER_SANITIZE_SPECIAL_CHARS);
        // $skripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);

        // $tanggal_sidang = date_format(date_create($this->request->getPost("tanggal")), 'Y-m-d');
        // $jam_sidang = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);

        // $this->sidangSkripsiModel->update($idSidangSkripsi, [
        //     'tanggal' => $tanggal_sidang . " " . $jam_sidang,
        //     'ruangan' => $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS),
        //     'dosen_penguji' => $this->request->getPost("dosen_penguji"),
        //     'klien' => $this->request->getPost("klien")
        // ]);

        // session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Sidang Skripsi Berhasil diperbarui", "text" => "Jadwal Sidang Skripsi telah diperbarui"]);

        // return redirect()->to(base_url("Kaprodi/sidangSkripsi"));


        // 1. Autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        // 2. [PERBAIKAN] Ambil ID Skripsi dari Database (Bukan dari Input Form yang disabled)
        // Kita cari data jadwal yang sedang diedit
        $jadwalExisting = $this->sidangSkripsiModel->find($idSidangSkripsi);

        if (!$jadwalExisting) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "Data jadwal tidak ditemukan."]);
            return redirect()->back();
        }

        $id_skripsi = $jadwalExisting['id_skripsi']; // Ambil ID Skripsi dari data yang sudah ada

        // 3. Ambil Data Inputan Baru
        $tanggal_input = $this->request->getPost("tanggal");
        $jam_sidang = $this->request->getPost("jam", FILTER_SANITIZE_SPECIAL_CHARS);
        $tanggal_sidang = date_format(date_create($tanggal_input), 'Y-m-d');

        $waktu_lengkap = $tanggal_sidang . " " . $jam_sidang;

        $ruangan = $this->request->getPost("ruangan", FILTER_SANITIZE_SPECIAL_CHARS);
        $dosen_penguji = $this->request->getPost("dosen_penguji");
        $ketua_sidang = $this->request->getPost("ketua_sidang");

        // 4. Cek Bentrok
        // Gunakan $id_skripsi yang kita ambil dari database tadi
        $statusBentrok = $this->sidangSkripsiModel->isJadwalBentrok($waktu_lengkap, $ruangan, $dosen_penguji, $ketua_sidang, $id_skripsi, $idSidangSkripsi);

        if ($statusBentrok !== false) {
            session()->setFlashdata("message", [
                "icon" => "error",
                "title" => "Gagal Mengubah Jadwal",
                "text" => $statusBentrok
            ]);
            return redirect()->back()->withInput();
        }

        // 5. Lakukan Update
        $this->sidangSkripsiModel->update($idSidangSkripsi, [
            'tanggal' => $waktu_lengkap,
            'ruangan' => $ruangan,
            'dosen_penguji' => $dosen_penguji,
            'ketua_sidang' => $ketua_sidang,
            'klien' => $this->request->getPost("klien")
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Jadwal sidang berhasil diperbarui"]);
        return redirect()->to(base_url("Kaprodi/sidangSkripsi"));
    }



    public function deleteJadwalSidangSkripsi($idSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->sidangSkripsiModel->delete($idSidangSkripsi);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Jadwal Sidang Skripsi Berhasil", "text" => "Jadwal Sidang Skripsi Berhasil dihapus"]);

        return redirect()->to(base_url("Kaprodi/sidangSkripsi"));
    }



    public function downloadFormatJadwalSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["kaprodi"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        redirect()->to(base_url("Kaprodi/sidangSkripsi"));

        return $this->response->download("folderResource/Format_Jadwal_Sidang_Skripsi.xlsx", null);
    }



    public function insertJadwalSidangSkripsiBatch()
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dataAkun = $this->prodiModel->find(session()->get("user_session")['id']);
        $validationRules = [
            'fileJadwal' => [
                'rules' => 'uploaded[fileJadwal]|mime_in[fileJadwal,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|ext_in[fileJadwal,xls,xlsx]|max_size[fileJadwal,10000]',
                'errors' => [
                    'uploaded' => 'Pilih File Jadwal Sidang Skripsi terlebih dahulu',
                    'mime_in' => 'File Jadwal Sidang Skripsi harus berupa Excel',
                    'ext_in' => 'File Jadwal Sidang Skripsi harus berekstensi .xls atau .xlsx',
                    'max_size' => 'Ukuran File Jadwal Sidang Skripsi tidak boleh lebih dari 10MB'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            $validation = \Config\Services::validation();
            session()->setFlashdata("message", ["icon" => "error", "title" => "Upload File Jadwal Sidang Skripsi Gagal", "text" => $validation->getError("fileJadwal")]);
            return redirect()->to(base_url("Kaprodi/sidangSkripsi"))->withInput();
        }


        $file = $this->request->getFile("fileJadwal");
        $reader = '';
        if ($file->getExtension() == 'xls') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        } else if ($file->getExtension() == 'xlsx') {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        }

        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($file->getTempName());

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $isAllSuccess = true;
        $htmlErrorMessage = '<table class="table table-bordered">';
        $htmlErrorMessage .= "<thead><tr><td>NPM</td><td>Pesan Error</td></tr></thead>";
        $htmlErrorMessage .= "<tbody>";
        for ($row = 2; $row <= $highestRow; ++$row) {
            if ($worksheet->getCell("A$row")->getValue() == null || trim($worksheet->getCell("A$row")->getValue() == "")) {
                $htmlErrorMessage .= "<tr><td></td><td>Kolom 'NPM' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $strNPM = strval(trim($worksheet->getCell("A$row")->getValue()));

            $mahasiswa = $this->mahasiswaModel->find($strNPM);
            if ($mahasiswa == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak terdaftar</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($mahasiswa['npm']);
            if ($lastSkripsi == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki skripsi</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($lastSkripsi['status'] != STATUS_SKRIPSI_DALAMPENGERJAAN) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut tidak memiliki skripsi dengan status 'Dalam Pengerjaan'</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $pengajuanSidang = $this->pengajuanSidangModel->where("id_skripsi", $lastSkripsi['id'])->orderBy("tanggal_pengajuan", "desc")->findAll();
            if (count($pengajuanSidang) == 0) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>NPM tersebut belum melakukan Pengajuan Sidang Skripsi</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($pengajuanSidang[0]['status'] != 'DISETUJUI') {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Pengajuan Sidang Skripsi NPM ini belum Disetujui</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $jadwalSidangByIdSkripsi = $this->sidangSkripsiModel->where("id_skripsi", $lastSkripsi['id'])->orderBy("tanggal", "desc")->findAll();

            $id_sidang = null;

            $isJadwalExist = count($jadwalSidangByIdSkripsi) != 0;

            if ($isJadwalExist) {
                $id_sidang = $jadwalSidangByIdSkripsi[0]['id'];

                $isReviewedOld = count($this->penilaianSidangModel->where('id_sidang_skripsi', $id_sidang)->findAll()) > 0;

                $isReviewedNew = count($this->penilaianSidangNewModel->where('id_sidang_skripsi', $id_sidang)->findAll()) > 0;

                if ($isReviewedOld || $isReviewedNew) {
                    $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Jadwal Sidang Skripsi ini tidak dapat diubah karena sudah dinilai</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            }

            if ($worksheet->getCell("B$row")->getValue() == null || trim($worksheet->getCell("B$row")->getValue()) == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Tanggal Sidang Skripsi' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $kolomTanggalSeminar = trim($worksheet->getCell("B$row")->getValue());

            if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4} ([0-1][0-9]|2[0-3]):([0-5][0-9])$/", $kolomTanggalSeminar)) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Format 'Tanggal Sidang Skripsi' tidak sesuai</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $tanggal = date_format(date_create_from_format('d-m-Y H:i', $kolomTanggalSeminar), 'Y-m-d H:i');


            if ($worksheet->getCell("C$row")->getValue() == null  || trim($worksheet->getCell("C$row")->getValue() == "")) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Ruangan' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $ruangan = trim($worksheet->getCell("C$row")->getValue());

            if ($worksheet->getCell("D$row")->getValue() == null || trim($worksheet->getCell("D$row")->getValue()) == "") {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Inisial Dosen Penguji' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $penguji1 = $this->dosenModel->getDosenByInisial(trim($worksheet->getCell("D$row")->getValue()));

            if ($penguji1 == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Dosen penguji untuk NPM ini tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($worksheet->getCell("E$row")->getValue() == null  || trim($worksheet->getCell("E$row")->getValue() == "")) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Kolom 'Inisial Klien' tidak boleh kosong (isi dengan '-' jika tidak memiliki klien)</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            $klien = $this->klienModel->where('inisial', trim($worksheet->getCell("E$row")->getValue()))->find();

            if (trim($worksheet->getCell("E$row")->getValue()) != '-' && $klien == null) {
                $htmlErrorMessage .= "<tr><td>$strNPM</td><td>Klien untuk NPM ini tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }


            $jadwal = [
                'id_skripsi' => $lastSkripsi['id'],
                'tanggal' => $tanggal,
                'ruangan' => $ruangan,
                'dosen_penguji' => $penguji1['id'],
                'klien' => $klien == null ? null : $klien[0]['id'],
            ];


            if ($id_sidang == null) {
                $this->sidangSkripsiModel->insert($jadwal);
            } else {
                $this->sidangSkripsiModel->update($id_sidang, $jadwal);
            }
        }

        $htmlErrorMessage .= "</tbody>";
        $htmlErrorMessage .= "</table>";

        $htmlErrorMessage = str_replace("<", "&lt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace(">", "&gt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace("/", "&#47;", $htmlErrorMessage);

        if ($isAllSuccess) {
            session()->setFlashdata("message", ["icon" => "success", "title" => "Jadwal Sidang Skripsi Berhasil dibuat", "text" => "Jadwal Sidang Skripsi berhasil dibuat sepenuhnya"]);
        } else {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Jadwal Sidang Skripsi Gagal dibuat", "html" => $htmlErrorMessage]);
        }

        return redirect()->to(base_url("Kaprodi/sidangSkripsi"));
    }


    public function dosen()
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }
        $inisialProdi = session()->get("user_session")['prodi'];
        $dataProdi = $this->prodiModel->getWhere(["inisial" => $inisialProdi])->getResultArray();
        if (count($dataProdi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        $listDosen = $this->dosenModel->getDosenInfoByIdProdi($dataProdi[0]['id']);

        $data = [
            "title" => "Kelola Dosen " . $inisialProdi,
            "listDosen" => $listDosen,
        ];

        return view("kaprodi/dosen", $data);
    }

    public function updateDosen()
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
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

        if ($this->dosenModel->isInitialAlreadyUsed($dosenId, $inisial)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Dosen Gagal", "text" => "Inisial telah digunakan!"]);
            return redirect()->to(base_url("Kaprodi/dosen"));
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
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Dosen Berhasil", "text" => "Data dosen berhasil diubah!"]);
        return redirect()->to(base_url("Kaprodi/dosen"));
    }


    public function profile()
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->prodiModel->getProdiInfo(session()->get("user_session")['id']);
        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Ubah Data Diri',
            'dataDiri' => $dataAkun,
        ];

        return view("Kaprodi/profile", $data);
    }


    public function saveProfile()
    {
        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $idProdi = $this->request->getPost("id");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $dataProdi = $this->prodiModel->getProdiInfo($idProdi);
        if ($dataProdi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->akunModel->isEmailAlreadyUsed($dataProdi['id_akun'], $email)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Email telah digunakan!"]);
            return redirect()->to(base_url("Kaprodi/profile"));
        }

        $this->akunModel->update($dataProdi['id_akun'], [
            "email" => $email,
        ]);

        $this->prodiModel->update($idProdi, [
            "nama" => $nama,
            "email" => $email,
        ]);


        session()->set("user_session", [
            'id' => $dataProdi['id'],
            'nama' => $nama,
            'email' => $email,
            'prodi' => $dataProdi['inisial'],
            'roles' => ['kaprodi'],
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Diri Berhasil", "text" => "Data diri berhasil diubah!"]);
        return redirect()->to(base_url("Kaprodi/profile"));
    }


    public function pembimbing()
    {

        //autentikasi
        if (!$this->authenticate(["kaprodi"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $prodi = $this->prodiModel->find(session()->get("user_session")['id']);
        if ($prodi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $pembimbing = [];
        if ($currentSemester != null)
            $pembimbing = $this->mahasiswaModel->getPembimbingMahasiswaByIdProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [
            "title" => "Pembimbing Mahasiswa " . $prodi['inisial'],
            "pembimbing" => $pembimbing,
            "semesterList" => $allSemester,
        ];

        return view("kaprodi/pembimbing", $data);
    }






    private function sendEmail($attachment, $to, $title, $message)
{
    $email = \Config\Services::email();
    
    // 1. Tambahkan ini agar tidak ada sisa data dari pengiriman sebelumnya
    $email->clear(true); 

    // 2. JANGAN pakai $email->initialize($config) lagi. 
    // Biarkan dia otomatis ambil dari Config/Email.php (Gmail)

    $email->setFrom('unitedkfr@gmail.com', 'Sistem Informasi Skripsi');
    $email->setTo($to);
    $email->setSubject($title);
    $email->setMessage($message);

    // 3. Hanya lampirkan jika variabel $attachment tidak kosong
    if (!empty($attachment)) {
        $email->attach($attachment);
    }

    if (!$email->send()) {
        // Jika gagal, log errornya untuk dibaca
        log_message('error', $email->printDebugger(['headers']));
        return false;
    }
    
    return true;
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
 private function kirimEmailReviewer($idSempro)
{
    $email = \Config\Services::email();
    $db = \Config\Database::connect();

    $data = $db->table('seminar_proposal sp')
        ->select('sp.*, p.judul, m.nama as nama_mhs, m.npm, d.nama as nama_dosen, d.email as email_dosen')
        ->join('proposal p', 'sp.id_proposal = p.id')
        ->join('mahasiswa m', 'p.npm = m.npm')
        ->join('dosen d', 'sp.dosen_penguji1 = d.id')
        ->where('sp.id', $idSempro)
        ->get()->getRowArray();

    if ($data && !empty($data['email_dosen'])) {
        $email->setTo($data['email_dosen']);
        $email->setFrom('noreply@uai.ac.id', 'Sistem Informasi Tugas Akhir');
        $email->setSubject('UNDANGAN REVIEWER: ' . $data['nama_mhs']);
        
        // --- TAMPILAN HTML YANG RAPI ---
        $message = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; border: 1px solid #ddd; padding: 20px;'>
            <h2 style='color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;'>Undangan Reviewer Seminar Proposal</h2>
            <p>Yth. <strong>" . $data['nama_dosen'] . "</strong>,</p>
            <p>Anda telah dijadwalkan sebagai <strong>Reviewer 1</strong> untuk mahasiswa berikut ini:</p>
            
            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <tr>
                    <td style='width: 35%; padding: 8px; background: #f9f9f9; font-weight: bold; border: 1px solid #eee;'>Nama Mahasiswa</td>
                    <td style='padding: 8px; border: 1px solid #eee;'>" . $data['nama_mhs'] . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; background: #f9f9f9; font-weight: bold; border: 1px solid #eee;'>NPM</td>
                    <td style='padding: 8px; border: 1px solid #eee;'>" . $data['npm'] . "</td>
                </tr>
                <tr>
                    <td style='padding: 8px; background: #f9f9f9; font-weight: bold; border: 1px solid #eee;'>Judul Proposal</td>
                    <td style='padding: 8px; border: 1px solid #eee;'><em>\"" . $data['judul'] . "\"</em></td>
                </tr>
                <tr>
                    <td style='padding: 8px; background: #f9f9f9; font-weight: bold; border: 1px solid #eee;'>Waktu Pelaksanaan</td>
                    <td style='padding: 8px; border: 1px solid #eee;'>" . date('d F Y', strtotime($data['tanggal'])) . " pukul " . date('H:i', strtotime($data['tanggal'])) . " WIB</td>
                </tr>
                <tr>
                    <td style='padding: 8px; background: #f9f9f9; font-weight: bold; border: 1px solid #eee;'>" . (!empty($data['link_konferensi']) ? 'Link Online' : 'Ruangan') . "</td>
                    <td style='padding: 8px; border: 1px solid #eee; color: #e67e22; font-weight: bold;'>" . (!empty($data['link_konferensi']) ? $data['link_konferensi'] : $data['ruangan']) . "</td>
                </tr>
            </table>

            <p style='margin-top: 25px;'>Mohon kesediaan Bapak/Ibu untuk hadir tepat waktu. Informasi lebih lengkap dapat diakses melalui portal dosen.</p>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
            <p style='font-size: 12px; color: #888;'>Ini adalah email otomatis dari Sistem Informasi Tugas Akhir. Harap tidak membalas email ini.</p>
        </div>
        ";

        $email->setMessage($message);
        
        return $email->send();
    }
    return false;
}
private function sendEmailSidang($to, $title, $message)
{
    $email = \Config\Services::email();
    $email->clear(true); 

    $email->setTo($to);
    $email->setSubject($title);
    $email->setMessage($message);

    return $email->send();
}


}
