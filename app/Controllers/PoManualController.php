<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\OpenPoModel;
use CodeIgniter\HTTP\ResponseInterface;

class PoManualController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $masterOrderModel;
    protected $materialModel;
    protected $openPoModel;
    protected $masterMaterialModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        // $this->stockModel = new StockModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        $allowed_roles = ['gbn', 'covering'];
        if (!in_array(session()->get('role'), $allowed_roles)) {
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
        $poManual = $this->openPoModel->getPoManual();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'poManual' => $poManual,
        ];
        return view($this->role . '/masterdata/po-manual', $data);
    }

    public function create()
    {
        $noModel = $this->masterOrderModel->getAllNoModel();
        $itemType = $this->masterMaterialModel->findAll();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'noModel' => $noModel,
            'itemType' => $itemType,
        ];
        return view($this->role . '/masterdata/po-manual-form', $data);
    }

    public function getNoOrderByModel()
    {
        $noModel    = $this->request->getGet('no_model');

        if ($noModel === null) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter missing']);
        }

        $data = $this->masterOrderModel->getNoOrderByModel($noModel);

        $noOrder = $data['no_order'] ?? 'Tidak ditemukan';

        return $this->response->setJSON(['no_order' => $noOrder]);
    }

    public function saveOpenPoManual()
    {
        $post = $this->request->getPost();
        $noModel = $post['no_model'];
        $db      = \Config\Database::connect();
        $builder = $db->table('master_order');
        $query   = $builder->select('buyer')
            ->where('no_model', $noModel)
            ->get();
        $buyerRow = $query->getRow();
        $buyer = $buyerRow ? $buyerRow->buyer : null;
        // dd($buyer);
        // $noModel = $post['no_model'] ?? [];

        // Jika tidak ditemukan di master_order -> tambahkan record master_order
        if (!$buyerRow) {
            try {
                // Siapkan data insert; gunakan buyer dari $post jika ada, atau null
                $insertMaster = [
                    'no_model' => $noModel,
                    'buyer'    => $post['buyer'] ?? '',
                    'no_order'    => $post['no_order'] ?? '',
                    'admin'    => session()->get('username'),
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ];
                //Insert ke master_order
                $builder->insert($insertMaster);

                // set $buyer supaya dipakai saat insert open PO
                $buyer = $insertMaster['buyer'];
            } catch (\Exception $e) {
                log_message('error', 'Gagal insert master_order untuk no_model ' . $noModel . ': ' . $e->getMessage());
            }
        }

        $items = $post['items'] ?? [];

        foreach ($items as $idx => $item) {
            // cari no_model yang sesuai index
            // $nm = $noModel[$idx]['no_model'] ?? null;

            // Build data sesuai allowedFields
            $data = [
                'buyer'               => $buyer,
                'no_model'            => $post['no_model'] ?? null,
                'item_type'           => $item['item_type']     ?? null,
                'kode_warna'          => $item['kode_warna']    ?? null,
                'color'               => $item['color']         ?? null,
                'spesifikasi_benang'  => $post['spesifikasi_benang'] ?? null,
                'kg_po'               => $item['kg_po']         ?? null,
                'keterangan'          => $post['keterangan']        ?? null,
                'ket_celup'           => $post['ket_celup']         ?? null,
                'bentuk_celup'        => $post['bentuk_celup']      ?? null,
                'kg_percones'         => $post['kg_percones']       ?? null,
                'jumlah_cones'        => $post['jumlah_cones']      ?? null,
                'jenis_produksi'      => $post['jenis_produksi']    ?? null,
                'contoh_warna'        => $post['contoh_warna']      ?? null,
                'penerima'            => $post['penerima']          ?? null,
                'penanggung_jawab'    => $post['penanggung_jawab']  ?? null,
                'po_plus'             => '0',
                'po_Booking'          => '0',
                'po_manual'           => '1',
                'admin'               => session()->get('username'),
                'id_induk'            => null,
            ];

            $this->openPoModel->insert($data);
        }

        return redirect()->to(base_url($this->role . '/masterdata/poManual'))
            ->with('success', 'Data PO Manual berhasil disimpan.');
    }

    public function detail()
    {
        $noModel = $this->request->getGet('no_model');
        $detail = $this->openPoModel->detailPoManual($noModel);
        $del = $detail[0]['delivery_awal'];
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'detail' => $detail,
            'del' => $del
        ];
        return view($this->role . '/masterdata/po-manual-detail', $data);
    }

    public function updatePoManual()
    {
        $post = $this->request->getPost();
        $id = $post['id_po'];
        $noModel = $post['no_model'];
        // dd($noModel);
        $data = [
            'buyer'               => $post['buyer'] ?? null,
            'no_model'            => $post['no_model'],
            'item_type'           => $post['item_type'] ?? null,
            'kode_warna'          => $post['kode_warna'] ?? null,
            'color'               => $post['color'] ?? null,
            'spesifikasi_benang'  => $post['spesifikasi_benang'] ?? null,
            'kg_po'               => $post['kg_po'] ?? null,
            'keterangan'          => $post['keterangan'] ?? null,
            'ket_celup'           => $post['ket_celup'] ?? null,
            'bentuk_celup'        => $post['bentuk_celup'] ?? null,
            'kg_percones'         => $post['kg_percones'] ?? null,
            'jumlah_cones'        => $post['jumlah_cones'] ?? null,
            'jenis_produksi'      => $post['jenis_produksi'] ?? null,
            'contoh_warna'        => $post['contoh_warna'] ?? null,
            'penerima'            => $post['penerima'] ?? null,
            'penanggung_jawab'    => $post['penanggung_jawab'] ?? null,
            'admin'               => session()->get('username'),
        ];

        $this->openPoModel->update($id, $data);

        return redirect()->to(base_url($this->role . '/masterdata/poManual/detail?no_model=' . $noModel))
            ->with('success', 'Data PO Manual berhasil diupdate.');
    }

    public function deletePoManual()
    {
        $id = $this->request->getPost('id_po');
        $noModel = $this->request->getPost('no_model');

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID PO tidak ditemukan']);
        }

        $deleted = $this->openPoModel->delete($id);

        if ($deleted) {
            return redirect()->to(base_url($this->role . '/masterdata/poManual/detail?no_model=' . $noModel))
                ->with('success', 'Data PO Manual Berhasil Di Hapus.');
        } else {
            return redirect()->to(base_url($this->role . '/masterdata/poManual/detail?no_model=' . $noModel))
                ->with('error', 'Data PO Manual Gagal Di Hapus.');
        }
    }
}
