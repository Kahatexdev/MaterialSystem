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
            // Jika startdate tidak tersedia, gunakan tanggal 3 hari ke belakang
            $startDate = date('Y-m-d', strtotime('-3 days'));
            // end date 7 hari ke depan
            $endDate = date('Y-m-d', strtotime('+6 days'));
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);

        // dd ($scheduleData);
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

        $poData = $this->openPoModel->getFilteredPO($itemType, $kodeWarna);

        if ($poData) {
            return $this->response->setJSON($poData);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }

    public function getPODetails()
    {
        $id_order = $this->request->getGet('id_order');
        $itemType = $this->request->getGet('itemType');
        $kodeWarna = $this->request->getGet('kodeWarna');
        $poDetails = $this->masterOrderModel->getDelivery($id_order);

        if (empty($poDetails)) {
            return $this->response->setJSON(['error' => 'Order not found']);
        }

        $model = $poDetails['no_model'];
        // var_dump($model);
        $reqStartMc = 'http://172.23.44.14/CapacityApps/public/api/reqstartmc/' . $model;
        $kg_kebutuhan = $this->openPoModel->getKgKebutuhan($model, $itemType, $kodeWarna);
        $cekSisaJatah = $this->scheduleCelupModel->cekSisaJatah($model, $itemType, $kodeWarna);
        $sisa_jatah = 0;
        if ($cekSisaJatah) {
            $qty_po = isset($cekSisaJatah[0]['qty_po']) ? (float) $cekSisaJatah[0]['qty_po'] : 0;
            $total_kg = isset($cekSisaJatah[0]['total_kg']) ? (float) $cekSisaJatah[0]['total_kg'] : 0;
            // var_dump($total_kg);

            $sisa_jatah = $qty_po - $total_kg ;
        }else{
            $sisa_jatah = $cekSisaJatah[0]['qty_po'] ;
        }
        try {
            // Fetch API response
            $json = file_get_contents($reqStartMc);

            if ($json === false) {
                throw new \Exception('Failed to fetch data from the API.');
            }

            // Decode JSON response
            $startMc = json_decode($json, true);
            // var_dump($startMc);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }

            // Assign start_mesin data
            $poDetails['start_mesin'] = $startMc['start_mc'];
            $poDetails['sisa_jatah'] = $sisa_jatah;
            $poDetails['kg_kebutuhan'] = $kg_kebutuhan['kg_po'];
        } catch (\Exception $e) {
            // Handle error and assign fallback value
            $poDetails['start_mesin'] = 'Data Not Found';
            $poDetails['sisa_jatah'] = $sisa_jatah;
            $poDetails['kg_kebutuhan'] = $kg_kebutuhan['kg_po'];
            log_message('error', 'Error fetching API data: ' . $e->getMessage());
        }

        // Return response
        return $this->response->setJSON($poDetails);
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
        // dd($item_type);
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
        $po = $this->openPoModel->getFilteredPO($item_type[0]['item_type'], $kode_warna[0]['kode_warna']);
// dd ($po);
        // var_dump($id_order);
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
            $qtyPO = $this->materialModel->getQtyPOByNoModel($row['no_model'], $row['item_type'], $row['kode_warna']);
            $row['qty_po'] = $qtyPO['qty_po'] ?? 0; // Default 0 jika tidak ada data
            $start_mc = $row['start_mc'];
        }

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'start_mc' => $start_mc,
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
        // dd ($data);
        return view($this->role . '/schedule/form-edit', $data);
    }

    public function getNoModel()
    {
        $id_order = $this->request->getGet('id_order');
        $no_model = $this->masterOrderModel->getNoModel($id_order);

        if ($no_model) {
            return $this->response->setJSON($no_model);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }
    public function updateSchedule()
    {
        // Ambil semua data dari form menggunakan POST
        $scheduleData = $this->request->getPost();
        // dd ($scheduleData);
        // Ambil id_mesin dan no_model
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]

        $dataBatch = []; // Untuk menyimpan batch data
        $updateMessage = null; // Menyimpan status pesan update atau insert

        // Looping data array untuk menyusun data yang akan disimpan
        foreach ($poList as $index => $po) {
            $id_celup = $scheduleData['id_celup'][$index] ?? null; // Dapatkan id_celup, jika ada
            $last_status = $scheduleData['last_status'][$index] ?? 'scheduled'; // Default status
            $start_mc = $scheduleData['start_mc'][$index] ?? null; // Default status
            $delivery_awal = $scheduleData['delivery_awal'][$index] ?? null; // Default status
            $delivery_akhir = $scheduleData['delivery_akhir'][$index] ?? null; // Default status
            $dataBatch[] = [
                'id_celup' => $id_celup, // ID ini bisa null untuk baris baru
                'id_mesin' => $id_mesin['id_mesin'],
                'no_model' => $scheduleData['po'][$index],
                'item_type' => $scheduleData['item_type'],
                'kode_warna' => $scheduleData['kode_warna'],
                'warna' => $scheduleData['warna'],
                'start_mc' => $start_mc,
                'kg_celup' => $scheduleData['qty_celup'][$index] ?? null,
                'po_plus' => $scheduleData['po_plus'][$index] ?? null,
                'lot_urut' => $scheduleData['lot_urut'],
                'lot_celup' => $scheduleData['lot_celup'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'],
                'last_status' => $last_status,
                'user_cek_status' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        // dd ($dataBatch);
        // Cek apakah data sudah ada, gunakan id_celup untuk mencari
        foreach ($dataBatch as $data) {
            $existingSchedule = $this->scheduleCelupModel->where([
                'id_celup' => $data['id_celup'],
            ])->first();
            // dd ($existingSchedule);
            if ($existingSchedule) {
                // Periksa apakah ada perubahan data
                $hasChanges = false;
                foreach ($data as $key => $value) {
                    if ($key !== 'created_at' && $key !== 'user_cek_status' && $existingSchedule[$key] != $value) {
                        $hasChanges = true;
                        break;
                    }
                }

                // Update hanya jika ada perubahan
                if ($hasChanges) {
                    $updateSuccess = $this->scheduleCelupModel->update($data['id_celup'], $data);
                    if ($updateSuccess) {
                        $updateMessage = 'Jadwal berhasil diupdate!';
                    } else {
                        $updateMessage = 'Gagal mengupdate jadwal!';
                    }
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
            return redirect()->back()->with('info', 'Tidak ada perubahan yang disimpan.');
        }
    }



    public function acrylic()
    {
        // Ambil parameter filter dari query string
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if ($startDate == null && $endDate == null) {
            // Jika startdate tidak tersedia, gunakan tanggal 3 hari ke belakang
            $startDate = date('Y-m-d', strtotime('-3 days'));
            // end date 7 hari ke depan
            $endDate = date('Y-m-d', strtotime('+6 days'));
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);
        // Ambil data mesin celup
        $mesin_celup = $this->mesinCelupModel->getMesinCelupAcrylic();

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
        return view($this->role . '/schedule/acrylic', $data);
    }


    public function nylon()
    {
        // Ambil parameter filter dari query string
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if ($startDate == null && $endDate == null) {
            // Jika startdate tidak tersedia, gunakan tanggal 3 hari ke belakang
            $startDate = date('Y-m-d', strtotime('-3 days'));
            // end date 7 hari ke depan
            $endDate = date('Y-m-d', strtotime('+6 days'));
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);
        // Ambil data mesin celup
        $mesin_celup = $this->mesinCelupModel->getMesinCelupNylon();

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
        return view($this->role . '/schedule/nylon', $data);
    }

    public function updateTglSchedule()
    {
        $id_celup = $this->request->getPost('id_celup');
        $tanggal_schedule = $this->request->getPost('tanggal_schedule');
        $no_mesin = $this->request->getPost('no_mesin');
        $lot_urut = $this->request->getPost('lot_urut');

        $cekData = $this->scheduleCelupModel->cekItemtypeandKodeWarna($no_mesin, $tanggal_schedule, $lot_urut);
        $data = [
            'tanggal_schedule' => $tanggal_schedule,
        ];

        $result = $this->scheduleCelupModel->update($id_celup, $data);

        if ($result) {
            return $this->response->setJSON(['success' => 'Tanggal schedule berhasil diupdate!']);
        } else {
            return $this->response->setJSON(['error' => 'Gagal mengupdate tanggal schedule!']);
        }
    }

    public function deleteSchedule()
    {
        $id_celup = $this->request->getPost('id_celup');
        $result = $this->scheduleCelupModel->delete($id_celup);

        if ($result) {
            return $this->response->setJSON(['success' => 'Jadwal berhasil dihapus!']);
        } else {
            return $this->response->setJSON(['error' => 'Gagal menghapus jadwal!']);
        }
    }

    public function validateSisaJatah()
    {
        $rows = $this->request->getPost('rows');

        $errors = [];
        $isValid = true;

        foreach ($rows as $row) {
            $no_model = $row['no_model'];
            $item_type = $row['item_type'];
            $kode_warna = $row['kode_warna'];
            $qty_celup = (float) $row['qty_celup'];
            $current_qty_celup = (float) $row['current_qty_celup'];

            // Validasi input
            if (empty($no_model) || empty($item_type) || empty($kode_warna) || $qty_celup <= 0) {
                $errors[] = [
                    'message' => 'Input tidak valid. Pastikan semua data terisi dengan benar.'
                ];
                $isValid = false;
                continue;
            }

            // Panggil query untuk cek sisa jatah
            $query = $this->scheduleCelupModel->cekSisaJatah($no_model, $item_type, $kode_warna);

            if ($query && isset($query[0])) {
                $result = $query[0];
                $qty_po = isset($result['qty_po']) ? (float) $result['qty_po'] : 0;
                $total_kg = isset($result['total_kg']) ? (float) $result['total_kg'] : 0;

                $sisa_jatah = $qty_po - $total_kg + $current_qty_celup;
                
                if ($sisa_jatah< 0) {
                    $errors[] = [
                        'message' => 'Sisa Jatah tidak mencukupi.',
                        'sisa_jatah' => $sisa_jatah,
                        'total_kg' => $total_kg,
                        'qty_po' => $qty_po,
                        'qtycelup' => $qty_celup,
                    ];
                    $isValid = false;
                }else{
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Sisa Jatah mencukupi.',
                        'sisa_jatah' => $sisa_jatah,
                        'total_kg' => $total_kg,
                        'qty_po' => $qty_po
                    ]);
                }
            } else {
                $errors[] = [
                    'message' => 'Data tidak ditemukan.'
                ];
                $isValid = false;
            }
        }

        if ($isValid) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sisa Jatah mencukupi.',
                'sisa_jatah' => $sisa_jatah,
                'total_kg' => $total_kg,
                'qty_po' => $qty_po
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $errors
            ]);
        }
    }





}
