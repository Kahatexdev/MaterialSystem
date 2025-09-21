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
use App\Models\KategoriReturModel;
use App\Models\PengeluaranModel;
use App\Models\ReturModel;
use App\Models\OtherOutModel;
use App\Models\OtherBonModel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpParser\Node\Expr\FuncCall;

class WarehouseController extends BaseController
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
    protected $kategoriModel;

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
        $this->kategoriModel = new KategoriReturModel();
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
        // $updateOrder = $this->masterOrderModel->getNullMc();

        // foreach ($updateOrder as $od) {
        //     $reqStartMc = 'http://172.23.44.14/CapacityApps/public/api/reqstartmc/' . $od['no_model'];

        //     try {
        //         // Fetch data dari API
        //         $json = file_get_contents($reqStartMc);
        //         // Decode JSON response
        //         $startMc = json_decode($json, true);
        //         if (empty($startMc)) {
        //             log_message('error', 'pdk ' . $od['no_model'] . ' gaada start mc');
        //         } else {
        //             $this->masterOrderModel->update(
        //                 $od['id_order'],
        //                 ['start_mc' => $startMc['start_mc']]
        //             );
        //         }
        //     } catch (\Exception $e) {

        //         // Log error
        //         log_message('error', 'Error fetching API data: ' . $e->getMessage());
        //     }
        // }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/index', $data);
    }
    // public function pemasukan()
    // {
    //     $id = $this->request->getPost('barcode');

    //     // $id = base64_decode($id);
    //     // dd($id);
    //     $cluster = $this->clusterModel->getDataCluster();

    //     // Ambil data dari session (jika ada)
    //     $existingData = session()->get('dataOut') ?? [];

    //     if (!empty($id)) {
    //         // Cek apakah barcode sudah ada di data yang tersimpan
    //         foreach ($existingData as $item) {
    //             if ($item['id_out_celup'] == $id) {
    //                 session()->setFlashdata('error', 'Barcode sudah ada di tabel!' . $id);
    //                 return redirect()->to(base_url($this->role . '/pemasukan'));
    //             }
    //         }

    //         // Ambil data dari database berdasarkan barcode yang dimasukkan
    //         $outCelup = $this->outCelupModel->getDataOut($id);
    //         if (empty($outCelup)) {
    //             $dataRetur = $this->returModel->getDataRetur($id);

    //             if (empty($dataRetur)) {
    //                 session()->setFlashdata('error', 'Data tidak ditemukan!');
    //                 return redirect()->to(base_url($this->role . '/pemasukan'));
    //             }

    //         }

    //         session()->setFlashdata('error', 'Barcode tidak ditemukan di database!' . $id);
    //             return redirect()->to(base_url($this->role . '/pemasukan'));
    //         } elseif (!empty($outCelup)) {
    //             // Tambahkan data baru ke dalam array
    //             $existingData = array_merge($existingData, $outCelup);
    //         }

    //         // Simpan kembali ke session
    //         session()->set('dataOut', $existingData);

    //         // Redirect agar form tidak resubmit saat refresh
    //         return redirect()->to(base_url($this->role . '/pemasukan'));
    //     }

    //     $data = [
    //         'active' => $this->active,
    //         'title' => 'Material System',
    //         'role' => $this->role,
    //         'dataOut' => $existingData, // Tampilkan data dari session
    //         'cluster' => $cluster,
    //         'error' => session()->getFlashdata('error'),
    //     ];

    //     return view($this->role . '/warehouse/form-pemasukan', $data);

    // }


    public function pemasukan()
    {
        $id = $this->request->getPost('barcode');
        $cluster = $this->clusterModel->getDataCluster();
        $kategori = $this->kategoriModel->findAll();
        // dd(session()->get('dataOut'));
        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataOut') ?? [];
        // dd ($existingData);

        if (!empty($id)) {
            // 1. Cek duplikasi
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', "Barcode sudah ada di tabel! ({$id})");
                    return redirect()->to(base_url($this->role . "/pemasukan"));
                }
            }

            // 2. Coba ambil dari outCelup
            $outCelup = $this->outCelupModel->getDataOut($id);
            // log_message('debug', 'Data outCelup: ' . json_encode($outCelup)); // Debugging
            if (!empty($outCelup)) {
                $newData = $outCelup;
            } else {
                // 3. Jika kosong, coba ambil dari retur
                $findId = $this->outCelupModel->find($id);
                if (empty($findId)) {
                    session()->setFlashdata('error', "Data tidak ditemukan! ({$id})");
                    return redirect()->to(base_url($this->role . "/pemasukan"));
                }
                $dataRetur = $this->returModel->getDataRetur($id, $findId['id_retur']);
                // log_message('debug', 'Data retur: ' . json_encode($dataRetur)); // Debugging
                if (!empty($dataRetur)) {
                    $newData = $dataRetur;
                } else {
                    // 4. Keduanya kosong → error
                    session()->setFlashdata('error', "Data tidak ditemukan! ({$id})");
                    return redirect()->to(base_url($this->role . "/pemasukan"));
                }
            }

            // 5. Merge & simpan session, lalu redirect
            $existingData = array_merge($existingData, $newData);
            session()->set('dataOut', $existingData);
            return redirect()->to(base_url($this->role . "/pemasukan"));
        }

        // Tampilkan form jika bukan POST atau barcode kosong
        $data = [
            'active'   => $this->active,
            'title'    => 'Material System',
            'role'     => $this->role,
            'dataOut'  => $existingData,
            'cluster'  => $cluster,
            'kategori'  => $kategori,
            'error'    => session()->getFlashdata('error'),
        ];

        return view($this->role . "/warehouse/form-pemasukan", $data);
    }

    public function prosesPemasukan()
    {
        $action = $this->request->getPost('action'); // Ambil tombol yang diklik

        if ($action === 'simpan') {
            // Proses Simpan Pemasukan
            return $this->prosesSimpanPemasukan();
        } elseif ($action === 'komplain') {
            // Proses Komplain
            return $this->prosesKomplain();
        } else {
            session()->setFlashdata('error', 'Aksi tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }
    }
    public function prosesSimpanPemasukan()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang
        // dd ($checkedIds);
        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $idOutCelup  = $this->request->getPost('id_out_celup');
        $noModels    = $this->request->getPost('no_model');
        $itemTypes   = $this->request->getPost('item_type');
        $kodeWarnas  = $this->request->getPost('kode_warna');
        $warnas      = $this->request->getPost('warna');
        $kgsMasuks   = $this->request->getPost('kgs_kirim') ?? $this->request->getPost('kgs_retur');
        $cnsMasuks   = $this->request->getPost('cns_kirim') ?? $this->request->getPost('cns_retur');
        $tglMasuks   = $this->request->getPost('tgl_masuk');
        $namaClusters = $this->request->getPost('cluster');
        $lotKirim    = $this->request->getPost('lot_kirim') ?? $this->request->getPost('lot_retur');
        $idRetur    = $this->request->getPost('id_retur') ?? null;
        // dd ($idOutCelup, $noModels, $itemTypes, $kodeWarnas, $warnas, $kgsMasuks, $cnsMasuks, $tglMasuks, $namaClusters, $lotKirim , $idRetur);
        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }

        // Validasi cluster
        $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();
        if ($clusterExists === 0) {
            session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $dataPemasukan = [];
        foreach ($checkedIds as $key => $idOut) {
            $dataPemasukan[] = [
                'id_out_celup'  => $idOutCelup[$key] ?? null,
                // 'no_model'      => $noModels[$key] ?? null,
                // 'item_type'     => $itemTypes[$key] ?? null,
                // 'kode_warna'    => $kodeWarnas[$key] ?? null,
                // 'warna'         => $warnas[$key] ?? null,
                // 'kgs_masuk'     => $kgsMasuks[$key] ?? null,
                // 'cns_masuk'     => $cnsMasuks[$key] ?? null,
                'tgl_masuk'     => $tglMasuks[$key] ?? null,
                'nama_cluster'  => $namaClusters,
                'admin'         => session()->get('username')
            ];
        }

        // Pastikan data pemasukan ada sebelum insert
        if (empty($dataPemasukan)) {
            session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
            return redirect()->to($this->role . '/pemasukan');
        }

        // Update session dataOut jika perlu
        $checked = session()->get('dataOut');
        if (!empty($checked)) {
            $idToRemove = array_column($dataPemasukan, 'id_out_celup');
            $filteredChecked = array_filter($checked, function ($tes) use ($idToRemove) {
                return !in_array($tes['id_out_celup'], $idToRemove);
            });
            if (!empty($filteredChecked)) {
                session()->set('dataOut', array_values($filteredChecked));
            } else {
                session()->remove('dataOut');
            }
        }

        // Cek duplikat pemasukan
        $cekDuplikat = $this->pemasukanModel
            ->whereIn('id_out_celup', array_column($dataPemasukan, 'id_out_celup'))
            ->countAllResults();

        if ($cekDuplikat == 0) {
            // Insert batch ke tabel pemasukan
            if ($this->pemasukanModel->insertBatch($dataPemasukan)) {

                // Persiapkan data stock untuk masing-masing record
                $dataStock = [];
                foreach ($checkedIds as $key => $idOut) {
                    $dataStock[] = [
                        'no_model'    => $noModels[$key] ?? null,
                        'item_type'   => $itemTypes[$key] ?? null,
                        'kode_warna'  => $kodeWarnas[$key] ?? null,
                        'warna'       => $warnas[$key] ?? null,
                        'kgs_in_out'  => $kgsMasuks[$key] ?? null,
                        'cns_in_out'  => $cnsMasuks[$key] ?? null,
                        'krg_in_out'  => 1, // Asumsikan setiap pemasukan hanya 1 kali
                        'lot_stock'   => $lotKirim[$key] ?? null,
                        'nama_cluster' => $namaClusters,
                        'admin'       => session()->get('username')
                    ];
                }

                // Looping untuk update/insert stock dan update id_stok di pemasukan
                foreach ($dataStock as $key => $stock) {
                    // $idOut = $checkedIds[$key] ?? null;
                    $idOut = $idOutCelup[$key] ?? null;
                    $idRetur = $this->request->getPost('id_retur')[$key] ?? null;

                    // Cek stok lama/bikin stok baru (sudah OK di kode Agan)
                    $existingStock = $this->stockModel
                        ->where('no_model', $stock['no_model'])
                        ->where('item_type', $stock['item_type'])
                        ->where('kode_warna', $stock['kode_warna'])
                        ->where('nama_cluster', $stock['nama_cluster'])
                        ->where('lot_stock', $stock['lot_stock'])
                        ->first();

                    if ($existingStock) {
                        $this->stockModel->update($existingStock['id_stock'], [
                            'kgs_in_out' => $existingStock['kgs_in_out'] + $stock['kgs_in_out'],
                            'cns_in_out' => $existingStock['cns_in_out'] + $stock['cns_in_out'],
                            'krg_in_out' => $existingStock['krg_in_out'] + 1
                        ]);
                        $idStok = $existingStock['id_stock'];
                    } else {
                        $this->stockModel->insert($stock);
                        $idStok = $this->stockModel->getInsertID();
                        // log_message('debug', 'ID Stok baru: ' . $idStok); // Debugging
                    }

                    // === UPDATE PEMASUKAN BERDASARKAN SUMBERNYA (gabungan RETUR & OUT_CELUP) ===
                    $sql = "
                            UPDATE pemasukan p
                            INNER JOIN out_celup oc 
                                ON oc.id_out_celup = p.id_out_celup
                            LEFT  JOIN retur r 
                                ON r.id_retur      = oc.id_retur
                            LEFT  JOIN schedule_celup sc 
                                ON sc.id_celup     = oc.id_celup
                            SET p.id_stock = ?
                            WHERE
                            -- Pilih baris RETUR jika ada, else baris SCHEDULE
                            (
                                r.id_retur IS NOT NULL 
                                AND r.id_retur = ?
                            )
                            OR
                            (
                                r.id_retur IS     NULL 
                                AND oc.id_out_celup = ?
                            )
                            -- Kemudian cocokkan atribut model/item/warna secara dinamis:
                            AND COALESCE(r.no_model,      sc.no_model)      = ?
                            AND COALESCE(r.item_type,     sc.item_type)     = ?
                            AND COALESCE(r.kode_warna,    sc.kode_warna)    = ?
                            AND p.nama_cluster                           = ?
                        ";
                    $params = [
                        $idStok,              // untuk SET p.id_stock
                        $idRetur,             // untuk kondisi r.id_retur = ?
                        $idOut,               // untuk kondisi sc.id_celup  = ?
                        $stock['no_model'],   // untuk membandingkan no_model
                        $stock['item_type'],  // untuk membandingkan item_type
                        $stock['kode_warna'], // untuk membandingkan kode_warna
                        $stock['nama_cluster']
                    ];
                    $this->db->query($sql, $params);
                    // dd($params);      
                }

                session()->setFlashdata('success', 'Data berhasil dimasukkan.');
            }
        } else {
            session()->setFlashdata('error', 'Gagal, Data pemasukan sudah ada.');
        }
        return redirect()->to($this->role . '/pemasukan');
    }

    private function prosesKomplain()
    {
        $checkedIds = $this->request->getPost('checked_id');
        $idOutCelup = $this->request->getPost('id_out_celup');
        $alasan = $this->request->getPost('alasan');
        $kategori = $this->request->getPost('kategori_retur');
        $post = $this->request->getPost();
        // dd($post);
        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dikomplain.');
            return redirect()->to($this->role . '/pemasukan');
        } elseif (empty($alasan) || empty($kategori)) {
            session()->setFlashdata('error', 'Alasan dan Kategori Tidak Boleh Kosong.');
            return redirect()->to($this->role . '/pemasukan');
        }

        $idCelup = $this->outCelupModel->getIdCelups($idOutCelup);

        // Tambahkan proses komplain sesuai kebutuhanmu
        $update = $this->scheduleCelupModel
            ->whereIn('id_celup', array_column($idCelup, 'id_celup'))
            ->set([
                'last_status' => 'complain',
                'ket_daily_cek' => $alasan
            ])
            ->update();

        if ($update) {
            $existing = session()->get('dataOut') ?? [];
            $filtered = array_filter($existing, fn($item) => !in_array($item['id_out_celup'], $post['id_out_celup']));
            session()->set('dataOut', array_values($filtered));
        }

        $dataRetur = [];
        foreach ($checkedIds as $idx => $outId) {
            $dataRetur[] = [
                'no_model'   => $post['no_model'][$idx],
                'item_type'  => $post['item_type'][$idx],
                'kode_warna' => $post['kode_warna'][$idx],
                'warna'      => $post['warna'][$idx],
                'area_retur' => 'GUDANG BENANG',
                'tgl_retur'  => date('Y-m-d'),
                'kgs_retur'  => $post['kgs_kirim'][$idx],
                'cns_retur'  => $post['cns_kirim'][$idx],
                'lot_retur'  => $post['lot_kirim'][$idx],
                'krg_retur'  => 1,
                'kategori'  => $post['kategori_retur'],
                'keterangan_area'  => null,
                'keterangan_gbn'  => $post['alasan'] ?? '',
                'waktu_acc_retur'  => null,
                'admin'  => session()->get('username'),
            ];
        }

        //SUM berdasarkan no_model, item_type, kode_warna, dan warna
        $grouped = [];
        foreach ($dataRetur as $row) {
            // buat key unik per kombinasi 4 kolom
            $key = $row['no_model']
                . '|' . $row['item_type']
                . '|' . $row['kode_warna']
                . '|' . $row['warna'];

            if (!isset($grouped[$key])) {
                // pertama kali, simpan seluruh row
                $grouped[$key] = $row;
            } else {
                // sudah ada, cukup sum kgs_retur, dan cns_retur
                $grouped[$key]['kgs_retur'] += $row['kgs_retur'];
                $grouped[$key]['cns_retur'] += $row['cns_retur'];
                $grouped[$key]['krg_retur'] += $row['krg_retur'];
            }
        }
        // Re-index jadi array numerik
        $dataRetur = array_values($grouped);

        // Insert batch
        $inserted = $this->returModel->insertBatch($dataRetur);

        // Flashdata & redirect
        if ($update && $inserted) {
            session()->setFlashdata('success', 'Data berhasil dikomplain dan retur disimpan.');
        } else {
            session()->setFlashdata('error', 'Gagal memproses komplain atau menyimpan retur.');
        }

        return redirect()->to($this->role . '/pemasukan');
    }
    public function reset_pemasukan()
    {
        session()->remove('dataOut');
        return redirect()->to(base_url($this->role . '/pemasukan'));
    }
    public function hapusListPemasukan()
    {
        $id = $this->request->getPost('id');

        // Ambil data dari session
        $existingData = session()->get('dataOut') ?? [];

        // Cek apakah data dengan ID yang dikirim ada di session
        foreach ($existingData as $key => $item) {
            if ($item['id_out_celup'] == $id) {
                // Hapus data tersebut dari array
                unset($existingData[$key]);
                // Update session dengan data yang sudah dihapus
                session()->set('dataOut', array_values($existingData));
                // Debug response
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function getCluster()
    {
        $kgs = $this->request->getPost('kgs');

        if ($kgs === null || $kgs === '') {
            return $this->response->setJSON([]); // Jika kosong, kirim array kosong
        }

        $data = $this->clusterModel->getCluster($kgs);

        return $this->response->setJSON($data);
    }
    public function getItemTypeByModel($no_model)
    {
        $retur = $this->request->getGet('retur');
        // Debug dulu isi yang diterima
        log_message('debug', 'No Model: ' . $no_model . ', Retur: ' . $retur);

        // Ambil da ta berdasarkan no_model yang dipilih
        if ($retur == 0) {
            $itemTypes = $this->outCelupModel->getItemTypeByModel($no_model);  // Gantilah dengan query sesuai kebutuhan
        } else {
            $itemTypes = $this->returModel->getItemTypeByModel($no_model);
        }

        // Return data dalam bentuk JSON
        return $this->response->setJSON($itemTypes);
    }
    public function getKodeWarna()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));
        $retur = $this->request->getGet('retur');

        // $coba = 'Y24046';
        // $coba2 = 'ACRYLIC TEXLAN 1/36';

        // log_message('debug', "$coba Fetching kode warna for no_model: $no_model, item_type: $item_type");
        if ($retur == 0) {
            $kodeWarna = $this->outCelupModel->getKodeWarnaByModelAndItemType($noModel, $itemType);
        } else {
            $kodeWarna = $this->returModel->getKodeWarnaByModelAndItemType($noModel, $itemType);
        }

        return $this->response->setJSON($kodeWarna);
    }
    public function getWarnaDanLot()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));
        $kodeWarna = $this->request->getGet('kodeWarna');
        $retur = $this->request->getGet('retur');

        // log_message('debug', "Fetching warna & lot for no_model: $no_model, item_type: $item_type, kode_warna: $kode_warna");
        if ($retur == 0) {
            $warna = $this->outCelupModel->getWarnaByKodeWarna($noModel, $itemType, $kodeWarna);
            $lotList = $this->outCelupModel->getLotByKodeWarna($noModel, $itemType, $kodeWarna);
        } else {
            $warna = $this->returModel->getWarnaByKodeWarna($noModel, $itemType, $kodeWarna);
            $lotList = $this->returModel->getLotByKodeWarna($noModel, $itemType, $kodeWarna);
        }

        return $this->response->setJSON([
            'warna' => is_array($warna) ? ($warna['warna'] ?? '') : $warna,
            'lot' => $lotList
        ]);
    }
    public function getKgsDanCones()
    {
        $no_model = $this->request->getGet('noModel');
        $item_type = $this->request->getGet('itemType');
        $kode_warna = $this->request->getGet('kodeWarna');
        $lot_kirim = $this->request->getGet('lotKirim');
        $no_karung = $this->request->getGet('noKarung');
        $retur = $this->request->getGet('retur');

        try {
            if ($retur == 0) {
                $data = $this->outCelupModel->getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung);
            } else {
                $data = $this->returModel->getKgsDanCones($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung);
            }
            log_message('error', "PARAM: retur=$retur, no_model=$no_model, item_type=$item_type, kode_warna=$kode_warna, lot_kirim=$lot_kirim, no_karung=$no_karung");
            log_message('error', "DATA: " . json_encode($data));

            if ($data) {
                return $this->response->setJSON([
                    'success' => true,
                    'kgs_kirim' => $data['kgs_kirim'],
                    'cones_kirim' => $data['cones_kirim'],
                    'gw_kirim' => $data['gw_kirim'],
                    'id_out_celup' => $data['id_out_celup']
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
        } catch (\Exception $e) {
            // log_message('error', 'Error getKgsDanCones: ' . $e->getMessage()); // Log error
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server']);
        }
    }
    public function prosesPemasukanManual()
    {
        $action = $this->request->getPost('action'); // Ambil nilai tombol yang diklik
        $retur = $this->request->getPost('retur') === 'on' ? 1 : 0;

        if ($action === 'komplain') {
            $idOutCelup = $this->request->getPost('id_out_celup');
            $alasan = $this->request->getPost('alasan');

            $idCelup = $this->outCelupModel->getIdCelup($idOutCelup);

            if (!$idCelup) {
                session()->setFlashdata('error', 'Data tidak ditemukan.');
                return redirect()->back();
            } elseif (empty($alasan)) {
                session()->setFlashdata('error', 'Alasan tidak boleh kosong.');
                return redirect()->back();
            }
            $update = $this->scheduleCelupModel
                ->where('id_celup', $idCelup)
                ->set([
                    'last_status' => 'complain',
                    'ket_daily_cek' => $alasan
                ])
                ->update();

            if ($update) {
                session()->setFlashdata('success', 'Komplain berhasil dikirim.');
            } else {
                session()->setFlashdata('error', 'Komplain gagal dikirim.');
            }
            return redirect()->to($this->role . '/pemasukan');
        } elseif ($action === 'simpan') {

            $idOutCelup = $this->request->getPost('id_out_celup');
            $noModels = $this->request->getPost('no_model');
            $itemTypes = $this->request->getPost('item_type');
            $kodeWarnas = $this->request->getPost('kode_warna');
            $warnas = $this->request->getPost('warna');
            $kgsMasuks = $this->request->getPost('kgs_kirim');
            $cnsMasuks = $this->request->getPost('cns_kirim');
            $tglMasuks = $this->request->getPost('tgl_kirim');
            $namaClusters = $this->request->getPost('cluster');
            $lotKirim = $this->request->getPost('lot_kirim');

            // Pastikan nama_cluster ada di dalam tabel cluster
            $clusterExists = $this->clusterModel->where('nama_cluster', $namaClusters)->countAllResults();

            if ($clusterExists === 0) {
                session()->setFlashdata('error', 'Cluster yang dipilih tidak valid.');
                return redirect()->to($this->role . '/pemasukan');
            }

            $dataPemasukan = [
                'id_out_celup' => $idOutCelup,
                'no_model' => $noModels,
                'item_type' => $itemTypes,
                'kode_warna' => $kodeWarnas,
                'warna' => $warnas,
                'kgs_kirim' => $kgsMasuks,
                'cones_kirim' => $cnsMasuks,
                'tgl_masuk' => $tglMasuks,
                'nama_cluster' => $namaClusters,
                'admin' => session()->get('username')
            ];
            // dd($dataPemasukan);
            // Debugging: cek apakah data tidak kosong sebelum insert
            if (empty($dataPemasukan)) {
                session()->setFlashdata('error', 'Tidak ada data yang dimasukkan.');
                return redirect()->to($this->role . '/pemasukan');
            }

            $cekDuplikat = $this->pemasukanModel
                ->where('id_out_celup', $idOutCelup)
                ->countAllResults();

            if ($cekDuplikat == 0) {
                //insert tabel pemasukan
                if ($this->pemasukanModel->insert($dataPemasukan)) {
                    $idPemasukan = $this->pemasukanModel->getInsertID();
                    if ($idPemasukan) {

                        $dataStock = [
                            'no_model' => $noModels,
                            'item_type' => $itemTypes,
                            'kode_warna' => $kodeWarnas,
                            'warna' => $warnas,
                            'kgs_in_out' => $kgsMasuks,
                            'cns_in_out' => $cnsMasuks,
                            'krg_in_out' => 1, // Asumsikan setiap pemasukan hanya 1 kali
                            'lot_stock' => $lotKirim,
                            'nama_cluster' => $namaClusters,
                            'admin' => session()->get('username')
                        ];


                        $existingStock = $this->stockModel
                            ->where('no_model', $dataStock['no_model'])
                            ->where('item_type', $dataStock['item_type'])
                            ->where('kode_warna', $dataStock['kode_warna'])
                            ->where('lot_stock', $dataStock['lot_stock'])
                            ->where('nama_cluster', $dataStock['nama_cluster'])
                            ->first();

                        if ($existingStock) {
                            $this->stockModel->update($existingStock['id_stock'], [
                                'kgs_in_out' => $existingStock['kgs_in_out'] + $dataStock['kgs_in_out'],
                                'cns_in_out' => $existingStock['cns_in_out'] + $dataStock['cns_in_out'],
                                'krg_in_out' => $existingStock['krg_in_out'] + 1
                            ]);
                            $idStok = $existingStock['id_stock'];
                        } else {
                            $this->stockModel->insert($dataStock);
                            $idStok = $this->stockModel->getInsertID();
                        }

                        $this->pemasukanModel
                            ->set('id_stock', $idStok)
                            ->where('id_pemasukan', $idPemasukan)
                            ->update();
                    }
                }
                session()->setFlashdata('success', 'Data berhasil dimasukkan.');
            } else {
                session()->setFlashdata('error', 'Gagal, Data pemasukan sudah ada.');
            }
            return redirect()->to($this->role . '/pemasukan');
        }
    }
    public function pengeluaranJalur()
    {
        $id = $this->request->getPost('barcode');
        $cluster = $this->clusterModel->getDataCluster();

        // Ambil data dari session (jika ada)
        $existingData = session()->get('dataOutJalur') ?? [];

        if (!empty($id)) {
            // Cek apakah barcode sudah ada di data yang tersimpan
            foreach ($existingData as $item) {
                if ($item['id_out_celup'] == $id) {
                    session()->setFlashdata('error', 'Barcode sudah ada di tabel!');
                    return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
                }
            }

            // Ambil data dari database berdasarkan barcode yang dimasukkan
            $inGudang = $this->pemasukanModel->getDataForOut($id);

            if (empty($inGudang)) {
                session()->setFlashdata('error', 'Barcode tidak ditemukan di database!');
                return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
            } elseif (!empty($inGudang)) {
                // Tambahkan data baru ke dalam array
                $existingData = array_merge($existingData, $inGudang);
            }

            // Simpan kembali ke session
            session()->set('dataOutJalur', $existingData);

            // Redirect agar form tidak resubmit saat refresh
            return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
        }

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataOutJalur' => $existingData, // Tampilkan data dari session
            'cluster' => $cluster,
            'error' => session()->getFlashdata('error'),
        ];

        return view($this->role . '/warehouse/form-pengeluaran', $data);
    }

    public function search()
    {
        $noModel = $this->request->getPost('noModel');
        $warna = $this->request->getPost('warna');

        $results = $this->stockModel->searchStock($noModel, $warna);
        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);
        // var_dump($resultsArray);
        // Hitung total kgs_in_out untuk seluruh data
        $totalKgsByCluster = []; // Array untuk menyimpan total Kgs per cluster
        $capacityByCluster = []; // Array untuk menyimpan kapasitas per cluster

        foreach ($resultsArray as $item) {
            $namaCluster = $item['nama_cluster'];
            $kgs = (float)$item['Kgs'];
            $kgsStockAwal = (float)$item['KgsStockAwal'];
            $kapasitas = (float)$item['kapasitas'];

            // Inisialisasi total Kgs dan kapasitas untuk cluster jika belum ada
            if (!isset($totalKgsByCluster[$namaCluster])) {
                $totalKgsByCluster[$namaCluster] = 0;
                $totalKgsStockAwalByCluster[$namaCluster] = 0;
                $capacityByCluster[$namaCluster] = $kapasitas;
            }

            // Tambahkan Kgs ke total untuk nama_cluster tersebut
            $totalKgsByCluster[$namaCluster] += $kgs;
            $totalKgsStockAwalByCluster[$namaCluster] += $kgsStockAwal;
        }

        // Iterasi melalui data dan hitung sisa kapasitas
        foreach ($resultsArray as &$item) { // Gunakan reference '&' agar perubahan berlaku pada item
            $namaCluster = $item['nama_cluster'];
            $totalKgsInCluster = $totalKgsByCluster[$namaCluster];
            $totalKgsStockAwalInCluster = $totalKgsStockAwalByCluster[$namaCluster];
            $kapasitasCluster = $capacityByCluster[$namaCluster];

            $sisa_space = $kapasitasCluster - $totalKgsInCluster - $totalKgsStockAwalInCluster;
            $item['sisa_space'] = max(0, $sisa_space); // Pastikan sisa_space tidak negatif
        }
        // var_dump ($resultsArray);
        return $this->response->setJSON($resultsArray);
    }

    public function getSisaKapasitas()
    {
        $results = $this->stockModel->getKapasitas();

        $resultsArray = json_decode(json_encode($results), true);

        foreach ($resultsArray as &$item) {
            $sisaSpace = $item['kapasitas'] - $item['Kgs'] - $item['KgsStockAwal'];
            $item['sisa_space'] = $sisaSpace;
        }
        // var_dump($resultsArray);
        return $this->response->setJSON(
            [
                'success' => true,
                'data' => $resultsArray
            ]
        );
    }

    public function getClusterbyId()
    {
        $id = $this->request->getPost('id');
        $results = $this->clusterModel->getClusterById($id);
        $resultsArray = json_decode(json_encode($results), true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultsArray
        ]);
    }

    public function updateCluster()
    {
        if ($this->request->isAJAX()) {
            $idStock = $this->request->getPost('id_stock');
            $clusterOld = $this->request->getPost('cluster_old');
            $namaCluster = $this->request->getPost('nama_cluster');
            $kgs = $this->request->getPost('kgs');
            $cones = $this->request->getPost('cones');
            $karung = $this->request->getPost('krg');
            $lot = $this->request->getPost('lot');

            $idStock = $this->stockModel->where('id_stock', $idStock)->first();

            if (!$idStock) {
                return $this->response->setJSON(['success' => false, 'message' => 'Stock tidak ditemukan']);
            }


            $kgsInput = (int)$kgs;
            $cnsInput = (int)$cones;
            $krgInput = (int)$karung;

            $kgsInOut = !empty($idStock['kgs_in_out']) ? (int)$idStock['kgs_in_out'] : 0;
            $kgsStockAwal = !empty($idStock['kgs_stock_awal']) ? (int)$idStock['kgs_stock_awal'] : 0;

            $cnsInOut = !empty($idStock['cns_in_out']) ? (int)$idStock['cns_in_out'] : 0;
            $cnsStockAwal = !empty($idStock['cns_stock_awal']) ? (int)$idStock['cns_stock_awal'] : 0;

            $krgInOut = !empty($idStock['krg_in_out']) ? (int)$idStock['krg_in_out'] : 0;
            $krgStockAwal = !empty($idStock['krg_stock_awal']) ? (int)$idStock['krg_stock_awal'] : 0;

            // Gunakan stok yang tersedia
            $stokKgsTersedia = $kgsInOut > 0 ? $kgsInOut : $kgsStockAwal;
            $stokCnsTersedia = $cnsInOut > 0 ? $cnsInOut : $cnsStockAwal;
            $stokKrgTersedia = $krgInOut > 0 ? $krgInOut : $krgStockAwal;

            // Validasi: Pastikan stok cukup sebelum lanjut
            if ($stokKgsTersedia < $kgsInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah KGS melebihi stok yang tersedia']);
            }
            if ($stokCnsTersedia < $cnsInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah Cones melebihi stok yang tersedia']);
            }
            if ($stokKrgTersedia < $krgInput) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jumlah Karung melebihi stok yang tersedia']);
            }

            // Perhitungan stok setelah pengurangan
            if (

                $kgsInOut > 0
            ) {
                $kgsInOut -= $kgsInput;
            } else {
                $kgsStockAwal -= $kgsInput;
            }

            if (

                $cnsInOut > 0
            ) {
                $cnsInOut -= $cnsInput;
            } else {
                $cnsStockAwal -= $cnsInput;
            }

            if (

                $krgInOut > 0
            ) {
                $krgInOut -= $krgInput;
            } else {
                $krgStockAwal -= $krgInput;
            }

            // Hindari nilai negatif
            $kgsInOut = max(0, $kgsInOut);
            $kgsStockAwal = max(0, $kgsStockAwal);

            $cnsInOut = max(0, $cnsInOut);
            $cnsStockAwal = max(0, $cnsStockAwal);

            $krgInOut = max(0, $krgInOut);
            $krgStockAwal = max(0, $krgStockAwal);

            // Menentukan lot yang digunakan
            $lot = !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : $idStock['lot_awal'];
            // log_message('debug', 'Lot yang digunakan: ' . $lot);

            $noModel = $idStock['no_model'];
            $itemType = $idStock['item_type'];
            $kodeWarna = $idStock['kode_warna'];
            $warna = $idStock['warna'];

            if ($idStock['kgs_in_out'] < $kgs || $idStock['cns_in_out'] < $cones || $idStock['krg_in_out'] < $karung) {
                $kgsInOut = $idStock['kgs_stock_awal'] - $kgs;
                $cnsInOut = $idStock['cns_stock_awal'] - $cones;
                $krgInOut = $idStock['krg_stock_awal'] - $karung;
            } else {
                $kgsInOut = $idStock['kgs_in_out'] - $kgs;
                $cnsInOut = $idStock['cns_in_out'] - $cones;
                $krgInOut = $idStock['krg_in_out'] - $karung;
            }
            // $kgsInOut = $idStock['kgs_in_out']-$kgs;
            // $cnsInOut = $idStock['cns_in_out']-$cones;
            // $krgInOut = $idStock['krg_in_out']-$karung;
            // $lotStock = "";
            // var_dump($idStock, $clusterOld, $namaCluster, $kgs, $cones, $karung, $lot);

            $dataStock = [
                'no_model' => $noModel,
                'item_type' => $itemType,
                'kode_warna' => $kodeWarna,
                'warna' => $warna,
                'kgs_stock_awal' => empty($idStock['lot_stock']) ? $kgs : 0,
                'kgs_in_out' => !empty($idStock['lot_stock']) ? $kgs : 0,
                'cns_stock_awal' => empty($idStock['lot_stock']) ? $cones : 0,
                'cns_in_out' => !empty($idStock['lot_stock']) ? $cones : 0,
                'krg_stock_awal' => empty($idStock['lot_stock']) ? $karung : 0,
                'krg_in_out' => !empty($idStock['lot_stock']) ? number_format($karung, 0, '.', '') : 0,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '',  // Pastikan lot_stock hanya diisi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '',  // Gunakan lot_awal jika lot_stock kosong
                'nama_cluster' => $namaCluster,
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->stockModel->insert($dataStock);

            // Ambil ID dari stok baru setelah insert
            $idStockNew = $this->stockModel->getInsertID();

            if (!$idStockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data stock baru']);
            }

            // Simpan riwayat pemindahan palet
            $dataHistory = [
                'id_stock_old' => $idStock['id_stock'],
                'id_stock_new' => $idStockNew,
                'cluster_old' => $clusterOld,
                'cluster_new' => $namaCluster,
                'kgs' => $kgs,
                'cns' => $cones,
                'krg' => $karung, // Tambahkan jumlah karung ke log history
                'lot' => $lot,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $stockNew = $this->historyPindahPalet->insert($dataHistory);

            if (!$stockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan riwayat pemindahan palet']);
            }

            // Update ke database
            $updateStock = $this->stockModel->update($idStock['id_stock'], [
                'kgs_in_out' => $kgsInOut,
                'kgs_stock_awal' => $kgsStockAwal,
                'cns_in_out' => $cnsInOut,
                'cns_stock_awal' => $cnsStockAwal,
                'krg_in_out' => $krgInOut,
                'krg_stock_awal' => $krgStockAwal,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '', // Hanya isi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '', // Hanya isi jika lot_stock kosong
            ]);


            if ($stockNew && $updateStock) {
                return $this->response->setJSON(['success' => true, 'message' => 'Cluster berhasil diperbarui']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui Cluster']);
            }
        } else {
            return redirect()->to(base_url($this->role . '/warehouse'));
        }
    }

    public function getPindahOrder()
    {
        $idStock = $this->request->getPost('id_stock');
        $data = $this->stockModel->getStockInPemasukanById($idStock);
        $dataArray = json_decode(json_encode($data), true);
        foreach ($dataArray as &$id) {
            $other = $this->otherOutModel->getQty($id['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($id['id_out_celup']);

            $kgsOther = !empty($other) ? (float) $other['kgs_other_out'] : 0;
            $kgsOutByCns = !empty($outByCns) ? (float) $outByCns['kgs_out'] : 0;
            $cnsOther = !empty($other) ? (int) $other['cns_other_out'] : 0;
            $cnsOutByCns = !empty($outByCns) ? (int) $outByCns['cns_out'] : 0;

            // kurangi other out & pengeluaran by cones
            $id['kgs_kirim'] = round((float) $id['kgs_kirim'] - $kgsOther - $kgsOutByCns, 2);
            $id['cones_kirim'] = (int) $id['cones_kirim'] - $cnsOther - $cnsOutByCns;
        }
        // var_dump($dataArray);
        // log_message('debug', 'Data Stock: ' . print_r($dataArray, true));
        if (empty($dataArray)) {
            return $this->response->setJSON(['error' => false, 'message' => 'Data tidak ditemukan']);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'data' => $dataArray
            ]);
        }

        return $this->response->setJSON($dataArray);
    }

    public function getNoModel()
    {
        $noModelOld = $this->request->getVar('noModelOld');
        $kodeWarna = $this->request->getVar('kodeWarna');
        $term = $this->request->getVar('term');
        // var_dump($kodeWarna);
        // log_message('debug', 'Fetching no_model for kode_warna: ' . $kodeWarna);
        $results = $this->materialModel->getNoModel($noModelOld, $kodeWarna, $term);
        // last query
        // log_message('debug', 'Last Query: ' . $this->db->getLastQuery());
        $resultsArray = json_decode(json_encode($results), true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultsArray
        ]);
    }

    public function savePindahOrder()
    {
        $reqData = $this->request->getPost();

        // 1) Validasi model tujuan
        if (empty($reqData['no_model_tujuan']) || count(explode('|', $reqData['no_model_tujuan'])) < 4) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data model tujuan tidak lengkap atau tidak valid.'
            ]);
        }
        [$noModel, $itemType, $kodeWarna, $warna] = explode('|', $reqData['no_model_tujuan']);

        $idOutCelup = $reqData['idOutCelup'] ?? [];
        $idStock    = $reqData['id_stock']   ?? [];

        // 2) Ambil data outCelup & stock lama
        $dataOutCelup = [];
        foreach ($idOutCelup as $id) {
            if ($d = $this->outCelupModel->find($id)) {
                $dataOutCelup[] = $d;
            }
        }
        $idStockData = [];
        foreach ($idStock as $id) {
            if ($s = $this->stockModel->find($id)) {
                $idStockData[] = $s;
            }
        }
        if (
            count($dataOutCelup) !== count($idOutCelup)
            || count($idStockData)  !== count($idStock)
        ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Beberapa data tidak ditemukan di database.'
            ]);
        }

        // 3) Hitung TOTAL semua kgs/cones/krg untuk stock baru
        $totalKgs = $totalCns = $totalKrgOut = $totalKrgIn = 0;

        foreach ($dataOutCelup as $i => $d) {
            $other = $this->otherOutModel->getQty($d['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($d['id_out_celup']);
            // 
            $kgs = round($d['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0), 2);
            $cns = $d['cones_kirim'] - ($other['cns_other_out'] ?? 0) - ($outByCns['cns_out'] ?? 0);
            $kgsOutByCns = $reqData['kgs_out'][$d['id_out_celup']] ?? 0;
            $cnsOutByCns = $reqData['cns_out'][$d['id_out_celup']] ?? 0;

            $kgsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $kgsOutByCns : $kgs;
            $cnsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $cnsOutByCns : $cns;
            $krgKirimOut = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? 0 : 1; // untuk pengurangan stock pdk lama
            $krgKirimIn = 1; // untuk insert stock di pdk baru

            // simpan hasil per id_out_celup
            $detailKirim[$i] = [
                'kgs' => $kgsKirim,
                'cns' => $cnsKirim,
                'krgOut' => $krgKirimOut,
                'krgIn' => $krgKirimIn,
            ];

            $totalKgs += $kgsKirim;
            $totalCns += $cnsKirim;
            $totalKrgOut += $krgKirimOut; // untuk pengurangan stock pdk lama
            $totalKrgIn += $krgKirimIn; // Asumsikan setiap pemasukan hanya 1 kali
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $idOutCelupBaru  = [];
        $idPemasukanBaru = [];

        // 4) Loop: insert out_celup baru, pemasukan baru, dan update stok LAMA
        foreach ($dataOutCelup as $i => $data) {
            $cluster  = $idStockData[$i]['nama_cluster'];
            $lotStock = $idStockData[$i]['lot_stock'];
            $oldIdOC  = $data['id_out_celup'];

            // Ambil hasil hitungan yang sudah disesuaikan
            $kgsKirim = $detailKirim[$i]['kgs'];
            $cnsKirim = $detailKirim[$i]['cns'];
            $krgOut = $detailKirim[$i]['krgOut'];
            $krgIn = $detailKirim[$i]['krgIn'];

            // — baru out_celup
            $idRetur = $this->outCelupModel
                ->select('id_retur')
                ->where('id_out_celup', $oldIdOC)
                ->first();
            if ($idRetur) {
                $this->outCelupModel->insert([
                    'id_retur'    => $idRetur['id_retur'] ?? null,
                    'id_bon'      => $data['id_bon'] ?? null,
                    'id_celup'    => $data['id_celup'] ?? null,
                    'no_model'    => $noModel,
                    'no_karung'   => $data['no_karung'],
                    'kgs_kirim'   => $kgsKirim,
                    'cones_kirim' => $cnsKirim,
                    'lot_kirim'   => $lotStock,
                    'ganti_retur' => '0',
                    'admin'       => session()->get('username'),
                ]);
                $idOutCelupBaru[] = $this->outCelupModel->getInsertID();
            }

            // — tandai out_jalur pada pemasukan lama
            $this->pemasukanModel
                ->set('out_jalur', '1')
                ->where('id_out_celup', $oldIdOC)
                ->update();

            // — insert pemasukan baru (tanpa id_stock dulu)
            $this->pemasukanModel->insert([
                'id_out_celup' => $idOutCelupBaru[$i],
                'tgl_masuk'    => date('Y-m-d'),
                'nama_cluster' => $cluster,
                'out_jalur'    => '0',
                'admin'        => session()->get('username'),
            ]);
            $idPemasukanBaru[] = $this->pemasukanModel->getInsertID();

            // — update stok LAMA (kurangi sesuai tiap item)
            $newKgs = max(0, $idStockData[$i]['kgs_in_out']   -= $kgsKirim);
            $newCns = max(0, $idStockData[$i]['cns_in_out']   -= $cnsKirim);
            $newKrg = max(0, $idStockData[$i]['krg_in_out']   -= $krgOut);
            $this->stockModel->update($idStock[$i], [
                'kgs_in_out'  => $newKgs,
                'cns_in_out'  => $newCns,
                'krg_in_out'  => $newKrg,
                'lot_stock'   => $lotStock,
                'lot_awal'    => $idStockData[$i]['lot_awal'] ?? $idStockData[$i]['lot_stock'],
                'nama_cluster' => $cluster,
            ]);
        }

        // 5) Sekali saja: cek stock ada atau tidak
        // Ambil record stock yang matching model, type, warna, cluster dan lot
        $existingStock = $this->stockModel
            ->where('no_model',   $noModel)
            ->where('item_type',  $itemType)
            ->where('kode_warna', $kodeWarna)
            ->where('warna',      $warna)
            ->where('nama_cluster', $cluster)
            ->where('lot_awal', $lotStock)
            ->first();

        if ($existingStock) {
            // — jika sudah ada, update stok lama (tambah total dari pemindahan)
            $this->stockModel->update($existingStock['id_stock'], [
                'kgs_stock_awal' => $existingStock['kgs_stock_awal'] + $totalKgs,
                'cns_stock_awal' => $existingStock['cns_stock_awal'] + $totalCns,
                'krg_stock_awal' => $existingStock['krg_stock_awal'] + $totalKrgIn,
                'lot_awal'       => $lotStock,          // perbarui lot terakhir
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $newStockId = $existingStock['id_stock'];
        } else {
            // — jika belum ada, insert stock baru
            $this->stockModel->insert([
                'no_model'       => $noModel,
                'item_type'      => $itemType,
                'kode_warna'     => $kodeWarna,
                'warna'          => $warna,
                'kgs_stock_awal' => $totalKgs,
                'cns_stock_awal' => $totalCns,
                'krg_stock_awal' => $totalKrgIn,
                'lot_awal'       => $lotStock,
                'nama_cluster'   => $cluster,
                'admin'          => session()->get('username'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
            $newStockId = $this->stockModel->getInsertID();
        }

        // 6) Update semua pemasukan baru agar pakai stock yang sama
        foreach ($idPemasukanBaru as $pid) {
            $this->pemasukanModel->update($pid, ['id_stock' => $newStockId]);
        }

        // insert data history
        $this->historyStock->insert([
            'id_stock_old'  => $idStockData[0]['id_stock'], // ID stok lama
            'id_stock_new'  => $newStockId, // ID stok baru
            'cluster_old'   => $idStockData[0]['nama_cluster'], // Cluster lama
            'cluster_new'   => $cluster, // Cluster baru
            'kgs'           => $totalKgs, // Total kgs
            'cns'           => $totalCns, // Total cns
            'krg'           => $totalKrgOut, // Total krg
            'lot'           => $idStockData[0]['lot_stock'], // Lot stok lama
            'keterangan'    => "Pindah Order", // Keterangan pemindahan
            'admin'         => session()->get('username'), // Admin yang melakukan
            'created_at'    => date('Y-m-d H:i:s'), // Waktu pemindahan
            'updated_at'    => null, // Kolom updated_at bisa null karena belum ada perubahan
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses pemindahan order. Transaksi dibatalkan.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil dipindahkan.'
        ]);
    }
    public function updateNoModel()
    {
        if ($this->request->isAJAX()) {
            $idStock = $this->request->getPost('id_stock');
            $clusterOld = $this->request->getPost('namaCluster');
            $noModel = $this->request->getPost('no_model');
            $kgs = (int) $this->request->getPost('kgs');
            $cones = (int) $this->request->getPost('cones');
            $karung = (int) $this->request->getPost('krg');

            // log_message('debug', 'Data No Model: ' . print_r($noModel, true));
            // log_message('debug', 'Data clusterOld: ' . print_r($clusterOld, true));

            // Ambil data stok lama
            $idStock = $this->stockModel->where('id_stock', $idStock)->first();
            if (!$idStock) {
                return $this->response->setJSON(['success' => false, 'message' => 'Stock tidak ditemukan']);
            }

            // Ambil lot_awal atau lot_stock
            $lot = !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : $idStock['lot_awal'];

            // Cari data order berdasarkan no_model
            $findData = $this->masterOrderModel->where('no_model', $noModel)->first();
            if (!$findData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Order tidak ditemukan']);
            }

            // log_message('debug', 'Data Order: ' . print_r($findData, true));

            // Cari material berdasarkan order
            $material = $this->materialModel->getMaterialByIdOrderItemTypeKodeWarna(
                $findData['id_order'],
                $idStock['item_type'],
                $idStock['kode_warna']
            );

            if (empty($material)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Material tidak ditemukan']);
            }

            // log_message('debug', 'Data Material: ' . print_r($material, true));

            $noModel = $findData['no_model'];
            $itemType = $material[0]['item_type'];
            $kodeWarna = $material[0]['kode_warna'];
            $warna = $material[0]['color'];

            // $itemType = $material['item_type'];
            // $kodeWarna = $material['kode_warna'];
            // $warna = $material['color'];

            if ($idStock['kgs_in_out'] < $kgs || $idStock['cns_in_out'] < $cones || $idStock['krg_in_out'] < $karung) {
                $kgsInOut = $idStock['kgs_stock_awal'] - $kgs;
                $cnsInOut = $idStock['cns_stock_awal'] - $cones;
                $krgInOut = $idStock['krg_stock_awal'] - $karung;
            } else {
                $kgsInOut = $idStock['kgs_in_out'] - $kgs;
                $cnsInOut = $idStock['cns_in_out'] - $cones;
                $krgInOut = $idStock['krg_in_out'] - $karung;
            }

            $dataStock = [
                'no_model' => $noModel,
                'item_type' => $itemType,
                'kode_warna' => $kodeWarna,
                'warna' => $warna,
                'kgs_stock_awal' => $kgs,
                'kgs_in_out' => 0,
                'cns_stock_awal' => $cones,
                'cns_in_out' => 0,
                'krg_stock_awal' => $karung,
                'krg_in_out' => 0,
                'lot_awal' => $lot,
                'nama_cluster' => $clusterOld,
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert data stock baru
            if (!$this->stockModel->insert($dataStock)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan stock baru']);
            }

            // log_message('debug', 'Data Stock: ' . print_r($dataStock, true));

            // Ambil ID stock baru
            $idStockNew = $this->stockModel->getInsertID();
            if (!$idStockNew) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal mendapatkan ID stock baru']);
            }

            // Simpan riwayat pemindahan order
            $dataHistory = [
                'id_stock_old' => $idStock['id_stock'],
                'id_stock_new' => $idStockNew,
                'nama_cluster' => $clusterOld,
                'kgs' => $kgs,
                'cns' => $cones,
                'krg' => $karung,
                'lot' => $lot,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->historyPindahOrder->insert($dataHistory)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan riwayat pemindahan order']);
            }

            // log_message('debug', 'Data Cluster: ' . print_r($dataHistory, true));

            // Validasi stok cukup sebelum dikurangkan
            $kgsInOut = max(0, $idStock['kgs_in_out'] - $kgs);
            $cnsInOut = max(0, $idStock['cns_in_out'] - $cones);
            $krgInOut = max(0, $idStock['krg_in_out'] - $karung);
            $kgsStockAwal = max(0, $idStock['kgs_stock_awal'] - $kgs);
            $cnsStockAwal = max(0, $idStock['cns_stock_awal'] - $cones);
            $krgStockAwal = max(0, $idStock['krg_stock_awal'] - $karung);

            // Update stock lama dengan mengurangi stok yang dipindahkan
            $updateStockData = [
                'kgs_stock_awal' => $kgsStockAwal,
                'cns_stock_awal' => $cnsStockAwal,
                'krg_stock_awal' => $krgStockAwal,
                'kgs_in_out' => $kgsInOut,
                'cns_in_out' => $cnsInOut,
                'krg_in_out' => $krgInOut,
                'lot_stock' => !empty($idStock['lot_stock']) ? $idStock['lot_stock'] : '', // Hanya isi jika ada
                'lot_awal' => empty($idStock['lot_stock']) ? $idStock['lot_awal'] : '', // Hanya isi jika lot_stock kosong
            ];

            if (!$this->stockModel->update($idStock['id_stock'], $updateStockData)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui stock lama']);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Cluster berhasil diperbarui']);
        } else {
            return redirect()->to(base_url($this->role . '/warehouse'));
        }
    }

    public function prosesPengeluaranJalur()
    {
        $checkedIds = $this->request->getPost('checked_id'); // Ambil index yang dicentang

        if (empty($checkedIds)) {
            session()->setFlashdata('error', 'Tidak ada data yang dipilih.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        $idOutCelup = $this->request->getPost('id_out_celup');

        // Pastikan data tidak kosong
        if (empty($idOutCelup) || !is_array($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }
        //update tabel pemasukan
        if (!empty($checkedIds)) {
            $whereIds = array_map(fn($index) => $idOutCelup[$index] ?? null, $checkedIds);
            $whereIds = array_filter($whereIds); // Hapus nilai NULL jika ada

            if (!empty($whereIds)) {
                $update = $this->pemasukanModel
                    ->whereIn('id_out_celup', $whereIds)
                    ->set(['out_jalur' => '1'])
                    ->update();

                if ($update) {
                    session()->setFlashdata('success', 'Data berhasil dikeluarkan');

                    // Hapus data yang sudah diproses dari session dataOut
                    $dataOut = session()->get('dataOutJalur');

                    if (!empty($dataOut) && is_array($dataOut)) {
                        $filteredDataOut = array_filter($dataOut, function ($item) use ($whereIds) {
                            return isset($item['id_out_celup']) && !in_array($item['id_out_celup'], $whereIds);
                        });

                        // Perbarui session atau hapus jika kosong
                        if (!empty($filteredDataOut)) {
                            session()->set('dataOutJalur', array_values($filteredDataOut));
                        } else {
                            session()->remove('dataOutJalur');
                        }
                    }
                } else {
                    session()->setFlashdata('error', 'Gagal mengupdate data.');
                }
            }
        }

        return redirect()->to($this->role . '/pengeluaran_jalur');
    }
    public function resetPengeluaranJalur()
    {
        session()->remove('dataOutJalur');
        return redirect()->to(base_url($this->role . '/pengeluaran_jalur'));
    }
    public function hapusListPengeluaran()
    {
        $id = $this->request->getPost('id');

        // Ambil data dari session
        $existingData = session()->get('dataOutJalur') ?? [];

        // Cek apakah data dengan ID yang dikirim ada di session
        foreach ($existingData as $key => $item) {
            if ($item['id_out_celup'] == $id) {
                // Hapus data tersebut dari array
                unset($existingData[$key]);
                // Update session dengan data yang sudah dihapus
                session()->set('dataOutJalur', array_values($existingData));
                // Debug response
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
            }
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
    }
    public function getItemTypeForOut($no_model)
    {
        // Ambil data berdasarkan no_model yang dipilih
        $itemTypes = $this->pemasukanModel->getItemTypeByModel($no_model);  // Gantilah dengan query sesuai kebutuhan

        // Return data dalam bentuk JSON
        return $this->response->setJSON($itemTypes);
    }
    public function getKodeWarnaForOut()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));

        // log_message('debug', "Fetching kode warna for noModel: $noModel, itemType: $itemType");

        $kodeWarna = $this->pemasukanModel->getKodeWarnaByItemType($noModel, $itemType);

        return $this->response->setJSON($kodeWarna);
    }
    public function getWarnaDanLotForOut()
    {
        $noModel = $this->request->getGet('noModel');
        $itemType = urldecode($this->request->getGet('itemType'));
        $kodeWarna = $this->request->getGet('kodeWarna');

        // log_message('debug', "Fetching warna & lot for no_model: $no_model, item_type: $item_type, kode_warna: $kode_warna");

        $warna = $this->pemasukanModel->getWarnaByKodeWarna($noModel, $itemType, $kodeWarna);
        $lotList = $this->pemasukanModel->getLotByKodeWarna($noModel, $itemType, $kodeWarna);

        return $this->response->setJSON([
            'warna' => $warna ?? '',
            'lot' => $lotList
        ]);
    }
    public function getKgsCnsClusterForOut()
    {
        $no_model = $this->request->getGet('noModel');
        $item_type = $this->request->getGet('itemType');
        $kode_warna = $this->request->getGet('kodeWarna');
        $lot_kirim = $this->request->getGet('lotKirim');
        $no_karung = $this->request->getGet('noKarung');
        try {
            $data = $this->pemasukanModel->getKgsConesClusterForOut($no_model, $item_type, $kode_warna, $lot_kirim, $no_karung);

            if ($data) {
                return $this->response->setJSON([
                    'success' => true,
                    'kgs_kirim' => $data['kgs_kirim'],
                    'cones_kirim' => $data['cones_kirim'],
                    'id_out_celup' => $data['id_out_celup'],
                    'nama_cluster' => $data['nama_cluster']
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }
        } catch (\Exception $e) {
            // log_message('error', 'Error getKgsDanCones: ' . $e->getMessage()); // Log error
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server']);
        }
    }
    public function prosesPengeluaranJalurManual()
    {
        $idOutCelup = $this->request->getPost('id_out_celup');

        // Pastikan data tidak kosong
        if (empty($idOutCelup)) {
            session()->setFlashdata('error', 'Data yang dikirim kosong atau tidak valid.');
            return redirect()->to($this->role . '/pengeluaran_jalur');
        }

        $update = $this->pemasukanModel
            ->where('id_out_celup', $idOutCelup)
            ->set('out_jalur', '1')
            ->update();

        if ($update) {
            session()->setFlashdata('success', 'Data berhasil dikeluarkan');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data.');
        }

        return redirect()->to($this->role . '/pengeluaran_jalur');
    }
    public function prosesComplain()
    {
        $idOutCelup = $this->request->getPost('id_out_celup');
        $alasan = $this->request->getPost('alasan');
        $post = $this->request->getPost();
        // dd($post);
        $idCelup = $this->outCelupModel->getIdCelup($idOutCelup);

        if (!$idCelup) {
            session()->setFlashdata('error', 'Data tidak ditemukan.');
            return redirect()->back();
        }

        $update = $this->scheduleCelupModel
            ->where('id_celup', $idCelup)
            ->set([
                'last_status' => 'complain',
                'ket_daily_cek' => $alasan
            ])
            ->update();

        if ($update) {
            // Ambil session dataOut
            $existingData = session()->get('dataOut') ?? [];

            // Hapus data berdasarkan idOutCelup
            $filteredData = array_filter($existingData, function ($item) use ($idOutCelup) {
                return $item['id_out_celup'] != $idOutCelup;
            });

            // Simpan kembali dataOut ke session tanpa data yang dihapus
            session()->set('dataOut', array_values($filteredData));
        }

        $dataRetur[] = [
            'no_model'   => $post['no_model'],
            'item_type'  => $post['item_type'],
            'kode_warna' => $post['kode_warna'],
            'warna'      => $post['warna'],
            'area_retur' => 'GUDANG BENANG',
            'tgl_retur'  => date('Y-m-d'),
            'kgs_retur'  => $post['kgs_kirim'],
            'cns_retur'  => $post['cns_kirim'],
            'lot_retur'  => $post['lot_kirim'],
            'krg_retur'  => 1,
            'kategori'  => $post['kategori_retur'],
            'keterangan_area'  => null,
            'keterangan_gbn'  => $post['alasan'] ?? '',
            'waktu_acc_retur'  => null,
            'admin'  => session()->get('username'),
        ];

        // Insert batch
        $inserted = $this->returModel->insertBatch($dataRetur);

        // Flashdata & redirect
        if ($update && $inserted) {
            session()->setFlashdata('success', 'Data berhasil dikomplain dan retur disimpan.');
        } else {
            session()->setFlashdata('error', 'Gagal memproses komplain atau menyimpan retur.');
        }

        return redirect()->to($this->role . '/pemasukan');
    }

    public function reportPoBenang($jenis = null)
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report PO Benang',
            'active' => $this->active,
            'jenis' => $jenis
        ];

        return view($this->role . '/warehouse/report-po-benang', $data);
    }

    public function filterPoBenang()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? null;
        $data = $this->materialModel->getFilterPoBenang($key, $jenis);

        $startMc = [];
        $result = [];

        foreach ($data as $row) {
            $model = isset($row['no_model']) ? $row['no_model'] : '';

            if ($model === '') {
                $row['start_mc'] = 'Belum Ada Start Mc';
                $result[] = $row;
                continue;
            }

            // getStartMc
            if (!isset($startMc[$model])) {
                $url = 'http://172.23.44.14/CapacityApps/public/api/getStartMc/' . urlencode($model);
                $resp = @file_get_contents($url);
                if ($resp !== false) {
                    $json = json_decode($resp, true);
                    $startMc[$model] = $json['start_mc'] ?? 'Belum Ada Start Mc';
                } else {
                    // fallback jika API error / tidak dapat diakses
                    $startMc[$model] = 'Belum Ada Start Mc';
                }
            }

            $row['start_mc'] = $startMc[$model];
            $result[] = $row;
        }

        return $this->response->setJSON($result);
    }

    public function reportDatangBenang()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Datang Benang',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-datang-benang', $data);
    }

    public function filterDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterDatangBenang($key, $tanggalAwal, $tanggalAkhir);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function simpanPengeluaranJalur($idTtlPemesanan)
    {
        $area = $this->request->getGet('Area');
        $KgsPesan = $this->request->getGet('KgsPesan');
        $CnsPesan = $this->request->getGet('CnsPesan');
        $pinjam = $this->request->getGet('pinjam');
        $data = $this->request->getPost();
        // dd($data);
        // Validasi dasar: pastikan id_pemasukan ada
        if (empty($data['id_pemasukan'])) {
            session()->setFlashdata('error', 'Data pemasukan tidak valid.');
            return redirect()->back();
        }

        // Ambil area dari parameter GET, bisa juga divalidasi jika wajib
        $area = $this->request->getGet('Area');
        if (empty($area)) {
            session()->setFlashdata('error', 'Area tidak boleh kosong.');
            return redirect()->back();
        }

        // Pastikan id_pemasukan berupa array
        $idPemasukanArray = (array)$data['id_pemasukan'];

        // Ambil data pemasukan untuk semua id yang dipilih
        $pemasukanData = $this->outCelupModel->findOutCelup($idPemasukanArray);
        // dd($pemasukanData);

        if (!$pemasukanData) {
            session()->setFlashdata('error', 'Data pemasukan tidak ditemukan.');
            return redirect()->back();
        }

        // Mulai transaksi
        $this->db->transStart();

        try {
            // Proses setiap data pemasukan
            foreach ($pemasukanData as $pemasukan) {
                // Ambil data tabel out_celup terkait
                $outCelup = $this->outCelupModel->find($pemasukan['id_out_celup']);
                $other = $this->otherOutModel->getQty($pemasukan['id_out_celup'], $pemasukan['nama_cluster']);
                $outByCns = $this->pengeluaranModel->getQtyOutByCns($pemasukan['id_out_celup']);
                // jika kgs manual kosong maka
                $rawKgsManual = $data['kgs_out'][$pemasukan['id_pemasukan']] ?? 0;
                $rawCnsManual = $data['cns_out'][$pemasukan['id_pemasukan']] ?? 0;

                if ($rawKgsManual > 0 || $rawCnsManual > 0) {
                    $kgsOut =  floatval($rawKgsManual);
                    $cnsOut = floatval($rawCnsManual);
                    $krgOut = 0;
                } else {
                    $kgsOut = round($pemasukan['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0), 2);
                    $cnsOut = $pemasukan['cones_kirim'] - ($other['cns_other_out'] ?? 0) - ($outByCns['cns_out'] ?? 0);
                    $krgOut = 1;
                    $this->pemasukanModel->update($pemasukan['id_pemasukan'], ['out_jalur' => "1"]);
                }

                // Update field out_jalur pada tabel pemasukan

                // Siapkan data pengeluaran sesuai masing-masing pemasukan
                $insertData = [
                    'id_out_celup'       => $pemasukan['id_out_celup'],
                    'id_stock'           => $pemasukan['id_stock'],
                    'area_out'           => $area,
                    'tgl_out'            => date('Y-m-d H:i:s'),
                    'kgs_out'            => $kgsOut,
                    'cns_out'            => $cnsOut,
                    'krg_out'            => $krgOut,
                    'nama_cluster'       => $pemasukan['nama_cluster'],
                    'lot_out'            => $outCelup['lot_kirim'], // pastikan field ini ada di data pemasukan
                    'id_total_pemesanan' => $idTtlPemesanan,
                    'status'             => 'Pengeluaran Jalur',
                    'keterangan_gbn'     => $data['keterangan'][$pemasukan['id_pemasukan']],
                    'admin'              => $this->username,
                    'created_at'         => date('Y-m-d H:i:s')
                ];
                // dd ($insertData);
                // Insert data pengeluaran
                $this->pengeluaranModel->insert($insertData);

                // --- UPDATE TABEL STOCK BERDASARKAN id_stock ---
                // Ambil data stok berdasarkan id_stock yang terkait
                $stok = $this->db->table('stock')
                    ->where('id_stock', $pemasukan['id_stock'])
                    ->get()->getResultArray();

                if (!empty($stok)) {
                    // Siapkan field yang akan diproses
                    $fields = ['kgs', 'cns', 'krg'];
                    $newStock = [];

                    foreach ($fields as $field) {
                        // Ambil nilai dari database atau 0 jika kosong, gunakan casting ke float
                        $inOut = !empty($stok[0][$field . '_in_out']) ? (float)$stok[0][$field . '_in_out'] : 0;
                        $awal  = !empty($stok[0][$field . '_stock_awal']) ? (float)$stok[0][$field . '_stock_awal'] : 0;

                        // Nilai yang akan dikurangkan berdasarkan nilai output yang diterima
                        $input = (float)$insertData[$field . '_out'];

                        // Jika field in_out memiliki nilai, update in_out; jika tidak, update stock_awal
                        if ($inOut > 0) {
                            $result = $inOut - $input;
                            $newStock[$field . '_in_out'] = abs($result) < 0.0001 ? 0 : $result;
                        } else {
                            $result = $awal - $input;
                            $newStock[$field . '_stock_awal'] = abs($result) < 0.0001 ? 0 : $result;
                        }
                    }

                    // Update data stock di database berdasarkan id_stock
                    $this->db->table('stock')
                        ->where('id_stock', $pemasukan['id_stock'])
                        ->update($newStock);
                }
                // --- END UPDATE TABEL STOCK ---

                // JIKA PMINJAM ORDER, INSERT HISTORY STOCK
                if ($pinjam == 'YA') {
                    $insertHistory = [
                        'id_stock_old'   => $pemasukan['id_stock'],
                        'cluster_old'    => $pemasukan['nama_cluster'],
                        'cluster_new'    => $pemasukan['nama_cluster'],
                        'kgs'            => $pemasukan['kgs_kirim'],
                        'cns'            => $pemasukan['cones_kirim'],
                        'lot'            => $outCelup['lot_kirim'], // pastikan field ini ada di data pemasukan
                        'krg'            => 1,
                        'keterangan'     => 'Pinjam Order',
                        'admin'          => $this->username,
                        'created_at'     => date('Y-m-d H:i:s')
                    ];
                    // dd ($insertData);
                    // Insert data pengeluaran
                    $this->historyStock->insert($insertHistory);
                }
            }
            // Commit jika semua berhasil
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new \Exception('Transaksi gagal, semua perubahan dibatalkan.');
            }
            // Setelah semua data selesai di proses, set flash alert success
            session()->setFlashdata('success', 'Data pengeluaran jalur berhasil disimpan.');
            return redirect()->to('gbn/selectClusterWarehouse/' . $idTtlPemesanan . '?Area=' . $area . '&KgsPesan=' . $KgsPesan . '&CnsPesan=' . $CnsPesan);
        } catch (\Exception $e) {
            // Rollback jika ada error
            $this->db->transRollback();

            // Set error message
            session()->setFlashdata('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function savePengeluaranJalur()
    {
        $data = $this->request->getJSON();

        $insertData = [
            'id_out_celup' => $data->idOutCelup,
            'area_out' => $data->area,
            'tgl_out' => date('Y-m-d H:i:s'),
            'kgs_out' => $data->qtyKGS,
            'cns_out' => $data->qtyCNS,
            'krg_out' => $data->qtyKarung,
            'nama_cluster' => $data->namaCluster,
            'lot_out' => $data->lotFinal,
            'status' => 'Pengeluaran Jalur',
            'admin' => $this->username,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $stok = $this->stockModel->getDataByIdStok($data->idStok);

        $stokData = [
            'kgs' => [
                'in_out' => !empty($stok[0]['kgs_in_out']) ? (float)$stok[0]['kgs_in_out'] : 0,
                'awal' => !empty($stok[0]['kgs_stock_awal']) ? (float)$stok[0]['kgs_stock_awal'] : 0,
                'input' => (float)$insertData['kgs_out']
            ],
            'cns' => [
                'in_out' => !empty($stok[0]['cns_in_out']) ? (float)$stok[0]['cns_in_out'] : 0,
                'awal' => !empty($stok[0]['cns_stock_awal']) ? (float)$stok[0]['cns_stock_awal'] : 0,
                'input' => (float)$insertData['cns_out']
            ],
            'krg' => [
                'in_out' => !empty($stok[0]['krg_in_out']) ? (float)$stok[0]['krg_in_out'] : 0,
                'awal' => !empty($stok[0]['krg_stock_awal']) ? (float)$stok[0]['krg_stock_awal'] : 0,
                'input' => (float)$insertData['krg_out']
            ]
        ];

        foreach ($stokData as $key => $item) {
            $stokTersedia = $item['in_out'] > 0 ? $item['in_out'] : $item['awal'];

            if ($stokTersedia < $item['input']) {
                return $this->response->setJSON([
                    'error' => false,
                    'message' => "Jumlah " . strtoupper($key) . " melebihi stok yang tersedia"
                ]);
            }
        }

        foreach ($stokData as $key => &$item) {
            if ($item['in_out'] > 0) {
                $item['in_out'] = $this->roundSafe($item['in_out'] - $item['input'], 2);
            } else {
                $item['awal'] = $this->roundSafe($item['awal'] - $item['input'], 2);
            }
        }

        $insertData['lot_out'] = !empty($stok[0]['lot_stock']) ? $stok[0]['lot_stock'] : $stok[0]['lot_awal'];

        if ($this->pengeluaranModel->insert($insertData)) {
            $this->stockModel->updateStock(
                $data->idStok,
                (float)$stokData['kgs']['in_out'],
                (float)$stokData['kgs']['awal'],
                $stokData['cns']['in_out'],
                $stokData['cns']['awal'],
                $stokData['krg']['in_out'],
                $stokData['krg']['awal']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data berhasil disimpan & stok diperbarui'
            ]);
        } else {
            return $this->response->setJSON([
                'error' => false,
                'message' => 'Gagal menyimpan data'
            ]);
        }
    }
    public function getNamaCluster()
    {
        $cluster = $this->request->getVar('namaCluster');
        $kgsPindah = $this->request->getVar('totalKgs');

        $results = $this->clusterModel->getNamaCluster($cluster, $kgsPindah);

        $resultsArray = json_decode(json_encode($results), true);

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultsArray
        ]);
    }
    public function getPindahCluster()
    {
        $idStock = $this->request->getPost('id_stock');
        $data = $this->stockModel->getStockInPemasukanById($idStock);
        $dataArray = json_decode(json_encode($data), true);
        foreach ($dataArray as &$id) {
            $other = $this->otherOutModel->getQty($id['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($id['id_out_celup']);
            $pindahOrder = $this->historyStock->getKgsPindahOrder($id['id_out_celup']);

            $kgsOther = !empty($other) ? (float) $other['kgs_other_out'] : 0;
            $kgsOutByCns = !empty($outByCns) ? (float) $outByCns['kgs_out'] : 0;
            $kgsPindahOrder = !empty($pindahOrder) ? (float) $pindahOrder['kgs_pindah_order'] : 0;
            $cnsOther = !empty($other) ? (int) $other['cns_other_out'] : 0;
            $cnsOutByCns = !empty($outByCns) ? (int) $outByCns['cns_out'] : 0;
            $cnsPindahOrder = !empty($pindahOrder) ? (int) $pindahOrder['cns_pindah_order'] : 0;

            // kurangi other out & pengeluaran by cones
            $id['kgs_kirim'] = round((float) $id['kgs_kirim'] - $kgsPindahOrder - $kgsOther - $kgsOutByCns, 2);
            $id['cones_kirim'] = (int) $id['cones_kirim'] - $cnsOther - $cnsOutByCns - $cnsPindahOrder;
        }

        // var_dump($dataArray);
        // log_message('debug', 'Data Stock: ' . print_r($dataArray, true));
        if (empty($dataArray)) {
            return $this->response->setJSON(['error' => false, 'message' => 'Data tidak ditemukan']);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'data' => $dataArray
            ]);
        }

        return $this->response->setJSON($dataArray);
    }
    public function savePindahCluster()
    {
        $cluster = $this->request->getPost('cluster_tujuan');
        $details = $this->request->getPost('detail') ?? [];

        // Hitung total kgs, cns, dan krg dari $details
        $totalKgs = $totalCns = $totalKrg = 0;
        foreach ($details as $data) {
            $totalKgs += $data['kgs'];
            $totalCns += $data['cns'];
            $totalKrg += $data['krg'];
        }

        foreach ($details as $index => $data) {
            // cek apakah stock ini stock awal atau bukan
            $cekStock = $this->stockModel->find($data['id_stock']);

            // Tentukan stock_awal
            $stock_awal = empty($cekStock['lot_awal']) ? '' : 'Ya';

            // cek apakah stock data baru sudah ada di tabel
            $criteria = [
                'no_model' => $data['no_model'],
                'item_type' => $data['item_type'],
                'kode_warna' => $data['kode_warna'],
                'warna' => $data['warna'],
                'lot' => $data['lot'],
                'nama_cluster' => $cluster,
                'stock_awal' => $stock_awal,
            ];
            $cekStockBaru = $this->stockModel->getDataClusterPindah($criteria);

            if ($cekStockBaru) {
                // Update stok jika sudah ada
                $updateDataIn =
                    [
                        'kgs_in_out' => $cekStockBaru['kgs_in_out'] + $data['kgs'],
                        'cns_in_out' => $cekStockBaru['cns_in_out'] + $data['cns'],
                        'krg_in_out' => $cekStockBaru['krg_in_out'] + $data['krg']
                    ];

                $this->stockModel->update($cekStockBaru['id_stock'], $updateDataIn);

                $newStockId = $cekStockBaru['id_stock'];
                // Update id_stock di tabel pemasukan berdasarkan id_out_celup
                $updatePemasukan = [
                    'id_stock' => $cekStockBaru['id_stock'],
                    'nama_cluster' => $cluster,
                ];

                $updateIdStock = $this->pemasukanModel
                    ->where('id_out_celup', $data['id_out_celup'])
                    ->set($updatePemasukan)
                    ->update();

                if ($updateIdStock) {
                    log_message('info', 'Berhasil memperbarui id_stock di tabel pemasukan: ' . json_encode($updatePemasukan));
                } else {
                    log_message('error', 'Gagal memperbarui id_stock di tabel pemasukan: ' . json_encode($updatePemasukan));
                    return [
                        'status' => 'error',
                        'message' => 'Gagal memperbarui id_stock di tabel pemasukan',
                    ];
                }
            }
            // inser data baru jika belum ada data
            else {
                // Jika data stok baru belum ada, tambahkan data baru
                $insertData = [
                    'no_model'          => $data['no_model'],
                    'item_type'         => $data['item_type'],
                    'kode_warna'        => $data['kode_warna'],
                    'warna'             => $data['warna'],
                    'nama_cluster'      => $cluster,
                    'kgs_stock_awal'    => $stock_awal == 'Ya' ? $data['kgs'] : 0,
                    'cns_stock_awal'    => $stock_awal == 'Ya' ? $data['cns'] : 0,
                    'krg_stock_awal'    => $stock_awal == 'Ya' ? $data['krg'] : 0,
                    'lot_awal'          => $stock_awal == 'Ya' ? $data['lot'] : '',
                    'kgs_in_out'        => $stock_awal == '' ? $data['kgs'] : 0,
                    'cns_in_out'        => $stock_awal == '' ? $data['cns'] : 0,
                    'krg_in_out'        => $stock_awal == '' ? $data['krg'] : 0,
                    'lot_stock'         => $stock_awal == '' ? $data['lot'] : '',
                    'admin'             => session()->get('username'),
                    'created_at'        => date('Y-m-d H:i:s'),
                    'updated_at'        => NULL,
                ];

                if ($this->stockModel->insert($insertData)) {
                    // Dapatkan ID stok baru
                    $newStockId = $this->stockModel->getInsertID();

                    if ($newStockId) {
                        // Update id_stock di tabel pemasukan berdasarkan id_out_celup
                        $updatePemasukan = [
                            'id_stock' => $newStockId,
                            'nama_cluster' => $cluster,
                        ];

                        $updateIdStock = $this->pemasukanModel
                            ->where('id_out_celup', $data['id_out_celup'])
                            ->set($updatePemasukan)
                            ->update();

                        if ($updateIdStock) {
                            log_message('info', 'Berhasil memperbarui id_stock di tabel pemasukan: ' . json_encode($updatePemasukan));
                        } else {
                            log_message('error', 'Gagal memperbarui id_stock di tabel pemasukan: ' . json_encode($updatePemasukan));
                            return [
                                'status' => 'error',
                                'message' => 'Gagal memperbarui id_stock di tabel pemasukan',
                            ];
                        }
                    } else {
                        log_message('error', 'Gagal mendapatkan ID stok baru setelah insert.');
                        return [
                            'status' => 'error',
                            'message' => 'Gagal mendapatkan ID stok baru',
                        ];
                    }
                } else {
                    log_message('error', 'Gagal menambahkan data stok baru: ' . json_encode($insertData));
                    return [
                        'status' => 'error',
                        'message' => 'Gagal menambahkan data stok baru',
                    ];
                }
            }
            // Update stok keluar
            $updateDataOut = $stock_awal == '' ?
                [
                    'kgs_in_out' => $cekStock['kgs_in_out'] - $data['kgs'],
                    'cns_in_out' => $cekStock['cns_in_out'] - $data['cns'],
                    'krg_in_out' => $cekStock['krg_in_out'] - $data['krg'],
                ] :
                [
                    'kgs_stock_awal' => $cekStock['kgs_stock_awal'] - $data['kgs'],
                    'cns_stock_awal' => $cekStock['cns_stock_awal'] - $data['cns'],
                    'krg_stock_awal' => $cekStock['krg_stock_awal'] - $data['krg'],
                ];

            $kurangiStock = $this->stockModel->update($cekStock['id_stock'], $updateDataOut);

            if (!$kurangiStock) {
                return [
                    'status' => 'error',
                    'message' => 'Gagal mengurangi stock',
                ];
            }
        }
        // insert data history
        $insertHistory = [
            'id_stock_old'  => $details[0]['id_stock'],
            'id_stock_new'  => $newStockId,
            'cluster_old'   => $details[0]['cluster_old'],
            'cluster_new'   => $cluster,
            'kgs'           => $totalKgs, // Total kgs
            'cns'           => $totalCns, // Total cns
            'krg'           => $totalKrg, // Total krg
            'lot'           => $details[0]['lot'],
            'keterangan'    => "Pindah Cluster",
            'admin'         => session()->get('username'),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => NULL,
        ];
        $history = $this->historyStock->insert($insertHistory);
        if (!$history) {
            return [
                'status' => 'error',
                'message' => 'Gagal insert history',
            ];
        }
        return $this->response->setJSON([
            'success' => true,
            'received' => $cluster,
            'details' => $cekStock,
        ]);
    }
    public function reportPengiriman()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Pengirirman Area',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-pengiriman', $data);
    }
    public function filterPengiriman()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pengeluaranModel->getFilterPengiriman($key, $tanggalAwal, $tanggalAkhir);
        // dd($data);
        return $this->response->setJSON($data);
    }
    public function reportGlobal()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Global',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-global', $data);
    }
    public function filterReportGlobal()
    {
        $key = $this->request->getGet('key');
        log_message('debug', 'Received key: ' . $key);  // Log key yang diterima
        if (empty($key)) {
            return $this->response->setJSON(['error' => 'Key is missing']);
        }

        $data = $this->masterOrderModel->getFilterReportGlobal($key);
        // Log data yang diterima dari model
        log_message('debug', 'Query result: ' . print_r($data, true));

        if (empty($data)) {
            return $this->response->setJSON(['error' => 'No data found']);
        }

        return $this->response->setJSON($data);
    }
    public function reportGlobalNylon()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Global',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-global-nylon', $data);
    }
    public function filterReportGlobalNylon()
    {
        $key = $this->request->getGet('key');
        $jenis = 'NYLON';
        log_message('debug', 'Received key: ' . $key);  // Log key yang diterima
        if (empty($key)) {
            return $this->response->setJSON(['error' => 'Key is missing']);
        }

        $data = $this->masterOrderModel->getFilterReportGlobal($key, $jenis);
        // Log data yang diterima dari model
        log_message('debug', 'Query result: ' . print_r($data, true));

        if (empty($data)) {
            return $this->response->setJSON(['error' => 'No data found']);
        }

        return $this->response->setJSON($data);
    }
    // public function savePengeluaranSelainOrder()
    // {
    //     // Pastikan ini adalah request AJAX / POST
    //     if (!$this->request->isAJAX()) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Invalid request.'
    //         ]);
    //     }

    //     // Ambil input dari request
    //     $idOutCelup    = $this->request->getPost('id_out_celup');
    //     $kategori      = $this->request->getPost('kategori');
    //     $kgsOtherOut   = $this->request->getPost('kgs_other_out');
    //     $cnsOtherOut   = $this->request->getPost('cns_other_out');
    //     $krgOtherOut   = $this->request->getPost('krg_other_out');
    //     $lot           = $this->request->getPost('lot');
    //     $idStock       = $this->request->getPost('id_stock');
    //     $namaCluster   = $this->request->getPost('nama_cluster');

    //     log_message('debug', 'idStock: ' . print_r($idStock, true));

    //     // Validasi awal
    //     if (!$idOutCelup || !$kategori) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'ID celup atau kategori tidak boleh kosong.'
    //         ]);
    //     }

    //     // Simulasi simpan data, ganti dengan logika sebenarnya
    //     $model = $this->otherOutModel; // Sesuaikan dengan model kamu
    //     $save = $model->insert([
    //         'id_out_celup' => $idOutCelup,
    //         'kategori' => $kategori,
    //         'tgl_other_out' => date('Y-m-d'),
    //         'kgs_other_out' => $kgsOtherOut,
    //         'cns_other_out' => $cnsOtherOut,
    //         'krg_other_out' => $krgOtherOut,
    //         'lot_other_out' => $lot,
    //         'admin' => session()->get('username'),
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'nama_cluster' => $namaCluster,
    //     ]);

    //     if ($save) {
    //         // Mengambil data stok yang ada berdasarkan id_stock
    //         $selectStock = $this->stockModel->where('id_stock', $idStock)->first();

    //         // Pastikan stok ditemukan
    //         if ($selectStock) {
    //             $kgsStockAwal = $selectStock['kgs_stock_awal'];
    //             $kgsInOut = $selectStock['kgs_in_out'];

    //             if ($kgsStockAwal >= $kgsOtherOut) {
    //                 $kgsNew = $selectStock['kgs_stock_awal'] - $kgsOtherOut;
    //                 $cnsNew = $selectStock['cns_stock_awal'] - $cnsOtherOut;
    //                 $krgNew = $selectStock['krg_stock_awal'] - $krgOtherOut;

    //                 // Update data stok
    //                 $this->stockModel->set('kgs_stock_awal', $kgsNew)
    //                     ->set('cns_stock_awal', $cnsNew)
    //                     ->set('krg_stock_awal', $krgNew)
    //                     ->where('id_stock', $idStock)
    //                     ->update();
    //             } elseif ($kgsInOut >= $kgsOtherOut) {
    //                 $kgsNew = $selectStock['kgs_in_out'] - $kgsOtherOut;
    //                 $cnsNew = $selectStock['cns_in_out'] - $cnsOtherOut;
    //                 $krgNew = $selectStock['krg_in_out'] - $krgOtherOut;

    //                 // Update data stok
    //                 $this->stockModel->set('kgs_in_out', $kgsNew)
    //                     ->set('cns_in_out', $cnsNew)
    //                     ->set('krg_in_out', $krgNew)
    //                     ->where('id_stock', $idStock)
    //                     ->update();
    //             }
    //         }

    //         $selectOutCelup = $this->outCelupModel->where('id_out_celup', $idOutCelup)->first();
    //         $totalPengeluaran = $this->pengeluaranModel
    //             ->selectSum('kgs_out')
    //             ->where('id_out_celup', $idOutCelup)
    //             ->first();

    //         $totalOtherOut = $this->otherOutModel
    //             ->selectSum('kgs_other_out')
    //             ->where('id_out_celup', $idOutCelup)
    //             ->first();
    //         $kgsMasuk = $selectOutCelup['kgs_kirim'];
    //         $kgsKeluar = ($totalPengeluaran['kgs_out'] ?? 0) + ($totalOtherOut['kgs_other_out'] ?? 0);
    //         // Debugging
    //         // log_message('debug', 'kgsMasuk: ' . $kgsMasuk);
    //         // log_message('debug', 'kgsKeluar: ' . $kgsKeluar);
    //         if (round($kgsKeluar, 2) == round($kgsMasuk, 2)) {
    //             // Update data stok
    //             $this->pemasukanModel->set('out_jalur', '1')
    //                 ->where('id_out_celup', $idOutCelup)
    //                 ->where('id_stock', $idStock)
    //                 ->update();
    //         }
    //         // if ($krgOtherOut == 1) {
    //         //     // Update data stok
    //         //     $this->pemasukanModel->set('out_jalur', '1')
    //         //         ->where('id_out_celup', $idOutCelup)
    //         //         ->where('id_stock', $idStock)
    //         //         ->update();
    //         // }

    //         // Mengirim response JSON ke frontend
    //         return $this->response->setJSON([
    //             'success' => true,
    //             'message' => 'Data berhasil disimpan.'
    //         ]);
    //     } else {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Gagal menyimpan data.'
    //         ]);
    //     }
    // }

    public function savePengeluaranSelainOrder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request.'
            ]);
        }

        $idOutCelup    = $this->request->getPost('id_out_celup');
        $kategori      = $this->request->getPost('kategori');
        $kgsOtherOut   = $this->request->getPost('kgs_other_out');
        $cnsOtherOut   = $this->request->getPost('cns_other_out');
        $krgOtherOut   = $this->request->getPost('krg_other_out');
        $lot           = $this->request->getPost('lot');
        $idStock       = $this->request->getPost('id_stock');
        $namaCluster   = $this->request->getPost('nama_cluster');

        if (!$idOutCelup || !$kategori) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID celup atau kategori tidak boleh kosong.'
            ]);
        }

        $this->db->transBegin();

        try {
            $save = $this->otherOutModel->insert([
                'id_out_celup' => $idOutCelup,
                'kategori' => $kategori,
                'tgl_other_out' => date('Y-m-d'),
                'kgs_other_out' => $kgsOtherOut,
                'cns_other_out' => $cnsOtherOut,
                'krg_other_out' => $krgOtherOut,
                'lot_other_out' => $lot,
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
                'nama_cluster' => $namaCluster,
            ]);

            if ($save) {
                $selectStock = $this->stockModel->where('id_stock', $idStock)->first();

                if ($selectStock) {
                    $kgsStockAwal = $selectStock['kgs_stock_awal'];
                    $kgsInOut     = $selectStock['kgs_in_out'];

                    if ($kgsStockAwal >= $kgsOtherOut) {
                        $kgsNew = $selectStock['kgs_stock_awal'] - $kgsOtherOut;
                        $cnsNew = $selectStock['cns_stock_awal'] - $cnsOtherOut;
                        $krgNew = $selectStock['krg_stock_awal'] - $krgOtherOut;

                        $this->stockModel->set('kgs_stock_awal', $kgsNew)
                            ->set('cns_stock_awal', $cnsNew)
                            ->set('krg_stock_awal', $krgNew)
                            ->where('id_stock', $idStock)
                            ->update();
                    } elseif ($kgsInOut >= $kgsOtherOut) {
                        $kgsNew = $selectStock['kgs_in_out'] - $kgsOtherOut;
                        $cnsNew = $selectStock['cns_in_out'] - $cnsOtherOut;
                        $krgNew = $selectStock['krg_in_out'] - $krgOtherOut;

                        $this->stockModel->set('kgs_in_out', $kgsNew)
                            ->set('cns_in_out', $cnsNew)
                            ->set('krg_in_out', $krgNew)
                            ->where('id_stock', $idStock)
                            ->update();
                    }
                }

                $selectOutCelup = $this->outCelupModel->where('id_out_celup', $idOutCelup)->first();
                $totalPengeluaran = $this->pengeluaranModel
                    ->selectSum('kgs_out')
                    ->where('id_out_celup', $idOutCelup)
                    ->first();

                $totalOtherOut = $this->otherOutModel
                    ->selectSum('kgs_other_out')
                    ->where('id_out_celup', $idOutCelup)
                    ->first();

                $kgsMasuk  = $selectOutCelup['kgs_kirim'];
                $kgsKeluar = ($totalPengeluaran['kgs_out'] ?? 0) + ($totalOtherOut['kgs_other_out'] ?? 0);

                if (round($kgsKeluar, 2) == round($kgsMasuk, 2)) {
                    $this->pemasukanModel->set('out_jalur', '1')
                        ->where('id_out_celup', $idOutCelup)
                        ->where('id_stock', $idStock)
                        ->update();
                }
            }

            // cek hasil transaksi
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Transaksi gagal.']);
            } else {
                $this->db->transCommit();
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil disimpan.']);
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function otherIn()
    {
        $no_model = $this->masterOrderModel->getAllNoModel();
        $cluster = $this->clusterModel->getDataCluster();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'no_model' => $no_model,
            'cluster' => $cluster
        ];
        return view($this->role . '/warehouse/form-other-inNew', $data);
    }
    public function getItemTypeForOtherIn($idOrder)
    {
        // Pastikan $idOrder diambil dengan benar
        if (!$idOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID Order tidak ditemukan.'
            ]);
        }

        // Ambil data dari model
        $itemTypes = $this->db->table('material') // Ganti 'nama_tabel' dengan nama tabel Anda
            ->distinct()
            ->select('item_type') // Pastikan 'id' dan 'item_type' adalah nama kolom yang valid
            ->where('id_order', $idOrder)
            ->get()
            ->getResultArray();

        // Jika data ditemukan, kembalikan sebagai JSON
        if (!empty($itemTypes)) {
            return $this->response->setJSON($itemTypes);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan untuk ID Order ini.'
            ]);
        }
    }
    public function getKodeWarnaForOtherIn()
    {
        $idOrder = $this->request->getPost('id_order');
        $itemType = $this->request->getPost('item_type');
        // Pastikan $idOrder diambil dengan benar
        if (!$idOrder && !$itemType) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID Order tidak ditemukan.'
            ]);
        }

        // Ambil data dari model
        $kodeWarna = $this->db->table('material') // Ganti 'nama_tabel' dengan nama tabel Anda
            ->distinct()
            ->select('kode_warna') // Pastikan 'id' dan 'item_type' adalah nama kolom yang valid
            ->where('id_order', $idOrder)
            ->where('item_type', $itemType)
            ->get()
            ->getResultArray();

        // Jika data ditemukan, kembalikan sebagai JSON
        if (!empty($kodeWarna)) {
            return $this->response->setJSON($kodeWarna);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan untuk ID Order ini.',
                'data' => $kodeWarna
            ]);
        }
    }
    public function getWarnaForOtherIn()
    {
        $idOrder = $this->request->getPost('id_order');
        $itemType = $this->request->getPost('item_type');
        $kodeWarna = $this->request->getPost('kode_warna');
        // Pastikan $idOrder diambil dengan benar
        if (!$idOrder && !$itemType && !$kodeWarna) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID Order tidak ditemukan.'
            ]);
        }

        // Ambil data dari model
        $warna = $this->db->table('material') // Ganti 'nama_tabel' dengan nama tabel Anda
            ->distinct()
            ->select('color') // Pastikan 'id' dan 'item_type' adalah nama kolom yang valid
            ->where('id_order', $idOrder)
            ->where('item_type', $itemType)
            ->where('kode_warna', $kodeWarna)
            ->get() // Jalankan query
            ->getRow(); // Ambil satu baris pertama

        // Jika data ditemukan, kembalikan sebagai JSON
        if (!empty($warna)) {
            return $this->response->setJSON($warna);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan untuk ID Order ini.',
                'data' => $kodeWarna
            ]);
        }
    }
    public function saveOtherIn()
    {
        $data = $this->request->getPost();
        // dd($data);
        $db = \Config\Database::connect();
        $db->transBegin();  // Mulai transaksi
        // dd($data);
        if (empty($data['id_order'])) {
            // prepare create new master order
            $dataOrder = [
                'no_model' => $data['no_model'],
                'no_order' => '-',
                'buyer' => '-',
                'foll_up' => '-',
                'lco_date' => '00/00/0000',
                'admin' => session()->get('username')
            ];

            // Insert new master order
            $saveOrder = $this->masterOrderModel->insert($dataOrder);
            $idOrder = $this->masterOrderModel->insertID();
        } else {
            $idOrder = $data['id_order'];
        }
        // === Cek master_material (wajib dicek selalu, meski order ada) ===
        $masterMaterial = $this->masterMaterialModel
            ->where('item_type', $data['item_type'])
            ->first();

        if (empty($masterMaterial)) {
            $this->masterMaterialModel->insert([
                'item_type' => $data['item_type'],
                'deskripsi' => $data['item_type'],
                'jenis'     => null
            ]);
        }

        // === Cek material (per order + item_type + warna) ===
        $material = $this->materialModel
            ->where('id_order', $idOrder)
            ->where('item_type', $data['item_type'])
            ->where('kode_warna', $data['kode_warna'])
            ->first();

        if (empty($material)) {
            $this->materialModel->insert([
                'id_order'   => $idOrder,
                'style_size' => '',
                'area'       => '',
                'color'      => $data['warna'],
                'item_type'  => $data['item_type'],
                'kode_warna' => $data['kode_warna'],
                'composition' => '',
                'gw'         => 0,
                'qty_pcs'    => 0,
                'loss'       => 0,
                'kgs'        => $data['total_kgs'],
                'admin'      => session()->get('username')
            ]);
        } else {
            $newKgs = $material['kgs'] + $data['total_kgs'];
            // Update material jika sudah ada
            $this->materialModel->update($material['id_material'], [
                'gw'         => 0,
                'qty_pcs'    => 0,
                'loss'       => 0,
                'kgs'        => $newKgs,
                'admin'      => session()->get('username')
            ]);
        }

        // 1) Insert Bon
        $dataOtherBon = [
            'no_model'       => $data['no_model'],
            'item_type'      => $data['item_type'],
            'kode_warna'     => $data['kode_warna'],
            'warna'          => $data['warna'],
            'tgl_datang'     => $data['tgl_datang'],
            'no_surat_jalan' => $data['no_surat_jalan'],
            'detail_sj'      => $data['detail_sj'],
            'keterangan'     => $data['keterangan'],
            'ganti_retur'    => $data['ganti_retur'],
            'po_tambahan'    => ($data['detail_sj'] === "PO(+)") ? '1' : '0',
            'admin'          => session()->get('username'),
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        // dd($dataOtherBon);
        $saveBon = $this->otherBonModel->insert($dataOtherBon);
        $id_other_in = $this->otherBonModel->insertID();
        if (!$saveBon) {
            $db->transRollback();
            session()->setFlashdata('error', "Gagal menyimpan data Bon");
            return redirect()->to(base_url($this->role . "/otherIn"));
        }

        // 2) Loop insert karung + pemasukan
        $jumlahKrg = count($data['no_karung']);
        for ($i = 0; $i < $jumlahKrg; $i++) {
            // a) Insert ke outCelup
            $dataKrg = [
                'id_other_bon' => $id_other_in,
                'no_model'     => $data['no_model'],
                'l_m_d'        => $data['l_m_d'],
                'harga'        => $data['harga'],
                'no_karung'    => $data['no_karung'][$i],
                'gw_kirim'     => $data['gw'][$i],
                'kgs_kirim'    => $data['kgs'][$i],
                'cones_kirim'  => $data['cones'][$i],
                'lot_kirim'    => $data['lot'],
                'ganti_retur'  => $data['ganti_retur'],
                'admin'        => session()->get('username'),
                'created_at'   => date('Y-m-d H:i:s'),
            ];
            $saveKrg = $this->outCelupModel->insert($dataKrg);
            $id_out_celup = $this->outCelupModel->insertID();

            if (!$saveKrg) {
                $db->transRollback();
                session()->setFlashdata('error', "Gagal menyimpan data Karung ke-$i");
                return redirect()->to(base_url($this->role . "/otherIn"));
            }

            // b) Insert ke pemasukan
            $dataPemasukan = [
                'id_out_celup' => $id_out_celup,
                'tgl_masuk'    => date('Y-m-d'),
                'nama_cluster' => $data['cluster'][$i],
                'out_jalur'    => '0',
                'admin'        => session()->get('username'),
                'created_at'   => date('Y-m-d H:i:s'),
            ];
            $savePemasukan = $this->pemasukanModel->insert($dataPemasukan);
            $idPemasukan = $this->pemasukanModel->insertID();

            if (!$savePemasukan) {
                $db->transRollback();
                session()->setFlashdata('error', "Gagal menyimpan pemasukan ke-$i");
                return redirect()->to(base_url($this->role . "/otherIn"));
            }

            // c) Cek stock lama / insert baru
            $existingStock = $this->stockModel
                ->where('no_model', $data['no_model'])
                ->where('item_type', $data['item_type'])
                ->where('kode_warna', $data['kode_warna'])
                ->where('nama_cluster', $data['cluster'][$i]) // Fix: 'nama_cluster'
                ->where('lot_stock', $data['lot'])
                ->first();

            if ($existingStock) {
                // Update stok lama
                $this->stockModel->update($existingStock['id_stock'], [
                    'kgs_in_out' => $existingStock['kgs_in_out'] + $data['kgs'][$i],
                    'cns_in_out' => $existingStock['cns_in_out'] + $data['cones'][$i],
                    'krg_in_out' => $existingStock['krg_in_out'] + 1,
                ]);
                $idStok = $existingStock['id_stock'];
            } else {
                // Insert stok baru
                $newStock = [
                    'no_model'     => $data['no_model'],
                    'item_type'    => $data['item_type'],
                    'kode_warna'   => $data['kode_warna'],
                    'warna'        => $data['warna'],
                    'nama_cluster' => $data['cluster'][$i],
                    'lot_stock'    => $data['lot'],
                    'kgs_in_out'   => $data['kgs'][$i],
                    'cns_in_out'   => $data['cones'][$i],
                    'krg_in_out'   => 1,
                    'admin'        => session()->get('username'),
                    'created_at'   => date('Y-m-d H:i:s'),
                ];
                $this->stockModel->insert($newStock);
                $idStok = $this->stockModel->insertID();
            }

            // d) Update pemasukan dengan id_stok
            $this->pemasukanModel->update($idPemasukan, [
                'id_stock' => $idStok
            ]);
        }

        // 3) Commit transaksi
        if ($db->transStatus() === false) {
            $db->transRollback();
            session()->setFlashdata('error', "Transaksi gagal, data dibatalkan");
        } else {
            $db->transCommit();
            session()->setFlashdata('success', "Data berhasil disimpan");
        }

        return redirect()->to(base_url($this->role . "/otherIn"));
    }
    public function listBarcode()
    {
        $tglOtherBon = $this->otherBonModel->getTglDataOtherBon();

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'tglDatang' => $tglOtherBon,
        ];
        return view($this->role . '/warehouse/otherBarcodePertgl', $data);
    }
    public function listBarcodeFilter()
    {
        $filterDate = $this->request->getJSON(true)['filter_date'] ?? null;

        if (!$filterDate) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Tanggal filter tidak boleh kosong.']);
        }

        $tgl = $this->otherBonModel->filterTglDataOtherBon($filterDate);

        return $this->response->setJSON($tgl);
    }
    public function detailListBarcode($tglDatang)
    {
        $dataOtherBon = $this->otherBonModel->getDataOtherBon($tglDatang);

        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataBon' => $dataOtherBon,
        ];
        return view($this->role . '/warehouse/detailOtherBarcode', $data);
    }

    public function reportGlobalStockBenang()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-global-benang', $data);
    }

    public function filterReportGlobalBenang()
    {
        $key = $this->request->getGet('key');
        $jenis = 'BENANG';

        // $data = $this->stockModel->getFilterReportGlobalBenang($key);
        $data = $this->masterOrderModel->getFilterReportGlobal($key, $jenis);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function reportSisaPakaiBenang()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Report Sisa Pakai Benang',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-sisa-pakai-benang', $data);
    }

    public function filterSisaPakai()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $jenis = $this->request->getGet('jenis');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        // $data = $this->stockModel->getFilterSisaPakaiBenang($bulan, $noModel, $kodeWarna);
        $data = $this->materialModel->getFilterSisaPakai($jenis, $bulan, $noModel, $kodeWarna);

        return $this->response->setJSON($data);
    }

    public function reportSisaPakaiNylon()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Report Sisa Pakai Nylon',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-sisa-pakai-nylon', $data);
    }

    public function filterSisaPakaiNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiNylon($bulan, $noModel, $kodeWarna);

        return $this->response->setJSON($data);
    }

    public function reportSisaPakaiSpandex()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Report Sisa Pakai Spandex',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-sisa-pakai-spandex', $data);
    }

    public function filterSisaPakaiSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiSpandex($bulan, $noModel, $kodeWarna);

        return $this->response->setJSON($data);
    }

    public function reportSisaPakaiKaret()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Report Sisa Pakai Karet',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-sisa-pakai-karet', $data);
    }

    public function filterSisaPakaiKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiKaret($bulan, $noModel, $kodeWarna);

        return $this->response->setJSON($data);
    }
    public function historyPindahOrder()
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

        return view($this->role . '/warehouse/history-pindah-order', [
            'role'    => $this->role,
            'title'   => 'History Pindah Order',
            'active'  => $this->active,
            'history' => $dataPindah,
        ]);
    }

    public function reportSisaDatangBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        // dd($bulan);
        $getFilterData = $this->materialModel->getFilterSisaDatangBenang($bulan, $noModel, $kodeWarna);
        // dd($getFilterData);
        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'active' => $this->active,
            'role' => $this->role,
            'title' => 'Report Sisa Datang Benang',
            'getFilterData' => $getFilterData
        ];
        // dd($data);

        return view($this->role . '/warehouse/report-sisa-datang-benang', $data);
    }

    public function reportSisaDatangNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $getFilterData = $this->materialModel->getFilterSisaDatangNylon($bulan, $noModel, $kodeWarna);

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'active' => $this->active,
            'role' => $this->role,
            'title' => 'Report Sisa Datang Nylon',
            'getFilterData' => $getFilterData
        ];

        return view($this->role . '/warehouse/report-sisa-datang-nylon', $data);
    }

    public function reportSisaDatangSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $getFilterData = $this->materialModel->getFilterSisaDatangSpandex($bulan, $noModel, $kodeWarna);

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'active' => $this->active,
            'role' => $this->role,
            'title' => 'Report Sisa Datang Spandex',
            'getFilterData' => $getFilterData
        ];

        return view($this->role . '/warehouse/report-sisa-datang-spandex', $data);
    }

    public function reportSisaDatangKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;

        $getFilterData = $this->materialModel->getFilterSisaDatangKaret($bulan, $noModel, $kodeWarna);

        if ($this->request->isAJAX()) {
            // set header JSON dan langsung echo data
            return $this->response
                ->setStatusCode(200)
                ->setJSON($getFilterData);
        }

        $data = [
            'active' => $this->active,
            'role' => $this->role,
            'title' => 'Report Sisa Datang Karet',
            'getFilterData' => $getFilterData
        ];

        return view($this->role . '/warehouse/report-sisa-datang-karet', $data);
    }

    public function reportBenangMingguan()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Benang Per Minggu',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-benang-mingguan', $data);
    }

    public function filterBenangMingguan()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterBenang($tanggalAwal, $tanggalAkhir);
        return $this->response->setJSON($data);
    }

    public function reportBenangBulanan()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Benang Per Bulan',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-benang-bulanan', $data);
    }

    public function filterBenangBulanan()
    {
        $bulan = $this->request->getGet('bulan');
        if (empty($bulan) || !preg_match('/^\d{4}\-\d{2}$/', $bulan)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Parameter “bulan” harus dalam format YYYY-MM']);
        }

        $timestamp     = strtotime($bulan . '-01');
        $tanggalAwal   = date('Y-m-01', $timestamp);
        $tanggalAkhir  = date('Y-m-t', $timestamp);
        $data = $this->pemasukanModel->getFilterBenang($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
    public function getKeteranganDatang()
    {
        $idBon = $this->request->getGet('id_bon');
        $idOtherBon = $this->request->getGet('id_other_bon');

        $data = [];

        if ($idBon) {
            $data = $this->bonCelupModel->where('id_bon', $idBon)->first();
        } elseif ($idOtherBon) {
            $data = $this->otherBonModel->where('id_other_bon', $idOtherBon)->first();
        }

        return $this->response->setJSON([
            'keterangan' => $data['keterangan'] ?? ''
        ]);
    }
    public function updateKeteranganDatang()
    {
        $idBon = $this->request->getPost('id_bon');
        $idOtherBon = $this->request->getPost('id_other_bon');
        $keterangan = $this->request->getPost('keterangan');

        if ($idBon) {
            $this->bonCelupModel->update($idBon, ['keterangan' => $keterangan]);
        } elseif ($idOtherBon) {
            $this->otherBonModel->update($idOtherBon, ['keterangan' => $keterangan]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            // 'message' => 'Keterangan berhasil diperbarui.'
        ]);
    }

    public function listOtherBarcode()
    {
        $listBarcode = $this->pemasukanModel->listOtherBarcode();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "List Other Barcode",
            'listBarcode' => $listBarcode,
        ];

        return view($this->role . '/warehouse/list-other-barcode', $data);
    }

    public function detailOtherBarcode($tglDatang)
    {
        $detailOtherBarcode = $this->pemasukanModel->detailOtherBarcode($tglDatang);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Detail Other Barcode",
            'detailOtherBarcode' => $detailOtherBarcode,
            'tglDatang' => $tglDatang
        ];
        return view($this->role . '/warehouse/detail-other-barcode', $data);
    }

    public function hapusPengeluaranJalur()
    {
        $idPengeluaran = $this->request->getPost('id_pengeluaran');
        $idOutCelup    = $this->request->getPost('id_out_celup');
        $idStock       = $this->request->getPost('id_stock');
        $kgsOut        = (float) $this->request->getPost('kgs_out');
        $cnsOut        = (float) $this->request->getPost('cns_out');
        $krgOut        = (float) $this->request->getPost('krg_out');

        if ($idPengeluaran && $idOutCelup && $idStock) {
            $this->db->transStart();

            $this->pengeluaranModel
                ->where('status', 'Pengeluaran Jalur')
                ->where('id_pengeluaran', $idPengeluaran)
                ->delete();

            if ($krgOut > 0) {
                $this->pemasukanModel
                    ->where('id_out_celup', $idOutCelup)
                    ->set('out_jalur', '0')
                    ->update();
            }

            $select = $this->stockModel
                ->select('kgs_in_out, cns_in_out, krg_in_out')
                ->where('id_stock', $idStock)
                ->first();

            if ($select) {
                $this->stockModel
                    ->where('id_stock', $idStock)
                    ->set([
                        'kgs_in_out' => $select['kgs_in_out'] + $kgsOut,
                        'cns_in_out' => $select['cns_in_out'] + $cnsOut,
                        'krg_in_out' => $select['krg_in_out'] + $krgOut
                    ])
                    ->update();
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data pengeluaran jalur berhasil dihapus.'
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }

    public function reportDatangNylon()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Datang Nylon',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-datang-nylon', $data);
    }

    public function filterDatangNylon()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterDatangNylon($key, $tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }

    public function detailDatangBenang($idOutCelup)
    {
        $detailDatangBenang = $this->pemasukanModel->getDetailDatangBenang($idOutCelup);
        // dd($detailDatangBenang);
        if (empty($detailDatangBenang)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        return $this->response->setJSON($detailDatangBenang);
    }

    public function updateDatangBenang()
    {
        $data = $this->request->getPost();
        // log_message('info', 'Update Datang Benang: ' . json_encode($data));

        if (empty($data['id_out_celup'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak lengkap.'
            ]);
        }

        // Cek apakah id_out_celup ada
        $idCek = $this->outCelupModel
            ->where('id_out_celup', $data['id_out_celup'])
            ->first();

        if (!$idCek) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data out celup tidak ditemukan.'
            ]);
        }

        // Kalau sudah ada keterkaitan dengan other_bon
        if (!empty($idCek['id_other_bon'])) {
            $idPemasukan = $this->pemasukanModel
                ->where('id_out_celup', $data['id_out_celup'])
                ->first();

            if (!$idPemasukan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data pemasukan tidak ditemukan.'
                ]);
            }

            $idStock = $this->stockModel
                ->where('id_stock', $idPemasukan['id_stock'])
                ->first();

            if (!$idStock) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data stock tidak ditemukan.'
                ]);
            }

            // Hitung ulang kgs & cones
            if ($data['kgs_kirim'] != $idStock['kgs_in_out']) {
                $kgsInout   = ($idStock['kgs_in_out'] - $idCek['kgs_kirim']) + $data['kgs_kirim'];
                $conesInout = ($idStock['cns_in_out'] - $idCek['cones_kirim']) + $data['cones_kirim'];
            } else {
                $kgsInout   = $data['kgs_kirim'];
                $conesInout = $data['cones_kirim'];
            }

            $lotInout = $data['lot_kirim'];

            // Data untuk update out_celup
            $dataOut = [
                'no_model'   => $data['no_model'],
                'kgs_kirim'  => $data['kgs_kirim'],
                'cones_kirim' => $data['cones_kirim'],
                'lot_kirim'  => $lotInout,
                'harga'      => $data['harga'],
                'gw_kirim'   => $data['gw_kirim']
            ];

            // Data untuk update stock
            $stock = [
                'no_model'     => $data['no_model'],
                'item_type'    => $data['item_type'],
                'kode_warna'   => $data['kode_warna'],
                'warna'        => $data['warna'],
                'kgs_in_out'   => $kgsInout,
                'cns_in_out' => $conesInout,
                'lot_stock'    => $lotInout
            ];

            $this->outCelupModel->update($data['id_out_celup'], $dataOut);
            $this->stockModel->update($idStock['id_stock'], $stock);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak dapat dirubah karena tidak valid.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function importPemasukan()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/import-pemasukan', $data);
    }

    public function reportPemakaianNylon()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Pemakaian Nylon',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-pemakaian-nylon', $data);
    }

    public function filterPemakaianNylon()
    {
        $buyer = $this->request->getGet('buyer');

        $data = $this->pengeluaranModel->getFilterPemakaianNylonByBuyer($buyer);
        // dd($data);
        return $this->response->setJSON($data);
    }
    public function getNoKarung()
    {
        $noModel   = $this->request->getGet('noModel');
        $itemType  = $this->request->getGet('itemType');
        $kodeWarna = $this->request->getGet('kodeWarna');
        $lot       = $this->request->getGet('lot');
        $retur     = $this->request->getGet('retur'); // 1 atau 0

        // Safety check
        if (!$noModel || !$itemType || !$kodeWarna || !$lot) {
            return $this->response->setJSON([
                'karung' => [],
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $id = [
            'no_model' => $noModel,
            'item_type' => $itemType,
            'kode_warna' => $kodeWarna,
            'lot' => $lot,
        ];
        // Ambil da ta berdasarkan no_model yang dipilih
        if ($retur == 0) {
            $data = $this->outCelupModel->getNoKarung($id);
        } else {
            $data = $this->returModel->getNoKarung($id);
        }

        // filter: buang id_out_celup yang sudah ada di tabel pemasukan
        $filtered = [];
        foreach ($data as $row) {
            $exists = $this->db->table('pemasukan')
                ->where('id_out_celup', $row['id_out_celup'])
                ->where('out_jalur', 0)
                ->countAllResults();

            if ($exists == 0) { // kalau belum ada → simpan
                $filtered[] = $row;
            }
        }

        return $this->response->setJSON([
            'data' => $filtered
        ]);
    }

    public function reportOtherOut()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/warehouse/report-other-out', $data);
    }

    public function filterOtherOut()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->otherOutModel->getFilterOtherOut($key, $tanggalAwal, $tanggalAkhir);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function reportIndri()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Report Indri',
            'active' => $this->active
        ];
        return view($this->role . '/warehouse/report-indri', $data);
    }

    public function filterReportIndri()
    {
        $buyer = $this->request->getGet('buyer');
        $deliveryAwal = $this->request->getGet('delivery_awal');
        $deliveryAkhir = $this->request->getGet('delivery_akhir');
        // dd($buyer, $deliveryAwal, $deliveryAkhir);
        $data = $this->materialModel->getFilterReportIndri($buyer, $deliveryAwal, $deliveryAkhir);
        // dd($data);
        return $this->response->setJSON($data);
    }

    public function savePindahOrderTest()
    {
        $reqData = $this->request->getPost();

        // 1) Validasi model tujuan
        if (empty($reqData['no_model_tujuan']) || count(explode('|', $reqData['no_model_tujuan'])) < 4) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data model tujuan tidak lengkap atau tidak valid.'
            ]);
        }
        [$noModel, $itemType, $kodeWarna, $warna] = explode('|', $reqData['no_model_tujuan']);

        $idOutCelup = $reqData['idOutCelup'] ?? [];
        $idStock    = $reqData['id_stock']   ?? [];

        // 2) Ambil data outCelup & stock lama
        $dataOutCelup = [];
        foreach ($idOutCelup as $id) {
            if ($d = $this->outCelupModel->find($id)) {
                $dataOutCelup[] = $d;
            }
        }
        $idStockData = [];
        foreach ($idStock as $id) {
            if ($s = $this->stockModel->find($id)) {
                $idStockData[] = $s;
            }
        }
        if (
            count($dataOutCelup) !== count($idOutCelup)
            || count($idStockData)  !== count($idStock)
        ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Beberapa data tidak ditemukan di database.'
            ]);
        }

        // -------------------------------------------------------
        // PREPASS: hitung TOTAL semua kgs/cones/krg untuk stock baru
        // supaya ketika kita insert/update stock tujuan (di dalam foreach)
        // kita dapat menggunakan total akhir (bukan partial dari iterasi pertama).
        // -------------------------------------------------------
        $totalKgs = $totalCns = $totalKrgOut = $totalKrgIn = 0;
        foreach ($dataOutCelup as $i => $d) {
            $other = $this->otherOutModel->getQty($d['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($d['id_out_celup']);
            $pindahOrder = $this->historyStock->getKgsPindahOrder($d['id_out_celup']);

            $kgs = round($d['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0) - ($pindahOrder['kgs_pindah_order'] ?? 0), 2);
            $cns = $d['cones_kirim'] - ($other['cns_other_out'] ?? 0) - ($outByCns['cns_out'] ?? 0) - ($pindahOrder['cns_pindah_order'] ?? 0);
            $kgsOutByCns = $reqData['kgs_out'][$d['id_out_celup']] ?? 0;
            $cnsOutByCns = $reqData['cns_out'][$d['id_out_celup']] ?? 0;

            $kgsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $kgsOutByCns : $kgs;
            $cnsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $cnsOutByCns : $cns;
            $krgKirimOut = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? 0 : 1;
            $krgKirimIn = 1;

            $totalKgs += $kgsKirim;
            $totalCns += $cnsKirim;
            $totalKrgOut += $krgKirimOut;
            $totalKrgIn += $krgKirimIn;
        }

        // -------------------------------------------------------
        // MAIN LOOP: lakukan operasi per out_celup (history, update stok lama, pemasukan)
        // tapi untuk insert/update stock tujuan: gunakan guard supaya hanya sekali per tujuan.
        // -------------------------------------------------------
        $idOutCelupBaru  = [];
        $idPemasukanBaru = [];
        $detailKirim     = [];
        $processedDest   = []; // guard untuk mencegah insert/update stock tujuan berulang
        $newStockIdMap   = []; // menyimpan id_stock baru per tujuan key

        foreach ($dataOutCelup as $i => $d) {
            $other = $this->otherOutModel->getQty($d['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($d['id_out_celup']);
            $pindahOrder = $this->historyStock->getKgsPindahOrder($d['id_out_celup']);

            $kgs = round($d['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0) - ($pindahOrder['kgs_pindah_order'] ?? 0), 2);
            $cns = $d['cones_kirim'] - ($other['cns_other_out'] ?? 0) - ($outByCns['cns_out'] ?? 0) - ($pindahOrder['cns_pindah_order'] ?? 0);
            $kgsOutByCns = $reqData['kgs_out'][$d['id_out_celup']] ?? 0;
            $cnsOutByCns = $reqData['cns_out'][$d['id_out_celup']] ?? 0;

            $kgsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $kgsOutByCns : $kgs;
            $cnsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $cnsOutByCns : $cns;
            $krgKirimOut = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? 0 : 1;
            $krgKirimIn = 1;

            // simpan hasil per id_out_celup (bila perlu nanti)
            $detailKirim[$i] = [
                'kgs' => $kgsKirim,
                'cns' => $cnsKirim,
                'krgOut' => $krgKirimOut,
                'krgIn' => $krgKirimIn,
            ];

            $cluster  = $idStockData[$i]['nama_cluster'];
            $lotStock = $idStockData[$i]['lot_stock'];
            $oldIdOC  = $d['id_out_celup'];

            // — baru out_celup (sama seperti sebelumnya)
            $idRetur = $this->outCelupModel
                ->select('id_retur')
                ->where('id_out_celup', $oldIdOC)
                ->first();
            if ($idRetur) {
                $this->outCelupModel->insert([
                    'id_retur'    => $idRetur['id_retur'] ?? null,
                    'id_bon'      => $d['id_bon'] ?? null,
                    'id_celup'    => $d['id_celup'] ?? null,
                    'no_model'    => $noModel,
                    'no_karung'   => $d['no_karung'],
                    'kgs_kirim'   => $kgsKirim,
                    'cones_kirim' => $cnsKirim,
                    'lot_kirim'   => $lotStock,
                    'ganti_retur' => '0',
                    'admin'       => session()->get('username'),
                ]);
                $idOutCelupBaru[] = $this->outCelupModel->getInsertID();
            }

            // — update stok LAMA (kurangi sesuai tiap item, pakai nilai per-item)
            $newKgs = max(0, $idStockData[$i]['kgs_in_out'] - $totalKgs);
            $newCns = max(0, $idStockData[$i]['cns_in_out'] - $totalCns);
            $newKrg = max(0, $idStockData[$i]['krg_in_out'] - $totalKrgOut);
            $this->stockModel->update($idStock[$i], [
                'kgs_in_out'   => $newKgs,
                'cns_in_out'   => $newCns,
                'krg_in_out'   => $newKrg,
                'lot_stock'    => $lotStock,
                'lot_awal'     => $idStockData[$i]['lot_awal'] ?? $idStockData[$i]['lot_stock'],
                'nama_cluster' => $cluster,
            ]);

            // --- Buat key unik untuk tujuan stock supaya insert/update tujuan hanya sekali
            $destKey = implode('|', [$noModel, $itemType, $kodeWarna, $warna, $cluster, $lotStock]);

            if (!isset($processedDest[$destKey])) {
                // cek stock tujuan (gunakan total akhir yang sudah dihitung)
                $existingStock = $this->stockModel
                    ->where('no_model',   $noModel)
                    ->where('item_type',  $itemType)
                    ->where('kode_warna', $kodeWarna)
                    ->where('warna',      $warna)
                    ->where('nama_cluster', $cluster)
                    ->where('lot_awal', $lotStock)
                    ->first();

                if ($existingStock) {
                    // — jika sudah ada, update stok tujuan (tambah total dari semua pemindahan)
                    $this->stockModel->update($existingStock['id_stock'], [
                        'kgs_stock_awal' => $existingStock['kgs_stock_awal'] + $totalKgs,
                        'cns_stock_awal' => $existingStock['cns_stock_awal'] + $totalCns,
                        'krg_stock_awal' => $existingStock['krg_stock_awal'] + $totalKrgIn,
                        'lot_awal'       => $lotStock,
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
                    $newStockId = $existingStock['id_stock'];
                } else {
                    // — jika belum ada, insert stock tujuan (pakai total akhir)
                    $this->stockModel->insert([
                        'no_model'       => $noModel,
                        'item_type'      => $itemType,
                        'kode_warna'     => $kodeWarna,
                        'warna'          => $warna,
                        'kgs_stock_awal' => $totalKgs,
                        'cns_stock_awal' => $totalCns,
                        'krg_stock_awal' => $totalKrgIn,
                        'lot_awal'       => $lotStock,
                        'nama_cluster'   => $cluster,
                        'admin'          => session()->get('username'),
                        'created_at'     => date('Y-m-d H:i:s'),
                    ]);
                    $newStockId = $this->stockModel->getInsertID();
                }

                // simpan mapping & tandai processed supaya tidak jalan lagi untuk tujuan ini
                $newStockIdMap[$destKey] = $newStockId;
                $processedDest[$destKey] = true;
            } else {
                // sudah ada mapping dari iterasi sebelumnya
                $newStockId = $newStockIdMap[$destKey];
            }

            // --- insert data history (per out_celup) menggunakan newStockId yang sudah valid
            $this->historyStock->insert([
                'id_stock_old'  => $idStockData[0]['id_stock'],
                'id_stock_new'  => $newStockId,
                'id_out_celup'  => $oldIdOC,
                'cluster_old'   => $idStockData[0]['nama_cluster'],
                'cluster_new'   => $cluster,
                'kgs'           => $kgsKirim,
                'cns'           => $cnsKirim,
                'krg'           => $krgKirimOut,
                'lot'           => $idStockData[0]['lot_stock'],
                'keterangan'    => "Pindah Order",
                'admin'         => session()->get('username'),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ]);

            // --- sekarang ambil history untuk out_celup lama (termasuk yg baru saja diinsert)
            $dataHistory = $this->historyStock
                ->where('id_out_celup', $oldIdOC)
                ->findAll();

            $totalKgsHistory = array_sum(array_column($dataHistory, 'kgs'));

            log_message('debug', 'Data History: ' . print_r($dataHistory, true));
            log_message('debug', 'Total Kgs History: ' . $totalKgsHistory);

            $dataOutCelupLama = $this->outCelupModel->find($oldIdOC);
            $kgsKirim = $dataOutCelupLama['kgs_kirim'];
            if ($totalKgsHistory >= $kgsKirim) {
                // — tandai out_jalur pada pemasukan lama
                $this->pemasukanModel
                    ->set('out_jalur', '1')
                    ->where('id_out_celup', $oldIdOC)
                    ->update();
            } else {
                $this->pemasukanModel
                    ->set('out_jalur', '0')
                    ->where('id_out_celup', $oldIdOC)
                    ->update();
            }

            // — insert pemasukan baru (tanpa id_stock dulu)
            $this->pemasukanModel->insert([
                'id_out_celup' => $idOutCelupBaru[$i] ?? null,
                'tgl_masuk'    => date('Y-m-d'),
                'nama_cluster' => $cluster,
                'out_jalur'    => '0',
                'admin'        => session()->get('username'),
            ]);
            $idPemasukanBaru[] = $this->pemasukanModel->getInsertID();
        } // end foreach

        // update id_stock pada pemasukan baru (pakai mapping newStockIdMap)
        // jika hanya satu tujuan, ambil newStockId dari mapping; jika banyak tujuan, kita pakai destKey dari tiap pemasukan
        // di implementasi ini, semua pemasukan dibuat pada iterasi yg sama urutannya, dan setiap pemasukan terkait idOutCelupBaru indexnya.
        // Kita akan pasangkan setiap pemasukan dengan newStockId sesuai destinasi yang sama (pakai last $newStockId jika hanya 1 tujuan).
        // Simpel: jika hanya satu tujuan, gunakan newStockId terakhir; kalau banyak tujuan, gunakan mapping via destKey reconstruct (but user biasanya 1 tujuan).
        // Untuk safety, ambil last inserted newStockId jika exists.
        $lastNewStockId = end($newStockIdMap) ?: ($newStockId ?? null);

        foreach ($idPemasukanBaru as $pid) {
            $this->pemasukanModel->update($pid, ['id_stock' => $lastNewStockId]);
        }

        // selesai (asumsi transaksi di luar / tidak diubah)
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data berhasil dipindahkan.'
        ]);
    }

    // public function savePindahOrderTest()
    // {
    //     $reqData = $this->request->getPost();

    //     // 1) Validasi model tujuan
    //     if (empty($reqData['no_model_tujuan']) || count(explode('|', $reqData['no_model_tujuan'])) < 4) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Data model tujuan tidak lengkap atau tidak valid.'
    //         ]);
    //     }
    //     [$noModel, $itemType, $kodeWarna, $warna] = explode('|', $reqData['no_model_tujuan']);

    //     $idOutCelup = $reqData['idOutCelup'] ?? [];
    //     $idStock    = $reqData['id_stock']   ?? [];

    //     // 2) Ambil data outCelup & stock lama
    //     $dataOutCelup = [];
    //     foreach ($idOutCelup as $id) {
    //         if ($d = $this->outCelupModel->find($id)) {
    //             $dataOutCelup[] = $d;
    //         }
    //     }
    //     $idStockData = [];
    //     foreach ($idStock as $id) {
    //         if ($s = $this->stockModel->find($id)) {
    //             $idStockData[] = $s;
    //         }
    //     }
    //     if (
    //         count($dataOutCelup) !== count($idOutCelup)
    //         || count($idStockData)  !== count($idStock)
    //     ) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Beberapa data tidak ditemukan di database.'
    //         ]);
    //     }

    //     // 3) Hitung TOTAL semua kgs/cones/krg untuk stock baru
    //     $totalKgs = $totalCns = $totalKrgOut = $totalKrgIn = 0;

    //     $idOutCelupBaru  = [];
    //     $idPemasukanBaru = [];

    //     foreach ($dataOutCelup as $i => $d) {
    //         $other = $this->otherOutModel->getQty($d['id_out_celup']);
    //         $outByCns = $this->pengeluaranModel->getQtyOutByCns($d['id_out_celup']);
    //         $pindahOrder = $this->historyStock->getKgsPindahOrder($d['id_out_celup']);

    //         $kgs = round($d['kgs_kirim'] - ($other['kgs_other_out'] ?? 0) - ($outByCns['kgs_out'] ?? 0) - ($pindahOrder['kgs_pindah_order'] ?? 0), 2);
    //         $cns = $d['cones_kirim'] - ($other['cns_other_out'] ?? 0) - ($outByCns['cns_out'] ?? 0) - ($pindahOrder['cns_pindah_order'] ?? 0);
    //         $kgsOutByCns = $reqData['kgs_out'][$d['id_out_celup']] ?? 0;
    //         $cnsOutByCns = $reqData['cns_out'][$d['id_out_celup']] ?? 0;

    //         $kgsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $kgsOutByCns : $kgs;
    //         $cnsKirim = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? $cnsOutByCns : $cns;
    //         $krgKirimOut = (!empty($kgsOutByCns) && $kgsOutByCns > 0) ? 0 : 1; // untuk pengurangan stock pdk lama
    //         $krgKirimIn = 1; // untuk insert stock di pdk baru

    //         // simpan hasil per id_out_celup
    //         $detailKirim[$i] = [
    //             'kgs' => $kgsKirim,
    //             'cns' => $cnsKirim,
    //             'krgOut' => $krgKirimOut,
    //             'krgIn' => $krgKirimIn,
    //         ];

    //         $totalKgs += $kgsKirim;
    //         $totalCns += $cnsKirim;
    //         $totalKrgOut += $krgKirimOut; // untuk pengurangan stock pdk lama
    //         $totalKrgIn += $krgKirimIn; // Asumsikan setiap pemasukan hanya 1 kali
    //         $cluster  = $idStockData[$i]['nama_cluster'];
    //         $lotStock = $idStockData[$i]['lot_stock'];
    //         $oldIdOC  = $d['id_out_celup'];

    //         // Ambil hasil hitungan yang sudah disesuaikan
    //         $kgsKirim = $detailKirim[$i]['kgs'];
    //         $cnsKirim = $detailKirim[$i]['cns'];
    //         $krgOut = $detailKirim[$i]['krgOut'];
    //         $krgIn = $detailKirim[$i]['krgIn'];

    //         // — baru out_celup
    //         $idRetur = $this->outCelupModel
    //             ->select('id_retur')
    //             ->where('id_out_celup', $oldIdOC)
    //             ->first();
    //         if ($idRetur) {
    //             $this->outCelupModel->insert([
    //                 'id_retur'    => $idRetur['id_retur'] ?? null,
    //                 'id_bon'      => $d['id_bon'] ?? null,
    //                 'id_celup'    => $d['id_celup'] ?? null,
    //                 'no_model'    => $noModel,
    //                 'no_karung'   => $d['no_karung'],
    //                 'kgs_kirim'   => $kgsKirim,
    //                 'cones_kirim' => $cnsKirim,
    //                 'lot_kirim'   => $lotStock,
    //                 'ganti_retur' => '0',
    //                 'admin'       => session()->get('username'),
    //             ]);
    //             $idOutCelupBaru[] = $this->outCelupModel->getInsertID();
    //         }

    //         // — update stok LAMA (kurangi sesuai tiap item)
    //         $newKgs = max(0, $idStockData[$i]['kgs_in_out']   - $totalKgs);
    //         $newCns = max(0, $idStockData[$i]['cns_in_out']   - $totalCns);
    //         $newKrg = max(0, $idStockData[$i]['krg_in_out']   - $totalKrgOut);
    //         $this->stockModel->update($idStock[$i], [
    //             'kgs_in_out'  => $newKgs,
    //             'cns_in_out'  => $newCns,
    //             'krg_in_out'  => $newKrg,
    //             'lot_stock'   => $lotStock,
    //             'lot_awal'    => $idStockData[$i]['lot_awal'] ?? $idStockData[$i]['lot_stock'],
    //             'nama_cluster' => $cluster,
    //         ]);

    //         // 5) Sekali saja: cek stock ada atau tidak
    //         // Ambil record stock yang matching model, type, warna, cluster dan lot
    //         $existingStock = $this->stockModel
    //             ->where('no_model',   $noModel)
    //             ->where('item_type',  $itemType)
    //             ->where('kode_warna', $kodeWarna)
    //             ->where('warna',      $warna)
    //             ->where('nama_cluster', $cluster)
    //             ->where('lot_awal', $lotStock)
    //             ->first();

    //         if ($existingStock) {
    //             // — jika sudah ada, update stok lama (tambah total dari pemindahan)
    //             $this->stockModel->update($existingStock['id_stock'], [
    //                 'kgs_stock_awal' => $existingStock['kgs_stock_awal'] + $totalKgs,
    //                 'cns_stock_awal' => $existingStock['cns_stock_awal'] + $totalCns,
    //                 'krg_stock_awal' => $existingStock['krg_stock_awal'] + $totalKrgIn,
    //                 'lot_awal'       => $lotStock,          // perbarui lot terakhir
    //                 'updated_at'     => date('Y-m-d H:i:s'),
    //             ]);
    //             $newStockId = $existingStock['id_stock'];
    //         } else {
    //             // — jika belum ada, insert stock baru
    //             $this->stockModel->insert([
    //                 'no_model'       => $noModel,
    //                 'item_type'      => $itemType,
    //                 'kode_warna'     => $kodeWarna,
    //                 'warna'          => $warna,
    //                 'kgs_stock_awal' => $totalKgs,
    //                 'cns_stock_awal' => $totalCns,
    //                 'krg_stock_awal' => $totalKrgIn,
    //                 'lot_awal'       => $lotStock,
    //                 'nama_cluster'   => $cluster,
    //                 'admin'          => session()->get('username'),
    //                 'created_at'     => date('Y-m-d H:i:s'),
    //             ]);
    //             $newStockId = $this->stockModel->getInsertID();
    //         }

    //         // insert data history

    //         $this->historyStock->insert([
    //             'id_stock_old'  => $idStockData[0]['id_stock'], // ID stok lama
    //             'id_stock_new'  => $newStockId, // ID stok baru
    //             'id_out_celup'  => $oldIdOC, // ID Oc
    //             'cluster_old'   => $idStockData[0]['nama_cluster'], // Cluster lama
    //             'cluster_new'   => $cluster, // Cluster baru
    //             'kgs'           => $kgsKirim, // Total kgs
    //             'cns'           => $cnsKirim, // Total cns
    //             'krg'           => $krgKirimOut, // Total krg
    //             'lot'           => $idStockData[0]['lot_stock'], // Lot stok lama
    //             'keterangan'    => "Pindah Order", // Keterangan pemindahan
    //             'admin'          => session()->get('username'), // Admin yang melakukan
    //             'created_at'    => date('Y-m-d H:i:s'), // Waktu pemindahan
    //             'updated_at'    => null, // Kolom updated_at bisa null karena belum ada perubahan
    //         ]);

    //         $dataHistory = $this->historyStock
    //             ->where('id_out_celup', $oldIdOC)
    //             ->findAll();

    //         $totalKgsHistory = array_sum(array_column($dataHistory, 'kgs'));

    //         // log_message('debug', 'Data History: ' . print_r($dataHistory, true));
    //         // log_message('debug', 'Total Kgs History: ' . $totalKgsHistory);

    //         $dataOutCelupLama = $this->outCelupModel->find($oldIdOC);
    //         $kgsKirim = $dataOutCelupLama['kgs_kirim'];
    //         if ($totalKgsHistory >= $kgsKirim) {
    //             // — tandai out_jalur pada pemasukan lama
    //             $this->pemasukanModel
    //                 ->set('out_jalur', '1')
    //                 ->where('id_out_celup', $oldIdOC)
    //                 ->update();

    //             // — insert pemasukan baru (tanpa id_stock dulu)
    //             $this->pemasukanModel->insert([
    //                 'id_out_celup' => $idOutCelupBaru[$i],
    //                 'tgl_masuk'    => date('Y-m-d'),
    //                 'nama_cluster' => $cluster,
    //                 'out_jalur'    => '0',
    //                 'admin'        => session()->get('username'),
    //             ]);
    //             $idPemasukanBaru[] = $this->pemasukanModel->getInsertID();
    //         } else {
    //             // — tandai out_jalur pada pemasukan lama
    //             $this->pemasukanModel
    //                 ->set('out_jalur', '0')
    //                 ->where('id_out_celup', $oldIdOC)
    //                 ->update();

    //             // — insert pemasukan baru (tanpa id_stock dulu)
    //             $this->pemasukanModel->insert([
    //                 'id_out_celup' => $idOutCelupBaru[$i],
    //                 'tgl_masuk'    => date('Y-m-d'),
    //                 'nama_cluster' => $cluster,
    //                 'out_jalur'    => '0',
    //                 'admin'        => session()->get('username'),
    //             ]);
    //             $idPemasukanBaru[] = $this->pemasukanModel->getInsertID();
    //         }
    //     }

    //     // log_message('info', "Total Kgs: $totalKgs, data pindah : " . json_encode($pindahOrder) . ", data kgs: " . json_encode($kgs));
    //     $db = \Config\Database::connect();
    //     $db->transStart();


    //     log_message('info', 'Total karung: ' . $totalKrgOut . ', detail: ' . json_encode($detailKirim[$i]));
    //     log_message('info', 'Total Kgs: ' . $totalKgs);
    //     log_message('info', 'Total Cns: ' . $totalCns);

    //     foreach ($idPemasukanBaru as $pid) {
    //         $this->pemasukanModel->update($pid, ['id_stock' => $newStockId]);
    //     }

    //     $db->transComplete();

    //     if ($db->transStatus() === false) {
    //         return $this->response->setJSON([
    //             'success' => false,
    //             'message' => 'Gagal memproses pemindahan order. Transaksi dibatalkan.'
    //         ]);
    //     }

    //     return $this->response->setJSON([
    //         'success' => true,
    //         'message' => 'Data berhasil dipindahkan.'
    //     ]);
    // }

    public function getPindahOrderTest()
    {
        $idStock = $this->request->getPost('id_stock');
        $data = $this->stockModel->getStockInPemasukanById($idStock);
        // dd($data);
        $dataArray = json_decode(json_encode($data), true);
        foreach ($dataArray as &$id) {
            $other = $this->otherOutModel->getQty($id['id_out_celup']);
            $outByCns = $this->pengeluaranModel->getQtyOutByCns($id['id_out_celup']);
            $pindahOrder = $this->historyStock->getKgsPindahOrder($id['id_out_celup']);

            $kgsOther = !empty($other) ? (float) $other['kgs_other_out'] : 0;
            $kgsOutByCns = !empty($outByCns) ? (float) $outByCns['kgs_out'] : 0;
            $kgsPindahOrder = !empty($pindahOrder) ? (float) $pindahOrder['kgs_pindah_order'] : 0;
            $cnsOther = !empty($other) ? (int) $other['cns_other_out'] : 0;
            $cnsOutByCns = !empty($outByCns) ? (int) $outByCns['cns_out'] : 0;
            $cnsPindahOrder = !empty($pindahOrder) ? (int) $pindahOrder['cns_pindah_order'] : 0;

            // kurangi other out & pengeluaran by cones
            $id['kgs_kirim'] = round((float) $id['kgs_kirim'] - $kgsPindahOrder - $kgsOther - $kgsOutByCns, 2);
            $id['cones_kirim'] = (int) $id['cones_kirim'] - $cnsOther - $cnsOutByCns - $cnsPindahOrder;
        }
        // var_dump($dataArray);
        // log_message('debug', 'Data Stock: ' . print_r($dataArray, true));
        // log_message('debug', 'pindah order: ' . print_r($kgsPindahOrder, true));
        if (empty($dataArray)) {
            return $this->response->setJSON(['error' => false, 'message' => 'Data tidak ditemukan']);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'data' => $dataArray
            ]);
        }

        return $this->response->setJSON($dataArray);
    }
}
