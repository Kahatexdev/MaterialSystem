<?php

namespace App\Models;

use CodeIgniter\Model;

class OtherOutModel extends Model
{
    protected $table            = 'other_out';
    protected $primaryKey       = 'id_other_out';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_other_out',
        'id_out_celup',
        'kategori',
        'tgl_other_out',
        'kgs_other_out',
        'cns_other_out',
        'krg_other_out',
        'lot_other_out',
        'nama_cluster',
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

    public function getQty($id, $namaCluster = null)
    {
        return $this->select('SUM(kgs_other_out) AS kgs_other_out, SUM(cns_other_out) AS cns_other_out, SUM(krg_other_out) AS krg_other_out')
            ->where('id_out_celup', $id)
            // ->where('nama_cluster', $namaCluster)
            ->first();
    }
    public function getPakaiLain($key, $jenis = null)
    {
        $builder = $this->db->table('other_out oo')
            ->select('oo.*, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, mm.jenis, sc.po_plus')
            ->join('out_celup oc', 'oc.id_out_celup = oo.id_out_celup', 'left')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->where('oc.no_model', $key);

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('oo.id_other_out')
            ->orderBy('oo.tgl_other_out, sc.item_type, sc.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }
}
