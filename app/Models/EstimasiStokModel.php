<?php

namespace App\Models;

use CodeIgniter\Model;

class EstimasiStokModel extends Model
{
    protected $table            = 'estimasi_stok';
    protected $primaryKey       = 'id_sm';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_model_old',
        'no_model_new',
        'item_type',
        'kode_warna',
        'lot',
        'kg_stock',
        'cones_stock',
        'karung_stock',
        'kg_aktual',
        'cns_aktual',
        'krg_aktual',
        'admin',
        'created_at',
        'updated_at',
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


    public function cekStok($cek)
    {
        return $this->select(' sum(kg_aktual) as kg_stok')
            ->where('no_model_new', $cek['no_model'])
            ->where('item_type', $cek['item_type'])
            ->where('kode_warna', $cek['kode_warna'])
            ->groupBy('kode_warna')
            ->first();
    }
}
