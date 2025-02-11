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
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => 'Retur',
        ];
        return view($this->role . '/retur/index', $data);
    }

    public function createBon()
    {
        $no_model = $this->scheduleCelupModel->getCelupDone();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Out Celup",
            'no_model' => $no_model,
        ];
        return view($this->role . '/out/createBon', $data);
    }

    public function getItemType()
    {
        $noModel = $this->request->getPost('no_model');

        if (!$noModel) {
            return $this->response->setJSON(['error' => 'No model provided'], 400);
        }

        $itemType = $this->scheduleCelupModel->getItemTypeByNoModel($noModel);

        return $this->response->setJSON($itemType);
    }

    public function getKodeWarna()
    {
        $noModel = $this->request->getPost('no_model');
        $itemType = $this->request->getPost('item_type');

        if (!$noModel || !$itemType) {
            return $this->response->setJSON(['error' => 'Invalid input'], 400);
        }

        // Ambil data dari model
        $kodeWarna = $this->scheduleCelupModel->getKodeWarnaByNoModelDanItemType($noModel, $itemType);

        return $this->response->setJSON($kodeWarna);
    }

    public function getWarna()
    {
        $noModel = $this->request->getPost('no_model');
        $itemType = $this->request->getPost('item_type');
        $kodeWarna = $this->request->getPost('kode_warna');

        if (!$noModel || !$itemType) {
            return $this->response->setJSON(['error' => 'Invalid input'], 400);
        }

        // Ambil data dari model
        $colorCodes = $this->scheduleCelupModel->getWarnaByNoModelItemDanKode($noModel, $itemType, $kodeWarna);

        return $this->response->setJSON($colorCodes);
    }

    public function saveBon()
    {
        $data = $this->request->getPost();
        // dd($data);
        // Simpan data bon
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

        // Ambil nilai input untuk parameter pencarian id_celup
        $itemType = $data['items'][0]['item_type'] ?? null;
        $kodeWarna = $data['items'][0]['kode_warna'] ?? null;
        $noModel = $data['items'][0]['no_model'] ?? null;


        $noKarung = $data['no_karung'] ?? [];
        // $gantiRetur = isset($data['ganti_retur']) ? '1' : '0';
        $tab = count($data['harga']);

        if (!empty($noModel)) {
            $saveDataOutCelup = [];

            $id_celup = $this->scheduleCelupModel->getIdCelupbyNoModelItemTypeKodeWarna($noModel, $itemType, $kodeWarna);

            for ($h = 0; $h < $tab; $h++) {
                $gantiRetur = isset($data['ganti_retur'][$h]) ? $data['ganti_retur'][$h] : '0';
                // Pastikan no_karung tidak kosong dan merupakan array
                if (!empty($data['no_karung'][$h]) && is_array($data['no_karung'][$h])) {
                    $jmldatapertab = count($data['no_karung'][$h]); // Ambil jumlah data yang benar

                    for ($i = 0; $i < $jmldatapertab; $i++) {
                        $saveDataOutCelup[] = [
                            'id_bon' => $id_bon,
                            'id_celup' => $id_celup['id_celup'] ?? null,
                            'l_m_d' => $data['l_m_d'][$h] ?? null,
                            'harga' => $data['harga'][$h] ?? null,
                            'no_karung' => $data['no_karung'][$h][$i] ?? null, // Ambil dari indeks $i
                            'gw_kirim' => $data['gw_kirim'][$h][$i] ?? null,
                            'kgs_kirim' => $data['kgs_kirim'][$h][$i] ?? null,
                            'cones_kirim' => $data['cones_kirim'][$h][$i] ?? null,
                            'lot_kirim' => $data['lot_kirim'][$h][$i] ?? '',
                            'ganti_retur' => $gantiRetur,
                            'admin' => session()->get('username'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => '',
                        ];
                    }
                }
            }

            // Debugging sebelum insert
            // dd($saveDataOutCelup);
        }
        $this->outCelupModel->insertBatch($saveDataOutCelup);

        return redirect()->to(base_url($this->role . '/outCelup'))->with('success', 'BON Berhasil Di Simpan.');
    }



    public function generateBarcode($idBon)
    {
        // data ALL BON
        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon
        // dd($detailBon);        // Mengelompokkan data detailBon berdasarkan no_model, item_type, dan kode_warna
        $groupedDetails = [];
        foreach ($detailBon as $detail) {
            $key = $detail['no_model'] . '|' . $detail['item_type'] . '|' . $detail['kode_warna'];
            $jmlKarung =
                $gantiRetur = ($detail['ganti_retur'] == 1) ? ' / Ganti Retur' : '';
            if (!isset($groupedDetails[$key])) {
                $groupedDetails[$key] = [
                    'no_model' => $detail['no_model'],
                    'item_type' => $detail['item_type'],
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
                $barcode = $generator->getBarcode($id['id_out_celup'], $generator::TYPE_EAN_13);
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
                    'barcode' => base64_encode($barcode),
                ];
            }
        }

        // Menggabungkan data utama dan detail yang sudah dikelompokkan
        $dataBon['groupedDetails'] = array_values($groupedDetails);

        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Generate",
            'id_bon' => $idBon,
            'dataBon' => $dataBon,
        ];
        // dd($data);
        return view($this->role . '/out/generate', $data);
    }
}
