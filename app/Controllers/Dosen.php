<?php



namespace App\Controllers;



use CodeIgniter\I18n\Time;



class Dosen extends BaseController
{

    protected $proposalModel;

    protected $dosenModel;

    protected $mahasiswaModel;

    protected $bidangModel;

    protected $prodiModel;

    protected $semproModel;

    protected $pembimbingModel;

    protected $catatanBimbinganModel;

    protected $skripsiModel;

    protected $seminarPrasidangModel;

    protected $sidangSkripsiModel;

    protected $penilaianSidangModel;

    protected $penelitianDosenModel;

    protected $semesterModel;

    protected $akunModel;

    protected $modeSemproModel;

    protected $klienModel;

    protected $penilaianSidangNewModel;

    protected $penilaianSidangParamModel;



    public function __construct()
    {

        $this->proposalModel = new \App\Models\ProposalModel();

        $this->dosenModel = new \App\Models\DosenModel();

        $this->mahasiswaModel = new \App\Models\MahasiswaModel();

        $this->bidangModel = new \App\Models\BidangModel();

        $this->prodiModel = new \App\Models\ProgramStudiModel();

        $this->semproModel = new \App\Models\SeminarProposalModel();

        $this->pembimbingModel = new \App\Models\PembimbingModel();

        $this->catatanBimbinganModel = new \App\Models\CatatanBimbinganModel();

        $this->skripsiModel = new \App\Models\SkripsiModel();

        $this->seminarPrasidangModel = new \App\Models\SeminarPrasidangModel();

        $this->sidangSkripsiModel = new \App\Models\SidangSkripsiModel();

        $this->penilaianSidangModel = new \App\Models\PenilaianSidangModel();

        $this->penelitianDosenModel = new \App\Models\PenelitianDosenModel();

        $this->semesterModel = new \App\Models\SemesterModel();

        $this->akunModel = new \App\Models\AkunModel();

        $this->modeSemproModel = new \App\Models\ModeSemproModel();

        $this->klienModel = new \App\Models\KlienModel();

        $this->penilaianSidangNewModel = new \App\Models\PenilaianSidangNewModel();

        $this->penilaianSidangParamModel = new \App\Models\PenilaianSidangParamModel();

    }



    public function index()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        // 2. Ambil Data Dosen yang sedang login
        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        // 3. Ambil Statistik Bimbingan dari SkripsiModel
        // (Pastikan SkripsiModel sudah ada fungsi getStatistikBimbingan yang kita buat sebelumnya)
        $statistik = $this->skripsiModel->getStatistikBimbingan($dataAkun['id']);

        $data = [
            'title' => 'Dashboard Dosen',
            'dataAkun' => $dataAkun,
            'statistik' => $statistik
        ];

        // 4. Tampilkan View Dashboard, BUKAN redirect ke home
        return view("dosen/dashboard", $data);

    }



    public function pengujiSeminarProposal()
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataDosen = $this->dosenModel->find(session()->get("user_session")['id']);

        $prodi = $this->prodiModel->find($dataDosen['id_prodi']);

        $dosen = $this->dosenModel->getDosenByProdi($dataDosen['id_prodi']);

        $kodeSemester = $this->request->getGet("semester");



        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $modeSempro = $this->modeSemproModel->getModeSempro($currentSemester['kode'], $prodi['id']);

        $seminarProposal = [];
        if ($modeSempro != null && $modeSempro['mode'] == 'Asinkronus') {

            if ($currentSemester != null)
                $seminarProposal = $this->semproModel->getSemproByProdiAndKodeSemester($prodi['id'], $currentSemester['kode']);

        } else if ($modeSempro != null && ($modeSempro['mode'] == 'Sinkronus Daring' || $modeSempro['mode'] == 'Sinkronus Luring')) {

            if ($currentSemester != null)
                $seminarProposal = $this->semproModel->getSemproByDosenAndKodeSemester($dataDosen['id'], $currentSemester['kode']);

        }

        $data = [

            'title' => 'Review Seminar Proposal',

            'prodi' => $prodi,

            'dosen' => $dosen,

            'modeSempro' => $modeSempro,

            'seminarProposal' => $seminarProposal,

            'semesterList' => $allSemester,

        ];

        return view("dosen/penguji_seminar_proposal", $data);

    }



    public function insertPenilaianSempro($idSempro)
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $sempro = $this->semproModel->find($idSempro);

        $proposal = $this->proposalModel->find($sempro['id_proposal']);

        // $komentar = $this->request->getPost("komentar", FILTER_SANITIZE_SPECIAL_CHARS);
        $komentarInput = $this->request->getPost("komentar", FILTER_SANITIZE_SPECIAL_CHARS);

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($proposal['npm']);

        if ($lastSkripsi != null) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Penilaian Seminar Proposal Gagal", "text" => "Penilaian tidak dapat diubah karena mahasiswa sudah Menambahkan Skripsi!"]);
            return redirect()->to(base_url("dosen/pengujiSeminarProposal"));
        }

                // Siapkan data dasar
        $dataUpdate = [
            'status' => $this->request->getPost("status"),
            'pembuat_komentar' => $dataAkun['id'], // Last Updated By
            'jenis_skripsi' => $this->request->getPost("jenis_skripsi"),
        ];

        // LOGIKA PEMISAH KOLOM:
        // Cek apakah Dosen yang login adalah Penguji 1 atau Penguji 2?
        if ($dataAkun['id'] == $sempro['dosen_penguji1']) {
            // Dosen 1 mengisi kolom 'komentar' (lama)
            $dataUpdate['komentar'] = $komentarInput;
        }
        elseif ($dataAkun['id'] == $sempro['dosen_penguji2']) {
            // Dosen 2 mengisi kolom 'komentar_2' (baru)
            $dataUpdate['komentar_2'] = $komentarInput;
        }
        else {
            // Fallback: Jika admin/kaprodi yang edit, default ke kolom 1
            $dataUpdate['komentar'] = $komentarInput;
        }

        $this->proposalModel->update($sempro['id_proposal'], $dataUpdate);



        // $this->proposalModel->update($sempro['id_proposal'], [

        //     'komentar' => $komentar,

        //     'status' => $this->request->getPost("status"),

        //     'pembuat_komentar' => $dataAkun['id'],

        //     'jenis_skripsi' => $this->request->getPost("jenis_skripsi"),

        // ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Penilaian Seminar Proposal Berhasil", "text" => "Seminar Proposal berhasil dinilai!"]);

        return redirect()->to(base_url("dosen/pengujiSeminarProposal"));

    }













    public function bimbingan()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        $dosen = $this->dosenModel->find(session()->get("user_session")['id']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $mahasiswaBimbingan = [];
        if ($currentSemester != null) {

            $mahasiswaBimbingan = $this->pembimbingModel->getAllMahasiswaBimbinganByKodeSemester($dosen['id'], $currentSemester['kode']);

            for ($i = 0; $i < count($mahasiswaBimbingan); $i++) {

                $idPembimbing = $mahasiswaBimbingan[$i]['id'];

                $hasilBimbingan = $this->catatanBimbinganModel->getWhere(['id_pembimbing' => $idPembimbing])->getResultArray();

                $mahasiswaBimbingan[$i]['jumlah_bimbingan'] = count($hasilBimbingan);

            }
        }

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Mahasiswa Bimbingan',

            'mahasiswaBimbingan' => $mahasiswaBimbingan,

            'semesterList' => $allSemester,

        ];



        return view('dosen/bimbingan', $data);

    }



    public function detailBimbingan($idBimbingan)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dosen = $this->dosenModel->find(session()->get("user_session")['id']);

        $dataPembimbing = $this->pembimbingModel->find($idBimbingan);
        if ($dataPembimbing == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($dataPembimbing['id_dosen'] != $dosen['id']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $lastSkripsi = $this->skripsiModel->find($dataPembimbing['id_skripsi']);
        if ($lastSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $semester = $this->semesterModel->find($lastSkripsi['kode_semester']);

        $mahasiswa = $this->mahasiswaModel->find($lastSkripsi['npm']);
        if ($mahasiswa == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $catatanBimbingan = $this->catatanBimbinganModel->getWhere(['id_pembimbing' => $dataPembimbing['id']])->getResultArray();

        $data = [

            'title' => 'Detail Bimbingan',

            'mahasiswa' => $mahasiswa,

            'semester' => $semester,

            'catatanBimbingan' => $catatanBimbingan,

            'pembimbing' => $dataPembimbing,

        ];

        return view("dosen/detail_bimbingan", $data);

    }



    public function setujuiBimbingan($idCatatan)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $this->catatanBimbinganModel->update($idCatatan, [

            'status' => 'DISETUJUI',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Catatan Bimbingan Disetujui", "text" => "Catatan Bimbingan berhasil disetujui!"]);

        return redirect()->back();

    }

    public function setujuiSemua($idPembimbing)
    {
        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        $this->catatanBimbinganModel->updateAllToDisetujui($idPembimbing);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Catatan Bimbingan Disetujui", "text" => "Semua Catatan Bimbingan berhasil disetujui!"]);

        return redirect()->back();
    }



    public function tolakBimbingan($idCatatan)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $this->catatanBimbinganModel->update($idCatatan, [

            'status' => 'DITOLAK',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Catatan Bimbingan Ditolak", "text" => "Catatan Bimbingan berhasil ditolak!"]);

        return redirect()->back();

    }


    public function tolakSemua($idPembimbing)
    {
        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }

        $this->catatanBimbinganModel->updateAllToDitolak($idPembimbing);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Catatan Bimbingan Ditolak", "text" => "Semua Catatan Bimbingan berhasil ditolak!"]);

        return redirect()->back();
    }










    public function pengujiSeminarPrasidang()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $dosen = $this->dosenModel->getDosenByProdi($dataAkun['id_prodi']);

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $seminarPrasidang = [];
        if ($currentSemester != null)
            $seminarPrasidang = $this->seminarPrasidangModel->getSeminarPrasidangByDosenAndKodeSemester($dataAkun['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Jadwal Seminar Prasidang',

            'dosen' => $dosen,

            'seminarPrasidang' => $seminarPrasidang,

            'semesterList' => $allSemester,

        ];



        return view("dosen/penguji_seminar_prasidang", $data);

    }



    public function reviewSeminarPrasidang($idSeminarPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $detailSeminarPrasidang = $this->seminarPrasidangModel->getDetailSeminarPrasidangById($idSeminarPrasidang);

        //dd($detailSeminarPrasidang);

        if (count($detailSeminarPrasidang) == 0) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        if ($detailSeminarPrasidang[0]['dosen_reviewer'] != $dataAkun['id']) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $data = [

            'title' => 'Review Seminar Prasidang',

            'dataAkun' => $dataAkun,

            'detailSeminarPrasidang' => $detailSeminarPrasidang[0]

        ];



        return view("dosen/review_seminar_prasidang", $data);

    }



    public function komentariSeminarPrasidang($idSeminarPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $komentar = $this->request->getPost("komentar", FILTER_SANITIZE_SPECIAL_CHARS);

        $status = $this->request->getPost("status");

        $rekomendasi_nilai = $this->request->getPost("rekomendasi_nilai");

        if ($status == 'TIDAK LAYAK SIDANG') {

            $npm = $this->request->getPost("npm");

            $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);

            if ($lastSkripsi['jumlah_penolakan_sidang'] == (BATAS_PENGULANGAN_SEMINAR - 1) && $status == 'TIDAK LAYAK SIDANG') {
                $this->skripsiModel->update($lastSkripsi['id'], [
                    'status' => 'TIDAK LULUS',
                    'jumlah_penolakan_sidang' => $lastSkripsi['jumlah_penolakan_sidang'] + 1,
                    'tanggal_selesai_skripsi' => date_format(Time::now('Asia/Jakarta', 'en_us'), 'Y-m-d H:i:s'),
                ]);
            } else {
                $this->skripsiModel->update($lastSkripsi['id'], [
                    'jumlah_penolakan_sidang' => $lastSkripsi['jumlah_penolakan_sidang'] + 1,
                ]);
            }
        }

        $this->seminarPrasidangModel->update($idSeminarPrasidang, [

            'komentar_reviewer' => $komentar,

            'status' => $status,

            'rekomendasi_nilai' => $rekomendasi_nilai,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Hasil Review Seminar Prasidang Terkirim", "text" => "Hasil Review telah terkirim kepada mahasiswa"]);

        return redirect()->to(base_url("dosen/pengujiSeminarPrasidang"));

    }













    public function pengujiSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        $role = session()->get("user_session")['roles'][0];

        if ($role == "dosen" || $role == "dospem eksternal") {
            $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);
        } else {
            $dataAkun = $this->klienModel->find(session()->get("user_session")['id']);
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $sidangSkripsi = [];
        if ($currentSemester != null)
            $sidangSkripsi = $this->sidangSkripsiModel->getSidangSkripsiByDosenOrKlienAndKodeSemester($dataAkun['id'], $currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Jadwal Sidang Skripsi',

            'sidangSkripsi' => $sidangSkripsi,

            'dataAkun' => $dataAkun,

            'semesterList' => $allSemester,

        ];



        return view("dosen/penguji_sidang_skripsi", $data);

    }



    public function penilaianSidangSkripsi($idSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $detailSidangSkripsi = $this->sidangSkripsiModel->getDetailSidangSkripsiById($idSidangSkripsi);

        if (count($detailSidangSkripsi) == 0) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        if (
            $detailSidangSkripsi[0]['id_pembimbing1'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['id_pembimbing2'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['id_pembimbing_agama'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['dosen_penguji'] != $dataAkun['id']
        ) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $nilaiSidang = $this->penilaianSidangModel->getWhere([

            'id_sidang_skripsi' => $idSidangSkripsi,

            'id_dosen' => $dataAkun['id'],

        ])->getResultArray();



        $statusPenampilanNilai = $this->penilaianSidangModel->getStatusPenampilanNilai($idSidangSkripsi);


        $data = [

            'title' => 'Penilaian Sidang Skripsi',

            'dataAkun' => $dataAkun,

            'detailSidangSkripsi' => $detailSidangSkripsi[0],

            'nilaiSidang' => $nilaiSidang,

            'statusPenampilanNilai' => $statusPenampilanNilai,

        ];



        return view("dosen/penilaian_sidang_skripsi", $data);

    }



    public function insertNilaiSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $idSidangSkripsi = $this->request->getPost("idSidangSkripsi");



        $detailSidangSkripsi = $this->sidangSkripsiModel->getDetailSidangSkripsiById($idSidangSkripsi);

        if (count($detailSidangSkripsi) == 0) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);



        if (
            $detailSidangSkripsi[0]['id_pembimbing1'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['id_pembimbing2'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['id_pembimbing_agama'] != $dataAkun['id'] &&

            $detailSidangSkripsi[0]['dosen_penguji'] != $dataAkun['id']
        ) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $idDosen = $this->request->getPost("idDosen");

        $nilai_1 = $this->request->getPost("nilai_1");

        $nilai_2 = $this->request->getPost("nilai_2");

        $nilai_3 = $this->request->getPost("nilai_3");

        $nilai_4 = $this->request->getPost("nilai_4");

        $nilai_5 = $this->request->getPost("nilai_5");

        $nilai_6 = $this->request->getPost("nilai_6");

        $nilai_7 = $this->request->getPost("nilai_7");

        $nilai_8 = $this->request->getPost("nilai_8");

        $nilai_9 = $this->request->getPost("nilai_9");

        $nilai_10 = $this->request->getPost("nilai_10");

        $nilai_11 = $this->request->getPost("nilai_11");

        $nilai_12 = $this->request->getPost("nilai_12");

        $nilai_akhir = $this->request->getPost("nilai_akhir");

        $grade = $this->request->getPost("grade");

        $status = $this->request->getPost("status");

        $tunda_nilai = $this->request->getPost("tundaNilai");



        $this->penilaianSidangModel->insert([

            'id_dosen' => $idDosen,

            'id_sidang_skripsi' => $idSidangSkripsi,

            'nilai_1' => $nilai_1,

            'nilai_2' => $nilai_2,

            'nilai_3' => $nilai_3,

            'nilai_4' => $nilai_4,

            'nilai_5' => $nilai_5,

            'nilai_6' => $nilai_6,

            'nilai_7' => $nilai_7,

            'nilai_8' => $nilai_8,

            'nilai_9' => $nilai_9,

            'nilai_10' => $nilai_10,

            'nilai_11' => $nilai_11,

            'nilai_12' => $nilai_12,

            'nilai_akhir' => $nilai_akhir,

            'grade' => $grade,

            'status' => $status,

            'tunda_nilai' => $tunda_nilai != null ? 'YA' : 'TIDAK',

        ]);



        $this->perbaruiStatusKelulusan($idSidangSkripsi);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Beri Penilaian Sidang Berhasil", "text" => "Penilaian Sidang Skripsi telah berhasil disimpan"]);

        return redirect()->back();

    }



    public function updateNilaiSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $idPenilaianSidang = $this->request->getPost("idPenilaianSidang");

        $idSidangSkripsi = $this->request->getPost("idSidangSkripsi");

        $idDosen = $this->request->getPost("idDosen");

        $nilai_1 = $this->request->getPost("nilai_1");

        $nilai_2 = $this->request->getPost("nilai_2");

        $nilai_3 = $this->request->getPost("nilai_3");

        $nilai_4 = $this->request->getPost("nilai_4");

        $nilai_5 = $this->request->getPost("nilai_5");

        $nilai_6 = $this->request->getPost("nilai_6");

        $nilai_7 = $this->request->getPost("nilai_7");

        $nilai_8 = $this->request->getPost("nilai_8");

        $nilai_9 = $this->request->getPost("nilai_9");

        $nilai_10 = $this->request->getPost("nilai_10");

        $nilai_11 = $this->request->getPost("nilai_11");

        $nilai_12 = $this->request->getPost("nilai_12");

        $nilai_akhir = $this->request->getPost("nilai_akhir");

        $grade = $this->request->getPost("grade");

        $status = $this->request->getPost("status");

        $tunda_nilai = $this->request->getPost("tundaNilai");



        $this->penilaianSidangModel->update($idPenilaianSidang, [

            'id_dosen' => $idDosen,

            'id_sidang_skripsi' => $idSidangSkripsi,

            'nilai_1' => $nilai_1,

            'nilai_2' => $nilai_2,

            'nilai_3' => $nilai_3,

            'nilai_4' => $nilai_4,

            'nilai_5' => $nilai_5,

            'nilai_6' => $nilai_6,

            'nilai_7' => $nilai_7,

            'nilai_8' => $nilai_8,

            'nilai_9' => $nilai_9,

            'nilai_10' => $nilai_10,

            'nilai_11' => $nilai_11,

            'nilai_12' => $nilai_12,

            'nilai_akhir' => $nilai_akhir,

            'grade' => $grade,

            'status' => $status,

            'tunda_nilai' => $tunda_nilai != null ? 'YA' : 'TIDAK',

        ]);



        $this->perbaruiStatusKelulusan($idSidangSkripsi);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Beri Penilaian Sidang Berhasil", "text" => "Penilaian Sidang Skripsi telah berhasil disimpan"]);

        return redirect()->back();

    }



    public function perbaruiStatusKelulusan($idSidangSkripsi)
    {

        $sidangSkripsi = $this->sidangSkripsiModel->find($idSidangSkripsi);

        if ($sidangSkripsi == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }



        $lastSkripsi = $this->skripsiModel->find($sidangSkripsi['id_skripsi']);



        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);

        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);

        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);

        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);



        $nilaiPembimbing1 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu1[0]['id_dosen']])->getResultArray();

        $nilaiPembimbing2 = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingIlmu2[0]['id_dosen']])->getResultArray();

        $nilaiPembimbingAgama = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $pembimbingAgama[0]['id_dosen']])->getResultArray();

        $nilaiPenguji = $this->penilaianSidangModel->getWhere(['id_sidang_skripsi' => $idSidangSkripsi, 'id_dosen' => $sidangSkripsi['dosen_penguji']])->getResultArray();



        //set data kelulusan

        if (count($nilaiPembimbing1) == 1 && ($pembimbingIlmu2[0]['id_dosen'] == null || ($pembimbingIlmu2[0]['id_dosen'] != null && count($nilaiPembimbing2) == 1)) && count($nilaiPembimbingAgama) == 1 && count($nilaiPenguji) == 1) {

            $jumlah = 0.0;

            $total_nilai = 0.0;



            //jika punya pembimbing 2

            if ($pembimbingIlmu2[0]['id_dosen'] != null) {

                $jumlah = floatval($nilaiPembimbing1[0]['nilai_akhir']) + floatval($nilaiPembimbing2[0]['nilai_akhir']) + floatval($nilaiPembimbingAgama[0]['nilai_akhir']) + floatval($nilaiPenguji[0]['nilai_akhir']);

                $total_nilai = (float) $jumlah / (float) 4;

            } //jika gak punya pembimbing 2 
            else if ($pembimbingIlmu2[0]['id_dosen'] == null) {

                $jumlah = floatval($nilaiPembimbing1[0]['nilai_akhir']) + floatval($nilaiPembimbingAgama[0]['nilai_akhir']) + floatval($nilaiPenguji[0]['nilai_akhir']);

                $total_nilai = (float) $jumlah / (float) 3;

            }



            $grade = "";



            if ($total_nilai >= 3.76) {

                $grade = "A";

            } else if ($total_nilai >= 3.51) {

                $grade = "A-";

            } else if ($total_nilai >= 3.10) {

                $grade = "B+";

            } else if ($total_nilai >= 2.76) {

                $grade = "B";

            }



            $status = "";



            if ($total_nilai >= 2.76) {

                $status = 'LULUS';

            } else {

                $status = 'TIDAK LULUS';

            }



            //update sidang skripsi

            $this->sidangSkripsiModel->update($idSidangSkripsi, [

                'total_nilai' => $total_nilai,

                'grade' => $grade,

                'status' => $status,

            ]);

            $sidangSkripsi = $this->sidangSkripsiModel->find($idSidangSkripsi);



            //update skripsi

            $this->skripsiModel->update($lastSkripsi['id'], [

                'status' => $status,

            ]);

        }

    }





    public function penilaianSidangSkripsiN($idSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }

        $detailSidangSkripsi = $this->sidangSkripsiModel->getDetailSidangSkripsiById($idSidangSkripsi);
        if (count($detailSidangSkripsi) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($detailSidangSkripsi[0]['jenis_skripsi'] == 'SKRIPSI REGULER') {
            return $this->penilaianSidangNonProyek($idSidangSkripsi, $detailSidangSkripsi);
        } else {
            return $this->penilaianSidangProyek($idSidangSkripsi, $detailSidangSkripsi);
        }

    }

    private function penilaianSidangNonProyek($idSidangSkripsi, $detailSidangSkripsi)
    {
        $role = session()->get("user_session")['roles'][0];

        if ($role == "dosen" || $role == "dospem eksternal") {
            $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);
        } else {
            $dataAkun = $this->klienModel->find(session()->get("user_session")['id']);
        }

        $peran = null;
        if ($detailSidangSkripsi[0]['id_pembimbing1'] == $dataAkun['id']) {
            $peran = "Pembimbing Ilmu 1";
        } elseif ($detailSidangSkripsi[0]['id_pembimbing2'] == $dataAkun['id']) {
            $peran = "Pembimbing Ilmu 2";
        } elseif ($detailSidangSkripsi[0]['id_pembimbing_agama'] == $dataAkun['id']) {
            $peran = "Pembimbing Agama";
        } elseif ($detailSidangSkripsi[0]['dosen_penguji'] == $dataAkun['id']) {
            $peran = "Dosen Penguji";
        } elseif ($detailSidangSkripsi[0]['id_klien'] == $dataAkun['id']) {
            $peran = "Klien";
        } else {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $rubrik = $this->penilaianSidangParamModel->getRubrikPdsi2023();

        $nilaiSebelumnya = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $dataAkun['id'], $peran);

        $statusPenampilanNilai = $this->penilaianSidangModel->getStatusPenampilanNilai($idSidangSkripsi);

        $data = [

            'title' => 'Penilaian Sidang Skripsi',

            'dataAkun' => $dataAkun,

            'detailSidangSkripsi' => $detailSidangSkripsi[0],

            'rubrik' => $rubrik,

            'statusPenampilanNilai' => $statusPenampilanNilai,

            'peranPenilai' => $peran,

            'nilaiSaatIni' => $nilaiSebelumnya,

        ];

        return view("dosen/penilaian_sidang_non_proyek", $data);
    }

    private function penilaianSidangProyek($idSidangSkripsi, $detailSidangSkripsi)
    {
        $role = session()->get("user_session")['roles'][0];

        if ($role == "dosen" || $role == "dospem eksternal") {
            $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);
        } else {
            $dataAkun = $this->klienModel->find(session()->get("user_session")['id']);
        }

        $pemangkuKepentingan = null;
        if ($detailSidangSkripsi[0]['id_pembimbing1'] == $dataAkun['id']) {
            $pemangkuKepentingan = "PEMBIMBING ILMU";
        } elseif ($detailSidangSkripsi[0]['id_pembimbing2'] == $dataAkun['id']) {
            $pemangkuKepentingan = "PEMBIMBING ILMU";
        } elseif ($detailSidangSkripsi[0]['id_pembimbing_agama'] == $dataAkun['id']) {
            $pemangkuKepentingan = "PEMBIMBING AGAMA";
        } elseif ($detailSidangSkripsi[0]['dosen_penguji'] == $dataAkun['id']) {
            $pemangkuKepentingan = "PENGUJI";
        } elseif ($detailSidangSkripsi[0]['id_klien'] == $dataAkun['id']) {
            $pemangkuKepentingan = "KLIEN";
        } else {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $rubrik = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan($pemangkuKepentingan);

        $nilaiSebelumnya = $this->penilaianSidangNewModel->getPreviousNilaiSidang($idSidangSkripsi, $dataAkun['id'], $pemangkuKepentingan);

        $statusPenampilanNilai = $this->penilaianSidangModel->getStatusPenampilanNilai($idSidangSkripsi);

        $data = [

            'title' => 'Penilaian Sidang Skripsi',

            'dataAkun' => $dataAkun,

            'detailSidangSkripsi' => $detailSidangSkripsi[0],

            'rubrik' => $rubrik,

            'statusPenampilanNilai' => $statusPenampilanNilai,

            'peranPenilai' => $pemangkuKepentingan,

            'nilaiSaatIni' => $nilaiSebelumnya,

        ];

        return view("dosen/penilaian_sidang_proyek", $data);
    }


    public function insertNilaiSidangSkripsiN()
    {
        //autentikasi
        if (!$this->authenticate(["dosen", "dospem eksternal", "klien"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $peranPenilai = $this->request->getPost("peran_penilai");
        $idPenilai = $this->request->getPost("id_penilai");
        $dataAkun = null;
        switch (strtoupper($peranPenilai)) {
            case "PEMBIMBING ILMU":
            case "PEMBIMBING ILMU 1":
            case "PEMBIMBING ILMU 2":
            case "PEMBIMBING AGAMA":
            case "DOSEN PENGUJI":
            case "PENGUJI":
                $dataAkun = $this->dosenModel->find($idPenilai);
                break;
            case "KLIEN":
                $dataAkun = $this->klienModel->find($idPenilai);
                break;
        }

        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        $idSidangSkripsi = $this->request->getPost("id_sidang_skripsi");
        $sidangSkripsi = $this->sidangSkripsiModel->find($idSidangSkripsi);
        if ($sidangSkripsi == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $jenisSkripsi = $this->request->getPost("jenis_skripsi");

        $rubrikNilai = null;
        if ($jenisSkripsi == JENIS_SKRIPSI_PROYEK_TIM || $jenisSkripsi == JENIS_SKRIPSI_PROYEK_MANDIRI) {
            $rubrikNilai = $this->penilaianSidangParamModel->getRubrikByPemangkuKepentingan($peranPenilai);
        } else {
            $rubrikNilai = $this->penilaianSidangParamModel->where(["kode" => "PDSI-DETAIL-DESKRIPSI-2023"])->findAll();
        }

        $dataPenilaian = [];
        foreach ($rubrikNilai as $r) {
            $id = $r['urutan'];
            $nilai = $this->request->getPost("nilai_" . $id, FILTER_SANITIZE_SPECIAL_CHARS);
            array_push($dataPenilaian, [
                'id_rubrik' => $id,
                'id_penilai' => $dataAkun["id"],
                'peran_penilai' => $peranPenilai,
                'nilai' => $nilai,
                'id_sidang_skripsi' => $sidangSkripsi['id'],
            ]);
        }

        $this->penilaianSidangNewModel->upsertNilaiSidang($dataPenilaian);

        //itung jumlah orang yang seharusnya menilai skripsi ini
        $pembimbingSkripsi = $this->pembimbingModel->getAllPembimbingByIdSkripsi($sidangSkripsi['id_skripsi']);
        $jumlahPenilai = count($pembimbingSkripsi);
        $jumlahPenilai += ($sidangSkripsi['dosen_penguji'] != null ? 1 : 0);
        $jumlahPenilai += ($sidangSkripsi['klien'] != null ? 1 : 0);

        //itung jumlah nilai yang udah diberikan
        $jumlahNilaiMasuk = count($this->penilaianSidangNewModel->getPeranPenilaiByIdSidangSkripsi($sidangSkripsi['id']));

        //kalo setiap orang udah ngasih nilai maka
        if ($jumlahPenilai == $jumlahNilaiMasuk) {

            $idPembimbing1 = null;
            $idPembimbing2 = null;
            $idPembimbingAgama = null;
            foreach ($pembimbingSkripsi as $p) {
                $idPembimbing1 = strcmp(strtolower(trim($p['role'])), 'pembimbing ilmu 1') == 0 ? $p['id_dosen'] : $idPembimbing1;
                $idPembimbing2 = strcmp(strtolower(trim($p['role'])), 'pembimbing ilmu 2') == 0 ? $p['id_dosen'] : $idPembimbing2;
                $idPembimbingAgama = strcmp(strtolower(trim($p['role'])), 'pembimbing agama') == 0 ? $p['id_dosen'] : $idPembimbingAgama;
            }

            //ambil nilai akhirnya berdasarkan jenis skripsi
            $nilaiAkhir = null;
            if ($jenisSkripsi == JENIS_SKRIPSI_PROYEK_TIM || $jenisSkripsi == JENIS_SKRIPSI_PROYEK_MANDIRI) {
                $nilaiAkhir = $this->penilaianSidangNewModel->getNilaiAkhirSidangProyekById($sidangSkripsi['id'])['nilai_akhir'];
            } else if ($jenisSkripsi == JENIS_SKRIPSI_REGULER) {
                $nilaiAkhir = $this->penilaianSidangNewModel->getAverageByIdSidang($sidangSkripsi['id_skripsi'], $sidangSkripsi['id'], $sidangSkripsi['dosen_penguji'], $idPembimbing1, $idPembimbing2, $idPembimbingAgama)['nilai_akhir'];
            }

            //cek apakah lulus atau tidak
            $statusKelulusan = null;
            if ($jenisSkripsi == JENIS_SKRIPSI_PROYEK_TIM || $jenisSkripsi == JENIS_SKRIPSI_PROYEK_MANDIRI) {
                $statusKelulusan = $nilaiAkhir >= BATAS_NILAI_KELULUSAN_PROYEK ? STATUS_SKRIPSI_LULUS : STATUS_SKRIPSI_TIDAKLULUS;
            } else if ($jenisSkripsi == JENIS_SKRIPSI_REGULER) {
                $statusKelulusan = $nilaiAkhir >= BATAS_NILAI_KELULUSAN_SKRIPSI_REGULER ? STATUS_SKRIPSI_LULUS : STATUS_SKRIPSI_TIDAKLULUS;
            }

            $grade = "-";
            if ($nilaiAkhir >= BATAS_BAWAH_NILAI_A) {
                $grade = 'A';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_AMIN) {
                $grade = 'A-';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_AB) {
                $grade = 'AB';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_BPLUS) {
                $grade = 'B+';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_B) {
                $grade = 'B';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_BMIN) {
                $grade = 'B-';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_BC) {
                $grade = 'BC';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_CPLUS) {
                $grade = 'C+';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_C) {
                $grade = 'C';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_CMIN) {
                $grade = 'C-';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_CD) {
                $grade = 'CD';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_DPLUS) {
                $grade = 'D+';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_D) {
                $grade = 'D';
            } elseif ($nilaiAkhir >= BATAS_BAWAH_NILAI_E) {
                $grade = 'E';
            }

            //update status kelulusan dan nilai di sidang skripsi
            $this->sidangSkripsiModel->update($sidangSkripsi['id'], [
                "total_nilai" => $nilaiAkhir,
                "status" => strtoupper($statusKelulusan),
                "grade" => $grade,
            ]);

            //update status kelulusan di skripsi
            $this->skripsiModel->update($sidangSkripsi['id_skripsi'], [
                "status" => $statusKelulusan
            ]);
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Beri Penilaian Sidang Berhasil", "text" => "Penilaian Sidang Skripsi telah berhasil disimpan"]);

        return redirect()->back();

    }


    public function penelitian()
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        $dataAkun = $this->dosenModel->find(session()->get("user_session")['id']);

        $daftarPenelitian = $this->penelitianDosenModel->getAllPenelitianByIdDosen($dataAkun['id']);

        $bidang = $this->bidangModel->getBidangByProdi($dataAkun['id_prodi']);



        $data = [

            'title' => 'Kelola Penelitian',

            'daftarPenelitian' => $daftarPenelitian,

            'bidang' => $bidang,

            'dataAkun' => $dataAkun,

        ];



        return view("dosen/penelitian", $data);

    }



    public function insertPenelitian()
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $id_dosen = $this->request->getPost("id_dosen");

        $id_bidang = $this->request->getPost("bidang");

        $judul = $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS);

        $deskripsi = $this->request->getPost("deskripsi", FILTER_SANITIZE_SPECIAL_CHARS);

        $jumlah_peneliti = $this->request->getPost("jumlah_peneliti", FILTER_SANITIZE_SPECIAL_CHARS);



        $this->penelitianDosenModel->insert([

            'id_dosen' => $id_dosen,

            'id_bidang' => $id_bidang,

            'judul' => $judul,

            'deskripsi' => $deskripsi,

            'jumlah_peneliti' => $jumlah_peneliti,

            'status' => 'TERSEDIA',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Tambah Penelitian Berhasil", "text" => "Penelitian berhasil ditambahkan!"]);

        return redirect()->to(base_url("dosen/penelitian"));

    }



    public function updatePenelitian($idPenelitian)
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $id_bidang = $this->request->getPost("bidang");

        $judul = $this->request->getPost("judul", FILTER_SANITIZE_SPECIAL_CHARS);

        $deskripsi = $this->request->getPost("deskripsi", FILTER_SANITIZE_SPECIAL_CHARS);

        $jumlah_peneliti = $this->request->getPost("jumlah_peneliti", FILTER_SANITIZE_SPECIAL_CHARS);

        $status = $this->request->getPost("status");



        $this->penelitianDosenModel->update($idPenelitian, [

            'id_bidang' => $id_bidang,

            'judul' => $judul,

            'deskripsi' => $deskripsi,

            'jumlah_peneliti' => $jumlah_peneliti,

            'status' => $status,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Penelitian Berhasil", "text" => "Penelitian berhasil diubah!"]);

        return redirect()->to(base_url("dosen/penelitian"));

    }



    public function deletePenelitian($idPenelitian)
    {

        //autentikasi

        if (!$this->authenticate(["dosen"])) {

            return redirect()->to(base_url("unauthorized.php"));

        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();

        }



        $this->penelitianDosenModel->delete($idPenelitian);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Hapus Penelitian Berhasil", "text" => "Penelitian berhasil dihapus!"]);

        return redirect()->to(base_url("dosen/penelitian"));

    }


    public function profile()
    {
        //autentikasi
        if (!$this->authenticate(["dosen", "dospem eksternal"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->dosenModel->getDosenInfo(session()->get("user_session")['id']);
        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Ubah Data Diri',
            'dataDiri' => $dataAkun,
        ];

        return view("dosen/profile", $data);
    }


    public function saveProfile()
    {
        //autentikasi
        if (!$this->authenticate(["dosen", "dospem eksternal"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dosenId = $this->request->getPost("id");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);
        $inisial = $this->request->getPost("inisial", FILTER_SANITIZE_SPECIAL_CHARS);
        $instansi = $this->request->getPost("instansi", FILTER_SANITIZE_SPECIAL_CHARS);


        $dataDosen = $this->dosenModel->getDosenInfo($dosenId);
        if ($dataDosen == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->dosenModel->isInitialAlreadyUsed($dosenId, $inisial)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Inisial telah digunakan!"]);
            return redirect()->to(base_url("Dosen/profile"));
        }

        if ($this->akunModel->isEmailAlreadyUsed($dataDosen['id_akun'], $email)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Email telah digunakan!"]);
            return redirect()->to(base_url("Dosen/profile"));
        }

        $this->akunModel->update($dataDosen['id_akun'], [
            "email" => $email,
        ]);

        $this->dosenModel->update($dosenId, [
            "nama" => $nama,
            "email" => $email,
            "inisial" => $inisial,
            "instansi" => $instansi,
        ]);


        session()->set("user_session", [
            'id' => $dataDosen['id'],
            'nama' => $nama,
            'email' => $email,
            'prodi' => $dataDosen['inisial_prodi'],
            'roles' => ['dosen'],
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Diri Berhasil", "text" => "Data diri berhasil diubah!"]);
        return redirect()->to(base_url("Dosen/profile"));
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