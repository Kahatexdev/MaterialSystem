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

    public function getHistoryPindahOrder($noModel = null, $kodeWarna = null)
    {
        $builder = $this->db->table('history_stock')
            ->select('s_old.no_model AS no_model_old, s_new.no_model AS no_model_new, s_old.item_type, s_old.kode_warna, s_old.warna, history_stock.kgs, history_stock.cns, history_stock.lot, history_stock.cluster_old, history_stock.cluster_new, history_stock.keterangan, history_stock.created_at')
            ->join('stock s_old', 's_old.id_stock = history_stock.id_stock_old')
            ->join('stock s_new', 's_new.id_stock = history_stock.id_stock_new')
            ->where('keterangan', 'Pindah Order');

        if (!empty($noModel)) {
            $builder->where('s_new.no_model', $noModel)
                ->orWhere('s_old.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('s_new.kode_warna', $kodeWarna);
        }

        return $builder->groupBy('history_stock.id_history_pindah')->orderBy('history_stock.created_at')->get()->getResultArray();
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
}
