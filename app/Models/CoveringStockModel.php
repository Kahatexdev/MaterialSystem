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
        'color',
        'code',
        'lmd',
        'ttl_cns',
        'ttl_kg',
        'box',
        'no_rak',
        'posisi_rak',
        'no_palet',
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

    public function stokCovering()
    {
        return $this->db->table('stock_covering cs')
            ->select('cs.*, IF(cs.ttl_kg > 0, "ada", "habis") AS status')
            ->get()
            ->getResultArray();
    }

    public function getStockByJenisColorCode($jenis, $color, $code)
    {
        return $this->db->table('stock_covering')
            ->select('*')
            ->where('jenis', $jenis)
            ->where('color', $color)
            ->where('code', $code)
            ->get()
            ->getRowArray();
    }
}
