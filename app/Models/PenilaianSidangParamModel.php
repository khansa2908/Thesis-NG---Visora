<?php

namespace App\Models;



use CodeIgniter\Model;



class PenilaianSidangParamModel extends Model
{

    protected $table = "penilaian_sidang_param";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['kode', 'konten', 'tipe', 'urutan'];

    protected $useTimestamps = false;

    public function getRubrikPdsi2023()
    {

        $db = \Config\Database::connect();

        $sql = "SELECT 
                    d.urutan as urutan, d.konten as deskripsi, sm.konten as desk_sangat_memuaskan, 
                    m.konten as desk_memuaskan, km.konten as desk_kurang_memuaskan, 
                    tm.konten as desk_tidak_memuaskan, h.konten as header, b.konten as bobot
                FROM
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-DETAIL-DESKRIPSI-2023') d,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-DESKRIPSI-SANGAT-MEMUASKAN-2023') sm,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-DESKRIPSI-MEMUASKAN-2023') m,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-DESKRIPSI-KURANG-MEMUASKAN-2023') km,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-DESKRIPSI-TIDAK-MEMUASKAN-2023') tm
                LEFT join 
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-HEADER-DESKRIPSI-2023') h ON tm.urutan = h.urutan
                LEFT join 
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'PDSI-BOBOT-2023') b ON tm.urutan = b.urutan
                WHERE 
                    d.urutan = sm.urutan AND sm.urutan = m.urutan AND m.urutan = km.urutan AND km.urutan = tm.urutan
                ORDER BY
                    d.urutan";

        $result = $db->query($sql);

        return $result->getResultArray();

    }

    public function getRubrikByPemangkuKepentingan($pemangkuKepentingan)
    {
        $pemangkuKepentingan = strtoupper(str_replace(" ", "", $pemangkuKepentingan));

        $db = \Config\Database::connect();

        $sql = "SELECT
                    d.urutan as urutan, d.konten as deskripsi, b.konten as bobot, i.konten as indikator
                FROM
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'TI-$pemangkuKepentingan-DESKRIPSI-2023') d,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'TI-$pemangkuKepentingan-BOBOT-2023') b,
                    (SELECT * FROM penilaian_sidang_param WHERE kode = 'TI-$pemangkuKepentingan-INDIKATOR-2023') i
                WHERE 
                    d.urutan = b.urutan AND b.urutan = i.urutan
                ORDER BY
                    urutan";

        $result = $db->query($sql);

        return $result->getResultArray();
    }

    public function getAllSumBobot()
    {
        $db = \Config\Database::connect();

        $sql = "SELECT 
                    count(id) as count_poin_penilaian, SUM(KONTEN) as sum_bobot, 'PEMBIMBING ILMU' as pemangku_kepentingan 
                    from penilaian_sidang_param psp 
                    where kode = 'TI-PEMBIMBINGILMU-BOBOT-2023'
                union 
                    select 
                    count(id) as count_poin_penilaian, SUM(KONTEN) as sum_bobot, 'PEMBIMBING AGAMA' as pemangku_kepentingan 
                    from penilaian_sidang_param psp 
                    where kode = 'TI-PEMBIMBINGAGAMA-BOBOT-2023'
                union 
                    select 
                    count(id) as count_poin_penilaian, SUM(KONTEN) as sum_bobot, 'PENGUJI' as pemangku_kepentingan 
                    from penilaian_sidang_param psp 
                    where kode = 'TI-PENGUJI-BOBOT-2023'
                union 
                    select 
                    count(id) as count_poin_penilaian, SUM(KONTEN) as sum_bobot, 'KLIEN' as pemangku_kepentingan 
                    from penilaian_sidang_param psp 
                    where kode = 'TI-KLIEN-BOBOT-2023'";

        $result = $db->query($sql);

        return $result->getResultArray();
    }
}