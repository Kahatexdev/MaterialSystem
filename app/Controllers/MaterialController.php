<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use PhpParser\Node\Stmt\Else_;

class MaterialController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $materialModel;
    protected $masterOrderModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();

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
        $style = $this->request->getPost('style_size') ?? [];
        $inisial = $this->request->getPost('inisial') ?? [];
        $id_order = $this->request->getPost('id_order');
        $default = $this->materialModel->getStyleSizeAndInisial($id_order);
        $defaultStyle  = $default[0]['style_size'];
        $defaultInisial = $default[0]['inisial'];

        if (count($style) === 0) {
            $style   = [$defaultStyle];
            $inisial = [$defaultInisial];
        }

        try {
            if (count($style) > 0) {
                for ($i = 0; $i < count($style); $i++) {
                    $saveData = [
                        'id_order'   => esc($idOrder),
                        'style_size' => esc($style[$i]),
                        'inisial'    => esc($inisial[$i]),
                        'area'       => esc($data['area']),
                        'item_type'  => esc($data['item_type']),
                        'kode_warna' => esc($data['kode_warna']),
                        'color'      => esc($data['color']),
                        'composition' => esc($data['composition']),
                        'gw'         => esc($data['gw']),
                        'qty_pcs'    => esc($data['qty_pcs']),
                        'loss'       => esc($data['loss']),
                        'kgs'        => esc($data['kgs']),
                        'keterangan' => esc($data['keterangan']),
                        'admin'      => session()->get('id_user'),
                    ];
                    if (!$this->materialModel->insert($saveData)) {
                        throw new \Exception('Gagal insert pada baris ke-' . ($i + 1));
                    }
                }
            }
            // Kalau tidak ada style sama sekali, buat satu insert tanpa style/inisial
            else {
                $saveData = [
                    'id_order'   => esc($idOrder),
                    'style_size' => null,
                    'inisial'    => null,
                    'area'       => esc($data['area']),
                    'item_type'  => esc($data['item_type']),
                    'kode_warna' => esc($data['kode_warna']),
                    'color'      => esc($data['color']),
                    'composition' => esc($data['composition']),
                    'gw'         => esc($data['gw']),
                    'qty_pcs'    => esc($data['qty_pcs']),
                    'loss'       => esc($data['loss']),
                    'kgs'        => esc($data['kgs']),
                    'keterangan' => esc($data['keterangan']),
                    'admin'      => session()->get('id_user'),
                ];
                if (!$this->materialModel->insert($saveData)) {
                    throw new \Exception('Gagal insert data material.');
                }
            }

            return redirect()->to(base_url("$this->role/material/$idOrder"))
                ->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->to(base_url("$this->role/material/$idOrder"))
                ->with('error', 'Data gagal disimpan: ' . $e->getMessage());
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
        $delivery = $this->request->getPost('delivery');
        $unit = $this->request->getPost('pu');

        if ($unit == 'CJ') {
            $unit = 'Cijerah';
        } elseif ($unit == 'MJ') {
            $unit = 'Majalaya';
        } else {
            $unit = 'Belum di Assign';
        }
        $idOrder = $this->masterOrderModel
            ->where('no_model', $model)
            ->first();

        if (!$idOrder) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Material belum ada']);
        }

        $update = $this->masterOrderModel->update(
            $idOrder['id_order'],
            [
                'delivery_awal' => $delivery['delivery_awal'],
                'delivery_akhir' => $delivery['delivery_akhir'],
                'unit' => $unit
            ]
        );
        $areal = $this->materialModel->assignAreal($idOrder['id_order'], $area);

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
        $del = $openPo[0]['delivery_awal'] ?? 'yyyy-mm-dd';
        foreach ($openPo as &$po) {
            $po['po_plus'] = ($po['po_plus'] == '1') ? 'YA' : 'TIDAK';
        }
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
                'jenis2' => $jenis2,
                'del' => $del
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
        $oldItem   = $this->request->getPost('old_item');
        $keterangan = $this->request->getPost('keterangan');
        $poData = $this->openPoModel->find($idPo);
        $idSch = $this->scheduleCelupModel->getIdSch($poData)['id_celup'] ?? null;

        $data = [
            'item_type'  => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'color'      => $this->request->getPost('color'),
            'kg_po'      => $this->request->getPost('kg_po'),
            'ket_celup'  => $this->request->getPost('ket_celup'),
        ];
        $sch = [
            'no_model'   => $this->request->getPost('no_model'),
            'item_type'  => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'warna'      => $this->request->getPost('color'),
        ];
        // $db = db_connect();
        // $db->transStart();

        $po = $this->openPoModel->update($idPo, $data);
        if ($po) {
            $updateSch = $this->scheduleCelupModel->where('id_celup', $idSch)->set($sch)->update();
            $this->openPoModel->where('id_po', $idPo)->set('keterangan', $keterangan)->update();
            return redirect()->back()->with('success', 'Data berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Update gagal. Silakan coba lagi.');
        }

        // $db->transComplete();


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

    public function deleteSelected()
    {
        if ($this->request->isAJAX()) {
            $ids = $this->request->getJSON()->ids ?? [];

            if (!empty($ids)) {
                $this->materialModel->whereIn('id_material', $ids)->delete();
                return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada data untuk dihapus.']);
            }
        }

        return redirect()->back()->with('error', 'Permintaan tidak valid.');
    }

    public function materialTypeEdit()
    {
        $id_order = $this->request->getPost('id_order');
        $item_type = $this->request->getPost('item_type');
        $material_type = $this->request->getPost('material_type');

        // Pecah item_type yang dikirim dari select
        // Misal value = "Nylon | K01 | Blue"
        $parts = explode('|', $item_type);
        $itemType = trim($parts[0] ?? '');
        $kodeWarna = trim($parts[1] ?? '');
        $color = trim($parts[2] ?? '');

        // Validasi data
        if (empty($itemType) || empty($kodeWarna) || empty($color) || empty($material_type)) {
            return redirect()->back()->with('error', 'Semua field harus diisi.');
        }

        // Data yang akan diupdate
        $data = [
            'material_type' => $material_type,
        ];

        // Update semua baris berdasarkan group (item_type, kode_warna, color)
        $update = $this->materialModel
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->where('color', $color)
            ->where('id_order', $id_order)
            ->set($data)
            ->update();

        if ($update) {
            return redirect()->back()->with('success', 'Material Type berhasil diperbarui untuk group tersebut.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui Material Type.');
        }
    }
}
