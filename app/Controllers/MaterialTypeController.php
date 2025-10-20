<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialTypeModel;

class MaterialTypeController extends BaseController
{
    protected $role;
    protected $materialTypeModel;

    public function __construct()
    {
        $this->role = session()->get('role');
        $this->materialTypeModel = new MasterMaterialTypeModel();
    }

    public function index()
    {
        $data['materialTypes'] = $this->materialTypeModel->findAll();
        $data['title'] = 'Master Material Type';
        $data['role'] = $this->role;
        $data['active'] = 'masterMaterialType';

        return view($this->role . '/materialtype/index', $data);
    }

    public function tampilMasterMaterialType()
    {
        if (!$this->request->isAJAX()) {
            return view($this->role . '/materialtype/index', [
                'active' => $this->active ?? 'masterMaterialType',
                'title'  => 'Material Type System',
                'role'   => $this->role,
            ]);
        }

        $req   = $this->request->getPost();
        $draw  = (int)($req['draw'] ?? 1);
        $start = (int)($req['start'] ?? 0);
        $length = (int)($req['length'] ?? 10);
        $search = trim($req['search']['value'] ?? '');

        // Kolom yang valid untuk order (index mengikuti DataTables)
        $columns = ['material_type', 'admin', 'created_at'];
        $orderIdx = (int)($req['order'][0]['column'] ?? 1);
        $orderDir = strtolower($req['order'][0]['dir'] ?? 'asc');
        $orderDir = in_array($orderDir, ['asc', 'desc'], true) ? $orderDir : 'asc';
        $orderCol = $columns[$orderIdx] ?? 'material_type';

        // Total keseluruhan (tanpa filter)
        $recordsTotal = $this->materialTypeModel->countAll();

        // Builder dasar
        $builder = $this->materialTypeModel->builder();
        $builder->select('material_type, admin, created_at');

        // Pencarian (opsional)
        if ($search !== '') {
            $builder->groupStart()
                ->like('material_type', $search)
                ->orLike('admin', $search)
                ->groupEnd();
        }

        // Hitung filtered
        $countBuilder = clone $builder;
        $recordsFiltered = $countBuilder->countAllResults();

        // Order + paging
        $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start);

        $rows = $builder->get()->getResultArray();

        $data = [];
        $no = $start + 1;
        foreach ($rows as $r) {
            $mt = htmlspecialchars($r['material_type'], ENT_QUOTES, 'UTF-8'); // <- aman untuk attribute

            $data[] = [
                'no'            => $no++,
                'material_type' => $r['material_type'],
                'admin'         => $r['admin'],
                'created_at'    => $r['created_at'],
                'action'        =>
                '<button class="btn btn-sm btn-warning btn-edit" data-material_type="' . $mt . '">Edit</button>
             <button class="btn btn-sm btn-danger btn-delete" data-material_type="' . $mt . '">Delete</button>',
            ];
        }


        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function getMasterMaterialTypeDetails()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getGet('materialType'); // kiriman mentah
            // $id = urldecode($id); // tidak wajib, ajax jQuery sudah handle.
            $data = $this->materialTypeModel->where('material_type', $id)->first();

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

    public function saveMasterMaterialType()
    {
        if ($this->request->isAJAX()) {
            $data = [
                'material_type' => esc($this->request->getPost('material_type')),
                'admin' => esc(session()->get('username')),

                // Tambahkan field lain yang ingin disimpan
            ];

            if ($this->materialTypeModel->insert($data)) {
                return $this->response->setJSON(['message' => 'Data berhasil disimpan.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menyimpan data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateMasterMaterialType()
    {
        if ($this->request->isAJAX()) {

            $id = $this->request->getPost('material_type_old');

            $data = [
                'material_type' => esc($this->request->getPost('material_type')),
                'admin' => esc(session()->get('username')),

                // Tambahkan field lain yang ingin diperbarui
            ];

            if ($this->materialTypeModel->update($id, $data)) {
                return $this->response->setJSON(['message' => 'Data berhasil diupdate.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal mengupdate data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function deleteMasterMaterialType()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('materialType');

            if ($this->materialTypeModel->delete($id)) {
                return $this->response->setJSON(['message' => 'Data berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['error' => 'Gagal menghapus data.'], 500);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
}
