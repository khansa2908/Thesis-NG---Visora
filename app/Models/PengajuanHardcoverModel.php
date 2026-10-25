<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanHardcoverModel extends Model
{
    protected $table = 'pengajuan_hardcover';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_skripsi', 'file_hardcover', 'tanggal_pengajuan', 'status', 'komentar_penolakan', 'verifikator'];
    protected $useTimestamps = false;

    // Mengambil semua list pengajuan untuk tabel (Join ke Mahasiswa & Prodi)
    public function getAllPengajuan()
    {
        return $this->select('pengajuan_hardcover.*, skripsi.judul, mahasiswa.nama as nama_mhs, mahasiswa.npm, program_studi.nama as nama_prodi')
                    ->join('skripsi', 'skripsi.id = pengajuan_hardcover.id_skripsi')
                    ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                    ->join('program_studi', 'program_studi.id = mahasiswa.id_prodi')
                    ->orderBy('tanggal_pengajuan', 'DESC')
                    ->findAll();
    }

    public function getAllPengajuanByKodeSemester($kodeSemester)
    {
        return $this->select('pengajuan_hardcover.*, skripsi.judul, skripsi.kode_semester, mahasiswa.nama as nama_mhs, mahasiswa.npm, program_studi.nama as nama_prodi')
                    ->join('skripsi', 'skripsi.id = pengajuan_hardcover.id_skripsi')
                    ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                    ->join('program_studi', 'program_studi.id = mahasiswa.id_prodi')
                    ->where('skripsi.kode_semester', $kodeSemester) // Filter Semester
                    ->orderBy('tanggal_pengajuan', 'DESC')
                    ->findAll();
    }

    // Mengambil detail satu pengajuan
    public function getDetailPengajuan($id)
    {
        return $this->select('pengajuan_hardcover.*, skripsi.judul, mahasiswa.nama as nama_mhs, mahasiswa.npm, program_studi.nama as nama_prodi')
                    ->join('skripsi', 'skripsi.id = pengajuan_hardcover.id_skripsi')
                    ->join('mahasiswa', 'mahasiswa.npm = skripsi.npm')
                    ->join('program_studi', 'program_studi.id = mahasiswa.id_prodi')
                    ->where('pengajuan_hardcover.id', $id)
                    ->first();
    }


    // protected $DBGroup          = 'default';
    // protected $table            = 'pengajuanhardcovers';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    // protected $insertID         = 0;
    // protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    // protected $allowedFields    = [];

    // // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];
}
