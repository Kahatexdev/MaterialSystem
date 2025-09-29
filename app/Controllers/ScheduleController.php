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
use App\Models\StockModel;
use CodeIgniter\CLI\Console;
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
    protected $stockModel;

    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->stockModel = new StockModel();

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

        // if ($startDate == null && $endDate == null) {
        //     // Jika startdate tidak tersedia, gunakan tanggal 3 hari ke belakang
        //     $startDate = date('Y-m-d', strtotime('+5 days'));
        //     // end date 7 hari ke depan
        //     $endDate = date('Y-m-d', strtotime('+14 days'));
        // }

        if ($startDate && $endDate) {
            // Kalau ada dari GET → simpan ke session
            session()->set('start_date', $startDate);
            session()->set('end_date', $endDate);
        } else {
            // Kalau tidak ada GET → coba ambil dari session
            $startDate = session()->get('start_date');
            $endDate   = session()->get('end_date');
        }

        // Kalau session juga kosong → baru pakai default
        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-d', strtotime('+5 days'));
            $endDate   = date('Y-m-d', strtotime('+14 days'));

            // simpan default ke session
            session()->set('start_date', $startDate);
            session()->set('end_date', $endDate);
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);

        // dd($scheduleData);
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
        if (!empty($scheduleDetails['id_induk'])) {
        }
        foreach ($scheduleDetails as &$item) {
            if (empty($item['id_induk'])) {
                // Tanpa induk: langsung pakai no_model anak
                $masterOrder = $this->masterOrderModel
                    ->where('no_model', $item['no_model'])
                    ->first();

                $item['delivery_awal']  = $masterOrder['delivery_awal'] ?? null;
                $item['delivery_akhir'] = $masterOrder['delivery_akhir'] ?? null;
            } else {
                // Ada induk: ambil no_model induk terlebih dahulu
                $parentPo = $this->openPoModel
                    ->where('id_po', $item['id_induk'])
                    ->first();

                if ($parentPo) {
                    // Bersihkan label "POCOVERING" dari no_model induk
                    $noModelInduk = trim(str_replace('POCOVERING', '', $parentPo['no_model']));

                    $masterOrder = $this->masterOrderModel
                        ->where('no_model', $noModelInduk)
                        ->first();

                    $item['delivery_awal']  = $masterOrder['delivery_awal'] ?? null;
                    $item['delivery_akhir'] = $masterOrder['delivery_akhir'] ?? null;
                } else {
                    // Induk tidak ditemukan
                    $item['delivery_awal']  = null;
                    $item['delivery_akhir'] = null;
                }
            }
        }
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

        $id_mesin = $this->mesinCelupModel->select('id_mesin')->where('no_mesin', $no_mesin)->first();
        // $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->masterMaterialModel->getItemType();
        $min = $this->mesinCelupModel->getMinCaps($no_mesin);
        $max = $this->mesinCelupModel->getMaxCaps($no_mesin);
        // $po = $this->openPoModel->getNomorModel();
        // dd($jenis_bahan_baku, $item_type);
        // Jika data tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$no_mesin || !$tanggal_schedule || !$lot_urut) {
            return redirect()->back();
        }
        $historySch = $this->scheduleCelupModel->getHistorySch($id_mesin, $tanggal_schedule, $lot_urut);

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'tanggal_schedule' => $tanggal_schedule,
            'lot_urut' => $lot_urut,
            // 'jenis_bahan_baku' => $jenis_bahan_baku,
            'item_type' => $item_type,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],
            'po' => '',
            'start_date' => $this->request->getGet('start_date'),
            'end_date' =>  $this->request->getGet('end_date'),
            'history' => $historySch
        ];
        // dd($data);

        return view($this->role . '/schedule/form-create', $data);
    }
    public function createsample()
    {
        // Ambil data dari URL menggunakan GET
        $no_mesin = $this->request->getGet('no_mesin');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $lot_urut = $this->request->getGet('lot_urut');
        $no_model = $this->request->getGet('no_model');

        // $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();
        $item_type = $this->masterMaterialModel->getItemType();
        $min = $this->mesinCelupModel->getMinCaps($no_mesin);
        $max = $this->mesinCelupModel->getMaxCaps($no_mesin);

        // dd($jenis_bahan_baku, $item_type);
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
            // 'jenis_bahan_baku' => $jenis_bahan_baku,
            'item_type' => $item_type,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],

            'start_date' => $this->request->getGet('start_date'),
            'end_date' =>  $this->request->getGet('end_date')
        ];
        // dd($data);

        return view($this->role . '/schedule/form-create-sample', $data);
    }

    public function getItemType()
    {
        // Ambil kodeWarna dan warna dari parameter GET
        $kodeWarna = $this->request->getGet('kode_warna');
        $warna = $this->request->getGet('warna');

        // Validasi kodeWarna dan warna
        if (empty($kodeWarna) || empty($warna)) {
            return $this->response->setJSON([]); // Kembalikan array kosong jika kodeWarna atau warna tidak valid
        }

        // Ambil item_type dari model
        $itemType = $this->openPoModel->getItemType($kodeWarna, $warna);

        // Kembalikan item_type dalam format JSON
        return $this->response->setJSON($itemType);
    }

    public function getKodeWarna()
    {
        // Ambil query dari parameter GET
        $query = $this->request->getGet('query');

        // Validasi query
        if (empty($query) || strlen($query) < 2) {
            return $this->response->setJSON([]); // Kembalikan array kosong jika query tidak valid
        }

        // Ambil data dari model
        $results = $this->openPoModel->getKodeWarna($query);

        // Format data untuk dikirim ke frontend
        $suggestions = [];
        foreach ($results as $result) {
            $suggestions[] = [
                'kode_warna' => $result['kode_warna'], // Sesuaikan dengan nama kolom di database
            ];
        }

        // Kembalikan data dalam format JSON
        return $this->response->setJSON($suggestions);
    }


    public function getWarna()
    {
        // Ambil kode_warna dari parameter GET
        $kodeWarna = $this->request->getGet('kode_warna');

        // Validasi kode_warna
        if (empty($kodeWarna)) {
            return $this->response->setJSON([]); // Kembalikan array kosong jika kode_warna tidak valid
        }

        // Ambil warna dari model
        $warna = $this->openPoModel->getWarna($kodeWarna);

        // Kembalikan warna dalam format JSON
        return $this->response->setJSON($warna);
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
        // Ambil parameter dari GET request
        $kode_warna = $this->request->getGet('kode_warna');
        $warna      = $this->request->getGet('warna');
        $item_type  = $this->request->getGet('item_type');
        $id_induk   = $this->request->getGet('id_induk');

        // Validasi parameter wajib
        if (empty($kode_warna) || empty($warna) || empty($item_type)) {
            return $this->response->setJSON(['error' => 'Parameter tidak lengkap']);
        }
        $po = [];
        if (!empty($id_induk)) {
            $id_po = $this->openPoModel->find($id_induk);
            if (!empty($id_po)) {
                $deliv = $this->openPoModel->getFilteredPO($id_po['kode_warna'], $id_po['color'], $id_po['item_type']);
                $po = $this->openPoModel->getFilteredCovering($kode_warna, $warna, $item_type);
                // var_dump($po);
            } else {
                // Jika id_induk tidak valid, gunakan parameter yang dikirim
                $po = $this->openPoModel->getFilteredPO($kode_warna, $warna, $item_type);
            }
        } else {
            // Jika id_induk tidak tersedia, gunakan parameter yang dikirim
            $po = $this->openPoModel->getFilteredPO($kode_warna, $warna, $item_type);
        }
        // var_dump($po);
        // Kembalikan data PO jika ditemukan, atau error jika tidak ada data
        if (!empty($po)) {
            return $this->response->setJSON($po);
        } else {
            return $this->response->setJSON(['error' => 'Data PO kosong']);
        }
    }


    public function getPODetails()
    {
        // Ambil parameter dari request
        $no_model = $this->request->getGet('no_model');
        $itemType = urldecode($this->request->getGet('item_type'));
        $kodeWarna = $this->request->getGet('kode_warna');

        // Validasi parameter
        if (empty($no_model)) {
            return $this->response->setJSON(['error' => 'Invalid request no_model']);
        } elseif (empty($itemType)) {
            return $this->response->setJSON(['error' => 'Invalid request itemtype']);
        } elseif (empty($kodeWarna)) {
            return $this->response->setJSON(['error' => 'Invalid request kode_warna']);
        }

        $poCovering = $this->openPoModel->select('id_po, id_induk, no_model')
            ->where('no_model', $no_model)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->first();
        // Tentukan no_model induk
        if (empty($poCovering['id_induk'])) {
            $no_model_induk = $poCovering['no_model']; // Sudah induk
        } else {
            // Cari induknya berdasarkan id_po = id_induk
            $parentPo = $this->openPoModel->select('no_model')
                ->where('id_po', $poCovering['id_induk'])
                ->first();

            if (!$parentPo) {
                return $this->response->setJSON([
                    'status' => 'fail',
                    'message' => 'Data induk tidak ditemukan'
                ]);
            }

            $no_model_induk = $parentPo['no_model'];
        }

        $no_model_induk = preg_replace('/^POCOVERING\s*/i', '', $no_model_induk);

        // Gunakan no_model_induk untuk ambil delivery awal/akhir dari master_order
        $deliveryPoCovering = $this->masterOrderModel
            ->select('delivery_awal, delivery_akhir')
            ->where('no_model', $no_model_induk)
            ->first();
        if (!$deliveryPoCovering) {
            return $this->response->setJSON([
                'status' => 'fail',
                'message' => 'Delivery tidak ditemukan'
            ]);
        }

        // Ambil data kg_kebutuhan dari model
        $kg_kebutuhan = $this->openPoModel->getKgKebutuhan($no_model, $itemType, $kodeWarna);

        // Ambil data sisa jatah dari model (bisa berisi lebih dari 1 baris)
        $cekSisaJatah = $this->scheduleCelupModel->cekSisaJatah($no_model, $itemType, $kodeWarna);
        // var_dump($cekSisaJatah);
        $total_qty_po = 0;
        $total_scheduled = 0;
        if (!empty($cekSisaJatah)) {
            // Looping untuk menjumlahkan seluruh data dari cekSisaJatah
            foreach ($cekSisaJatah as $row) {
                $total_qty_po += isset($row['qty_po']) ? (float)$row['qty_po'] : 0;
                $total_scheduled += isset($row['total_kg']) ? (float)$row['total_kg'] : 0;
            }
            $sisa_jatah = $total_qty_po - $total_scheduled;
        } else {
            // Jika tidak ada schedule, sisa jatah sama dengan nilai kg_po dari openPoModel
            $sisa_jatah = isset($kg_kebutuhan['kg_po']) ? (float)$kg_kebutuhan['kg_po'] : 0;
            $total_qty_po = $sisa_jatah;
        }
        // URL API untuk mengambil data start mesin
        $reqStartMc = 'http://172.23.39.117/CapacityApps/public/api/reqstartmc/' . $no_model;

        try {
            // Fetch data dari API
            $json = file_get_contents($reqStartMc);
            if ($json === false) {
                throw new \Exception('Failed to fetch data from the API.');
            }
            // Decode JSON response
            $startMc = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            if (!isset($startMc['start_mc'])) {
                throw new \Exception('Start MC data not found in API response.');
            }

            // Assign data ke $poDetails
            $poDetails['start_mesin'] = $startMc['start_mc'];
            $poDetails['qty_po'] = $total_qty_po;
            $poDetails['sisa_jatah'] = $sisa_jatah;
            $poDetails['kg_kebutuhan'] = $kg_kebutuhan['kg_po'] ?? 0;
            $poDetails['delivery_awal'] = $deliveryPoCovering['delivery_awal'] ?? 0;
            $poDetails['delivery_akhir'] = $deliveryPoCovering['delivery_akhir'] ?? 0;
        } catch (\Exception $e) {
            // Tangani error dan assign fallback value
            $poDetails['start_mesin'] = 'Data Not Found';
            $poDetails['qty_po'] = $total_qty_po;
            $poDetails['sisa_jatah'] = $sisa_jatah;
            $poDetails['kg_kebutuhan'] = $kg_kebutuhan['kg_po'] ?? 0;
            $poDetails['delivery_awal'] = $deliveryPoCovering['delivery_awal'] ?? 0;
            $poDetails['delivery_akhir'] = $deliveryPoCovering['delivery_akhir'] ?? 0;
            // Log error
            log_message('error', 'Error fetching API data: ' . $e->getMessage());
        }

        // Kembalikan response dalam format JSON
        return $this->response->setJSON($poDetails);
    }


    public function getQtyPO()
    {
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $color = $this->request->getGet('color');
        $itemTypeEncoded = urldecode($this->request->getGet('item_type'));
        // $idInduk = $this->request->getGet('id_induk');
        $qtyPO = $this->openPoModel->getQtyPO($noModel, $kodeWarna, $color, $itemTypeEncoded);

        if ($qtyPO) {
            return $this->response->setJSON($qtyPO);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }

    public function getStock()
    {
        $kodeWarna = $this->request->getGet('kode_warna');
        $color = $this->request->getGet('color');
        $itemTypeEncoded = urldecode($this->request->getGet('item_type'));
        $cekStok = $this->stockModel->getStockForSchedule($kodeWarna, $color, $itemTypeEncoded);

        if ($cekStok) {
            return $this->response->setJSON($cekStok);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }

    public function getKeterangan()
    {
        $kodeWarna = $this->request->getGet('kode_warna');
        $color = $this->request->getGet('color');
        $itemTypeEncoded = urldecode($this->request->getGet('item_type'));
        $noModel = $this->request->getGet('no_model');
        $cekPO = $this->openPoModel->getKeteranganForSchedule($kodeWarna, $color, $itemTypeEncoded, $noModel);

        if ($cekPO) {
            return $this->response->setJSON($cekPO);
        } else {
            return $this->response->setJSON(['error' => 'No data found']);
        }
    }
    public function saveSchedule()
    {
        $scheduleData = $this->request->getPost();
        // dd($this->request->getPost());

        // Ambil id_mesin dan no_model
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $mesin = $this->mesinCelupModel->getKeteranganMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]
        $dataBatch = []; // Untuk menyimpan batch data

        foreach ($poList as $index => $po) {
            $no_model = $this->masterOrderModel->getNoModel($po);
            $dataBatch[] = [
                'id_mesin' => $id_mesin['id_mesin'],
                'no_model' => $scheduleData['po'][$index],
                'item_type' => $scheduleData['item_type'][$index] ?? null,
                'kode_warna' => $scheduleData['kode_warna'],
                'warna' => $scheduleData['warna'],
                'start_mc' => $scheduleData['tgl_start_mc'][$index] ?? null,
                'kg_celup' => $scheduleData['qty_celup'][$index],
                'lot_urut' => $scheduleData['lot_urut'],
                'lot_celup' => $scheduleData['lot_celup'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'],
                'last_status' => 'scheduled',
                'ket_schedule' => $scheduleData['ket_schedule'][$index] ?? null,
                'po_plus' => $scheduleData['po_plus'][$index] ?? 0,
                'user_cek_status' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $result = $this->scheduleCelupModel->insertBatch($dataBatch);
        // dd($result);

        $mapping = [
            'ACRYLIC'            => 'acrylic',
            'BENANG'             => '',
            'NYLON'              => 'nylon',
            'MC BENANG SAMPLE'   => 'sample',
        ];
        $ket   = strtoupper($mesin['ket_mesin']);
        $view  = $mapping[$ket] ?? 'index';
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        // Cek apakah data berhasil disimpan
        if ($result) {
            return redirect()->to(session()->get('role') . '/schedule?start_date=' . $start_date . '&end_date=' . $end_date)->with('success', 'Jadwal berhasil disimpan!');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan jadwal!');
        }
    }

    public function editSchedule()
    {
        $no_mesin = $this->request->getGet('no_mesin');
        $id_mesin = $this->mesinCelupModel->select('id_mesin')->where('no_mesin', $no_mesin)->first();
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $lot_urut = $this->request->getGet('lot_urut');
        $jenis_bahan_baku = $this->masterMaterialModel->getJenisBahanBaku();

        $min = $this->mesinCelupModel->getMinCaps($no_mesin);
        $max = $this->mesinCelupModel->getMaxCaps($no_mesin);
        $jmlLot = $this->mesinCelupModel->select('jml_lot')
            ->where('no_mesin', $no_mesin)
            ->first();
        $jmlLot = intval($jmlLot['jml_lot']); // Pastikan jmlLot adalah integer
        // dd($jmlLot);
        $scheduleData = $this->scheduleCelupModel->getScheduleDetailsData($id_mesin, $tanggal_schedule, $lot_urut);
        // dd($scheduleData);

        if (!empty($scheduleData['id_induk'])) {
        }
        foreach ($scheduleData as &$item) {
            if (empty($item['id_induk'])) {
                // Tanpa induk: langsung pakai no_model anak
                $masterOrder = $this->masterOrderModel
                    ->where('no_model', $item['no_model'])
                    ->first();

                $item['delivery_awal']  = $masterOrder['delivery_awal'] ?? null;
                $item['delivery_akhir'] = $masterOrder['delivery_akhir'] ?? null;
            } else {
                // Ada induk: ambil no_model induk terlebih dahulu
                $parentPo = $this->openPoModel
                    ->where('id_po', $item['id_induk'])
                    ->first();

                if ($parentPo) {
                    // Bersihkan label "POCOVERING" dari no_model induk
                    $noModelInduk = trim(str_replace('POCOVERING', '', $parentPo['no_model']));

                    $masterOrder = $this->masterOrderModel
                        ->where('no_model', $noModelInduk)
                        ->first();

                    $item['delivery_awal']  = $masterOrder['delivery_awal'] ?? null;
                    $item['delivery_akhir'] = $masterOrder['delivery_akhir'] ?? null;
                } else {
                    // Induk tidak ditemukan
                    $item['delivery_awal']  = null;
                    $item['delivery_akhir'] = null;
                }
            }
        }

        // $jenis = [];
        $kodeWarna = '';
        $warna = '';
        foreach ($scheduleData as &$row) {
            $itemType = $row['item_type'];
            $kodeWarna = $row['kode_warna'];
            // dd ($kodeWarna);
            $noModel = $row['no_model'];
            $warna = $row['warna'];
            $qty_celup = (float) $row['qty_celup']; // Pastikan jadi float
            // $jenis = $this->masterMaterialModel->getJenisByitemType($itemType);
            // Ambil data order dan validasi

            $Order = $this->materialModel->getQtyPOByNoModel($noModel, $itemType, $kodeWarna);
            $qtyPO = $this->openPoModel->getQtyPO($noModel, $kodeWarna, $warna, $itemType);
            $id_induk = $this->openPoModel->getIdInduk($noModel, $itemType, $kodeWarna);
            if ($id_induk) {
                $id_po = $this->openPoModel->find($id_induk['id_induk']);
                if (isset($id_po['kode_warna'], $id_po['color'], $id_po['item_type'])) {
                    $kodeWarnaCovering = $id_po['kode_warna'];
                    $warnaCovering     = $id_po['color'];
                    $itemTypeCovering  = $id_po['item_type'];
                    // dd ($kodeWarnaCovering, $warnaCovering, $itemTypeCovering); 
                    $deliv = $this->openPoModel->getFilteredPO($kodeWarnaCovering, $warnaCovering, $itemTypeCovering);
                    $qtyPO = $this->openPoModel->getQtyPOForCvr($noModel, $itemType, $kodeWarna);
                    // $Order['delivery_awal'] = $deliv[0]['delivery_awal'];
                    // $Order['delivery_akhir'] = $deliv[0]['delivery_akhir'];
                } else {
                    // Tangani kondisi saat id_po tidak memiliki key yang diharapkan.
                    // Misalnya, log error atau set nilai default
                    log_message('error', 'Field kode_warna tidak ditemukan pada hasil openPoModel->find()');
                }
            }

            $kg_kebutuhan = $this->openPoModel->getKgKebutuhan($noModel, $itemType, $kodeWarna);
            // $row['delivery_awal'] = $Order['delivery_awal'] ?? null;
            // $row['delivery_akhir'] = $Order['delivery_akhir'] ?? null;
            $row['qty_po'] = $qtyPO['kg_po'] ?? 0;
            $row['qty_po_plus'] = $qtyPO['qty_po_plus'] ?? 0;

            // Pastikan 'kg_po' ada di $kg_kebutuhan
            $kg_po = isset($kg_kebutuhan['kg_po']) ? (float) $kg_kebutuhan['kg_po'] : 0;
            $row['kg_kebutuhan'] = $kg_po;
            $tagihan = $this->openPoModel->getQtyPO($noModel, $kodeWarna, $warna, $itemType);
            $row['sisa_jatah'] = $tagihan['sisa_kg_po'] ?? 0;
        }
        unset($row);
        $historySch = $this->scheduleCelupModel->getHistorySch($id_mesin, $tanggal_schedule, $lot_urut);
        if (empty($historySch)) {
            $this->create();
        }
        // dd($historySch);
        // dd($scheduleData);
        // Persiapkan data untuk view
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'no_mesin' => $no_mesin,
            'tanggal_schedule' => $tanggal_schedule,
            'lot_urut' => $lot_urut,
            'scheduleData' => $scheduleData,
            'jenis_bahan_baku' => $jenis_bahan_baku,
            'readonly' => true,
            'min_caps' => $min['min_caps'],
            'max_caps' => $max['max_caps'],
            // 'jenis' => $jenis[0]['jenis'],
            'kode_warna' => $kodeWarna,
            'warna' => $warna,
            'jmlLot' => $jmlLot,
            'history' => $historySch,
            'start_date' => $this->request->getGet('start_date'),
            'end_date' =>  $this->request->getGet('end_date')
        ];

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
        // dd($scheduleData);
        // Ambil id_mesin berdasarkan no_mesin yang dikirimkan
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]

        // dd ($scheduleData, $id_mesin, $poList);

        $dataBatch = []; // Untuk menyimpan batch data
        $updateMessage = null; // Menyimpan status pesan update atau insert

        $i = 0; // Counter untuk sinkronisasi indeks scheduleData
        foreach ($poList as $key => $po) {
            // Dapatkan id_celup dari data (bisa null jika baris baru)
            $id_celup = $scheduleData['id_celup'][$i] ?? null;
            $postedPoPlus = $scheduleData['po_plus'] ?? [];

            // Jika id_celup sudah ada, coba ambil data schedule dari database
            if (!empty($id_celup)) {
                $existingSchedule = $this->scheduleCelupModel->find($id_celup);
                if ($existingSchedule && !empty($existingSchedule['no_model'])) {
                    // Jika no_model sudah ada di schedule, gunakan itu
                    $no_model = $existingSchedule['no_model'];
                } else {
                    // Jika tidak ada, ambil dari masterOrderModel
                    $findNoModel = $this->masterOrderModel->getNoModel($po);
                    if (empty($findNoModel)) {
                        // Misalnya, log error atau tetapkan nilai default
                        log_message('error', "No model not found for PO: {$po}");
                        $no_model = $scheduleData['po'][$i];
                    } else {
                        $no_model = $findNoModel['no_model'];
                    }
                }
            } else {
                // Jika id_celup null, ambil no_model dari masterOrderModel
                $findNoModel = $this->masterOrderModel->getNoModel($po);
                if (empty($findNoModel)) {
                    // Misalnya, log error atau tetapkan nilai default
                    log_message('error', "No model not found for PO: {$po}");
                    $no_model = $scheduleData['po'][$i];
                } else {
                    $no_model = $findNoModel['no_model'];
                }
            }

            $poPlusValue = '0';
            if (!empty($id_celup) && isset($postedPoPlus[$id_celup])) {
                $poPlusValue = (string)$postedPoPlus[$id_celup];
            } elseif (isset($postedPoPlus[$i])) {
                $poPlusValue = (string)$postedPoPlus[$i];
            } elseif (is_array($postedPoPlus) && array_values($postedPoPlus) != $postedPoPlus && count($postedPoPlus) == 1) {
                $poPlusValue = (string)reset($postedPoPlus);
            }

            // Ambil nilai lainnya dengan menggunakan indeks counter $i
            $last_status    = $scheduleData['last_status'][$i] ?? 'scheduled';
            $start_mc       = $scheduleData['tgl_start_mc'][$i] ?? null;
            $delivery_awal  = $scheduleData['delivery_awal'][$i] ?? null;
            $delivery_akhir = $scheduleData['delivery_akhir'][$i] ?? null;

            $dataBatch[] = [
                'id_celup'         => $id_celup, // Bisa null untuk baris baru
                'id_mesin'         => $id_mesin['id_mesin'],
                'no_model'         => $no_model, // Gunakan no_model yang sudah didapat
                'item_type'        => $scheduleData['item_type'][$i] ?? null,
                // Pastikan indeks digunakan jika data berupa array per baris
                'kode_warna'       => $scheduleData['kode_warna'] ?? null,
                'warna'            => $scheduleData['warna'] ?? null,
                'start_mc'         => $start_mc,
                'kg_celup'         => $scheduleData['qty_celup'][$i] ?? null,
                'po_plus'          => $poPlusValue,
                'lot_urut'         => $scheduleData['lot_urut'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'] ?? null,
                'ket_schedule'     => $scheduleData['ket_schedule'][$i] ?? null,
                'last_status'      => $last_status,
                'user_cek_status'  => session()->get('username'),
                'created_at'       => date('Y-m-d H:i:s'),
            ];

            $i++; // Naikkan counter agar indeks scheduleData selalu berurutan
        }
        // dd($dataBatch); // Hapus dd() untuk melanjutkan proses update/insert

        // Proses update atau insert
        foreach ($dataBatch as $data) {
            if (!empty($data['id_celup'])) {
                // Jika id_celup ada, periksa apakah data sudah ada di database
                $existingSchedule = $this->scheduleCelupModel->find($data['id_celup']);
                if ($existingSchedule) {
                    // Periksa apakah ada perubahan data (kecuali created_at & user_cek_status)
                    $hasChanges = false;
                    foreach ($data as $key => $value) {
                        if ($key !== 'created_at' && $key !== 'user_cek_status' && isset($existingSchedule[$key]) && $existingSchedule[$key] != $value) {
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
                    // Jika id_celup ada tetapi data tidak ditemukan (misalnya, data telah dihapus), insert data baru
                    $insertSuccess = $this->scheduleCelupModel->insert($data);
                    if ($insertSuccess) {
                        $updateMessage = 'Jadwal berhasil disimpan!';
                    } else {
                        $updateMessage = 'Gagal menyimpan jadwal!';
                    }
                }
            } else {
                // Jika id_celup null, berarti data baru, lakukan insert
                $insertSuccess = $this->scheduleCelupModel->insert($data);
                if ($insertSuccess) {
                    $updateMessage = 'Jadwal berhasil disimpan!';
                } else {
                    $updateMessage = 'Gagal menyimpan jadwal!';
                }
            }
        }

        // Setelah proses selesai, Anda bisa mengembalikan response atau redirect sesuai kebutuhan
        // Contoh:
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        if ($updateMessage) {
            return redirect()->to(session()->get('role') . '/schedule?start_date=' . $start_date . '&end_date=' . $end_date)->with('success', $updateMessage);
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

                if ($sisa_jatah < 0) {
                    $errors[] = [
                        'message' => 'Sisa Jatah tidak mencukupi.',
                        'sisa_jatah' => $sisa_jatah,
                        'total_kg' => $total_kg,
                        'qty_po' => $qty_po,
                        'qtycelup' => $qty_celup,
                    ];
                    $isValid = false;
                } else {
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

    public function sample()
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
        $mesin_celup = $this->mesinCelupModel->getMesinCelupSample();

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
        return view($this->role . '/schedule/sample', $data);
    }

    public function reqschedule()
    {
        $filterTglSch = $this->request->getPost('filter_tglsch');
        $filterTglSchsampai = $this->request->getPost('filter_tglschsampai');
        $filterNoModel = $this->request->getPost('filter_nomodel');
        $showExcel = (!empty($filterTglSch) || !empty($filterTglSchsampai) || !empty($filterNoModel));

        $sch = $this->scheduleCelupModel->getSchedule($filterTglSch, $filterTglSchsampai, $filterNoModel);

        // fetching delivery
        $listPdk = $this->masterOrderModel->getNullDeliv() ?? null;
        if ($listPdk) {
            $client = \Config\Services::curlrequest([
                'baseURI' => 'http://172.23.39.117/CapacityApps/public/api/',
                'timeout' => 5
            ]);

            // 3) Loop dan merge API result
            foreach ($listPdk as &$row) {
                try {
                    $res = $client->get('getDeliveryAwalAkhir', [
                        'query' => ['model' => $row['no_model']]
                    ]);
                    $body = json_decode($res->getBody(), true);
                    $this->masterOrderModel->updateDeliv($row['no_model'], $body);
                    continue;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }


        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'data_sch' => $sch,
            'showExcel' => $showExcel,
            'filterTglSch' => $filterTglSch,
            'filterTglSchsampai' => $filterTglSchsampai,
            'filterNoModel' => $filterNoModel,
        ];
        return view($this->role . '/schedule/reqschedule', $data);
    }

    public function showschedule($id)
    {
        $sch = $this->scheduleCelupModel->getDataByIdCelup($id);
        $uniqueData = [];
        foreach ($sch as $key => $id) {
            // Ambil parameter dari data schedule
            $nomodel = $id['no_model'];
            $itemtype = $id['item_type'];
            $kodewarna = $id['kode_warna'];

            // Debug untuk memastikan parameter tidak null

            $pdk = $this->materialModel->getQtyPOForCelup($nomodel, $itemtype, $kodewarna);
            // Pastikan $pdk memiliki data valid sebelum dipakai
            if (!$pdk) {
                $id_induk = $this->openPoModel->getIdInduk($nomodel, $itemtype, $kodewarna);
                if ($id_induk) {
                    $id_po = $this->openPoModel->find($id_induk['id_induk']);
                    if (isset($id_po['kode_warna'], $id_po['color'], $id_po['item_type'])) {
                        $kodeWarnaCovering = $id_po['kode_warna'];
                        $warnaCovering     = $id_po['color'];
                        $itemTypeCovering  = $id_po['item_type'];
                        // dd ($kodeWarnaCovering, $warnaCovering, $itemTypeCovering); 
                        $deliv = $this->openPoModel->getFilteredPO($kodeWarnaCovering, $warnaCovering, $itemTypeCovering);
                        $pdk = $this->openPoModel->getQtyPOForCvr($nomodel, $itemtype, $kodewarna);
                        $pdk['delivery_awal'] = $deliv[0]['delivery_awal'];
                        $pdk['delivery_akhir'] = $deliv[0]['delivery_akhir'];
                    } else {

                        log_message('error', 'Field kode_warna tidak ditemukan pada hasil openPoModel->find()');
                    }
                }
            }
            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];

            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {
                // Buat array data unik
                $uniqueData[$keys] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $id['warna'],
                    'start_mc' => $id['start_mc'],
                    'del_awal' => $pdk['delivery_awal'],
                    'del_akhir' => $pdk['delivery_akhir'],
                    'qty_po' => $pdk['qty_po'],
                    'qty_po_plus' => 0,
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                    'tgl_bon' => $id['tanggal_bon'],
                    'tgl_celup' => $id['tanggal_celup'],
                    'tgl_bongkar' => $id['tanggal_bongkar'],
                    'tgl_press' => $id['tanggal_press'],
                    'tgl_oven' => $id['tanggal_oven'],
                    'tgl_tl' => $id['tanggal_tl'],
                    'tgl_teslab' => $id['tanggal_teslab'],
                    'tgl_rajut_pagi' => $id['tanggal_rajut_pagi'],
                    'tgl_kelos' => $id['tanggal_kelos'],
                    'tgl_acc' => $id['tanggal_acc'],
                    'tgl_reject' => $id['tanggal_reject'],
                    'tgl_pb' => $id['tanggal_perbaikan'],
                    'last_status' => $id['last_status'],
                    'ket_daily_cek' => $id['ket_daily_cek'],
                    'qty_celup_plus' => $id['qty_celup_plus'],
                    'admin' => $id['user_cek_status'],
                ];
            }
        }
        // dd($uniqueData);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
            'po' => array_column($uniqueData, 'no_model'),
        ];
        return view($this->role . '/schedule/form-show', $data);
    }

    public function reportSchBenang()
    {
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
            ];
        return view($this->role . '/schedule/report-schedule-benang', $data);
    }

    public function filterSchBenang()
    {
        $key = $this->request->getGet('key') ?? '';
        $tanggalSch = $this->request->getGet('tanggal_schedule') ?? '';
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchBenang($tanggalAwal, $tanggalAkhir, $key, $tanggalSch);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function reportSchNylon()
    {
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
            ];
        return view($this->role . '/schedule/report-schedule-nylon', $data);
    }

    public function filterSchNylon()
    {
        $key = $this->request->getGet('key') ?? '';
        $tanggalSch = $this->request->getGet('tanggal_schedule') ?? '';
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchNylon($tanggalAwal, $tanggalAkhir, $key, $tanggalSch);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function reportSchWeekly()
    {
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
            ];
        return view($this->role . '/schedule/report-schedule-weekly', $data);
    }

    public function filterSchWeekly()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');
        $jenis = $this->request->getGet('jenis') ?? 'all';
        $data = $this->scheduleCelupModel->getFilterSchWeekly($tglAwal, $tglAkhir, $jenis);

        return $this->response->setJSON($data);
    }

    public function reportDataTagihanBenang()
    {
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
            ];
        return view($this->role . '/schedule/report-tagihan-benang', $data);
    }

    public function filterTagihanBenang()
    {
        $noModel       = $this->request->getGet('no_model');
        $kodeWarna     = $this->request->getGet('kode_warna');
        $deliveryAwal  = $this->request->getGet('delivery_awal');
        $deliveryAkhir = $this->request->getGet('delivery_akhir');
        $tglAwal       = $this->request->getGet('tanggal_awal');
        $tglAkhir      = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchTagihanBenang($noModel, $kodeWarna, $deliveryAwal, $deliveryAkhir, $tglAwal, $tglAkhir);

        foreach ($data as &$row) {
            $stockAwal    = (float) $row['stock_awal'];
            $datangSolid  = (float) $row['qty_datang_solid'];
            $gantiRetur   = (float) $row['qty_ganti_retur_solid']; // =0 jika null
            $qtyPo        = (float) $row['qty_po'];
            $poPlus       = (float) ($row['po_plus'] ?? 0);
            $returBelang  = (float) ($row['retur_belang'] ?? 0);

            if ($gantiRetur > 0) {
                $tagihanDatang = ($stockAwal + $datangSolid + $gantiRetur) - $qtyPo - $poPlus - $returBelang;
            } else {
                $tagihanDatang = ($stockAwal + $datangSolid) - $qtyPo - $poPlus;
            }
            // tambahkan ke array
            $row['tagihan_datang'] = $tagihanDatang;
        }
        unset($row);

        return $this->response->setJSON($data);
    }
    public function saveScheduleSample()
    {
        $scheduleData = $this->request->getPost();
        // dd($this->request->getPost());

        // Ambil id_mesin dan no_model
        $id_mesin = $this->mesinCelupModel->getIdMesin($scheduleData['no_mesin']);
        $mesin = $this->mesinCelupModel->getKeteranganMesin($scheduleData['no_mesin']);
        $poList = $scheduleData['po']; // Array po[]
        // dd ($poList);
        $dataBatch = []; // Untuk menyimpan batch data

        foreach ($poList as $index => $po) {
            $no_model = $this->masterOrderModel->where('no_model', $po)->first();
            // dd ($po,$no_model);
            if (empty($no_model)) {
                // data insert ke master_order
                $noOrder = '';
                $cekModel = $scheduleData['po'][$index];
                $buyer = '';
                $FU = '';
                $lcodate = date('Y-m-d');
                $deliveryAwal = $scheduleData['delivery_awal'][$index] ?? null;
                $deliveryAkhir = $scheduleData['delivery_akhir'][$index] ?? null;
                $admin = session()->get('username');
                // dd ($no_model, $buyer, $FU, $lcodate, $deliveryAwal, $deliveryAkhir, $admin);
                // inser data ke master_order
                $this->masterOrderModel->insert([
                    'no_order' => $noOrder,
                    'no_model' => $cekModel,
                    'buyer' => $buyer,
                    'foll_up' => $FU,
                    'lco_date' => $lcodate,
                    'delivery_awal' => $deliveryAwal,
                    'delivery_akhir' => $deliveryAkhir,
                    'admin' => $admin,
                ]);

                // get inserted id_order
                $newOrder = $this->masterOrderModel->getInsertID();
                // dd ($newOrder);
                $masterMaterial = $this->masterMaterialModel->where('item_type', $scheduleData['item_type'][$index])->first();
                // dd ($masterMaterial);
                if (empty($masterMaterial)) {

                    // dd ($scheduleData['item_type'][$index], $scheduleData['jenis_bahan_baku']);
                    // Jika tidak ada data di master_material, buat data baru
                    $this->masterMaterialModel->insert([
                        'item_type' => $scheduleData['item_type'][$index],
                        'deskripsi' => $scheduleData['item_type'][$index],
                        'jenis' => $scheduleData['jenis_bahan_baku']
                    ]);

                    // dd ($newOrder, $scheduleData['item_type'][$index], $scheduleData['warna'], $scheduleData['kode_warna'], $scheduleData['qty_celup'][$index]);
                    // data insert ke Material
                }
                $this->materialModel->insert([
                    'id_order' => $newOrder,
                    'style_size' => '',
                    'area' => 'SAMPLE',
                    'color' => $scheduleData['warna'],
                    'item_type' => $scheduleData['item_type'][$index],
                    'kode_warna' => $scheduleData['kode_warna'],
                    'composition' => '',
                    'gw' => 0,
                    'qty_pcs' => 0,
                    'loss' => 0,
                    'kgs' => $scheduleData['qty_po'][$index],
                    'admin' => session()->get('username')
                ]);
            }
            // data untuk insert ke po
            $this->openPoModel->insert([
                'no_model' => $no_model['no_model'] ?? $cekModel,
                'item_type' => $scheduleData['item_type'][$index],
                'kode_warna' => $scheduleData['kode_warna'],
                'color' => $scheduleData['warna'],
                'kg_po' => $scheduleData['qty_po'][$index],
                'keterangan' => '',
                'penerima' => 'RETNO',
                'penanggung_jawab' => session()->get('username'),
                'po_plus' => $scheduleData['po_plus'][$index] ?? 0,
                'admin' => session()->get('username')
            ]);
            // dd ($newOrder);

            $dataBatch[] = [
                'id_mesin' => $id_mesin['id_mesin'],
                'no_model' => $scheduleData['po'][$index],
                'item_type' => $scheduleData['item_type'][$index] ?? null,
                'kode_warna' => $scheduleData['kode_warna'],
                'warna' => $scheduleData['warna'],
                'start_mc' => $scheduleData['tgl_start_mc'][$index] ?? null,
                'kg_celup' => $scheduleData['qty_celup'][$index],
                'lot_urut' => $scheduleData['lot_urut'],
                'lot_celup' => $scheduleData['lot_celup'] ?? null,
                'tanggal_schedule' => $scheduleData['tanggal_schedule'],
                'last_status' => 'scheduled',
                'ket_schedule' => $scheduleData['ket_schedule'][$index] ?? null,
                'po_plus' => $scheduleData['po_plus'][$index] ?? 0,
                'user_cek_status' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $result = $this->scheduleCelupModel->insertBatch($dataBatch);
        // dd($result);

        $mapping = [
            'ACRYLIC'            => 'acrylic',
            'BENANG'             => '',
            'NYLON'              => 'nylon',
            'MC BENANG SAMPLE'   => 'sample',
        ];
        $ket   = strtoupper($mesin['ket_mesin']);
        $view  = $mapping[$ket] ?? 'index';
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        // Cek apakah data berhasil disimpan
        if ($result) {
            return redirect()->to(session()->get('role') . '/schedule?start_date=' . $start_date . '&end_date=' . $end_date)->with('success', 'Jadwal berhasil disimpan!');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan jadwal!');
        }
    }

    public function statusBahanBaku()
    {
        return view($this->role . '/statusbahanbaku/index', [
            'active' => $this->active,
            'title' => 'Status Bahan Baku',
            'role' => $this->role,
        ]);
    }

    public function filterstatusbahanbaku()
    {
        // Mengambil data master
        $model = $this->request->getGet('model');
        $search = $this->request->getGet('search');
        if (!empty($model)) {
            $masterApi = 'http://172.23.39.117/CapacityApps/public/api/getStartMc/' . $model;
            $masterResponse = file_get_contents($masterApi);
            $master = json_decode($masterResponse, true);
        } else {
            $master = [
                'kd_buyer_order' => '-',
                'no_model'       => '-',
                'delivery_awal'  => '-',  // MIN dari apsperstyle.delivery
                'delivery_akhir' => '-',  // MAX dari apsperstyle.delivery
                'start_mc'       => '-' // MIN dari tanggal_planning.start_mesin
            ];
        }


        // Mengambil nilai 'search' yang dikirim oleh frontend
        // Jika search ada, panggil API eksternal dengan query parameter 'search'
        $params = [
            'model'  => $model ?? '',
            'search' => $search ?? ''
        ];

        $apiUrl = 'http://172.23.39.117/MaterialSystem/public/api/statusbahanbaku/?' . http_build_query($params);

        // Mengambil data dari API eksternal
        $response = file_get_contents($apiUrl);
        $status = json_decode($response, true);
        // dd($response);
        // Filter data berdasarkan 'no_model' jika ada keyword 'search'

        // Gabungkan data master dan status dalam satu array
        $responseData = [
            'master' => $master, // Data master dari getStartMc
            'status' => $status // Data status yang sudah difilter (gunakan array_values untuk mereset indeks array)
        ];

        // Kembalikan data yang sudah difilter ke frontend
        return $this->response->setJSON($responseData);
    }

    public function getDataEditSchedule()
    {
        $request = service('request');
        $postData = $request->getPost();

        $draw = $postData['draw'];
        $start = $postData['start'];
        $length = $postData['length'];
        $search = $postData['search']['value'];

        $filterTglSch       = $postData['filter_tglsch'] ?? null;
        $filterTglSchsampai = $postData['filter_tglschsampai'] ?? null;

        $select = 'schedule_celup.*, mesin_celup.no_mesin as no_mesin';

        $builder = $this->scheduleCelupModel
            ->select($select)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin', 'left')
            ->where('schedule_celup.id_celup !=', null)
            ->where('schedule_celup.id_mesin !=', null);

        if (!empty($filterTglSch) && !empty($filterTglSchsampai)) {
            $builder->where('schedule_celup.tanggal_schedule >=', $filterTglSch)
                ->where('schedule_celup.tanggal_schedule <=', $filterTglSchsampai);
        } elseif (!empty($filterTglSch)) {
            $builder->where('schedule_celup.tanggal_schedule', $filterTglSch);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $search)
                ->orLike('schedule_celup.lot_celup', $search)
                ->orLike('schedule_celup.kode_warna', $search)
                ->groupEnd();
        }

        $totalFiltered = $this->scheduleCelupModel->countAllResults(false);

        $data = $builder
            ->orderBy('schedule_celup.id_celup', 'DESC')
            ->findAll($length, $start);

        // Buat response array
        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {

            $result[] = [
                'no' => "<span>{$no}</span>",
                'no_mc' => "<span>{$row['no_mesin']}</span>",
                'no_model' => "<span>{$row['no_model']}</span>",
                'item_type' => "<span>{$row['item_type']}</span>",
                'lot_celup' => "<span>{$row['lot_celup']}</span>",
                'kode_warna' => "<span>{$row['kode_warna']}</span>",
                'warna' => "<span>{$row['warna']}</span>",
                'start_mc' => "<span>" .
                    (!empty($row['start_mc']) && $row['start_mc'] !== '0000-00-00 00:00:00'
                        ? date('d-m-Y', strtotime($row['start_mc']))
                        : 'Belum update')
                    . "</span>",
                'tanggal_schedule' => "<span>{$row['tanggal_schedule']}</span>",
                'ket_schedule' => "<span class='wrap-text'>{$row['ket_schedule']}</span>",
                'action' => '<a href="' . base_url($this->role . '/edit/' . $row['id_celup']) . '" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail"><i class="fas fa-eye"></i></a>'
            ];
            $no++;
        }

        $totalRecords = $this->scheduleCelupModel
            ->where('id_celup !=', null)
            ->where('id_mesin !=', null)
            ->countAllResults();

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $result
        ]);
    }

    public function getDataSchedule()
    {
        $request = service('request');
        $postData = $request->getPost();

        $draw   = $postData['draw'];
        $start  = $postData['start'];
        $length = $postData['length'];
        $search = trim((string)($postData['search']['value'] ?? ''));
        if ($search === '' && !empty($postData['filter_nomodel'])) {
            $search = trim((string)$postData['filter_nomodel']);
        }

        $filterTglSch       = $postData['filter_tglsch'] ?? null;
        $filterTglSchsampai = $postData['filter_tglschsampai'] ?? null;

        $select = 'schedule_celup.*, 
           mesin_celup.no_mesin as no_mesin,
           IF(schedule_celup.po_plus = "0", schedule_celup.kg_celup, 0) AS qty_celup, 
           IF(schedule_celup.po_plus = "1", schedule_celup.kg_celup, 0) AS qty_celup_plus,
           open_po.kg_po';

        $builder = $this->scheduleCelupModel
            ->select($select)
            ->join('mesin_celup', 'mesin_celup.id_mesin = schedule_celup.id_mesin', 'left')
            ->join('open_po', 'open_po.no_model = schedule_celup.no_model 
                    AND open_po.item_type = schedule_celup.item_type 
                    AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->where('schedule_celup.id_celup !=', null)
            ->where('schedule_celup.id_mesin !=', null);

        if (!empty($filterTglSch) && !empty($filterTglSchsampai)) {
            $builder->where('schedule_celup.tanggal_schedule >=', $filterTglSch)
                ->where('schedule_celup.tanggal_schedule <=', $filterTglSchsampai);
        } elseif (!empty($filterTglSch)) {
            $builder->where('schedule_celup.tanggal_schedule', $filterTglSch);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('schedule_celup.no_model', $search)
                ->orLike('schedule_celup.lot_celup', $search)
                ->orLike('schedule_celup.kode_warna', $search)
                ->groupEnd();
        }

        $totalFiltered = $this->scheduleCelupModel->countAllResults(false);

        $data = $builder
            ->orderBy('schedule_celup.id_celup', 'DESC')
            ->findAll($length, $start);

        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {

            // --- handle PO (+) dan substring ---
            $poFull = $row['no_model'];
            if (isset($row['po_plus']) && $row['po_plus'] == 1) {
                $poFull = '(+) ' . $poFull;
            }
            $poDisplay = strlen($poFull) > 27 ? substr($poFull, 0, 27) . '...' : $poFull;

            // --- tombol action hanya kalau last_status != complain ---
            $actionBtn = '';
            if ($row['last_status'] != 'complain') {
                $actionBtn = '<a href="' . base_url($this->role . '/schedule/reqschedule/show/' . $row['id_celup']) . '" 
                            class="btn btn-info" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Lihat Detail">
                            <i class="fas fa-eye"></i></a>';
            }

            $result[] = [
                'no'                => "<span>{$no}</span>",
                'no_mc'             => "<span>{$row['no_mesin']}</span>",
                'no_model'          => "<span>{$poDisplay}</span>",
                'item_type'         => "<span>{$row['item_type']}</span>",
                'lot_celup'         => "<span>{$row['lot_celup']}</span>",
                'kode_warna'        => "<span>{$row['kode_warna']}</span>",
                'warna'             => "<span>{$row['warna']}</span>",
                'start_mc'          => "<span>" .
                    (!empty($row['start_mc']) && $row['start_mc'] !== '0000-00-00 00:00:00'
                        ? date('d-m-Y', strtotime($row['start_mc']))
                        : 'Belum update')
                    . "</span>",
                'tanggal_schedule'  => "<span>{$row['tanggal_schedule']}</span>",
                'last_status'       => "<span>{$row['last_status']}</span>",
                'kg_po'             => "<span>{$row['kg_po']}</span>",
                'kg_celup'          => "<span>{$row['kg_celup']}</span>",
                'ket_schedule'      => "<span class='wrap-text'>{$row['ket_schedule']}</span>",
                'action'            => $actionBtn
            ];
            $no++;
        }

        $totalRecords = $this->scheduleCelupModel
            ->where('id_celup !=', null)
            ->where('id_mesin !=', null)
            ->countAllResults();

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $result
        ]);
    }
}
