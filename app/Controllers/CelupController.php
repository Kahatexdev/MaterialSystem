<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;

class CelupController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['celup']] != session()->get('role')) {
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
        return view($this->role . '/dashboard/index', $data);
    }
    public function schedule()
    {
        $sch = $this->scheduleCelupModel->getSchedule();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'data_sch' => $sch,
        ];
        return view($this->role . '/schedule/index', $data);
    }

    public function outCelup()
    {
        $scheduleDone = $this->scheduleCelupModel->getScheduleDone();

        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'schedule' => $scheduleDone,
        ];
        return view($this->role . '/out/index', $data);
    }

    public function insertBon($id_celup)
    {
        $no_model = $this->masterOrderModel->getNoModel($id_celup);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'id_celup' => $id_celup,
            'no_model' => $no_model['no_model'],
        ];
        return view($this->role . '/out/createBon', $data);
    }

    public function saveBon()
    {
        $data = $this->request->getPost();

        $saveDataBon = [
            'id_celup' => $data['id_celup'],
            'tgl_datang' => $data['tgl_datang'],
            'l_m_d' => $data['l_m_d'],
            'harga' => $data['harga'],
            'gw' => $data['gw'],
            'nw' => $data['nw'],
            'cones' => $data['cones'],
            'karung' => $data['karung'],
            'no_surat_jalan' => $data['no_surat_jalan'],
            'detail_sj' => $data['detail_sj'],
            'ganti_retur' => $data['ganti_retur'],
            'admin' => $data['admin'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => '',
        ];
        // dd($saveDataBon);
        $this->bonCelupModel->insert($saveDataBon);

        $id_bon = $this->bonCelupModel->insertID();

        $saveDataOutCelup = [
            'id_bon' => $id_bon,
            'id_celup' => $data['id_celup'],
            'gw_kirim' => $data['gw_kirim'],
            'kgs_kirim' => $data['kgs_kirim'],
            'cones_kirim' => $data['cones_kirim'],
            'lot_kirim' => $data['lot_kirim'],
            'ganti_retur' => $data['ganti_retur'],
            'admin' => $data['admin'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => '',
        ];

        $this->outCelupModel->insert($saveDataOutCelup);

        return redirect()->to(base_url($this->role . '/outCelup'))->with('success', 'BON Berhasil Di Simpan.');
    }
}
