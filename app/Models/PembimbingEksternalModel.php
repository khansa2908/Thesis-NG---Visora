<?php

namespace App\Models;

use CodeIgniter\Model;

class PembimbingEksternalModel extends Model
{
    protected $table            = 'pembimbing_eksternal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['nama_lengkap', 'instansi', 'email', 'kontak', 'file_ktp', 'file_cv', 'file_npwp'];
    protected $useTimestamps    = true;
}