<?php 

namespace App\Models;



use CodeIgniter\Model;



class MahasiswaModel extends Model 

{

    protected $table = "mahasiswa";

    protected $primaryKey = "npm";

    protected $useAutoIncrement = false;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['npm', 'nama', 'email', 'angkatan', 'id_prodi', 'sks_lulus', 'pembimbing_akademik', 'mk_sedang_diambil', 'mk_akan_diambil', 'status_persetujuan_skripsi', 'file_khs', 'file_krs', 'file_persetujuan_skripsi', 'tanggal_pengajuan_skripsi'];

    protected $useTimestamps = false;



    public function getAllPengajuanSkripsi() {

        $db = \Config\Database::connect();

        $sql = "SELECT m.nama as nama_mahasiswa, m.npm, m.sks_lulus, m.mk_sedang_diambil, m.mk_akan_diambil, m.tanggal_pengajuan_skripsi,

        m.file_khs, m.file_krs, m.file_persetujuan_skripsi, m.status_persetujuan_skripsi, prodi.inisial as inisial_prodi, prodi.nama as nama_prodi,

        d.inisial as inisial_pembimbing_akademik, d.nama as nama_pembimbing_akademik

        FROM mahasiswa as m

        INNER JOIN dosen as d on d.id = m.pembimbing_akademik

        INNER JOIN program_studi as prodi on prodi.id = m.id_prodi

        WHERE m.sks_lulus is not null and

        m.tanggal_pengajuan_skripsi is not null and

        m.pembimbing_akademik is not null and

        m.mk_sedang_diambil is not null and

        m.mk_akan_diambil is not null and

        m.file_khs is not null and

        m.file_krs is not null and

        m.file_persetujuan_skripsi is not null

        order by m.tanggal_pengajuan_skripsi DESC";

        $result = $db->query($sql);

        return $result->getResultArray();

    }



    public function getPengajuanSkripsiByDateRange($dari, $hingga) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.nama as nama_mahasiswa, m.npm, m.sks_lulus, m.mk_sedang_diambil, m.mk_akan_diambil, m.tanggal_pengajuan_skripsi,

        m.file_khs, m.file_krs, m.file_persetujuan_skripsi, m.status_persetujuan_skripsi, prodi.inisial as inisial_prodi, prodi.nama as nama_prodi,

        d.inisial as inisial_pembimbing_akademik, d.nama as nama_pembimbing_akademik

        FROM mahasiswa as m

        INNER JOIN dosen as d on d.id = m.pembimbing_akademik

        INNER JOIN program_studi as prodi on prodi.id = m.id_prodi

        WHERE m.sks_lulus is not null and

        m.tanggal_pengajuan is not null and

        m.pembimbing_akademik is not null and

        m.mk_sedang_diambil is not null and

        m.mk_akan_diambil is not null and

        m.file_khs is not null and

        m.file_krs is not null and

        m.file_persetujuan_skripsi is not null and

        m.tanggal_pengajuan_skripsi between ? and ? 

        order by m.tanggal_pengajuan_skripsi DESC";

        $result = $db->query($sql, [$dari, $hingga]);

        return $result->getResultArray();

    }



    public function getPengajuanSkripsiByNpm($npm) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.nama as nama_mahasiswa, m.npm, m.sks_lulus, m.mk_sedang_diambil, m.mk_akan_diambil,

        m.file_khs, m.file_krs, m.file_persetujuan_skripsi, m.status_persetujuan_skripsi, 

        prodi.nama as nama_prodi, d.nama as nama_pembimbing_akademik

        FROM mahasiswa as m

        INNER JOIN dosen as d on d.id = m.pembimbing_akademik

        INNER JOIN program_studi as prodi on prodi.id = m.id_prodi

        WHERE m.sks_lulus is not null and

        m.pembimbing_akademik is not null and

        m.mk_sedang_diambil is not null and

        m.mk_akan_diambil is not null and

        m.file_khs is not null and

        m.file_krs is not null and

        m.file_persetujuan_skripsi is not null and

        m.npm = ?";



        $result = $db->query($sql, [$npm]);



        if (count($result->getResultArray()) == 0) {

            return null;

        }



        return $result->getResultArray()[0];

    }



    public function getMahasiswaBelumDapatSemproByProdi($idProdi) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama, p1.judul as judul_proposal, b.nama as nama_bidang

                from mahasiswa as m

                inner join proposal as p1 on p1.npm = m.npm

                inner join bidang as b on b.id = p1.id_bidang

                left join seminar_proposal as sm on sm.id_proposal = p1.id

                inner join semester as s on s.kode = p1.kode_semester

                where m.id_prodi = ? 

                and p1.tanggal_upload = (SELECT max(p2.tanggal_upload) from proposal as p2 where p2.npm = m.npm)

                and p1.status = 'TERTUNDA'

                and sm.id is null

                and p1.file_proposal is not null 
                
                and p1.file_proposal <> ''
                
                and s.status = 'AKTIF'";

        $result = $db->query($sql, [$idProdi]);

        return $result->getResultArray();

    }



    public function getMahasiswaBelumDapatSempraByProdi($idProdi) {

        $db = \Config\Database::connect();

        $sql = "SELECT DISTINCT m.npm, m.nama, s.judul AS judul, b.nama AS nama_bidang
        FROM mahasiswa AS m
        INNER JOIN skripsi AS s ON s.npm = m.npm
        INNER JOIN pengajuan_prasidang AS pp ON pp.id_skripsi = s.id
        INNER JOIN bidang AS b ON b.id = s.id_bidang
        INNER JOIN semester as sem ON sem.kode = s.kode_semester
        WHERE 
        m.id_prodi = ?
        AND s.tanggal_skripsi = (SELECT max(s2.tanggal_skripsi) FROM skripsi AS s2 WHERE s2.npm = m.npm)
        AND s.status = 'Dalam Pengerjaan'
        AND pp.id = (SELECT ps.id from pengajuan_prasidang as ps WHERE ps.id_skripsi = s.id and ps.status = 'DISETUJUI' order by ps.tanggal_pengajuan desc limit 1)
        AND ((SELECT COUNT(sm.id) FROM seminar_prasidang AS sm WHERE sm.id_skripsi = s.id) = 0 OR (SELECT pp.tanggal_pengajuan > sm.tanggal and sm.status = 'TIDAK LAYAK SIDANG' FROM seminar_prasidang AS sm WHERE sm.id_skripsi = s.id ORDER BY sm.tanggal DESC LIMIT 1) = 1)
        AND sem.status = 'AKTIF'";

        $result = $db->query($sql, [$idProdi]);

        return $result->getResultArray();

    }


    // OLD VERSION
    // public function getMahasiswaBelumDapatSidangByProdi($idProdi) {

    //     $db = \Config\Database::connect();

    //     $sql = "SELECT m.npm, m.nama, s.judul AS judul, s.klien as rekomendasi_klien, s.jenis as jenis_skripsi, b.nama AS nama_bidang, 

    //             d1.nama as nama_pembimbing1, d2.nama as nama_pembimbing2, d3.nama as nama_pembimbing_agama

    //             FROM mahasiswa AS m

    //             INNER JOIN skripsi AS s ON s.npm = m.npm

    //             INNER JOIN pengajuan_sidang AS ps ON ps.id_skripsi = s.id

    //             INNER JOIN bidang AS b ON b.id = s.id_bidang

    //             LEFT JOIN sidang_skripsi AS ss ON ss.id_skripsi = s.id

    //             -- JOIN DIPERBAIKI: Filter Role langsung di sini agar presisi
    //             INNER JOIN pembimbing as p1 on (p1.id_skripsi = s.id AND p1.role = 'Pembimbing Ilmu 1')
                
    //             -- LEFT JOIN Pembimbing 2 (Agar kalau tidak ada P2, data mhs tetap muncul)
    //             LEFT JOIN pembimbing as p2 on (p2.id_skripsi = s.id AND p2.role = 'Pembimbing Ilmu 2')

    //             INNER JOIN pembimbing as p3 on (p3.id_skripsi = s.id AND p3.role = 'Pembimbing Agama')

    //             INNER JOIN dosen as d1 on d1.id = p1.id_dosen

    //             LEFT JOIN dosen as d2 on d2.id = p2.id_dosen

    //             INNER JOIN dosen as d3 on d3.id = p3.id_dosen

    //             INNER JOIN semester as sem on sem.kode = s.kode_semester

    //             WHERE m.id_prodi = ? 

    //             AND s.tanggal_skripsi = (SELECT max(s2.tanggal_skripsi) FROM skripsi AS s2 WHERE s2.npm = m.npm)

    //             AND s.status = 'Dalam Pengerjaan'

    //             AND ps.status = 'DISETUJUI'

    //             AND p1.role = 'Pembimbing Ilmu 1'

    //             AND p2.role in ('Pembimbing Ilmu 2', null)

    //             AND p3.role = 'Pembimbing Agama'

    //             AND ss.id IS NULL
                
    //             AND sem.status = 'AKTIF'

    //             GROUP BY m.npm"; // <--- PERBAIKAN: Menambahkan Group By agar tidak duplikat

    //     $result = $db->query($sql, [$idProdi]);

    //     return $result->getResultArray();

    // }


    //Second Version
    // public function getMahasiswaBelumDapatSidangByProdi($idProdi) {
    //     $db = \Config\Database::connect();

    //     // KITA GUNAKAN 'DISTINCT' SEBAGAI PENGAMAN TAMBAHAN
    //     $sql = "SELECT DISTINCT m.npm, m.nama, s.judul AS judul, s.klien as rekomendasi_klien, s.jenis as jenis_skripsi, b.nama AS nama_bidang, 
    //             d1.nama as nama_pembimbing1, d2.nama as nama_pembimbing2, d3.nama as nama_pembimbing_agama

    //             FROM mahasiswa AS m
    //             INNER JOIN skripsi AS s ON s.npm = m.npm
    //             INNER JOIN bidang AS b ON b.id = s.id_bidang
    //             INNER JOIN semester as sem on sem.kode = s.kode_semester

    //             -- JOIN Pembimbing tetap sama
    //             INNER JOIN pembimbing as p1 on (p1.id_skripsi = s.id AND p1.role = 'Pembimbing Ilmu 1')
    //             LEFT JOIN pembimbing as p2 on (p2.id_skripsi = s.id AND p2.role = 'Pembimbing Ilmu 2')
    //             INNER JOIN pembimbing as p3 on (p3.id_skripsi = s.id AND p3.role = 'Pembimbing Agama')

    //             INNER JOIN dosen as d1 on d1.id = p1.id_dosen
    //             LEFT JOIN dosen as d2 on d2.id = p2.id_dosen
    //             INNER JOIN dosen as d3 on d3.id = p3.id_dosen

    //             -- LEFT JOIN untuk memastikan dia belum ada di jadwal sidang manapun
    //             LEFT JOIN sidang_skripsi AS ss ON ss.id_skripsi = s.id

    //             WHERE m.id_prodi = ? 
    //             AND s.tanggal_skripsi = (SELECT max(s2.tanggal_skripsi) FROM skripsi AS s2 WHERE s2.npm = m.npm)
    //             AND s.status = 'Dalam Pengerjaan'
    //             AND sem.status = 'AKTIF'

    //             -- PERUBAHAN UTAMA DI SINI:
    //             -- Jangan di-JOIN, tapi cek apakah ID Skripsi ini ada di daftar pengajuan yang disetujui
    //             -- Ini mencegah duplikasi baris jika ada banyak pengajuan
    //             AND s.id IN (
    //                 SELECT id_skripsi 
    //                 FROM pengajuan_sidang 
    //                 WHERE status = 'DISETUJUI'
    //             )

    //             -- Pastikan belum punya jadwal sidang
    //             AND ss.id IS NULL

    //             -- Order by nama biar rapi di dropdown
    //             ORDER BY m.nama ASC";

    //     $result = $db->query($sql, [$idProdi]);
    //     return $result->getResultArray();
    // }



    // Third Version
public function getMahasiswaBelumDapatSidangByProdi($idProdi) {
        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama, s.judul AS judul, s.klien as rekomendasi_klien, s.jenis as jenis_skripsi, b.nama AS nama_bidang, 
                
                -- GUNAKAN MAX() AGAR DATA GANDA DISATUKAN
                MAX(d1.nama) as nama_pembimbing1, 
                MAX(d2.nama) as nama_pembimbing2, 
                MAX(d3.nama) as nama_pembimbing_agama

                FROM mahasiswa AS m
                INNER JOIN skripsi AS s ON s.npm = m.npm
                INNER JOIN pengajuan_sidang AS ps ON ps.id_skripsi = s.id
                INNER JOIN bidang AS b ON b.id = s.id_bidang
                LEFT JOIN sidang_skripsi AS ss ON ss.id_skripsi = s.id

                -- JOIN Pembimbing
                INNER JOIN pembimbing as p1 on (p1.id_skripsi = s.id AND p1.role = 'Pembimbing Ilmu 1')
                LEFT JOIN pembimbing as p2 on (p2.id_skripsi = s.id AND p2.role = 'Pembimbing Ilmu 2')
                INNER JOIN pembimbing as p3 on (p3.id_skripsi = s.id AND p3.role = 'Pembimbing Agama')

                INNER JOIN dosen as d1 on d1.id = p1.id_dosen
                LEFT JOIN dosen as d2 on d2.id = p2.id_dosen
                INNER JOIN dosen as d3 on d3.id = p3.id_dosen

                INNER JOIN semester as sem on sem.kode = s.kode_semester

                WHERE m.id_prodi = ? 
                AND s.tanggal_skripsi = (SELECT max(s2.tanggal_skripsi) FROM skripsi AS s2 WHERE s2.npm = m.npm)
                AND s.status = 'Dalam Pengerjaan'
                AND ps.status = 'DISETUJUI'
                AND ss.id IS NULL
                AND sem.status = 'AKTIF'

                -- GROUP BY UNTUK MEMASTIKAN 1 BARIS PER MAHASISWA
                GROUP BY m.npm, m.nama, s.judul, s.klien, s.jenis, b.nama";

        $result = $db->query($sql, [$idProdi]);
        return $result->getResultArray();
    }



    public function getMahasiswaLolosSempro() {

        $db = \Config\Database::connect();

        $builder = $db->table("mahasiswa");



        return $builder->

        select("mahasiswa.npm as npm, mahasiswa.nama as nama_mahasiswa, mahasiswa.id_prodi as id_prodi, judul, d1.nama as dosen_usulan1, d2.nama as dosen_usulan2, status, bidang.nama as nama_bidang")->

        join("proposal", "proposal.npm = mahasiswa.npm")->

        join("bidang", "bidang.id = proposal.id_bidang")->

        join("dosen as d1", "d1.id = proposal.dosen_usulan1")->

        join("dosen as d2", "d2.id = proposal.dosen_usulan2")->

        getWhere([

            "proposal.status" => 'DITERIMA',

        ])->

        getResultArray();

    }



    public function getMahasiswaTanpaPembimbing() {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama as nama_mahasiswa, m.id_prodi, s.judul, b.nama as nama_bidang, 

        d1.nama as dosen_usulan1, d2.nama as dosen_usulan2

        from mahasiswa as m

        inner join skripsi as s on s.npm = m.npm

        inner join proposal as pro on pro.npm = m.npm

        inner join bidang as b on b.id = s.id_bidang

        left join pembimbing as pem on pem.id_skripsi = s.id

        inner join dosen as d1 on d1.id = pro.dosen_usulan1

        inner join dosen as d2 on d2.id = pro.dosen_usulan2

        inner join semester as sem on sem.kode = s.kode_semester

        where s.tanggal_skripsi = (select max(tanggal_skripsi) from skripsi as s2 where s2.npm = m.npm) and

        s.status = 'Dalam Pengerjaan' and

        pro.tanggal_upload = (select max(tanggal_upload) from proposal as pro2 where pro2.npm = m.npm) and

        pem.id is null and
        
        sem.status = 'AKTIF'";

        $result = $db->query($sql);

        return $result->getResultArray();

    }



    public function getMahasiswaDenganPembimbingByKodeSemester($kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama as nama_mahasiswa, m.id_prodi, s.judul, b.nama as nama_bidang, b.inisial as inisial_bidang,

        prod.nama as nama_prodi, prod.inisial as inisial_prodi, d1.id as id_pembimbing1, d2.id as id_pembimbing2, 

        d3.id as id_pembimbing_agama, s.id as id_skripsi, 

        (select count(id) from catatan_bimbingan where id_pembimbing in (pem1.id, pem2.id, pem3.id)) as total_bimbingan

        from mahasiswa as m

        inner join skripsi as s on s.npm = m.npm

        inner join bidang as b on b.id = s.id_bidang

        inner join program_studi as prod on prod.id = m.id_prodi

        inner join pembimbing as pem1 on pem1.id_skripsi = s.id

        inner join pembimbing as pem2 on pem2.id_skripsi = s.id

        inner join pembimbing as pem3 on pem3.id_skripsi = s.id

        inner join dosen as d1 on d1.id = pem1.id_dosen

        left join dosen as d2 on d2.id = pem2.id_dosen

        inner join dosen as d3 on d3.id = pem3.id_dosen

        where s.tanggal_skripsi = (select max(tanggal_skripsi) from skripsi as s2 where s2.npm = m.npm) and

        pem1.role = 'Pembimbing Ilmu 1' and

        pem2.role = 'Pembimbing Ilmu 2' and

        pem3.role = 'Pembimbing Agama' and

        s.kode_semester = ?

        order by s.tanggal_skripsi";

        $result = $db->query($sql, [$kodeSemester]);

        return $result->getResultArray();

    }



    public function getAllPembimbingMahasiswaByKodeSemester($kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama as nama_mahasiswa, m.id_prodi, s.judul, b.nama as nama_bidang, b.inisial as inisial_bidang,
        prod.nama as nama_prodi, prod.inisial as inisial_prodi, 
        d1.nama as nama_pembimbing_1, d1.inisial as inisial_pembimbing_1,
        d2.nama as nama_pembimbing_2, d2.inisial as inisial_pembimbing_2,
        d3.nama as nama_pembimbing_agama, d3.inisial as inisial_pembimbing_agama,
        s.id as id_skripsi,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem1.id and status = 'DISETUJUI') as jumlah_bimbingan_1,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem2.id and status = 'DISETUJUI') as jumlah_bimbingan_2,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem3.id and status = 'DISETUJUI') as jumlah_bimbingan_agama,
        (select count(id) from catatan_bimbingan where id_pembimbing in (pem1.id, pem2.id, pem3.id) and status = 'DISETUJUI') as total_bimbingan
        from mahasiswa as m
        inner join skripsi as s on s.npm = m.npm
        inner join bidang as b on b.id = s.id_bidang
        inner join program_studi as prod on prod.id = m.id_prodi
        inner join pembimbing as pem1 on pem1.id_skripsi = s.id
        inner join pembimbing as pem2 on pem2.id_skripsi = s.id
        inner join pembimbing as pem3 on pem3.id_skripsi = s.id
        inner join dosen as d1 on d1.id = pem1.id_dosen
        left join dosen as d2 on d2.id = pem2.id_dosen
        inner join dosen as d3 on d3.id = pem3.id_dosen
        where s.tanggal_skripsi = (select max(tanggal_skripsi) from skripsi as s2 where s2.npm = m.npm) and
        pem1.role = 'Pembimbing Ilmu 1' and
        pem2.role = 'Pembimbing Ilmu 2' and
        pem3.role = 'Pembimbing Agama' and
        s.kode_semester = ?
        order by s.tanggal_skripsi";

        $result = $db->query($sql, [$kodeSemester]);

        return $result->getResultArray();

    }


    public function getPembimbingMahasiswaByIdProdiAndKodeSemester($idProdi, $kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama as nama_mahasiswa, m.id_prodi, s.judul, b.nama as nama_bidang, b.inisial as inisial_bidang, 
        d1.nama as nama_pembimbing_1, d1.inisial as inisial_pembimbing_1,
        d2.nama as nama_pembimbing_2, d2.inisial as inisial_pembimbing_2,
        d3.nama as nama_pembimbing_agama, d3.inisial as inisial_pembimbing_agama,
        s.id as id_skripsi,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem1.id and status = 'DISETUJUI') as jumlah_bimbingan_1,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem2.id and status = 'DISETUJUI') as jumlah_bimbingan_2,
        (select count(id) from catatan_bimbingan where id_pembimbing = pem3.id and status = 'DISETUJUI') as jumlah_bimbingan_agama,
        (select count(id) from catatan_bimbingan where id_pembimbing in (pem1.id, pem2.id, pem3.id) and status = 'DISETUJUI') as total_bimbingan
        from mahasiswa as m
        inner join skripsi as s on s.npm = m.npm
        inner join bidang as b on b.id = s.id_bidang
        inner join program_studi as prod on prod.id = m.id_prodi
        inner join pembimbing as pem1 on pem1.id_skripsi = s.id
        inner join pembimbing as pem2 on pem2.id_skripsi = s.id
        inner join pembimbing as pem3 on pem3.id_skripsi = s.id
        inner join dosen as d1 on d1.id = pem1.id_dosen
        left join dosen as d2 on d2.id = pem2.id_dosen
        inner join dosen as d3 on d3.id = pem3.id_dosen
        where s.tanggal_skripsi = (select max(tanggal_skripsi) from skripsi as s2 where s2.npm = m.npm) and
        pem1.role = 'Pembimbing Ilmu 1' and
        pem2.role = 'Pembimbing Ilmu 2' and
        pem3.role = 'Pembimbing Agama' and
        prod.id = ? and
        s.kode_semester = ?
        order by s.tanggal_skripsi";

        $result = $db->query($sql, [$idProdi, $kodeSemester]);

        return $result->getResultArray();
    }

    public function getMahasiswaInfo($npm) {
        $db = \Config\Database::connect();

        $sql = "SELECT m.*, p.nama as nama_prodi, p.inisial as inisial_prodi, a.id as id_akun
        FROM mahasiswa as m
        inner join program_studi as p on p.id = m.id_prodi
        inner join akun as a on a.email = m.email
        where m.npm = ?";

        $result = $db->query($sql, [$npm]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }



    public function getPembimbingMahasiswaByProdi($id_prodi) {

        $db = \Config\Database::connect();

        $sql = "SELECT m.npm, m.nama as nama_mahasiswa, m.id_prodi, s.judul, b.nama as nama_bidang, b.inisial as inisial_bidang,

        d1.nama as nama_pembimbing_1, d1.inisial as inisial_pembimbing_1,

        d2.nama as nama_pembimbing_2, d2.inisial as inisial_pembimbing_2,

        d3.nama as nama_pembimbing_agama, d3.inisial as inisial_pembimbing_agama,

        s.id as id_skripsi,

        (select count(id) from catatan_bimbingan where id_pembimbing = pem1.id and status = 'DISETUJUI') as jumlah_bimbingan_1,

        (select count(id) from catatan_bimbingan where id_pembimbing = pem2.id and status = 'DISETUJUI') as jumlah_bimbingan_2,

        (select count(id) from catatan_bimbingan where id_pembimbing = pem3.id and status = 'DISETUJUI') as jumlah_bimbingan_agama,

        (select count(id) from catatan_bimbingan where id_pembimbing in (pem1.id, pem2.id, pem3.id) and status = 'DISETUJUI') as total_bimbingan

        from mahasiswa as m

        inner join skripsi as s on s.npm = m.npm

        inner join bidang as b on b.id = s.id_bidang

        inner join program_studi as prod on prod.id = m.id_prodi

        inner join pembimbing as pem1 on pem1.id_skripsi = s.id

        inner join pembimbing as pem2 on pem2.id_skripsi = s.id

        inner join pembimbing as pem3 on pem3.id_skripsi = s.id

        inner join dosen as d1 on d1.id = pem1.id_dosen

        left join dosen as d2 on d2.id = pem2.id_dosen

        inner join dosen as d3 on d3.id = pem3.id_dosen

        where s.tanggal_skripsi = (select max(tanggal_skripsi) from skripsi as s2 where s2.npm = m.npm) and

        pem1.role = 'Pembimbing Ilmu 1' and

        pem2.role = 'Pembimbing Ilmu 2' and

        pem3.role = 'Pembimbing Agama' and 

        prod.id = ?

        order by s.tanggal_skripsi";

        $result = $db->query($sql, [$id_prodi]);

        return $result->getResultArray();

    }

}