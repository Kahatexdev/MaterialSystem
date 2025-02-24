<?php

namespace App\Models;

use CodeIgniter\Model;

class PemasukanModel extends Model
{
    protected $table            = 'pemasukan';
    protected $primaryKey       = 'id_pemasukan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pemasukan',
        'id_out_celup',
        'id_retur',
        'no_model',
        'item_type',
        'kode_warna',
        'warna',
        'kgs_masuk',
        'cns_masuk',
        'tgl_masuk',
        'nama_cluster',
        'history_order',
        'history_jalur',
        'out_jalur',
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

    public function getDataForOut($id)
    {
        return $this->db->table('pemasukan')
            ->select('pemasukan.*, out_celup.lot_kirim')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('pemasukan.id_out_celup', $id)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getItemTypeByModel($pdk)
    {
        return $this->select('item_type')
            ->where('no_model', $pdk)
            ->groupBy('item_type')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKodeWarnaByItemType($no_model, $item_type)
    {
        return $this->select('kode_warna')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->groupBy('kode_warna')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getWarnaByKodeWarna($no_model, $item_type, $kode_warna)
    {
        $result = $this->select('warna')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('warna')
            ->distinct()
            ->get()
            ->getRowArray(); // Ambil satu baris saja

        return $result ? $result['warna'] : null; // Pastikan hanya warna yang dikembalikan
    }

    public function getLotByKodeWarna($no_model, $item_type, $kode_warna)
    {
        return $this->select('out_celup.lot_kirim')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('pemasukan.no_model', $no_model)
            ->where('pemasukan.item_type', $item_type)
            ->where('pemasukan.kode_warna', $kode_warna)
            ->groupBy('out_celup.lot_kirim')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKgsConesClusterForOut($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
        $query = $this->db->table('pemasukan p')
            ->select('oc.id_out_celup, p.kgs_masuk, p.cns_masuk, p.nama_cluster')
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup')
            ->where('p.no_model', $no_model)
            ->where('p.item_type', $item_type)
            ->where('p.kode_warna', $kode_warna)
            ->where('oc.lot_kirim', $lot_kirim)
            ->where('oc.no_karung', $no_karung)
            ->get();

        $sql = $this->db->getLastQuery(); // Debugging query
        log_message('error', 'Query getKgsDanCones: ' . $sql); // Log ke CI4 logs

        return $query->getRowArray(); // Pastikan return berbentuk array
    }

    public function getDataForPengiriman($id)
    {
        return $this->db->table('pemasukan')
            ->select('pemasukan.*, out_celup.lot_kirim')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('pemasukan.id_out_celup', $id)
            ->where('pemasukan.out_jalur', '1')
            ->distinct()
            ->get()
            ->getResultArray();
    }
    public function stockInOut($no_model, $item_type, $kode_warna)
    {
        $inout = $this->select('no_model, item_type, kode_warna, 
        SUM(kgs_masuk) AS masuk, 
        SUM(pengeluaran.kgs_out) AS keluar')
            ->join('pengeluaran', 'pengeluaran.id_out_celup = pemasukan.id_out_celup', 'left')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('kode_warna')
            ->first(); // Ambil satu row, bukan array of array

        return $inout ?? ['masuk' => 0, 'keluar' => 0]; // Jika NULL, default ke array kosong
    }
}
