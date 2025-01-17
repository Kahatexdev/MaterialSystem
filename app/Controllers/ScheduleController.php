<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MesinCelupModel;
use App\Models\ScheduleCelupModel;

class ScheduleController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;
    protected $scheduleCelupModel;
    
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();


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

    public function getScheduleDetails($machine, $date,$lot)
    {
        dd ($machine, $date,$lot);
        // Simulasi data jadwal
        $scheduleData = [
            
        ];

        $mesin_celup = $this->mesinCelupModel->getMesinCelupBenang();
        $totalCapacityUsed = array_sum(array_column($scheduleData, 'weight'));
        $totalCapacityMax = array_sum(array_column($mesin_celup, 'max_caps'));

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'scheduleData' => $scheduleData,
            'mesin_celup' => $mesin_celup,
            'totalCapacityUsed' => $totalCapacityUsed,
            'totalCapacityMax' => $totalCapacityMax,
            'currentDate' => new \DateTime($date),
        ];

        return view($this->role . '/schedule/index', $data);
    }

}
