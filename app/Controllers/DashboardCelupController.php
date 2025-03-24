<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ScheduleCelupModel;

class DashboardCelupController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $scheduleCelupModel;

    public function __construct()
    {
        $this->scheduleCelupModel = new ScheduleCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['celup']] != session()->get('role')) {
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
        $scheduled = $this->scheduleCelupModel->countStatusScheduled();
        $reschedule = $this->scheduleCelupModel->countStatusReschedule();
        $done = $this->scheduleCelupModel->countStatusDone();
        $retur = $this->scheduleCelupModel->countStatusRetur();
        $mesin = $this->scheduleCelupModel->getMesinKapasitasHariIni();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'scheduled' => $scheduled,
            'reschedule' => $reschedule,
            'done' => $done,
            'retur' => $retur,
            'mesin' => $mesin
        ];
        return view($this->role . '/dashboard/index', $data);
    }
}
