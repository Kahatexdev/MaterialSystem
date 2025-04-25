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
        $this->openPoModel = new OpenPoModel();
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
            // dd($orderExists);
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
            } else {
                return redirect()->back()->with('error', 'Data dengan No Model ' . $orderExists['no_model'] . ' sudah ada di database.');
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

                // Ambil nilai qty_pcs dan bersihkan dari pemisah ribuan
                $qty_raw = $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue();
                $qty_pcs = intval(str_replace([',', '.'], '', $qty_raw));
                $kgs_raw = $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue();
                $kgs = floatval(str_replace([','], '', $kgs_raw));

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
                    'qty_pcs'    => $qty_pcs, // Menggunakan variabel yang telah diproses
                    'loss'       => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue() ?? 0,
                    'kgs'        => number_format($kgs, 2, '.', ''),
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

    public function reviseMU()
    {
        // Ambil file yang diupload dan validasi file
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
        }

        // (Opsional) Cek ekstensi file jika diperlukan
        $allowedExtensions = ['xls', 'xlsx', 'csv'];
        if (!in_array($file->getClientExtension(), $allowedExtensions)) {
            return redirect()->back()->with('error', 'File yang diupload harus berformat Excel atau CSV.');
        }

        // Inisialisasi model-model yang dibutuhkan
        $masterOrderModel    = new MasterOrderModel();
        $materialModel       = new MaterialModel();
        $masterMaterialModel = new MasterMaterialModel();

        // Ambil username admin dari session
        $admin = session()->get('username');
        if (!$admin) {
            return redirect()->back()->with('error', 'Session expired. Please log in again.');
        }

        try {
            // Load file Excel/CSV dan ambil sheet aktif
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();

            // Ambil no_model dari file Excel (misalnya dari sel B9)
            $no_model = str_replace([': '], '', $sheet->getCell('B9')->getValue());
            if (empty($no_model)) {
                return redirect()->back()->with('error', 'No Model tidak ditemukan di file.');
            }

            // Cari master order berdasarkan no_model
            $masterOrder = $masterOrderModel->where('no_model', $no_model)->first();
            if (!$masterOrder) {
                return redirect()->back()->with('error', 'Master order dengan No Model ' . $no_model . ' tidak ditemukan.');
            }
            $id_order = $masterOrder['id_order'];

            // --- Update data master_order berdasarkan header file Excel ---
            $no_order    = str_replace([': '], '', $sheet->getCell('B5')->getValue());
            if (empty($no_order)) {
                return redirect()->back()->with('error', 'No Order tidak ditemukan di file.');
            }
            $buyer       = str_replace([': '], '', $sheet->getCell('B6')->getValue());
            $lco_dateRaw = str_replace([': '], '', $sheet->getCell('B4')->getFormattedValue());
            $foll_up     = str_replace([': '], '', $sheet->getCell('D5')->getValue());

            // Validasi format tanggal
            $date_object = DateTime::createFromFormat('d.m.Y', $lco_dateRaw);
            if ($date_object) {
                $lco_date = $date_object->format('Y-m-d');
            } else {
                return redirect()->back()->with('error', 'Format tanggal LCO tidak valid.');
            }

            $masterDataUpdate = [
                'no_order'  => $no_order,
                'buyer'     => $buyer,
                'lco_date'  => $lco_date,
                'foll_up'   => $foll_up,
                'admin'     => $admin,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $masterOrderModel->update($id_order, $masterDataUpdate);
            // --- End update master_order ---

            // Ambil data material lama (yang sudah ada di DB) untuk master order ini
            $existingMaterials = $materialModel->where('id_order', $id_order)->findAll();
            $existingKeys = [];
            foreach ($existingMaterials as $material) {
                // Normalisasi data untuk composite key
                $oldStyleSize = strtoupper(trim($material['style_size']));
                $oldItemType  = strtoupper(trim($material['item_type']));
                $oldKodeWarna = strtoupper(trim($material['kode_warna']));
                $oldColor     = strtoupper(trim($material['color']));

                // Composite key: style_size_itemType_kodeWarna_color
                $existingKey = $oldStyleSize . '_' . $oldItemType . '_' . $oldKodeWarna . '_' . $oldColor;
                $existingKeys[$existingKey] = $material;
            }

            // Mapping header sesuai format file Excel
            $headerMap = [
                'Color'          => 'A',
                'Item Type'      => 'B',
                'Kode Warna'     => 'C',
                'Item Nr'        => 'D', // berisi style_size
                'Composition(%)' => 'E',
                'GW/pc'          => 'F',
                'Qty/pcs'        => 'G',
                'Loss'           => 'H',
                'Kgs'            => 'I',
            ];

            // Array untuk menyimpan composite key dari file Excel revisi
            $newMaterialKeys = [];

            // Iterasi baris data material (misalnya mulai dari baris 15)
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowIndex  = $row->getRowIndex();
                if ($rowIndex < 15) {
                    continue;
                }

                // Ambil raw data untuk style_size
                $style_sizeRaw = $sheet->getCell($headerMap['Item Nr'] . $rowIndex)->getValue();
                if (empty($style_sizeRaw)) {
                    // Lewati baris tanpa style_size
                    continue;
                }

                // (Opsional) Cek apakah style_size mengandung 'X'
                if (stripos($style_sizeRaw, 'X') === false) {
                    log_message('error', 'Baris ' . $rowIndex . ' tidak mengandung X: ' . $style_sizeRaw);
                    continue;
                }

                // Validasi dengan API (pastikan respons valid dan memiliki format yang diharapkan)
                $validate = $this->validateWithAPI($no_model, $style_sizeRaw);
                if (!$validate || !isset($validate['size'])) {
                    return redirect()->back()->with(
                        'error',
                        'Data <strong>StyleSize</strong> pada baris ke-' . $rowIndex . ': '
                            . $style_sizeRaw
                            . ' tidak valid atau tidak ditemukan di CapacityApps.'
                    );
                }

                // Normalisasi style_size dari API
                $style_size = strtoupper(trim($validate['size']));

                // Ambil dan validasi item type
                $raw_item_type = $sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue();
                if (empty($raw_item_type)) {
                    return redirect()->back()->with('error', 'Item Type tidak boleh kosong pada baris ' . $rowIndex);
                }
                $item_type = strtoupper(trim($raw_item_type));
                if (!$masterMaterialModel->checkItemType($item_type)) {
                    return redirect()->back()->with('error', $item_type . ' tidak ada di database pada baris ' . $rowIndex);
                }

                // Normalisasi data lain
                $kode_warna = strtoupper(trim($sheet->getCell($headerMap['Kode Warna'] . $rowIndex)->getValue()));
                $color      = strtoupper(trim($sheet->getCell($headerMap['Color'] . $rowIndex)->getValue()));

                // Buat composite key baru
                $key = $style_size . '_' . $item_type . '_' . $kode_warna . '_' . $color;
                $newMaterialKeys[] = $key;

                // Validasi nilai numeric untuk Qty, GW, dan Kgs
                $qty_raw = $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue();
                $qty_pcs = intval(str_replace([',', '.'], '', $qty_raw));
                if (!is_numeric($qty_pcs)) {
                    return redirect()->back()->with('error', 'Qty/pcs tidak valid pada baris ' . $rowIndex);
                }

                // Siapkan data material baru dari file Excel
                $materialData = [
                    'id_order'    => $id_order,
                    'style_size'  => $style_size,
                    'area'        => $validate['area'] ?? '',
                    'inisial'     => $validate['inisial'] ?? '',
                    'color'       => $color,
                    'item_type'   => $item_type,
                    'kode_warna'  => $kode_warna,
                    'composition' => $sheet->getCell($headerMap['Composition(%)'] . $rowIndex)->getValue(),
                    'gw'          => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(),
                    'qty_pcs'     => $qty_pcs,
                    'loss'        => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue() ?? 0,
                    'kgs'         => $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue(),
                    'admin'       => $admin,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                // Jika composite key sudah ada, update data material lama
                if (isset($existingKeys[$key])) {
                    $materialModel->update($existingKeys[$key]['id_material'], $materialData);
                } else {
                    // Jika belum ada, tambahkan created_at untuk data baru dan insert
                    $materialData['created_at'] = date('Y-m-d H:i:s');
                    $materialModel->insert($materialData);
                }
            }

            // Setelah proses import, update data lama yang tidak ada di file revisi
            foreach ($existingMaterials as $material) {
                $oldStyleSize = strtoupper(trim($material['style_size']));
                $oldItemType  = strtoupper(trim($material['item_type']));
                $oldKodeWarna = strtoupper(trim($material['kode_warna']));
                $oldColor     = strtoupper(trim($material['color']));

                $existingKey = $oldStyleSize . '_' . $oldItemType . '_' . $oldKodeWarna . '_' . $oldColor;
                if (!in_array($existingKey, $newMaterialKeys)) {
                    $updateData = [
                        'qty_pcs'     => null,
                        'gw'          => null,
                        'loss'        => null,
                        'kgs'         => null,
                        'composition' => null,
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ];
                    $materialModel->update($material['id_material'], $updateData);
                }
            }

            return redirect()->back()->with('success', 'Data revisi MU berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat merevisi data: ' . $e->getMessage());
        }
    }

    public function material($id)
    {
        $id_order = $id; // Ambil id_order dari URL
        if (!$id_order) {
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'ID Order tidak ditemukan.');
        }
        $itemType = $this->masterMaterialModel->getItemType();
        $orderData = $this->materialModel->getMaterial($id_order);

        if (empty($orderData)) {
            session()->setFlashdata('error', 'Data Material tidak ditemukan! Silakan impor ulang data.');
            return redirect()->to(base_url($this->role . '/masterdata'));
        }

        $areaData = array_column($orderData, 'area');
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
            'id_order' => $id_order,
            'itemType' => $itemType,
            'area' => $areaData,
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
            return redirect()->to(base_url($this->role . '/material/' . $idorder))->with('success', 'Data Berhasil dihapus.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $idorder))->with('error', 'Data gagal dihapus.');
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
        // dd($data);
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
            $this->openPoModel->insert($itemData);
        }

        return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data PO Berhasil Di Tambahkan.');
    }

    public function reportMasterOrder()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/masterdata/report-master-order', $data);
    }

    public function filterMasterOrder()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->masterOrderModel->getFilterMasterOrder($key, $tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
}
