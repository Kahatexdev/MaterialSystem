<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MesinCelupModel;
use App\Models\ScheduleCelupModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;

class ScheduleController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;
    protected $scheduleCelupModel;
    protected $materialModel;
    protected $masterMaterialModel;
    
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();


        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['gbn']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
    protected function isLogedin()
    {
        if (!session()->get('id_user')) {
            return redirect()->to(base_url('/login'));
        }
    }

    public function index()
    {
        // Simulasi data jadwal
        $scheduleData = $this->scheduleCelupModel->getScheduleCelup();
        // dd ($scheduleData);
        $mesin_celup = $this->mesinCelupModel->getMesinCelupBenang();
        $totalCapacityUsed = array_sum(array_column($scheduleData, 'weight'));
        $totalCapacityMax = array_sum(array_column($mesin_celup, 'max_caps'));
        // dd ($mesin_celup);
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'scheduleData' => $scheduleData,
            'mesin_celup' => $mesin_celup,
            'totalCapacityUsed' => $totalCapacityUsed,
            'totalCapacityMax' => $totalCapacityMax,
            'currentDate' => new \DateTime('2025-01-14'),
        ];

        return view($this->role . '/schedule/index', $data);
    }

    public function getScheduleDetails($machine, $date, $lot)
    {
        $getScheduleDetails = $this->scheduleCelupModel->getScheduleDetails($machine, $date, $lot);
        // Jika data ditemukan, kembalikan view khusus untuk modal
        if ($getScheduleDetails) {
            return view($this->role . '/schedule/modal_details', [
                'scheduleDetails' => $getScheduleDetails
            ]);
        }

        // Jika data tidak ditemukan
        return '<p class="text-danger text-center">Data tidak ditemukan.</p>';
    }

    public function create()
    {
        // Ambil data dari URL menggunakan GET
        $no_mesin = $this->request->getGet('no_mesin');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $lot_urut = $this->request->getGet('lot_urut');

        $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->masterMaterialModel->getItemType();
        // dd ($jenis_bahan_baku);
        // dd ($no_mesin, $tanggal_schedule, $lot_urut);
        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$no_mesin || !$tanggal_schedule || !$lot_urut) {
            return redirect()->back();
        }

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'tanggal_schedule' => $tanggal_schedule,
            'lot_urut' => $lot_urut,
            'jenis_bahan_baku' => $jenis_bahan_baku,
            'item_type' => $item_type,
        ];

        return view($this->role . '/schedule/form', $data);
    }



}
