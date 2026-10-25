<?php

namespace App\Models;



use CodeIgniter\Model;



class PenilaianSidangNewModel extends Model
{

    protected $table = "penilaian_sidang_new";

    protected $primaryKey = "id_rubrik";

    protected $useAutoIncrement = false;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_rubrik', 'id_sidang_skripsi', 'id_penilai', 'peran_penilai', 'nilai'];

    protected $useTimestamps = false;

    public function getPenilaianSidang($idPenilai, $peranPenilai, $idSidangSkripsi)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT psn.id_rubrik, psn.nilai from penilaian_sidang_new as psn where psn.id_sidang_skripsi = ? and id_penilai = ? and peran_penilai = ? order by psn.id_rubrik";

        $result = $db->query($sql, [$idSidangSkripsi, $idPenilai, $peranPenilai]);

        return $result->getResultArray();
    }

    public function upsertNilaiSidang($arrayNilai)
    {
        $db = \Config\Database::connect();

        $sql = "REPLACE INTO penilaian_sidang_new(id_rubrik, id_penilai, peran_penilai, nilai, id_sidang_skripsi)
        values(?, ?, ?, ?, ?)";

        foreach ($arrayNilai as $an) {
            $db->query($sql, [$an['id_rubrik'], $an['id_penilai'], $an['peran_penilai'], $an['nilai'], $an['id_sidang_skripsi']]);
        }

    }

    public function getPeranPenilaiByIdSidangSkripsi($idSidangSkripsi)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT psn.peran_penilai
        FROM penilaian_sidang_new AS psn
        WHERE psn.id_sidang_skripsi = ?
        GROUP BY peran_penilai";

        $result = $db->query($sql, [$idSidangSkripsi]);
        return $result->getResultArray();
    }

    public function getPreviousNilaiSidang($idSidangSkripsi, $idPenilai, $peranPenilai)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT * FROM penilaian_sidang_new 
        where id_sidang_skripsi = ? and
        id_penilai = ? and
        peran_penilai = ?";

        $result = $db->query($sql, [$idSidangSkripsi, $idPenilai, $peranPenilai]);

        return $result->getResultArray();
    }

    public function getAverageByIdSidang($idSkripsi, $idSidangSkripsi, $idPenguji, $idPembimbing1, $idPembimbing2, $idPembimbingAgama)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT 
        -- ==========================
        -- 1. BAGIAN PENGUJI
        -- ==========================
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) as section1_avg_penguji,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) as section2_avg_penguji,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) as section3_avg_penguji,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) as section4_avg_penguji,
        
        -- [KOLOM INI YANG SEBELUMNYA HILANG]
        (
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) * 0.20 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) * 0.25 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) * 0.15 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) * 0.40
        ) as total_penguji,

        -- ==========================
        -- 2. BAGIAN PEMBIMBING 1
        -- ==========================
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) as section1_avg_pembimbing1,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) as section2_avg_pembimbing1,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) as section3_avg_pembimbing1,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) as section4_avg_pembimbing1,

        -- [KOLOM INI YANG SEBELUMNYA HILANG]
        (
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) * 0.20 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) * 0.25 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) * 0.15 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) * 0.40
        ) as total_pembimbing1,

        -- ==========================
        -- 3. BAGIAN PEMBIMBING 2
        -- ==========================
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) as section1_avg_pembimbing2,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) as section2_avg_pembimbing2,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) as section3_avg_pembimbing2,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) as section4_avg_pembimbing2,

        -- [KOLOM INI YANG SEBELUMNYA HILANG]
        (
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) * 0.20 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) * 0.25 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) * 0.15 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) * 0.40
        ) as total_pembimbing2,

        -- ==========================
        -- 4. BAGIAN PEMBIMBING AGAMA
        -- ==========================
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) as section1_avg_pembimbing_agama,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) as section2_avg_pembimbing_agama,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) as section3_avg_pembimbing_agama,
        COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) as section4_avg_pembimbing_agama,

        -- [KOLOM INI YANG SEBELUMNYA HILANG]
        (
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 1 AND 3), 0) * 0.20 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 4 AND 6), 0) * 0.25 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 7 AND 9), 0) * 0.15 +
            COALESCE((select round(avg(psn.nilai), 2) from penilaian_sidang_new psn where psn.id_penilai = ? and psn.id_sidang_skripsi = ? and psn.id_rubrik BETWEEN 10 AND 12), 0) * 0.40
        ) as total_pembimbing_agama,

        -- ==========================
        -- NILAI AKHIR (Dengan LIMIT 1 pada pembagi agar aman dari duplikat)
        -- ==========================
        (
            SELECT ROUND(
                (
                    (IFNULL((select sum(nilai) from penilaian_sidang_new where id_penilai = ? and peran_penilai = 'Dosen Penguji' and id_sidang_skripsi = ?), 0) * 0.40) +
                    (IFNULL((select sum(nilai) from penilaian_sidang_new where id_penilai = ? and peran_penilai = 'Pembimbing Ilmu 1' and id_sidang_skripsi = ?), 0) * 0.35) +
                    (IFNULL((select sum(nilai) from penilaian_sidang_new where id_penilai = ? and peran_penilai = 'Pembimbing Ilmu 2' and id_sidang_skripsi = ?), 0) * 0.35) +
                    (IFNULL((select sum(nilai) from penilaian_sidang_new where id_penilai = ? and peran_penilai = 'Pembimbing Agama' and id_sidang_skripsi = ?), 0) * 0.15)
                ) 
                / 
                (
                    if((select p.id_dosen from pembimbing as p where p.id_skripsi = ? and p.role = 'Pembimbing Ilmu 1' LIMIT 1) is not null, 1, 0) + 
                    if((select p.id_dosen from pembimbing as p where p.id_skripsi = ? and p.role = 'Pembimbing Ilmu 2' LIMIT 1) is not null, 1, 0) + 
                    if((select p.id_dosen from pembimbing as p where p.id_skripsi = ? and p.role = 'Pembimbing Agama' LIMIT 1) is not null, 1, 0) + 
                    if((select ss.dosen_penguji from sidang_skripsi as ss where ss.id_skripsi = ? LIMIT 1) is not null, 1, 0)
                )
            , 2)
        ) as nilai_akhir";

        // Binding Parameter (Harus Urut Sesuai Tanda Tanya di SQL)
        $params = [
            // 1. Penguji (8 params: 4 sections x 2)
            $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi,
            // 2. Total Penguji (8 params)
            $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi, $idPenguji, $idSidangSkripsi,

            // 3. Pembimbing 1 (8 params)
            $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi,
            // 4. Total Pembimbing 1 (8 params)
            $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi, $idPembimbing1, $idSidangSkripsi,

            // 5. Pembimbing 2 (8 params)
            $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi,
            // 6. Total Pembimbing 2 (8 params)
            $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi, $idPembimbing2, $idSidangSkripsi,

            // 7. Agama (8 params)
            $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi,
            // 8. Total Agama (8 params)
            $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi, $idPembimbingAgama, $idSidangSkripsi,

            // 9. Nilai Akhir (8 params)
            $idPenguji, $idSidangSkripsi,
            $idPembimbing1, $idSidangSkripsi,
            $idPembimbing2, $idSidangSkripsi,
            $idPembimbingAgama, $idSidangSkripsi,
            $idSkripsi, $idSkripsi, $idSkripsi, $idSkripsi
        ];

        $result = $db->query($sql, $params);
        return $result->getRowArray();
    }

    public function getTotalProyekByIdSidang($idSkripsi, $idSidangSkripsi, $jenisSkripsi)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT

        ROUND((select ifnull(sum(psp.konten / 100 * ps.nilai), 0) from penilaian_sidang_new as ps 
        inner join penilaian_sidang_param as psp on ps.id_rubrik = psp.urutan and psp.kode = 'TI-PENGUJI-BOBOT-2023'
        where ps.peran_penilai = 'Penguji' and ps.id_sidang_skripsi = ? ), 2) as total_penguji,

        ROUND((select ifnull(sum(psp.konten / 100 * ps.nilai), 0) from penilaian_sidang_new as ps 
        inner join penilaian_sidang_param as psp on ps.id_rubrik = psp.urutan and psp.kode = 'TI-PEMBIMBINGILMU-BOBOT-2023'
        where ps.peran_penilai = 'Pembimbing Ilmu' and ps.id_sidang_skripsi = ? ), 2) as total_pembimbing_ilmu,

        ROUND((select ifnull(sum(psp.konten / 100 * ps.nilai), 0) from penilaian_sidang_new as ps 
        inner join penilaian_sidang_param as psp on ps.id_rubrik = psp.urutan and psp.kode = 'TI-PEMBIMBINGAGAMA-BOBOT-2023'
        where ps.peran_penilai = 'Pembimbing Agama' and ps.id_sidang_skripsi = ? ), 2) as total_pembimbing_agama,

        ROUND((select ifnull(sum(psp.konten / 100 * ps.nilai), 0) from penilaian_sidang_new as ps 
        inner join penilaian_sidang_param as psp on ps.id_rubrik = psp.urutan and psp.kode = 'TI-KLIEN-BOBOT-2023'
        where ps.peran_penilai = 'Klien' and ps.id_sidang_skripsi = ? ), 2) as total_klien,

        ROUND( (select total_penguji + total_pembimbing_ilmu + total_pembimbing_agama + total_klien), 2) as nilai_akhir";

        $result = $db->query($sql, [$idSidangSkripsi, $idSidangSkripsi, $idSidangSkripsi, $idSidangSkripsi]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }
        return $result->getResultArray()[0];
    }

    public function getNilaiAkhirSidangProyekById($idSidangSkripsi)
    {
        $db = \Config\Database::connect();

        $sql = "SELECT round(sum( (select konten from penilaian_sidang_param where urutan = id_rubrik and kode like 'TI-%-BOBOT-2023') * nilai / 100), 2) as nilai_akhir
        FROM penilaian_sidang_new
        where id_sidang_skripsi = ?
        group by id_sidang_skripsi";

        $result = $db->query($sql, [$idSidangSkripsi]);
        return $result->getResultArray()[0];
    }
}