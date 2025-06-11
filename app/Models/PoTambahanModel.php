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
        'area',
        'no_model',
        'style_size',
        'item_type',
        'kode_warna',
        'color',
        'pcs_po_tambahan',
        'kg_po_tambahan',
        'cns_po_tambahan',
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

        return $this->select('po_tambahan.*, master_order.delivery_akhir, material.composition, material.gw, material.qty_pcs, material.loss, pem.kgs_pesan, SUM(pengeluaran.kgs_out) AS kgs_kirim')
            ->join('master_order', 'master_order.no_model = po_tambahan.no_model', 'left')
            ->join('material', 'master_order.id_order = material.id_order AND po_tambahan.item_type = material.item_type AND po_tambahan.kode_warna = material.kode_warna AND po_tambahan.color = material.color', 'left')
            ->join("({$subquery->getCompiledSelect()}) pem", 'material.id_material = pem.id_material AND po_tambahan.area = pem.admin', 'left')
            ->join('pemesanan', 'pemesanan.id_material = material.id_material', 'left') // Diperlukan
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('pengeluaran', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->where('po_tambahan.area', $area)
            ->where('po_tambahan.no_model', $noModel)
            ->groupBy('po_tambahan.id_po_tambahan')
            ->orderBy('po_tambahan.created_at', 'ASC')
            ->orderBy('po_tambahan.style_size', 'ASC')
            ->orderBy('po_tambahan.item_type', 'ASC')
            ->orderBy('po_tambahan.kode_warna', 'ASC')
            ->findAll();
    }

    public function getData()
    {
        return $this->select('po_tambahan.id_po_tambahan, po_tambahan.no_model, po_tambahan.item_type, po_tambahan.kode_warna, po_tambahan.color, SUM(po_tambahan.kg_po_tambahan) AS kg_poplus, SUM(po_tambahan.cns_po_tambahan) AS cns_poplus, po_tambahan.status, DATE(po_tambahan.created_at) AS tgl_poplus, po_tambahan.admin')
            ->groupBy('DATE(po_tambahan.created_at)', false)
            ->groupBy('no_model')
            ->groupBy('item_type')
            ->groupBy('kode_warna')
            ->groupBy('status')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
