<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinCoveringModel extends Model
{
    protected $table            = 'mesin_covering';
    protected $primaryKey       = 'id_mesin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_mesin',
        'nama',
        'jenis',
        'buatan',
        'merk',
        'type',
        'jml_spindle',
        'tahun',
        'jml_unit',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getAllMesinCovering()
    {
        return $this->table('mesin_covering')
            ->select('*')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }
}
