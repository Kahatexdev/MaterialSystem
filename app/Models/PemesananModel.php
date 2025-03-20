<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananModel extends Model
{
    protected $table            = 'pemesanan';
    protected $primaryKey       = 'id_pemesanan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pemesanan',
        'id_material',
        'tgl_list',
        'tgl_pesan',
        'tgl_pakai',
        'jl_mc',
        'ttl_qty_cones',
        'ttl_berat_cones',
        'sisa_kgs_mc',
        'sisa_cones_mc',
        'lot',
        'keterangan',
        'po_tambahan',
        'id_pengeluaran',
        'id_retur',
        'status_kirim',
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

    public function getDataPemesanan($area, $jenis)
    {
        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan, p.tgl_pakai, m.area, mo.no_model, m.item_type, m.kode_warna, m.color, SUM(p.jl_mc) AS jl_mc, (SUM(COALESCE(p.ttl_berat_cones, 0)) - SUM(COALESCE(p.sisa_kgs_mc, 0))) AS kgs_pesan, (SUM(COALESCE(p.ttl_qty_cones, 0)) - SUM(COALESCE(p.sisa_cones_mc, 0))) AS cns_pesan, CASE WHEN p.po_tambahan = '1' THEN 'YA' ELSE '' END AS po_tambahan")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('m.area', $area)
            ->where('mm.jenis', $jenis)
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
    public function getListPemesananByArea($area)
    {
        $query = $this->db->table('pemesanan')
            ->select("
                pemesanan.admin,
                pemesanan.tgl_pakai,
                master_order.no_model,
                material.item_type,
                material.kode_warna,
                material.color,
                SUM(material.kgs) AS kg_keb,
                SUM(pemesanan.jl_mc) AS jl_mc,
                SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
                SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
                AVG(pemesanan.sisa_kgs_mc) AS qty_sisa,
                AVG(pemesanan.sisa_cones_mc) AS cns_sisa,
                pemesanan.lot,
                pemesanan.keterangan,
                (SUM(material.kgs) - SUM(DISTINCT COALESCE(pengeluaran.id_pengeluaran, 0) * COALESCE(pengeluaran.kgs_out, 0) / NULLIF(COALESCE(pengeluaran.id_pengeluaran, 1), 0))) AS sisa_jatah
            ")
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('pengeluaran', 'pengeluaran.id_pengeluaran = pemesanan.id_pengeluaran', 'left')
            ->where('pemesanan.admin', $area)
            ->where('pemesanan.status_kirim', '')
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai')
            ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC')
            ->orderBy('pemesanan.tgl_pakai', 'DESC');
        return $query->get()->getResultArray();
    }

    public function getJenisPemesananCovering($jenis)
    {
        return $this->select('pemesanan.tgl_pakai, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->groupBy('pemesanan.tgl_pakai')
            ->findAll();
    }

    public function getListPemesananCovering($jenis, $tgl_pakai)
    {
        return $this->select('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_berat_cones) AS total_pesan, SUM(pemesanan.ttl_qty_cones) AS total_cones, pemesanan.admin')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->groupBy('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, pemesanan.admin')
            ->findAll();
    }

    public function totalPemesananPerHari()
    {
        return $this->select('COUNT(pemesanan.tgl_pesan) as pemesanan_per_hari')
            ->where('pemesanan.tgl_pesan', date('Y-m-d'))
            ->first();
    }
}
