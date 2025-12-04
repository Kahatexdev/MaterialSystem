<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Exception;



class CoveringWarehouseController extends BaseController
{



    public function __construct()
    {
        helper('filesystem');

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
        // 1. Ambil pilihan unik
        $jenisOptions        = $this->coveringStockModel->select('jenis')->distinct()->orderBy('jenis')->findColumn('jenis');
        $benangOptions       = $this->coveringStockModel->select('jenis_benang')->distinct()->orderBy('jenis_benang')->findColumn('jenis_benang');
        $mesinOptions        = $this->coveringStockModel->select('jenis_mesin')->distinct()->orderBy('jenis_mesin')->findColumn('jenis_mesin');

        // 2. Baca filter dari GET
        $fJenis     = $this->request->getGet('jenis')        ?? '';
        $fBenang    = $this->request->getGet('jenis_benang') ?? '';
        $fMesin     = $this->request->getGet('jenis_mesin')          ?? '';

        // 3. Query dengan kondisi dinamis
        $builder = $this->coveringStockModel
            ->select('stock_covering.*, IF(stock_covering.ttl_kg > 0, "ada", "habis") AS status')
            ->orderBy('ttl_kg', 'DESC')
            ->orderBy('ttl_cns', 'DESC');

        if ($fJenis) {
            $builder->where('jenis', $fJenis);
        }
        if ($fBenang) {
            $builder->where('jenis_benang', $fBenang);
        }
        if ($fMesin) {
            $builder->where('jenis_mesin', $fMesin);
        }

        $stok = $builder->findAll();

        return view($this->role . '/warehouse/index', [
            'active'         => $this->active,
            'title'          => 'Warehouse',
            'role'           => $this->role,
            'stok'           => $stok,
            // data untuk filter dropdown
            'jenisOptions'   => $jenisOptions,
            'benangOptions'  => $benangOptions,
            'mesinOptions'   => $mesinOptions,
            // nilai yang sedang dipilih
            'fJenis'         => $fJenis,
            'fBenang'        => $fBenang,
            'fMesin'         => $fMesin,
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
                    'color'    => $rowData[1],
                    'code'           => $rowData[2],
                    'jenis_mesin'            => $rowData[3],
                    'dr'              => (float) ($rowData[4] ?? 0),
                    'jenis_cover'      => $rowData[5],
                    'jenis_benang'    => $rowData[6],
                    'lmd'             => isset($rowData[7]) ? implode(', ', array_map('trim', explode(',', $rowData[7]))) : null,
                    'ttl_kg'          => (float) ($rowData[8] ?? 0),
                    'ttl_cns'         => (float) ($rowData[9] ?? 0),
                    'admin'           => session()->get('username'),
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

    public function importStokCovering()
    {
        // ---------- 1. VALIDASI FILE UPLOAD ----------
        $file = $this->request->getFile('file_excel');

        // Antisipasi kalau field name salah / tidak ada file
        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan. Pastikan input name="file_excel".');
        }

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal di-upload.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['xls', 'xlsx'], true)) {
            return redirect()->back()->with('error', 'Format file harus .xls atau .xlsx');
        }

        // ---------- 2. BACA FILE EXCEL DENGAN TRY/CATCH ----------
        try {
            $reader = ($ext === 'xlsx')
                ? new \PhpOffice\PhpSpreadsheet\Reader\Xlsx()
                : new \PhpOffice\PhpSpreadsheet\Reader\Xls();

            // Kalau file besar, bisa matikan kalkulasi & format untuk hemat memori
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($file->getTempName());
        } catch (\Throwable $e) {
            log_message('error', 'IMPORT COVERING: Gagal membaca file excel. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'File Excel tidak bisa dibaca. Pastikan formatnya benar.');
        }

        $headerRow = 3;
        $mapping   = [
            'JENIS BARANG' => 'jenis',
            'WARNA'        => 'color',
            'KODE'         => 'code',
            'JENIS COVER'  => 'jenis_cover',
            'JENIS BENANG' => 'jenis_benang',
            'JENIS MESIN'  => 'jenis_mesin',
            'DR'           => 'dr',
            'LMD'          => 'lmd',
            'STOK KG'      => 'ttl_kg',
            'STOK CONES'   => 'ttl_cns',
            'KETERANGAN'   => 'keterangan',
        ];

        $admin    = session()->get('username') ?? session()->get('email') ?? 'SYSTEM';
        $errors   = [];
        $updates  = [];
        $history  = [];

        $sheet     = $spreadsheet->getActiveSheet();
        $sheetName = $sheet->getTitle();

        // ---------- 3. TANGKAP TANGGAL IMPORT ----------
        $rawDate = trim((string)$sheet->getCell('B2')->getValue());
        $tanggal = $this->parseDate($rawDate, $errors, $sheetName);
        if (!$tanggal) {
            // Kalau tanggal invalid, langsung stop supaya tidak tersimpan setengah-setengah
            return redirect()->back()->with('error', 'Tanggal tidak valid di sheet aktif (B2).');
        }

        // ---------- 4. AMBIL SEMUA ROW ----------
        $rows = $sheet->toArray(null, true, true, true);

        if (!isset($rows[$headerRow])) {
            return redirect()->back()->with('error', "Header tidak ditemukan di baris {$headerRow}.");
        }

        $rawHeader = $this->normalizeHeader($rows[$headerRow]);

        // Mapping kolom Excel (A,B,C,...) -> field DB (jenis, color, dst)
        $colToKey = [];
        foreach ($rawHeader as $col => $heading) {
            if (isset($mapping[$heading])) {
                $colToKey[$col] = $mapping[$heading];
            }
        }

        if (empty($colToKey)) {
            return redirect()->back()->with('error', 'Header tidak dikenali. Pastikan judul kolom sesuai template (JENIS BARANG, WARNA, KODE, dst).');
        }

        // ---------- 5. LOOP BARIS DATA ----------
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex <= $headerRow) {
                continue;
            }

            // Skip kalau semua kolom kosong
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            // Build data baru dari file
            $new = [
                'admin'      => $admin,
                'created_at' => "{$tanggal} 00:00:00",
            ];

            foreach ($colToKey as $col => $key) {
                $val = $row[$col] ?? null;
                if (in_array($key, ['ttl_kg', 'ttl_cns'], true)) {
                    $new[$key] = $this->parseNumber($val);
                } else {
                    $new[$key] = is_null($val) ? null : trim((string)$val);
                }
            }

            // Required fields (identity)
            if (empty($new['jenis']) || empty($new['code']) || empty($new['dr'])) {
                $errors[] = "Sheet {$sheetName} baris {$rowIndex}: 'jenis' atau 'code' atau 'dr' kosong.";
                continue;
            }

            // Build where clause TANPA ttl_kg / ttl_cns
            $where = $this->buildWhereClause($new);

            log_message('debug', "IMPORT COVERING: mencari stok (sheet {$sheetName} row {$rowIndex}) where=" . json_encode($where));

            // Cari stok yang cocok
            try {
                $stock = $this->coveringStockModel->where($where)->first();
            } catch (\Throwable $e) {
                // Kalau query where aneh (misal karena karakter spesial), catat dan lanjut baris lain
                $errors[] = "Sheet {$sheetName} baris {$rowIndex}: gagal mencari stok. (" . $e->getMessage() . ")";
                log_message('error', 'IMPORT COVERING: Error saat mencari stok: ' . $e->getMessage());
                continue;
            }

            if (!$stock) {
                log_message('debug', "IMPORT COVERING: Tidak ditemukan stok untuk baris {$rowIndex}. data file=" . json_encode($new));
                $errors[] = "Sheet {$sheetName} baris {$rowIndex}: Tidak ada stok yang cocok.";
                continue;
            }

            $id      = (int) $stock['id_covering_stock'];
            $old_kg  = (float) ($stock['ttl_kg'] ?? 0);
            $old_cns = (float) ($stock['ttl_cns'] ?? 0);
            $new_kg  = (float) ($new['ttl_kg'] ?? 0);
            $new_cns = (float) ($new['ttl_cns'] ?? 0);

            // Kalau tidak ada perubahan, skip
            if ($new_kg == $old_kg && $new_cns == $old_cns) {
                continue;
            }

            $delta_kg  = $new_kg - $old_kg;
            $delta_cns = $new_cns - $old_cns;

            // Siapkan record update
            $updates[] = [
                'id_covering_stock' => $id,
                'ttl_kg'            => $new_kg,
                'ttl_cns'           => $new_cns,
            ];

            // Siapkan history
            $history[] = [
                'no_model'      => null,
                'jenis'         => $new['jenis'],
                'jenis_benang'  => $new['jenis_benang'] ?? null,
                'jenis_cover'   => $new['jenis_cover'] ?? null,
                'color'         => $new['color'] ?? null,
                'code'          => $new['code'],
                'lmd'           => $new['lmd'] ?? null,
                'ttl_cns'       => $delta_cns,
                'ttl_kg'        => $delta_kg,
                'admin'         => $admin,
                'keterangan'    => $new['keterangan'] ?? '',
                'created_at'    => "{$tanggal} 00:00:00",
            ];
        }

        // ---------- 6. SIMPAN KE DATABASE DENGAN TRANSAKSI + CHUNK ----------
        if (empty($updates) && empty($history)) {
            // Tidak ada data yang berubah sama sekali
            $msg = $errors
                ? 'Tidak ada data yang diubah. Beberapa baris bermasalah: <br>' . implode('<br>', $errors)
                : 'Tidak ada data yang perlu di-update.';
            return redirect()->back()->with('warning', $msg);
        }

        $db = \Config\Database::connect();
        $db->transException(true); // kalau ada error query, lempar exception
        $db->transStart();

        try {
            // Biar aman kalau data besar, kita chunk per 500 row
            if (!empty($updates)) {
                $this->chunkedUpdateBatch($this->coveringStockModel, $updates, 'id_covering_stock', 500);
            }

            if (!empty($history)) {
                $this->chunkedInsertBatch($this->historyCoveringStockModel, $history, 500);
            }

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'IMPORT COVERING: Gagal menyimpan ke DB. Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data ke database: ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            log_message('error', 'IMPORT COVERING: transStatus false tanpa exception eksplisit.');
            return redirect()->back()->with('error', 'Gagal menyimpan data (transaksi DB gagal).');
        }

        // ---------- 7. RESPONSE ----------
        if ($errors) {
            $msgType = empty($updates) ? 'error' : 'warning';
            return redirect()->back()->with($msgType, implode('<br>', $errors));
        }

        return redirect()->to(base_url($this->role . '/warehouse'))
            ->with('success', 'Data berhasil diimpor.');
    }

    /* ================== HELPER ================== */

    private function normalizeHeader(array $row): array
    {
        return array_map(function ($h) {
            return strtoupper(trim((string)$h));
        }, $row);
    }

    private function parseNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        $s = trim((string)$value);
        $s = str_replace(' ', '', $s); // hapus spasi

        // Format "1.234,56" -> "1234.56"
        if (substr_count($s, '.') > 0 && strpos($s, ',') !== false) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        } else {
            // hanya koma sebagai desimal "0,69"
            if (strpos($s, ',') !== false && strpos($s, '.') === false) {
                $s = str_replace(',', '.', $s);
            }
        }

        return is_numeric($s) ? (float)$s : 0.0;
    }

    private function parseDate($raw, array &$errors, string $sheet)
    {
        $raw = trim((string)$raw);

        if ($raw === '') {
            $errors[] = "Sheet {$sheet}: Tanggal import kosong (B2).";
            return null;
        }

        // dd/mm/yyyy
        $dt = \DateTime::createFromFormat('d/m/Y', $raw);
        if ($dt instanceof \DateTime) {
            return $dt->format('Y-m-d');
        }

        // yyyy-mm-dd
        $dt = \DateTime::createFromFormat('Y-m-d', $raw);
        if ($dt instanceof \DateTime) {
            return $dt->format('Y-m-d');
        }

        // Excel serial
        if (is_numeric($raw)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($raw)
                    ->format('Y-m-d');
            } catch (\Throwable $e) {
                // fallback ke error
            }
        }

        $errors[] = "Sheet {$sheet}: Tanggal import tidak valid (B2). Nilai mentah: {$raw}";
        return null;
    }

    private function buildWhereClause(array $rec): array
    {
        $normalize = function ($v) {
            if (is_null($v)) return null;
            $s = trim((string)$v);
            return $s === '' ? null : $s;
        };

        $where = [
            'jenis'        => $normalize($rec['jenis'] ?? null),
            'color'        => $normalize($rec['color'] ?? null),
            'code'         => $normalize($rec['code'] ?? null),
            'jenis_cover'  => $normalize($rec['jenis_cover'] ?? null),
            'jenis_benang' => $normalize($rec['jenis_benang'] ?? null),
            'jenis_mesin'  => $normalize($rec['jenis_mesin'] ?? null),
            'dr'           => $normalize($rec['dr'] ?? null),
        ];

        // Buang key yang null/empty supaya where() tidak pakai kondisi kosong
        return array_filter($where, function ($value) {
            return !is_null($value) && $value !== '';
        });
    }

    /**
     * Update batch dengan chunk untuk menghindari error jika data besar.
     *
     * @param \CodeIgniter\Model $model
     * @param array $rows
     * @param string $pk
     * @param int $chunkSize
     * @return void
     */
    private function chunkedUpdateBatch($model, array $rows, string $pk, int $chunkSize = 500): void
    {
        $chunks = array_chunk($rows, $chunkSize);
        foreach ($chunks as $chunk) {
            $model->updateBatch($chunk, $pk);
        }
    }

    /**
     * Insert batch dengan chunk.
     *
     * @param \CodeIgniter\Model $model
     * @param array $rows
     * @param int $chunkSize
     * @return void
     */
    private function chunkedInsertBatch($model, array $rows, int $chunkSize = 500): void
    {
        $chunks = array_chunk($rows, $chunkSize);
        foreach ($chunks as $chunk) {
            $model->insertBatch($chunk);
        }
    }


    public function deleteStokBarangJadi($id)
    {
        // Hanya terima AJAX
        if (! $this->request->isAJAX()) {
            return redirect()->back();
        }

        $stock = $this->coveringStockModel->find($id);
        if (!$stock) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Data stok tidak ditemukan.']);
        }

        if ($this->coveringStockModel->delete($id)) {
            return $this->response
                ->setStatusCode(200)
                ->setJSON(['success' => true, 'message' => 'Data stok berhasil dihapus.']);
        } else {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Gagal menghapus data stok.']);
        }
    }

    public function templateStokBarangJadi()
    {
        $stok = $this->coveringStockModel->orderBy('jenis_benang ASC')->orderBy('dr ASC')->findAll();
        // Define headings based on mapping
        $headers = [
            'A' => 'JENIS BARANG',
            'B' => 'WARNA',
            'C' => 'KODE',
            'D' => 'JENIS MESIN',
            'E' => 'DR',
            'F' => 'JENIS COVER',
            'G' => 'JENIS BENANG',
            'H' => 'LMD',
            'I' => 'STOK CONES',
            'J' => 'STOK KG',
            'K' => 'KETERANGAN',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // STOCK sheet dengan data stok
        $sheetIn = $spreadsheet->getActiveSheet();
        $sheetIn->setTitle('STOCK');
        $this->applyTemplateFormat($sheetIn, $headers, $stok);

        // Prepare download
        $filename = 'TEMPLATE_IMPORT_STOK_COVERING_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Apply header, styling, borders, and data to a sheet
     */
    protected function applyTemplateFormat($sheet, array $headers, array $data)
    {
        // Instruction and date
        $sheet->setCellValue('A1', 'TEMPLATE DATA STOK BARANG JADI (COVERING)');
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A2', 'Tanggal Import (dd/mm/yyyy):');
        $sheet->setCellValue('B2', date('d/m/Y'));
        $sheet->getStyle('A1:K2')->getFont()->setBold(true);

        // Header row
        foreach ($headers as $col => $title) {
            $sheet->setCellValue("{$col}3", $title);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // apply styling A1
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        // blue sky background color
        $sheet->getStyle('A1')->getFill()->getStartColor()->setARGB('87CEEB'); // Sky blue background
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // styling a2:b2
        $sheet->getStyle('A2:B2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        // green tua tapi jangan ketuaan background color
        $sheet->getStyle('A2:B2')->getFill()->getStartColor()->setARGB('228B22'); // Forest green background
        // Apply header styling (background color and bold font)
        $headerRange = 'A3:' . array_key_last($headers) . '3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF00'); // Yellow background

        // Fill data rows
        $startRow = 4;
        $row = $startRow;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item['jenis'] ?? '');
            $sheet->setCellValue("B{$row}", $item['color'] ?? '');
            $sheet->setCellValue("C{$row}", $item['code'] ?? '');
            $sheet->setCellValue("D{$row}", $item['jenis_mesin'] ?? '');
            $sheet->setCellValue("E{$row}", $item['dr'] ?? '');
            $sheet->setCellValue("F{$row}", $item['jenis_cover'] ?? '');
            $sheet->setCellValue("G{$row}", $item['jenis_benang'] ?? '');
            $sheet->setCellValue("H{$row}", $item['lmd'] ?? '');
            $sheet->setCellValue("I{$row}", 0);
            $sheet->setCellValue("J{$row}", 0);
            $sheet->setCellValue("K{$row}", '');
            $sheet->setCellValue("L{$row}", '');
            $row++;
        }

        // Determine last row for borders
        $lastRow = max($row - 1, 3);
        $fullRange = 'A3:' . array_key_last($headers) . $lastRow;

        // Apply borders to all cells in range
        $sheet->getStyle($fullRange)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        // set alignment center for all cells in range
        $sheet->getStyle($fullRange)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public function getStockByJenis($jenis)
    {
        $stok = $this->coveringStockModel->getStockByJenis($jenis);

        $data = [
            'role' => $this->role,
            'title' => 'Stock ' . ucfirst($jenis),
            'active' => $this->active,
            'jenis' => $jenis,
            'stok' => $stok
        ];
        return view($this->role . '/warehouse/stock-' . $jenis, $data);
    }

    public function getStockByMesin($mesin)
    {
        $stok = $this->coveringStockModel->getStockByMesin($mesin);

        $data = [
            'role' => $this->role,
            'title' => 'Stock ' . ucfirst($mesin),
            'active' => $this->active,
            'mesin' => $mesin,
            'stok' => $stok
        ];
        return view($this->role . '/warehouse/stockpermesin', $data);
    }
}
