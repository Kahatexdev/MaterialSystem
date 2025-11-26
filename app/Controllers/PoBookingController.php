<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\OpenPoModel;
use CodeIgniter\HTTP\ResponseInterface;

class PoBookingController extends BaseController
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
        $poBooking = $this->openPoModel->getPoBooking();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'poBooking' => $poBooking
        ];
        return view($this->role . '/masterdata/po-booking', $data);
    }
    public function create()
    {
        $buyer = $this->masterOrderModel->getBuyer();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'buyer' => $buyer,
        ];
        return view($this->role . '/masterdata/po-booking-form', $data);
    }

    public function getItemType()
    {
        $q       = $this->request->getGet('q');
        $itemType = $this->masterMaterialModel->getItemTypeList($q);
        // dd($itemType);
        $result = array_map(fn($r) => [
            'id'   => $r['item_type'],
            'text' => $r['item_type']
        ], $itemType);
        return $this->response->setJSON($result);
    }

    public function getKodeWarna()
    {
        $buyer    = $this->request->getGet('buyer');
        $itemType = $this->request->getGet('item_type');

        if (! $buyer || ! $itemType) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Parameter missing']);
        }

        // model sudah punya method getKodeWarnaByBuyerAndItemType($buyer,$itemType)
        $rows = $this->materialModel
            ->getKodeWarnaByBuyerAndItemType($buyer, $itemType);

        // format untuk Select2 / dropdown:
        $result = array_map(fn($r) => [
            'id'   => $r['kode_warna'],
            'text' => $r['kode_warna']
        ], $rows);

        return $this->response->setJSON($result);
    }

    public function getColor()
    {
        $buyer    = $this->request->getGet('buyer');
        $itemType = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');

        if ($buyer === null || $itemType === null || $kodeWarna === null) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Parameter missing']);
        }

        $data = $this->materialModel->getWarnaByBuyerItemTypeAndKodeWarna($buyer, $itemType, $kodeWarna);

        $color = $data['color'] ?? 'Tidak ditemukan';

        return $this->response->setJSON(['color' => $color]);
    }

    public function saveOpenPoBooking()
    {
        $post = $this->request->getPost();
        // dd($post);
        // pecah array
        $items    = $post['items'] ?? [];
        // $noModels = $post['no_model'] ?? [];

        // Loop setiap index
        foreach ($items as $idx => $item) {
            // cari no_model yang sesuai index
            // $nm = $noModels[$idx]['no_model'] ?? null;

            // Build data sesuai allowedFields
            $data = [
                'buyer'               => $post['buyer'],
                'no_model'            => $post['no_model'],
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
                'po_plus'             => $post['po_plus'] ?? null,
                'po_booking'          => '1',
                'po_manual'           => '0',
                'admin'               => session()->get('username'),
                'id_induk'            => null,
            ];

            $this->openPoModel->insert($data);
        }

        return redirect()->to(base_url($this->role . '/masterdata/poBooking'))
            ->with('success', 'Data PO Booking berhasil disimpan.');
    }

    public function detail()
    {
        $noModel = $this->request->getGet('no_model');
        $detail = $this->openPoModel->detailPoBooking($noModel);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'detail' => $detail,
        ];
        return view($this->role . '/masterdata/po-booking-detail', $data);
    }

    public function updatePoBooking()
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

        return redirect()->to(base_url($this->role . '/masterdata/poBooking/detail?no_model=' . $noModel))
            ->with('success', 'Data PO Booking berhasil diupdate.');
    }

    public function deletePoBooking()
    {
        $id = $this->request->getPost('id_po');
        $noModel = $this->request->getPost('no_model');

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID PO tidak ditemukan']);
        }

        $deleted = $this->openPoModel->delete($id);

        if ($deleted) {
            return redirect()->to(base_url($this->role . '/masterdata/poBooking/detail?no_model=' . $noModel))
                ->with('success', 'Data PO Booking Berhasil Di Hapus.');
        } else {
            return redirect()->to(base_url($this->role . '/masterdata/poBooking/detail?no_model=' . $noModel))
                ->with('error', 'Data PO Booking Gagal Di Hapus.');
        }
    }
}
