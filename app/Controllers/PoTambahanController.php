<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use FontLib\Table\Type\post;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;
use App\Models\ClusterModel;
use App\Models\PemasukanModel;
use App\Models\StockModel;
use App\Models\HistoryPindahPalet;
use App\Models\HistoryPindahOrder;
use App\Models\HistoryStock;
use App\Models\PengeluaranModel;
use App\Models\ReturModel;
use App\Models\OtherOutModel;
use App\Models\OtherBonModel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\PoTambahanModel;

class PoTambahanController extends BaseController
{
    protected $role;
    protected $username;
    protected $active;
    protected $filters;
    protected $request;
    protected $db;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;
    protected $clusterModel;
    protected $pemasukanModel;
    protected $stockModel;
    protected $historyPindahPalet;
    protected $historyPindahOrder;
    protected $historyStock;
    protected $pengeluaranModel;
    protected $returModel;
    protected $otherOutModel;
    protected $otherBonModel;
    protected $poTambahanModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->clusterModel = new ClusterModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->stockModel = new StockModel();
        $this->historyPindahPalet = new HistoryPindahPalet();
        $this->historyPindahOrder = new HistoryPindahOrder();
        $this->historyStock = new HistoryStock();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->returModel = new ReturModel();
        $this->otherOutModel = new OtherOutModel();
        $this->otherBonModel = new OtherBonModel();
        $this->db = \Config\Database::connect(); // Menghubungkan ke database
        $this->poTambahanModel = new PoTambahanModel();

        $this->role = session()->get('role');
        $this->username = session()->get('username');
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
        $poTambahan = $this->poTambahanModel->getData();
        $data = [
            'active' => $this->active,
            'title' => 'Po Tambahan',
            'role' => $this->role,
            'poTambahan' => $poTambahan,
        ];
        return view($this->role . '/poplus/index', $data);
    }

    public function prosesApprovePoPlusArea()
    {
        $tglPo = $this->request->getPost('tgl_poplus');
        $noModel = $this->request->getPost('no_model');
        $itemType = $this->request->getPost('item_type');
        $kodeWarna = $this->request->getPost('kode_warna');
        $status = $this->request->getPost('status');
        $area = $this->request->getPost('area');
        $tglApprove = date('Y-m-d');

        $validate = [
            'no_model'   => $noModel,
            'area'       => $area,
            'item_type'  => $itemType,
            'kode_warna' => $kodeWarna,
        ];

        $idMaterialResult = $this->materialModel->getId($validate);
        $idMaterial = array_column($idMaterialResult, 'id_material');

        if (empty($idMaterial)) {
            return redirect()->to(base_url($this->role . '/poplus'))->with('error', 'Tidak ada data material yang ditemukan.');
        }

        // Jalankan update hanya jika data ada
        $builder = $this->poTambahanModel
            ->builder()
            ->whereIn('id_material', $idMaterial)
            ->where('status', $status)
            ->like('created_at', $tglPo, 'after');

        $updated = $builder->update([
            'status' => 'approved',
            'tanggal_approve' => $tglApprove
        ]);

        if ($updated) {
            return redirect()->to(base_url($this->role . '/poplus'))->with('success', 'Data Po Tambahan Berhasil disetujui.');
        } else {
            return redirect()->to(base_url($this->role . '/poplus'))->with('error', 'Data Po Tambahan Gagal disetuji.');
        }
    }
    public function detailPoPlus()
    {
        $tglPo = $this->request->getGet('tgl_poplus');
        $noModel = $this->request->getGet('no_model');
        $itemType = $this->request->getGet('item_type');
        $kodeWarna = $this->request->getGet('kode_warna');
        $warna = $this->request->getGet('warna');
        $status = $this->request->getGet('status');
        $area = $this->request->getGet('area');

        $validate = [
            'no_model'   => $noModel,
            'area'       => $area,
            'item_type'  => $itemType,
            'kode_warna' => $kodeWarna,
        ];

        $idMaterialResult = $this->materialModel->getId($validate);
        $idMaterial = array_column($idMaterialResult, 'id_material');

        if (empty($idMaterial)) {
            return redirect()->to(base_url($this->role . '/poplus'))->with('error', 'Tidak ada data material yang ditemukan.');
        }

        $details = $this->poTambahanModel->detailPoTambahan($idMaterial, $tglPo, $status);

        $pphInisial = [];

        foreach ($details as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $loss = $items['loss'];
            $gwpcs = ($gw * $comp) / 100;
            $styleSize = urlencode($styleSize);
            $apiUrl  = 'http://192.168.1.3/CapacityApps/public/api/getDataPerinisial/' . $area . '/' . $noModel . '/' . $styleSize;

            $response = file_get_contents($apiUrl);

            if ($response === FALSE) {
                log_message('error', "API tidak bisa diakses: $apiUrl");
                return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
            } else {
                $data = json_decode($response, true);

                if (!is_array($data)) {
                    log_message('error', "Response API tidak valid: $response");
                    return $this->response->setJSON(["error" => "Data dari API tidak valid"]);
                }

                $bruto = $data['bruto'] ?? 0;
                $bs_mesin = $data['bs_mesin'] ?? 0;
                if ($gw == 0) {
                    $pph = 0;
                } else {

                    $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;
                }
                $ttl_kebutuhan = ($data['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($data['qty'] * $comp * $gw / 100 / 1000));

                $pphInisial[] = [
                    'area'  => $area,
                    'style_size'    => $items['style_size'],
                    'inisial'       => $data['inisial'],
                    'item_type'     => $itemType,
                    'kode_warna'    => $kodeWarna,
                    'color'         => $warna,
                    'ttl_kebutuhan' => $ttl_kebutuhan,
                    'gw'            => $items['gw'],
                    'loss'          => $items['loss'],
                    'composition'   => $items['composition'],
                    'jarum'         => $data['machinetypeid'] ?? null,
                    'bruto'         => $bruto,
                    'netto'         => $bruto - $data['bs_setting'] ?? 0,
                    'qty'           => $data['qty'] ?? 0,
                    'sisa'          => $data['sisa'] ?? 0,
                    'po_plus'       => $data['po_plus'] ?? 0,
                    'bs_setting'    => $data['bs_setting'] ?? 0,
                    'bs_mesin'      => $bs_mesin,
                    'pph'           => $pph,
                    'pph_persen'    => ($ttl_kebutuhan != 0) ? ($pph / $ttl_kebutuhan) * 100 : 0,
                    'po_plus'       => $items['poplus_mc_kg'] + $items['plus_pck_kg']
                ];
            }
        }

        $dataToSort = array_filter($pphInisial, 'is_array');

        usort($dataToSort, function ($a, $b) {
            return $a['inisial'] <=> $b['inisial']
                ?: $a['item_type'] <=> $b['item_type']
                ?: $a['kode_warna'] <=> $b['kode_warna'];
        });

        $data = [
            'active' => $this->active,
            'title' => 'Po Tambahan',
            'role' => $this->role,
            'detail' => $dataToSort,
            'tglPo' => $tglPo,
            'noModel' => $noModel,
            'itemType' => $itemType,
            'kodeWarna' => $kodeWarna,
            'warna' => $warna,
            'area' => $area,
        ];
        return view($this->role . '/poplus/detail', $data);
    }
    public function reportPoTambahan()
    {
        $noModel   = $this->request->getPost('model')     ?? '';
        $area   = $this->request->getPost('area')     ?? '';
        $kodeWarna = $this->request->getPost('kode_warna') ?? '';
        $tglPoDari = $this->request->getPost('tgl_po_dari') ?? '';
        $tglPoSampai = $this->request->getPost('tgl_po_sampai') ?? '';

        // 1) Ambil data
        $dataPoPlus = $this->poTambahanModel->getDataPoPlus($tglPoDari, $tglPoSampai, $noModel, $area, $kodeWarna);

        // 4) Response
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($dataPoPlus);
        }

        return view($this->role . '/poplus/report-po-tambahan', [
            'role'    => $this->role,
            'title'   => 'Report PO Tambahan',
            'active'  => $this->active,
            'poPlus' => $dataPoPlus,
        ]);
    }
    public function prosesRejectPoPlusArea()
    {
        $tglPo = $this->request->getPost('tgl_poplus');
        $noModel = $this->request->getPost('no_model');
        $itemType = $this->request->getPost('item_type');
        $kodeWarna = $this->request->getPost('kode_warna');
        $status = $this->request->getPost('status');
        $area = $this->request->getPost('area');
        $ketGbn = $this->request->getPost('ket_gbn');

        $validate = [
            'no_model'   => $noModel,
            'area'       => $area,
            'item_type'  => $itemType,
            'kode_warna' => $kodeWarna,
        ];

        $idMaterialResult = $this->materialModel->getId($validate);
        $idMaterial = array_column($idMaterialResult, 'id_material');

        if (empty($idMaterial)) {
            return redirect()->to(base_url($this->role . '/poplus'))->with('error', 'Tidak ada data material yang ditemukan.');
        }

        // Jalankan update hanya jika data ada
        $builder = $this->poTambahanModel
            ->builder()
            ->whereIn('id_material', $idMaterial)
            ->where('status', $status)
            ->like('created_at', $tglPo, 'after');

        $updated = $builder->update(['status' => 'rejected', 'ket_gbn' => $ketGbn]);

        if ($updated) {
            return redirect()->to(base_url($this->role . '/poplus'))->with('success', 'Data Po Tambahan Berhasil ditolak.');
        } else {
            return redirect()->to(base_url($this->role . '/poplus'))->with('error', 'Data Po Tambahan Gagal ditolak.');
        }
    }
}
