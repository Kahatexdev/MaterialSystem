<?php

namespace App\Controllers;

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
use App\Models\PengeluaranModel;
use App\Models\ReturModel;
use App\Models\KategoriReturModel;
use App\Models\PoTambahanModel;
use App\Models\TrackingPoCovering;
use PHPUnit\Framework\Attributes\IgnoreFunctionForCodeCoverage;

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
    protected $pengeluaranModel;
    protected $returModel;
    protected $kategoriReturModel;
    protected $poTambahanModel;
    protected $trackingPoCovering;


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
        $this->pengeluaranModel = new PengeluaranModel();
        $this->returModel = new ReturModel();
        $this->kategoriReturModel = new KategoriReturModel();
        $this->poTambahanModel = new PoTambahanModel();
        $this->trackingPoCovering = new TrackingPoCovering();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
    }

    public function index()
    {
        //
    }

    // v3
    public function statusbahanbaku($noModel)
    {
        $search = $this->request->getGet('search');
        // $rows   = $this->materialModel->MaterialPDK($noModel);
        $rows   = $this->openPoModel->MaterialPDK($noModel);
        $res    = [];

        // Field‑field schedule yang ingin di-merge (tidak termasuk 'qty_po'!)
        $fields = [
            'jenis',
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
            'admin',
            'created_at',
            'updated_at',
            'kg_stock',
        ];

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
                // dd ($allSchedules);
                if (! empty($allSchedules)) {
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

                if (! empty($allCoverings)) {
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

        // Loop melalui data untuk pembaruan
        foreach ($data['items'] as $item) {
            if (!is_array($item)) {
                log_message('error', 'Invalid item structure: ' . json_encode($item));
                continue; // Lewati jika struktur tidak sesuai
            }

            foreach ($item as $row) {
                // Validasi setiap row
                if (empty($row['id_material']) || empty($row['qty_cns']) || empty($row['qty_berat_cns'])) {
                    log_message('error', 'Invalid row data: ' . json_encode($row));
                    continue; // Lewati jika data tidak lengkap
                }
                // Ambil data berdasarkan id_material
                $existingData = $this->materialModel->find($row['id_material']);
                log_message('debug', 'Existing data: ' . json_encode($existingData));

                // Siapkan data untuk pembaruan
                $updateData = [
                    'qty_cns'       => $row['qty_cns'],
                    'qty_berat_cns' => $row['qty_berat_cns'],
                ];

                // Periksa jika ada perubahan sebelum melakukan update
                if (
                    $existingData['qty_cns'] != $updateData['qty_cns'] ||
                    $existingData['qty_berat_cns'] != $updateData['qty_berat_cns']
                ) {
                    try {
                        // Gunakan model untuk melakukan update
                        $update = $this->materialModel->update($row['id_material'], [
                            'qty_cns'       => $updateData['qty_cns'],
                            'qty_berat_cns' => $updateData['qty_berat_cns'],
                        ]);

                        if ($update) {
                            $updateCount++;
                            log_message('error', 'Update successful for id_material: ' . $row['id_material']);
                        } else {
                            return $this->respond([
                                'status'  => 'error',
                                'message' => $row['id_material'] . " data gagal diperbarui",
                            ], 500);
                            log_message('error', 'Update failed for id_material: ' . $row['id_material']);
                        }
                    } catch (\Exception $e) {
                        log_message('critical', 'Exception during update: ' . $e->getMessage());
                    }
                } else {
                    log_message('error', 'No changes needed for id_material: ' . $row['id_material']);
                }
            }
        }
        // Kembalikan respon setelah seluruh loop selesai
        return $this->respond([
            'status'  => 'success',
            'message' => "$updateCount data berhasil diperbarui",
        ], 200);
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

        for ($i = 0; $i < $length; $i++) {
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
                    'message' => "Data pemesanan sudah ada. " . $resultItem['po_tambahan'],
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
        $dataList = $this->pemesananModel->getListPemesananByArea($area);

        return $this->respond($dataList, 200);
    }
    public function getUpdateListPemesanan()
    {
        $data = $this->request->getPost([
            'area',
            'tgl_pakai',
            'no_model',
            'item_type',
            'kode_warna',
            'color',
            'po_tambahan'
        ]);

        $dataList = $this->pemesananModel->getListPemesananByUpdate($data);

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
            $materialUpdate = [
                'qty_cns'       => $qtyCns,
                'qty_berat_cns' => $qtyBeratCns,
            ];

            $updateMaterial = $this->materialModel->update($idMaterial, $materialUpdate);

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
            $materialUpdate = [
                'qty_cns'       => $qtyCns,
                'qty_berat_cns' => $qtyBeratCns,
            ];

            $updateMaterial = $this->materialModel->update($idMaterial, $materialUpdate);

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

        $data = [
            'area' => $area,
            'jenis' => $jenis,
            'tanggal_pakai' => $tanggal_pakai,
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

        $data = $this->materialModel->getStyleSizeByBb($noModel, $itemType, $kodeWarna);

        return $this->respond($data, 200);
    }

    // get data pengiriman
    public function getPengirimanArea()
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        // $noModel = 'DB2501';
        $results = $this->pengeluaranModel->searchPengiriman($noModel);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);

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
                $warnaData['kgs_out'] = $kirimArea['kgs_out'] ?? 0;
            }
        }

        return response()->setJSON($materials);
    }
    public function savePoTambahan()
    {
        $req = $this->request->getJSON(true);
        if (empty($req['items']) || !is_array($req['items'])) {
            return $this->respond(['status' => 'error', 'message' => 'Items tidak ditemukan.'], 400);
        }

        $sukses = 0;
        $gagal  = 0;

        foreach ($req['items'] as $item) {
            // 1) Cari id_material berdasarkan no_model, area, item_type, kode_warna, style_size
            $validate = [
                'no_model'   => $item['no_model'],
                'area'       => $item['area'],
                'item_type'  => $item['item_type'],
                'kode_warna' => $item['kode_warna'],
                'style_size' => $item['style_size'],
            ];

            $idMat = $this->materialModel->getIdMaterial($validate);
            if (empty($idMat)) {
                // gagal jika tidak ada material
                $gagal++;
                continue;
            }
            // getIdMaterial sekarang mengembalikan single id
            $item['id_material'] = $idMat;

            // 2) Hapus field yang tidak ada di table po_tambahan
            unset(
                $item['area'],
                $item['no_model'],
                $item['item_type'],
                $item['kode_warna'],
                $item['color'],
                $item['style_size']
            );

            // 3) Simpan
            if ($this->poTambahanModel->insert($item)) {
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
    }
    public function filterPoTambahan()
    {
        $area = $this->request->getGet('area');
        $tglBuat = $this->request->getGet('tglBuat');
        $noModel = $this->request->getGet('model') ?? null;

        // $area = 'KK2A';
        // $noModel = '';
        // $tglBuat = '2025-06-17';

        $filterData = $this->poTambahanModel->filterData($area, $tglBuat, $noModel);

        return $this->respond($filterData);
    }
    public function cekMaterial($id)
    {
        $material = $this->materialModel->materialCek($id);
        return $this->response->setJSON($material);
    }
    public function listRetur($area)
    {


        $listRetur = $this->returModel->getListRetur($area);
        return $this->response->setJSON($listRetur);
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
            'style_size' => $this->request->getGet('style_size'),
            'area' => $this->request->getGet('area'),
        ];

        $data = $this->poTambahanModel->getKgPoTambahan($params);

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
}
