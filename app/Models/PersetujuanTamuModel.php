<?php 

namespace App\Models;



use CodeIgniter\Model;



class PersetujuanTamuModel extends Model 

{

    protected $table = "persetujuan_tamu";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'inisial', 'instansi', 'email', 'username', 'password', 'tanggal_pendaftaran'];

    protected $useTimestamps = false;

    public function getAllPersetujuanAkunTamu() {
        $db = \Config\Database::connect();

        $sql = "SELECT pt.id, pt.nama, pt.inisial, pt.instansi, pt.email, pt.tanggal_pendaftaran
        FROM persetujuan_tamu as pt";

        $result = $db->query($sql);    
        return $result->getResultArray();
    }
}