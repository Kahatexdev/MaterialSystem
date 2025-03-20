<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryStockCoveringModel extends Model
{
    protected $table            = 'history_stock_covering';
    protected $primaryKey       = 'id_history_covering_stock';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_model',
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

    public function getPemasukan()
    {
        return $this->db->table('history_stock_covering')
            ->select('*')
            ->where('ttl_kg >=', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getPengeluaran()
    {
        return $this->db->table('history_stock_covering')
            ->select('*')
            ->where('ttl_kg <=', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
