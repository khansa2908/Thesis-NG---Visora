<?php 

namespace App\Models;



use CodeIgniter\Model;



class ProgramStudiModel extends Model 

{

    protected $table = "program_studi";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'inisial', 'id_fakultas', 'email'];

    protected $useTimestamps = false;

    public function getProdiInfo($idProdi) {
        $db = \Config\Database::connect();

        $sql = "SELECT p.*, a.id as id_akun
        FROM program_studi as p
        inner join akun as a on a.email = p.email
        where p.id = ?";

        $result = $db->query($sql, [$idProdi]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

}