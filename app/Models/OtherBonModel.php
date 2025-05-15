<?php

namespace App\Models;

use CodeIgniter\Model;

class OtherBonModel extends Model
{
    protected $table            = 'other_bon';
    protected $primaryKey       = 'id_other_bon';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_other_bon', 'no_model', 'item_type', 'kode_warna', 'warna', 'tgl_datang', 'no_surat_jalan', 'detail_sj', 'ganti_retur', 'admin', 'created_at', 'update_at'];

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

    public function getTglDataOtherBon()
    {
        return $this->distinct()
            ->select('tgl_datang')
            ->orderBy('tgl_datang', 'DESC')
            ->findAll();
    }
    public function filterTglDataOtherBon($tglDatang)
    {
        return $this->distinct()
            ->select('tgl_datang')
            ->where('tgl_datang', $tglDatang)
            ->findAll();
    }
    public function getDataOtherBon($tglDatang)
    {
        return $this->select('other_bon.id_other_bon, other_bon.tgl_datang, other_bon.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan, out_celup.l_m_d, out_celup.harga, SUM(out_celup.gw_kirim) AS gw, SUM(out_celup.kgs_kirim) AS kgs, out_celup.lot_kirim, out_celup.ganti_retur, other_bon.admin, other_bon.created_at')
            ->join('out_celup', 'out_celup.id_other_bon=other_bon.id_other_bon', 'left')
            ->where('other_bon.tgl_datang', $tglDatang)
            ->groupBy('other_bon.id_other_bon')
            ->orderBy('other_bon.id_other_bon', 'DESC')
            ->findAll();
    }
    public function getDataById($id)
    {
        return $this->select('out_celup.id_out_celup, out_celup.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, gw_kirim, out_celup.kgs_kirim, out_celup.cones_kirim, out_celup.lot_kirim, out_celup.ganti_retur, out_celup.no_karung')
            ->join('out_celup', 'out_celup.id_other_bon=other_bon.id_other_bon', 'left')
            ->where('other_bon.id_other_bon', $id)
            ->groupBy('out_celup.id_out_celup')
            ->orderBy('out_celup.id_out_celup', 'ASC')
            ->findAll();
    }
}
