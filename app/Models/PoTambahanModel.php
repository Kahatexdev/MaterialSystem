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

    public function filterData($area, $noModel)
    {
        $subquery = $this->db->table('pemesanan')
            ->select('id_material, admin, SUM(ttl_berat_cones) AS kgs_pesan')
            ->groupBy('id_material, admin');

        return $this->select('po_tambahan.*, master_order.no_model, master_order.delivery_akhir, material.item_type, material.kode_warna, material.color, material.style_size, material.composition, material.gw, material.qty_pcs, material.loss, pem.kgs_pesan, SUM(pengeluaran.kgs_out) AS kgs_kirim')
            ->join('material', 'po_tambahan.id_material = material.id_order', 'left')
            ->join('master_order', 'material.id_order = master_order.id_order', 'left')
            ->join("({$subquery->getCompiledSelect()}) pem", 'material.id_material = pem.id_material', 'left')
            ->join('pemesanan', 'pemesanan.id_material = material.id_material', 'left') // Diperlukan
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('pengeluaran', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->where('material.area', $area)
            ->where('master_order.no_model', $noModel)
            ->groupBy('po_tambahan.id_po_tambahan')
            ->orderBy('po_tambahan.created_at', 'ASC')
            ->orderBy('material.style_size', 'ASC')
            ->orderBy('material.item_type', 'ASC')
            ->orderBy('material.kode_warna', 'ASC')
            ->findAll();
    }

    public function getData()
    {
        return $this->select('po_tambahan.id_po_tambahan, po_tambahan.no_model, po_tambahan.item_type, po_tambahan.kode_warna, po_tambahan.color, SUM(po_tambahan.kg_po_tambahan) AS kg_poplus, SUM(po_tambahan.cns_po_tambahan) AS cns_poplus, po_tambahan.status, DATE(po_tambahan.created_at) AS tgl_poplus, po_tambahan.admin, master_material.jenis')
            ->join('master_material', 'master_material.item_type = po_tambahan.item_type', 'left')
            ->groupBy('DATE(po_tambahan.created_at)', false)
            ->groupBy('no_model')
            ->groupBy('item_type')
            ->groupBy('kode_warna')
            ->groupBy('status')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
