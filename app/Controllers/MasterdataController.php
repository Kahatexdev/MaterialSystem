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
        // dd($file);
        // Check if file is uploaded successfully
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or file is invalid.');
        }

        try {
            // Load file Excel atau CSV
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            // Pastikan kolom header sesuai format
            $headers = array_map('strtolower', $sheetData[0]);
            $requiredHeaders = ['id_material', 'id_order', 'style_size', 'area', 'inisial', 'color', 'item_type', 'kode_warna', 'warna', 'composition', 'gw', 'qty_pcs', 'loss', 'kgs', 'admin'];
            if (array_diff($requiredHeaders, $headers)) {
                return redirect()->back()->with('error', 'Format header file tidak sesuai!');
            }

            $data = $this->validateWithAPI('MF4599', 'J425114-4 22X6');
            // Loop data untuk validasi dengan API
            $validData = [];
            $invalidRows = [];
            for ($i = 1; $i < count($sheetData); $i++) {
                $row = array_combine($headers, $sheetData[$i]);

                // Validasi dengan API
                $order =  $this->validateWithAPI('no_model', 'style_size');
                if (!$order) {
                    $invalidRows[] = $sheetData[$i]; // Simpan baris invalid
                    continue;
                }

                // Jika valid, tambahkan ke data untuk diimpor
                $validData[] = [
                    'id_material' => $row['id_material'],
                    'id_order'    => $row['id_order'],
                    'style_size'  => $row['style_size'],
                    'area'        => $order['area'],
                    'inisial'     => $order['inisial'],
                    'color'       => $row['color'],
                    'item_type'   => $row['item_type'],
                    'kode_warna'  => $row['kode_warna'],
                    'warna'       => $row['warna'],
                    'composition' => $row['composition'],
                    'gw'          => $row['gw'],
                    'qty_pcs'     => $row['qty_pcs'],
                    'loss'        => $row['loss'],
                    'kgs'         => $row['kgs'],
                    'admin'       => $row['admin'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }

            if (empty($validData)) {
                return redirect()->back()->with('error', 'Tidak ada data valid yang dapat diimpor!');
            }

            // Inisialisasi model dan simpan ke database
            $materialModel = new MaterialModel();
            $materialModel->insertBatch($validData);

            $message = 'Data berhasil diimpor!';
            if (!empty($invalidRows)) {
                $message .= ' Beberapa baris gagal divalidasi dan tidak diimpor.';
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
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
