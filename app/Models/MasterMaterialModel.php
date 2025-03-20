<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterMaterialModel extends Model
{
    protected $table            = 'master_material';
    protected $primaryKey       = 'item_type';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['item_type', 'deskripsi', 'jenis', 'ukuran'];


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

    public function checkItemType($item_type)
    {
        return $this->where('item_type', $item_type)->findAll();
    }

    public function getJenisBahanBaku()
    {
        return $this->select('jenis')->distinct()->findAll();
    }

    public function getItemtype()
    {
        return $this->select('item_type')->findAll();
    }

    public function getItemTypeByJenis($jenis)
    {
        return $this->select('item_type')->where('jenis', $jenis)->findAll();
    }

    public function getJenisByitemType($item_type)
    {
        return $this->select('jenis')->where('item_type', $item_type)->findAll();
    }

    public function updateMasterMaterial($id, $data)
    {
        return $this->where('item_type', $id)->set($data)->update();
    }

    public function getJenis()
    {
        $query = $this->distinct()
            ->select('jenis')
            ->orderBy('jenis', 'ASC')
            ->findAll();

        // Mengubah hasil query menjadi array dengan nilai 'area' saja
        $uniqueArea = array_column($query, 'jenis');
        return $uniqueArea;
    }

    public function getJenisSpandexKaret()
    {
        $query = $this->distinct()
            ->select('jenis')
            ->whereIn('jenis', ['Spandex', 'Karet']) // Hanya mengambil jenis yang sesuai
            ->orderBy('jenis', 'ASC')
            ->findAll();

        $uniqueArea = array_column($query, 'jenis');
        return $uniqueArea;
    }
}
