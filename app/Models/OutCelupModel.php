<?php

namespace App\Models;

use CodeIgniter\Model;
use PDO;
use PHPUnit\Framework\Attributes\IgnoreFunctionForCodeCoverage;

class OutCelupModel extends Model
{
    protected $table            = 'out_celup';
    protected $primaryKey       = 'id_out_celup';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_out_celup',
        'id_bon',
        'id_celup',
        'l_m_d',
        'harga',
        'no_karung',
        'gw_kirim',
        'kgs_kirim',
        'cones_kirim',
        'lot_kirim',
        'ganti_retur',
        'admin',
        'created_at',
        'updated_at',
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

    public function getDetailBonByIdBon($id)
    {
        return $this->select('master_order.buyer, master_material.ukuran, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, out_celup.*')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'right')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'right')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'right')
            ->where('out_celup.id_bon', $id)
            ->groupBy('id_out_celup')
            ->findAll();
    }

    public function getDataOut($id)
    {
        return $this->db->table('out_celup')
            ->select('out_celup.*, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.lot_kirim')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.id_out_celup', $id)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getDataOutCelup()
    {
        return $this->db->table('out_celup')
            ->select('bon_celup.id_bon, out_celup.id_out_celup,
                  GROUP_CONCAT(DISTINCT schedule_celup.no_model ORDER BY schedule_celup.no_model ASC SEPARATOR ", ") as no_model_list, 
                  bon_celup.tgl_datang, 
                  bon_celup.no_surat_jalan, 
                  bon_celup.detail_sj')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->join('bon_celup', 'out_celup.id_bon = bon_celup.id_bon')
            ->groupBy('bon_celup.id_bon')
            ->get()
            ->getResultArray();
    }

    public function getDetailByIdBon($id_bon)
    {
        return $this->select('schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, 
                              out_celup.l_m_d, out_celup.harga, out_celup.ganti_retur, 
                              out_celup.gw_kirim, out_celup.kgs_kirim, out_celup.cones_kirim, 
                              out_celup.lot_kirim')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.id_bon', $id_bon)
            ->findAll();
    }

    public function dataCelup($idbon, $idCelup)
    {
        return $this->where('id_bon', $idbon)
            ->where('id_celup', $idCelup)
            ->findAll();
    }
}
