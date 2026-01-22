<?php 

namespace App\Models;



use CodeIgniter\Model;



class SeminarProposalModel extends Model 

{

    protected $table = "seminar_proposal";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_proposal', 'tanggal', 'ruangan', 'link_video', 'link_konferensi', 'dosen_penguji1', 'dosen_penguji2', 'nomor_berita_acara'];

    protected $useTimestamps = false;


    // --- Method Baru untuk Fitur Cetak Berita Acara ---
    // Mengambil detail lengkap seminar (Mhs, Judul, Penguji, Pembimbing) berdasarkan ID Seminar
    public function getDetailSeminar($idSeminar) {
        $db = \Config\Database::connect();
        $builder = $db->table('seminar_proposal');
        
        $builder->select('seminar_proposal.*, 
            mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, 
            proposal.judul,
            proposal.status,
            program_studi.nama as nama_prodi,
            d1.nama as nama_penguji1, d1.inisial as inisial_penguji1,
            d2.nama as nama_penguji2, d2.inisial as inisial_penguji2,
            dp1.nama as nama_pembimbing1, dp1.inisial as inisial_pembimbing1,
            dp2.nama as nama_pembimbing2, dp2.inisial as inisial_pembimbing2
        ');
        
        // Join ke tabel terkait
        $builder->join('proposal', 'proposal.id = seminar_proposal.id_proposal');
        $builder->join('mahasiswa', 'mahasiswa.npm = proposal.npm');
        $builder->join('program_studi', 'program_studi.id = mahasiswa.id_prodi');
        
        // Join untuk mendapatkan nama Dosen Penguji (dari tabel seminar_proposal)
        $builder->join('dosen as d1', 'd1.id = seminar_proposal.dosen_penguji1', 'left');
        $builder->join('dosen as d2', 'd2.id = seminar_proposal.dosen_penguji2', 'left');
        
        // Join untuk mendapatkan nama Dosen Pembimbing (dari tabel proposal)
        $builder->join('dosen as dp1', 'dp1.id = proposal.dosen_usulan1', 'left');
        $builder->join('dosen as dp2', 'dp2.id = proposal.dosen_usulan2', 'left');
        
        $builder->where('seminar_proposal.id', $idSeminar);

        $query = $builder->get();
        return $query->getRowArray(); // Mengembalikan 1 baris data
    }


    public function getSemproByKodeSemester($kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT sp.*,
        m.npm, m.nama as nama_mahasiswa,
        p.judul, p.status, p.komentar, p.komentar_2, p.pembuat_komentar, p.file_proposal,
        b.nama as nama_bidang, b.inisial as inisial_bidang,
        prod.nama as nama_prodi, prod.inisial as inisial_prodi,
        ms.mode as mode_sempro
        from seminar_proposal as sp
        inner join proposal as p on p.id = sp.id_proposal
        inner join mahasiswa as m on m.npm = p.npm
        inner join bidang as b on b.id = p.id_bidang
        inner join program_studi as prod on prod.id = m.id_prodi
        inner join mode_sempro as ms on ms.kode_semester = p.kode_semester and ms.id_prodi = prod.id
        where p.kode_semester = ?
        order by sp.tanggal DESC";

        $result = $db->query($sql, [$kodeSemester]);

        return $result->getResultArray();
    }


    public function getSemproByProdiAndKodeSemester($idProdi, $kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT sp.*,
        m.npm, m.nama as nama_mahasiswa,
        p.judul, p.status, p.komentar, p.komentar_2, p.pembuat_komentar, p.jenis_skripsi, p.file_proposal,
        b.nama as nama_bidang, b.inisial as inisial_bidang
        from seminar_proposal as sp
        inner join proposal as p on p.id = sp.id_proposal
        inner join mahasiswa as m on m.npm = p.npm
        inner join bidang as b on b.id = p.id_bidang
        where m.id_prodi = ? and
        p.kode_semester = ?
        order by sp.tanggal DESC";

        $result = $db->query($sql, [$idProdi, $kodeSemester]);

        return $result->getResultArray();
    }

    public function getJadwalByNPM($npm) {

        $db = \Config\Database::connect();

        $builder = $db->table("seminar_proposal");



        return $builder->

        select("seminar_proposal.*, mahasiswa.npm, proposal.judul")->

        join("proposal" , "proposal.id = seminar_proposal.id_proposal")->

        join("mahasiswa", "mahasiswa.npm = proposal.npm")->

        orderBy("tanggal", "ASC")->

        getWhere(["mahasiswa.npm" => $npm])->

        getResultArray();

    }



    public function isEditable($idSempro) {

        $db = \Config\Database::connect();

        $builder = $db->table("seminar_proposal");



        $sempro = $builder->

        select("seminar_proposal.*, proposal.status")->

        join("proposal", "proposal.id = seminar_proposal.id_proposal")->

        getWhere(['seminar_proposal.id' => $idSempro])->getResultArray()[0];



        return $sempro['status'] == 'TERTUNDA';

    }

    public function getSemproByDosenAndKodeSemester($idDosen, $kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT sp.*,

        m.npm, m.nama as nama_mahasiswa,

        p.judul, p.status, p.komentar, p.komentar_2, p.pembuat_komentar, p.jenis_skripsi, p.file_proposal,

        b.nama as nama_bidang, b.inisial as inisial_bidang

        from seminar_proposal as sp

        inner join proposal as p on p.id = sp.id_proposal

        inner join mahasiswa as m on m.npm = p.npm

        inner join bidang as b on b.id = p.id_bidang

        where (sp.dosen_penguji1 = ? or sp.dosen_penguji2 = ?) and

        p.kode_semester = ?

        order by sp.tanggal DESC";

        $result = $db->query($sql, [$idDosen, $idDosen, $kodeSemester]);

        return $result->getResultArray();

    }



    public function getSemproByDosenDateRange($idDosen, $dari, $hingga) {

        $db = \Config\Database::connect();

        $sql = "SELECT sp.*, m.npm, m.nama as nama_mahasiswa, p.judul, p.status, p.komentar, p.komentar_2, p.pembuat_komentar,

        b.nama as nama_bidang, b.inisial as inisial_bidang

        from seminar_proposal as sp

        inner join proposal as p on p.id = sp.id_proposal

        inner join mahasiswa as m on m.npm = p.npm

        inner join bidang as b on b.id = p.id_bidang

        where (sp.dosen_penguji1 = ? or sp.dosen_penguji2 = ?) and

        sp.tanggal between ? and ?

        order by sp.tanggal DESC";

        $result = $db->query($sql, [$idDosen, $idDosen, $dari, $hingga]);

        return $result->getResultArray();

    }



}