<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\ScheduleCelupModel;
use App\Models\PemasukanModel;

class DashboardGbnController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $masterOrderModel;
    protected $scheduleCelupModel;
    protected $pemasukanModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->pemasukanModel = new PemasukanModel();

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
        $pdk = $this->masterOrderModel->getPdk();
        $schedule = $this->scheduleCelupModel->countScheduleDone();
        $pemasukan = $this->pemasukanModel->getTotalKarungMasuk();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'pdk' => $pdk,
            'schedule' => $schedule,
            'pemasukan' => $pemasukan
        ];
        return view($this->role . '/dashboard/index', $data);
    }
}
