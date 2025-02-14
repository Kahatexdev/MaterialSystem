<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;

class MaterialController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $materialModel;
    protected $masterMaterialModel;

    public function __construct()
    {
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
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/dashboard/index', $data);
    }

    public function tambahMaterial()
    {
        $data = $this->request->getPost();
        $idOrder = $this->request->getPost('id_order');

        $saveData = [
            'id_order' => $idOrder,
            'style_size' => $data['style_size'],
            'area' => $data['area'],
            'inisial' => $data['inisial'],
            'color' => $data['color'],
            'item_type' => $data['item_type'],
            'kode_warna' => $data['kode_warna'],
            'composition' => $data['composition'],
            'gw' => $data['gw'],
            'qty_pcs' => $data['qty_pcs'],
            'loss' => $data['loss'],
            'kgs' => $data['kgs'],
            'admin' => session()->get('id_user'),
        ];

        if ($this->materialModel->insert($saveData)) {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('success', 'Data berhasil disimpan.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('error', 'Data gagal disimpan.');
        }
    }
}
