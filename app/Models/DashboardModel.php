<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    // Kita hapus parameter semester agar semua data tampil
    public function getStatusTaPerProdi($idProdi)
    {
        $db = \Config\Database::connect();

        $query = "
            -- 1. HITUNG SEMINAR PROPOSAL
            -- (Join ini SUDAH TERBUKTI BERHASIL di debugger kamu)
            SELECT 'Seminar Proposal' as status, COUNT(DISTINCT m.npm) as jumlah
            FROM mahasiswa m
            JOIN proposal p ON p.npm = m.npm 
            JOIN seminar_proposal sp ON sp.id_proposal = p.id
            WHERE m.id_prodi = ? 
            
            UNION ALL
            
            -- 2. HITUNG SEMINAR PRA SIDANG
            -- (Menggunakan id_skripsi sesuai screenshot tabel kamu)
            SELECT 'Seminar Pra Sidang' as status, COUNT(DISTINCT m.npm) as jumlah
            FROM mahasiswa m
            JOIN skripsi s ON s.npm = m.npm
            JOIN seminar_prasidang spr ON spr.id_skripsi = s.id 
            WHERE m.id_prodi = ? 
            
            UNION ALL
            
            -- 3. HITUNG SIDANG AKHIR
            -- (Join ini SUDAH TERBUKTI BERHASIL di debugger kamu)
            SELECT 'Sidang Akhir' as status, COUNT(DISTINCT m.npm) as jumlah
            FROM mahasiswa m
            JOIN skripsi s ON s.npm = m.npm
            JOIN sidang_skripsi ss ON ss.id_skripsi = s.id
            WHERE m.id_prodi = ? 
        ";

        // Parameter hanya ID PRODI
        $params = [$idProdi, $idProdi, $idProdi];

        return $db->query($query, $params)->getResultArray();
    }

    // FUNGSI UNTUK USER STORY 35 (Beban Reviewer Sempro)
    public function getBebanReviewerSempro($semester, $id_prodi)
    {
        // 1. Kita pilih tabel Dosen sebagai data utama
        $this->builder = $this->db->table('dosen');

        // 2. Ambil Nama atau Inisial Dosen
        $this->builder->select('dosen.nama, dosen.inisial');

        // 3. Subquery Sakti: Menghitung berapa kali dosen ini nguji di semester terpilih
        // Logika: Hitung data di seminar_proposal DIMANA dosen ini jadi Penguji 1 ATAU Penguji 2
        // DAN proposalnya ada di semester yang dipilih.
        $subquery = "(SELECT COUNT(*) FROM seminar_proposal sp 
                      JOIN proposal p ON sp.id_proposal = p.id 
                      WHERE p.kode_semester = '$semester' 
                      AND (sp.dosen_penguji1 = dosen.id OR sp.dosen_penguji2 = dosen.id)) as jumlah_uji";

        $this->builder->select($subquery);

        // 4. Filter Dosen berdasarkan Prodinya (1 = TI, 2 = PDSI)
        $this->builder->where('dosen.id_prodi', $id_prodi);

        // 5. Urutkan dari yang paling rajin nguji (Opsional)
        $this->builder->orderBy('jumlah_uji', 'DESC');

        return $this->builder->get()->getResultArray();
    }

    public function getBebanReviewerPrasidang($semester, $id_prodi)
    {
        $this->builder = $this->db->table('dosen');
        $this->builder->select('dosen.nama, dosen.inisial');

        // Hitung dari seminar_prasidang (Kolom dosen_reviewer)
        // Kita join ke tabel skripsi untuk cek semester
        $subquery = "(SELECT COUNT(*) FROM seminar_prasidang spr 
                      JOIN skripsi s ON spr.id_skripsi = s.id 
                      WHERE s.kode_semester = '$semester' 
                      AND spr.dosen_reviewer = dosen.id) as jumlah_uji";

        $this->builder->select($subquery);
        $this->builder->where('dosen.id_prodi', $id_prodi);
        $this->builder->orderBy('jumlah_uji', 'DESC');

        return $this->builder->get()->getResultArray();
    }

    public function getBebanPengujiSidang($semester, $id_prodi)
    {
        $this->builder = $this->db->table('dosen');
        $this->builder->select('dosen.nama, dosen.inisial');

        // HITUNG beban dari kolom 'ketua_sidang' DAN 'dosen_penguji'
        $subquery = "(SELECT COUNT(*) FROM sidang_skripsi ss 
                      JOIN skripsi s ON ss.id_skripsi = s.id 
                      WHERE s.kode_semester = '$semester' 
                      AND (ss.ketua_sidang = dosen.id OR ss.dosen_penguji = dosen.id)) as jumlah_uji";

        $this->builder->select($subquery);
        $this->builder->where('dosen.id_prodi', $id_prodi);
        $this->builder->orderBy('jumlah_uji', 'DESC');

        return $this->builder->get()->getResultArray();
    }

    public function getAllSemesters()
    {
        return $this->db->table('semester')->orderBy('kode', 'DESC')->get()->getResultArray();
    }
}
