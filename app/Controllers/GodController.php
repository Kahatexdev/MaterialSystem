<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\PemasukanModel;
use App\Models\StockModel;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\ClusterModel;
use App\Models\MasterWarnaBenangModel;
use App\Models\MasterMaterialModel;
use App\Models\OtherBonModel;
use App\Models\PemesananModel;
use App\Models\TotalPemesananModel;
use App\Models\PengeluaranModel;
use App\Models\PoTambahanModel;
use App\Models\TotalPoTambahanModel;




class GodController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $stockModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $pemasukanModel;
    protected $masterOrderModel;
    protected $materialModel;
    protected $clusterModel;
    protected $masterWarnaBenangModel;
    protected $request;
    protected $masterMaterialModel;
    protected $otherBonModel;
    protected $pemesananModel;
    protected $totalPemesananModel;
    protected $pengeluaranModel;
    protected $poTambahanModel;
    protected $totalPoTambahanModel;


    public function __construct()
    {

        $this->stockModel = new StockModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->clusterModel = new ClusterModel();
        $this->masterWarnaBenangModel = new MasterWarnaBenangModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->otherBonModel = new OtherBonModel();
        $this->pemesananModel = new PemesananModel();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->poTambahanModel = new PoTambahanModel();
        $this->totalPoTambahanModel = new TotalPoTambahanModel();
        $this->request = \Config\Services::request();

        $this->role = session()->get('role');

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

        $data = [
            'role' => $this->role,
            'title' => 'God Monitoring',
            'active' => $this->active,
        ];
        return view($this->role . '/god/importStock', $data);
    }
    private function parseExcelDate($value)
    {
        if (!$value) return null;

        if (is_numeric($value)) {
            return date('Y-m-d', ExcelDate::excelToTimestamp($value));
        }

        if (strtotime($value)) {
            return date('Y-m-d', strtotime($value));
        }

        return null;
    }


    public function importStock()
    {
        try {
            $file = $this->request->getFile('fileExcel');
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                log_message('error', 'ImportStock: file tidak valid atau sudah dipindahkan.');
                return $this->response->setJSON([
                    'status'   => 'error',
                    'message'  => 'File tidak valid atau sudah dipindahkan.',
                    'errorMsg' => 'File tidak valid atau sudah dipindahkan.'
                ]);
            }

            // Simpan file
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $path = WRITEPATH . 'uploads/' . $newName;

            // Baca Excel
            $rows        = IOFactory::load($path)->getActiveSheet()->toArray();
            $scheduleMdl = $this->scheduleCelupModel;
            $outCelupMdl = $this->outCelupModel;

            $scheduleMdl   = $this->scheduleCelupModel;
            $outCelupMdl   = $this->outCelupModel;
            $pemasukanMdl  = $this->pemasukanModel;
            $stockMdl      = $this->stockModel;
            $clusterMdl    = $this->clusterModel;

            $count     = 0;
            $errorLogs = [];

            function normalizeItemType(string $rawType): string
            {
                // mapping kata → singkatan
                $map = [
                    'COTTON'  => 'CTN',
                    'ORGANIC' => 'ORG',
                    'SPNDX' => 'SPDX',
                    // tambahkan mapping lain kalau perlu
                ];
                // lakukan penggantian untuk setiap mapping
                foreach ($map as $search => $replace) {
                    // \b supaya hanya kata utuh; i supaya case-insensitive
                    $rawType = preg_replace(
                        '/\b' . preg_quote($search, '/') . '\b/i',
                        $replace,
                        $rawType
                    );
                }
                return $rawType;
            }

            foreach (array_slice($rows, 3) as $i => $row) {
                $line = $i + 4;
                if (empty($row[1]) || empty($row[6])) {
                    $msg = "Baris $line dilewati: cluster/no_model kosong.";
                    log_message('warning', 'ImportStock: ' . $msg);
                    $errorLogs[] = $msg;
                    continue;
                }

                // prepare data fields
                $noModel    = trim($row[6]);
                $rawType = trim($row[9]);
                $kodeWarna  = trim($row[10]);
                $color      = trim($row[11]);

                try {
                    // parsing tanggal
                    $delAwal  = $this->parseExcelDate($row[7]);
                    $delAkhir = $this->parseExcelDate($row[8]);

                    // cek referensi master order
                    $masterOrder = $this->masterOrderModel
                        ->where('no_model', $noModel)
                        ->where('delivery_awal', $delAwal)
                        ->first();

                    if (! $masterOrder) {
                        $msg = sprintf(
                            "Baris %d dilewati: master order tidak ditemukan (no_model=%s, delivery_awal=%s)",
                            $line,
                            $noModel,
                            $delAwal
                        );
                        log_message('warning', 'ImportStock: ' . $msg);
                        $errorLogs[] = $msg;
                        continue;
                    }

                    $normalizedType = normalizeItemType($rawType);
                    // normalisasi kode warna
                    if (preg_match('/LB\s\d+-[\d-]+$/', $kodeWarna)) {
                        // Contoh: LB 123-456 jadi LB.00123-456
                        $kodeWarna = preg_replace('/LB\s(\d+-[\d-]+)$/', 'LB.00$1', $kodeWarna);
                    }
                    if (preg_match('/^(LC|LCJ|KHT|KP|C|KPM)\s+[\d-]+(?:-[\d-]+)*$/', $kodeWarna)) {
                        // Tangani LCJ 00130-1-1 dan variasi lain
                        $kodeWarna = preg_replace('/^([A-Z]+)\s+(.+)$/', '$1.$2', $kodeWarna);
                    }

                    // cek material
                    $material = $this->materialModel
                        ->where('id_order', $masterOrder['id_order'])
                        ->where('item_type', $normalizedType)
                        ->where('kode_warna', $kodeWarna)
                        ->first();
                    // var_dump($normalizedType, $kodeWarna);
                    if (! $material) {
                        $msg = sprintf(
                            "Baris %d dilewati: material tidak ditemukan (item_type=%s, kode_warna=%s)",
                            $line,
                            $normalizedType,
                            $kodeWarna
                        );
                        log_message('warning', 'ImportStock: ' . $msg);
                        $errorLogs[] = $msg;
                        continue;
                    }

                    // Jika kg_celup = 0, baris dilewati
                    $kgCelup = floatval(str_replace(',', '.', $row[12]));
                    if ($kgCelup == 0) {
                        $msg = "Baris $line dilewati: kg_celup = 0.";
                        log_message('warning', 'ImportStock: ' . $msg);
                        $errorLogs[] = $msg;
                        continue;
                    }

                    // Insert schedule_celup
                    $scheduleMdl->insert([
                        'no_model'         => $noModel,
                        'item_type'        => $normalizedType,
                        'kode_warna'       => $kodeWarna,
                        'warna'            => $material['color'],
                        'kg_celup'         => $kgCelup,
                        'lot_urut'         => 1,
                        'lot_celup'        => trim(ltrim($row[16], ', ')),
                        'tanggal_schedule' => date('Y-m-d'),
                        'tanggal_kelos'    => date('Y-m-d'),
                        'last_status'      => 'sent',
                        'ket_daily_cek'    => 'Kelos (' . date('Y-m-d') . ')',
                        'po_plus'          => '0',
                        'user_cek_status'  => session()->get('role'),
                        'created_at'       => date('Y-m-d H:i:s'),
                        'updated_at'       => date('Y-m-d H:i:s'),
                    ]);

                    $idCelup = $scheduleMdl->getInsertID();

                    // Ambil nilai krg (jumlah bagian)
                    $krg = intval(trim($row[14]));

                    $clusterName = strtoupper(trim($row[1]));

                    // Cek cluster dulu
                    if (!$this->clusterModel->where('nama_cluster', $clusterName)->first()) {
                        $msg = sprintf(
                            "Baris %d dilewati: cluster '%s' tidak ditemukan.",
                            $line,
                            $clusterName
                        );
                        log_message('warning', 'ImportStock: ' . $msg);
                        $errorLogs[] = $msg;
                        continue;
                    }

                    // Hanya proceed kalau ada yang dikirim
                    if ($kgCelup > 0 && $krg > 0) {
                        // Hitung porsi per bagian
                        $partKg    = $kgCelup / $krg;
                        $partCones = floatval(str_replace(',', '.', $row[13])) / $krg;

                        // Loop sebanyak $krg kali
                        for ($i = 0; $i < $krg; $i++) {
                            $outCelupMdl->insert([
                                'id_celup'    => $idCelup,
                                'no_model'    => $noModel,
                                'l_m_d'       => floatval(str_replace(',', '.', $row[3])),
                                'kgs_kirim'   => number_format($partKg, 2, '.', ''),         // bulatkan sesuai kebutuhan
                                'cones_kirim' => round($partCones),      // bulatkan sesuai kebutuhan
                                'lot_kirim'   => trim(ltrim($row[16], ', ')), // atau sesuaikan bila lot unik tiap iterasi
                                'ganti_retur' => 0,
                                'admin'       => session()->get('username'),
                                'created_at'  => date('Y-m-d H:i:s'),
                                'updated_at'  => date('Y-m-d H:i:s'),
                            ]);
                            $idOutCelup = $outCelupMdl->getInsertID();

                            // 2) Siapkan data stock unique key
                            $stockKey = [
                                'no_model'     => $noModel,
                                'item_type'    => $normalizedType,
                                'kode_warna'   => $kodeWarna,
                                'warna'        => $material['color'],
                                'lot_stock'    => trim(ltrim($row[16], ', '))
                            ];

                            // 3) Cek existing stock
                            $existing = $stockMdl->where($stockKey)->first();
                            if ($existing) {
                                // update qty
                                $newKgs = $existing['kgs_in_out'] + round($partKg, 2);
                                $newCns = $existing['cns_in_out'] + round($partCones);
                                $newKrg = $existing['krg_in_out'] + 1;
                                $stockMdl->update($existing['id_stock'], [
                                    'kgs_in_out' => $newKgs,
                                    'cns_in_out' => $newCns,
                                    'krg_in_out' => $newKrg,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                                $idStock = $existing['id_stock'];
                            } else {
                                // insert baru
                                $stockMdl->insert(array_merge($stockKey, [
                                    'kgs_stock_awal' => 0,
                                    'cns_stock_awal' => 0,
                                    'krg_stock_awal' => 0,
                                    'kgs_in_out'     => round($partKg, 2),
                                    'cns_in_out'     => round($partCones),
                                    'krg_in_out'     => 1,
                                    'nama_cluster'   => $clusterName,
                                    'admin'          => session()->get('username'),
                                    'created_at'     => date('Y-m-d H:i:s')
                                ]));
                                $idStock = $stockMdl->getInsertID();
                            }

                            // 4) Insert pemasukan dan update dengan id_stock
                            $pemasukanMdl->insert([
                                'id_out_celup' => $idOutCelup,
                                'tgl_masuk'    => date('Y-m-d'),
                                'nama_cluster' => $clusterName,
                                'out_jalur'    => '0',
                                'admin'        => session()->get('username'),
                                'created_at'   => date('Y-m-d H:i:s'),
                                'updated_at'   => date('Y-m-d H:i:s')
                            ]);
                            $idPemasukan = $pemasukanMdl->getInsertID();
                            $pemasukanMdl->update($idPemasukan, ['id_stock' => $idStock, 'updated_at' => date('Y-m-d H:i:s')]);
                        }
                    }

                    $count++;
                } catch (\Exception $exRow) {
                    $msg = "Baris $line gagal: " . $exRow->getMessage();
                    log_message('error', 'ImportStock: ' . $msg);
                    $errorLogs[] = $msg;
                }
            }

            return $this->response->setJSON([
                'status'   => 'success',
                'inserted' => $count,
                'errors'   => $errorLogs,
                'errorMsg' => count($errorLogs) ? $errorLogs : null
            ]);
        } catch (\Exception $ex) {
            log_message('error', 'ImportStock fatal: ' . $ex->getMessage());
            return $this->response->setJSON([
                'status'   => 'error',
                'message'  => 'Terjadi kesalahan: ' . $ex->getMessage(),
                'errorMsg' => $ex->getMessage()
            ]);
        }
    }

    public function masterWarnaBenang()
    {
        $data = [
            'role' => $this->role,
            'title' => 'God Monitoring',
            'active' => $this->active,
        ];
        return view($this->role . '/god/import-master-warna-benang', $data);
    }

    public function importMasterWarnaBenang()
    {
        $file = $this->request->getFile('fileExcel');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return redirect()->back()->with('error', 'Format file harus .xls, .xlsx, atau .csv.');
        }

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // mulai dari baris kedua
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            // Skip baris kosong
            if (empty($row[0])) {
                continue;
            }

            $data = [
                'kode_warna'  => trim($row[0]),
                'warna'       => trim($row[1] ?? ''),
                'warna_dasar' => trim($row[2] ?? '')
            ];

            // Cek apakah kode_warna sudah ada
            $existing = $this->masterWarnaBenangModel->find($data['kode_warna']);
            if (!$existing) {
                $this->masterWarnaBenangModel->insert($data);
            }
        }

        return redirect()->back()->with('success', 'Data berhasil diimport.');
    }

    public function pengeluaranSementara()
    {
        $data = [
            'role' => $this->role,
            'title' => 'Pengeluaran Sementara',
            'active' => $this->active,
        ];
        return view($this->role . '/god/pengeluaranSementara', $data);
    }


    // import sementara pengeluaran stok
    // 1) Helper: normalizer generik (huruf besar, spasi, simbol seragam)
    private function canon(string $s): string
    {
        $s = strtoupper(trim($s));
        $s = preg_replace('/\s+/', ' ', $s);

        // Hapus spasi SEBELUM persen, tapi pertahankan spasi SETELAH persen
        $s = preg_replace('/\s+%/', '%', $s);
        // Pastikan ada tepat satu spasi setelah % jika diikuti huruf (SSTR, SOFT, dll)
        $s = preg_replace('/%\s*(?=[A-Z])/', '% ', $s);

        // Seragamkan 'x' jadi ' X '
        $s = preg_replace('/\s*[x×]\s*/', ' X ', $s);
        // Seragamkan slash
        if (!is_string($s)) {
            $s = (string)$s;
        }
        $s = preg_replace('/\s*\/\s*/', '/', $s);

        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }


    // LOT: hapus hanya leading ", " (boleh berulang). Selain itu jangan diubah.
    // private function normalizeLots(?string $raw): string
    // {
    //     if ($raw === null) return '';
    //     $s = (string)$raw;
    //     // hapus satu atau lebih blok: (spasi)* , (spasi)* di AWAL string
    //     $s = preg_replace('/^(?:\s*,\s*)+/u', '', $s);
    //     // jangan trim; biarkan spasi/format lain tetap
    //     return $s;
    // }

    private function normalizeLots(?string $raw): string
    {
        if ($raw === null) return '';
        $s = (string)$raw;

        // Normalisasi whitespace aneh -> spasi biasa, dan samakan varian simbol
        $s = str_replace(
            [
                "\xC2\xA0",        /* NBSP */
                "\xE3\x80\x80",    /* ideographic space */
                "\xEF\xBC\x8C",    /* full-width comma ， */
                "\xEF\xBC\x8B"     /* full-width plus ＋ */
            ],
            [' ', ' ', ',', '+'],
            $s
        );
        // Hapus zero-width & BOM
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);

        // Trim kiri–kanan (unicode-safe)
        $s = preg_replace('/^\s+|\s+$/u', '', $s);

        // Hapus blok koma di awal (", ", "， " dst)
        $s = preg_replace('/^(?:[,]\s*)+/u', '', $s);

        // Split oleh koma -> bersihkan token kosong
        $parts = preg_split('/[,]+/u', $s, -1, PREG_SPLIT_NO_EMPTY);

        $parts = array_values(array_filter(array_map(function ($t) {
            // Trim tiap token
            $t = preg_replace('/^\s+|\s+$/u', '', (string)$t);

            // Hapus spasi di sekitar 'X' / 'x' / '×' diapit alnum ASCII
            // "25E182 X 25E185" -> "25E182X25E185"
            $t = preg_replace('/(?<=\w)\s*[x×]\s*(?=\w)/ui', 'X', $t);

            // Hapus spasi di sekitar '+' diapit alnum ASCII
            // "25F339 + 25F337" -> "25F339+25F337"
            $t = preg_replace('/(?<=\w)\s*\+\s*(?=\w)/u', '+', $t);

            // Rapikan spasi beruntun di dalam token (opsional)
            $t = preg_replace('/[ \t]+/u', ' ', $t);

            return $t === '' ? null : $t;
        }, $parts)));

        // Gabungkan lagi dalam format baku "A, B, C"
        return implode(', ', $parts);
    }



    // 2) Helper: normalize item type via kamus
    private function normalizeItemType(string $raw): string
    {
        // Pindahkan kamus ke luar loop supaya tidak dibuat berulang
        static $map = null;
        if ($map === null) {
            $rawMap = [
                'COTTON CD 20S ORG 100%' => 'CTN CD 20S ORG 100%',
                'COTTON CD 20S ORG 100% SSTR' => 'CTN CD 20S ORG 100% SSTR',
                'COTON CB 20S BCI SSTR' => 'CTN CB 20S BCI SSTR',
                'COTTON CB 20S SSTR' => 'CTN CB 20S SSTR',
                'COTTON CB 20S BCI SSTR' => 'CTN CB 20S BCI SSTR',
                'COTTON CB 20S ORG 100% SSTR' => 'CTN CB 20S ORG 100% SSTR',
                'COTTON CB 32S BCI SSTR' => 'CTN CB 32S BCI SSTR',
                'COTTON CB 70/30 20S RECYCLED BCI' => 'CTN CB 20S 70/30 RECY BCI SOFT',
                'COTTON CB 70/30 32S RECY BCI SOFT' => 'CTN CB 32S 70/30 RECY BCI SOFT',
                'COTTON CB ECO CLMX 20S 55/30/15 BCI SSTR' => 'CTN CB ECO CLMX 20S 55/30/15 BCI SSTR SERAP AIR',
                'COTTON CD 20S' => 'CTN CD 20S',
                'COTTON CD 20S BCI' => 'CTN CD 20S BCI',
                'COTTON CD 20S BCI SSTR' => 'CTN CD 20S BCI SSTR',
                'COTTON CD 20S CVC 55/45' => 'CVC CD 20S 55/45',
                'COTTON CD 20S ORG 100 %' => 'CTN CD 20S ORG 100%',
                'COTTON CD 20S ORG 100% SSTR TWIST X2' => 'CTN CD 20S ORG SSTR TWIST X2',
                'COTTON CD 32S BCI' => 'CTN CD 32S BCI',
                'COTTON CD ORG 20S 100%' => 'CTN CD 20S ORG 100%',
                'COTTON COMPACT CB 20S BCI SSTR' => 'CTN CB COMPACT 20S BCI SSTR',
                'COTTON MISTY 20S' => 'CTN MISTY 20S',
                'COTTON MISTY 20S ORG 100%' => 'CTN MISTY ORG 20S 100%',
                'COTTON MISTY 20S ORG 100% ALOEVERA' => 'CTN MISTY ORG 20S 100% ALOE',
                'COTTON MISTY 32S' => 'CTN MISTY 32S',
                'COTTON MISTY 32S ANTI BACTERY' => 'CTN MISTY 32S ANTI BACTERY',
                'COTTON MISTY ORG 20S 100%' => 'CTN MISTY ORG 20S 100%',
                'COTTON MISTY ORGANIC 100% 20S' => 'CTN MISTY ORG 20S 100%',
                'COTTON ORG SPDX LYCRA 20D/32S' => 'CTN ORG SPDX LYCRA 20D/32S',
                'COTTON ORG SPNDX LYCRA 20D/32S' => 'CTN ORG SPDX LYCRA 20D/32S',
                'LUREX 1/150 X COTTON CD 20S BCI' => 'LUREX 1/150 X CTN CD 20S BCI',
                'LUREX 1/150 X COTTON CD 20S BCI SSTR' => 'LUREX 1/150 X CTN CD 20S BCI SSTR',
                'LUREX 1/150 X COTTON CD 20S ORG 100% SSTR' => 'LUREX 1/150 X CTN CD 20S ORG 100% SSTR',
                'LUREX 1/150 X COTTON CD 20S SSTR' => 'LUREX 1/150 X CTN CD 20S SSTR',
                'MISTY CB 70/30 20S RECYCLE' => 'MISTY CB 20S 70/30 RECY',
                'MISTY CB 70/30 32S RECYCLE' => 'MISTY CB 32S 70/30 RECY',
                'MISTY ORG SPNDX LYCRA 20D/32S' => 'MISTY ORG SPD LYCRA 20D/32S',
                'POLYESTER CLMX 20S SERAP AIR ANTI BACTERY' => 'POLY CLMX 20S SERAP AIR ANTI BACTERY',
                'SPUN POLYESTER 20S' => 'SPUN POLY 20S',
                'SPUN POLYESTER 32S' => 'SPUN POLY 32S',
                'SPUN POLYESTER 32S RECY SSTR SERAP AIR' => 'SPUN POLYESTER 32S RECY SSTR SERAP AIR',
                'SPUN POLYESTER 32S SERAP AIR SSTR' => 'SPUN POLYESTER 32S SSTR SERAP AIR',
                'SPUN POLYESTER 32S TWIST X2' => 'SP 32S X MISTY TWIST X2',
                'T/C RECY 65/35 CB 20S SSTR SERAP AIR' => 'T/C RECY 20S CB 65/35 BCI SSTR SERAP AIR',
            ];
            // Index-kan dengan kunci sudah dicannonize
            $map = [];
            foreach ($rawMap as $k => $v) {
                $map[$this->canon($k)] = $this->canon($v);
            }
        }

        $c = $this->canon($raw);
        // kalau sudah dalam bentuk target (kanon) biarkan
        if (in_array($c, $map, true)) return $c;
        // kalau ada di sisi kiri, ganti ke target
        if (isset($map[$c])) return $map[$c];
        // default: pakai hasil canon tanpa mapping (biar tetap konsisten)
        return $c;
    }

    // helper: hitung saldo tersedia berdasarkan skema "in_out = akumulasi KELUAR"
    private function computeAvailability(array $row): array
    {
        $kgAwal   = (float)($row['kgs_stock_awal'] ?? 0);
        $kgKeluar = (float)($row['kgs_in_out']     ?? 0);  // akumulasi keluar


        $cnsAwal   = (int)($row['cns_stock_awal'] ?? 0);
        $cnsKeluar = (int)($row['cns_in_out']     ?? 0);   // akumulasi keluar

        $krgAwal   = (int)($row['krg_stock_awal'] ?? 0);
        $krgKeluar = (int)($row['krg_in_out']     ?? 0);   // akumulasi keluar

        if (!empty($row['kgs_stock_awal']) && (float)$row['kgs_stock_awal'] > 0) {
            $availKg = (float)$row['kgs_stock_awal'];
        } elseif (!empty($row['kgs_in_out']) && (float)$row['kgs_in_out'] > 0) {
            $availKg = (float)$row['kgs_in_out'];
        } else {
            $availKg = 0.0;
        }
        if (!empty($row['cns_stock_awal']) && (int)$row['cns_stock_awal'] > 0) {
            $availCns = (int)$row['cns_stock_awal'];
        } elseif (!empty($row['cns_in_out']) && (int)$row['cns_in_out'] > 0) {
            $availCns = (int)$row['cns_in_out'];
        } else {
            $availCns = 0;
        }
        if (!empty($row['krg_stock_awal']) && (int)$row['krg_stock_awal'] > 0) {
            $availKrg = (int)$row['krg_stock_awal'];
        } elseif (!empty($row['krg_in_out']) && (int)$row['krg_in_out'] > 0) {
            $availKrg = (int)$row['krg_in_out'];
        } else {
            $availKrg = 0;
        }

        return ['kg' => $availKg, 'cns' => $availCns, 'krg' => $availKrg];
    }


    // public function uploadPengeluaranSementara()
    // {
    //     $file = $this->request->getFile('fileExcel');
    //     if (!$file || !$file->isValid() || $file->hasMoved()) {
    //         return $this->response->setJSON([
    //             'status'   => 'error',
    //             'message'  => 'File tidak valid atau sudah dipindahkan.',
    //             'errorMsg' => 'File tidak valid atau sudah dipindahkan.'
    //         ]);
    //     }

    //     $newName = $file->getRandomName();
    //     $file->move(WRITEPATH . 'uploads', $newName);
    //     $path = WRITEPATH . 'uploads/' . $newName;

    //     $rows     = IOFactory::load($path)->getActiveSheet()->toArray();
    //     $stockMdl = $this->stockModel;

    //     $count     = 0;
    //     $errorLogs = [];

    //     // --- DEFINISIKAN INDEX KOLUM (biar jelas & mudah diubah) ---
    //     $idxCluster    = 1;   // nama_cluster
    //     $idxItemType   = 9;   // item_type (mentah dari excel)
    //     $idxNoModel    = 6;   // no_model
    //     $idxKodeWarna  = 10;   // kode_warna
    //     $idxWarna      = 11;  // warna
    //     $idxLot        = 16;  // lot_stock
    //     $idxKgKeluar   = 12;   // kg_keluar
    //     $idxCnsKeluar  = 13;   // <-- PISAH dari no_model! (ganti sesuai file excel kamu)
    //     $idxKrgKeluar  = 14;   // kg_keluar 

    //     foreach (array_slice($rows, 3) as $i => $row) {
    //         $line = $i + 3;

    //         // Validasi minimal kolom wajib
    //         if (!isset($row[$idxCluster], $row[$idxNoModel], $row[$idxItemType])) {
    //             $msg = "Baris $line dilewati: kolom wajib tidak lengkap.";
    //             log_message('warning', 'PengeluaranSementara: ' . $msg . ' Row=' . json_encode($row));
    //             $errorLogs[] = $msg;
    //             continue;
    //         }
    //         if (trim((string)$row[$idxCluster]) === '' || trim((string)$row[$idxNoModel]) === '') {
    //             $msg = "Baris $line dilewati: nama_cluster/no_model kosong.";
    //             log_message('warning', 'PengeluaranSementara: ' . $msg);
    //             $errorLogs[] = $msg;
    //             continue;
    //         }

    //         try {
    //             // --- NORMALISASI FIELD KUNCI ---
    //             $cluster   = $this->canon((string)$row[$idxCluster]);
    //             $noModel   = $this->canon((string)$row[$idxNoModel]);
    //             $itemType  = $this->normalizeItemType((string)$row[$idxItemType]); // <— POIN UTAMA
    //             $kodeWarna = isset($row[$idxKodeWarna]) ? $this->canon((string)$row[$idxKodeWarna]) : '';
    //             $warna     = isset($row[$idxWarna]) ? $this->canon((string)$row[$idxWarna]) : '';
    //             // sebelumnya JANGAN pakai $this->canon(...) untuk lot
    //             $lot = isset($row[$idxLot]) ? $this->normalizeLots($row[$idxLot]) : '';


    //             if (preg_match('/LB\s\d+-[\d-]+$/', $kodeWarna)) {
    //                 // Contoh: LB 123-456 jadi LB.00123-456
    //                 $kodeWarna = preg_replace('/LB\s(\d+-[\d-]+)$/', 'LB.00$1', $kodeWarna);
    //             }
    //             if (preg_match('/^(LC|LCJ|KHT|KP|C|KPM)\s+[\d-]+(?:-[\d-]+)*$/', $kodeWarna)) {
    //                 // Tangani LCJ 00130-1-1 dan variasi lain
    //                 $kodeWarna = preg_replace('/^([A-Z]+)\s+(.+)$/', '$1.$2', $kodeWarna);
    //             }
    //             // Siapkan key stock yang sudah dinormalisasi
    //             $stockKey = [
    //                 'nama_cluster' => $cluster,
    //                 'no_model'     => $noModel,
    //                 'item_type'    => $itemType,
    //                 'kode_warna'   => $kodeWarna,
    //                 // 'warna'        => $warna,
    //                 'lot_stock'    => $lot,
    //             ];

    //             // Ambil existing stok berdasar key normalized
    //             $builder = $stockMdl->builder();
    //             $builder->where('nama_cluster', $cluster)
    //                 ->where('no_model', $noModel)
    //                 ->where('item_type', $itemType);

    //             if ($kodeWarna !== '') {
    //                 $builder->where('kode_warna', $kodeWarna);
    //             }
    //             // if ($warna      !== '') {
    //             //     $builder->where('warna', $warna);
    //             // }
    //             if ($lot        !== '') {
    //                 $builder->where('lot_stock', $lot);
    //             }

    //             $existing = $builder->get()->getRowArray();

    //             // Parse angka keluar
    //             $kgKeluar  = isset($row[$idxKgKeluar])  ? (float)str_replace(',', '.', (string)$row[$idxKgKeluar]) : 0.0;
    //             $cnsKeluar = isset($row[$idxCnsKeluar]) ? (int)preg_replace('/[^\d\-]/', '', (string)$row[$idxCnsKeluar]) : 0;
    //             $krgKeluar = isset($row[$idxKrgKeluar]) ? (int)preg_replace('/[^\d\-]/', '', (string)$row[$idxKrgKeluar]) : 0;

    //             // if ($kgKeluar < 0 && $cnsKeluar < 0) {
    //             //     $msg = "Baris $line dilewati: kg_stok dan cns_stok harus > 0.";
    //             //     log_message('warning', 'PengeluaranSementara: ' . $msg);
    //             //     $errorLogs[] = $msg;
    //             //     continue;
    //             // }

    //             if ($existing) {
    //                 // 1) hitung saldo tersedia
    //                 $avail = $this->computeAvailability($existing);

    //                 // // 2) validasi cukup
    //                 // if (empty($kgKeluar) && empty($cnsKeluar)) {
    //                 //     $msg = "Baris $line: stok tidak cukup. Sisa KG={$avail['kg']}, CNS={$avail['cns']}, minta keluar KG={$kgKeluar}, CNS={$cnsKeluar}.";
    //                 //     log_message('warning', 'PengeluaranSementara: ' . $msg);
    //                 //     $errorLogs[] = $msg;
    //                 //     continue;
    //                 // }

    //                 // 3) update akumulasi KELUAR (in_out += keluar)
    //                 // Tentukan field mana yang diupdate: jika kgs_stock_awal > 0, update kgs_in_out (pengeluaran); jika kgs_stock_awal == 0, update kgs_stock_awal (pemasukan awal)
    //                 if (isset($existing['kgs_stock_awal']) && (float)$existing['kgs_stock_awal'] > 0) {
    //                     // Update akumulasi KELUAR (in_out += keluar)
    //                     $newKg  = $kgKeluar;
    //                     $newCns = $cnsKeluar;
    //                     $newKrg = $krgKeluar;

    //                     $this->stockModel->update($existing['id_stock'], [
    //                         'kgs_in_out' => $newKg,
    //                         'cns_in_out' => $newCns,
    //                         'krg_in_out' => $newKrg,
    //                         'updated_at' => date('Y-m-d H:i:s')
    //                     ]);
    //                 } else {
    //                     // Jika belum ada stok awal, update kgs_stock_awal (pemasukan awal)
    //                     $newKgAwal  = $kgKeluar;
    //                     $newCnsAwal = $cnsKeluar;
    //                     $newKrgAwal = $krgKeluar;

    //                     $this->stockModel->update($existing['id_stock'], [
    //                         'kgs_stock_awal' => $newKgAwal,
    //                         'cns_stock_awal' => $newCnsAwal,
    //                         'krg_stock_awal' => $newKrgAwal,
    //                         'updated_at'     => date('Y-m-d H:i:s')
    //                     ]);
    //                 }
    //                 $count++;
    //             } else {
    //                 $msg = "Baris $line: stok tidak ditemukan untuk key yang dinormalisasi.";
    //                 log_message('warning', 'PengeluaranSementara: ' . $msg . ' Key=' . json_encode($stockKey));
    //                 $errorLogs[] = $msg;
    //                 continue;
    //             }
    //         } catch (\Throwable $e) {
    //             log_message('error', 'PengeluaranSementara (baris ' . $line . '): ' . $e->getMessage());
    //             $errorLogs[] = "Baris $line: " . $e->getMessage();
    //         }
    //     }

    //     // Hapus file setelah diproses
    //     @unlink($path);

    //     if (!empty($errorLogs)) {
    //         return $this->response->setJSON([
    //             'status'    => 'error',
    //             'message'   => 'Sebagian/semua baris gagal diproses. Pengeluaran sementara berhasil diproses.' . $count,
    //             'errorLogs' => $errorLogs
    //         ]);
    //     }

    //     return $this->response->setJSON([
    //         'status'  => 'success',
    //         'message' => 'Pengeluaran sementara berhasil diproses.',
    //         'count'   => $count
    //     ]);
    // }

    public function uploadPengeluaranSementara()
    {
        $file = $this->request->getFile('fileExcel');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON([
                'status'   => 'error',
                'message'  => 'File tidak valid atau sudah dipindahkan.',
                'errorMsg' => 'File tidak valid atau sudah dipindahkan.'
            ]);
        }

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $path = WRITEPATH . 'uploads/' . $newName;

        $rows     = IOFactory::load($path)->getActiveSheet()->toArray();
        $stockMdl = $this->stockModel;

        $successCount = 0;
        $failedCount  = 0;
        $errorLogs    = [];

        // --- DEFINISIKAN INDEX KOLUM (biar jelas & mudah diubah) ---
        $idxCluster    = 1;   // nama_cluster
        $idxItemType   = 9;   // item_type (mentah dari excel)
        $idxNoModel    = 6;   // no_model
        $idxKodeWarna  = 10;  // kode_warna
        $idxWarna      = 11;  // warna
        $idxLot        = 16;  // lot_stock
        $idxKgKeluar   = 12;  // kg_keluar
        $idxCnsKeluar  = 13;  // cns_keluar
        $idxKrgKeluar  = 14;  // krg_keluar

        // mulai data di baris ke-4 (index 3)
        foreach (array_slice($rows, 3) as $i => $row) {
            $line = $i + 4; // nomor baris excel human friendly

            // helper untuk push error terstruktur
            $pushErr = function (string $reason, array $ctx = []) use (&$errorLogs, &$failedCount, $line) {
                $failedCount++;
                $payload = array_merge([
                    'line'   => $line,
                    'reason' => $reason,
                ], $ctx);
                $errorLogs[] = $payload;
                log_message('warning', 'PengeluaranSementara: ' . $reason . ' | ctx=' . json_encode($payload, JSON_UNESCAPED_UNICODE));
            };

            // Validasi minimal kolom wajib
            if (!isset($row[$idxCluster], $row[$idxNoModel], $row[$idxItemType])) {
                $pushErr('Kolom wajib tidak lengkap', ['row_sample' => array_slice($row, 0, 18)]);
                continue;
            }
            if (trim((string)$row[$idxCluster]) === '' || trim((string)$row[$idxNoModel]) === '') {
                $pushErr('nama_cluster/no_model kosong', ['cluster' => $row[$idxCluster] ?? null, 'no_model' => $row[$idxNoModel] ?? null]);
                continue;
            }

            try {
                // --- NORMALISASI FIELD KUNCI ---
                $cluster   = $this->canon((string)$row[$idxCluster]);
                $noModel   = $this->canon((string)$row[$idxNoModel]);
                $itemType  = $this->normalizeItemType((string)$row[$idxItemType]); // <— POIN UTAMA
                $kodeWarna = isset($row[$idxKodeWarna]) ? $this->canon((string)$row[$idxKodeWarna]) : '';
                $warna     = isset($row[$idxWarna]) ? $this->canon((string)$row[$idxWarna]) : '';
                $lot       = isset($row[$idxLot]) ? $this->normalizeLots($row[$idxLot]) : '';

                // Format khusus kode warna
                if (preg_match('/LB\s\d+-[\d-]+$/', $kodeWarna)) {
                    $kodeWarna = preg_replace('/LB\s(\d+-[\d-]+)$/', 'LB.00$1', $kodeWarna);
                }
                if (preg_match('/^(LC|LCJ|KHT|KP|C|KPM)\s+[\d-]+(?:-[\d-]+)*$/', $kodeWarna)) {
                    $kodeWarna = preg_replace('/^([A-Z]+)\s+(.+)$/', '$1.$2', $kodeWarna);
                }

                $stockKey = [
                    'nama_cluster' => $cluster,
                    'no_model'     => $noModel,
                    'item_type'    => $itemType,
                    'kode_warna'   => $kodeWarna,
                    'lot_stock'    => $lot,
                ];

                // Ambil existing stok berdasar key normalized
                $builder = $stockMdl->builder();
                $builder->where('nama_cluster', $cluster)
                    ->where('no_model', $noModel)
                    ->where('item_type', $itemType);

                if ($kodeWarna !== '') $builder->where('kode_warna', $kodeWarna);
                if ($lot !== '') {
                    // Cari baik di lot_stock maupun lot_awal
                    $builder->groupStart()
                        ->where('lot_stock', $lot)
                        ->orWhere('lot_awal', $lot)
                        ->groupEnd();
                }

                $existing = $builder->get()->getRowArray();
                // log_message('debug', 'PengeluaranSementara: mencari stok existing | key=' . json_encode($stockKey) . ' | found=' . ($existing ? 'YES' : 'NO'));
                // Parse angka keluar
                $kgKeluar  = isset($row[$idxKgKeluar])  ? (float)str_replace(',', '.', (string)$row[$idxKgKeluar]) : 0.0;
                $cnsKeluar = isset($row[$idxCnsKeluar]) ? (int)preg_replace('/[^\d\-]/', '', (string)$row[$idxCnsKeluar]) : 0;
                $krgKeluar = isset($row[$idxKrgKeluar]) ? (int)preg_replace('/[^\d\-]/', '', (string)$row[$idxKrgKeluar]) : 0;

                if (!$existing) {
                    $pushErr('Stok tidak ditemukan (key normalisasi tidak match)', ['key' => $stockKey]);
                    continue;
                }

                // (Opsional) validasi angka negatif/aneh
                // if ($kgKeluar < 0 || $cnsKeluar < 0 || $krgKeluar < 0) { ... }

                // 1) hitung saldo tersedia
                $avail = $this->computeAvailability($existing);

                // 3) update akumulasi KELUAR (in_out += keluar) ATAU set stok_awal jika awal=0
                if (isset($existing['kgs_stock_awal']) && (float)$existing['kgs_stock_awal'] > 0 && $existing['cns_stock_awal'] > 0) {
                    // Overwrite ke nilai keluar yang baru (sesuai kode asal); jika mau akumulatif: $existing['kgs_in_out'] + $kgKeluar
                    $this->stockModel->update($existing['id_stock'], [
                        'kgs_stock_awal' => (float)$kgKeluar,
                        'cns_stock_awal' => (int)$cnsKeluar,
                        'krg_stock_awal' => (int)$krgKeluar,
                        'updated_at'     => date('Y-m-d H:i:s')
                    ]);
                } else if (isset($existing['kgs_in_out']) && (float)$existing['kgs_in_out'] > 0 && $existing['cns_in_out'] > 0) {
                    // Set stok awal jika awal masih 0
                    $this->stockModel->update($existing['id_stock'], [
                        'kgs_in_out' => (float)$kgKeluar,
                        'cns_in_out' => (int)$cnsKeluar,
                        'krg_in_out' => (int)$krgKeluar,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    continue; // dilewati jika tidak ada stok awal maupun in_out
                }

                $successCount++;
            } catch (\Throwable $e) {
                $pushErr('Exception: ' . $e->getMessage(), [
                    'key'        => $stockKey ?? null,
                    'row_sample' => array_slice($row, 0, 18)
                ]);
                continue;
            }
        }

        // Hapus file setelah diproses
        @unlink($path);

        if ($failedCount > 0) {
            return $this->response->setJSON([
                'status'         => 'error',
                'message'        => "Sebagian/semua baris gagal diproses. Berhasil: {$successCount}, Gagal: {$failedCount}.",
                'success_count'  => $successCount,
                'failed_count'   => $failedCount,
                'errorLogs'      => $errorLogs
            ]);
        }

        // SUKSES PENUH → hanya tampilkan count sukses (tanpa errorLogs)
        return $this->response->setJSON([
            'status' => 'success',
            'count'  => $successCount,
            'message' => "Pengeluaran sementara berhasil diproses ({$successCount} baris)."
        ]);
    }


    public function prosesImportPemasukan()
    {
        ini_set('max_execution_time', 1000); // 1000 detik

        $admin = session()->get('username') ?? 'system';
        $file  = $this->request->getFile('fileImport');

        // Buat batch ID supaya penelusuran log lebih gampang
        try {
            $batchId = 'IMP-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));
        } catch (\Throwable $e) {
            $batchId = 'IMP-' . date('Ymd-His') . '-RND';
        }

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            log_message(
                'error',
                "[{$batchId}] File tidak valid atau sudah dipindahkan. name={name} err={err}",
                ['name' => $file ? $file->getClientName() : '(null)', 'err' => $file ? $file->getErrorString() : '(null)']
            );
            return redirect()->back()->with('error', 'File tidak valid atau sudah dipindahkan.');
        }

        $origName = $file->getClientName();
        $newName  = $file->getRandomName();
        $destDir  = WRITEPATH . 'uploads';
        $file->move($destDir, $newName);
        $filePath = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;

        log_message(
            'info',
            "[{$batchId}] START import by={admin} orig={orig} tmp={tmp}",
            ['admin' => $admin, 'orig' => $origName, 'tmp' => $filePath]
        );

        try {
            $sheetArr = IOFactory::load($filePath)
                ->getActiveSheet()
                ->toArray(null, true, true, true);
            log_message('info', "[{$batchId}] Excel loaded rows={rows}", ['rows' => count($sheetArr)]);
        } catch (\Throwable $e) {
            @unlink($filePath);
            log_message('error', "[{$batchId}] Gagal membaca Excel: {msg}", ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
        @unlink($filePath);

        // --- Helper angka ---
        $toFloat = static function ($v) {
            if ($v === null) return 0.0;
            $s = trim((string)$v);
            if ($s === '') return 0.0;
            $s = preg_replace('/[^\d\-,.]/', '', $s);
            if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
                $s = str_replace('.', '', $s);
                $s = str_replace(',', '.', $s);
            } else {
                if (strpos($s, ',') !== false) {
                    $s = str_replace(',', '.', $s);
                }
            }
            return (float)$s;
        };
        $toInt = static function ($v) {
            if ($v === null) return 0;
            $s = preg_replace('/[^\d\-]/', '', (string)$v);
            if ($s === '' || $s === '-') return 0;
            return (int)$s;
        };

        $db = \Config\Database::connect();
        $success = 0;
        $failed  = [];

        // Mulai dari baris ke-18 (melewati header)
        foreach (array_slice($sheetArr, 3, null, true) as $rowNum => $row) {
            // dd($row);
            // Mapping kolom
            $namaCluster = trim((string)($row['J'] ?? ''));
            $noModel     = trim((string)($row['K'] ?? ''));
            $itemType    = trim((string)($row['L'] ?? ''));
            $kodeWarna   = trim((string)($row['M'] ?? ''));
            // $warna       = trim((string)($row['N'] ?? ''));
            $lot         = trim((string)($row['O'] ?? ''));
            $kgsMasuk    = $toFloat($row['P'] ?? 0);
            $cnsMasuk    = $toInt($row['Q'] ?? 0);
            $krgMasuk    = max(1, $toInt($row['R'] ?? 1)); // minimal 1 karung

            // Log raw ringkas (hindari membludak)
            log_message(
                'debug',
                "[{$batchId}] Row {row} raw: no_model={no_model}, item_type={item_type}, k_warna={kode_warna}, cluster={cluster}, lot={lot}, kgs={kgs}, cns={cns}, krg={krg}",
                [
                    'row'        => $rowNum,
                    'no_model'   => $noModel,
                    'item_type'  => $itemType,
                    'kode_warna' => $kodeWarna,
                    // 'warna'      => $warna,
                    'cluster'    => $namaCluster,
                    'lot'        => $lot,
                    'kgs'        => $kgsMasuk,
                    'cns'        => $cnsMasuk,
                    'krg'        => $krgMasuk,
                ]
            );

            // Skip baris kosong total
            if ($noModel === '' && $itemType === '' && $kodeWarna === '' && $namaCluster === '') {
                log_message('debug', "[{$batchId}] Row {row} di-skip (kosong).", ['row' => $rowNum]);
                continue;
            }

            // Validasi kolom wajib
            if ($noModel === '' || $itemType === '' || $kodeWarna === '' || $namaCluster === '') {
                $msg = "Baris {$rowNum}: kolom wajib kosong (no_model/item_type/kode_warna/nama_cluster).";
                $failed[] = $msg;
                log_message('warning', "[{$batchId}] {msg}", ['msg' => $msg]);
                continue;
            }

            try {
                $db->transBegin();

                // 1) Upsert master_order
                $orderRow = $this->masterOrderModel
                    ->select('id_order')
                    ->where('no_model', $noModel)
                    ->first();

                if ($orderRow) {
                    $idOrder = (int)$orderRow['id_order'];
                    // log_message('debug', "[{$batchId}] Row {row} master_order EXIST id={id}", ['row' => $rowNum, 'id' => $idOrder]);
                } else {
                    $this->masterOrderModel->insert([
                        'no_model'   => $noModel,
                        'no_order'   => '-',
                        'buyer'      => '-',
                        'foll_up'    => '-',
                        'lco_date'   => date('Y-m-d'), // default hari ini
                        'admin'      => $admin,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $idOrder = (int)$this->masterOrderModel->insertID();
                    // log_message('info', "[{$batchId}] Row {row} master_order INSERT id={id}", ['row' => $rowNum, 'id' => $idOrder]);
                }

                // 2) Upsert master_material
                $mm = $this->masterMaterialModel->where('item_type', $itemType)->first();
                // dd($mm);
                if (!$mm) {
                    $this->masterMaterialModel->insert([
                        'item_type'  => $itemType,
                        'deskripsi'  => $itemType,
                        'jenis'      => null,
                        'ukuran'      => null,
                    ]);
                    // log_message('info', "[{$batchId}] Row {row} master_material INSERT item_type={it}", ['row' => $rowNum, 'it' => $itemType]);
                }

                // 3) Upsert material
                $material = $this->materialModel
                    ->where('id_order', $idOrder)
                    ->where('item_type', $itemType)
                    ->where('kode_warna', $kodeWarna)
                    ->first();

                if (empty($material)) {
                    $this->materialModel->insert([
                        'id_order'    => $idOrder,
                        'style_size'  => '',
                        'area'        => '',
                        // 'color'       => $warna,
                        'color'       => '',
                        'item_type'   => $itemType,
                        'kode_warna'  => $kodeWarna,
                        'composition' => 0,
                        'gw'          => 0,
                        'qty_pcs'     => 0,
                        'loss'        => 0,
                        'kgs'         => 0,
                        'admin'       => $admin,
                        'created_at'  => date('Y-m-d H:i:s'),
                    ]);

                    $id_material = (int)$this->materialModel->insertID();

                    // 3) Upsert material
                    $material = $this->materialModel
                        ->where('id_material', $id_material)
                        ->first();

                    // log_message(
                    //     'info',
                    //     "[{$batchId}] Row {row} material INSERT (no prev) it={it} kw={kw}",
                    //     ['row' => $rowNum, 'it' => $itemType, 'kw' => $kodeWarna]
                    // );
                }

                // 4) other_bon
                $this->otherBonModel->insert([
                    'no_model'       => $noModel,
                    'item_type'      => $material['item_type'],
                    'kode_warna'     => $material['kode_warna'],
                    // 'warna'          => $warna ?: null,
                    'warna'          => $material['color'],
                    'tgl_datang'     => date('Y-m-d'),
                    'no_surat_jalan' => 'STOK IMPORT',
                    'detail_sj'      => '',
                    'keterangan'     => 'Import XLS',
                    'ganti_retur'    => '0',
                    'admin'          => $admin,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                $id_other_in = (int)$this->otherBonModel->insertID();
                if ($id_other_in <= 0) {
                    $dbErr = $db->error();
                    log_message('error', "[{$batchId}] Row {row} other_bon INSERT gagal dbErr={err}", ['row' => $rowNum, 'err' => json_encode($dbErr)]);
                    throw new \RuntimeException('Gagal insert other_bon');
                }
                // log_message('debug', "[{$batchId}] Row {row} other_bon id={id}", ['row' => $rowNum, 'id' => $id_other_in]);

                // 5) out_celup
                $this->outCelupModel->insert([
                    'id_other_bon' => $id_other_in,
                    'no_model'     => $noModel,
                    'l_m_d'        => '',
                    'harga'        => 0,
                    'no_karung'    => null,
                    'gw_kirim'     => 0,
                    'kgs_kirim'    => $kgsMasuk,
                    'cones_kirim'  => $cnsMasuk,
                    'lot_kirim'    => $lot,
                    'ganti_retur'  => '0',
                    'admin'        => $admin,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
                $id_out_celup = (int)$this->outCelupModel->insertID();
                if ($id_out_celup <= 0) {
                    $dbErr = $db->error();
                    log_message('error', "[{$batchId}] Row {row} out_celup INSERT gagal dbErr={err}", ['row' => $rowNum, 'err' => json_encode($dbErr)]);
                    throw new \RuntimeException('Gagal insert out_celup');
                }

                // 6) stock
                $existingStock = $this->stockModel
                    ->where('no_model', $noModel)
                    ->where('item_type', $material['item_type'])
                    ->where('kode_warna', $material['kode_warna'])
                    ->where('nama_cluster', $namaCluster)
                    ->where('lot_stock', $lot)
                    ->first();

                if ($existingStock) {
                    $upd = [
                        'kgs_in_out'     => $kgsMasuk != 0 ? (float)$kgsMasuk : (float)($existingStock['kgs_in_out'] ?? 0),
                        'cns_in_out'     => $cnsMasuk != 0 ? (int)$cnsMasuk : (int)($existingStock['cns_in_out'] ?? 0),
                        'krg_in_out'     => $krgMasuk != 0 ? (int)$krgMasuk : (int)($existingStock['krg_in_out'] ?? 0),
                        'kgs_stock_awal' => $kgsMasuk == 0 ? (float)$kgsMasuk : (float)($existingStock['kgs_stock_awal'] ?? 0),
                        'cns_stock_awal' => $cnsMasuk == 0 ? (int)$cnsMasuk : (int)($existingStock['cns_stock_awal'] ?? 0),
                        'krg_stock_awal' => $krgMasuk == 0 ? (int)$krgMasuk : (int)($existingStock['krg_stock_awal'] ?? 0),
                        'lot_stock'      => isset($lot) && $lot !== '' ? $lot : ($existingStock['lot_stock'] ?? ($existingStock['lot_awal'] ?? null)),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ];
                    $this->stockModel->update($existingStock['id_stock'], $upd);
                    $idStok = (int)$existingStock['id_stock'];
                    // log_message(
                    //     'debug',
                    //     "[{$batchId}] Row {row} stock UPDATE id={id} kgs+={kgs} cns+={cns} krg+={krg}",
                    //     ['row' => $rowNum, 'id' => $idStok, 'kgs' => $kgsMasuk, 'cns' => $cnsMasuk, 'krg' => $krgMasuk]
                    // );
                } else {
                    $this->stockModel->insert([
                        'no_model'     => $noModel,
                        'item_type'    => $material['item_type'],
                        'kode_warna'   => $material['kode_warna'],
                        'warna'        => $material['color'],
                        'nama_cluster' => $namaCluster,
                        'lot_stock'    => '',
                        'lot_awal'     => $lot,
                        'kgs_stock_awal' => $kgsMasuk,
                        'cns_stock_awal' => $cnsMasuk,
                        'krg_stock_awal' => max(1, $krgMasuk),
                        'kgs_in_out'   => 0,
                        'cns_in_out'   => 0,
                        'krg_in_out'   => 0,
                        'admin'        => $admin,
                        'created_at'   => date('Y-m-d H:i:s'),
                    ]);
                    $idStok = (int)$this->stockModel->insertID();
                    if ($idStok <= 0) {
                        $dbErr = $db->error();
                        log_message('error', "[{$batchId}] Row {row} stock INSERT gagal dbErr={err}", ['row' => $rowNum, 'err' => json_encode($dbErr)]);
                        throw new \RuntimeException('Gagal insert stock');
                    }
                    // log_message('debug', "[{$batchId}] Row {row} stock INSERT id={id}", ['row' => $rowNum, 'id' => $idStok]);
                }

                // 7) pemasukan
                $this->pemasukanModel->insert([
                    'id_out_celup' => $id_out_celup,
                    'id_stock'     => $idStok,
                    'tgl_masuk'    => date('Y-m-d'),
                    'nama_cluster' => $namaCluster,
                    'out_jalur'    => '0',
                    'admin'        => $admin,
                    'created_at'   => date('Y-m-d H:i:s'),
                ]);
                $idPemasukan = (int)$this->pemasukanModel->insertID();
                if ($idPemasukan <= 0) {
                    $dbErr = $db->error();
                    log_message('error', "[{$batchId}] Row {row} pemasukan INSERT gagal dbErr={err}", ['row' => $rowNum, 'err' => json_encode($dbErr)]);
                    throw new \RuntimeException('Gagal insert pemasukan');
                }

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    $msg = "Baris {$rowNum}: Gagal menyimpan (status transaksi).";
                    $failed[] = $msg;
                    log_message('error', "[{$batchId}] {msg}", ['msg' => $msg]);
                    continue;
                }

                $db->transCommit();
                $success++;
                // log_message(
                //     'info',
                //     "[{$batchId}] Row {row} COMMIT ok (pemasukan_id={pid}, stock_id={sid}, out_celup_id={oid}, other_bon_id={bid})",
                //     ['row' => $rowNum, 'pid' => $idPemasukan, 'sid' => $idStok, 'oid' => $id_out_celup, 'bid' => $id_other_in]
                // );
            } catch (\Throwable $e) {
                if ($db->transStatus()) {
                    $db->transRollback();
                }
                $msg = "Baris {$rowNum}: " . $e->getMessage();
                $failed[] = $msg;
                // Sertakan potongan data untuk diagnosis
                log_message(
                    'error',
                    "[{$batchId}] EXCEPTION row={row} msg={msg} ctx={ctx}",
                    [
                        'row' => $rowNum,
                        'msg' => $e->getMessage(),
                        'ctx' => json_encode([
                            'no_model' => $noModel,
                            'item_type' => $material['item_type'],
                            'kode_warna' => $material['kode_warna'],
                            'warna' => $material['color'],
                            'cluster' => $namaCluster,
                            'lot' => $lot,
                            'kgs' => $kgsMasuk,
                            'cns' => $cnsMasuk,
                            'krg' => $krgMasuk,
                        ], JSON_UNESCAPED_UNICODE)
                    ]
                );
            }
        }

        if ($success > 0 && empty($failed)) {
            session()->setFlashdata('success', "Import selesai. Berhasil: {$success} baris. Batch={$batchId}");
            // log_message('info', "[{$batchId}] DONE success={s} failed=0", ['s' => $success]);
        } else {
            $msg = "Import selesai parsial. Berhasil: {$success} baris; Gagal: " . count($failed) . " baris. Batch={$batchId}";
            if (!empty($failed)) {
                session()->setFlashdata('error_detail', implode("\n", $failed));
            }
            session()->setFlashdata('error', $msg);
            log_message('warning', "[{$batchId}] DONE partial success={s} failed={f}", ['s' => $success, 'f' => count($failed)]);
        }

        return redirect()->to(base_url($this->role . "/importPemasukan"));
    }

    public function importPemesanan()
    {
        $data = [
            'active' => 'importPemesanan',
            'title'  => 'Import Pemesanan',
            'role'   => $this->role,
        ];

        return view($this->role . '/god/importPemesanan', $data);
    }

    public function prosesImportPemesanan()
    {
        ini_set('memory_limit', '1024M'); // atau 2048M jika perlu
        set_time_limit(0);
        $db = \Config\Database::connect();
        $db->transBegin(); // mulai transaksi

        try {
            // --- bagian validasi & baca file tetap sama ---
            $file = $this->request->getFile('fileImport');
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return redirect()->back()->with('error', 'File tidak valid atau sudah dipindahkan.');
            }
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
                return redirect()->back()->with('error', 'Format file harus .xlsx / .xls / .csv');
            }

            $newName = $file->getRandomName();
            $destDir = WRITEPATH . 'uploads';
            $file->move($destDir, $newName);
            $path = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;

            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            //               null,  calcFormulas=false, formatData=false, returnCellRef=true
            $rows = $sheet->toArray(null, false,        false,           true);


            if (empty($rows) || count($rows) < 2) {
                return redirect()->back()->with('error', 'Tidak ada data (minimal header + 1 baris).');
            }

            $headerRow = $rows[1];
            $colMap = [];
            foreach ($headerRow as $col => $title) {
                $colMap[$col] = is_string($title) ? strtolower(trim($title)) : '';
            }

            $ok = 0;
            $err = 0;
            $errLogs = [];

            foreach (array_slice($rows, 1, null, true) as $rowNum => $row) {
                $r = (int)$rowNum;

                $dt = $this->parseTanggalJam($sheet, $colMap, $r);
                $data = [];
                foreach ($row as $col => $val) {
                    $key = $colMap[$col] ?? null;
                    if ($key) $data[$key] = trim((string)$val);
                }

                if (empty($data['id_bahan_baku'])) {
                    continue;
                }

                $rowOrder = $this->masterOrderModel
                    ->select('master_order.id_order, COUNT(material.id_material) AS jml_material')
                    ->join('material', 'material.id_order = master_order.id_order', 'left')
                    ->where('master_order.no_model', $data['no_model'])
                    ->groupBy('master_order.id_order')
                    ->orderBy('jml_material', 'DESC')     // paling banyak material di atas
                    ->orderBy('master_order.id_order', 'DESC') // tie-breaker: id_order terbaru
                    ->first();

                $idOrder = $rowOrder['id_order'] ?? null;
                // 2) Ambil id_material terbaru untuk kombinasi (id_order, item_type, kode_warna)
                // Catatan: di Excel-mu kolom 'jenis' dipakai sebagai item_type (pastikan mappingnya benar).
                $idMaterialRow = $this->materialModel
                    ->select('id_material')
                    ->where('id_order', $idOrder)
                    ->where('item_type',  $data['jenis'])      // <-- jika kolommu bernama 'item_type', pastikan $data['jenis'] benar
                    ->where('kode_warna', $data['kode_warna'])
                    ->orderBy('id_material', 'DESC')                 // ambil yang terbaru jika duplikat
                    ->first();

                $idMaterial = $idMaterialRow['id_material'] ?? null;
                // dd ($idMaterialRow, $idMaterial);
                if (!$idMaterial) {
                    $err++;
                    $errLogs[] = "Baris $r: material tidak ditemukan untuk [no_model={$data['no_model']} / jenis={$data['jenis']} / kode={$data['kode_warna']}]";
                    continue;
                }

                $jalur = $this->clusterModel->select('nama_cluster')
                    ->where('nama_cluster', $data['jalur'] ?? '')
                    ->first();
                if (empty($jalur)) {
                    $jalur = NULL;
                }
                // dd($jalur);
                $payload = [
                    'id_material'      => (int)$idMaterial,
                    'tgl_list'         => $dt['tgl_pesan'],
                    'tgl_pesan'        => $dt['tgl_pesan'],
                    'tgl_pakai'        => $dt['tgl_pakai'],
                    'jl_mc'            => (int)($data['jl_mc'] ?? 0),
                    'ttl_qty_cones'    => (int)($data['ttl_qty_cones'] ?? 0),
                    'ttl_berat_cones'  => (float)($data['ttl_berat_cones'] ?? 0),
                    'sisa_kgs_mc'      => (float)($data['sisa_kgs_mc'] ?? 0),
                    'sisa_cones_mc'    => (int)($data['sisa_cns_mc'] ?? 0),
                    'lot'              => $data['lot'] ?? null,
                    'keterangan'       => $data['keterangan'] ?? null,
                    'po_tambahan'      => (isset($data['po_tambahan']) && strtoupper(trim($data['po_tambahan'])) === 'YA') ? '1' : '0',
                    'status_kirim'     => 'YA',
                    'admin'            => strtoupper($data['area']),
                    'additional_time'  => null,
                    'created_at'       => date('Y-m-d H:i:s'),
                ];

                if ($dt['tgl_pesan'] && $dt['jam_pesan']) {
                    $payload['tgl_pesan'] = $dt['tgl_pesan'] . ' ' . $dt['jam_pesan'];
                }

                $this->pemesananModel->insert($payload);
                $idPemesanan = (int)$this->pemesananModel->insertID();

                $this->totalPemesananModel->insert([
                    'ttl_jl_mc'     => (int)($data['jl_mc'] ?? 0),
                    'ttl_kg'        => (float)($data['ttl_berat_cones'] ?? 0),
                    'ttl_cns'       => (float)($data['ttl_qty_cones'] ?? 0),
                ]);
                $idTtlPemesanan = (int)$this->totalPemesananModel->insertID();

                $this->pemesananModel->update($idPemesanan, [
                    'id_total_pemesanan' => $idTtlPemesanan
                ]);

                $pengeluaranData = [
                    'id_total_pemesanan'    => $idTtlPemesanan,
                    'area_out'              => strtoupper($data['area']),
                    'tgl_out'               => $dt['tgl_pakai'],
                    'kgs_out'               => $data['kgs_pakai'],
                    'cns_out'               => $data['cns_pakai'] ?? 0,
                    'krg_out'               => $data['krg_pakai'] ?? 0,
                    'lot_out'               => $data['lot'],
                    'nama_cluster'          => $jalur,
                    'status'                => 'Pengiriman Area',
                    'keterangan_gbn'        => $data['ket'],
                    'admin'                 => session()->get('username'),
                    'created_at'            => date('Y-m-d H:i:s'),
                ];


                $this->pengeluaranModel->insert($pengeluaranData);

                $ok++;
            }

            // kalau ada error material tapi tetap mau commit hasil OK:
            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Terjadi error database saat import.');
            } else {
                $db->transCommit();
            }

            $msg = "Import selesai: OK=$ok | ERR=$err";
            if ($err) {
                $msg .= " | Detail: <ul>";
                foreach ($errLogs as $errLog) {
                    $msg .= "<li>" . htmlspecialchars($errLog) . "</li>";
                }
                $msg .= "</ul>";
            }
            return redirect()->back()->with($err ? 'error' : 'success', $msg);
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }


    /**
     * Cari huruf kolom dari nama header (case-insensitive),
     * cocok pas, kalau tidak ada coba "contains".
     */
    private function pickCol(array $colMap, string $name): ?string
    {
        $name = strtolower($name);
        foreach ($colMap as $col => $title) {
            if ($title === $name) return $col;
        }
        foreach ($colMap as $col => $title) {
            if ($title !== '' && strpos($title, $name) !== false) return $col;
        }
        return null;
    }

    /**
     * Ambil & normalisasi waktu_list, tgl_pesan, jam_pesan, tgl_pakai dari baris $r.
     * Kembalikan array: tgl_list (Y-m-d), jam_list (H:i:s), tgl_pesan, jam_pesan, tgl_pakai.
     */
    private function parseTanggalJam(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, array $colMap, int $r): array
    {
        $out = ['tgl_pesan' => null, 'jam_pesan' => null, 'tgl_pakai' => null];

        // $colWaktuList = $this->pickCol($colMap, 'waktu_list');
        $colTglPesan  = $this->pickCol($colMap, 'tgl_pesan');
        $colJamPesan  = $this->pickCol($colMap, 'jam_pesan');
        $colTglPakai  = $this->pickCol($colMap, 'tgl_pakai');

        // if ($colWaktuList) {
        //     $cell = $sheet->getCell($colWaktuList . $r);
        //     $d = $this->parseCellDateTime($cell);
        //     $out['tgl_list'] = $d['date'];
        //     $out['jam_list'] = $d['time'];
        // }
        if ($colTglPesan) {
            $cell = $sheet->getCell($colTglPesan . $r);
            $d = $this->parseCellDateTime($cell);
            $out['tgl_pesan'] = $d['date'];
        }
        if ($colJamPesan) {
            $cell = $sheet->getCell($colJamPesan . $r);
            $d = $this->parseCellDateTime($cell);
            $out['jam_pesan'] = $d['time'];
        }
        if ($colTglPakai) {
            $cell = $sheet->getCell($colTglPakai . $r);
            $d = $this->parseCellDateTime($cell);
            $out['tgl_pakai'] = $d['date'];
        }

        return $out;
    }

    /**
     * Parse 1 cell jadi ['date'=>Y-m-d|null, 'time'=>H:i:s|null].
     * Support:
     * - Serial Excel (cell formatted as date/time)
     * - "m/d/Y [H:i[:s]]"
     * - "Y-m-d"
     * - "YYYY年M月D日" (angka sampah di depan diabaikan)
     * - "YYYYMMDD"
     */
    private function parseCellDateTime(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell): array
    {
        $tz = new \DateTimeZone('Asia/Jakarta');
        $val = $cell->getValue();

        // Jika native date/time Excel
        if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
            // excelToDateTimeObject lebih aman untuk berbagai versi
            $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
            if ($dt instanceof \DateTimeInterface) {
                $dt = (new \DateTime($dt->format('Y-m-d H:i:s')))->setTimezone($tz);
                return ['date' => $dt->format('Y-m-d'), 'time' => $dt->format('H:i:s')];
            }
        }

        $s = is_string($val) ? trim($val) : '';
        if ($s === '') return ['date' => null, 'time' => null];

        // bersihkan zero-width/BOM & whitespace
        $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);
        $s = preg_replace('/\s+/u', ' ', $s);

        // Ambil time jika ada
        $time = null;
        if (preg_match('/\b(\d{1,2}):(\d{2})(?::(\d{2}))?\b/', $s, $m)) {
            $h = str_pad((string)min(23, (int)$m[1]), 2, '0', STR_PAD_LEFT);
            $i = str_pad((string)min(59, (int)$m[2]), 2, '0', STR_PAD_LEFT);
            $sec = isset($m[3]) ? str_pad((string)min(59, (int)$m[3]), 2, '0', STR_PAD_LEFT) : '00';
            $time = "$h:$i:$sec";
        }

        // 2025年6月30日 (abaikan angka di depan)
        if (preg_match('/(20\d{2})\D+(\d{1,2})\D+(\d{1,2})/u', $s, $m)) {
            $date = sprintf('%04d-%02d-%02d', (int)$m[1], max(1, min(12, (int)$m[2])), max(1, min(31, (int)$m[3])));
            return ['date' => $date, 'time' => $time];
        }

        // m/d/Y
        if (preg_match('/\b(\d{1,2})\/(\d{1,2})\/(\d{2,4})\b/', $s, $m)) {
            $y = (int)$m[3];
            if ($y < 100) $y += 2000;
            $date = sprintf('%04d-%02d-%02d', $y, max(1, min(12, (int)$m[1])), max(1, min(31, (int)$m[2])));
            return ['date' => $date, 'time' => $time];
        }

        // Y-m-d
        if (preg_match('/\b(\d{4})-(\d{1,2})-(\d{1,2})\b/', $s, $m)) {
            $date = sprintf('%04d-%02d-%02d', (int)$m[1], max(1, min(12, (int)$m[2])), max(1, min(31, (int)$m[3])));
            return ['date' => $date, 'time' => $time];
        }

        // YYYYMMDD
        if (preg_match('/\b(20\d{2})(\d{2})(\d{2})\b/', $s, $m)) {
            $date = sprintf('%04d-%02d-%02d', (int)$m[1], max(1, min(12, (int)$m[2])), max(1, min(31, (int)$m[3])));
            return ['date' => $date, 'time' => $time];
        }

        return ['date' => null, 'time' => $time];
    }

    public function formPoTambahan()
    {
        $apiUrl = 'http://172.23.39.117/CapacityApps/public/api/getNoModel';

        // Mengambil data dari API eksternal
        $response = @file_get_contents($apiUrl);

        if ($response === false) {
            log_message('error', 'Gagal mengambil data dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $model = json_decode($response, true);

        // Grouping berdasarkan factory
        $dataGrouped = [];
        foreach ($model as $row) {
            $factory = $row['factory'];
            $mastermodel = $row['mastermodel'];

            if (!isset($dataGrouped[$factory])) {
                $dataGrouped[$factory] = [];
            }
            $dataGrouped[$factory][] = $mastermodel;
        }
        // $noModel = array_unique(array_column($model, 'mastermodel'));
        $data = [
            'active' => 'PoTambahan',
            'area' => $dataGrouped,
            'title' => 'Po Tambahan',
            // 'area' => $area,
            'role' => session()->get('role'),
        ];
        return view(session()->get('role') . '/poplus/form-po-tambahan', $data);
    }

    public function poTambahanDetail($noModel, $area)
    {
        $idOrder = $this->masterOrderModel->getIdOrder($noModel);
        $materialData = $this->masterOrderModel->getMaterial($idOrder, $area);

        foreach ($materialData as $itemType => &$itemData) {
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

        // Ambil item_type saja (key dari level pertama JSON)
        $itemTypes = [];
        foreach ($materialData as $key => $value) {
            if (isset($value['item_type'])) {
                $itemTypes[] = [
                    'item_type' => $value['item_type']
                ];
            }
        }

        // Ambil semua style_size
        $styleSize = [];
        foreach ($materialData as $itemTypeData) {
            if (isset($itemTypeData['kode_warna']) && is_array($itemTypeData['kode_warna'])) {
                foreach ($itemTypeData['kode_warna'] as $kodeWarnaData) {
                    if (isset($kodeWarnaData['style_size']) && is_array($kodeWarnaData['style_size'])) {
                        foreach ($kodeWarnaData['style_size'] as $style) {
                            if (isset($style['style_size'])) {
                                $styleSize[] = $style['style_size'];
                            }
                        }
                    }
                }
            }
        }

        $styleSize = array_unique($styleSize);
        // log_message('debug', 'STYLE SIZE LIST: ' . print_r($styleSize, true));

        $apiUrl = 'http://172.23.39.117/CapacityApps/public/api/getSisaPerSize/' . $area . '/' . $noModel
            . '?styles[]=' . implode('&styles[]=', array_map('urlencode', $styleSize));

        $response = @file_get_contents($apiUrl);

        if ($response === false) {
            log_message('error', 'Gagal mengambil data dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $sisaAll = json_decode($response, true);
        // log_message('debug', 'SISA ALL: ' . print_r($sisaAll, true));

        // contoh akses hasil
        foreach ($sisaAll as $style => $data) {
            $qtyOrderList[$style] = (float)($data['qty'] ?? 0);
            $sisaOrderList[$style] = (float)($data['sisa'] ?? 0);
            $poPlusList[$style] = (float)($data['po_plus'] ?? 0);
        }

        // Ambil BS MESIN per style_size
        $bsMesinList = [];
        $apiUrl = 'http://172.23.39.117/CapacityApps/public/api/getBsMesin/' . $area . '/' . $noModel
            . '?styles[]=' . implode('&styles[]=', array_map('urlencode', $styleSize));
        // Mengambil data dari API eksternal
        $response = @file_get_contents($apiUrl);
        // log_message('debug', 'BS Mesin API response: ' . $response);
        if ($response === false) {
            log_message('error', 'Gagal mengambil data dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $bsMesinAll = json_decode($response, true);
        // log_message('debug', 'BS MC ALL: ' . print_r($bsMesinAll, true));

        // Simpan hasil per style
        foreach ($bsMesinAll as $style => $bsGram) {
            $bsMesinList[$style] = (float)$bsGram;
        }

        // Ambil BS SETTING per style_size
        $bsSettingList = [];
        $apiUrl = 'http://172.23.39.117/CapacityApps/public/api/getBsSetting'
            . '?area=' . urlencode($area)
            . '&no_model=' . urlencode($noModel)
            . '&styles[]=' . implode('&styles[]=', array_map('urlencode', $styleSize));

        $response = @file_get_contents($apiUrl);

        if ($response === false) {
            log_message('error', 'Gagal mengambil data BS Setting dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $bsSettingAll = json_decode($response, true);
        // log_message('debug', 'BS Setting ALL: ' . print_r($bsSettingAll, true));

        // Simpan hasil per style
        foreach ($bsSettingAll as $style => $qty) {
            $bsSettingList[$style] = (int)$qty;
        }

        $apiUrl = 'http://172.23.39.117/CapacityApps/public/api/getDataBruto'
            . '?area=' . rawurlencode($area)
            . '&no_model=' . rawurlencode($noModel)
            . '&styles[]=' . implode('&styles[]=', array_map('rawurlencode', $styleSize));

        $response = @file_get_contents($apiUrl);
        // log_message('debug', 'PPH API URL: ' . $apiUrl);

        if ($response === false) {
            log_message('error', 'Gagal mengambil data PPH dari API');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Tidak dapat mengambil data dari API']);
        }

        $prodAll = json_decode($response, true);

        $brutoList = [];

        foreach ($styleSize as $st) {
            $brutoList[$st] = isset($prodAll[$st]['bruto'])
                ? (float)$prodAll[$st]['bruto']
                : 0;
        }

        // log_message('debug', 'Prod All: ' . print_r($prodAll, true));
        // log_message('debug', 'Mat Data: ' . print_r($materialData, true));

        return $this->response->setJSON([
            'item_types' => $itemTypes,
            'material' => $materialData,
            'qty_order' => $qtyOrderList,
            'sisa_order' => $sisaOrderList,
            'bs_mesin' => $bsMesinList,
            'bs_setting' => $bsSettingList,
            'bruto' => $prodAll,
            'plusPck' => $poPlusList,
        ]);
    }

    public function savePoTambahan()
    {
        try {
            $req = $this->request->getJSON(true);
            if (empty($req) || !isset($req[0])) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Payload invalid.']);
            }

            // --- Siapkan items sesuai permintaan ---
            $items = array_map(function ($item) {
                return [
                    'area'              => $item['area'] ?? '',
                    'no_model'          => $item['no_model'] ?? '',
                    'item_type'         => $item['item_type'] ?? '',
                    'kode_warna'        => $item['kode_warna'] ?? '',
                    'color'             => $item['color'] ?? '',
                    'style_size'        => $item['style_size'] ?? '',
                    'ttl_terima_kg'     => (float) ($item['terima_kg'] ?? 0),
                    'ttl_sisa_jatah'    => (float) ($item['sisa_jatah'] ?? 0),
                    'ttl_sisa_bb_dimc'  => (float) ($item['sisa_bb_mc'] ?? 0),
                    'sisa_order_pcs'    => (float) ($item['sisa_order_pcs'] ?? 0),
                    'bs_mesin_kg'       => (float) ($item['bs_mesin_kg'] ?? 0),
                    'bs_st_pcs'         => (float) ($item['bs_st_pcs'] ?? 0),
                    'poplus_mc_kg'      => (float) ($item['poplus_mc_kg'] ?? 0),
                    'poplus_mc_cns'     => (float) ($item['poplus_mc_cns'] ?? 0),
                    'plus_pck_pcs'      => (float) ($item['plus_pck_pcs'] ?? 0),
                    'plus_pck_kg'       => (float) ($item['plus_pck_kg'] ?? 0),
                    'plus_pck_cns'      => (float) ($item['plus_pck_cns'] ?? 0),
                    'ttl_tambahan_kg'   => (float) ($item['total_kg_po'] ?? 0),
                    'ttl_tambahan_cns'  => (float) ($item['total_cns_po'] ?? 0),
                    'delivery_po_plus'  => $item['delivery_po_plus'] ?? '',
                    'keterangan'        => $item['keterangan'] ?? '',
                    'loss_aktual'       => (float) ($item['loss_aktual'] ?? 0),
                    'loss_tambahan'     => (float) ($item['loss_tambahan'] ?? 0),
                    'admin'             => $item['area'] ?? '',
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
            }, $req);

            if (empty($items)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Items tidak ditemukan.']);
            }

            $sukses = 0;
            $gagal  = 0;
            $today  = date('Y-m-d');

            // ambil sample dari item pertama (karena kombinasi no_model, item_type, kode_warna sama)
            $sample = $items[0];

            // --- cek apakah total_potambahan sudah ada ---
            $exist = $this->poTambahanModel
                ->select('po_tambahan.id_total_potambahan')
                ->join('material m', 'm.id_material = po_tambahan.id_material')
                ->join('master_order mo', 'mo.id_order = m.id_order')
                ->where('mo.no_model', $sample['no_model'])
                ->where('m.item_type', $sample['item_type'])
                ->where('m.kode_warna', $sample['kode_warna'])
                ->where('DATE(po_tambahan.created_at)', $today)
                ->first();

            // log_message('info', 'Sample data: ' . print_r($sample, true));
            // log_message('info', 'Exist data: ' . print_r($exist, true));

            if ($exist && $exist['id_total_potambahan']) {
                // --- update total yang sudah ada ---
                $idTotal = $exist['id_total_potambahan'];
                $ttlData = $this->totalPoTambahanModel->find($idTotal);

                $this->totalPoTambahanModel->update($idTotal, [
                    'ttl_terima_kg'    => ($sample['ttl_terima_kg'] ?? 0),
                    'ttl_sisa_jatah'   => ($sample['ttl_sisa_jatah'] ?? 0),
                    'ttl_sisa_bb_dimc' => ($ttlData['ttl_sisa_bb_dimc'] ?? 0) + ($sample['ttl_sisa_bb_dimc'] ?? 0),
                    'ttl_tambahan_kg'  => ($ttlData['ttl_tambahan_kg'] ?? 0) + ($sample['ttl_tambahan_kg'] ?? 0),
                    'ttl_tambahan_cns' => ($ttlData['ttl_tambahan_cns'] ?? 0) + ($sample['ttl_tambahan_cns'] ?? 0),
                ]);
            } else {
                // --- insert total baru ---
                // --- Insert total_potambahan baru ---
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
            }

            // --- loop kedua: insert detail ---
            foreach ($items as $item) {
                $idMat = $this->materialModel->getIdMaterial([
                    'no_model'   => $item['no_model'],
                    'area'       => $item['area'],
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
                    // 'lebih_pakai_kg'      => $item['lebih_pakai_kg'] ?? 0,
                    'delivery_po_plus'    => $item['delivery_po_plus'] ?? '',
                    'admin'               => $item['area'] ?? '',
                    'created_at'          => date('Y-m-d H:i:s'),
                ];

                if ($this->poTambahanModel->insert($dataDetail)) {
                    $sukses++;
                } else {
                    $gagal++;
                }
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'sukses'  => $sukses,
                'gagal'   => $gagal,
                'message' => "Sukses insert: $sukses, Gagal insert: $gagal",
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in savePoTambahan: ' . $e);
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => 'failed',
                    'sukses'  => 0,
                    'gagal'   => 1,
                    'message' => "Gagal insert: " . $e->getMessage(),
                ]);
        }
    }
}
