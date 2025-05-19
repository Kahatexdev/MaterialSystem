<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\StockModel;
use App\Models\OpenPoModel;

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
    protected $openPoModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->stockModel = new StockModel();
        $this->masterMaterialModel = new MasterMaterialModel();
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

    // public function saveOpenPOGabungan()
    // {
    //     $data = $this->request->getPost();

    //     // 1. Ambil model ID (id_order) dan ubah jadi list no_model
    //     $modelIds = array_column($data['no_model'], 'no_model'); // ['12', '10']
    //     $modelList = $this->masterOrderModel
    //         ->select('no_model')
    //         ->whereIn('id_order', $modelIds)
    //         ->findAll();

    //     $noModels = array_column($modelList, 'no_model'); // ['L25065', 'L25066']

    //     // 2. Grouping item berdasarkan kombinasi item_type + kode_warna + color
    //     $grouped = [];
    //     foreach ($data['items'] as $item) {
    //         $key = "{$item['item_type']}|{$item['kode_warna']}|{$item['color']}";
    //         if (! isset($grouped[$key])) {
    //             $grouped[$key] = [
    //                 'item_type'  => $item['item_type'],
    //                 'kode_warna' => $item['kode_warna'],
    //                 'color'      => $item['color'],
    //                 'kg_po'      => 0,
    //             ];
    //         }
    //         $grouped[$key]['kg_po'] += (float) $item['kg_po'];
    //     }

    //     $groupedItems = array_values($grouped);
    //     // 🚨 Validasi: pastikan hanya ada satu kombinasi unik
    //     if (count($groupedItems) > 1) {
    //         return redirect()->to(base_url($this->role . '/masterdata'))->with('error', 'Gagal menyimpan: Kombinasi item_type, kode warna, dan warna tidak sama semua.');
    //     }

    //     // 3. Simpan header PO gabungan (hanya ambil item pertama sebagai "sample")
    //     $headerData = [
    //         'no_model'         => 'POGABUNGAN ' . implode(',', $modelIds),
    //         'item_type'        => $groupedItems[0]['item_type'],
    //         'kode_warna'       => $groupedItems[0]['kode_warna'],
    //         'color'            => $groupedItems[0]['color'],
    //         'kg_po'            => array_sum(array_column($groupedItems, 'kg_po')),
    //         'keterangan'       => $data['keterangan'] ?? '',
    //         'penerima'         => $data['penerima'],
    //         'penanggung_jawab' => $data['penanggung_jawab'],
    //         'admin'            => session()->get('username') ?? 'system',
    //         'created_at'       => date('Y-m-d H:i:s'),
    //         'updated_at'       => date('Y-m-d H:i:s'),
    //         'id_induk'         => null,
    //     ];

    //     // 4. Transaksi DB
    //     $db = \Config\Database::connect();
    //     $db->transStart();

    //     // 5. Insert header dan dapatkan parentId
    //     $this->openPoModel->insert($headerData);
    //     $parentId = $this->openPoModel->insertID();

    //     // 6. Siapkan data detail - kombinasi per model dan per grup
    //     $batch = [];
    //     foreach ($modelIds as $modelId) {
    //         foreach ($groupedItems as $item) {
    //             $batch[] = [
    //                 'no_model'         => $modelId,
    //                 'item_type'        => $item['item_type'],
    //                 'kode_warna'       => $item['kode_warna'],
    //                 'color'            => $item['color'],
    //                 'kg_po'            => $item['kg_po'],
    //                 'keterangan'       => $data['keterangan'] ?? '',
    //                 'penerima'         => $data['penerima'],
    //                 'penanggung_jawab' => $data['penanggung_jawab'],
    //                 'admin'            => session()->get('username') ?? 'system',
    //                 'created_at'       => date('Y-m-d H:i:s'),
    //                 'updated_at'       => date('Y-m-d H:i:s'),
    //                 'id_induk'         => $parentId,
    //             ];
    //         }
    //     }

    //     // 7. Insert batch detail
    //     $this->openPoModel->insertBatch($batch);

    //     // 8. Commit / rollback
    //     $db->transComplete();
    //     if ($db->transStatus()) {
    //         return redirect()->to(base_url($this->role . '/masterdata'))
    //             ->with('success', 'Data PO Gabungan berhasil disimpan.');
    //     }

    //     return redirect()->to(base_url($this->role . '/masterdata'))->with('error', 'Data PO Gabungan gagal disimpan.');
    // }

    public function saveOpenPOGabungan()
    {
        // 1. Ambil input dan korelasi model IDs dengan nomor model
        $data = $this->request->getPost();
        // dd($data);
        $modelIds = array_column($data['no_model'], 'no_model');      // ['12','10']
        $modelList = $this->masterOrderModel
            ->select('id_order, no_model')
            ->whereIn('id_order', $modelIds)
            ->findAll();

        // Map id_order => no_model
        $noModelMap = [];
        foreach ($modelList as $m) {
            $noModelMap[$m['id_order']] = $m['no_model'];
        }
        // dd($noModelMap);
        // 2. Hitung total untuk header dan siapkan detail original
        $totalKg = 0;
        $details = [];
        $ket = '';
        foreach ($data['items'] as $idx => $it) {
            // Asumsi: $data['items'] mengikuti urutan $modelIds jika kolom per model
            $modelId = $modelIds[$idx] ?? null;
            $totalKg += (float) $it['kg_po'];
            $details[] = [
                'model_id'   => $modelId,
                'item_type'  => $it['item_type'],
                'kode_warna' => $it['kode_warna'],
                'color'      => $it['color'],
                'kg_po'      => (float) $it['kg_po'],
            ];

            $kgPo = (float) $it['kg_po']; // Konversi kg_po menjadi float untuk penghitungan
            $noModel = $noModelMap[$modelId] ?? ''; // Cari no_model berdasarkan modelId
            $ket .= "$noModel = $kgPo";
            // Tambahkan '/' kecuali untuk data terakhir
            if ($idx < count($data['items']) - 1) {
                $ket .= ' / ';
            }
        }
        // Hitung sisa
        $sisa = $data['ttl_keb'] - $totalKg;

        // Tambahkan " / STOCK $sisa" jika sisa lebih dari 0
        if ($sisa > 0) {
            $ket .= " / STOCK = $sisa";
        }

        $keys = array_map(function ($d) {
            return $d['item_type'] . '|' . $d['kode_warna'] . '|' . $d['color'];
        }, $details);
        $uniqueKeys = array_unique($keys);
        if (count($uniqueKeys) > 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tidak disimpan: kombinasi item_type, kode warna, dan color harus sama.');
        }
        // 3. Persist header PO gabungan
        $headerData = [
            'no_model'         => 'POGABUNGAN ' . implode('_', $noModelMap),
            'item_type'        => $details[0]['item_type'],
            'kode_warna'       => $details[0]['kode_warna'],
            'color'            => $details[0]['color'],
            'kg_po'            => $data['ttl_keb'],
            'keterangan'       => $data['keterangan'],
            'ket_celup'        => $ket,
            'penerima'         => $data['penerima'],
            'penanggung_jawab' => $data['penanggung_jawab'],
            'bentuk_celup'     => $data['bentuk_celup'],
            'kg_percones'      => $data['kg_percones'],
            'jumlah_cones'     => $data['jumlah_cones'],
            'jenis_produksi'   => $data['jenis_produksi'],
            'contoh_warna'     => $data['contoh_warna'],
            'admin'            => session()->get('username'),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
            'id_induk'         => null,
        ];
        // dd ($headerData, $details);
        $db = \Config\Database::connect();
        $db->transStart();

        $this->openPoModel->insert($headerData);
        $parentId = $this->openPoModel->insertID();
        // dd($parentId);
        // 4. Siapkan dan insert detail per model sesuai data original
        $batch = [];
        foreach ($details as $d) {
            $batch[] = [
                'no_model'         => $noModelMap[$d['model_id']] ?? '-',
                'item_type'        => '',
                'kode_warna'       => '',
                'color'            => '',
                'kg_po'            => $d['kg_po'],
                'keterangan'       => $data['keterangan'] ?? '',
                'penerima'         => $data['penerima'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'admin'            => session()->get('username'),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
                'id_induk'         => $parentId,
            ];
        }
        // dd ($batch);
        $this->openPoModel->insertBatch($batch);

        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan PO gabungan.');
        }

        return redirect()->to(base_url($this->role . '/masterdata'))
            ->with('success', 'Data PO Gabungan berhasil disimpan.');
    }

    public function listPoGabungan()
    {
        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');
        // dd($tujuan, $jenis, $jenis2);

        // Tentukan penerima berdasarkan tujuan
        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } elseif ($tujuan == 'COVERING') {
            $penerima = 'Paryanti';
        } else {
            return redirect()->back()->with('error', 'Tujuan tidak valid.');
        }

        $buyer = [];
        $openPoGabung = $this->openPoModel->listOpenPoGabung($jenis, $jenis2, $penerima);
        foreach ($openPoGabung as &$po) {
            $buyersData = $this->openPoModel->getBuyer($po['id_po']); // Ambil semua data buyer terkait
            if (is_array($buyersData) && count($buyersData) > 0) {
                // Ambil semua buyer, no_order, dan delivery_awal
                $buyers = array_column($buyersData, 'buyer');
                $noOrders = array_column($buyersData, 'no_order');
                $deliveries = array_column($buyersData, 'delivery_awal');

                // Tentukan buyer: kosong jika lebih dari satu jenis
                $po['buyer'] = count(array_unique($buyers)) === 1 ? $buyers[0] : null;

                // Tentukan delivery_awal paling awal
                $earliestDeliveryIndex = array_keys($deliveries, min($deliveries))[0];
                $po['delivery_awal'] = $deliveries[$earliestDeliveryIndex];

                // Tentukan no_order yang berhubungan dengan delivery_awal paling awal
                $po['no_order'] = $noOrders[$earliestDeliveryIndex];
            } else {
                // Jika tidak ada data buyersData
                $po['buyer'] = null;
                $po['no_order'] = null;
                $po['delivery_awal'] = null;
            }
        }
        // Pastikan untuk tidak menggunakan referensi lagi setelah loop selesai
        unset($po);
        $masterOrder = $this->masterOrderModel->select('master_order.id_order,master_order.no_model')->findAll();

        // dd($openPoGabung);
        $data =
            [
                'active' => $this->active,
                'title' => 'Material System',
                'role' => $this->role,
                'masterOrder' => $masterOrder,
                'openPoGabung' => $openPoGabung,
                'tujuan' => $tujuan,
                'penerima' => $penerima,
                'jenis' => $jenis,
                'jenis2' => $jenis2
            ];
        // dd($tujuan, $jenis, $jenis2);
        return view($this->role . '/mastermaterial/list-open-pogabung', $data);
    }

    public function getPoGabungan($id_po)
    {
        // data induk
        $parent = $this->openPoModel
            ->where('id_po', $id_po)
            ->where('id_induk IS NULL', null, false)
            ->first();

        // data anak
        $children = $this->openPoModel
            ->where('id_induk', $id_po)
            ->findAll();

        return $this->response->setJSON([
            'parent'   => $parent,
            'children' => $children
        ]);
    }

    public function updatePoGabungan()
    {
        $post = $this->request->getPost();
        $parentId = $post['id_po'];

        // 1) Update tiap Child
        if (! empty($post['children']) && is_array($post['children'])) {
            foreach ($post['children'] as $child) {
                $this->openPoModel->update($child['id_po'], [
                    'kg_po' => $child['kg_po']
                ]);
            }
        }

        // 2) Hitung total kg_po anak
        $childrenKgTotal = $this->openPoModel
            ->selectSum('kg_po', 'total')
            ->where('id_induk', $post['id_po'])
            ->first()['total'] ?? 0;

        // 3) Ambil kg_stock (bisa null/0 jika tidak diisi)
        $kgStock = isset($post['kg_stock']) ? floatval($post['kg_stock']) : 0;
        $kgPo = $childrenKgTotal + $kgStock;
        $children = $this->openPoModel
            ->select(['no_model', 'kg_po'])
            ->where('id_induk', $parentId)
            ->findAll();

        $parts = [];
        foreach ($children as $c) {
            $parts[] = "{$c['no_model']} = {$c['kg_po']}";
        }
        $stock = ($childrenKgTotal + $kgStock) - $kgStock;  // sesuai rumus
        $keterangan = implode(' / ', $parts) . " STOCK / {$stock} KG";

        $updateParent = [
            'item_type'      => $post['item_type'],
            'kode_warna'     => $post['kode_warna'],
            'color'          => $post['color'],
            'bentuk_celup'   => $post['bentuk_celup'],
            'kg_percones'    => $post['kg_percones'],
            'jumlah_cones'   => $post['jumlah_cones'],
            'jenis_produksi' => $post['jenis_produksi'],
            'kg_po'          => $kgPo,
            'ket_celup'      => $keterangan,
        ];
        $this->openPoModel->update($parentId, $updateParent);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function deletePoGabungan($id_po)
    {
        // Hapus semua anak dulu
        $this->openPoModel->where('id_induk', $id_po)->delete();
        // Hapus parent
        $this->openPoModel->delete($id_po);

        return $this->response->setJSON(['status' => 'ok']);
    }
}
