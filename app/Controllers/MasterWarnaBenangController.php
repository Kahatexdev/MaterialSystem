<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MasterWarnaBenangModel;
use CodeIgniter\HTTP\ResponseInterface;

class MasterWarnaBenangController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterWarnaBenangModel;

    public function __construct()
    {
        $this->masterWarnaBenangModel = new MasterWarnaBenangModel();

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
        // $masterWarna = $this->masterWarnaBenangModel->findAll();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            // 'masterWarna' => $masterWarna,
        ];
        return view($this->role . '/masterdata/master-warna-benang', $data);
    }

    public function getMasterWarnaBenang()
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
            $orderColumnName = $request['columns'][$orderColumnIndex]['data'] ?? 'kode_warna';

            // Query total data tanpa filter
            $totalRecords = $this->masterWarnaBenangModel->countAll();

            // Query data dengan filter
            $query = $this->masterWarnaBenangModel->groupStart()
                ->like('kode_warna', $search)
                ->orLike('warna', $search)
                ->orLike('warna_dasar', $search)
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
            <button class="btn btn-sm btn-warning btn-edit" data-id="' . esc($item['kode_warna']) . '">Update</button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="' . esc($item['kode_warna']) . '">Delete</button>
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

        return view($this->role . '/masterdata/master-warna-benang', [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ]);
    }

    public function saveMasterWarnaBenang()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'kode_warna' => esc($this->request->getPost('kode_warna')),
                'warna' => esc($this->request->getPost('warna')),
                'warna_dasar' => esc($this->request->getPost('warna_dasar')),
            ];

            if ($this->masterWarnaBenangModel->insert($data)) {
                return $this->response->setJSON(['message' => 'Data berhasil disimpan.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menyimpan data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function getMasterWarnaBenangDetails()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getGet('id');

            $data = $this->masterWarnaBenangModel->where('kode_warna', $id)->first();

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

    public function updateMasterWarnaBenang()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getPost('kode_warna_old');

            $data = [
                'kode_warna' => esc($this->request->getPost('kode_warna')),
                'warna' => esc($this->request->getPost('warna')),
                'warna_dasar' => esc($this->request->getPost('warna_dasar')),
            ];

            if ($this->masterWarnaBenangModel->updateMasterWarnaBenang($id, $data)) {
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function deleteMasterWarnaBenang()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('id');

            if ($this->masterWarnaBenangModel->delete($id)) {
                return $this->response->setJSON(['message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menghapus data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
}
