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
        return $this->select('master_order.no_model, area, kode_warna, item_type, color, sum(kgs) as qty_po')
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
    public function getMU($model, $styleSize, $area)
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

    public function getMaterialForPPH($area, $no_model = null)
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
            ->where('material.area', $area);
        // ->where('material.style_size', 'J401514-17 18X6');

        // Tambahkan filter untuk no_model jika ada
        if (!empty($no_model)) {
            $builder->where('master_order.no_model', $no_model);
        }

        // Pastikan semua kolom yang tidak menggunakan agregasi masuk dalam groupBy
        $builder->groupBy('
        material.style_size, 
        material.item_type, 
        material.kode_warna
    ');

        return $builder->findAll();
    }
}
