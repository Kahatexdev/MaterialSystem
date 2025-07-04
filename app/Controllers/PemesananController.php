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
        // dd ($dataPemesanan);
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

    public function selectClusterWarehouse($id)
    {
        $KgsPesan = $this->request->getGet('KgsPesan');
        $CnsPesan = $this->request->getGet('CnsPesan');
        $getPemesanan = $this->totalPemesananModel->getDataPemesananbyId($id);
        // dd($getPemesanan);
        $cluster = $this->stockModel->getDataCluster($getPemesanan['no_model'], $getPemesanan['item_type'], $getPemesanan['kode_warna'], $getPemesanan['color']);
        // dd($getPemesanan, $cluster);

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
            // 'id_total_pemesanan' => $getPemesanan['id_total_pemesanan'],
            // 'namaCluster' => $getPemesanan['nama_cluster'],
            // 'id_out_celup' => $getPemesanan['id_out_celup'],
            'id' => $id,
            'KgsPesan' => $KgsPesan,
            'CnsPesan' => $CnsPesan,
        ];

        // dd ($data);
        return view($this->role . '/pemesanan/select-cluster', $data);
    }

    public function getDataByIdStok($id)
    {
        // $data = $this->stockModel->getDataByIdStok($id);
        $stock = $this->pemasukanModel->getDataByIdStok($id);
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
            'username' => session()->get('role'),
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

        $success = $this->pemesananModel->additionalTimeReject($area, $tglPakai, $jenis);

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

        $tglPakai = $this->pemesananModel->getTglPemesananByJenis($jenis);
        // dd($tglPakai);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis,
            'tglPakai' => $tglPakai,
        ];
        return view($this->role . '/pemesanan/persiapanBarangPertgl', $data);
    }
    public function pemesananArea()
    {
        function fetchApiData($url)
        {
            try {
                $response = file_get_contents($url);
                if ($response === false) {
                    throw new \Exception("Error fetching data from $url");
                }
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from $url");
                }
                return $data;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return null;
            }
        }

        $tglPakai = $this->request->getGet('tgl_pakai') ?? DATE('Y-m-d');
        $noModel = $this->request->getGet('model');

        $dataList = $this->pemesananModel->getDataPemesananArea($tglPakai, $noModel);

        foreach ($dataList as $key => $order) {
            $dataList[$key]['ttl_kebutuhan_bb'] = 0;
            $area = $order['admin'];
            if (isset($order['no_model'], $order['item_type'], $order['kode_warna'])) {
                $styleList = $this->materialModel->getStyleSizeByBb($order['no_model'], $order['item_type'], $order['kode_warna']);

                if ($styleList) {
                    $totalRequirement = 0;
                    foreach ($styleList as $style) {
                        if (isset($style['no_model'], $style['style_size'], $style['gw'], $style['composition'], $style['loss'])) {
                            $orderApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model='
                                . $order['no_model'] . '&style_size=' . urlencode($style['style_size']) . '&area=' . urlencode($area);

                            // TAMBAHKAN INI UNTUK NAMPIL DI CONSOLE BROWSER
                            echo "<script>console.log('API URL: " . htmlspecialchars($orderApiUrl) . "');</script>";

                            $orderQty = fetchApiData($orderApiUrl);
                            if (isset($orderQty['qty'])) {
                                $requirement = $orderQty['qty'] * $style['gw'] * ($style['composition'] / 100) * (1 + ($style['loss'] / 100)) / 1000;
                                $totalRequirement += $requirement;
                                $dataList[$key]['qty'] = $orderQty['qty'];
                            }
                        }
                    }
                    $dataList[$key]['ttl_kebutuhan_bb'] = $totalRequirement;
                }

                $data = [
                    'area' => $area,
                    'no_model' => $order['no_model'],
                    'item_type' => $order['item_type'],
                    'kode_warna' => $order['kode_warna'],
                ];

                $pengiriman = $this->pengeluaranModel->getTotalPengiriman($data);
                $dataList[$key]['ttl_pengiriman'] = $pengiriman['kgs_out'] ?? 0;

                // Hitung sisa jatah
                $dataList[$key]['sisa_jatah'] = $dataList[$key]['ttl_kebutuhan_bb'] - $dataList[$key]['ttl_pengiriman'];
            }
            // TAMPILKAN HASILNYA DI SINI
            // dd($dataList[$key]['ttl_kebutuhan_bb']);
        }

        $data = [
            'active' => $this->active,
            'title' => 'Pemesanan',
            'role' => $this->role,
            'dataList' => $dataList,
        ];
        return view($this->role . '/pemesanan/index', $data);
    }
    public function getUpdateListPemesanan()
    {
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

        $dataList = $this->pemesananModel->getListPemesananByUpdate($data);

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
                $urlQty = 'http://172.23.44.14/CapacityApps/public/api/getQtyOrder?no_model=' . $pemesanan['no_model']
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

                if ($qty > 0) {
                    $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
                    $pemesanan['ttl_keb'] = $ttlKeb;
                }
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
                'ttl_jl_mc'          => $pemesanan['ttl_jl_mc'],
                'ttl_kg'             => number_format($pemesanan['ttl_kg'], 2),
                'po_tambahan'        => $pemesanan['po_tambahan'],
                'ttl_keb'            => number_format($pemesanan['ttl_keb'], 2),
                'kg_out'             => number_format($pemesanan['kgs_out'], 2),
                'lot_out'            => $pemesanan['lot_out'],
                // field retur kosong
                'tgl_retur'          => null,
                'kgs_retur'          => null,
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

                if ($qty > 0) {
                    $kebutuhan = (($qty * $data['gw'] * ($data['composition'] / 100)) * (1 + ($data['loss'] / 100)) / 1000) + $kgPoTambahan;
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
                'ttl_kg'             => null,
                'po_tambahan'        => null,
                'ttl_keb'            => number_format($retur['ttl_keb'], 2),
                'kg_out'             => null,
                'lot_out'            => null,
                'tgl_retur'          => $retur['tgl_retur'],
                'kgs_retur'          => number_format($retur['kgs_retur'], 2),
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
        return view($this->role . '/pemesanan/sisaKebutuhanArea', $data);
    }

    public function pinjamOrder()
    {
        $area = $this->request->getGet('area');
        $jenis = $this->request->getGet('jenis');
        $tglPakai = $this->request->getGet('tglpakai');
        dd($area, $jenis, $tglPakai);
        // $dataPemesanan = $this->totalPemesananModel->getDataPemesanan($area, $jenis, $tglPakai);
        // $getData = $this->stockModel->pemakaianArea();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role
        ];
        return view($this->role . '/pemesanan/pinjam-order', $data);
    }
}
