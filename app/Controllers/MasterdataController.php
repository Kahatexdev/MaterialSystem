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

class MasterdataController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    public function __construct()
    {
        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['gbn']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();

        $this->masterOrderModel = new MasterOrderModel();
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
                    // Siapkan data untuk dimasukkan ke dalam validDataMaterial
                    $validDataMaterial[] = [
                        'id_order' => $id_order['id_order'],
                        'style_size' => $validate['size'],
                        'area' => $validate['area'],
                        'inisial' => $validate['inisial'],
                        'color' => $sheet->getCell($headerMap['Color'] . $rowIndex)->getValue(),
                        'item_type' => $sheet->getCell($headerMap['Item Type'] . $rowIndex)->getValue(),
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
            // dd($validDataOrder, $validDataMaterial);
            // Simpan data material ke database
            $materialModel = new MaterialModel();
            $materialModel->insertBatch($validDataMaterial);


            return redirect()->back()->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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
}
