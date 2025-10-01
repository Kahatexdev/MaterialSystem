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
        $builder->select('retur.id_retur, retur.no_model, retur.item_type, retur.kode_warna, retur.warna, retur.area_retur, retur.tgl_retur, SUM(retur.kgs_retur) AS kgs_retur, SUM(retur.cns_retur) AS cns_retur, COUNT(retur.krg_retur) AS total_karung, retur.lot_retur, retur.kategori, retur.keterangan_area, retur.keterangan_gbn, retur.waktu_acc_retur, retur.admin, master_material.jenis');
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
        $builder->groupBy('retur.no_model, retur.item_type, retur.kode_warna, retur.warna, retur.area_retur, retur.tgl_retur, retur.lot_retur, retur.kategori');
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
        $query = $this->select('out_celup.id_out_celup, out_celup.kgs_kirim, out_celup.cones_kirim, out_celup.gw_kirim')
            ->join('out_celup', 'out_celup.id_retur = retur.id_retur')
            ->where('out_celup.no_model', $no_model)
            ->where('retur.item_type', $item_type)
            ->where('retur.kode_warna', $kode_warna)
            ->where('out_celup.lot_kirim', $lot_kirim)
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
    public function filterData($area, $tglBuat, $noModel = null)
    {
        $db = \Config\Database::connect();

        $subPengeluaran = $db->table('pengeluaran p')
            ->select('m.id_order, m.item_type, m.kode_warna, SUM(p.kgs_out) AS terima_kg')
            ->join('pemesanan pm', 'pm.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = pm.id_material', 'left')
            ->where('p.area_out', $area)
            ->groupBy('m.id_order, m.item_type, m.kode_warna');

        $subPoTambahan = $db->table('po_tambahan pt')
            ->select("
        m.id_order, 
        m.item_type, 
        m.kode_warna, 
        SUM(pt.sisa_order_pcs) AS sisa_order_pcs,
        SUM(pt.poplus_mc_kg) AS poplus_mc_kg,
        MAX(pt.poplus_mc_cns) AS poplus_mc_cns,
        SUM(pt.plus_pck_pcs) AS plus_pck_pcs,
        SUM(pt.plus_pck_kg) AS plus_pck_kg,
        MAX(pt.plus_pck_cns) AS plus_pck_cns,
        MAX(tp.ttl_tambahan_kg) AS ttl_tambahan_kg,
        MAX(tp.ttl_tambahan_cns) AS ttl_tambahan_cns,
        MAX(tp.ttl_sisa_bb_dimc) AS sisa_bb_mc,
        MAX(pt.status) AS status
    ")
            ->join('total_potambahan tp', 'tp.id_total_potambahan = pt.id_total_potambahan', 'left')
            ->join('material m', 'm.id_material = pt.id_material', 'left')
            ->where('pt.admin', $area)
            ->where('pt.status', 'approved')
            ->groupBy('m.id_order, m.item_type, m.kode_warna');

        // Subquery SUM material
        $subMaterial = $db->table('material')
            ->select('material.id_material, SUM(material.kgs) AS kgs_sum, SUM(material.qty_pcs) AS qty_pcs_sum')
            ->groupBy('material.id_order, material.item_type, material.kode_warna');

        $builder = $this->select("
        retur.kgs_retur, retur.cns_retur, retur.krg_retur, retur.lot_retur,
        retur.kategori, retur.keterangan_gbn, retur.admin,
        peng.kgs_out AS terima_kg,
        poplus.sisa_bb_mc,
        poplus.sisa_order_pcs,
        poplus.poplus_mc_kg,
        poplus.poplus_mc_cns,
        poplus.plus_pck_pcs,
        poplus.plus_pck_kg,
        poplus.plus_pck_cns,
        poplus.ttl_tambahan_kg,
        poplus.ttl_tambahan_cns, 
        master_order.no_model, master_order.delivery_akhir, material.item_type,
        material.kode_warna, material.color, material.style_size,
        mat.kgs_sum AS kgs,
    ")
            ->join('master_order', 'retur.no_model = master_order.no_model', 'left')
            ->join('material', 'master_order.id_order = material.id_order AND retur.item_type = material.item_type AND retur.kode_warna = material.kode_warna', 'left')
            ->join('po_tambahan', 'po_tambahan.id_material = material.id_material', 'left')
            // ->join("({$subPengeluaran->getCompiledSelect()}) peng", 'peng.no_model = master_order.no_model AND peng.item_type = material.item_type AND peng.kode_warna = material.kode_warna', 'left')
            ->join("({$subPengeluaran->getCompiledSelect()}) peng", 'peng.id_order = master_order.id_order AND peng.item_type = material.item_type AND peng.kode_warna = material.kode_warna', 'left')
            ->join("({$subPoTambahan->getCompiledSelect()}) poplus", 'poplus.id_order = master_order.id_order AND poplus.item_type = material.item_type AND poplus.kode_warna = material.kode_warna', 'left')
            ->join("({$subMaterial->getCompiledSelect()}) mat", 'mat.id_material = material.id_material', 'left')
            ->where('retur.area_retur', $area)
            ->where('retur.tgl_retur', $tglBuat);

        if (!empty($noModel)) {
            $builder->where('master_order.no_model', $noModel);
        }

        return $builder
            ->groupBy('retur.id_retur')
            ->orderBy('master_order.no_model', 'ASC')
            ->orderBy('material.item_type', 'ASC')
            ->orderBy('material.kode_warna', 'ASC')
            ->findAll();
    }
    public function getDataReturGbn($key, $jenis = null)
    {
        $builder = $this->db->table('retur')
            ->select('retur.*')
            ->join('master_material mm', 'mm.item_type = retur.item_type', 'left')
            ->where('retur.no_model', $key)
            ->where('retur.area_retur', 'GUDANG BENANG');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('retur.id_retur')
            ->orderBy('retur.item_type, retur.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getDataReturArea($key, $jenis = null)
    {
        $builder = $this->db->table('retur')
            ->select('retur.*')
            ->join('master_material mm', 'mm.item_type = retur.item_type', 'left')
            ->where('retur.no_model', $key)
            ->where('retur.area_retur <>', 'GUDANG BENANG');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('retur.id_retur')
            ->orderBy('retur.item_type, retur.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getDataReturStock($key, $jenis = null)
    {
        $builder = $this->db->table('retur')
            ->select('retur.*')
            ->join('master_material mm', 'mm.item_type = retur.item_type', 'left')
            ->join('kategori_retur kr', 'retur.kategori = kr.nama_kategori', 'left')
            ->where('retur.no_model', $key)
            ->where('retur.area_retur <>', 'GUDANG BENANG')
            ->where('kr.tipe_kategori', 'SIMPAN ULANG');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('retur.id_retur')
            ->orderBy('retur.item_type, retur.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getDataReturTitip($key, $jenis = null)
    {
        $builder = $this->db->table('retur')
            ->select('retur.*')
            ->join('master_material mm', 'mm.item_type = retur.item_type', 'left')
            ->join('kategori_retur kr', 'retur.kategori = kr.nama_kategori', 'left')
            ->where('retur.no_model', $key)
            ->where('retur.area_retur <>', 'GUDANG BENANG')
            ->where('kr.tipe_kategori', 'BAHAN BAKU TITIP');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('retur.id_retur')
            ->orderBy('retur.item_type, retur.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getNoKarung($id)
    {
        return $this->select('out_celup.no_karung, out_celup.gw_kirim, out_celup.cones_kirim')
            ->join('retur', 'retur.id_retur=out_celup.id_retur')
            ->where('out_celup.no_model', $id['no_mdoel'])
            ->where('retur.item_type', $id['item_type'])
            ->where('retur.kode_warna', $id['kode_warna'])
            ->where('out_celup.lot_kirim', $id['lot'])
            ->findAll();
    }
    public function getTotalRetur($data)
    {
        return $this->select('SUM(retur.kgs_retur) AS kgs_retur')
            ->where('retur.area_retur', $data['area'])
            ->where('retur.waktu_acc_retur IS NOT NULL')
            ->where('retur.no_model', $data['no_model'])
            ->where('retur.item_type', $data['item_type'])
            ->where('retur.kode_warna', $data['kode_warna'])
            ->first();
    }
}
