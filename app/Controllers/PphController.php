<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;


class PphController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;


    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterOrderModel = new MasterOrderModel();


        $this->role = session()->get('role');
        if ($this->filters   = ['role' => ['monitoring']] != session()->get('role')) {
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
        $area = $this->materialModel->getDataArea();
        $data = [
            'active' => $this->active,
            'title' => 'PPH',
            'role' => $this->role,
            'area' => $area,
        ];

        return view($this->role . '/pph/index', $data);
    }

    public function pphPerArea($area)
    {
        $data = [
            'active' => $this->active,
            'title' => 'PPH',
            'role' => $this->role,
            'area' => $area,
        ];
        return view($this->role . '/pph/pphPerArea', $data);
    }

    // public function modelForPPH($noModel)
    // {
    //     $   
    // }
    public function tampilPerStyle()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getVar();

            // Data dummy (replace dengan database jika diperlukan)
            $pph = [
                [
                    'jarum' => 'J-001',
                    'no_model' => 'M-1001',
                    'area' => 'Area A',
                    'delivery' => '2025-01-10',
                    'jenis' => 'Jenis 1',
                    'warna' => 'Merah',
                    'kode_warna' => 'KW-001',
                    'los' => 10,
                    'komposisi' => 50,
                    'gw' => 100,
                    'qty_po' => 200,
                    'total_produksi' => 180,
                    'sisa' => 20,
                    'total_kebutuhan' => 200,
                    'total_pemakaian' => 180,
                    'persen_pemakaian' => 90,
                ],
                [
                    'jarum' => 'J-002',
                    'no_model' => 'M-1002',
                    'area' => 'Area B',
                    'delivery' => '2025-01-10',
                    'jenis' => 'Jenis 2',
                    'warna' => 'Biru',
                    'kode_warna' => 'KW-002',
                    'los' => 10,
                    'komposisi' => 50,
                    'gw' => 100,
                    'qty_po' => 200,
                    'total_produksi' => 180,
                    'sisa' => 20,
                    'total_kebutuhan' => 200,
                    'total_pemakaian' => 180,
                    'persen_pemakaian' => 90,
                ]
            ];

            // Total data tanpa filter
            $totalData = count($pph);

            // Filter berdasarkan pencarian
            $search = $request['search']['value'] ?? '';
            $filteredData = array_filter($pph, function ($row) use ($search) {
                return stripos(implode(' ', $row), $search) !== false;
            });

            // Sorting
            $sortColumnIndex = $request['order'][0]['column'];
            $sortColumnName = $request['columns'][$sortColumnIndex]['data'];
            $sortDirection = $request['order'][0]['dir'];
            usort($filteredData, function ($a, $b) use ($sortColumnName, $sortDirection) {
                if ($sortDirection == 'asc') {
                    return $a[$sortColumnName] <=> $b[$sortColumnName];
                }
                return $b[$sortColumnName] <=> $a[$sortColumnName];
            });

            // Pagination
            $start = $request['start'];
            $length = $request['length'];
            $pagedData = array_slice($filteredData, $start, $length);

            // Tambahkan kolom nomor
            $pagedData = array_map(function ($item, $index) use ($start) {
                $item['no'] = $start + $index + 1;
                return $item;
            }, $pagedData, array_keys($pagedData));

            // Format respons JSON
            $data = [
                'draw' => $request['draw'],
                'recordsTotal' => $totalData,
                'recordsFiltered' => count($filteredData),
                'data' => $pagedData,
            ];

            return $this->response->setJSON($data);
        }

        // View untuk halaman awal
        return view($this->role . '/pph/pphPerStyle', [
            'active' => $this->active,
            'title' => 'PPH: Per Style',
            'role' => $this->role,
        ]);
    }

    public function tampilPerDays()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getVar();

            // Data dummy (replace dengan data dari database jika diperlukan)
            $pph = [
                [
                    'tgl_prod' => '2025-01-08',
                    'no_model' => 'M-1001',
                    'inisial' => 'AB',
                    'jenis' => 'Jenis 1',
                    'warna' => 'Merah',
                    'kode_warna' => 'KW-001',
                    'komposisi' => 50,
                    'gw' => 100,
                    'total_pph' => 500,
                ],
                [
                    'tgl_prod' => '2025-01-09',
                    'no_model' => 'M-1002',
                    'inisial' => 'CD',
                    'jenis' => 'Jenis 2',
                    'warna' => 'Biru',
                    'kode_warna' => 'KW-002',
                    'komposisi' => 40,
                    'gw' => 80,
                    'total_pph' => 320,
                ]
            ];

            // Total data tanpa filter
            $totalData = count($pph);

            // Filter berdasarkan pencarian
            $search = $request['search']['value'] ?? '';
            $filteredData = array_filter($pph, function ($row) use ($search) {
                return stripos(implode(' ', $row), $search) !== false;
            });

            // Sorting
            $sortColumnIndex = $request['order'][0]['column'];
            $sortColumnName = $request['columns'][$sortColumnIndex]['data'];
            $sortDirection = $request['order'][0]['dir'];
            usort($filteredData, function ($a, $b) use ($sortColumnName, $sortDirection) {
                if ($sortDirection == 'asc') {
                    return $a[$sortColumnName] <=> $b[$sortColumnName];
                }
                return $b[$sortColumnName] <=> $a[$sortColumnName];
            });

            // Pagination
            $start = $request['start'];
            $length = $request['length'];
            $pagedData = array_slice($filteredData, $start, $length);

            // Tambahkan kolom nomor
            $pagedData = array_map(function ($item, $index) use ($start) {
                $item['no'] = $start + $index + 1;
                return $item;
            }, $pagedData, array_keys($pagedData));

            // Format respons JSON
            $data = [
                'draw' => $request['draw'],
                'recordsTotal' => $totalData,
                'recordsFiltered' => count($filteredData),
                'data' => $pagedData,
            ];

            return $this->response->setJSON($data);
        }

        // View untuk halaman awal
        return view($this->role . '/pph/pphPerDays', [
            'active' => $this->active,
            'title' => 'PPH: Per Days',
            'role' => $this->role,
        ]);
    }

    public function tampilPerModel($area)
    {
        // Ambil parameter pencarian no_model
        $searchNoModel = $this->request->getGet('no_model');

        // Jika parameter belum diisi, kembalikan view dengan data kosong
        if (empty($searchNoModel)) {
            return view($this->role . '/pph/pphPerModel', [
                'active'     => $this->active,
                'title'      => 'PPH',
                'role'       => $this->role,
                'area'       => $area,
                'mergedData' => [] // Tidak ada data sampai search diisi
            ]);
        }
        // Get data from the database model.
        $models = $this->materialModel->getMaterialForPPH($area);

        // Build API URL dynamically.
        $apiUrl = "http://172.23.44.14/CapacityApps/public/api/getDataForPPH/"
            . urlencode($area) . "/"
            . urlencode($searchNoModel);

        // Use CodeIgniter's HTTP client instead of file_get_contents.
        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', $apiUrl);
        $apiData = json_decode($response->getBody(), true);

        // Initialize the array for merged data.
        $modelData = [];

        // Process API data.
        if ($apiData && is_array($apiData)) {
            foreach ($apiData as $key => $item) {
                // Explode the key to get area, model, and style size.
                $keyParts = explode('-', $key);
                if (count($keyParts) >= 3) {
                    $areaFromKey     = $keyParts[0];
                    $noModelFromKey  = $keyParts[1];
                    $styleSizeFromKey = implode('-', array_slice($keyParts, 2));

                    // Only include if the area and model match.
                    if ($areaFromKey === $area && $noModelFromKey === $searchNoModel) {
                        $modelData[$key] = [
                            'area'         => $item['area'] ?? null,
                            'no_model'     => $item['no_model'] ?? null,
                            'style_size'   => $styleSizeFromKey,
                            'bruto'        => $item['bruto'] ?? null,
                            'delivery_awal' => $item['delivery_awal'] ?? null,
                            'item_type'    => $item['item_type'] ?? null,
                            'color'        => $item['color'] ?? null,
                            'kode_warna'   => $item['kode_warna'] ?? null,
                            'composition'  => $item['composition'] ?? null,
                            'gw'           => $item['gw'] ?? null,
                            'qty_pcs'      => $item['qty_pcs'] ?? null,
                            'loss'         => $item['loss'] ?? null,
                            'ttl_kebutuhan' => $item['ttl_kebutuhan'] ?? null,
                        ];
                    }
                }
            }
        }

        // Merge database data if not already included from API.
        foreach ($models as $model) {
            $key = $model['area'] . '-' . $model['no_model'] . '-' . $model['style_size'];
            if (!isset($modelData[$key])) {
                $modelData[$key] = [
                    'area'         => $model['area'],
                    'no_model'     => $model['no_model'],
                    'style_size'   => $model['style_size'],
                    'bruto'        => null, // API might have bruto; otherwise, keep it null.
                    'delivery_awal' => $model['delivery_awal'] ?? null,
                    'item_type'    => $model['item_type'] ?? null,
                    'color'        => $model['color'] ?? null,
                    'kode_warna'   => $model['kode_warna'] ?? null,
                    'composition'  => $model['composition'] ?? null,
                    'gw'           => $model['gw'] ?? null,
                    'qty_pcs'      => $model['qty_pcs'] ?? null,
                    'loss'         => $model['loss'] ?? null,
                    'ttl_kebutuhan' => $model['ttl_kebutuhan'] ?? null,
                ];
            }
        }

        // Set flash message if no data found.
        if (empty($models)) {
            session()->setFlashdata('error', 'Data tidak ditemukan untuk area: ' . $area);
        }

        $mergedData = [];
        // Loop untuk menggabungkan data
        foreach ($models as $row) {
            // Buat key gabungan dari data database
            $key = $row['area'] . '-' . $row['no_model'] . '-' . $row['style_size'];

            // Cek apakah ada data dari API dengan key yang sama
            if (isset($apiData[$key])) {
                // Gabungkan kedua array; kamu bisa mengatur prioritas data jika perlu
                $mergedRow = array_merge($row, $apiData[$key]);
            } else {
                $mergedRow = $row;
            }
            $mergedData[] = $mergedRow;
        }

        $data = [
            'active'    => $this->active,
            'title'     => 'PPH',
            'role'      => $this->role,
            'area'      => $area, // Passing the merged data to the view.
            'mergedData' => $mergedData
        ];
        // dd($mergedData);
        return view($this->role . '/pph/pphPerModel', $data);
    }
}
