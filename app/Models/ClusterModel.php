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

    public function getClusterGroupI()
    {
        return $this->select('cluster.kapasitas, 
                      COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0) AS total_qty, 
                      cluster.nama_cluster, 
                      RIGHT(cluster.nama_cluster, 3) AS simbol_cluster')
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->groupStart()
            ->groupStart()
            ->like('cluster.nama_cluster', 'I.%.09.%', 'after')
            ->where('cluster.nama_cluster >=', 'I.A.09.a')
            ->where('cluster.nama_cluster <=', 'I.B.09.b')
            ->groupEnd()
            ->orGroupStart()
            ->like('cluster.nama_cluster', 'I.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.08.%', 'after')
            ->groupEnd()
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }

    public function getClusterGroupII()
    {
        return $this->select('cluster.kapasitas, 
                      COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0) AS total_qty, 
                      cluster.nama_cluster, 
                      RIGHT(cluster.nama_cluster, 3) AS simbol_cluster')
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->GroupStart()
            ->like('cluster.nama_cluster', 'II.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.08.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.09.%', 'after')
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }

    public function getClusterGroupIII()
    {
        return $this->select('cluster.kapasitas, 
                      COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0) AS total_qty, 
                      cluster.nama_cluster, 
                      RIGHT(cluster.nama_cluster, 3) AS simbol_cluster')
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->GroupStart()
            ->like('cluster.nama_cluster', 'III.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.08.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.09.%', 'after')
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }
}