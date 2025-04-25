<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\StockModel;

class PoGabunganController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $stockModel;
    protected $masterMaterialModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->stockModel = new StockModel();
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
        $jenis = $this->masterMaterialModel->getJenis();
        // dd($jenis);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis
        ];
        return view($this->role . '/masterdata/po-gabungan', $data);
    }

    public function poGabunganNylon()
    {
        $id = 2;
        $masterOrder = $this->masterOrderModel->getMaterialOrder($id);
        $orderData = $this->masterOrderModel->find($id);
        foreach ($masterOrder as &$order) { // Note: pass by reference to modify the original array
            foreach ($order['kode_warna'] as &$item) {
                $model = $item['no_model'];
                $itemType = $item['item_type'];
                $kodeWarna = $item['kode_warna'];

                $cek = [
                    'no_model' => $model,
                    'item_type' => $itemType,
                    'kode_warna' => $kodeWarna,
                ];

                $cekStok = $this->stockModel->cekStok($cek);

                if ($cekStok) {
                    $kebutuhan = max(0, $item['total_kg'] - $cekStok['kg_stok']); // Ensure no negative values
                    $item['kg_stok'] = $cekStok['kg_stok'];
                    $item['kg_po'] = $kebutuhan;
                } else {
                    $item['kg_stok'] = 0;
                    $item['kg_po'] = $item['total_kg'];
                }
            }
        }

        $data = [
            'model' => $orderData['no_model'],
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'order' => $masterOrder,
            'id_order' => $id
        ];
        // return view($this->role . '/mastermaterial/openPO', $data);
        return view($this->role . '/masterdata/po-gabungan-nylon', $data);
    }
}
