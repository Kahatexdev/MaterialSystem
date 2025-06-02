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
        $style = $this->request->getPost('style_size');
        $inisial = $this->request->getPost('inisial');

        try {
            for ($i = 0; $i < count($style); $i++) {
                $saveData = [
                    'id_order'     => esc($idOrder),
                    'style_size'   => esc($style[$i]),
                    'area'         => esc($data['area']),
                    'inisial'      => esc($inisial[$i]),
                    'color'        => esc($data['color']),
                    'item_type'    => esc($data['item_type']),
                    'kode_warna'   => esc($data['kode_warna']),
                    'composition'  => esc($data['composition']),
                    'gw'           => esc($data['gw']),
                    'qty_pcs'      => esc($data['qty_pcs']),
                    'loss'         => esc($data['loss']),
                    'kgs'          => esc($data['kgs']),
                    'admin'        => session()->get('id_user'),
                ];

                if (!$this->materialModel->insert($saveData)) {
                    // Kalau insert gagal, lempar error manual
                    throw new \Exception("Gagal insert pada baris ke-" . ($i + 1));
                }
            }

            return redirect()->to(base_url($this->role . '/material/' . $idOrder))
                ->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))
                ->with('error', 'Data gagal disimpan: ' . esc($e->getMessage()));
        }
    }


    public function updateArea($id_order)
    {
        $getArea = $this->request->getPost('edit_all_area');
        // dd($getArea);
        if ($getArea == 'Gedung 1' || $getArea == 'Gedung 2' || $getArea == 'MJ') {
            $this->masterOrderModel->update($id_order, ['unit' => 'MAJALAYA']);
        } else {
            $this->masterOrderModel->update($id_order, ['unit' => 'CIJERAH']);
        }

        if ($this->materialModel->updateAreaPerNoModel($id_order, $getArea)) {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data berhasil diupdate.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('error', 'Data gagal diupdate.');
        }
    }
    public function splitMaterial()
    {
        $post = $this->request->getPost();
        $oldId      = $post['id_material_old'];
        $idOrder    = $post['id_order'];
        $styleSize  = $post['style_size'];
        $inisial    = $post['inisial'];
        $gw         = $post['gw'];
        $loss       = $post['loss'];
        $qtyPcs     = $post['qty_pcs'];
        $area       = $post['area'];
        $it1        = $post['item_type_1'];
        $it2        = $post['item_type_2'];
        $comp1      = $post['comp1'];
        $comp2      = $post['comp2'];
        $kgs1       = $post['kgs_1'];
        $kgs2       = $post['kgs_2'];

        // Data untuk record baru
        $dataNew = [
            'id_order'     => $idOrder,
            'style_size'   => $styleSize,
            'inisial'      => $inisial,
            'gw'           => $gw,
            'loss'         => $loss,
            'item_type'    => $it2,
            'composition'  => $comp2,
            'color'        => $post['color']       ?? null,
            'kode_warna'   => $post['kode_warna']  ?? null,
            'qty_pcs'      => $qtyPcs,
            'kgs'          => $kgs2,
            'area'         => $area
        ];

        // Data untuk update record lama 
        $dataOld = [
            'item_type'   => $it1,
            'composition' => $comp1,
            'qty_pcs'     => $qtyPcs,
            'kgs'         => $kgs1,
            'area'         => $area
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        // update lama
        $this->materialModel->update($oldId, $dataOld);
        // insert baru
        $this->materialModel->insert($dataNew);

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Gagal split material']);
        }
        return $this->response->setJSON(['message' => 'Material berhasil di‑split!']);
    }

    // public function splitMaterial()
    // {
    //     $id_material_old = $this->request->getPost('id_material_old');
    //     $id_order = $this->request->getPost('id_order');
    //     $style_size = $this->request->getPost('style_size');
    //     $inisial = $this->request->getPost('inisial');
    //     $gw = $this->request->getPost('gw');
    //     $loss = $this->request->getPost('loss');
    //     $item_type = $this->request->getPost('item_type');
    //     $composition = $this->request->getPost('composition');
    //     $kode_warna = $this->request->getPost('kode_warna');
    //     $color = $this->request->getPost('color');

    //     $qty_pcs_1 = $this->request->getPost('qty_pcs_1');
    //     $qty_pcs_2 = $this->request->getPost('qty_pcs_2');
    //     $kgs_1 = $this->request->getPost('kgs_1');
    //     $kgs_2 = $this->request->getPost('kgs_2');

    //     $split_area_1 = $this->request->getPost('split_area_1');
    //     $split_area_2 = $this->request->getPost('split_area_2');

    //     // Data untuk id_material baru (Area 2)
    //     $dataNew = [
    //         'id_order' => $id_order,
    //         'style_size' => $style_size,
    //         'inisial' => $inisial,
    //         'gw' => $gw,
    //         'loss' => $loss,
    //         'item_type' => $item_type,
    //         'composition' => $composition,
    //         'color' => $color,
    //         'kode_warna' => $kode_warna,
    //         'qty_pcs' => $qty_pcs_2,
    //         'kgs' => $kgs_2,
    //         'area' => $split_area_2
    //     ];

    //     // Update id_material lama dengan qty dan kgs baru (Area 1)
    //     $dataOld = [
    //         'qty_pcs' => $qty_pcs_1,
    //         'kgs' => $kgs_1,
    //         'area' => $split_area_1
    //     ];

    //     // Mulai transaksi database
    //     $db = \Config\Database::connect();
    //     $db->transStart();

    //     // Update id_material lama
    //     $this->materialModel->update($id_material_old, $dataOld);

    //     // Insert id_material baru
    //     $this->materialModel->insert($dataNew);

    //     // Selesaikan transaksi
    //     $db->transComplete();

    //     if ($db->transStatus() === false) {
    //         return $this->response->setStatusCode(404)->setJSON(['error' => 'Gagal split material']);
    //     } else {
    //         return $this->response->setJSON(['message' => 'Material berhasil di-split!']);
    //     }
    // }
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
        $noModel   = $this->request->getPost('no_model');
        $keterangan = $this->request->getPost('keterangan');

        $data = [
            'item_type'  => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'color'      => $this->request->getPost('color'),
            'kg_po'      => $this->request->getPost('kg_po'),
            'ket_celup'  => $this->request->getPost('ket_celup'),
        ];

        $db = db_connect();
        $db->transStart();

        $this->openPoModel->update($idPo, $data);

        $builder = $db->table($this->openPoModel->table);
        $builder
            ->where('no_model', $noModel)
            ->update(['keterangan' => $keterangan]);

        $db->transComplete();

        if ($db->transStatus()) {
            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Update gagal. Silakan coba lagi.');
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
