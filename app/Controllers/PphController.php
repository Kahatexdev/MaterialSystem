<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PphController extends BaseController
{
    protected $role;
    protected $filters;
    protected $active;

    public function __construct()
    {
        $this->role = session()->get('role');
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
            'title' => 'PPH',
            'role' => $this->role,
        ];
        return view($this->role . '/pph/index', $data);
    }
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
            'title' => 'PPH: Per Days',
            'role' => $this->role,
        ]);
    }

    public function tampilPerModel()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getVar();

            // Data dummy (ganti dengan query database jika diperlukan)
            $models = [
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
                    'delivery' => '2025-01-12',
                    'jenis' => 'Jenis 2',
                    'warna' => 'Biru',
                    'kode_warna' => 'KW-002',
                    'los' => 15,
                    'komposisi' => 40,
                    'gw' => 80,
                    'qty_po' => 150,
                    'total_produksi' => 140,
                    'sisa' => 10,
                    'total_kebutuhan' => 150,
                    'total_pemakaian' => 140,
                    'persen_pemakaian' => 93.3,
                ]
            ];

            // Total data tanpa filter
            $totalData = count($models);

            // Filter berdasarkan pencarian
            $search = $request['search']['value'] ?? '';
            $filteredData = array_filter($models, function ($row) use ($search) {
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
        return view($this->role . '/pph/pphPerModel', [
            'title' => 'PPH: Per Model',
            'role' => $this->role,
        ]);
    }



}
