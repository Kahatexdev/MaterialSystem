<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananSpandexKaretModel extends Model
{
    protected $table            = 'pemesanan_spandex_karet';
    protected $primaryKey       = 'id_psk';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_psk',
        'id_total_pemesanan',
        'status',
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

    public function getListPemesananCovering($jenis, $tgl_pakai)
    {
        return $this->select('pemesanan_spandex_karet.id_psk,pemesanan_spandex_karet.id_total_pemesanan, pemesanan_spandex_karet.status,pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_berat_cones) AS total_pesan, SUM(pemesanan.ttl_qty_cones) AS total_cones, pemesanan.admin')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan_spandex_karet.id_total_pemesanan', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->groupBy('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, pemesanan.admin')
            ->findAll();
    }

    public function getDataForPdf($jenis, $tgl_pakai)
    {
        return $this->select('pemesanan_spandex_karet.id_psk, pemesanan_spandex_karet.status,pemesanan.tgl_pakai, master_material.jenis, material.item_type, master_order.no_model, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_berat_cones) AS total_pesan, SUM(pemesanan.ttl_qty_cones) AS total_cones, pemesanan.admin AS area, history_stock_covering.*')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan_spandex_karet.id_total_pemesanan', 'left')
            ->join('history_stock_covering', 'history_stock_covering.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->where('history_stock_covering.id_total_pemesanan = pemesanan_spandex_karet.id_total_pemesanan')
            ->groupBy('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, pemesanan.admin, history_stock_covering.id_history_covering_stock')
            ->findAll();
    }

    public function getListPemesananSpandexKaret($area, $jenis, $tgl_pakai)
    {
        return $this->select('pemesanan_spandex_karet.id_psk, pemesanan_spandex_karet.status,pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_berat_cones) AS total_pesan, SUM(pemesanan.ttl_qty_cones) AS total_cones, pemesanan.admin')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan_spandex_karet.id_total_pemesanan', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('material.area', $area)
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->groupBy('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, pemesanan.admin')
            ->findAll();
    }

    public function getNoModelById($id_psk)
    {
        return $this->select('master_order.no_model')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan_spandex_karet.id_total_pemesanan', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan_spandex_karet.id_psk', $id_psk)
            ->first();
    }
}
