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
        // json_encode($scheduleData);
        // var_dump ($scheduleData);
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
        if ($scheduleDetails) {
            return response()->setJSON($scheduleDetails);
        } else {
            return response()->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);          
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

        return view($this->role . '/schedule/form-create', $data);
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

    public function getQtyPO()
    {
        $id_order = $this->request->getGet('id_order');
        $item_type = $this->request->getGet('item_type');
        $kode_warna = $this->request->getGet('kode_warna');
        $qtyPO = $this->materialModel->getQtyPO($id_order, $item_type, $kode_warna);

        if ($qtyPO) {
            return $this->response->setJSON($qtyPO);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }

    public function saveSchedule()
    {
        // Ambil semua data dari form menggunakan POST
        $scheduleData = $this->request->getPost();

        // Ambil id_mesin dan no_model
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]

        $dataBatch = []; // Untuk menyimpan batch data

        // Looping data array untuk menyusun data yang akan disimpan
        foreach ($poList as $index => $po) {
            $no_model = $this->masterOrderModel->getNoModel($po);

            $dataBatch[] = [
                'id_mesin' => $id_mesin['id_mesin'],
                'no_model' => $no_model['no_model'],
                'item_type' => $scheduleData['item_type'],
                'kode_warna' => $scheduleData['kode_warna'],
                'warna' => $scheduleData['warna'],
                'start_mc' => $scheduleData['tgl_start_mc'][$index] ?? null,
                'kg_celup' => $scheduleData['qty_celup'][$index],
                'lot_urut' => $scheduleData['lot_urut'],
                'lot_celup' => $scheduleData['lot_celup'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'],
                'user_cek_status' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Debugging untuk memeriksa data sebelum menyimpannya
        // var_dump($dataBatch); 
        // dd($dataBatch);

        // Simpan batch data ke database
        $result = $this->scheduleCelupModel->insertBatch($dataBatch);

        // Cek apakah data berhasil disimpan
        if ($result) {
            return redirect()->to(session()->get('role') . '/schedule')->with('success', 'Jadwal berhasil disimpan!');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan jadwal!');
        }
    }


    public function editSchedule($id)
    {
        // Ambil data dari database
        $scheduleData = $this->scheduleCelupModel->getScheduleDetailsById($id);
        // var_dump($scheduleData);
        $no_mesin = $this->mesinCelupModel->getNoMesin($scheduleData['id_mesin']);
        $jenis = $this->masterMaterialModel->select('jenis')->where('item_type', $scheduleData['item_type'])->first();
        $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->masterMaterialModel->getItemType();
        $min = $this->mesinCelupModel->getMinCaps($scheduleData['id_mesin']);
        $max = $this->mesinCelupModel->getMaxCaps($scheduleData['id_mesin']);
        $poData = $this->openPoModel->getNomorModel();

        // Get PO details related to the schedule, assuming this is stored in a related table
        // $poDetails = $this->scheduleCelupModel->getPODetailsByScheduleId($id);

        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$scheduleData) {
            return redirect()->back();
        }

        // Passing the schedule data, including PO details, to the view
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'scheduleData' => $scheduleData,
            'no_mesin' => $no_mesin['no_mesin'],
            'jenis' => $jenis['jenis'],
            'jenis_bahan_baku' => $jenis_bahan_baku,
            'item_type' => $item_type,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],
            'poData' => $poData,
        ];

        return view($this->role . '/schedule/form-edit', $data);
    }


}
