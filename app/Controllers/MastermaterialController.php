<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;

class MastermaterialController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterMaterialModel;

    public function __construct()
    {
        $this->masterMaterialModel = new MasterMaterialModel();


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
        $masterMaterial = $this->masterMaterialModel->findAll();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'masterMaterial' => $masterMaterial,
        ];
        return view($this->role . '/mastermaterial/index', $data);
    }

    public function tampilMasterMaterial()
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
            $orderColumnName = $request['columns'][$orderColumnIndex]['data'] ?? 'item_type';

            // Query total data tanpa filter
            $totalRecords = $this->masterMaterialModel->countAll();

            // Query data dengan filter
            $query = $this->masterMaterialModel->groupStart()
                ->like('item_type', $search)
                ->orLike('deskripsi', $search)
                ->orLike('jenis', $search)
                ->orLike('ukuran', $search)
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
            <button class="btn btn-sm btn-warning btn-edit" data-id="' . esc($item['item_type']) . '">Update</button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="' . esc($item['item_type']) . '">Delete</button>
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

        return view($this->role . '/mastermaterial/index', [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ]);
    }


    public function getMasterMaterialDetails()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getGet('id');

            $data = $this->masterMaterialModel->where('item_type', $id)->first();

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

    public function saveMasterMaterial()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'item_type' => esc($this->request->getPost('item_type')),
                'deskripsi' => esc($this->request->getPost('deskripsi')),
                'jenis' => esc($this->request->getPost('jenis')),
                'ukuran' => esc($this->request->getPost('ukuran')),

                // Tambahkan field lain yang ingin disimpan
            ];

            if ($this->masterMaterialModel->insert($data)) {
                return $this->response->setJSON(['message' => 'Data berhasil disimpan.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menyimpan data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateMasterMaterial()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getPost('item_type_old');

            $data = [
                'item_type' => esc($this->request->getPost('item_type')),
                'deskripsi' => esc($this->request->getPost('deskripsi')),
                'jenis' => esc($this->request->getPost('jenis')),
                'ukuran' => esc($this->request->getPost('ukuran')),
                // Tambahkan field lain yang ingin diperbarui
            ];

            if ($this->masterMaterialModel->updateMasterMaterial($id, $data)) {
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function deleteMasterMaterial()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('id');

            if ($this->masterMaterialModel->delete($id)) {
                return $this->response->setJSON(['message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menghapus data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
}
