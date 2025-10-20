<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table            = 'material';
    protected $primaryKey       = 'id_material';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_material', 'id_order', 'style_size', 'area', 'inisial', 'color', 'item_type', 'kode_warna', 'composition', 'gw', 'gw_aktual', 'qty_pcs', 'loss', 'kgs', 'keterangan', 'material_type', 'admin', 'created_at', 'updated_at', 'deleted_at'];

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

    public function getMaterial($id_order)
    {
        return $this->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.id_order', $id_order)
            ->where('material.deleted_at', null)
            ->findAll();
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

    public function MaterialPDK($model = null, $search = null)
    {
        $builder = $this->select('
            master_order.no_model,
            area,
            material.kode_warna,
            material.item_type,
            material.color,
            SUM(kgs) AS qty_po,
            master_material.jenis
        ')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left');

        if (!empty($model)) {
            $builder->like('master_order.no_model', $model);
        }

        if (!empty($search)) {
            $builder->like('material.kode_warna', $search);
        }

        $builder->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color')->orderBy('  master_material.jenis');

        return $builder->findAll();
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
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('material.gw >', 0);

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
        return $this->select('master_material.jenis, material.*, IFNULL(kebutuhan_cones.qty_cns, 0) AS qty_cns, IFNULL(kebutuhan_cones.qty_berat_cns, 0) AS qty_berat_cns')
            ->join('(SELECT id_material, qty_cns, qty_berat_cns FROM kebutuhan_cones WHERE area="' . $area . '") AS kebutuhan_cones', 'material.id_material=kebutuhan_cones.id_material', 'left')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->join('master_material', 'master_material.item_type=material.item_type')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $styleSize)
            // ->where('material.area', $area)
            ->orderBy('master_material.jenis, material.item_type', 'ASC')
            ->findAll();
    }
    public function assignAreal($idOrder, $area)
    {
        return $this->set('area', $area)
            ->where('id_order', $idOrder)
            ->update();
    }
    public function getStyleSizeByBb($noModel, $itemType, $kodeWarna, $warna = null)
    {
        $builder = $this->select('
            master_order.no_model, 
            material.item_type, 
            material.kode_warna, 
            material.style_size, 
            material.kgs, 
            material.gw, 
            material.composition, 
            material.loss, 
            SUM(material.kgs) AS kgs
        ')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_order.no_model', $noModel)
            ->where('material.item_type', $itemType)
            ->where('material.kode_warna', $kodeWarna);
        // jika item_type bukan JHT, tambahkan kondisi composition > 0
        if (stripos($itemType, 'JHT') === false) {
            $builder->where('material.composition >', 0);
        }
        if ($warna !== null) {
            $builder->where('material.color', $warna);
        }

        return $builder->groupBy('material.style_size')->findAll();
    }

    public function getNoModel($noModelOld, $kodeWarna, $term = null)
    {
        $builder =  $this->select('material.item_type, material.kode_warna, master_order.no_model, material.color')
            ->join('master_order', 'master_order.id_order = material.id_order')
            // ->where('master_order.no_model !=', $noModelOld)
            ->where('material.color IS NOT NULL')
            // ->where('material.kode_warna', $kodeWarna)
            ->groupBy('material.item_type, material.kode_warna, master_order.no_model');

        if ($term) {
            $builder->groupStart()
                ->like('master_order.no_model',   $term)
                ->orLike('material.kode_warna', $term)
                ->groupEnd();
        }
        $builder->groupBy([
            'material.item_type',
            'material.kode_warna',
            'master_order.no_model',
            'material.color',
        ]);

        return $builder->get()->getResultArray();
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
            'COALESCE(sc.item_type, ot.item_type) AS item_type',
            'COALESCE(sc.kode_warna, ot.kode_warna) AS kode_warna',
            'SUM(CASE WHEN oc.ganti_retur = "0" THEN oc.kgs_kirim ELSE 0 END) AS kgs_datang',
            'SUM(CASE WHEN oc.ganti_retur = "1" THEN oc.kgs_kirim ELSE 0 END) AS kgs_ganti_retur'
        ])
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup', 'left')
            ->join('other_bon ot', 'oc.id_other_bon = ot.id_other_bon', 'left')
            ->groupBy([
                'oc.no_model',
                'item_type',
                'kode_warna'
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
    public function getMaterialByNoModel($noModel)
    {
        return $this->select('color, item_type, kode_warna, SUM(kgs) as kg')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->where('master_order.no_model', $noModel)
            ->groupBy('item_type, kode_warna, color')
            ->findAll();
    }

    public function getMaterialForPemesananRosso($model, $styleSize, $area)
    {
        return $this->select('master_material.jenis, material.*')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->join('master_material', 'master_material.item_type=material.item_type')
            ->where('master_order.no_model', $model)
            ->where('material.style_size', $styleSize)
            // ->where('material.area', $area)
            ->like('material.item_type', '%JHT%')
            ->orderBy('master_material.jenis, material.item_type', 'ASC')
            ->findAll();
    }

    public function getGWAktual($noModel, $styleSize)
    {
        return $this->select('material.gw_aktual')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->where('master_order.no_model', $noModel)
            ->where('material.style_size', $styleSize)
            ->first();
    }

    public function getMaterialID($noModel, $styleSize)
    {
        return $this->select('material.id_material')
            ->join('master_order', 'master_order.id_order=material.id_order')
            ->where('master_order.no_model', $noModel)
            ->where('material.style_size', $styleSize)
            ->findAll();
    }

    public function updateGwAktual($idMaterial, $gwAktual)
    {
        return $this->set('gw_aktual', $gwAktual)
            ->whereIn('id_material', $idMaterial)
            ->update();
    }

    // public function getFilterSisaPakai($jenis, $bulan = null, $noModel = null, $kodeWarna = null)
    // {
    //     $builder =  $this->db->table('material m')->select('
    //         mo.no_model, m.item_type, m.kode_warna, m.color, s.kgs_stock_awal, s.lot_awal,
    //         mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
    //         p.area_out, p.kgs_out,
    //         op.kg_po,
    //         r.kgs_retur, r.lot_retur,
    //         mm.jenis
    //     ')
    //         ->join('master_order mo', 'mo.id_order = m.id_order')
    //         ->join('stock s', 'mo.no_model = s.no_model')
    //         ->join('master_material mm', 'mm.item_type = m.item_type')
    //         ->join('pengeluaran p', 'p.id_stock = s.id_stock AND p.lot_out = s.lot_stock AND p.nama_cluster = s.nama_cluster', 'left')
    //         ->join('open_po op', 'op.no_model = mo.no_model AND op.item_type = m.item_type AND op.kode_warna = m.kode_warna', 'left')
    //         ->join('retur r', 'r.no_model = mo.no_model AND r.item_type = m.item_type AND r.kode_warna = m.kode_warna', 'left')
    //         ->where('mm.jenis', $jenis)
    //         ->groupBy('mo.no_model')
    //         ->groupBy('m.item_type')
    //         ->groupBy('m.kode_warna');

    //     if (!empty($noModel)) {
    //         $builder->where('mo.no_model', $noModel);
    //     }

    //     if (!empty($kodeWarna)) {
    //         $builder->where('m.kode_warna', $kodeWarna);
    //     }

    //     if (!empty($bulan)) {
    //         $builder->where('MONTH(mo.delivery_awal)', $bulan);
    //     }

    //     return $builder->get()->getResultArray();
    // }
    public function getFilterSisaPakai($jenis, $bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->db->table('material m')
            ->select("
            mo.no_model,
            mo.buyer,
            m.item_type,
            m.kode_warna,
            m.color,
            mm.jenis,
            mo.lco_date,
            mo.foll_up,
            mo.no_order,
            mo.delivery_awal,
            mo.delivery_akhir,
            mo.unit,
            mo.start_mc,

            -- kg pesan
            (
                SELECT SUM(mat.kgs)
                FROM material mat
                WHERE mat.id_order = mo.id_order
                AND mat.item_type = m.item_type
                AND mat.kode_warna = m.kode_warna
            ) AS kg_pesan,

            -- stock awal
            (
                SELECT COALESCE(SUM(s.kgs_stock_awal), 0) + COALESCE(SUM(s.kgs_in_out), 0)
                FROM stock s
                WHERE s.no_model = mo.no_model
                AND s.item_type = m.item_type
                AND s.kode_warna = m.kode_warna
            ) AS kgs_stock_awal,

            (
                SELECT GROUP_CONCAT(DISTINCT s.lot_stock)
                FROM stock s
                WHERE s.no_model = mo.no_model
                AND s.item_type = m.item_type
                AND s.kode_warna = m.kode_warna
            ) AS lot_awal,

            -- pengeluaran (pakai benang)
            (
                SELECT SUM(COALESCE(p.kgs_out, 0))
                FROM pengeluaran p
                JOIN pemesanan pem ON pem.id_total_pemesanan = p.id_total_pemesanan
                JOIN material mat ON mat.id_material = pem.id_material
                JOIN master_order mo2 ON mo2.id_order = mat.id_order
                WHERE mo2.no_model = mo.no_model
                AND mat.item_type = m.item_type
                AND mat.kode_warna = m.kode_warna
                AND p.status = 'Pengiriman Area'
            ) AS kgs_out,

            -- pengeluaran (pakai (+) benang & nylon)
            (
                SELECT SUM(COALESCE(p.kgs_out, 0))
                FROM pengeluaran p
                JOIN stock s2 ON s2.id_stock = p.id_stock
                JOIN pemesanan pem ON pem.id_total_pemesanan = p.id_total_pemesanan
                WHERE s2.no_model = mo.no_model
                AND s2.item_type = m.item_type
                AND s2.kode_warna = m.kode_warna
                AND pem.po_tambahan = '1'
                AND p.status = 'Pengiriman Area'
            ) AS kgs_out_plus,

            -- Pengeluaran (Pakai Spandex & Karet)
            (
                SELECT SUM(COALESCE(p.kgs_out, 0))
                FROM pengeluaran p
                JOIN pemesanan pem ON pem.id_total_pemesanan = p.id_total_pemesanan
                JOIN material mat ON mat.id_material = pem.id_material
                JOIN master_order mo2 ON mo2.id_order = mat.id_order
                WHERE mo2.no_model = mo.no_model
                AND mat.item_type = m.item_type
                AND mat.kode_warna = m.kode_warna
                AND p.id_psk IS NOT NULL
                AND pem.po_tambahan = '0'
            ) AS kgs_out_spandex_karet,

            -- Pengeluaran (Pakai (+) Spandex & Karet)
            (
                SELECT SUM(COALESCE(p.kgs_out, 0))
                FROM pengeluaran p
                JOIN pemesanan pem ON pem.id_total_pemesanan = p.id_total_pemesanan
                JOIN material mat ON mat.id_material = pem.id_material
                JOIN master_order mo2 ON mo2.id_order = mat.id_order
                WHERE mo2.no_model = mo.no_model
                AND mat.item_type = m.item_type
                AND mat.kode_warna = m.kode_warna
                AND p.id_psk IS NOT NULL
                AND pem.po_tambahan = '1'
            ) AS kgs_out_spandex_karet_plus,

            -- other_out (pakai_selain_order)
            (
                SELECT SUM(COALESCE(oo.kgs_other_out, 0))
                FROM other_out oo
                JOIN stock s3 ON s3.lot_stock = oo.lot_other_out AND s3.nama_cluster = oo.nama_cluster
                WHERE s3.no_model = mo.no_model
                AND s3.item_type = m.item_type
                AND s3.kode_warna = m.kode_warna
            ) AS kgs_other_out,

            -- pengeluaran (area)
            (
                SELECT p.area_out
                FROM pengeluaran p
                JOIN stock s2 ON s2.id_stock = p.id_stock
                WHERE s2.no_model = mo.no_model
                AND s2.item_type = m.item_type
                AND s2.kode_warna = m.kode_warna
                LIMIT 1
            ) AS area_out,

            (
                SELECT GROUP_CONCAT(DISTINCT p.lot_out)
                FROM pengeluaran p
                JOIN stock s2 ON s2.id_stock = p.id_stock
                WHERE s2.no_model = mo.no_model
                AND s2.item_type = m.item_type
                AND s2.kode_warna = m.kode_warna
            ) AS lot_out,

            -- open po
            (
                SELECT SUM(COALESCE(op.kg_po, 0))
                FROM open_po op
                WHERE op.no_model = mo.no_model
                AND op.item_type = m.item_type
                AND op.kode_warna = m.kode_warna
            ) AS kg_po,

            -- retur
            (
                SELECT SUM(COALESCE(r.kgs_retur, 0))
                FROM retur r
                WHERE r.no_model = mo.no_model
                AND r.item_type = m.item_type
                AND r.kode_warna = m.kode_warna
            ) AS kgs_retur,

            (
                SELECT GROUP_CONCAT(DISTINCT r.lot_retur)
                FROM retur r
                WHERE r.no_model = mo.no_model
                AND r.item_type = m.item_type
                AND r.kode_warna = m.kode_warna
            ) AS lot_retur,

            -- po tambahan
            (
                SELECT pp.tanggal_approve
                FROM po_tambahan pp
                JOIN material m2 ON m2.id_material = pp.id_material
                WHERE m2.id_order = mo.id_order
                AND m2.item_type = m.item_type
                AND m2.kode_warna = m.kode_warna
                GROUP BY m2.id_order, m2.item_type, m2.kode_warna
            ) AS tgl_terima_po_plus,

            (
                SELECT DATE(pp.created_at)
                FROM po_tambahan pp
                JOIN material m2 ON m2.id_material = pp.id_material
                WHERE m2.id_order = mo.id_order
                AND m2.item_type = m.item_type
                AND m2.kode_warna = m.kode_warna
                GROUP BY m2.id_order, m2.item_type, m2.kode_warna
            ) AS tgl_po_plus_area,

            (
                SELECT SUM(ttl_tambahan_kg)
                FROM total_potambahan tp
                JOIN po_tambahan pp ON pp.id_total_potambahan = tp.id_total_potambahan
                JOIN material m2 ON m2.id_material = pp.id_material
                JOIN master_order mo2 ON mo2.id_order = m2.id_order
                WHERE m2.id_order = mo.id_order
                AND m2.item_type = m.item_type
                AND m2.kode_warna = m.kode_warna
                AND pp.status LIKE '%approved%'
                GROUP BY m2.id_material
            ) AS kg_po_plus,

            (
                SELECT pp.delivery_po_plus
                FROM po_tambahan pp
                JOIN material m2 ON m2.id_material = pp.id_material
                WHERE m2.id_order = mo.id_order
                AND m2.item_type = m.item_type
                AND m2.kode_warna = m.kode_warna
                GROUP BY m2.id_order, m2.item_type, m2.kode_warna
            ) AS delivery_po_plus
        ")
            ->join('master_material mm', 'm.item_type = mm.item_type', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->where('mm.jenis', $jenis);

        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('m.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }
        return $builder
            ->groupBy('mo.no_model')
            ->groupBy('m.item_type')
            ->groupBy('m.kode_warna')
            ->orderBy('m.item_type, m.kode_warna', 'ASC')
            ->get()->getResultArray();
    }

    // public function getFilterPoBenang($key)
    // {
    //     $this->select('master_order.no_model, master_order.foll_up, master_order.lco_date, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.memo, master_order.unit, master_material.jenis, material.area, material.item_type, material.kode_warna, material.color, SUM(material.kgs) AS kg_po, material.created_at AS tgl_input, material.admin, SUM(COALESCE(stock.kgs_stock_awal,0) + COALESCE(stock.kgs_in_out,0)) AS kgs_stock, GROUP_CONCAT(DISTINCT COALESCE(stock.lot_stock, stock.lot_awal) SEPARATOR ', ') AS lot_stock')
    //         ->join('master_order', 'master_order.id_order = material.id_order', 'left')
    //         ->join('master_material', 'master_material.item_type = material.item_type', 'left')
    //         ->join('stock', 'stock.item_type = material.item_type AND stock.kode_warna = material.kode_warna AND stock.warna = material.color', 'left')
    //         ->where('master_material.jenis', 'BENANG')
    //         ->groupBy('material.item_type, material.kode_warna, material.color');

    //     // Cek apakah ada input key untuk pencarian
    //     if (!empty($key)) {
    //         $this->groupStart()
    //             ->like('master_order.no_model', $key)
    //             ->orLike('material.item_type', $key)
    //             ->orLike('material.kode_warna', $key)
    //             ->orLike('material.color', $key)
    //             ->groupEnd();
    //     }

    //     return $this->findAll();
    // }
    public function getFilterPoBenang($key = null, $jenis = 'BENANG')
    {
        $db = \Config\Database::connect();

        // 1) Subquery KGS (aggregate sekali per kombinasi)
        $stockKgs = $db->table('stock')
            ->select("
            item_type,
            kode_warna,
            warna,
            SUM(COALESCE(kgs_stock_awal,0) + COALESCE(kgs_in_out,0)) AS kgs_stock
        ")
            ->groupBy('item_type, kode_warna, warna')
            ->getCompiledSelect(false);

        // 2) Subqueries untuk LOT dengan filter yang lebih ketat
        $subLot1 = $db->table('stock')
            ->select("item_type, kode_warna, warna, lot_stock AS lot")
            ->where('lot_stock IS NOT NULL')
            ->where('lot_stock !=', '')
            ->where('TRIM(lot_stock) !=', '')
            ->getCompiledSelect(false);

        $subLot2 = $db->table('stock')
            ->select("item_type, kode_warna, warna, lot_awal AS lot")
            ->where('lot_awal IS NOT NULL')
            ->where('lot_awal !=', '')
            ->where('TRIM(lot_awal) !=', '')
            ->getCompiledSelect(false);

        // 3) Gabungkan dengan UNION ALL dan aggregate
        $unionLots = "({$subLot1} UNION ALL {$subLot2})";

        $stockLots = $db->table("({$unionLots}) AS u_lots")
            ->select("
            item_type,
            kode_warna,
            warna,
            GROUP_CONCAT(DISTINCT TRIM(lot) ORDER BY lot SEPARATOR ', ') AS lot_stock
        ")
            ->groupBy('item_type, kode_warna, warna')
            ->getCompiledSelect(false);

        $subPoPlus = $db->table('po_tambahan')
            ->select('
                po_tambahan.id_material, 
                SUM(po_tambahan.poplus_mc_kg + po_tambahan.plus_pck_kg) AS kg_po_plus, 
                po_tambahan.tanggal_approve, 
                DATE(po_tambahan.created_at) AS tgl_po_plus_area, 
                po_tambahan.delivery_po_plus,
                material.item_type,
                material.kode_warna,
                material.color
            ')
            ->join('material', 'material.id_material = po_tambahan.id_material', 'left')
            ->where('po_tambahan.tanggal_approve IS NOT NULL')
            ->where('po_tambahan.status', 'approved')
            ->groupBy('po_tambahan.tanggal_approve, material.item_type, material.kode_warna, material.color')
            ->getCompiledSelect(false);

        // 4) Query utama
        $builder = $db->table('material')
            ->select("
            master_order.no_model,
            master_order.foll_up,
            master_order.lco_date,
            master_order.no_order,
            master_order.buyer,
            master_order.delivery_awal,
            master_order.delivery_akhir,
            master_order.memo,
            master_order.unit,
            master_material.jenis,
            material.area,
            material.item_type,
            material.kode_warna,
            material.color,
            material.loss,
            material.material_type,
            SUM(material.kgs) AS kg_po,
            material.created_at AS tgl_input,
            material.admin,
            COALESCE(stockKgs.kgs_stock, 0) AS kgs_stock,
            COALESCE(lotSub.lot_stock, '-') AS lot_stock,
            COALESCE(plusSub.kg_po_plus, 0) AS kg_po_plus,
            plusSub.tgl_po_plus_area,
            plusSub.delivery_po_plus,
            plusSub.tanggal_approve
        ")
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join(
                "({$stockKgs}) AS stockKgs",
                'stockKgs.item_type = material.item_type 
             AND stockKgs.kode_warna = material.kode_warna 
             AND stockKgs.warna = material.color',
                'left'
            )
            ->join(
                "({$stockLots}) AS lotSub",
                'lotSub.item_type = material.item_type 
             AND lotSub.kode_warna = material.kode_warna 
             AND lotSub.warna = material.color',
                'left'
            )
            ->join(
                "({$subPoPlus}) AS plusSub",
                'plusSub.id_material = material.id_material 
             AND plusSub.item_type = material.item_type 
             AND plusSub.kode_warna = material.kode_warna 
             AND plusSub.color = material.color',
                'left'
            )
            ->where('master_material.jenis', $jenis);

        // Filter pencarian
        if (!empty($key)) {
            $builder->groupStart()
                ->like('master_order.no_model', $key)
                ->orLike('material.item_type', $key)
                ->orLike('material.kode_warna', $key)
                ->orLike('material.color', $key)
                ->groupEnd();
        }

        // Group by yang lebih lengkap
        $builder->groupBy([
            'master_order.no_model',
            'material.item_type',
            'material.kode_warna',
            'material.color'
        ]);

        return $builder->get()->getResultArray();
    }

    public function getFilterReportIndri($buyer = null, $deliveryAwal = null, $deliveryAkhir = null)
    {
        $builder = $this->select('
            master_order.no_model,
            master_order.buyer,
            material.area,
            material.item_type,
            material.kode_warna,
            material.color,
            material.loss,
            SUM(material.kgs) AS kg_po,
         
        ')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left');

        if (!empty($buyer)) {
            $builder->where('master_order.buyer', $buyer);
        }

        if (!empty($deliveryAwal)) {
            $builder->where('master_order.delivery_awal >=', $deliveryAwal);
        }

        if (!empty($deliveryAkhir)) {
            $builder->where('master_order.delivery_akhir <=', $deliveryAkhir);
        }

        return $builder
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color')
            ->findAll();
    }

    public function getItemTypeByIdOrder($idOrder)
    {
        return $this->select('item_type, kode_warna, color')
            ->where('id_order', $idOrder)
            ->groupBy('item_type, kode_warna, color')
            ->findAll();
    }
}
