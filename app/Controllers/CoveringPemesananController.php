<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\PemesananModel;
use App\Models\PemesananSpandexKaretModel;

class CoveringPemesananController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterMaterialModel;
    protected $pemesananModel;
    protected $pemesananSpandexKaretModel;

    public function __construct()
    {
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();

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
        $listPemesanan = $this->pemesananSpandexKaretModel->getListPemesananCovering($jenis, $tgl_pakai);
        // dd ($listPemesanan);
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

        // Ambil hanya kolom 'tgl_pakai' lalu buat jadi unik
        $tglPakaiList = array_column($data, 'tgl_pakai');
        $uniqTglPakai = array_values(array_unique($tglPakaiList));

        return $this->response->setJSON($uniqTglPakai);
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

    public function pesanKeCovering($id)
    {
        try {
            $dataPemesanan = $this->pemesananModel->getPemesananSpandex($id);

            if (!$dataPemesanan) {
                return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
            }

            $dataSpandexKaret = [
                'id_total_pemesanan' => $dataPemesanan['id_total_pemesanan'],
                'status' => 'REQUEST',
                'admin' => session()->get('username'),
            ];

            if ($this->pemesananSpandexKaretModel->insert($dataSpandexKaret)) {
                return redirect()->back()->with('success', 'Pemesanan berhasil dikirim ke Covering.');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan data pemesanan.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePemesanan($id_psk)
    {
        $status = $this->request->getPost('status');
        // dd ($status);
        $this->pemesananSpandexKaretModel->update($id_psk, [
            'status' => $status
        ]);

        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }
}
