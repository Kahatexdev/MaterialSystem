<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;
use App\Models\ClusterModel;
use App\Models\PemasukanModel;
use App\Models\StockModel;
use App\Models\HistoryPindahPalet;
use App\Models\HistoryPindahOrder;

class ApiController extends ResourceController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;
    protected $clusterModel;
    protected $pemasukanModel;
    protected $stockModel;
    protected $historyPindahPalet;
    protected $historyPindahOrder;


    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->clusterModel = new ClusterModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->stockModel = new StockModel();
        $this->historyPindahPalet = new HistoryPindahPalet();
        $this->historyPindahOrder = new HistoryPindahOrder();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
    }

    public function index()
    {
        //
    }
    public function statusbahanbaku($area)
    {
        $search = $this->request->getGet('search');
        $model = $this->materialModel->orderPerArea($area);

        $res = [];
        foreach ($model as &$row) {
            $schedule = $this->scheduleCelupModel->schedulePerArea($row['no_model'], $row['item_type'], $row['kode_warna'], $search);

            $scheduleData = !empty($schedule) ? $schedule[0] : [];

            $fields = [
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
                'po_plus'
            ];

            foreach ($fields as $field) {
                $row[$field] = $scheduleData[$field] ?? ''; // Isi dengan data jadwal atau kosong jika tidak ada
            }

            $res[] = $row;
        }
        return $this->respond($res, 200);
    }
    public function cekBahanBaku($model)
    {
        $search = '';
        $material = $this->materialModel->MaterialPerOrder($model);
        $res = [];
        foreach ($material as &$row) {
            $schedule = $this->scheduleCelupModel->schedulePerArea($row['no_model'], $row['item_type'], $row['kode_warna'], $search);

            $scheduleData = !empty($schedule) ? $schedule[0] : [];

            $fields = [
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
                'po_plus'
            ];

            foreach ($fields as $field) {
                $row[$field] = $scheduleData[$field] ?? ''; // Isi dengan data jadwal atau kosong jika tidak ada
            }

            $res[] = $row;
        }
        return $this->respond($res, 200);
    }

    public function cekStok($model)
    {
        $material = $this->materialModel->MaterialPerOrder($model);
        $res = [];
        foreach ($material as &$row) {

            $stock = $this->stockModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['stock' => 0];
            $inout = $this->pemasukanModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['masuk' => 0, 'keluar' => 0];
            $row['stock'] = $stock['stock'] ?? 0;
            $row['masuk'] = $inout['masuk'] ?? 0;
            $row['keluar'] = $inout['keluar'];
            $res[] = $row;
        }
        return $this->respond($res, 200);
    }
    public function getMU($model, $styleSize)
    {
        $mu = $this->materialModel->getMU($model, $styleSize);
        $res = [];
        foreach ($mu as &$data) {
            $res[] = $data;
        }
        return $this->respond($res, 200);
    }
}
