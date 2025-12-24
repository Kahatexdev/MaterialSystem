<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleCelupModel extends Model
{
    protected $table            = 'schedule_celup';
    protected $primaryKey       = 'id_celup';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_celup',
        'id_mesin',
        'id_bon',
        'no_model',
        'item_type',
        'kode_warna',
        'warna',
        'start_mc',
        'kg_celup',
        'lot_urut',
        'lot_celup',
        'tanggal_schedule',
        'tanggal_bon',
        'tanggal_celup',
        'tanggal_bongkar',
        'tanggal_press_oven',
        'tanggal_tl',
        'tanggal_rajut_pagi',
        'tanggal_kelos',
        'tanggal_acc',
        'tanggal_reject',
        'tanggal_matching',
        'tanggal_perbaikan',
        'tanggal_teslab',
        'serah_terima_acc',
        'matching',
        'last_status',
        'ket_daily_cek',
        'ket_schedule',
        'po_plus',
        'user_cek_status',
        'admin',
        'created_at',
        'updated_at',
        'admin'
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

    public function getScheduleCelup()
    {
        return $this->table('schedule_celup')
            ->select('*, mesin_celup.no_mesin, sum(kg_celup) as total_kg')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut')
            ->findAll();
    }

    public function getScheduleDetails($machine, $date, $lot)
    {
        return $this->table('schedule_celup')
            ->select('schedule_celup.*, mesin_celup.no_mesin, sum(kg_celup) as total_kg, open_po.ket_celup, open_po.keterangan, open_po.id_induk')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->where('mesin_celup.no_mesin', $machine)
            ->where('schedule_celup.tanggal_schedule', $date)
            ->where('schedule_celup.lot_urut', $lot)
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut')
            ->groupBy('schedule_celup.id_celup')
            ->findAll();
    }

    public function getWeight($machine, $date, $lot)
    {
        return $this->table('schedule_celup')
            ->select('kg_celup')
            ->where('id_mesin', $machine)
            ->where('tanggal_schedule', $date)
            ->where('lot_urut', $lot)
            ->findAll();
    }

    public function saveSchedule($data)
    {
        return $this->table('schedule_celup')
            ->insertbatch($data);
    }

    public function getScheduleDetailsById($id)
    {
        return $this->table('schedule_celup')
            ->select('*, mesin_celup.no_mesin, sum(kg_celup) as total_kg')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('id_celup', $id)
            ->first();
    }

    public function getScheduleDetailsData($machine, $date, $lot)
    {
        return $this->select('schedule_celup.id_celup,sum(schedule_celup.kg_celup) as qty_celup,schedule_celup.item_type, schedule_celup.no_model, DATE(schedule_celup.start_mc) AS start_mc, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.last_status, schedule_celup.po_plus, DATE(schedule_celup.updated_at) as last_update,TIME(schedule_celup.updated_at) as jam_update,schedule_celup.user_cek_status as admin, schedule_celup.ket_schedule, open_po.keterangan, open_po.id_induk')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna')
            ->where('tanggal_schedule', $date)
            ->where('id_mesin', $machine)
            ->where('lot_urut', $lot)
            ->groupStart() // buka grouping WHERE
            ->where('last_status', 'scheduled')
            ->groupEnd() // tutup grouping WHERE
            ->groupBy('id_celup')
            ->findAll();
    }

    public function getTanggalCelup($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('tanggal_celup')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function getLotCelup($no_mesin, $tanggal_schedule)
    {
        return $this->table('schedule_celup')
            ->select('lot_celup')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function getKetDailyCek($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('ket_daily_cek')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function getNoModel($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('schedule_celup.no_model, master_order.no_model as master_no_model, master_order.id_order')
            ->where('schedule_celup.no_mesin', $no_mesin)
            ->where('schedule_celup.tanggal_schedule', $tanggal_schedule)
            ->where('schedule_celup.lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('master_order', 'master_order.id_order = schedule_celup.id_order')  // Fix join with correct table
            ->distinct()
            ->findAll();  // Use findAll() for multiple results
    }

    public function getScheduleCelupbyDate($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('schedule_celup')
            ->select('schedule_celup.*, mesin_celup.no_mesin, SUM(schedule_celup.kg_celup) as total_kg')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('tanggal_schedule >=', $startDate->format('Y-m-d'))
            ->where('tanggal_schedule <=', $endDate->format('Y-m-d'))
            ->whereIn('schedule_celup.last_status', ['scheduled', 'celup', 'reschedule', 'bon', 'bongkar']) // Filter berdasarkan last_status
            // ->whereIn('schedule_celup.last_status', ['scheduled', 'celup', 'reschedule', 'bon', 'bongkar', 'press_oven', 'tes_luntur', 'rajut', 'acc', 'reject', 'perbaikan', 'tes_lab']) // Filter berdasarkan last_status
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut');

        return $builder->get()->getResultArray();
    }

    public function getSchedule($filterTglSch = null, $filterTglSchsampai = null, $filterNoModel = null)
    {
        // Ambil tanggal 1 bulan lalu dalam format YYYY-MM-DD
        $lastMonth = date('Y-m-d', strtotime('1 month ago'));

        // Build query
        $builder = $this->builder()
            ->select('schedule_celup.*, schedule_celup.user_cek_status AS admin,
                  mesin_celup.no_mesin, schedule_celup.admin AS admin_sch,
                  IF(schedule_celup.po_plus = "0", schedule_celup.kg_celup, 0) AS qty_celup, 
                  IF(schedule_celup.po_plus = "1", schedule_celup.kg_celup, 0) AS qty_celup_plus,
                  open_po.kg_po')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin', 'left')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->where('schedule_celup.id_celup !=', null)
            ->where('schedule_celup.id_mesin !=', null);
        // Filter berdasarkan tanggal jika ada
        if ($filterTglSch && !$filterTglSchsampai) {
            $builder->where('schedule_celup.tanggal_schedule >=', $filterTglSch)
                ->where('schedule_celup.tanggal_schedule <=', date('Y-m-d')); // Hanya ambil data sampai hari ini
        } elseif ($filterTglSch && $filterTglSchsampai) {
            $builder->where('schedule_celup.tanggal_schedule >=', $filterTglSch)
                ->where('schedule_celup.tanggal_schedule <=', $filterTglSchsampai);
        } else {
            // Jika tidak ada filter tanggal, ambil data dari 1 bulan lalu sampai hari ini
            // $builder->where('schedule_celup.tanggal_schedule >=', $lastMonth)
            //     ->where('schedule_celup.tanggal_schedule <=', date('Y-m-d'));
        }

        // Filter berdasarkan no_model atau kode_warna
        if ($filterNoModel) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $filterNoModel)
                ->orLike('schedule_celup.kode_warna', $filterNoModel)
                ->orLike('schedule_celup.lot_celup', $filterNoModel)
                ->groupEnd();
        }

        // Filter berdasarkan last_status
        // $builder->where('schedule_celup.last_status !=', 'done');

        // Grouping untuk menghindari duplikasi
        $builder->groupBy([
            'schedule_celup.id_mesin',
            'schedule_celup.id_celup',
            'schedule_celup.tanggal_schedule',
            'schedule_celup.lot_urut'
        ]);

        // sortby order by created_at DESC 
        $builder->orderBy('schedule_celup.created_at', 'DESC');
        // $builder->limit(30);

        return $builder->get()->getResultArray();
    }


    public function getDataByIdCelup($id)
    {
        return $this->select('schedule_celup.*, sum(kg_celup) as qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus, mesin_celup.no_mesin')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('schedule_celup.id_celup', $id)
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut')
            ->findAll();
    }
    public function getScheduleDone()
    {
        return $this->select('schedule_celup.*, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus')
            ->where('last_status', 'done')
            ->groupBy('id_celup')
            ->findAll();
    }
    public function cekItemtypeandKodeWarna($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('item_type, kode_warna')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function cekSisaJatah($no_model, $item_type, $kode_warna)
    {
        $data = $this->select('
        SUM(schedule_celup.kg_celup) AS total_kg,
        material.id_order,
        material.qty_po,
        schedule_celup.item_type,
        schedule_celup.kode_warna,
        master_order.no_model
    ')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
            ->join(
                '(SELECT 
            SUM(material.kgs) AS qty_po, 
            id_order, 
            item_type, 
            kode_warna
          FROM material
          GROUP BY id_order, item_type, kode_warna) AS material',
                'material.id_order = master_order.id_order
         AND material.item_type = schedule_celup.item_type
         AND material.kode_warna = schedule_celup.kode_warna',
                'left'
            )
            ->where('schedule_celup.no_model', $no_model)
            ->where('schedule_celup.item_type', $item_type)
            ->where('schedule_celup.kode_warna', $kode_warna)
            // Ubah groupBy menjadi berdasarkan field yang unik per record
            ->groupBy(['schedule_celup.kode_warna'])
            ->findAll();

        if (!empty($data)) {
            return $data;
        }

        return null;
    }

    public function getCelupDoneAndComplain()
    {
        return $this
            ->select('id_celup,no_model, item_type, kode_warna, warna, lot_celup, kg_celup, po_plus')
            ->whereIn('last_status', ['done', 'complain'])
            // ->where('lot_celup IS NOT NULL')
            // ->where('last_status', 'complain')
            ->groupBy('id_celup')
            ->findAll();
    }
    public function getNoModelCreateBon()
    {
        return $this->select('no_model')->distinct()->orderBy('no_model', 'ASC');
    }
    public function getItemTypeByNoModel($noModel)
    {
        return $this->table('schedule_celup')
            ->select('item_type')
            ->where('no_model', $noModel)
            ->where('last_status', 'done')
            ->groupBy('item_type')
            ->findAll();
    }
    public function getKodeWarnaByNoModelDanItemType($noModel, $itemType)
    {
        return $this->table('schedule_celup')
            ->select('kode_warna')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->groupBy('kode_warna')
            ->findAll();
    }
    public function getWarnaByNoModelItemDanKode($noModel, $itemType, $kodeWarna)
    {
        return $this->table('schedule_celup')
            ->select('warna')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->groupBy('warna')
            ->first();
    }
    public function getIdCelupbyNoModelItemTypeKodeWarna($noModel, $itemType, $kodeWarna)
    {
        return $this->table('schedule_celup')
            ->select('id_celup')
            ->where('no_model', $noModel)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->groupBy('id_celup')
            ->first();
    }
    public function getScheduleBon($id_bon)
    {
        return $this->select('schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.id_celup')
            ->join('out_celup', 'out_celup.id_celup=schedule_celup.id_celup')
            ->where('out_celup.id_bon', $id_bon)
            ->groupBy('schedule_celup.id_celup')
            ->findAll();
    }
    public function schedulePerArea($model, $itemType, $kodeWarna, $search)
    {

        $db = \Config\Database::connect();

        // Subquery: Hitung stok berdasarkan kombinasi model/type/warna
        $stockSub = $db->table('stock')
            ->select('no_model, item_type, kode_warna, SUM(kgs_stock_awal + kgs_in_out) AS kg_stock, admin')
            ->groupBy(['no_model', 'item_type', 'kode_warna'])
            ->getCompiledSelect();

        // Subquery: Hitung po_tambahan per model/type/warna
        // Subquery: po_tambahan
        $poTambahanSub = $db->table('po_tambahan') // hilangkan alias pt di sini
            ->select('mo.no_model, m.item_type, m.kode_warna, SUM(po_tambahan.poplus_mc_kg + po_tambahan.plus_pck_kg) AS total_po_tambahan')
            ->join('material m', 'm.id_material = po_tambahan.id_material')
            ->join('master_order mo', 'mo.id_order = m.id_order')
            ->groupBy(['mo.no_model', 'm.item_type', 'm.kode_warna'])
            ->getCompiledSelect();

        // Main query builder
        $builder = $db->table('schedule_celup AS sc')
            ->select([
                'sc.id_celup',
                'sc.no_model',
                'sc.item_type',
                'sc.kode_warna',
                'sc.kg_celup',
                'sc.lot_urut',
                'sc.lot_celup',
                'sc.tanggal_schedule',
                'sc.tanggal_bon',
                'sc.tanggal_celup',
                'sc.tanggal_bongkar',
                'sc.tanggal_press_oven',
                'sc.tanggal_tl',
                'sc.tanggal_rajut_pagi',
                'sc.tanggal_kelos',
                'sc.serah_terima_acc',
                'sc.tanggal_acc',
                'sc.tanggal_reject',
                'sc.tanggal_matching',
                'sc.tanggal_perbaikan',
                'sc.tanggal_teslab',
                'sc.last_status',
                'sc.ket_daily_cek',
                'sc.ket_schedule',
                'sc.po_plus',
                'COALESCE(st.kg_stock, 0) AS kg_stock',
                'COALESCE(pt.total_po_tambahan, 0) AS total_po_tambahan',
                'mm.jenis'
            ])
            ->join("($stockSub) AS st", 'st.no_model = sc.no_model AND st.item_type = sc.item_type AND st.kode_warna = sc.kode_warna', 'left')
            ->join("($poTambahanSub) AS pt", 'pt.no_model = sc.no_model AND pt.item_type = sc.item_type AND pt.kode_warna = sc.kode_warna', 'left')
            ->join('master_material AS mm', 'mm.item_type = sc.item_type', 'left')
            ->like('sc.no_model', $model)
            ->where('sc.item_type', $itemType)
            ->where('sc.kode_warna', $kodeWarna);

        // Filter pencarian
        if (!empty($search)) {
            $builder->groupStart()
                ->like('sc.no_model', $search)
                ->orLike('sc.kode_warna', $search)
                ->orLike('sc.tanggal_schedule', $search)
                ->orLike('sc.lot_celup', $search)
                ->groupEnd();
        }

        // Grouping agar tidak terjadi duplikasi id_celup
        $builder->groupBy('sc.id_celup');

        $query = $builder->get();

        if (!$query) {
            log_message('error', 'schedulePerArea query error: ' . $db->getLastQuery());
            return [];
        }

        return $query->getResultArray();
    }

    // public function getDataComplain()
    // {
    //     return $this->select('schedule_celup.id_celup, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.lot_celup, mesin_celup.no_mesin, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus, schedule_celup.ket_daily_cek,schedule_celup.lot_urut, schedule_celup.tanggal_schedule, out_celup.id_bon, bon_celup.tgl_datang, bon_celup.no_surat_jalan, bon_celup.detail_sj')
    //         ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
    //         ->join('out_celup', 'out_celup.no_model = schedule_celup.no_model', 'left')
    //         ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon')
    //         ->where('schedule_celup.last_status', 'complain')
    //         ->groupBy('schedule_celup.id_celup')
    //         ->groupBy('bon_celup.id_bon')
    //         ->findAll();
    // }

    public function getDataComplain($tglSch = null, $tglKirim = null, $noModel = null)
    {
        $builder = $this->select('schedule_celup.id_celup, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.lot_celup, mesin_celup.no_mesin, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus, schedule_celup.ket_daily_cek,schedule_celup.lot_urut, schedule_celup.tanggal_schedule, out_celup.id_bon, bon_celup.tgl_datang, bon_celup.no_surat_jalan, bon_celup.detail_sj')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('out_celup', 'out_celup.no_model = schedule_celup.no_model', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon')
            ->where('schedule_celup.last_status', 'complain');
        if (!empty($tglSch)) {
            $builder = $this->where('schedule_celup.tanggal_schedule', $tglSch);
        }
        if (!empty($tglKirim)) {
            $builder = $this->where('bon_celup.tgl_datang', $tglKirim);
        }
        if (!empty($noModel)) {
            $builder = $builder->groupStart()
                ->where('schedule_celup.no_model', $noModel)
                ->orLike('schedule_celup.kode_warna', $noModel)
                ->groupEnd();
        }
        return $builder->groupBy('bon_celup.id_bon')
            ->groupBy('bon_celup.id_bon')
            ->findAll();
    }

    public function countStatusScheduled()
    {
        return $this->select('COUNT(id_celup) as total_scheduled')
            ->where('last_status', 'scheduled') // Sesuaikan last status jika perlu
            ->where('DATE(tanggal_schedule)', date('Y-m-d'))
            ->first();
    }
    public function countStatusReschedule()
    {
        return $this->select('COUNT(id_celup) as total_reschedule')
            ->where('last_status', 'reschedule') // Sesuaikan last status jika perlu
            ->first();
    }
    public function countStatusDone()
    {
        return $this->select('COUNT(id_celup) as total_done')
            ->where('last_status', 'done')
            ->where('DATE(tanggal_kelos)', date('Y-m-d'))
            ->first();
    }
    public function countStatusRetur()
    {
        return $this->select('COUNT(id_celup) as total_retur')
            ->where('last_status', 'retur') // Sesuaikan last status jika perlu
            ->first();
    }

    public function getMesinKapasitasHariIni()
    {
        return $this->db->table('mesin_celup')
            ->select('mesin_celup.no_mesin,  (mesin_celup.max_caps * mesin_celup.jml_lot) AS max_caps, COALESCE(SUM(schedule_celup.kg_celup), 0) as kapasitas_terpakai')
            ->join('schedule_celup', 'schedule_celup.id_mesin = mesin_celup.id_mesin AND schedule_celup.tanggal_schedule = CURDATE()', 'left')
            ->groupBy('mesin_celup.no_mesin, mesin_celup.max_caps,mesin_celup.jml_lot')
            ->get()->getResultArray();
    }

    public function getFilterSchBenang($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    {
        // 1) Normalize to full datetime
        if (strlen($tanggal_awal) === 10) {
            $tanggal_awal .= ' 00:00:00';
        }
        if (strlen($tanggal_akhir) === 10) {
            $tanggal_akhir .= ' 23:59:59';
        }

        $db = \Config\Database::connect();

        $datangSub = $db->table('pemasukan')
            ->select("
            out_celup.no_model,
            schedule_celup.item_type,
            schedule_celup.kode_warna,
            SUM(out_celup.kgs_kirim) AS kgs_datang,
            GROUP_CONCAT(DISTINCT DATE_FORMAT(bon_celup.tgl_datang, '%d-%m-%Y') ORDER BY bon_celup.tgl_datang SEPARATOR ' / ') AS tgl_datang
        ")
            ->join('out_celup',     'out_celup.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup   = out_celup.id_celup', 'left')
            ->join('bon_celup', 'bon_celup.id_bon   = out_celup.id_bon')
            ->groupBy([
                'out_celup.no_model',
                'schedule_celup.item_type',
                'schedule_celup.kode_warna'
            ])
            ->getCompiledSelect(false);

        $poPlus = $db->table('po_tambahan')
            ->select('SUM(COALESCE(po_tambahan.poplus_mc_kg,0) + COALESCE(po_tambahan.plus_pck_kg,0)) AS total_poplus, po_tambahan.id_material,
             material.item_type, material.kode_warna, material.color, material.id_order')
            ->join('material', 'material.id_material = po_tambahan.id_material')
            ->where('po_tambahan.status', 'approved')
            ->groupBy(['po_tambahan.id_material', 'po_tambahan.status', 'po_tambahan.admin', 'material.item_type', 'material.kode_warna', 'material.color', 'material.id_order'])
            ->getCompiledSelect(false);

        $stokAwal = $db->table('stock')
            ->select('SUM(stock.kgs_stock_awal) AS kgs_stock_awal, stock.no_model, stock.item_type, stock.kode_warna, stock.warna')
            ->groupBy(['stock.no_model', 'stock.item_type', 'stock.kode_warna', 'stock.warna'])
            ->getCompiledSelect(false);

        // 3) Main query on schedule_celup
        $builder = $db->table('schedule_celup')
            ->select([
                'schedule_celup.*',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'master_order.lco_date',
                'mesin_celup.no_mesin',
                'mesin_celup.ket_mesin',
                'master_material.jenis',
                'datang_sub.kgs_datang',
                'datang_sub.tgl_datang',
                'po_plus_sub.total_poplus',
                'stok_awal_sub.kgs_stock_awal',
            ])
            ->join('master_order',    'master_order.no_model       = schedule_celup.no_model')
            ->join('master_material', 'master_material.item_type   = schedule_celup.item_type')
            ->join('mesin_celup',     'mesin_celup.id_mesin        = schedule_celup.id_mesin')
            ->join(
                "({$datangSub}) AS datang_sub",
                'datang_sub.no_model = schedule_celup.no_model
         AND datang_sub.item_type = schedule_celup.item_type
         AND datang_sub.kode_warna = schedule_celup.kode_warna',
                'left'
            )
            ->join(
                "({$poPlus}) AS po_plus_sub",
                'po_plus_sub.item_type   = schedule_celup.item_type
             AND po_plus_sub.kode_warna = schedule_celup.kode_warna
             AND po_plus_sub.color      = schedule_celup.warna
             AND po_plus_sub.id_order   = master_order.id_order',
                'left'
            )
            ->join(
                "({$stokAwal}) AS stok_awal_sub",
                'stok_awal_sub.no_model   = schedule_celup.no_model
             AND stok_awal_sub.item_type = schedule_celup.item_type
             AND stok_awal_sub.kode_warna = schedule_celup.kode_warna
             AND stok_awal_sub.warna      = schedule_celup.warna',
                'left'
            )
            ->where('master_material.jenis', 'BENANG');

        // 4) Optional keyword filter
        if (!empty($key)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->groupEnd();
        }

        // 5) Optional exact schedule-date filter
        if (!empty($tanggal_schedule)) {
            $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
        }

        if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
                ->where("schedule_celup.start_mc <=", $tanggal_akhir);
        }

        $builder->groupBy('schedule_celup.id_celup');

        return $builder->get()->getResult();
    }

    public function getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    {
        // Format ke datetime jika belum
        if (strlen($tanggal_awal) === 10) {
            $tanggal_awal .= ' 00:00:00';
        }
        if (strlen($tanggal_akhir) === 10) {
            $tanggal_akhir .= ' 23:59:59';
        }

        $db = \Config\Database::connect();

        // Subquery: summary material per item_type + kode_warna + color
        $materialSubquery = $db->table('material')
            ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
            ->groupBy(['id_order', 'item_type', 'kode_warna', 'color'])
            ->getCompiledSelect(false);

        // Subquery: handle gabungan -> ambil anak2 dari open_po
        $openPoSubquery = $db->table('open_po anak')
            ->select('anak.no_model, anak.kg_po, induk.no_model AS induk_model, induk.item_type AS induk_item_type, induk.kode_warna AS induk_kode_warna')
            ->join('open_po induk', 'anak.id_induk = induk.id_po')
            ->getCompiledSelect(false);

        $datangSub = $db->table('pemasukan')
            ->select("
        schedule_celup.no_model AS no_model_schedule,
        out_celup.no_model AS no_model_out,
        schedule_celup.item_type,
        schedule_celup.kode_warna,
        SUM(out_celup.kgs_kirim) AS kgs_datang,
        GROUP_CONCAT(DISTINCT DATE_FORMAT(bon_celup.tgl_datang, '%d-%m-%Y') 
            ORDER BY bon_celup.tgl_datang SEPARATOR ' / ') AS tgl_datang
    ")
            ->join('out_celup', 'out_celup.id_out_celup = pemasukan.id_out_celup', 'left')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon', 'left')
            ->groupBy([
                'schedule_celup.no_model',
                'out_celup.no_model',
                'schedule_celup.item_type',
                'schedule_celup.kode_warna'
            ])
            ->getCompiledSelect(false);

        $poPlus = $db->table('po_tambahan')
            ->select('SUM(COALESCE(po_tambahan.poplus_mc_kg,0) + COALESCE(po_tambahan.plus_pck_kg,0)) AS total_poplus, po_tambahan.id_material,
             material.item_type, material.kode_warna, material.color, material.id_order')
            ->join('material', 'material.id_material = po_tambahan.id_material')
            ->where('po_tambahan.status', 'approved')
            ->groupBy(['po_tambahan.id_material', 'po_tambahan.status', 'po_tambahan.admin', 'material.item_type', 'material.kode_warna', 'material.color', 'material.id_order'])
            ->getCompiledSelect(false);

        $stokAwal = $db->table('stock')
            ->select('SUM(stock.kgs_stock_awal) AS kgs_stock_awal,stock.item_type, stock.kode_warna, stock.warna')
            ->groupBy(['stock.item_type', 'stock.kode_warna', 'stock.warna'])
            ->getCompiledSelect(false);

        // Main builder
        $builder = $db->table('schedule_celup')
            ->select([
                'schedule_celup.*',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'master_order.lco_date',
                'mesin_celup.no_mesin',
                'mesin_celup.ket_mesin',
                'master_material.jenis',
                'material_summary.total_kgs',
                'open_po_anak.no_model AS no_model_anak',
                'open_po_anak.kg_po AS kg_po_anak',
                'datang_sub.kgs_datang AS kgs_datang',
                'datang_sub.tgl_datang',
                'po_plus_sub.total_poplus',
                'stok_awal_sub.kgs_stock_awal',
            ])
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join("({$openPoSubquery}) AS open_po_anak", 'open_po_anak.induk_model = schedule_celup.no_model AND open_po_anak.induk_item_type = schedule_celup.item_type AND open_po_anak.induk_kode_warna = schedule_celup.kode_warna', 'left')
            ->join('master_order', 'master_order.no_model = COALESCE(open_po_anak.no_model, schedule_celup.no_model)')
            ->join(
                "({$materialSubquery}) AS material_summary",
                'material_summary.item_type = schedule_celup.item_type 
             AND material_summary.kode_warna = schedule_celup.kode_warna 
             AND material_summary.color = schedule_celup.warna 
             AND material_summary.id_order = master_order.id_order',
                'left'
            )
            ->join(
                "({$datangSub}) AS datang_sub",
                "datang_sub.no_model_schedule = schedule_celup.no_model
     AND datang_sub.item_type = schedule_celup.item_type
     AND datang_sub.kode_warna = schedule_celup.kode_warna
     AND datang_sub.no_model_out = COALESCE(open_po_anak.no_model, schedule_celup.no_model)",
                'left'
            )
            ->join(
                "({$poPlus}) AS po_plus_sub",
                'po_plus_sub.item_type   = schedule_celup.item_type
             AND po_plus_sub.kode_warna = schedule_celup.kode_warna
             AND po_plus_sub.color      = schedule_celup.warna
             AND po_plus_sub.id_order   = master_order.id_order',
                'left'
            )
            ->join(
                "({$stokAwal}) AS stok_awal_sub",
                'stok_awal_sub.item_type = schedule_celup.item_type
             AND stok_awal_sub.kode_warna = schedule_celup.kode_warna
             AND stok_awal_sub.warna      = schedule_celup.warna',
                'left'
            )
            ->where('master_material.jenis', 'NYLON');

        if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
                ->where("schedule_celup.start_mc <=", $tanggal_akhir);
        }

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->groupEnd();
        }

        if (!empty($tanggal_schedule)) {
            $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
        }

        return $builder->get()->getResult();
    }


    // public function getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    // {
    //     // Format ke datetime jika belum
    //     if (strlen($tanggal_awal) === 10) {
    //         $tanggal_awal .= ' 00:00:00';
    //     }
    //     if (strlen($tanggal_akhir) === 10) {
    //         $tanggal_akhir .= ' 23:59:59';
    //     }

    //     $db = \Config\Database::connect();

    //     // Subquery: summary material per item_type + kode_warna + color
    //     $materialSubquery = $db->table('material')
    //         ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
    //         ->groupBy(['id_order', 'item_type', 'kode_warna', 'color'])
    //         ->getCompiledSelect(false);

    //     //
    //     // Subquery A: gabungkan rows open_po per (id_induk, no_model) => sum_kg per anak
    //     // (equivalent to: SELECT id_induk, no_model, SUM(kg_po) AS sum_kg FROM open_po GROUP BY id_induk, no_model)
    //     //
    //     $openPoPerChild = $db->table('open_po')
    //         ->select('id_induk, no_model, SUM(kg_po) AS sum_kg')
    //         ->groupBy(['id_induk', 'no_model'])
    //         ->getCompiledSelect(false);

    //     //
    //     // Subquery B: dari hasil A, join ke induk dan aggregate per induk:
    //     // - anak_models_detail  => "no_model::sum_kg||no2::sum_kg2..."
    //     // - child_no_model       => anak dengan sum_kg terbesar (representative) -- tersedia tapi TIDAK dipakai untuk join
    //     // - total_kg_po          => SUM(sum_kg) per induk (unique per anak)
    //     //
    //     $openPoSubquery = $db->table("({$openPoPerChild}) t")
    //         ->select("
    //         induk.no_model AS induk_model,
    //         GROUP_CONCAT(CONCAT(t.no_model, '::', t.sum_kg) SEPARATOR '||') AS anak_models_detail,
    //         SUBSTRING_INDEX(GROUP_CONCAT(t.no_model ORDER BY t.sum_kg DESC SEPARATOR ','), ',', 1) AS child_no_model,
    //         SUM(t.sum_kg) AS total_kg_po
    //     ")
    //         ->join('open_po induk', 't.id_induk = induk.id_po')
    //         ->groupBy('induk.no_model')
    //         ->getCompiledSelect(false);

    //     // Main builder
    //     $builder = $db->table('schedule_celup')
    //         ->select([
    //             'schedule_celup.*',
    //             'master_order.delivery_awal',
    //             'master_order.delivery_akhir',
    //             'master_order.lco_date',
    //             'mesin_celup.no_mesin',
    //             'mesin_celup.ket_mesin',
    //             'master_material.jenis',
    //             'material_summary.total_kgs',
    //             'open_po_anak.anak_models_detail',
    //             'open_po_anak.total_kg_po',
    //             'open_po_anak.child_no_model' // tersedia jika mau dipakai di view
    //         ])
    //         ->join('mesin_celup',     'mesin_celup.id_mesin        = schedule_celup.id_mesin')
    //         ->join('master_material', 'master_material.item_type   = schedule_celup.item_type')

    //         // join ke derived open_po yang sudah aggregated per induk (left karena tidak selalu ada)
    //         ->join("({$openPoSubquery}) AS open_po_anak", 'open_po_anak.induk_model = schedule_celup.no_model', 'left')

    //         // JOIN master_order HANYA untuk no_model yang BUKAN gabungan (tidak diawali "POGABUNGAN ")
    //         // note: kondisi join dicantumkan pada ON clause agar baris gabungan tetap muncul tanpa master_order cols
    //         ->join(
    //             'master_order',
    //             "master_order.no_model = schedule_celup.no_model",
    //             'left'
    //         )

    //         // material_summary bergantung pada master_order.id_order -> left join
    //         ->join(
    //             "({$materialSubquery}) AS material_summary",
    //             'material_summary.item_type   = schedule_celup.item_type
    //          AND material_summary.kode_warna = schedule_celup.kode_warna
    //          AND material_summary.color      = schedule_celup.warna
    //          AND material_summary.id_order   = master_order.id_order',
    //             'left'
    //         )
    //         ->where('master_material.jenis', 'NYLON')
    //         ->orderBy('schedule_celup.id_celup', 'DESC');

    //     if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    //         $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
    //             ->where("schedule_celup.start_mc <=", $tanggal_akhir);
    //     }

    //     if (!empty($key)) {
    //         $builder->groupStart()
    //             ->like('schedule_celup.no_model', $key)
    //             ->orLike('schedule_celup.kode_warna', $key)
    //             ->groupEnd();
    //     }

    //     if (!empty($tanggal_schedule)) {
    //         $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
    //     }

    //     return $builder->get()->getResult();
    // }

    // public function getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    // {
    //     // Format ke datetime jika belum
    //     if (strlen($tanggal_awal) === 10) {
    //         $tanggal_awal .= ' 00:00:00';
    //     }
    //     if (strlen($tanggal_akhir) === 10) {
    //         $tanggal_akhir .= ' 23:59:59';
    //     }

    //     $db = \Config\Database::connect();

    //     // Subquery: summary material per item_type + kode_warna + color
    //     $materialSubquery = $db->table('material')
    //         ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
    //         ->groupBy(['id_order', 'item_type', 'kode_warna', 'color'])
    //         ->getCompiledSelect(false);

    //     // Subquery: ambil semua anak per induk sebagai string "no_model::kg_po" dipisah "||"
    //     // + ambil 1 child_no_model representative (MIN) + total kg semua anak
    //     $openPoSubquery = $db->table('open_po anak')
    //         ->select("
    //         induk.no_model AS induk_model,
    //         GROUP_CONCAT(CONCAT(anak.no_model, '::', anak.kg_po) SEPARATOR '||') AS anak_models_detail,
    //         MIN(anak.no_model) AS child_no_model,
    //     ")
    //         ->join('open_po induk', 'anak.id_induk = induk.id_po')
    //         ->groupBy('induk.no_model')
    //         ->getCompiledSelect(false);

    //     // Main builder
    //     $builder = $db->table('schedule_celup')
    //         ->select([
    //             'schedule_celup.*',
    //             'master_order.delivery_awal',
    //             'master_order.delivery_akhir',
    //             'master_order.lco_date',
    //             'mesin_celup.no_mesin',
    //             'mesin_celup.ket_mesin',
    //             'master_material.jenis',
    //             'material_summary.total_kgs',
    //             'open_po_anak.anak_models_detail',
    //         ])
    //         ->join('mesin_celup',     'mesin_celup.id_mesin        = schedule_celup.id_mesin')
    //         ->join('master_material', 'master_material.item_type   = schedule_celup.item_type')
    //         // join ke derived open_po yang sudah aggregated per induk
    //         ->join("({$openPoSubquery}) AS open_po_anak", 'open_po_anak.induk_model = schedule_celup.no_model', 'left')
    //         // join master_order: pakai child_no_model jika ada, kalau tidak pakai schedule_celup.no_model
    //         ->join('master_order', 'master_order.no_model = COALESCE(open_po_anak.child_no_model, schedule_celup.no_model)')
    //         ->join(
    //             "({$materialSubquery}) AS material_summary",
    //             'material_summary.item_type   = schedule_celup.item_type
    //          AND material_summary.kode_warna = schedule_celup.kode_warna
    //          AND material_summary.color      = schedule_celup.warna
    //          AND material_summary.id_order   = master_order.id_order',
    //             'left'
    //         )
    //         ->where('master_material.jenis', 'NYLON')
    //         ->orderBy('schedule_celup.id_celup', 'DESC');

    //     if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    //         $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
    //             ->where("schedule_celup.start_mc <=", $tanggal_akhir);
    //     }

    //     if (!empty($key)) {
    //         $builder->groupStart()
    //             ->like('schedule_celup.no_model', $key)
    //             ->orLike('schedule_celup.kode_warna', $key)
    //             ->groupEnd();
    //     }

    //     if (!empty($tanggal_schedule)) {
    //         $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
    //     }

    //     return $builder->get()->getResult();
    // }


    // public function getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    // {
    //     if (strlen($tanggal_awal) === 10) {
    //         $tanggal_awal .= ' 00:00:00';
    //     }
    //     if (strlen($tanggal_akhir) === 10) {
    //         $tanggal_akhir .= ' 23:59:59';
    //     }

    //     $db = \Config\Database::connect();

    //     // Subquery: summary material
    //     $materialSubquery = $db->table('material')
    //         ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
    //         ->groupBy(['id_order', 'item_type', 'kode_warna', 'color'])
    //         ->getCompiledSelect(false);

    //     // Subquery: anak open_po, tapi group by induk biar ga duplikat
    //     $openPoSubquery = $db->table('open_po anak')
    //         ->select('induk.no_model AS induk_model, GROUP_CONCAT(anak.no_model) AS anak_models, SUM(anak.kg_po) AS total_kg_po')
    //         ->join('open_po induk', 'anak.id_induk = induk.id_po')
    //         ->groupBy('induk.no_model')
    //         ->getCompiledSelect(false);

    //     $builder = $db->table('schedule_celup')
    //         ->select([
    //             'schedule_celup.*',
    //             'master_order.delivery_awal',
    //             'master_order.delivery_akhir',
    //             'master_order.lco_date',
    //             'mesin_celup.no_mesin',
    //             'mesin_celup.ket_mesin',
    //             'master_material.jenis',
    //             'material_summary.total_kgs',
    //             'open_po_anak.total_kg_po',
    //             'open_po_anak.anak_models'
    //         ])
    //         ->join('mesin_celup',     'mesin_celup.id_mesin        = schedule_celup.id_mesin')
    //         ->join('master_material', 'master_material.item_type   = schedule_celup.item_type')
    //         ->join("({$openPoSubquery}) AS open_po_anak", 'open_po_anak.induk_model = schedule_celup.no_model', 'left')
    //         ->join('master_order', "(
    //             (open_po_anak.anak_models IS NOT NULL AND FIND_IN_SET(master_order.no_model, open_po_anak.anak_models))
    //             OR (open_po_anak.anak_models IS NULL AND master_order.no_model = schedule_celup.no_model)
    //         )")
    //         ->join(
    //             "({$materialSubquery}) AS material_summary",
    //             'material_summary.item_type   = schedule_celup.item_type
    //      AND material_summary.kode_warna = schedule_celup.kode_warna
    //      AND material_summary.color      = schedule_celup.warna
    //      AND material_summary.id_order   = master_order.id_order',
    //             'left'
    //         )
    //         ->where('master_material.jenis', 'NYLON');

    //     if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    //         $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
    //             ->where("schedule_celup.start_mc <=", $tanggal_akhir);
    //     }

    //     if (!empty($key)) {
    //         $builder->groupStart()
    //             ->like('schedule_celup.no_model', $key)
    //             ->orLike('schedule_celup.kode_warna', $key)
    //             ->groupEnd();
    //     }

    //     if (!empty($tanggal_schedule)) {
    //         $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
    //     }

    //     return $builder->get()->getResult();
    // }


    // public function getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key = null, $tanggal_schedule = null)
    // {
    //     // Format ke datetime jika belum
    //     if (strlen($tanggal_awal) === 10) {
    //         $tanggal_awal .= ' 00:00:00';
    //     }
    //     if (strlen($tanggal_akhir) === 10) {
    //         $tanggal_akhir .= ' 23:59:59';
    //     }

    //     $db = \Config\Database::connect();

    //     // Subquery: summary material per item_type + kode_warna + color
    //     $materialSubquery = $db->table('material')
    //         ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
    //         ->groupBy(['id_order', 'item_type', 'kode_warna', 'color'])
    //         ->getCompiledSelect(false);

    //     // Main builder
    //     $builder = $db->table('schedule_celup')
    //         ->select([
    //             'schedule_celup.*',
    //             'master_order.delivery_awal',
    //             'master_order.delivery_akhir',
    //             'master_order.lco_date',
    //             'mesin_celup.no_mesin',
    //             'mesin_celup.ket_mesin',
    //             'master_material.jenis',
    //             'material_summary.total_kgs',
    //         ])
    //         ->join('master_order',    'master_order.no_model       = schedule_celup.no_model')
    //         ->join('master_material', 'master_material.item_type   = schedule_celup.item_type')
    //         ->join('mesin_celup',     'mesin_celup.id_mesin        = schedule_celup.id_mesin')
    //         ->join(
    //             "({$materialSubquery}) AS material_summary",
    //             'material_summary.item_type   = schedule_celup.item_type
    //          AND material_summary.kode_warna = schedule_celup.kode_warna
    //          AND material_summary.color      = schedule_celup.warna
    //          AND material_summary.id_order   = master_order.id_order',
    //             'left'
    //         )
    //         ->where('master_material.jenis', 'NYLON');

    //     if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
    //         $builder->where("schedule_celup.start_mc >=", $tanggal_awal)
    //             ->where("schedule_celup.start_mc <=", $tanggal_akhir);
    //     }

    //     // Cek apakah ada input key untuk pencarian
    //     if (!empty($key)) {
    //         $builder->groupStart()
    //             ->like('schedule_celup.no_model', $key)
    //             ->orLike('schedule_celup.kode_warna', $key)
    //             ->groupEnd();
    //     }

    //     if (!empty($tanggal_schedule)) {
    //         $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
    //     }

    //     return $builder->get()->getResult();
    // }

    public function schTerdekat()
    {
        $today = date('Y-m-d');
        $fiveDaysLater = date('Y-m-d', strtotime('+5 days'));

        return $this->select('no_model, item_type, kode_warna, warna, tanggal_schedule, mesin_celup.no_mesin')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('last_status', 'scheduled')
            ->where('tanggal_schedule >=', $today)
            ->where('tanggal_schedule <=', $fiveDaysLater)
            ->orderBy('tanggal_schedule', 'ASC')
            ->limit(5)
            ->findAll();
    }
    public function getIdCelups($data)
    {
        $row = $this->select('id_celup')
            ->where('no_model', $data['no_model'])
            ->where('item_type', $data['item_type'])
            ->where('kode_warna', $data['kode_warna'])
            ->where('lot_celup', $data['lot_retur'])
            ->first();
        return $row ? (int)$row['id_celup'] : null;
    }

    public function getFilterSchBenangNylon($tglAwal, $tglAkhir)
    {
        $builder = $this->select('schedule_celup.*, mesin_celup.no_mesin, mesin_celup.min_caps, mesin_celup.max_caps, open_po.ket_celup, master_material.jenis, master_order.delivery_awal')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model')
            ->whereIn('master_material.jenis', ['NYLON', 'BENANG'])
            ->whereIn('mesin_celup.ket_mesin', ['NYLON', 'BENANG'])
            ->groupBy('schedule_celup.id_celup')
            ->orderBy('schedule_celup.tanggal_schedule', 'ASC')
            ->orderBy('mesin_celup.no_mesin', 'ASC');

        if (!empty($tglAwal) && !empty($tglAkhir)) {
            $builder->where('schedule_celup.tanggal_schedule >=', $tglAwal)
                ->where('schedule_celup.tanggal_schedule <=', $tglAkhir);
        } elseif (!empty($tglAwal)) {
            $builder->where('schedule_celup.tanggal_schedule >=', $tglAwal);
        } elseif (!empty($tglAkhir)) {
            $builder->where('schedule_celup.tanggal_schedule <=', $tglAkhir);
        }

        return $builder->findAll();
    }

    public function getFilterSchWeekly($tglAwal, $tglAkhir, $jenis)
    {
        $db = \Config\Database::connect();
        $session = \Config\Services::session();
        $role = $session->get('role');

        // --- SUBQUERY 1: Hitung total per no_model (kg_per_model)
        $sub = $db->table('schedule_celup s')
            ->select("
            s.item_type,
            s.kode_warna,
            s.warna,
            s.tanggal_schedule,
            s.id_mesin,
            s.id_bon,
            s.no_po,
            s.lot_urut,
            s.lot_celup,
            s.start_mc,
            s.po_plus,
            s.ket_schedule,
            s.no_model,
            s.last_status,
            SUM(s.kg_celup) AS kg_per_model,
            MIN(s.id_celup) AS id_celup_per_model
        ")
            ->where('s.tanggal_schedule >=', $tglAwal)
            ->where('s.tanggal_schedule <=', $tglAkhir);
        if ($role !== 'celup') {
            $sub->where('s.last_status', 'scheduled');
        }
        // ->where('s.last_status', 'scheduled')
        $sub = $sub->groupBy('
        s.item_type, 
        s.kode_warna, 
        s.warna, 
        s.tanggal_schedule, 
        s.id_mesin, 
        s.lot_urut, 
        s.no_model, 
        s.id_bon, 
        s.no_po, 
        s.lot_celup, 
        s.start_mc, 
        s.po_plus, 
        s.ket_schedule, 
        s.last_status
    ')
            ->getCompiledSelect();

        // --- SUBQUERY 2: Agregasi ke level mesin + lot
        $sub2 = $db->table("({$sub}) AS s")
            ->select("
            s.item_type,
            s.kode_warna,
            s.warna,
            s.tanggal_schedule,
            s.id_mesin,
            s.lot_urut,
            s.lot_celup,
            s.start_mc,
            s.po_plus,
            s.ket_schedule,
            s.last_status,
            SUM(s.kg_per_model) AS kg_celup,
            GROUP_CONCAT(DISTINCT s.no_model ORDER BY s.no_model SEPARATOR ', ') AS no_model,
            GROUP_CONCAT(CONCAT(s.no_model, '=', REPLACE(FORMAT(s.kg_per_model, 2), ',', '')) ORDER BY s.no_model SEPARATOR ', ') AS no_model_detail,
            COUNT(*) AS cnt,
            MIN(s.id_celup_per_model) AS id_celup
        ")
            ->groupBy('
            s.item_type, 
            s.kode_warna, 
            s.warna, 
            s.tanggal_schedule, 
            s.id_mesin, 
            s.lot_urut, 
            s.lot_celup, 
            s.start_mc, 
            s.po_plus, 
            s.ket_schedule, 
            s.last_status
        ')
            ->getCompiledSelect();

        // --- QUERY UTAMA
        $builder = $db->table("({$sub2}) AS schedule_celup")
            ->select("
            mesin_celup.no_mesin,
            schedule_celup.id_celup,
            schedule_celup.no_model,
            schedule_celup.no_model_detail,
            schedule_celup.item_type,
            schedule_celup.kode_warna,
            schedule_celup.warna,
            schedule_celup.kg_celup,
            schedule_celup.start_mc,
            schedule_celup.lot_urut,
            schedule_celup.lot_celup,
            schedule_celup.tanggal_schedule,
            schedule_celup.po_plus,
            schedule_celup.ket_schedule,
            mesin_celup.id_mesin,
            mesin_celup.min_caps,
            mesin_celup.max_caps,
            master_material.jenis,
            MAX(COALESCE(master_order.delivery_awal, parent_master.delivery_awal)) AS delivery_awal,
            schedule_celup.last_status
        ")
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join(
                'open_po open_po_child',
                "open_po_child.no_model = schedule_celup.no_model 
             AND (
                 open_po_child.kode_warna = schedule_celup.kode_warna
                 OR open_po_child.item_type = schedule_celup.item_type
             )",
                'left'
            )
            ->join('open_po op_parent', 'op_parent.id_po = open_po_child.id_induk', 'left')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
            ->join(
                "master_order parent_master",
                "(
                parent_master.no_model = TRIM(REPLACE(op_parent.no_model, 'POCOVERING ', ''))
                OR parent_master.no_model = TRIM(SUBSTRING_INDEX(op_parent.no_model, ' ', -1))
                OR parent_master.no_model = TRIM(op_parent.no_model)
            )",
                'left'
            )
            ->where('schedule_celup.tanggal_schedule >=', $tglAwal)
            ->where('schedule_celup.tanggal_schedule <=', $tglAkhir)
            // ->where('schedule_celup.last_status', 'scheduled')
            ->orderBy('schedule_celup.tanggal_schedule', 'ASC')
            ->orderBy('mesin_celup.no_mesin', 'ASC');

        // hanya tambahkan filter last_status di query utama kalau role bukan 'celup'
        if ($role !== 'celup') {
            $builder->where('schedule_celup.last_status', 'scheduled');
        }
        // dd($role);
        // --- Filter jenis mesin
        if (!empty($jenis)) {
            $builder->where('mesin_celup.ket_mesin', $jenis);

            if ($jenis === 'BENANG') {
                $builder->where('mesin_celup.no_mesin >=', 1)
                    ->where('mesin_celup.no_mesin <=', 38);
            } elseif ($jenis === 'ACRYLIC') {
                $builder->where('mesin_celup.no_mesin >=', 39)
                    ->where('mesin_celup.no_mesin <=', 43);
            }
        } else {
            $builder->where('mesin_celup.no_mesin >=', 1)
                ->where('mesin_celup.no_mesin <=', 43);
        }

        // --- Grouping akhir
        $builder->groupBy('
        schedule_celup.no_model,
        schedule_celup.item_type,
        schedule_celup.kode_warna,
        schedule_celup.warna,
        schedule_celup.tanggal_schedule,
        mesin_celup.id_mesin,
        schedule_celup.lot_urut,
        schedule_celup.lot_celup,
        schedule_celup.start_mc,
        schedule_celup.po_plus,
        schedule_celup.ket_schedule,
        schedule_celup.last_status
    ');

        return $builder->get()->getResultArray();
    }


    // public function getFilterSchWeekly($tglAwal, $tglAkhir, $jenis)
    // {
    //     $db = \Config\Database::connect();
    //     $sub = $db->table('schedule_celup s')
    //         ->select("
    //         s.item_type,
    //         s.kode_warna,
    //         s.warna,
    //         s.tanggal_schedule,
    //         s.id_mesin,
    //         s.id_bon,
    //         s.no_po,
    //         s.lot_urut,
    //         s.lot_celup,
    //         s.start_mc,
    //         s.po_plus,
    //         s.ket_schedule,
    //         SUM(s.kg_celup) AS kg_celup,
    //         GROUP_CONCAT(DISTINCT s.no_model ORDER BY s.no_model SEPARATOR ', ') AS no_model,
    //         COUNT(*) AS cnt,
    //         MIN(s.id_celup) AS id_celup
    //     ")
    //         ->where('s.tanggal_schedule >=', $tglAwal)
    //         ->where('s.tanggal_schedule <=', $tglAkhir)
    //         ->groupBy('s.item_type, s.kode_warna, s.warna, s.tanggal_schedule, s.id_mesin, s.lot_urut')
    //         ->getCompiledSelect();

    //     $builder = $db->table("({$sub}) AS schedule_celup")
    //         ->select('schedule_celup.id_celup, schedule_celup.id_mesin, schedule_celup.id_bon, schedule_celup.no_po, GROUP_CONCAT(DISTINCT schedule_celup.no_model) AS no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.start_mc, schedule_celup.lot_urut, schedule_celup.lot_celup, schedule_celup.tanggal_schedule, schedule_celup.po_plus, schedule_celup.kg_celup, schedule_celup.ket_schedule, mesin_celup.no_mesin, mesin_celup.min_caps, mesin_celup.max_caps, master_material.jenis, MAX(COALESCE(master_order.delivery_awal, parent_master.delivery_awal)) AS delivery_awal')
    //         ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
    //         // join open_po child: match no_model plus either kode_warna OR item_type (toleran jika salah satunya berubah)
    //         ->join(
    //             'open_po open_po_child',
    //             "open_po_child.no_model = schedule_celup.no_model 
    //          AND (
    //              open_po_child.kode_warna = schedule_celup.kode_warna
    //              OR open_po_child.item_type = schedule_celup.item_type
    //          )",
    //             'left'
    //         )
    //         // ambil parent PO berdasarkan id_induk dari child (jika ada)
    //         ->join('open_po op_parent', 'op_parent.id_po = open_po_child.id_induk', 'left')
    //         ->join('master_material', 'master_material.item_type = schedule_celup.item_type', 'left')
    //         // jalur normal: master_order langsung matching schedule no_model (kalau ada)
    //         ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
    //         // jalur induk: match parent no_model (POCOVERING)
    //         ->join(
    //             "master_order parent_master",
    //             "(
    //             parent_master.no_model = TRIM(REPLACE(op_parent.no_model, 'POCOVERING ', ''))
    //             OR parent_master.no_model = TRIM(SUBSTRING_INDEX(op_parent.no_model, ' ', -1))
    //             OR parent_master.no_model = TRIM(op_parent.no_model)
    //         )",
    //             'left'
    //         )
    //         ->where('schedule_celup.tanggal_schedule >=', $tglAwal)
    //         ->where('schedule_celup.tanggal_schedule <=', $tglAkhir)
    //         ->orderBy('schedule_celup.tanggal_schedule', 'ASC')
    //         ->orderBy('mesin_celup.no_mesin', 'ASC');

    //     if (!empty($jenis)) {
    //         $builder->where('mesin_celup.ket_mesin', $jenis);

    //         if ($jenis === 'BENANG') {
    //             $builder->where('mesin_celup.no_mesin >=', 1)
    //                 ->where('mesin_celup.no_mesin <=', 38);
    //         } elseif ($jenis === 'ACRYLIC') {
    //             $builder->where('mesin_celup.no_mesin >=', 39)
    //                 ->where('mesin_celup.no_mesin <=', 43);
    //         }
    //     } else {
    //         $builder->where('mesin_celup.no_mesin >=', 1)
    //             ->where('mesin_celup.no_mesin <=', 43);
    //     }

    //     $builder->groupBy('schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.tanggal_schedule, mesin_celup.id_mesin, schedule_celup.lot_urut');

    //     return $builder->get()->getResultArray();
    // }

    public function getSchBenangNylon()
    {
        return $this->select('schedule_celup.*')
            ->findAll();
    }

    public function getFilterSchTagihanBenang($noModel = null, $kodeWarna = null, $deliveryAwal  = null, $deliveryAkhir = null, $tglAwal = null, $tglAkhir = null)
    {
        // Subquery Stock
        $subSt = $this->db->table('stock')
            ->select('stock.no_model,stock.item_type,stock.kode_warna,stock.warna,SUM(kgs_stock_awal) AS stock_awal')
            ->groupBy(['stock.no_model', 'stock.item_type', 'stock.kode_warna', 'stock.warna']);

        // Subquery Retur
        $subRt = $this->db->table('retur')
            ->select('retur.no_model,retur.item_type,retur.kode_warna,retur.warna,SUM(retur.kgs_retur) AS retur_stock')
            ->join('kategori_retur', 'kategori_retur.nama_kategori = retur.kategori')
            ->where('waktu_acc_retur IS NOT NULL')
            ->where('kategori_retur.tipe_kategori', 'SIMPAN ULANG')
            ->groupBy(['retur.no_model', 'retur.item_type', 'retur.kode_warna', 'retur.warna']);

        // Subquery qty_po_plus 
        $subPoPlusDistinct = $this->db->table('po_tambahan')
            ->select([
                'master_order.no_model',
                'material.item_type',
                'material.kode_warna',
                'material.color',
                'COALESCE(total_potambahan.ttl_tambahan_kg, 0) AS ttl_tambahan_kg',
                'po_tambahan.created_at'
            ])
            ->join(
                'total_potambahan',
                'total_potambahan.id_total_potambahan = po_tambahan.id_total_potambahan',
                'left'
            )
            ->join(
                'material',
                'material.id_material = po_tambahan.id_material',
                'left'
            )
            ->join(
                'master_order',
                'master_order.id_order = material.id_order',
                'left'
            )
            ->where('po_tambahan.status', 'approved')
            ->where('po_tambahan.tanggal_approve IS NOT NULL', null, false)
            ->distinct();

        $subPoPlus = $this->db->table("({$subPoPlusDistinct->getCompiledSelect()}) ppd")
            ->select([
                'ppd.no_model',
                'ppd.item_type',
                'ppd.kode_warna',
                'ppd.color',
                'SUM(ppd.ttl_tambahan_kg) AS po_plus'
            ])
            ->groupBy([
                'ppd.no_model',
                'ppd.item_type',
                'ppd.kode_warna',
                'ppd.color'
            ]);

        //Sub Pemasukan
        $subPm = $this->db->table('pemasukan')
            ->select('sc.no_model, sc.item_type, sc.kode_warna, sc.warna, SUM(oc.kgs_kirim) AS qty_datang_solid')
            ->join('out_celup oc', 'oc.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup')
            ->groupBy(['oc.no_model', 'sc.item_type', 'sc.kode_warna', 'sc.warna',]);

        //Sub Ganti Retur Solid
        $subOc = $this->db->table('pemasukan')
            ->select('oc.no_model, SUM(oc.kgs_kirim) AS qty_ganti_retur_solid, sc.item_type, sc.kode_warna, sc.warna')
            ->join('out_celup oc', 'oc.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup')
            ->where('oc.ganti_retur', '1')
            ->groupBy(['oc.no_model', 'sc.item_type', 'sc.kode_warna', 'sc.warna']);

        // Subquery Retur Pengembalian
        $subRtBl = $this->db->table('history_stock')
            ->select('out_celup.no_model, schedule_celup.item_type,schedule_celup.kode_warna,schedule_celup.warna,SUM(history_stock.kgs) AS retur_belang')
            ->join('out_celup', 'out_celup.id_out_celup = history_stock.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join(
                'kategori_retur kr',
                "kr.nama_kategori = TRIM(
                SUBSTRING(
                    history_stock.keterangan,
                    LOCATE('(', history_stock.keterangan) + 1,
                    LOCATE(')', history_stock.keterangan) - LOCATE('(', history_stock.keterangan) - 1
                )
            )",
                'left',
                false
            )
            ->like('history_stock.keterangan', 'Retur Celup')
            ->where('kr.tipe_kategori', 'PENGEMBALIAN')
            ->groupBy(['history_stock.id_out_celup']);

        $subSc = $this->db->table('schedule_celup')
            ->select('no_model, item_type, kode_warna, warna, SUM(kg_celup) AS qty_sch, MIN(start_mc) AS start_mc')
            ->groupBy(['no_model', 'item_type', 'kode_warna', 'warna']);

        // 3) Main query
        $builder = $this->db->table("({$subSc->getCompiledSelect()}) sc")
            ->select([
                'sc.no_model',
                'sc.item_type',
                'sc.kode_warna',
                'sc.warna',
                'material.area',
                'pp.po_plus',
                'mo.delivery_awal',
                'mo.delivery_akhir',
                'sc.start_mc',
                'st.stock_awal',
                'rt.retur_stock',
                'sc.qty_sch',
                'pm.qty_datang_solid',
                'oc.qty_ganti_retur_solid',
                'rtbl.retur_belang',
            ])
            ->join('master_order mo', 'mo.no_model = sc.no_model')
            ->join('material', 'material.id_order = mo.id_order')
            ->join("({$subOc->getCompiledSelect()}) oc", 'oc.no_model = sc.no_model AND oc.item_type   = sc.item_type AND oc.kode_warna  = sc.kode_warna AND oc.warna = sc.warna', 'left')
            ->join("({$subSt->getCompiledSelect()}) st", 'st.no_model   = oc.no_model AND st.item_type  = sc.item_type AND st.kode_warna = sc.kode_warna AND st.warna = sc.warna', 'left')
            ->join("({$subPoPlus->getCompiledSelect()}) pp", 'pp.no_model = sc.no_model AND pp.item_type = sc.item_type AND pp.kode_warna = sc.kode_warna AND pp.color = sc.warna', 'left')
            ->join("({$subRt->getCompiledSelect()}) rt", 'rt.no_model = sc.no_model AND rt.item_type = sc.item_type AND rt.kode_warna = sc.kode_warna AND sc.warna = rt.warna', 'left')
            ->join("({$subPm->getCompiledSelect()}) pm", 'pm.no_model = sc.no_model AND pm.item_type   = sc.item_type AND pm.kode_warna  = sc.kode_warna AND pm.warna = sc.warna', 'left')
            ->join("({$subRtBl->getCompiledSelect()}) rtbl", 'rtbl.no_model = sc.no_model AND rtbl.item_type = sc.item_type AND rtbl.kode_warna = sc.kode_warna', 'left');

        // 4) Filter
        if ($noModel) {
            $builder->where('sc.no_model', $noModel);
        }
        if ($kodeWarna) {
            $builder->like('sc.kode_warna', $kodeWarna);
        }
        if ($deliveryAwal && $deliveryAkhir) {
            $builder
                ->where('mo.delivery_awal >=', $deliveryAwal)
                ->where('mo.delivery_akhir <=', $deliveryAkhir);
        }
        if ($tglAwal && $tglAkhir) {
            $builder
                ->where('sc.start_mc >=', $tglAwal)
                ->where('sc.start_mc <=', $tglAkhir);
        }

        return $builder
            ->groupBy([
                'sc.no_model',
                'sc.item_type',
                'sc.kode_warna',
                'sc.warna',
            ])
            ->get()
            ->getResultArray();
    }
    public function cekSch($model, $keb)
    {
        $data = $this->select('tanggal_schedule')
            ->where('no_model', $model['no_model'])
            ->where('item_type', $keb['item_type'])
            ->where('kode_warna', $keb['kode_warna'])
            ->where('warna', $keb['color'])
            ->first();
        return $data['tanggal_schedule'] ?? null;
    }
    public function getIdSch($data)
    {
        return $this->select('id_celup')
            ->where('no_model', $data['no_model'])
            ->where('item_type', $data['item_type'])
            ->where('kode_warna', $data['kode_warna'])
            ->where('warna', $data['color'])
            ->first();
    }
    public function getHistorySch($machine, $date, $lot)
    {
        return $this->select('schedule_celup.id_celup,sum(schedule_celup.kg_celup) as qty_celup,schedule_celup.item_type, schedule_celup.no_model, DATE(schedule_celup.start_mc) AS start_mc, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.last_status, schedule_celup.po_plus, DATE(schedule_celup.updated_at) as last_update,TIME(schedule_celup.updated_at) as jam_update,schedule_celup.user_cek_status as admin, schedule_celup.ket_schedule, open_po.keterangan, open_po.id_induk')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna')
            ->where('tanggal_schedule', $date)
            ->where('id_mesin', $machine)
            ->where('lot_urut', $lot)
            ->where('last_status !=', 'scheduled')
            ->groupBy('id_celup')
            ->findAll();
    }

    // public function getPindahMesin(int $idCelup): array
    // {
    //     $sql = "
    //     WITH RECURSIVE
    //     seq AS (
    //       SELECT 1 AS n
    //       UNION ALL
    //       SELECT n + 1
    //       FROM seq
    //       WHERE n < (SELECT COALESCE(MAX(m.jml_lot), 1) FROM mesin_celup m)
    //     ),
    //     src AS (
    //       SELECT
    //         sc.id_celup,
    //         sc.id_mesin,
    //         sc.lot_urut,
    //         DATE(sc.tanggal_schedule) AS tgl,
    //         sc.kode_warna
    //       FROM schedule_celup sc
    //       WHERE sc.id_celup = :id_celup:
    //     ),
    //     lot_series AS (
    //       SELECT
    //         m.id_mesin,
    //         m.no_mesin,
    //         m.jml_lot,
    //         m.max_caps,
    //         s.n AS lot_urut
    //       FROM mesin_celup m
    //       JOIN seq s ON s.n <= m.jml_lot
    //     ),
    //     kosong AS (
    //       SELECT
    //         ls.id_mesin,
    //         ls.no_mesin,
    //         ls.lot_urut
    //       FROM lot_series ls
    //       JOIN src ON 1=1
    //       LEFT JOIN schedule_celup sch
    //         ON  sch.id_mesin = ls.id_mesin
    //         AND sch.lot_urut = ls.lot_urut
    //         AND DATE(sch.tanggal_schedule) = src.tgl
    //         AND sch.last_status != 'celup'
    //       WHERE sch.id_mesin IS NULL
    //     )
    //     SELECT
    //       k.id_mesin,
    //       m.no_mesin,
    //       k.lot_urut,
    //       m.max_caps,
    //       0 AS kg_terjadwal,
    //       m.max_caps AS sisa_caps
    //     FROM kosong k
    //     JOIN src s         ON 1=1
    //     JOIN mesin_celup m ON m.id_mesin = k.id_mesin
    //     WHERE NOT (k.id_mesin = s.id_mesin AND k.lot_urut = s.lot_urut)
    //       AND NOT EXISTS (
    //         SELECT 1
    //         FROM schedule_celup x
    //         WHERE x.id_mesin = k.id_mesin
    //           AND DATE(x.tanggal_schedule) = s.tgl
    //           AND x.last_status != 'celup'
    //       )
    //     ORDER BY m.no_mesin, k.lot_urut
    //     ";

    //     return $this->db->query($sql, ['id_celup' => $idCelup])->getResultArray();
    // }

    public function getPindahMesin(int $idCelup): array
    {
        // Ambil tgl_schedule dari id_celup sumber
        $row = $this->db->table('schedule_celup')
            ->select('DATE(tanggal_schedule) AS tgl', false)
            ->where('id_celup', $idCelup)
            ->get()
            ->getRowArray();

        if (!$row || empty($row['tgl'])) {
            return [];
        }

        $tgl = $row['tgl'];

        // Mesin yang tidak punya schedule aktif (last_status != 'celup') pada tgl tsb
        $builder = $this->db->table('mesin_celup m')
            ->select('DISTINCT m.id_mesin, m.no_mesin', false)
            ->orderBy('m.no_mesin');

        // NOT EXISTS subquery
        $builder->where("NOT EXISTS (
        SELECT 1 FROM schedule_celup sch
        WHERE sch.id_mesin = m.id_mesin
          AND DATE(sch.tanggal_schedule) = " . $this->db->escape($tgl) . "
          AND sch.last_status <> 'celup'
    )", null, false);

        return $builder->get()->getResultArray();
    }

    public function getMesinSlots(
        string $tanggal,        // format: 'Y-m-d'
        string $itemType,
        string $kodeWarna,
        string $lastStatus = 'scheduled'
    ): array {
        $sql = "
            SELECT
                m.id_mesin,
                m.no_mesin,
                m.max_caps,
                m.jml_lot,
                slots.lot_urut,
                sc.id_celup,
                sc.no_model,
                sc.item_type,
                sc.kode_warna,
                sc.warna,
                sc.kg_celup,
                sc.tanggal_schedule,
                sc.last_status,
                CASE 
                    WHEN sc.id_celup IS NULL THEN 'kosong'
                    ELSE 'terisi'
                END AS status_slot
            FROM mesin_celup m
            -- Generate slot lot_urut = 1 s/d jml_lot (sementara hardcode max 2)
            JOIN (
                SELECT 1 AS lot_urut
                UNION ALL
                SELECT 2 AS lot_urut
            ) AS slots
                ON slots.lot_urut <= m.jml_lot
            -- Join ke schedule_celup per mesin + per lot_urut + filter tgl + item + warna + status
            LEFT JOIN schedule_celup sc
                ON sc.id_mesin = m.id_mesin
            AND sc.lot_urut = slots.lot_urut
            AND sc.tanggal_schedule = ?
            AND sc.last_status      = ?
            AND sc.item_type        = ?
            AND sc.kode_warna       = ?
            ORDER BY 
                m.no_mesin,
                slots.lot_urut
        ";

        $params = [
            $tanggal,
            $lastStatus,
            $itemType,
            $kodeWarna,
        ];

        return $this->db->query($sql, $params)->getResultArray();
    }

    public function getScheduleWithOpenPo(
        string $noModel,
        string $itemType,
        string $kodeWarna,
        string $color,
        string $status = 'scheduled',
        string $created
    ): array {
        $db = \Config\Database::connect(); // atau $this->db kalau di dalam Model CI4

        // Subquery untuk open_po (bikin kolom created_date = DATE(created_at))
        $subQuery = $db->table('open_po')
            ->select("
                id_po,
                no_model,
                item_type,
                kode_warna,
                color,
                kg_po,
                admin,
                created_at,
                DATE(created_at) AS created_date
            ")
            ->getCompiledSelect();

        // Query utama
        $builder = $db->table('schedule_celup sc');

        $builder->select("
            sc.id_celup,
            sc.id_mesin,
            sc.no_model,
            sc.item_type,
            sc.kode_warna,
            sc.warna,
            sc.kg_celup,
            sc.lot_urut,
            sc.lot_celup,
            sc.last_status,
            sc.tanggal_schedule,
            sc.admin                    AS sc_admin,
            DATE(sc.created_at)         AS sc_created_date,

            po.id_po               AS po_id,
            po.no_model                 AS po_no_model,
            po.item_type                AS po_item_type,
            po.kode_warna               AS po_kode_warna,
            po.color                    AS po_color,
            po.kg_po,
            po.admin                    AS po_admin,
            po.created_date             AS po_created_date
        ");

        // JOIN ke subquery open_po
        $builder->join("({$subQuery}) AS po", "
                po.no_model      = sc.no_model
            AND po.item_type     = sc.item_type
            AND po.kode_warna    = sc.kode_warna
            AND po.color         = sc.warna
            AND po.kg_po         = sc.kg_celup
            AND po.created_date  = DATE(sc.created_at)
            AND po.admin         = sc.admin
        ");

        // Filter utama
        $builder->where('sc.no_model', $noModel);
        $builder->where('sc.item_type', $itemType);
        $builder->where('sc.kode_warna', $kodeWarna);
        $builder->where('sc.warna', $color);
        $builder->where('sc.last_status', $status);
        $builder->where('DATE(sc.created_at)', $created);

        return $builder->get()->getResultArray();
    }
}
