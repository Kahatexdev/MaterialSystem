<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WarehouseBBModel;
use App\Models\HistoryStockBBModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class CoveringWarehouseBBController extends BaseController
{
    protected $warehouseBBModel;
    protected $historyStockBBModel;
    protected $role;
    protected $active;

    public function __construct()
    {
        $this->warehouseBBModel = new WarehouseBBModel();
        $this->historyStockBBModel = new HistoryStockBBModel();
        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
    }

    public function index()
    {
        $warehouseBB  = $this->warehouseBBModel->getWarehouseBB();

        $data = [
            'title' => 'Warehouse Bahan Baku Covering',
            'active' => $this->active,
            'role' => $this->role,
            'warehouseBB' => $warehouseBB
        ];
        return view($this->role . '/warehousebb/index', $data);
    }

    public function store()
    {
        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $this->request->getPost('kg'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        // Validate required fields
        if (
            empty($data['denier']) ||
            empty($data['jenis_benang']) ||
            empty($data['warna']) ||
            empty($data['kode']) ||
            empty($data['kg']) ||
            empty($data['admin'])
        ) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        // Cek duplikasi berdasarkan denier, jenis_benang, warna, kode
        $existing = $this->warehouseBBModel
            ->where('denier', $data['denier'])
            ->where('jenis_benang', $data['jenis_benang'])
            ->where('warna', $data['warna'])
            ->where('kode', $data['kode'])
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Data dengan kombinasi Denier, Jenis Benang, Warna, dan Kode tersebut sudah ada.');
        }

        // Insert into history stock
        $historyData = [
            'denier' => $data['denier'],
            'jenis' => $data['jenis_benang'],
            'jenis_benang' => $data['jenis_benang'],
            'color' => $data['warna'],
            'code' => $data['kode'],
            'ttl_cns' => 0, // Assuming this is not used in covering
            'ttl_kg' => $data['kg'],
            'admin' => $data['admin'],
            'keterangan' => $data['keterangan']
        ];

        if (!$this->historyStockBBModel->insert($historyData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah data ke history stock.');
        }

        if (!$this->warehouseBBModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah data ke warehouse.');
        }

        return redirect()->back()->with('success', 'Data berhasil ditambahkan ke warehouse Bahan Baku Covering.');
    }

    public function update($id)
    {
        $kgBaru = (float) $this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];
        // dd($data, $id);
        // Ambil data lama
        $dataLama = $this->warehouseBBModel->find($id);
        if (!$dataLama) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Cek apakah kg lama < 0
        if ($dataLama['kg'] < 0) {
            return redirect()->back()->with('error', 'Stok bahan baku saat ini minus! Silakan periksa data sebelumnya.');
        }

        // Hitung selisih untuk history
        $selisihKg = $kgBaru - $dataLama['kg'];
        // dd($selisihKg);
        if ($selisihKg != 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => 0, // Assuming this is not used in covering
                'ttl_kg' => $selisihKg,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        $this->warehouseBBModel->update($id, $data);

        return redirect()->to($this->role . '/warehouseBB')->with('success', 'Data berhasil diupdate.');
    }

    public function pemasukan()
    {
        $idstockbb = $this->request->getPost('idstockbb');
        $kgBaru = (float)$this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'ttl_cns' => $this->request->getPost('ttl_cns'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        if ($kgBaru > 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => $data['ttl_cns'],
                'ttl_kg' => $kgBaru,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        if ($idstockbb && $kgBaru > 0) {
            $stokLama = $this->warehouseBBModel->find($idstockbb);

            if ($stokLama) {
                $kgBaru = $stokLama['kg'] + $kgBaru;
                $this->warehouseBBModel->update($idstockbb, ['kg' => $kgBaru]);

                return redirect()->to($this->role . '/warehouseBB')->with('success', 'Data pemasukan berhasil ditambahkan');
            }

            return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data tidak ditemukan');
        }

        return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data pemasukan gagal ditambahkan');
    }

    public function pengeluaran()
    {
        $idstockbb = $this->request->getPost('idstockbb');
        $kgBaru = (float)$this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'ttl_cns' => $this->request->getPost('ttl_cns'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        if ($kgBaru > 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => $data['ttl_cns'],
                'ttl_kg' => -$kgBaru,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        if ($idstockbb && $kgBaru > 0) {
            // Ambil stok sekarang dulu
            $stok = $this->warehouseBBModel->find($idstockbb);

            if ($stok && $stok['kg'] >= $kgBaru) {
                $this->warehouseBBModel->update($idstockbb, ['kg' => $stok['kg'] - $kgBaru]);

                return redirect()->to($this->role . '/warehouseBB')->with('success', 'Pengeluaran berhasil dikurangi.');
            }
            return redirect()->to($this->role . '/warehouseBB')->with('error', 'Stok tidak mencukupi untuk pengeluaran');
        }

        return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data tidak valid untuk pengeluaran');
    }

    public function importStokBahanBakuJenis()
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
            return redirect()->to(base_url($this->role . '/warehouseBB'))->with('warning', $msg);
        }

        // Semua berhasil
        return redirect()->to(base_url($this->role . '/warehouseBB'))
            ->with('success', "Import berhasil seluruhnya ({$successCount} baris)");
    }

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
                    'denier'          => $rowData[0],
                    'jenis_benang'    => $rowData[1],
                    'warna'           => $rowData[2],
                    'kode'            => $rowData[3],
                    'kg'              => $rowData[4],
                    'keterangan'      => $rowData[5] ?? '',
                    'admin'           => session()->get('username'),
                ];

                // Cek duplikat
                if ($this->warehouseBBModel->getStockByDenierJenisWarna(
                    $item['denier'],
                    $item['jenis_benang'],
                    $item['warna'],
                    $item['kode'],
                    $item['kg']
                )) {
                    $failures[$rowNum] = 'Duplikat data di database';
                    $failures[$rowNum] .= ' (Denier: ' . $item['denier'] . ', Jenis Benang: ' . $item['jenis_benang'] . ', Warna: ' . $item['warna'] . ', Kode: ' . $item['kode'] . ')';
                    continue;
                }

                // Insert
                $this->warehouseBBModel->insert($item);
                $successCount++;
            } catch (\Exception $e) {
                $failures[$rowNum] = 'Error: ' . $e->getMessage();
            }
        }

        return [$successCount, $failures];
    }

    public function importStokBahanBaku()
    {
        $file = $this->request->getFile('file_excel');
        if (! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            return redirect()->back()->with('error', 'File tidak valid atau gagal di-upload.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, ['xls', 'xlsx'])) {
            return redirect()->back()->with('error', 'Format file harus .xls atau .xlsx');
        }

        $reader      = $ext === 'xlsx' ? new Xlsx() : new Xls();
        $spreadsheet = $reader->load($file->getTempName());

        $sheetNames = $spreadsheet->getSheetNames();

        // mapping header->field
        $headerRow = 3;
        $mapStock = [
            'DENIER'       => 'denier',
            'JENIS BENANG' => 'jenis_benang',
            'WARNA'        => 'warna',
            'KODE'         => 'kode',
            'CONES'        => 'ttl_cns',
            'KG'           => 'ttl_kg',
            'KETERANGAN'   => 'keterangan',
        ];

        $mapHistory = [
            'DENIER'       => 'denier',
            'JENIS BENANG' => 'jenis_benang',
            'WARNA'        => 'color',
            'KODE'         => 'code',
            'CONES'        => 'ttl_cns',
            'KG'           => 'ttl_kg',
            'KETERANGAN'   => 'keterangan',
        ];

        $admin    = session()->get('username') ?? session()->get('email');
        $nowLabel = 'Import ' . date('Y-m-d H:i:s');

        // untuk agregasi stok
        $agg = [];          // [ idstockbb => ['origKg'=>..., 'origCns'=>..., 'deltaKg'=>..., 'deltaCns'=>...] ]
        $historyData = [];
        $errors      = [];

        foreach ($sheetNames as $name) {
            $sheet = $spreadsheet->getSheetByName($name);
            if (! $sheet) continue;

            $mult = (strtoupper($name) === 'PENGELUARAN') ? -1 : 1;

            // ambil tanggal import sekali
            if (empty($tanggalImport)) {
                $tanggalImport = $sheet->getCell('B2')->getFormattedValue();
                if (empty($tanggalImport)) {
                    return redirect()->back()
                        ->with('error', "Tanggal import tidak ditemukan di B2 pada sheet {$name}");
                }
            }

            $rows      = $sheet->toArray(null, true, true, true);
            $rawHeader = array_map('strtoupper', array_map('trim', $rows[$headerRow]));

            foreach ($rows as $i => $row) {
                if ($i <= $headerRow || empty(array_filter($row))) continue;

                // mapping stok
                $item = [
                    'admin'      => $admin,
                    'keterangan' => "{$nowLabel} [{$name}]",
                    'created_at' => \DateTime::createFromFormat('d/m/Y', $tanggalImport)
                        ? \DateTime::createFromFormat('d/m/Y', $tanggalImport)->format('Y-m-d 00:00:00')
                        : $tanggalImport,
                ];
                foreach ($rawHeader as $col => $hd) {
                    if (! isset($mapStock[$hd])) continue;
                    $fld = $mapStock[$hd];
                    $val = $row[$col];
                    // normalize angka
                    if (in_array($fld, ['ttl_kg', 'ttl_cns'], true)) {
                        $c = str_replace(',', '.', $val);
                        $val = is_numeric($c) ? (float) $c : 0;
                    }
                    $item[$fld] = $val;
                }
                if (empty($item['denier']) || empty($item['jenis_benang']) || empty($item['warna'])) {
                    $errors[] = "Sheet {$name} baris {$i}: data tidak lengkap (denier/jenis/warna/kode).";
                    continue; // skip jika data tidak lengkap
                }
                // cari stok eksisting
                $stock = $this->warehouseBBModel
                    ->where('denier', $item['denier'])
                    ->where('jenis_benang', $item['jenis_benang'])
                    ->where('warna', $item['warna'])
                    ->where('kode', $item['kode'])
                    ->first();
                if (! $stock) {
                    $errors[] = "Sheet {$name} baris {$i}: stok tidak ditemukan untuk kode {$item['kode']}.";
                    continue; // skip jika stok tidak ditemukan
                }

                // ambil delta absolut & terapkan multiplier
                $inKg  = abs($item['ttl_kg']  ?? 0);
                $inCns = abs($item['ttl_cns'] ?? 0);
                $deltaKg  = $mult * $inKg;
                $deltaCns = $mult * $inCns;

                $id = $stock['idstockbb'];
                // simpan orig + akumulasi delta
                if (! isset($agg[$id])) {
                    $agg[$id] = [
                        'origKg'   => $stock['kg']    ?? 0,
                        'origCns'  => $stock['ttl_cns'] ?? 0,
                        'deltaKg'  => 0,
                        'deltaCns' => 0,
                    ];
                }
                $agg[$id]['deltaKg']  += $deltaKg;
                $agg[$id]['deltaCns'] += $deltaCns;

                // mapping history (pakai mapHistory)
                $hist = [
                    'admin'      => $admin,
                    'keterangan' => "{$nowLabel} [{$name}]",
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                foreach ($rawHeader as $col => $hd) {
                    if (! isset($mapHistory[$hd])) continue;
                    $fld = $mapHistory[$hd];
                    $val = $row[$col];
                    if (in_array($fld, ['ttl_kg', 'ttl_cns'], true)) {
                        $c = str_replace(',', '.', $val);
                        $val = is_numeric($c) ? (float) $c : 0;
                    }
                    $hist[$fld] = $val;
                }
                // ganti nilai history jadi signed delta
                $hist['ttl_kg']  = $deltaKg;
                $hist['ttl_cns'] = $deltaCns;

                $historyData[] = $hist;
            }
        }

        // build updateData sekali jalan
        $updateData = [];
        foreach ($agg as $id => $v) {
            $newKg  = $v['origKg']  + $v['deltaKg'];
            $newCns = $v['origCns'] + $v['deltaCns'];

            // optional: validasi stok negatif
            if ($newKg < 0 || $newCns < 0) {
                $errors[] = "Stock {$hist['jenis_benang']} {$hist['denier']} {$hist['color']} {$hist['code']} Stock tidak mencukupi.";
                continue;
            }

            $updateData[] = [
                'idstockbb' => $id,
                'kg'        => $newKg,
            ];
        }

        // simpan ke DB
        $db = \Config\Database::connect();
        $db->transStart();
        if (! empty($updateData)) {
            $this->warehouseBBModel->updateBatch($updateData, 'idstockbb');
        }
        if (! empty($historyData)) {
            $this->historyStockBBModel->insertBatch($historyData);
        }
        $db->transComplete();

        // persiapkan flash message
        $flash = [];
        if (! empty($errors)) {
            return redirect()->back()->with('warning', implode('<br>', $errors));
        }

        return redirect()->back()->with('success', 'Import stok bahan baku berhasil untuk semua data.');
    }


    public function deleteBahanBakuCov($id)
    {
        $this->warehouseBBModel->delete($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function templateStokBahanBaku()
    {
        $stok = $this->warehouseBBModel->findAll();
        // dd ($stok);
        // Define headings based on mapping
        $headers = [
            'A' => 'DENIER',
            'B' => 'JENIS BENANG',
            'C' => 'WARNA',
            'D' => 'KODE',
            'E' => 'CONES',
            'F' => 'KG',
            'G' => 'KETERANGAN',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // PEMASUKAN sheet dengan data stok
        $sheetIn = $spreadsheet->getActiveSheet();
        $sheetIn->setTitle('PEMASUKAN');
        $this->applyTemplateFormat($sheetIn, $headers, $stok);

        // PENGELUARAN sheet juga menampilkan data stok
        $sheetOut = $spreadsheet->createSheet();
        $sheetOut->setTitle('PENGELUARAN');
        $this->applyTemplateFormat($sheetOut, $headers, $stok);

        // Prepare download
        $filename = 'TEMPLATE_IMPORT_BAHAN_BAKU_' . date('Ymd_His') . '.xlsx';
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
        $sheet->setCellValue('A1', 'TEMPLATE DATA STOK BAHAN BAKU UNTUK ' . strtoupper($sheet->getTitle()));
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'Tanggal Import (dd/mm/yyyy):');
        $sheet->setCellValue('B2', date('d/m/Y'));
        $sheet->getStyle('A1:G2')->getFont()->setBold(true);

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
            $sheet->setCellValue("A{$row}", $item['denier'] ?? '');
            $sheet->setCellValue("B{$row}", $item['jenis_benang'] ?? '');
            $sheet->setCellValue("C{$row}", $item['warna'] ?? '');
            $sheet->setCellValue("D{$row}", $item['kode'] ?? '');
            $sheet->setCellValue("E{$row}", 0);
            $sheet->setCellValue("F{$row}", 0);
            $sheet->setCellValue("G{$row}", $item['keterangan'] ?? '');
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
}
