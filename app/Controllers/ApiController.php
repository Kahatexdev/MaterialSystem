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
use App\Models\StockModel;
use App\Models\HistoryPindahPalet;
use App\Models\HistoryPindahOrder;

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
    protected $historyPindahPalet;
    protected $historyPindahOrder;


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
        $this->historyPindahPalet = new HistoryPindahPalet();
        $this->historyPindahOrder = new HistoryPindahOrder();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
    }

    public function index()
    {
        //
    }
    public function statusbahanbaku($area)
    {
        $search = $this->request->getGet('search');
        $model = $this->materialModel->orderPerArea($area);

        $res = [];
        foreach ($model as &$row) {
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
            $row['stock'] = $stock['stock'] ?? 0;
            $row['masuk'] = $inout['masuk'] ?? 0;
            $row['keluar'] = $inout['keluar'];
            $res[] = $row;
        }
        return $this->respond($res, 200);
    }
    public function getMaterialForPemesanan($model, $styleSize, $area)
    {
        $mu = $this->materialModel->getMU($model, $styleSize, $area);

        return $this->respond($mu, 200);
    }

    public function getMaterialForPPH($area)
    {
        $material = $this->materialModel->getMaterialForPPH($area);

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
        $noModel = $this->request->getPost('noModel') ?? '';
        $warna = $this->request->getPost('warna') ?? '';

        $results = $this->stockModel->searchStock($noModel, $warna);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);
       
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
        
        return $this->respond($resultsArray, 200);

    }
    public function listPemesanan($area)
    {
        $dataList = $this->pemesananModel->getListPemesananByArea($area);

        return $this->respond($dataList, 200);
    }
}
