<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseBBModel extends Model
{
    protected $table            = 'stock_bb_covering';
    protected $primaryKey       = 'idstockbb';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'denier',
        'jenis_benang',
        'warna',
        'kode',
        'kg',
        'keterangan',
        'admin'
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

    public function getWarehouseBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }

    public function getDataByJenis($jenis)
    {
        return $this->select('*')
            ->where('jenis_benang', $jenis)
            ->findAll();
    }

    public function getStockByDenierJenisWarna($denier, $jenis_benang, $warna, $kode)
    {
        return $this->db->table('stock_bb_covering')
            ->select('*')
            ->where('denier', $denier)
            ->where('jenis_benang', $jenis_benang)
            ->where('warna', $warna)
            ->where('kode', $kode)
            ->get()
            ->getRowArray();
    }
}
