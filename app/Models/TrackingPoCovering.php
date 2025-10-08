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
                induk.no_model AS no_model_anak,
                induk.item_type,
                induk.kode_warna,
                induk.color,
                induk.kg_po,
                induk.created_at
            ')
            ->join('open_po AS induk', 'tracking_po_covering.id_po_gbn = induk.id_induk')
            ->where('DATE(induk.created_at)', $date)
            ->where('induk.penerima', 'Retno')
            ->where('induk.penanggung_jawab', 'Paryanti')
            ->get()
            ->getResultArray();
    }

    public function statusBahanBaku($model, $itemType, $kodeWarna, $search = null)
    {
        $builder = $this->select([
            'induk.item_type',
            'induk.kode_warna',
            'tracking_po_covering.id_po_gbn',
            'tracking_po_covering.status',
            'tracking_po_covering.keterangan',
            'tracking_po_covering.admin',
            'tracking_po_covering.created_at',
            'tracking_po_covering.updated_at'
        ])
            ->join('open_po AS induk', 'tracking_po_covering.id_po_gbn = induk.id_po', 'left')
            ->join('open_po', 'open_po.id_induk = tracking_po_covering.id_po_gbn', 'left')
            ->like('induk.no_model',   $model)
            ->where('induk.item_type',  $itemType)
            ->where('induk.kode_warna', $kodeWarna);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('induk.no_model', $search)
                ->orLike('induk.item_type', $search)
                ->orLike('induk.kode_warna', $search)
                ->groupEnd();
        }
        // dd($builder->getCompiledSelect());
        return $builder->findAll();
    }

    public function dailyUpdateTrackingPO()
    {
        return $this->db->table('tracking_po_covering')
            ->select('
                tracking_po_covering.id_tpc,
                tracking_po_covering.status,
                tracking_po_covering.keterangan,
                tracking_po_covering.admin,
                induk.no_model AS no_model_anak,
                induk.item_type,
                induk.kode_warna,
                induk.color,
                induk.kg_po,
                induk.created_at
            ')
            ->join('open_po AS induk', 'tracking_po_covering.id_po_gbn = induk.id_induk')
            ->where('induk.created_at >=', date('Y-m-01', strtotime('-1 month')))
            ->where('induk.created_at <', date('Y-m-01', strtotime('+2 month')))
            ->where('induk.penerima', 'Retno')
            ->where('induk.penanggung_jawab', 'Paryanti')
            ->get()
            ->getResultArray();
    }

    public function trackingDataPoBooking($date)
    {
        return $this->db->table('tracking_po_covering')
            ->select('
                tracking_po_covering.id_tpc,
                tracking_po_covering.status,
                tracking_po_covering.keterangan,
                tracking_po_covering.admin,
                open_po.no_model,
                open_po.item_type,
                open_po.kode_warna,
                open_po.color,
                open_po.kg_po,
                open_po.created_at
            ')
            ->join('open_po', 'tracking_po_covering.id_po_gbn = open_po.id_po')
            ->where('DATE(open_po.created_at)', $date)
            ->where('open_po.penerima', 'Retno')
            ->where('open_po.penanggung_jawab', 'Paryanti')
            ->where('open_po.po_booking', '1')
            ->get()
            ->getResultArray();
    }
}
