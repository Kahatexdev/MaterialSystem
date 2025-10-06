<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardKantorController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;

    public function __construct()
    {
        // $this->scheduleCelupModel = new ScheduleCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['kantordepan']] != session()->get('role')) {
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

        $data = [
            'active' => $this->active,
            'title' => 'Report',
            'role' => $this->role
        ];
        return view($this->role . '/dashboard/index', $data);
    }
}
