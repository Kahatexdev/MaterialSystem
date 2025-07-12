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
            $builder->where('s_new.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('s_new.kode_warna', $kodeWarna);
        }

        return $builder->groupBy('history_stock.id_history_pindah')->orderBy('history_stock.created_at')->get()->getResultArray();
    }
    public function getHistoryPinjamOrder() {}
}
