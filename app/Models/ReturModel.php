<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturModel extends Model
{
    protected $table            = 'retur';
    protected $primaryKey       = 'id_retur';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_model',
        'item_type',
        'kode_warna',
        'warna',
        'area_retur',
        'tgl_retur',
        'kgs_retur',
        'cns_retur',
        'krg_retur',
        'lot_retur',
        'kategori',
        'keterangan_area',
        'keterangan_gbn',
        'waktu_acc_retur',
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

    public function getFilteredData($filters)
    {
        $builder = $this->db->table('retur');
        $builder->select('retur.*, master_material.jenis');
        $builder->join('master_material', 'master_material.item_type = retur.item_type', 'left');

        // Apply filters
        if (!empty($filters['jenis'])) {
            $builder->where('master_material.jenis', $filters['jenis']);
        }
        if (!empty($filters['area'])) {
            $builder->where('retur.area_retur', $filters['area']);
        }
        if (!empty($filters['no_model'])) {
            $builder->where('retur.no_model', $filters['no_model']);
        }
        if (!empty($filters['item_type'])) {
            $builder->where('retur.item_type', $filters['item_type']);
        }
        if (!empty($filters['kode_warna'])) {
            $builder->where('retur.kode_warna', $filters['kode_warna']);
        }
        if (!empty($filters['tgl_retur'])) {
            $builder->where('retur.tgl_retur', $filters['tgl_retur']);
        }

        $builder->where('retur.waktu_acc_retur IS NULL');
        $builder->orderBy('retur.tgl_retur', 'DESC');

        return $builder->get()->getResultArray();
    }
}
