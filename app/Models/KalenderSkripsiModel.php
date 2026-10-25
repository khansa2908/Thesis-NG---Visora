<?php 

namespace App\Models;



use CodeIgniter\Model;



class KalenderSkripsiModel extends Model 

{

    protected $table = "kalender_skripsi";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama_kegiatan', 'jenis_kegiatan', 'id_prodi', 'kode_semester', 'tanggal_mulai', 'tanggal_selesai'];

    protected $useTimestamps = false;


    /**
     * Cek apakah pendaftaran DIBUKA untuk mahasiswa prodi tertentu?
     * Return: true (Buka) atau false (Tutup)
     */
    public function isRegistrationOpen($jenis, $idProdi, $kodeSemester)
    {
        $now = date('Y-m-d H:i:s');
        
        // Cari jadwal aktif untuk jenis & prodi tersebut
        $schedule = $this->where('jenis_kegiatan', $jenis)
                         ->where('id_prodi', $idProdi)
                         ->where('kode_semester', $kodeSemester)
                         ->where('tanggal_mulai <=', $now)
                         ->where('tanggal_selesai >=', $now)
                         ->orderBy('id', 'DESC') // Prioritaskan jadwal yang paling baru dibuat
                         ->first();

        // Jika ada jadwalnya, berarti BUKA.
        return $schedule ? true : false;
    }

    // Ambil info deadline untuk Warning
    public function getUpcomingDeadline($jenis, $idProdi, $kodeSemester)
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('jenis_kegiatan', $jenis)
                    ->where('id_prodi', $idProdi)
                    ->where('kode_semester', $kodeSemester)
                    ->where('tanggal_selesai >=', $now)
                    ->orderBy('tanggal_selesai', 'ASC')
                    ->first();
    }

    /**
     * [BARU] Mengambil data jadwal aktif berdasarkan jenis, prodi, dan semester.
     * Digunakan untuk mengecek apakah tombol harus disable atau form harus hidden.
     */
    public function getJadwalAktif($jenis, $idProdi, $kodeSemester)
    {
        $now = date('Y-m-d H:i:s');
        
        return $this->where('jenis_kegiatan', $jenis)
                    ->where('id_prodi', $idProdi)
                    ->where('kode_semester', $kodeSemester)
                    ->where('tanggal_mulai <=', $now)
                    ->where('tanggal_selesai >=', $now)
                    ->orderBy('id', 'DESC') // [KUNCI]: Ambil inputan TERBARU jika ada jadwal ganda
                    ->first(); 
    }
}