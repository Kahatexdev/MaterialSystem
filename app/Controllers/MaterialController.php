<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\OpenPoModel;

class MaterialController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $materialModel;
    protected $masterOrderModel;
    protected $masterMaterialModel;
    protected $openPoModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->openPoModel = new OpenPoModel();

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
        return view($this->role . '/dashboard/index', $data);
    }

    public function tambahMaterial()
    {
        $data = $this->request->getPost();
        $idOrder = $this->request->getPost('id_order');

        $saveData = [
            'id_order' => $idOrder,
            'style_size' => $data['style_size'],
            'area' => $data['area'],
            'inisial' => $data['inisial'],
            'color' => $data['color'],
            'item_type' => $data['item_type'],
            'kode_warna' => $data['kode_warna'],
            'composition' => $data['composition'],
            'gw' => $data['gw'],
            'qty_pcs' => $data['qty_pcs'],
            'loss' => $data['loss'],
            'kgs' => $data['kgs'],
            'admin' => session()->get('id_user'),
        ];

        if ($this->materialModel->insert($saveData)) {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('success', 'Data berhasil disimpan.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('error', 'Data gagal disimpan.');
        }
    }

    public function updateArea($id_order)
    {
        $getArea = $this->request->getPost('edit_all_area');

        if ($this->materialModel->updateAreaPerNoModel($id_order, $getArea)) {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data berhasil diupdate.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('error', 'Data gagal diupdate.');
        }
    }

    public function splitMaterial()
    {
        $id_material_old = $this->request->getPost('id_material_old');
        $id_order = $this->request->getPost('id_order');
        $style_size = $this->request->getPost('style_size');
        $inisial = $this->request->getPost('inisial');
        $gw = $this->request->getPost('gw');
        $loss = $this->request->getPost('loss');
        $item_type = $this->request->getPost('item_type');
        $composition = $this->request->getPost('composition');
        $kode_warna = $this->request->getPost('kode_warna');
        $color = $this->request->getPost('color');

        $qty_pcs_1 = $this->request->getPost('qty_pcs_1');
        $qty_pcs_2 = $this->request->getPost('qty_pcs_2');
        $kgs_1 = $this->request->getPost('kgs_1');
        $kgs_2 = $this->request->getPost('kgs_2');

        $split_area_1 = $this->request->getPost('split_area_1');
        $split_area_2 = $this->request->getPost('split_area_2');

        // Data untuk id_material baru (Area 2)
        $dataNew = [
            'id_order' => $id_order,
            'style_size' => $style_size,
            'inisial' => $inisial,
            'gw' => $gw,
            'loss' => $loss,
            'item_type' => $item_type,
            'composition' => $composition,
            'color' => $color,
            'kode_warna' => $kode_warna,
            'qty_pcs' => $qty_pcs_2,
            'kgs' => $kgs_2,
            'area' => $split_area_2
        ];

        // Update id_material lama dengan qty dan kgs baru (Area 1)
        $dataOld = [
            'qty_pcs' => $qty_pcs_1,
            'kgs' => $kgs_1,
            'area' => $split_area_1
        ];

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        // Update id_material lama
        $this->materialModel->update($id_material_old, $dataOld);

        // Insert id_material baru
        $this->materialModel->insert($dataNew);

        // Selesaikan transaksi
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Gagal split material']);
        } else {
            return $this->response->setJSON(['message' => 'Material berhasil di-split!']);
        }
    }
    public function assignArea()
    {
        $model = $this->request->getPost('model'); // Gunakan POST
        $area = $this->request->getPost('area');

        $idOrder = $this->masterOrderModel
            ->where('no_model', $model)
            ->first();

        if (!$idOrder) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Material belum ada']);
        }

        $update = $this->materialModel->assignAreal($idOrder['id_order'], $area);

        if ($update) {
            return $this->response->setStatusCode(200)->setJSON(['success' => 'Berhasil Assign Area']);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Gagal Assign Area di Material']);
        }
    }

    public function listOpenPO($no_model)
    {
        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');
        // Tentukan penerima berdasarkan tujuan
        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } elseif ($tujuan == 'COVERING') {
            $penerima = 'Paryanti';
        } else {
            return redirect()->back()->with('error', 'Tujuan tidak valid.');
        }

        $itemType = $this->masterMaterialModel->getItemType();
        $openPo = $this->openPoModel->listOpenPo($no_model, $jenis, $jenis2, $penerima);

        // dd($openPo);
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
                'itemType' => $itemType,
                'openPo' => $openPo,
                'tujuan' => $tujuan,
                'no_model' => $no_model,
                'penerima' => $penerima,
                'jenis' => $jenis,
                'jenis2' => $jenis2
            ];
        // dd($tujuan, $jenis, $jenis2);
        return view($this->role . '/mastermaterial/list-open-po', $data);
    }

    public function getPoDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->openPoModel->find($id);

            if ($data) {
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updatePo()
    {
        $idPo = $this->request->getPost('id_po');
        $data = [
            'item_type'  => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'color'      => $this->request->getPost('color'),
            'kg_po'      => $this->request->getPost('kg_po')
        ];

        if ($this->openPoModel->update($idPo, $data)) {
            return redirect()->back()->with('success', 'Berhasil memperbarui data.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function deletePo($id)
    {
        // Cek apakah data ada
        $po = $this->openPoModel->find($id);
        if (!$po) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        // Hapus data dari database
        if ($this->openPoModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }
}
