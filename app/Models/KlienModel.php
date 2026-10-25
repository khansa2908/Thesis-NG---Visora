<?php 

namespace App\Models;



use CodeIgniter\Model;



class KlienModel extends Model 

{

    protected $table = "klien";

    protected $primaryKey = "id";

    protected $useAutoIncrement = true;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['nama', 'inisial', 'instansi', 'email'];

    protected $useTimestamps = false;

    public function getAllKlienInfo() {
        $db = \Config\Database::connect();

        $sql = "SELECT k.*, a.status as status_akun
        FROM klien as k
        inner join akun as a on a.email = k.email";

        $result = $db->query($sql);

        return $result->getResultArray();
    }

    public function getKlienInfo($idKlien) {
        $db = \Config\Database::connect();

        $sql = "SELECT k.*, a.id as id_akun
        FROM klien as k
        inner join akun as a on a.email = k.email
        where k.id = ?";

        $result = $db->query($sql, [$idKlien]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

    public function isInitialAlreadyUsed($idKlien, $newInisial) {
        $db = \Config\Database::connect();

        $sql = "SELECT * from klien where id <> ? and inisial = ?";

        $result = $db->query($sql, [$idKlien, $newInisial]);

        return count($result->getResultArray()) > 0;
    }

}