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
            ->select('schedule_celup.*, mesin_celup.no_mesin, sum(kg_celup) as total_kg, open_po.ket_celup, open_po.keterangan')
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

    public function getSchedule($filterTglSch = null, $filterNoModel = null)
    {
        // dd($filterNoModel, $filterTglSch);
        // Mulai query builder
        $builder = $this->builder()
            ->select('schedule_celup.*, mesin_celup.no_mesin, 
                   IF(po_plus = "0", kg_celup, 0) AS qty_celup, 
                   IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin');
        // exclude statuses
        // ->where('schedule_celup.last_status !=', 'done')
        // ->where('schedule_celup.last_status !=', 'sent')
        // ->where('schedule_celup.last_status !=', 'complain');

        // Filter tanggal jika ada
        if ($filterTglSch) {
            // pastikan $filterTglSch sudah diformat yyyy-mm-dd
            $builder->where('schedule_celup.tanggal_schedule', $filterTglSch);
        }

        // Filter no_model atau kode_warna jika ada
        if ($filterNoModel) {
            $builder
                ->groupStart()
                ->like('schedule_celup.no_model', $filterNoModel)
                ->orLike('schedule_celup.kode_warna', $filterNoModel)
                ->groupEnd();
        }

        // Pengelompokan hasil
        $builder
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.id_celup')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut');

        $query = $builder->get();
        return $query->getResultArray();
        //     return $this->select('schedule_celup.*, mesin_celup.no_mesin, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus')
        //         ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
        //         ->where('schedule_celup.last_status !=', 'done')
        //         ->where('schedule_celup.last_status !=', 'sent')
        //         ->where('schedule_celup.last_status !=', 'complain')
        //         ->$filter
        //         ->groupBy('schedule_celup.id_mesin')
        //         ->groupBy('schedule_celup.id_celup')
        //         ->groupBy('schedule_celup.tanggal_schedule')
        //         ->groupBy('schedule_celup.lot_urut')
        //         ->findAll();
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


    public function getCelupDone()
    {
        return $this
            ->select('id_celup,no_model, item_type, kode_warna, warna, lot_celup')
            ->where('last_status', 'done')
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
        return $this->where('id_bon', $id_bon)
            ->findAll();
    }
    public function schedulePerArea($model, $itemType, $kodeWarna, $search)
    {
        $builder = $this->select(
            [
                'schedule_celup.no_model',
                'schedule_celup.item_type',
                'schedule_celup.kode_warna',
                'COALESCE(SUM(schedule_celup.kg_celup), 0) AS kg_celup',
                'schedule_celup.lot_urut',
                'schedule_celup.lot_celup',
                'schedule_celup.tanggal_schedule',
                'schedule_celup.tanggal_bon',
                'schedule_celup.tanggal_celup',
                'schedule_celup.tanggal_bongkar',
                'schedule_celup.tanggal_press_oven',
                // 'schedule_celup.tanggal_oven',
                'schedule_celup.tanggal_tl',
                'schedule_celup.tanggal_rajut_pagi',
                'schedule_celup.tanggal_kelos',
                'schedule_celup.serah_terima_acc',
                'schedule_celup.tanggal_acc',
                'schedule_celup.tanggal_reject',
                'schedule_celup.tanggal_matching',
                'schedule_celup.tanggal_perbaikan',
                'schedule_celup.tanggal_teslab',
                'schedule_celup.last_status',
                'schedule_celup.ket_daily_cek',
                'schedule_celup.po_plus',
                'COALESCE(stock.kg_stock, 0) AS kg_stock'
            ]
        )
            ->join('(SELECT no_model, item_type, kode_warna, SUM(kgs_stock_awal + kgs_in_out) AS kg_stock FROM stock GROUP BY no_model, item_type, kode_warna) AS stock', 'stock.no_model = schedule_celup.no_model AND stock.item_type = schedule_celup.item_type AND stock.kode_warna = schedule_celup.kode_warna')
            ->where('schedule_celup.no_model', $model)
            ->where('schedule_celup.item_type', $itemType)
            ->where('schedule_celup.kode_warna', $kodeWarna);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $search)
                ->orLike('schedule_celup.kode_warna', $search)
                ->orLike('schedule_celup.tanggal_schedule', $search)
                ->orLike('schedule_celup.lot_celup', $search)
                ->groupEnd();
        }

        return $builder->findAll();
    }
    public function getDataComplain()
    {
        return $this->select('schedule_celup.*, mesin_celup.no_mesin, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('schedule_celup.last_status', 'complain')
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.id_celup')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut')
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
            ->select('mesin_celup.no_mesin, mesin_celup.max_caps, COALESCE(SUM(schedule_celup.kg_celup), 0) as kapasitas_terpakai')
            ->join('schedule_celup', 'schedule_celup.id_mesin = mesin_celup.id_mesin AND schedule_celup.tanggal_schedule = CURDATE()', 'left')
            ->groupBy('mesin_celup.no_mesin, mesin_celup.max_caps')
            ->get()->getResultArray();
    }

    public function getFilterSchBenang($key, $tanggal_schedule, $tanggal_awal, $tanggal_akhir)
    {
        $db = \Config\Database::connect();

        // Subquery: summary material per item_type + kode_warna + color
        $materialSubquery = $db->table('material')
            ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
            ->groupBy(['id_order', 'item_type', 'kode_warna', 'color']);

        // Main builder
        $builder = $this->select('
        schedule_celup.*, 
        master_order.delivery_awal, 
        master_order.delivery_akhir, 
        mesin_celup.no_mesin, 
        mesin_celup.ket_mesin, 
        master_material.jenis,
        material_summary.total_kgs
    ')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join(
                '(' . $materialSubquery->getCompiledSelect(false) . ') AS material_summary',
                'material_summary.item_type = schedule_celup.item_type 
                AND material_summary.kode_warna = schedule_celup.kode_warna 
                AND material_summary.color = schedule_celup.warna
                AND material_summary.id_order = master_order.id_order',
                'left'
            )

            ->where('master_material.jenis', 'BENANG');

        // Filter pencarian (key)
        if (!empty($key)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->groupEnd();
        }

        // Filter tanggal schedule
        if (!empty($tanggal_schedule)) {
            $builder->where('schedule_celup.tanggal_schedule', $tanggal_schedule);
        }

        // Filter rentang tanggal start_mc
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $builder->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $builder->where('schedule_celup.start_mc >=', $tanggal_awal)
                    ->where('schedule_celup.start_mc <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $builder->where('schedule_celup.start_mc >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $builder->where('schedule_celup.start_mc <=', $tanggal_akhir);
            }
            $builder->groupEnd();
        }

        return $builder->get()->getResult();
    }

    public function getFilterSchNylon($key, $tanggal_schedule, $tanggal_awal, $tanggal_akhir)
    {
        $db = \Config\Database::connect();

        // Subquery: summary material per item_type + kode_warna + color
        $materialSubquery = $db->table('material')
            ->select('id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs')
            ->groupBy(['id_order', 'item_type', 'kode_warna', 'color']);

        // Main builder
        $builder = $this->select('
        schedule_celup.*, 
        master_order.delivery_awal, 
        master_order.delivery_akhir, 
        mesin_celup.no_mesin, 
        mesin_celup.ket_mesin, 
        master_material.jenis,
        material_summary.total_kgs
    ')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join(
                '(' . $materialSubquery->getCompiledSelect(false) . ') AS material_summary',
                'material_summary.item_type = schedule_celup.item_type 
                AND material_summary.kode_warna = schedule_celup.kode_warna 
                AND material_summary.color = schedule_celup.warna
                AND material_summary.id_order = master_order.id_order',
                'left'
            )
            ->where('master_material.jenis', 'NYLON');

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

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $builder->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $builder->where('schedule_celup.start_mc >=', $tanggal_awal)
                    ->where('schedule_celup.start_mc <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $builder->where('schedule_celup.start_mc >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $builder->where('schedule_celup.start_mc <=', $tanggal_akhir);
            }
            $builder->groupEnd();
        }

        return $builder->get()->getResult();
    }

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

    public function getFilterSchWeekly($tglAwal, $tglAkhir)
    {
        $builder = $this->select('schedule_celup.*, mesin_celup.no_mesin, mesin_celup.min_caps, mesin_celup.max_caps, open_po.ket_celup, master_material.jenis, master_order.delivery_awal')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model')
            ->join('master_material', 'master_material.item_type = schedule_celup.item_type')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model')
            ->where('mesin_celup.ket_mesin !=', 'ACRYLIC')
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

    public function getSchBenangNylon()
    {
        return $this->select('schedule_celup.*')
            ->findAll();
    }

    public function getFilterSchTagihanBenang(
        $noModel       = null,
        $kodeWarna     = null,
        $deliveryAwal  = null,
        $deliveryAkhir = null,
        $tglAwal       = null,
        $tglAkhir      = null
    ) {
        // 1) Subquery kebutuhan material
        $subMaterial = $this->db->table('material')
            ->select('mo.no_model, material.item_type, material.kode_warna, material.color AS warna, SUM(material.kgs) AS qty_po, material.area')
            ->join('master_order mo', 'mo.id_order = material.id_order')
            ->groupBy(['mo.no_model', 'material.item_type', 'material.kode_warna', 'material.color',]);

        // Subquery Po Tambahan
        $subPo = $this->db->table('po_tambahan')
            ->select('mo.no_model, m.item_type, m.kode_warna, m.color AS warna, SUM(po_tambahan.poplus_mc_kg + po_tambahan.plus_pck_kg) AS po_plus')
            ->join('material m', 'm.id_material = po_tambahan.id_material')
            ->join('master_order mo', 'mo.id_order = m.id_order')
            ->groupBy(['mo.no_model', 'm.item_type', 'm.kode_warna', 'm.color', 'm.id_order',]);

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

        //Sub Open Po
        $subOp = $this->db->table('open_po')
            ->select('no_model, item_type, kode_warna, SUM(kg_po) AS qty_sch')
            ->groupBy(['no_model', 'item_type', 'kode_warna']);

        //Sub Pemasukan
        $subPm = $this->db->table('pemasukan')
            ->select('sc.no_model, sc.item_type, sc.kode_warna, sc.warna, SUM(oc.kgs_kirim) AS qty_datang_solid')
            ->join('out_celup oc', 'oc.id_out_celup = pemasukan.id_out_celup')
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup')
            ->groupBy(['sc.no_model', 'sc.item_type', 'sc.kode_warna', 'sc.warna',]);

        //Sub Ganti Retur Solid
        $subOc = $this->db->table('out_celup')
            ->select('out_celup.no_model, SUM(out_celup.kgs_kirim) AS qty_ganti_retur_solid, sc.item_type, sc.kode_warna, sc.warna')
            ->join('schedule_celup sc', 'sc.id_celup = out_celup.id_celup')
            ->where('out_celup.ganti_retur', '1')
            ->groupBy(['sc.no_model', 'sc.item_type', 'sc.kode_warna', 'sc.warna',]);

        // Subquery Retur Pengembalian
        $subRtBl = $this->db->table('retur')
            ->select('retur.no_model,retur.item_type,retur.kode_warna,retur.warna,SUM(retur.kgs_retur) AS retur_belang')
            ->join('kategori_retur', 'kategori_retur.nama_kategori = retur.kategori')
            ->where('waktu_acc_retur IS NOT NULL')
            ->where('kategori_retur.tipe_kategori', 'PENGEMBALIAN')
            ->groupBy(['retur.no_model', 'retur.item_type', 'retur.kode_warna', 'retur.warna']);

        // 3) Main query
        $builder = $this->db->table('schedule_celup sc')
            ->select([
                'sc.no_model',
                'sc.item_type',
                'sc.kode_warna',
                'sc.warna',
                'k.area',
                'k.qty_po',
                'pp.po_plus',
                'mo.delivery_awal',
                'mo.delivery_akhir',
                'sc.start_mc',
                'st.stock_awal',
                'rt.retur_stock',
                'op.qty_sch',
                'pm.qty_datang_solid',
                'oc.qty_ganti_retur_solid',
                'rtbl.retur_belang',
            ])
            ->join('master_order mo', 'mo.no_model = sc.no_model')
            ->join("({$subMaterial->getCompiledSelect()}) k", 'k.no_model = sc.no_model AND k.item_type = sc.item_type AND k.kode_warna = sc.kode_warna AND k.warna = sc.warna', 'left')
            ->join("({$subPo->getCompiledSelect()}) pp", 'pp.no_model = sc.no_model AND pp.item_type  = sc.item_type AND pp.kode_warna = sc.kode_warna AND pp.warna = sc.warna', 'left')
            ->join("({$subSt->getCompiledSelect()}) st", 'st.no_model   = sc.no_model AND st.item_type  = sc.item_type AND st.kode_warna = sc.kode_warna AND st.warna = sc.warna', 'left')
            ->join("({$subOp->getCompiledSelect()}) op", 'op.no_model = sc.no_model AND op.item_type = sc.item_type AND op.kode_warna = sc.kode_warna', 'left')
            ->join("({$subRt->getCompiledSelect()}) rt", 'rt.no_model = sc.no_model AND rt.item_type = sc.item_type AND rt.kode_warna = sc.kode_warna', 'left')
            ->join("({$subPm->getCompiledSelect()}) pm", 'pm.no_model = sc.no_model AND pm.item_type   = sc.item_type AND pm.kode_warna  = sc.kode_warna AND pm.warna = sc.warna', 'left')
            ->join("({$subOc->getCompiledSelect()}) oc", 'oc.no_model = sc.no_model AND oc.item_type   = sc.item_type AND oc.kode_warna  = sc.kode_warna AND oc.warna = sc.warna', 'left')
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
                ->where('mo.delivery_awal <=', $deliveryAkhir);
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
                'k.qty_po',
                'mo.delivery_awal',
                'mo.delivery_akhir',
                'sc.start_mc',
            ])
            ->get()
            ->getResultArray();
    }
}
