<?php 

namespace App\Models;



use CodeIgniter\Model;



class FakultasModel extends Model 

{

    protected $table = "fakultas";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'inisial', 'email'];

    protected $useTimestamps = false;

    public function getFakultasInfo($idFakultas) {
        $db = \Config\Database::connect();

        $sql = "SELECT f.*, a.id as id_akun
        FROM fakultas as f
        inner join akun as a on a.email = f.email
        where f.id = ?";

        $result = $db->query($sql, [$idFakultas]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

}