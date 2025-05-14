<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MasterMaterialModel;
use App\Models\MaterialModel;
use App\Models\ReturModel;
use App\Models\PemasukanModel;
use App\Models\OutCelupModel;
use App\Models\KategoriReturModel;
use App\Models\ScheduleCelupModel;



class ReturController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $masterMaterial;
    protected $materialModel;
    protected $returModel;
    protected $pemasukanModel;
    protected $outCelupModel;
    protected $kategoriReturModel;
    protected $scheduleCelupModel;


    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterial = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->returModel = new ReturModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->outCelupModel = new OutCelupModel();
        $this->kategoriReturModel = new KategoriReturModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();

        $this->role = session()->get('role');
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
        // Ambil data retur
        $dataRetur = $this->returModel->findAll();
        // dd ($dataRetur);
        $getJenisBb = $this->masterMaterial->getJenisBahanBaku();
        // $urlApi = 'http://172.23.39.114/CapacityApps/public/api/getDataArea';
        $urlApi = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $getArea = json_decode(file_get_contents($urlApi), true);
        // dd ($getArea);
        $jenis = $this->request->getGet('jenis');
        $area = $this->request->getGet('area');
        $tgl = $this->request->getGet('tgl_retur');
        $model = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        // Logika untuk menentukan apakah ada filter
        $isFiltered = $jenis || $area || $tgl || $model || $kodeWarna;
        $isFiltered = urlencode($isFiltered);
        // Ambil data hanya jika ada filter
        $retur = $isFiltered ? $this->returModel->getFilteredData($this->request->getGet()) : $dataRetur;
        $data = [
            'title' => 'Retur',
            'retur' => $retur,
            'jenis' => $getJenisBb,
            'area' => $getArea,
            'active' => $this->active,
            'role' => $this->role,
            'filters' => $this->filters,
            'isFiltered' => $isFiltered
        ];

        return view($data['role'] . '/retur/index', $data);
    }

    public function approve()
    {
        $id = $this->request->getPost('id_retur');
        $approve = $this->request->getPost('catatan');
        $text = 'Approve: ' . $approve;
        $data = [
            'keterangan_gbn' => $text,
            'waktu_acc_retur' => date('Y-m-d H:i:s'),
            'admin' => session()->get('username')
        ];
        $this->returModel->update($id, $data);
        // log_message('info', 'Data update retur: ' . json_encode($data));
        $dataRetur = $this->returModel->find($id);
        $idCelup = $this->scheduleCelupModel->getIdCelups($dataRetur);
        $barcodeNew = [
            'id_retur'       => $dataRetur['id_retur'],
            'id_celup'       => $idCelup,
            'no_model'       => $dataRetur['no_model'],
            'no_karung'     => (int)$dataRetur['krg_retur'] ?? 0,
            'kgs_kirim'          => (float)$dataRetur['kgs_retur'],
            'cones_kirim'      => (int)$dataRetur['cns_retur'],
            'lot_kirim'      => $dataRetur['lot_retur'],
            'admin'      => session()->get('username')
        ];

        // log_message('info', 'Data barcodeNew: ' . json_encode($barcodeNew));
        // dd ($barcodeNew);
        $this->outCelupModel->insert($barcodeNew);

        // dd ($dataPemasukan);
        // flashdata
        session()->setFlashdata('success', 'Data berhasil di update.');
        return redirect()->to(base_url(session()->get('role') . '/retur'));
    }

    public function reject()
    {
        $id = $this->request->getPost('id_retur');
        $reject = $this->request->getPost('catatan');
        $text = 'Reject: ' . $reject;
        $data = [
            'keterangan_gbn' => $text,
            'waktu_acc_retur' => date('Y-m-d H:i:s'),
            'admin' => session()->get('username')
        ];
        $this->returModel->update($id, $data);
        // flashdata
        session()->setFlashdata('success', 'Data berhasil di update.');
        return redirect()->to(base_url(session()->get('role') . '/retur'));
    }
    public function returArea()
    {
        $data = $this->kategoriReturModel->getKategoriRetur();
        $kategoriRetur = [];
        foreach ($data as $item) {
            $kategoriRetur[] = [
                'nama_kategori' => $item['nama_kategori'],
                'tipe_kategori' => $item['tipe_kategori']
            ];
        }
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);

        return view($this->role . '/retur/index', [
            'active'     => $this->active,
            'title'      => 'PPH',
            'role'       => $this->role,
            'area'       => $area,
            'kategori' => $kategoriRetur,
        ]);
    }

    public function listBarcodeRetur()
    {
        $listRetur = $this->returModel->listBarcodeRetur();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "List Barcode Retur",
            'listRetur' => $listRetur,
        ];
        // dd($data);
        return view($this->role . '/retur/list-barcode-retur', $data);
    }

    public function detailBarcodeRetur($tglRetur)
    {
        $detailRetur = $this->returModel->detailBarcodeRetur($tglRetur);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Detail Barcode Retur",
            'detailRetur' => $detailRetur,
            'tglRetur' => $tglRetur
        ];
        return view($this->role . '/retur/detail-barcode-retur', $data);
    }

    public function reportReturArea()
    {
        $getKategori = $this->kategoriReturModel->getKategoriRetur();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Report Retur Area",
            'getKategori' => $getKategori
        ];
        return view($this->role . '/retur/report-retur-area', $data);
    }

    public function filterReturArea()
    {
        $area = $this->request->getGet('area');
        $kategori = $this->request->getGet('kategori');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->returModel->getFilterReturArea($area, $kategori, $tanggalAwal, $tanggalAkhir);

        if (!empty($data)) {
            foreach ($data as $key => $dt) {
                $kirim = $this->outCelupModel->getDataKirim($dt['id_retur']);
                $data[$key]['kg_kirim'] = $kirim['kg_kirim'] ?? 0;
                $data[$key]['cns_kirim'] = $kirim['cns_kirim'] ?? 0;
                $data[$key]['krg_kirim'] = $kirim['krg_kirim'] ?? 0;
                $data[$key]['lot_out'] = $kirim['lot_out'] ?? '-';
            }
        }

        return $this->response->setJSON($data);
    }
}
