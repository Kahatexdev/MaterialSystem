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
        'tanggal_press',
        'tanggal_oven',
        'tanggal_tl',
        'tanggal_rajut_pagi',
        'tanggal_kelos',
        'tanggal_acc',
        'tanggal_reject',
        'tanggal_perbaikan',
        'last_status',
        'ket_daily_cek',
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
            ->select('*, mesin_celup.no_mesin, sum(kg_celup) as total_kg')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
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
        return $this->select('schedule_celup.id_celup,sum(schedule_celup.kg_celup) as qty_celup,schedule_celup.item_type, schedule_celup.no_model, DATE(schedule_celup.start_mc) AS start_mc, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.last_status, schedule_celup.po_plus')
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
            ->whereIn('schedule_celup.last_status', ['scheduled', 'celup', 'reschedule', 'bon', 'bongkar', 'press', 'oven', 'tl', 'rajut', 'acc', 'reject', 'perbaikan']) // Filter berdasarkan last_status
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut');

        return $builder->get()->getResultArray();
    }

    public function getSchedule()
    {
        return $this->select('schedule_celup.*, mesin_celup.no_mesin, IF(po_plus = "0", kg_celup, 0) AS qty_celup, IF(po_plus = "1", kg_celup, 0) AS qty_celup_plus')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('schedule_celup.last_status !=', 'done')
            ->where('schedule_celup.last_status !=', 'sent')
            ->where('schedule_celup.last_status !=', 'complain')
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.id_celup')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut')
            ->findAll();
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
                'start_mc',
                'kg_celup',
                'lot_urut',
                'lot_celup',
                'tanggal_schedule',
                'tanggal_bon',
                'tanggal_celup',
                'tanggal_bongkar',
                'tanggal_press',
                'tanggal_oven',
                'tanggal_tl',
                'tanggal_rajut_pagi',
                'tanggal_kelos',
                'tanggal_acc',
                'tanggal_reject',
                'tanggal_perbaikan',
                'last_status',
                'ket_daily_cek',
                'po_plus',
            ]
        )
            ->where('no_model', $model)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('no_model', $search)
                ->orLike('kode_warna', $search)
                ->orLike('tanggal_schedule', $search)
                ->orLike('lot_celup', $search)
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
            ->groupBy('id_celup')
            ->first();
    }
    public function countStatusReschedule()
    {
        return $this->select('COUNT(id_celup) as total_reschedule')
            ->where('last_status', 'reschedule') // Sesuaikan last status jika perlu
            ->groupBy('id_celup')
            ->first();
    }
    public function countStatusDone()
    {
        return $this->select('COUNT(id_celup) as total_done')
            ->where('last_status', 'done') // Sesuaikan last status jika perlu
            ->groupBy('id_celup')
            ->first();
    }
    public function countStatusRetur()
    {
        return $this->select('COUNT(id_celup) as total_retur')
            ->where('last_status', 'retur') // Sesuaikan last status jika perlu
            ->groupBy('id_celup')
            ->first();
    }
}
