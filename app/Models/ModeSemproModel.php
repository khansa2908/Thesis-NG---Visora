<?php 

namespace App\Models;



use CodeIgniter\Model;



class ModeSemproModel extends Model 

{

    protected $table = "mode_sempro";

    protected $primaryKey = "kode_semester";

    protected $useAutoIncrement = false;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['kode_semester', 'id_prodi', 'mode'];

    protected $useTimestamps = false;

    public function updateModeSempro($modeSempro, $kodeSemester, $idProdi) {
        $db = \Config\Database::connect();

        $sql = "UPDATE mode_sempro
        set mode = ?
        where kode_semester = ? and id_prodi = ?";

        $db->query($sql, [$modeSempro, $kodeSemester, $idProdi]);
    }

    public function getModeSempro($kodeSemester, $idProdi) {
        $db = \Config\Database::connect();

        $sql = "SELECT * from mode_sempro
        where kode_semester = ? and id_prodi = ?";

        $result = $db->query($sql, [$kodeSemester, $idProdi]);

        if (count($result->getResultArray()) == 0) {
            return null;
        }

        return $result->getResultArray()[0];
    }

}