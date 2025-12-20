<?php

namespace App\Controllers;

use App\Database\Seeds\MasterRangePemesanan as SeedsMasterRangePemesanan;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;
use App\Models\ClusterModel;
use App\Models\PemasukanModel;
use App\Models\PemesananModel;
use App\Models\TotalPemesananModel;
use App\Models\StockModel;
use App\Models\HistoryPindahPalet;
use App\Models\HistoryPindahOrder;
use App\Models\HistoryStock;
use App\Models\PengeluaranModel;
use App\Models\ReturModel;
use App\Models\KategoriReturModel;
use App\Models\PoTambahanModel;
use App\Models\TrackingPoCovering;
use App\Models\KebutuhanCones;
use App\Models\MasterRangePemesanan;
use App\Models\OtherOutModel;
use App\Models\TotalPoTambahanModel;
use Dompdf\Dompdf;
use PHPUnit\Framework\Attributes\IgnoreFunctionForCodeCoverage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


class ApiController extends ResourceController
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
    protected $clusterModel;
    protected $pemasukanModel;
    protected $stockModel;
    protected $pemesananModel;
    protected $totalPemesananModel;
    protected $historyPindahPalet;
    protected $historyPindahOrder;
    protected $historyStock;
    protected $pengeluaranModel;
    protected $returModel;
    protected $kategoriReturModel;
    protected $poTambahanModel;
    protected $trackingPoCovering;
    protected $kebutuhanCones;
    protected $masterRangePemesanan;
    protected $totalPoTambahanModel;
    protected $otherOutModel;

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
        $this->pemesananModel = new PemesananModel();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->historyPindahPalet = new HistoryPindahPalet();
        $this->historyPindahOrder = new HistoryPindahOrder();
        $this->historyStock = new HistoryStock();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->returModel = new ReturModel();
        $this->kategoriReturModel = new KategoriReturModel();
        $this->poTambahanModel = new PoTambahanModel();
        $this->trackingPoCovering = new TrackingPoCovering();
        $this->kebutuhanCones = new KebutuhanCones();
        $this->masterRangePemesanan = new MasterRangePemesanan();
        $this->totalPoTambahanModel = new TotalPoTambahanModel();
        $this->otherOutModel = new OtherOutModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        service('renderer')->setVar('capacityUrl', api_url('capacity'));
    }

    public function index()
    {
        //
    }

    // v3
    public function statusbahanbaku()
    {
        $model = $this->request->getGet('model') ?? null;
        $search = $this->request->getGet('search') ?? null;
        $rows   = $this->materialModel->MaterialPDK($model, $search);

        if (empty($rows)) {
            log_message('error', "MaterialPDK kosong untuk model: $model");
            return $this->respond([], 200);
        }
        // $rows   = $this->openPoModel->MaterialPDK($noModel);
        $res    = [];

        // Field‑field schedule yang ingin di-merge (tidak termasuk 'qty_po'!)
        $fields = [

            'start_mc',
            'kg_celup',
            'lot_urut',
            'lot_celup',
            'tanggal_schedule',
            'tanggal_bon',
            'tanggal_celup',
            'tanggal_bongkar',
            'tanggal_press_oven',
            'tanggal_tl',
            'tanggal_rajut_pagi',
            'tanggal_kelos',
            'serah_terima_acc',
            'tanggal_acc',
            'tanggal_reject',
            'tanggal_matching',
            'tanggal_perbaikan',
            'last_status',
            'ket_daily_cek',
            'po_plus',
            'id_po_gbn',
            'status',
            'keterangan',
            'ket_schedule',
            'admin',
            'created_at',
            'updated_at',
            'kg_stock',
            'total_po_tambahan'
        ];
        // log_message('debug', 'Isi $rows: ' . json_encode($rows));

        foreach ($rows as $row) {
            $jenis = strtoupper($row['jenis']);

            // Simpan qty_po dari PO master
            $masterQty = $row['qty_po'];
            if (in_array($jenis, ['BENANG', 'NYLON'])) {
                $allSchedules = $this->scheduleCelupModel
                    ->schedulePerArea(
                        $row['no_model'],
                        $row['item_type'],
                        $row['kode_warna'],
                        $search
                    );

                if (!empty($allSchedules)) {
                    foreach ($allSchedules as $scheduleData) {
                        // Start dangan data master
                        $newRow = $row;

                        // Pastikan qty_po tetap dari master
                        $newRow['qty_po'] = $masterQty;

                        // Merge data schedule
                        $scheduleData['jenis'] = $row['jenis'];
                        foreach ($fields as $f) {
                            $newRow[$f] = $scheduleData[$f] ?? '';
                        }

                        $res[] = $newRow;
                        // dd($allSchedules);  
                    }
                } else {
                    // Kalau gak ada schedule, tetap munculkan 1 baris dengan qty_po
                    $newRow = $row;
                    $newRow['qty_po'] = $masterQty;
                    foreach ($fields as $f) {

                        $newRow[$f] = '';
                    }

                    $res[] = $newRow;
                }
            } else if (in_array($jenis, ['KARET', 'SPANDEX'])) {
                $allCoverings = $this->trackingPoCovering
                    ->statusBahanBaku(
                        $row['no_model'],
                        $row['item_type'],
                        $row['kode_warna'],
                        $search
                    );
                // dd($allCoverings);
                if (!empty($allCoverings)) {
                    foreach ($allCoverings as $coverData) {
                        $newRow = $row;
                        $newRow['qty_po'] = $masterQty;

                        $coverData['jenis'] = $row['jenis'];
                        foreach ($fields as $f) {
                            $newRow[$f] = $coverData[$f] ?? '';
                        }

                        $res[] = $newRow;
                    }
                } else {
                    $newRow = $row;
                    $newRow['qty_po'] = $masterQty;
                    foreach ($fields as $f) {
                        $newRow[$f] = '';
                    }
                    $res[] = $newRow;
                }
            } else {
                // jenis lain
                $newRow = $row;
                $newRow['qty_po'] = $masterQty;
                foreach ($fields as $f) {
                    $newRow[$f] = '';
                }
                $res[] = $newRow;
            }
        }
        return $this->respond($res, 200);
    }

    public function cekBahanBaku($model)
    {
        $search = '';
        $material = $this->materialModel->MaterialPerOrder($model);
        $res = [];
        foreach ($material as &$row) {
            $schedule = $this->scheduleCelupModel->schedulePerArea($row['no_model'], $row['item_type'], $row['kode_warna'], $search);

            $scheduleData = !empty($schedule) ? $schedule[0] : [];

            $fields = [
                'start_mc',
                'kg_celup',
                'lot_urut',
                'lot_celup',
                'tanggal_schedule',
                'tanggal_bon',
                'tanggal_celup',
                'tanggal_bongkar',
                'tanggal_press',
                'tanggal_oven',
                'tanggal_tl',
                'tanggal_rajut_pagi',
                'tanggal_kelos',
                'tanggal_acc',
                'tanggal_reject',
                'tanggal_perbaikan',
                'last_status',
                'ket_daily_cek',
                'po_plus'
            ];

            foreach ($fields as $field) {
                $row[$field] = $scheduleData[$field] ?? ''; // Isi dengan data jadwal atau kosong jika tidak ada
            }

            $res[] = $row;
        }
        return $this->respond($res, 200);
    }

    public function cekStok($model)
    {
        $material = $this->materialModel->MaterialPerOrder($model);
        $res = [];
        foreach ($material as &$row) {

            $stock = $this->stockModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['stock' => 0];
            $inout = $this->pemasukanModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['masuk' => 0, 'keluar' => 0];
            // dd($inout);
            $row['stock'] = $stock['stock'] ?? 0;
            $row['masuk'] = $inout['masuk'] ?? 0;
            $row['keluar'] = $inout['keluar'];
            $res[] = $row;
        }
        return $this->respond($material, 200);
    }
    public function getMaterialForPemesanan($model, $styleSize, $area)
    {
        $mu = $this->materialModel->getMaterialForPemesanan($model, $styleSize, $area);

        return $this->respond($mu, 200);
    }

    public function getMaterialForPPH($model)
    {
        $material = $this->materialModel->getMaterialForPPH($model);
        if (empty($material)) {
            return $this->failNotFound('Data tidak ditemukan');
        } else {
            return $this->respond($material, 200);
        }
    }

    public function getMaterialForPPHByAreaAndNoModel($area, $noModel)
    {
        $material = $this->materialModel->getMaterialForPPHByNoModel($area, $noModel);

        log_message('info', 'Material: ' . json_encode($material));
        log_message('info', 'Area: ' . $area);
        log_message('info', 'No Model: ' . $noModel);
        if (empty($material)) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        return $this->respond($material, 200);
    }
    public function insertQtyCns()
    {
        // Ambil data dari request
        $data = $this->request->getPost();
        log_message('debug', 'Data received: ' . json_encode($data)); // Logging untuk debugging awal        

        $updateCount = 0; // Inisialisasi variabel untuk menghitung jumlah data yang berhasil diperbarui

        // Validasi data utama
        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Data items tidak ditemukan atau tidak valid',
            ], 400);
        }

        $db = \Config\Database::connect();

        $db->transStart();

        try {
            foreach ($data['items'] as $item) {
                if (!is_array($item)) {
                    log_message('error', 'Invalid item structure: ' . json_encode($item));
                    continue;
                }

                foreach ($item as $row) {
                    // if (empty($row['id_material']) || empty($row['qty_cns']) || empty($row['qty_berat_cns'])) {
                    //     log_message('error', 'Invalid row data: ' . json_encode($row));
                    //     continue;
                    // }

                    // Cek apakah data sudah ada
                    $existingData = $this->kebutuhanCones
                        ->where('id_material', $row['id_material'])
                        ->where('area', $data['area'])
                        ->countAllResults();

                    if ($existingData > 0) {
                        // Update
                        $this->kebutuhanCones
                            ->where('id_material', $row['id_material'])
                            ->where('area', $data['area'])
                            ->set([
                                'qty_cns'       => $row['qty_cns'],
                                'qty_berat_cns' => $row['qty_berat_cns'],
                                'updated_at'    => date('Y-m-d H:i:s')
                            ])
                            ->update();
                        $updateCount++;
                    } else {
                        // Insert
                        $this->kebutuhanCones->insert([
                            'id_material'   => $row['id_material'],
                            'qty_cns'       => $row['qty_cns'],
                            'qty_berat_cns' => $row['qty_berat_cns'],
                            'area'          => $data['area'],
                            'created_at'    => date('Y-m-d H:i:s')
                        ]);
                    }
                    log_message('info', $existingData);
                }
            }

            // Commit transaksi jika semua OK
            if ($db->transStatus() === FALSE) {
                $db->transRollback();
                return $this->respond([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ], 500);
            } else {
                $db->transCommit();
                return $this->respond([
                    'status'  => 'success',
                    'message' => "$updateCount data berhasil diperbarui",
                ], 200);
            }
        } catch (\Exception $e) {
            // Rollback jika ada exception
            $db->transRollback();
            log_message('error', 'Transaction failed: ' . $e->getMessage());
            return $this->respond([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function saveListPemesanan()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);
        log_message('debug', 'Data received: ' . json_encode($data));

        if (empty($data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Tidak ada data list pemesanan",
            ], 400);
        }

        // Validasi awal: pastikan key `id_material` ada dan valid
        if (!isset($data['id_material']) || !is_array($data['id_material'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Data id_material tidak valid atau tidak ditemukan",
            ], 400);
        }

        $length = count($data['id_material']); // Ambil panjang array
        $result = [];
        $lastGroupKey = null;

        for ($i = 0; $i < $length; $i++) {
            // Buat groupKey sesuai data kamu (misalnya no_model + item_type + kode_warna)
            $groupKey = ($data['no_model'][$i] ?? '') . '-' . ($data['item_type'][$i] ?? '') . '-' . ($data['kode_warna'][$i] ?? '');

            // Cek apakah ini baris pertama group
            if ($groupKey !== $lastGroupKey) {
                $sisaKgs        = $data['stock_kg'][$i] ?? 0;
                $sisaCns        = $data['stock_cns'][$i] ?? 0;
                $keterangan     = $data['keterangan'][$i] ?? '';
                $lot            = $data['lot'][$i] ?? '';
                $lastGroupKey   = $groupKey;
            } else {
                $sisaKgs        = 0;
                $sisaCns        = 0;
                $keterangan     = "";
                $lot            = "";
            }

            $resultItem = [
                'id_material'     => $data['id_material'][$i] ?? null,
                'tgl_list'        => date('Y-m-d'),
                'tgl_pakai'       => $data['tgl_pakai'][$i] ?? null,
                'jl_mc'           => $data['jalan_mc'][$i] ?? null,
                'ttl_qty_cones'   => $data['ttl_cns'][$i] ?? null,
                'ttl_berat_cones' => $data['ttl_berat_cns'][$i] ?? null,
                'po_tambahan'     => $data['po_tambahan'][$i] ?? 0,
                'admin'           => $data['area'][$i] ?? null,
                'no_model'        => $data['no_model'][$i] ?? null,
                'style_size'      => $data['style_size'][$i] ?? null,
                'item_type'       => $data['item_type'][$i] ?? null,
                'kode_warna'      => $data['kode_warna'][$i] ?? null,
                'warna'           => $data['warna'][$i] ?? null,
                'keterangan'      => $keterangan,
                'lot'             => $lot,
                'sisa_kgs_mc'     => $sisaKgs,
                'sisa_cones_mc'   => $sisaCns,
                // 'lot'             => $data['lot'][$i] ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
            ];

            // Validasi data untuk setiap elemen
            if (empty($resultItem['id_material']) || empty($resultItem['tgl_pakai']) || empty($resultItem['admin'])) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Data tidak valid pada baris ke-$i",
                    'debug'   => $resultItem,
                ], 400);
            }

            // Cek apakah data dengan kombinasi unik sudah ada di database
            $existingData = $this->pemesananModel
                ->where('id_material', $resultItem['id_material'])
                ->where('tgl_pakai', $resultItem['tgl_pakai'])
                ->where('po_tambahan', $resultItem['po_tambahan'])
                ->where('admin', $resultItem['admin'])
                ->first();

            if ($existingData) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Data pemesanan sudah ada.",
                    'debug'   => $existingData,
                ], 400);
            }

            $result[] = $resultItem;
        }

        log_message('debug', 'Data prepared for batch insert: ' . json_encode($result));

        try {
            // Lakukan insert batch ke database
            $insert = $this->pemesananModel->insertBatch($result);

            if ($insert) {
                // Hapus session `pemesananBb` jika ada
                $session = session();
                if ($session->has('pemesananBb')) {
                    $session->remove('pemesananBb');
                }

                return $this->respond([
                    'status'  => 'success',
                    'message' => count($result) . " data berhasil disimpan",
                ], 200);
            } else {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Tidak ada data yang berhasil disimpan",
                ], 400);
            }
        } catch (\Exception $e) {
            log_message('critical', 'Exception during batch insert: ' . $e->getMessage());
            return $this->respond([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function stockbahanbaku($area)
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        $warna = $this->request->getGet('warna') ?? '';

        $results = $this->stockModel->searchStockArea($area, $noModel, $warna);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);

        return $this->respond($resultsArray, 200);
    }
    public function listPemesanan($area)
    {
        $pdk = $this->request->getGet('searchPdk') ?? '';
        $dataList = $this->pemesananModel->getListPemesananByArea($area, $pdk);

        return $this->respond($dataList, 200);
    }
    public function listReportPemesanan($area, $tgl_pakai = null)
    {

        $dataList = $this->pemesananModel->getListReportPemesananByArea($area, $tgl_pakai);

        return $this->respond($dataList, 200);
    }
    public function getUpdateListPemesanan()
    {
        $role = session()->get('role');
        $data = $this->request->getPost([
            'area',
            'tgl_pakai',
            'no_model',
            'item_type',
            'kode_warna',
            'color',
            'po_tambahan'
        ]);

        $dataList = $this->pemesananModel->getListPemesananByUpdate($data, $role);

        return $this->respond([
            'status'  => 'success',
            'data' => $dataList,
        ], 200);
    }
    public function updateListPemesanan()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);

        // Fungsi untuk parsing key nested menjadi array multidimensi
        function parseNestedKeys($data)
        {
            $result = [];
            foreach ($data as $key => $value) {
                if (preg_match('/^(.*)\[(\d+)\]\[(.*)\]$/', $key, $matches)) {
                    $mainKey = $matches[1]; // "items"
                    $index = $matches[2];  // "0", "1", dll.
                    $subKey = $matches[3]; // "id_material", dll.
                    $result[$mainKey][$index][$subKey] = is_array($value) ? $value[0] : $value;
                } else {
                    $result[$key] = is_array($value) ? $value[0] : $value;
                }
            }
            return $result;
        }

        $data = parseNestedKeys($data); // Parsing data

        log_message('debug', 'Parsed data: ' . json_encode($data, JSON_PRETTY_PRINT));


        // Validasi data
        if (empty($data) || !isset($data['items'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Tidak ada data list pemesanan",
            ], 400);
        }
        // Log data yang diterima
        // log_message('debug', 'Data received: ' . json_encode($data['items']));

        // Looping data 'items'
        foreach ($data['items'] as $index => $item) {
            log_message('debug', "Processing item {$index}: " . json_encode($item));

            // Contoh akses data
            $idMaterial = $item['id_material'];
            $idPemesanan = $item['id_pemesanan'];
            $jalanMc = $item['jalan_mc'];
            $qtyCns = $item['qty_cns'];
            $ttlQtyCns = $item['ttl_qty_cns'];
            $qtyBeratCns = $item['qty_berat_cns'];
            $ttlBeratCns = $item['ttl_berat_cns'];

            // Lakukan operasi sesuai kebutuhan, contoh update data
            $updateMaterial = $this->kebutuhanCones
                ->where('id_material', $idMaterial)
                ->where('area', $data['area']) // tambah filter area
                ->set([
                    'qty_cns'       => $qtyCns,
                    'qty_berat_cns' => $qtyBeratCns,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ])
                ->update();

            if (!$updateMaterial) {
                log_message('error', "Gagal update material untuk id_material: {$idMaterial}");
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Gagal update material untuk id_material: {$idMaterial}",
                ], 400);
            }

            log_message('debug', 'ini' . $index);
            // Kondisi untuk data pertama saja
            if ($index === 0) {
                $pemesananUpdate = [
                    'jl_mc'             => $jalanMc,
                    'ttl_qty_cones'     => $ttlQtyCns,
                    'ttl_berat_cones'   => $ttlBeratCns,
                    'sisa_kgs_mc'       => $data['sisa_kg'],  // Isi hanya untuk data pertama
                    'sisa_cones_mc'     => $data['sisa_cns'], // Isi hanya untuk data pertama
                    'lot'               => $data['lot'],
                    'keterangan'        => $data['keterangan'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            } else {
                $pemesananUpdate = [
                    'jl_mc'             => $jalanMc,
                    'ttl_qty_cones'     => $ttlQtyCns,
                    'ttl_berat_cones'   => $ttlBeratCns,
                    'lot'               => $data['lot'],
                    'keterangan'        => $data['keterangan'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            }

            $updatePemesanan = $this->pemesananModel->update($idPemesanan, $pemesananUpdate);

            if (!$updatePemesanan) {
                log_message('error', "Gagal update pemesanan untuk id_pemesanan: {$idPemesanan}");
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Gagal update pemesanan untuk id_pemesanan: {$idPemesanan}",
                ], 400);
            }
        }

        // Jika semua data berhasil diperbarui
        return $this->respond([
            'status'  => 'success',
            'message' => "Semua data berhasil diperbarui",
        ], 200);
    }
    public function updatePemesananArea()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);

        // Fungsi untuk parsing key nested menjadi array multidimensi
        function parseNestedKeys2($data)
        {
            $result = [];
            foreach ($data as $key => $value) {
                if (preg_match('/^(.*)\[(\d+)\]\[(.*)\]$/', $key, $matches)) {
                    $mainKey = $matches[1]; // "items"
                    $index = $matches[2];  // "0", "1", dll.
                    $subKey = $matches[3]; // "id_material", dll.
                    $result[$mainKey][$index][$subKey] = is_array($value) ? $value[0] : $value;
                } else {
                    $result[$key] = is_array($value) ? $value[0] : $value;
                }
            }
            return $result;
        }

        $data = parseNestedKeys2($data); // Parsing data

        log_message('debug', 'Parsed data: ' . json_encode($data, JSON_PRETTY_PRINT));


        // Validasi data
        if (empty($data) || !isset($data['items'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Tidak ada data list pemesanan",
            ], 400);
        }
        // Log data yang diterima
        // log_message('debug', 'Data received: ' . json_encode($data['items']));

        // Inisialisasi penjumlahan total
        $totalJalanMc = 0;
        $totalTtlQtyCns = 0;
        $totalTtlBeratCns = 0;
        // Looping data 'items'
        foreach ($data['items'] as $index => $item) {
            log_message('debug', "Processing item {$index}: " . json_encode($item));

            // Contoh akses data
            $idMaterial = $item['id_material'];
            $idPemesanan = $item['id_pemesanan'];
            $jalanMc = $item['jalan_mc'];
            $qtyCns = $item['qty_cns'];
            $ttlQtyCns = $item['ttl_qty_cns'];
            $qtyBeratCns = $item['qty_berat_cns'];
            $ttlBeratCns = $item['ttl_berat_cns'];

            // Tambahkan ke total
            $totalJalanMc     += (float) $jalanMc;
            $totalTtlQtyCns   += (float) $ttlQtyCns;
            $totalTtlBeratCns += (float) $ttlBeratCns;

            // Lakukan operasi sesuai kebutuhan, contoh update data
            $updateMaterial = $this->kebutuhanCones
                ->where('id_material', $idMaterial)
                ->where('area', $data['area']) // tambah filter area
                ->set([
                    'qty_cns'       => $qtyCns,
                    'qty_berat_cns' => $qtyBeratCns,
                    'updated_at'    => date('Y-m-d H:i:s'),
                ])
                ->update();

            if (!$updateMaterial) {
                log_message('error', "Gagal update material untuk id_material: {$idMaterial}");
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Gagal update material untuk id_material: {$idMaterial}",
                ], 400);
            }

            log_message('debug', 'ini' . $index);
            // Kondisi untuk data pertama saja
            if ($index === 0) {
                $pemesananUpdate = [
                    'tgl_pakai'         => $data['tgl_pakai'],
                    'jl_mc'             => $jalanMc,
                    'ttl_qty_cones'     => $ttlQtyCns,
                    'ttl_berat_cones'   => $ttlBeratCns,
                    'sisa_kgs_mc'       => $data['sisa_kg'],  // Isi hanya untuk data pertama
                    'sisa_cones_mc'     => $data['sisa_cns'], // Isi hanya untuk data pertama
                    'lot'               => $data['lot'],
                    'keterangan'        => $data['keterangan'],
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            } else {
                $pemesananUpdate = [
                    'tgl_pakai'         => $data['tgl_pakai'],
                    'jl_mc'             => $jalanMc,
                    'ttl_qty_cones'     => $ttlQtyCns,
                    'ttl_berat_cones'   => $ttlBeratCns,
                    'updated_at'        => date('Y-m-d H:i:s'),
                ];
            }

            $updatePemesanan = $this->pemesananModel->update($idPemesanan, $pemesananUpdate);

            if (!$updatePemesanan) {
                log_message('error', "Gagal update pemesanan untuk id_pemesanan: {$idPemesanan}");
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Gagal update pemesanan untuk id_pemesanan: {$idPemesanan}",
                ], 400);
            }
        }
        // Setelah semua item diproses, update total_pemesanan
        $sisaKg  = isset($data['sisa_kg']) ? (float) $data['sisa_kg'] : 0;
        $sisaCns = isset($data['sisa_cns']) ? (float) $data['sisa_cns'] : 0;

        $ttlKgBaru  = $totalTtlBeratCns - $sisaKg;
        $ttlCnsBaru = $totalTtlQtyCns - $sisaCns;

        // Ambil id_total_pemesanan dari item pertama
        $idTotalPemesanan = $data['items'][0]['id_total_pemesanan'] ?? null;

        if ($idTotalPemesanan) {
            $updateTotalPemesanan = [
                'ttl_jl_mc'   => $totalJalanMc,
                'ttl_kg'      => $ttlKgBaru,
                'ttl_cns'     => $ttlCnsBaru,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            $updateResult = $this->totalPemesananModel->update($idTotalPemesanan, $updateTotalPemesanan);

            if (!$updateResult) {
                log_message('error', "Gagal update total_pemesanan untuk id: {$idTotalPemesanan}");
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Gagal update total_pemesanan untuk id: {$idTotalPemesanan}",
                ], 400);
            }
        }


        // Jika semua data berhasil diperbarui
        return $this->respond([
            'status'  => 'success',
            'message' => "Semua data berhasil diperbarui",
        ], 200);
    }
    public function kirimPemesanan()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);
        log_message('debug', 'Data received: ' . json_encode($data));

        $updatePemesanan = $this->pemesananModel->kirimPemesanan($data);

        if ($updatePemesanan['status'] === 'error') {
            log_message('error', $updatePemesanan['message']);
            return $this->respond([
                'status'  => 'error',
                'message' => $updatePemesanan['message'],
            ], 400);
        }

        // Jika semua data berhasil diperbarui
        return $this->respond([
            'status'  => 'success',
            'message' => $updatePemesanan['message'],
            'success_count' => $updatePemesanan['success_count'],
            'failure_count' => $updatePemesanan['failure_count'],
        ], 200);
    }
    public function hapusOldPemesanan()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);

        // Validasi data input
        if (empty($data['tgl_pakai']) || empty($data['area'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tanggal pakai atau area tidak valid.',
            ], 400); // HTTP 400 Bad Request
        }

        // Panggil model untuk menghapus data
        $deletedCount = $this->pemesananModel->deleteListPemesananOtomatis([
            'tgl_pakai' => $data['tgl_pakai'],
            'admin' => $data['area'],
        ]);;
        // log_message('debug', 'Data received: ' . $deletedCount);
        if ($deletedCount) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => "$deletedCount data berhasil dihapus.",
                'data' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => "$deletedCount Tidak ada data yang dihapus.",
            ], 404); // HTTP 404 Not Found
        }
    }
    public function pph()
    {
        $noModel = $this->request->getGet('model') ?? '';

        $results = $this->materialModel->getMaterialForPPH($noModel);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);

        return $this->respond($resultsArray, 200);
    }
    public function getMU()
    {
        $noModel = $this->request->getGet('model') ?? '';
        $size = $this->request->getGet('size') ?? '';

        $results = $this->materialModel->getMU($noModel, $size);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);

        return $this->respond($resultsArray, 200);
    }

    public function requestAdditionalTime($area)
    {
        $jenis = $this->request->getGet('jenis') ?? '';
        $tanggal_pakai = $this->request->getGet('tanggal_pakai') ?? '';
        $alasan = $this->request->getGet('alasan') ?? '';

        $data = [
            'area' => $area,
            'jenis' => $jenis,
            'tanggal_pakai' => $tanggal_pakai,
            'alasan_tambahan_waktu' => $alasan,
        ];

        $update = $this->pemesananModel->reqAdditionalTime($data);

        // Siapkan respons JSON berdasarkan hasil update
        $response = [
            'status' => (bool) $update,
            'message' => $update ? 'Pengajuan tambahan waktu berhasil dikirim.' : 'Update gagal. Periksa tanggal pakai pemesanan',
            'affectedRows' => $update ?: 0, // Jumlah baris yang terpengaruh (default 0 jika gagal)
        ];

        return $this->respond($response, 200);
    }
    public function getStyleSizeByBb()
    {
        $noModel = $this->request->getGet('no_model') ?? '';
        $itemType = $this->request->getGet('item_type') ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';
        $warna = $this->request->getGet('warna') ?? '';

        $data = $this->materialModel->getStyleSizeByBb($noModel, $itemType, $kodeWarna, $warna);

        return $this->respond($data, 200);
    }

    // get data pengiriman
    public function getPengirimanArea()
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        $results = $this->pengeluaranModel->searchPengiriman2($noModel);
        $lotKirim = $this->pengeluaranModel->getLotKirim($noModel);
        // Ubah jadi array
        $resultsArray = json_decode(json_encode($results), true);
        $lotKirimArray = json_decode(json_encode($lotKirim), true);

        // --- Buat index lot kirim per kombinasi unik ---
        $lotGrouped = [];
        foreach ($lotKirimArray as $lot) {
            $key = $lot['no_model'] . '|' . $lot['kode_warna'] . '|' . $lot['item_type'] . '|' . $lot['warna'];
            $lotGrouped[$key][] = $lot['lot_kirim'];
        }

        // --- Gabungkan ke results utama ---
        foreach ($resultsArray as &$res) {
            $key = $res['no_model'] . '|' . $res['kode_warna'] . '|' . $res['item_type'] . '|' . $res['warna'];
            $res['lot_kirim'] = $lotGrouped[$key] ?? [];
        }

        unset($res);

        // Response akhir
        return $this->respond($resultsArray, 200);
    }

    public function getGwBulk()
    {
        $input = $this->request->getJSON(true);
        $result = [];

        foreach ($input as $item) {
            $model = $item['model'];
            $size = $item['size'];

            $gw = $this->materialModel->getGw($model, $size); // fungsi ambil dari DB
            $result[] = [
                'model' => $model,
                'size'  => $size,
                'gw'    => $gw
            ];
        }

        return $this->response->setJSON($result);
    }

    public function getKategoriRetur()
    {
        $kategoriRetur = $this->kategoriReturModel->getKategoriRetur();

        if (empty($kategoriRetur)) {
            return $this->failNotFound('Data tidak ditemukan');
        } else {
            return $this->respond($kategoriRetur, 200);
        }
    }

    public function saveRetur()
    {
        helper(['form']);
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->fail('Data tidak ditemukan', ResponseInterface::HTTP_BAD_REQUEST);
        }

        $result = $this->returModel->insert($data);

        if (!$result) {
            return $this->fail('Gagal menyimpan data', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Data berhasil disimpan',
            'insert_id' => $this->returModel->getInsertID()
        ]);
    }
    public function getTotalPengiriman()
    {
        $area = $this->request->getGet('area') ?? '';
        $no_model = $this->request->getGet('no_model') ?? '';
        $item_type = $this->request->getGet('item_type') ?? '';
        $kode_warna = $this->request->getGet('kode_warna') ?? '';
        $data = [
            'area' => $area,
            'no_model' => $no_model,
            'item_type' => $item_type,
            'kode_warna' => $kode_warna,
        ];

        $totalPengiriman = $this->pengeluaranModel->getTotalPengiriman($data);

        return $this->respond($totalPengiriman, 200);
    }
    public function getTotalRetur()
    {
        $area = $this->request->getGet('area') ?? '';
        $no_model = $this->request->getGet('no_model') ?? '';
        $item_type = $this->request->getGet('item_type') ?? '';
        $kode_warna = $this->request->getGet('kode_warna') ?? '';
        $data = [
            'area' => $area,
            'no_model' => $no_model,
            'item_type' => $item_type,
            'kode_warna' => $kode_warna,
        ];

        $totalRetur = $this->returModel->getTotalRetur($data);

        return $this->respond($totalRetur, 200);
    }
    public function cekStokPerstyle($model, $style)
    {
        $material = $this->materialModel->MaterialPerStyle($model, $style);
        $res = [];
        foreach ($material as &$row) {

            $stock = $this->stockModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['stock' => 0];
            $inout = $this->pemasukanModel->stockInOut($row['no_model'], $row['item_type'], $row['kode_warna']) ?? ['masuk' => 0, 'keluar' => 0];
            $row['stock'] = $stock['stock'] ?? 0;
            $row['masuk'] = $inout['masuk'] ?? 0;
            $row['keluar'] = $inout['keluar'];
            $res[] = $row;
        }
        return $this->respond($res, 200);
    }
    public function poTambahanDetail($noModel, $area)
    {
        $idOrder = $this->masterOrderModel->getIdOrder($noModel);
        $materials = $this->masterOrderModel->getMaterial($idOrder, $area);

        foreach ($materials as $itemType => &$itemData) {
            foreach ($itemData['kode_warna'] as $kodeWarna => &$warnaData) {
                // Ambil kirimArea hanya sekali per kode_warna
                $dataKirim = [
                    'area' => $area,
                    'no_model' => $noModel,
                    'item_type' => $itemType,
                    'kode_warna' => $kodeWarna
                ];

                $kirimArea = $this->pengeluaranModel->getTotalPengiriman($dataKirim);
                $returArea = $this->returModel->getTotalRetur($dataKirim);
                $warnaData['kgs_out'] = ($kirimArea['kgs_out'] ?? 0) - ($returArea['kgs_retur'] ?? 0);
            }
        }

        return response()->setJSON($materials);
    }
    // public function savePoTambahan()
    // {
    //     try {
    //         $req = $this->request->getJSON(true);
    //         if (empty($req['items']) || !is_array($req['items'])) {
    //             return $this->respond(['status' => 'error', 'message' => 'Items tidak ditemukan.'], 400);
    //         }

    //         $sukses = 0;
    //         $gagal  = 0;
    //         $today  = date('Y-m-d');

    //         // ambil sample dari item pertama (karena kombinasi no_model, item_type, kode_warna sama)
    //         $sample = $req['items'][0];

    //         // --- cek apakah total_potambahan sudah ada ---
    //         $exist = $this->poTambahanModel
    //             ->select('po_tambahan.id_total_potambahan')
    //             ->join('material m', 'm.id_material = po_tambahan.id_material')
    //             ->join('master_order mo', 'mo.id_order = m.id_order')
    //             ->where('mo.no_model', $sample['no_model'])
    //             ->where('m.item_type', $sample['item_type'])
    //             ->where('m.kode_warna', $sample['kode_warna'])
    //             ->where('DATE(po_tambahan.created_at)', $today)
    //             ->first();

    //         if ($exist && $exist['id_total_potambahan']) {
    //             // --- update total yang sudah ada ---
    //             $idTotal = $exist['id_total_potambahan'];
    //             // $this->totalPoTambahanModel->update($idTotal, $ttl);
    //             $this->totalPoTambahanModel->update($idTotal, [
    //                 'ttl_terima_kg'    => ($sample['ttl_terima_kg'] ?? 0),
    //                 'ttl_sisa_jatah'   => ($sample['ttl_sisa_jatah'] ?? 0),
    //                 'ttl_sisa_bb_dimc' => $this->totalPoTambahanModel->select('ttl_sisa_bb_dimc')->find($idTotal)['ttl_sisa_bb_dimc'] + ($sample['ttl_sisa_bb_dimc'] ?? 0),
    //                 'ttl_tambahan_kg'  => $this->totalPoTambahanModel->select('ttl_tambahan_kg')->find($idTotal)['ttl_tambahan_kg'] + ($sample['ttl_tambahan_kg'] ?? 0),
    //                 'ttl_tambahan_cns' => $this->totalPoTambahanModel->select('ttl_tambahan_cns')->find($idTotal)['ttl_tambahan_cns'] + ($sample['ttl_tambahan_cns'] ?? 0),
    //             ]);
    //         } else {
    //             // --- insert total baru ---
    //             // --- Insert total_potambahan baru ---
    //             $dataTotal = [
    //                 'ttl_terima_kg'    => $sample['ttl_terima_kg'] ?? 0,
    //                 'ttl_sisa_jatah'   => $sample['ttl_sisa_jatah'] ?? 0,
    //                 'ttl_sisa_bb_dimc' => $sample['ttl_sisa_bb_dimc'] ?? 0,
    //                 'ttl_tambahan_kg'  => $sample['ttl_tambahan_kg'] ?? 0,
    //                 'ttl_tambahan_cns' => $sample['ttl_tambahan_cns'] ?? 0,
    //                 'loss_aktual'      => $sample['loss_aktual'] ?? 0,
    //                 'loss_tambahan'    => $sample['loss_tambahan'] ?? 0,
    //                 'keterangan'       => $sample['keterangan'] ?? '',
    //                 'created_at'       => date('Y-m-d H:i:s'),
    //             ];

    //             $this->totalPoTambahanModel->insert($dataTotal);
    //             $idTotal = $this->totalPoTambahanModel->getInsertID();
    //         }

    //         // --- loop kedua: insert detail ---
    //         foreach ($req['items'] as $item) {
    //             $idMat = $this->materialModel->getIdMaterial([
    //                 'no_model'   => $item['no_model'],
    //                 // 'area'       => $item['area'],
    //                 'item_type'  => $item['item_type'],
    //                 'kode_warna' => $item['kode_warna'],
    //                 'style_size' => $item['style_size'],
    //             ]);

    //             if (empty($idMat)) {
    //                 $gagal++;
    //                 continue;
    //             }

    //             $dataDetail = [
    //                 'id_material'         => $idMat,
    //                 'id_total_potambahan' => $idTotal,
    //                 'sisa_order_pcs'      => $item['sisa_order_pcs'] ?? 0,
    //                 'bs_mesin_kg'         => $item['bs_mesin_kg'] ?? 0,
    //                 'bs_st_pcs'           => $item['bs_st_pcs'] ?? 0,
    //                 'poplus_mc_kg'        => $item['poplus_mc_kg'] ?? 0,
    //                 'poplus_mc_cns'       => $item['poplus_mc_cns'] ?? 0,
    //                 'plus_pck_pcs'        => $item['plus_pck_pcs'] ?? 0,
    //                 'plus_pck_kg'         => $item['plus_pck_kg'] ?? 0,
    //                 'plus_pck_cns'        => $item['plus_pck_cns'] ?? 0,
    //                 // 'lebih_pakai_kg'      => $item['lebih_pakai_kg'] ?? 0,
    //                 'delivery_po_plus'    => $item['delivery_po_plus'] ?? '',
    //                 'admin'               => $item['admin'] ?? session()->get('username'),
    //                 'created_at'          => date('Y-m-d H:i:s'),
    //             ];

    //             if ($this->poTambahanModel->insert($dataDetail)) {
    //                 $sukses++;
    //             } else {
    //                 $gagal++;
    //             }
    //         }

    //         return $this->respond([
    //             'status'  => 'success',
    //             'sukses'  => $sukses,
    //             'gagal'   => $gagal,
    //             'message' => "Sukses insert: $sukses, Gagal insert: $gagal",
    //         ]);
    //     } catch (\Exception $e) {
    //         log_message('error', 'Error in savePoTambahan: ' . $e);
    //         return $this->respond([
    //             'status'  => 'failed',
    //             'sukses'  => 0,
    //             'gagal'   => 1,
    //             'message' => "Gagal insert: " . $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function savePoTambahan()
    {
        try {
            $req = $this->request->getJSON(true);
            if (empty($req['items']) || !is_array($req['items'])) {
                return $this->respond(['status' => 'error', 'message' => 'Items tidak ditemukan.'], 400);
            }

            $sukses = 0;
            $gagal  = 0;
            $today  = date('Y-m-d');

            $sample = $req['items'][0];

            // ==========================================================
            // 1. CEK SEMUA id_material DI ITEM APAKAH SUDAH ADA DI po_tambahan
            // ==========================================================
            foreach ($req['items'] as $item) {

                $idMat = $this->materialModel->getIdMaterial([
                    'no_model'   => $item['no_model'],
                    'item_type'  => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'style_size' => $item['style_size'],
                ]);

                if (!$idMat) continue;

                $existPo = $this->poTambahanModel
                    ->where('id_material', $idMat)
                    ->where('admin', $item['admin'])
                    ->where('DATE(created_at)', $today)
                    ->first();

                if ($existPo) {
                    return $this->respond([
                        'status'  => 'error',
                        'message' => 'Data untuk material ini sudah pernah diinput hari ini. Penyimpanan dibatalkan.'
                    ], 400);
                }
            }

            // ==========================================================
            // 2. INSERT total_potambahan KARENA BELUM ADA DATA HARI INI
            // ==========================================================
            $dataTotal = [
                'ttl_terima_kg'    => $sample['ttl_terima_kg'] ?? 0,
                'ttl_sisa_jatah'   => $sample['ttl_sisa_jatah'] ?? 0,
                'ttl_sisa_bb_dimc' => $sample['ttl_sisa_bb_dimc'] ?? 0,
                'ttl_tambahan_kg'  => $sample['ttl_tambahan_kg'] ?? 0,
                'ttl_tambahan_cns' => $sample['ttl_tambahan_cns'] ?? 0,
                'loss_aktual'      => $sample['loss_aktual'] ?? 0,
                'loss_tambahan'    => $sample['loss_tambahan'] ?? 0,
                'keterangan'       => $sample['keterangan'] ?? '',
                'created_at'       => date('Y-m-d H:i:s'),
            ];

            $this->totalPoTambahanModel->insert($dataTotal);
            $idTotal = $this->totalPoTambahanModel->getInsertID();

            // ==========================================================
            // 3. INSERT DETAIL KE po_tambahan
            // ==========================================================
            foreach ($req['items'] as $item) {

                $idMat = $this->materialModel->getIdMaterial([
                    'no_model'   => $item['no_model'],
                    'item_type'  => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'style_size' => $item['style_size'],
                ]);

                if (empty($idMat)) {
                    $gagal++;
                    continue;
                }

                $dataDetail = [
                    'id_material'         => $idMat,
                    'id_total_potambahan' => $idTotal,
                    'sisa_order_pcs'      => $item['sisa_order_pcs'] ?? 0,
                    'bs_mesin_kg'         => $item['bs_mesin_kg'] ?? 0,
                    'bs_st_pcs'           => $item['bs_st_pcs'] ?? 0,
                    'poplus_mc_kg'        => $item['poplus_mc_kg'] ?? 0,
                    'poplus_mc_cns'       => $item['poplus_mc_cns'] ?? 0,
                    'plus_pck_pcs'        => $item['plus_pck_pcs'] ?? 0,
                    'plus_pck_kg'         => $item['plus_pck_kg'] ?? 0,
                    'plus_pck_cns'        => $item['plus_pck_cns'] ?? 0,
                    'delivery_po_plus'    => $item['delivery_po_plus'] ?? '',
                    'admin'               => $item['admin'] ?? session()->get('username'),
                    'created_at'          => date('Y-m-d H:i:s'),
                ];

                if ($this->poTambahanModel->insert($dataDetail)) {
                    $sukses++;
                } else {
                    $gagal++;
                }
            }

            return $this->respond([
                'status'  => 'success',
                'sukses'  => $sukses,
                'gagal'   => $gagal,
                'message' => "Sukses insert: $sukses, Gagal insert: $gagal",
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in savePoTambahan: ' . $e);
            return $this->respond([
                'status'  => 'failed',
                'sukses'  => 0,
                'gagal'   => 1,
                'message' => "Gagal insert: " . $e->getMessage(),
            ], 500);
        }
    }
    public function filterPoTambahan()
    {
        $area = $this->request->getGet('area');
        $tglBuat = $this->request->getGet('tglBuat');
        $noModel = $this->request->getGet('model') ?? null;

        $data = [];
        $filterData = $this->poTambahanModel->filterData($area, $tglBuat, $noModel);

        // Ambil kolom-kolom unik dari hasil filterData
        $noModels    = array_unique(array_column($filterData, 'no_model'));
        $itemTypes   = array_unique(array_column($filterData, 'item_type'));
        $kodeWarnas  = array_unique(array_column($filterData, 'kode_warna'));
        $colors      = array_unique(array_column($filterData, 'color'));

        $validate = [
            'area'       => $area,
            'no_model'   => $noModels,
            'item_type'  => $itemTypes,
            'kode_warna' => $kodeWarnas,
            'color'      => $colors,
        ];

        $listRetur = $this->returModel->getQtyRetur($validate);

        $data = [
            'dataPoTambahan' => $filterData,
            'dataRetur' => $listRetur,
        ];

        return $this->respond($data);
    }
    public function cekMaterial($id)
    {
        $material = $this->materialModel->materialCek($id);
        return $this->response->setJSON($material);
    }
    public function listRetur($area)
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        $tglBuat = $this->request->getGet('tglBuat') ?? '';

        $listRetur = [];
        $material = [];
        $kirim = [];
        $poPlus = [];
        $listRetur = $this->returModel->getListRetur($area, $noModel, $tglBuat);

        $noModels    = array_unique(array_column($listRetur, 'no_model'));
        if (!empty($noModels)) {
            $material = $this->materialModel->getMaterialByModels($noModels);

            $itemTypes    = array_unique(array_column($material, 'item_type'));
            $kodeWarnas    = array_unique(array_column($material, 'kode_warna'));

            $validate = [
                'area' => $area,
                'no_model' => $noModels,
                'item_type' => $itemTypes,
                'kode_warna' => $kodeWarnas,
            ];

            if (!empty($validate)) {
                $kirim = $this->pengeluaranModel->getQtyKirim($validate);
                $poPlus = $this->totalPoTambahanModel->getTotalPoTambahan($validate);
            }
        }

        $data = [
            'listRetur' => $listRetur,
            'material' => $material,
            'kirim' => $kirim,
            'poPlus' => $poPlus,
        ];
        return $this->response->setJSON($data);
    }
    public function filterTglPakai($area)
    {
        $tgl_awal = $this->request->getGet('awal');
        $tgl_akhir = $this->request->getGet('akhir');

        $listTglPaki = $this->pemesananModel->getTglPakai($area, $tgl_awal, $tgl_akhir);
        return $this->response->setJSON($listTglPaki);
    }
    public function getDataPemesanan()
    {
        $area = $this->request->getGet('area');
        $jenis = $this->request->getGet('jenis');
        $tgl_pakai = $this->request->getGet('tgl_pakai');

        $listTglPaki = $this->pemesananModel->getreportPemesanan($area, $jenis, $tgl_pakai);
        return $this->response->setJSON($listTglPaki);
    }
    public function getNoModelByPoTambahan()
    {
        // Ambil parameter 'area' dari query string
        $area = $this->request->getGet('area');

        // Periksa apakah parameter 'area' tersedia
        if (empty($area)) {
            return $this->response
                ->setStatusCode(400) // Kode HTTP 400 (Bad Request)
                ->setJSON(['error' => 'Parameter area is required']);
        }

        $data = $this->poTambahanModel->getNoModelByArea($area);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getStyleSizeByPoTambahan()
    {
        // Ambil parameter 'area' dari query string
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('no_model');

        // Periksa apakah parameter 'area' tersedia
        if (empty($area)) {
            return $this->response
                ->setStatusCode(400) // Kode HTTP 400 (Bad Request)
                ->setJSON(['error' => 'Parameter area is required']);
        }

        $data = $this->poTambahanModel->getStyleSizeBYNoModelArea($area, $noModel);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getMUPoTambahan()
    {
        $no_model = $this->request->getGet('no_model');
        $style_size = $this->request->getGet('style_size');
        $area = $this->request->getGet('area');

        $data = $this->poTambahanModel->getMuPoTambahan($no_model, $style_size, $area);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getKgTambahan()
    {
        $params = [
            'no_model' => $this->request->getGet('no_model'),
            'item_type' => $this->request->getGet('item_type'),
            'kode_warna' => $this->request->getGet('kode_warna'),
            'style_size' => $this->request->getGet('style_size') ?? null,
            'area' => $this->request->getGet('area'),
        ];

        // $data = $this->poTambahanModel->getKgPoTambahan($params);
        $data = $this->totalPoTambahanModel->getKgPoTambahan($params);


        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getPemesananByAreaModel()
    {
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('no_model');

        $data = $this->pemesananModel->getPemesananByAreaModel($area, $noModel);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getReturByAreaModel()
    {
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('no_model');

        $data = $this->returModel->getReturByAreaModel($area, $noModel);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getKgPoTambahan()
    {
        $area = $this->request->getGet('area');
        $noModel = $this->request->getGet('no_model');

        $data = $this->returModel->getReturByAreaModel($area, $noModel);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getMaterialByNoModel($noModel)
    {
        $data = $this->materialModel->getMaterialByNoModel($noModel);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }
    public function getMaterialForPemesananRosso($model, $styleSize, $area)
    {
        $mu = $this->materialModel->getMaterialForPemesananRosso($model, $styleSize, $area);

        return $this->respond($mu, 200);
    }
    public function listExportRetur($area)
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        $tglBuat = $this->request->getGet('tglBuat') ?? '';

        $data = [];
        $listRetur = $this->returModel->filterData($area, $tglBuat, $noModel);

        // Ambil kolom-kolom unik dari hasil filterData
        $noModels    = array_unique(array_column($listRetur, 'no_model'));
        $itemTypes   = array_unique(array_column($listRetur, 'item_type'));
        $kodeWarnas  = array_unique(array_column($listRetur, 'kode_warna'));
        $colors      = array_unique(array_column($listRetur, 'color'));

        $validate = [
            'area'       => $area,
            'no_model'   => $noModels,
            'item_type'  => $itemTypes,
            'kode_warna' => $kodeWarnas,
            'color'      => $colors,
        ];

        $dataPoTamabahan = $this->poTambahanModel->getDataPlus($validate);
        $material = $this->materialModel->getMaterialForPPH($noModel);
        $data = [
            'dataRetur' => $listRetur,
            'dataPoTambahan' => $dataPoTamabahan,
            'material' => $material,
        ];
        return $this->response->setJSON($data);
    }

    public function getGWAktual()
    {
        $no_model = $this->request->getGet('pdk');
        $style_size = $this->request->getGet('size');

        $data = $this->materialModel->getGWAktual($no_model, $style_size);

        return $this->response
            ->setStatusCode(200)
            ->setJSON($data);
    }

    public function saveGWAktual()
    {
        $data = $this->request->getGet();
        $no_model = $data['pdk'];
        $style_size = $data['size'];
        $gwAktual = $data['gw_aktual'];

        // Validasi input
        $idMaterial = $this->materialModel->getMaterialID($no_model, $style_size);

        $materialIDs = array_column($idMaterial, 'id_material');

        if ($idMaterial) {
            $updated = $this->materialModel->updateGwAktual($materialIDs, $gwAktual);
        }

        if ($updated) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['status' => 'success', 'message' => 'GW aktual berhasil diupdate.']);
        } else {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Gagal mengupdate GW aktual.']);
        }
    }
    public function getpengaduan()
    {
        $username = urlencode(session()->get('username'));
        $role     = session()->get('role');
        $url      = api_url('capacity') . 'pengaduan/' . $username . '/' . $role;

        try {
            $json = @file_get_contents($url);
            if ($json === false) {
                throw new \Exception('Gagal mengambil data dari API');
            }

            $response = json_decode($json, true);
            $data = [
                'pengaduan' => $response['pengaduan'] ?? [],
                'replies'   => $response['replies'] ?? [],
                'role' => $role,
                'title' => 'Pengaduan',
                'active' => $this->active
            ];

            // Check if $role is not empty and view file exists
            $viewPath = APPPATH . 'Views/' . $role . '/pengaduan/index.php';
            if (empty($role) || !is_file($viewPath)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Role is empty or view file does not exist: ' . $viewPath,
                ])->setStatusCode(500);
            }

            return view($role . '/pengaduan/index', $data);
        } catch (\Exception $e) {
            $data = [
                'message' => $e->getMessage(),
                'role'    => $role,
                'title'   => 'Pengaduan',
                'active'  => $this->active
            ];
            return view($role . '/pengaduan/index', $data);
        }
    }
    public function filterDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');
        $poPlus = $this->request->getGet('po_plus');

        $data = $this->pemasukanModel->getFilterDatangBenang($key, $tanggalAwal, $tanggalAkhir, $poPlus);
        // dd($data, $poPlus);
        return $this->response->setJSON($data);
    }
    public function filterPoBenang()
    {
        $key = $this->request->getGet('key');

        $data = $this->materialModel->getFilterPoBenang($key);

        return $this->response->setJSON($data);
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
    public function filterReportGlobal()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis');
        log_message('debug', 'Received key: ' . $key);  // Log key yang diterima
        if (empty($key)) {
            return $this->response->setJSON(['error' => 'Key is missing']);
        }

        $data = $this->masterOrderModel->getFilterReportGlobal($key, $jenis);
        // Log data yang diterima dari model
        // log_message('debug', 'Query result: ' . print_r($data, true));

        if (empty($data)) {
            return $this->response->setJSON(['error' => 'No data found']);
        }

        return $this->response->setJSON($data);
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

    public function filterReportGlobalNylon()
    {
        $key = $this->request->getGet('key');
        $jenis = 'NYLON';
        // log_message('debug', 'Received key: ' . $key);  // Log key yang diterima
        if (empty($key)) {
            return $this->response->setJSON(['error' => 'Key is missing']);
        }

        $data = $this->masterOrderModel->getFilterReportGlobal($key, $jenis);
        // Log data yang diterima dari model
        // log_message('debug', 'Query result: ' . print_r($data, true));

        if (empty($data)) {
            return $this->response->setJSON(['error' => 'No data found']);
        }

        return $this->response->setJSON($data);
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
        $data = $this->materialModel->getFilterSisaPakai($jenis, $bulan, $noModel, $kodeWarna);

        return $this->response->setJSON($data);
    }

    public function historyPindahOrder()
    {
        $noModelOld   = $this->request->getGet('no_model_old') ?? '';
        $noModelNew   = $this->request->getGet('no_model_new') ?? '';
        $kodeWarna    = $this->request->getGet('kode_warna') ?? '';

        if ($noModelOld === '' && $noModelNew === '' && $kodeWarna === '') {
            $dataPindah = $this->historyStock->getHistoryPindahOrder(null, null, null, 10);
        } else {
            $dataPindah = $this->historyStock->getHistoryPindahOrder($noModelOld, $noModelNew, $kodeWarna);
        }

        return $this->response->setJSON($dataPindah);
    }

    public function reportSisaDatangBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getFilterData = $this->materialModel->getFilterSisaDatangBenang($delivery, $noModel, $kodeWarna);
        // dd($getFilterData);
        return $this->response
            ->setStatusCode(200)
            ->setJSON($getFilterData);
    }

    public function reportSisaDatangNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getFilterData = $this->materialModel->getFilterSisaDatangNylon($delivery, $noModel, $kodeWarna);

        // set header JSON dan langsung echo data
        return $this->response
            ->setStatusCode(200)
            ->setJSON($getFilterData);
    }

    public function reportSisaDatangSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getFilterData = $this->materialModel->getFilterSisaDatangSpandex($delivery, $noModel, $kodeWarna);

        // set header JSON dan langsung echo data
        return $this->response
            ->setStatusCode(200)
            ->setJSON($getFilterData);
    }

    public function reportSisaDatangKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        $getFilterData = $this->materialModel->getFilterSisaDatangKaret($delivery, $noModel, $kodeWarna);

        // set header JSON dan langsung echo data
        return $this->response
            ->setStatusCode(200)
            ->setJSON($getFilterData);
    }

    public function filterBenangMingguan()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterBenang($tanggalAwal, $tanggalAkhir);
        return $this->response->setJSON($data);
    }

    public function filterBenangBulanan()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterBenang($tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }
    public function getMasterRangePemesanan()
    {
        $day = $this->request->getGet('day');
        $area = $this->request->getGet('area');

        $data = $this->masterRangePemesanan->where('days', $day)->where('area', $area)->first();;
        return $this->response->setJSON($data);
    }
    public function pengaduanExport($idPengaduan)
    {

        // Ambil data dari API lokal
        $client = service('curlrequest');
        $response = $client->get(api_url('capacity') . "ExportPengaduan/{$idPengaduan}");

        $pengaduan = json_decode($response->getBody(), true);

        if (!$pengaduan || !isset($pengaduan['id_pengaduan'])) {
            return "Data tidak ditemukan.";
        }

        $data['pengaduan'] = $pengaduan;

        // Render view jadi HTML
        $html = view(session()->get('role') . '/pengaduan/pdf_view', $data);

        // Inisialisasi Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Auto download
        return $dompdf->stream('pengaduan_' . $idPengaduan . '.pdf', ["Attachment" => true]);
    }

    public function filterReportKebutuhanBahanBaku()
    {
        $jenis = $this->request->getGet('jenis');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->masterOrderModel->getFilterKebutuhanBahanBaku($jenis, $tanggalAwal, $tanggalAkhir);

        return $this->response->setJSON($data);
    }

    public function excelReportKebutuhanBahanBaku()
    {
        $jenis = $this->request->getGet('jenis');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->masterOrderModel->getFilterKebutuhanBahanBaku($jenis, $tanggalAwal, $tanggalAkhir);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Kebutuhan Bahan Baku');
        $sheet->mergeCells('A1:C1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Item Type", "Total Kebutuhan (Kg)"];
        // $header = ["No", "No Model", "Buyer", "Foll Up", "Item Type", "Delivery Awal", "Delivery Akhir", "Total Kebutuhan (Kg)"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);
        $sheet->getStyle('A3:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:C3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    // $item['no_model'],
                    // $item['buyer'],
                    // $item['foll_up'],
                    $item['item_type'],
                    // $item['delivery_awal'],
                    // $item['delivery_akhir'],
                    number_format($item['total_kebutuhan'], 2, '.', ',')
                ]
            ], NULL, 'A' . $row);
            $row++;
        }

        // Atur border untuk seluruh tabel
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A3:C' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:C' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Total_Kebutuhan' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function getBBForSummaryPlanner()
    {
        $noModel = $this->request->getGet('no_model');
        if (!$noModel) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter no_model tidak ditemukan'
            ]);
        }
        // Ubah string "AK5485,AK5484,..." jadi array
        $models = array_map('trim', explode(',', $noModel));

        // Ambil data styleSize by bb
        $bbData = $this->materialModel->getBBForSummaryPlanner($models);

        // 🔹 Susun ulang array agar key-nya pakai no_model
        $bb = [];
        foreach ($bbData as $row) {
            $modelName = $row['no_model'] ?? 'UNKNOWN';
            if (!isset($bb[$modelName])) {
                $bb[$modelName] = [];
            }
            $bb[$modelName][] = $row; // bisa juga = $row kalau 1 data per model
        }

        // 🔹 Return hasil JSON
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $bb
        ]);
    }
    public function getListKirim($area, $tgl)
    {
        $list = $this->pengeluaranModel->getKirimArea($area, $tgl);
        $list = $list ?? [];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $list
        ]);
    }
    public function countKirimArea($area, $tgl)
    {
        $num = $this->pengeluaranModel->countKirim($area, $tgl);

        return $this->response->setJSON([
            'count' => $num
        ]);
    }
    public function updateTerimaArea($id)
    {
        $update = $this->pengeluaranModel->update($id, ['terima_area' => 1]);
        $this->response->setStatusCode($update ? 200 : 500);
    }

    public function getTglScheduleBulk()
    {
        $request = service('request');
        $json = $request->getJSON(true); // ambil input JSON sebagai array

        if (!isset($json['models']) || !is_array($json['models'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Parameter "models" wajib dan harus berupa array'
            ])->setStatusCode(400);
        }
        $models = $json['models'];

        log_message('debug', 'Schedule bulk result: ' . json_encode($models));

        // 🔹 Query ke DB (gunakan model kamu sendiri)
        $db = \Config\Database::connect();
        $builder = $db->table('schedule_celup'); // ganti sesuai nama tabel kamu
        $builder->select('no_model, MIN(tanggal_schedule) AS tgl_schedule');
        $builder->join('master_material', 'master_material.item_type=schedule_celup.item_type');
        $builder->where('master_material.jenis', 'BENANG');
        $builder->whereIn('no_model', $models);
        $builder->groupBy('no_model');

        $query = $builder->get();
        $result = $query->getResultArray();

        // 🔹 Untuk jaga-jaga kalau ada model yang gak ketemu
        $existingModels = array_column($result, 'no_model');
        foreach ($models as $m) {
            if (!in_array($m, $existingModels)) {
                $result[$m] = [
                    'tgl_schedule' => null
                ];
            }
        }
        log_message('debug', 'Schedule bulk result: ' . json_encode($result));
        return $this->respond($result, 200);
    }

    public function searchStock()
    {
        $no_model = $this->request->getGet('no_model');
        $warna = $this->request->getGet('warna') ?? '';

        $filteredData = $this->stockModel->searchStock($no_model, $warna);

        return $this->respond($filteredData);
    }
    public function getAllGw()
    {
        log_message('info', '🟢 Log info jalan');
        // Ambil data JSON dari body request
        $json = $this->request->getJSON(true); // true = hasilnya array, bukan object

        // Misalnya kamu mau proses atau validasi datanya dulu
        $result = [];
        foreach ($json as $item) {
            //get data gw aktual || gw MU
            $gw = $this->materialModel->getAllGw($item['no_model'], $item['size']);

            // gunakan gw aktual, jika kosong gunakan gw MU
            $gwValue = (isset($gw['gw_aktual']) && $gw['gw_aktual'] > 0)
                ? $gw['gw_aktual']
                : ($gw['gw'] ?? 0); // fallback kalau null

            $result[] = [
                'no_model' => strtoupper($item['no_model']),
                'size'     => strtoupper($item['size']),
                'gw'       => $gwValue
            ];
        }

        // Kirim respons balik ke client
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Data diterima',
            'data' => $result
        ]);
    }

    public function getDataStockAwal()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataStockAwal = $this->historyStock->getDataStockAwal($key, $jenis);

        return $this->response->setJSON($dataStockAwal);
    }

    public function getDataDatangSolid()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataDatangSolid = $this->pemasukanModel->getDatangSolid($key, $jenis);

        return $this->response->setJSON($dataDatangSolid);
    }

    public function getDataDatangSolidPlus()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataPlusDatangSolid = $this->pemasukanModel->getPlusDatangSolid($key, $jenis);

        return $this->response->setJSON($dataPlusDatangSolid);
    }

    public function getDataGantiRetur()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataGantiRetur = $this->pemasukanModel->getGantiRetur($key, $jenis);

        return $this->response->setJSON($dataGantiRetur);
    }

    public function getDataDatangLurex()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataDatangLurex = $this->pemasukanModel->getDatangLurex($key, $jenis);

        return $this->response->setJSON($dataDatangLurex);
    }

    public function getDataDatangLurexPlus()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataPlusDatangLurex = $this->pemasukanModel->getPlusDatangLurex($key, $jenis);

        return $this->response->setJSON($dataPlusDatangLurex);
    }

    public function getDataReturGbn()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataReturGbn = $this->historyStock->getDataReturGbn($key, $jenis);

        return $this->response->setJSON($dataReturGbn);
    }

    public function getDataReturArea()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataReturArea = $this->returModel->getDataReturArea($key, $jenis);

        return $this->response->setJSON($dataReturArea);
    }

    public function getDataPakaiArea()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataPakaiArea = $this->pengeluaranModel->getPakaiArea($key, $jenis);

        return $this->response->setJSON($dataPakaiArea);
    }

    public function getDataPakaiLain()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataPakaiLain = $this->otherOutModel->getPakaiLain($key, $jenis);

        return $this->response->setJSON($dataPakaiLain);
    }

    public function getDataReturStock()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataReturStock = $this->returModel->getDataReturStock($key, $jenis);

        return $this->response->setJSON($dataReturStock);
    }

    public function getDataReturTitip()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataReturTitip = $this->returModel->getDataReturTitip($key, $jenis);

        return $this->response->setJSON($dataReturTitip);
    }

    public function getDataDipinjam()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataOrderDipinjam = $this->pengeluaranModel->getDataDipinjam($key, $jenis);

        return $this->response->setJSON($dataOrderDipinjam);
    }

    public function getDataDipindah()
    {
        $key = $this->request->getGet('key');
        $jenis = $this->request->getGet('jenis') ?? '';

        $dataOrderDipindah = $this->historyStock->getDataDipindah($key, $jenis);

        return $this->response->setJSON($dataOrderDipindah);
    }
}
