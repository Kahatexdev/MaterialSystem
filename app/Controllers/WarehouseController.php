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
        $cluster = $this->clusterModel->getDataCluster();

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataOut') ?? [];

        if (!empty($id)) {
            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
                    return redirect()->to(base_url($this->role . '/pemasukan'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $outCelup = $this->outCelupModel->getDataOut($id);

            if (empty($outCelup)) {
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
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
    public function getItemTypeByModel($no_model)
    {
        // Ambil data berdasarkan no_model yang dipilih
        $itemTypes = $this->outCelupModel->getItemTypeByModel($no_model);  // Gantilah dengan query sesuai kebutuhan

        // Return data dalam bentuk JSON
        return $this->response->setJSON($itemTypes);
    }
    public function getKodeWarna($no_model, $item_type)
    {
        log_message('debug', "Fetching kode warna for no_model: $no_model, item_type: $item_type");

        $kodeWarna = $this->outCelupModel->getKodeWarnaByModelAndItemType($no_model, $item_type);

        return $this->response->setJSON($kodeWarna);
    }
    public function getWarnaDanLot($no_model, $item_type, $kode_warna)
    {
        log_message('debug', "Fetching warna & lot for no_model: $no_model, item_type: $item_type, kode_warna: $kode_warna");

        $warna = $this->outCelupModel->getWarnaByKodeWarna($no_model, $item_type, $kode_warna);
        $lotList = $this->outCelupModel->getLotByKodeWarna($no_model, $item_type, $kode_warna);

        return $this->response->setJSON([
            'warna' => $warna ?? '',
            'lot' => $lotList
        ]);
    }
    public function getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung)
    {
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
            log_message('error', 'Error getKgsDanCones: ' . $e->getMessage()); // Log error
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server']);
        }
    }
    public function prosesPemasukan()
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
        $kgsMasuks = $this->request->getPost('kgs_masuk');
        $cnsMasuks = $this->request->getPost('cns_masuk');
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

        //insert tabel pemasukan
        $this->pemasukanModel->insertBatch($dataPemasukan);

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
    public function prosesPemasukanManual()
    {
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
    public function prosesPengeluaranJalur()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');
        $noModels = $this->request->getPost('no_model');
        $itemTypes = $this->request->getPost('item_type');
        $kodeWarnas = $this->request->getPost('kode_warna');
        $warnas = $this->request->getPost('warna');
        $kgsKeluars = $this->request->getPost('kgs_keluar');
        $cnsKeluars = $this->request->getPost('cns_keluar');
        $tglMasuks = $this->request->getPost('tgl_keluar');
        $namaClusters = $this->request->getPost('nama_cluster');
        $lotKirim = $this->request->getPost('lot_kirim');

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        // Pastikan nama_cluster ada di dalam tabel cluster
        $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

        if ($clusterExists === 0) {
            session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
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
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        // Ambil data session
        $checked = session()->get('dataOutJalur');

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
                session()->set('dataOutJalur', array_values($filteredChecked));
            } else {
                // Hapus session jika tidak ada data tersisa
                session()->remove('dataOutJalur');
            }
        }

        //insert tabel pemasukan
        $this->pemasukanModel->insertBatch($dataPemasukan);

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
}
