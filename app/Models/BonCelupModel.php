<?php

namespace App\Models;

use CodeIgniter\Model;

class BonCelupModel extends Model
{
    protected $table            = 'bon_celup';
    protected $primaryKey       = 'id_bon';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bon',
        'tgl_datang',
        'no_surat_jalan',
        'detail_sj',
        'admin',
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

    public function getData()
    {
        return $this->select('bon_celup.id_bon, bon_celup.tgl_datang, bon_celup.no_surat_jalan, bon_celup.detail_sj')
            ->findAll();
    }

    public function saveBon($saveDataBon, $saveDataOutCelup)
    {
        $this->db->table('bon_celup')->insert($saveDataBon);

        $this->db->table('out_celup')->insert($saveDataOutCelup);
    }

    public function getDataById($id)
    {
        return $this->select('bon_celup.*')
            ->first();
    }
}
