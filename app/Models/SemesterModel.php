<?php 

namespace App\Models;



use CodeIgniter\Model;



class SemesterModel extends Model 

{

    protected $table = "semester";

    protected $primaryKey = "kode";

    protected $useAutoIncrement = false;

    protected $returnType = "array";

    protected $useSoftDeletes = false;

    protected $allowedFields = ['kode', 'nama', 'status'];

    protected $useTimestamps = false;

    public function isSemesterDeleteable($kodeSemester) {

        $db = \Config\Database::connect();

        $sql = "SELECT prop.id as id_proposal, skri.id as id_skripsi, mak.id as id_makalah
        FROM semester as sem
        INNER JOIN proposal as prop on sem.kode = prop.kode_semester
        INNER JOIN skripsi as skri on sem.kode = skri.kode_semester
        INNER JOIN makalah as mak on sem.kode = mak.kode_semester
        WHERE sem.kode = ?";

        $result = $db->query($sql, [$kodeSemester]);

        return count($result->getResultArray()) == 0;
    }   

    public function getActiveSemester() {

        $db = \Config\Database::connect();

        $sql = "SELECT *
        FROM semester
        WHERE status = 'AKTIF'";

        $result = $db->query($sql);
        if (count($result->getResultArray()) == 0)
            return null;
        
        return $result->getResultArray()[0];
    }
}