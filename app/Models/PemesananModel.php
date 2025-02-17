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
}
