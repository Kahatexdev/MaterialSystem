<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use CodeIgniter\HTTP\ResponseInterface;

class PoBookingController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $masterOrderModel;
    protected $materialModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        // $this->stockModel = new StockModel();
        // $this->masterMaterialModel = new MasterMaterialModel();
        // $this->openPoModel = new OpenPoModel();

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
        $buyer = $this->masterOrderModel->getBuyer();
        // dd($itemType);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'buyer' => $buyer,
        ];
        return view($this->role . '/masterdata/po-booking-form', $data);
    }

    public function getItemType($buyer)
    {
        if ($buyer === null) {
            return $this->response->setStatusCode(400)
                ->setJSON(['error' => 'buyerId missing']);
        }
        $itemType = $this->materialModel->getItemTypeByBuyer($buyer);

        $result = array_map(fn($r) => [
            'id'   => $r['item_type'],
            'text' => $r['item_type']
        ], $itemType);
        return $this->response->setJSON($result);
    }

    public function getKodeWarna()
    {
        $buyer    = $this->request->getGet('buyer');
        $itemType = $this->request->getGet('item_type');

        if (! $buyer || ! $itemType) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Parameter missing']);
        }

        // model sudah punya method getKodeWarnaByBuyerAndItemType($buyer,$itemType)
        $rows = $this->materialModel
            ->getKodeWarnaByBuyerAndItemType($buyer, $itemType);

        // format untuk Select2 / dropdown:
        $result = array_map(fn($r) => [
            'id'   => $r['kode_warna'],
            'text' => $r['kode_warna']
        ], $rows);

        return $this->response->setJSON($result);
    }

    public function getColor()
    {
        $buyer    = $this->request->getGet('buyer');
        $itemType = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        if ($buyer === null || $itemType === null || $kodeWarna === null) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter missing']);
        }

        $data = $this->materialModel->getWarnaByBuyerItemTypeAndKodeWarna($buyer, $itemType, $kodeWarna);

        $color = $data['color'] ?? 'Tidak ditemukan';

        return $this->response->setJSON(['color' => $color]);
    }
}
