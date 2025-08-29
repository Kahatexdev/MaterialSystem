<?php

namespace App\Models;

use CodeIgniter\Model;
use PDO;
use PHPUnit\Framework\Attributes\IgnoreFunctionForCodeCoverage;

class OutCelupModel extends Model
{
    protected $table            = 'out_celup';
    protected $primaryKey       = 'id_out_celup';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_out_celup',
        'id_bon',
        'id_celup',
        'id_other_bon',
        'no_model',
        'id_retur',
        'l_m_d',
        'harga',
        'no_karung',
        'gw_kirim',
        'kgs_kirim',
        'cones_kirim',
        'lot_kirim',
        'ganti_retur',
        'operator_packing',
        'shift',
        'admin',
        'created_at',
        'updated_at',
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

    //Detail Bon Lama
    // public function getDetailBonByIdBon($id)
    // {
    //     // Ambil semua no_model dari out_celup untuk bon ini
    //     $subModels = $this->db->table('out_celup')
    //         ->select('no_model')
    //         ->where('id_bon', $id)
    //         ->get()
    //         ->getResultArray();

    //     $modelList = [];

    //     foreach ($subModels as $row) {
    //         $no_model = strtoupper(str_replace('POGABUNGAN', '', $row['no_model']));
    //         $models = preg_split('/[_\s]+/', trim($no_model));
    //         foreach ($models as $m) {
    //             if (!in_array($m, $modelList)) {
    //                 $modelList[] = $m;
    //             }
    //         }
    //     }

    //     if (empty($modelList)) {
    //         return [];
    //     }

    //     // Ambil data lengkap dari out_celup dan gabungkan manual ke buyer, ukuran, dll
    //     return $this->select('
    //         out_celup.*, 
    //         schedule_celup.item_type, 
    //         schedule_celup.kode_warna, 
    //         schedule_celup.warna,
    //         master_material.ukuran,
    //         master_order.buyer
    //     ')
    //         ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
    //         ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
    //         ->join('master_order', 'master_order.no_model IN ("' . implode('","', $modelList) . '")', '', false) // RAW JOIN
    //         ->where('out_celup.id_bon', $id)
    //         ->groupBy('id_out_celup')
    //         ->findAll();
    // }

    public function getDetailBonByIdBon($id)
    {
        // Ambil semua no_model dari out_celup untuk bon ini
        $subModels = $this->db->table('out_celup')
            ->select('no_model')
            ->where('id_bon', $id)
            ->get()
            ->getResultArray();

        $modelList = [];

        foreach ($subModels as $row) {
            $no_model = strtoupper(str_replace('POGABUNGAN', '', $row['no_model']));
            $models = preg_split('/[_\s]+/', trim($no_model));
            foreach ($models as $m) {
                if (!in_array($m, $modelList)) {
                    $modelList[] = $m;
                }
            }
        }

        if (empty($modelList)) {
            return [];
        }

        // Ambil data lengkap dari out_celup dan gabungkan manual ke buyer, ukuran, dll
        return $this->select('
            out_celup.*, 
            schedule_celup.item_type, 
            schedule_celup.kode_warna, 
            schedule_celup.warna,
            master_material.ukuran,
            master_buyer.nama_buyer as buyer,
            open_po.spesifikasi_benang
        ')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
            ->join('master_order', 'master_order.no_model = out_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->join('master_buyer', 'master_order.buyer = master_buyer.kode_buyer', 'left')
            ->where('out_celup.id_bon', $id)
            ->groupBy('id_out_celup')
            ->orderBy('id_celup,no_karung', 'ASC')
            ->findAll();
    }

    public function getDataOut($id)
    {
        return $this->db->table('out_celup')
            ->select('out_celup.*, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.id_out_celup', $id)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getDataOutCelup()
    {
        return $this->db->table('out_celup')
            ->select('bon_celup.id_bon, out_celup.id_out_celup,
                  GROUP_CONCAT(DISTINCT schedule_celup.no_model ORDER BY schedule_celup.no_model ASC SEPARATOR ", ") as no_model_list, 
                  bon_celup.tgl_datang, 
                  bon_celup.no_surat_jalan, 
                  bon_celup.detail_sj,
                 GROUP_CONCAT(out_celup.id_celup) as id_celup_list')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->join('bon_celup', 'out_celup.id_bon = bon_celup.id_bon')
            ->groupBy('bon_celup.id_bon')
            ->orderBy('bon_celup.id_bon', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getDetailByIdBon($id_bon)
    {
        return $this->select('schedule_celup.id_celup, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, 
                              out_celup.l_m_d, out_celup.harga, out_celup.ganti_retur, 
                              out_celup.gw_kirim, out_celup.kgs_kirim, out_celup.cones_kirim, 
                              out_celup.lot_kirim')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.id_bon', $id_bon)
            ->findAll();
    }

    public function dataCelup($idbon, $idCelup)
    {
        return $this->where('id_bon', $idbon)
            ->where('id_celup', $idCelup)
            ->findAll();
    }

    public function getItemTypeByModel($pdk)
    {
        return $this->db->table('out_celup')
            ->select('schedule_celup.item_type')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.no_model', $pdk)
            ->groupBy('schedule_celup.no_model')
            ->groupBy('schedule_celup.item_type')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKodeWarnaByModelAndItemType($no_model, $item_type)
    {
        return $this->db->table('out_celup')
            ->select('schedule_celup.kode_warna')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.no_model', $no_model)
            ->where('schedule_celup.item_type', $item_type)
            ->groupBy('schedule_celup.kode_warna')
            ->get()
            ->getResultArray();
    }

    public function getWarnaByKodeWarna($no_model, $item_type, $kode_warna)
    {
        $result = $this->db->table('out_celup')
            ->select('schedule_celup.warna')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.no_model', $no_model)
            ->where('schedule_celup.item_type', $item_type)
            ->where('schedule_celup.kode_warna', $kode_warna)
            ->groupBy('schedule_celup.warna')
            ->distinct()
            ->get()
            ->getRowArray(); // Ambil satu baris saja

        return $result ? $result['warna'] : null; // Pastikan hanya warna yang dikembalikan
    }

    public function getLotByKodeWarna($no_model, $item_type, $kode_warna)
    {
        return $this->db->table('out_celup')
            ->select('out_celup.lot_kirim')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup')
            ->where('out_celup.no_model', $no_model)
            ->where('schedule_celup.item_type', $item_type)
            ->where('schedule_celup.kode_warna', $kode_warna)
            ->where('out_celup.no_karung IS NOT NULL')
            ->groupBy('out_celup.lot_kirim')
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
        $query = $this->db->table('out_celup oc')
            ->select('oc.id_out_celup, oc.kgs_kirim, oc.cones_kirim, oc.gw_kirim')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup')
            ->where('oc.no_model', $no_model)
            ->where('sc.item_type', $item_type)
            ->where('sc.kode_warna', $kode_warna)
            ->where('oc.lot_kirim', $lot_kirim)
            ->where('oc.no_karung', $no_karung)
            ->get();

        $sql = $this->db->getLastQuery(); // Debugging query
        log_message('error', 'Query getKgsDanCones: ' . $sql); // Log ke CI4 logs

        return $query->getRowArray(); // Pastikan return berbentuk array
    }

    public function getIdCelup($idOut)
    {
        return $this->select('id_celup')
            ->where('id_out_celup', $idOut)
            ->first();
    }

    public function getIdCelups($idOut)
    {
        return $this->select('id_celup')
            ->whereIn('id_out_celup', $idOut)
            ->findAll();
    }
    // public function findOutCelup($idPemasukan)
    // {
    //     return $this->select('pemasukan.id_pemasukan, pemasukan.id_stock, out_celup.id_out_celup, out_celup.kgs_kirim, out_celup.cones_kirim, out_celup.no_karung, pemasukan.nama_cluster, out_celup.lot_kirim')
    //         ->join('pemasukan', 'pemasukan.id_out_celup = out_celup.id_out_celup', 'left')
    //         ->whereIn('pemasukan.id_pemasukan', $idPemasukan)
    //         ->findAll();
    // }
    public function findOutCelup($idPemasukan)
    {
        return $this->select('pemasukan.id_pemasukan, pemasukan.id_stock, out_celup.id_out_celup, (out_celup.kgs_kirim - COALESCE(other_out.kgs_other_out, 0)) AS kgs_kirim, (out_celup.cones_kirim - COALESCE(other_out.cns_other_out, 0)) AS cones_kirim, out_celup.no_karung, pemasukan.nama_cluster, out_celup.lot_kirim')
            ->join('pemasukan', 'pemasukan.id_out_celup = out_celup.id_out_celup', 'left')
            ->join('other_out', 'other_out.id_out_celup = out_celup.id_out_celup AND pemasukan.nama_cluster = other_out.nama_cluster', 'left')
            ->whereIn('pemasukan.id_pemasukan', $idPemasukan)
            ->findAll();
    }

    public function getDataReturByTgl($tglRetur)
    {
        return $this->select('out_celup.*, retur.no_model, retur.item_type, retur.kode_warna, retur.warna, retur.kgs_retur, retur.cns_retur, retur.krg_retur, retur.lot_retur, retur.tgl_retur, retur.kategori')
            ->join('retur', 'retur.id_retur = out_celup.id_retur')
            ->where('retur.tgl_retur', $tglRetur)
            ->orderBy('out_celup.id_out_celup', 'ASC')
            ->findAll();
    }
    public function getDataKirim($idRetur)
    {
        // Ambil id_celup dari id_retur
        $idCelup = $this->select('id_celup')->where('id_retur', $idRetur)->first();

        // Validasi jika data tidak ditemukan
        if (!$idCelup) {
            return null;
        }

        // Ambil semua id_out_celup yang terkait dengan id_celup
        $idOutRows = $this->select('id_out_celup')
            ->where('id_celup', $idCelup['id_celup'])
            ->findAll();

        // Ubah menjadi array datar
        $idOut = array_column($idOutRows, 'id_out_celup');

        // Jika tidak ada id_out_celup, return null atau default kosong
        if (empty($idOut)) {
            return [
                'kg_kirim' => 0,
                'cns_kirim' => 0,
                'krg_kirim' => 0,
            ];
        }

        // Join dengan tabel pengeluaran dan hitung total
        $pengeluaran = $this->select(
            'pengeluaran.lot_out,SUM(pengeluaran.kgs_out) as kg_kirim, 
                                 SUM(pengeluaran.cns_out) as cns_kirim, 
                                 SUM(pengeluaran.krg_out) as krg_kirim'
        )
            ->join('pengeluaran', 'pengeluaran.id_out_celup = out_celup.id_out_celup', 'left')
            ->whereIn('pengeluaran.id_out_celup', $idOut)
            ->first();
        return $pengeluaran;
    }

    public function getDataOtherBarcode($tglDatang)
    {
        return $this->select('out_celup.*, other_bon.no_model, other_bon.item_type, other_bon.kode_warna, other_bon.warna, other_bon.no_surat_jalan')
            ->join('other_bon', 'other_bon.id_other_bon = out_celup.id_other_bon')
            ->where('other_bon.tgl_datang', $tglDatang)
            ->orderBy('out_celup.id_out_celup', 'ASC')
            ->findAll();
    }
    public function getNoKarung($id)
    {
        // Query ambil no karung
        return $this->select('out_celup.no_karung, out_celup.gw_kirim, out_celup.kgs_kirim, out_celup.cones_kirim, out_celup.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup=out_celup.id_celup', 'left')
            ->where('out_celup.no_model', $id['no_model'])
            ->where('schedule_celup.item_type', $id['item_type'])
            ->where('schedule_celup.kode_warna', $id['kode_warna'])
            ->where('out_celup.lot_kirim', $id['lot'])
            ->where('out_celup.no_karung IS NOT NULL')
            ->groupBy('out_celup.id_out_celup')
            ->orderBy('out_celup.id_bon, out_celup.no_karung', 'ASC')
            ->get()->getResultArray();

        return $result;
    }
}
