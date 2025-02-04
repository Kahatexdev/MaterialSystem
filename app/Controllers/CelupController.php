<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Picqer\Barcode\BarcodeGeneratorPNG;
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
        $filterTglSch = $this->request->getPost('filter_tglsch');
        $filterNoModel = $this->request->getPost('filter_nomodel');

        $sch = $this->scheduleCelupModel->getSchedule();

        if ($filterTglSch && $filterNoModel) {
            $sch = array_filter($sch, function ($data) use ($filterTglSch, $filterNoModel) {
                return $data['tanggal_schedule'] === $filterTglSch &&
                    (strpos($data['no_model'], $filterNoModel) !== false || strpos($data['kode_warna'], $filterNoModel) !== false);
            });
        } elseif ($filterTglSch) {
            // Filter berdasarkan tanggal saja
            $sch = array_filter($sch, function ($data) use ($filterTglSch) {
                return $data['tanggal_schedule'] === $filterTglSch;
            });
        } elseif ($filterNoModel) {
            // Filter berdasarkan nomor model atau kode warna saja
            $sch = array_filter($sch, function ($data) use ($filterNoModel) {
                return (strpos($data['no_model'], $filterNoModel) !== false || strpos($data['kode_warna'], $filterNoModel) !== false);
            });
        }


        $uniqueData = [];
        foreach ($sch as $key => $id) {
            // Ambil parameter dari data schedule
            $nomodel = $id['no_model'];
            $itemtype = $id['item_type'];
            $kodewarna = $id['kode_warna'];

            // Debug untuk memastikan parameter tidak null
            if (empty($nomodel) || empty($itemtype) || empty($kodewarna)) {
                log_message('error', "Parameter null: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip data jika ada parameter kosong
            }

            // Panggil fungsi model untuk mendapatkan qty_po dan warna
            $pdk = $this->materialModel->getQtyPOForCelup($nomodel, $itemtype, $kodewarna);

            if (!$pdk) {
                log_message('error', "Data null dari model: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip jika $pdk kosong
            }

            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];

            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {

                // Buat array data unik
                $uniqueData[] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $pdk['color'],
                    'start_mc' => $id['start_mc'],
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                ];
            }
        }

        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
        ];
        return view($this->role . '/schedule/reqschedule', $data);
    }
    public function editStatus($id)
    {
        $sch = $this->scheduleCelupModel->getDataByIdCelup($id);
        $uniqueData = [];
        foreach ($sch as $key => $id) {
            // Ambil parameter dari data schedule
            $nomodel = $id['no_model'];
            $itemtype = $id['item_type'];
            $kodewarna = $id['kode_warna'];

            // Debug untuk memastikan parameter tidak null
            if (empty($nomodel) || empty($itemtype) || empty($kodewarna)) {
                log_message('error', "Parameter null: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip data jika ada parameter kosong
            }

            // Panggil fungsi model untuk mendapatkan qty_po dan warna
            $pdk = $this->materialModel->getQtyPOForCelup($nomodel, $itemtype, $kodewarna);

            // Pastikan $pdk memiliki data valid sebelum dipakai
            if (!$pdk) {
                log_message('error', "Data null dari model: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip jika $pdk kosong
            }
            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];

            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {
                // Buat array data unik
                $uniqueData[$keys] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $pdk['color'],
                    'start_mc' => $id['start_mc'],
                    'del_awal' => $pdk['delivery_awal'],
                    'del_akhir' => $pdk['delivery_akhir'],
                    'qty_po' => $pdk['qty_po'],
                    'qty_po_plus' => 0,
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                    'tgl_bon' => $id['tanggal_bon'],
                    'tgl_celup' => $id['tanggal_celup'],
                    'tgl_bongkar' => $id['tanggal_bongkar'],
                    'tgl_press' => $id['tanggal_press'],
                    'tgl_oven' => $id['tanggal_oven'],
                    'tgl_tl' => $id['tanggal_tl'],
                    'tgl_rajut_pagi' => $id['tanggal_rajut_pagi'],
                    'tgl_kelos' => $id['tanggal_kelos'],
                    'tgl_acc' => $id['tanggal_acc'],
                    'tgl_reject' => $id['tanggal_reject'],
                    'tgl_pb' => $id['tanggal_perbaikan'],
                    'last_status' => $id['last_status'],
                    'ket_daily_cek' => $id['ket_daily_cek'],
                    'qty_celup_plus' => $id['qty_celup_plus'],
                    'admin' => $id['user_cek_status'],
                ];
            }
        }
        // dd($uniqueData);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
            'po' => array_column($uniqueData, 'no_model'),
        ];
        return view($this->role . '/schedule/form-edit', $data);
    }
    public function updateSchedule($id)
    {
        $lotCelup = $this->request->getPost('lot_celup');
        $tglBon = $this->request->getPost('tgl_bon');
        $tglCelup = $this->request->getPost('tgl_celup');
        $tglBongkar = $this->request->getPost('tgl_bongkar');
        $tglPress = $this->request->getPost('tgl_press');
        $tglOven = $this->request->getPost('tgl_oven');
        $tglTL = $this->request->getPost('tgl_tl');
        $tglRajut = $this->request->getPost('tgl_rajut');
        $tglACC = $this->request->getPost('tgl_acc');
        $tglKelos = $this->request->getPost('tgl_kelos');
        $tglReject = $this->request->getPost('tgl_reject');
        $tglPB = $this->request->getPost('tgl_pb');

        // Array untuk menyimpan nama variabel dan nilai tanggal
        $dates = [
            'Buka Bon' => $tglBon,
            'Celup' => $tglCelup,
            'Bongkar' => $tglBongkar,
            'Press' => $tglPress,
            'Oven' => $tglOven,
            'TL' => $tglTL,
            'Rajut Pagi' => $tglRajut,
            'ACC' => $tglACC,
            'Kelos' => $tglKelos,
            'Reject' => $tglReject,
            'PB' => $tglPB,
        ];

        // Filter tanggal yang kosong atau null
        $filteredDates = array_filter($dates, function ($value) {
            return !empty($value);
        });

        // Cari tanggal terbaru beserta labelnya
        $mostRecentDate = null;
        $mostRecentLabel = null;
        if (!empty($filteredDates)) {
            $mostRecentDate = max($filteredDates); // Tanggal paling baru
            $mostRecentLabel = array_search($mostRecentDate, $filteredDates); // Cari label sesuai tanggal
        }

        // Set nilai ketDailyCek berdasarkan tanggal terbaru dan labelnya
        if ($mostRecentDate && $mostRecentLabel) {
            $ketDailyCek = "$mostRecentLabel ($mostRecentDate)";
        }

        // Hanya masukkan nilai jika tidak kosong atau null
        $dataUpdate = [];
        if ($lotCelup) $dataUpdate['lot_celup'] = $lotCelup;
        if ($tglBon) $dataUpdate['tanggal_bon'] = $tglBon;
        if ($tglCelup) $dataUpdate['tanggal_celup'] = $tglCelup;
        if ($tglBongkar) $dataUpdate['tanggal_bongkar'] = $tglBongkar;
        if ($tglPress) $dataUpdate['tanggal_press'] = $tglPress;
        if ($tglOven) $dataUpdate['tanggal_oven'] = $tglOven;
        if ($tglTL) $dataUpdate['tanggal_tl'] = $tglTL;
        if ($tglRajut) $dataUpdate['tanggal_rajut'] = $tglRajut;
        if ($tglACC) $dataUpdate['tanggal_acc'] = $tglACC;
        if ($tglKelos) $dataUpdate['tanggal_kelos'] = $tglKelos;
        if ($tglReject) $dataUpdate['tanggal_reject'] = $tglReject;
        if ($tglPB) $dataUpdate['tanggal_pb'] = $tglPB;
        if ($ketDailyCek) $dataUpdate['ket_daily_cek'] = $ketDailyCek;

        // Jika tgl_celup diisi, update last_status menjadi 'celup'
        if (!empty($tglCelup)) {
            $dataUpdate['last_status'] = 'celup';
        }

        // Jika tgl_kelos diisi, update last_status menjadi 'done'
        if (!empty($tglKelos)) {
            $dataUpdate['last_status'] = 'done';
        }

        // Validasi apakah data dengan ID yang diberikan ada
        $existingProduction = $this->scheduleCelupModel->find($id);
        if (!$existingProduction) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Perbarui data di database
        $this->scheduleCelupModel->update($id, $dataUpdate);

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->to(base_url(session()->get('role') . '/reqschedule'))->withInput()->with('success', 'Data Berhasil diupdate');
    }

    public function outCelup()
    {
        $scheduleDone = $this->scheduleCelupModel->getScheduleDone();
        $uniqueData = [];

        foreach ($scheduleDone as $key => $id) {
            // Log::debug($key); // atau var_dump($key);
            $noModel = $id['no_model'];
            $itemType = $id['item_type'];
            $kodeWarna = $id['kode_warna'];

            $dataPo = $this->materialModel->getQtyPOForCelup($noModel, $itemType, $kodeWarna);

            $uniqueData[$key] = [
                'idCelup' => $id['id_celup'],
                'noModel' => $noModel,
                'itemType' => $itemType,
                'kodeWarna' => $kodeWarna,
                'warna' => $id['warna'],
                'startMc' => $id['start_mc'],
                'qtyPo' => number_format($dataPo['qty_po'], 2),
                'qtyPoPlus' => '',
                'tanggalSchedule' => $id['tanggal_schedule'],
                'qtyCelup' => number_format($id['qty_celup'], 2),
                'qtyCelupPlus' => number_format($id['qty_celup_plus'], 2),
            ];
        }

        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'schedule' => $uniqueData,
        ];
        return view($this->role . '/out/index', $data);
    }

    public function retur()
    {
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => 'Retur',
        ];
        return view($this->role . '/retur/index', $data);
    }

    // public function insertBon($id_celup)
    // {
    //     $no_model = $this->masterOrderModel->getNoModel($id_celup);
    //     $data = [
    //         'role' => $this->role,
    //         'active' => $this->active,
    //         'title' => "Out Celup",
    //         'id_celup' => $id_celup,
    //         'no_model' => $no_model['no_model'],
    //     ];
    //     return view($this->role . '/out/createBon', $data);
    // }
    public function insertBon()
    {
        // $no_model = $this->masterOrderModel->getNoModel($id_celup);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            // 'id_celup' => $id_celup,
            // 'no_model' => $no_model['no_model'],
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
            'admin' => session()->get('username'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => '',
        ];

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
            'admin' => session()->get('username'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => '',
        ];

        $this->outCelupModel->insert($saveDataOutCelup);

        return redirect()->to(base_url($this->role . '/outCelup'))->with('success', 'BON Berhasil Di Simpan.');
    }
    public function generate($text = '1') // Contoh default text
    {
        // Inisialisasi generator barcode
        $generator = new BarcodeGeneratorPNG();

        // Generate barcode PNG
        $barcode = $generator->getBarcode($text, $generator::TYPE_CODE_93);

        // Atur header agar output berupa gambar PNG
        header('Content-Type: image/png');

        // Tampilkan barcode
        echo $barcode;
        exit;
    }
}
