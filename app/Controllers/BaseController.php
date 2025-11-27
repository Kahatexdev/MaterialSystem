<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\PemasukanModel;
use App\Models\StockModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\ClusterModel;
use App\Models\MasterWarnaBenangModel;
use App\Models\MasterMaterialModel;
use App\Models\OtherBonModel;
use App\Models\OpenPoModel;
use App\Models\BonCelupModel;
use App\Models\PemesananModel;
use App\Models\TotalPemesananModel;
use App\Models\PengeluaranModel;
use App\Models\PoTambahanModel;
use App\Models\TotalPoTambahanModel;
use App\Models\ReturModel;
use App\Models\UserModel;
use App\Models\MesinCelupModel;
use App\Models\TrackingPoCovering;
use App\Models\PemesananSpandexKaretModel;
use App\Models\CoveringStockModel;
use App\Models\HistoryStockCoveringModel;
use App\Models\MasterBuyerModel;
use App\Models\HistoryStock;
use App\Models\OtherOutModel;
use App\Models\WarehouseBBModel;
use App\Models\HistoryStockBBModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $role;
    protected $active;
    protected $filters;
    protected $stockModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $pemasukanModel;
    protected $masterOrderModel;
    protected $materialModel;
    protected $clusterModel;
    protected $masterWarnaBenangModel;
    protected $masterMaterialModel;
    protected $otherBonModel;
    protected $pemesananModel;
    protected $totalPemesananModel;
    protected $pengeluaranModel;
    protected $poTambahanModel;
    protected $totalPoTambahanModel;
    protected $returModel;
    protected $userModel;
    protected $openPoModel;
    protected $bonCelupModel;
    protected $mesinCelupModel;
    protected $HistoryStockCoveringModel;
    protected $trackingPoCoveringModel;
    protected $historyCoveringStockModel;
    protected $coveringStockModel;
    protected $pemesananSpandexKaretModel;
    protected $warehouseBBModel;
    protected $historyStockBBModel;
    protected $historyStock;
    protected $otherOutModel;
    protected $masterBuyerModel;
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {

        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        service('renderer')->setVar('capacityUrl', api_url('capacity'));
        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = service('session');
        $this->stockModel = new StockModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->clusterModel = new ClusterModel();
        $this->masterWarnaBenangModel = new MasterWarnaBenangModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->otherBonModel = new OtherBonModel();
        $this->pemesananModel = new PemesananModel();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->poTambahanModel = new PoTambahanModel();
        $this->totalPoTambahanModel = new TotalPoTambahanModel();
        $this->returModel = new ReturModel();
        $this->userModel = new UserModel();
        $this->openPoModel = new OpenPoModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->HistoryStockCoveringModel = new HistoryStockCoveringModel();
        $this->trackingPoCoveringModel = new TrackingPoCovering();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->coveringStockModel = new CoveringStockModel();
        $this->historyCoveringStockModel = new HistoryStockCoveringModel();
        $this->warehouseBBModel = new WarehouseBBModel();
        $this->historyStockBBModel = new HistoryStockBBModel();
        $this->historyStock = new HistoryStock();
        $this->masterBuyerModel = new MasterBuyerModel();
        $this->otherOutModel = new OtherOutModel();

        $this->request = \Config\Services::request();

        $this->role = session()->get('role');

        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['gbn']] != session()->get('role')) {
            return redirect()->to(base_url('/login'));
        }
        $this->isLogedin();
    }
}
