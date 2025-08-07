<?php

namespace App\Models;

use CodeIgniter\Model;

class CoveringStockModel extends Model
{
    protected $table            = 'stock_covering';
    protected $primaryKey       = 'id_covering_stock';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis',
        'jenis_benang',
        'jenis_cover',
        'jenis_mesin',
        'dr',
        'color',
        'code',
        'lmd',
        'ttl_cns',
        'ttl_kg',
        'admin',
        'keterangan'
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

    public function stokCovering($perPage = 9)
    {
        return $this->select('stock_covering.*, IF(stock_covering.ttl_kg > 0, "ada", "habis") AS status')
            ->orderBy('ttl_kg', 'DESC')
            ->paginate($perPage, 'warehouse');
    }

    public function getStockByJenisColorCodeMesin($jenis, $color, $code, $mesin,$dr, $jenisCover, $jenisBenang)
    {
        return $this->db->table('stock_covering')
            ->select('*')
            ->where('jenis', $jenis)
            ->where('color', $color)
            ->where('code', $code)
            ->where('jenis_mesin', $mesin)
            ->where('dr', $dr)
            ->where('jenis_cover', $jenisCover)
            ->where('jenis_benang', $jenisBenang)
            ->get()
            ->getRowArray();
    }

    public function getStockCover($jenisMesin,$jenisBenang, $jenisCover)
    {
        return $this->select('*')
            ->when($jenisMesin !== null && $jenisMesin !== '', function($query) use ($jenisMesin) {
                return $query->where('jenis_mesin', $jenisMesin);
            })
            ->when($jenisBenang !== null && $jenisBenang !== '', function($query) use ($jenisBenang) {
                return $query->where('jenis_benang', $jenisBenang);
            })
            ->when($jenisCover !== null && $jenisCover !== '', function($query) use ($jenisCover) {
                return $query->where('jenis_cover', $jenisCover);
            })
            ->orderBy('jenis_benang', 'ASC')
            ->orderBy('dr', 'ASC')
            ->findAll();
    }

    public function getAllJenisMesin()
    {
        return $this->distinct()
            ->select('jenis_mesin')
            ->orderBy('jenis_mesin', 'ASC')
            ->findAll();
    }

    public function getAllDr()
    {
        return $this->distinct()
            ->select('dr')
            ->orderBy('dr', 'ASC')
            ->findAll();
    }

    public function getStockByJenis($jenis)
    {
        return $this->where('jenis_benang', $jenis)->orderBy('jenis_benang', 'ASC')
            ->orderBy('dr', 'ASC')
            ->findAll();
    }
}
