<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use FontLib\Table\Type\post;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;
use App\Models\ClusterModel;
use App\Models\PemasukanModel;
use App\Models\StockModel;
use App\Models\HistoryPindahPalet;
use App\Models\HistoryPindahOrder;
use App\Models\PengeluaranModel;
use App\Models\PemesananModel;
use App\Models\TotalPemesananModel;
use App\Models\OtherOutModel;
use App\Models\PemesananSpandexKaretModel;
use App\Models\ReturModel;
use App\Models\PoTambahanModel;
use App\Models\HistoryStock;
use App\Models\MasterRangePemesanan;
use CodeIgniter\API\ResponseTrait;


class PemesananController extends BaseController
{
    use ResponseTrait;
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
    protected $clusterModel;
    protected $pemasukanModel;
    protected $stockModel;
    protected $historyPindahPalet;
    protected $historyPindahOrder;
    protected $pengeluaranModel;
    protected $pemesananModel;
    protected $totalPemesananModel;
    protected $otherOutModel;
    protected $pemesananSpandexKaretModel;
    protected $poTambahanModel;
    protected $returModel;
    protected $historyStock;
    protected $masterRangePemesanan;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->clusterModel = new ClusterModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->stockModel = new StockModel();
        $this->historyPindahPalet = new HistoryPindahPalet();
        $this->historyPindahOrder = new HistoryPindahOrder();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->pemesananModel = new PemesananModel();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->otherOutModel = new OtherOutModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->poTambahanModel = new PoTambahanModel();
        $this->returModel = new ReturModel();
        $this->historyStock = new HistoryStock();
        $this->masterRangePemesanan = new MasterRangePemesanan();

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
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);
        $data = [
            'active' => $this->active,
            'title' => 'Pemesanan',
            'role' => $this->role,
            'area' => $area,
        ];
        return view($this->role . '/pemesanan/index', $data);
    }
    public function pemesananPerArea($ar)
    {
        $jenis = $this->masterMaterialModel->getJenis();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis,
            'area' => $ar,
        ];
        return view($this->role . '/pemesanan/pemesanan', $data);
    }

    public function pemesanan($area, $jenis)
    {
        $pemesananPertgl = $this->pemesananModel->getDataPemesananperTgl($area, $jenis);
        // dd ($pemesananPertgl);
        if (!is_array($pemesananPertgl)) {
            $pemesananPertgl = []; // Pastikan selalu array
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'area' => $area,
            'jenis' => $jenis,
            'pemesananPertgl' => $pemesananPertgl,
        ];
        return view($this->role . '/pemesanan/pemesananpertgl', $data);
    }

    public function detailPemesanan($area, $jenis, $tglPakai)
    {
        $dataPemesanan = $this->totalPemesananModel->getDataPemesanan($area, $jenis, $tglPakai);
        // dd ($dataPemesanan);
        if (!is_array($dataPemesanan)) {
            $dataPemesanan = [];
        }

        // Cek apakah sudah ada di pemesanan spandex karet
        foreach ($dataPemesanan as &$item) {
            $cekSpandex = $this->pemesananSpandexKaretModel
                ->where('id_total_pemesanan', $item['id_total_pemesanan'])
                ->first();
            $item['sudah_pesan_spandex'] = $cekSpandex ? true : false;
            $item['status'] = $cekSpandex ? $cekSpandex['status'] : 'BELUM PESAN';
        }
        // dd($dataPemesanan);

        $listPemesanan = $this->pemesananSpandexKaretModel->getListPemesananSpandexKaret($area, $jenis, $tglPakai);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataPemesanan' => $dataPemesanan,
            'area' => $area,
            'jenis' => $jenis,
            'tglPakai' => $tglPakai,
        ];

        return view($this->role . '/pemesanan/detailpemesanan', $data);
    }



    public function getStockByParams()
    {
        if ($this->request->isAJAX()) {
            $no_model = $this->request->getPost('no_model');
            $item_type = $this->request->getPost('item_type');
            $kode_warna = $this->request->getPost('kode_warna');
            $warna = $this->request->getPost('warna');

            $stockModel = new StockModel();
            $stocks = $stockModel->where([
                'no_model' => $no_model,
                'item_type' => $item_type,
                'kode_warna' => $kode_warna,
                'warna' => $warna
            ])->findAll(); // Ambil semua data yang cocok

            return $this->response->setJSON($stocks);
        }
    }


    public function filterPemesanan()
    {
        $area = $this->request->getPost('area');
        $jenis = $this->request->getPost('jenis');
        $filterDate = $this->request->getPost('filter_date');

        // log_message('debug', "Filter params: Area=$area, Jenis=$jenis, Tanggal=$filterDate");

        $dataPemesanan = $this->pemesananModel->getDataPemesananfiltered($area, $jenis, $filterDate);

        // log_message('debug', 'Data retrieved: ' . json_encode($dataPemesanan));

        return $this->response->setJSON($dataPemesanan);
    }

    public function pengirimanArea()
    {
        // Simpan data form ke session (jika diperlukan)
        $formData = [];
        session()->set('pengirimanForm', $formData);

        $id = $this->request->getPost('barcode');

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataPengiriman') ?? [];

        if (!empty($id)) {

            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
                    return redirect()->to(base_url($this->role . '/pengiriman_area'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $outJalur = $this->pengeluaranModel->getDataForOut($id);

            if (empty($outJalur)) {
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
                return redirect()->to(base_url($this->role . '/pengiriman_area'));
            } else {
                // Tambahkan data baru ke dalam array
                $existingData = array_merge($existingData, $outJalur);
            }

            // Simpan kembali ke session
            session()->set('dataPengiriman', $existingData);
            session()->set('pengirimanForm', $formData);

            // Redirect agar form tidak resubmit saat refresh
            return redirect()->to(base_url($this->role . '/pengiriman_area'));
        }

        // Persiapkan data untuk dikirim ke view
        $formData = session()->get('pengirimanForm') ?? [];
        $data = [
            'active'    => $this->active,
            'title'     => 'Material System',
            'role'      => $this->role,
            'dataOut'   => $existingData, // Tampilkan data dari session
            'error'     => session()->getFlashdata('error'),
            'area'      => $formData['area'] ?? '',
            'tgl_pakai' => $formData['tgl_pakai'] ?? '',
            'no_model'  => $formData['no_model'] ?? '',
            'item_type' => $formData['item_type'] ?? '',
            'kode_warna' => $formData['kode_warna'] ?? '',
            'warna'     => $formData['warna'] ?? '',
            'kgs_pesan' => $formData['kgs_pesan'] ?? '',
            'cns_pesan' => $formData['cns_pesan'] ?? '',
        ];

        return view($this->role . '/warehouse/form-pengiriman', $data);
    }


    // jangan di hapus , ini untuk view yg lama
    // public function pengirimanAreaManual()
    // {
    //     // Ambil current orders dari session, atau [] bila belum ada
    //     $data['delivery_area'] = session()->get('manual_delivery') ?? [];
    //     // dd ($data['delivery_area']);
    //     // dd  ($data['delivery_area']);
    //     // Ambil data dari database untuk dropdown
    //     // $item_types = $this->pengeluaranModel->getItemTypes();
    //     $data = [
    //         'active' => $this->active,
    //         'role' => $this->role,
    //         'title' => 'Form Pengiriman Manual',
    //         // 'item_types' => $item_types
    //     ];

    //     return view($this->role . '/warehouse/form-pengiriman-manual', $data);
    // }
    public function pengirimanAreaManual()
    {
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);
        $area = json_decode($response, true);

        $data = [
            'active' => $this->active,
            'role' => $this->role,
            'title' => 'Form Pengiriman Manual',
            'area' => $area
        ];

        return view($this->role . '/warehouse/form-pengiriman-manual2', $data);
    }


    public function getItemTypes()
    {
        if ($this->request->isAJAX()) {
            $no_model = $this->request->getGet('no_model');
            $item_types = $this->pengeluaranModel->getItemTypes($no_model);
            return $this->response->setJSON($item_types);
        }
    }

    public function getKodeWarna()
    {
        if ($this->request->isAJAX()) {
            $model = $this->request->getGet('no_model');
            $item_type = $this->request->getGet('item_type');
            $kodeWarna = $this->pengeluaranModel->getKodeWarna($model, $item_type);

            return $this->response->setJSON($kodeWarna);
        }
    }

    public function getWarna()
    {
        if ($this->request->isAJAX()) {
            $model = $this->request->getGet('no_model');
            $item_type = $this->request->getGet('item_type');
            $kode_warna = $this->request->getGet('kode_warna');
            $warna = $this->pengeluaranModel->getWarna($model, $item_type, $kode_warna);

            return $this->response->setJSON($warna);
        }
    }

    // novan
    // public function saveSessionDeliveryArea()
    // {
    //     // Ambil semua input POST
    //     $postData = $this->request->getPost();

    //     // Validasi data yang diperlukan: bisa menghasilkan array of records
    //     $validDatas = $this->pengeluaranModel->validateDeliveryData($postData);

    //     // Jika tidak ada data valid, kembalikan error
    //     if (empty($validDatas) || !is_array($validDatas)) {
    //         return $this->response
    //             ->setStatusCode(400)
    //             ->setJSON([
    //                 'success' => false,
    //                 'message' => 'Tidak ada data baru atau data sudah dikirim sebelumnya'
    //             ]);
    //     }

    //     /** @var \CodeIgniter\Session\Session */
    //     $session = session();

    //     // Ambil data session manual_delivery (jika belum ada, inisialisasi array kosong)
    //     $manualDelivery = $session->get('manual_delivery') ?? [];

    //     $addedCount = 0;

    //     foreach ($validDatas as $row) {
    //         // Cek duplikasi berdasarkan id_out_celup + area + tanggal
    //         $isDuplicate = array_filter($manualDelivery, function ($item) use ($row) {
    //             return
    //                 $item['id_out_celup'] == $row['id_out_celup']
    //                 && $item['area_out']  == $row['area_out']
    //                 && $item['tgl_out']   == $row['tgl_out'];
    //         });

    //         if ($isDuplicate) {
    //             // Lewati record yang sudah ada
    //             continue;
    //         }

    //         // Tambahkan ke array session
    //         $manualDelivery[] = [
    //             'id_pengeluaran' => $row['id_pengeluaran'],
    //             'id_out_celup'   => $row['id_out_celup'] ?? '',
    //             'no_model'       => $row['no_model']    ?? '',
    //             'item_type'      => $row['item_type']   ?? '',
    //             'jenis'          => $row['jenis']       ?? '',
    //             'kode_warna'     => $row['kode_warna']  ?? '',
    //             'warna'          => $row['warna']       ?? '',
    //             'area_out'       => $row['area_out']    ?? '',
    //             'tgl_out'        => $row['tgl_out']     ?? '',
    //             'kgs_out'        => $row['kgs_out']     ?? $row['ttl_kg'] ?? 0,
    //             'cns_out'        => $row['cns_out']     ?? $row['ttl_cns'] ?? 0,
    //             'krg_out'        => 0, // asumsi default
    //             'lot_out'        => $row['lot_out']     ?? '',
    //             'nama_cluster'   => $row['nama_cluster'] ?? '',
    //             'admin'          => $session->get('username')
    //         ];

    //         $addedCount++;
    //     }

    //     // Simpan kembali session
    //     $session->set('manual_delivery', $manualDelivery);

    //     if ($addedCount === 0) {
    //         return $this->response
    //             ->setStatusCode(409)
    //             ->setJSON([
    //                 'success' => false,
    //                 'message' => 'Semua data sudah ada di session'
    //             ]);
    //     }

    //     return $this->response
    //         ->setStatusCode(200)
    //         ->setJSON([
    //             'success'   => true,
    //             'message'   => "{$addedCount} record berhasil ditambahkan"
    //         ]);
    // }

    // alfa
    // public function saveSessionDeliveryArea()
    // {
    //     try {
    //         // Ambil semua input POST
    //         $postData = $this->request->getPost();

    //         // Validasi data yang diperlukan: bisa menghasilkan array of records
    //         $validDatas = $this->pengeluaranModel->validateDeliveryData($postData);
    //         // var_dump ($validDatas);
    //         // Jika model men-set error, log dan kembalikan
    //         if ($errors = $this->pengeluaranModel->errors()) {
    //             log_message('error', '[saveSessionDeliveryArea] Validasi model gagal: ' . json_encode($errors));
    //             return $this->response
    //                 ->setStatusCode(422)
    //                 ->setJSON([
    //                     'success' => false,
    //                     'message' => 'Validasi data gagal',
    //                     'errors'  => $errors,
    //                 ]);
    //         }

    //         // Jika tidak ada data valid, kembalikan error
    //         if (empty($validDatas) || !is_array($validDatas)) {
    //             return $this->response
    //                 ->setStatusCode(400)
    //                 ->setJSON([
    //                     'success' => false,
    //                     'message' => 'Tidak ada data baru atau data sudah dikirim sebelumnya'
    //                 ]);
    //         }

    //         /** @var \CodeIgniter\Session\Session */
    //         $session = session();

    //         // Ambil data session manual_delivery (jika belum ada, inisialisasi array kosong)
    //         $manualDelivery = $session->get('manual_delivery') ?? [];

    //         $addedCount = 0;

    //         foreach ($validDatas as $idx => $row) {
    //             // Pastikan semua field kunci tersedia
    //             if (!isset($row['id_pengeluaran'], $row['id_total_pemesanan'], $row['area_out'])) {
    //                 log_message('error', "[saveSessionDeliveryArea] Row ke-$idx missing key fields: " . json_encode($row));
    //                 continue;
    //             }

    //             // Cek duplikasi berdasarkan id_out_celup + area + tanggal
    //             $isDuplicate = array_filter($manualDelivery, function ($item) use ($row) {
    //                 return
    //                     $item['id_pengeluaran'] == $row['id_pengeluaran']
    //                     && $item['id_total_pemesanan']  == $row['id_total_pemesanan']
    //                     && $item['area_out']   == $row['area_out'];
    //             });

    //             if ($isDuplicate) {
    //                 // Lewati record yang sudah ada
    //                 continue;
    //             }

    //             // Tambahkan ke array session
    //             $manualDelivery[] = [
    //                 'id_pengeluaran' => $row['id_pengeluaran'] ?? null,
    //                 'id_out_celup'   => $row['id_out_celup'],
    //                 'tgl_pakai'      => $row['tgl_pakai'],
    //                 'no_model'       => $row['no_model']    ?? '',
    //                 'item_type'      => $row['item_type']   ?? '',
    //                 'jenis'          => $row['jenis']       ?? '',
    //                 'kode_warna'     => $row['kode_warna']  ?? '',
    //                 'warna'          => $row['warna']       ?? '',
    //                 'area_out'       => $row['area_out'],
    //                 'no_karung'      => $row['no_karung'],
    //                 'tgl_out'        => $row['tgl_out'],
    //                 'kgs_out'        => $row['kgs_out']     ?? $row['ttl_kg'] ?? 0,
    //                 'cns_out'        => $row['cns_out']     ?? $row['ttl_cns'] ?? 0,
    //                 'krg_out'        => 0, // asumsi default
    //                 'lot_out'        => $row['lot_out']     ?? '',
    //                 'nama_cluster'   => $row['nama_cluster'] ?? '',
    //                 'admin'          => $session->get('username'),
    //             ];

    //             $addedCount++;
    //         }

    //         // Simpan kembali session
    //         $session->set('manual_delivery', $manualDelivery);

    //         if ($addedCount === 0) {
    //             return $this->response
    //                 ->setStatusCode(409)
    //                 ->setJSON([
    //                     'success' => false,
    //                     'message' => 'Semua data sudah ada di session'
    //                 ]);
    //         }

    //         return $this->response
    //             ->setStatusCode(200)
    //             ->setJSON([
    //                 'success'   => true,
    //                 'message'   => "{$addedCount} record berhasil ditambahkan"
    //             ]);
    //     } catch (\Throwable $e) {
    //         // Tangani exception tak terduga
    //         log_message('error', '[saveSessionDeliveryArea] Exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    //         return $this->response
    //             ->setStatusCode(500)
    //             ->setJSON([
    //                 'success' => false,
    //                 'message' => 'Terjadi kesalahan server',
    //                 'error'   => $e->getMessage(), // atau hilangkan di production
    //             ]);
    //     }
    // }

    // bira
    public function saveSessionDeliveryArea()
    {
        // Ambil semua input POST
        $postData = $this->request->getPost();

        // Validasi data yang diperlukan: bisa menghasilkan array of records
        $validDatas = $this->pengeluaranModel->validateDeliveryData($postData);

        // Jika tidak ada data valid, kembalikan error
        if (!is_array($validDatas) || count($validDatas) === 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Tidak ada data baru atau data sudah dikirim sebelumnya'
            ]);
        }


        /** @var \CodeIgniter\Session\Session */
        $session = session();

        // Ambil data session manual_delivery (jika belum ada, inisialisasi array kosong)
        $manualDelivery = $session->get('manual_delivery') ?? [];

        $addedCount = 0;

        foreach ($validDatas as $row) {
            // Cek duplikasi berdasarkan id_out_celup + area + tanggal
            $isDuplicate = array_filter($manualDelivery, function ($item) use ($row) {
                return
                    $item['id_out_celup'] == $row['id_out_celup']
                    && $item['area_out']  == $row['area_out']
                    && $item['tgl_out']   == $row['tgl_out'];
            });

            if ($isDuplicate) {
                // Lewati record yang sudah ada
                continue;
            }

            // Tambahkan ke array session
            $manualDelivery[] = [
                'id_pengeluaran' => $row['id_pengeluaran'] ?? null,
                'id_out_celup'   => $row['id_out_celup'],
                'tgl_pakai'      => $row['tgl_pakai'],
                'no_model'       => $row['no_model']    ?? '',
                'item_type'      => $row['item_type']   ?? '',
                'jenis'          => $row['jenis']       ?? '',
                'kode_warna'     => $row['kode_warna']  ?? '',
                'warna'          => $row['warna']       ?? '',
                'area_out'       => $row['area_out'],
                'no_karung'      => $row['no_karung'],
                'tgl_out'        => $row['tgl_out'],
                'kgs_out'        => $row['kgs_out']     ?? $row['ttl_kg'] ?? 0,
                'cns_out'        => $row['cns_out']     ?? $row['ttl_cns'] ?? 0,
                'krg_out'        => 0, // asumsi default
                'lot_out'        => $row['lot_out']     ?? '',
                'nama_cluster'   => $row['nama_cluster'] ?? '',
                'admin'          => $session->get('username'),
                'jenis'          => $row['jenis'],
            ];

            $addedCount++;
        }

        // Simpan kembali session
        $session->set('manual_delivery', $manualDelivery);

        if ($addedCount === 0) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'Semua data sudah ada di session'
                ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'success'   => true,
                'message'   => "{$addedCount} record berhasil ditambahkan"
            ]);
    }

    public function removeSessionDelivery()
    {
        $indexes = $this->request->getPost('indexes'); // array
        $idx = $this->request->getPost('index');

        $session = session()->get('manual_delivery') ?? [];

        if ($idx !== null) {
            if (isset($session[$idx])) {
                array_splice($session, $idx, 1);
                session()->set('manual_delivery', $session);
                return $this->response->setJSON(['success' => true]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Index tidak ditemukan']);
        }

        // kalau banyak index dikirim
        if (!empty($indexes) && is_array($indexes)) {
            // urutkan descending supaya tidak geser index
            rsort($indexes);
            foreach ($indexes as $i) {
                if (isset($session[$i])) {
                    array_splice($session, $i, 1);
                }
            }
            session()->set('manual_delivery', array_values($session));
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data terpilih']);
    }

    // public function updateStatusKirim()
    // {
    //     $post      = $this->request->getPost();
    //     dd ($post);
    //     $ids       = array_filter(array_map('intval', $post['id_pengeluaran'] ?? []));
    //     $kgsList   = $post['kgs_out'] ?? [];
    //     $cnsList   = $post['cns_out'] ?? [];
    //     $lotList   = $post['lot_out'] ?? [];

    //     if (empty($ids)) {
    //         return redirect()->back()->with('error', 'Tidak ada pengeluaran valid.');
    //     }

    //     $sessionUser  = session('username');
    //     $updatedCount = 0;

    //     foreach ($ids as $index => $id) {
    //         $record = $this->pengeluaranModel->find($id);
    //         if (!$record) {
    //             continue;
    //         }

    //         $resultJenis = $this->pemesananModel->getJenisPemesananbyIdTtlPesan($record['id_total_pemesanan']);
    //         $jenis = '';

    //         if (is_array($resultJenis)) {
    //             // misalnya return ['jenis' => 'spandex']
    //             $jenis = strtolower($resultJenis[$index]['jenis'] ?? '');
    //         } else {
    //             // kalau return string langsung
    //             $jenis = strtolower((string) $resultJenis);
    //         }


    //         // Data update untuk pengeluaran
    //         $data = [
    //             'status' => 'Pengiriman Area',
    //             'admin'  => $sessionUser,
    //         ];

    //         if (isset($kgsList[$index]) && $kgsList[$index] !== '') {
    //             $data['kgs_out'] = (float) $kgsList[$index];
    //         }
    //         if (isset($cnsList[$index]) && $cnsList[$index] !== '') {
    //             $data['cns_out'] = (int) $cnsList[$index];
    //         }
    //         if (isset($lotList[$index]) && $lotList[$index] !== '') {
    //             $data['lot_out'] = $lotList[$index];
    //         }

    //         // Update pengeluaran
    //         if ($this->pengeluaranModel->update($id, $data)) {

    //             // Kalau jenis BUKAN spandex/karet → update stock
    //             if (!in_array($jenis, ['spandex', 'karet'])) {
    //                 $stok = $this->stockModel->find($record['id_stock']);
    //                 // dd ($stok);
    //                 if ($stok) {
    //                     $kgsStokNew = $stok['kgs_in_out'];
    //                     $cnsStokNew = $stok['cns_in_out'];

    //                     if (isset($kgsList[$index]) && $kgsList[$index] !== '') {
    //                         $kgsStokNew = ($stok['kgs_in_out'] + $record['kgs_out']) - $kgsList[$index];
    //                     }
    //                     if (isset($cnsList[$index]) && $cnsList[$index] !== '') {
    //                         $cnsStokNew = ($stok['cns_in_out'] + $record['cns_out']) - $cnsList[$index];
    //                     }

    //                     $this->stockModel->update($record['id_stock'], [
    //                         'kgs_in_out' => $kgsStokNew,
    //                         'cns_in_out' => $cnsStokNew,
    //                     ]);
    //                 }
    //             }

    //             $updatedCount++;
    //         }
    //     }

    //     session()->remove('manual_delivery');
    //     session()->setFlashdata(
    //         $updatedCount > 0 ? 'success' : 'error',
    //         $updatedCount > 0
    //             ? "{$updatedCount} status berhasil diperbarui"
    //             : 'Gagal memperbarui status atau data out tidak ada'
    //     );

    //     return redirect()->to(base_url("{$this->role}/pengiriman_area_manual"));
    // }

    // public function updateStatusKirim()
    // {
    //     $post = $this->request->getPost();
    //     // dd ($post);
    //     $ids     = array_filter(array_map('intval', $post['id_pengeluaran'] ?? []));
    //     // dd ($ids);
    //     $kgsList = $post['kgs_out'] ?? [];
    //     $cnsList = $post['cns_out'] ?? [];
    //     $lotList = $post['lot_out'] ?? [];

    //     if (empty($ids)) {
    //         return redirect()->back()->with('error', 'Tidak ada pengeluaran valid.');
    //     }

    //     $sessionUser  = (string) session('username');
    //     $updatedCount = 0;

    //     foreach ($ids as $index => $id) {
    //         $record = $this->pengeluaranModel->find($id);
    //         // dd ($record);
    //         if (!$record) continue;

    //         // Ambil jenis (dipertahankan logic aslinya)
    //         $resultJenis = $this->pemesananModel->getJenisPemesananbyIdTtlPesan($record['id_total_pemesanan']);
    //         $jenis = is_array($resultJenis)
    //             ? strtolower((string) ($resultJenis[$index]['jenis'] ?? ''))
    //             : strtolower((string) $resultJenis);

    //         // Data update pengeluaran
    //         $data = [
    //             'status' => 'Pengiriman Area',
    //             'admin'  => $sessionUser,
    //         ];
    //         if (isset($kgsList[$index]) && $kgsList[$index] !== '') $data['kgs_out'] = (float) $kgsList[$index];
    //         if (isset($cnsList[$index]) && $cnsList[$index] !== '') $data['cns_out'] = (int)   $cnsList[$index];
    //         if (isset($lotList[$index]) && $lotList[$index] !== '') $data['lot_out'] = (string)$lotList[$index];

    //         if (!$this->pengeluaranModel->update($id, $data)) continue;

    //         // === PERUBAHAN: Tidak lagi mengecualikan spandex/karet.
    //         // Selalu cek stok; jika ada, lakukan kalkulasi pengurangan sesuai rumus lama.
    //         if($record['id_stock'] === null) {
    //             $updatedCount++;
    //             continue;
    //         }
    //         $stok = $this->stockModel->find($record['id_stock']);
    //         // dd ($stok);
    //         if ($stok) {
    //             // Nilai OUT efektif (prioritas input baru; fallback ke nilai record lama)
    //             $outKgsBaru = array_key_exists('kgs_out', $data) ? (float)$data['kgs_out'] : (float)$record['kgs_out'];
    //             $outCnsBaru = array_key_exists('cns_out', $data) ? (int)  $data['cns_out'] : (int)  $record['cns_out'];
    //             $outKrgBaru = array_key_exists('krg_out', $data) ? (int)  $data['krg_out'] : (int)  $record['krg_out'];
    //             $lotBaru    = array_key_exists('lot_out', $data) ?        $data['lot_out'] :        $record['lot_out'];

    //             // Default agar tidak undefined (tidak mengubah behavior)
    //             $kgsAwalStokNew  = (float)($stok['kgs_stock_awal']);
    //             $kgsInOutStokNew = (float)($stok['kgs_in_out']);
    //             $cnsAwalStokNew  = (int)  ($stok['cns_stock_awal']);
    //             $cnsInOutStokNew = (int)  ($stok['cns_in_out']);
    //             $krgAwalStokNew  = (int)  ($stok['krg_stock_awal']);
    //             $krgInOutStokNew = (int)  ($stok['krg_in_out']);
    //             $lotAwalStokNew  = (string)($stok['lot_awal']  ?? '');
    //             $lotInOutStokNew = (string)($stok['lot_stock'] ?? '');

    //             // ===== Hitung KGS =====
    //             if ($outKgsBaru !== 0.0) {
    //                 if ((float)$stok['kgs_stock_awal'] > 0 && (float)$stok['kgs_in_out'] <= 0) {
    //                     $kgsAwalStokNew = (float)$stok['kgs_stock_awal'] - $outKgsBaru;
    //                 } else {
    //                     $kgsInOutStokNew = (float)$stok['kgs_in_out'] - $outKgsBaru;
    //                 }
    //             }

    //             // ===== Hitung CNS =====
    //             if ($outCnsBaru !== 0) {
    //                 if ((int)$stok['cns_stock_awal'] > 0 && (int)$stok['cns_in_out'] <= 0) {
    //                     $cnsAwalStokNew = (int)$stok['cns_stock_awal'] - $outCnsBaru;
    //                 } else {
    //                     $cnsInOutStokNew = (int)$stok['cns_in_out'] - $outCnsBaru;
    //                 }
    //             }

    //             // ===== Hitung KRG =====
    //             if ($outKrgBaru !== 0) {
    //                 if ((int)$stok['krg_stock_awal'] > 0 && (int)$stok['krg_in_out'] <= 0) {
    //                     $krgAwalStokNew = (int)$stok['krg_stock_awal'] - $outKrgBaru;
    //                 } else {
    //                     $krgInOutStokNew = (int)$stok['krg_in_out'] - $outKrgBaru;
    //                 }
    //             }

    //             // ===== Hitung LOT ===== (dipertahankan logika if-OR aslinya)
    //             if ($lotBaru !== null && $lotBaru !== '') {
    //                 if ((!empty($stok['lot_awal']) && $stok['lot_awal'] !== '') || (empty($stok['lot_stock']) && $stok['lot_stock'] === '')) {
    //                     $lotAwalStokNew = (string)$lotBaru;
    //                 } else {
    //                     $lotInOutStokNew = (string)$lotBaru;
    //                 }
    //             }

    //             // Validasi KGS tidak minus (mengikuti validasi asli)
    //             $sisaKgsAwal  = (float)$stok['kgs_stock_awal'] - (float)$outKgsBaru;
    //             // dd ($sisaKgsAwal);
    //             $sisaKgsInOut = (float)$stok['kgs_in_out']     - (float)$outKgsBaru;
    //             // dd ($sisaKgsInOut);
    //             if ($sisaKgsAwal < 0 || $sisaKgsInOut < 0) {
    //                 session()->setFlashdata('error', 'Stok KGS tidak mencukupi untuk Pengeluaran Tersebut. Silahkan Cek Kembali.');
    //                 return redirect()->to(base_url("{$this->role}/pengiriman_area_manual"));
    //             } else{
    //                 // Update stok
    //                 $this->stockModel->update($record['id_stock'], [
    //                     'kgs_stock_awal' => $kgsAwalStokNew,
    //                     'kgs_in_out'     => $kgsInOutStokNew,
    //                     'cns_stock_awal' => $cnsAwalStokNew,
    //                     'cns_in_out'     => $cnsInOutStokNew,
    //                     'krg_stock_awal' => $krgAwalStokNew,
    //                     'krg_in_out'     => $krgInOutStokNew,
    //                     'lot_awal'       => $lotAwalStokNew,
    //                     'lot_stock'      => $lotInOutStokNew,
    //                 ]);
    //             }
    //         }
    //         // Jika stok tidak ada (termasuk spandex/karet): tidak ada pengurangan, lanjutkan
    //         $updatedCount++;
    //     }

    //     session()->remove('manual_delivery');
    //     session()->setFlashdata(
    //         $updatedCount > 0 ? 'success' : 'error',
    //         $updatedCount > 0 ? "{$updatedCount} status berhasil diperbarui" : 'Gagal memperbarui status atau data out tidak ada'
    //     );

    //     return redirect()->to(base_url("{$this->role}/pengiriman_area_manual"));
    // }


    // public function updateStatusKirim()
    // {
    //     $this->db = \Config\Database::connect();
    //     $post = $this->request->getPost();

    //     // --- Ambil input keyed-by-id agar tidak tergantung index urutan
    //     $ids = array_map('intval', $post['id_pengeluaran'] ?? []);
    //     if (empty($ids)) {
    //         return redirect()->back()->with('error', 'Tidak ada pengeluaran valid.');
    //     }

    //     // Expect: kgs_out[ID] = val, cns_out[ID] = val, lot_out[ID] = val, (opsional) krg_out[ID] = val
    //     $kgsById = $post['kgs_out'] ?? [];
    //     $cnsById = $post['cns_out'] ?? [];
    //     $krgById = $post['krg_out'] ?? []; // kalau tidak dipakai, hapus semua bagian KRG
    //     $lotById = $post['lot_out'] ?? [];

    //     $sessionUser  = (string) session('username');
    //     $updatedCount = 0;
    //     $errors       = [];

    //     foreach ($ids as $id) {
    //         $record = $this->pengeluaranModel->find($id);
    //         if (!$record) {
    //             $errors[] = "ID $id: pengeluaran tidak ditemukan.";
    //             continue;
    //         }

    //         // Siapkan data update pengeluaran
    //         $dataPengeluaran = [
    //             'status' => 'Pengiriman Area',
    //             'admin'  => $sessionUser,
    //         ];
    //         if (isset($kgsById[$id]) && $kgsById[$id] !== '') $dataPengeluaran['kgs_out'] = (float) $kgsById[$id];
    //         if (isset($cnsById[$id]) && $cnsById[$id] !== '') $dataPengeluaran['cns_out'] = (int)   $cnsById[$id];
    //         if (isset($krgById[$id]) && $krgById[$id] !== '') $dataPengeluaran['krg_out'] = (int)   $krgById[$id];
    //         if (isset($lotById[$id]) && $lotById[$id] !== '') $dataPengeluaran['lot_out'] = (string)$lotById[$id];

    //         // Transaksi per-ROW agar atomik
    //         $this->db->transStart();

    //         if (!$this->pengeluaranModel->update($id, $dataPengeluaran)) {
    //             $this->db->transRollback();
    //             $errors[] = "ID $id: gagal update pengeluaran.";
    //             continue;
    //         }

    //         // Jika tidak ada id_stock, cukup update pengeluaran saja
    //         if (empty($record['id_stock'])) {
    //             $this->db->transComplete();
    //             if ($this->db->transStatus()) $updatedCount++;
    //             else $errors[] = "ID $id: transaksi gagal.";
    //             continue;
    //         }

    //         $stok = $this->stockModel->find($record['id_stock']);
    //         if (!$stok) {
    //             // stok tidak ditemukan: lanjut tanpa ubah stok
    //             $this->db->transComplete();
    //             if ($this->db->transStatus()) $updatedCount++;
    //             else $errors[] = "ID $id: transaksi gagal (stok).";
    //             continue;
    //         }

    //         // Ambil nilai OUT efektif (prioritas input baru; fallback record lama)
    //         $outKgs = array_key_exists('kgs_out', $dataPengeluaran) ? (float)$dataPengeluaran['kgs_out'] : (float)$record['kgs_out'];
    //         $outCns = array_key_exists('cns_out', $dataPengeluaran) ? (int)$dataPengeluaran['cns_out']   : (int)$record['cns_out'];
    //         $outKrg = array_key_exists('krg_out', $dataPengeluaran) ? (int)$dataPengeluaran['krg_out']   : (int)$record['krg_out'];
    //         $lotBaru = array_key_exists('lot_out', $dataPengeluaran) ? (string)$dataPengeluaran['lot_out'] : (string)$record['lot_out'];

    //         // Siapkan nilai baru (default = kondisi sekarang)
    //         $new = [
    //             'kgs_stock_awal' => (float)$stok['kgs_stock_awal'],
    //             'kgs_in_out'     => (float)$stok['kgs_in_out'],
    //             'cns_stock_awal' => (int)$stok['cns_stock_awal'],
    //             'cns_in_out'     => (int)$stok['cns_in_out'],
    //             'krg_stock_awal' => (int)$stok['krg_stock_awal'],
    //             'krg_in_out'     => (int)$stok['krg_in_out'],
    //             'lot_awal'       => (string)($stok['lot_awal'] ?? ''),
    //             'lot_stock'      => (string)($stok['lot_stock'] ?? ''),
    //         ];

    //         // ===== KGS: pilih ember yang dipakai & validasi ember itu saja
    //         if ($outKgs != 0.0) {
    //             if ($stok['kgs_stock_awal'] > 0 && $stok['kgs_in_out'] <= 0) {
    //                 if ($stok['kgs_stock_awal'] - $outKgs < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok KGS awal tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['kgs_stock_awal'] = $stok['kgs_stock_awal'] - $outKgs;
    //             } else {
    //                 if ($stok['kgs_in_out'] - $outKgs < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok KGS in/out tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['kgs_in_out'] = $stok['kgs_in_out'] - $outKgs;
    //             }
    //         }

    //         // ===== CNS
    //         if ($outCns != 0) {
    //             if ($stok['cns_stock_awal'] > 0 && $stok['cns_in_out'] <= 0) {
    //                 if ($stok['cns_stock_awal'] - $outCns < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok CNS awal tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['cns_stock_awal'] = $stok['cns_stock_awal'] - $outCns;
    //             } else {
    //                 if ($stok['cns_in_out'] - $outCns < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok CNS in/out tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['cns_in_out'] = $stok['cns_in_out'] - $outCns;
    //             }
    //         }

    //         // ===== KRG (opsional)
    //         if ($outKrg != 0) {
    //             if ($stok['krg_stock_awal'] > 0 && $stok['krg_in_out'] <= 0) {
    //                 if ($stok['krg_stock_awal'] - $outKrg < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok KRG awal tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['krg_stock_awal'] = $stok['krg_stock_awal'] - $outKrg;
    //             } else {
    //                 if ($stok['krg_in_out'] - $outKrg < 0) {
    //                     $this->db->transRollback();
    //                     $errors[] = "ID $id: Stok KRG in/out tidak cukup.";
    //                     continue;
    //                 }
    //                 $new['krg_in_out'] = $stok['krg_in_out'] - $outKrg;
    //             }
    //         }

    //         // ===== LOT (sederhanakan aturan)
    //         if ($lotBaru !== '') {
    //             // Contoh aturan: kalau lot_awal kosong → isi lot_awal; else → isi lot_stock
    //             if ($new['lot_awal'] === '') $new['lot_awal'] = $lotBaru;
    //             else                         $new['lot_stock'] = $lotBaru;
    //         }

    //         if (!$this->stockModel->update($record['id_stock'], $new)) {
    //             $this->db->transRollback();
    //             $errors[] = "ID $id: gagal update stok.";
    //             continue;
    //         }

    //         $this->db->transComplete();
    //         if ($this->db->transStatus()) $updatedCount++;
    //         else $errors[] = "ID $id: transaksi gagal.";
    //     }

    //     session()->remove('manual_delivery');

    //     if ($updatedCount > 0 && empty($errors)) {
    //         session()->setFlashdata('success', "{$updatedCount} status berhasil diperbarui");
    //     } elseif ($updatedCount > 0 && !empty($errors)) {
    //         session()->setFlashdata('success', "{$updatedCount} status berhasil diperbarui, namun ada sebagian gagal: " . implode(' | ', $errors));
    //     } else {
    //         session()->setFlashdata('error', 'Tidak ada yang diperbarui: ' . implode(' | ', $errors));
    //     }

    //     return redirect()->to(base_url("{$this->role}/pengiriman_area_manual"));
    // }

    public function updateStatusKirim()
    {
        $this->db = \Config\Database::connect();
        $post = $this->request->getPost();
        // dd($post);

        // ambil baris yang dipilih
        $selectedIndexes = $post['selected'] ?? [];

        // jika tidak ada data yg di pilih
        if (empty($selectedIndexes)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        // Expect: kgs_out[ID] = val, cns_out[ID] = val, lot_out[ID] = val, (opsional) krg_out[ID] = val
        $kgsById = $post['kgs_out'] ?? [];
        $cnsById = $post['cns_out'] ?? [];
        $krgById = $post['krg_out'] ?? []; // kalau tidak dipakai, hapus semua bagian KRG
        $lotById = $post['lot_out'] ?? [];
        $jenisId = $post['jenis'] ?? [];

        $sessionUser  = (string) session('username');
        $updatedCount = 0;
        $errors       = [];

        foreach ($selectedIndexes as $index => $id) {
            $id = (int) $id; // pastikan integer
            $record = $this->pengeluaranModel->find($id); // get data pengeluaran
            if (!$record) {
                $errors[] = "ID $id: pengeluaran tidak ditemukan.";
                continue;
            }
            // Siapkan data update pengeluaran
            $dataPengeluaran = [
                'status' => 'Pengiriman Area',
                'admin'  => $sessionUser,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            if (isset($kgsById[$index]) && $kgsById[$index] !== '') $dataPengeluaran['kgs_out'] = (float) $kgsById[$index];
            if (isset($cnsById[$index]) && $cnsById[$index] !== '') $dataPengeluaran['cns_out'] = (int)   $cnsById[$index];
            if (isset($krgById[$index]) && $krgById[$index] !== '') $dataPengeluaran['krg_out'] = (int)   $krgById[$index];
            if (isset($lotById[$index]) && $lotById[$index] !== '') $dataPengeluaran['lot_out'] = (string)$lotById[$index];

            $this->db->transStart();
            if (!$this->pengeluaranModel->update($id, $dataPengeluaran)) {
                $this->db->transRollback();
                $errors[] = "ID $id: gagal update pengeluaran.";
                continue;
            }

            // jika bb benang & nylon update stock
            if ($jenisId[$index] == 'BENANG' || $jenisId[$index] == 'NYLON') {
                // get data stock
                $stock = $this->stockModel->find($record['id_stock']);
                if ($stock) {
                    // Nilai OUT efektif (prioritas input baru; fallback ke nilai record lama)
                    $outKgsBaru = array_key_exists('kgs_out', $dataPengeluaran) ? (float)$dataPengeluaran['kgs_out'] : (float)$record['kgs_out'];
                    $outCnsBaru = array_key_exists('cns_out', $dataPengeluaran) ? (int)  $dataPengeluaran['cns_out'] : (int)  $record['cns_out'];
                    $outKrgBaru = array_key_exists('krg_out', $dataPengeluaran) ? (int)  $dataPengeluaran['krg_out'] : (int)  $record['krg_out'];
                    $lotBaru    = array_key_exists('lot_out', $dataPengeluaran) ?        $dataPengeluaran['lot_out'] :        $record['lot_out'];
                    // Default agar tidak undefined (tidak mengubah behavior)
                    $kgsAwalStockNew  = (float)($stock['kgs_stock_awal']);
                    $kgsInOutStockNew = (float)($stock['kgs_in_out']);
                    $cnsAwalStockNew  = (int)  ($stock['cns_stock_awal']);
                    $cnsInOutStockNew = (int)  ($stock['cns_in_out']);
                    $krgAwalStockNew  = (int)  ($stock['krg_stock_awal']);
                    $krgInOutStockNew = (int)  ($stock['krg_in_out']);
                    $lotAwalStockNew  = (string)($stock['lot_awal']  ?? '');
                    $lotInOutStockNew = (string)($stock['lot_stock'] ?? '');
                    // cek apakah kgs & cones nya berubah
                    if ($kgsById[$index] != $record['kgs_out'] || $cnsById[$index] != $record['cns_out']) {
                        // jika berubah, update stock nya
                        // ===== Hitung KGS =====
                        if ($outKgsBaru != 0.0) {
                            if ((float)$stock['kgs_stock_awal'] > 0) {
                                $kgsAwalStockNew = (float)$stock['kgs_stock_awal'] + $record['kgs_out'] - $outKgsBaru;
                            } else {
                                $kgsInOutStockNew = (float)$stock['kgs_in_out'] + $record['kgs_out'] - $outKgsBaru;
                            }
                            if ($kgsAwalStockNew < 0 || $kgsInOutStockNew < 0) {
                                $this->db->transRollback();
                                $errors[] = "ID $id: Stok KGS tidak cukup.";
                                continue;
                            }
                        }
                        // ===== Hitung CNS =====
                        if ($outCnsBaru !== 0) {
                            if ((float)$stock['cns_stock_awal'] > 0) {
                                $cnsAwalStockNew = (float)$stock['cns_stock_awal'] + $record['cns_out'] - $outCnsBaru;
                            } else {
                                $cnsInOutStockNew = (float)$stock['cns_in_out'] + $record['cns_out'] - $outCnsBaru;
                            }
                            $cnsAwalStockNew  = max(0, $cnsAwalStockNew);
                            $cnsInOutStockNew = max(0, $cnsInOutStockNew);
                        }
                        // ===== Hitung KRG =====
                        if ($outKrgBaru !== 0) {
                            if ((int)$stock['krg_stock_awal'] > 0) {
                                $krgAwalStockNew = (int)$stock['krg_stock_awal'] + $record['krg_out'] - $outKrgBaru;
                            } else {
                                $krgInOutStockNew = (int)$stock['krg_in_out'] + $record['krg_out'] - $outKrgBaru;
                            }
                        }

                        // ===== Hitung LOT ===== (dipertahankan logika if-OR aslinya)
                        if ($lotBaru !== null && $lotBaru !== '') {
                            if (!empty($stock['lot_awal']) && $stock['lot_awal'] !== '') {
                                $lotAwalStockNew = (string)$lotBaru;
                            } else {
                                $lotInOutStockNew = (string)$lotBaru;
                            }
                        }

                        $this->stockModel->update($record['id_stock'], [
                            'kgs_stock_awal' => $kgsAwalStockNew,
                            'kgs_in_out'     => $kgsInOutStockNew,
                            'cns_stock_awal' => $cnsAwalStockNew,
                            'cns_in_out'     => $cnsInOutStockNew,
                            'krg_stock_awal' => $krgAwalStockNew,
                            'krg_in_out'     => $krgInOutStockNew,
                            'lot_awal'       => $lotAwalStockNew,
                            'lot_stock'      => $lotInOutStockNew,
                        ]);
                    }
                }
            }
            $this->db->transComplete();
            if ($this->db->transStatus()) $updatedCount++;
            else $errors[] = "ID $id: transaksi gagal.";
        }
        session()->remove('manual_delivery');

        if ($updatedCount > 0 && empty($errors)) {
            session()->setFlashdata('success', "{$updatedCount} status berhasil diperbarui");
        } elseif ($updatedCount > 0 && !empty($errors)) {
            session()->setFlashdata('success', "{$updatedCount} status berhasil diperbarui, namun ada sebagian gagal: " . implode(' | ', $errors));
        } else {
            session()->setFlashdata('error', 'Tidak ada yang diperbarui: ' . implode(' | ', $errors));
        }

        return redirect()->to(base_url("{$this->role}/pengiriman_area_manual"));
    }

    // public function pengirimanArea()
    // {

    //     // Simpan data form ke session
    //     $formData = [];

    //     // Simpan ke session
    //     session()->set('pengirimanForm', $formData);
    //     // var_dump(session()->get('pengirimanForm'));
    //     // exit;

    //     $id = $this->request->getPost('barcode');
    //     $cluster = $this->clusterModel->getDataCluster();

    //     // Ambil data dari session (jika ada)
    //     $existingData = session()->get('dataPengiriman') ?? [];

    //     if (!empty($id)) {
    //         // Cek apakah barcode sudah ada di data yang tersimpan
    //         foreach ($existingData as $item) {
    //             if ($item['id_out_celup'] == $id) {
    //                 session()->set('pengirimanForm', $formData);
    //                 session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
    //                 // return redirect()->to(base_url($this->role . '/pengiriman_area'));
    //             }
    //         }

    //         // Ambil data dari database berdasarkan barcode yang dimasukkan
    //         $outJalur = $this->pengeluaranModel->getDataForOut($id);    
    //         // dd ($outJalur);
    //         if (empty($outJalur)) {
    //             session()->set('pengirimanForm', $formData);
    //             session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
    //             // return redirect()->to(base_url($this->role . '/pengiriman_area'));
    //         } elseif (!empty($outJalur)) {
    //             // Tambahkan data baru ke dalam array
    //             $existingData = array_merge($existingData, $outJalur);
    //         }

    //         // Simpan kembali ke session
    //         session()->set('dataPengiriman', $existingData);
    //         session()->set('pengirimanForm', $formData);
    //         // var_dump(session()->get('dataPengiriman'));
    //         // Redirect agar form tidak resubmit saat refresh
    //         return redirect()->to(base_url($this->role . '/pengiriman_area'));
    //     }

    //     // Ambil kembali data form dari session
    //     $formData = session()->get('pengirimanForm') ?? [];
    //     $data = [
    //         'active' => $this->active,
    //         'title' => 'Material System',
    //         'role' => $this->role,
    //         'dataOut' => $existingData, // Tampilkan data dari session
    //         'cluster' => $cluster,
    //         'error' => session()->getFlashdata('error'),
    //         'area' => $formData['area'] ?? '',
    //         'tgl_pakai' => $formData['tgl_pakai'] ?? '',
    //         'no_model' => $formData['no_model'] ?? '',
    //         'item_type' => $formData['item_type'] ?? '',
    //         'kode_warna' => $formData['kode_warna'] ?? '',
    //         'warna' => $formData['warna'] ?? '',
    //         'kgs_pesan' => $formData['kgs_pesan'] ?? '',
    //         'cns_pesan' => $formData['cns_pesan'] ?? '',
    //     ];
    //     // dd ($data);

    //     return view($this->role . '/warehouse/form-pengiriman', $data);
    // }
    public function resetPengirimanArea()
    {
        session()->remove('dataPengiriman');
        return redirect()->to(base_url($this->role . '/pengiriman_area'));
    }
    public function hapusListPengiriman()
    {
        $id = $this->request->getPost('id');

        // Ambil data dari session
        $existingData = session()->get('dataPengiriman') ?? [];

        // Cek apakah data dengan ID yang dikirim ada di session
        foreach ($existingData as $key => $item) {
            if ($item['id_out_celup'] == $id) {
                // Hapus data tersebut dari array
                unset($existingData[$key]);
                // Update session dengan data yang sudah dihapus
                session()->set('dataPengiriman', array_values($existingData));
                // Debug response
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function prosesPengirimanArea()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pengiriman_area');
        }
        $area = $this->request->getPost('area');
        $idPengeluaran = $this->request->getPost('idPengeluaran');
        $idOutCelup = $this->request->getPost('id_out_celup');
        $itemTypes = $this->request->getPost('item_type');
        $kodeWarnas = $this->request->getPost('kode_warna');
        $tglOuts = $this->request->getPost('tgl_kirim');
        $kgsOuts = $this->request->getPost('kgs_kirim');
        $cnsOuts = $this->request->getPost('cns_kirim');
        $namaClusters = $this->request->getPost('nama_cluster');
        $lotKirims = $this->request->getPost('lot_kirim');
        // dd ($checkedIds,$idOutCelup, $itemTypes, $kodeWarnas, $tglOuts, $kgsOuts, $cnsOuts, $namaClusters, $lotKirims, $area, $idPengeluaran);
        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengiriman_area');
        }

        // Pastikan nama_cluster ada di dalam tabel cluster

        $dataKirim = [];

        foreach ($checkedIds as $key => $idOut) {
            $dataKirim[] = [
                'id_out_celup' => $idOutCelup[$key] ?? null,
                'area_out' => $area[$key] ?? null,
                'tgl_out' => $tglOuts[$key] ?? null,
                'kgs_out' => $kgsOuts[$key] ?? null,
                'cns_out' => $cnsOuts[$key] ?? null,
                'krg_out' => 1, // Asumsikan setiap pemasukan hanya 1 kali
                'lot_out' => $lotKirims[$key] ?? null,
                'nama_cluster' => $namaClusters[$key] ?? null,
                'admin' => session()->get('username')
            ];
        }
        // dd ($dataKirim);
        // Debugging: cek apakah data tidak kosong sebelum insert
        if (empty($dataKirim)) {
            session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
            return redirect()->to($this->role . '/pengiriman_area');
        }

        // Ambil data session
        $checked = session()->get('dataPengiriman');
        // dd ($checked);
        // Jika session tidak kosong
        if (!empty($checked)) {
            // Ambil daftar ID yang ingin dihapus
            $idToRemove = array_column($dataKirim, 'id_out_celup');

            // Filter session agar hanya menyisakan data yang tidak ada di $dataKirim
            $filteredChecked = array_filter($checked, function ($tes) use ($idToRemove) {
                return !in_array($tes['id_out_celup'], $idToRemove);
            });

            // Jika hasil filtering masih ada data, simpan kembali ke session
            if (!empty($filteredChecked)) {
                session()->set('dataPengiriman', array_values($filteredChecked));
            } else {
                // Hapus session jika tidak ada data tersisa
                session()->remove('dataPengiriman');
            }
        }
        // Ambil semua id_out_celup yang sudah ada dengan status 'Pengiriman Area'
        $existingIds = $this->pengeluaranModel
            ->select('id_out_celup')
            ->whereIn('id_out_celup', array_column($dataKirim, 'id_out_celup'))
            ->where('status', 'Pengiriman Area')
            ->findAll();
        // dd ($existingIds);
        $existingIds = array_column($existingIds, 'id_out_celup');
        // dd ($existingIds);
        // Filter data yang belum ada duplikat
        $dataToUpdate = array_filter($dataKirim, function ($item) use ($existingIds) {
            return !in_array($item['id_out_celup'], $existingIds);
        });
        // dd ($dataToUpdate);
        // Lakukan update hanya untuk data yang tidak duplikat
        if (!empty($dataToUpdate)) {
            foreach ($dataToUpdate as $item) {
                $this->pengeluaranModel
                    ->where('id_out_celup', $item['id_out_celup'])
                    ->set(['status' => 'Pengiriman Area'])
                    ->update();
            }
            // dd ($dataToUpdate);
            $jumlahUpdate = count($dataToUpdate);
            session()->setFlashdata('success', "$jumlahUpdate data berhasil diperbarui menjadi Pengiriman Area.");
        } else {
            session()->setFlashdata('error', 'Semua data sudah dikirim sebelumnya (duplikat).');
        }

        return redirect()->to($this->role . '/pengiriman_area');
    }

    // public function selectClusterWarehouse($id)
    // {
    //     $KgsPesan = $this->request->getGet('KgsPesan');
    //     $CnsPesan = $this->request->getGet('CnsPesan');
    //     $getPemesanan = $this->totalPemesananModel->getDataPemesananbyId($id);
    //     $getPersiapanPengeluaran = $this->pengeluaranModel->getKgPersiapanPengeluaran($getPemesanan['id_total_pemesanan']);
    //     $getPengiriman = $this->pengeluaranModel->getKgPengiriman($getPemesanan['id_total_pemesanan']);
    //     $cluster = $this->stockModel->getDataCluster($getPemesanan['no_model'], $getPemesanan['item_type'], $getPemesanan['kode_warna'], $getPemesanan['color']);
    //     $ketPemesanan = $this->pemesananModel->select('id_total_pemesanan, GROUP_CONCAT(DISTINCT keterangan_gbn) AS ket_gbn')
    //         ->where('id_total_pemesanan', $id)
    //         ->first();

    //     $data = [
    //         'active' => $this->active,
    //         'title' => 'Material System',
    //         'role' => $this->role,
    //         'cluster' => $cluster,
    //         'noModel' => $getPemesanan['no_model'],
    //         'itemType' => $getPemesanan['item_type'],
    //         'kodeWarna' => $getPemesanan['kode_warna'],
    //         'noModel' => $getPemesanan['no_model'],
    //         'area' => $getPemesanan['admin'],
    //         'id' => $id,
    //         'ketGbn' => $ketPemesanan['ket_gbn'] ?? '',
    //         'KgsPesan' => $KgsPesan,
    //         'CnsPesan' => $CnsPesan,
    //         'kgPersiapan' => $getPersiapanPengeluaran['kgs_out'],
    //         'kgPengiriman' => $getPengiriman['kgs_out'],
    //     ];

    //     // dd ($data);
    //     return view($this->role . '/pemesanan/select-cluster', $data);
    // }

    // SELECT CLUSTER V2 BIRA
    // public function selectClusterWarehouse(int $id)
    // {
    //     // Ambil kebutuhan dari query string (opsional)
    //     $KgsPesan = (float)($this->request->getGet('KgsPesan') ?? 0);
    //     $CnsPesan = (int)($this->request->getGet('CnsPesan') ?? 0);
    //     $area     = $this->request->getGet('Area');

    //     // 1 query detail pemesanan (hanya kolom yang dipakai)
    //     $pemesanan = $this->totalPemesananModel->findWithDetails($id);
    //     if (!$pemesanan) {
    //         return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
    //     }

    //     $noModel = $pemesanan['no_model'];
    //     // 1 query agregat untuk dua status sekaligus
    //     $agg = $this->pengeluaranModel->sumKgsByStatus($id);
    //     $kgPersiapan = (float)($agg['kgs_persiapan'] ?? 0);
    //     $kgPengiriman = (float)($agg['kgs_pengiriman'] ?? 0);

    //     // 1 query cluster (grouping)
    //     $clusterRows = $this->stockModel->listCluster(
    //         $pemesanan['no_model'],
    //         $pemesanan['item_type'],
    //         $pemesanan['kode_warna'],
    //         $pemesanan['color']
    //     );

    //     // keterangan gabungan (tetap 1 query ringan)
    //     $ket = $this->pemesananModel
    //         ->select('GROUP_CONCAT(DISTINCT keterangan_gbn) AS ket_gbn')
    //         ->where('id_total_pemesanan', $id)
    //         ->first();

    //     $dataPemesanan = [];
    //     $dataRetur = [];

    //     if (!empty($area) && !empty($noModel)) {
    //         $dataPemesanan = $this->pemesananModel->getPemesananByAreaModel($area, $noModel);
    //         $dataRetur = $this->returModel->getReturByAreaModel($area, $noModel);
    //     }

    //     $mergedData = [];
    //     $kebutuhan = [];

    //     // Tambahkan semua data pemesanan ke mergedData
    //     foreach ($dataPemesanan as $key => $pem) {
    //         // ambil data styleSize by bb
    //         $getStyle = $this->materialModel->getStyleSizeByBb($pem['no_model'], $pem['item_type'], $pem['kode_warna']);

    //         $ttlKeb = 0;
    //         $ttlQty = 0;

    //         foreach ($getStyle as $i => $data) {
    //             // Ambil qty
    //             $urlQty = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=' . urlencode($pem['no_model'])
    //                 . '&style_size=' . urlencode($data['style_size'])
    //                 . '&area=' . $area;

    //             $qtyResponse = file_get_contents($urlQty);
    //             $qtyData     = json_decode($qtyResponse, true);
    //             $qty         = (intval($qtyData['qty']) ?? 0);

    //             // Ambil kg po tambahan
    //             $kgPoTambahan = floatval(
    //                 $this->poTambahanModel->getKgPoTambahan([
    //                     'no_model'    => $pem['no_model'],
    //                     'item_type'   => $pem['item_type'],
    //                     'kode_warna'  => $pem['kode_warna'],
    //                     'style_size'  => $data['style_size'],
    //                     'area'        => $area,
    //                 ])['ttl_keb_potambahan'] ?? 0
    //             );

    //             if ($qty >= 0) {
    //                 if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
    //                     $kebutuhan = $data['kgs'] ?? 0;
    //                 } else {
    //                     $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
    //                 }
    //                 $pem['ttl_keb'] = $ttlKeb;
    //             }
    //             $ttlKeb += $kebutuhan;
    //             $ttlQty += $qty;
    //         }
    //         $pem['qty']     = $ttlQty; // ttl qty pcs
    //         $pem['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


    //         $mergedData[] = [
    //             'no_model'           => $pem['no_model'],
    //             'item_type'          => $pem['item_type'],
    //             'kode_warna'         => $pem['kode_warna'],
    //             'color'              => $pem['color'],
    //             'max_loss'           => $pem['max_loss'],
    //             'tgl_pakai'          => $pem['tgl_pakai'],
    //             'id_total_pemesanan' => $pem['id_total_pemesanan'],
    //             'ttl_jl_mc'          => (int)($pem['ttl_jl_mc'] ?? 0),
    //             'ttl_kg'             => (float)($pem['ttl_kg'] ?? 0),   // ← JANGAN number_format di sini
    //             'po_tambahan'        => (int)($pem['po_tambahan'] ?? 0),
    //             'ttl_keb'            => (float)$ttlKeb,                       // ← hasil hitung, mentah
    //             'kg_out'             => (float)($pem['kgs_out'] ?? 0),  // ← mentah
    //             'lot_out'            => $pem['lot_out'],
    //             // field retur kosong
    //             'tgl_retur'          => null,
    //             'kgs_retur'          => null,
    //             'lot_retur'          => null,
    //             'ket_gbn'            => null,
    //         ];
    //         $kebutuhanDipakai[$key] = true;
    //     }

    //     // Tambahkan semua data retur ke mergedData (data pemesanan diset null)
    //     foreach ($dataRetur as $retur) {
    //         // ambil data styleSize by bb
    //         $getStyle = $this->materialModel->getStyleSizeByBb($retur['no_model'], $retur['item_type'], $retur['kode_warna']);

    //         $ttlKeb = 0;
    //         $ttlQty = 0;

    //         foreach ($getStyle as $i => $data) {
    //             // Ambil qty
    //             $urlQty = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=' . $retur['no_model']
    //                 . '&style_size=' . urlencode($data['style_size'])
    //                 . '&area=' . $area;

    //             $qtyResponse = file_get_contents($urlQty);
    //             $qtyData     = json_decode($qtyResponse, true);
    //             $qty         = (intval($qtyData['qty']) ?? 0);

    //             // Ambil kg po tambahan
    //             $kgPoTambahan = floatval(
    //                 $this->poTambahanModel->getKgPoTambahan([
    //                     'no_model'    => $retur['no_model'],
    //                     'item_type'   => $retur['item_type'],
    //                     'kode_warna'  => $retur['kode_warna'],
    //                     'style_size'  => $data['style_size'],
    //                     'area'        => $area,
    //                 ])['ttl_keb_potambahan'] ?? 0
    //             );

    //             if ($qty >= 0) {
    //                 if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
    //                     $kebutuhan = $data['kgs'] ?? 0;
    //                 } else {
    //                     $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
    //                 }
    //                 $retur['ttl_keb'] = $ttlKeb;
    //             }
    //             $ttlKeb += $kebutuhan;
    //             $ttlQty += $qty;
    //         }
    //         $retur['qty']     = $ttlQty; // ttl qty pcs
    //         $retur['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


    //         $mergedData[] = [
    //             'no_model'           => $retur['no_model'],
    //             'item_type'          => $retur['item_type'],
    //             'kode_warna'         => $retur['kode_warna'],
    //             'color'              => $retur['warna'],
    //             'max_loss'           => 0,
    //             'tgl_pakai'          => null,
    //             'id_total_pemesanan' => null,
    //             'ttl_jl_mc'          => null,
    //             'ttl_kg'             => 0.0,                                   // ← angka 0
    //             'po_tambahan'        => 0,
    //             'ttl_keb'            => (float)$ttlKeb,                        // ← mentah
    //             'kg_out'             => 0.0,                                   // ← angka 0
    //             'lot_out'            => null,
    //             'tgl_retur'          => $retur['tgl_retur'],
    //             'kgs_retur'          => (float)($retur['kgs_retur'] ?? 0),     // ← mentah
    //             'lot_retur'          => $retur['lot_retur'],
    //             'ket_gbn'            => $retur['keterangan_gbn'],
    //         ];
    //     }

    //     if ($mergedData) {
    //         usort($mergedData, function ($a, $b) {
    //             // Bandingkan item_type (ASC)
    //             $cmpItem = strcmp($a['item_type'], $b['item_type']);
    //             if ($cmpItem !== 0) {
    //                 return $cmpItem;
    //             }

    //             // Bandingkan kode_warna (ASC)
    //             $cmpWarna = strcmp($a['kode_warna'], $b['kode_warna']);
    //             if ($cmpWarna !== 0) {
    //                 return $cmpWarna;
    //             }

    //             // Ambil tanggal (prioritas tgl_pakai, fallback ke tgl_retur)
    //             $tanggalA = $a['tgl_pakai'] ?: $a['tgl_retur'];
    //             $tanggalB = $b['tgl_pakai'] ?: $b['tgl_retur'];

    //             // Handle tanggal kosong supaya selalu di bawah
    //             if (empty($tanggalA) && !empty($tanggalB)) return 1;
    //             if (!empty($tanggalA) && empty($tanggalB)) return -1;

    //             // Bandingkan tanggal (DESC)
    //             return strtotime($tanggalB) <=> strtotime($tanggalA);
    //         });
    //     }

    //     $sisa = 0;
    //     foreach ($mergedData as $row) {
    //         if (
    //             $row['no_model'] === $pemesanan['no_model'] &&
    //             $row['item_type'] === $pemesanan['item_type'] &&
    //             $row['kode_warna'] === $pemesanan['kode_warna']
    //         ) {
    //             $sisa = $row['ttl_keb'] - $row['kg_out'] + $row['kgs_retur'] ?? 0;
    //             break;
    //         }
    //     }

    //     $data = [
    //         'active'        => $this->active,
    //         'title'         => 'Material System',
    //         'role'          => $this->role,
    //         'cluster'       => $clusterRows,
    //         'noModel'       => $noModel,
    //         'itemType'      => $pemesanan['item_type'],
    //         'kodeWarna'     => $pemesanan['kode_warna'],
    //         'color'         => $pemesanan['color'],
    //         'area'          => $pemesanan['admin'],
    //         'id'            => $id,
    //         'ketGbn'        => $ket['ket_gbn'] ?? '',
    //         'KgsPesan'      => $KgsPesan,
    //         'CnsPesan'      => $CnsPesan,
    //         'kgPersiapan'   => $kgPersiapan,
    //         'kgPengiriman'  => $kgPengiriman,
    //         'sisaKebutuhan' => $sisa,
    //     ];

    //     return view($this->role . '/pemesanan/select-cluster', $data);
    // }


    // SELECT CLUSTER V3 ALFA - dengan optimasi cache
    // di dalam class PemesananController
    private function makeCacheKey(string $prefix, array $parts): string
    {
        $raw = $prefix . '_' . implode('_', $parts);
        return preg_replace('/[^A-Za-z0-9_]/', '_', $raw); // aman utk CI cache
    }


    public function selectClusterWarehouse(int $id)
    {
        function safeCacheKey(string $prefix, array $parts): string
        {
            $raw = $prefix . '_' . implode('_', $parts);
            // sisakan A-Z a-z 0-9 dan underscore; yang lain diubah jadi underscore
            return preg_replace('/[^A-Za-z0-9_]/', '_', $raw);
        }
        $KgsPesan = (float)($this->request->getGet('KgsPesan') ?? 0);
        $CnsPesan = (int)($this->request->getGet('CnsPesan') ?? 0);
        $area     = $this->request->getGet('Area');

        $pemesanan = $this->totalPemesananModel->findWithDetails($id);
        if (!$pemesanan) {
            return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
        }

        $noModel = $pemesanan['no_model'];

        // --- agregat jalur/pengiriman (1 query) ---
        $agg = $this->pengeluaranModel->sumKgsByStatus($id);
        $kgPersiapan  = (float)($agg['kgs_persiapan'] ?? 0);
        $kgPengiriman = (float)($agg['kgs_pengiriman'] ?? 0);

        // --- cluster (1 query) ---
        $clusterRows = $this->stockModel->listCluster(
            $pemesanan['no_model'],
            $pemesanan['item_type'],
            $pemesanan['kode_warna'],
            $pemesanan['color']
        );

        // --- keterangan gabungan (1 query ringan) ---
        $ket = $this->pemesananModel->select('GROUP_CONCAT(DISTINCT keterangan_gbn) AS ket_gbn')
            ->where('id_total_pemesanan', $id)->first();

        $dataPemesanan = [];
        $dataRetur = [];

        if (!empty($area) && !empty($noModel)) {
            $dataPemesanan = $this->pemesananModel->getPemesananByAreaModel($area, $noModel);
            $dataRetur     = $this->returModel->getReturByAreaModel($area, $noModel);
        }

        // === PREFETCH semua style_size (sekali) utk kombinasi ini ===
        $styleRows = $this->materialModel->getStyleSizeByBb(
            $pemesanan['no_model'],
            $pemesanan['item_type'],
            $pemesanan['kode_warna']
        );
        $styleSizes = array_values(array_unique(array_column($styleRows, 'style_size')));

        // === Ambil QTY Capacity BATCH ===
        // cache 5 menit biar cepet kalo user bolak-balik
        $cacheKeyQty = safeCacheKey('qty_bulk', [$area, $noModel, implode(',', $styleSizes)]);
        // Ambil + cache 10 detik
        $qtyMap = cache()->remember($cacheKeyQty, 10, function () use ($noModel, $area, $styleSizes) {
            $client = service('curlrequest');
            $query  = http_build_query([
                'no_model'    => $noModel,
                'area'        => $area,
                'style_size' => implode(',', $styleSizes),
            ]);
            // dd ($query);
            try {
                $resp = $client->get("http://172.23.44.14/CapacityApps/public/api/getQtyOrderBulk?{$query}", [
                    'timeout' => 3,
                    'connect_timeout' => 1.5,
                ]);
                return json_decode($resp->getBody(), true) ?: [];
            } catch (\Throwable $e) {
                log_message('error', 'Bulk QTY API error: ' . $e->getMessage());
                return []; // fallback
            }
        });

        // === Ambil Po Tambahan BULK ===
        $poTambahanMap = $this->poTambahanModel->getKgPoTambahanBulk([
            'no_model'   => $pemesanan['no_model'],
            'item_type'  => $pemesanan['item_type'],
            'kode_warna' => $pemesanan['kode_warna'],
            'area'       => $area,
        ], $styleSizes);

        // Helper hitung kebutuhan utk satu baris (reusable utk pemesanan & retur)
        $hitungKebutuhan = function () use ($styleRows, $qtyMap, $poTambahanMap, $pemesanan) {
            $ttlKeb = 0.0;
            $ttlQty = 0;
            $isJht  = isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false;

            foreach ($styleRows as $sr) {
                $sz   = $sr['style_size'];
                $gw   = (float)($sr['gw'] ?? 0);
                $comp = (float)($sr['composition'] ?? 0);
                $loss = (float)($sr['loss'] ?? 0);
                $kgs  = (float)($sr['kgs'] ?? 0);

                $qty  = (int)($qtyMap[$sz]['qty'] ?? 0);
                $kgPo = (float)($poTambahanMap[$sz] ?? 0);

                if ($isJht) {
                    // Khusus JHT: pakai kgs langsung
                    $kebutuhan = $kgs + $kgPo;
                } else {
                    // rumus umum
                    $kebutuhan = (($qty * $gw * ($comp / 100.0)) * (1.0 + ($loss / 100.0)) / 1000.0) + $kgPo;
                }
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            return [$ttlKeb, $ttlQty];
        };

        // === Bangun mergedData jauh lebih cepat (tanpa N+1) ===
        $mergedData = [];

        foreach ($dataPemesanan as $pem) {
            // dd ($pem);
            [$ttlKeb, $ttlQty] = $hitungKebutuhan();

            $mergedData[] = [
                'no_model'           => $pem['no_model'],
                'item_type'          => $pem['item_type'],
                'kode_warna'         => $pem['kode_warna'],
                'color'              => $pem['color'],
                'max_loss'           => $pem['max_loss'],
                'tgl_pakai'          => $pem['tgl_pakai'],
                'id_total_pemesanan' => $pem['id_total_pemesanan'],
                'ttl_jl_mc'          => (int)($pem['ttl_jl_mc'] ?? 0),
                'ttl_kg'             => (float)($pem['ttl_kg'] ?? 0),
                'po_tambahan'        => (int)($pem['po_tambahan'] ?? 0),
                'ttl_keb'            => (float) $ttlKeb,
                'kg_out'             => (float)($pem['kgs_out'] ?? 0),
                'lot_out'            => $pem['lot_out'],
                'tgl_retur'          => null,
                'kgs_retur'          => 0.0,
                'lot_retur'          => null,
                'ket_gbn'            => null,
            ];
            // dd ($mergedData);
        }

        foreach ($dataRetur as $retur) {
            // NOTE: di kode lama kamu cek $pemesanan['item_type'] di loop retur — itu bug.
            [$ttlKeb, $ttlQty] = $hitungKebutuhan();

            $mergedData[] = [
                'no_model'           => $retur['no_model'],
                'item_type'          => $retur['item_type'],
                'kode_warna'         => $retur['kode_warna'],
                'color'              => $retur['warna'],
                'max_loss'           => 0,
                'tgl_pakai'          => null,
                'id_total_pemesanan' => null,
                'ttl_jl_mc'          => null,
                'ttl_kg'             => 0.0,
                'po_tambahan'        => 0,
                'ttl_keb'            => (float) $ttlKeb,
                'kg_out'             => 0.0,
                'lot_out'            => null,
                'tgl_retur'          => $retur['tgl_retur'],
                'kgs_retur'          => (float)($retur['kgs_retur'] ?? 0),
                'lot_retur'          => $retur['lot_retur'],
                'ket_gbn'            => $retur['keterangan_gbn'],
            ];
        }

        // (opsional) sorting seperti sebelumnya
        if ($mergedData) {
            // dd($mergedData);
            usort($mergedData, function ($a, $b) {
                $cmpItem  = strcmp($a['item_type'], $b['item_type']);
                if ($cmpItem) return $cmpItem;
                $cmpKode  = strcmp($a['kode_warna'], $b['kode_warna']);
                if ($cmpKode) return $cmpKode;
                $ta = $a['tgl_pakai'] ?: $a['tgl_retur'];
                $tb = $b['tgl_pakai'] ?: $b['tgl_retur'];
                if (empty($ta) && !empty($tb)) return 1;
                if (!empty($ta) && empty($tb)) return -1;
                return strtotime($tb) <=> strtotime($ta);
            });
        }

        // 1) Total kebutuhan (ttl_keb) dihitung sekali saja
        [$ttlKebForCombo, $ttlQtyForCombo] = $hitungKebutuhan(); // pakai styleRows/qtyMap/poTambahanMap

        // 2) Total kgs_out dari $dataPemesanan (status "Pengiriman Area" sudah di-SUM di subquery p)
        $ttlkgsout = 0.0;
        foreach ($dataPemesanan as $pem) {
            if (
                $pem['no_model']   === $pemesanan['no_model'] &&
                $pem['item_type']  === $pemesanan['item_type'] &&
                $pem['kode_warna'] === $pemesanan['kode_warna']
            ) {
                $ttlkgsout += (float) ($pem['kgs_out'] ?? 0);
            }
        }

        // 3) Total retur untuk kombinasi yang sama
        $ttlRetur = 0.0;
        foreach ($dataRetur as $ret) {
            if (
                $ret['no_model']   === $pemesanan['no_model'] &&
                $ret['item_type']  === $pemesanan['item_type'] &&
                $ret['kode_warna'] === $pemesanan['kode_warna']
            ) {
                $ttlRetur += (float) ($ret['kgs_retur'] ?? 0);
            }
        }

        // 4) Sisa kebutuhan = ttl_keb - total_kgs_out + total_retur
        $sisa = $ttlKebForCombo - $ttlkgsout + $ttlRetur;

        // render view dulu ke variabel
        $html = view($this->role . '/pemesanan/select-cluster', [
            'active'        => $this->active,
            'title'         => 'Material System',
            'role'          => $this->role,
            'cluster'       => $clusterRows,
            'noModel'       => $noModel,
            'itemType'      => $pemesanan['item_type'],
            'kodeWarna'     => $pemesanan['kode_warna'],
            'color'         => $pemesanan['color'],
            'area'          => $pemesanan['admin'],
            'id'            => $id,
            'ketGbn'        => $ket['ket_gbn'] ?? '',
            'KgsPesan'      => $KgsPesan,
            'CnsPesan'      => $CnsPesan,
            'kgPersiapan'   => $kgPersiapan,
            'kgPengiriman'  => $kgPengiriman,
            'sisaKebutuhan' => $sisa,
        ]);

        // setelah view selesai dibuat → hapus cache terkait
        $cacheKeyQty = $this->makeCacheKey('qty_bulk', [$area, $noModel, implode(',', $styleSizes)]);
        cache()->delete($cacheKeyQty);

        // lalu return hasil view
        return $html;
    }



    public function getDataByIdStok($id)
    {
        // $data = $this->stockModel->getDataByIdStok($id);
        $stock = $this->pemasukanModel->getDataByIdStok($id);
        log_message('debug', 'ini : ' . json_encode($stock));

        $data = [];
        foreach ($stock as $dt) {
            $other = $this->otherOutModel->getQty($dt['id_out_celup'], $dt['nama_cluster']);

            $data[] = [
                'id_pemasukan' => $dt['id_pemasukan'],
                'no_karung' => $dt['no_karung'],
                'tgl_masuk' => $dt['tgl_masuk'],
                'nama_cluster' => $dt['nama_cluster'],
                'no_model' => $dt['no_model'],
                'item_type' => $dt['item_type'],
                'kode_warna' => $dt['kode_warna'],
                'warna' => $dt['warna'],
                'lot_kirim' => $dt['lot_kirim'],
                'kgs_kirim' => round($dt['kgs_kirim'] - ($other[0]['kgs_other_out'] ?? 0), 2),
                'cones_kirim' => $dt['cones_kirim'] - ($other[0]['cns_other_out'] ?? 0),
                'id_out_celup' => $dt['id_out_celup']
            ];
        }
        // Debugging
        return $this->response->setJSON($data);
    }

    public function getDataByCluster()
    {
        $dataRaw = $this->request->getGet();
        log_message('debug', 'ini data cluster : ' . json_encode($dataRaw, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // normalize $data (trim + bersihkan whitespace aneh + samakan slash)
        $data = array_map(static function ($v) {
            if (!is_string($v)) return $v;
            // hapus zero-width & BOM
            $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v);
            // ganti NBSP dan full-width slash -> normal
            $v = str_replace(["\xC2\xA0", "／"], [' ', '/'], $v);
            // trim unicode-safe
            $v = preg_replace('/^\s+|\s+$/u', '', $v);
            return $v;
        }, $dataRaw);

        log_message('debug', 'ini trim data : ' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $stock = $this->pemasukanModel->getDataByCluster($data);
        log_message('debug', 'ini : ' . json_encode($stock));

        $data = [];
        foreach ($stock as $dt) {
            $other = $this->otherOutModel->getQty($dt['id_out_celup'], $dt['nama_cluster']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($dt['id_out_celup']);
            $pindahOrder = $this->historyStock->getKgsPindahOrder($dt['id_out_celup']);
            $sisaKg  = round($dt['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0) - ((float) $pindahOrder['kgs_pindah_order'] ?? 0), 2);
            $sisaCns = (int)$dt['cones_kirim'] - (int)($other['cns_other_out'] ?? 0) - (int)($outByCns['cns_out'] ?? 0) - ((int) $pindahOrder['cns_pindah_order'] ?? 0);
            // log_message('info', "ini sisaKg $sisaKg, sisaCns $sisaCns");
            if ($sisaKg <= 0 && $sisaCns <= 0) {
                // lewati baris ini; tidak layak tampil
                continue;
            }

            $data[] = [
                'id_pemasukan' => $dt['id_pemasukan'],
                'no_karung' => $dt['no_karung'],
                'tgl_masuk' => $dt['tgl_masuk'],
                'nama_cluster' => $dt['cluster_real'],
                'no_model' => $dt['no_model'],
                'item_type' => $dt['item_type'],
                'kode_warna' => $dt['kode_warna'],
                'warna' => $dt['warna'],
                'lot_kirim' => $dt['lot_kirim'],
                'kgs_kirim' => $sisaKg,
                'cones_kirim' => $sisaCns,
                'id_out_celup' => $dt['id_out_celup']
            ];
        }
        // Debugging
        // dd($data);

        return $this->response->setJSON($data);
    }

    public function saveUsage()
    {
        $session = session();
        // $session->remove('usage_data'); // Hapus hanya data penggunaan
        // $session->destroy(); // Hapus semua session

        // Ambil data dari JSON request
        $data = $this->request->getJSON(true);

        $idStok = $data['idStok'] ?? null;
        $qtyKGS = $data['qtyKGS'] ?? null;
        $qtyCNS = $data['qtyCNS'] ?? null;
        $qtyKarung = $data['qtyKarung'] ?? null;
        $noModel = $data['noModel'] ?? null;
        $namaCluster = $data['namaCluster'] ?? null;
        $idOutCelup = $data['idOutCelup'] ?? null;
        $area = $data['area'] ?? null;

        // Validasi jika ada nilai yang kosong
        if (empty($idStok) || empty($qtyKGS) || empty($qtyCNS) || empty($qtyKarung) || empty($noModel) || empty($namaCluster) || empty($idOutCelup)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Semua field harus diisi'
            ]);
        }

        $newData = [
            'id_stok' => $idStok,
            'qty_kgs' => $qtyKGS,
            'qty_cns' => $qtyCNS,
            'qty_karung' => $qtyKarung,
            'no_model' => $noModel,
            'nama_cluster' => $namaCluster,
            'id_out_celup' => $idOutCelup,
            'area' => $area
        ];

        // Cek apakah sudah ada data sebelumnya di session
        $usageData = $session->get('usage_data') ?? [];

        // Tambahkan data baru ke array yang sudah ada
        $usageData[] = $newData;

        // Simpan kembali ke session
        $session->set('usage_data', $usageData);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data penggunaan stock berhasil disimpan ke session',
            'data' => $usageData
        ]);
    }

    public function reportPemesananArea()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Pemesanan',
            'active' => $this->active,
        ];
        return view($this->role . '/pemesanan/report-pemesanan', $data);
    }

    public function filterPemesananArea()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananArea($key, $tanggalAwal, $tanggalAkhir);
        //Ambil dulu dari data kemudian ambil total pemesanan
        // $tgl_pakai = [];
        // $idTotalPemesanan = [];
        // $idMaterial = [];
        // $area = [];
        // $id_order = [];
        // $item_type = [];
        // $kode_warna = [];
        // foreach ($data as $dt) {
        //     $tgl_pakai[] = $dt['tgl_pakai'];
        //     $idTotalPemesanan[] = $dt['id_total_pemesanan'];
        //     $idMaterial[] = $dt['id_material'];
        //     $area[] = $dt['admin'];
        // }
        // dd($data, $tgl_pakai, $idTotalPemesanan, $idMaterial, $area);
        // $material = $this->materialModel->find($idMaterial);
        // $item_type = $this->materialModel->select('item_type')->whereIn('id_material', $idMaterial)->groupBy('item_type')->get()->getResultArray();
        // dd($item_type);
        // $totalPemesanan = $this->totalPemesananModel->getTotalPemesanan($area, $item_type, $kode_warna, $id_order, $tgl_pakai);
        return $this->response->setJSON($data);
    }
    public function getCountStatusRequest()
    {
        $countWt = $this->pemesananModel->countStatusRequest();

        return $this->response->setJSON(['count' => $countWt]);
    }
    public function requestAdditionalTime()
    {
        $dataRequest = $this->pemesananModel->getStatusRequest();
        $data = [
            'role' => $this->role,
            'title' => 'Additional Time',
            'active' => $this->active,
            'dataRequest' => $dataRequest,
        ];
        return view($this->role . '/pemesanan/additional-time', $data);
    }
    public function additionalTimeAccept()
    {
        $area      = $this->request->getPost('admin');
        $tglPakai  = $this->request->getPost('tgl_pakai');
        $jenis     = $this->request->getPost('jenis');
        $maxTime   = $this->request->getPost('max_time');
        $maxTime   = $maxTime . ':00';

        // Validasi input (opsional, tambahkan sesuai kebutuhan)
        if (empty($area) || empty($tglPakai) || empty($jenis) || empty($maxTime)) {
            return redirect()->to(base_url($this->role . '/pemesanan/requestAdditionalTime'))
                ->with('error', 'Input tidak valid');
        }
        $data = [
            'area' => $area,
            'tgl_pakai' => $tglPakai,
            'jenis' => $jenis,
            'max_time' => $maxTime,
            'username' => session()->get('username'),
        ];

        $success = $this->pemesananModel->additionalTimeAccept($data);

        return redirect()->to(base_url($this->role . '/pemesanan/requestAdditionalTime'))
            ->with(
                $success ? 'success' : 'error',
                $success ? 'Request berhasil disetujui'
                    : 'Request gagal diproses'
            );
    }
    public function additionalTimeReject()
    {
        $area      = $this->request->getPost('admin');
        $tglPakai  = $this->request->getPost('tgl_pakai');
        $jenis     = $this->request->getPost('jenis');
        $username  = session()->get('username');

        $success = $this->pemesananModel->additionalTimeReject($area, $tglPakai, $jenis, $username);

        return redirect()->to(base_url($this->role . '/pemesanan/requestAdditionalTime'))
            ->with(
                $success ? 'success' : 'error',
                $success ? 'Request berhasil ditolak'
                    : 'Request gagal diproses'
            );
    }

    public function permintaanKaretCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-permintaan-karet', $data);
    }

    public function permintaanSpandexCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-permintaan-spandex', $data);
    }

    public function getFilterPemesananKaret()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananKaret($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }

    public function getFilterPemesananSpandex()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananSpandex($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
    public function listBarangKeluarPertgl()
    {
        $jenis = $this->request->getPost('jenis');
        $tglPakaiFilter = $this->request->getPost('filter_date') ?? '';

        $tglPakai = $this->pemesananModel->getTglPemesananByJenis($jenis, $tglPakaiFilter);

        if ($this->request->getMethod() === 'post') {
            return $this->response->setJSON($tglPakai);
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis,
            'tglPakai' => $tglPakai,
        ];
        return view($this->role . '/pemesanan/persiapanBarangPertgl', $data);
    }
    public function filterListBarangKeluarPertgl()
    {
        // $area = $this->request->getPost('area');
        $jenis = $this->request->getPost('jenis');
        $filterDate = $this->request->getPost('filter_date');

        // log_message('debug', "Filter params: Area=$area, Jenis=$jenis, Tanggal=$filterDate");

        $dataPemesanan = $this->pemesananModel->getTglPemesananByJenis($jenis, $filterDate);

        // log_message('debug', 'Data retrieved: ' . json_encode($dataPemesanan));

        return $this->response->setJSON($dataPemesanan);
    }
    public function detailListBarangKeluar()
    {
        $jenis    = $this->request->getGet('jenis');
        $tglPakai = $this->request->getGet('tglPakai');

        $ttlPesan = $this->totalPemesananModel->getTtlPesan($jenis, $tglPakai);
        $ttlPersiapan = $this->pengeluaranModel->getTtlPersiapan($jenis, $tglPakai);
        // dd($ttlPesan, $ttlPersiapan);

        // Ambil noModel sesuai jenis request
        if ($this->request->isAJAX()) {
            // Kalau dari fetch POST JSON
            if ($this->request->getMethod() === 'post') {
                $noModel = $this->request->getJSON()->noModel ?? null;
            } else {
                // Kalau dari fetch GET query string
                $noModel = $this->request->getGet('noModel');
            }
        } else {
            // Normal GET request biasa
            $noModel = $this->request->getGet('noModel');
        }

        $detail = $this->pengeluaranModel->getDataPemesananExport($jenis, $tglPakai, $noModel);

        if ($this->request->isAJAX()) {
            // Return JSON kalau dari AJAX
            return $this->response->setJSON($detail);
        }

        // Return view kalau akses normal
        $data = [
            'active'   => $this->active,
            'title'    => 'Material System',
            'role'     => $this->role,
            'jenis'    => $jenis,
            'tglPakai' => $tglPakai,
            'detail'   => $detail,
            'ttlPesan' => $ttlPesan,
            'ttlPersiapan' => $ttlPersiapan
        ];
        return view($this->role . '/pemesanan/detailPersiapanBarangPertgl', $data);
    }
    // public function pemesananArea()
    // {
    //     function fetchApiData($url)
    //     {
    //         try {
    //             $response = file_get_contents($url);
    //             if ($response === false) {
    //                 throw new \Exception("Error fetching data from $url");
    //             }
    //             $data = json_decode($response, true);
    //             if (json_last_error() !== JSON_ERROR_NONE) {
    //                 throw new \Exception("Invalid JSON response from $url");
    //             }
    //             return $data;
    //         } catch (\Exception $e) {
    //             error_log($e->getMessage());
    //             return null;
    //         }
    //     }

    //     $tglPakai = $this->request->getGet('tgl_pakai');
    //     $noModel = $this->request->getGet('model');
    //     $role = session()->get('role');
    //     // dd($tglPakai);
    //     if (!$tglPakai) {
    //         $dataList = [];
    //     } else {
    //         $dataList = $this->pemesananModel->getDataPemesananArea($tglPakai, $noModel, $role);
    //     }
    //     // $dataList = [];
    //     // dd($dataList);
    //     foreach ($dataList as $key => $order) {
    //         $dataList[$key]['ttl_kebutuhan_bb'] = 0;
    //         $area = $order['admin'];
    //         if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
    //             $styleList = $this->materialModel->getStyleSizeByBb($order['no_model'], $order['item_type'], $order['kode_warna']);

    //             if ($styleList) {
    //                 $totalRequirement = 0;
    //                 foreach ($styleList as $style) {
    //                     if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
    //                         $orderApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model='
    //                             . $order['no_model'] . '&style_size=' . urlencode($style['style_size']) . '&area=' . urlencode($area);

    //                         // TAMBAHKAN INI UNTUK NAMPIL DI CONSOLE BROWSER
    //                         echo "<script>console.log('API URL: " . htmlspecialchars($orderApiUrl) . "');</script>";

    //                         $orderQty = fetchApiData($orderApiUrl);
    //                         if (isset($orderQty['qty'])) {
    //                             $requirement = $orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000;
    //                             $totalRequirement += $requirement;
    //                             $dataList[$key]['qty'] = $orderQty['qty'];
    //                         }
    //                     }
    //                 }
    //                 $dataList[$key]['ttl_kebutuhan_bb'] = $totalRequirement;
    //             }

    //             $data = [
    //                 'area' => $area,
    //                 'no_model' => $order['no_model'],
    //                 'item_type' => $order['item_type'],
    //                 'kode_warna' => $order['kode_warna'],
    //             ];

    //             $pengiriman = $this->pengeluaranModel->getTotalPengiriman($data);
    //             $dataList[$key]['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;

    //             // Hitung sisa jatah
    //             $dataList[$key]['sisa_jatah'] = $dataList[$key]['ttl_kebutuhan_bb'] - $dataList[$key]['ttl_pengiriman'];
    //         }
    //         // TAMPILKAN HASILNYA DI SINI
    //         // dd($dataList[$key]['ttl_kebutuhan_bb']);
    //     }

    //     $data = [
    //         'active' => $this->active,
    //         'title' => 'Pemesanan',
    //         'role' => $this->role,
    //         'dataList' => $dataList,
    //     ];
    //     return view($this->role . '/pemesanan/index', $data);
    // }

    public function pemesananArea()
    {
        return view($this->role . '/pemesanan/index', [
            'active' => $this->active,
            'title'  => 'Pemesanan',
            'role'   => $this->role,
        ]);
    }


    public function pemesananAreaData()
    {
        // --- ambil parameter DataTables
        $draw   = (int)($this->request->getGet('draw') ?? 1);
        $start  = (int)($this->request->getGet('start') ?? 0);
        $length = (int)($this->request->getGet('length') ?? 35);

        $tglPakai = trim($this->request->getGet('tgl_pakai') ?? '');
        $noModel  = trim($this->request->getGet('model') ?? '');
        $area     = trim($this->request->getGet('area') ?? '');
        $search   = trim($this->request->getGet('search')['value'] ?? '');
        $role     = session()->get('role') ?? 'user';

        // jika tgl_pakai kosong → kembalikan kosong (sesuai request sebelumnya)
        if ($tglPakai === '') {
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // ambil data dasar (tanpa paging dulu)
        $rows = $this->pemesananModel->getDataPemesananArea($tglPakai, $noModel, $role, $area, $search);

        $recordsTotal    = $rows['meta']['total'] ?? count($rows['data']);
        $recordsFiltered = $rows['meta']['filtered'] ?? count($rows['data']);
        $dataList        = $rows['data'];

        // H I T U N G  tambahan kebutuhan & pengiriman per baris
        // (dipindah ke endpoint AJAX agar view ringan)
        $enhanced = [];
        foreach ($dataList as $order) {
            $order['ttl_kebutuhan_bb'] = 0.0;
            $areaRow = $order['admin'];
            if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                // ambil style size & parameter gw/composition/loss
                $styleList = $this->materialModel->getStyleSizeByBb(
                    $order['no_model'],
                    $order['item_type'],
                    $order['kode_warna']
                );

                if ($styleList) {
                    $totalRequirement = 0.0;
                    foreach ($styleList as $style) {
                        if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
                            $orderApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model='
                                . $order['no_model'] . '&style_size=' . urlencode($style['style_size']) . '&area=' . urlencode($areaRow);

                            // panggil helper lokal (tanpa echo di view)
                            $orderQty = $this->fetchApiDataSilently($orderApiUrl);
                            if (isset($orderQty['qty'])) {
                                $requirement = $orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000;
                                $totalRequirement += $requirement;
                                $order['qty'] = $orderQty['qty'];
                            }
                        }
                    }
                    $order['ttl_kebutuhan_bb'] = $totalRequirement;
                }

                // total pengiriman aktual area
                $reqPengiriman = [
                    'area'      => $areaRow,
                    'no_model'  => $order['no_model'],
                    'item_type' => $order['item_type'],
                    'kode_warna' => $order['kode_warna'],
                ];
                $pengiriman = $this->pengeluaranModel->getTotalPengiriman($reqPengiriman);
                $order['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;
                $order['sisa_jatah']     = ($order['ttl_kebutuhan_bb'] ?? 0) - ($order['ttl_pengiriman'] ?? 0);
            }

            // kolom turunan
            $ttl_kg_pesan  = (float)$order['qty_pesan'] - (float)$order['qty_sisa'];
            $ttl_cns_pesan = (int)$order['cns_pesan'] - (int)$order['cns_sisa'];

            $order['_ttl_kg_pesan']  = number_format($ttl_kg_pesan, 2);
            $order['_ttl_cns_pesan'] = $ttl_cns_pesan;
            $order['_ttl_pengiriman'] = number_format((float)$order['ttl_pengiriman'], 2);
            $order['_sisa_jatah']    = number_format((float)$order['sisa_jatah'], 2);
            $order['_status_jatah']  = ($order['sisa_jatah'] > 0)
                ? (($ttl_kg_pesan >= $order['sisa_jatah']) ? 'Pemesanan Melebihi Jatah' : '')
                : 'Habis Jatah';

            $enhanced[] = $order;
        }

        // paging di level PHP (opsional, bisa juga dilakukan di query builder jika mau)
        // $paged = array_slice($enhanced, $start, $length);

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal'    => count($enhanced),   // opsional, biar konsisten
            'recordsFiltered' => count($enhanced),
            'data' => $enhanced,                     // kirim full
        ]);
    }

    /**
     * Helper panggil API tanpa echo.
     */
    private function fetchApiDataSilently($url)
    {
        try {
            $response = @file_get_contents($url);
            if ($response === false) return [];
            $data = json_decode($response, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $data : [];
        } catch (\Throwable $e) {
            return [];
        }
    }


    public function getUpdateListPemesanan()
    {
        $role = session()->get('role');
        $data = $this->request->getPost([
            'area',
            'tgl_pakai',
            'no_model',
            'item_type',
            'kode_warna',
            'color',
            'po_tambahan'
        ]);
        // Log isi $data
        log_message('info', 'getUpdateListPemesanan → input data: ' . print_r($data, true));

        $dataList = $this->pemesananModel->getListPemesananByUpdate($data, $role);

        // Cetak ke log (application/logs) dengan level INFO
        log_message('info', 'getUpdateListPemesanan → dataList: ' . print_r($dataList, true));

        return $this->respond([
            'status'  => 'success',
            'data' => $dataList,
        ], 200);
    }
    public function sisaKebutuhanArea()
    {
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);
        $allArea = json_decode($response, true);

        // get filter
        $area = $this->request->getGet('filter_area') ?? '';
        $noModel = $this->request->getGet('filter_model') ?? '';

        // Initialize dataPemesanan as empty by default
        $dataPemesanan = [];
        $dataRetur = [];

        if (!empty($area) && !empty($noModel)) {
            $dataPemesanan = $this->pemesananModel->getPemesananByAreaModel($area, $noModel);
            $dataRetur = $this->returModel->getReturByAreaModel($area, $noModel);
        }
        // dd($dataPemesanan);

        $mergedData = [];
        $kebutuhan = [];

        // Tambahkan semua data pemesanan ke mergedData
        foreach ($dataPemesanan as $key => $pemesanan) {
            // ambil data styleSize by bb
            $getStyle = $this->materialModel->getStyleSizeByBb($pemesanan['no_model'], $pemesanan['item_type'], $pemesanan['kode_warna']);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $urlQty = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=' . urlencode($pemesanan['no_model'])
                    . '&style_size=' . urlencode($data['style_size'])
                    . '&area=' . $area;

                $qtyResponse = file_get_contents($urlQty);
                $qtyData     = json_decode($qtyResponse, true);
                $qty         = (intval($qtyData['qty']) ?? 0);

                // Ambil kg po tambahan
                $kgPoTambahan = floatval(
                    $this->poTambahanModel->getKgPoTambahan([
                        'no_model'    => $pemesanan['no_model'],
                        'item_type'   => $pemesanan['item_type'],
                        'kode_warna'  => $pemesanan['kode_warna'],
                        'style_size'  => $data['style_size'],
                        'area'        => $area,
                    ])['ttl_keb_potambahan'] ?? 0
                );

                if ($qty >= 0) {
                    if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
                        $kebutuhan = $data['kgs'] ?? 0;
                    } else {
                        $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
                    }
                    $pemesanan['ttl_keb'] = $ttlKeb;
                }
                // dd($kgPoTambahan);
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            $pemesanan['qty']     = $ttlQty; // ttl qty pcs
            $pemesanan['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


            $mergedData[] = [
                'no_model'           => $pemesanan['no_model'],
                'item_type'          => $pemesanan['item_type'],
                'kode_warna'         => $pemesanan['kode_warna'],
                'color'              => $pemesanan['color'],
                'max_loss'           => $pemesanan['max_loss'],
                'tgl_pakai'          => $pemesanan['tgl_pakai'],
                'id_total_pemesanan' => $pemesanan['id_total_pemesanan'],
                'ttl_jl_mc'          => (int)($pemesanan['ttl_jl_mc'] ?? 0),
                'ttl_kg'             => (float)($pemesanan['ttl_kg'] ?? 0),   // ← JANGAN number_format di sini
                'ttl_cns'            => (int)($pemesanan['ttl_cns'] ?? 0),  // ← JANGAN number_format di sini
                'po_tambahan'        => (int)($pemesanan['po_tambahan'] ?? 0),
                'ttl_keb'            => (float)$ttlKeb,                       // ← hasil hitung, mentah
                'kg_out'             => (float)($pemesanan['kgs_out'] ?? 0),  // ← mentah
                'cns_out'            => (int)($pemesanan['cns_out'] ?? 0),  // ← mentah
                'lot_out'            => $pemesanan['lot_out'],
                // field retur kosong
                'tgl_retur'          => null,
                'kgs_retur'          => null,
                'cns_retur'          => null,
                'lot_retur'          => null,
                'ket_gbn'            => null,
            ];
            $kebutuhanDipakai[$key] = true;
        }

        // Tambahkan semua data retur ke mergedData (data pemesanan diset null)
        foreach ($dataRetur as $retur) {
            // ambil data styleSize by bb
            $getStyle = $this->materialModel->getStyleSizeByBb($retur['no_model'], $retur['item_type'], $retur['kode_warna']);
            // dd($getStyle);

            $ttlKeb = 0;
            $ttlQty = 0;

            foreach ($getStyle as $i => $data) {
                // Ambil qty
                $urlQty = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=' . $retur['no_model']
                    . '&style_size=' . urlencode($data['style_size'])
                    . '&area=' . $area;

                $qtyResponse = file_get_contents($urlQty);
                $qtyData     = json_decode($qtyResponse, true);
                $qty         = (intval($qtyData['qty']) ?? 0);

                // Ambil kg po tambahan
                $kgPoTambahan = floatval(
                    $this->poTambahanModel->getKgPoTambahan([
                        'no_model'    => $retur['no_model'],
                        'item_type'   => $retur['item_type'],
                        'kode_warna'  => $retur['kode_warna'],
                        'style_size'  => $data['style_size'],
                        'area'        => $area,
                    ])['ttl_keb_potambahan'] ?? 0
                );

                if ($qty >= 0) {
                    if (isset($pemesanan['item_type']) && stripos($pemesanan['item_type'], 'JHT') !== false) {
                        $kebutuhan = $data['kgs'] ?? 0;
                    } else {
                        $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
                    }
                    $retur['ttl_keb'] = $ttlKeb;
                }
                $ttlKeb += $kebutuhan;
                $ttlQty += $qty;
            }
            $retur['qty']     = $ttlQty; // ttl qty pcs
            $retur['ttl_keb'] = $ttlKeb; // ttl kebutuhan bb


            $mergedData[] = [
                'no_model'           => $retur['no_model'],
                'item_type'          => $retur['item_type'],
                'kode_warna'         => $retur['kode_warna'],
                'color'              => $retur['warna'],
                'max_loss'           => 0,
                'tgl_pakai'          => null,
                'id_total_pemesanan' => null,
                'ttl_jl_mc'          => null,
                'ttl_kg'             => 0.0,                                   // ← angka 0
                'ttl_cns'            => 0,                                     // ← angka 0
                'po_tambahan'        => 0,
                'ttl_keb'            => (float)$ttlKeb,                        // ← mentah
                'kg_out'             => 0.0,                                   // ← angka 0
                'cns_out'            => 0,                                     // ← angka 0
                'lot_out'            => null,
                'tgl_retur'          => $retur['tgl_retur'],
                'kgs_retur'          => (float)($retur['kgs_retur'] ?? 0),     // ← mentah
                'cns_retur'          => (int)($retur['cns_retur'] ?? 0),     // ← mentah
                'lot_retur'          => $retur['lot_retur'],
                'ket_gbn'            => $retur['keterangan_gbn'],
            ];
        }

        if ($mergedData) {
            usort($mergedData, function ($a, $b) {
                // Bandingkan item_type (ASC)
                $cmpItem = strcmp($a['item_type'], $b['item_type']);
                if ($cmpItem !== 0) {
                    return $cmpItem;
                }

                // Bandingkan kode_warna (ASC)
                $cmpWarna = strcmp($a['kode_warna'], $b['kode_warna']);
                if ($cmpWarna !== 0) {
                    return $cmpWarna;
                }

                // Ambil tanggal (prioritas tgl_pakai, fallback ke tgl_retur)
                $tanggalA = $a['tgl_pakai'] ?: $a['tgl_retur'];
                $tanggalB = $b['tgl_pakai'] ?: $b['tgl_retur'];

                // Handle tanggal kosong supaya selalu di bawah
                if (empty($tanggalA) && !empty($tanggalB)) return 1;
                if (!empty($tanggalA) && empty($tanggalB)) return -1;

                // Bandingkan tanggal (DESC)
                return strtotime($tanggalB) <=> strtotime($tanggalA);
            });
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'allArea' => $allArea,
            'dataPemesanan' => $mergedData, // Pass the filtered data
            'area' => $area, // Pass the filtered data
            'noModel' => $noModel, // Pass the filtered data
        ];

        // dd($data);
        return view($this->role . '/pemesanan/sisaKebutuhanArea', $data);
    }

    // JANGAN DI HAPUS BUAT PERBANDINGAN NANTI KALO DATA NYA BANYAK LAMA YG MANA
    // public function sisaKebutuhanArea()
    // {
    //     // Ambil daftar area dari API eksternal
    //     $apiUrl   = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
    //     $response = file_get_contents($apiUrl);
    //     $allArea  = json_decode($response, true) ?? [];

    //     // Ambil filter
    //     $area    = $this->request->getGet('filter_area')  ?? '';
    //     $noModel = $this->request->getGet('filter_model') ?? '';

    //     // Proses data pemesanan dan retur jika filter diisi
    //     $dataP = [];
    //     $dataR = [];
    //     if (!empty($area) && !empty($noModel)) {
    //         $dataP = $this->processRecords(
    //             $this->pemesananModel->getPemesananByAreaModel($area, $noModel),
    //             false,
    //             $area
    //         );

    //         $dataR = $this->processRecords(
    //             $this->returModel->getReturByAreaModel($area, $noModel),
    //             true,
    //             $area
    //         );
    //     }

    //     // Gabung dan sort hasil
    //     $merged = array_merge($dataP, $dataR);
    //     $this->sortByTanggalDesc($merged);

    //     // Render view
    //     return view(
    //         $this->role . '/pemesanan/sisaKebutuhanArea',
    //         [
    //             'active'        => $this->active,
    //             'title'         => 'Material System',
    //             'role'          => $this->role,
    //             'allArea'       => $allArea,
    //             'dataPemesanan' => $merged,
    //             'area'          => $area,
    //             'noModel'       => $noModel,
    //         ]
    //     );
    // }

    // private function fetchQtyForStyles(string $noModel, string $area, array $styles): array
    // {
    //     $results = [];
    //     foreach ($styles as $style) {
    //         $url = sprintf(
    //             'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=%s&style_size=%s&area=%s',
    //             $noModel,
    //             urlencode($style['style_size']),
    //             $area
    //         );
    //         $resp = json_decode(file_get_contents($url), true) ?? [];
    //         $results[$style['style_size']] = intval($resp['qty'] ?? 0);
    //     }
    //     return $results;
    // }

    // private function processRecords(array $records, bool $isRetur, string $area): array
    // {
    //     $output = [];
    //     foreach ($records as $r) {
    //         // Ambil styleSize data
    //         $styles  = $this->materialModel->getStyleSizeByBb(
    //             $r['no_model'],
    //             $r['item_type'],
    //             $isRetur ? $r['kode_warna'] : $r['kode_warna']
    //         );

    //         // Caching qty per style
    //         $qtyMap  = $this->fetchQtyForStyles($r['no_model'], $area, $styles);

    //         $ttlQty  = 0;
    //         $ttlKeb  = 0.0;

    //         foreach ($styles as $s) {
    //             $qty = $qtyMap[$s['style_size']] ?? 0;
    //             $poTambahan = floatval(
    //                 $this->poTambahanModel->getKgPoTambahan([
    //                     'no_model'   => $r['no_model'],
    //                     'item_type'  => $r['item_type'],
    //                     'kode_warna' => $r['kode_warna'],
    //                     'style_size' => $s['style_size'],
    //                     'area'       => $area,
    //                 ])['ttl_keb_potambahan'] ?? 0
    //             );

    //             if ($qty > 0) {
    //                 $keb = (
    //                     ($qty * $s['gw'] * ($s['composition']/100))
    //                     * (1 + ($s['loss']/100))
    //                     / 1000
    //                 ) + $poTambahan;
    //                 $ttlKeb += $keb;
    //                 $ttlQty += $qty;
    //             }
    //         }

    //         // Bangun row output
    //         $row = [
    //             'no_model'           => $r['no_model'],
    //             'item_type'          => $r['item_type'],
    //             'kode_warna'         => $r['kode_warna'],
    //             'color'              => $isRetur ? $r['warna'] : $r['color'],
    //             'max_loss'           => $isRetur ? 0      : $r['max_loss'],
    //             'tgl_pakai'          => $isRetur ? null   : $r['tgl_pakai'],
    //             'tgl_retur'          => $isRetur ? $r['tgl_retur'] : null,
    //             'id_total_pemesanan' => $isRetur ? null   : $r['id_total_pemesanan'],
    //             'ttl_jl_mc'          => $isRetur ? null   : $r['ttl_jl_mc'],
    //             'ttl_kg'             => $isRetur ? null   : number_format($r['ttl_kg'], 2),
    //             'po_tambahan'        => $isRetur ? null   : $r['po_tambahan'],
    //             'qty'                => $ttlQty,
    //             'ttl_keb'            => number_format($ttlKeb, 2),
    //             'kg_out'             => $isRetur ? null   : number_format($r['kgs_out'] ?? 0, 2),
    //             'lot_out'            => $isRetur ? null   : $r['lot_out'],
    //             'kgs_retur'          => $isRetur ? number_format($r['kgs_retur'], 2) : null,
    //             'lot_retur'          => $isRetur ? $r['lot_retur'] : null,
    //             'ket_gbn'            => $isRetur ? $r['keterangan_gbn'] : null,
    //         ];

    //         $output[] = $row;
    //     }
    //     return $output;
    // }

    // private function sortByTanggalDesc(array &$data)
    // {
    //     usort($data, function ($a, $b) {
    //         $cmp = strcmp($a['item_type'], $b['item_type']);
    //         if ($cmp !== 0) return $cmp;

    //         $cmp = strcmp($a['kode_warna'], $b['kode_warna']);
    //         if ($cmp !== 0) return $cmp;

    //         $tA = $a['tgl_pakai'] ?: $a['tgl_retur'];
    //         $tB = $b['tgl_pakai'] ?: $b['tgl_retur'];

    //         if (empty($tA) && !empty($tB)) return 1;
    //         if (!empty($tA) && empty($tB)) return -1;

    //         return strtotime($tB) <=> strtotime($tA);
    //     });
    // }
    //  END JANGAN DI HAPUS BUAT PERBANDINGAN NANTI KALO DATA NYA BANYAK LAMA YG MANA


    public function optionsPinjamOrder()
    {
        $itemType  = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getData = $this->stockModel->getPinjamOrder($itemType, $kodeWarna);

        return $this->response->setJSON($getData);
    }

    public function getNoModelPinjamOrder()
    {
        $noModel  = $this->request->getGet('no_model');
        $itemType  = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getData = $this->stockModel->getNoModelPinjamOrder($noModel, $itemType, $kodeWarna);

        return $this->response->setJSON($getData);
    }

    public function getClusterPinjamOrder()
    {
        $noModel  = $this->request->getGet('no_model');
        $itemType  = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getData = $this->stockModel->getClusterPinjamOrder($noModel, $itemType, $kodeWarna);
        log_message('info', 'no model :' . $noModel);
        log_message('info', 'item type :' . $itemType);
        log_message('info', 'kode warna :' . $kodeWarna);
        log_message('info', 'kode warna :' . json_encode($getData));
        return $this->response->setJSON($getData);
    }

    public function detailPinjamOrder()
    {
        $noModel   = $this->request->getGet('no_model');
        $itemType  = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');
        $cluster = $this->request->getGet('cluster');

        $detail = $this->stockModel->getPinjamOrderDetail($noModel, $itemType, $kodeWarna, $cluster);
        return $this->response->setJSON($detail);
    }
    public function HistoryPinjamOrder()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        $dataPinjam = $this->historyStock->getHistoryPinjamOrder($noModel, $kodeWarna);
        // dd($dataPinjam);

        // // 2) Siapkan HTTP client
        // $client = \Config\Services::curlrequest([
        //     'baseURI' => 'http://172.23.44.14/CapacityApps/public/api/',
        //     'timeout' => 5
        // ]);

        // // 3) Loop dan merge API result
        // foreach ($dataPindah as &$row) {
        //     try {
        //         $res = $client->get('getDeliveryAwalAkhir', [
        //             'query' => ['model' => $row['no_model_new']]
        //         ]);
        //         $body = json_decode($res->getBody(), true);
        //         $row['delivery_awal']  = $body['delivery_awal']  ?? '-';
        //         $row['delivery_akhir'] = $body['delivery_akhir'] ?? '-';
        //     } catch (\Exception $e) {
        //         $row['delivery_awal']  = '-';
        //         $row['delivery_akhir'] = '-';
        //     }
        // }
        // unset($row);

        // 4) Response
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($dataPinjam);
        }

        return view($this->role . '/pemesanan/history-pinjam-order', [
            'role'    => $this->role,
            'title'   => 'History Pindah Order',
            'active'  => $this->active,
            'history' => $dataPinjam,
        ]);
    }
    public function saveKetGbnInPemesanan()
    {
        $id = $this->request->getPost('id_total_pemesanan');
        $ket = $this->request->getPost('keterangan_gbn');
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap'])->setStatusCode(400);
        }

        $this->pemesananModel
            ->where('id_total_pemesanan', $id)
            ->set('keterangan_gbn', $ket)
            ->set('hak_akses', session()->get('username'))
            ->update();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Keterangan berhasil disimpan']);
    }

    public function ubahTanggalPemesanan()
    {
        $listArea = $this->masterRangePemesanan->getArea();
        $sort = ['KK1A', 'KK1B', 'KK2A', 'KK2B', 'KK2C', 'KK5G', 'KK7K', 'KK7L', 'KK8D', 'KK8F', 'KK8J', 'KK9D', 'KK10', 'KK11M'];
        usort($listArea, function ($a, $b) use ($sort) {
            $posA = array_search($a['area'], $sort);
            $posB = array_search($b['area'], $sort);

            $posA = $posA === false ? PHP_INT_MAX : $posA;
            $posB = $posB === false ? PHP_INT_MAX : $posB;

            return $posA <=> $posB;
        });

        $date = DATE('Y-m-d');
        $day = date('l');
        $dayList = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $today = $dayList[$day] ?? $day;

        $rangeTgl = $this->masterRangePemesanan->getRangeByDay($day);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'date' => $date,
            'today'  => $today,
            'day'  => $day,
            'rangeTgl' => $rangeTgl,
            'listArea' => $listArea,
        ];
        return view($this->role . '/pemesanan/ubah-tgl-pemesanan', $data);
    }

    public function updateRangeSeluruhArea()
    {
        $getPost = $this->request->getPost();
        $day = $getPost['days'] ?? null;

        if (!empty($getPost)) {
            $updateData = [
                'range_spandex' => $getPost['range_spandex'] ?? null,
                'range_karet' => $getPost['range_karet'] ?? null,
                'range_benang' => $getPost['range_benang'] ?? null,
                'range_nylon' => $getPost['range_nylon'] ?? null,
            ];

            $this->masterRangePemesanan->where('days', $day)->set($updateData)->update();

            return redirect()->back()->with('success', 'Range tanggal berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Data tidak valid.');
        }
    }

    public function updateRangeAreaTertentu()
    {
        $post = $this->request->getPost();

        $areas      = (array) ($post['area'] ?? []);
        $days       = (array) ($post['days'] ?? []);
        $spandexArr = (array) ($post['range_spandex'] ?? []);
        $karetArr   = (array) ($post['range_karet'] ?? []);
        $benangArr  = (array) ($post['range_benang'] ?? []);
        $nylonArr   = (array) ($post['range_nylon'] ?? []);

        if (empty($areas) || empty($days)) {
            return redirect()->back()->with('error', 'Area atau days kosong.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $totalUpdated = 0;
        $count = max(count($areas), count($days));
        for ($i = 0; $i < $count; $i++) {
            $area = $areas[$i] ?? null;
            $day  = $days[$i] ?? null;
            if (!$area || !$day) continue; // skip pasangan tak lengkap

            // Ambil nilai scalar untuk index ini (bisa null)
            $updateData = [
                'range_spandex' => $spandexArr[$i] ?? null,
                'range_karet'   => $karetArr[$i] ?? null,
                'range_benang'  => $benangArr[$i] ?? null,
                'range_nylon'   => $nylonArr[$i] ?? null,
            ];

            // Pastikan semua value scalar (bukan array)
            foreach ($updateData as $k => $v) {
                if (is_array($v)) $updateData[$k] = null;
            }

            $this->masterRangePemesanan
                ->where('days', $day)
                ->where('area', $area)
                ->set($updateData)
                ->update();

            // cek affectedRows (opsional)
            if ($this->masterRangePemesanan->db->affectedRows() > 0) {
                $totalUpdated++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update, rollback.');
        }

        return redirect()->back()->with('success', "Selesai. Total terupdate: {$totalUpdated}.");
    }
}
