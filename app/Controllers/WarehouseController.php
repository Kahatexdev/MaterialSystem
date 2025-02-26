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
use Picqer\Barcode\BarcodeGeneratorPNG;

class WarehouseController extends BaseController
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
    protected $clusterModel;
    protected $pemasukanModel;
    protected $stockModel;
    protected $historyPindahPalet;
    protected $historyPindahOrder;
    protected $pengeluaranModel;

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
        return view($this->role . '/warehouse/index', $data);
    }
    public function pemasukan()
    {
        $id = $this->request->getPost('barcode');

        // $id = base64_decode($id);
        // dd($id);
        $cluster = $this->clusterModel->getDataCluster();

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataOut') ?? [];

        if (!empty($id)) {
            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!' . $id);
                    return redirect()->to(base_url($this->role . '/pemasukan'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $outCelup = $this->outCelupModel->getDataOut($id);

            if (empty($outCelup)) {
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!' . $id);
                return redirect()->to(base_url($this->role . '/pemasukan'));
            } elseif (!empty($outCelup)) {
                // Tambahkan data baru ke dalam array
                $existingData = array_merge($existingData, $outCelup);
            }

            // Simpan kembali ke session
            session()->set('dataOut', $existingData);

            // Redirect agar form tidak resubmit saat refresh
            return redirect()->to(base_url($this->role . '/pemasukan'));
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataOut' => $existingData, // Tampilkan data dari session
            'cluster' => $cluster,
            'error' => session()->getFlashdata('error'),
        ];

        return view($this->role . '/warehouse/form-pemasukan', $data);
    }
    public function prosesPemasukan()
    {
        $action = $this->request->getPost('action'); // Ambil tombol yang diklik

        if ($action === 'simpan') {
            // Proses Simpan Pemasukan
            return $this->prosesSimpanPemasukan();
        } elseif ($action === 'komplain') {
            // Proses Komplain
            return $this->prosesKomplain();
        } else {
            session()->setFlashdata('error', 'Aksi tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }
    }
    public function prosesSimpanPemasukan()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');
        $noModels = $this->request->getPost('no_model');
        $itemTypes = $this->request->getPost('item_type');
        $kodeWarnas = $this->request->getPost('kode_warna');
        $warnas = $this->request->getPost('warna');
        $kgsMasuks = $this->request->getPost('kgs_kirim');
        $cnsMasuks = $this->request->getPost('cns_kirim');
        $tglMasuks = $this->request->getPost('tgl_masuk');
        $namaClusters = $this->request->getPost('cluster');
        $lotKirim = $this->request->getPost('lot_kirim');

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }

        // Pastikan nama_cluster ada di dalam tabel cluster
        $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

        if ($clusterExists === 0) {
            session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $dataPemasukan = [];

        foreach ($checkedIds as $key => $idOut) {
            $dataPemasukan[] = [
                'id_out_celup' => $idOutCelup[$key] ?? null,
                'no_model' => $noModels[$key] ?? null,
                'item_type' => $itemTypes[$key] ?? null,
                'kode_warna' => $kodeWarnas[$key] ?? null,
                'warna' => $warnas[$key] ?? null,
                'kgs_masuk' => $kgsMasuks[$key] ?? null,
                'cns_masuk' => $cnsMasuks[$key] ?? null,
                'tgl_masuk' => $tglMasuks[$key] ?? null,
                'nama_cluster' => $namaClusters,
                'admin' => session()->get('username')
            ];
        }

        // Debugging: cek apakah data tidak kosong sebelum insert
        if (empty($dataPemasukan)) {
            session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
            return redirect()->to($this->role . '/pemasukan');
        }

        // Ambil data session
        $checked = session()->get('dataOut');

        // Jika session tidak kosong
        if (!empty($checked)) {
            // Ambil daftar ID yang ingin dihapus
            $idToRemove = array_column($dataPemasukan, 'id_out_celup');

            // Filter session agar hanya menyisakan data yang tidak ada di $dataPemasukan
            $filteredChecked = array_filter($checked, function ($tes) use ($idToRemove) {
                return !in_array($tes['id_out_celup'], $idToRemove);
            });

            // Jika hasil filtering masih ada data, simpan kembali ke session
            if (!empty($filteredChecked)) {
                session()->set('dataOut', array_values($filteredChecked));
            } else {
                // Hapus session jika tidak ada data tersisa
                session()->remove('dataOut');
            }
        }

        $cekDuplikat = $this->pemasukanModel
            ->whereIn('id_out_celup', array_column($dataPemasukan, 'id_out_celup'))
            ->countAllResults();

        if ($cekDuplikat == 0) {
            //insert tabel pemasukan
            if ($this->pemasukanModel->insertBatch($dataPemasukan)) {
                $dataStock = [];
                foreach ($checkedIds as $key => $idOut) {
                    $dataStock[] = [
                        'no_model' => $noModels[$key] ?? null,
                        'item_type' => $itemTypes[$key] ?? null,
                        'kode_warna' => $kodeWarnas[$key] ?? null,
                        'warna' => $warnas[$key] ?? null,
                        'kgs_in_out' => $kgsMasuks[$key] ?? null,
                        'cns_in_out' => $cnsMasuks[$key] ?? null,
                        'krg_in_out' => 1, // Asumsikan setiap pemasukan hanya 1 kali
                        'lot_stock' => $lotKirim[$key] ?? null,
                        'nama_cluster' => $namaClusters,
                        'admin' => session()->get('username')
                    ];
                }

                foreach ($dataStock as $stock) {
                    $existingStock = $this->stockModel
                        ->where('no_model', $stock['no_model'])
                        ->where('item_type', $stock['item_type'])
                        ->where('kode_warna', $stock['kode_warna'])
                        ->where('lot_stock', $stock['lot_stock'])
                        ->first(); // Ambil satu record yang sesuai

                    if ($existingStock) {
                        // Jika sudah ada, update jumlahnya
                        $this->stockModel->update($existingStock['id_stock'], [
                            'kgs_in_out' => $existingStock['kgs_in_out'] + $stock['kgs_in_out'],
                            'cns_in_out' => $existingStock['cns_in_out'] + $stock['cns_in_out'],
                            'krg_in_out' => $existingStock['krg_in_out'] + 1
                        ]);
                    } else {
                        // Jika belum ada, insert data baru
                        $this->stockModel->insert($stock);
                    }
                }

                session()->setFlashdata('success', 'Data berhasil dimasukkan.');
            }
        } else {
            session()->setFlashdata('error', 'Gagal, Data pemasukan sudah ada.');
        }
        return redirect()->to($this->role . '/pemasukan');
    }
    private function prosesKomplain()
    {
        $checkedIds = $this->request->getPost('checked_id');

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dikomplain.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');
        $alasan = $this->request->getPost('alasan');

        $idCelup = $this->outCelupModel->getIdCelups($idOutCelup);


        // Tambahkan proses komplain sesuai kebutuhanmu
        $update = $this->scheduleCelupModel
            ->whereIn('id_celup', array_column($idCelup, 'id_celup'))
            ->set([
                'last_status' => 'complain',
                'ket_daily_cek' => $alasan
            ])
            ->update();

        if ($update) {
            // Ambil session dataOut
            $existingData = session()->get('dataOut') ?? [];

            // Pastikan $idOutCelup dalam bentuk array
            $idOutCelup = is_array($idOutCelup) ? $idOutCelup : [$idOutCelup];

            // Hapus data berdasarkan idOutCelup
            $filteredData = array_filter($existingData, function ($item) use ($idOutCelup) {
                return !in_array($item['id_out_celup'], $idOutCelup);
            });

            // Simpan kembali dataOut ke session tanpa data yang dihapus
            session()->set('dataOut', array_values($filteredData));

            session()->setFlashdata('success', 'Data berhasil dikomplain');
        } else {
            session()->setFlashdata('error', 'Gagal mengkomplain data.');
        }
        return redirect()->to($this->role . '/pemasukan');
    }
    public function reset_pemasukan()
    {
        session()->remove('dataOut');
        return redirect()->to(base_url($this->role . '/pemasukan'));
    }
    public function hapusListPemasukan()
    {
        $id = $this->request->getPost('id');

        // Ambil data dari session
        $existingData = session()->get('dataOut') ?? [];

        // Cek apakah data dengan ID yang dikirim ada di session
        foreach ($existingData as $key => $item) {
            if ($item['id_out_celup'] == $id) {
                // Hapus data tersebut dari array
                unset($existingData[$key]);
                // Update session dengan data yang sudah dihapus
                session()->set('dataOut', array_values($existingData));
                // Debug response
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function getCluster()
    {
        $kgs = $this->request->getPost('kgs');

        if ($kgs === null || $kgs === '') {
            return $this->response->setJSON([]); // Jika kosong, kirim array kosong
        }

        $data = $this->clusterModel->getCluster($kgs);

        return $this->response->setJSON($data);
    }
    public function getItemTypeByModel($no_model)
    {
        // Ambil data berdasarkan no_model yang dipilih
        $itemTypes = $this->outCelupModel->getItemTypeByModel($no_model);  // Gantilah dengan query sesuai kebutuhan

        // Return data dalam bentuk JSON
        return $this->response->setJSON($itemTypes);
    }
    public function getKodeWarna()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));

        // $coba = 'Y24046';
        // $coba2 = 'ACRYLIC TEXLAN 1/36';

        // log_message('debug', "$coba Fetching kode warna for no_model: $no_model, item_type: $item_type");
        $kodeWarna = $this->outCelupModel->getKodeWarnaByModelAndItemType($noModel, $itemType);

        return $this->response->setJSON($kodeWarna);
    }
    public function getWarnaDanLot()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));
        $kodeWarna = $this->request->getGet('kodeWarna');

        // log_message('debug', "Fetching warna & lot for no_model: $no_model, item_type: $item_type, kode_warna: $kode_warna");

        $warna = $this->outCelupModel->getWarnaByKodeWarna($noModel, $itemType, $kodeWarna);
        $lotList = $this->outCelupModel->getLotByKodeWarna($noModel, $itemType, $kodeWarna);

        return $this->response->setJSON([
            'warna' => $warna ?? '',
            'lot' => $lotList
        ]);
    }
    public function getKgsDanCones()
    {
        $no_model = $this->request->getGet('noModel');
        $item_type = $this->request->getGet('itemType');
        $kode_warna = $this->request->getGet('kodeWarna');
        $lot_kirim = $this->request->getGet('lotKirim');
        $no_karung = $this->request->getGet('noKarung');
        try {
            $data = $this->outCelupModel->getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung);

            if ($data) {
                return $this->response->setJSON([
                    'success' => true,
                    'kgs_kirim' => $data['kgs_kirim'],
                    'cones_kirim' => $data['cones_kirim'],
                    'id_out_celup' => $data['id_out_celup']
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
        } catch (\Exception $e) {
            // log_message('error', 'Error getKgsDanCones: ' . $e->getMessage()); // Log error
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server']);
        }
    }
    public function prosesPemasukanManual()
    {
        $action = $this->request->getPost('action'); // Ambil nilai tombol yang diklik

        if ($action === 'komplain') {
            $idOutCelup = $this->request->getPost('id_out_celup');
            $alasan = $this->request->getPost('alasan');

            $idCelup = $this->outCelupModel->getIdCelup($idOutCelup);

            if (!$idCelup) {
                session()->setFlashdata('error', 'Data tidak ditemukan.');
                return redirect()->back();
            }
            $update = $this->scheduleCelupModel
                ->where('id_celup', $idCelup)
                ->set([
                    'last_status' => 'complain',
                    'ket_daily_cek' => $alasan
                ])
                ->update();

            if ($update) {
                session()->setFlashdata('success', 'Komplain berhasil dikirim.');
            } else {
                session()->setFlashdata('error', 'Komplain gagal dikirim.');
            }
            return redirect()->to($this->role . '/pemasukan');
        } elseif ($action === 'simpan') {

            $idOutCelup = $this->request->getPost('id_out_celup');
            $noModels = $this->request->getPost('no_model');
            $itemTypes = $this->request->getPost('item_type');
            $kodeWarnas = $this->request->getPost('kode_warna');
            $warnas = $this->request->getPost('warna');
            $kgsMasuks = $this->request->getPost('kgs_kirim');
            $cnsMasuks = $this->request->getPost('cns_kirim');
            $tglMasuks = $this->request->getPost('tgl_kirim');
            $namaClusters = $this->request->getPost('cluster');
            $lotKirim = $this->request->getPost('lot_kirim');

            // Pastikan nama_cluster ada di dalam tabel cluster
            $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

            if ($clusterExists === 0) {
                session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
                return redirect()->to($this->role . '/pemasukan');
            }

            $dataPemasukan = [];

            $dataPemasukan[] = [
                'id_out_celup' => $idOutCelup,
                'no_model' => $noModels,
                'item_type' => $itemTypes,
                'kode_warna' => $kodeWarnas,
                'warna' => $warnas,
                'kgs_masuk' => $kgsMasuks,
                'cns_masuk' => $cnsMasuks,
                'tgl_masuk' => $tglMasuks,
                'nama_cluster' => $namaClusters,
                'admin' => session()->get('username')
            ];
            // dd($dataPemasukan);
            // Debugging: cek apakah data tidak kosong sebelum insert
            if (empty($dataPemasukan)) {
                session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
                return redirect()->to($this->role . '/pemasukan');
            }

            $cekDuplikat = $this->pemasukanModel
                ->whereIn('id_out_celup', array_column($dataPemasukan, 'id_out_celup'))
                ->countAllResults();

            if ($cekDuplikat == 0) {
                //insert tabel pemasukan
                if ($this->pemasukanModel->insertBatch($dataPemasukan)) {

                    $dataStock = [];
                    $dataStock[] = [
                        'no_model' => $noModels,
                        'item_type' => $itemTypes,
                        'kode_warna' => $kodeWarnas,
                        'warna' => $warnas,
                        'kgs_in_out' => $kgsMasuks,
                        'cns_in_out' => $cnsMasuks,
                        'krg_in_out' => 1, // Asumsikan setiap pemasukan hanya 1 kali
                        'lot_stock' => $lotKirim,
                        'nama_cluster' => $namaClusters,
                        'admin' => session()->get('username')
                    ];

                    foreach ($dataStock as $stock) {
                        $existingStock = $this->stockModel
                            ->where('no_model', $stock['no_model'])
                            ->where('item_type', $stock['item_type'])
                            ->where('kode_warna', $stock['kode_warna'])
                            ->where('lot_stock', $stock['lot_stock'])
                            ->first(); // Ambil satu record yang sesuai

                        if ($existingStock) {
                            // Jika sudah ada, update jumlahnya
                            $this->stockModel->update($existingStock['id_stock'], [
                                'kgs_in_out' => $existingStock['kgs_in_out'] + $stock['kgs_in_out'],
                                'cns_in_out' => $existingStock['cns_in_out'] + $stock['cns_in_out'],
                                'krg_in_out' => $existingStock['krg_in_out'] + 1
                            ]);
                        } else {
                            // Jika belum ada, insert data baru
                            $this->stockModel->insert($stock);
                        }
                    }
                }
                session()->setFlashdata('success', 'Data berhasil dimasukkan.');
            } else {
                session()->setFlashdata('error', 'Gagal, Data pemasukan sudah ada.');
            }
            return redirect()->to($this->role . '/pemasukan');
        }
    }
    public function pengeluaranJalur()
    {
        $id = $this->request->getPost('barcode');
        $cluster = $this->clusterModel->getDataCluster();

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataOutJalur') ?? [];

        if (!empty($id)) {
            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
                    return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $inGudang = $this->pemasukanModel->getDataForOut($id);

            if (empty($inGudang)) {
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
                return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
            } elseif (!empty($inGudang)) {
                // Tambahkan data baru ke dalam array
                $existingData = array_merge($existingData, $inGudang);
            }

            // Simpan kembali ke session
            session()->set('dataOutJalur', $existingData);

            // Redirect agar form tidak resubmit saat refresh
            return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataOutJalur' => $existingData, // Tampilkan data dari session
            'cluster' => $cluster,
            'error' => session()->getFlashdata('error'),
        ];

        return view($this->role . '/warehouse/form-pengeluaran', $data);
    }

    public function search()
    {
        $noModel = $this->request->getPost('noModel');
        $warna = $this->request->getPost('warna');

        $results = $this->stockModel->searchStock($noModel, $warna);
        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);
        // var_dump($resultsArray);
        // Hitung total kgs_in_out untuk seluruh data
        $totalKgsByCluster = []; // Array untuk menyimpan total Kgs per cluster
        $capacityByCluster = []; // Array untuk menyimpan kapasitas per cluster

        foreach ($resultsArray as $item) {
            $namaCluster = $item['nama_cluster'];
            $kgs = (float)$item['Kgs'];
            $kgsStockAwal = (float)$item['KgsStockAwal'];
            $kapasitas = (float)$item['kapasitas'];

            // Inisialisasi total Kgs dan kapasitas untuk cluster jika belum ada
            if (!isset($totalKgsByCluster[$namaCluster])) {
                $totalKgsByCluster[$namaCluster] = 0;
                $totalKgsStockAwalByCluster[$namaCluster] = 0;
                $capacityByCluster[$namaCluster] = $kapasitas;
            }

            // Tambahkan Kgs ke total untuk nama_cluster tersebut
            $totalKgsByCluster[$namaCluster] += $kgs;
            $totalKgsStockAwalByCluster[$namaCluster] += $kgsStockAwal;
        }

        // Iterasi melalui data dan hitung sisa kapasitas
        foreach ($resultsArray as &$item) { // Gunakan reference '&' agar perubahan berlaku pada item
            $namaCluster = $item['nama_cluster'];
            $totalKgsInCluster = $totalKgsByCluster[$namaCluster];
            $totalKgsStockAwalInCluster = $totalKgsStockAwalByCluster[$namaCluster];
            $kapasitasCluster = $capacityByCluster[$namaCluster];

            $sisa_space = $kapasitasCluster - $totalKgsInCluster - $totalKgsStockAwalInCluster;
            $item['sisa_space'] = max(0, $sisa_space); // Pastikan sisa_space tidak negatif
        }
        // var_dump ($resultsArray);
        return $this->response->setJSON($resultsArray);
    }

    public function getSisaKapasitas()
    {
        $results = $this->stockModel->getKapasitas();

        $resultsArray = json_decode(json_encode($results), true);

        foreach ($resultsArray as &$item) {
            $sisaSpace = $item['kapasitas'] - $item['Kgs'] - $item['KgsStockAwal'];
            $item['sisa_space'] = $sisaSpace;
        }
        // var_dump($resultsArray);
        return $this->response->setJSON(
            [
                'success' => true,
                'data' => $resultsArray
            ]
        );
    }

    public function getClusterbyId()
    {
        $id = $this->request->getPost('id');
        $results = $this->clusterModel->getClusterById($id);
        $resultsArray = json_decode(json_encode($results), true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultsArray
        ]);
    }

    public function updateCluster()
    {
        if ($this->request->isAJAX()) {
            $idStock = $this->request->getPost('id_stock');
            $clusterOld = $this->request->getPost('cluster_old');
            $namaCluster = $this->request->getPost('nama_cluster');
            $kgs = $this->request->getPost('kgs');
            $cones = $this->request->getPost('cones');
            $karung = $this->request->getPost('krg');
            $lot = $this->request->getPost('lot');

            $idStock = $this->stockModel->where('id_stock', $idStock)->first();

            if (!$idStock) {
                return $this->response->setJSON(['success' => false, 'message' => 'Stock tidak ditemukan']);
            }


            $kgsInput = (int)$kgs;
            $cnsInput = (int)$cones;
            $krgInput = (int)$karung;

            $kgsInOut = !empty($idStock['kgs_in_out']) ? (int)$idStock['kgs_in_out'] : 0;
            $kgsStockAwal = !empty($idStock['kgs_stock_awal']) ? (int)$idStock['kgs_stock_awal'] : 0;

            $cnsInOut = !empty($idStock['cns_in_out']) ? (int)$idStock['cns_in_out'] : 0;
            $cnsStockAwal = !empty($idStock['cns_stock_awal']) ? (int)$idStock['cns_stock_awal'] : 0;

            $krgInOut = !empty($idStock['krg_in_out']) ? (int)$idStock['krg_in_out'] : 0;
            $krgStockAwal = !empty($idStock['krg_stock_awal']) ? (int)$idStock['krg_stock_awal'] : 0;

            // Gunakan stok yang tersedia
            $stokKgsTersedia = $kgsInOut > 0 ? $kgsInOut : $kgsStockAwal;
            $stokCnsTersedia = $cnsInOut > 0 ? $cnsInOut : $cnsStockAwal;
            $stokKrgTersedia = $krgInOut > 0 ? $krgInOut : $krgStockAwal;

            // Validasi: Pastikan stok cukup sebelum lanjut
            if ($stokKgsTersedia < $kgsInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah KGS melebihi stok yang tersedia']);
            }
            if ($stokCnsTersedia < $cnsInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah Cones melebihi stok yang tersedia']);
            }
            if ($stokKrgTersedia < $krgInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah Karung melebihi stok yang tersedia']);
            }

            // Perhitungan stok setelah pengurangan
            if (

                $kgsInOut > 0
            ) {
                $kgsInOut -= $kgsInput;
            } else {
                $kgsStockAwal -= $kgsInput;
            }

            if (

                $cnsInOut > 0
            ) {
                $cnsInOut -= $cnsInput;
            } else {
                $cnsStockAwal -= $cnsInput;
            }

            if (

                $krgInOut > 0
            ) {
                $krgInOut -= $krgInput;
            } else {
                $krgStockAwal -= $krgInput;
            }

            // Hindari nilai negatif
            $kgsInOut = max(0, $kgsInOut);
            $kgsStockAwal = max(0, $kgsStockAwal);

            $cnsInOut = max(0, $cnsInOut);
            $cnsStockAwal = max(0, $cnsStockAwal);

            $krgInOut = max(0, $krgInOut);
            $krgStockAwal = max(0, $krgStockAwal);

            // Menentukan lot yang digunakan
            $lot = !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : $idStock['lot_awal'];
            // log_message('debug', 'Lot yang digunakan: ' . $lot);

            $noModel = $idStock['no_model'];
            $itemType = $idStock['item_type'];
            $kodeWarna = $idStock['kode_warna'];
            $warna = $idStock['warna'];

            if ($idStock['kgs_in_out'] < $kgs || $idStock['cns_in_out'] < $cones || $idStock['krg_in_out'] < $karung) {
                $kgsInOut = $idStock['kgs_stock_awal'] - $kgs;
                $cnsInOut = $idStock['cns_stock_awal'] - $cones;
                $krgInOut = $idStock['krg_stock_awal'] - $karung;
            } else {
                $kgsInOut = $idStock['kgs_in_out'] - $kgs;
                $cnsInOut = $idStock['cns_in_out'] - $cones;
                $krgInOut = $idStock['krg_in_out'] - $karung;
            }
            // $kgsInOut = $idStock['kgs_in_out']-$kgs;
            // $cnsInOut = $idStock['cns_in_out']-$cones;
            // $krgInOut = $idStock['krg_in_out']-$karung;
            // $lotStock = "";
            // var_dump($idStock, $clusterOld, $namaCluster, $kgs, $cones, $karung, $lot);

            $dataStock = [
                'no_model' => $noModel,
                'item_type' => $itemType,
                'kode_warna' => $kodeWarna,
                'warna' => $warna,
                'kgs_stock_awal' => empty($idStock['lot_stock']) ? $kgs : 0,
                'kgs_in_out' => !empty($idStock['lot_stock']) ? $kgs : 0,
                'cns_stock_awal' => empty($idStock['lot_stock']) ? $cones : 0,
                'cns_in_out' => !empty($idStock['lot_stock']) ? $cones : 0,
                'krg_stock_awal' => empty($idStock['lot_stock']) ? $karung : 0,
                'krg_in_out' => !empty($idStock['lot_stock']) ? number_format($karung, 0, '.', '') : 0,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '',  // Pastikan lot_stock hanya diisi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '',  // Gunakan lot_awal jika lot_stock kosong
                'nama_cluster' => $namaCluster,
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->stockModel->insert($dataStock);

            // Ambil ID dari stok baru setelah insert
            $idStockNew = $this->stockModel->getInsertID();

            if (!$idStockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data stock baru']);
            }

            // Simpan riwayat pemindahan palet
            $dataHistory = [
                'id_stock_old' => $idStock['id_stock'],
                'id_stock_new' => $idStockNew,
                'cluster_old' => $clusterOld,
                'cluster_new' => $namaCluster,
                'kgs' => $kgs,
                'cns' => $cones,
                'krg' => $karung, // Tambahkan jumlah karung ke log history
                'lot' => $lot,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $stockNew = $this->historyPindahPalet->insert($dataHistory);

            if (!$stockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan riwayat pemindahan palet']);
            }

            // Update ke database
            $updateStock = $this->stockModel->update($idStock['id_stock'], [
                'kgs_in_out' => $kgsInOut,
                'kgs_stock_awal' => $kgsStockAwal,
                'cns_in_out' => $cnsInOut,
                'cns_stock_awal' => $cnsStockAwal,
                'krg_in_out' => $krgInOut,
                'krg_stock_awal' => $krgStockAwal,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '', // Hanya isi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '', // Hanya isi jika lot_stock kosong
            ]);


            if ($stockNew && $updateStock) {
                return $this->response->setJSON(['success' => true, 'message' => 'Cluster berhasil diperbarui']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui Cluster']);
            }
        } else {
            return redirect()->to(base_url($this->role . '/warehouse'));
        }
    }

    public function getNoModel()
    {
        $results = $this->stockModel->getNoModel();

        $resultsArray = json_decode(json_encode($results), true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultsArray
        ]);
    }

    public function updateNoModel()
    {
        if ($this->request->isAJAX()) {
            $idStock = $this->request->getPost('id_stock');
            $clusterOld = $this->request->getPost('namaCluster');
            $noModel = $this->request->getPost('no_model');
            $kgs = (int) $this->request->getPost('kgs');
            $cones = (int) $this->request->getPost('cones');
            $karung = (int) $this->request->getPost('krg');

            // log_message('debug', 'Data No Model: ' . print_r($noModel, true));
            // log_message('debug', 'Data clusterOld: ' . print_r($clusterOld, true));

            // Ambil data stok lama
            $idStock = $this->stockModel->where('id_stock', $idStock)->first();
            if (!$idStock) {
                return $this->response->setJSON(['success' => false, 'message' => 'Stock tidak ditemukan']);
            }

            // Ambil lot_awal atau lot_stock
            $lot = !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : $idStock['lot_awal'];

            // Cari data order berdasarkan no_model
            $findData = $this->masterOrderModel->where('no_model', $noModel)->first();
            if (!$findData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan']);
            }

            // log_message('debug', 'Data Order: ' . print_r($findData, true));

            // Cari material berdasarkan order
            $material = $this->materialModel->getMaterialByIdOrderItemTypeKodeWarna(
                $findData['id_order'],
                $idStock['item_type'],
                $idStock['kode_warna']
            );

            if (empty($material)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Material tidak ditemukan']);
            }

            // log_message('debug', 'Data Material: ' . print_r($material, true));

            $noModel = $findData['no_model'];
            $itemType = $material[0]['item_type'];
            $kodeWarna = $material[0]['kode_warna'];
            $warna = $material[0]['color'];

            // $itemType = $material['item_type'];
            // $kodeWarna = $material['kode_warna'];
            // $warna = $material['color'];

            if ($idStock['kgs_in_out'] < $kgs || $idStock['cns_in_out'] < $cones || $idStock['krg_in_out'] < $karung) {
                $kgsInOut = $idStock['kgs_stock_awal'] - $kgs;
                $cnsInOut = $idStock['cns_stock_awal'] - $cones;
                $krgInOut = $idStock['krg_stock_awal'] - $karung;
            } else {
                $kgsInOut = $idStock['kgs_in_out'] - $kgs;
                $cnsInOut = $idStock['cns_in_out'] - $cones;
                $krgInOut = $idStock['krg_in_out'] - $karung;
            }

            $dataStock = [
                'no_model' => $noModel,
                'item_type' => $itemType,
                'kode_warna' => $kodeWarna,
                'warna' => $warna,
                'kgs_stock_awal' => $kgs,
                'kgs_in_out' => 0,
                'cns_stock_awal' => $cones,
                'cns_in_out' => 0,
                'krg_stock_awal' => $karung,
                'krg_in_out' => 0,
                'lot_awal' => $lot,
                'nama_cluster' => $clusterOld,
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert data stock baru
            if (!$this->stockModel->insert($dataStock)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan stock baru']);
            }

            // log_message('debug', 'Data Stock: ' . print_r($dataStock, true));

            // Ambil ID stock baru
            $idStockNew = $this->stockModel->getInsertID();
            if (!$idStockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendapatkan ID stock baru']);
            }

            // Simpan riwayat pemindahan order
            $dataHistory = [
                'id_stock_old' => $idStock['id_stock'],
                'id_stock_new' => $idStockNew,
                'nama_cluster' => $clusterOld,
                'kgs' => $kgs,
                'cns' => $cones,
                'krg' => $karung,
                'lot' => $lot,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->historyPindahOrder->insert($dataHistory)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan riwayat pemindahan order']);
            }

            // log_message('debug', 'Data Cluster: ' . print_r($dataHistory, true));

            // Validasi stok cukup sebelum dikurangkan
            $kgsInOut = max(0, $idStock['kgs_in_out'] - $kgs);
            $cnsInOut = max(0, $idStock['cns_in_out'] - $cones);
            $krgInOut = max(0, $idStock['krg_in_out'] - $karung);
            $kgsStockAwal = max(0, $idStock['kgs_stock_awal'] - $kgs);
            $cnsStockAwal = max(0, $idStock['cns_stock_awal'] - $cones);
            $krgStockAwal = max(0, $idStock['krg_stock_awal'] - $karung);

            // Update stock lama dengan mengurangi stok yang dipindahkan
            $updateStockData = [
                'kgs_stock_awal' => $kgsStockAwal,
                'cns_stock_awal' => $cnsStockAwal,
                'krg_stock_awal' => $krgStockAwal,
                'kgs_in_out' => $kgsInOut,
                'cns_in_out' => $cnsInOut,
                'krg_in_out' => $krgInOut,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '', // Hanya isi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '', // Hanya isi jika lot_stock kosong
            ];

            if (!$this->stockModel->update($idStock['id_stock'], $updateStockData)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui stock lama']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Cluster berhasil diperbarui']);
        } else {
            return redirect()->to(base_url($this->role . '/warehouse'));
        }
    }

    public function prosesPengeluaranJalur()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }
        //update tabel pemasukan
        if (!empty($checkedIds)) {
            $whereIds = array_map(fn($index) => $idOutCelup[$index] ?? null, $checkedIds);
            $whereIds = array_filter($whereIds); // Hapus nilai NULL jika ada

            if (!empty($whereIds)) {
                $update = $this->pemasukanModel
                    ->whereIn('id_out_celup', $whereIds)
                    ->set(['out_jalur' => '1'])
                    ->update();

                if ($update) {
                    session()->setFlashdata('success', 'Data berhasil dikeluarkan');

                    // Hapus data yang sudah diproses dari session dataOut
                    $dataOut = session()->get('dataOutJalur');

                    if (!empty($dataOut) && is_array($dataOut)) {
                        $filteredDataOut = array_filter($dataOut, function ($item) use ($whereIds) {
                            return isset($item['id_out_celup']) && !in_array($item['id_out_celup'], $whereIds);
                        });

                        // Perbarui session atau hapus jika kosong
                        if (!empty($filteredDataOut)) {
                            session()->set('dataOutJalur', array_values($filteredDataOut));
                        } else {
                            session()->remove('dataOutJalur');
                        }
                    }
                } else {
                    session()->setFlashdata('error', 'Gagal mengupdate data.');
                }
            }
        }

        return redirect()->to($this->role . '/pengeluaran_jalur');
    }
    public function resetPengeluaranJalur()
    {
        session()->remove('dataOutJalur');
        return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
    }
    public function hapusListPengeluaran()
    {
        $id = $this->request->getPost('id');

        // Ambil data dari session
        $existingData = session()->get('dataOutJalur') ?? [];

        // Cek apakah data dengan ID yang dikirim ada di session
        foreach ($existingData as $key => $item) {
            if ($item['id_out_celup'] == $id) {
                // Hapus data tersebut dari array
                unset($existingData[$key]);
                // Update session dengan data yang sudah dihapus
                session()->set('dataOutJalur', array_values($existingData));
                // Debug response
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function getItemTypeForOut($no_model)
    {
        // Ambil data berdasarkan no_model yang dipilih
        $itemTypes = $this->pemasukanModel->getItemTypeByModel($no_model);  // Gantilah dengan query sesuai kebutuhan

        // Return data dalam bentuk JSON
        return $this->response->setJSON($itemTypes);
    }
    public function getKodeWarnaForOut($no_model, $item_type)
    {
        // log_message('debug', "Fetching kode warna for no_model: $no_model, item_type: $item_type");

        $kodeWarna = $this->pemasukanModel->getKodeWarnaByItemType($no_model, $item_type);

        return $this->response->setJSON($kodeWarna);
    }
    public function getWarnaDanLotForOut($no_model, $item_type, $kode_warna)
    {
        // log_message('debug', "Fetching warna & lot for no_model: $no_model, item_type: $item_type, kode_warna: $kode_warna");

        $warna = $this->pemasukanModel->getWarnaByKodeWarna($no_model, $item_type, $kode_warna);
        $lotList = $this->pemasukanModel->getLotByKodeWarna($no_model, $item_type, $kode_warna);

        return $this->response->setJSON([
            'warna' => $warna ?? '',
            'lot' => $lotList
        ]);
    }
    public function getKgsCnsClusterForOut($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
        try {
            $data = $this->pemasukanModel->getKgsConesClusterForOut($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung);

            if ($data) {
                return $this->response->setJSON([
                    'success' => true,
                    'kgs_kirim' => $data['kgs_masuk'],
                    'cones_kirim' => $data['cns_masuk'],
                    'id_out_celup' => $data['id_out_celup'],
                    'nama_cluster' => $data['nama_cluster']
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
        } catch (\Exception $e) {
            // log_message('error', 'Error getKgsDanCones: ' . $e->getMessage()); // Log error
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server']);
        }
    }
    public function prosesPengeluaranJalurManual()
    {
        $idOutCelup = $this->request->getPost('id_out_celup');

        // Pastikan data tidak kosong
        if (empty($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        $update = $this->pemasukanModel
            ->where('id_out_celup', $idOutCelup)
            ->set('out_jalur', '1')
            ->update();

        if ($update) {
            session()->setFlashdata('success', 'Data berhasil dikeluarkan');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data.');
        }

        return redirect()->to($this->role . '/pengeluaran_jalur');
    }
    public function prosesComplain()
    {
        $idOutCelup = $this->request->getPost('id_out_celup');
        $alasan = $this->request->getPost('alasan');

        $idCelup = $this->outCelupModel->getIdCelup($idOutCelup);

        if (!$idCelup) {
            session()->setFlashdata('error', 'Data tidak ditemukan.');
            return redirect()->back();
        }

        $update = $this->scheduleCelupModel
            ->where('id_celup', $idCelup)
            ->set([
                'last_status' => 'complain',
                'ket_daily_cek' => $alasan
            ])
            ->update();

        if ($update) {
            // Ambil session dataOut
            $existingData = session()->get('dataOut') ?? [];

            // Hapus data berdasarkan idOutCelup
            $filteredData = array_filter($existingData, function ($item) use ($idOutCelup) {
                return $item['id_out_celup'] != $idOutCelup;
            });

            // Simpan kembali dataOut ke session tanpa data yang dihapus
            session()->set('dataOut', array_values($filteredData));

            session()->setFlashdata('success', 'Data berhasil dikomplain');
        } else {
            session()->setFlashdata('error', 'Gagal mengkomplain data.');
        }
        return redirect()->back();
    }
}
