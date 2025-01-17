<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOrderModel extends Model
{
    protected $table            = 'master_order';
    protected $primaryKey       = 'id_order';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_order',
        'no_order',
        'no_model',
        'buyer',
        'foll_up',
        'lco_date',
        'memo',
        'delivery_awal',
        'delivery_akhir',
        'unit',
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


    public function findIdOrder($no_order)
    {
        return $this->select('id_order')->where('no_order', $no_order)->first();
    }

    public function checkDatabase($no_order, $no_model, $buyer, $lco_date, $foll_up)
    {
        return $this->where('no_order', $no_order)
            ->where('no_model', $no_model)
            ->where('buyer', $buyer)
            ->where('lco_date', $lco_date)
            ->where('foll_up', $foll_up)
            ->first();
    }
    public function getMaterialOrder($id)
    {
        return $this->select('no_model,buyer,delivery_akhir, material.item_type, material.color, material.kode_warna, sum(material.kgs) as kg')
            ->join('material', 'material.id_order=master_order.id_order')
            ->where('master_order.id_order', $id)
            ->groupBy('material.kode_warna')
            ->findAll();
    }
}
