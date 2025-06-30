<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\OpenPoModel;
use CodeIgniter\HTTP\ResponseInterface;

class PoManualController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $masterOrderModel;
    protected $materialModel;
    protected $openPoModel;
    protected $masterMaterialModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        // $this->stockModel = new StockModel();

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
        $itemType = $this->masterMaterialModel->findAll();
        // dd($itemType);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'itemType' => $itemType,
        ];
        return view($this->role . '/masterdata/po-manual-form', $data);
    }
}
