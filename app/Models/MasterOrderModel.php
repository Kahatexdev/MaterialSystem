<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterOrderModel extends Model
{
    protected $table            = 'master_order';
    protected $primaryKey       = 'id_order';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_order',
        'no_order',
        'no_model',
        'buyer',
        'foll_up',
        'lco_date',
        'memo',
        'start_mc',
        'jarum',
        'delivery_awal',
        'delivery_akhir',
        'unit',
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


    public function findIdOrder($no_order)
    {
        return $this->select('id_order')->where('no_order', $no_order)->first();
    }

    public function checkDatabase($no_order, $no_model, $buyer, $lco_date, $foll_up)
    {
        return $this->where('no_order', $no_order)
            ->where('no_model', $no_model)
            ->where('buyer', $buyer)
            ->where('lco_date', $lco_date)
            ->where('foll_up', $foll_up)
            ->first();
    }

    public function getMaterialOrder($id)
    {
        $data = $this->select('no_model,buyer, delivery_awal, delivery_akhir, material.item_type, material.color, material.kode_warna, sum(material.kgs) as total_kg')
            ->join('material', 'material.id_order=master_order.id_order')
            ->where('master_order.id_order', $id)
            ->where('material.composition >=', 0)
            ->where('material.gw !=', 0)
            ->where('material.qty_pcs !=', 0)
            ->where('material.loss >=', 0)
            ->where('material.kgs >', 0)
            ->groupBy(['material.item_type', 'material.kode_warna'])
            ->orderBy('material.item_type')
            ->findAll();
        // Susun data menjadi terstruktur
        $result = [];
        foreach ($data as $row) {
            $itemType = $row['item_type'];
            if (!isset($result[$itemType])) {
                $result[$itemType] = [
                    'no_model' => $row['no_model'],
                    'item_type' => $itemType,
                    'kode_warna' => [],
                ];
            }
            $result[$itemType]['kode_warna'][] = [
                'no_model' => $row['no_model'],
                'item_type' => $itemType,
                'kode_warna' => $row['kode_warna'],
                'color' => $row['color'],
                'total_kg' => $row['total_kg'],
            ];
        }
        return $result;
    }

    public function getDatabyNoModel($no_model)
    {
        return $this->select('no_order, buyer, delivery_awal, delivery_akhir')
            ->where('no_model', $no_model)
            ->findAll();
    }

    public function getDelivery($no_model)
    {
        return $this->select('no_model,delivery_awal, delivery_akhir')
            ->where('no_model', $no_model)
            ->distinct()
            ->first();
    }

    public function getNoModel($id_order)
    {
        return $this->select('no_model')
            ->where('id_order', $id_order)
            ->first();
    }

    public function getDeliveryDates($noModel)
    {
        return $this->select('delivery_awal, delivery_akhir')
            ->where('no_model', $noModel)
            ->first();
    }

    public function getIdOrder($noModel)
    {
        return $this->select('id_order')
            ->where('no_model', $noModel)
            ->first();
    }

    public function getFilterMasterOrder($key, $tanggal_awal, $tanggal_akhir)
    {
        $this->select('master_order.*');

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $this->groupStart()
                ->like('master_order.buyer', $key)
                ->orLike('master_order.foll_up', $key)
                ->groupEnd();
        }

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('master_order.delivery_awal >=', $tanggal_awal)
                    ->where('master_order.delivery_awal <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('master_order.delivery_awal >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('master_order.delivery_awal <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }

        return $this->findAll();
    }

    public function getMaterialPoGabungan()
    {
        $data = $this->select('no_model,buyer, delivery_awal, delivery_akhir, material.item_type, material.color, material.kode_warna, sum(material.kgs) as total_kg')
            ->join('material', 'material.id_order=master_order.id_order')
            ->where('master_order.id_order')
            ->where('material.composition !=', 0)
            ->where('material.gw !=', 0)
            ->where('material.qty_pcs !=', 0)
            ->where('material.loss !=', 0)
            ->where('material.kgs >', 0)
            ->groupBy(['material.item_type', 'material.kode_warna'])
            ->orderBy('material.item_type')
            ->findAll();
    }

    public function getUnit($no_model)
    {
        return $this->select('unit')
            ->where('no_model', $no_model)
            ->first();
    }

    public function getFilterReportGlobal($noModel, $jenis = null)
    {
        $builder = $this->select("
        master_order.no_model,
        master_order.buyer,
        material.item_type,
        material.kode_warna,
        material.color,
        material.loss,
        material.area,

        -- total kgs material
        (
            SELECT SUM(COALESCE(m.kgs, 0))
            FROM material m
            WHERE m.id_order = master_order.id_order
            AND m.item_type = material.item_type
            AND m.kode_warna = material.kode_warna
        ) AS kgs,

        -- total po tambahan
        (
            SELECT 
            SUM(COALESCE(pt.poplus_mc_kg, 0) + COALESCE(pt.plus_pck_kg, 0))
            FROM po_tambahan pt
            WHERE pt.id_material = material.id_material
            AND pt.status = 'approved'
        ) AS qty_poplus,

        -- stock awal tanpa duplikasi
        (
            SELECT SUM(COALESCE(s.kgs_stock_awal, 0))
            FROM stock s
            WHERE s.id_stock IN (
                SELECT DISTINCT s2.id_stock
                FROM stock s2
                JOIN pemasukan p ON p.id_stock = s2.id_stock
                JOIN out_celup oc ON oc.id_out_celup = p.id_out_celup
                JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
                WHERE sc.no_model = master_order.no_model
                AND sc.kode_warna = material.kode_warna
                AND sc.item_type = material.item_type
            )
        ) AS stock_awal,

        -- stock akhir
        (
            SELECT SUM(COALESCE(s.kgs_in_out, 0))
            FROM stock s
            WHERE EXISTS (
                SELECT 1
                FROM pemasukan p
                LEFT JOIN out_celup oc ON oc.id_out_celup = p.id_out_celup
                LEFT JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
                WHERE p.id_stock = s.id_stock
                AND sc.no_model = master_order.no_model
                AND sc.kode_warna = material.kode_warna
                AND sc.item_type = material.item_type
            )
        ) AS stock_akhir,

        -- datang solid
        (
            SELECT 
            SUM(CASE WHEN COALESCE(sc.po_plus,0) = 0 THEN oc.kgs_kirim ELSE 0 END)
            FROM pemasukan p
            JOIN out_celup oc ON p.id_out_celup = oc.id_out_celup
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
        ) AS datang_solid,

        -- plus datang solid
        (
            SELECT 
            SUM(CASE WHEN COALESCE(sc.po_plus,0) = 1 THEN oc.kgs_kirim ELSE 0 END)
            FROM pemasukan p
            JOIN out_celup oc ON p.id_out_celup = oc.id_out_celup
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
        ) AS plus_datang_solid,

        -- ganti retur
        (
            SELECT SUM(COALESCE(oc.kgs_kirim, 0))
            FROM other_bon ob
            JOIN out_celup oc ON ob.id_other_bon = oc.id_other_bon
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
            AND ob.ganti_retur = 1
        ) AS ganti_retur,

        -- datang lurex
        (
            SELECT SUM(COALESCE(oc.kgs_kirim, 0))
            FROM other_bon ob
            JOIN out_celup oc ON ob.id_other_bon = oc.id_other_bon
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
            AND ob.ganti_retur <> 1
            AND ob.no_surat_jalan LIKE '%LRX%'
        ) AS datang_lurex,

        -- plus datang lurex
        (
            SELECT SUM(COALESCE(oc.kgs_kirim, 0))
            FROM other_bon ob
            JOIN out_celup oc ON ob.id_other_bon = oc.id_other_bon
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
            AND sc.po_plus = 1
            AND ob.no_surat_jalan LIKE '%LRX%'
        ) AS plus_datang_lurex,

        -- retur pb gudang benang
        (
            SELECT SUM(COALESCE(r.kgs_retur, 0))
            FROM retur r
            JOIN kategori_retur kr ON r.kategori = kr.nama_kategori
            WHERE r.no_model = master_order.no_model
            AND r.kode_warna = material.kode_warna
            AND r.item_type = material.item_type
            AND r.area_retur = 'GUDANG BENANG'
        ) AS retur_pb_gbn,

        -- retur pb area
        (
            SELECT SUM(COALESCE(r.kgs_retur, 0))
            FROM retur r
            JOIN kategori_retur kr ON r.kategori = kr.nama_kategori
            WHERE r.no_model = master_order.no_model
            AND r.kode_warna = material.kode_warna
            AND r.item_type = material.item_type
            AND r.area_retur <> 'GUDANG BENANG'
        ) AS retur_pb_area,

        -- pakai area 
        (
            SELECT SUM(COALESCE(p.kgs_out, 0))
            FROM pengeluaran p
            JOIN out_celup oc ON oc.id_out_celup = p.id_out_celup
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
        ) AS pakai_area,

        -- lot
        (
            SELECT COALESCE(s.lot_stock, 0)
            FROM stock s
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            LIMIT 1
        ) AS lot, 

        -- kgs other out
        (
            SELECT SUM(COALESCE(oo.kgs_other_out, 0))
            FROM other_out oo
            JOIN out_celup oc ON oc.id_out_celup = oo.id_out_celup
            JOIN schedule_celup sc ON sc.id_celup = oc.id_celup
            WHERE sc.no_model = master_order.no_model
            AND sc.kode_warna = material.kode_warna
            AND sc.item_type = material.item_type
        ) AS kgs_other_out,
         
        -- retur stock
        (
            SELECT SUM(COALESCE(r.kgs_retur, 0))
            FROM retur r
            JOIN kategori_retur kr ON r.kategori = kr.nama_kategori
            WHERE r.no_model = master_order.no_model
            AND r.kode_warna = material.kode_warna
            AND r.item_type = material.item_type
            AND kr.tipe_kategori = 'SIMPAN ULANG'
        ) AS retur_stock,

        -- retur titip
        (
            SELECT SUM(COALESCE(r.kgs_retur, 0))
            FROM retur r
            JOIN kategori_retur kr ON r.kategori = kr.nama_kategori
            WHERE r.no_model = master_order.no_model
            AND r.kode_warna = material.kode_warna
            AND r.item_type = material.item_type
            AND kr.tipe_kategori = 'BAHAN BAKU TITIP'
        ) AS retur_titip,

        -- dipinjam
        (
            SELECT SUM(COALESCE(hs.kgs, 0))
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            AND hs.keterangan = 'Pinjam Order'
        ) AS dipinjam,

        -- pindah order
        (
            SELECT SUM(COALESCE(hs.kgs, 0))
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            AND hs.keterangan = 'Pindah Order'
        ) AS pindah_order,

        -- cns pindah order
        (
            SELECT SUM(COALESCE(hs.cns, 0))
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            AND hs.keterangan = 'Pindah Order'
        ) AS cns_pindah_order,

        -- cluster
        (
            SELECT COALESCE(s.nama_cluster, 0)
            FROM stock s
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            LIMIT 1
        ) AS cluster, 

        -- tgl pindah
        (
            SELECT COALESCE(DATE(hs.created_at), 0)
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            LIMIT 1
        ) AS tgl_pindah,
         
        -- ket pindah
        (
            SELECT COALESCE(hs.keterangan, 0)
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            LIMIT 1
        ) AS ket_pindah,

        -- nomodel new pindah order
        (
            SELECT COALESCE(s_new.no_model, 0)
            FROM history_stock hs
            JOIN stock s_old ON s_old.id_stock = hs.id_stock_old   -- stock lama
            JOIN stock s_new ON s_new.id_stock = hs.id_stock_new   -- stock baru
            WHERE s_old.no_model = master_order.no_model
            AND s_old.kode_warna = material.kode_warna
            AND s_old.item_type = material.item_type
            LIMIT 1
        ) AS nomodel_new,

        -- admin pindah
        (
            SELECT COALESCE(hs.admin, 0)
            FROM history_stock hs
            JOIN stock s ON hs.id_stock_old = s.id_stock
            WHERE s.no_model = master_order.no_model
            AND s.kode_warna = material.kode_warna
            AND s.item_type = material.item_type
            LIMIT 1
        ) AS admin_pindah,
    ")
            ->join('material', 'material.id_order = master_order.id_order', 'left')
            ->join('master_material', 'material.item_type = master_material.item_type', 'left')
            ->where('master_order.no_model', $noModel);

        if (!empty($jenis)) {
            $builder->where('master_material.jenis', $jenis);
        }

        return $builder
            ->groupBy('master_order.no_model')
            ->groupBy('material.item_type')
            ->groupBy('material.kode_warna')
            ->orderBy('material.item_type, material.kode_warna', 'ASC')
            ->findAll();
    }

    public function getMaterial($id, $area)
    {
        $data = $this->select('no_model, buyer, delivery_awal, delivery_akhir, material.style_size, material.item_type, material.color, material.kode_warna, sum(material.kgs) as kg_mu, material.composition, material.gw, material.loss')
            ->join('material', 'material.id_order=master_order.id_order')
            ->where('master_order.id_order', $id)
            ->where('material.area', $area)
            ->where('material.composition !=', 0)
            ->where('material.gw !=', 0)
            ->where('material.qty_pcs !=', 0)
            ->where('material.loss !=', 0)
            ->where('material.kgs >', 0)
            ->groupBy(['material.item_type', 'material.kode_warna', 'material.style_size'])
            ->orderBy('material.item_type')
            ->findAll();
        // Susun data menjadi terstruktur
        $result = [];
        foreach ($data as $row) {
            $itemType = $row['item_type'];
            $kodeWarna = $row['kode_warna'];
            if (!isset($result[$itemType])) {
                $result[$itemType] = [
                    'item_type' => $itemType,
                    'kode_warna' => [],
                ];
            }
            if (!isset($result[$itemType]['kode_warna'][$kodeWarna])) {
                $result[$itemType]['kode_warna'][$kodeWarna] = [
                    'color' => $row['color'],
                    'style_size' => []
                ];
            }
            $result[$itemType]['kode_warna'][$kodeWarna]['style_size'][] = [
                'no_model' => $row['no_model'],
                'style_size' => $row['style_size'],
                'kg_mu' => $row['kg_mu'],
                'composition' => $row['composition'],
                'gw' => $row['gw'],
                'loss' => $row['loss'],
            ];
        }
        return $result;
    }
    public function getAllNoModel()
    {
        return $this->select('id_order, no_model')
            ->distinct()
            ->orderBy('no_model', 'ASC')
            ->findAll();
    }

    public function getBuyer()
    {
        return $this->select('buyer')
            ->groupBy('buyer')
            ->findAll();
    }

    public function getNoOrderByModel($noModel)
    {
        return $this->select('no_order')
            ->where('no_model', $noModel)
            ->first();
    }
    public function getNullDeliv()
    {
        return $this->select('no_model')
            ->where('delivery_awal', null)
            ->where('delivery_akhir', null)
            ->findAll();
    }
    public function updateDeliv($model, $body)
    {
        if ($body['unit'] == 'CJ') {
            $unit = 'CIJERAH';
        } elseif ($body['unit'] == 'MJ') {
            $unit = 'MAJALAYA';
        } else {
            $unit = 'Belum di Assign';
        }
        return  $this->where('no_model', $model)
            ->set('delivery_awal', $body['delivery_awal'])
            ->set('delivery_akhir', $body['delivery_akhir'])
            ->set('unit', $unit)
            ->update();
    }
    public function getNullMc()
    {
        return $this->select('master_order.id_order, no_model')
            ->join('material', 'master_order.id_order =material.id_order ', 'left')
            ->where('start_mc', null)
            ->where('lco_date !=', '0000-00-00')
            ->where('delivery_awal !=', null)
            ->where('delivery_akhir >=', '2025-08-30')
            ->notLike('material.area', 'Gedung')
            ->where('LENGTH(no_model) <=', 6, false) // tambahin false biar ga di-escape
            ->orderBy('RAND()')
            ->groupBy('master_order.id_order, master_order.no_model')
            ->limit(100)
            ->findAll();
    }
}
