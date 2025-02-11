<?php

namespace App\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
    protected $table            = 'cluster';
    protected $primaryKey       = 'nama_cluster';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_cluster',
        'kapasitas',
        'keterangan',
        'group',
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

    public function getDataCluster()
    {
        return $this->findAll();
    }

    public function getCluster($kgs)
    {
        return $this->db->table('cluster') // Gunakan nama tabel langsung
            ->select('cluster.nama_cluster, (cluster.kapasitas - IFNULL(SUM(stock.kgs_stock_awal), 0) - IFNULL(SUM(stock.kgs_in_out), 0)) AS sisa_kapasitas', false)
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->groupBy('cluster.nama_cluster')
            ->having('sisa_kapasitas >=', $kgs, false) // Filter kapasitas lebih dari $kgs
            ->orderBy('cluster.nama_cluster', 'ASC')
            ->get()
            ->getResultArray();
    }
}
