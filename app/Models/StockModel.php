<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table            = 'stock';
    protected $primaryKey       = 'id_stock';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_stock',
        'no_model',
        'item_type',
        'kode_warna',
        'warna',
        'kgs_stock_awal',
        'cns_stock_awal',
        'krg_stock_awal',
        'lot_awal',
        'kgs_in_out',
        'cns_in_out',
        'krg_in_out',
        'lot_stock',
        'nama_cluster',
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

    public function searchStock($noModel, $warna)
    {
        $builder = $this->db->table($this->table);

        if (!empty($noModel)) {
            $builder->groupStart()
                ->like('stock.no_model', $noModel)
                ->orLike('cluster.nama_cluster', $noModel)
                ->groupEnd();
        }

        if (!empty($warna)) {
            $builder->like('stock.kode_warna', $warna);
        }
        $builder->like('kode_warna', $warna);

        // Query dengan agregasi SUM(kgs_in_out) dan perhitungan sisa kapasitas
        $builder->select('
            stock.*, 
            COALESCE(SUM(stock.kgs_in_out), 0) AS Kgs, 
            COALESCE(SUM(stock.kgs_stock_awal), 0) AS KgsStockAwal, 
            COALESCE(SUM(stock.krg_in_out), 0) AS Krg, 
            COALESCE(SUM(stock.krg_stock_awal), 0) AS KrgStockAwal,
            COALESCE(SUM(stock.cns_in_out), 0) AS Cns, 
            COALESCE(SUM(stock.cns_stock_awal), 0) AS CnsStockAwal,

            cluster.*
        ')
            ->join('cluster', 'cluster.nama_cluster = stock.nama_cluster', 'left')
            ->groupBy([
                'stock.no_model',
                'stock.kode_warna',
                'stock.warna',
                'stock.item_type',
                'stock.lot_stock',
                'stock.nama_cluster',
                'cluster.kapasitas'
            ])
            ->orderBy('stock.nama_cluster', 'ASC')
            ->limit(10);

        return $builder->get()->getResult();
    }

    public function getKapasitas()
    {
        $builder = $this->db->table('cluster');
        $builder->select(
            'cluster.nama_cluster, cluster.kapasitas,
                      COALESCE(SUM(stock.kgs_in_out), 0) AS Kgs,
                      COALESCE(SUM(stock.kgs_stock_awal), 0) AS KgsStockAwal, 
                      COALESCE(SUM(stock.krg_in_out), 0) AS Krg, 
                      COALESCE(SUM(stock.krg_stock_awal), 0) AS KrgStockAwal'
        )
            ->join('stock', 'cluster.nama_cluster = stock.nama_cluster', 'left') // Left join agar semua cluster tampil
            ->groupBy('cluster.nama_cluster'); // Hanya group by nama_cluster

        return $builder->get()->getResult();
    }

    public function updateClusterStock($idStock, $namaCluster)
    {
        return $this->db->table('stock')
            ->where('id_stock', $idStock)
            ->update(['nama_cluster' => $namaCluster]);
    }

    public function getNoModel()
    {
        return $this->select('material.item_type, material.kode_warna, master_order.no_model')
            ->from('material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->join('stock s', 'material.item_type = s.item_type AND material.kode_warna = s.kode_warna') // Memberikan alias 's' untuk table stock
            ->distinct()
            ->get()
            ->getResult();
    }

    public function cekStok($cek)
    {
        return $this->select(' sum(kgs_stock_awal) as kg_stok')
            ->where('no_model', $cek['no_model'])
            ->where('item_type', $cek['item_type'])
            ->where('kode_warna', $cek['kode_warna'])
            ->groupBy('kode_warna')
            ->first();
    }
    public function stockInOut($model, $itemType, $kodeWarna)
    {
        return $this->select('sum(kgs_stock_awal+kgs_in_out) as stock')
            ->where('no_model', $model)
            ->where('item_type', $itemType)
            ->where('kode_Warna', $kodeWarna)
            ->groupBy('kode_warna')
            ->first();
    }
    // public function searchStockArea($area, $noModel = null, $warna = null)
    // {
    //     $query = $this->select('
    //         stock.no_model, 
    //         stock.item_type, 
    //         stock.kode_warna,
    //         stock.warna,
    //         COALESCE(SUM(stock.kgs_in_out), 0) AS Kgs, 
    //         COALESCE(SUM(stock.kgs_stock_awal), 0) AS KgsStockAwal, 
    //         COALESCE(SUM(stock.krg_in_out), 0) AS Krg, 
    //         COALESCE(SUM(stock.krg_stock_awal), 0) AS KrgStockAwal,
    //         COALESCE(SUM(stock.cns_in_out), 0) AS Cns, 
    //         COALESCE(SUM(stock.cns_stock_awal), 0) AS CnsStockAwal,
    //         cluster.*,
    //         material.area
    //     ')
    //     ->join('cluster', 'cluster.nama_cluster = stock.nama_cluster', 'left')
    //     ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
    //     ->join('material', 
    //         'material.id_order = master_order.id_order 
    //         AND material.item_type = stock.item_type 
    //         AND material.kode_warna = stock.kode_warna 
    //         AND material.color = stock.warna', 
    //         'left'
    //     )
    //     ->where('material.area', $area);

    //     if (!empty($noModel)) {
    //         $query->where('stock.no_model', $noModel);
    //     }

    //     if (!empty($warna)) {
    //         $query->where('stock.kode_warna', $warna);
    //     }

    //     return $query->groupBy([
    //         'stock.no_model',
    //         'stock.kode_warna',
    //         'stock.warna',
    //         'stock.item_type',
    //         'stock.lot_stock',
    //         'stock.nama_cluster',
    //         'cluster.kapasitas'
    //     ])
    //     ->orderBy('stock.nama_cluster', 'ASC')
    //     ->limit(10)
    //     ->findAll(); // Tambahkan findAll() agar hasilnya berupa array, bukan model
    // }

    public function searchStockArea($area, $noModel = null, $warna = null)
    {
        $builder = $this->db->table('stock')
            ->select('stock.*, cluster.nama_cluster, cluster.kapasitas, material.area')
            ->join('cluster', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->join('material', 'material.id_order = master_order.id_order', 'left')
            ->where('material.area', $area) // Menyesuaikan dengan area yang dicari
            ->groupBy('stock.no_model, stock.item_type, stock.kode_warna, stock.lot_stock, cluster.nama_cluster')
            ->orderBy('stock.no_model, stock.item_type, stock.kode_warna, stock.lot_stock, cluster.nama_cluster', 'ASC');

        if (!empty($noModel)) {
            $builder->where('stock.no_model', $noModel);
        }
        if (!empty($warna)) {
            $builder->where('stock.kode_warna', $warna);
        }

        return $builder->get()->getResultArray();
    }

    public function getStock($no_model, $item_type, $kode_warna, $warna)
    {
        return $this->select('sum(kgs_stock_awal) as kgs_stock_awal, sum(cns_stock_awal) as cns_stock_awal, sum(krg_stock_awal) as krg_stock_awal, sum(kgs_in_out) as kgs_in_out, sum(cns_in_out) as cns_in_out, sum(krg_in_out) as krg_in_out, sum(lot_stock) as lot_stock')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->where('warna', $warna)
            ->groupBy('kode_warna')
            ->first();
    }

    public function getDataCluster($noModel, $itemType, $kodeWarna, $warna)
    {
        return $this->select('nama_cluster, kgs_stock_awal, cns_stock_awal, krg_stock_awal, lot_awal, kgs_in_out, cns_in_out, krg_in_out, lot_stock')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->where('warna', $warna)
            ->groupBy('nama_cluster')
            ->get()
            ->getResultArray();
    }
}
