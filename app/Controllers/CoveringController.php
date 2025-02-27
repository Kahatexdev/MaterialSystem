<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;

class CoveringController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['covering']] != session()->get('role')) {
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
            'title' => 'Covering',
            'role' => $this->role,
        ];
        return view($this->role . '/dashboard/index', $data);
    }

    public function po()
    {
        $poCovering = $this->openPoModel->getPOCovering();
        // dd ($poCovering);
        $data = [
            'active' => $this->active,
            'title' => 'PO Celup',
            'role' => $this->role,
            'poCovering' => $poCovering,
        ];
        return view($this->role . '/po/index', $data);
    }

    public function schedule()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Schedule Celup',
            'role' => $this->role,
        ];
        return view($this->role . '/schedule/index', $data);
    }

    public function poDetail($tgl_po)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $poDetail = $this->openPoModel->getPODetailCovering($tgl_po);
        $coveringData = session()->get('covering_data');
        if (empty($coveringData)) {
            $coveringData[0] = [
                'no_model' => '',
                'item_type' => '',
                'itemTypeCovering' => '',
                'kodeWarnaCovering' => '',
                'qty_covering' => ''
            ];
        }
        $data = [
            'active' => $this->active,
            'title' => 'PO Celup',
            'role' => $this->role,
            'tgl_po' => $tgl_po,
            'poDetail' => $poDetail,
            'coveringData' => $coveringData,
        ];
        return view($this->role . '/po/detail', $data);
    }

    public function getDetailByNoModel($tgl_po, $noModel)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $noModel = urldecode($noModel);
        $detail = $this->openPoModel->getDetailByNoModel($tgl_po, $noModel);
        return $this->response->setJSON($detail);
    }

    public function simpanKeSession()
    {
        // Ambil data dari POST
        $items = $this->request->getPost('items');

        // Ambil data lama dari session jika ada
        $existingData = session()->get('covering_data') ?? [];

        // Gabungkan data baru dengan data lama
        $updatedData = array_merge($existingData, $items);

        // Simpan ke session
        session()->set('covering_data', $updatedData);

        // Beri response atau redirect
        return redirect()->back()->with('success', 'Data berhasil disimpan di session');
    }

    public function savePOCovering()
    {
        $data = $this->request->getPost();
        $coveringData = session()->get('covering_data') ?? [];
        $data['covering_data'] = $coveringData;

        // Pastikan selected_items ada dan merupakan array
        $selectedItems = $data['selected_items'] ?? [];
        $existingSelectedItems = session()->get('selected_items') ?? [];

        $data['selected_items'] = [];

        foreach ($selectedItems as $selectedIndex) {
            if (isset($coveringData[$selectedIndex])) {
                $selectedItem = $coveringData[$selectedIndex];
                $data['selected_items'][] = $selectedItem;
                $existingSelectedItems[$selectedIndex] = $selectedItem;

                // Hapus item yang telah dipilih dari coveringData
                unset($coveringData[$selectedIndex]);
            }
        }

        // Simpan kembali array yang telah diperbarui ke sesi
        session()->set('covering_data', array_values($coveringData));
        session()->set('selected_items', $existingSelectedItems);

        print_r($data);
    }
}
