<?php 

namespace App\Models;



use CodeIgniter\Model;



class SidangSkripsiModel extends Model 

{

    protected $table = "sidang_skripsi";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_skripsi', 'tanggal', 'ruangan','ketua_sidang', 'dosen_penguji', 'total_nilai', 'grade', 'status', 'klien', 'nomor_berita_acara'];

    protected $useTimestamps = false;



    public function getSidangSkripsiByProdiAndKodeSemester($idProdi, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama AS nama_mahasiswa, s.judul, b.nama AS nama_bidang, b.inisial AS inisial_bidang,

        kli.inisial as inisial_klien, kli.instansi as instansi_klien, kli.id as id_klien,

        ss.*, 
        
        -- [BARU] Data Ketua Sidang
        ketua.nama as nama_ketua, ketua.inisial as inisial_ketua,

        penguji.nama as nama_penguji, penguji.inisial as inisial_penguji, 

        d1.nama AS nama_pembimbing1, d2.nama AS nama_pembimbing2, d3.nama as nama_pembimbing_agama, 

        d1.inisial AS inisial_pembimbing1, d2.inisial AS inisial_pembimbing2, d3.inisial as inisial_pembimbing_agama, 
        IFNULL(
            (SELECT NULLIF(count(ps.id), 0) FROM penilaian_sidang as ps where ps.id_sidang_skripsi = ss.id), 
            (SELECT count(psn.nilai) FROM penilaian_sidang_new as psn where psn.id_sidang_skripsi = ss.id)
        ) as jumlah_nilai_masuk

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        INNER JOIN mahasiswa AS m ON m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        INNER JOIN dosen AS penguji ON penguji.id = ss.dosen_penguji

        -- [BARU] Join ke Dosen sebagai Ketua Sidang
        LEFT JOIN dosen AS ketua ON ketua.id = ss.ketua_sidang

        INNER JOIN dosen AS d1 ON d1.id = p1.id_dosen

        LEFT JOIN dosen AS d2 ON d2.id = p2.id_dosen

        INNER JOIN dosen AS d3 ON d3.id = p3.id_dosen

        left join klien as kli on kli.id = ss.klien

        WHERE m.id_prodi = ? and

        p1.role = 'Pembimbing Ilmu 1' and

        p2.role = 'Pembimbing Ilmu 2' and

        p3.role = 'Pembimbing Agama' and

        s.kode_semester = ?

        order by ss.tanggal DESC

        ";

        $result = $db->query($sql, [$idProdi, $kode_semester]);

        return $result->getResultArray();

    }


    public function getSidangSkripsiByKodeSemester($kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama AS nama_mahasiswa, s.judul, b.nama AS nama_bidang, b.inisial AS inisial_bidang,

        ss.*, penguji.nama as nama_penguji, penguji.inisial as inisial_penguji, 

        d1.nama AS nama_pembimbing1, d2.nama AS nama_pembimbing2, d3.nama as nama_pembimbing_agama, 

        d1.inisial AS inisial_pembimbing1, d2.inisial AS inisial_pembimbing2, d3.inisial as inisial_pembimbing_agama, 
        
        IFNULL(
            (SELECT NULLIF(count(ps.id), 0) FROM penilaian_sidang as ps where ps.id_sidang_skripsi = ss.id), 
            (SELECT count(psn.nilai) FROM penilaian_sidang_new as psn where psn.id_sidang_skripsi = ss.id)
        ) as jumlah_nilai_masuk,

        prodi.nama as nama_prodi, prodi.inisial as inisial_prodi

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        INNER JOIN mahasiswa AS m ON m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        INNER JOIN dosen AS penguji ON penguji.id = ss.dosen_penguji

        INNER JOIN dosen AS d1 ON d1.id = p1.id_dosen

        LEFT JOIN dosen AS d2 ON d2.id = p2.id_dosen

        INNER JOIN dosen AS d3 ON d3.id = p3.id_dosen

        inner join program_studi as prodi on prodi.id = m.id_prodi

        WHERE

        p1.role = 'Pembimbing Ilmu 1' and

        p2.role = 'Pembimbing Ilmu 2' and

        p3.role = 'Pembimbing Agama' and

        s.kode_semester = ?

        order by ss.tanggal DESC

        ";

        $result = $db->query($sql, [$kode_semester]);

        return $result->getResultArray();

    }



    public function getSidangSkripsiByDateRange($idProdi, $dari, $hingga) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama AS nama_mahasiswa, s.judul, b.nama AS nama_bidang, b.inisial AS inisial_bidang,

        ss.*, penguji.nama as nama_penguji, penguji.inisial as inisial_penguji, 

        d1.nama AS nama_pembimbing1, d2.nama AS nama_pembimbing2, d3.nama as nama_pembimbing_agama, 

        d1.inisial AS inisial_pembimbing1, d2.inisial AS inisial_pembimbing2, d3.inisial as inisial_pembimbing_agama

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        INNER JOIN mahasiswa AS m ON m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        INNER JOIN dosen AS penguji ON penguji.id = ss.dosen_penguji

        INNER JOIN dosen AS d1 ON d1.id = p1.id_dosen

        LEFT JOIN dosen AS d2 ON d2.id = p2.id_dosen

        INNER JOIN dosen AS d3 ON d3.id = p3.id_dosen

        WHERE m.id_prodi = ? and

        p1.role = 'Pembimbing Ilmu 1' and

        p2.role = 'Pembimbing Ilmu 2' and

        p3.role = 'Pembimbing Agama' and

        ss.tanggal between ? and ?

        order by ss.tanggal DESC";

        $result = $db->query($sql, [$idProdi, $dari, $hingga]);

        return $result->getResultArray();

    }



    public function getSidangSkripsiByDosenOrKlienAndKodeSemester($idDosen, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT ss.*, 

        m.nama as nama_mahasiswa,

        s.judul, s.npm, s.kode_semester,

        b.nama AS nama_bidang, b.inisial AS inisial_bidang, 

        p1.id_dosen as id_pembimbing_1, 

        p2.id_dosen as id_pembimbing_2, 

        p3.id_dosen as id_pembimbing_agama,

        kli.id as id_klien,

        NULLIF(
            (select count(ps.id) from penilaian_sidang ps where ps.id_sidang_skripsi = ss.id and ps.id_dosen = ?) = 0 and 
            (select count(psn.nilai) from penilaian_sidang_new psn where psn.id_sidang_skripsi = ss.id and psn.id_penilai = ?) = 0,
            1 
        ) as status_nilai

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        inner join mahasiswa as m on m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        left join klien as kli on kli.id = ss.klien

        WHERE 

        (ss.dosen_penguji = ? or p1.id_dosen = ? or p2.id_dosen = ? or p3.id_dosen = ? or ss.klien = ?) and

        p1.role = 'Pembimbing Ilmu 1' and

        p2.role = 'Pembimbing Ilmu 2' and

        p3.role = 'Pembimbing Agama' and

        s.kode_semester = ?

        ORDER BY ss.tanggal DESC";

        $result = $db->query($sql, [$idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $kode_semester]);

        return $result->getResultArray();

    }

    public function getSidangSkripsiByKlienAndKodeSemester($idKlien, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT ss.*, 

        m.nama as nama_mahasiswa,

        s.judul, s.npm,

        b.nama AS nama_bidang, b.inisial AS inisial_bidang, 

        kli.id as kli_id

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        inner join mahasiswa as m on m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        inner join klien as kli on kli.id = ss.klien

        WHERE kli.id = ? and s.kode_semester = ?      

        ORDER BY ss.tanggal DESC";

        $result = $db->query($sql, [$idKlien, $kode_semester]);

        return $result->getResultArray();

    }

    public function getSidangSkripsiByDosenDateRange($idDosen, $dari, $hingga) {

        $db = \Config\Database::connect();

        $sql = "SELECT ss.*, 

        m.nama as nama_mahasiswa,

        s.judul, s.npm,

        b.nama AS nama_bidang, b.inisial AS inisial_bidang, 

        p1.id_dosen as id_pembimbing_1, 

        p2.id_dosen as id_pembimbing_2, 

        p3.id_dosen as id_pembimbing_agama,

        (select ps.status from penilaian_sidang as ps where ps.id_sidang_skripsi = ss.id and ps.id_dosen = ?) as status_nilai 

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        inner join mahasiswa as m on m.npm = s.npm

        INNER JOIN bidang AS b ON b.id = s.id_bidang

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        WHERE 

        (ss.dosen_penguji = ? or p1.id_dosen = ? or p2.id_dosen = ? or p3.id_dosen = ?) and

        p1.role = 'Pembimbing Ilmu 1' and

        p2.role = 'Pembimbing Ilmu 2' and

        p3.role = 'Pembimbing Agama' and

        ss.tanggal between ? and ?

        ORDER BY ss.tanggal DESC";

        $result = $db->query($sql, [$idDosen, $idDosen, $idDosen, $idDosen, $idDosen, $dari, $hingga]);

        return $result->getResultArray();

    }



    public function getDetailSidangSkripsiById($idSidangSkripsi) {

        // $db = \Config\Database::connect();

        // $sql = "SELECT ss.*, 

        // s.judul, s.jenis as jenis_skripsi,

        // m.npm, m.nama as nama_mahasiswa, 

        // b.nama AS nama_bidang, 

        // d1.nama as pembimbing1, d1.id as id_pembimbing1,

        // d2.nama as pembimbing2, d2.id as id_pembimbing2,

        // d3.nama as pembimbing_agama, d3.id as id_pembimbing_agama,

        // kli.instansi as instansi_klien, kli.id as id_klien,

        // ps.file_draft_final, ps.file_form_bimbingan, ps.file_persyaratan_sidang,

        // reviewer.nama as nama_reviewer_sempra,

        // sempra.komentar_reviewer as komentar_sempra, sempra.rekomendasi_nilai,

        // reviewer_sempro.nama as nama_reviewer_sempro,

        // prop.komentar as komentar_sempro,

        // penguji.nama AS nama_penguji

        // FROM sidang_skripsi AS ss

        // INNER JOIN skripsi AS s ON s.id = ss.id_skripsi

        // inner join seminar_prasidang as sempra on sempra.id = (select id from seminar_prasidang where status = 'LAYAK SIDANG' and id_skripsi = s.id ORDER BY tanggal desc limit 1)

        // INNER JOIN pengajuan_sidang AS ps ON ps.id_skripsi = s.id

        // INNER JOIN mahasiswa As m ON m.npm = s.npm

        // inner join proposal as prop on prop.id = (select id from proposal where status in ('DITERIMA', 'REVISI') and npm = s.npm ORDER BY tanggal_upload desc limit 1)

        // INNER JOIN bidang AS b ON b.id = s.id_bidang

        // INNER JOIN pembimbing AS p1 ON p1.id_skripsi = s.id

        // INNER JOIN pembimbing AS p2 ON p2.id_skripsi = s.id

        // INNER JOIN pembimbing AS p3 ON p3.id_skripsi = s.id

        // INNER JOIN dosen AS penguji ON penguji.id = ss.dosen_penguji

        // INNER JOIN dosen AS d1 ON d1.id = p1.id_dosen

        // LEFT JOIN dosen AS d2 ON d2.id = p2.id_dosen

        // INNER JOIN dosen AS d3 ON d3.id = p3.id_dosen

        // inner join dosen as reviewer on reviewer.id = sempra.dosen_reviewer

        // inner join dosen as reviewer_sempro on reviewer_sempro.id = prop.pembuat_komentar

        // left join klien as kli on kli.id = ss.klien

        // WHERE ss.id = ? AND 

        // p1.role = 'Pembimbing Ilmu 1' AND

        // p2.role = 'Pembimbing Ilmu 2' AND

        // p3.role = 'Pembimbing Agama'";

        // $result = $db->query($sql, [$idSidangSkripsi]);

        // return $result->getResultArray();


        
        $db = \Config\Database::connect();

        $sql = "SELECT ss.*, 
        s.judul, s.jenis as jenis_skripsi,
        m.npm, m.nama as nama_mahasiswa, 
        b.nama AS nama_bidang, 
        
        -- Data Pembimbing
        d1.nama as pembimbing1, d1.id as id_pembimbing1, d1.nip as nip_pembimbing1,
        d2.nama as pembimbing2, d2.id as id_pembimbing2, d2.nip as nip_pembimbing2,
        d3.nama as pembimbing_agama, d3.id as id_pembimbing_agama, d3.nip as nip_pembimbing_agama,
        
        kli.instansi as instansi_klien, kli.id as id_klien,
        ps.file_draft_final, ps.file_form_bimbingan, ps.file_persyaratan_sidang,
        
        reviewer.nama as nama_reviewer_sempra,
        sempra.komentar_reviewer as komentar_sempra, sempra.rekomendasi_nilai,
        reviewer_sempro.nama as nama_reviewer_sempro,
        prop.komentar as komentar_sempro,
        
        -- Data Penguji
        penguji.nama AS nama_penguji,
        penguji.nip AS nip_penguji,

        -- [BARU] Data Ketua Sidang
        ketua.nama as nama_ketua_sidang,
        ketua.nip as nip_ketua_sidang

        FROM sidang_skripsi AS ss
        INNER JOIN skripsi AS s ON s.id = ss.id_skripsi
        
        -- Subquery untuk Seminar Prasidang (Ambil yang terakhir & Layak)
        INNER JOIN seminar_prasidang as sempra on sempra.id = (
            select id from seminar_prasidang 
            where status = 'LAYAK SIDANG' and id_skripsi = s.id 
            ORDER BY tanggal desc limit 1
        )
        
        INNER JOIN pengajuan_sidang AS ps ON ps.id_skripsi = s.id
        INNER JOIN mahasiswa As m ON m.npm = s.npm
        
        -- Subquery Proposal (Ambil yang terakhir diterima/revisi)
        INNER JOIN proposal as prop on prop.id = (
            select id from proposal 
            where status in ('DITERIMA', 'REVISI') and npm = s.npm 
            ORDER BY tanggal_upload desc limit 1
        )
        
        INNER JOIN bidang AS b ON b.id = s.id_bidang
        
        -- Join Pembimbing (Logic yang sudah diperbaiki)
        INNER JOIN pembimbing AS p1 ON (p1.id_skripsi = s.id AND p1.role = 'Pembimbing Ilmu 1')
        LEFT JOIN pembimbing AS p2 ON (p2.id_skripsi = s.id AND p2.role = 'Pembimbing Ilmu 2')
        INNER JOIN pembimbing AS p3 ON (p3.id_skripsi = s.id AND p3.role = 'Pembimbing Agama')
        
        -- Join Dosen Berdasarkan Peran
        INNER JOIN dosen AS penguji ON penguji.id = ss.dosen_penguji
        LEFT JOIN dosen AS ketua ON ketua.id = ss.ketua_sidang  -- <--- JOIN KETUA SIDANG
        
        INNER JOIN dosen AS d1 ON d1.id = p1.id_dosen
        LEFT JOIN dosen AS d2 ON d2.id = p2.id_dosen
        INNER JOIN dosen AS d3 ON d3.id = p3.id_dosen
        
        INNER JOIN dosen as reviewer on reviewer.id = sempra.dosen_reviewer
        INNER JOIN dosen as reviewer_sempro on reviewer_sempro.id = prop.pembuat_komentar
        
        LEFT JOIN klien as kli on kli.id = ss.klien
        
        WHERE ss.id = ?";

        $result = $db->query($sql, [$idSidangSkripsi]);

        return $result->getResultArray();
    }



    public function getJadwalByNPM($npm) {

        $db = \Config\Database::connect();

        $sql = "SELECT ss.*, s.judul, penguji.nama as nama_penguji, penguji.inisial as inisial_penguji, 

        d1.nama as nama_pembimbing1, d1.inisial as inisial_pembimbing1,

        d2.nama as nama_pembimbing2, d2.inisial as inisial_pembimbing2,

        d3.nama as nama_pembimbing_agama, d3.inisial as inisial_pembimbing_agama,

        kli.instansi as instansi_klien, kli.inisial as inisial_klien

        FROM sidang_skripsi AS ss

        INNER JOIN skripsi AS s on s.id = ss.id_skripsi

        INNER JOIN pembimbing as p1 on p1.id_skripsi = s.id

        INNER JOIN pembimbing as p2 on p2.id_skripsi = s.id

        INNER JOIN pembimbing as p3 on p3.id_skripsi = s.id

        INNER JOIN dosen as penguji on penguji.id = ss.dosen_penguji

        INNER JOIN dosen as d1 on d1.id = p1.id_dosen

        LEFT JOIN dosen as d2 on d2.id = p2.id_dosen

        INNER JOIN dosen as d3 on d3.id = p3.id_dosen

        LEFT JOIN klien as kli on kli.id = ss.klien

        WHERE s.npm = ? AND

        p1.role = 'Pembimbing Ilmu 1' AND

        p2.role = 'Pembimbing Ilmu 2' AND

        p3.role = 'Pembimbing Agama'

        ORDER BY ss.tanggal ASC";

        $result = $db->query($sql, [$npm]);

        return $result->getResultArray();

    }



    public function getAllSidangSkripsiByProdiAndKodeSemester($id_prodi, $kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT mhs.nama as nama_mahasiswa, mhs.npm as npm,

        skri.judul as judul, skri.id as id_skripsi,

        ss.tanggal, ss.ruangan, ss.total_nilai, ss.grade, ss.status,

        penguji.inisial as inisial_penguji, penguji.nama as nama_penguji 

        from sidang_skripsi as ss

        inner join skripsi as skri on skri.id = ss.id_skripsi

        inner join mahasiswa as mhs on mhs.npm = skri.npm

        inner join dosen as penguji on penguji.id = ss.dosen_penguji

        where mhs.id_prodi = ? and skri.kode_semester = ?

        order by ss.tanggal DESC";

        $result = $db->query($sql, [$id_prodi, $kode_semester]);

        return $result->getResultArray();

    }



    public function getAllSidangSkripsiByKodeSemester($kode_semester) {

        $db = \Config\Database::connect();

        $sql = "SELECT mhs.nama as nama_mahasiswa, mhs.npm as npm,

        prodi.inisial as inisial_prodi, prodi.nama as nama_prodi,

        skri.judul as judul, skri.id as id_skripsi,

        ss.tanggal, ss.ruangan, ss.total_nilai, ss.grade, ss.status,

        penguji.inisial as inisial_penguji, penguji.nama as nama_penguji 

        from sidang_skripsi as ss

        inner join skripsi as skri on skri.id = ss.id_skripsi

        inner join mahasiswa as mhs on mhs.npm = skri.npm

        inner join program_studi as prodi on prodi.id = mhs.id_prodi

        inner join dosen as penguji on penguji.id = ss.dosen_penguji

        where skri.kode_semester = ?

        order by ss.tanggal DESC";

        $result = $db->query($sql, [$kode_semester]);

        return $result->getResultArray();

    }

    /**
     * Cek apakah jadwal bentrok dengan jadwal lain yang sudah ada.
     * Mengembalikan true jika bentrok, false jika aman.
     * * @param string $tanggal Waktu mulai sidang (format Y-m-d H:i:s)
     * @param string $ruangan Nama ruangan
     * @param int $dosenPenguji ID Dosen Penguji
     * @param int|null $excludeId ID Sidang yang sedang diedit (opsional, agar tidak bentrok dengan diri sendiri saat update)
     */
    /**
     * Cek Bentrok LENGKAP (Ruangan & Semua Dosen terlibat).
     */
    public function isJadwalBentrok($tanggal, $ruangan, $dosenPengujiBaru, $ketuaSidangBaru, $idSkripsiBaru, $excludeIdSidang = null)
    {
        $db = \Config\Database::connect();
        $durasiMenit = 90; 
        
        $waktuMulaiBaru = strtotime($tanggal);
        $waktuSelesaiBaru = $waktuMulaiBaru + ($durasiMenit * 60);
        
        // --- KUMPULKAN ORANG DI JADWAL BARU ---
        $participantsBaru = [];
        if($dosenPengujiBaru) $participantsBaru[] = $dosenPengujiBaru;
        if($ketuaSidangBaru) $participantsBaru[] = $ketuaSidangBaru; // <--- Cek Ketua juga

        // Ambil Pembimbing
        $queryPembimbing = $db->table('pembimbing')
                              ->select('id_dosen')
                              ->where('id_skripsi', $idSkripsiBaru)
                              ->get()
                              ->getResultArray();
        
        foreach($queryPembimbing as $pem) {
            if($pem['id_dosen'] != null) $participantsBaru[] = $pem['id_dosen'];
        }

        // --- AMBIL JADWAL EKSISTING ---
        $hariH = date('Y-m-d', $waktuMulaiBaru);
        $builder = $this->builder();
        $builder->select('id, id_skripsi, tanggal, ruangan, dosen_penguji, ketua_sidang'); // Ambil ketua_sidang lama
        $builder->like('tanggal', $hariH, 'after'); 

        if ($excludeIdSidang) {
            $builder->where('id !=', $excludeIdSidang);
        }

        $existingSchedules = $builder->get()->getResultArray();

        // --- LOOP CHECK ---
        foreach ($existingSchedules as $jadwal) {
            $existingStart = strtotime($jadwal['tanggal']);
            $existingEnd = $existingStart + ($durasiMenit * 60);

            if ($waktuMulaiBaru < $existingEnd && $waktuSelesaiBaru > $existingStart) {
                
                // Cek Ruangan
                $ruanganBaruNorm = strtolower(str_replace(['ruang ', 'ruangan '], '', strtolower($ruangan)));
                $ruanganLamaNorm = strtolower(str_replace(['ruang ', 'ruangan '], '', strtolower($jadwal['ruangan'])));

                if (trim($ruanganBaruNorm) == trim($ruanganLamaNorm)) {
                    return "Bentrok Ruangan: " . $jadwal['ruangan'] . " sudah terpakai.";
                }

                // Cek Personil (Sekarang termasuk Ketua Sidang)
                $participantsLama = [];
                if($jadwal['dosen_penguji']) $participantsLama[] = $jadwal['dosen_penguji'];
                if($jadwal['ketua_sidang']) $participantsLama[] = $jadwal['ketua_sidang']; // <--- Masukkan Ketua Lama

                $queryPembimbingLama = $db->table('pembimbing')->select('id_dosen')->where('id_skripsi', $jadwal['id_skripsi'])->get()->getResultArray();
                foreach($queryPembimbingLama as $pemLama) {
                    if($pemLama['id_dosen'] != null) $participantsLama[] = $pemLama['id_dosen'];
                }

                $bentrokOrang = array_intersect($participantsBaru, $participantsLama);

                if (!empty($bentrokOrang)) {
                    $idDosenBentrok = reset($bentrokOrang); 
                    $dosen = $db->table('dosen')->getWhere(['id' => $idDosenBentrok])->getRowArray();
                    $namaDosen = $dosen ? $dosen['nama'] : 'Dosen';
                    return "Bentrok Dosen: " . $namaDosen . " sibuk di jadwal lain (sebagai Ketua/Penguji/Pembimbing).";
                }
            }
        }
        return false;
    }
}