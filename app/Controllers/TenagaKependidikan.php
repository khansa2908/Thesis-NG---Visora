<?php



namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;


class TenagaKependidikan extends BaseController

{

    protected $mahasiswaModel;

    protected $dosenModel;

    protected $prodiModel;

    protected $pembimbingModel;

    protected $skripsiModel;

    protected $catatanBimbinganModel;

    protected $pengajuanPrasidangModel;

    protected $pengajuanSidangModel;

    protected $sidangSkripsiModel;

    protected $penilaianSidangModel;

    protected $semesterModel;

    protected $akunModel;

    protected $tendikModel;

    protected $semproModel;

    protected $seminarPrasidangModel;

    protected $persetujuanTamuModel;

    protected $aksesModel;

    protected $roleModel;

    protected $klienModel;

    protected $penilaianSidangNewModel;

    protected $penilaianSidangParamModel;

    protected $pengajuanHardcoverModel;

    protected $pembimbingEksternalModel;

    public function __construct()

    {

        $this->mahasiswaModel = new \App\Models\MahasiswaModel();

        $this->dosenModel = new \App\Models\DosenModel();

        $this->prodiModel = new \App\Models\ProgramStudiModel();

        $this->pembimbingModel = new \App\Models\PembimbingModel();

        $this->skripsiModel = new \App\Models\SkripsiModel();

        $this->catatanBimbinganModel = new \App\Models\CatatanBimbinganModel();

        $this->pengajuanPrasidangModel = new \App\Models\PengajuanPrasidangModel();

        $this->pengajuanSidangModel = new \App\Models\PengajuanSidangModel();

        $this->sidangSkripsiModel = new \App\Models\SidangSkripsiModel();

        $this->penilaianSidangModel = new \App\Models\PenilaianSidangModel();

        $this->semesterModel = new \App\Models\SemesterModel();

        $this->akunModel = new \App\Models\AkunModel();

        $this->tendikModel = new \App\Models\TenagaKependidikanModel();

        $this->semproModel = new \App\Models\SeminarProposalModel();

        $this->seminarPrasidangModel = new \App\Models\SeminarPrasidangModel();

        $this->persetujuanTamuModel = new \App\Models\PersetujuanTamuModel();

        $this->aksesModel = new \App\Models\AksesModel();

        $this->roleModel = new \App\Models\RoleModel();

        $this->klienModel = new \App\Models\KlienModel();

        $this->penilaianSidangNewModel = new \App\Models\PenilaianSidangNewModel();

        $this->penilaianSidangParamModel = new \App\Models\PenilaianSidangParamModel();

        $this->pembimbingEksternalModel = new \App\Models\PembimbingEksternalModel();

        $this->pengajuanHardcoverModel = new \App\Models\PengajuanHardcoverModel();
    }



    public function index()
    {
        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("fakultas/dashboard"));
        }



        return redirect()->to(base_url("home"));
    }



    public function pengajuanSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $pengajuanPenulisanSkripsi = $this->mahasiswaModel->getAllPengajuanSkripsi();

        $dari = $this->request->getGet("dari");

        $hingga = $this->request->getGet("hingga");



        if ($dari != null && $hingga != null) {

            $hingga = date_create($hingga);

            date_add($hingga, date_interval_create_from_date_string("1 days"));

            $hingga = date_format($hingga, 'Y-m-d');

            $pengajuanPenulisanSkripsi = $this->mahasiswaModel->getPengajuanSkripsiByDateRange($dari, $hingga);
        }

        $data = [

            'title' => "Pengajuan Penulisan Skripsi",

            'pengajuanPenulisanSkripsi' => $pengajuanPenulisanSkripsi,

        ];

        return view("tenagaKependidikan/pengajuan_skripsi", $data);
    }



    public function detailPengajuanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $mahasiswa = $this->mahasiswaModel->find($npm);

        if ($mahasiswa == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }



        $detailPengajuan = $this->mahasiswaModel->getPengajuanSkripsiByNpm($npm);

        if ($detailPengajuan == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [

            'title' => 'Detail Pengajuan Penulisan Skripsi',

            'detailPengajuan' => $detailPengajuan,

        ];



        return view("tenagaKependidikan/detail_pengajuan_skripsi", $data);
    }



    public function terimaPengajuanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->mahasiswaModel->update($npm, [

            'status_persetujuan_skripsi' => 'Disetujui',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan diterima", "text" => "Pengajuan Penyusunan skripsi $npm disetujui"]);

        return redirect()->to(base_url("TenagaKependidikan/pengajuanSkripsi"));
    }



    public function tolakPengajuanSkripsi($npm)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->mahasiswaModel->update($npm, [

            'status_persetujuan_skripsi' => 'Ditolak',

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan ditolak", "text" => "Pengajuan Penyusunan skripsi $npm ditolak"]);

        return redirect()->to(base_url("tenagaKependidikan/pengajuanSkripsi"));
    }



    public function pembimbing()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $mahasiswaTanpaPembimbing = $this->mahasiswaModel->getMahasiswaTanpaPembimbing();

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $mahasiswaDenganPembimbing = [];
        if ($currentSemester != null)
            $mahasiswaDenganPembimbing = $this->mahasiswaModel->getMahasiswaDenganPembimbingByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $data = [

            'title' => "Kelola Pembimbing",

            'mahasiswaTanpaPembimbing' => $mahasiswaTanpaPembimbing,

            'dosen' => $dosen,

            'mahasiswaDenganPembimbing' => $mahasiswaDenganPembimbing,

            'semesterList' => $allSemester,

            'currentSemester' => $currentSemester,

        ];



        return view("tenagaKependidikan/pembimbing", $data);
    }



    public function insertPembimbing()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("mahasiswa");

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);

        $idPembimbing1 = $this->request->getPost("pembimbing1");

        $idPembimbing2 = $this->request->getPost("pembimbing2");

        $idPembimbingAgama = $this->request->getPost("pembimbingAgama");



        $arrayInsert = array();

        array_push($arrayInsert, [

            'id_skripsi' => $lastSkripsi['id'],

            'id_dosen' => $idPembimbing1,

            'role' => 'Pembimbing Ilmu 1'

        ]);



        array_push($arrayInsert, [

            'id_skripsi' => $lastSkripsi['id'],

            'id_dosen' => $idPembimbing2,

            'role' => 'Pembimbing Ilmu 2'

        ]);



        array_push($arrayInsert, [

            'id_skripsi' => $lastSkripsi['id'],

            'id_dosen' => $idPembimbingAgama,

            'role' => 'Pembimbing Agama'

        ]);



        $this->pembimbingModel->insertBatch($arrayInsert);





        session()->setFlashdata("message", ["icon" => "success", "title" => "Input Pembimbing Berhasil", "text" => "Pembimbing untuk Mahasiswa $npm telah berhasil ditambahkan"]);

        return redirect()->to(base_url("TenagaKependidikan/pembimbing"));
    }



    public function downloadFormatPembimbing()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        redirect()->to(base_url("dosen/pembimbing"));

        return $this->response->download("folderResource/Format_Pembimbing_Skripsi_Mahasiswa.xlsx", null);
    }



    public function insertPembimbingBatch()
    {

        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }


        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }


        $validationRules = [
            'filePembimbing' => [
                'rules' => 'uploaded[filePembimbing]|mime_in[filePembimbing,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|ext_in[filePembimbing,xls,xlsx]|max_size[filePembimbing,10000]',
                'errors' => [
                    'uploaded' => 'Pilih File Pembimbing Skripsi Mahasiswa terlebih dahulu',
                    'mime_in' => 'File Pembimbing Skripsi Mahasiswa harus berupa Excel',
                    'ext_in' => 'File Pembimbing Skripsi Mahasiswa harus berekstensi .xls atau .xlsx',
                    'max_size' => 'Ukuran File Pembimbing Skripsi Mahasiswa tidak boleh lebih dari 10MB'
                ]
            ]
        ];


        if (!$this->validate($validationRules)) {
            $validation = \Config\Services::validation();
            session()->setFlashdata("message", ["icon" => "error", "title" => "Upload File Pembimbing Skripsi Gagal", "text" => $validation->getError("filePembimbing")]);
            return redirect()->to(base_url("TenagaKependidikan/pembimbing"))->withInput();
        }


        $file = $this->request->getFile("filePembimbing");
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
            // $arrayPembimbing = [];
            $npm = $worksheet->getCell("A$row")->getValue();
            $inisialPembimbing1 = $worksheet->getCell("B$row")->getValue();
            $inisialPembimbing2 = $worksheet->getCell("C$row")->getValue();
            $inisialPembimbingAgama = $worksheet->getCell("D$row")->getValue();

            if ($npm == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Kolom 'NPM' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($inisialPembimbing1 == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Kolom 'Inisial Dosen Pembimbing Ilmu 1' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($inisialPembimbing2 == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Kolom 'Inisial Dosen Pembimbing Ilmu 2' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($inisialPembimbingAgama == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Kolom 'Inisial Dosen Pembimbing Agama' tidak boleh kosong</td></tr>";
                $isAllSuccess = false;
                continue;
            }


            $mahasiswa = $this->mahasiswaModel->find($npm);
            $dosen_pembimbing1 = $this->dosenModel->getDosenByInisial($inisialPembimbing1);
            $dosen_pembimbing2 = $this->dosenModel->getDosenByInisial($inisialPembimbing2);
            $dosen_pembimbing_agama = $this->dosenModel->getDosenByInisial($inisialPembimbingAgama);


            if ($mahasiswa == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($dosen_pembimbing1 == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Inisial Dosen Pembimbing Ilmu 1 untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($dosen_pembimbing2 == null && $inisialPembimbing2 != "-") {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Inisial Dosen Pembimbing Ilmu 2 untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }

            if ($dosen_pembimbing_agama == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Inisial Dosen Pembimbing Agama untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }


            $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);
            //cek apakah skripsinya ada
            if ($lastSkripsi == null) {
                $htmlErrorMessage .= "<tr><td>$npm</td><td>Skripsi untuk NPM tersebut tidak ditemukan</td></tr>";
                $isAllSuccess = false;
                continue;
            }


            $resultPembimbing1 = $this->pembimbingModel->getWhere(["id_skripsi" => $lastSkripsi['id'], "role" => "Pembimbing Ilmu 1"])->getResultArray();

            $resultPembimbing2 = $this->pembimbingModel->getWhere(["id_skripsi" => $lastSkripsi['id'], "role" => "Pembimbing Ilmu 2"])->getResultArray();

            $resultPembimbingAgama = $this->pembimbingModel->getWhere(["id_skripsi" => $lastSkripsi['id'], "role" => "Pembimbing Agama"])->getResultArray();


            $isPembimbing1Exist = count($resultPembimbing1) != 0;
            $isPembimbing2Exist = count($resultPembimbing2) != 0;
            $isPembimbingAgamaExist = count($resultPembimbingAgama) != 0;

            $idPembimbing1 = null;
            $idPembimbing2 = null;
            $idPembimbingAgama = null;

            if ($isPembimbing1Exist) {
                $idPembimbing1 = $resultPembimbing1[0]['id'];

                $isCatatanBimbingan1Exist = count($this->catatanBimbinganModel->getWhere(['id_pembimbing' => $idPembimbing1])->getResultArray()) != 0;

                if ($isCatatanBimbingan1Exist) {
                    $htmlErrorMessage .= "<tr><td>$npm</td><td>Tidak dapat diperbarui karena Mahasiswa telah melakukan bimbingan dengan Pembimbing Ilmu 1</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            }

            if ($isPembimbing2Exist) {
                $idPembimbing2 = $resultPembimbing2[0]['id'];

                $isCatatanBimbingan2Exist = count($this->catatanBimbinganModel->getWhere(['id_pembimbing' => $idPembimbing2])->getResultArray()) != 0;

                if ($isCatatanBimbingan2Exist) {
                    $htmlErrorMessage .= "<tr><td>$npm</td><td>Tidak dapat diperbarui karena Mahasiswa telah melakukan bimbingan dengan Pembimbing Ilmu 2</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            }

            if ($isPembimbingAgamaExist) {
                $idPembimbingAgama = $resultPembimbingAgama[0]['id'];

                $isCatatanBimbinganAgamaExist = count($this->catatanBimbinganModel->getWhere(['id_pembimbing' => $idPembimbingAgama])->getResultArray()) != 0;

                if ($isCatatanBimbinganAgamaExist) {
                    $htmlErrorMessage .= "<tr><td>$npm</td><td>Tidak dapat diperbarui karena Mahasiswa telah melakukan bimbingan dengan Pembimbing Agama</td></tr>";
                    $isAllSuccess = false;
                    continue;
                }
            }

            $id_skripsi = $lastSkripsi['id'];

            // if ( $this->dosenModel->getDosenByInisial($worksheet->getCell("B$row")->getValue()) == null || $this->dosenModel->getDosenByInisial($worksheet->getCell("B$row")->getValue())['id_prodi'] != $mahasiswa['id_prodi'] ) {

            //     continue;

            // }

            // $dosen_pembimbing1 = $this->dosenModel->getDosenByInisial($worksheet->getCell("B$row")->getValue())['id'];

            // $dosen_pembimbing2 = null;

            // if ($worksheet->getCell("C$row")->getValue() != "-") {

            //     if ( $this->dosenModel->getDosenByInisial($worksheet->getCell("C$row")->getValue()) == null || $this->dosenModel->getDosenByInisial($worksheet->getCell("C$row")->getValue())['id_prodi'] != $mahasiswa['id_prodi'] ) {

            //         continue;

            //     }

            //     $dosen_pembimbing2 = $this->dosenModel->getDosenByInisial($worksheet->getCell("C$row")->getValue())['id'];

            // }

            // if ( $this->dosenModel->getDosenByInisial($worksheet->getCell("D$row")->getValue()) == null ) {

            //     continue;

            // }

            // $dosen_pembimbing_agama = $this->dosenModel->getDosenByInisial($worksheet->getCell("D$row")->getValue())['id'];



            if ($idPembimbing1 == null) {
                $this->pembimbingModel->insert([
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing1['id'],
                    'role' => 'Pembimbing Ilmu 1'
                ]);
            } else {
                $this->pembimbingModel->update($idPembimbing1, [
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing1['id'],
                    'role' => 'Pembimbing Ilmu 1'
                ]);
            }


            if ($idPembimbing2 == null) {
                $this->pembimbingModel->insert([
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing2 == null ? null : $dosen_pembimbing2['id'],
                    'role' => 'Pembimbing Ilmu 2'
                ]);
            } else {
                $this->pembimbingModel->update($idPembimbing2, [
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing2 == null ? null : $dosen_pembimbing2['id'],
                    'role' => 'Pembimbing Ilmu 2'
                ]);
            }


            if ($idPembimbingAgama == null) {
                $this->pembimbingModel->insert([
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing_agama['id'],
                    'role' => 'Pembimbing Agama'
                ]);
            } else {
                $this->pembimbingModel->update($idPembimbingAgama, [
                    'id_skripsi' => $id_skripsi,
                    'id_dosen' => $dosen_pembimbing_agama['id'],
                    'role' => 'Pembimbing Agama'
                ]);
            }
        }

        $htmlErrorMessage .= "</tbody>";
        $htmlErrorMessage .= "</table>";

        $htmlErrorMessage = str_replace("<", "&lt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace(">", "&gt;", $htmlErrorMessage);
        $htmlErrorMessage = str_replace("/", "&#47;", $htmlErrorMessage);

        if ($isAllSuccess) {
            session()->setFlashdata("message", ["icon" => "success", "title" => "Pembimbing Skripsi Berhasil Ditambahkan", "text" => "Pembimbing Skripsi berhasil ditambahkan sepenuhnya"]);
        } else {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Pembimbing Skripsi Gagal Ditambahkan", "html" => $htmlErrorMessage]);
        }

        return redirect()->to(base_url("TenagaKependidikan/pembimbing"));
    }



    public function updatePembimbing()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("npm");

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);

        $id_skripsi = $lastSkripsi['id'];

        $id1 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Ilmu 1')->first();

        $id2 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Ilmu 2')->first();

        $id3 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Agama')->first();

        $idPembimbing1 = $this->request->getPost("pembimbing1");

        $idPembimbing2 = $this->request->getPost("pembimbing2");

        $idPembimbingAgama = $this->request->getPost("pembimbingAgama");



        $this->pembimbingModel->update($id1, [

            'id_dosen' => $idPembimbing1,

        ]);



        if ($id2 != null) {

            $this->pembimbingModel->update($id2, [

                'id_dosen' => $idPembimbing2,

            ]);
        } else {

            $this->pembimbingModel->insert([

                'npm' => $npm,

                'id_dosen' => $idPembimbing2,

                'role' => 'Pembimbing Ilmu 2'

            ]);
        }



        $this->pembimbingModel->update($id3, [

            'id_dosen' => $idPembimbingAgama,

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Update Pembimbing Berhasil", "text" => "Pembimbing untuk Mahasiswa $npm telah berhasil diperbarui"]);

        return redirect()->to(base_url("TenagaKependidikan/pembimbing"));
    }



    public function deletePembimbing($npm)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }

        $lastSkripsi = $this->skripsiModel->getMahasiswaLastSkripsi($npm);

        $id_skripsi = $lastSkripsi['id'];

        $id1 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Ilmu 1')->first();

        $id2 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Ilmu 2')->first();

        $id3 = $this->pembimbingModel->select('id')->where("id_skripsi", $id_skripsi)->where("role", 'Pembimbing Agama')->first();



        $this->pembimbingModel->delete($id1);



        if ($id2 != null) {

            $this->pembimbingModel->delete($id2);
        }



        $this->pembimbingModel->delete($id3);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Update Pembimbing Berhasil", "text" => "Pembimbing untuk Mahasiswa $npm telah berhasil diperbarui"]);

        return redirect()->to(base_url("TenagaKependidikan/pembimbing"));
    }

    public function pembimbingEksternal()
    {
        $data = [
            'title' => 'Kelola Pembimbing Eksternal',
            'pembimbing' => $this->pembimbingEksternalModel->findAll()
        ];
        return view('TenagaKependidikan/pembimbing_eksternal', $data);
    }

    // 2. Logika Upload Dokumen
    public function uploadDokumen()
    {
        $id = $this->request->getPost('id_pembimbing');
        $jenisDokumen = $this->request->getPost('jenis_dokumen'); // ktp, cv, atau npwp
        $file = $this->request->getFile('file_dokumen');

        // Validasi sederhana
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ada.');
        }

        // Tentukan nama kolom database berdasarkan jenis dokumen
        $kolomDB = 'file_' . $jenisDokumen; // jadi file_ktp, file_cv, dll

        // Generate nama file random
        $namaFileBaru = $file->getRandomName();

        // Pindahkan file ke folder public/uploads/pembimbing
        $file->move('uploads/pembimbing', $namaFileBaru);

        // Update Database
        $this->pembimbingEksternalModel->update($id, [
            $kolomDB => $namaFileBaru
        ]);

        return redirect()->back()->with('success', 'Dokumen ' . strtoupper($jenisDokumen) . ' berhasil diunggah.');
    }

    // 3. Logika Hapus Dokumen
    public function hapusDokumen($id, $jenis)
    {
        // Ambil data user dulu untuk tahu nama filenya
        $user = $this->pembimbingEksternalModel->find($id);
        $kolomDB = 'file_' . $jenis;
        $namaFile = $user[$kolomDB];

        // Hapus file fisik jika ada
        if ($namaFile && file_exists('uploads/pembimbing/' . $namaFile)) {
            unlink('uploads/pembimbing/' . $namaFile);
        }

        // Set database jadi NULL
        $this->pembimbingEksternalModel->update($id, [
            $kolomDB => null
        ]);

        return redirect()->back()->with('success', 'Dokumen ' . strtoupper($jenis) . ' berhasil dihapus.');
    }

    // -----------------------------------------------------------------------
    // LOGIKA PERSETUJUAN AKUN TAMU -> JADI PEMBIMBING EKSTERNAL
    // -----------------------------------------------------------------------

    // public function jadikanPembimbingEksternal($id_user_tamu)
    // {
    //     // 1. Panggil Model User (Tempat data tamu berada)
    //     // Pastikan kamu punya model ini. Biasanya UserModel atau UsersModel
    //     $userModel = new \App\Models\UserModel();

    //     // 2. Ambil data Tamu berdasarkan ID
    //     $tamu = $userModel->find($id_user_tamu);

    //     if (!$tamu) {
    //         return redirect()->back()->with('error', 'Data tamu tidak ditemukan.');
    //     }

    //     // 3. Siapkan data untuk dimasukkan ke tabel 'pembimbing_eksternal'
    //     // PERHATIKAN: Bagian kiri adalah nama kolom di tabel pembimbing_eksternal
    //     // Bagian kanan ($tamu['...']) adalah nama kolom di tabel users (sesuaikan!)
    //     $dataPembimbing = [
    //         'nama_lengkap' => $tamu['fullname'],   // Sesuaikan dg kolom nama di tabel users
    //         'instansi'     => $tamu['instansi'],   // Pastikan tamu punya kolom instansi saat daftar
    //         'email'        => $tamu['email'],
    //         'kontak'       => $tamu['no_hp'],      // Sesuaikan dg kolom no_hp di tabel users
    //         // File dokumen dibiarkan NULL dulu, karena nanti diupload manual
    //     ];

    //     // 4. Masukkan ke tabel Pembimbing Eksternal
    //     $this->pembimbingModel->insert($dataPembimbing);

    //     // 5. Update Role User tersebut jadi 'dospem eksternal' (Agar bisa login sebagai dospem)
    //     $userModel->update($id_user_tamu, [
    //         'role' => 'dospem eksternal',  // Atau id_role sesuai sistem kamu
    //         'status_akun' => 'active'      // Aktifkan akunnya
    //     ]);

    //     return redirect()->to('TenagaKependidikan/pembimbingEksternal')
    //         ->with('success', 'Akun berhasil disetujui dan masuk ke daftar Pembimbing Eksternal.');
    // }

    public function jadikanPembimbingEksternal($id_user_tamu)
    {
        // 1. Ganti UserModel dengan PersetujuanTamuModel
        // Karena data 'Tamu' ada di tabel persetujuan_tamu
        
        // 2. Ambil data Tamu berdasarkan ID
        $tamu = $this->persetujuanTamuModel->find($id_user_tamu);

        if (!$tamu) {
            return redirect()->back()->with('error', 'Data tamu tidak ditemukan.');
        }

        // 3. Siapkan data untuk dimasukkan ke tabel 'pembimbing_eksternal'
        // Perbaikan: Sesuaikan nama kolom dengan database kamu ($tamu['nama'], bukan 'fullname')
        $dataPembimbing = [
            'nama_lengkap' => $tamu['nama'],      // Di DB kamu kolomnya 'nama'
            'instansi'     => $tamu['instansi'], 
            'email'        => $tamu['email'],
            'kontak'       => isset($tamu['no_hp']) ? $tamu['no_hp'] : '-', // Cek jika ada no_hp
            // File dokumen dibiarkan NULL dulu
        ];

        // 4. Masukkan ke tabel Pembimbing Eksternal
        // Gunakan model yang sudah di-load di __construct
        $this->pembimbingEksternalModel->insert($dataPembimbing);

        // 5. Update Akun Tamu (Pindahkan ke tabel Akun & Dosen jika perlu)
        // Logika ini sebenarnya duplikat dengan fungsi 'setujuiAkunTamu' di bawah.
        // Tapi agar error ini hilang, kita redirect saja ke fungsi yang lebih lengkap:
        
        return $this->setujuiAkunTamu($id_user_tamu, 'dospem');
    }


    public function pengajuanPrasidang()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $daftarPengajuan = [];
        if ($currentSemester != null)
            $daftarPengajuan = $this->pengajuanPrasidangModel->getAllPengajuanPrasidangByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Pengajuan Seminar Prasidang',

            'daftarPengajuan' => $daftarPengajuan,

            'semesterList' => $allSemester,

        ];

        return view("tenagaKependidikan/pengajuan_pra_sidang", $data);
    }



    public function detailPengajuanPrasidang($idPengajuanPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $detailPengajuan = $this->pengajuanPrasidangModel->getDetailPengajuanById($idPengajuanPrasidang);

        if ($detailPengajuan == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [

            'title' => 'Detail Pengajuan Prasidang',

            'detailPengajuan' => $detailPengajuan,

        ];



        return view("tenagaKependidikan/detail_pengajuan_pra_sidang", $data);
    }



    public function setujuiPengajuanPrasidang($idPengajuanPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $this->pengajuanPrasidangModel->update($idPengajuanPrasidang, [

            'status' => 'DISETUJUI'

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan Seminar Prasidang Diterima", "text" => "Pengajuan Seminar Prasidang berhasil diterima!"]);

        return redirect()->to(base_url("TenagaKependidikan/pengajuanPrasidang"));
    }



    public function tolakPengajuanPrasidang($idPengajuanPrasidang)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("npm");

        $this->pengajuanPrasidangModel->update($idPengajuanPrasidang, [

            'status' => 'DITOLAK'

        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan Seminar Prasidang Ditolak", "text" => "Pengajuan Seminar Prasidang telah ditolak!"]);

        return redirect()->to(base_url("TenagaKependidikan/pengajuanPrasidang"));
    }



    public function pengajuanSidangSkripsi()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $pengajuanSidangSkripsi = [];
        if ($currentSemester != null)
            $pengajuanSidangSkripsi = $this->pengajuanSidangModel->getAllPengajuanSidangByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            "title" => "Pengajuan Sidang Skripsi",

            "pengajuanSidangSkripsi" => $pengajuanSidangSkripsi,

            "semesterList" => $allSemester,

        ];



        return view("tenagaKependidikan/pengajuan_sidang_skripsi", $data);
    }



    public function detailPengajuanSidangSkripsi($idPengajuanSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        $detailPengajuan = $this->pengajuanSidangModel->getDetailPengajuanById($idPengajuanSidangSkripsi);



        //dd($detailPengajuan);

        if ($detailPengajuan == null) {

            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [

            'title' => 'Detail Pengajuan Sidang Skripsi',

            'detailPengajuan' => $detailPengajuan,

        ];



        return view("tenagaKependidikan/detail_pengajuan_sidang_skripsi", $data);
    }



    public function tolakPengajuanSidangSkripsi($idPengajuanSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $npm = $this->request->getPost("npm");

        $pengajuanSidang = $this->pengajuanSidangModel->find($idPengajuanSidangSkripsi);

        if ($pengajuanSidang == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Pengajuan Sidang Skripsi Tidak Ditemukan", "text" => "Pengajuan Sidang Skripsi Tidak Ditemukan"]);

            return redirect()->to(base_url("TenagaKependidikan/pengajuansidangskripsi"));
        }



        $this->pengajuanSidangModel->update($idPengajuanSidangSkripsi, [

            'status' => 'DITOLAK',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan Ditolak", "text" => "Pengajuan Sidang Skripsi telah Ditolak"]);

        return redirect()->to(base_url("TenagaKependidikan/pengajuansidangskripsi"));
    }



    public function setujuiPengajuanSidangSkripsi($idPengajuanSidangSkripsi)
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }



        //buat mencegah access langsung dari link

        if ($this->request->getMethod() != 'post') {

            return redirect()->back();
        }



        $pengajuanSidang = $this->pengajuanSidangModel->find($idPengajuanSidangSkripsi);

        if ($pengajuanSidang == null) {

            session()->setFlashdata("message", ["icon" => "error", "title" => "Pengajuan Sidang Skripsi Tidak Ditemukan", "text" => "Pengajuan Sidang Skripsi Tidak Ditemukan"]);

            return redirect()->to(base_url("TenagaKependidikan/pengajuansidangskripsi"));
        }



        $this->pengajuanSidangModel->update($idPengajuanSidangSkripsi, [

            'status' => 'DISETUJUI',

        ]);



        session()->setFlashdata("message", ["icon" => "success", "title" => "Pengajuan Disetujui", "text" => "Pengajuan Sidang Skripsi telah Disetujui"]);

        return redirect()->to(base_url("TenagaKependidikan/pengajuansidangskripsi"));
    }











    public function penilaianSidang()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $mahasiswa = [];
        if ($currentSemester != null)
            $mahasiswa = $this->sidangSkripsiModel->getAllSidangSkripsiByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $data = [

            'title' => 'Penilaian Sidang Mahasiswa',

            'mahasiswa' => $mahasiswa,

            'semesterList' => $allSemester,

        ];

        return view("tenagaKependidikan/penilaian_sidang", $data);
    }



    public function hasilSidangSkripsi($idSkripsi)
    {

        //autentikasi
        if (!$this->authenticate(["tendik"])) {
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
        return view("tenagaKependidikan/hasil_sidang_skripsi", $data);
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
        return view("tenagaKependidikan/hasil_sidang_skripsi_non_proyek", $data);
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
        return view("tenagaKependidikan/hasil_sidang_skripsi_proyek", $data);
    }


    public function cetakBeritaAcara($id_skripsi)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
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

        $prodi = $this->prodiModel->find($mahasiswa['id_prodi']);
        $sidangSkripsi = $this->sidangSkripsiModel->getWhere(['id_skripsi' => $id_skripsi])->getResultArray()[0];

        $tahunAkademik = explode("-", $lastSkripsi['kode_semester'])[0];
        $semester = explode("-", $lastSkripsi['kode_semester'])[1];

        if ($tahunAkademik <= BATAS_TAHUN_AKADEMIK_LAMA && $semester <= BATAS_SEMESTER_LAMA) {
            return $this->cetakBeritaAcaraOld($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi);
        }

        if ($lastSkripsi['jenis'] == JENIS_SKRIPSI_PROYEK_MANDIRI || $lastSkripsi['jenis'] == JENIS_SKRIPSI_PROYEK_TIM) {
            return $this->cetakBeritaAcaraProyek($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi);
        } else {
            return $this->cetakBeritaAcaraNonProyek($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi);
        }
    }

    private function cetakBeritaAcaraOld($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi)
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

        return view("tenagaKependidikan/berita_acara", $data);
    }


    private function cetakBeritaAcaraProyek($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi)
    {
        $pembimbingIlmu = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $this->dosenModel->find($sidangSkripsi['dosen_penguji']);
        $klien = $this->klienModel->find($sidangSkripsi['klien']);

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

        return view("tenagaKependidikan/berita_acara_new", $data);
    }

    private function cetakBeritaAcaraNonProyek($lastSkripsi, $sidangSkripsi, $mahasiswa, $prodi)
    {
        $pembimbingIlmu1 = $this->pembimbingModel->getPembimbingIlmu1ByIdSkripsi($lastSkripsi['id']);
        $pembimbingIlmu2 = $this->pembimbingModel->getPembimbingIlmu2ByIdSkripsi($lastSkripsi['id']);
        $pembimbingAgama = $this->pembimbingModel->getPembimbingAgamaByIdSkripsi($lastSkripsi['id']);
        $penguji = $sidangSkripsi['dosen_penguji'] == null ? null : $this->dosenModel->find($sidangSkripsi['dosen_penguji']);

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

        return view("tenagaKependidikan/berita_acara_new", $data);
    }


    public function cetakBeritaAcaraBulk()
    {

        //autentikasi

        if (!$this->authenticate(["tendik"])) {

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

        return view("tenagaKependidikan/berita_acara", $data);
    }

    private function printBeritaAcaraBulkNew($listSkripsi)
    {
        $arrBeritaAcara = [];

        foreach ($listSkripsi as $skripsi) {
            $mahasiswa = $this->mahasiswaModel->find($skripsi['npm']);
            $listSidangSkripsi = $this->sidangSkripsiModel->getWhere(["id_skripsi" => $skripsi['id']])->getResultArray();

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

    public function dosen()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }
        $listDosen = $this->dosenModel->getAllDosenInfo();
        $listDosenEksternal = $this->dosenModel->getAllDosenEksternalInfo();
        $listProdi = $this->prodiModel->findAll();

        $data = [
            "title" => "Kelola Dosen FTI",
            "listDosen" => $listDosen,
            "listDosenEksternal" => $listDosenEksternal,
            "listProdi" => $listProdi,
        ];

        return view("tenagaKependidikan/dosen", $data);
    }

    public function updateDosen()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
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
            return redirect()->to(base_url("TenagaKependidikan/dosen"));
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
        return redirect()->to(base_url("TenagaKependidikan/dosen"));
    }

    public function updateDosenEksternal()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
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
        $instansi = $this->request->getPost("instansi", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->dosenModel->isInitialAlreadyUsed($dosenId, $inisial)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Dosen Gagal", "text" => "Inisial telah digunakan!"]);
            return redirect()->to(base_url("TenagaKependidikan/dosen"));
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
            "instansi" => $instansi,
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Dosen Berhasil", "text" => "Data dosen berhasil diubah!"]);
        return redirect()->to(base_url("TenagaKependidikan/dosen"));
    }

    public function inactivatedDosenEksternal($idDosen)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dataDosen = $this->dosenModel->getDosenInfo($idDosen);
        if ($dataDosen == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataDosen['id_akun'], [
            "status" => "INACTIVE",
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Nonaktifkan Akun Dosen Berhasil", "text" => "Akun dosen berhasil dinonaktifkan!"]);
        return redirect()->to(base_url("TenagaKependidikan/dosen"));
    }

    public function reactivatedDosenEksternal($idDosen)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dataDosen = $this->dosenModel->getDosenInfo($idDosen);
        if ($dataDosen == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataDosen['id_akun'], [
            "status" => "ACTIVE",
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Aktifkan Akun Dosen Berhasil", "text" => "Akun dosen berhasil diaktifkan!"]);
        return redirect()->to(base_url("TenagaKependidikan/dosen"));
    }


    public function klien()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $listKlien = $this->klienModel->getAllKlienInfo();

        $data = [
            "title" => "Kelola Klien",
            "listKlien" => $listKlien,
        ];

        return view("tenagaKependidikan/klien", $data);
    }

    public function updateKlien()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $klienId = $this->request->getPost("idKlien");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);
        $inisial = $this->request->getPost("inisial", FILTER_SANITIZE_SPECIAL_CHARS);
        $instansi = $this->request->getPost("instansi", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($this->klienModel->isInitialAlreadyUsed($klienId, $inisial)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Klien Gagal", "text" => "Inisial telah digunakan!"]);
            return redirect()->to(base_url("TenagaKependidikan/klien"));
        }

        $dataKlien = $this->klienModel->getKlienInfo($klienId);
        if ($dataKlien == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataKlien['id_akun'], [
            "email" => $email,
        ]);

        $this->klienModel->update($klienId, [
            "nama" => $nama,
            "email" => $email,
            "inisial" => $inisial,
            "instansi" => $instansi,
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah data Klien Berhasil", "text" => "Data klien berhasil diubah!"]);
        return redirect()->to(base_url("TenagaKependidikan/klien"));
    }

    public function inactivatedKlien($idKlien)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dataKlien = $this->klienModel->getKlienInfo($idKlien);
        if ($dataKlien == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataKlien['id_akun'], [
            "status" => "INACTIVE",
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Nonaktifkan Akun Klien Berhasil", "text" => "Akun klien berhasil dinonaktifkan!"]);
        return redirect()->to(base_url("TenagaKependidikan/klien"));
    }

    public function reactivatedKlien($idKlien)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $dataKlien = $this->klienModel->getKlienInfo($idKlien);
        if ($dataKlien == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->akunModel->update($dataKlien['id_akun'], [
            "status" => "ACTIVE",
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Aktifkan Akun Klien Berhasil", "text" => "Akun klien berhasil diaktifkan!"]);
        return redirect()->to(base_url("TenagaKependidikan/klien"));
    }

    public function profile()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $dataAkun = $this->tendikModel->getTendikInfo(session()->get("user_session")['id']);
        if ($dataAkun == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Ubah Data Diri',
            'dataDiri' => $dataAkun,
        ];

        return view("tenagaKependidikan/profile", $data);
    }


    public function saveProfile()
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $idTendik = $this->request->getPost("id");
        $nama = $this->request->getPost("nama", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $this->request->getPost("email", FILTER_SANITIZE_SPECIAL_CHARS);

        $dataTendik = $this->tendikModel->getTendikInfo($idTendik);
        if ($dataTendik == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->akunModel->isEmailAlreadyUsed($dataTendik['id_akun'], $email)) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Ubah Data Diri Gagal", "text" => "Email telah digunakan!"]);
            return redirect()->to(base_url("TenagaKependidikan/profile"));
        }

        $this->akunModel->update($dataTendik['id_akun'], [
            "email" => $email,
        ]);

        $this->tendikModel->update($idTendik, [
            "nama" => $nama,
            "email" => $email,
        ]);


        session()->set("user_session", [
            'id' => $dataTendik['id'],
            'nama' => $nama,
            'email' => $email,
            'prodi' => "-",
            'roles' => ['tendik'],
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Ubah Data Diri Berhasil", "text" => "Data diri berhasil diubah!"]);
        return redirect()->to(base_url("TenagaKependidikan/profile"));
    }



    public function seminarProposal()
    {

        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $seminarProposal = [];
        if ($currentSemester != null)
            $seminarProposal = $this->semproModel->getSemproByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $data = [

            "title" => "Seminar Proposal Mahasiswa FTI",
            "seminarProposal" => $seminarProposal,
            "dosen" => $dosen,
            "semesterList" => $allSemester,
            "currentSemester" => $currentSemester,
        ];

        return view("tenagaKependidikan/seminar_proposal", $data);
    }

    // --- FUNCTION BARU UNTUK CETAK BERITA ACARA SEMPRO ---
    public function cetakBeritaAcaraSempro()
    {
        // 1. Autentikasi Tendik
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 2. Pastikan akses via POST (dari Form Modal)
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        // 3. Ambil Data dari Input Modal
        $idSeminar = $this->request->getPost('id_seminar');
        $nomorBA   = $this->request->getPost('nomor_berita_acara');

        // 4. Update Nomor Berita Acara ke Database
        // Menggunakan semproModel yang sudah didefinisikan di __construct
        $this->semproModel->update($idSeminar, [
            'nomor_berita_acara' => $nomorBA
        ]);

        // 5. Ambil Data Lengkap Seminar (Join Mahasiswa, Dosen, dll)
        // Fungsi getDetailSeminar sudah kita buat di SeminarProposalModel sebelumnya
        $dataSeminar = $this->semproModel->getDetailSeminar($idSeminar);

        if ($dataSeminar == null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 6. Siapkan data untuk dikirim ke View Cetak
        $data = [
            'title'       => 'Berita Acara Seminar Proposal',
            'seminar'     => $dataSeminar,
            'nomor_surat' => $nomorBA,
            // Helper tanggal indo jika diperlukan
            'tanggal_indo' => date('d-m-Y', strtotime($dataSeminar['tanggal']))
        ];

        // 7. Tampilkan View Cetak (PDF Preview)
        // Kita akan membuat view baru bernama 'berita_acara_sempro'
        return view("tenagaKependidikan/berita_acara_sempro", $data);
    }


    public function sidangSkripsi()
    {

        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $sidangSkripsi = [];
        if ($currentSemester != null)
            $sidangSkripsi = $this->sidangSkripsiModel->getSidangSkripsiByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $data = [

            "title" => "Sidang Skripsi FTI",

            "sidangSkripsi" => $sidangSkripsi,

            "dosen" => $dosen,

            "semesterList" => $allSemester,

            "currentSemester" => $currentSemester,

        ];



        return view("tenagaKependidikan/sidang_skripsi", $data);
    }

    // --- FITUR BARU: UPDATE & CETAK BA SIDANG ---
    public function updateAndCetakBASidang()
    {
        // 1. Autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 2. Cek Method POST
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        // 3. Ambil Data
        $idSkripsi = $this->request->getPost('id_skripsi');
        $nomorBA   = $this->request->getPost('nomor_berita_acara');

        // 4. Update Nomor BA ke Database
        // Kita update berdasarkan id_skripsi karena di view pemicunya adalah id_skripsi
        $this->sidangSkripsiModel->where('id_skripsi', $idSkripsi)
            ->set(['nomor_berita_acara' => $nomorBA])
            ->update();

        // 5. Panggil fungsi cetak lama (Reuse Code)
        return $this->cetakBeritaAcara($idSkripsi);
    }

    public function seminarPrasidang()
    {

        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $kodeSemester = $this->request->getGet("semester");

        $currentSemester = $this->semesterModel->getActiveSemester();
        if ($kodeSemester != null)
            $currentSemester = $this->semesterModel->find($kodeSemester);

        $seminarPrasidang = [];
        if ($currentSemester != null)
            $seminarPrasidang = $this->seminarPrasidangModel->getPrasidangByKodeSemester($currentSemester['kode']);

        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->get()->getResultArray();

        $dosen = $this->dosenModel->findAll();

        $data = [

            "title" => "Seminar Prasidang Mahasiswa FTI",

            "seminarPrasidang" => $seminarPrasidang,

            "dosen" => $dosen,

            "semesterList" => $allSemester,

            "currentSemester" => $currentSemester,

        ];



        return view("tenagaKependidikan/seminar_prasidang", $data);
    }


    public function cetakSuratTugas()
    {
        $npm        = $this->request->getPost('npm_mhs');
        $no_surat   = $this->request->getPost('no_surat');
        $tgl_input  = $this->request->getPost('tanggal_surat');

        $db = \Config\Database::connect();
        $builder = $db->table('skripsi');

        $builder->select('
            mahasiswa.nama as nama_mhs,
            mahasiswa.npm,
            program_studi.inisial as inisial_prodi,
            skripsi.judul as judul,
            d1.nama as pembimbing_ilmu1,
            d2.nama as pembimbing_ilmu2,
            da.nama as pembimbing_agama
        ');

        $builder->join('mahasiswa', 'mahasiswa.npm = skripsi.npm');
        $builder->join('program_studi', 'program_studi.id = mahasiswa.id_prodi', 'left');

        $builder->join('pembimbing as pb1', "pb1.id_skripsi = skripsi.id AND pb1.role = 'Pembimbing Ilmu 1'", 'left');
        $builder->join('dosen as d1', 'd1.id = pb1.id_dosen', 'left');

        $builder->join('pembimbing as pb2', "pb2.id_skripsi = skripsi.id AND pb2.role = 'Pembimbing Ilmu 2'", 'left');
        $builder->join('dosen as d2', 'd2.id = pb2.id_dosen', 'left');

        $builder->join('pembimbing as pba', "pba.id_skripsi = skripsi.id AND pba.role = 'Pembimbing Agama'", 'left');
        $builder->join('dosen as da', 'da.id = pba.id_dosen', 'left');

        $builder->where('mahasiswa.npm', $npm);
        $builder->orderBy('skripsi.id', 'DESC');

        $hasil = $builder->get()->getRowArray();

        if (!$hasil) {
            return redirect()->back()->with('message', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data tidak ditemukan.']);
        }

        // --- LOGIKA DINAMIS KAPRODI & JABATAN ---
        // Default (TI)
        $namaKaprodi = 'Elan Suherlan, M.Si.';
        $jabatanKaprodi = 'Kepala Program Studi Teknik Informatika';

        // Cek jika prodi adalah PdSI
        if (isset($hasil['inisial_prodi']) && $hasil['inisial_prodi'] == 'PdSI') {
            $namaKaprodi = 'Danang Dwijo Kangko, S.Hum., M.P.';
            $jabatanKaprodi = 'Kepala Program Studi Perpustakaan dan Sains Informasi';
        }
        // ----------------------------------------

        if (empty($hasil['pembimbing_ilmu2'])) $hasil['pembimbing_ilmu2'] = '-';
        if (empty($hasil['pembimbing_agama'])) $hasil['pembimbing_agama'] = '-';

        $bulanIndo = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $pecahTgl = explode('-', $tgl_input);
        $tanggal_fix = $pecahTgl[2] . ' ' . $bulanIndo[$pecahTgl[1]] . ' ' . $pecahTgl[0];

        $pathLogo = FCPATH . 'assets/img/fti.jpg';
        $logoBase64 = '';
        if (file_exists($pathLogo)) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($pathLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        $dataPDF = [
            'no_surat'      => $no_surat,
            'tanggal_surat' => $tanggal_fix,
            'kaprodi'       => $namaKaprodi,
            'jabatan_kaprodi' => $jabatanKaprodi, // Kirim Jabatan ke View
            'logo_src'      => $logoBase64,
            'data'          => $hasil
        ];

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $html = view('tenagakependidikan/surat_tugas_pdf', $dataPDF);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Surat_Tugas_" . $npm . ".pdf", ["Attachment" => 0]);
    }


    public function cetakUndanganSidang()
    {
        $id_jadwal  = $this->request->getPost('id_jadwal');
        $no_surat   = $this->request->getPost('no_surat');
        $tgl_input  = $this->request->getPost('tanggal_surat');

        $db = \Config\Database::connect();

        $builder = $db->table('sidang_skripsi');

        $builder->select('
            mahasiswa.nama as nama_mhs,
            mahasiswa.npm,
            program_studi.nama as nama_prodi,
            program_studi.inisial as inisial_prodi,
            skripsi.id as id_skripsi,
            skripsi.judul as judul,
            sidang_skripsi.tanggal as tanggal_sidang,
            sidang_skripsi.ruangan as ruang,
            p1.nama as ketua_penguji,
            p2.nama as anggota_penguji,
            klien.nama as nama_klien  // Kita ambil nama klien saja
        ');

        $builder->join('skripsi', 'skripsi.id = sidang_skripsi.id_skripsi');
        $builder->join('mahasiswa', 'mahasiswa.npm = skripsi.npm');
        $builder->join('program_studi', 'program_studi.id = mahasiswa.id_prodi', 'left');
        $builder->join('dosen as p1', 'p1.id = sidang_skripsi.ketua_sidang', 'left');
        $builder->join('dosen as p2', 'p2.id = sidang_skripsi.dosen_penguji', 'left');
        $builder->join('klien', 'klien.id = sidang_skripsi.klien', 'left'); // Join Klien

        $builder->where('sidang_skripsi.id', $id_jadwal);

        $hasil = $builder->get()->getRowArray();

        if (!$hasil) {
            return redirect()->back()->with('message', ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Data sidang tidak ditemukan.']);
        }

        // --- AMBIL PEMBIMBING ---
        $pembimbingModel = $db->table('pembimbing');

        $pb1 = $pembimbingModel->select('dosen.nama')
            ->join('dosen', 'dosen.id = pembimbing.id_dosen')
            ->where('pembimbing.id_skripsi', $hasil['id_skripsi'])
            ->where('pembimbing.role', 'Pembimbing Ilmu 1')
            ->get()->getRowArray();

        $pb2 = $pembimbingModel->select('dosen.nama')
            ->join('dosen', 'dosen.id = pembimbing.id_dosen')
            ->where('pembimbing.id_skripsi', $hasil['id_skripsi'])
            ->where('pembimbing.role', 'Pembimbing Ilmu 2')
            ->get()->getRowArray();

        $pba = $pembimbingModel->select('dosen.nama')
            ->join('dosen', 'dosen.id = pembimbing.id_dosen')
            ->where('pembimbing.id_skripsi', $hasil['id_skripsi'])
            ->where('pembimbing.role', 'Pembimbing Agama')
            ->get()->getRowArray();

        $hasil['pembimbing1'] = $pb1 ? $pb1['nama'] : '-';
        $hasil['pembimbing2'] = $pb2 ? $pb2['nama'] : '-';
        $hasil['pembimbing_agama'] = $pba ? $pba['nama'] : '-';

        // --- KAPRODI ---
        $namaKaprodi = 'Elan Suherlan, M.Si.';
        $jabatanKaprodi = 'Kepala Program Studi Teknik Informatika';

        if (isset($hasil['inisial_prodi']) && $hasil['inisial_prodi'] == 'PdSI') {
            $namaKaprodi = 'Danang Dwijo Kangko, S.Hum., M.P.';
            $jabatanKaprodi = 'Kepala Program Studi Perpustakaan dan Sains Informasi';
        }

        // --- FORMAT TANGGAL ---
        $bulanIndo = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];

        $pecahTglSurat = explode('-', $tgl_input);
        $tgl_surat_indo = $pecahTglSurat[2] . ' ' . $bulanIndo[$pecahTglSurat[1]] . ' ' . $pecahTglSurat[0];

        $timestamp = strtotime($hasil['tanggal_sidang']);
        $tgl_sidang_indo = date('d', $timestamp) . '-' . date('m', $timestamp) . '-' . date('Y', $timestamp); // Format 22-07-2025 sesuai contoh
        $jam_mulai = date('H:i', $timestamp);

        $daftarHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $hari_sidang = $daftarHari[date('l', $timestamp)];

        // --- LOGO ---
        $pathLogo = FCPATH . 'assets/img/fti.jpg';
        $logoBase64 = '';
        if (file_exists($pathLogo)) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $dataImg = file_get_contents($pathLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
        }

        $dataPDF = [
            'no_surat'      => $no_surat,
            'tanggal_surat' => $tgl_surat_indo,
            'kaprodi'       => $namaKaprodi,
            'jabatan_kaprodi' => $jabatanKaprodi,
            'logo_src'      => $logoBase64,
            'data'          => $hasil,
            'hari_sidang'   => $hari_sidang,
            'tgl_sidang'    => $tgl_sidang_indo,
            'jam_mulai'     => $jam_mulai
        ];

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $html = view('tenagakependidikan/surat_undangan_sidang_pdf', $dataPDF);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Undangan_Sidang_" . $hasil['npm'] . ".pdf", ["Attachment" => 0]);
    }


    // ---------------------------------------------------------
    // 1. FITUR HAPUS JADWAL SIDANG
    // ---------------------------------------------------------
    public function deleteJadwalSidang($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sidang_skripsi');

        $builder->where('id', $id);
        $builder->delete();

        return redirect()->back()->with('message', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jadwal sidang berhasil dihapus.'
        ]);
    }

    // ---------------------------------------------------------
    // 2. FITUR AMBIL DATA UNTUK MODAL EDIT (AJAX)
    // ---------------------------------------------------------
    public function getJsonJadwalSidang($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sidang_skripsi');
        $data = $builder->where('id', $id)->get()->getRowArray();

        // Pecah Datetime (YYYY-MM-DD HH:MM:SS) menjadi Tanggal & Jam terpisah
        // Supaya mudah masuk ke input type="date" dan type="time"
        if ($data) {
            $timestamp = strtotime($data['tanggal']);
            $data['tanggal_saja'] = date('Y-m-d', $timestamp);
            $data['jam_saja']     = date('H:i', $timestamp);
        }

        return $this->response->setJSON($data);
    }

    // ---------------------------------------------------------
    // 3. FITUR SIMPAN PERUBAHAN (UPDATE)
    // ---------------------------------------------------------
    public function updateJadwalSidang()
    {
        $id = $this->request->getPost('id_jadwal');

        // Ambil inputan
        $tanggal = $this->request->getPost('tanggal'); // YYYY-MM-DD
        $jam     = $this->request->getPost('jam');     // HH:MM
        $ruangan = $this->request->getPost('ruangan');

        // Gabungkan Tanggal & Jam jadi format DATETIME (YYYY-MM-DD HH:MM:SS)
        $datetime_lengkap = $tanggal . ' ' . $jam . ':00';

        $dataUpdate = [
            'tanggal' => $datetime_lengkap,
            'ruangan' => $ruangan
            // Kalau mau bisa edit penguji juga, tambahkan di sini
            // 'ketua_sidang' => ..., 
            // 'dosen_penguji' => ...
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('sidang_skripsi');
        $builder->where('id', $id);
        $builder->update($dataUpdate);

        return redirect()->back()->with('message', [
            'icon' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jadwal sidang berhasil diperbarui.'
        ]);
    }



    // --- [FIX] FITUR PERSETUJUAN AKUN TAMU (YANG HILANG) ---
    public function persetujuanAkunTamu()
    {
        // 1. Autentikasi Tendik
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 2. Ambil semua data pengajuan akun tamu
        // Menggunakan model yang sudah ada di constructor
        $dataPersetujuan = $this->persetujuanTamuModel->findAll();

        $data = [
            'title' => 'Persetujuan Akun Tamu',
            // [FIX] Ganti nama key array jadi 'listAkunTamu' (sesuai error di View)
            'listAkunTamu' => $dataPersetujuan 
        ];

        // 3. Tampilkan View
        // Pastikan file view ini ada: app/Views/tenagaKependidikan/persetujuan_akun_tamu.php
        return view('tenagaKependidikan/persetujuan_akun_tamu', $data);
    }



    public function setujuiAkunTamu($idAkunTamu, $tipeAkun)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $infoAkunTamu = $this->persetujuanTamuModel->find($idAkunTamu);

        if ($infoAkunTamu == null) {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal!", "text" => "Akun gagal disetujui karena terjadi kesalahan!"]);
            return redirect()->to(base_url("TenagaKependidikan/persetujuanAkunTamu"));
        }

        switch ($tipeAkun) {
            case 'dospem':
                // 1. Masukkan ke tabel AKUN (Login)
                $idAkunDisetujui = $this->akunModel->insert([
                    "username" => $infoAkunTamu['username'],
                    "email" => $infoAkunTamu['email'],
                    "password" => $infoAkunTamu['password'],
                    "status" => "ACTIVE"
                ], true);

                // 2. Set ROLE
                $infoRole = $this->roleModel->where("nama", "dospem eksternal")->first();
                $this->aksesModel->insert([
                    "id_akun" => $idAkunDisetujui,
                    "id_role" => $infoRole['id'],
                ]);

                // 3. Masukkan ke tabel DOSEN (Profile)
                $this->dosenModel->insert([
                    "nama" => $infoAkunTamu['nama'],
                    "email" => $infoAkunTamu['email'],
                    "inisial" => $infoAkunTamu['inisial'],
                    "instansi" => $infoAkunTamu['instansi'],
                ]);

                // ============================================================
                // [BARU] 4. Masukkan ke tabel PEMBIMBING EKSTERNAL (Kelola Dokumen)
                // ============================================================

                // Panggil model baru
                $pembimbingEksternalModel = new \App\Models\PembimbingEksternalModel();

                // Cek kolom kontak/hp di tabel persetujuan_tamu kamu namanya apa?
                // Di sini saya asumsikan namanya 'no_hp'. Kalau beda, ganti ya.
                $kontak = isset($infoAkunTamu['no_hp']) ? $infoAkunTamu['no_hp'] : '-';

                $pembimbingEksternalModel->insert([
                    "nama_lengkap" => $infoAkunTamu['nama'],
                    "instansi"     => $infoAkunTamu['instansi'],
                    "email"        => $infoAkunTamu['email'],
                    "kontak"       => $kontak,
                    "file_ktp"     => null,
                    "file_cv"      => null,
                    "file_npwp"    => null
                ]);
                // ============================================================


                // 5. Hapus dari tabel Persetujuan Tamu
                $this->persetujuanTamuModel->delete(['id', $idAkunTamu]);

                session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil!", "text" => "Akun berhasil disetujui sebagai Dosen Pembimbing!"]);
                break;

            case 'klien':
                $idAkunDisetujui = $this->akunModel->insert([
                    "username" => $infoAkunTamu['username'],
                    "email" => $infoAkunTamu['email'],
                    "password" => $infoAkunTamu['password'],
                    "status" => "ACTIVE"
                ], true);

                $infoRole = $this->roleModel->where("nama", "klien")->first();
                $this->aksesModel->insert([
                    "id_akun" => $idAkunDisetujui,
                    "id_role" => $infoRole['id'],
                ]);

                $this->klienModel->insert([
                    "nama" => $infoAkunTamu['nama'],
                    "email" => $infoAkunTamu['email'],
                    "inisial" => $infoAkunTamu['inisial'],
                    "instansi" => $infoAkunTamu['instansi'],
                ]);

                $this->persetujuanTamuModel->delete(['id', $idAkunTamu]);

                session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil!", "text" => "Akun berhasil disetujui sebagai Klien!"]);
                break;

            default:
                session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal!", "text" => "Akun gagal disetujui karena terjadi kesalahan!"]);
                break;
        }


        return redirect()->to(base_url("TenagaKependidikan/persetujuanAkunTamu"));
    }

    public function tolakAkunTamu($idAkunTamu)
    {
        //autentikasi
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        //buat mencegah access langsung dari link
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $this->persetujuanTamuModel->delete(['id', $idAkunTamu]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil!", "text" => "Akun berhasil ditolak"]);
        return redirect()->to(base_url("TenagaKependidikan/persetujuanAkunTamu"));
    }


    private function authenticate($roles)
    {

        $userSession = session("user_session");

        if ($userSession == null) {
            return false;
        }

        $userRoles = isset($userSession['roles']) ? array_map('strtolower', $userSession['roles']) : [];

        // Allow Admin to access TenagaKependidikan features as well
        if (in_array('admin', $userRoles)) {
            return true;
        }

        // Normalize roles passed in and check
        foreach ($roles as $r) {
            if (in_array(strtolower($r), $userRoles)) {
                return true;
            }
        }

        return false;
    }


    // --- USER STORY 13: Pengajuan Penulisan Skripsi ---
   

    // --- FUNGSI AJAX FETCH NOTIFIKASI ---
   public function fetchNotif()
    {
        if (ob_get_length()) ob_clean();
        $db = \Config\Database::connect();
        $notifList = []; 
        $unreadCount = 0;

        // 1. Notif Skripsi (US 13) - Update Status ke "Dalam Pengerjaan"
        $q13 = $db->table('skripsi')->select('mahasiswa.nama, skripsi.is_read_admin')
                  ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                  ->where('skripsi.status', 'Dalam Pengerjaan')->get()->getResultArray();
        foreach ($q13 as $row) {
            if ($row['is_read_admin'] == 0) $unreadCount++;
            $notifList[] = [
                'pesan' => 'Penulisan Skripsi: ' . $row['nama'],
                'is_read_admin' => $row['is_read_admin'],
                'link'  => base_url('TenagaKependidikan/pengajuanSkripsi')
            ];
        }

        // 2. Notif Prasidang (US 14) - Status "TERTUNDA"
        $q14 = $db->table('pengajuan_prasidang')->select('mahasiswa.nama, pengajuan_prasidang.is_read_admin')
                  ->join('skripsi', 'skripsi.id = pengajuan_prasidang.id_skripsi')
                  ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                  ->where('pengajuan_prasidang.status', 'TERTUNDA')->get()->getResultArray();
        foreach ($q14 as $row) {
            if ($row['is_read_admin'] == 0) $unreadCount++;
            $notifList[] = [
                'pesan' => 'Prasidang: ' . $row['nama'],
                'is_read_admin' => $row['is_read_admin'],
                'link'  => base_url('TenagaKependidikan/pengajuanPrasidang')
            ];
        }

        // 3. Notif Sidang (US 15) - Status "TERTUNDA"
        $q15 = $db->table('pengajuan_sidang')->select('mahasiswa.nama, pengajuan_sidang.is_read_admin')
                  ->join('skripsi', 'skripsi.id = pengajuan_sidang.id_skripsi')
                  ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                  ->where('pengajuan_sidang.status', 'TERTUNDA')->get()->getResultArray();
        foreach ($q15 as $row) {
            if ($row['is_read_admin'] == 0) $unreadCount++;
            $notifList[] = [
                'pesan' => 'Sidang: ' . $row['nama'],
                'is_read_admin' => $row['is_read_admin'],
                'link'  => base_url('TenagaKependidikan/pengajuanSidangSkripsi')
            ];
        }

        return $this->response->setJSON(['unread' => $unreadCount, 'list' => $notifList]);
    }

    // --- FUNGSI MARK AS READ ---
    public function markAsRead()
    {
        $db = \Config\Database::connect();
        $db->table('skripsi')->update(['is_read_admin' => 1]);
        $db->table('pengajuan_prasidang')->update(['is_read_admin' => 1]);
        $db->table('pengajuan_sidang')->update(['is_read_admin' => 1]);

        return $this->response->setJSON(['status' => 'success']);
    }

    // --- FUNGSI HALAMAN HISTORY ---
    public function semua_notifikasi()
    {
        $db = \Config\Database::connect();
        
        $skripsi = $db->table('skripsi')->select('mahasiswa.nama, skripsi.status, skripsi.is_read_admin')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->whereIn('skripsi.status', ['Dalam Peninjauan', 'Dilihat'])->get()->getResultArray();

        $prasidang = $db->table('pengajuan_prasidang')->select('mahasiswa.nama, pengajuan_prasidang.status, pengajuan_prasidang.is_read_admin')
            ->join('skripsi', 'skripsi.id = pengajuan_prasidang.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->whereIn('pengajuan_prasidang.status', ['TERTUNDA', 'DILIHAT'])->get()->getResultArray();

        $sidang = $db->table('pengajuan_sidang')->select('mahasiswa.nama, pengajuan_sidang.status, pengajuan_sidang.is_read_admin')
            ->join('skripsi', 'skripsi.id = pengajuan_sidang.id_skripsi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->whereIn('pengajuan_sidang.status', ['TERTUNDA', 'DILIHAT'])->get()->getResultArray();

        return view('tenagaKependidikan/semua_notifikasi', [
            'title'     => 'History Notifikasi',
            'skripsi'   => $skripsi,
            'prasidang' => $prasidang,
            'sidang'    => $sidang
        ]);
    }


    
    // --- FITUR VERIFIKASI HARD COVER ---

    public function verifikasiHardcover()
    {
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 1. Logika Semester (Mirip Sidang Skripsi)
        $kodeSemester = $this->request->getGet("semester");
        $currentSemester = $this->semesterModel->getActiveSemester();
        
        // Jika ada input semester dari dropdown, gunakan itu
        if ($kodeSemester != null) {
            $currentSemester = $this->semesterModel->find($kodeSemester);
        }

        // 2. Ambil Data Berdasarkan Semester
        $pengajuan = [];
        if ($currentSemester != null) {
            $pengajuan = $this->pengajuanHardcoverModel->getAllPengajuanByKodeSemester($currentSemester['kode']);
        }

        // 3. Ambil List Semua Semester (Untuk Dropdown)
        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->findAll();

        $data = [
            'title' => "Verifikasi Hard Cover Skripsi",
            'pengajuan' => $pengajuan,
            'semesterList' => $allSemester,    // Kirim list semester
            'currentSemester' => $currentSemester // Kirim semester terpilih
        ];

        return view("tenagaKependidikan/verifikasi_hardcover", $data);
    }

    public function detailVerifikasiHardcover($id)
    {
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        $detail = $this->pengajuanHardcoverModel->getDetailPengajuan($id);

        if (!$detail) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Detail Verifikasi Hard Cover',
            'detail' => $detail
        ];

        return view("tenagaKependidikan/detail_verifikasi_hardcover", $data);
    }

    public function setujuiHardcover($id)
    {
        if (!$this->authenticate(["tendik"])) return redirect()->to(base_url("unauthorized.php"));
        if ($this->request->getMethod() != 'post') return redirect()->back();

        // Update status jadi DISETUJUI dan simpan ID Verifikator (Tendik yang login)
        $this->pengajuanHardcoverModel->update($id, [
            'status' => 'DISETUJUI',
            'verifikator' => session()->get('user_session')['id'],
            'komentar_penolakan' => null // Reset komentar jika ada
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Pengajuan Hard Cover Disetujui"]);
        return redirect()->to(base_url("TenagaKependidikan/verifikasiHardcover"));
    }

    public function tolakHardcover($id)
    {
        if (!$this->authenticate(["tendik"])) return redirect()->to(base_url("unauthorized.php"));
        if ($this->request->getMethod() != 'post') return redirect()->back();

        $alasan = $this->request->getPost('komentar_penolakan');

        // Update status jadi DITOLAK beserta alasannya
        $this->pengajuanHardcoverModel->update($id, [
            'status' => 'DITOLAK',
            'verifikator' => session()->get('user_session')['id'],
            'komentar_penolakan' => $alasan
        ]);

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Pengajuan Hard Cover Ditolak"]);
        return redirect()->to(base_url("TenagaKependidikan/verifikasiHardcover"));
    }



    // --- FITUR UNGGAH HARD COVER MANUAL (TENDIK) ---

    public function unggahHardcover()
    {
        if (!$this->authenticate(["tendik"])) {
            return redirect()->to(base_url("unauthorized.php"));
        }

        // 1. Logika Filter Semester
        $kodeSemester = $this->request->getGet("semester");
        $currentSemester = $this->semesterModel->getActiveSemester();
        
        // Jika user memilih semester dari dropdown
        if ($kodeSemester != null) {
            $currentSemester = $this->semesterModel->find($kodeSemester);
        }

        // 2. Ambil Data Pengajuan Berdasarkan Semester Terpilih
        $pengajuan = [];
        if ($currentSemester != null) {
            $pengajuan = $this->pengajuanHardcoverModel->getAllPengajuanByKodeSemester($currentSemester['kode']);
        }

        // 3. Ambil List Semua Semester (Untuk isi Dropdown)
        $allSemester = $this->semesterModel->orderBy("status", "ASC")->orderBy("kode", "DESC")->findAll();

        // 4. Ambil Data Mahasiswa (Untuk Modal Upload)
        // [UPDATE] Sekarang difilter berdasarkan semester yang dipilih ($currentSemester)
        $listMahasiswaBuilder = $this->skripsiModel
            ->select('skripsi.id as id_skripsi, skripsi.judul, skripsi.npm, mahasiswa.nama, mahasiswa.id_prodi')
            ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
            ->where('skripsi.status', 'Lulus');
            
        // Jika ada semester yang dipilih, filter juga mahasiswanya
        if ($currentSemester != null) {
            $listMahasiswaBuilder->where('skripsi.kode_semester', $currentSemester['kode']);
        }

        $listMahasiswa = $listMahasiswaBuilder->orderBy('mahasiswa.nama', 'ASC')->findAll();

        $data = [
            'title' => "Unggah Hard Cover Mahasiswa",
            'pengajuan' => $pengajuan,
            'listMahasiswa' => $listMahasiswa,
            'semesterList' => $allSemester,
            'currentSemester' => $currentSemester 
        ];

        return view("tenagaKependidikan/unggah_hardcover", $data);
    }


    
    public function simpanHardcoverManual()
    {
        if (!$this->authenticate(["tendik"])) return redirect()->to(base_url("unauthorized.php"));
        if ($this->request->getMethod() != 'post') return redirect()->back();

        // 1. Ambil Inputan
        $idSkripsi = $this->request->getPost('id_skripsi');
        
        // 2. LOGIC BARU: Status Otomatis DISETUJUI & Tanpa Komentar
        $statusInput = 'DISETUJUI'; 
        $komentar = null;
        
        // 3. Validasi File
        $file = $this->request->getFile('file_hardcover');
        $namaFile = "";

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Generate nama file unik
            $namaFile = 'Hardcover_Manual_' . $idSkripsi . '_' . date('YmdHis') . '.pdf';
            // Pindahkan ke folderHardcover
            $file->move('folderHardcover', $namaFile);
        } else {
            session()->setFlashdata("message", ["icon" => "error", "title" => "Gagal", "text" => "File Hard Cover wajib diunggah!"]);
            return redirect()->back()->withInput();
        }

        // 4. Cek apakah data sudah ada sebelumnya?
        $cekAda = $this->pengajuanHardcoverModel->where('id_skripsi', $idSkripsi)->first();

        $dataSimpan = [
            'id_skripsi' => $idSkripsi,
            'file_hardcover' => $namaFile,
            'tanggal_pengajuan' => date('Y-m-d H:i:s'),
            'status' => $statusInput, // Pasti DISETUJUI
            'komentar_penolakan' => $komentar, // Pasti NULL
            'verifikator' => session()->get('user_session')['id'] 
        ];

        if ($cekAda) {
            $this->pengajuanHardcoverModel->update($cekAda['id'], $dataSimpan);
        } else {
            $this->pengajuanHardcoverModel->insert($dataSimpan);
        }

        session()->setFlashdata("message", ["icon" => "success", "title" => "Berhasil", "text" => "Hard Cover berhasil diunggah dan otomatis disetujui!"]);
        return redirect()->to(base_url("TenagaKependidikan/unggahHardcover"));
    }
}

