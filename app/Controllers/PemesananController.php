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

class PemesananController extends BaseController
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
    protected $pemesananModel;

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
        $dataPemesanan = $this->pemesananModel->getDataPemesanan($area, $jenis, $tglPakai);

        if (!is_array($dataPemesanan)) {
            $dataPemesanan = []; // Pastikan selalu array
        }

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
        // session()->remove('dataPengiriman');
        $area = $this->request->getPost('area');
        $tglPakai = $this->request->getPost('tgl_pakai');
        $noModel = $this->request->getPost('no_model');
        $itemType = $this->request->getPost('item_type');
        $kodeWarna = $this->request->getPost('kode_warna');
        $warna = $this->request->getPost('warna');
        $kgsPesan = $this->request->getPost('kgs_pesan');
        $cnsPesan = $this->request->getPost('cns_pesan');

        // Simpan data form ke session
        $formData = [
            'area' => $area,
            'tgl_pakai' => $tglPakai,
            'no_model' => $noModel,
            'item_type' => $itemType,
            'kode_warna' => $kodeWarna,
            'warna' => $warna,
            'kgs_pesan' => $kgsPesan,
            'cns_pesan' => $cnsPesan,
        ];

        // Simpan ke session
        session()->set('pengirimanForm', $formData);
        // var_dump(session()->get('pengirimanForm'));
        // exit;

        $id = $this->request->getPost('barcode');
        $cluster = $this->clusterModel->getDataCluster();

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataPengiriman') ?? [];

        if (!empty($id)) {
            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->set('pengirimanForm', $formData);
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
                    // return redirect()->to(base_url($this->role . '/pengiriman_area'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $outJalur = $this->pemasukanModel->getDataForPengiriman($id);

            if (empty($outJalur)) {
                session()->set('pengirimanForm', $formData);
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
                // return redirect()->to(base_url($this->role . '/pengiriman_area'));
            } elseif (!empty($outJalur)) {
                // Tambahkan data baru ke dalam array
                $existingData = array_merge($existingData, $outJalur);
            }

            // Simpan kembali ke session
            session()->set('dataPengiriman', $existingData);
            session()->set('pengirimanForm', $formData);

            // Redirect agar form tidak resubmit saat refresh
            return redirect()->to(base_url($this->role . '/pengiriman_area'));
        }

        // Ambil kembali data form dari session
        $formData = session()->get('pengirimanForm') ?? [];

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataOut' => $existingData, // Tampilkan data dari session
            'cluster' => $cluster,
            'error' => session()->getFlashdata('error'),
            'area' => $formData['area'] ?? '',
            'tgl_pakai' => $formData['tgl_pakai'] ?? '',
            'no_model' => $formData['no_model'] ?? '',
            'item_type' => $formData['item_type'] ?? '',
            'kode_warna' => $formData['kode_warna'] ?? '',
            'warna' => $formData['warna'] ?? '',
            'kgs_pesan' => $formData['kgs_pesan'] ?? '',
            'cns_pesan' => $formData['cns_pesan'] ?? '',
        ];

        return view($this->role . '/warehouse/form-pengiriman', $data);
    }
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
    public function prosesPengirimanArea($area, $tglPakai)
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pengiriman_area/' . $area . '/' . $tglPakai);
        }

        $idOutCelup = $this->request->getPost('id_out_celup');
        $itemTypes = $this->request->getPost('item_type');
        $kodeWarnas = $this->request->getPost('kode_warna');
        $tglOuts = $this->request->getPost('tgl_kirim');
        $kgsOuts = $this->request->getPost('kgs_kirim');
        $cnsOuts = $this->request->getPost('cns_kirim');
        $namaClusters = $this->request->getPost('nama_cluster');
        $lotKirims = $this->request->getPost('lot_kirim');

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengiriman_area/' . $area . '/' . $tglPakai);
        }

        // Pastikan nama_cluster ada di dalam tabel cluster
        $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

        if ($clusterExists === 0) {
            session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
            return redirect()->to($this->role . '/pengiriman_area/' . $area . '/' . $tglPakai);
        }

        $dataKirim = [];

        foreach ($checkedIds as $key => $idOut) {
            $dataKirim[] = [
                'id_out_celup' => $idOutCelup[$key] ?? null,
                'area_out' => $area ?? null,
                'tgl_out' => $tglOuts[$key] ?? null,
                'kgs_out' => $kgsOuts[$key] ?? null,
                'cns_out' => $cnsOuts[$key] ?? null,
                'krg_out' => 1, // Asumsikan setiap pemasukan hanya 1 kali
                'lot_out' => $lotKirims[$key] ?? null,
                'nama_cluster' => $namaClusters,
                'admin' => session()->get('username')
            ];
        }

        // Debugging: cek apakah data tidak kosong sebelum insert
        if (empty($dataKirim)) {
            session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
            return redirect()->to($this->role . '/pengiriman_area/' . $area . '/' . $tglPakai);
        }

        // Ambil data session
        $checked = session()->get('dataPengiriman');

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

        $cekDuplikat = $this->pengeluranModel
            ->whereIn('id_out_celup', array_column($dataKirim, 'id_out_celup'))
            ->countAllResults();

        if ($cekDuplikat == 0) {
            //insert tabel pemasukan
            if ($this->pengeluaranModel->insertBatch($dataKirim)) {
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
        return redirect()->to($this->role . '/pengiriman_area/' . $area . '/' . $tglPakai);
    }

    public function selectClusterWarehouse($id)
    {
        $getPemesanan = $this->pemesananModel->getDataPemesananbyId($id);
        $cluster = $this->stockModel->getDataCluster($getPemesanan['no_model'], $getPemesanan['item_type'], $getPemesanan['kode_warna'], $getPemesanan['color']);
        // dd ($getPemesanan, $cluster);

        // if (!$cluster) {
        //     session()->setFlashdata('error', 'Cluster tidak ditemukan');
        //     // redirect back
        //     return redirect()->back();
        // } 
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'cluster' => $cluster,
            'noModel' => $getPemesanan['no_model'],
            'area' => $getPemesanan['admin'],
            // 'namaCluster' => $getPemesanan['nama_cluster'],
            // 'id_out_celup' => $getPemesanan['id_out_celup'],
            'id' => $id,
        ];

        // dd ($data);
        return view($this->role . '/pemesanan/select-cluster', $data);
    }

    public function getDataByIdStok($id)
    {
        $data = $this->stockModel->getDataByIdStok($id);
        // Debugging
        // var_dump($data);
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
}
