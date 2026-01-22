<?php 

namespace App\Models;



use CodeIgniter\Model;



class DosenModel extends Model 

{

    protected $table = "dosen";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'email', 'inisial', 'id_prodi', 'instansi'];

    protected $useTimestamps = false;



    public function getDosenByProdi($idProdi) {

        $db = \Config\Database::connect();

        $builder = $db->table("dosen");

        return $builder->

        select()->

        getWhere([

            "id_prodi" => $idProdi,

        ])->

        getResultArray();

    }



    public function getDosenByInisial($inisial) {

        $db = \Config\Database::connect();

        $builder = $db->table("dosen");

        $arrayDosen = $builder->

        select()->

        getWhere([

            "inisial" => $inisial,

        ])->

        getResultArray();



        if (count($arrayDosen) == 0) {

            return null;

        }



        return $arrayDosen[0];

    }

    public function isInitialAlreadyUsed($idDosen, $newInisial) {
        $db = \Config\Database::connect();

        $sql = "SELECT * from dosen where id <> ? and inisial = ?";

        $result = $db->query($sql, [$idDosen, $newInisial]);

        return count($result->getResultArray()) > 0;
    }



    public function getDosenWithCountMahasiswaBimbinganByProdiAndKodeSemester($id_prodi, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT d.inisial as inisial_dosen, count(d.id) as jumlah_mahasiswa, d.nama as nama_dosen, d.id as id_dosen, s.kode_semester

        from dosen as d

        left join pembimbing as pem on pem.id_dosen = d.id

        inner join skripsi as s on s.id = pem.id_skripsi

        where d.id_prodi = ? and s.kode_semester = ?

        group by d.id

        order by d.id ASC";

        $result = $db->query($sql, [$id_prodi, $kode_semester]);

        return $result->getResultArray();

    }

    public function getDosenWithCountMahasiswaBimbinganByKodeSemester($kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT d.inisial as inisial_dosen, count(d.id) as jumlah_mahasiswa, d.nama as nama_dosen, d.id as id_dosen, s.kode_semester,

        prodi.nama as nama_prodi, prodi.inisial as inisial_prodi

        from dosen as d

        left join pembimbing as pem on pem.id_dosen = d.id

        inner join skripsi as s on s.id = pem.id_skripsi

        inner join program_studi as prodi on prodi.id = d.id_prodi

        where s.kode_semester = ?

        group by d.id

        order by d.id ASC";

        $result = $db->query($sql, [$kode_semester]);

        return $result->getResultArray();

    }



    public function getMahasiswaBimbinganByIdDosenAndKodeSemester($id_dosen, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT mhs.npm as npm, mhs.nama as nama_mahasiswa, 
        skri.judul as judul, skri.status as status_skripsi, skri.jenis as jenis_skripsi,
        bid.inisial as inisial_bidang, bid.nama as nama_bidang, 
        pem.role as peran_pembimbing, 
        (select count(hasil_bimbingan) from catatan_bimbingan where id_pembimbing = pem.id and status = 'DISETUJUI') as jumlah_bimbingan
        

        from dosen as d

        inner join pembimbing as pem on pem.id_dosen = d.id

        inner join skripsi as skri on skri.id = pem.id_skripsi

        inner join bidang as bid on bid.id = skri.id_bidang

        inner join mahasiswa as mhs on mhs.npm = skri.npm

        where d.id = ? and skri.kode_semester = ?

        order by (select max(tanggal_bimbingan) from catatan_bimbingan where id_pembimbing = pem.id) DESC";

        $result = $db->query($sql, [$id_dosen, $kode_semester]);

        return $result->getResultArray();

    }

    public function getAllDosenInfo() {
        $db = \Config\Database::connect();

        $sql = "SELECT d.*, p.nama as nama_prodi, p.inisial as inisial_prodi
        FROM dosen as d
        inner join program_studi as p on p.id = d.id_prodi";

        $result = $db->query($sql);

        return $result->getResultArray();
    }

    public function getAllDosenEksternalInfo() {
        $db = \Config\Database::connect();

        $sql = "SELECT d.*, a.status as status_akun
        FROM dosen as d
        inner join akun as a on a.email = d.email
        where d.id_prodi is null";

        $result = $db->query($sql);

        return $result->getResultArray();
    }

    public function getDosenInfoByIdProdi($idProdi) {
        $db = \Config\Database::connect();

        $sql = "SELECT d.*, p.nama as nama_prodi
        FROM dosen as d
        inner join program_studi as p on p.id = d.id_prodi
        where p.id = ?";

        $result = $db->query($sql, [$idProdi]);

        return $result->getResultArray();
    }

    public function getDosenInfo($idDosen) {
        $db = \Config\Database::connect();

        $sql = "SELECT d.*, p.nama as nama_prodi, p.inisial as inisial_prodi, a.id as id_akun
        FROM dosen as d
        left join program_studi as p on p.id = d.id_prodi
        inner join akun as a on a.email = d.email
        where d.id = ?";

        $result = $db->query($sql, [$idDosen]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

}