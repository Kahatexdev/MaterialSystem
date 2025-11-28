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
use App\Models\OpenPoModel;
use App\Models\EstimasiStokModel;
use App\Models\MasterMaterialTypeModel;
use App\Models\StockModel;
use App\Models\TrackingPoCovering;
use App\Models\PoTambahanModel;
use App\Models\ScheduleCelupModel;

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
    protected $trackingPoCoveringModel;
    protected $poTambahanModel;
    protected $scheduleCelupModel;
    protected $masterMaterialTypeModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->estimasiStokModel = new EstimasiStokModel();
        $this->openPoModel = new OpenPoModel();
        $this->stockModel = new StockModel();
        $this->trackingPoCoveringModel = new TrackingPoCovering();
        $this->poTambahanModel = new PoTambahanModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->masterMaterialTypeModel = new MasterMaterialTypeModel();

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
        return view($this->role . '/masterdata/index', $data);
    }
    public function indexmon()
    {
        $masterOrder = $this->masterOrderModel->orderBy('id_order', 'DESC')->findAll();
        $material = $this->materialModel->findAll();
        // Ambil semua id_order dari material
        $materialOrderIds = array_column($material, 'id_order');
        $duplikatMU = $this->materialModel->getDataDuplicate();
        $duplikatIds = array_map(function ($row) {
            return $row->id_order;
        }, $duplikatMU);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'masterOrder' => $masterOrder,
            'material' => $material,
            'materialOrderIds' => $materialOrderIds,
            'duplikatMU' => $duplikatIds
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
        $jarum = $this->request->getPost('jarum');
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
                    $style_size = $sheet->getCell('E' . $key)->getValue();
                    if (!empty($no_model) && !empty($style_size)) {
                        $validate = $this->validateWithAPI($no_model, $style_size);
                        if ($validate) {
                            // break; // Gunakan validasi dari baris pertama yang valid
                            continue; // Lanjutkan untuk mencari baris berikutnya yang valid
                        }
                    }
                }

                if (is_array($validate) && isset($validate['factory']) && $validate['factory'] == 'Belum Ada Area') {
                    $unit = 'Belum di Assign';
                } elseif (is_array($validate) && isset($validate['factory']) && strpos($validate['factory'], 'GEDUNG') !== false) {
                    $unit = 'MAJALAYA';
                } elseif (is_array($validate) && isset($validate['factory']) && strpos($validate['factory'], 'KK') !== false) {
                    $unit = 'CIJERAH';
                } else {
                    $unit = 'Belum di Assign';
                }

                // if (!$validate) {
                //     return redirect()->back()->with('error', 'Validasi master order gagal, tidak ditemukan style size yang valid.');
                // }
                // Siapkan data master order
                $masterData = [
                    'no_order'       => $no_order,
                    'no_model'       => $no_model,
                    'buyer'          => $buyer,
                    'foll_up'        => $foll_up,
                    'lco_date'       => $lco_date,
                    'memo'           => NULL,
                    'jarum'           => $jarum,
                    'delivery_awal'  => $validate['delivery_awal'] ?? NULL,
                    'delivery_akhir' => $validate['delivery_akhir'] ?? NULL,
                    'unit'           => $unit ?? NULL,
                    'admin'          => $admin,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => NULL,
                ];
                // dd ($masterData);
                $masterOrderModel->insert($masterData);
            }
            // dd($orderExists);
            // else {
            //     return redirect()->back()->with('error', 'Data dengan No Model ' . $orderExists['no_model'] . ' sudah ada di database.');
            // }

            // Dapatkan id_order untuk digunakan pada tabel material
            $orderData = $masterOrderModel->findIdOrder($no_order, $no_model);
            if (!$orderData) {
                return redirect()->back()->with('error', 'Gagal menemukan ID Order untuk ' . $no_order);
            }
            $id_order = $orderData['id_order'];
            // Mapping header untuk data material
            $headerMap = [
                'Color'          => 'A',
                'Material Nr'    => 'B',
                'Item Type'      => 'C',
                'Kode Warna'     => 'D',
                'Item Nr'        => 'E',
                'Composition(%)' => 'F',
                'GW/pc'          => 'G',
                'Qty/pcs'        => 'H',
                'Loss'           => 'I',
                'Kgs'            => 'J',
            ];

            $validDataMaterial = [];
            $invalidRows       = [];

            // Iterasi baris data material (misalnya mulai dari baris kedua)
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowIndex  = $row->getRowIndex();
                // Ambil dan validasi style_size
                $style_size = $sheet->getCell('E' . $rowIndex)->getValue();
                if (empty($no_model) || empty($style_size)) {
                    $invalidRows[] = $rowIndex;
                    continue;
                }

                $validate = $this->validateWithAPI($no_model, $style_size);

                $final_style_size = $validate['size'] ?? $style_size;
                $final_area       = $validate['area'] ?? null;
                $final_inisial    = $validate['inisial'] ?? null;

                // Ambil dan sanitasi item type
                $item_type = trim($sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue());
                if (empty($item_type)) {
                    return redirect()->back()->with('error', 'Item Type tidak boleh kosong pada baris ' . $rowIndex);
                }
                $item_type = htmlspecialchars($item_type, ENT_QUOTES, 'UTF-8');

                // Cek apakah item type ada di database
                $checkItemType = $masterMaterialModel->checkItemType($item_type);
                if (!$checkItemType) {
                    continue;
                }

                // Ambil nilai qty_pcs dan bersihkan dari pemisah ribuan
                $qty_raw = $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue();
                $qty_pcs = intval(str_replace([',', '.'], '', $qty_raw));
                $kgs_raw = $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue();
                $kgs = floatval(str_replace([','], '', $kgs_raw));

                // Siapkan data material
                $validDataMaterial[] = [
                    'id_order'      => $id_order,
                    'material_nr'   => $sheet->getCell($headerMap['Material Nr'] . $rowIndex)->getValue(),
                    'style_size'    => $final_style_size,
                    'area'          => $final_area,
                    'inisial'       => $final_inisial,
                    'color'         => $sheet->getCell($headerMap['Color'] . $rowIndex)->getValue(),
                    'item_type'     => htmlspecialchars_decode($item_type),
                    'kode_warna'    => $sheet->getCell($headerMap['Kode Warna'] . $rowIndex)->getValue(),
                    'composition'   => $sheet->getCell($headerMap['Composition(%)'] . $rowIndex)->getValue(),
                    'gw'            => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(),
                    'gw_aktual'     => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(), // Asumsikan gw_aktual sama dengan gw
                    'qty_pcs'       => $qty_pcs, // Menggunakan variabel yang telah diproses
                    'loss'          => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue() ?? 0,
                    'kgs'           => number_format($kgs, 2, '.', ''),
                    'admin'         => $admin,
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }
            // dd($validDataMaterial);
            // ================================================
            // VALIDASI DI AKHIR SEBELUM INSERT MATERIAL
            // ================================================

            // 1. Pastikan ada data material yang valid
            if (empty($validDataMaterial)) {
                return redirect()->back()->with('error', 'Tidak ada data material valid untuk di‐insert.');
            }

            // 2. Cek duplikat material untuk order ini
            $duplicateRows = [];
            foreach ($validDataMaterial as $idx => $mat) {
                $exists = $materialModel
                    ->where('id_order', $mat['id_order'])
                    ->where('style_size', $mat['style_size'])
                    ->where('item_type', $mat['item_type'])
                    ->where('kode_warna', $mat['kode_warna'])
                    ->first();
                if ($exists) {
                    // catat index (baris ke‐berapa di array validDataMaterial)
                    $duplicateRows[] = $idx + 1;
                }
            }
            if (!empty($duplicateRows)) {
                return redirect()->back()->with(
                    'error',
                    'Terdapat material yang sudah ada di DB untuk order ini (baris ke‐array: '
                        . implode(', ', $duplicateRows) . ').'
                );
            }

            // 3. Jika lolos validasi, insert batch
            $materialModel->insertBatch($validDataMaterial);

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

    // public function reviseMU()
    // {
    //     // Ambil file yang diupload dan validasi file
    //     $file = $this->request->getFile('file');
    //     if (!$file || !$file->isValid()) {
    //         return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
    //     }

    //     // (Opsional) Cek ekstensi file jika diperlukan
    //     $allowedExtensions = ['xls', 'xlsx', 'csv'];
    //     if (!in_array($file->getClientExtension(), $allowedExtensions)) {
    //         return redirect()->back()->with('error', 'File yang diupload harus berformat Excel atau CSV.');
    //     }

    //     // Inisialisasi model-model yang dibutuhkan
    //     $masterOrderModel    = new MasterOrderModel();
    //     $materialModel       = new MaterialModel();
    //     $masterMaterialModel = new MasterMaterialModel();

    //     // Ambil username admin dari session
    //     $admin = session()->get('username');
    //     if (!$admin) {
    //         return redirect()->back()->with('error', 'Session expired. Please log in again.');
    //     }

    //     try {
    //         // Load file Excel/CSV dan ambil sheet aktif
    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
    //         $sheet       = $spreadsheet->getActiveSheet();

    //         // Ambil no_model dari file Excel (misalnya dari sel B9)
    //         $no_model = str_replace([': '], '', $sheet->getCell('B9')->getValue());
    //         if (empty($no_model)) {
    //             return redirect()->back()->with('error', 'No Model tidak ditemukan di file.');
    //         }

    //         // Cari master order berdasarkan no_model
    //         $masterOrder = $masterOrderModel->where('no_model', $no_model)->first();
    //         if (!$masterOrder) {
    //             return redirect()->back()->with('error', 'Master order dengan No Model ' . $no_model . ' tidak ditemukan.');
    //         }
    //         $id_order = $masterOrder['id_order'];

    //         // --- Update data master_order berdasarkan header file Excel ---
    //         $no_order    = str_replace([': '], '', $sheet->getCell('B5')->getValue());
    //         if (empty($no_order)) {
    //             return redirect()->back()->with('error', 'No Order tidak ditemukan di file.');
    //         }
    //         $buyer       = str_replace([': '], '', $sheet->getCell('B6')->getValue());
    //         $lco_dateRaw = str_replace([': '], '', $sheet->getCell('B4')->getFormattedValue());
    //         $foll_up     = str_replace([': '], '', $sheet->getCell('D5')->getValue());

    //         // Validasi format tanggal
    //         $date_object = DateTime::createFromFormat('d.m.Y', $lco_dateRaw);
    //         if ($date_object) {
    //             $lco_date = $date_object->format('Y-m-d');
    //         } else {
    //             return redirect()->back()->with('error', 'Format tanggal LCO tidak valid.');
    //         }

    //         $masterDataUpdate = [
    //             'no_order'  => $no_order,
    //             'buyer'     => $buyer,
    //             'lco_date'  => $lco_date,
    //             'foll_up'   => $foll_up,
    //             'admin'     => $admin,
    //             'updated_at' => date('Y-m-d H:i:s'),
    //         ];
    //         $masterOrderModel->update($id_order, $masterDataUpdate);
    //         // --- End update master_order ---

    //         // Ambil data material lama (yang sudah ada di DB) untuk master order ini
    //         $existingMaterials = $materialModel->where('id_order', $id_order)->findAll();
    //         $existingKeys = [];
    //         foreach ($existingMaterials as $material) {
    //             // Normalisasi data untuk composite key
    //             $oldStyleSize = strtoupper(trim($material['style_size']));
    //             $oldItemType  = strtoupper(trim($material['item_type']));
    //             $oldKodeWarna = strtoupper(trim($material['kode_warna']));
    //             $oldColor     = strtoupper(trim($material['color']));

    //             // Composite key: style_size_itemType_kodeWarna_color
    //             $existingKey = $oldStyleSize . '_' . $oldItemType . '_' . $oldKodeWarna . '_' . $oldColor;
    //             $existingKeys[$existingKey] = $material;
    //         }

    //         // Mapping header sesuai format file Excel
    //         $headerMap = [
    //             'Color'          => 'A',
    //             'Item Type'      => 'B',
    //             'Kode Warna'     => 'C',
    //             'Item Nr'        => 'D', // berisi style_size
    //             'Composition(%)' => 'E',
    //             'GW/pc'          => 'F',
    //             'Qty/pcs'        => 'G',
    //             'Loss'           => 'H',
    //             'Kgs'            => 'I',
    //         ];

    //         // Array untuk menyimpan composite key dari file Excel revisi
    //         $newMaterialKeys = [];

    //         // Iterasi baris data material (misalnya mulai dari baris 15)
    //         foreach ($sheet->getRowIterator(2) as $row) {
    //             $rowIndex  = $row->getRowIndex();
    //             if ($rowIndex < 14) {
    //                 continue;
    //             }

    //             // Ambil raw data untuk style_size
    //             $style_sizeRaw = $sheet->getCell($headerMap['Item Nr'] . $rowIndex)->getValue();
    //             if (empty($style_sizeRaw)) {
    //                 // Lewati baris tanpa style_size
    //                 continue;
    //             }

    //             // (Opsional) Cek apakah style_size mengandung 'X'
    //             if (stripos($style_sizeRaw, 'X') === false) {
    //                 log_message('error', 'Baris ' . $rowIndex . ' tidak mengandung X: ' . $style_sizeRaw);
    //                 continue;
    //             }

    //             // Validasi dengan API (pastikan respons valid dan memiliki format yang diharapkan)
    //             $validate = $this->validateWithAPI($no_model, $style_sizeRaw);
    //             if (!$validate || !isset($validate['size'])) {
    //                 // Catat error, tapi lanjutkan ke baris berikutnya tanpa return
    //                 log_message('error', 'Data StyleSize pada baris ke-' . $rowIndex . ': ' . $style_sizeRaw . ' tidak valid atau tidak ditemukan di CapacityApps.');
    //                 continue;
    //             }

    //             // Normalisasi style_size dari API
    //             $style_size = strtoupper(trim($validate['size'] ?? $style_sizeRaw));

    //             // Ambil dan validasi item type
    //             $raw_item_type = $sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue();
    //             if (empty($raw_item_type)) {
    //                 return redirect()->back()->with('error', 'Item Type tidak boleh kosong pada baris ' . $rowIndex);
    //             }
    //             $item_type = strtoupper(trim($raw_item_type));
    //             if (!$masterMaterialModel->checkItemType($item_type)) {
    //                 return redirect()->back()->with('error', $item_type . ' tidak ada di database pada baris ' . $rowIndex);
    //             }

    //             // Normalisasi data lain
    //             $kode_warna = strtoupper(trim($sheet->getCell($headerMap['Kode Warna'] . $rowIndex)->getValue()));
    //             $color      = strtoupper(trim($sheet->getCell($headerMap['Color'] . $rowIndex)->getValue()));

    //             // Buat composite key baru
    //             $key = $style_size . '_' . $item_type . '_' . $kode_warna . '_' . $color;
    //             $newMaterialKeys[] = $key;

    //             // Validasi nilai numeric untuk Qty, GW, dan Kgs
    //             $qty_raw = $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue();
    //             $qty_pcs = intval(str_replace([',', '.'], '', $qty_raw));
    //             if (!is_numeric($qty_pcs)) {
    //                 return redirect()->back()->with('error', 'Qty/pcs tidak valid pada baris ' . $rowIndex);
    //             }

    //             // Siapkan data material baru dari file Excel
    //             $materialData = [
    //                 'id_order'    => $id_order,
    //                 'style_size'  => $style_size,
    //                 'area'        => $validate['area'] ?? NULL,
    //                 'inisial'     => $validate['inisial'] ?? NULL,
    //                 'color'       => $color,
    //                 'item_type'   => $item_type,
    //                 'kode_warna'  => $kode_warna,
    //                 'composition' => $sheet->getCell($headerMap['Composition(%)'] . $rowIndex)->getValue(),
    //                 'gw'          => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(),
    //                 'gw_aktual'   => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(), // Asumsikan gw_aktual sama dengan gw
    //                 'qty_pcs'     => $qty_pcs,
    //                 'loss'        => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue() ?? 0,
    //                 'kgs'         => $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue(),
    //                 'admin'       => $admin,
    //                 'updated_at'  => date('Y-m-d H:i:s'),
    //             ];

    //             // Jika composite key sudah ada, update data material lama
    //             if (isset($existingKeys[$key])) {
    //                 $materialModel->update($existingKeys[$key]['id_material'], $materialData);
    //             } else {
    //                 // Jika belum ada, tambahkan created_at untuk data baru dan insert
    //                 $materialData['created_at'] = date('Y-m-d H:i:s');
    //                 $materialModel->insert($materialData);
    //             }
    //         }

    //         // Setelah proses import, update data lama yang tidak ada di file revisi
    //         // foreach ($existingMaterials as $material) {
    //         //     $oldStyleSize = strtoupper(trim($material['style_size']));
    //         //     $oldItemType  = strtoupper(trim($material['item_type']));
    //         //     $oldKodeWarna = strtoupper(trim($material['kode_warna']));
    //         //     $oldColor     = strtoupper(trim($material['color']));

    //         //     $existingKey = $oldStyleSize . '_' . $oldItemType . '_' . $oldKodeWarna . '_' . $oldColor;
    //         //     if (!in_array($existingKey, $newMaterialKeys)) {
    //         //         $updateData = [
    //         //             'qty_pcs'     => null,
    //         //             'gw'          => null,
    //         //             'loss'        => null,
    //         //             'kgs'         => null,
    //         //             'composition' => null,
    //         //             'updated_at'  => date('Y-m-d H:i:s'),
    //         //         ];
    //         //         $materialModel->update($material['id_material'], $updateData);
    //         //     }
    //         // }

    //         return redirect()->back()->with('success', 'Data revisi MU berhasil diperbarui.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat merevisi data: ' . $e->getMessage());
    //     }
    // }

    /**
     * Mendeteksi baris & kolom header berdasarkan alias label.
     * @return array [int|null $headerRow, array $colMap field => [colLetter, foundLabel]]
     */
    private function detectHeader(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $aliases): array
    {
        // Scan 1..50 baris pertama untuk cari header
        $maxScanRows = min(70, $sheet->getHighestRow());
        $highestCol  = $sheet->getHighestColumn();
        $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);

        $normLabel = function ($v) {
            $v = is_scalar($v) ? (string)$v : '';
            $v = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $v)), 'UTF-8');
            return $v;
        };

        for ($row = 1; $row <= $maxScanRows; $row++) {
            // Ambil satu baris kandidat header
            $labels = [];
            for ($col = 1; $col <= $maxColIndex; $col++) {
                $addr   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                $val    = $sheet->getCell($addr) ? $sheet->getCell($addr)->getCalculatedValue() : '';
                $labels[$col] = $normLabel($val);
            }

            // Cocokkan minimal beberapa alias kunci
            $found = [];
            foreach ($aliases as $field => $cands) {
                $candsNorm = array_map($normLabel, $cands);
                $found[$field] = null;
                foreach ($labels as $colIdx => $lab) {
                    if ($lab === '') continue;
                    if (in_array($lab, $candsNorm, true)) {
                        $found[$field] = $colIdx;
                        break;
                    }
                }
            }

            // Syarat minimal: field inti ketemu
            $must = ['color', 'material_nr', 'item_type', 'kode_warna', 'style_size', 'qty_pcs'];
            $ok = true;
            foreach ($must as $m) {
                if (!isset($found[$m]) || $found[$m] === null) {
                    $ok = false;
                    break;
                }
            }
            if (!$ok) continue;

            // Build colMap
            $colMap = [];
            foreach ($found as $field => $idx) {
                if ($idx === null) continue;
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx);
                $colMap[$field] = [$colLetter, $labels[$idx]];
            }

            return [$row, $colMap];
        }

        return [null, []];
    }

    /** Tetap dipakai dari kode lama agar kompatibel */
    private function removeLabelIfAny($v)
    {
        $v = is_scalar($v) ? (string)$v : '';
        return preg_replace('/^[^:]*:\s*/', '', trim($v));
    }


    public function reviseMU()
    {
        // ===== 0) Validasi request & session =====
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file diupload atau file invalid.');
        }

        $allowedExt = ['xls', 'xlsx', 'csv'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExt, true)) {
            return redirect()->back()->with('error', 'File harus Excel/CSV.');
        }

        $admin = session()->get('username');
        if (!$admin) {
            return redirect()->back()->with('error', 'Session expired. Silakan login ulang.');
        }

        // ===== 1) Model =====
        $masterOrderModel    = new MasterOrderModel();
        $materialModel       = new MaterialModel();
        $masterMaterialModel = new MasterMaterialModel();

        // ===== 2) Konstanta & Util =====
        // Jika TRUE → material yang tidak ada lagi di file revisi akan di-soft delete (nonaktif)
        // Jika FALSE → dibiarkan (tidak dihapus).
        $SOFT_DELETE_REMOVED = true;

        $norm = static function ($v) {
            $v = is_scalar($v) ? (string)$v : '';
            $v = trim(preg_replace('/\s+/u', ' ', $v));
            return mb_strtoupper($v, 'UTF-8');
        };
        $stripLabel = static function ($v) {
            $v = is_scalar($v) ? (string)$v : '';
            return preg_replace('/^[^:]*:\s*/', '', trim($v));
        };
        $parseExcelDate = static function ($raw) {
            if ($raw === null || $raw === '') return null;

            // numeric serial → via PhpSpreadsheet helper
            if (is_numeric($raw)) {
                try {
                    $dt = ExcelDate::excelToDateTimeObject($raw);
                    return $dt ? $dt->format('Y-m-d') : null;
                } catch (\Throwable $e) {
                    // fallback
                }
            }

            // coba format umum
            $try = date_create((string)$raw);
            return $try ? $try->format('Y-m-d') : null;
        };
        $toInt = static function ($raw) {
            if ($raw === null || $raw === '') return null;
            $clean = preg_replace('/[^\d\-]/', '', (string)$raw);
            if ($clean === '' || $clean === '-') return null;
            return (int)$clean;
        };
        $toFloat = static function ($raw) {
            if ($raw === null || $raw === '') return 0.0;
            // ganti koma → titik
            $v = str_replace(',', '.', (string)$raw);
            // buang karakter non numerik kecuali . dan -
            $v = preg_replace('/[^0-9\.\-]/', '', $v);
            return (float)$v;
        };

        // ===== 3) Load spreadsheet (read-only) =====
        try {
            $reader = IOFactory::createReaderForFile($file->getTempName());
            if (method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(true);
            }
            $spreadsheet = $reader->load($file->getTempName());
            $sheet       = $spreadsheet->getActiveSheet();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuka file: ' . $e->getMessage());
        }

        // ===== 4) Ambil header Master Order (lebih toleran lokasi sel) =====
        try {
            // Flexible: cari di beberapa kandidat sel (fallback ke pencarian label di sekitar)
            $c_get = fn($addr) => $sheet->getCell($addr) ? ($sheet->getCell($addr)->getCalculatedValue() ?? '') : '';
            $c_getFmt = fn($addr) => $sheet->getCell($addr) ? ($sheet->getCell($addr)->getFormattedValue() ?? '') : '';

            // Lokasi default sesuai kode awal
            $raw_no_model = $stripLabel($c_get('B9'));
            $raw_no_order = $stripLabel($c_get('B5'));
            $raw_buyer    = $stripLabel($c_get('B6'));
            $raw_lco      = $stripLabel($c_getFmt('B4'));
            $raw_foll_up  = $stripLabel($c_get('D5'));

            $no_model = $norm($raw_no_model);
            if ($no_model === '') {
                return redirect()->back()->with('error', 'No Model tidak ditemukan (coba cek cell B9).');
            }

            $masterOrder = $masterOrderModel->where('no_model', $no_model)->first();
            if (!$masterOrder) {
                return redirect()->back()->with('error', 'Master order No Model ' . $no_model . ' tidak ditemukan.');
            }
            $id_order = (int)$masterOrder['id_order'];

            $no_order = $norm($raw_no_order);
            if ($no_order === '') {
                return redirect()->back()->with('error', 'No Order kosong (B5).');
            }

            $lco_date = $parseExcelDate($raw_lco);
            if (!$lco_date) {
                return redirect()->back()->with('error', 'Tanggal LCO tidak valid (B4).');
            }

            $buyer   = $norm($raw_buyer);
            $foll_up = $norm($raw_foll_up);

            $masterDataUpdate = [
                'no_order'   => $no_order,
                'buyer'      => $buyer,
                'lco_date'   => $lco_date,
                'foll_up'    => $foll_up,
                'admin'      => $admin,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membaca header MU: ' . $e->getMessage());
        }

        // ===== 5) Siapkan peta existing materials & index =====
        // Composite key: style_size|item_type|kode_warna|color (semua di-normalize uppercase)
        // ===== 5) Siapkan peta existing materials & index =====
        // Ambil kolom lengkap agar bisa dibandingkan
        $existingRows = $materialModel
            ->select('id_material, material_nr, style_size, item_type, kode_warna, color, composition, gw, qty_pcs, loss, kgs')
            ->where('id_order', $id_order)
            ->findAll();

        // key komposit → row lama
        $existingMap = [];    // key => row array
        // index by style_size agar bisa deteksi “ganti key” (insert baru tapi style sama)
        $byStyleIndex = [];   // style_size => array of row arrays

        foreach ($existingRows as $r) {
            $key = implode('|', [
                $norm($r['style_size']),
                $norm($r['item_type']),
                $norm($r['kode_warna']),
                $norm($r['color']),
            ]);
            $existingMap[$key] = $r;

            $ss = $norm($r['style_size']);
            $byStyleIndex[$ss][] = $r;
        }

        $changedRows = [];    // list perubahan per baris update
        $changedTally = [      // agregat per kolom utk ringkasan
            'color' => 0,
            'materialnr' => 0,
            'itemtype' => 0,
            'kodewarna' => 0,
            'itemNR' => 0,
            'composition' => 0,
            'gw' => 0,
            'qty' => 0,
            'loss' => 0,
            'kgs' => 0,
        ];
        // helper map dari nama kolom DB → label yang kamu minta
        $LABELS = [
            'color' => 'color',
            'material_nr' => 'materialnr',
            'item_type' => 'itemtype',
            'kode_warna' => 'kodewarna',
            'style_size' => 'itemNR',
            'composition' => 'composition',
            'gw' => 'gw',
            'qty_pcs' => 'qty',
            'loss' => 'loss',
            'kgs' => 'kgs',
        ];
        // pembanding angka (hindari noise 0 vs 0.0, dan string vs float)
        $eq = function ($a, $b) {
            if (is_numeric($a) || is_numeric($b)) return (float)$a === (float)$b;
            return (string)$a === (string)$b;
        };
        $fmtNum = function ($v, int $dec = 2) {
            if ($v === null || $v === '') return '—';
            // pastikan numeric → format dengan titik desimal
            return is_numeric($v) ? number_format((float)$v, $dec, '.', '') : (string)$v;
        };


        // ===== 6) Auto-detect header kolom tabel detail =====
        // Cari baris header dengan label minimal: Color, Item Type, Kode Warna, Item Nr, Qty/pcs
        $headerAliases = [
            'color'           => ['color', 'warna'],
            'material_nr'    => ['material nr', 'materialnr', 'material no', 'material'],
            'item_type'       => ['item type', 'itemtype', 'tipe item'],
            'kode_warna'      => ['kode warna', 'kode_warna', 'code color', 'color code'],
            'style_size'      => ['item nr', 'itemnr', 'style_size', 'style size', 'item no', 'style'],
            'composition'     => ['composition(%)', 'composition', 'compo', 'komposisi'],
            'gw'              => ['gw/pc', 'gw', 'gw per pc', 'gross weight'],
            'qty_pcs'         => ['qty/pcs', 'qty pcs', 'qty', 'pcs'],
            'loss'            => ['loss', 'los'],
            'kgs'             => ['kgs', 'kg', 'kilogram'],
        ];

        [$headerRow, $colMap] = $this->detectHeader($sheet, $headerAliases);
        if ($headerRow === null) {
            return redirect()->back()->with('error', 'Header tabel detail tidak ditemukan. Pastikan ada kolom: Color,Material NR, Item Type, Kode Warna, Item Nr, Qty/pcs, dll.');
        }
        $dataStartRow = $headerRow + 1;

        // ===== 7) Persist: Transaksi + batch upsert =====
        $db   = \Config\Database::connect();
        $now  = date('Y-m-d H:i:s');
        $errs = [];
        $stats = ['insert' => 0, 'update' => 0, 'skip' => 0, 'disabled' => 0];

        // Cache untuk menghindari hit DB/API berulang
        $validItemTypes = $masterMaterialModel->select('item_type')
            ->groupBy('item_type')->findColumn('item_type');
        $validItemTypesNorm = array_fill_keys(array_map($norm, (array)$validItemTypes), true);

        $apiCache = []; // key: no_model|raw_style_size_norm -> ['size'=>.., 'area'=>.., 'inisial'=>..]

        // untuk mendeteksi material yang "hilang" di revisi
        $seenKeys = [];

        // kumpulkan insert/update lalu batch per CHUNK
        $BATCH_SIZE = 500;
        $toInsert = [];
        $toUpdate = [];

        $db->transStart();
        try {
            // 7a) update master_order
            $masterOrderModel->update($id_order, $masterDataUpdate);

            $highestRow = $sheet->getHighestRow();
            for ($row = $dataStartRow; $row <= $highestRow; $row++) {

                // Baca nilai kolom dengan aman
                $getVal = function (string $field) use ($sheet, $colMap, $row) {
                    if (!isset($colMap[$field])) return '';
                    [$colLetter] = $colMap[$field];
                    $cell = $sheet->getCell($colLetter . $row);
                    return $cell ? $cell->getCalculatedValue() : '';
                };

                // Baca raw values
                $raw_material_nr = (string)$getVal('material_nr');
                $raw_style_size = (string)$getVal('style_size');
                $raw_item_type  = (string)$getVal('item_type');
                $raw_kode       = (string)$getVal('kode_warna');
                $raw_color      = (string)$getVal('color');

                // Deteksi baris kosong total → skip
                if ($raw_material_nr === '' && $raw_style_size === '' && $raw_item_type === '' && $raw_kode === '') {
                    $stats['skip']++;
                    continue;
                }

                // Validasi minimal
                if ($raw_style_size === '') {
                    $errs[] = "Baris $row: Item Nr (style_size) kosong.";
                    $stats['skip']++;
                    continue;
                }

                // Wajib mengandung 'X'?
                if (stripos($raw_style_size, 'X') === false) {
                    // log & skip (sesuai versi awalmu)
                    log_message('warning', "Baris $row: style_size tidak mengandung 'X' => {$raw_style_size}");
                    $stats['skip']++;
                    continue;
                }

                // Validasi ke API CapacityApps (cache per style_size)
                $styleKey = $no_model . '|' . $norm($raw_style_size);
                if (!isset($apiCache[$styleKey])) {
                    try {
                        $apiRes = $this->validateWithAPI($no_model, $raw_style_size);
                    } catch (\Throwable $e) {
                        $apiRes = null;
                    }
                    $apiCache[$styleKey] = $apiRes ?: null;
                }
                $apiData = $apiCache[$styleKey];
                if (!$apiData || !isset($apiData['size'])) {
                    $errs[] = "Baris $row: StyleSize '{$raw_style_size}' tidak valid/tidak ditemukan di CapacityApps.";
                    $stats['skip']++;
                    continue;
                }

                // Normalisasi kunci
                $material_nr = $norm($raw_material_nr);
                $style_size = $norm($apiData['size']);
                $item_type  = $norm($raw_item_type ?: '');
                if ($item_type === '' || !isset($validItemTypesNorm[$item_type])) {
                    $errs[] = "Baris $row: Item Type '{$item_type}' tidak ada di MasterMaterial.";
                    $stats['skip']++;
                    continue;
                }

                $kode_warna = $norm($raw_kode);
                $color      = $norm($raw_color);

                // Numerik
                $qty_pcs = $toInt($getVal('qty_pcs'));
                if ($qty_pcs === null) {
                    $errs[] = "Baris $row: Qty/pcs tidak valid.";
                    $stats['skip']++;
                    continue;
                }
                $composition = (string)$getVal('composition');
                $gw          = $toFloat($getVal('gw'));
                $loss        = $toFloat($getVal('loss'));
                $kgs         = $toFloat($getVal('kgs'));

                // composite key
                $key = implode('|', [$style_size, $item_type, $kode_warna, $color]);
                $seenKeys[$key] = true;

                $payload = [
                    'id_order'    => $id_order,
                    'material_nr' => $material_nr,
                    'style_size'  => $style_size,
                    'area'        => $apiData['area']    ?? null,
                    'inisial'     => $apiData['inisial'] ?? null,
                    'color'       => $color,
                    'item_type'   => $item_type,
                    'kode_warna'  => $kode_warna,
                    'composition' => $composition,
                    'gw'          => $gw,
                    'gw_aktual'   => $gw,
                    'qty_pcs'     => $qty_pcs,
                    'loss'        => $loss,
                    'kgs'         => $kgs,
                    'admin'       => $admin,
                    'updated_at'  => $now,
                ];

                if (isset($existingMap[$key])) {
                    $old = $existingMap[$key]; // row lama lengkap
                    $payload['id_material'] = (int)$old['id_material'];
                    $toUpdate[] = $payload;

                    $diffCols    = [];
                    $changesText = [];

                    // key fields (jarang berubah, tapi kita catat kalau iya)
                    if (!$eq($old['color'], $payload['color'])) {
                        $diffCols[]    = 'color';
                        $changesText[] = 'Color: <b>' . $old['color'] . '</b> → <b>' . $payload['color'] . '</b>';
                    }
                    if (!$eq($old['material_nr'], $payload['material_nr'])) {
                        $diffCols[]    = 'material_nr';
                        $changesText[] = 'MaterialNR: <b>' . htmlspecialchars($old['material_nr']) . '</b> → <b>' . htmlspecialchars($payload['material_nr']) . '</b>';
                    }

                    if (!$eq($old['item_type'], $payload['item_type'])) {
                        $diffCols[]    = 'item_type';
                        $changesText[] = 'ItemType: <b>' . $old['item_type'] . '</b> → <b>' . $payload['item_type'] . '</b>';
                    }
                    if (!$eq($old['kode_warna'], $payload['kode_warna'])) {
                        $diffCols[]    = 'kode_warna';
                        $changesText[] = 'Kode Warna: <b>' . $old['kode_warna'] . '</b> → <b>' . $payload['kode_warna'] . '</b>';
                    }
                    if (!$eq($old['style_size'], $payload['style_size'])) {
                        $diffCols[]    = 'style_size';
                        $changesText[] = 'ItemNR: <b>' . $old['style_size'] . '</b> → <b>' . $payload['style_size'] . '</b>';
                    }

                    // value fields (ini yang kamu minta)
                    if (!$eq($old['composition'], $payload['composition'])) {
                        $diffCols[]    = 'composition';
                        $changesText[] = 'Composition: <b>' . htmlspecialchars($old['composition']) . '</b> → <b>' . htmlspecialchars($payload['composition']) . '</b>';
                    }
                    if (!$eq($old['gw'], $payload['gw'])) {
                        $diffCols[]    = 'gw';
                        $changesText[] = 'GW: <b>'  . $fmtNum($old['gw'], 2)       . '</b> → <b>' . $fmtNum($payload['gw'], 2) . '</b>';
                    }
                    if (!$eq($old['qty_pcs'], $payload['qty_pcs'])) {
                        $diffCols[]    = 'qty_pcs';
                        $changesText[] = 'Qty: <b>' . $fmtNum($old['qty_pcs'], 0) . '</b> → <b>' . $fmtNum($payload['qty_pcs'], 0) . '</b>';
                    }
                    if (!$eq($old['loss'], $payload['loss'])) {
                        $diffCols[]    = 'loss';
                        $changesText[] = 'Loss: <b>' . $fmtNum($old['loss'], 2) . '</b> → <b>' . $fmtNum($payload['loss'], 2) . '</b>';
                    }
                    if (!$eq($old['kgs'], $payload['kgs'])) {
                        $diffCols[]    = 'kgs';
                        $changesText[] = 'Kgs: <b>' . $fmtNum($old['kgs'], 2) . '</b> → <b>' . $fmtNum($payload['kgs'], 2) . '</b>';
                    }

                    if ($diffCols) {
                        // tally
                        foreach ($diffCols as $c) {
                            $changedTally[$LABELS[$c]]++;
                        }

                        // simpan baris berubah + nilai lama (khusus yang kamu mau tampilkan)
                        $changedRows[] = [
                            'id_material'        => (int)$old['id_material'],
                            'material_nr'       => $payload['material_nr'],
                            'style_size'         => $payload['style_size'],
                            'item_type'          => $payload['item_type'],
                            'kode_warna'         => $payload['kode_warna'],
                            'color'              => $payload['color'],
                            'changed'            => array_map(fn($c) => $LABELS[$c], $diffCols),

                            // nilai lama (untuk konsumsi UI/CSV)
                            'prev_qty_pcs'       => $old['qty_pcs'],
                            'prev_gw'            => $old['gw'],
                            'prev_composition'   => $old['composition'],

                            // nilai baru (opsional, kalau mau dipakai juga)
                            'new_qty_pcs'        => $payload['qty_pcs'],
                            'new_gw'             => $payload['gw'],
                            'new_composition'    => $payload['composition'],

                            // kalimat ringkas lama → baru (buat alert)
                            'note'               => implode('; ', $changesText),
                        ];
                    }
                } else {
                    // INSERT BARU
                    $payload['created_at'] = $now;
                    $toInsert[] = $payload;
                }


                // flush per batch
                if (count($toUpdate) >= $BATCH_SIZE) {
                    $materialModel->updateBatch($toUpdate, 'id_material');
                    $stats['update'] += count($toUpdate);
                    $toUpdate = [];
                }
                if (count($toInsert) >= $BATCH_SIZE) {
                    $materialModel->insertBatch($toInsert);
                    $stats['insert'] += count($toInsert);
                    $toInsert = [];
                }
            }

            // sisa batch
            if ($toUpdate) {
                $materialModel->updateBatch($toUpdate, 'id_material');
                $stats['update'] += count($toUpdate);
                // add swall itemtype was update

            }
            if ($toInsert) {
                $materialModel->insertBatch($toInsert);
                $stats['insert'] += count($toInsert);
            }

            // 7b) Optional: soft delete material yang hilang di file revisi
            $removedRows = []; // simpan row lama lengkap untuk pairing
            if ($SOFT_DELETE_REMOVED) {
                $toDisableIds = [];
                foreach ($existingMap as $key => $rowOld) {
                    if (!isset($seenKeys[$key])) {
                        $toDisableIds[] = (int)$rowOld['id_material'];
                        $removedRows[]  = $rowOld; // simpan row lama (punya style_size, item_type, dll)
                    }
                }
                if ($toDisableIds) {
                    foreach (array_chunk($toDisableIds, $BATCH_SIZE) as $chunk) {
                        $rows = array_map(fn($id) => [
                            'id_material' => $id,
                            'composition' => 0,
                            'gw'          => 0,
                            'qty_pcs'     => 0,
                            'loss'        => 0,
                            'kgs'         => 0,
                            'updated_at'  => $now,
                            'admin'       => $admin,
                            // 'is_active' => 0, // kalau ada
                        ], $chunk);
                        $materialModel->updateBatch($rows, 'id_material');
                        $stats['disabled'] += count($rows);
                    }
                }
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi DB gagal diselesaikan.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal revisi MU: ' . $e->getMessage());
        }

        // ===== 8) Rekonstruksi "key shift" yang akurat (setelah kita tahu mana removed) =====

        // Helper kesetaraan
        $eq = function ($a, $b) {
            if (is_numeric($a) || is_numeric($b)) return (float)$a === (float)$b;
            return (string)$a === (string)$b;
        };


        // Kumpulkan list insert rows (kita sudah punya $toInsert di atas)
        // Tapi $toInsert dipakai batch, pastikan ia masih berisi semua insert yang dilakukan.
        // Jika kamu reset $toInsert setelah insertBatch, simpan salinannya di variabel lain (mis. $allInsertedRows) sebelum di-reset.
        $allInsertedRows = $allInsertedRows ?? $toInsert ?? []; // jika sebelumnya kamu sudah menyimpan

        // Group by style_size
        $insBySS = [];
        foreach ($allInsertedRows as $ins) {
            $ss = (string)$ins['style_size'];
            $insBySS[$ss][] = $ins;
        }
        $remBySS = [];
        foreach ($removedRows as $rem) {
            $ss = (string)$rem['style_size'];
            $remBySS[$ss][] = $rem;
        }

        // Fungsi skor kemiripan (prioritas kecocokan exact di key lain)
        $scoreOf = function (array $ins, array $rem) use ($eq) {
            $score = 0;
            // bobot: cocok item_type +2, kode_warna +2, color +1 (atur sesuai preferensi)
            if ($eq($ins['item_type'],  $rem['item_type']))  $score += 2;
            if ($eq($ins['kode_warna'], $rem['kode_warna'])) $score += 2;
            if ($eq($ins['color'],      $rem['color']))      $score += 1;
            return $score;
        };

        $noteUpdates = []; // <--- KUMPULKAN UPDATE KETERANGAN DI SINI

        // Pairing: untuk setiap insert, cari removed terbaik di SS yang sama
        foreach ($insBySS as $ss => $insList) {
            $remList = $remBySS[$ss] ?? [];
            if (!$remList) continue;

            // tandai removed yang sudah dipakai biar tidak dipakai ulang
            $used = array_fill(0, count($remList), false);

            foreach ($insList as $ins) {
                $bestIdx = -1;
                $bestScore = -1;
                foreach ($remList as $i => $rem) {
                    if ($used[$i]) continue;
                    $s = $scoreOf($ins, $rem);
                    if ($s > $bestScore) {
                        $bestScore = $s;
                        $bestIdx = $i;
                    }
                }
                if ($bestIdx < 0) continue; // tidak ada pasangan

                $used[$bestIdx] = true;
                $rem = $remList[$bestIdx];

                // Tentukan kolom key apa yang bergeser
                $diffCols = [];
                if (!$eq($rem['item_type'],  $ins['item_type']))  $diffCols[] = 'item_type';
                if (!$eq($rem['kode_warna'], $ins['kode_warna'])) $diffCols[] = 'kode_warna';
                if (!$eq($rem['color'],      $ins['color']))      $diffCols[] = 'color';

                if ($diffCols) {
                    // tally & catat baris perubahan (lebih akurat daripada versi on-insert)
                    foreach ($diffCols as $c) {
                        $changedTally[$LABELS[$c]]++;
                    }

                    $changesText = [];
                    if (in_array('item_type', $diffCols, true)) {
                        $changesText[] = 'ItemType: ' . $rem['item_type']  . ' → ' . $ins['item_type'];
                    }
                    if (in_array('kode_warna', $diffCols, true)) {
                        $changesText[] = 'Kode Warna: ' . $rem['kode_warna'] . ' → ' . $ins['kode_warna'];
                    }
                    if (in_array('color', $diffCols, true)) {
                        $changesText[] = 'Color: ' . $rem['color'] . ' → ' . $ins['color'];
                    }

                    $changedRows[] = [
                        'id_material'     => null,
                        'style_size'      => $ins['style_size'],
                        'item_type'       => $ins['item_type'],
                        'kode_warna'      => $ins['kode_warna'],
                        'color'           => $ins['color'],
                        'prev_item_type'  => $rem['item_type'],
                        'prev_kode_warna' => $rem['kode_warna'],
                        'prev_color'      => $rem['color'],
                        'changed'         => array_map(fn($c) => $LABELS[$c], $diffCols),
                        'note'            => implode('; ', $changesText),
                    ];
                }
                // Daripada update per baris langsung, kumpulkan dulu:
                $noteUpdates[] = [
                    'id_material' => (int)$rem['id_material'],
                    'keterangan'  => 'Revisi MU: dipindah → '
                        . 'Color=' . ($ins['color'] ?? '-')
                        . ', ItemType=' . ($ins['item_type'] ?? '-')
                        . ', KodeWarna=' . ($ins['kode_warna'] ?? '-')
                        . ', StyleSize=' . ($ins['style_size'] ?? '-'),
                ];
            }
        }
        // Setelah selesai pairing SEMUA, eksekusi batch update "keterangan":
        if (!empty($noteUpdates)) {
            foreach (array_chunk($noteUpdates, $BATCH_SIZE) as $chunk) {
                $materialModel->updateBatch($chunk, 'id_material');
            }
        }
        // ===== 8) Hasil (versi <ul><li>) =====
        $summaryLis = [];
        foreach (['color', 'materialnr', 'itemtype', 'kodewarna', 'itemNR', 'composition', 'gw', 'qty', 'loss', 'kgs'] as $lbl) {
            if (!empty($changedTally[$lbl])) {
                $summaryLis[] = '<li><b>' . strtoupper(htmlspecialchars($lbl)) . '</b>: ' . (int)$changedTally[$lbl] . '</li>';
            }
        }
        $changedSummary = $summaryLis
            ? '<div><b>Kolom berubah</b><ul style="margin:6px 0 0 18px;">' . implode('', $summaryLis) . '</ul></div>'
            : '';

        // header utama
        $msg  = 'Revisi MU selesai.';
        $msg .= '<ul style="margin:6px 0 0 18px;">'
            .  '<li>Insert: '  . (int)$stats['insert']  . '</li>'
            .  '<li>Update: '  . (int)$stats['update']  . '</li>'
            .  '<li>Skip: '    . (int)$stats['skip']    . '</li>'
            .  ($SOFT_DELETE_REMOVED ? '<li>Disabled: ' . (int)$stats['disabled'] . '</li>' : '')
            .  '</ul>'
            .  $changedSummary;

        // contoh baris perubahan (maks 8 item)
        if (!empty($changedRows)) {
            $sample = array_slice($changedRows, 0, 8);
            $items  = [];
            foreach ($sample as $r) {
                $header = 'SS ' . htmlspecialchars((string)$r['style_size'])
                    . ' [' . htmlspecialchars((string)$r['item_type']) . '/'
                    . htmlspecialchars((string)$r['kode_warna']) . '/'
                    . htmlspecialchars((string)$r['color']) . ']';

                // sub-list detail perubahan (note sudah berisi "Field: lama → baru; ...")
                $sub = '';
                if (!empty($r['note'])) {
                    // pecah jadi beberapa li biar rapi
                    $parts = array_map('trim', explode(';', $r['note']));
                    $subLis = [];
                    foreach ($parts as $p) {
                        if ($p === '') continue;
                        $subLis[] = '<li>' . htmlspecialchars($p) . '</li>';
                    }
                    if ($subLis) {
                        $sub = '<ul style="margin:4px 0 0 18px;">' . implode('', $subLis) . '</ul>';
                    }
                }

                // daftar label kolom yang berubah (singkat)
                $short = '';
                if (!empty($r['changed'])) {
                    $short = ' → ' . implode(', ', array_map('htmlspecialchars', $r['changed']));
                }

                $items[] = '<li>' . $header . $short . $sub . '</li>';
            }
            $more = count($changedRows) > 8
                ? '<div style="margin-top:4px;">… (total ' . (int)count($changedRows) . ' baris berubah)</div>'
                : '';
            $msg .= '<div style="margin-top:10px;"><b>Data perubahan</b>'
                . '<ul style="margin:6px 0 0 18px;">' . implode('', $items) . '</ul>'
                . $more . '</div>';
        }


        // error → tampilkan list
        if (!empty($errs)) {
            $sampleErr = array_slice($errs, 0, 12);
            $errLis = [];
            foreach ($sampleErr as $e) {
                $errLis[] = '<li>' . htmlspecialchars((string)$e) . '</li>';
            }
            $moreErr = count($errs) > 12
                ? '<div style="margin-top:4px;">… (total ' . (int)count($errs) . ' catatan)</div>'
                : '';

            return redirect()->back()->with(
                'warning',
                $msg
                    . '<div style="margin-top:12px;"><b>Catatan</b>'
                    . '<ul style="margin:6px 0 0 18px;">' . implode('', $errLis) . '</ul>'
                    . $moreErr . '</div>'
            );
        }

        return redirect()->back()->with('success', $msg);
    }



    public function material($id)
    {
        $id_order = $id; // Ambil id_order dari URL
        if (!$id_order) {
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'ID Order tidak ditemukan.');
        }
        $model = $this->masterOrderModel->select('no_model')->where('id_order', $id_order)->first();
        $itemType = $this->masterMaterialModel->getItemType();
        $orderData = $this->materialModel->getMaterial($id_order);
        $totalKebutuhan = $this->materialModel->getTotalKebutuhan($id_order);
        $itemTypeByIdOrder = $this->materialModel->getItemTypeByIdOrder($id_order);
        $materialtype = $this->masterMaterialTypeModel->getMaterialType();

        if ($totalKebutuhan) {
            foreach ($totalKebutuhan as &$keb) { // ← Pake referensi &
                $tgl = $this->scheduleCelupModel->cekSch($model, $keb) ?? '-';
                $keb['tanggal_schedule'] = $tgl;
            }
            unset($keb); // good practice setelah foreach by reference
        }
        // dd($totalKebutuhan);

        // dd($totalKebutuhan);
        $styleSize = $this->materialModel->getStyle($id);
        $materialNr = $this->materialModel->getMaterialNr($id);

        if (empty($orderData)) {
            session()->setFlashdata('error', 'Data Material tidak ditemukan! Silakan impor ulang data.');
            return redirect()->to(base_url($this->role . '/masterdata'));
        }

        $areaData = array_column($orderData, 'area');
        $model = $orderData[0]['no_model'];
        if (!$orderData) {
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'Data Order tidak ditemukan.');
        }
        // dd($orderData);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'orderData' => $orderData,
            'no_model' => $model,
            'id_order' => $id_order,
            'itemType' => $itemType,
            'area' => $areaData,
            'style' => $styleSize,
            'kebutuhan' => $totalKebutuhan,
            'itemTypeByIdOrder' => $itemTypeByIdOrder,
            'materialtype' => $materialtype,
            'materialNr' => $materialNr,
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
            'material_nr' => $this->request->getPost('material_nr'),
            'area' => $this->request->getPost('area'),
            'inisial' => $this->request->getPost('inisial'),
            'color' => $this->request->getPost('color'),
            'item_type' => $this->request->getPost('item_type'),
            'kode_warna' => $this->request->getPost('kode_warna'),
            'composition' => $this->request->getPost('composition'),
            'gw' => $this->request->getPost('gw'),
            'gw_aktual' => $this->request->getPost('gw_aktual'),
            'qty_pcs' => $this->request->getPost('qty_pcs'),
            'loss' => $this->request->getPost('loss'),
            'kgs' => $this->request->getPost('kgs'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        if ($this->materialModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/material/' . $idOrder))->with('success', 'Data Berhasil.');
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

    public function deleteDuplicateMu($id_order)
    {
        $deleteMU = $this->materialModel->deleteDuplicate($id_order);
        if ($deleteMU) {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data Duplikat Berhasil dihapus.');
        } else {
            return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('error', 'Data Duplikat Gagal dihapus.');
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
        $noModels  = $data['no_model'] ?? null;  // sekarang tetap string
        $id_order = $id;
        $buyer = $this->masterOrderModel
            ->select('buyer')
            ->where('no_model', $noModels)
            ->get()
            ->getRow('buyer');

        $items = $data['items'] ?? [];
        if (($data['penerima'] ?? '') === 'Paryanti') {
            // dd($totalKg, $details);
            $headerData = [];
            foreach ($items as $item) {
                $spesifikasiBenang = (!empty($item['jenis_benang']) && !empty($item['spesifikasi_benang'])) ? $item['jenis_benang'] . ' ' . $item['spesifikasi_benang'] : NULL;
                $headerData[] = [
                    'buyer'                 => $buyer,
                    'no_model'              => 'POCOVERING ' . $noModels,
                    'item_type'             => $item['item_type'],
                    'kode_warna'            => $item['kode_warna'],
                    'color'                 => $item['color'],
                    'spesifikasi_benang'    => $spesifikasiBenang,
                    'kg_po'                 => $item['kg_po'],
                    'keterangan'            => $data['keterangan'] ?? '',
                    'ket_celup'             => $item['ket_celup'],
                    'bentuk_celup'          => $item['bentuk_celup'],
                    'kg_percones'           => $item['kg_percones'],
                    'jumlah_cones'          => $item['jumlah_cones'],
                    'jenis_produksi'        => $item['jenis_produksi'],
                    'contoh_warna'          => $item['contoh_warna'],
                    'penerima'              => $data['penerima'],
                    'po_plus'               => $data['po_plus'],
                    'penanggung_jawab'      => $data['penanggung_jawab'],
                    'admin'                 => session()->get('username'),
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),
                    'id_induk'              => null,
                ];
            }
            // dd ($headerData, $items);
            $db = \Config\Database::connect();
            $db->transStart();

            // 1. Insert semua header
            $this->openPoModel->insertBatch($headerData);

            // 2. Ambil ID pertama
            $firstInsertId = $db->insertID();
            $count = count($headerData);
            $insertedIds = range($firstInsertId, $firstInsertId + $count - 1);

            // 3. Siapkan detail per header (1-to-1)
            $batch = [];
            foreach ($items as $i => $d) {
                $spesifikasiBenang = (!empty($d['jenis_benang']) && !empty($d['spesifikasi_benang'])) ? $d['jenis_benang'] . ' ' . $d['spesifikasi_benang'] : NULL;
                $batch[] = [
                    'buyer'                 => $buyer,
                    'no_model'              => $noModels ?? '-',
                    'item_type'             => $d['item_type'],
                    'kode_warna'            => $d['kode_warna'],
                    'color'                 => $d['color'],
                    'spesifikasi_benang'    => $spesifikasiBenang,
                    'kg_po'                 => $d['kg_po'],
                    'keterangan'            => $data['keterangan'] ?? '',
                    'ket_celup'             => $d['ket_celup'],
                    'bentuk_celup'          => $d['bentuk_celup'],
                    'kg_percones'           => $d['kg_percones'],
                    'jumlah_cones'          => $d['jumlah_cones'],
                    'jenis_produksi'        => $d['jenis_produksi'],
                    'contoh_warna'          => $d['contoh_warna'],
                    'penerima'              => $data['penerima'],
                    'po_plus'               => $data['po_plus'],
                    'penanggung_jawab'      => $data['penanggung_jawab'],
                    'admin'                 => session()->get('username'),
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),
                    'id_induk'              => $insertedIds[$i], // id header ke-i
                ];
            }
            // dd ($batch);
            $this->openPoModel->insertBatch($batch);

            // 3. Tracking data
            $trackingData = [];
            foreach ($items as $i => $item) {
                $trackingData[] = [
                    'id_po_gbn'    => $insertedIds[$i],
                    'status'       => '',
                    'keterangan'   => $data['keterangan'] ?? '',
                    'admin'        => 'covering'
                ];
            }

            $this->trackingPoCoveringModel->insertBatch($trackingData);


            $db->transComplete();
            if (!$db->transStatus()) {
                return redirect()->back()->with('error', 'Gagal menyimpan PO Covering.');
                dd('Transaksi gagal', $db->error(), $db->getLastQuery());
            }

            return redirect()->to(base_url($this->role . '/material/' . $id_order))
                ->with('success', 'Data PO Covering berhasil disimpan.');
        } else {
            foreach ($items as $item) {
                $spesifikasiBenang = (!empty($item['jenis_benang']) && !empty($item['spesifikasi_benang'])) ? $item['jenis_benang'] . ' ' . $item['spesifikasi_benang'] : NULL;
                $itemData = [
                    'role'                  => $this->role,
                    'buyer'                 => $buyer,
                    'no_model'              => $data['no_model'],
                    'item_type'             => $item['item_type'],
                    'kode_warna'            => $item['kode_warna'],
                    'color'                 => $item['color'],
                    'spesifikasi_benang'    => $spesifikasiBenang,
                    'kg_po'                 => $item['kg_po'],
                    'keterangan'            => $data['keterangan'],
                    'ket_celup'             => $item['ket_celup'],
                    'bentuk_celup'          => $item['bentuk_celup'],
                    'kg_percones'           => $item['kg_percones'],
                    'jumlah_cones'          => $item['jumlah_cones'],
                    'jenis_produksi'        => $item['jenis_produksi'],
                    'contoh_warna'          => $item['contoh_warna'],
                    'penerima'              => $data['penerima'],
                    'po_plus'               => $data['po_plus'],
                    'penanggung_jawab'      => $data['penanggung_jawab'],
                    'admin'                 => session()->get('username'),
                ];
                // Simpan data ke database
                $this->openPoModel->insert($itemData);
            }
        }


        return redirect()->to(base_url($this->role . '/material/' . $id_order))->with('success', 'Data PO Berhasil Di Tambahkan.');
    }

    private function saveOpenPOGabungan()
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
        }
        // dd(        $totalKg, $details);
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
            'kg_po'            => $totalKg,
            'keterangan'       => $data['keterangan'] ?? '',
            'penerima'         => $data['penerima'],
            'penanggung_jawab' => $data['penanggung_jawab'],
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
        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menyimpan PO gabungan.');
        }

        return redirect()->to(base_url($this->role . '/masterdata'))
            ->with('success', 'Data PO Gabungan berhasil disimpan.');
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

    public function getMasterData()
    {
        $request = service('request');
        $postData = $request->getPost();

        $draw = $postData['draw'];
        $start = $postData['start'];
        $length = $postData['length'];
        $search = $postData['search']['value'];

        // Total tanpa filter
        $totalRecords = $this->masterOrderModel->countAll();

        // Apply filter jika ada pencarian
        // if (!empty($search)) {
        //     $this->masterOrderModel->getMasterOrder($length, $start, $search);
        // }

        $totalFiltered = $this->masterOrderModel->countFiltered($search);

        $data = $this->masterOrderModel->getMasterOrder($length, $start, $search);
        // dd($data);
        foreach ($data as &$dt) {
            $tanggal_po = $this->openPoModel->getTanggalPo($dt);
            $dt['tanggal_po'] = $tanggal_po ?? '-';
        }
        unset($dt); // amanin reference
        // Material untuk cross-check id_order
        $material = $this->materialModel->findAll();
        $materialOrderIds = array_column($material, 'id_order');

        // Buat response array
        $result = [];
        foreach ($data as $row) {
            $isNotInMaterial = !in_array($row['id_order'], $materialOrderIds);
            $style = $isNotInMaterial ? 'color:red;' : '';

            $result[] = [
                'foll_up' => "<span style='$style'>{$row['foll_up']}</span>",
                'lco_date' => "<span style='$style'>{$row['lco_date']}</span>",
                'no_model' => "<span style='$style'>{$row['no_model']}</span>",
                'no_order' => "<span style='$style'>{$row['no_order']}</span>",
                'jarum' => "<span style='$style'>{$row['jarum']}</span>",
                'buyer' => "<span style='$style'>{$row['buyer']}</span>",
                'memo' => "<span style='$style'>{$row['memo']}</span>",
                'start_mc' => "<span style='$style'>" .
                    (!empty($row['start_mc']) && $row['start_mc'] !== '0000-00-00 00:00:00'
                        ? date('d-m-Y', strtotime($row['start_mc']))
                        : 'Belum update')
                    . "</span>",

                'delivery_awal' => "<span style='$style'>{$row['delivery_awal']}</span>",
                'delivery_akhir' => "<span style='$style'>{$row['delivery_akhir']}</span>",
                'unit' => "<span style='$style'>{$row['unit']}</span>",
                'area' => "<span style='$style'>{$row['area']}</span>",
                'tanggal_po' => "<span style='$style'>{$row['tanggal_po']}</span>",
                'tanggal_import' => "<span style='$style'>{$row['created_at']}</span>",
                'admin' => "<span style='$style'>{$row['admin']}</span>",
                'action' => "
                <a href='" . base_url($this->role . '/material/' . $row['id_order']) . "' class='btn btn-info btn-sm'>Detail</a>
                <button class='btn btn-warning btn-sm btn-edit' data-id='{$row['id_order']}'>Update</button>
            "
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $result
        ]);
    }

    public function reportKebutuhanBahanBaku()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/masterdata/report-kebutuhan-bb', $data);
    }

    public function filterReportKebutuhanBahanBaku()
    {
        $jenis = $this->request->getGet('jenis');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->masterOrderModel->getFilterKebutuhanBahanBaku($jenis, $tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
}
