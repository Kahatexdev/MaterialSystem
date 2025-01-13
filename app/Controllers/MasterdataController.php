<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MasterdataController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    public function __construct()
    {
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

    public function importMU()
    {
        // Get the uploaded file
        $file = $this->request->getFile('file');

        // Check if file is uploaded successfully
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
        }

        try {
            // Load Excel atau CSV file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();

            // Validasi dan proses data
            $validData = [];
            $invalidRows = [];

            foreach ($sheet->getRowIterator() as $key => $row) {
                // Skip header
                if ($key === 1) {
                    continue;
                }

                // Ambil data dari kolom sesuai kebutuhan
                $no_model = $sheet->getCell('B9')->getValue(); // Kolom 
                $style_size = $sheet->getCell('D'. $key)->getValue(); // Kolom

                // Validasi data per baris
                if (!$no_model || !$style_size) {
                    $invalidRows[] = $key;
                    continue;
                }

                $makan = $this->validateWithAPI($no_model, $style_size);
                // Siapkan data valid untuk disimpan
                
            }
            dd ($makan);
            dd ($validData);
            // Jika ada data valid, simpan ke database
            // if (!empty($validData)) {
            //     // Simpan data ke database menggunakan model (contoh: $this->model->insertBatch())
            //     $this->model->insertBatch($validData);
            // }

            // Handle hasil proses
            if (!empty($invalidRows)) {
                return redirect()->back()->with('warning', 'Some rows have invalid data: ' . implode(', ', $invalidRows));
            }

            return redirect()->back()->with('success', 'Data imported successfully.');
        } catch (\Exception $e) {
            // Handle error
            return redirect()->back()->with('error', 'An error occurred while importing the file: ' . $e->getMessage());
        }
    }



    private function validateWithAPI($no_model, $style_size)
    {
        $style_size_encoded = str_replace(' ', '%20', $style_size);
        $param = $no_model . '/' . $style_size_encoded;

        $url = 'http://172.23.39.116/CapacityApps/public/api/orderMaterial/' . $param;

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
