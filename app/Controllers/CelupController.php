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
use Picqer\Barcode\BarcodeGeneratorPNG;

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
        // dd($sch);
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
                    'admin' => $id['user_cek_status'],
                ];
            }
        }
        // dd($uniqueData);
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
        // dd($sch);
        $uniqueData = [];
        foreach ($sch as $key => $id) {
            // Ambil parameter dari data schedule
            $nomodel = $id['no_model'];
            $itemtype = $id['item_type'];
            $kodewarna = $id['kode_warna'];
            $warna = $id['warna'];
            // Debug untuk memastikan parameter tidak null
            if (empty($nomodel) || empty($itemtype) || empty($kodewarna)) {
                log_message('error', "Parameter null: no_model={$nomodel}, item_type={$itemtype}, kode_warna={$kodewarna}");
                continue; // Skip data jika ada parameter kosong
            }
            // dd($sch);
            // Panggil fungsi model untuk mendapatkan qty_po dan warna
            $pdk = $this->openPoModel->getQtyPO($nomodel,  $kodewarna, $warna, $itemtype);
            // dd ($pdk);
            if (!$pdk) {
                $id_induk = $this->openPoModel->getIdInduk($nomodel, $itemtype, $kodewarna);
                // dd ($id_induk);
                if ($id_induk) {
                    $id_po = $this->openPoModel->select('id_po,no_model,kode_warna,color,item_type')->where('no_model', $nomodel)->where('item_type', $itemtype)->where('kode_warna', $kodewarna)->first();
                    // dd ($id_po);
                    if (isset($id_po['kode_warna'], $id_po['color'], $id_po['item_type'])) {
                        $noModelCovering = $id_po['no_model'];
                        $kodeWarnaCovering = $id_po['kode_warna'];
                        $warnaCovering     = $id_po['color'];
                        $itemTypeCovering  = $id_po['item_type'];
                        // dd ($kodeWarnaCovering, $warnaCovering, $itemTypeCovering); 
                        $deliv = $this->openPoModel->getFilteredPOCov($kodeWarnaCovering, $warnaCovering, $itemTypeCovering);
                        // dd ($kodeWarnaCovering, $warnaCovering, $itemTypeCovering,$deliv);
                        $pdk = $this->openPoModel->getQtyPOForCvr($nomodel, $itemtype, $kodewarna);
                        $pdk['delivery_awal'] = $deliv[0]['delivery_awal'] ?? null;
                        $pdk['delivery_akhir'] = $deliv[0]['delivery_akhir'] ?? null;
                    } else {

                        log_message('error', 'Field kode_warna tidak ditemukan pada hasil openPoModel->find()');
                    }
                }
            }
            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];
            // dd($pdk);
            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {
                // Buat array data unik
                $uniqueData[$keys] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $id['warna'],
                    'start_mc' => $id['start_mc'],
                    'del_awal' => $pdk['delivery_awal'] ?? null,
                    'del_akhir' => $pdk['delivery_akhir'] ?? null,
                    'qty_po' => $pdk['kg_po'] ?? null,
                    'qty_po_plus' =>  $pdk['qty_po_plus'] ?? null,
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                    'tgl_bon' => $id['tanggal_bon'],
                    'tgl_celup' => $id['tanggal_celup'],
                    'tgl_bongkar' => $id['tanggal_bongkar'],
                    'tgl_press_oven' => $id['tanggal_press_oven'],
                    // 'tgl_oven' => $id['tanggal_oven'],
                    'tgl_tl' => $id['tanggal_tl'],
                    'tgl_teslab' => $id['tanggal_teslab'],
                    'tgl_rajut_pagi' => $id['tanggal_rajut_pagi'],
                    'tgl_kelos' => $id['tanggal_kelos'],
                    'serah_terima_acc' => $id['serah_terima_acc'],
                    'tgl_acc' => $id['tanggal_acc'],
                    'tgl_reject' => $id['tanggal_reject'],
                    'tgl_matching' => $id['tanggal_matching'],
                    'tgl_pb' => $id['tanggal_perbaikan'],
                    'last_status' => $id['last_status'],
                    'ket_daily_cek' => $id['ket_daily_cek'],
                    'qty_celup_plus' => $id['qty_celup_plus'],
                    'ket_schedule' => $id['ket_schedule'],
                    'admin' => $id['user_cek_status'],
                    'created_at' => $id['created_at'],
                    'updated_at' => $id['updated_at'],
                ];
            }
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
            'po' => array_column($uniqueData, 'no_model'),
        ];
        return view($this->role . '/schedule/edit-status', $data);
    }

    public function updateSchedule($id)
    {
        $lotCelup = $this->request->getPost('lot_celup');
        $tglBon = $this->request->getPost('tgl_bon');
        $tglCelup = $this->request->getPost('tgl_celup');
        $tglBongkar = $this->request->getPost('tgl_bongkar');
        $tglPressOven = $this->request->getPost('tgl_press_oven');
        // $tglOven = $this->request->getPost('tgl_oven');
        $tglTL = $this->request->getPost('tgl_tl');
        $tglTesLab = $this->request->getPost('tgl_teslab');
        $tglRajut = $this->request->getPost('tgl_rajut_pagi');
        $tglSerahTerimaAcc = $this->request->getPost('serah_terima_acc');
        $tglACC = $this->request->getPost('tgl_acc');
        $tglKelos = $this->request->getPost('tgl_kelos');
        $tglReject = $this->request->getPost('tgl_reject');
        $tglMatching = $this->request->getPost('tgl_matching');
        $tglPB = $this->request->getPost('tgl_pb');
        $qtyCelup = $this->request->getPost('qty_celup');
        $ketSchedule = $this->request->getPost('ket_schedule');
        $user  = session()->get('username');

        // Array untuk menyimpan nama variabel dan nilai tanggal
        $dates = [
            'Buka Bon' => $tglBon,
            'Celup' => $tglCelup,
            'Bongkar' => $tglBongkar,
            'Press Oven' => $tglPressOven,
            // 'Oven' => $tglOven,
            'TL' => $tglTL,
            'TesLab' => $tglTesLab,
            'Rajut Pagi' => $tglRajut,
            'Serah Terima ACC' => $tglSerahTerimaAcc,
            'ACC KK' => $tglACC,
            'Kelos' => $tglKelos,
            'Reject KK' => $tglReject,
            'Matching' => $tglMatching,
            'PB' => $tglPB,
        ];

        // Filter tanggal yang kosong atau null
        $filteredDates = array_filter($dates, function ($value) {
            return !empty($value);
        });

        $ketDailyCek = null;

        // Cari tanggal terbaru beserta labelnya
        $mostRecentDate = null;
        $mostRecentLabel = null;
        if (!empty($filteredDates)) {
            $mostRecentDate = max($filteredDates); // Tanggal paling baru
            $mostRecentLabel = array_search($mostRecentDate, $filteredDates); // Cari label sesuai tanggal
            $ketDailyCek = "$mostRecentLabel (" . date('d-m-Y', strtotime($mostRecentDate)) . ")";
        }

        // Set nilai ketDailyCek berdasarkan tanggal terbaru dan labelnya
        if ($mostRecentDate && $mostRecentLabel) {
            $mostRecentDateFormatted = date('d-m-Y', strtotime($mostRecentDate)); // Format: DD-MM-YYYY
            $ketDailyCek = "$mostRecentLabel ($mostRecentDateFormatted)";
        }

        // Hanya masukkan nilai jika tidak kosong atau null
        $dataUpdate = [];
        if ($lotCelup) $dataUpdate['lot_celup'] = $lotCelup;
        if ($tglBon) $dataUpdate['tanggal_bon'] = $tglBon;
        if ($tglCelup) $dataUpdate['tanggal_celup'] = $tglCelup;
        if ($tglBongkar) $dataUpdate['tanggal_bongkar'] = $tglBongkar;
        if ($tglPressOven) $dataUpdate['tanggal_press_oven'] = $tglPressOven;
        // if ($tglOven) $dataUpdate['tanggal_oven'] = $tglOven;
        if ($tglTL) $dataUpdate['tanggal_tl'] = $tglTL;
        if ($tglTesLab) $dataUpdate['tanggal_teslab'] = $tglTesLab;
        if ($tglRajut) $dataUpdate['tanggal_rajut_pagi'] = $tglRajut;
        if ($tglSerahTerimaAcc) $dataUpdate['serah_terima_acc'] = $tglSerahTerimaAcc;
        if ($tglACC) $dataUpdate['tanggal_acc'] = $tglACC;
        if ($tglReject) $dataUpdate['tanggal_reject'] = $tglReject;
        if ($tglMatching) $dataUpdate['tanggal_matching'] = $tglMatching;
        if ($tglPB) $dataUpdate['tanggal_perbaikan'] = $tglPB;
        if ($tglKelos) $dataUpdate['tanggal_kelos'] = $tglKelos;
        if ($user) $dataUpdate['user_cek_status'] = $user;
        if ($ketDailyCek) $dataUpdate['ket_daily_cek'] = $ketDailyCek;
        $dataUpdate['kg_celup'] = $qtyCelup;
        $dataUpdate['ket_schedule'] = $ketSchedule;

        // Jika tgl_bon diisi, update last_status menjadi 'bon'
        if (!empty($tglBon)) {
            $dataUpdate['last_status'] = 'bon';
        }

        // Jika tgl_celup diisi, update last_status menjadi 'celup'
        if (!empty($tglCelup)) {
            $dataUpdate['last_status'] = 'celup';
        }
        // Jika tgl_bongkar diisi, update last_status menjadi 'celup'
        if (!empty($tglBongkar)) {
            $dataUpdate['last_status'] = 'bongkar';
        }

        // Jika tgl_press_oven diisi, update last_status menjadi 'celup'
        if (!empty($tglPressOven)) {
            $dataUpdate['last_status'] = 'press_oven';
        }

        // Jika tgl_tl diisi, update last_status menjadi 'celup'
        if (!empty($tglTL)) {
            $dataUpdate['last_status'] = 'tes_luntur';
        }

        if (!empty($tglTesLab)) {
            $dataUpdate['last_status'] = 'tes_lab';
        }

        // Jika tgl_rajut_pagi diisi, update last_status menjadi 'celup'
        if (!empty($tglRajut)) {
            $dataUpdate['last_status'] = 'rajut';
        }

        // Jika tgl_acc diisi, update last_status menjadi 'celup'
        if (!empty($tglACC)) {
            $dataUpdate['last_status'] = 'acc';
        }

        // Jika serah_terima_acc diisi, update last_status menjadi 'serah_terima_acc'
        if (!empty($tglSerahTerimaAcc)) {
            $dataUpdate['last_status'] = 'serah_terima_acc';
        }

        // Jika tgl_reject diisi, update last_status menjadi 'done'
        if (!empty($tglReject)) {
            $dataUpdate['last_status'] = 'reject';
        }

        // Jika tgl_matching diisi, update last_status menjadi 'matching'
        if (!empty($tglMatching)) {
            $dataUpdate['last_status'] = 'matching';
        }

        // Jika tgl_perbaikan diisi, update last_status menjadi 'done'
        if (!empty($tglPB)) {
            $dataUpdate['last_status'] = 'perbaikan';
        }

        // Jika tgl_kelos diisi, update last_status menjadi 'done'
        if (!empty($tglKelos)) {
            $dataUpdate['last_status'] = 'done';
        }
        // dd($dataUpdate);
        // Validasi apakah data dengan ID yang diberikan ada
        $existingProduction = $this->scheduleCelupModel->find($id);
        if (!$existingProduction) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
        // dd($dataUpdate);
        // Perbarui data di database
        $this->scheduleCelupModel->update($id, $dataUpdate);

        // Redirect ke halaman sebelumnya dengan pesan sukses
        return redirect()->to(base_url(session()->get('role') . '/reqschedule'))->withInput()->with('success', 'Data Berhasil diupdate');
    }

    public function outCelup()
    {
        $outCelup = $this->outCelupModel->getDataOutCelup();
        // dd($outCelup);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'outCelup' => $outCelup,
        ];
        return view($this->role . '/out/index', $data);
    }

    public function getDetail($id_bon)
    {
        $data = $this->outCelupModel->getDetailByIdBon($id_bon);

        if (!$data) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON($data);
    }

    public function retur()
    {
        $filterTglSch = $this->request->getPost('filter_tglsch');
        $filterNoModel = $this->request->getPost('filter_nomodel');

        $sch = $this->scheduleCelupModel->getDataComplain();

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
                    'ket_daily_cek' => $id['ket_daily_cek'],
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
            'title' => 'Retur GBN',
            'role' => $this->role,
            'data_sch' => $sch,
            'uniqueData' => $uniqueData,
        ];
        return view($this->role . '/retur/index', $data);
    }

    public function createBon()
    {
        $no_model = $this->scheduleCelupModel->getCelupDoneAndComplain();
        // dd($no_model);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'no_model' => $no_model,
        ];
        return view($this->role . '/out/createBon', $data);
    }
    public function getItem($id)
    {
        $item = $this->scheduleCelupModel->getScheduleDetailsById($id);
        return $this->response->setJSON($item);
    }


    public function saveBon()
    {
        $data = $this->request->getPost();

        $saveDataBon = [
            'detail_sj' => $data['detail_sj'],
            'no_surat_jalan' => $data['no_surat_jalan'],
            'tgl_datang' => $data['tgl_datang'],
            'admin' => session()->get('username'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => '',
        ];

        $this->bonCelupModel->insert($saveDataBon);

        $id_bon = $this->bonCelupModel->insertID();

        $noKarung = $data['no_karung'] ?? [];
        // $gantiRetur = isset($data['ganti_retur']) ? '1' : '0';
        $tab = count($data['harga']);


        $saveDataOutCelup = [];

        for ($h = 0; $h < $tab; $h++) {

            $id_celup = $data['items'][$h]['id_celup'] ?? null;
            $lot = $this->scheduleCelupModel->select('lot_celup')->where('id_celup', $id_celup)->first();
            // dd($lot, $id_celup, $id_bon);

            $gantiRetur = isset($data['ganti_retur'][$h]) ? $data['ganti_retur'][$h] : '0';
            // Pastikan no_karung tidak kosong dan merupakan array
            if (!empty($data['no_karung'][$h]) && is_array($data['no_karung'][$h])) {
                $jmldatapertab = count($data['no_karung'][$h]); // Ambil jumlah data yang benar

                for ($i = 0; $i < $jmldatapertab; $i++) {

                    $saveDataOutCelup[] = [
                        'id_bon' => $id_bon,
                        'id_celup' => $id_celup ?? null,
                        'no_model' => $data['items'][$h]['no_model'],
                        'l_m_d' => $data['l_m_d'][$h] ?? null,
                        'harga' => $data['harga'][$h] ?? null,
                        'no_karung' => $data['no_karung'][$h][$i] ?? null, // Ambil dari indeks $i
                        'gw_kirim' => $data['gw_kirim'][$h][$i] ?? null,
                        'kgs_kirim' => $data['kgs_kirim'][$h][$i] ?? null,
                        'cones_kirim' => $data['cones_kirim'][$h][$i] ?? null,
                        'lot_kirim' => $lot['lot_celup'],
                        'ganti_retur' => $gantiRetur,
                        'operator_packing' => $data['operator_packing'][$h][$i] ?? null,
                        'shift' => $data['shift'][$h][$i] ?? null,
                        'admin' => session()->get('username'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => '',
                    ];
                }
            }
            // Perbarui total pengiriman dan status pada tabel schedule_celup
            $totalPengiriman = $this->scheduleCelupModel
                ->select('COALESCE(SUM(out_celup.kgs_kirim), 0) as total_kirim, schedule_celup.kg_celup')
                ->join('out_celup', 'schedule_celup.id_celup = out_celup.id_celup', 'left')
                ->where('out_celup.id_celup', $id_celup)
                ->first();
            if ($totalPengiriman && $totalPengiriman['total_kirim'] ?? 0 >= $totalPengiriman['kg_celup']) {
                $this->scheduleCelupModel->update($id_celup, ['id_bon' => $id_bon, 'last_status' => 'sent']);
            }
        }


        // Debugging sebelum insert
        // dd($saveDataOutCelup);

        $this->outCelupModel->insertBatch($saveDataOutCelup);

        return redirect()->to(base_url($this->role . '/outCelup'))->with('success', 'BON Berhasil Di Simpan.');
    }

    public function editBon($id_bon)
    {
        $bonData = $this->bonCelupModel->where('id_bon', $id_bon)->first();
        $celupData = $this->scheduleCelupModel->getScheduleBon($id_bon);
        $items = [];

        foreach ($celupData as $dt) {
            $idCelup = $dt['id_celup'];
            $karungData = $this->outCelupModel->dataCelup($id_bon, $idCelup);

            // Buat array baru untuk setiap item
            $item = [
                'id_celup'   => $idCelup,
                'model'      => $dt['no_model'],
                'itemType'   => $dt['item_type'],
                'kodeWarna'  => $dt['kode_warna'],
                'warna'      => $dt['warna'],
                'karung'     => [] // Inisialisasi array karung kosong
            ];

            // Tambahkan semua data karung ke dalam array
            foreach ($karungData as $out) {
                $item['karung'][] = [
                    'id_out_celup' => $out['id_out_celup'],
                    'l_m_d'        => $out['l_m_d'],
                    'harga'        => $out['harga'],
                    'no_karung'    => $out['no_karung'],
                    'gw_kirim'     => $out['gw_kirim'],
                    'kgs_kirim'    => $out['kgs_kirim'],
                    'cones_kirim'  => $out['cones_kirim'],
                    'lot_kirim'    => $out['lot_kirim'],
                ];
                $item['l_m_d'] = $out['l_m_d'];
                $item['harga'] = $out['harga'];
                $item['ganti_retur'] = $out['ganti_retur'];
            }

            // Tambahkan item ke dalam array utama
            $items[] = $item;
        }

        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'bon' => $bonData,
            'item' => $items

        ];
        // dd($data);
        return view($this->role . '/out/editBon', $data);
    }

    public function updateBon()
    {
        $id_bon = $this->request->getPost('id_bon');

        $dataBon = [
            'tgl_datang' => $this->request->getPost('tgl_datang'),
            'no_surat_jalan' => $this->request->getPost('no_surat_jalan'),
            'detail_sj' => $this->request->getPost('detail_sj'),
        ];

        // Update data utama di bonCelupModel
        $this->bonCelupModel->update($id_bon, $dataBon);

        // Update setiap karung
        $id_out_celup_list = $this->request->getPost('id_out_celup');
        $no_karung_list = $this->request->getPost('no_karung');
        $l_m_d_list = $this->request->getPost('l_m_d');
        $harga_list = $this->request->getPost('harga');
        $ganti_retur_list = $this->request->getPost('ganti_retur');
        $gw_kirim_list = $this->request->getPost('gw_kirim');
        $kgs_kirim_list = $this->request->getPost('kgs_kirim');
        $cones_kirim_list = $this->request->getPost('cones_kirim');
        $lot_kirim_list = $this->request->getPost('lot_kirim');

        foreach ($id_out_celup_list as $index => $karungIds) {
            foreach ($karungIds as $karungIndex => $id_out_celup) {
                $dataKarung = [
                    'no_karung'    => $no_karung_list[$index][$karungIndex] ?? null,
                    'l_m_d'        => $l_m_d_list[$index] ?? null, // Diperbaiki
                    'harga'        => floatval(str_replace(',', '.', $harga_list[$index] ?? 0)), // Diperbaiki
                    'ganti_retur'  => $ganti_retur_list[$index] ?? 0, // Diperbaiki
                    'gw_kirim'     => $gw_kirim_list[$index][$karungIndex] ?? null,
                    'kgs_kirim'    => $kgs_kirim_list[$index][$karungIndex] ?? null,
                    'cones_kirim'  => $cones_kirim_list[$index][$karungIndex] ?? null,
                    'lot_kirim'    => $lot_kirim_list[$index][$karungIndex] ?? null,
                ];
                $this->outCelupModel->update($id_out_celup, $dataKarung);
            }
        }

        // Redirect kembali dengan pesan sukses
        return redirect()->to(base_url($this->role . '/outCelup'))->with('success', 'Data berhasil diperbarui');
    }

    public function deleteBon($id)
    {
        $this->scheduleCelupModel->where('id_bon', $id)->set(['id_bon' => null])->update();
        $this->bonCelupModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }


    public function generateBarcode($idBon)
    {
        $path = FCPATH . 'assets/img/logo-kahatexbw.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
        // data ALL BON
        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon

        $groupedDetails = [];
        foreach ($detailBon as $detail) {
            $key = $detail['no_model'] . '|' . $detail['item_type'] . '|' . $detail['kode_warna'];
            $itemTypeAsli = $detail['item_type'];
            $ukuranBenang = strtoupper($detail['ukuran']);
            $itemTypeBaru = '';

            // Jika $ukuranBenang ada di $itemTypeAsli, hapus dan simpan hasilnya di $itemTypeBaru
            if (!empty($ukuranBenang) && strpos($itemTypeAsli, $ukuranBenang) !== false) {
                $itemTypeBaru = trim(str_replace($ukuranBenang, '', $itemTypeAsli));
            } else {
                $itemTypeBaru = $itemTypeAsli;
            }

            $gantiRetur = ($detail['ganti_retur'] == 1) ? ' / Ganti Retur' : '';
            if (!isset($groupedDetails[$key])) {
                $groupedDetails[$key] = [
                    'no_model' => $detail['no_model'],
                    'item_type' => $itemTypeBaru,
                    'spesifikasi_benang' => $detail['spesifikasi_benang'],
                    'kode_warna' => $detail['kode_warna'],
                    'warna' => $detail['warna'],
                    'buyer' => $detail['buyer'],
                    'ukuran' => $detail['ukuran'],
                    'lot_kirim' => $detail['lot_kirim'],
                    'l_m_d' => $detail['l_m_d'],
                    'harga' => $detail['harga'],
                    'detailPengiriman' =>  [],
                    'totals' => [
                        'cones_kirim' => 0,
                        'gw_kirim' => 0,
                        'kgs_kirim' => 0,
                        'admin' => $detail['admin'],
                    ],
                    'ganti_retur' => $gantiRetur,
                    'jmlKarung' => 0,
                    'barcodes' => [], // Untuk menyimpan barcode
                ];
            }
            // Menambahkan data pengiriman untuk grup ini tanpa dijumlahkan
            $groupedDetails[$key]['detailPengiriman'][] = [
                'id_out_celup' => $detail['id_out_celup'],
                'cones_kirim' => $detail['cones_kirim'],
                'gw_kirim' => $detail['gw_kirim'],
                'kgs_kirim' => $detail['kgs_kirim'],
                'lot_kirim' => $detail['lot_kirim'],
                'no_karung' => $detail['no_karung'],
            ];
            // Menambahkan nilai ke total
            $groupedDetails[$key]['totals']['gw_kirim'] += $detail['gw_kirim'];
            $groupedDetails[$key]['totals']['kgs_kirim'] += $detail['kgs_kirim'];
            $groupedDetails[$key]['totals']['cones_kirim'] += $detail['cones_kirim'];

            // Menghitung jumlah baris data detailBon pada grup ini (jumlah karung)
            $groupedDetails[$key]['jmlKarung'] = count($groupedDetails[$key]['detailPengiriman']);

            // Tambahkan ID outCelup
            $groupedDetails[$key]['idsOutCelup'][] = $detail['id_out_celup'];
        }

        // Buat instance Barcode Generator
        $generator = new BarcodeGeneratorPNG();

        // Hasilkan barcode untuk setiap ID outCelup di grup
        foreach ($groupedDetails as &$group) {
            foreach ($group['detailPengiriman'] as $outCelup => $id) {
                // Hasilkan barcode dan encode sebagai base64
                // $id_out_celup = str_pad($id['id_out_celup'], 12, '0', STR_PAD_LEFT);
                // $barcode = $generator->getBarcode($id_out_celup, $generator::TYPE_EAN_13);
                $barcode = $generator->getBarcode($id['id_out_celup'], $generator::TYPE_CODE_128);
                $group['barcodes'][] = [
                    'no_model' => $group['no_model'],
                    'item_type' => $group['item_type'],
                    'kode_warna' => $group['kode_warna'],
                    'warna' => $group['warna'],
                    'id_out_celup' => $id['id_out_celup'],
                    'gw' => $id['gw_kirim'],
                    'kgs' => $id['kgs_kirim'],
                    'cones' => $id['cones_kirim'],
                    'lot' => $id['lot_kirim'],
                    'no_karung' => $id['no_karung'],
                    'admin' => $group['totals']['admin'],
                    'barcode' => 'data:image/png;base64,' . base64_encode($barcode),
                ];
            }
        }


        $dataBon['groupedDetails'] = array_values($groupedDetails);
        dd($dataBon);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Generate",
            'id_bon' => $idBon,
            'dataBon' => $dataBon,
            'img' => $img
        ];
        // dd($data);
        return view($this->role . '/out/generate', $data);
    }

    public function editRetur($id)
    {
        $sch = $this->scheduleCelupModel->getDataByIdCelup($id);
        // dd ($sch);
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
        return view($this->role . '/retur/edit-retur', $data);
    }

    public function prosesEditRetur($id)
    {
        $submitType = $this->request->getPost('submit_type');

        if ($submitType === 'celup_ulang') {
            $dataUpdate = [
                'lot_celup' => NULL,
                'tanggal_bon' => NULL,
                'tanggal_celup' => NULL,
                'tanggal_bongkar' => NULL,
                'tanggal_press' => NULL,
                'tanggal_oven' => NULL,
                'tanggal_tl' => NULL,
                'tanggal_rajut_pagi' => NULL,
                'tanggal_acc' => NULL,
                'tanggal_kelos' => NULL,
                'tanggal_reject' => NULL,
                'tanggal_perbaikan' => NULL,
                'last_status' => 'scheduled',
                'user_cek_status' => session('username'),
            ];

            $update = $this->scheduleCelupModel->update($id, $dataUpdate);

            // Redirect ke halaman sebelumnya dengan pesan sukses
            return redirect()->to(base_url(session()->get('role') . '/retur'))->withInput()->with('success', 'Berhasil Reschedule');
        } elseif ($submitType === 'simpan') {
            $lotCelup = $this->request->getPost('lot_celup');
            $tglBon = $this->request->getPost('tgl_bon');
            $tglCelup = $this->request->getPost('tgl_celup');
            $tglBongkar = $this->request->getPost('tgl_bongkar');
            $tglPress = $this->request->getPost('tgl_press');
            $tglOven = $this->request->getPost('tgl_oven');
            $tglTL = $this->request->getPost('tgl_tl');
            $tglRajut = $this->request->getPost('tgl_rajut_pagi');
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
                $mostRecentDateFormatted = date('d-m-Y', strtotime($mostRecentDate)); // Format: DD-MM-YYYY
                $ketDailyCek = "$mostRecentLabel ($mostRecentDateFormatted)";
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
            if ($tglRajut) $dataUpdate['tanggal_rajut_pagi'] = $tglRajut;
            if ($tglACC) $dataUpdate['tanggal_acc'] = $tglACC;
            if ($tglKelos) $dataUpdate['tanggal_kelos'] = $tglKelos;
            if ($tglReject) $dataUpdate['tanggal_reject'] = $tglReject;
            if ($tglPB) $dataUpdate['tanggal_perbaikan'] = $tglPB;
            if ($ketDailyCek) $dataUpdate['ket_daily_cek'] = $ketDailyCek;

            // Prioritaskan status dengan urutan yang benar
            if (!empty($tglReject)) {
                $dataUpdate['last_status'] = 'reject';
            } elseif (!empty($tglPB)) {
                $dataUpdate['last_status'] = 'perbaikan';
            } elseif (!empty($tglKelos)) {
                $dataUpdate['last_status'] = 'done';
            } elseif (!empty($tglACC)) {
                $dataUpdate['last_status'] = 'acc';
            } elseif (!empty($tglRajut)) {
                $dataUpdate['last_status'] = 'rajut';
            } elseif (!empty($tglTL)) {
                $dataUpdate['last_status'] = 'tl';
            } elseif (!empty($tglOven)) {
                $dataUpdate['last_status'] = 'oven';
            } elseif (!empty($tglPress)) {
                $dataUpdate['last_status'] = 'press';
            } elseif (!empty($tglBongkar)) {
                $dataUpdate['last_status'] = 'bongkar';
            } elseif (!empty($tglCelup)) {
                $dataUpdate['last_status'] = 'celup';
            } elseif (!empty($tglBon)) {
                $dataUpdate['last_status'] = 'bon';
            } elseif (!empty($lotCelup)) {
                $dataUpdate['last_status'] = 'done';
            }

            // Validasi apakah data dengan ID yang diberikan ada
            $existingProduction = $this->scheduleCelupModel->find($id);
            if (!$existingProduction) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            // Perbarui data di database
            $update = $this->scheduleCelupModel->update($id, $dataUpdate);

            // Jika update berhasil dan lot_celup diisi, update out_celup
            if ($update && !empty($lotCelup)) {
                $this->outCelupModel->where('id_celup', $id)
                    ->set('lot_kirim', $lotCelup)
                    ->update();
            }
            // Redirect ke halaman sebelumnya dengan pesan sukses
            return redirect()->to(base_url(session()->get('role') . '/retur'))->withInput()->with('success', 'Data Berhasil diupdate');
        }
    }
}
