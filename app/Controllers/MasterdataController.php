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

class MasterdataController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPOModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPOModel = new OpenPOModel();

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

    public function tampilMasterOrder()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost();

            // Ambil parameter
            $search = $request['search']['value'] ?? '';
            $start = intval($request['start']);
            $length = intval($request['length']);
            $orderColumnIndex = $request['order'][0]['column'] ?? 1; // Default kolom ke-1
            $orderDirection = $request['order'][0]['dir'] ?? 'asc';
            $orderColumnName = $request['columns'][$orderColumnIndex]['data'] ?? 'foll_up';

            // Query total data tanpa filter
            $totalRecords = $this->masterOrderModel->countAll();

            // Query data dengan filter
            $query = $this->masterOrderModel->like('foll_up', $search)
                ->orLike('lco_date', $search)
                ->orLike('no_model', $search)
                ->orLike('no_order', $search)
                ->orLike('buyer', $search)
                ->orLike('memo', $search)
                ->orLike('delivery_awal', $search)
                ->orLike('delivery_akhir', $search)
                ->orLike('admin', $search);

            $filteredRecords = $query->countAllResults(false); // Total data yang difilter

            // Sorting dan pagination
            $data = $query->orderBy($orderColumnName, $orderDirection)
                ->findAll($length, $start);

            // Tambahkan kolom nomor dan tombol aksi
            foreach ($data as $index => $item) {
                $data[$index]['no'] = $start + $index + 1;

                // Tombol Detail
                $data[$index]['action'] = '
                <button class="btn btn-sm btn-info btn-detail" data-id="' . $item['id_order'] . '">Detail</button>
                <button class="btn btn-sm btn-warning btn-edit" data-id="' . $item['id_order'] . '">Update</button>

            ';
            }

            // Format response JSON
            $response = [
                'draw' => intval($request['draw']),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];

            return $this->response->setJSON($response);
        }

        return view($this->role . '/masterdata/index', [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ]);
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
        if ($this->request->isAJAX()) {
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
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function importMU()
    {
        // Get the uploaded file
        $file = $this->request->getFile('file');

        // Check if file is uploaded successfully
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
        }

        try {
            // Load Excel or CSV file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();

            // Initialize arrays
            $validDataOrder = [];
            $validDataMaterial = [];
            $invalidRows = [];
            $data = [];
            // Get username from session
            $admin = session()->get('username');
            if (!$admin) {
                return redirect()->back()->with('error', 'Session expired. Please log in again.');
            }

            // Array untuk menampung data yang dikelompokkan berdasarkan style_size
            $groupedData = [];

            foreach ($sheet->getRowIterator() as $key => $row) {
                // Skip header
                if ($key === 1) {
                    continue;
                }

                // Ambil data dari kolom sesuai kebutuhan
                $style_size = $sheet->getCell('D' . $key)->getValue(); // Kolom D
                $no_model = $sheet->getCell('B9')->getValue(); // Kolom B9
                $no_model = str_replace([': '], '', $no_model);
                $no_order = $sheet->getCell('B5')->getValue(); // Kolom B5
                $no_order = str_replace([': '], '', $no_order);
                $buyer = $sheet->getCell('B6')->getValue(); // Kolom B6
                $buyer = str_replace([': '], '', $buyer);
                $lco_date = $sheet->getCell('B4')->getFormattedValue(); // Kolom B4
                $lco_date = str_replace([': '], '', $lco_date);

                // dd($no_model, $style_size, $no_order, $buyer, $lco_date);
                // Konversi format tanggal dari d.m.Y ke Y-m-d
                $date_object = DateTime::createFromFormat('d.m.Y', $lco_date);
                if ($date_object) {
                    $formatted_date = $date_object->format('Y-m-d'); // Format MySQL
                }
                $foll_up = $sheet->getCell('D5')->getValue(); // Kolom D5
                $foll_up = str_replace([': '], '', $foll_up);

                $checkdatabase = $this->masterOrderModel->checkDatabase($no_order, $no_model, $buyer, $formatted_date, $foll_up);
                if ($checkdatabase) {
                    return redirect()->back()->with('error', 'Data sudah ada di database.');
                }
                // Validasi data per baris sebelum dimasukkan ke grup
                if (!$no_model || !$style_size) {
                    $invalidRows[] = $key;
                    continue;
                }

                // Simpan data dalam kelompok berdasarkan style_size
                $groupedData[$style_size][] = [
                    'lco_date' => $formatted_date,
                    'no_order' => $no_order,
                    'buyer' => $buyer,
                    'no_model' => $no_model,
                    'foll_up' => $foll_up,
                ];
            }

            // Sekarang lakukan validasi hanya sekali per group (style_size)
            $validDataOrder = [];
            foreach ($groupedData as $style_size => $orders) {
                // Misalnya, lakukan validasi untuk setiap grup data
                foreach ($orders as $order) {
                    $validate = $this->validateWithAPI($order['no_model'], $style_size);

                    if ($validate != NULL) {
                        $validDataOrder[] = [
                            'id_order' => NULL,
                            'no_order' => $order['no_order'],
                            'no_model' => $validate['no_model'],
                            'buyer' => $order['buyer'],
                            'foll_up' => $order['foll_up'],
                            'lco_date' => $formatted_date,
                            'memo' => NULL,
                            'delivery_awal' => $validate['delivery_awal'],
                            'delivery_akhir' => $validate['delivery_akhir'],
                            'admin' => $admin,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => NULL,
                        ];
                    } else {
                        // Jika validasi gagal, bisa menambahkan ke daftar invalid
                        $invalidRows[] = $order['no_order']; // Anda bisa menambahkan key atau data lain sebagai penanda
                    }
                }
            }

            $data = [
                'no_order' => $no_order,
                'no_model' => $no_model,
                'buyer' => $buyer,
                'foll_up' => $foll_up,
                'lco_date' => $formatted_date,
                'memo' => NULL,
                'delivery_awal' => $validate['delivery_awal'],
                'delivery_akhir' => $validate['delivery_akhir'],
                'admin' => $admin,
                'created_at' => date('Y-m-d H:i:s'),
            ];


            // Simpan data order ke database
            $masterOrderModel = new MasterOrderModel();
            $masterOrderModel->insert($data);

            // Cari header pada baris pertama
            // Map header ke kolom
            $headerMap = [
                'Color' => 'A', // Kolom untuk "Color", sesuaikan dengan header file Anda
                'Item Type' => 'B', // Kolom untuk "Item Type", sesuaikan dengan header file Anda
                'Kode Warna' => 'C', // Kolom untuk "Kode Warna", sesuaikan dengan header file Anda
                'Item Nr' => 'D', // Kolom untuk "Item Nr", sesuaikan dengan header file Anda
                'Composition(%)' => 'E', // Kolom untuk "Composition(%)", sesuaikan dengan header file Anda
                'GW/pc' => 'F', // Kolom untuk "GW/pc", sesuaikan dengan header file Anda
                'Qty/pcs' => 'G', // Kolom untuk "Qty/pcs", sesuaikan dengan header file Anda
                'Loss' => 'H', // Kolom untuk "Loss", sesuaikan dengan header file Anda
                'Kgs' => 'I', // Kolom untuk "Kgs", sesuaikan dengan header file Anda
            ];

            // Iterasi data dimulai dari baris kedua
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowIndex = $row->getRowIndex();

                // Ambil data dari sel tertentu
                $no_model = $sheet->getCell('B9')->getValue(); // Kolom B9
                $no_model = str_replace([': '], '', $no_model);
                $style_size = $sheet->getCell('D' . $rowIndex)->getValue(); // Kolom D
                $no_order = $sheet->getCell('B5')->getValue(); // Kolom B5
                $no_order = str_replace([': '], '', $no_order);

                // get id_order
                $id_order = $masterOrderModel->findIdOrder($no_order);

                // Validasi melalui API
                $validate = $this->validateWithAPI($no_model, $style_size);

                if ($validate) {
                    // Validasi item type
                    $item_type = trim($sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue());

                    // Validasi apakah item_type tidak kosong
                    if (empty($item_type)) {
                        return redirect()->back()->with('error', 'Item Type tidak boleh kosong.');
                    }

                    // Pastikan item_type aman sebelum diteruskan ke model
                    $item_type = htmlspecialchars($item_type, ENT_QUOTES, 'UTF-8');

                    // Cek keberadaan item_type di database
                    $checkItemType = $this->masterMaterialModel->checkItemType($item_type);

                    if (!$checkItemType) {
                        return redirect()->back()->with('error', $item_type . ' tidak ada di database.');
                    }
                    // Siapkan data untuk dimasukkan ke dalam validDataMaterial
                    $validDataMaterial[] = [
                        'id_order' => $id_order['id_order'],
                        'style_size' => $validate['size'],
                        'area' => $validate['area'],
                        'inisial' => $validate['inisial'],
                        'color' => $sheet->getCell($headerMap['Color'] . $rowIndex)->getValue(),
                        'item_type' => $item_type,
                        'kode_warna' => $sheet->getCell($headerMap['Kode Warna'] . $rowIndex)->getValue(),
                        'composition' => $sheet->getCell($headerMap['Composition(%)'] . $rowIndex)->getValue(), // Tetap isi dengan Composition(%) yang valid
                        'gw' => $sheet->getCell($headerMap['GW/pc'] . $rowIndex)->getValue(),
                        'qty_pcs' => $sheet->getCell($headerMap['Qty/pcs'] . $rowIndex)->getValue(),
                        'loss' => $sheet->getCell($headerMap['Loss'] . $rowIndex)->getValue(),
                        'kgs' => $sheet->getCell($headerMap['Kgs'] . $rowIndex)->getValue(),
                        'admin' => $admin,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];
                } else {
                    $invalidRows[] = $rowIndex; // Tambahkan baris tidak valid
                }
            }

            // Simpan data material ke database
            $materialModel = new MaterialModel();
            $materialModel->insertBatch($validDataMaterial);

            // Redirect ke halaman sebelumnya dengan pesan sukses
            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimport data.');
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
            // Jika id_order tidak ditemukan, redirect atau tampilkan error
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'ID Order tidak ditemukan.');
        }

        // Fetch data terkait id_order dari database (contoh)
        $orderData = $this->masterOrderModel->find($id_order);

        if (!$orderData) {
            // Jika data tidak ditemukan, redirect atau tampilkan error
            return redirect()->to(base_url($this->role . '/masterOrder'))->with('error', 'Data Order tidak ditemukan.');
        }
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'orderData' => $orderData, // Kirim data ke view
            'no_model' => $orderData['no_model']
        ];

        return view($this->role . '/material/index', $data);
    }

    public function tampilMaterial()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost();

            // Ambil parameter
            $search = $request['search']['value'] ?? '';
            $start = intval($request['start']);
            $length = intval($request['length']);
            $orderColumnIndex = $request['order'][0]['column'] ?? 1; // Default kolom ke-1
            $orderDirection = $request['order'][0]['dir'] ?? 'asc';
            $orderColumnName = $request['columns'][$orderColumnIndex]['data'] ?? 'style_size';

            // Query total data tanpa filter
            $totalRecords = $this->materialModel->countAll();

            // Query data dengan filter
            $query = $this->materialModel->like('style_size', $search)
                ->orLike('area', $search)
                ->orLike('inisial', $search)
                ->orLike('color', $search)
                ->orLike('item_type', $search)
                ->orLike('kode_warna', $search)
                ->orLike('composition', $search)
                ->orLike('gw', $search)
                ->orLike('qty_pcs', $search)
                ->orLike('loss', $search)
                ->orLike('kgs', $search)
                ->orLike('admin', $search);

            $filteredRecords = $query->countAllResults(false); // Total data yang difilter

            // Sorting dan pagination
            $data = $query->orderBy($orderColumnName, $orderDirection)
                ->findAll($length, $start);

            // Tambahkan kolom nomor dan tombol aksi
            foreach ($data as $index => $item) {
                $data[$index]['no'] = $start + $index + 1;

                // Tombol Detail
                $data[$index]['action'] = '
                <button class="btn btn-sm btn-warning btn-edit" data-id="' . $item['id_material'] . '">Update</button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="' . $item['id_material'] . '">Delete</button>

            ';
            }

            // Format response JSON
            $response = [
                'draw' => intval($request['draw']),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];

            return $this->response->setJSON($response);
        }
        return view($this->role . '/material/index', [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ]);
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
        if ($this->request->isAJAX()) {
            $id = $this->request->getPost('id_material');

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


                // Tambahkan field lain yang ingin diperbarui
            ];

            if ($this->materialModel->update($id, $data)) {
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function deleteMaterial($id)
    {
        if ($this->request->isAJAX()) {
            if ($this->materialModel->delete($id)) {
                return $this->response->setJSON(['message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menghapus data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function openPO($id)
    {

        $masterOrder = $this->masterOrderModel->getMaterialOrder($id);
        $orderData = $this->masterOrderModel->find($id);

        // foreach($masterOrder as $order){
        //     $model = $order['no_model'];
        //     $itemType = $order['item_type'];
        //     $kodeWarna = $order['kode_warna'];

        //     $cek=[
        //         'no_model'=>$model,
        //         'item_type'=>$itemType,
        //         'kode_warna'=>$kodeWarna
        //     ]
        //     $cekStok = $this->estimasiStokModel($cek);

        // }
        $data = [
            'model' => $orderData['no_model'],
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'order' => $masterOrder
        ];
        return view($this->role . '/material/openPO', $data);
    }

    public function exportOpenPO()
    {
        // Ambil data dari form
        $data = $this->request->getPost();

        // dd($data);

        // Proses setiap item
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
                'penanggung_jawab' => $data['penanggung_jawab'],
                'admin'            => session()->get('username'),
            ];

            // Simpan data ke database
            $this->openPOModel->insert($itemData);
        }

        return redirect()->back()->with('success', 'Data berhasil diimport.');
    }
}
