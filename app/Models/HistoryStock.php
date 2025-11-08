<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoryStock extends Model
{
    protected $table            = 'history_stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_history_pindah',
        'id_stock_old',
        'id_stock_new',
        'id_out_celup',
        'cluster_old',
        'cluster_new',
        'kgs',
        'cns',
        'krg',
        'lot',
        'keterangan',
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

    public function getHistoryPindahOrder($noModelOld = null, $noModelNew = null, $kodeWarna = null, $limit = null)
    {
        $builder = $this->db->table('history_stock')
            ->select('s_old.no_model AS no_model_old, s_new.no_model AS no_model_new, s_old.item_type, s_old.kode_warna, s_old.warna, history_stock.kgs, history_stock.cns, history_stock.lot, history_stock.cluster_old, history_stock.cluster_new, history_stock.keterangan, history_stock.created_at, s_new.kode_warna')
            ->join('stock s_old', 's_old.id_stock = history_stock.id_stock_old')
            ->join('stock s_new', 's_new.id_stock = history_stock.id_stock_new')
            ->where('keterangan', 'Pindah Order');

        if (!empty($noModelOld)) {
            $builder->like('s_old.no_model', $noModelOld);
        }

        if (!empty($noModelNew)) {
            $builder->like('s_new.no_model', $noModelNew);
        }

        if (!empty($kodeWarna)) {
            $builder->groupStart()
                ->like('s_old.kode_warna', $kodeWarna)
                ->orLike('s_new.kode_warna', $kodeWarna)
                ->groupEnd();
        }

        $builder->orderBy('history_stock.created_at', 'DESC');

        if ($limit) {
            $builder->limit((int)$limit);
        }

        return $builder->get()->getResultArray();
    }
    public function getHistoryPinjamOrder($no_model, $kode_warna)
    {
        $builder = $this->select('
            stock.no_model AS no_model_dipinjam,
            stock.item_type AS item_type,
            stock.kode_warna AS kode_warna,
            stock.warna AS warna,
            history_stock.cluster_old,
            history_stock.kgs,
            history_stock.cns,
            history_stock.lot,
            history_stock.keterangan,
            history_stock.admin,
            history_stock.created_at,
            master_order.no_model AS no_model_meminjam
        ')
            ->join('pengeluaran', 'pengeluaran.id_stock = history_stock.id_stock_old', 'left')
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('history_stock.id_stock_new', null);

        if (!empty($no_model)) {
            $builder->where('stock.no_model', $no_model);
        }

        if (!empty($kode_warna)) {
            $builder->where('stock.kode_warna', $kode_warna);
        }

        return $builder->findAll();
    }

    public function getDataStockAwal($key, $jenis = null)
    {
        $builder = $this->db->table('history_stock hs')
            ->select('s_new.no_model as no_model_new, s_new.item_type, s_new.kode_warna, s_new.warna, hs.kgs, hs.cns, hs.lot, s_new.nama_cluster, DATE(hs.created_at) AS tgl_pindah, hs.keterangan, s_old.no_model as no_model_old, hs.admin')
            ->join('stock s_old', 's_old.id_stock = hs.id_stock_old', 'left')
            ->join('stock s_new', 's_new.id_stock = hs.id_stock_new', 'left')
            ->join('master_material mm ', 'mm.item_type = s_new.item_type', 'left')
            ->where('s_new.no_model', $key)
            ->where('hs.keterangan', 'Pindah Order');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }
        return $builder->groupBy('hs.id_history_pindah')
            ->orderBy('s_old.no_model', 'ASC')
            ->get()
            ->getResultArray();
    }

    // public function getDataDipinjam($key, $jenis = null)
    // {
    //     $builder = $this->db->table('history_stock hs')
    //         ->select('s_new.no_model as no_model_new, s_new.item_type, s_new.kode_warna, s_new.warna, hs.kgs, hs.cns, hs.lot, s_new.nama_cluster, DATE(hs.created_at) AS tgl_pindah, hs.keterangan, s_old.no_model as no_model_old, hs.admin, p.area_out, pm.tgl_pakai, pm.po_tambahan')
    //         ->join('stock s_old', 's_old.id_stock = hs.id_stock_old', 'left')
    //         ->join('stock s_new', 's_new.id_stock = hs.id_stock_new', 'left')
    //         ->join('pengeluaran p', 's_new.id_stock = p.id_stock', 'left')
    //         ->join('pemesanan pm', 'p.id_total_pemesanan = pm.id_total_pemesanan', 'left')
    //         ->join('master_material mm ', 'mm.item_type = s_new.item_type', 'left')
    //         ->where('s_old.no_model', $key)
    //         ->where('hs.keterangan', 'Pinjam Order');

    //     if (!empty($jenis)) {
    //         $builder->where('mm.jenis', $jenis);
    //     }
    //     return $builder->groupBy('hs.id_history_pindah')
    //         ->orderBy('s_old.no_model', 'ASC')
    //         ->get()
    //         ->getResultArray();
    // }

    public function getDataDipindah($key, $jenis = null)
    {
        $builder = $this->db->table('history_stock hs')
            ->select('s_new.no_model as no_model_new, s_new.item_type, s_new.kode_warna, s_new.warna, hs.kgs, hs.cns, hs.lot, s_new.nama_cluster, DATE(hs.created_at) AS tgl_pindah, hs.keterangan, s_old.no_model as no_model_old, hs.admin')
            ->join('stock s_old', 's_old.id_stock = hs.id_stock_old', 'left')
            ->join('stock s_new', 's_new.id_stock = hs.id_stock_new', 'left')
            ->join('master_material mm ', 'mm.item_type = s_new.item_type', 'left')
            ->where('s_old.no_model', $key)
            ->where('hs.keterangan', 'Pindah Order');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }
        return $builder->groupBy('hs.id_history_pindah')
            ->orderBy('s_old.no_model', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getKgsPindahOrder($idOutCelup)
    {
        return $this->select('SUM(kgs) AS kgs_pindah_order, SUM(cns) AS cns_pindah_order')
            ->where('id_out_celup', $idOutCelup)
            ->where('keterangan', 'Pindah Order')
            ->first();
    }

    public function getKgsReturCelup($idOutCelup)
    {
        return $this->select('SUM(kgs) AS kgs_retur_celup, SUM(cns) AS cns_retur_celup')
            ->where('id_out_celup', $idOutCelup)
            ->like('keterangan', 'Retur Celup : ')
            ->first();
    }

    public function getFilterHistoryReturCelup($no_model = null, $no_surat = null)
    {
        $builder = $this->db->table('history_stock')
            ->select('stock.no_model, stock.item_type, stock.kode_warna, stock.warna, history_stock.kgs, history_stock.cns, history_stock.lot, history_stock.krg, stock.nama_cluster, DATE(history_stock.created_at) AS tgl_retur, history_stock.keterangan, history_stock.admin, bon_celup.no_surat_jalan, history_stock.created_at, history_stock.updated_at')
            ->join('stock', 'stock.id_stock = history_stock.id_stock_old', 'left')
            ->join('out_celup', 'history_stock.id_out_celup = out_celup.id_out_celup', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->like('history_stock.keterangan', 'Retur Celup :');

        if (!empty($no_model)) {
            $builder->where('stock.no_model', $no_model);
        }
        if (!empty($no_surat)) {
            $builder->where('bon_celup.no_surat_jalan', $no_surat);
        }

        return $builder->orderBy('history_stock.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
