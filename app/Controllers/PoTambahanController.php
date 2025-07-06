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
        //
    }
    public function reportPoTambahan()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        // 1) Ambil data
        $dataPindah = $this->historyStock->getHistoryPindahOrder($noModel, $kodeWarna);

        // 2) Siapkan HTTP client
        $client = \Config\Services::curlrequest([
            'baseURI' => 'http://172.23.44.14/CapacityApps/public/api/',
            'timeout' => 5
        ]);

        // 3) Loop dan merge API result
        foreach ($dataPindah as &$row) {
            try {
                $res = $client->get('getDeliveryAwalAkhir', [
                    'query' => ['model' => $row['no_model_new']]
                ]);
                $body = json_decode($res->getBody(), true);
                $row['delivery_awal']  = $body['delivery_awal']  ?? '-';
                $row['delivery_akhir'] = $body['delivery_akhir'] ?? '-';
            } catch (\Exception $e) {
                $row['delivery_awal']  = '-';
                $row['delivery_akhir'] = '-';
            }
        }
        unset($row);

        // 4) Response
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($dataPindah);
        }

        return view($this->role . '/poplus/report-po-tambahan', [
            'role'    => $this->role,
            'title'   => 'Report PO Tambahan',
            'active'  => $this->active,
            'history' => $dataPindah,
        ]);
    }
}
