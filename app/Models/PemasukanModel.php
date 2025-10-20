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
        $inout = $this->select('schedule_celup.no_model, item_type, kode_warna, 
        SUM(out_celup.kgs_kirim) AS masuk, 
        SUM(pengeluaran.kgs_out) AS keluar')
            ->join('pengeluaran', 'pengeluaran.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->where('schedule_celup.no_model', $no_model)
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

    public function getFilterDatangBenang($key, $tanggal_awal, $tanggal_akhir, $poPlus)
    {
        $subMaterial = $this->db->table('material')
            ->select('master_order.no_model, material.item_type, material.kode_warna, material.color, SUM(material.kgs) AS total_kgs')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->groupBy('material.id_order, material.item_type, material.kode_warna, material.color')
            ->getCompiledSelect();
        // Query 1: Untuk data yang punya id_other_bon (other_bon)
        $builder1 = $this->db->table('pemasukan')
            ->select('out_celup.id_out_celup, other_bon.id_other_bon, out_celup.id_other_bon, out_celup.l_m_d, out_celup.harga, out_celup.no_karung, SUM(DISTINCT out_celup.kgs_kirim) AS kgs_kirim, SUM(DISTINCT out_celup.cones_kirim) AS cones_kirim, SUM(DISTINCT out_celup.gw_kirim) AS gw_kirim,, out_celup.lot_kirim, out_celup.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan, other_bon.tgl_datang, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, m.total_kgs AS kgs_material, pemasukan.tgl_masuk, pemasukan.nama_cluster, other_bon.keterangan, pemasukan.admin, other_bon.po_tambahan AS po_plus')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon')
            ->join('master_order', 'master_order.no_model = out_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = out_celup.no_model AND open_po.kode_warna = other_bon.kode_warna AND open_po.item_type = other_bon.item_type', 'left')
            ->join("($subMaterial) m", "m.no_model  = out_celup.no_model AND m.item_type  = other_bon.item_type AND m.kode_warna = other_bon.kode_warna AND m.color = other_bon.warna", 'left')
            ->join('master_material', 'master_material.item_type = other_bon.item_type', 'left')
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->where('master_material.jenis', 'BENANG')
            ->groupBy('out_celup.id_out_celup, other_bon.id_other_bon, out_celup.no_model, other_bon.item_type, other_bon.kode_warna');
        // Query 2: Untuk data pemasukan biasa dari schedule celup
        $builder2 = $this->db->table('pemasukan')
            ->select('bon_celup.id_bon, out_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, SUM(DISTINCT out_celup.kgs_kirim) AS kgs_kirim, SUM(DISTINCT out_celup.cones_kirim) AS cones_kirim, pemasukan.tgl_masuk, pemasukan.nama_cluster, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, m.total_kgs AS kgs_material, out_celup.lot_kirim, bon_celup.no_surat_jalan, bon_celup.tgl_datang, out_celup.l_m_d, SUM(DISTINCT out_celup.gw_kirim) AS gw_kirim, out_celup.harga, bon_celup.keterangan, pemasukan.admin, schedule_celup.po_plus')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join('master_order', 'master_order.no_model = out_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = master_order.no_model AND open_po.kode_warna = schedule_celup.kode_warna AND open_po.item_type = schedule_celup.item_type', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->join("($subMaterial) m", "m.no_model  = schedule_celup.no_model AND m.item_type  = schedule_celup.item_type AND m.kode_warna = schedule_celup.kode_warna AND m.color = schedule_celup.warna", 'left')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
            ->where('master_material.jenis', 'BENANG')
            ->groupBy('out_celup.id_out_celup, bon_celup.id_bon, out_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna');

        // Filter PO(+) jika checkbox dicentang
        if ($poPlus !== null && $poPlus !== '') {
            $builder1->where('other_bon.po_tambahan', $poPlus);
            $builder2->where('schedule_celup.po_plus', $poPlus);
        }

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            // builder1 untuk pencarian di kolom other_bon
            $builder1->groupStart()
                ->like('out_celup.no_model', $key)
                ->orLike('other_bon.item_type', $key)
                ->orLike('other_bon.kode_warna', $key)
                ->orLike('other_bon.warna', $key)
                ->orLike('other_bon.tgl_datang', $key)
                ->orLike('out_celup.lot_kirim', $key)
                ->groupEnd();

            // builder2 untuk pencarian di kolom schedule_celup
            $builder2->groupStart()
                ->like('out_celup.no_model', $key)
                ->orLike('schedule_celup.item_type', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->orLike('schedule_celup.warna', $key)
                ->orLike('bon_celup.tgl_datang', $key)
                ->orLike('out_celup.lot_kirim', $key)
                ->groupEnd();
        }

        // Filter tanggal (berlaku di kedua query)
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal)->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal)->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            }
        }

        // Eksekusi dan gabungkan hasilnya
        $result1 = $builder1->get()->getResultArray();
        $result2 = $builder2->get()->getResultArray();

        $merged = array_merge($result1, $result2);
        usort($merged, function ($a, $b) {
            return strtotime($b['tgl_datang']) <=> strtotime($a['tgl_datang']);
        });

        return $merged;
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
        return
            $this->select('
                COALESCE(out_celup.no_model, retur.no_model, other_bon.no_model, stock.no_model) AS no_model,
                COALESCE(retur.item_type, other_bon.item_type, stock.item_type) AS item_type,
                COALESCE(retur.kode_warna, other_bon.kode_warna, stock.kode_warna) AS kode_warna,
                COALESCE(retur.warna, other_bon.warna, stock.warna) AS warna,
                pemasukan.*,
                out_celup.no_karung, 
                out_celup.lot_kirim, out_celup.kgs_kirim, out_celup.cones_kirim,
                stock.nama_cluster AS cluster_real
                ')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('retur', 'retur.id_retur = out_celup.id_retur', 'left')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon', 'left') // tambahkan join
            ->join('stock', 'stock.id_stock=pemasukan.id_stock', 'left')
            ->where('stock.nama_cluster', $data['cluster'])
            ->where('pemasukan.out_jalur', '0')
            ->where("COALESCE(out_celup.no_model, retur.no_model, other_bon.no_model, stock.no_model) = ", $data['no_model'])
            ->where("COALESCE(retur.item_type, other_bon.item_type, stock.item_type) = ", $data['item_type'])
            ->where("COALESCE(retur.kode_warna, other_bon.kode_warna, stock.kode_warna) = ", $data['kode_warna'])
            // ->orWhere('stock.kgs_in_out >', 0)
            // ->orWhere('stock.kgs_stock_awal >', 0)
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

    public function getFilterBenang($tanggal_awal, $tanggal_akhir)
    {
        // Query 1: Untuk data yang punya id_other_bon (other_bon)
        $builder1 = $this->db->table('pemasukan')
            ->select('COUNT(out_celup.id_out_celup) AS total_karung, out_celup.id_other_bon, out_celup.l_m_d, out_celup.harga, out_celup.no_karung, SUM(out_celup.gw_kirim) AS gw, SUM(out_celup.kgs_kirim) AS kgs_kirim, SUM(out_celup.cones_kirim) AS cones, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan, other_bon.detail_sj, other_bon.tgl_datang, DATE(pemasukan.created_at) AS tgl_input, master_material.jenis, master_material.ukuran, master_warna_benang.warna_dasar,pemasukan.tgl_masuk')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon')
            ->join('master_material', 'master_material.item_type = other_bon.item_type')
            ->join('master_warna_benang', 'master_warna_benang.kode_warna = other_bon.kode_warna', 'left')
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->where('master_material.jenis', 'BENANG')
            ->orderBy('other_bon.no_surat_jalan', 'ASC')
            ->groupBy('other_bon.tgl_datang, other_bon.item_type, other_bon.no_surat_jalan');

        $builder2 = $this->db->table('pemasukan')
            ->select('schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, SUM(out_celup.kgs_kirim) AS kgs_kirim, SUM(out_celup.gw_kirim) AS gw, bon_celup.tgl_datang, COUNT(pemasukan.id_out_celup) AS total_karung, out_celup.l_m_d, out_celup.harga, SUM(out_celup.cones_kirim) AS cones, bon_celup.no_surat_jalan, master_material.jenis, master_material.ukuran, DATE(pemasukan.created_at) AS tgl_input, bon_celup.detail_sj, master_warna_benang.warna_dasar,pemasukan.tgl_masuk')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join('master_warna_benang', 'master_warna_benang.kode_warna = schedule_celup.kode_warna', 'left')
            ->where('master_material.jenis', 'BENANG')
            ->orderBy('bon_celup.no_surat_jalan', 'ASC')
            ->groupBy('bon_celup.tgl_datang, schedule_celup.item_type, bon_celup.no_surat_jalan');

        // Filter tanggal (berlaku di kedua query)
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal)->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal)->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            }
        }

        // Eksekusi dan gabungkan hasilnya
        $result1 = $builder1->get()->getResultArray();
        $result2 = $builder2->get()->getResultArray();

        // Gabungkan hasilnya
        $merged = array_merge($result1, $result2);

        // Urutkan berdasarkan tgl_masuk
        usort($merged, function ($a, $b) {
            // Urutkan dulu berdasarkan tgl_datang ASC
            $tglCompare = strtotime($a['tgl_datang']) - strtotime($b['tgl_datang']);
            if ($tglCompare !== 0) {
                return $tglCompare;
            }
            // Jika tgl_datang sama, urutkan berdasarkan no_surat_jalan ASC
            return strcmp($a['no_surat_jalan'], $b['no_surat_jalan']);
        });

        return $merged;
    }

    public function listOtherBarcode()
    {
        return $this->db->table('pemasukan')
            ->select('other_bon.tgl_datang')
            ->join('out_celup', 'pemasukan.id_out_celup = out_celup.id_out_celup')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon', 'left')
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->groupBy('other_bon.tgl_datang')
            ->orderBy('other_bon.tgl_datang', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function detailOtherBarcode($tgl_datang)
    {
        return $this->db->table('pemasukan')
            ->select('out_celup.id_out_celup, out_celup.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan, out_celup.lot_kirim, out_celup.kgs_kirim, out_celup.cones_kirim, other_bon.tgl_datang')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon', 'left')
            ->where('other_bon.tgl_datang', $tgl_datang)
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->get()
            ->getResultArray();
    }

    public function getFilterDatangNylon($key, $tanggal_awal, $tanggal_akhir, $poPlus)
    {
        $subMaterial = $this->db->table('material')
            ->select('master_order.no_model, material.item_type, material.kode_warna, material.color, SUM(material.kgs) AS total_kgs')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->groupBy('material.id_order, material.item_type, material.kode_warna, material.color')
            ->getCompiledSelect();
        // Query 1: Untuk data yang punya id_other_bon (other_bon)
        $builder1 = $this->db->table('pemasukan')
            ->select('out_celup.id_out_celup, other_bon.id_other_bon, out_celup.id_other_bon, out_celup.l_m_d, out_celup.harga, out_celup.no_karung, SUM(out_celup.gw_kirim) AS gw_kirim, SUM(out_celup.kgs_kirim) AS kgs_kirim, SUM(out_celup.cones_kirim) AS cones_kirim, out_celup.lot_kirim, other_bon.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan, other_bon.tgl_datang, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, m.total_kgs AS kgs_material, pemasukan.tgl_masuk, pemasukan.nama_cluster, other_bon.keterangan, pemasukan.admin, other_bon.po_tambahan AS po_plus')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon')
            ->join('master_order', 'master_order.no_model = other_bon.no_model', 'left')
            ->join('open_po', 'open_po.no_model = other_bon.no_model AND open_po.kode_warna = other_bon.kode_warna AND open_po.item_type = other_bon.item_type', 'left')
            ->join("($subMaterial) m", "m.no_model  = other_bon.no_model AND m.item_type  = other_bon.item_type AND m.kode_warna = other_bon.kode_warna AND m.color = other_bon.warna", 'left')
            ->join('master_material', 'master_material.item_type = other_bon.item_type', 'left')
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->where('master_material.jenis', 'NYLON')
            ->groupBy('other_bon.id_other_bon, other_bon.no_model, other_bon.item_type, other_bon.kode_warna');

        $builder2 = $this->db->table('pemasukan')
            ->select('bon_celup.id_bon, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, SUM(out_celup.kgs_kirim) AS kgs_kirim, COUNT(out_celup.cones_kirim) AS cones_kirim, pemasukan.tgl_masuk, pemasukan.nama_cluster, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, m.total_kgs AS kgs_material, out_celup.lot_kirim, bon_celup.no_surat_jalan, bon_celup.tgl_datang, out_celup.l_m_d, out_celup.gw_kirim, out_celup.harga, bon_celup.keterangan, pemasukan.admin, schedule_celup.po_plus')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = master_order.no_model AND open_po.kode_warna = schedule_celup.kode_warna AND open_po.item_type = schedule_celup.item_type', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->join("($subMaterial) m", "m.no_model  = schedule_celup.no_model AND m.item_type  = schedule_celup.item_type AND m.kode_warna = schedule_celup.kode_warna AND m.color = schedule_celup.warna", 'left')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
            ->where('master_material.jenis', 'NYLON')
            ->groupBy('bon_celup.id_bon, out_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna');

        // Filter PO(+) jika checkbox dicentang
        if ($poPlus !== null && $poPlus !== '') {
            $builder1->where('other_bon.po_tambahan', $poPlus);
            $builder2->where('schedule_celup.po_plus', $poPlus);
        }

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            // builder1 untuk pencarian di kolom other_bon
            $builder1->groupStart()
                ->like('other_bon.no_model', $key)
                ->orLike('other_bon.item_type', $key)
                ->orLike('other_bon.kode_warna', $key)
                ->groupEnd();

            // builder2 untuk pencarian di kolom schedule_celup
            $builder2->groupStart()
                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.item_type', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->orLike('schedule_celup.warna', $key)
                ->groupEnd();
        }

        // Filter tanggal (berlaku di kedua query)
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal)->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal)->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $builder1->where('other_bon.tgl_datang >=', $tanggal_awal);
                $builder2->where('bon_celup.tgl_datang >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $builder1->where('other_bon.tgl_datang <=', $tanggal_akhir);
                $builder2->where('bon_celup.tgl_datang <=', $tanggal_akhir);
            }
        }

        // Eksekusi dan gabungkan hasilnya
        $result1 = $builder1->get()->getResultArray();
        $result2 = $builder2->get()->getResultArray();

        $merged = array_merge($result1, $result2);
        usort($merged, function ($a, $b) {
            return strtotime($b['tgl_datang']) <=> strtotime($a['tgl_datang']);
        });

        return $merged;
    }

    public function getDetailDatangBenang($idOutCelup)
    {
        return $this->db->table('pemasukan')
            ->select('pemasukan.*, out_celup.*,other_bon.*,stock.*,master_order.buyer,master_order.foll_up, master_order.no_order, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, other_bon.warna AS warna')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon', 'left')
            ->join('master_order', 'master_order.no_model = other_bon.no_model', 'left')
            ->join('stock', 'stock.id_stock = pemasukan.id_stock', 'left')
            ->where('pemasukan.id_out_celup', $idOutCelup)
            ->get()
            ->getRowArray();
    }

    public function getDatangSolid($key, $jenis = null)
    {
        $builder = $this->db->table('pemasukan p')
            ->select('p.created_at as tgl_terima, p.nama_cluster, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, oc.kgs_kirim as qty_datang, oc.cones_kirim as cones_datang, oc.lot_kirim as lot_datang, COALESCE(bc.no_surat_jalan, ob.no_surat_jalan) as no_surat_jalan, oc.l_m_d, COALESCE(bc.tgl_datang, ob.tgl_datang) as tgl_datang, COALESCE(bc.keterangan, ob.keterangan) as keterangan, p.admin')
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            ->join('bon_celup bc', 'oc.id_bon = bc.id_bon', 'left')
            ->join('other_bon ob', 'oc.id_other_bon = ob.id_other_bon', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->where('oc.no_model', $key)
            ->notLike('sc.item_type', '%LUREX%')
            ->where('oc.ganti_retur <>', '1')
            ->where('sc.po_plus <>', '1');
        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder->groupBy('p.created_at')
            ->orderBy('sc.item_type, sc.kode_warna, sc.warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getPlusDatangSolid($key, $jenis = null)
    {
        $builder = $this->db->table('pemasukan p')
            ->select("p.created_at as tgl_terima, p.nama_cluster, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, oc.kgs_kirim as qty_datang, oc.cones_kirim as cones_datang, oc.lot_kirim as lot_datang, COALESCE(bc.no_surat_jalan, ob.no_surat_jalan) as no_surat_jalan, oc.l_m_d, COALESCE(bc.tgl_datang, ob.tgl_datang) as tgl_datang, COALESCE(bc.keterangan, ob.keterangan) as keterangan, p.admin,
            -- Subquery qty_poplus
              (
                SELECT SUM(COALESCE(pt.poplus_mc_kg, 0) + COALESCE(pt.plus_pck_kg, 0))
                FROM po_tambahan pt
                WHERE pt.id_material = m.id_material
                AND pt.status = 'approved'
              ) AS qty_poplus
            ")
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            ->join('bon_celup bc', 'oc.id_bon = bc.id_bon', 'left')
            ->join('other_bon ob', 'oc.id_other_bon = ob.id_other_bon', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->join('master_order mo', 'sc.no_model = mo.no_model', 'left')
            ->join('material m', 'sc.item_type = m.item_type AND mo.id_order = m.id_order AND sc.kode_warna = m.kode_warna', 'left')
            ->where('oc.no_model', $key)
            ->notLike('sc.item_type', '%LUREX%')
            ->where('oc.ganti_retur <>', '1')
            ->where('sc.po_plus', '1');
        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder->groupBy('p.created_at')
            ->orderBy('sc.item_type, sc.kode_warna, sc.warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getGantiRetur($key, $jenis = null)
    {
        $builder = $this->db->table('pemasukan p')
            ->select("p.created_at as tgl_terima, p.nama_cluster, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, oc.kgs_kirim as qty_datang, oc.cones_kirim as cones_datang, oc.lot_kirim as lot_datang, COALESCE(bc.no_surat_jalan, ob.no_surat_jalan) as no_surat_jalan, oc.l_m_d, COALESCE(bc.tgl_datang, ob.tgl_datang) as tgl_datang, COALESCE(bc.keterangan, ob.keterangan) as keterangan, p.admin,
            -- Subquery qty_poplus
              (
                SELECT SUM(COALESCE(pt.poplus_mc_kg, 0) + COALESCE(pt.plus_pck_kg, 0))
                FROM po_tambahan pt
                WHERE pt.id_material = m.id_material
                AND pt.status = 'approved'
              ) AS qty_poplus
            ")
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            ->join('bon_celup bc', 'oc.id_bon = bc.id_bon', 'left')
            ->join('other_bon ob', 'oc.id_other_bon = ob.id_other_bon', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->join('master_order mo', 'sc.no_model = mo.no_model', 'left')
            ->join('material m', 'sc.item_type = m.item_type AND mo.id_order = m.id_order AND sc.kode_warna = m.kode_warna', 'left')
            ->where('oc.no_model', $key)
            ->where('oc.ganti_retur', '1');
        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder->groupBy('p.created_at')
            ->orderBy('sc.item_type, sc.kode_warna, sc.warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getDatangLurex($key, $jenis = null)
    {
        $builder = $this->db->table('pemasukan p')
            ->select('p.created_at as tgl_terima, p.nama_cluster, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, oc.kgs_kirim as qty_datang, oc.cones_kirim as cones_datang, oc.lot_kirim as lot_datang, COALESCE(bc.no_surat_jalan, ob.no_surat_jalan) as no_surat_jalan, oc.l_m_d, COALESCE(bc.tgl_datang, ob.tgl_datang) as tgl_datang, COALESCE(bc.keterangan, ob.keterangan) as keterangan, p.admin')
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            ->join('bon_celup bc', 'oc.id_bon = bc.id_bon', 'left')
            ->join('other_bon ob', 'oc.id_other_bon = ob.id_other_bon', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->where('oc.no_model', $key)
            ->like('sc.item_type', '%LUREX%')
            ->where('oc.ganti_retur <>', '1')
            ->where('sc.po_plus <>', '1');
        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder->groupBy('p.created_at')
            ->orderBy('sc.item_type, sc.kode_warna, sc.warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getPlusDatangLurex($key, $jenis = null)
    {
        $builder = $this->db->table('pemasukan p')
            ->select("p.created_at as tgl_terima, p.nama_cluster, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, oc.kgs_kirim as qty_datang, oc.cones_kirim as cones_datang, oc.lot_kirim as lot_datang, COALESCE(bc.no_surat_jalan, ob.no_surat_jalan) as no_surat_jalan, oc.l_m_d, COALESCE(bc.tgl_datang, ob.tgl_datang) as tgl_datang, COALESCE(bc.keterangan, ob.keterangan) as keterangan, p.admin,
            -- Subquery qty_poplus
              (
                SELECT SUM(COALESCE(pt.poplus_mc_kg, 0) + COALESCE(pt.plus_pck_kg, 0))
                FROM po_tambahan pt
                WHERE pt.id_material = m.id_material
                AND pt.status = 'approved'
              ) AS qty_poplus
            ")
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            ->join('bon_celup bc', 'oc.id_bon = bc.id_bon', 'left')
            ->join('other_bon ob', 'oc.id_other_bon = ob.id_other_bon', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->join('master_order mo', 'sc.no_model = mo.no_model', 'left')
            ->join('material m', 'sc.item_type = m.item_type AND mo.id_order = m.id_order AND sc.kode_warna = m.kode_warna', 'left')
            ->where('oc.no_model', $key)
            ->like('sc.item_type', '%LUREX%')
            ->where('oc.ganti_retur <>', '1')
            ->where('sc.po_plus', '1');
        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder->groupBy('p.created_at')
            ->orderBy('sc.item_type, sc.kode_warna, sc.warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function listPindahOrderBarcode()
    {
        return $this->db->table('pemasukan')
            ->select('pemasukan.id_out_celup, stock.id_stock, stock.no_model, stock.item_type, stock.kode_warna, stock.warna, stock.lot_awal, history_stock.keterangan')
            ->join('stock', 'stock.id_stock = pemasukan.id_stock', 'left')
            ->join('history_stock', 'history_stock.id_stock_new = pemasukan.id_stock', 'left')
            ->where('history_stock.id_out_celup IS NOT NULL')
            ->where('history_stock.id_out_celup_new IS NOT NULL')
            ->where('history_stock.keterangan', 'Pindah Order')
            ->where('history_stock.krg', 1)
            ->groupBy('stock.no_model')
            ->orderBy('history_stock.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function detailPindahOrderBarcode($idStock)
    {
        return $this->db->table('pemasukan')
            ->select('out_celup.id_out_celup, out_celup.no_karung, out_celup.kgs_kirim, out_celup.cones_kirim, out_celup.lot_kirim, pemasukan.id_stock, stock.no_model, stock.item_type, stock.kode_warna, stock.warna, history_stock.keterangan, history_stock.krg, out_celup.admin')
            ->join('stock', 'stock.id_stock = pemasukan.id_stock', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('history_stock', 'history_stock.id_out_celup_new = out_celup.id_out_celup', 'left')
            ->where('history_stock.id_stock_new', $idStock)
            ->where('history_stock.id_out_celup IS NOT NULL')
            ->where('history_stock.id_out_celup_new IS NOT NULL')
            ->where('history_stock.keterangan', 'Pindah Order')
            ->where('history_stock.krg', 1)
            ->orderBy('out_celup.no_karung', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function searchStockDetail($noModel = null, $kodeWarna = null)
    {
        $db = \Config\Database::connect();

        // base SQL tanpa filter
        $sql = "
        SELECT 
            cl.nama_cluster, 
            oc.no_model, 
            st.item_type, 
            st.kode_warna, 
            st.warna, 
            cl.kapasitas, 
            oc.no_karung,
            oc.lot_kirim AS lot_stock,
            hs.lot AS lot_awal,
            -- Kgs: kalau ada history_stock, maka 0; kalau tidak, hitung dari oc dan pengeluaran
            IF(
                COALESCE(hs.kgsAwal, 0) > 0 AND pm.out_jalur = 0,
                0,
                oc.kgs_kirim 
                    - SUM(COALESCE(p.kgs_out, 0)) 
                    - SUM(COALESCE(oo.kgs_other_out, 0))
            ) AS Kgs,

            -- Cns: kalau ada history_stock, maka 0; kalau tidak, hitung dari oc dan pengeluaran
            IF(
                COALESCE(hs.cnsAwal, 0) > 0 AND pm.out_jalur = 0,
                0,
                oc.cones_kirim 
                    - SUM(COALESCE(p.cns_out, 0)) 
                    - SUM(COALESCE(oo.cns_other_out, 0))
            ) AS Cns,

            -- Stock awal (jika ada history)
            IF(
                COALESCE(hs.kgsAwal,0) > 0, 
                COALESCE(hs.kgsAwal,0) - SUM(COALESCE(p.kgs_out,0)) - SUM(COALESCE(oo.kgs_other_out,0)), 
                0
            ) AS KgsStockAwal, 

            IF(
                COALESCE(hs.cnsAwal,0) > 0, 
                COALESCE(hs.cnsAwal,0) - SUM(COALESCE(p.cns_out,0)) - SUM(COALESCE(oo.cns_other_out,0)), 
                0
            ) AS CnsStockAwal, 

            IF(
                COALESCE(hs.krgAwal,0) > 0, 
                COALESCE(hs.krgAwal,0) - SUM(COALESCE(p.krg_out,0)) - SUM(COALESCE(oo.krg_other_out,0)), 
                0
            ) AS KrgStockAwal

        FROM pemasukan pm 
        LEFT JOIN out_celup oc 
            ON oc.id_out_celup = pm.id_out_celup
        LEFT JOIN stock st 
            ON pm.id_stock = st.id_stock

        LEFT JOIN (
            SELECT 
                hs.id_out_celup,
                hs.id_history_pindah,
                hs.id_stock_new,
                oc1.no_karung,
                SUM(hs.kgs) AS kgsAwal, 
                SUM(hs.cns) AS cnsAwal, 
                SUM(hs.krg) AS krgAwal,
                hs.lot
            FROM history_stock hs
            LEFT JOIN out_celup oc1 
                ON oc1.id_out_celup = hs.id_out_celup
            LEFT JOIN stock st 
                ON st.id_stock = hs.id_stock_new
            WHERE hs.keterangan = 'Pindah Order'
            GROUP BY hs.id_out_celup, hs.id_stock_new, oc1.no_karung
        ) hs 
            ON hs.id_stock_new = st.id_stock 

        LEFT JOIN pengeluaran p 
            ON p.id_out_celup = oc.id_out_celup
        LEFT JOIN other_out oo 
            ON oo.id_out_celup = oc.id_out_celup
        LEFT JOIN cluster cl 
            ON cl.nama_cluster = st.nama_cluster

        WHERE 1=1
    ";

        // Tambahkan filter dinamis
        $params = [];
        if (!empty($noModel)) {
            $sql .= " AND st.no_model = :no_model:";
            $params['no_model'] = $noModel;
        }
        if (!empty($kodeWarna)) {
            $sql .= " AND st.kode_warna = :kode_warna:";
            $params['kode_warna'] = $kodeWarna;
        }

        // Kelompokkan hasil
        $sql .= "
            GROUP BY 
                st.id_stock, 
                oc.id_out_celup

            HAVING 
                KgsStockAwal <> 0
                OR 
                Kgs <> 0

            ORDER BY pm.id_out_celup DESC
        ";

        // Jalankan query
        $query = $db->query($sql, $params);
        return $query->getResultArray();
    }
}
