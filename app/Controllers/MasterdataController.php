<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use DateTime;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPOModel;
use App\Models\EstimasiStokModel;
use App\Models\StockModel;

class MasterdataController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $estimasiStokModel;
    protected $openPoModel;
    protected $stockModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->estimasiStokModel = new EstimasiStokModel();
        $this->openPOModel = new OpenPoModel();
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
        $masterOrder = $this->masterOrderModel->findAll();
        $material = $this->materialModel->findAll();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'masterOrder' => $masterOrder,
            'material' => $material,
        ];
        return view($this->role . '/masterdata/index', $data);
    }



    public function getOrderDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->masterOrderModel->find($id);

            if ($data) {
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateOrder()
    {

        $id = $this->request->getPost('id_order');

        $data = [
            'foll_up' => $this->request->getPost('foll_up'),
            'lco_date' => $this->request->getPost('lco_date'),
            'no_model' => $this->request->getPost('no_model'),
            'no_order' => $this->request->getPost('no_order'),
            'buyer' => $this->request->getPost('buyer'),
            'memo' => $this->request->getPost('memo'),
            'delivery_awal' => $this->request->getPost('delivery_awal'),
            'delivery_akhir' => $this->request->getPost('delivery_akhir'),

            // Tambahkan field lain yang ingin diperbarui
        ];

        if ($this->masterOrderModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/masterdata'))->with('success', 'Data berhasil diupdate.');
        } else {
            return redirect()->to(base_url($this->role . '/masterdata'))->with('error', 'Data gagal diupdate.');
        }
    }

    public function importMU()
    {
        // Ambil file yang diupload
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
        }

        // Inisialisasi model-model
        $masterOrderModel    = new MasterOrderModel();
        $materialModel       = new MaterialModel();
        $masterMaterialModel = new MasterMaterialModel();

        // Ambil username admin dari session
        $admin = session()->get('username');
        if (!$admin) {
            return redirect()->back()->with('error', 'Session expired. Please log in again.');
        }

        try {
            // Load file Excel/CSV
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();

            // Ambil data header master order dari sel-sel tertentu
            $no_model    = str_replace([': '], '', $sheet->getCell('B9')->getValue());
            $no_order    = str_replace([': '], '', $sheet->getCell('B5')->getValue());
            $buyer       = str_replace([': '], '', $sheet->getCell('B6')->getValue());
            $lco_dateRaw = str_replace([': '], '', $sheet->getCell('B4')->getFormattedValue());
            $foll_up     = str_replace([': '], '', $sheet->getCell('D5')->getValue());

            // Konversi format tanggal dari d.m.Y ke Y-m-d
            $date_object = DateTime::createFromFormat('d.m.Y', $lco_dateRaw);
            if ($date_object) {
                $lco_date = $date_object->format('Y-m-d');
            } else {
                return redirect()->back()->with('error', 'Format tanggal LCO tidak valid.');
            }

            // Cek apakah master order sudah ada di database
            $orderExists = $masterOrderModel->checkDatabase($no_order, $no_model, $buyer, $lco_date, $foll_up);

            // Jika master order belum ada, lakukan validasi dengan mencari baris material yang memiliki style_size valid
            if (!$orderExists) {
                $validate = null;
                foreach ($sheet->getRowIterator() as $key => $row) {
                    // Lewati baris header
                    if ($key == 1) {
                        continue;
                    }
                    $style_size = $sheet->getCell('D' . $key)->getValue();
                    if (!empty($no_model) && !empty($style_size)) {
                        $validate = $this->validateWithAPI($no_model, $style_size);
                        if ($validate) {
                            break; // Gunakan validasi dari baris pertama yang valid
                        }
                    }
                }

                if (!$validate) {
                    return redirect()->back()->with('error', 'Validasi master order gagal, tidak ditemukan style size yang valid.');
                }

                // Siapkan data master order
                $masterData = [
                    'no_order'       => $no_order,
                    'no_model'       => $no_model,
                    'buyer'          => $buyer,
                    'foll_up'        => $foll_up,
                    'lco_date'       => $lco_date,
                    'memo'           => NULL,
                    'delivery_awal'  => $validate['delivery_awal'],
                    'delivery_akhir' => $validate['delivery_akhir'],
                    'admin'          => $admin,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => NULL,
                ];
                $masterOrderModel->insert($masterData);
            }

            // Dapatkan id_order untuk digunakan pada tabel material
            $orderData = $masterOrderModel->findIdOrder($no_order);
            if (!$orderData) {
                return redirect()->back()->with('error', 'Gagal menemukan ID Order untuk ' . $no_order);
            }
            $id_order = $orderData['id_order'];

            // Mapping header untuk data material
            $headerMap = [
                'Color'          => 'A',
                'Item Type'      => 'B',
                'Kode Warna'     => 'C',
                'Item Nr'        => 'D',
                'Composition(%)' => 'E',
                'GW/pc'          => 'F',
                'Qty/pcs'        => 'G',
                'Loss'           => 'H',
                'Kgs'            => 'I',
            ];

            $validDataMaterial = [];
            $invalidRows       = [];

            // Iterasi baris data material (misalnya mulai dari baris kedua)
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowIndex  = $row->getRowIndex();
                $style_size = $sheet->getCell('D' . $rowIndex)->getValue();
                if (empty($no_model) || empty($style_size)) {
                    $invalidRows[] = $rowIndex;
                    continue;
                }

                $validate = $this->validateWithAPI($no_model, $style_size);
                if (!$validate) {
                    $invalidRows[] = $rowIndex;
                    continue;
                }

                // Ambil dan sanitasi item type
                $item_type = trim($sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue());
                if (empty($item_type)) {
                    return redirect()->back()->with('error', 'Item Type tidak boleh kosong pada baris ' . $rowIndex);
                }
                $item_type = htmlspecialchars($item_type, ENT_QUOTES, 'UTF-8');

                // Cek apakah item type ada di database
                $checkItemType = $masterMaterialModel->checkItemType($item_type);
                if (!$checkItemType) {
                    return redirect()->back()->with('error', $item_type . ' tidak ada di database pada baris ' . $rowIndex);
                }

                // Siapkan data material
                $validDataMaterial[] = [
                    'id_order'   => $id_order,
                    'style_size' => $validate['size'],
                    'area'       => $validate['area'],
                    'inisial'    => $validate['inisial'],
                    'color'      => $sheet->getCell($headerMap['Color'] . $rowIndex)->getValue(),
                    'item_type'  => htmlspecialchars_decode($item_type),
                    'kode_warna' => $sheet->getCell($headerMap['Kode Warna'] . $rowIndex)->getValue(),
                    'composition' => $sheet->getCell($headerMap['Composition(%)'] . $rowIndex)->getValue(),
                    'gw'         => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(),
                    'qty_pcs'    => $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue(),
                    'loss'       => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue(),
                    'kgs'        => $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue(),
                    'admin'      => $admin,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            // Simpan data material jika ada data yang valid
            if (!empty($validDataMaterial)) {
                $materialModel->insertBatch($validDataMaterial);
            }

            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }


    private function validateWithAPI($no_model, $style_size)
    {
        $style_size_encoded = str_replace(' ', '%20', $style_size);
        $param = $no_model . '/' . $style_size_encoded;

        $url = 'http://172.23.44.14/CapacityApps/public/api/orderMaterial/' . $param;

        try {
            $json = @file_get_contents($url);
            $response = json_decode($json, true);
            return $response;
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function material($id)
    {
        $id_order = $id; // Ambil id_order dari URL
        if (!$id_order) {
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'ID Order tidak ditemukan.');
        }

        $orderData = $this->materialModel->getMaterial($id_order);
        $model = $orderData[0]['no_model'];
        if (!$orderData) {
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'Data Order tidak ditemukan.');
        }
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'orderData' => $orderData,
            'no_model' => $model,
            'id_order' => $id_order
        ];

        return view($this->role . '/mastermaterial/detailMaterial', $data);
    }


    public function getMaterialDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->materialModel->find($id);

            if ($data) {
                return $this->response->setJSON($data);
            } else {
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateMaterial()
    {
        $id = $this->request->getPost('id_material');
        $idOrder = $this->request->getPost('id_order');
        $data = [
            'style_size' => $this->request->getPost('style_size'),
            'area' => $this->request->getPost('area'),
            'inisial' => $this->request->getPost('inisial'),
            'color' => $this->request->getPost('color'),
            'item_type' => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'composition' => $this->request->getPost('composition'),
            'gw' => $this->request->getPost('gw'),
            'qty_pcs' => $this->request->getPost('qty_pcs'),
            'loss' => $this->request->getPost('loss'),
            'kgs' => $this->request->getPost('kgs'),


        ];

        if ($this->materialModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('success', 'Data Berhsil.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('error', 'Data Gagal Di Update.');
        }
    }

    public function deleteMaterial($id, $idorder)
    {


        if ($this->materialModel->delete($id)) {
            return redirect()->to(base_url($this->role . '/material/' . $idorder))->with('success', 'Data Berhsil.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $idorder))->with('error', 'Data gagal.');
        }
    }

    public function openPO($id)
    {
        $masterOrder = $this->masterOrderModel->getMaterialOrder($id);
        $orderData = $this->masterOrderModel->find($id);
        foreach ($masterOrder as &$order) { // Note: pass by reference to modify the original array
            foreach ($order['kode_warna'] as &$item) {
                $model = $item['no_model'];
                $itemType = $item['item_type'];
                $kodeWarna = $item['kode_warna'];

                $cek = [
                    'no_model' => $model,
                    'item_type' => $itemType,
                    'kode_warna' => $kodeWarna,
                ];

                $cekStok = $this->stockModel->cekStok($cek);

                if ($cekStok) {
                    $kebutuhan = max(0, $item['total_kg'] - $cekStok['kg_stok']); // Ensure no negative values
                    $item['kg_stok'] = $cekStok['kg_stok'];
                    $item['kg_po'] = $kebutuhan;
                } else {
                    $item['kg_stok'] = 0;
                    $item['kg_po'] = $item['total_kg'];
                }
            }
        }

        $data = [
            'model' => $orderData['no_model'],
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'order' => $masterOrder,
            'id_order' => $id
        ];
        return view($this->role . '/mastermaterial/openPO', $data);
    }

    public function saveOpenPO($id)
    {
        $data = $this->request->getPost();
        $id_order = $id;
        // dd($id_order);

        $items = $data['items'] ?? [];
        foreach ($items as $item) {
            $itemData = [
                'role'             => $this->role,
                'no_model'         => $data['no_model'],
                'item_type'        => $item['item_type'],
                'kode_warna'       => $item['kode_warna'],
                'color'            => $item['color'],
                'kg_po'            => $item['kg_po'],
                'keterangan'       => $data['keterangan'],
                'penerima'         => $data['penerima'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'admin'            => session()->get('username'),
            ];
            // Simpan data ke database
            $this->openPOModel->insert($itemData);
        }

        return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data PO Berhasil Di Tambahkan.');
    }
}
