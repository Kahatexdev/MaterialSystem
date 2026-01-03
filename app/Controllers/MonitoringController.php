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
        $client = service('curlrequest');

        $response = $client->get(
            'http://172.23.39.117/ComplaintSystem/public/api/MS/user',
            [
                'http_errors' => false, // 🔥 PENTING
            ]
        );

        $getData = json_decode($response->getBody(), true);

        $data = [
            'active' => $this->active,
            'title' => 'Monitoring',
            'role' => $this->role,
            'dataUser' => $getData['userData'],
        ];
        return view($this->role . '/user/index', $data);
    }

    public function tambahUser()
    {
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $role     = $this->request->getPost("role");

        $client = service('curlrequest');

        $response = $client->post(
            'http://172.23.39.117/ComplaintSystem/public/api/userAdd',
            [
                'http_errors' => false, // 🔥 PENTING
                'form_params' => [
                    'username' => $username,
                    'password' => $password,
                    'role' => $role,
                    'ket' => 'MS',
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);
        // dd($data);
        if ($data['success']) {
            return redirect()->to(base_url(session()->get('role') . '/user'))->with('success', 'User Berhasil di input');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/user'))->with('error', 'User Gagal di input');
        }
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
        $id_user = $this->request->getPost('id_user');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role');

        // payload API
        $payload = [
            'id_user'       => $id_user,
            'username' => $username,
            'role'     => $role,
        ];

        // dd($username,$password,$role,$payload);
        if (!empty($password)) {
            $payload['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $client = service('curlrequest');

        try {
            $response = $client->post(
                'http://172.23.39.117/ComplaintSystem/public/api/userUpdate',
                [
                    'http_errors' => false,
                    'form_params' => $payload,
                ]
            );
            // dd($payload);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal koneksi ke API');
        }

        // 🔥 WAJIB cek status API
        if ($response->getStatusCode() !== 200) {
            return redirect()->back()->with('error', 'Update user gagal (API)');
        }

        return redirect()->back()->with('success', 'User berhasil di update');
    }

    public function deleteUser($id)
    {
        $client = service('curlrequest');

        $response = $client->post(
            'http://172.23.39.117/ComplaintSystem/public/api/userDelete',
            [
                'http_errors' => false, // 🔥 PENTING
                'form_params' => [
                    'id_user' => $id
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);
        // dd($data);
        if ($data['success']) {
            return redirect()->to(base_url(session()->get('role') . '/user'))->with('success', 'User Berhasil di hapus');
        } else {
            return redirect()->to(base_url(session()->get('role') . '/user'))->with('error', 'User Gagal di hapus');
        }
    }

    public function retur()
    {
        $filterTglSch = $this->request->getPost('filter_tglsch');
        $filterNoModel = $this->request->getPost('filter_nomodel');

        $sch = $this->scheduleCelupModel->getDataComplain();

        if ($filterTglSch && $filterNoModel) {
            $sch = array_filter($sch, function ($data) use ($filterTglSch, $filterNoModel) {
                return $data['tanggal_schedule'] === $filterTglSch &&
                    (strpos($data['no_model'], $filterNoModel) !== false || strpos($data['kode_warna'], $filterNoModel) !== false);
            });
        } elseif ($filterTglSch) {
            // Filter berdasarkan tanggal saja
            $sch = array_filter($sch, function ($data) use ($filterTglSch) {
                return $data['tanggal_schedule'] === $filterTglSch;
            });
        } elseif ($filterNoModel) {
            // Filter berdasarkan nomor model atau kode warna saja
            $sch = array_filter($sch, function ($data) use ($filterNoModel) {
                return (strpos($data['no_model'], $filterNoModel) !== false || strpos($data['kode_warna'], $filterNoModel) !== false);
            });
        }


        $uniqueData = [];
        foreach ($sch as $key => $id) {
            // Ambil parameter dari data schedule
            $nomodel = $id['no_model'];
            $itemtype = $id['item_type'];
            $kodewarna = $id['kode_warna'];

            // Debug untuk memastikan parameter tidak null
            if (empty($nomodel) || empty($itemtype) || empty($kodewarna)) {
                log_message('error', "Parameter null: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip data jika ada parameter kosong
            }

            // Panggil fungsi model untuk mendapatkan qty_po dan warna
            $pdk = $this->materialModel->getQtyPOForCelup($nomodel, $itemtype, $kodewarna);

            if (!$pdk) {
                log_message('error', "Data null dari model: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip jika $pdk kosong
            }

            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];

            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {

                // Buat array data unik
                $uniqueData[] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $pdk['color'],
                    'ket_daily_cek' => $id['ket_daily_cek'],
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                ];
            }
        }

        $data = [
            'active' => $this->active,
            'title' => 'Retur GBN',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
        ];
        return view($this->role . '/retur/retur-gbn', $data);
    }
}
