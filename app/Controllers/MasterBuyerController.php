<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class MasterBuyerController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;

    public function __construct()
    {

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
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
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,

        ];
        return view($this->role . '/masterbuyer/index', $data);
    }

    public function tampilMasterBuyer()
    {
        if ($this->request->isAJAX()) {
            $request = $this->request->getPost();

            // Validasi dan sanitasi input
            $search = esc($request['search']['value'] ?? '');
            $start = isset($request['start']) ? intval($request['start']) : 0;
            $length = isset($request['length']) ? intval($request['length']) : 10;
            $orderColumnIndex = $request['order'][0]['column'] ?? 0; // Default kolom pertama
            $orderDirection = $request['order'][0]['dir'] ?? 'asc';

            // Pastikan nilai kolom valid
            $orderColumnName = $request['columns'][$orderColumnIndex]['data'] ?? 'id_buyer';

            // Query total data tanpa filter
            $totalRecords = $this->masterBuyerModel->countAll();

            // Query data dengan filter
            $query = $this->masterBuyerModel->groupStart()
                ->like('kode_buyer', $search)
                ->orLike('nama_buyer', $search)
                ->groupEnd();

            $filteredRecords = $query->countAllResults(false);

            // Sorting dan pagination
            $data = $query->orderBy($orderColumnName, $orderDirection)
                ->findAll($length, $start);

            // Tambahkan kolom nomor dan tombol aksi
            foreach ($data as $index => $item) {
                $data[$index]['no'] = $start + $index + 1;

                // Sanitasi data output untuk menghindari XSS
                $data[$index]['action'] = '
            <button class="btn btn-sm btn-warning btn-edit" data-id="' . esc($item['id_buyer']) . '">Update</button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="' . esc($item['id_buyer']) . '">Delete</button>
        ';
            }

            // Format response JSON
            $response = [
                'draw' => intval($request['draw'] ?? 0),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];

            return $this->response->setJSON($response);
        }

        return view($this->role . '/masterbuyer/index', [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ]);
    }

    public function saveMasterBuyer()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'kode_buyer' => esc($this->request->getPost('kode_buyer')),
                'nama_buyer' => esc($this->request->getPost('nama_buyer')),
            ];

            if ($this->masterBuyerModel->insert($data)) {
                return $this->response->setJSON(['message' => 'Data berhasil disimpan.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menyimpan data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function getMasterBuyerDetails()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getGet('id');

            $data = $this->masterBuyerModel->where('id_buyer', $id)->first();

            if ($data) {
                log_message('debug', 'Data ditemukan: ' . json_encode($data));
                return $this->response->setJSON($data);
            } else {
                log_message('error', 'Data tidak ditemukan untuk ID: ' . $id);
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.'], 404);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateMasterBuyer()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getPost('id_buyer');

            $data = [
                'kode_buyer' => esc($this->request->getPost('kode_buyer')),
                'nama_buyer' => esc($this->request->getPost('nama_buyer')),
            ];

            if ($this->masterBuyerModel->update($id, $data)) {
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function deleteMasterBuyer()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('id');

            if ($this->masterBuyerModel->delete($id)) {
                return $this->response->setJSON(['message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menghapus data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
}
