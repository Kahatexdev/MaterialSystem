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
use App\Models\HistoryStockCoveringModel;

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
    protected $historyCoveringStockModel;

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
        $this->historyCoveringStockModel = new HistoryStockCoveringModel();

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
        // $perPage      = 9;
        // $stok = $this->coveringStockModel->stokCovering($perPage);
        // $pager = $this->coveringStockModel->pager;
        // // dd($stok);
        // $data = [
        //     'active' => $this->active,
        //     'title' => 'Warehouse',
        //     'role' => $this->role,
        //     'stok' => $stok,
        //     'pager' => $pager,
        // ];

        // return view($this->role . '/warehouse/index', $data);
        // Ambil parameter filter dari GET

        helper('form');
        $q       = $this->request->getGet('q', FILTER_SANITIZE_STRING);
        $status  = $this->request->getGet('status', FILTER_SANITIZE_STRING);
        $mesin   = $this->request->getGet('mesin', FILTER_SANITIZE_STRING);
        $dr      = $this->request->getGet('dr', FILTER_SANITIZE_STRING);
        $jb      = $this->request->getGet('jenis_benang', FILTER_SANITIZE_STRING);
        $perPage = 9;

        // Bangun query dengan chaining
        $builder = $this->coveringStockModel
            ->select('stock_covering.*, IF(stock_covering.ttl_kg > 0, "ada", "habis") AS status')
            ->orderBy('ttl_kg', 'DESC');
        // dd($builder);
        if ($q) {
            $builder->like('jenis', $q);
        }
        // **GANTI FILTER status**:
        if ($status === 'ada') {
            $builder->where('ttl_kg >', 0);
        } elseif ($status === 'habis') {
            $builder->where('ttl_kg <=', 0);
        }
        if ($mesin) {
            $builder->where('jenis_mesin', $mesin);
        }
        if ($dr) {
            $builder->where('dr', $dr);
        }
        if ($jb) {
            $builder->where('jenis_benang', $jb);
        }

        // Paginate hasilnya
        $stok  = $builder->paginate($perPage, 'warehouse');
        $pager = $builder->pager;

        return view($this->role . '/warehouse/index', [
            'active'              => $this->active,
            'title'               => 'Warehouse',
            'role'                => $this->role,
            'stok'                => $stok,
            'pager'               => $pager,
            // Untuk daftar filter dropdown tetap lengkap:
            'unique_jenis_mesin'  => $this->coveringStockModel->getAllJenisMesin(),
            'unique_dr'           => $this->coveringStockModel->getAllDr(),
            'request' => $this->request,
        ]);
    }

    public function create()
    {
        // Aturan validasi
        $rules = [
            'jenis'      => 'required',
            'color'      => 'required',
            'code'       => 'required',
            'ttl_kg'     => 'required|numeric',
            'ttl_cns'    => 'required|numeric'
        ];

        // Validasi input
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lmdInput = $this->request->getPost('lmd');
        $lmdValue = !empty($lmdInput) && is_array($lmdInput) ? implode(', ', $lmdInput) : ($lmdInput ?? null);


        $posisiRakInput = $this->request->getPost('posisi_rak');
        $posisiRakValue = is_array($posisiRakInput) ? implode(',', $posisiRakInput) : $posisiRakInput;

        $admin = session()->get('role');
        $jenis = $this->request->getPost('jenis');
        $color = $this->request->getPost('color');
        $code = $this->request->getPost('code');
        $mesin = $this->request->getPost('jenis_mesin');
        $dr = $this->request->getPost('dr');
        $jenisCover = $this->request->getPost('jenis_cover');
        $jenisBenang = $this->request->getPost('jenis_benang');
        // dd($mesin);
        $existingStock = $this->coveringStockModel->getStockByJenisColorCodeMesin($jenis, $color, $code, $mesin, $dr, $jenisCover, $jenisBenang);
        if ($existingStock) {
            return redirect()->back()->withInput()->with('error', 'Data stok sudah ada! </br> Silahkan update stok yang sudah ada.');
        }

        $data = [
            'jenis'      => $this->request->getPost('jenis'),
            'jenis_cover' => $this->request->getPost('jenis_cover'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'jenis_mesin' => $this->request->getPost('jenis_mesin'),
            'dr'         => $this->request->getPost('dr'),
            'color'      => $this->request->getPost('color'),
            'code'       => $this->request->getPost('code'),
            'lmd'        => $lmdValue,
            'ttl_kg'     => $this->request->getPost('ttl_kg'),
            'ttl_cns'    => $this->request->getPost('ttl_cns'),
            'admin'      => $admin
        ];

        $this->historyCoveringStockModel->insert($data);

        // Simpan ke database dan redirect dengan pesan sukses atau error
        if ($this->coveringStockModel->insert($data)) {
            return redirect()->to(base_url($this->role . '/warehouse'))->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
        }
    }

    public function updateStock()
    {
        $postData = $this->request->getJSON(true); // Ambil data dari request JSON

        // Cek apakah stockItemId valid
        $stockData = $this->coveringStockModel->find($postData['stockItemId']);

        if (!$stockData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stock item tidak ditemukan!'
            ]);
        }

        // Cek apakah stockItemId sama dengan id_covering_stock
        if ($postData['stockItemId'] != $stockData['id_covering_stock']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stock Item ID tidak cocok dengan ID Covering Stock!'
            ]);
        }

        // Cek apakah action valid
        if (!in_array($postData['action'], ['add', 'remove'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Aksi tidak valid!'
            ]);
        }

        // Menentukan nilai perubahan stok berdasarkan action
        $changeAmountKg = ($postData['action'] == 'remove') ? -$postData['stockAmount'] : $postData['stockAmount'];
        $changeAmountCns = ($postData['action'] == 'remove') ? -$postData['amountcones'] : $postData['amountcones'];

        // Jika remove, cek apakah jumlah yang dikurangi tidak melebihi stok tersedia
        if ($postData['action'] == 'remove') {
            if ($postData['stockAmount'] > $stockData['ttl_kg'] || $postData['amountcones'] > $stockData['ttl_cns']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Jumlah pengurangan melebihi stok yang tersedia!'
                ]);
            }

            // Kurangi stok dengan memastikan tidak negatif
            $stockData['ttl_kg'] = max(0, $stockData['ttl_kg'] + $changeAmountKg);
            $stockData['ttl_cns'] = max(0, $stockData['ttl_cns'] + $changeAmountCns);
        } else {
            // Jika add, cukup tambahkan stok
            $stockData['ttl_kg'] += $changeAmountKg;
            $stockData['ttl_cns'] += $changeAmountCns;
        }

        // Lakukan update ke database
        $this->coveringStockModel->update($postData['stockItemId'], $stockData);
        // data history stock
        $historyStock = [
            'no_model'    => $postData['no_model'],
            'jenis'       => $stockData['jenis'],
            'jenis_benang' => $stockData['jenis_benang'],
            'jenis_cover' => $stockData['jenis_cover'],
            'jenis_mesin' => $stockData['jenis_mesin'],
            'dr'          => $stockData['dr'],
            'color'       => $stockData['color'],
            'code'        => $stockData['code'],
            'lmd'         => $stockData['lmd'],
            'ttl_cns'     => $changeAmountCns, // Jumlah yang berubah
            'ttl_kg'      => $changeAmountKg, // Jumlah yang berubah
            'admin'       => $stockData['admin'],
            'keterangan'  => $postData['stockNote'], // Catatan dari input
            'created_at'  => date('Y-m-d H:i:s') // Waktu penyimpanan
        ];
        $this->historyCoveringStockModel->insert($historyStock);

        return $this->response->setJSON([
            'success' => true,  // Pastikan ini ada
            'message' => 'Stock berhasil diperbarui',
            'data' => $postData
        ]);
    }

    public function getStock($id)
    {
        $stock = $this->coveringStockModel->find($id);

        if ($stock) {
            return $this->response->setJSON(['success' => true, 'stock' => $stock]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }
    }

    public function updateEditStock()
    {
        // Ambil data JSON dari request
        $json = $this->request->getJSON(true);
        // log_message('debug', 'Data JSON: ' . json_encode($json));
        // Validasi ID stok
        if (!isset($json['id_covering_stock'])) {
            return $this->fail("ID Stok tidak ditemukan!", 400);
        }

        $stockId = $json['id_covering_stock'];

        // Cek apakah data stok ada di database
        $existingStock = $this->coveringStockModel->find($stockId);
        if (!$existingStock) {
            return $this->failNotFound("Stok dengan ID $stockId tidak ditemukan!");
        }

        // Persiapkan data untuk update
        $updateData = [
            'jenis'       => $json['jenis'] ?? $existingStock['jenis'],
            'jenis_benang' => $json['jenis_benang'] ?? $existingStock['jenis_benang'],
            'jenis_cover' => $json['jenis_cover'] ?? $existingStock['jenis_cover'],
            'jenis_mesin' => $json['jenis_mesin'] ?? $existingStock['jenis_mesin'],
            'dr'          => $json['dr'] ?? $existingStock['dr'],
            'color'       => $json['color'] ?? $existingStock['color'],
            'code'        => $json['code'] ?? $existingStock['code'],
            'ttl_kg'      => $json['ttl_kg'] ?? $existingStock['ttl_kg'],
            'ttl_cns'     => $json['ttl_cns'] ?? $existingStock['ttl_cns'],
            'lmd'         => isset($json['lmd']) ? implode(", ", $json['lmd']) : $existingStock['lmd'],
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        // Lakukan update
        $update = $this->coveringStockModel->update($stockId, $updateData);

        if ($update) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Stok berhasil diperbarui!",
                'data'    => $updateData
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Gagal memperbarui stok!"
            ])->setStatusCode(500);
        }
    }

    public function reportPemasukan()
    {
        $selectedDate = $this->request->getGet('date'); // Ambil tanggal dari parameter GET
        $selectedDate2 = $this->request->getGet('date2'); // Ambil tanggal dari parameter GET
        $pemasukan = [];

        if ($selectedDate && $selectedDate2) {
            $pemasukan = $this->historyCoveringStockModel->getPemasukanByDate($selectedDate, $selectedDate2);
        }

        $data = [
            'active' => $this->active,
            'title' => 'Warehouse',
            'role' => $this->role,
            'pemasukan' => $pemasukan,
            'selectedDate' => $selectedDate, // Kirim ke view untuk referensi
            'selectedDate2' => $selectedDate2, // Kirim ke view untuk referensi
        ];

        return view($this->role . '/warehouse/report-pemasukan', $data);
    }


    public function reportPengeluaran()
    {
        $selectedDate = $this->request->getGet('date'); // Ambil tanggal dari parameter GET
        $selectedDate2 = $this->request->getGet('date2');
        $pengeluaran = [];

        if ($selectedDate) {
            $pengeluaran = $this->historyCoveringStockModel->getPengeluaranByDate($selectedDate, $selectedDate2);
        }

        $data = [
            'active' => $this->active,
            'title' => 'Warehouse',
            'role' => $this->role,
            'pengeluaran' => $pengeluaran,
            'selectedDate' => $selectedDate, // Kirim ke view untuk referensi
            'selectedDate2' => $selectedDate2 // Kirim ke view untuk referensi
        ];

        return view($this->role . '/warehouse/report-pengeluaran', $data);
    }

    public function reqschedule()
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


            $keys = $id['no_model'] . '-' . $id['item_type'] . '-' . $id['kode_warna'];

            // Pastikan key belum ada, jika belum maka tambahkan data
            if (!isset($uniqueData[$key])) {

                // Buat array data unik
                $uniqueData[] = [
                    'no_model' => $nomodel,
                    'item_type' => $itemtype,
                    'kode_warna' => $kodewarna,
                    'warna' => $id['warna'],
                    'start_mc' => $id['start_mc'],
                    'qty_celup' => $id['qty_celup'],
                    'no_mesin' => $id['no_mesin'],
                    'id_celup' => $id['id_celup'],
                    'lot_celup' => $id['lot_celup'],
                    'lot_urut' => $id['lot_urut'],
                    'tgl_schedule' => $id['tanggal_schedule'],
                    'last_status' => $id['last_status'],
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

    public function importStokBarangJadi()
    {
        $file = $this->request->getFile('file_excel');
        // Validasi file
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        // Cek ekstensi dan ukuran maksimal (misal max 5MB)
        $allowedExt = ['xlsx', 'xls'];
        $ext = $file->getExtension();
        if (!in_array($ext, $allowedExt)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Format file tidak didukung. Gunakan .xlsx atau .xls');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
        }

        // Simpan sementara
        $uploadDir = WRITEPATH . 'uploads/';
        $filePath = $uploadDir . $file->getName();
        try {
            $file->move($uploadDir, null, true);
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memindahkan file. ' . $e->getMessage());
        }

        // Jalankan import
        try {
            list($successCount, $failures) = $this->importStock($filePath);
        } catch (Exception $e) {
            unlink($filePath);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saat proses import: ' . $e->getMessage());
        }

        // Hapus file setelah selesai
        unlink($filePath);

        // Siapkan pesan
        if ($failures) {
            // Gagal sebagian
            $msg  = "Berhasil import: {$successCount}. Gagal import: " . count($failures) . ".";
            $msg .= '<ul>';
            foreach ($failures as $rowNum => $reason) {
                $msg .= "<li>Baris {$rowNum}: {$reason}</li>";
            }
            $msg .= '</ul>';
            return redirect()->to(base_url($this->role . '/warehouse'))->with('warning', $msg);
        }

        // Semua berhasil
        return redirect()->to(base_url($this->role . '/warehouse'))
            ->with('success', "Import berhasil seluruhnya ({$successCount} baris)");
    }

    /**
     * @return array [int $successCount, array $failures]
     */
    private function importStock(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $successCount = 0;
        $failures = [];

        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            $rowNum = $row->getRowIndex();

            // Validasi minimal
            if (
                count($rowData) < 10
                || empty($rowData[0])
                || empty($rowData[2])
            ) {
                $failures[$rowNum] = 'Data kolom kurang lengkap';
                continue;
            }

            // Mapping data
            try {
                $item = [
                    'jenis'          => $rowData[0],
                    'color'          => $rowData[1],
                    'code'           => $rowData[2],
                    'jenis_mesin'    => $rowData[3],
                    'dr'             => $rowData[4],
                    'jenis_cover'    => $rowData[5],
                    'jenis_benang'   => $rowData[6],
                    'lmd'            => isset($rowData[7]) ? implode(', ', explode(',', $rowData[7])) : null,
                    'ttl_kg'         => (float) ($rowData[8] ?? 0),
                    'ttl_cns'        => (int) ($rowData[9] ?? 0),
                    'admin'          => session()->get('role'),
                ];

                // Cek duplikat
                if ($this->coveringStockModel->getStockByJenisColorCodeMesin(
                    $item['jenis'],
                    $item['color'],
                    $item['code'],
                    $item['jenis_mesin'],
                    $item['dr'],
                    $item['jenis_cover'],
                    $item['jenis_benang']
                )) {
                    $failures[$rowNum] = 'Duplikat data di database';
                    $failures[$rowNum] .= ' (Jenis: ' . $item['jenis'] . ', Color: ' . $item['color'] . ', Code: ' . $item['code'] . ')';
                    continue;
                }

                // Insert
                $this->coveringStockModel->insert($item);
                $successCount++;
            } catch (\Exception $e) {
                $failures[$rowNum] = 'Error: ' . $e->getMessage();
            }
        }

        return [$successCount, $failures];
    }
}
