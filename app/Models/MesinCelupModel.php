<?php

namespace App\Models;

use CodeIgniter\Model;

class MesinCelupModel extends Model
{
    protected $table            = 'mesin_celup';
    protected $primaryKey       = 'id_mesin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_mesin',
        'min_caps',
        'max_caps',
        'jml_lot',
        'lmd',
        'ket_mesin',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getAllMesinCelup()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }

    public function getMesinCelupBenang()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->where('ket_mesin', 'BENANG')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }

    public function getMinCaps($no_mesin)
    {
        return $this->table('mesin_celup')
            ->select('min_caps')
            ->where('no_mesin', $no_mesin)
            ->first();
    }

    public function getMaxCaps($no_mesin)
    {
        return $this->table('mesin_celup')
            ->select('max_caps')
            ->where('no_mesin', $no_mesin)
            ->first();
    }

    public function getIdMesin($no_mesin)
    {
        return $this->table('mesin_celup')
            ->select('id_mesin')
            ->where('no_mesin', $no_mesin)
            ->first();
    }

    public function getNoMesin($id_mesin)
    {
        return $this->table('mesin_celup')
            ->select('no_mesin')
            ->where('id_mesin', $id_mesin)
            ->first();
    }

    public function getMesinCelupAcrylic()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->where('ket_mesin', 'ACRYLIC')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }

    public function getMesinCelupNylon()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->where('ket_mesin', 'NYLON')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }

    public function getMesinCelupSample()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->where('ket_mesin', 'MC BENANG SAMPLE')
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }

    public function getMesinCelupBenangNylon()
    {
        return $this->table('mesin_celup')
            ->select('*')
            ->whereIn('ket_mesin', ['BENANG', 'NYLON'])
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
    }
}
