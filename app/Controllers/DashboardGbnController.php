<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\MasterOrderModel;
use App\Models\ScheduleCelupModel;
use App\Models\PemasukanModel;
use App\Models\ClusterModel;
use App\Models\StockModel;
use App\Models\PemesananModel;

class DashboardGbnController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $masterOrderModel;
    protected $scheduleCelupModel;
    protected $pemasukanModel;
    protected $clusterModel;
    protected $stockModel;
    protected $pemesananModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->clusterModel = new ClusterModel();
        $this->stockModel = new StockModel();
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
        $pemesanan = $this->pemesananModel->totalPemesananPerHari();
        $schedule = $this->scheduleCelupModel->countStatusDone();
        $pemasukan = $this->pemasukanModel->getTotalKarungMasuk();
        $pengeluaran = $this->pemasukanModel->getTotalKarungKeluar();
        $groupI = $this->clusterModel->getClusterGroupI();
        $groupII = $this->clusterModel->getClusterGroupII();
        $groupIII = $this->clusterModel->getClusterGroupIII();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'pemesanan' => $pemesanan,
            'schedule' => $schedule,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'groupI' => $groupI,
            'groupII' => $groupII,
            'groupIII' => $groupIII
        ];
        return view($this->role . '/dashboard/index', $data);
    }

    public function getGroupData()
    {
        $group = $this->request->getPost('group');

        // Tentukan function model berdasarkan group
        switch ($group) {
            case 'I':
                $groupData = $this->clusterModel->getClusterGroupI();
                return view($this->role . '/dashboard/group_I', ['groupData' => $groupData, 'group' => $group]);
            case 'II':
                $groupData = $this->clusterModel->getClusterGroupII();
                return view($this->role . '/dashboard/group_II', ['groupData' => $groupData, 'group' => $group]);
            case 'III':
                $groupData = $this->clusterModel->getClusterGroupIII();
                return view($this->role . '/dashboard/group_III', ['groupData' => $groupData, 'group' => $group]);
            default:
                return "<p class='text-center text-danger'>Tidak ada data untuk Group $group.</p>";
        }
    }
}
