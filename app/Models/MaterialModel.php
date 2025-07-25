<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table            = 'material';
    protected $primaryKey       = 'id_material';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_material', 'id_order', 'style_size', 'area', 'inisial', 'color', 'item_type', 'kode_warna', 'composition', 'gw', 'qty_pcs', 'loss', 'kgs', 'admin', 'qty_cns', 'qty_berat_cns', 'created_at', 'updated_at'];

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

    public function getMaterial($id_order)
    {
        return $this->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.id_order', $id_order)->findAll();
    }
    public function getTotalKebutuhan($id_order)
    {
        return $this->select('material.item_type, material.kode_warna, material.color, sum(kgs) as kebutuhan')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.id_order', $id_order)
            ->groupBy('material.kode_warna, material.item_type')
            ->findAll();
    }

    public function getQtyPO($id_order, $item_type, $kode_warna)
    {
        return $this->db->table('material')
            ->select('sum(kgs) as kgs')
            ->where('id_order', $id_order)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('id_order')
            ->groupBy('item_type')
            ->groupBy('kode_warna')
            ->get()
            ->getRowArray();
    }

    public function getQtyPOByNoModel($noModel, $itemType, $kodeWarna)
    {
        return $this->select('SUM(kgs) as qty_po,master_order.delivery_awal, master_order.delivery_akhir')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->groupBy('no_model, item_type, kode_warna')
            ->first();
    }

    public function getNomorModel($id_order)
    {
        return $this->select('no_model, master_order.id_order')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.id_order', $id_order)
            ->first();
    }
    public function getQtyPOForCelup($nomodel, $itemtype, $kodewarna)
    {
        return $this->select('master_order.no_model, master_order.delivery_awal, master_order.delivery_akhir, material.item_type, material.kode_warna, material.color, sum(material.kgs) as qty_po')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_order.no_model', $nomodel)
            ->where('material.item_type', $itemtype)
            ->where('material.kode_warna', $kodewarna)
            ->groupBy('master_order.no_model')
            ->groupBy('material.item_type')
            ->groupBy('material.kode_warna')
            ->first();
    }

    public function getMaterialByIdOrderItemTypeKodeWarna($id_order, $item_type, $kode_warna)
    {
        return $this->where('id_order', $id_order)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->findAll();
    }
    public function orderPerArea($area)
    {
        return $this->select('master_order.no_model, area, material.kode_warna, material.item_type, material.color, sum(kgs) as qty_po')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('area', $area)
            ->groupBy('no_model,material.item_type,material.kode_warna,material.color')
            ->findAll();
    }
    public function MaterialPDK($model)
    {
        return $this->select('master_order.no_model, area, material.kode_warna, material.item_type, material.color, sum(kgs) as qty_po, master_material.jenis')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_order.no_model', $model)
            ->groupBy('no_model,material.item_type,material.kode_warna,material.color')
            ->findAll();
    }
    public function getArea()
    {
        return $this->select('area')
            ->distinct()
            ->findAll();
    }
    public function updateAreaPerNoModel($id_order, $area)
    {
        return $this->where('id_order', $id_order)
            ->set(['area' => $area])
            ->update();
    }
    public function MaterialPerOrder($model)
    {
        return $this->select('master_order.no_model,id_material, area, kode_warna, item_type, color, sum(kgs) as qty_po')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_order.no_model', $model)
            ->groupBy('no_model,item_type,kode_warna,color')
            ->findAll();
    }
    public function getDataArea()
    {
        $query = $this->distinct()
            ->select('area')
            ->orderBy('area', 'ASC')
            ->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'area' saja
        $uniqueArea = array_column($query, 'area');
        return $uniqueArea;
    }
    public function getMU($model, $styleSize)
    {
        return $this->select('master_material.jenis, material.*')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->join('master_material', 'master_material.item_type=material.item_type')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $styleSize)
            ->groupBy('material.item_type')
            ->groupBy('material.kode_warna')
            ->orderBy('master_material.jenis, material.item_type', 'ASC')
            ->findAll();
    }
    public function getGw($model, $styleSize)
    {
        return $this->select('material.gw')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $styleSize)
            ->orderBy('material.item_type', 'ASC')
            ->first();
    }
    public function getDataPPHInisial($area, $nomodel)
    {
        return $this->select('master_order.no_model, material.area, material.inisial, material.style_size, material.item_type, material.color, material.kode_warna, material.composition, material.gw, material.qty_pcs, material.loss, material.kgs')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->where('material.area', $area)
            ->where('master_order.no_model', $nomodel)
            ->orderBy('master_order.no_model, material.inisial, material.style_size, material.item_type, material.kode_warna', 'ASC')
            ->findAll();
    }

    public function getMaterialForPPHByNoModel($area, $searchNoModel = null)
    {
        return $this->select('material.id_order, master_order.no_model, material.area, master_order.delivery_awal, material.style_size, material.item_type,material.color, material.kode_warna, material.composition, material.gw,material.qty_pcs, material.loss, SUM(material.kgs) AS ttl_kebutuhan')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.area', $area)
            ->like('master_order.no_model', $searchNoModel)
            ->groupBy('material.style_size, material.item_type, material.kode_warna')
            ->findAll();
    }

    public function getMaterialForPPH($no_model = null)
    {
        $builder = $this->select('
            material.id_order, 
            master_order.no_model, 
            material.area, 
            master_order.delivery_awal, 
            material.style_size, 
            material.item_type, 
            material.color, 
            material.kode_warna, 
            material.composition, 
            material.gw, 
            material.qty_pcs, 
            material.loss, 
            SUM(material.kgs) AS ttl_kebutuhan
        ')
            ->join('master_order', 'master_order.id_order = material.id_order');

        // Tambahkan filter untuk no_model jika ada
        if (!empty($no_model)) {
            $builder->where('master_order.no_model', $no_model);
        }

        // Pastikan semua kolom yang tidak menggunakan agregasi masuk dalam groupBy
        $builder->groupBy('
        material.style_size, 
        material.item_type, 
        material.kode_warna,
        material.composition
    ');

        return $builder->findAll();
    }
    public function getMaterialForPemesanan($model, $styleSize, $area)
    {
        return $this->select('master_material.jenis, material.*')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->join('master_material', 'master_material.item_type=material.item_type')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $styleSize)
            ->where('material.area', $area)
            ->orderBy('master_material.jenis, material.item_type', 'ASC')
            ->findAll();
    }
    public function assignAreal($idOrder, $area)
    {
        return $this->set('area', $area)
            ->where('id_order', $idOrder)
            ->update();
    }
    public function getStyleSizeByBb($noModel, $itemType, $kodeWarna)
    {
        return $this->select('master_order.no_model, material.item_type, material.kode_warna, material.style_size, material.gw, material.composition, material.loss')
            ->join('master_order', 'master_order.id_order=material.id_order', 'left')
            ->where('master_order.no_model', $noModel)
            ->where('material.item_type', $itemType)
            ->where('material.kode_warna', $kodeWarna)
            ->groupBy('material.style_size')
            ->findAll();
    }

    public function getNoModel($noModelOld, $kodeWarna)
    {
        return $this->select('material.item_type, material.kode_warna, master_order.no_model, material.color')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model !=', $noModelOld)
            ->where('material.kode_warna', $kodeWarna)
            ->groupBy('material.item_type, material.kode_warna, master_order.no_model')
            ->findAll();
    }
    public function MaterialPerStyle($model, $style)
    {
        return $this->select('master_order.no_model, area, kode_warna, item_type, color, sum(kgs) as qty_po')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $style)
            ->groupBy('no_model,item_type,kode_warna,color')
            ->findAll();
    }
    public function materialCek($id)
    {
        return $this->select('master_order.no_model, area, kode_warna, item_type, color')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('material.id_material', $id)
            ->first();
    }
    public function getStyle($id)
    {
        return $this->select('style_size, inisial')->where('id_order', $id)->groupBy('style_size')->findAll();
    }
    public function getDataDuplicate()
    {
        $db = \Config\Database::connect();

        // Subquery: cari kombinasi duplikat
        $subquery = $db->table('material')
            ->select('CONCAT(id_order, style_size, item_type, kode_warna, kgs) as combo')
            ->groupBy('combo')
            ->having('COUNT(*) >', 1)
            ->getCompiledSelect();

        return $db->table('material')
            ->select('id_order')
            ->where("CONCAT(id_order, style_size, item_type, kode_warna, kgs) IN ($subquery)", null, false)
            ->groupBy('id_order')
            ->get()
            ->getResult();
    }
    public function deleteDuplicate($id_order)
    {
        $db = \Config\Database::connect();

        $sql = "
        DELETE m1 FROM material m1
        JOIN material m2
          ON m1.id_order = m2.id_order
          AND m1.style_size = m2.style_size
          AND m1.item_type = m2.item_type
          AND m1.kode_warna = m2.kode_warna
          AND m1.kgs = m2.kgs
          AND m1.id_material > m2.id_material
        WHERE m1.id_order = ?
    ";

        return $db->query($sql, [$id_order]);
    }
    public function getIdMaterial(array $validate)
    {
        $row = $this->select('material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $validate['no_model'])
            ->where('material.area', $validate['area'])
            ->where('material.item_type', $validate['item_type'])
            ->where('material.kode_warna', $validate['kode_warna'])
            ->where('material.style_size', $validate['style_size'])
            ->first();    // ambil satu baris

        return $row ? $row['id_material'] : null;
    }
    public function getId($validate)
    {
        return $this->select('material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $validate['no_model'])
            ->where('material.area', $validate['area'])
            ->where('material.item_type', $validate['item_type'])
            ->where('material.kode_warna', $validate['kode_warna'])
            ->findAll();
    }

    public function getStyleSizeAndInisial($id_order)
    {
        return $this->select('material.id_order, material.style_size, material.inisial')
            ->where('material.id_order', $id_order)
            ->groupBy('style_size, inisial')
            ->findAll();
    }

    public function getItemTypeByBuyer($buyer)
    {
        return $this->select('material.item_type')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.buyer', $buyer)
            ->groupBy('material.item_type')
            ->orderBy('material.item_type')
            ->findAll();
    }

    public function getKodeWarnaByBuyerAndItemType($buyer, $itemType)
    {
        return $this->select('material.kode_warna')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.buyer', $buyer)
            ->where('material.item_type', $itemType)
            ->groupBy('material.kode_warna')
            ->orderBy('material.kode_warna')
            ->findAll();
    }

    public function getWarnaByBuyerItemTypeAndKodeWarna($buyer, $itemType, $kodeWarna)
    {
        return $this->select('material.color')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.buyer', $buyer)
            ->where('material.item_type', $itemType)
            ->where('material.kode_warna', $kodeWarna)
            ->groupBy('material.color')
            ->orderBy('material.color')
            ->first();
    }

    public function getFilterSisaDatangBenang($bulan = null, $noModel = null, $kodeWarna = null)
    {
        // Subquery Material
        $material = $this->db->table('material')
            ->select(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area', 'SUM(material.kgs) AS kg_po'])
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->groupBy(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area']);

        // Subquery Stock
        $stock = $this->db->table('stock')->select(['no_model', 'item_type', 'kode_warna', 'lot_awal', 'SUM(kgs_stock_awal) AS kgs_stock_awal'])
            ->groupBy(['no_model', 'item_type', 'kode_warna', 'lot_awal']);

        // Subquery Retur (Complain)
        $retur = $this->db->table('retur')->select(['no_model', 'item_type', 'kode_warna', 'SUM(kgs_retur) AS qty_retur'])
            ->where('area_retur', 'GUDANG BENANG')
            ->groupBy(['no_model', 'item_type', 'kode_warna']);

        // Subquery Datang
        $datang = $this->db->table('pemasukan p')->select([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
            'SUM(CASE WHEN oc.ganti_retur = "0" THEN oc.kgs_kirim ELSE 0 END) AS kgs_datang',
            'SUM(CASE WHEN oc.ganti_retur = "1" THEN oc.kgs_kirim ELSE 0 END) AS kgs_ganti_retur'
        ])
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup');
        $datang->groupBy([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
        ]);

        // Main Query
        $builder = $this->db->table('(' . $material->getCompiledSelect(false) . ') AS m')
            ->join('master_order AS mo', 'mo.id_order = m.id_order')
            ->join('master_material AS mm', 'mm.item_type = m.item_type')
            ->join(
                '(' . $stock->getCompiledSelect(false)  . ') AS s',
                's.no_model = m.no_model AND s.item_type = m.item_type AND s.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $retur->getCompiledSelect(false)  . ') AS r',
                'r.no_model = m.no_model AND r.item_type = m.item_type AND r.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $datang->getCompiledSelect(false)  . ') AS d',
                'd.no_model = m.no_model AND d.item_type = m.item_type AND d.kode_warna = m.kode_warna',
                'left'
            )
            ->select(['mo.no_model', 'mo.lco_date', 'mo.foll_up', 'mo.no_order', 'm.area', 'mo.delivery_awal', 'mo.delivery_akhir', 'mo.unit', 'm.kg_po', 'm.item_type', 'm.kode_warna', 'm.color', 'mo.buyer', 'mm.jenis', 's.kgs_stock_awal', 's.lot_awal', 'r.qty_retur', 'd.kgs_datang', 'd.kgs_ganti_retur'])
            ->where('mm.jenis', 'BENANG');

        // Filters
        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('m.kode_warna', $kodeWarna);
        }
        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        // Final grouping and ordering
        $builder
            ->groupBy(['m.no_model', 'm.item_type', 'm.kode_warna', 'm.area'])
            ->orderBy('mo.no_model', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaDatangNylon($bulan = null, $noModel = null, $kodeWarna = null)
    {
        // Subquery Material
        $material = $this->db->table('material')
            ->select(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area', 'SUM(material.kgs) AS kg_po'])
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->groupBy(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area']);

        // Subquery Stock
        $stock = $this->db->table('stock')->select(['no_model', 'item_type', 'kode_warna', 'lot_awal', 'SUM(kgs_stock_awal) AS kgs_stock_awal'])
            ->groupBy(['no_model', 'item_type', 'kode_warna', 'lot_awal']);

        // Subquery Retur (Complain)
        $retur = $this->db->table('retur')->select(['no_model', 'item_type', 'kode_warna', 'SUM(kgs_retur) AS qty_retur'])
            ->where('area_retur', 'GUDANG BENANG')
            ->groupBy(['no_model', 'item_type', 'kode_warna']);

        // Subquery Datang
        $datang = $this->db->table('pemasukan p')->select([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
            'SUM(CASE WHEN oc.ganti_retur = "0" THEN oc.kgs_kirim ELSE 0 END) AS kgs_datang',
            'SUM(CASE WHEN oc.ganti_retur = "1" THEN oc.kgs_kirim ELSE 0 END) AS kgs_ganti_retur'
        ])
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup');
        $datang->groupBy([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
        ]);

        // Main Query
        $builder = $this->db->table('(' . $material->getCompiledSelect(false) . ') AS m')
            ->join('master_order AS mo', 'mo.id_order = m.id_order')
            ->join('master_material AS mm', 'mm.item_type = m.item_type')
            ->join(
                '(' . $stock->getCompiledSelect(false)  . ') AS s',
                's.no_model = m.no_model AND s.item_type = m.item_type AND s.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $retur->getCompiledSelect(false)  . ') AS r',
                'r.no_model = m.no_model AND r.item_type = m.item_type AND r.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $datang->getCompiledSelect(false)  . ') AS d',
                'd.no_model = m.no_model AND d.item_type = m.item_type AND d.kode_warna = m.kode_warna',
                'left'
            )
            ->select(['mo.no_model', 'mo.lco_date', 'mo.foll_up', 'mo.no_order', 'm.area', 'mo.delivery_awal', 'mo.delivery_akhir', 'mo.unit', 'm.kg_po', 'm.item_type', 'm.kode_warna', 'm.color', 'mo.buyer', 'mm.jenis', 's.kgs_stock_awal', 's.lot_awal', 'r.qty_retur', 'd.kgs_datang', 'd.kgs_ganti_retur'])
            ->where('mm.jenis', 'NYLON');

        // Filters
        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('m.kode_warna', $kodeWarna);
        }
        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        // Final grouping and ordering
        $builder
            ->groupBy(['m.no_model', 'm.item_type', 'm.kode_warna', 'm.area'])
            ->orderBy('mo.no_model', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaDatangSpandex($bulan = null, $noModel = null, $kodeWarna = null)
    {
        // Subquery Material
        $material = $this->db->table('material')
            ->select(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area', 'SUM(material.kgs) AS kg_po'])
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->groupBy(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area']);

        // Subquery Stock
        $stock = $this->db->table('stock')->select(['no_model', 'item_type', 'kode_warna', 'lot_awal', 'SUM(kgs_stock_awal) AS kgs_stock_awal'])
            ->groupBy(['no_model', 'item_type', 'kode_warna', 'lot_awal']);

        // Subquery Retur (Complain)
        $retur = $this->db->table('retur')->select(['no_model', 'item_type', 'kode_warna', 'SUM(kgs_retur) AS qty_retur'])
            ->where('area_retur', 'GUDANG BENANG')
            ->groupBy(['no_model', 'item_type', 'kode_warna']);

        // Subquery Datang
        $datang = $this->db->table('pemasukan p')->select([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
            'SUM(CASE WHEN oc.ganti_retur = "0" THEN oc.kgs_kirim ELSE 0 END) AS kgs_datang',
            'SUM(CASE WHEN oc.ganti_retur = "1" THEN oc.kgs_kirim ELSE 0 END) AS kgs_ganti_retur'
        ])
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup');
        $datang->groupBy([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
        ]);

        // Main Query
        $builder = $this->db->table('(' . $material->getCompiledSelect(false) . ') AS m')
            ->join('master_order AS mo', 'mo.id_order = m.id_order')
            ->join('master_material AS mm', 'mm.item_type = m.item_type')
            ->join(
                '(' . $stock->getCompiledSelect(false)  . ') AS s',
                's.no_model = m.no_model AND s.item_type = m.item_type AND s.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $retur->getCompiledSelect(false)  . ') AS r',
                'r.no_model = m.no_model AND r.item_type = m.item_type AND r.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $datang->getCompiledSelect(false)  . ') AS d',
                'd.no_model = m.no_model AND d.item_type = m.item_type AND d.kode_warna = m.kode_warna',
                'left'
            )
            ->select(['mo.no_model', 'mo.lco_date', 'mo.foll_up', 'mo.no_order', 'm.area', 'mo.delivery_awal', 'mo.delivery_akhir', 'mo.unit', 'm.kg_po', 'm.item_type', 'm.kode_warna', 'm.color', 'mo.buyer', 'mm.jenis', 's.kgs_stock_awal', 's.lot_awal', 'r.qty_retur', 'd.kgs_datang', 'd.kgs_ganti_retur'])
            ->where('mm.jenis', 'SPANDEX');

        // Filters
        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('m.kode_warna', $kodeWarna);
        }
        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        // Final grouping and ordering
        $builder
            ->groupBy(['m.no_model', 'm.item_type', 'm.kode_warna', 'm.area'])
            ->orderBy('mo.no_model', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getFilterSisaDatangKaret($bulan = null, $noModel = null, $kodeWarna = null)
    {
        // Subquery Material
        $material = $this->db->table('material')
            ->select(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area', 'SUM(material.kgs) AS kg_po'])
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->groupBy(['material.id_order', 'master_order.no_model', 'material.item_type', 'material.kode_warna', 'material.color', 'material.area']);

        // Subquery Stock
        $stock = $this->db->table('stock')->select(['no_model', 'item_type', 'kode_warna', 'lot_awal', 'SUM(kgs_stock_awal) AS kgs_stock_awal'])
            ->groupBy(['no_model', 'item_type', 'kode_warna', 'lot_awal']);

        // Subquery Retur (Complain)
        $retur = $this->db->table('retur')->select(['no_model', 'item_type', 'kode_warna', 'SUM(kgs_retur) AS qty_retur'])
            ->where('area_retur', 'GUDANG BENANG')
            ->groupBy(['no_model', 'item_type', 'kode_warna']);

        // Subquery Datang
        $datang = $this->db->table('pemasukan p')->select([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
            'SUM(CASE WHEN oc.ganti_retur = "0" THEN oc.kgs_kirim ELSE 0 END) AS kgs_datang',
            'SUM(CASE WHEN oc.ganti_retur = "1" THEN oc.kgs_kirim ELSE 0 END) AS kgs_ganti_retur'
        ])
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup');
        $datang->groupBy([
            'oc.no_model',
            'sc.item_type',
            'sc.kode_warna',
        ]);

        // Main Query
        $builder = $this->db->table('(' . $material->getCompiledSelect(false) . ') AS m')
            ->join('master_order AS mo', 'mo.id_order = m.id_order')
            ->join('master_material AS mm', 'mm.item_type = m.item_type')
            ->join(
                '(' . $stock->getCompiledSelect(false)  . ') AS s',
                's.no_model = m.no_model AND s.item_type = m.item_type AND s.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $retur->getCompiledSelect(false)  . ') AS r',
                'r.no_model = m.no_model AND r.item_type = m.item_type AND r.kode_warna = m.kode_warna',
                'left'
            )
            ->join(
                '(' . $datang->getCompiledSelect(false)  . ') AS d',
                'd.no_model = m.no_model AND d.item_type = m.item_type AND d.kode_warna = m.kode_warna',
                'left'
            )
            ->select(['mo.no_model', 'mo.lco_date', 'mo.foll_up', 'mo.no_order', 'm.area', 'mo.delivery_awal', 'mo.delivery_akhir', 'mo.unit', 'm.kg_po', 'm.item_type', 'm.kode_warna', 'm.color', 'mo.buyer', 'mm.jenis', 's.kgs_stock_awal', 's.lot_awal', 'r.qty_retur', 'd.kgs_datang', 'd.kgs_ganti_retur'])
            ->where('mm.jenis', 'KARET');

        // Filters
        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }
        if (!empty($kodeWarna)) {
            $builder->where('m.kode_warna', $kodeWarna);
        }
        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        // Final grouping and ordering
        $builder
            ->groupBy(['m.no_model', 'm.item_type', 'm.kode_warna', 'm.area'])
            ->orderBy('mo.no_model', 'ASC');

        return $builder->get()->getResultArray();
    }
}
