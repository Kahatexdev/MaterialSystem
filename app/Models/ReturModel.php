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

    public function getItemTypeByModel($pdk)
    {
        return $this->select('item_type')
            ->join('out_celup', 'out_celup.id_retur=retur.id_retur')
            ->where('no_model', $pdk)
            ->groupBy('no_model')
            ->groupBy('item_type')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKodeWarnaByModelAndItemType($no_model, $item_type)
    {
        return $this->select('kode_warna')
            ->join('out_celup', 'out_celup.id_retur=retur.id_retur')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->groupBy('kode_warna')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getWarnaByKodeWarna($no_model, $item_type, $kode_warna)
    {
        return $this->select('warna')
            ->join('out_celup', 'out_celup.id_retur=retur.id_retur')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('warna')
            ->distinct()
            ->get()
            ->getRowArray();
    }

    public function getLotByKodeWarna($no_model, $item_type, $kode_warna)
    {
        return $this->select('lot_retur AS lot_kirim')
            ->join('out_celup', 'out_celup.id_retur=retur.id_retur')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('lot_retur')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
        $query = $this->select('out_celup.id_out_celup, retur.kgs_retur as kgs_kirim, retur.cns_retur as cones_kirim')
            ->join('out_celup', 'out_celup.id_retur = retur.id_retur')
            ->where('retur.no_model', $no_model)
            ->where('retur.item_type', $item_type)
            ->where('retur.kode_warna', $kode_warna)
            ->where('retur.lot_retur', $lot_kirim)
            ->where('out_celup.no_karung', $no_karung)
            ->get();

        $sql = $this->db->getLastQuery(); // Debugging query
        log_message('error', 'Query getKgsDanCones: ' . $sql); // Log ke CI4 logs

        return $query->getRowArray(); // Pastikan return berbentuk array
    }

    public function getDataRetur($id, $idRetur)
    {
        return $this->db->table('out_celup')
            ->select('retur.*, out_celup.id_out_celup')
            ->join('retur', 'retur.id_retur = retur.id_retur', 'left')
            ->where('out_celup.id_out_celup', $id)
            ->where('retur.id_retur', $idRetur)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function listBarcodeRetur()
    {
        return $this->db->table('retur')
            ->select('tgl_retur')
            ->where('retur.waktu_acc_retur IS NOT NULL')
            ->like('retur.keterangan_gbn', 'Approve:')
            ->groupBy('tgl_retur')
            ->orderBy('tgl_retur', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function detailBarcodeRetur($tgl_retur)
    {
        return $this->db->table('retur')
            ->select('retur.id_retur, retur.no_model, retur.item_type, retur.kode_warna, retur.warna, retur.lot_retur, retur.kgs_retur, retur.cns_retur, retur.tgl_retur')
            ->where('retur.tgl_retur', $tgl_retur)
            ->where('retur.waktu_acc_retur IS NOT NULL')
            ->like('retur.keterangan_gbn', 'Approve:')
            ->get()
            ->getResultArray();
    }

    // public function getDataOut($id)
    // {
    //     return $this->db->table('out_celup')
    //         ->select('out_celup.*, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna')
    //         ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
    //         ->where('out_celup.id_out_celup', $id)
    //         ->distinct()
    //         ->get()
    //         ->getResultArray();
    // }
    public function getListRetur($area, $noModel = null, $tglBuat = null)
    {
        $builder = $this->where('area_retur', $area);
        if (!empty($noModel)) {
            $builder = $this->where('no_model', $noModel);
        }
        if (!empty($tglBuat)) {
            $builder = $this->where('DATE(created_at)', $tglBuat);
        }
        return $builder->findAll();
    }

    public function getFilterReturArea($area = null, $kategori = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $this->select('
        retur.id_retur, retur.no_model, retur.item_type, retur.kode_warna, retur.warna,
        ROUND(SUM(retur.kgs_retur), 2) AS kg, 
        SUM(retur.cns_retur) AS cns, 
        SUM(retur.krg_retur) AS karung,
        retur.lot_retur, retur.keterangan_area, retur.keterangan_gbn,
        retur.admin, retur.area_retur, retur.tgl_retur, retur.kategori, retur.waktu_acc_retur,
        mm.jenis,
        m.total_kgs,
        m.loss
    ')
            ->join('master_material mm', 'mm.item_type = retur.item_type', 'inner')
            ->join(
                '(SELECT item_type, kode_warna, ROUND(SUM(kgs), 2) as total_kgs, loss as loss FROM material GROUP BY item_type, kode_warna) m',
                'm.item_type = retur.item_type AND m.kode_warna = retur.kode_warna',
                'left'
            )
            ->where('retur.waktu_acc_retur IS NOT NULL')
            ->like('retur.keterangan_gbn', 'Approve:')
            ->groupBy('retur.no_model, retur.item_type, retur.kode_warna, retur.kategori');

        // Filter opsional
        if (!empty($area)) {
            $this->where('retur.area_retur', $area);
        }

        if (!empty($kategori)) {
            $this->where('retur.kategori', $kategori);
        }

        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('retur.tgl_retur >=', $tanggal_awal)
                    ->where('retur.tgl_retur <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('retur.tgl_retur >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('retur.tgl_retur <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }

        return $this->findAll();
    }
    public function getReturByAreaModel($area, $noModel)
    {
        return $this->select('retur.*')
            ->join('kategori_retur', 'kategori_retur.nama_kategori=retur.kategori', 'left')
            ->where('retur.area_retur', $area)
            ->where('retur.no_model', $noModel)
            ->where('kategori_retur.tipe_kategori', 'SIMPAN ULANG')
            ->groupBy('id_retur')
            ->findAll();
    }
}
