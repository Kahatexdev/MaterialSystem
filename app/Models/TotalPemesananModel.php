<?php

namespace App\Models;

use CodeIgniter\Model;

class TotalPemesananModel extends Model
{
    protected $table            = 'total_pemesanan';
    protected $primaryKey       = 'id_total_pemesanan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_total_pemesanan',
        'ttl_jl_mc',
        'id_material',
        'ttl_kg',
        'ttl_cns'
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

    public function getDataPemesanan($area, $jenis, $tgl_pakai)
    {
        $query = $this->db->table('total_pemesanan tp')
            ->select("tp.id_total_pemesanan, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, p.id_pemesanan, p.tgl_pakai, m.area, mo.no_model, m.item_type, m.kode_warna, m.color, GROUP_CONCAT(DISTINCT p.lot) AS lot_pesan, GROUP_CONCAT(DISTINCT p.keterangan) ket_pesan, SUM(CASE WHEN pp.status = 'pengiriman area' THEN pp.kgs_out ELSE 0 END) AS kg_kirim, 
        COUNT(CASE WHEN pp.status = 'pengiriman area' THEN pp.id_pengeluaran ELSE NULL END) AS krg_kirim, 
        GROUP_CONCAT(DISTINCT CASE WHEN pp.status = 'pengiriman area' THEN pp.lot_out ELSE NULL END) AS lot_kirim, 
        GROUP_CONCAT(DISTINCT CASE WHEN pp.status = 'pengiriman area' THEN pp.nama_cluster ELSE NULL END) AS cluster_kirim, CASE WHEN p.po_tambahan = '1' THEN 'YA' ELSE '' END AS po_tambahan")
            ->join('pengeluaran pp', 'pp.id_total_pemesanan = tp.id_total_pemesanan', 'left')
            ->join('pemesanan p', 'p.id_total_pemesanan = tp.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('m.area', $area)
            ->where('mm.jenis', $jenis)
            ->where('p.tgl_pakai', $tgl_pakai)
            ->groupBy('p.tgl_pakai')
            ->groupBy('m.area')
            ->groupBy('mo.no_model')
            ->groupBy('m.item_type')
            ->groupBy('m.kode_warna')
            ->groupBy('p.po_tambahan')
            ->get();
        if (!$query) {
            // Cek error pada query
            print_r($this->db->error());
            return false;
        }

        return $query->getResultArray();
    }

    public function getDataPemesananbyId($id)
    {
        return $this->select('
            total_pemesanan.id_total_pemesanan,
            pemesanan.id_pemesanan,
            pemesanan.tgl_pakai,
            total_pemesanan.ttl_jl_mc,
            total_pemesanan.ttl_kg,
            total_pemesanan.ttl_cns,
            pemesanan.lot,
            pemesanan.keterangan,
            pemesanan.po_tambahan,
            pemesanan.id_total_pemesanan,
            pemesanan.status_kirim,
            pemesanan.admin,
            material.id_material,
            material.item_type,
            material.kode_warna,
            material.color,
            material.style_size,
            material.qty_cns,
            material.qty_berat_cns,
            master_order.no_model
        ')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('total_pemesanan.id_total_pemesanan', $id)
            ->first();
    }
}
