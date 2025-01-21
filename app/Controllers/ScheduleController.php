<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MesinCelupModel;
use App\Models\ScheduleCelupModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\MasterOrderModel;

class ScheduleController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;
    protected $scheduleCelupModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $masterOrderModel;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->masterOrderModel = new MasterOrderModel();


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

    public function getScheduleDetails($no_mesin, $tanggal_schedule, $lot_urut)
    {
        // Get the schedule details from the model
        $scheduleDetails = $this->scheduleCelupModel->getScheduleDetails($no_mesin, $tanggal_schedule, $lot_urut);

        // Check if data is not empty
        if ($scheduleDetails) {
            // Load the modal view and pass the scheduleDetails to it
            echo view($this->role . '/schedule/modal_details', ['scheduleDetails' => $scheduleDetails]);
        } else {
            // If no data is found, display a message
            echo "<div class='text-center text-danger'>Data tidak ditemukan.</div>";
        }
    }



    public function create()
    {
        // Ambil data dari URL menggunakan GET
        $no_mesin = $this->request->getGet('no_mesin');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $lot_urut = $this->request->getGet('lot_urut');
        $no_model = $this->request->getGet('no_model');

        $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->masterMaterialModel->getItemType();
        $min = $this->mesinCelupModel->getMinCaps($no_mesin);
        $max = $this->mesinCelupModel->getMaxCaps($no_mesin);
        $po = $this->openPoModel->getNomorModel();
        // dd ($jenis_bahan_baku, $item_type, $min_caps, $max_caps);
        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$no_mesin || !$tanggal_schedule || !$lot_urut) {
            return redirect()->back();
        }

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'tanggal_schedule' => $tanggal_schedule,
            'lot_urut' => $lot_urut,
            'jenis_bahan_baku' => $jenis_bahan_baku,
            'item_type' => $item_type,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],
            'po' => $po,
        ];
        // var_dump($data);

        return view($this->role . '/schedule/form', $data);
    }

    public function getItemType()
    {
        $jenis = $this->request->getGet('jenis');
        $itemTypes = $this->masterMaterialModel->getItemTypeByJenis($jenis);

        // Assuming $itemTypes is an array of objects with 'item_type' as the key
        return $this->response->setJSON($itemTypes);
    }

    public function getWarnabyItemTypeandKodeWarna()
    {
        $item_type = $this->request->getGet('item_type');
        $kode_warna = $this->request->getGet('kode_warna');
        $warna = $this->openPoModel->getWarnabyItemTypeandKodeWarna($item_type, $kode_warna);

        // Cek apakah warna ditemukan
        if ($warna) {
            return $this->response->setJSON(['color' => $warna['color']]); // Kembalikan warna sebagai respons
        } else {
            return $this->response->setJSON(['color' => null]); // Jika warna tidak ditemukan
        }
    }


    public function getPO()
    {
        $itemType = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        // Debugging untuk memastikan parameter diterima
        // var_dump($itemType, $kodeWarna);

        if (!$itemType || !$kodeWarna) {
            return $this->response->setJSON([]);
        }

        $poData = $this->masterMaterialModel->getFilteredPO($itemType, $kodeWarna);

        if ($poData) {
            return $this->response->setJSON($poData);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }

    public function getPODetails()
    {
        $id_order = $this->request->getGet('id_order');
        $poDetails = $this->masterOrderModel->getDelivery($id_order);

        if ($poDetails) {
            return $this->response->setJSON($poDetails);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }
}
