<?php

namespace App\Models;

use CodeIgniter\Model;

class TotalPoTambahanModel extends Model
{
    protected $table            = 'total_potambahan';
    protected $primaryKey       = 'id_total_potambahan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_total_potambahan',
        'ttl_tambahan_kg',
        'ttl_tambahan_cns',
        'ttl_terima_kg',
        'ttl_sisa_jatah',
        'ttl_sisa_bb_dimc',
        'loss_aktual',
        'loss_tambahan',
        'keterangan',
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

    public function getKgPoTambahan($data)
    {
        $no_model = $data['no_model'] ?? null;
        $item_type = $data['item_type'] ?? null;
        $kode_warna = $data['kode_warna'] ?? null;
        $style_size = $data['style_size'] ?? null;
        $area = $data['area'] ?? null;

        $query = $this->select('total_potambahan.ttl_tambahan_kg AS ttl_keb_potambahan')
            ->join('po_tambahan', 'po_tambahan.id_total_potambahan = total_potambahan.id_total_potambahan', 'left')
            ->join('material', 'material.id_material = po_tambahan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('po_tambahan.admin', $area)
            ->where('master_order.no_model', $no_model)
            ->where('material.item_type', $item_type)
            ->where('material.kode_warna', $kode_warna)
            ->where('po_tambahan.status', 'approved');

        // If $style_size is not null, add the condition
        if (!empty($style_size)) {
            $query->where('material.style_size', $style_size);
        }

        $query = $query->groupBy('master_order.no_model, material.item_type, material.kode_warna')
            ->first();

        return $query;
    }
}
