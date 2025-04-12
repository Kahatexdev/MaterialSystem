<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\PemesananModel;

class CoveringPemesananController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterMaterialModel;
    protected $pemesananModel;

    public function __construct()
    {
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->pemesananModel = new PemesananModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['covering']] != session()->get('role')) {
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
        $jenis = $this->masterMaterialModel->getJenisSpandexKaret();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis,
        ];
        return view($this->role . '/pemesanan/index', $data);
    }

    public function pemesanan($jenis)
    {
        $dataPemesanan = $this->pemesananModel->getJenisPemesananCovering($jenis);
        // dd($dataPemesanan);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataPemesanan' => $dataPemesanan,
        ];
        return view($this->role . '/pemesanan/pemesanan', $data);
    }

    public function detailPemesanan($jenis, $tgl_pakai)
    {
        $listPemesanan = $this->pemesananModel->getListPemesananCovering($jenis, $tgl_pakai);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'listPemesanan' => $listPemesanan,
        ];
        return view($this->role . '/pemesanan/detail-pemesanan', $data);
    }

    public function reportPemesananKaretCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-pemesanan-karet', $data);
    }

    public function filterPemesananKaretCovering()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananKaret($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }

    public function reportPemesananSpandexCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-pemesanan-spandex', $data);
    }

    public function filterPemesananSpandexCovering()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananSpandex($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
}
