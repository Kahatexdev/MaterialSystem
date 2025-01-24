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
        'tanggal_rajut_pagi',
        'tanggal_kelos',
        'tanggal_acc',
        'tanggal_reject',
        'tanggal_perbaikan',
        'last_status',
        'ket_daily_cek',
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
        return $this->table('schedule_celup')
            ->select('*, mesin_celup.no_mesin')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('mesin_celup.no_mesin', $machine)
            ->where('schedule_celup.tanggal_schedule', $date)
            ->where('schedule_celup.lot_urut', $lot)
            ->groupBy('schedule_celup.id_celup')
            ->findAll();
    }

    public function getItemTypeByParameter($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('item_type')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function getKodeWarnaByParameter($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('kode_warna')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
            ->findAll();
    }

    public function getWarnaByParameter($no_mesin, $tanggal_schedule, $lot_urut)
    {
        return $this->table('schedule_celup')
            ->select('warna')
            ->where('no_mesin', $no_mesin)
            ->where('tanggal_schedule', $tanggal_schedule)
            ->where('lot_urut', $lot_urut)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->distinct()
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
            ->whereIn('schedule_celup.last_status', ['scheduled', 'celup', 'reschedule']) // Filter berdasarkan last_status
            ->groupBy('schedule_celup.id_mesin')
            ->groupBy('schedule_celup.tanggal_schedule')
            ->groupBy('schedule_celup.lot_urut');

        return $builder->get()->getResultArray();
    }

    public function getSchedule()
    {
        return $this->select('schedule_celup.*, mesin_celup.no_mesin, sum(schedule_celup.kg_celup) as total_kg')
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin')
            ->where('schedule_celup.last_status', 'scheduled')
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
}
