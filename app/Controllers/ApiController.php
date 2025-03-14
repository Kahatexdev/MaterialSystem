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
    public function getMU($model, $styleSize, $area)
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

        // Logging untuk debugging awal
        log_message('debug', 'Data received: ' . json_encode($data));

        // Inisialisasi variabel untuk menghitung jumlah data yang berhasil diperbarui
        $updateCount = 0;

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
                if (empty($row['id_material']) || empty($row['qty_cns']) || empty($row['qty_berat_cns']))) {
                    log_message('error', 'Invalid row data: ' . json_encode($row));
                    continue; // Lewati jika data tidak lengkap
                }

                // Siapkan data untuk pembaruan
                $updateData = [
                    'qty_cns'       => $row['qty_cns'],
                    'qty_berat_cns' => $row['qty_berat_cns'],
                ];

                // Lakukan pembaruan pada database
                try {
                    $update = $this->materialModel->update($row['id_material'], $updateData);
                    if ($update) {
                        $updateCount++;
                    } else {
                        log_message('error', 'Update failed for id_material: ' . $row['id_material']);
                    }
                } catch (\Exception $e) {
                    log_message('critical', 'Exception during update: ' . $e->getMessage());
                    return $this->respond([
                        'status'  => 'error',
                        'message' => 'Terjadi kesalahan saat memperbarui data',
                    ], 500);
                }
            }
        }

        // Kirimkan respons berdasarkan jumlah data yang diperbarui
        if ($updateCount > 0) {
            return $this->respond([
                'status'  => 'success',
                'message' => "$updateCount data berhasil diperbarui",
            ], 200);
        } else {
            return $this->respond([
                'status'  => 'error',
                'message' => "Tidak ada data yang berhasil diperbarui",
            ], 400);
        }
    }
    public function saveListPemesanan()
    {
        // Ambil data JSON dari request
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Tidak ada data list pemesanan",
            ], 400);
        }

        // Pastikan data yang diperlukan ada dan merupakan array
        if (!isset($data['id_material']) || !is_array($data['id_material'])) {
            return $this->respond([
                'status'  => 'error',
                'message' => "Data id_material tidak valid",
            ], 400);
        }

        // Asumsikan semua key memiliki panjang array yang sama
        $length = count($data['id_material']);
        $result = [];

        for ($i = 0; $i < $length; $i++) {
            $result[] = [
                'id_material'     => $data['id_material'][$i],
                'tgl_list'        => date('Y-m-d'),
                'tgl_pakai'       => $data['tgl_pakai'][$i],
                'jl_mc'           => $data['jalan_mc'][$i],
                'ttl_qty_cones'   => $data['ttl_cns'][$i],
                'ttl_berat_cones' => $data['ttl_berat_cns'][$i],
                'admin'           => $data['area'][$i],
                'no_model'        => $data['no_model'][$i],
                'style_size'      => $data['style_size'][$i],
                'item_type'       => $data['item_type'][$i],
                'kode_warna'      => $data['kode_warna'][$i],
                'warna'           => $data['warna'][$i],
                'created_at'      => date('Y-m-d H:i:s'),
            ];
        }

        try {
            // Cek apakah data sudah ada
            $existingData = $this->pemesananModel
            ->where('id_material', $data['id_material'])
            ->where('tgl_pakai', $data['tgl_pakai'])
            ->where('admin', $data['area'])
            ->first();

            if ($existingData) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Data dengan id_material '{$data['id_material']}', tgl_pakai '{$data['tgl_pakai']}', dan admin '{$data['admin']}' sudah ada.",
                    'debug'   => $existingData,
                ], 400);
            }
            $insert = $this->pemesananModel->insertBatch($result);
            if ($insert) {
                // Misalnya, data login sudah tersedia dari session sebelumnya atau request,
                // dan kita ingin memastikan bahwa session login tetap tersimpan atau diperbarui.
                $session = session();
                // Contoh: jika data login sudah ada dalam session, misalnya:
                // $session->get('user') atau jika ingin menyimpan data login baru:
                $userLoginData = [
                    'id_user'       => $data['id_user'] ?? 0,          // Sesuaikan dengan key yang ada
                    'username' => $data['username'] ?? 'default', // Sesuaikan dengan key yang ada
                    'role'  => $data['role'] ?? '',
                    'logged_in'=> true,
                ];
                $session->set('user', $userLoginData);
                // Hapus session 'pemesananBb' setelah menetapkan ulang session pengguna
                if ($session->has('pemesananBb')) {
                    $session->remove('pemesananBb');
                }

                return $this->respond([
                    'status'  => 'success',
                    'message' => count($result) . " data berhasil disimpan",
                    'debug'   => $result,
                ], 200);
            } else {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "Tidak ada data yang berhasil disimpan",
                    'debug'   => $result,
                ], 400);
            }
        } catch (\Exception $e) {
            log_message('critical', 'Exception during batch insert: ' . $e->getMessage());
            return $this->respond([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
                'debug'   => $result,
            ], 500);
        }
    }



}
