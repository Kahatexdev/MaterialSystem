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
            ->orderBy('stock.nama_cluster', 'ASC');
        // ->limit(10);

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
    public function searchStockArea($area, $noModel = null, $warna = null)
    {
        $builder = $this->db->table('stock s')
            ->select('
                s.*, 
                (SELECT COALESCE(SUM(kgs_in_out), 0) FROM stock WHERE no_model = s.no_model) AS Kgs,
                (SELECT COALESCE(SUM(kgs_stock_awal), 0) FROM stock WHERE no_model = s.no_model) AS KgsStockAwal,
                (SELECT COALESCE(SUM(krg_in_out), 0) FROM stock WHERE no_model = s.no_model) AS Krg,
                (SELECT COALESCE(SUM(krg_stock_awal), 0) FROM stock WHERE no_model = s.no_model) AS KrgStockAwal,
                (SELECT COALESCE(SUM(cns_in_out), 0) FROM stock WHERE no_model = s.no_model) AS Cns,
                (SELECT COALESCE(SUM(cns_stock_awal), 0) FROM stock WHERE no_model = s.no_model) AS CnsStockAwal,
                c.nama_cluster, c.kapasitas, m.area
            ')
            ->join('cluster c', 's.nama_cluster = c.nama_cluster', 'left')
            ->join('master_order mo', 'mo.no_model = s.no_model', 'left')
            ->join('material m', 'm.id_order = mo.id_order', 'left')
            ->where('m.area', $area)
            ->groupBy('s.no_model, s.item_type, s.kode_warna, s.lot_stock, c.nama_cluster')
            ->orderBy('s.no_model, s.item_type, s.kode_warna, s.lot_stock, c.nama_cluster', 'ASC');

        if (!empty($noModel)) {
            $builder->where('s.no_model', $noModel);
        }
        if (!empty($warna)) {
            $builder->where('s.kode_warna', $warna);
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
        return $this->select("
        nama_cluster,
        MAX(id_stock) AS id_stock,
        SUM(COALESCE(kgs_stock_awal, 0) + COALESCE(kgs_in_out, 0)) AS total_kgs,
        SUM(COALESCE(cns_stock_awal, 0) + COALESCE(cns_in_out, 0)) AS total_cns,
        SUM(COALESCE(krg_stock_awal, 0) + COALESCE(krg_in_out, 0)) AS total_krg,
        COALESCE(NULLIF(lot_awal, ''), NULLIF(lot_stock, '')) AS lot_final
    ")
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->where('warna', $warna)
            ->groupBy('nama_cluster')
            ->get()
            ->getResultArray();
    }



    // public function getDataByIdStok($idStok)
    // {
    //     // tampilkan data tabel pemasukan yang di join ke stock yang memiliki id_stok sama dengan idStok
    //     return $this->db->table('pemasukan')
    //         ->select('pemasukan.*, stock.*, SUM(kgs_stock_awal + kgs_in_out) AS total_kgs, 
    //     SUM(cns_stock_awal + cns_in_out) AS total_cns, 
    //     SUM(krg_stock_awal + krg_in_out) AS total_krg, 
    //     COALESCE(lot_awal, lot_stock) AS lot_final')
    //         ->join('stock', 'stock.id_stock = pemasukan.id_stock')
    //         ->where('pemasukan.id_stock', $idStok)
    //         ->groupBy('stock.id_stock')
    //         ->groupBy('pemasukan.id_pemasukan')
    //         // ->groupBy('pemasukan.id_stock'))
    //         ->get()
    //         ->getResultArray();
    // }

    public function getDataByIdStok($idStok)
    {
        // tampilkan data tabel pemasukan yang di join ke stock yang memiliki id_stok sama dengan idStok
        return $this->db->table('pemasukan')
            ->select('pemasukan.*, stock.*, SUM(kgs_stock_awal + kgs_in_out - COALESCE(other_out.kgs_other_out, 0)) AS total_kgs, 
        SUM(cns_stock_awal + cns_in_out - COALESCE(other_out.cns_other_out, 0)) AS total_cns, 
        SUM(krg_stock_awal + krg_in_out - COALESCE(other_out.krg_other_out, 0)) AS total_krg, 
        COALESCE(lot_awal, lot_stock) AS lot_final')
            ->join('stock', 'stock.id_stock = pemasukan.id_stock')
            ->join('other_out', 'other_out.id_out_celup = pemasukan.id_out_celup', 'left')
            ->where('pemasukan.id_stock', $idStok)
            ->groupBy('stock.id_stock')
            ->groupBy('pemasukan.id_pemasukan')
            // ->groupBy('pemasukan.id_stock'))
            ->get()
            ->getResultArray();
    }

    public function updateStock($id, $kgsInOut, $kgsStockAwal, $cnsInOut, $cnsStockAwal, $krgInOut, $krgStockAwal)
    {
        return $this->db->table('stock')
            ->where('id_stock', $id)
            ->update([
                'kgs_in_out' => $kgsInOut,
                'kgs_stock_awal' => $kgsStockAwal,
                'cns_in_out' => $cnsInOut,
                'cns_stock_awal' => $cnsStockAwal,
                'krg_in_out' => $krgInOut,
                'krg_stock_awal' => $krgStockAwal
            ]);
    }

    public function getStockInPemasukanById($idStok)
    {
        return $this->db->table('pemasukan')
            ->select('pemasukan.id_pemasukan, stock.nama_cluster, stock.id_stock, stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.lot_awal, stock.lot_stock,out_celup.*')
            ->join('stock', 'stock.id_stock = pemasukan.id_stock', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->where('pemasukan.id_stock', $idStok)
            ->where('pemasukan.out_jalur =', '0')
            ->groupBy('pemasukan.id_pemasukan')
            ->groupBy('stock.id_stock')
            ->groupBy('out_celup.id_out_celup')
            ->orderBy('pemasukan.id_pemasukan', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function cekStockOrder($no_model, $item_type, $kode_warna)
    {
        return $this->select('COALESCE(SUM(kgs_stock_awal), 0) as kgs_stock')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('kode_warna')
            ->first();
    }
    public function getDataClusterPindah($data)
    {

        $lotColumn = $data['stock_awal'] == "" ? 'lot_stock' : 'lot_awal';

        return $this->select('stock.*')
            ->where([
                'no_model'     => $data['no_model'],
                'item_type'    => $data['item_type'],
                'kode_warna'   => $data['kode_warna'],
                'warna'        => $data['warna'],
                'nama_cluster' => $data['nama_cluster'],
            ])
            ->where($lotColumn, $data['lot'])
            ->first();

        return $data;
    }

    public function getStockForSchedule($kodeWarna, $warna, $itemTypeEncoded)
    {
        return $this->select('sum(kgs_stock_awal) as kg_stok')
            ->where('kode_warna', $kodeWarna)
            ->where('warna', $warna)
            ->where('item_type', $itemTypeEncoded)
            ->first();
    }

    public function getFilterReportGlobalBenang($key)
    {
        return $this->select("
                stock.*, 
                material.loss,
                out_celup.ganti_retur,
                out_celup.id_out_celup,
                (
                    SELECT SUM(m.kgs)
                    FROM material m
                    JOIN master_order mo ON mo.id_order = m.id_order
                    WHERE m.item_type = stock.item_type 
                    AND m.kode_warna = stock.kode_warna 
                    AND m.color = stock.warna 
                    AND mo.no_model = stock.no_model
                ) AS qty_po,
                (
                    SELECT SUM(COALESCE(p.kgs_out, 0))
                    FROM pengeluaran p
                    JOIN out_celup oc ON oc.id_out_celup = p.id_out_celup
                    JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
                    WHERE sc.no_model = stock.no_model
                    AND sc.kode_warna = stock.kode_warna
                    AND sc.item_type = stock.item_type
                ) AS pakai_area,
                (
                    SELECT SUM(COALESCE(oc.kgs_kirim, 0))
                    FROM pemasukan pm
                    JOIN out_celup oc ON oc.id_out_celup = pm.id_out_celup
                    WHERE pm.id_stock = stock.id_stock
                    AND oc.ganti_retur = 0
                ) AS datang_solid,
                (
                    SELECT SUM(COALESCE(oc.kgs_kirim, 0))
                    FROM pemasukan pm
                    JOIN out_celup oc ON oc.id_out_celup = pm.id_out_celup
                    WHERE pm.id_stock = stock.id_stock
                    AND oc.ganti_retur = 1
                ) AS ganti_retur,
                 (
                    SELECT area_out
                    FROM pengeluaran p
                    WHERE p.lot_out = stock.lot_stock
                    AND p.nama_cluster = stock.nama_cluster
                ) AS area,
            ")
            ->join('material', 'material.item_type = stock.item_type AND material.kode_warna = stock.kode_warna', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('pemasukan', 'pemasukan.id_stock = stock.id_stock', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('pengeluaran', 'pengeluaran.lot_out = stock.lot_stock', 'left')
            ->where('stock.no_model', $key)
            ->groupBy('stock.id_stock')
            ->get()
            ->getResultArray();
    }

    public function getFilterSisaPakaiBenang($bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->select('
        stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.kgs_stock_awal, stock.lot_awal,
        mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
        pengeluaran.area_out, pengeluaran.kgs_out,
        open_po.kg_po,
        retur.kgs_retur, retur.lot_retur,
        mm.jenis
    ')
            ->join('master_order AS mo', 'mo.no_model = stock.no_model')
            ->join('master_material AS mm', 'mm.item_type = stock.item_type')
            ->join('pengeluaran', 'pengeluaran.lot_out = stock.lot_stock AND pengeluaran.nama_cluster = stock.nama_cluster', 'left')
            ->join('open_po', 'open_po.no_model = stock.no_model AND open_po.item_type = stock.item_type AND open_po.kode_warna = stock.kode_warna', 'left')
            ->join('retur', 'retur.no_model = stock.no_model AND retur.item_type = stock.item_type AND retur.kode_warna = stock.kode_warna', 'left')
            ->where('mm.jenis', 'BENANG')
            ->groupBy('stock.id_stock');

        if (!empty($noModel)) {
            $builder->where('stock.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('stock.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaPakaiNylon($bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->select('
        stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.kgs_stock_awal, stock.lot_awal,
        mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
        pengeluaran.area_out, pengeluaran.kgs_out,
        open_po.kg_po,
        retur.kgs_retur, retur.lot_retur,
        mm.jenis
    ')
            ->join('master_order AS mo', 'mo.no_model = stock.no_model')
            ->join('master_material AS mm', 'mm.item_type = stock.item_type')
            ->join('pengeluaran', 'pengeluaran.lot_out = stock.lot_stock AND pengeluaran.nama_cluster = stock.nama_cluster', 'left')
            ->join('open_po', 'open_po.no_model = stock.no_model AND open_po.item_type = stock.item_type AND open_po.kode_warna = stock.kode_warna', 'left')
            ->join('retur', 'retur.no_model = stock.no_model AND retur.item_type = stock.item_type AND retur.kode_warna = stock.kode_warna', 'left')
            ->where('mm.jenis', 'NYLON')
            ->groupBy('stock.id_stock');

        if (!empty($noModel)) {
            $builder->where('stock.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('stock.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaPakaiSpandex($bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->select('
        stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.kgs_stock_awal, stock.lot_awal,
        mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
        pengeluaran.area_out, pengeluaran.kgs_out,
        open_po.kg_po,
        retur.kgs_retur, retur.lot_retur,
        mm.jenis
    ')
            ->join('master_order AS mo', 'mo.no_model = stock.no_model')
            ->join('master_material AS mm', 'mm.item_type = stock.item_type')
            ->join('pengeluaran', 'pengeluaran.lot_out = stock.lot_stock AND pengeluaran.nama_cluster = stock.nama_cluster', 'left')
            ->join('open_po', 'open_po.no_model = stock.no_model AND open_po.item_type = stock.item_type AND open_po.kode_warna = stock.kode_warna', 'left')
            ->join('retur', 'retur.no_model = stock.no_model AND retur.item_type = stock.item_type AND retur.kode_warna = stock.kode_warna', 'left')
            ->where('mm.jenis', 'SPANDEX')
            ->groupBy('stock.id_stock');

        if (!empty($noModel)) {
            $builder->where('stock.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('stock.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaPakaiKaret($bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->select('
        stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.kgs_stock_awal, stock.lot_awal,
        mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
        pengeluaran.area_out, pengeluaran.kgs_out,
        open_po.kg_po,
        retur.kgs_retur, retur.lot_retur,
        mm.jenis
    ')
            ->join('master_order AS mo', 'mo.no_model = stock.no_model')
            ->join('master_material AS mm', 'mm.item_type = stock.item_type')
            ->join('pengeluaran', 'pengeluaran.lot_out = stock.lot_stock AND pengeluaran.nama_cluster = stock.nama_cluster', 'left')
            ->join('open_po', 'open_po.no_model = stock.no_model AND open_po.item_type = stock.item_type AND open_po.kode_warna = stock.kode_warna', 'left')
            ->join('retur', 'retur.no_model = stock.no_model AND retur.item_type = stock.item_type AND retur.kode_warna = stock.kode_warna', 'left')
            ->where('mm.jenis', 'KARET')
            ->groupBy('stock.id_stock');

        if (!empty($noModel)) {
            $builder->where('stock.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('stock.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        return $builder->get()->getResultArray();
    }
}
