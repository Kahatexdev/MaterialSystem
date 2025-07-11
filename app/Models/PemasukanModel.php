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
        'tgl_masuk',
        'nama_cluster',
        'out_jalur',
        'admin',
        'created_at',
        'updated_at',
        'id_stock'
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
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('schedule_celup.no_model', $no_model)
            ->where('schedule_celup.item_type', $item_type)
            ->where('schedule_celup.kode_warna', $kode_warna)
            ->groupBy('out_celup.lot_kirim')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKgsConesClusterForOut($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
        $query = $this->db->table('pemasukan p')
            ->select('oc.id_out_celup, oc.kgs_kirim, oc.cones_kirim, p.nama_cluster')
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup')
            ->where('sc.no_model', $no_model)
            ->where('sc.item_type', $item_type)
            ->where('sc.kode_warna', $kode_warna)
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
        SUM(out_celup.kgs_kirim) AS masuk, 
        SUM(pengeluaran.kgs_out) AS keluar')
            ->join('pengeluaran', 'pengeluaran.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->groupBy('kode_warna')
            ->first(); // Ambil satu row, bukan array of array

        return $inout ?? ['masuk' => 0, 'keluar' => 0]; // Jika NULL, default ke array kosong
    }

    public function getTotalKarungMasuk()
    {
        return $this->select('SUM(out_celup.no_karung) as total_karung_masuk')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('DATE(pemasukan.tgl_masuk)', date('Y-m-d')) // Hanya untuk tanggal hari ini
            ->first();
    }
    public function getTotalKarungKeluar()
    {
        return $this->select('SUM(out_celup.no_karung) as total_karung_keluar')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('DATE(pemasukan.tgl_masuk)', date('Y-m-d')) // Hanya untuk tanggal hari ini
            ->where('pemasukan.out_jalur', '1') // Hanya yang sudah keluar
            ->first();
    }

    public function getFilterDatangBenang($key, $tanggal_awal, $tanggal_akhir)
    {
        $this->select('schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, out_celup.kgs_kirim, out_celup.cones_kirim, pemasukan.tgl_masuk, pemasukan.nama_cluster, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, open_po.kg_po, out_celup.lot_kirim, bon_celup.no_surat_jalan, out_celup.l_m_d, out_celup.gw_kirim, out_celup.harga')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = master_order.no_model AND open_po.kode_warna = schedule_celup.kode_warna AND open_po.item_type = schedule_celup.item_type', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->groupBy('pemasukan.id_pemasukan')
            ->orderBy('pemasukan.tgl_masuk', 'DESC');


        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $this->groupStart()

                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.item_type', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->orLike('schedule_celup.warna', $key)
                ->groupEnd();
        }

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('pemasukan.tgl_masuk >=', $tanggal_awal)
                    ->where('pemasukan.tgl_masuk <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('pemasukan.tgl_masuk >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('pemasukan.tgl_masuk <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }


        return $this->findAll();
    }

    public function getDataByIdOutCelup($idOutCelup)
    {
        return $this->select('pemasukan.*, out_celup.lot_kirim')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->where('pemasukan.id_out_celup', $idOutCelup)
            ->groupBy('pemasukan.id_pemasukan')
            ->get()
            ->getResultArray();
    }
    public function getDataByIdStok($idStok)
    {
        return $this->select('
                COALESCE(schedule_celup.no_model, retur.no_model) AS no_model,
                COALESCE(schedule_celup.item_type, retur.item_type) AS item_type,
                COALESCE(schedule_celup.kode_warna, retur.kode_warna) AS kode_warna,
                COALESCE(schedule_celup.warna, retur.warna) AS warna,
                pemasukan.*,
                out_celup.no_karung, out_celup.lot_kirim, out_celup.kgs_kirim, out_celup.cones_kirim
            ')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('retur', 'retur.id_retur = out_celup.id_retur', 'left')
            ->where('id_stock', $idStok)
            ->where('out_jalur', "0")
            ->get()
            ->getResultArray();
    }
    public function getDataByCluster($data)
    {
        return $this->select('
                COALESCE(schedule_celup.no_model, retur.no_model) AS no_model,
                COALESCE(schedule_celup.item_type, retur.item_type) AS item_type,
                COALESCE(schedule_celup.kode_warna, retur.kode_warna) AS kode_warna,
                COALESCE(schedule_celup.warna, retur.warna) AS warna,
                pemasukan.*,
                out_celup.no_karung, out_celup.lot_kirim, out_celup.kgs_kirim, out_celup.cones_kirim
            ')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('retur', 'retur.id_retur = out_celup.id_retur', 'left')
            ->join('stock', 'stock.id_stock=pemasukan.id_stock', 'left')
            ->where('stock.no_model', $data['no_model'])
            ->where('stock.item_type', $data['item_type'])
            ->where('stock.kode_warna', $data['kode_warna'])
            ->where('stock.nama_cluster', $data['cluster'])
            ->where('out_jalur', "0")
            ->groupBy('out_celup.id_out_celup')
            ->get()
            ->getResultArray();
    }
    public function getDataInput($idPemasukan)
    {
        return $this->select('pemasukan.*, out_celup.no_karung, out_celup.lot_kirim')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->where('id_pemasukan', $idPemasukan)
            ->get()
            ->getResultArray();
    }

    public function getIdPemasukanByRetur($noModel, $itemType, $kodeWarna)
    {
        return $this->select('pemasukan.id_pemasukan')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->where('schedule_celup.no_model', $noModel)
            ->where('schedule_celup.item_type', $itemType)
            ->where('schedule_celup.kode_warna', $kodeWarna)
            ->get()
            ->getRowArray(); // Ambil satu baris saja
    }
}
