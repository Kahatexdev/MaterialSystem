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

    public function getTotalJenis()
    {
        return $this->select('COUNT(jenis_benang) AS total_jenis')
            ->first();
    }

    public function getTotalStockWarehouseBB()
    {
        return $this->select('SUM(kg) AS total_kg')
            ->first();
    }

    public function getJenisBenangTersedia()
    {
        return $this->select('COUNT(jenis_benang) AS jenis_benang_tersedia')
            ->where('kg >', 0)
            ->first();
    }

    public function getJenisBenangKosong()
    {
        return $this->select('COUNT(jenis_benang) AS jenis_benang_kosong')
            ->where('kg <=', 0)
            ->first();
    }

    public function getNylonBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->where('jenis_benang', 'NYLON')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }
    public function getPolyesterBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->where('jenis_benang', 'POLYESTER')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }
    public function getRecycledPolyesterBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->like('jenis_benang', 'RECYCLED POLYESTER')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }
    public function getSpandexBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->where('jenis_benang', 'SPANDEX')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }
    public function getRubberBB()
    {
        return $this->select('stock_bb_covering.*, IF(stock_bb_covering.kg > 0, "ada", "habis") AS status')
            ->where('jenis_benang', 'RUBBER')
            ->orderBy('kg', 'DESC')
            ->findAll();
    }
}
