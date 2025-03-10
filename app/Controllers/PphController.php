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

    public function tampilPerStyle($area)
    {
        // Ambil parameter pencarian no_model
        $searchNoModel = $this->request->getGet('no_model');

        // Jika parameter belum diisi, kembalikan view dengan data kosong
        if (empty($searchNoModel)) {
            return view($this->role . '/pph/pphPerStyle', [
                'active'     => $this->active,
                'title'      => 'PPH',
                'role'       => $this->role,
                'area'       => $area,
                'mergedData' => [] // Tidak ada data sampai search diisi
            ]);
        }
        // Get data from the database model.
        $dataPph = $this->materialModel->getDataPPHInisial($area, $searchNoModel);
        

        // Build API URL dynamically.
        $apiUrl = "http://172.23.44.14/CapacityApps/public/api/getDataForPPH/"
            . urlencode($area) . "/"
            . urlencode($searchNoModel);

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('GET', $apiUrl);

            // Pastikan response berhasil (status code 200)
            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Server mengembalikan status " . $response->getStatusCode());
            }

            $apiData = json_decode($response->getBody(), true);

            // Validasi jika response tidak valid atau kosong
            if (empty($apiData) || !is_array($apiData)) {
                session()->setFlashdata(
                    'error',
                    '<strong>Peringatan!</strong> Data dari CapacityApps tidak tersedia atau formatnya tidak valid. Silakan coba lagi nanti.'
                );
                $apiData = [];
            }
        } catch (\Exception $e) {
            session()->setFlashdata(
                'error',
                '<strong>Kesalahan!</strong> Gagal mengambil data dari <strong>CapacityApps</strong>.<br>'
                    . 'Silahkan Hubungi <strong>Monitoring</strong>' 
            );
            $apiData = [];
        }


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
                            'jarum'        => 'tes',
                            'area'         => $item['area'] ?? null,
                            'no_model'     => $item['no_model'] ?? null,
                            'style_size'   => $styleSizeFromKey,
                            'bruto'        => $item['bruto'] ?? 0,
                            'netto'        => ($item['bruto'] - $item['bs_setting']) ?? 0,
                            'item_type'     => $item['item_type'] ?? null,
                            'color'         => $item['color'] ?? null,
                            'kode_warna'    => $item['kode_warna'] ?? null,
                            'composition'   => $item['composition'] ?? 0,
                            'gw'            => $item['gw'] ?? null,
                            'qty_pcs'       => $item['qty_pcs'] ?? 0,
                            'bs_pcs'      => $item['bs_pcs'] ?? 0,
                            'bs_setting'    => $item['bs_setting'] ?? 0,
                            'loss'          => $item['loss'] ?? null,
                            'ttl_kebutuhan' => $item['ttl_kebutuhan'] ?? null,
                        ];
                    }
                }
            }
        }

        // Merge database data if not already included from API.
        foreach ($dataPph as $model) {
            $key = $model['area'] . '-' . $model['no_model'] . '-' . $model['style_size'];
            if (!isset($modelData[$key])) {
                $modelData[$key] = [
                    'jarum'        => 'tes',
                    'area'         => $model['area'],
                    'no_model'     => $model['no_model'],
                    'inisial'      => $model['inisial'],
                    'style_size'   => $model['style_size'],
                    'bruto'        => 0, // API might have bruto; otherwise, keep it null.
                    'netto'        => 0, // API might have bruto; otherwise, keep it null.
                    'item_type'    => $model['item_type'] ?? null,
                    'color'        => $model['color'] ?? null,
                    'kode_warna'   => $model['kode_warna'] ?? null,
                    'composition'  => $model['composition'] ?? 0,
                    'gw'           => $model['gw'] ?? 0,
                    'qty_pcs'      => $model['qty_pcs'] ?? 0,
                    'bs_pcs'     => 0,
                    'bs_setting'   => 0,
                    'loss'         => $model['loss'] ?? 0,
                    'kgs'          => $model['kgs'] ?? 0,
                ];
            }
        }
        // Set flash message if no data found.
        if (empty($dataPph)) {
            session()->setFlashdata('error', 'Data tidak ditemukan untuk area: ' . $area);
        }
        
        $mergedData = [];
        // Loop untuk menggabungkan data
        foreach ($dataPph as $row) {
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
        // dd($dataPph, $apiData, $mergedData);

        $data = [
            'active'    => $this->active,
            'title'     => 'PPH',
            'role'      => $this->role,
            'area'      => $area, // Passing the merged data to the view.
            'mergedData' => $mergedData
        ];
        // dd($mergedData);
        return view($this->role . '/pph/pphPerStyle', $data);
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
        // $models = $this->materialModel->getMaterialForPPH($area);
        $models = $this->materialModel->getMaterialForPPH($area, $searchNoModel);
        
        // Build API URL dynamically.
        $apiUrl = "http://172.23.44.14/CapacityApps/public/api/getDataForPPH/"
            . urlencode($area) . "/"
            . urlencode($searchNoModel);

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('GET', $apiUrl);

            // Pastikan response berhasil (status code 200)
            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Server mengembalikan status " . $response->getStatusCode());
            }

            $apiData = json_decode($response->getBody(), true);

            // Validasi jika response tidak valid atau kosong
            if (empty($apiData) || !is_array($apiData)) {
                session()->setFlashdata(
                    'error',
                    '<strong>Peringatan!</strong> Data dari CapacityApps tidak tersedia atau formatnya tidak valid. Silakan coba lagi nanti.'
                );
                $apiData = [];
            }
        } catch (\Exception $e) {
            session()->setFlashdata(
                'error',
                '<strong>Kesalahan!</strong> Gagal mengambil data dari <strong>CapacityApps</strong>.<br>'
                    . 'Silahkan Hubungi <strong>Monitoring</strong>' 
            );
            $apiData = [];
        }


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
                            'delivery_awal' => $item['delivery_awal'] ?? null,
                            'item_type'    => $item['item_type'] ?? null,
                            'color'        => $item['color'] ?? null,
                            'kode_warna'   => $item['kode_warna'] ?? null,
                            'composition'  => $item['composition'] ?? 0,
                            'gw'           => $item['gw'] ?? 0,
                            'loss'         => $item['loss'] ?? 0,
                            'ttl_kebutuhan' => $item['ttl_kebutuhan'] ?? 0,
                            'qty_pcs'      => $item['qty_pcs'] ?? 0,
                            'bruto'        => $item['bruto'] ?? 0,
                            'bs_pcs'      => $item['bs_pcs'] ?? 0,
                            'bs_setting'    => $item['bs_setting'] ?? 0,
                            'ttl_pemakaian' =>  0,
                        ];
                    }
                }
            }
        }

       $minum = [];

        // Gabungkan data dari API dan database
        foreach ($models as $model) {
            $key = $model['area'] . '-' . $model['no_model'] . '-' . $model['style_size'] . '-' . $model['item_type'] . '-' . $model['kode_warna'];

            $bruto = $model['bruto'] ?? 0;
            $bs_pcs = $model['bs_pcs'] ?? 0;
            $composition = $model['composition'] ?? 0;
            $gw = $model['gw'] ?? 0;

            $ttl_pemakaian = (($bruto + $bs_pcs) * ($composition / 100) * $gw) / 1000;
            
            $minum[$key] = [
                'area'         => $model['area'],
                'no_model'     => $model['no_model'],
                'style_size'   => $model['style_size'],
                'bruto'        => $model['bruto'] ?? 0, 
                'delivery_awal' => $model['delivery_awal'] ?? null,
                'item_type'    => $model['item_type'] ?? null,
                'color'        => $model['color'] ?? null,
                'kode_warna'   => $model['kode_warna'] ?? null,
                'composition'  => $model['composition'] ?? 0,
                'gw'           => $model['gw'] ?? 0,
                'loss'         => $model['loss'] ?? 0,
                'qty_pcs'      => $model['qty_pcs'] ?? 0,
                'bs_pcs'       => $model['bs_pcs'] ?? 0,
                'bs_setting'   => $model['bs_setting'] ?? 0,
                'ttl_kebutuhan' => $model['ttl_kebutuhan'] ?? 0,
                // 'ttl_pemakaian' => (($model['bruto'] + $model['bs_pcs']) * $model['composition'] / 100 * $model['gw'] / 1000) ?? 0,
            ];
        }
        // dd($minum);
        // Bandingkan dengan data dari API
        $gabungdData = [];
        // Loop untuk menggabungkan data
        foreach ($models as $row) {
            // Buat key gabungan dari data database
            $key = $row['area'] . '-' . $row['no_model'] . '-' . $row['style_size'];

            // Cek apakah ada data dari API dengan key yang sama
            if (isset($apiData[$key])) {
                // Gabungkan kedua array; kamu bisa mengatur prioritas data jika perlu
                $mergedRow = array_merge($row, $apiData[$key]);
                $bruto = $mergedRow['bruto'] ?? 0;
                // dd($bruto);
    
                $bs_pcs = $mergedRow['bs_pcs'] ?? 0;
                $composition = $mergedRow['composition'] ?? 0;
                $gw = $mergedRow['gw'] ?? 0;
    
                $ttl_pemakaian = (($bruto + $bs_pcs) * ($composition / 100) * $gw) / 1000;
            } else {
                $mergedRow = $row;
            }

            // Tambahkan ttl_pemakaian ke dalam array hasil merge
            $mergedRow['ttl_pemakaian'] = $ttl_pemakaian;
            // $ttl_pemakaian += $ttl_pemakaian;
            $gabungdData[] = $mergedRow;
        }

        // dd($gabungdData);
        // Set flash message if no data found.
        if (empty($models)) {
            session()->setFlashdata('error', 'Data tidak ditemukan untuk area: ' . $area);
        }

       $mergedData = [];
        $sumData = [];

        // Gabungkan data dari API dan hitung ttl_pemakaian
        foreach ($gabungdData as $row) {
            $key = ($row['area'] ?? '') . '-' . ($row['no_model'] ?? '') . '-' . ($row['style_size'] ?? '');

            if (!isset($sumData[$key])) {
                $sumData[$key] = [
                    'qty' => $row['qty_pcs'] ?? 0,
                    'bruto' => $row['bruto'] ?? 0,
                    'sisa' => $row['sisa'] ?? 0,
                    'bs_pcs' => $row['bs_pcs'] ?? 0,
                    'bs_setting' => $row['bs_setting'] ?? 0,
                    'ttl_pemakaian' => $row['ttl_pemakaian'] ?? 0
                ];
            } else {
                // Menjumlahkan data jika key sudah ada
                // $sumData[$key]['qty'] += $row['qty_pcs'] ?? 0;
                // $sumData[$key]['bruto'] += $row['bruto'] ?? 0;
                // $sumData[$key]['sisa'] += $row['sisa'] ?? 0;
                // $sumData[$key]['bs_pcs'] += $row['bs_pcs'] ?? 0;
                // $sumData[$key]['bs_setting'] += $row['bs_setting'] ?? 0;
                $sumData[$key]['ttl_pemakaian'] += $row['ttl_pemakaian'] ?? 0;
            }
        }

        // dd($sumData);
        // Gabungkan data dari database
        foreach ($models as $row) {
            $key = ($row['area'] ?? '') . '-' . ($row['no_model'] ?? '') . '-' . ($row['style_size'] ?? '');

            if (isset($sumData[$key])) {
                // Gabungkan data dari database dan API
                $mergedRow = array_merge($row, $sumData[$key]);
            } else {
                // Jika tidak ada di API, gunakan data dari database
                $mergedRow = $row;
            }

            $mergedData[$key] = $mergedRow;
        }
        // dd($mergedData);
        // Menjumlahkan bruto, bs_pcs, dan bs_setting
        $makan = [
            'total_qty_pcs' => 0,
            'total_bruto' => 0,
            'total_sisa' => 0,
            'total_bs_pcs' => 0,
            'total_bs_setting' => 0,
            'total_pemakaian' => 0
        ];

        foreach ($mergedData as $row) {
            $makan['total_qty_pcs'] += $row['qty'] ?? 0;
            $makan['total_bruto'] += $row['bruto'] ?? 0;
            $makan['total_sisa'] += $row['sisa'] ?? 0;
            $makan['total_bs_pcs'] += $row['bs_pcs'] ?? 0;
            $makan['total_bs_setting'] += $row['bs_setting'] ?? 0;
            $makan['total_pemakaian'] += $row['ttl_pemakaian'] ?? 0;
        }

        // dd($models, $apiData, $gabungdData, $mergedData, $makan);

        $data = [
            'active'    => $this->active,
            'title'     => 'PPH',
            'role'      => $this->role,
            'area'      => $area, // Passing the merged data to the view.
            'mergedData' => $mergedData,
            'makan'      => $makan // Totalan
        ];
        // dd($mergedData);
        return view($this->role . '/pph/pphPerModel', $data);
    }
}
