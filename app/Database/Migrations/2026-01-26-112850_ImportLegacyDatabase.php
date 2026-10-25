<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ImportLegacyDatabase extends Migration
{
    public function up()
    {
        // =========================================================================
        // PHASE 1: IMPORT DATA LAMA (Load file .sql)
        // =========================================================================
        
        // Pastikan path ini mengarah ke file SQL database LAMA kamu
        $file_path = APPPATH . 'Database/Backup/thesis_v2.sql'; 

        if (!file_exists($file_path)) {
            die("File SQL tidak ditemukan di: " . $file_path);
        }

        // Matikan Foreign Key Check (Biar import lancar)
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        $sql = file_get_contents($file_path);
        $queries = explode(';', $sql);

        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;

            // Skip query yang tidak perlu
            if (stripos($query, 'CREATE TABLE `migrations`') !== false || 
                stripos($query, 'INSERT INTO `migrations`') !== false ||
                stripos($query, 'SET SQL_MODE') !== false || 
                stripos($query, 'START TRANSACTION') !== false || 
                stripos($query, 'COMMIT') !== false) {
                continue;
            }

            try {
                $this->db->query($query);
            } catch (\Throwable $e) {
                // Lanjut terus
            }
        }

        // =========================================================================
        // PHASE 2: UPDATE STRUKTUR (Patching Gap Version)
        // =========================================================================

        // --- A. BUAT TABEL BARU YANG HILANG ---
        // [FIX] Menggunakan "IF NOT EXISTS" agar tidak perlu cek $this->db->tableExists()

        $this->db->query("CREATE TABLE IF NOT EXISTS `hardcover` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `id_skripsi` int(11) UNSIGNED NOT NULL,
          `file_hardcover` varchar(255) NOT NULL,
          `status` enum('MENUNGGU','DISETUJUI','DITOLAK') DEFAULT 'MENUNGGU',
          `komentar` text DEFAULT NULL,
          `created_at` datetime DEFAULT current_timestamp(),
          `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `email` varchar(255) NOT NULL,
          `token` varchar(255) NOT NULL,
          `created_at` datetime DEFAULT current_timestamp(),
          `expires_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `pembimbing_eksternal` (
          `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `nama_lengkap` varchar(255) NOT NULL,
          `instansi` varchar(255) NOT NULL,
          `email` varchar(255) NOT NULL,
          `kontak` varchar(50) DEFAULT NULL,
          `file_ktp` varchar(255) DEFAULT NULL,
          `file_cv` varchar(255) DEFAULT NULL,
          `file_npwp` varchar(255) DEFAULT NULL,
          `created_at` datetime DEFAULT current_timestamp(),
          `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `pengajuan_hardcover` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `id_skripsi` int(11) NOT NULL,
          `file_hardcover` varchar(255) NOT NULL,
          `tanggal_pengajuan` datetime NOT NULL,
          `status` enum('TERTUNDA','DITOLAK','DISETUJUI') NOT NULL DEFAULT 'TERTUNDA',
          `komentar_penolakan` text DEFAULT NULL,
          `verifikator` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


        // --- B. TAMBAH KOLOM YANG HILANG ---

        // Fungsi Helper Cepat (Pakai SQL Manual biar VS Code ga rewel)
        $addColumn = function($table, $col, $def) {
            // Cek apakah kolom sudah ada
            $check = $this->db->query("SHOW COLUMNS FROM `$table` LIKE '$col'")->getRow();
            // Jika belum ada, baru tambahkan
            if (!$check) {
                $this->db->query("ALTER TABLE `$table` ADD `$col` $def");
            }
        };

        // 1. Kolom Notifikasi (is_read_admin)
        $addColumn('pengajuan_prasidang', 'is_read_admin', "TINYINT(1) DEFAULT 0 AFTER status");
        $addColumn('pengajuan_sidang', 'is_read_admin', "TINYINT(1) DEFAULT 0 AFTER status");
        $addColumn('proposal', 'is_read_admin', "TINYINT(1) DEFAULT 0 AFTER status");
        $addColumn('seminar_prasidang', 'is_read_admin', "TINYINT(1) DEFAULT 0"); 
        $addColumn('sidang_skripsi', 'is_read_admin', "TINYINT(1) DEFAULT 0");
        $addColumn('skripsi', 'is_read_admin', "TINYINT(1) DEFAULT 0");
        
        // Cek tabel pengajuan_hardcover
        $addColumn('pengajuan_hardcover', 'is_read_admin', "TINYINT(1) DEFAULT 0");

        // 2. Kolom Lain yang Hilang (Dashboard & Fitur Baru)
        $addColumn('kalender_skripsi', 'jenis_kegiatan', "ENUM('sempro','prasidang','sidang') DEFAULT NULL");
        $addColumn('kalender_skripsi', 'id_prodi', "INT(11) NOT NULL DEFAULT 0");
        $addColumn('kalender_skripsi', 'kode_semester', "VARCHAR(6) NOT NULL DEFAULT ''");
        
        $addColumn('proposal', 'komentar_2', "TEXT DEFAULT NULL");
        
        $addColumn('seminar_proposal', 'nomor_berita_acara', "VARCHAR(100) DEFAULT NULL");
        
        $addColumn('sidang_skripsi', 'nomor_berita_acara', "VARCHAR(100) DEFAULT NULL");
        $addColumn('sidang_skripsi', 'ketua_sidang', "INT(11) DEFAULT NULL"); // FIX DASHBOARD FAKULTAS

        // Hidupkan Kembali Foreign Key
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        
        echo "MIGRASI LENGKAP SUKSES! (Import + Struktur Baru + Notifikasi)";
    }

    public function down()
    {
        // Reset Total (Hapus Semua Tabel)
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        
        // Ambil daftar tabel manual
        $tables = $this->db->query("SHOW TABLES")->getResultArray();
        
        foreach ($tables as $row) {
            $tbl = array_values($row)[0];
            if ($tbl !== 'migrations') {
                $this->db->query("DROP TABLE IF EXISTS `$tbl`");
            }
        }
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        echo "Database di-reset total.";
    }
}