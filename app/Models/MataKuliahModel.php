<?php

namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'kode_matkul';
    protected $returnType = 'array';

    protected $allowedFields = [
        'kode_matkul',
        'nama_matkul',
        'id_prodi',
    ];

    public function getMataKuliahByProdi(int $idProdi): array
    {
        return $this->db->table('mata_kuliah mk')
            ->select(
                'mk.kode_matkul,
                 mk.nama_matkul,
                 mk.id_prodi,
                 dk.kategori,
                 k.nama_kurikulum,
                 k.tahun_berlaku,
                 dk.semester_kurikulum'
            )
            ->join(
                'detail_kurikulum dk',
                'dk.kode_matkul = mk.kode_matkul',
                'left'
            )
            ->join(
                'kurikulum k',
                'k.id_kurikulum = dk.id_kurikulum',
                'left'
            )
            ->where('mk.id_prodi', $idProdi)
            ->orderBy('mk.kode_matkul', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getKurikulumByProdi(int $idProdi): array
    {
        return $this->db->table('kurikulum')
            ->where('id_prodi', $idProdi)
            ->orderBy('tahun_berlaku', 'DESC')
            ->get()
            ->getResultArray();
    }
}
