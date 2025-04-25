<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\StockModel;

class PoGabunganController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $stockModel;
    protected $masterMaterialModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->stockModel = new StockModel();
        $this->masterMaterialModel = new MasterMaterialModel();

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
        $jenis = $this->masterMaterialModel->getJenis();
        // dd($jenis);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis
        ];
        return view($this->role . '/masterdata/po-gabungan', $data);
    }

    public function poGabungan($jenis)
    {
        $masterOrder = $this->masterOrderModel->select('master_order.id_order,master_order.no_model')->findAll();
        // dd($masterOrder);
        $data = [
            'model' => $masterOrder,
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'order' => $masterOrder
        ];
        // return view($this->role . '/mastermaterial/openPO', $data);
        return view($this->role . '/masterdata/po-gabungan-form', $data);
    }
    
    public function poGabunganDetail($id_order)
    {
        $material = $this->masterOrderModel->getMaterialOrder($id_order);
        // dd($material);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'material' => $material
        ];
        return response()->setJSON($data);
    }

    public function cekStockOrder($no_model, $item_type, $kode_warna)
    {
        $no_model = $this->masterOrderModel->getNoModel($no_model);
        $stock = $this->stockModel->cekStockOrder($no_model, $item_type, $kode_warna);
        // dd($stock);
        return response()->setJSON($stock);
    }

    public function saveOpenPOGabungan()
    {
        $data = $this->request->getPost();

        // 1. Ambil model ID (id_order) dan ubah jadi list no_model
        $modelIds = array_column($data['no_model'], 'no_model'); // ['12', '10']
        $modelList = $this->masterOrderModel
            ->select('no_model')
            ->whereIn('id_order', $modelIds)
            ->findAll();

        $noModels = array_column($modelList, 'no_model'); // ['L25065', 'L25066']

        // 2. Grouping item berdasarkan kombinasi item_type + kode_warna + color
        $grouped = [];
        foreach ($data['items'] as $item) {
            $key = "{$item['item_type']}|{$item['kode_warna']}|{$item['color']}";
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'item_type'  => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'color'      => $item['color'],
                    'kg_po'      => 0,
                ];
            }
            $grouped[$key]['kg_po'] += (float) $item['kg_po'];
        }

        $groupedItems = array_values($grouped);
        // 🚨 Validasi: pastikan hanya ada satu kombinasi unik
        if (count($groupedItems) > 1) {
            return redirect()->to(base_url($this->role . '/masterdata'))->with('error', 'Gagal menyimpan: Kombinasi item_type, kode warna, dan warna tidak sama semua.');
        }

        // 3. Simpan header PO gabungan (hanya ambil item pertama sebagai "sample")
        $headerData = [
            'no_model'         => 'POGABUNGAN ' . implode(',', $modelIds),
            'item_type'        => $groupedItems[0]['item_type'],
            'kode_warna'       => $groupedItems[0]['kode_warna'],
            'color'            => $groupedItems[0]['color'],
            'kg_po'            => array_sum(array_column($groupedItems, 'kg_po')),
            'keterangan'       => $data['keterangan'] ?? '',
            'penerima'         => $data['penerima'],
            'penanggung_jawab' => $data['penanggung_jawab'],
            'admin'            => session()->get('username') ?? 'system',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
            'id_induk'         => null,
        ];

        $this->openPoModel->insert($headerData);
        $parentId = $this->openPoModel->insertID();
        // dd($parentId);
        // 4. Siapkan data detail (satu baris per kombinasi item_type + warna)
        $batch = [];
        foreach ($groupedItems as $index => $item) {
            $batch[] = [
                'no_model'         => implode(',', $noModels),
                'item_type'        => $item['item_type'],
                'kode_warna'       => $item['kode_warna'],
                'color'            => $item['color'],
                'kg_po'            => $item['kg_po'],
                'keterangan'       => $data['keterangan'] ?? '',
                'penerima'         => $data['penerima'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'admin'            => session()->get('username'),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
                'id_induk'         => $parentId,
            ];
        }

        // 5. Insert batch detail
        if ($this->openPoModel->insertBatch($batch)) {
            return redirect()->to(base_url($this->role . '/masterdata'))->with('success', 'Data PO Gabungan berhasil disimpan.');
        }

        return redirect()->to(base_url($this->role . '/masterdata'))->with('error', 'Data PO Gabungan gagal disimpan.');
    }
}
