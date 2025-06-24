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
    protected $request;


    public function __construct()
    {

        $this->stockModel = new StockModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->clusterModel = new ClusterModel();
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

            $count     = 0;
            $errorLogs = [];

            function normalizeItemType(string $rawType): string
            {
                // mapping kata → singkatan
                $map = [
                    'COTTON'  => 'CTN',
                    'ORGANIC' => 'ORG',
                    'SPNDX' => 'SPD',
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
                        ->where('item_type', $normalizedType)
                        ->where('kode_warna', $kodeWarna)
                        ->findAll();
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
                        'warna'            => $color,
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

                    // Insert out_celup
                    $outCelupMdl->insert([
                        'id_celup'    => $idCelup,
                        'no_model'    => $noModel,
                        'l_m_d'       => floatval(str_replace(',', '.', $row[3])),
                        'kgs_kirim'   => $kgCelup,
                        'cones_kirim' => floatval(str_replace(',', '.', $row[13])),
                        'lot_kirim'   => trim(ltrim($row[16], ', ')),
                        'ganti_retur' => 0,
                        'admin'       => session()->get('username'),
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);

                    $idOutCelup = $outCelupMdl->getInsertID();

                    // cek cluster
                    $clusterName = strtoupper(trim($row[1]));
                    if (! $this->clusterModel->where('nama_cluster', $clusterName)->first()) {
                        $msg = sprintf(
                            "Baris %d dilewati: cluster '%s' tidak ditemukan.",
                            $line,
                            $clusterName
                        );
                        log_message('warning', 'ImportStock: ' . $msg);
                        $errorLogs[] = $msg;
                        continue;
                    }

                    // Insert pemasukan
                    $this->pemasukanModel->insert([
                        'id_out_celup' => $idOutCelup,
                        'tgl_masuk'    => date('Y-m-d'),
                        'nama_cluster' => $clusterName,
                        'out_jalur'    => '0',
                        'admin'        => session()->get('username'),
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);

                    $idPemasukan = $this->pemasukanModel->getInsertID();

                    // Insert stock
                    $this->stockModel->insert([
                        'no_model'      => $noModel,
                        'item_type'     => $normalizedType,
                        'kode_warna'    => $kodeWarna,
                        'warna'         => $color,
                        'kgs_stock_awal' => 0,
                        'cns_stock_awal' => 0,
                        'krg_stock_awal' => 0,
                        'lot_awal'      => '',
                        'kgs_in_out'    => $kgCelup,
                        'cns_in_out'    => floatval(str_replace(',', '.', $row[13])),
                        'krg_in_out'    => trim($row[14]),
                        'lot_stock'     => trim(ltrim($row[16], ', ')),
                        'nama_cluster'  => $clusterName,
                        'admin'         => session()->get('username'),
                        'created_at'    => date('Y-m-d H:i:s')
                    ]);

                    $idStock = $this->stockModel->getInsertID();
                    $this->pemasukanModel->update($idPemasukan, ['id_stock' => $idStock]);

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
}
