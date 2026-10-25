<?php 

namespace App\Models;



use CodeIgniter\Model;



class TenagaKependidikanModel extends Model 

{

    protected $table = "tenaga_kependidikan";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'email'];

    protected $useTimestamps = false;

    public function getTendikInfo($idTendik) {
        $db = \Config\Database::connect();

        $sql = "SELECT tk.*, a.id as id_akun
        FROM tenaga_kependidikan as tk
        inner join akun as a on a.email = tk.email
        where tk.id = ?";

        $result = $db->query($sql, [$idTendik]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

}