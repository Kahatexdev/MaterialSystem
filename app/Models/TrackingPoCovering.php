<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackingPoCovering extends Model
{
    protected $table            = 'tracking_po_covering';
    protected $primaryKey       = 'id_tpc';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_po_gbn',
        'status',
        'keterangan',
        'admin',
        'created_at',
        'updated_at'
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

    public function trackingData()
    {
        return $this->db->table('tracking_po_covering')
            ->select('tracking_po_covering.id_tpc,tracking_po_covering.status, tracking_po_covering.keterangan,tracking_po_covering.admin,open_po.no_model, open_po.item_type, open_po.kode_warna, open_po.color, open_po.kg_po, open_po.created_at')
            ->join('open_po', 'tracking_po_covering.id_po_gbn = open_po.id_po')
            ->get()
            ->getResultArray();
    }

    public function trackingDataDaily($date)
    {
        return $this->db->table('tracking_po_covering')
            ->select('
                tracking_po_covering.id_tpc,
                tracking_po_covering.status,
                tracking_po_covering.keterangan,
                tracking_po_covering.admin,
                anak.no_model AS no_model_anak,
                anak.item_type,
                anak.kode_warna,
                anak.color,
                anak.kg_po,
                anak.created_at
            ')
            ->join('open_po AS induk', 'tracking_po_covering.id_po_gbn = induk.id_po')
            ->join('open_po AS anak', 'anak.id_induk = induk.id_po')
            ->where('DATE(anak.created_at)', $date)
            ->where('anak.penerima', 'Retno')
            ->where('anak.penanggung_jawab', 'Paryanti')
            ->get()
            ->getResultArray();
    }

    public function statusBahanBaku($model, $itemType, $kodeWarna, $search = null)
    {
        $builder = $this->select([
            'open_po.item_type',
            'open_po.kode_warna',
            'tracking_po_covering.id_po_gbn',
            'tracking_po_covering.status',
            'tracking_po_covering.keterangan',
            'tracking_po_covering.admin',
            'tracking_po_covering.created_at',
            'tracking_po_covering.updated_at'
        ])
            ->join('open_po', 'open_po.id_po = tracking_po_covering.id_po_gbn', 'left')
            ->like('open_po.no_model',   $model)
            ->where('open_po.item_type',  $itemType)
            ->where('open_po.kode_warna', $kodeWarna);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('open_po.no_model', $search)
                ->orLike('open_po.item_type', $search)
                ->orLike('open_po.kode_warna', $search)
                ->groupEnd();
        }

        return $builder->findAll();
    }
}
