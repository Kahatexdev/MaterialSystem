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
use DateTime;

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
        // Ambil parameter filter dari query string
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if ($startDate == null && $endDate == null) {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);
        var_dump($scheduleData);
        // Ambil data mesin celup
        $mesin_celup = $this->mesinCelupModel->getMesinCelupBenang();

        // Hitung total kapasitas yang sudah digunakan
        $totalCapacityUsed = array_sum(array_column($scheduleData, 'weight'));

        // Hitung total kapasitas maksimum dari semua mesin celup
        $totalCapacityMax = array_sum(array_column($mesin_celup, 'max_caps'));

        // Siapkan data untuk dikirimkan ke view
        $today = date('Y-m-d', strtotime('today'));
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'scheduleData' => $scheduleData,
            'mesin_celup' => $mesin_celup,
            'totalCapacityUsed' => $totalCapacityUsed,
            'totalCapacityMax' => $totalCapacityMax,
            'currentDate' => new \DateTime(),
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        // Render view dengan data yang sudah disiapkan
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
                'last_status' => 'scheduled',
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

    public function editSchedule()
    {
        $no_mesin = $this->request->getGet('no_mesin');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $lot_urut = $this->request->getGet('lot_urut');
        $no_model = $this->request->getGet('no_model');

        $scheduleData = $this->scheduleCelupModel->getScheduleDetailsData($no_mesin, $tanggal_schedule, $lot_urut);
        $master_order = $this->masterOrderModel->findAll();
        $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->scheduleCelupModel->getItemTypeByParameter($no_mesin, $tanggal_schedule, $lot_urut);
        $kode_warna = $this->scheduleCelupModel->getKodeWarnaByParameter($no_mesin, $tanggal_schedule, $lot_urut);
        $warna = $this->scheduleCelupModel->getWarnaByParameter($no_mesin, $tanggal_schedule, $lot_urut);
        $tanggal_celup = $this->scheduleCelupModel->getTanggalCelup($no_mesin, $tanggal_schedule, $lot_urut);
        $lot_celup = $this->scheduleCelupModel->getLotCelup($no_mesin, $tanggal_schedule, $lot_urut);
        $ket_daily_cek = $this->scheduleCelupModel->getKetDailyCek($no_mesin, $tanggal_schedule, $lot_urut);
        $jenis = $this->masterMaterialModel->select('jenis')->where('item_type', $scheduleData[0]['item_type'])->first();
        // $item_type = $this->masterMaterialModel->getItemType();
        $min = $this->mesinCelupModel->getMinCaps($no_mesin);
        $max = $this->mesinCelupModel->getMaxCaps($no_mesin);
        $id_order = $this->masterOrderModel->getIdOrder($no_model);
        $po = $this->openPoModel->getNomorModel();
        // var_dump($po);
        $readonly = true;
        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$no_mesin || !$tanggal_schedule || !$lot_urut) {
            return redirect()->back();
        }

        foreach ($scheduleData as &$row) {
            // Ambil delivery_awal dan delivery_akhir dari tabel master_order
            $deliveryDates = $this->masterOrderModel->getDeliveryDates($row['no_model']);
            if ($deliveryDates) {
                $row['delivery_awal'] = $deliveryDates['delivery_awal'] ?? null;
                $row['delivery_akhir'] = $deliveryDates['delivery_akhir'] ?? null;
            } else {
                $row['delivery_awal'] = null;
                $row['delivery_akhir'] = null;
            }

            // Hitung qty_po dari tabel order_details
            $qtyPO = $this->materialModel->getQtyPOByNoModel($row['no_model']);
            $row['qty_po'] = $qtyPO['qty_po'] ?? 0; // Default 0 jika tidak ada data
        }


        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'tanggal_schedule' => $tanggal_schedule,
            'lot_urut' => $lot_urut,
            'scheduleData' => $scheduleData,
            'jenis_bahan_baku' => $jenis_bahan_baku,
            'jenis' => $jenis['jenis'],
            'item_type' => $item_type,
            'kode_warna' => $kode_warna,
            'warna' => $warna,
            'tanggal_celup' => $tanggal_celup,
            'lot_celup' => $lot_celup,
            'ket_daily_cek' => $ket_daily_cek,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],
            'po' => $po,
            'readonly' => $readonly,
        ];

        return view($this->role . '/schedule/form-edit', $data);
    }

    public function updateSchedule()
    {
        // Ambil semua data dari form menggunakan POST
        $scheduleData = $this->request->getPost();

        // Ambil id_mesin dan no_model
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]

        $dataBatch = []; // Untuk menyimpan batch data
        $updateMessage = null; // Menyimpan status pesan update atau insert

        // Looping data array untuk menyusun data yang akan disimpan
        foreach ($poList as $index => $po) {
            $id_celup = $scheduleData['id_celup'][$index] ?? null; // Dapatkan id_celup, jika ada
            $dataBatch[] = [
                'id_celup' => $id_celup, // ID ini bisa null untuk baris baru
                'id_mesin' => $id_mesin['id_mesin'],
                'no_model' => $poList[$index],
                'item_type' => $scheduleData['item_type'],
                'kode_warna' => $scheduleData['kode_warna'],
                'warna' => $scheduleData['warna'],
                'start_mc' => $scheduleData['start_mc'][$index] ?? null,
                'delivery_awal' => $scheduleData['delivery_awal'][$index] ?? null,
                'delivery_akhir' => $scheduleData['delivery_akhir'][$index] ?? null,
                'qty_po' => $scheduleData['qty_po'][$index] ?? null,
                'qty_po_plus' => $scheduleData['qty_po_plus'][$index] ?? null,
                'kg_celup' => $scheduleData['qty_celup'][$index] ?? null,
                'po_plus' => $scheduleData['po_plus'][$index] ?? null,
                'lot_urut' => $scheduleData['lot_urut'],
                'lot_celup' => $scheduleData['lot_celup'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'],
                'tanggal_celup' => $scheduleData['tanggal_celup'],
                'ket_daily_cek' => $scheduleData['ket_daily_cek'],
                'user_cek_status' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Cek apakah data sudah ada, gunakan id_celup untuk mencari
        foreach ($dataBatch as $data) {
            $existingSchedule = $this->scheduleCelupModel->where([
                'id_celup' => $data['id_celup'],
            ])->first();

            if ($existingSchedule) {
                // Update data yang sudah ada
                $updateSuccess = $this->scheduleCelupModel->update($data['id_celup'], $data);
                if ($updateSuccess) {
                    $updateMessage = 'Jadwal berhasil diupdate!';
                } else {
                    $updateMessage = 'Gagal mengupdate jadwal!';
                }
            } else {
                // Insert data baru
                $insertSuccess = $this->scheduleCelupModel->insert($data);
                if ($insertSuccess) {
                    $updateMessage = 'Jadwal berhasil disimpan!';
                } else {
                    $updateMessage = 'Gagal menyimpan jadwal!';
                }
            }
        }

        // Redirect setelah seluruh proses selesai
        if ($updateMessage) {
            return redirect()->to(session()->get('role') . '/schedule')->with('success', $updateMessage);
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan atau mengupdate jadwal!');
        }
    }
}
