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
        $itemType = $this->masterMaterialModel->findAll();
        // dd($itemType);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'itemType' => $itemType,
        ];
        return view($this->role . '/masterdata/po-manual-form', $data);
    }

    public function saveOpenPoManual()
    {
        $post = $this->request->getPost();
        // dd($post);

        $noModel = $post['no_model'] ?? [];
        $items = $post['items'] ?? [];

        foreach ($items as $idx => $item) {
            // cari no_model yang sesuai index
            $nm = $noModel[$idx]['no_model'] ?? null;

            // Build data sesuai allowedFields
            $data = [
                'no_model'            => $nm,
                'item_type'           => $item['item_type']     ?? null,
                'kode_warna'          => $item['kode_warna']    ?? null,
                'color'               => $item['color']         ?? null,
                'spesifikasi_benang'  => $post['spesifikasi_benang'] ?? null,
                'kg_po'               => $item['kg_po']         ?? null,
                'keterangan'          => $post['keterangan']        ?? null,
                'ket_celup'           => $post['tujuan_po']         ?? null,
                'bentuk_celup'        => $post['bentuk_celup']      ?? null,
                'kg_percones'         => $post['kg_percones']       ?? null,
                'jumlah_cones'        => $post['jumlah_cones']      ?? null,
                'jenis_produksi'      => $post['jenis_produksi']    ?? null,
                'contoh_warna'        => $post['contoh_warna']      ?? null,
                'penerima'            => $post['penerima']          ?? null,
                'penanggung_jawab'    => $post['penanggung_jawab']  ?? null,
                'po_plus'             => '0',
                'po_booking'          => '0',
                'po_manual'           => '1',
                'admin'               => session()->get('username'),
                'id_induk'            => null,
            ];

            $this->openPoModel->insert($data);
        }

        return redirect()->to(base_url($this->role . '/masterdata'))
            ->with('success', 'Data PO Manual berhasil disimpan.');
    }
}
