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
use App\Models\MesinCelupModel;
use App\Models\CoveringStockModel;

class CoveringWarehouseController extends BaseController
{

    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;
    protected $coveringStockModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->coveringStockModel = new CoveringStockModel();

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
        $stok = $this->coveringStockModel->stokCovering();
        // dd($stok);
        $data = [
            'active' => $this->active,
            'title' => 'Warehouse',
            'role' => $this->role,
            'stok' => $stok
        ];

        return view($this->role . '/warehouse/index', $data);
    }

    // public function create()
    // {
    //     if ($this->request->isAJAX()) {
    //         $validation = \Config\Services::validation();
    //         $validation->setRules([
    //             'jenis'       => 'required',
    //             'color'       => 'required',
    //             'code'        => 'required',
    //             'lmd'         => 'required',
    //             'ttl_kg'      => 'required|numeric',
    //             'ttl_cns'     => 'required|numeric',
    //             'no_rak'      => 'required',
    //             'posisi_rak'  => 'required'
    //         ]);

    //         if (!$validation->withRequest($this->request)->run()) {
    //             return $this->response->setJSON([
    //                 'success' => false,
    //                 'message' => $validation->getErrors()
    //             ]);
    //         }

    //         $data = [
    //             'jenis'      => $this->request->getPost('jenis'),
    //             'color'      => $this->request->getPost('color'),
    //             'code'       => $this->request->getPost('code'),
    //             'lmd'        => $this->request->getPost('lmd'),
    //             'ttl_kg'     => $this->request->getPost('ttl_kg'),
    //             'ttl_cns'    => $this->request->getPost('ttl_cns'),
    //             'no_rak'     => $this->request->getPost('no_rak'),
    //             'posisi_rak' => $this->request->getPost('posisi_rak')
    //         ];

    //         $stockModel = new \App\Models\StockModel();
    //         $stockModel->insert($data);

    //         return $this->response->setJSON([
    //             'success' => true,
    //             'message' => 'Data berhasil disimpan.'
    //         ]);
    //     }

    //     return $this->response->setStatusCode(403);
    // }

    public function create()
    {
        // Aturan validasi
        $rules = [
            'jenis'      => 'required',
            'color'      => 'required',
            'code'       => 'required',
            'lmd'        => 'required',
            'box'        => 'required|numeric',
            'ttl_kg'     => 'required|numeric',
            'ttl_cns'    => 'required|numeric',
            'no_palet'   => 'required',
            'no_rak'     => 'required|numeric',
            'posisi_rak' => 'required'
        ];

        // Validasi input
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mengolah array menjadi string jika perlu
        $lmdInput = $this->request->getPost('lmd');
        $lmdValue = is_array($lmdInput) ? implode('', $lmdInput) : $lmdInput;

        $posisiRakInput = $this->request->getPost('posisi_rak');
        $posisiRakValue = is_array($posisiRakInput) ? implode(',', $posisiRakInput) : $posisiRakInput;

        $admin = session()->get('role');

        $data = [
            'jenis'      => $this->request->getPost('jenis'),
            'color'      => $this->request->getPost('color'),
            'code'       => $this->request->getPost('code'),
            'lmd'        => $lmdValue,
            'box'        => $this->request->getPost('box'),
            'ttl_kg'     => $this->request->getPost('ttl_kg'),
            'ttl_cns'    => $this->request->getPost('ttl_cns'),
            'no_palet'   => $this->request->getPost('no_palet'),
            'no_rak'     => $this->request->getPost('no_rak'),
            'posisi_rak' => $posisiRakValue,
            'admin'      => $admin
        ];

        // Simpan ke database dan redirect dengan pesan sukses atau error
        if ($this->coveringStockModel->insert($data)) {
            return redirect()->to(base_url($this->role . '/warehouse'))->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }
    }

    public function updateStock()
    {
        $json = $this->request->getJSON();
        log_message('debug', 'DATANYAAAA:::: ' . json_encode($json));
        if (!$json) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request'])->setStatusCode(400);
        }

        // Ambil data dari request
        $stockItemId = $json->stockItemId;
        // Ambil data dari JSON request
        $action = $json->action;
        $noModel = $json->no_model;
        $stockAmount = (float) $json->stockAmount; // Pastikan jadi float untuk perhitungan
        $amountCns = (int) $json->amount_cns; // Ubah ke integer
        $stockNote = $json->stockNote;

        // Ambil data dari database berdasarkan stockItemId
        $data = $this->coveringStockModel->find($stockItemId);

        // Pastikan $data tidak null sebelum mengolahnya
        if (!$data) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data stok tidak ditemukan!']);
        }

        // Jika action = "remove", kurangi ttl_kg dan ttl_cns
        if ($action === "remove") {
            $data['ttl_kg'] = max(0, $data['ttl_kg'] - $stockAmount); // Pastikan tidak negatif
            $data['ttl_cns'] = max(0, $data['ttl_cns'] - $amountCns);
        }

        // Gabungkan data dengan request JSON
        $mergedData = array_merge($data, [
            'stockItemId' => $stockItemId,
            'action'      => $action,
            'no_model'    => $noModel,
            'stockAmount' => $stockAmount,
            'amount_cns'  => $amountCns,
            'stockNote'   => $stockNote,
        ]);

        // Debug hasil gabungan
        log_message('debug', 'DATA GABUNGAN:::: ' . json_encode($mergedData));

        // Kembalikan response
        return $this->response->setJSON(['success' => true, 'data' => $mergedData]);
    }
}
