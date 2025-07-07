<?php

namespace App\Models;

use CodeIgniter\Model;

class PoTambahanModel extends Model
{
    protected $table            = 'po_tambahan';
    protected $primaryKey       = 'id_po_tambahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_material',
        'terima_kg',
        'sisa_bb_mc',
        'sisa_order_pcs',
        'bs_mesin_kg',
        'bs_st_pcs',
        'poplus_mc_kg',
        'poplus_mc_cns',
        'plus_pck_pcs',
        'plus_pck_kg',
        'plus_pck_cns',
        'lebih_pakai_kg',
        'keterangan',
        'status',
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

    public function filterData($area, $tglBuat, $noModel = null)
    {
        $builder = $this->select('po_tambahan.*, master_order.no_model, master_order.delivery_akhir, material.item_type, material.kode_warna, material.color, material.style_size, material.kgs, material.composition, material.gw, material.qty_pcs, material.loss')
            ->join('material', 'po_tambahan.id_material = material.id_material', 'left')
            ->join('master_order', 'material.id_order = master_order.id_order', 'left')
            ->where('material.area', $area)
            ->like('po_tambahan.created_at', $tglBuat);
        // Cek apakah tglBuat diisi, baru apply filter
        if (!empty($noModel)) {
            $builder->where('master_order.no_model', $noModel);
        }
        return $builder
            ->groupBy('po_tambahan.id_po_tambahan')
            ->orderBy('master_order.no_model', 'ASC')
            ->orderBy('material.item_type', 'ASC')
            ->orderBy('material.kode_warna', 'ASC')
            ->orderBy('material.style_size', 'ASC')
            ->findAll();
    }
    public function getData()
    {
        return $this->select('po_tambahan.id_po_tambahan, master_order.no_model, material.item_type, material.kode_warna, material.color, (SUM(po_tambahan.poplus_mc_kg) + SUM(po_tambahan.plus_pck_kg)) AS kg_poplus, (po_tambahan.poplus_mc_cns + po_tambahan.plus_pck_cns) AS cns_poplus, po_tambahan.status, DATE(po_tambahan.created_at) AS tgl_poplus, po_tambahan.admin, master_material.jenis')
            ->join('material', 'po_tambahan.id_material = material.id_material', 'left')
            ->join('master_order', 'material.id_order = master_order.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->groupBy('DATE(po_tambahan.created_at)', false)
            ->groupBy('master_order.no_model')
            ->groupBy('material.item_type')
            ->groupBy('material.kode_warna')
            ->groupBy('po_tambahan.status')
            ->orderBy('po_tambahan.status', 'ASC')
            ->orderBy('po_tambahan.created_at', 'DESC')
            ->findAll();
    }
    public function detailPoTambahan($idMaterial, $tglBuat, $status)
    {
        return $this->select('material.style_size, po_tambahan.*')
            ->join('material', 'po_tambahan.id_material = material.id_material', 'left')
            ->whereIn('po_tambahan.id_material', $idMaterial)
            ->like('po_tambahan.created_at', $tglBuat)
            ->where('po_tambahan.status', $status)
            ->findAll();
    }
    public function getNoModelByArea($area)
    {
        return $this->select('po_tambahan.admin, po_tambahan.status, master_order.no_model as model')
            ->join('material', 'material.id_material=po_tambahan.id_material', 'left')
            ->join('master_order', 'master_order.id_order=material.id_order', 'left')
            ->where('po_tambahan.admin', $area)
            ->where('po_tambahan.status', 'approved')
            ->groupBy('master_order.no_model')
            ->orderBy('master_order.no_model', 'ASC')
            ->findAll();
    }
    public function getStyleSizeBYNoModelArea($area, $noModel)
    {
        return $this->select('po_tambahan.admin, po_tambahan.status, master_order.no_model as model, material.style_size as size')
            ->join('material', 'material.id_material=po_tambahan.id_material', 'left')
            ->join('master_order', 'master_order.id_order=material.id_order', 'left')
            ->where('master_order.no_model', $noModel)
            ->where('po_tambahan.admin', $area)
            ->where('po_tambahan.status', 'approved')
            ->groupBy('material.style_size')
            ->orderBy('material.style_size', 'ASC')
            ->findAll();
    }
    public function getMuPoTambahan($no_model, $style_size, $area)
    {
        return $this->select(' master_material.jenis, material.*, SUM(po_tambahan.poplus_mc_kg+po_tambahan.plus_pck_kg) AS ttl_keb')
            ->join('material', 'material.id_material=po_tambahan.id_material', 'left')
            ->join('master_material', 'master_material.item_type=material.item_type', 'left')
            ->join('master_order', 'master_order.id_order=material.id_order', 'left')
            ->where('po_tambahan.admin', $area)
            ->where('master_order.no_model', $no_model)
            ->where('material.style_size', $style_size)
            ->where('po_tambahan.status', 'approved')
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna')
            ->findAll();
    }
    public function getKgPoTambahan($data)
    {
        $no_model = $data['no_model'] ?? null;
        $item_type = $data['item_type'] ?? null;
        $kode_warna = $data['kode_warna'] ?? null;
        $style_size = $data['style_size'] ?? null;
        $area = $data['area'] ?? null;

        return $this->select('SUM(po_tambahan.poplus_mc_kg+po_tambahan.plus_pck_kg) AS ttl_keb_potambahan')
            ->join('material', 'material.id_material=po_tambahan.id_material', 'left')
            ->join('master_material', 'master_material.item_type=material.item_type', 'left')
            ->join('master_order', 'master_order.id_order=material.id_order', 'left')
            ->where('po_tambahan.admin', $area)
            ->where('master_order.no_model', $no_model)
            ->where('material.style_size', $style_size)
            ->where('material.item_type', $item_type)
            ->where('material.kode_warna', $kode_warna)
            ->where('po_tambahan.status', 'approved')
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna')
            ->first();
    }
    public function getDataPoPlus($tgl_po, $no_model = null, $kode_warna = null)
    {
        $builder = $this->select('po_tambahan.id_po_tambahan, master_order.no_model, material.area, material.item_type, material.kode_warna, material.color, (SUM(po_tambahan.poplus_mc_kg) + SUM(po_tambahan.plus_pck_kg)) AS kg_poplus, (po_tambahan.poplus_mc_cns + po_tambahan.plus_pck_cns) AS cns_poplus, po_tambahan.status, DATE(po_tambahan.created_at) AS tgl_poplus, po_tambahan.admin, po_tambahan.keterangan, master_material.jenis')
            ->join('material', 'po_tambahan.id_material = material.id_material', 'left')
            ->join('master_order', 'material.id_order = master_order.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->groupBy('DATE(po_tambahan.created_at)', false)
            ->groupBy('master_order.no_model')
            ->groupBy('material.item_type')
            ->groupBy('material.kode_warna')
            ->groupBy('po_tambahan.status')
            ->where('DATE(po_tambahan.created_at)', $tgl_po)
            ->where('status', 'approved');
        if (!empty($noModel)) {
            $builder->where('master_order.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('material.kode_warna', $kodeWarna);
        }
        return $builder->orderBy('po_tambahan.status', 'ASC')
            ->orderBy('po_tambahan.created_at', 'DESC')
            ->findAll();
    }
}
