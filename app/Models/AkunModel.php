<?php 

namespace App\Models;



use CodeIgniter\Model;



class AkunModel extends Model 

{

    protected $table = "akun";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['username', 'email', 'password', 'role', 'status', 'id_role'];

    protected $useTimestamps = false;


    public function isEmailAlreadyUsed($idAkun, $newEmail) {
        $db = \Config\Database::connect();

        $sql = "SELECT * from akun where id <> ? and email = ?";

        $result = $db->query($sql, [$idAkun, $newEmail]);

        return count($result->getResultArray()) > 0;
    }
}