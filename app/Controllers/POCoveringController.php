<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OpenPoModel;

class POCoveringController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $openPoModel;

    public function __construct()
    {
        $this->openPoModel = new OpenPoModel();
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
        $getData = $this->openPoModel->getPOCovering();
        $data = [
            'active' => $this->active,
            'title' => 'PO Covering',
            'role' => $this->role,
            'getData' => $getData
        ];
        return view($this->role .'/pocovering/index', $data);
    }
}
