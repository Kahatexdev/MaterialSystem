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
    public function prosesPemasukan()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to('/pemasukan');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');
        $noModels = $this->request->getPost('no_model');
        $itemTypes = $this->request->getPost('item_type');
        $kodeWarnas = $this->request->getPost('kode_warna');
        $kgsMasuks = $this->request->getPost('kgs_masuk');
        $cnsMasuks = $this->request->getPost('cns_masuk');
        $tglMasuks = $this->request->getPost('tgl_masuk');
        $namaClusters = $this->request->getPost('cluster');
        var_dump($namaClusters);

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to('/pemasukan');
        }

        // Pastikan nama_cluster ada di dalam tabel cluster
        $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

        if ($clusterExists === 0) {
            session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
            return redirect()->to('/pemasukan');
        }

        $dataPemasukan = [];

        foreach ($idOutCelup as $key => $idOut) {
            $dataPemasukan[] = [
                'id_out_celup' => $idOutCelup[$key] ?? null,
                'no_model' => $noModels[$key] ?? null,
                'item_type' => $itemTypes[$key] ?? null,
                'kode_warna' => $kodeWarnas[$key] ?? null,
                'kgs_masuk' => $kgsMasuks[$key] ?? null,
                'cns_masuk' => $cnsMasuks[$key] ?? null,
                'tgl_masuk' => $tglMasuks[$key] ?? null,
                'nama_cluster' => $namaClusters ?? null,
            ];
        }

        // Debugging: cek apakah data tidak kosong sebelum insert
        if (empty($dataPemasukan)) {
            session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
            return redirect()->to('/pemasukan');
        }

        // Gunakan insertBatch untuk menyimpan banyak data
        $this->pemasukanModel->insertBatch($dataPemasukan);
        session()->setFlashdata('success', 'Data berhasil dimasukkan.');

        return redirect()->to('/pemasukan');
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
    public function pengeluaran()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/form-pengeluaran', $data);
    }
}
