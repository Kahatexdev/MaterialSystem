<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\ScheduleCelupModel;
use App\Models\PemesananModel;

class MonitoringController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $userModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $scheduleCelupModel;
    protected $pemesananModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->pemesananModel = new PemesananModel();

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
        $schTerdekat = $this->scheduleCelupModel->schTerdekat();
        $pemesanan = $this->pemesananModel->pemesananBelumDikirim();
        // dd($tes);
        $data = [
            'active' => $this->active,
            'title' => 'Monitoring',
            'role' => $this->role,
            'schTerdekat' => $schTerdekat,
            'pemesanan' => $pemesanan
        ];
        return view($this->role . '/dashboard/index', $data);
    }

    public function user()
    {
        $getData = $this->userModel->findAll();

        $data = [
            'active' => $this->active,
            'title' => 'Monitoring',
            'role' => $this->role,
            'dataUser' => $getData,
        ];
        return view($this->role . '/user/index', $data);
    }

    public function tambahUser()
    {
        $getData = $this->request->getPost();

        // Hash password sebelum menyimpan
        $data = [
            'username' => $getData['username'],
            'password' => password_hash($getData['password'], PASSWORD_BCRYPT), // Hash password dengan BCRYPT
            'role' => $getData['role'],
            'area' => $getData['area'],
        ];


        $this->userModel->save($data);

        return redirect()->to(base_url($this->role . '/user'))->with('success', 'Data berhasil ditambah.');
    }

    public function getUserDetails($id)
    {
        if ($this->request->isAJAX()) {
            $data = $this->userModel->find($id);

            if ($data) {
                return $this->response->setJSON([
                    'id_user' => $data['id_user'],
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'role' => $data['role'],
                    'area' => $data['area'],
                ]);
            } else {
                return $this->response->setJSON(['error' => 'Data tidak ditemukan.']);
            }
        }

        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    public function updateUser()
    {
        $id = $this->request->getPost('id_user');
        $pw = $this->request->getPost('password');
        $data = [
            'id_user' => $this->request->getPost('id_user'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($pw, PASSWORD_BCRYPT),
            'role' => $this->request->getPost('role'),
            'area' => $this->request->getPost('area')
        ];
        // dd($data);

        if ($this->userModel->update($id, $data)) {
            return redirect()->to(base_url($this->role . '/user'))->with('success', 'Data berhasil diubah.');
        } else {
            return redirect()->to(base_url($this->role . '/user'))->with('error', 'Data gagal diubah.');
        }
    }

    public function deleteUser($id)
    {
        $this->userModel->delete($id);
        return redirect()->to(base_url($this->role . '/user'))->with('success', 'Data berhasil dihapus.');
    }
}
