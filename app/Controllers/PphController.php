<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;


class PphController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;


    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterOrderModel = new MasterOrderModel();


        $this->role = session()->get('role');
        if ($this->filters   = ['role' => ['monitoring']] != session()->get('role')) {
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
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);

        $data = [
            'active' => $this->active,
            'title' => 'PPH',
            'role' => $this->role,
            'area' => $area,
        ];

        return view($this->role . '/pph/index', $data);
    }

    public function tampilPerStyle()
    {
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);
        $area = json_decode($response, true);

        return view($this->role . '/pph/pphPerStyle', [
            'active' => $this->active,
            'title' => 'PPH: Per Style',
            'role' => $this->role,
            'area' => $area,
            'dataPph' => []
        ]);
    }

    public function tampilPerModel()
    {
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);

        return view($this->role . '/pph/pphPerModel', [
            'active'     => $this->active,
            'title'      => 'PPH',
            'role'       => $this->role,
            'area'       => $area,
            'mergedData' => [] // Tidak ada data sampai search diisi
        ]);
    }
    public function pphPerhari()
    {
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);

        return view($this->role . '/pph/pphPerDays', [
            'active'     => $this->active,
            'title'      => 'PPH',
            'role'       => $this->role,
            'area'       => $area,
            'mergedData' => [] // Tidak ada data sampai search diisi
        ]);
    }

    public function getDataModel()
    {
        $model = $this->request->getGet('model');
        $area = $this->request->getGet('area');
        $models = $this->materialModel->getMaterialForPPH($area, $model);
        
        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $gwpcs = ($gw * $comp) / 100;
            $styleSize = urlencode($styleSize);
            $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataPerinisial/' . $area . '/' . $model . '/' . $styleSize;

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


                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'      => $items['kode_warna'],
                    'color'      => $items['color'],
                    'gw'         => $items['gw'],
                    'composition' => $items['composition'],
                    'jarum'      => $data['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'qty'        => $data['qty'] ?? 0,
                    'sisa'       => $data['sisa'] ?? 0,
                    'po_plus'    => $data['po_plus'] ?? 0,
                    'bs_setting' => $data['bs_setting'] ?? 0,
                    'bs_mesin'   => $bs_mesin,
                    'pph'        => (($bruto * $gwpcs) + $bs_mesin) / 1000,
                ];
            }
        }
        $result = [
            'qty' => 0,
            'sisa' => 0,
            'bruto' => 0,
            'bs_setting' => 0,
            'bs_mesin' => 0
        ];

        foreach ($pphInisial as $item) {
            $key = $item['item_type'] . '-' . $item['kode_warna'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'item_type' => null,
                    'kode_warna' => null,
                    'warna' => null,
                    'pph' => 0,
                    'jarum' => null,
                    'area' => null
                ];
            }

            // Akumulasi total qty, sisa, bruto, bs_setting, dan bs_mesin
            $result['qty'] += $item['qty'];
            $result['sisa'] += $item['sisa'];
            $result['bruto'] += $item['bruto'];
            $result['bs_setting'] += $item['bs_setting'];
            $result['bs_mesin'] += $item['bs_mesin'];

            // Simpan detail per type-color
            $result[$key]['item_type'] = $item['item_type'];
            $result[$key]['kode_warna'] = $item['kode_warna'];
            $result[$key]['warna'] = $item['color'];
            $result[$key]['pph'] += $item['pph'];
            $result[$key]['jarum'] = $item['jarum'];
            $result[$key]['area'] = $item['area'];
        }

        return $this->response->setJSON($result);
    }

    public function pphinisial()
    {
        $model = $this->request->getGet('model');
        $area = $this->request->getGet('area');
        $models = $this->materialModel->getMaterialForPPH($area, $model);
        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $gwpcs = ($gw * $comp) / 100;
            $styleSize = urlencode($styleSize);
            $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataPerinisial/' . $area . '/' . $model . '/' . $styleSize;

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
                $pph = (($bruto * $gwpcs) + $bs_mesin) / 1000;


                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'  => $items['kode_warna'],
                    'color'      => $items['color'],
                    'ttl_kebutuhan' => $items['ttl_kebutuhan'],
                    'gw'         => $items['gw'],
                    'composition' => $items['composition'],
                    'jarum'      => $data['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'netto'      => $bruto - $data['bs_setting'] ?? 0,
                    'qty'        => $data['qty'] ?? 0,
                    'sisa'       => $data['sisa'] ?? 0,
                    'po_plus'    => $data['po_plus'] ?? 0,
                    'bs_setting' => $data['bs_setting'] ?? 0,
                    'bs_mesin'   => $bs_mesin,
                    'pph'        => $pph,
                    'pph_persen' => ($pph / $items['ttl_kebutuhan']) * 100,
                ];
            }
        }

        $dataToSort = array_filter($pphInisial, 'is_array');

        usort($dataToSort, function ($a, $b) {
            return $a['inisial'] <=> $b['inisial']
                ?: $a['item_type'] <=> $b['item_type']
                ?: $a['kode_warna'] <=> $b['kode_warna'];
        });
        
        return $this->response->setJSON($dataToSort);
    }
    public function getDataPerhari()
    {
        $tanggal = $this->request->getGet('tanggal');
        $area = $this->request->getGet('area');
        $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getPPhPerhari/' . $area . '/' . $tanggal;
        $response = file_get_contents($apiUrl);
        if ($response === FALSE) {
            log_message('error', "API tidak bisa diakses: $apiUrl");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        } else {
        }
        return $this->response->setJSON($tanggal);
    }
}
