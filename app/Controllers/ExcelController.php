<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\BonCelupModel;
use App\Models\OutCelupModel;
use App\Models\PemasukanModel;
use App\Models\ScheduleCelupModel;
use App\Models\StockModel;
use App\Models\PemesananModel;
use App\Models\PengeluaranModel;
use App\Models\HistoryStockCoveringModel;
use App\Models\TotalPemesananModel;
use App\Models\ReturModel;
use App\Models\MesinCelupModel;
use App\Models\CoveringStockModel;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpParser\Node\Stmt\Else_;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ExcelController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $bonCelupModel;
    protected $outCelupModel;
    protected $pemasukanModel;
    protected $scheduleCelupModel;
    protected $stockModel;
    protected $pemesananModel;
    protected $pengeluaranModel;
    protected $historyCoveringStockModel;
    protected $totalPemesananModel;
    protected $returModel;
    protected $mesinCelupModel;
    protected $coveringStockModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->stockModel = new StockModel();
        $this->pemesananModel = new PemesananModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->historyCoveringStockModel = new HistoryStockCoveringModel();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->returModel = new ReturModel();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->coveringStockModel = new CoveringStockModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['monitoring']] != session()->get('role')) {
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
    public function excelPPHNomodel($area, $model)
    {
        $models = $this->materialModel->getMaterialForPPH($model);

        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $loss = $items['loss'];
            $gwpcs = ($gw * $comp) / 100;
            $styleSize = urlencode($styleSize);
            $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataPerinisial/' . $area . '/' . $model . '/' . $styleSize;

            $response = file_get_contents($apiUrl);

            if ($response === FALSE) {
                log_message('error', "API tidak bisa diakses: $apiUrl");
                return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
            } else {
                $data = json_decode($response, true);

                if (!is_array($data)) {
                    log_message('error', "Response API tidak valid: $response");
                    return $this->response->setJSON(["error" => "Data dari API tidak valid"]);
                }

                $bruto = $data['bruto'] ?? 0;
                $bs_mesin = $data['bs_mesin'] ?? 0;
                if ($gw == 0) {
                    $pph = 0;
                } else {
                    $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;
                }
                $ttl_kebutuhan = ($data['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($data['qty'] * $comp * $gw / 100 / 1000));



                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'      => $items['kode_warna'],
                    'color'      => $items['color'],
                    'gw'         => $items['gw'],
                    'composition' => $items['composition'],
                    'kgs'  => $ttl_kebutuhan,
                    'jarum'      => $data['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'qty'        => $data['qty'] ?? 0,
                    'sisa'       => $data['sisa'] ?? 0,
                    'po_plus'    => $data['po_plus'] ?? 0,
                    'bs_setting' => $data['bs_setting'] ?? 0,
                    'bs_mesin'   => $bs_mesin,
                    'pph'        => $pph
                ];
            }
        }
        $result = [
            'qty' => 0,
            'sisa' => 0,
            'bruto' => 0,
            'bs_setting' => 0,
            'bs_mesin' => 0
        ];

        $processedStyleSizes = []; // Untuk memastikan style_size tidak dihitung lebih dari sekali
        $temporaryData = []; // Untuk menyimpan data sementara dari style_size

        foreach ($pphInisial as $item) {
            $key = $item['item_type'] . '-' . $item['kode_warna'];
            $styleSizeKey = $item['style_size'];

            // Jika style_size sudah ada, jangan tambahkan lagi
            if (!isset($processedStyleSizes[$styleSizeKey])) {
                $temporaryData[] = [
                    'qty' => $item['qty'],
                    'sisa' => $item['sisa'],
                    'bruto' => $item['bruto'],
                    'bs_setting' => $item['bs_setting'],
                    'bs_mesin' => $item['bs_mesin']
                ];
                $processedStyleSizes[$styleSizeKey] = true;
            }

            if (!isset($result[$key])) {
                $result[$key] = [
                    'item_type' => $item['item_type'],
                    'kode_warna' => $item['kode_warna'],
                    'warna' => $item['color'],
                    'kgs' => 0,
                    'pph' => 0,
                    'jarum' => $item['jarum'],
                    'area' => $item['area']
                ];
            }

            // Akumulasi data berdasarkan item_type-kode_warna
            $result[$key]['kgs'] += $item['kgs'];
            $result[$key]['pph'] += $item['pph'];
        }

        // Menambahkan total dari style_size yang unik ke dalam result
        foreach ($temporaryData as $res) {
            $result['qty'] += $res['qty'];
            $result['sisa'] += $res['sisa'];
            $result['bruto'] += $res['bruto'];
            $result['bs_setting'] += $res['bs_setting'];
            $result['bs_mesin'] += $res['bs_mesin'];
        }

        // Hapus semua elemen dengan format style_size dari $result
        foreach (array_keys($result) as $key) {
            if (preg_match('/^\w+\s*\d+[Xx]\d+$/', $key)) {
                unset($result[$key]);
            }
        }

        $dataToSort = array_filter($result, 'is_array');

        usort($dataToSort, function ($a, $b) {
            return $a['item_type'] <=> $b['item_type'] ?: $a['kode_warna'] <=> $b['kode_warna'];
        });
        // dd($result);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        $styleBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Judul
        $sheet->setCellValue('A1', 'PPH Per Model ' . $model);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data Header
        $sheet->setCellValue('A2', 'Area');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A3', 'Qty');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A4', 'Sisa');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B2', ': ' . $area);
        $sheet->getStyle('B2')->getFont()->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B3', ': ' . number_format($result['qty'] / 24, 2));
        $sheet->getStyle('B3')->getFont()->setSize(12);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B4', ': ' . number_format($result['sisa'] / 24, 2));
        $sheet->getStyle('B4')->getFont()->setSize(12);
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D2', 'Produksi');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D3', 'Bs Setting');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D4', 'Bs Mesin');
        $sheet->getStyle('D4')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E2', ': ' . number_format($result['bruto'] / 24, 2));
        $sheet->getStyle('E2')->getFont()->setSize(12);
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E3', ': ' . number_format($result['bs_setting'] / 24, 2));
        $sheet->getStyle('E3')->getFont()->setSize(12);
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E4', ': ' . number_format($result['bs_mesin'], 2));
        $sheet->getStyle('E4')->getFont()->setSize(12);
        $sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $row_header = 5;

        $sheet->setCellValue('A' . $row_header, 'No');
        $sheet->setCellValue('B' . $row_header, 'Jenis');
        $sheet->setCellValue('C' . $row_header, 'Kode Warna');
        $sheet->setCellValue('D' . $row_header, 'Warna');
        $sheet->setCellValue('E' . $row_header, 'PO (kg)');
        $sheet->setCellValue('F' . $row_header, 'PPH (kg)');

        $sheet->getStyle('A' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row_header)->applyFromArray($styleHeader);

        // Isi data
        $row = 6;
        $no = 1;

        foreach ($dataToSort as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['item_type']);
            $sheet->setCellValue('C' . $row, $data['kode_warna']);
            $sheet->setCellValue('D' . $row, $data['warna']);
            $sheet->setCellValue('E' . $row, number_format($data['kgs'], 2));
            $sheet->setCellValue('F' . $row, number_format($data['pph'], 2));

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'PPH PER MODEL ' . $model . ' Area ' . $area . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function excelPPHInisial($area, $model)
    {
        $models = $this->materialModel->getMaterialForPPH($model);
        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
            $loss = $items['loss'];
            $gwpcs = ($gw * $comp) / 100;
            $styleSize = urlencode($styleSize);
            $apiUrl  = 'http://172.23.44.14/CapacityApps/public/api/getDataPerinisial/' . $area . '/' . $model . '/' . $styleSize;

            $response = file_get_contents($apiUrl);

            if ($response === FALSE) {
                log_message('error', "API tidak bisa diakses: $apiUrl");
                return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
            } else {
                $data = json_decode($response, true);

                if (!is_array($data)) {
                    log_message('error', "Response API tidak valid: $response");
                    return $this->response->setJSON(["error" => "Data dari API tidak valid"]);
                }

                $bruto = $data['bruto'] ?? 0;
                $bs_mesin = $data['bs_mesin'] ?? 0;
                if ($gw == 0) {
                    $pph = 0;
                } else {

                    $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;
                }
                $ttl_kebutuhan = ($data['qty'] * $comp * $gw / 100 / 1000) + ($loss / 100 * ($data['qty'] * $comp * $gw / 100 / 1000));



                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'  => $items['kode_warna'],
                    'color'      => $items['color'],
                    'ttl_kebutuhan' => $ttl_kebutuhan,
                    'gw'         => $items['gw'],
                    'loss'        => $items['loss'],
                    'composition' => $items['composition'],
                    'jarum'      => $data['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'netto'      => $bruto - $data['bs_setting'] ?? 0,
                    'qty'        => $data['qty'] ?? 0,
                    'sisa'       => $data['sisa'] ?? 0,
                    'po_plus'    => $data['po_plus'] ?? 0,
                    'bs_setting' => $data['bs_setting'] ?? 0,
                    'bs_mesin'   => $bs_mesin,
                    'pph'        => $pph,
                    'pph_persen' => ($ttl_kebutuhan != 0) ? ($pph / $ttl_kebutuhan) * 100 : 0,
                ];
            }
        }

        $dataToSort = array_filter($pphInisial, 'is_array');

        usort($dataToSort, function ($a, $b) {
            return $a['inisial'] <=> $b['inisial']
                ?: $a['item_type'] <=> $b['item_type']
                ?: $a['kode_warna'] <=> $b['kode_warna'];
        });
        // dd($result);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        $styleBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Judul
        $sheet->setCellValue('A1', 'PPH Per Inisial');
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data Header
        $sheet->setCellValue('A2', 'Area');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B2', ': ' . $area);
        $sheet->getStyle('B2')->getFont()->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A3', 'No Model');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B3', ': ' . $model);
        $sheet->getStyle('B3')->getFont()->setSize(12);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $row_header = 4;

        $sheet->setCellValue('A' . $row_header, 'No');
        $sheet->setCellValue('B' . $row_header, 'Jarum');
        $sheet->setCellValue('C' . $row_header, 'Inisial');
        $sheet->setCellValue('D' . $row_header, 'Style Size');
        $sheet->setCellValue('E' . $row_header, 'Jenis');
        $sheet->setCellValue('F' . $row_header, 'Kode Warna');
        $sheet->setCellValue('G' . $row_header, 'Warna');
        $sheet->setCellValue('H' . $row_header, 'Loss (%)');
        $sheet->setCellValue('I' . $row_header, 'Komposisi (%)');
        $sheet->setCellValue('J' . $row_header, 'GW (gr)');
        $sheet->setCellValue('K' . $row_header, 'Qty PO (dz)');
        $sheet->setCellValue('L' . $row_header, 'Total Kebutuhan (kg)');
        $sheet->setCellValue('M' . $row_header, 'Netto (dz)');
        $sheet->setCellValue('N' . $row_header, 'Bs MC (gr)');
        $sheet->setCellValue('O' . $row_header, 'Bs Setting (dz)');
        $sheet->setCellValue('P' . $row_header, 'PPH (kg)');
        $sheet->setCellValue('Q' . $row_header, 'PPH (%)');

        $sheet->getStyle('A' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('G' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('H' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('I' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('J' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('K' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('L' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('M' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('N' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('O' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('P' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('Q' . $row_header)->applyFromArray($styleHeader);

        // Isi data
        $row = 5;
        $no = 1;

        foreach ($dataToSort as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['jarum']);
            $sheet->setCellValue('C' . $row, $data['inisial']);
            $sheet->setCellValue('D' . $row, $data['style_size']);
            $sheet->setCellValue('E' . $row, $data['item_type']);
            $sheet->setCellValue('F' . $row, $data['kode_warna']);
            $sheet->setCellValue('G' . $row, $data['color']);
            $sheet->setCellValue('H' . $row, number_format($data['loss'], 2));
            $sheet->setCellValue('I' . $row, number_format($data['composition'], 2));
            $sheet->setCellValue('J' . $row, number_format($data['gw'], 2));
            $sheet->setCellValue('K' . $row, number_format($data['qty'] / 24, 2));
            $sheet->setCellValue('L' . $row, number_format($data['ttl_kebutuhan'], 2));
            $sheet->setCellValue('M' . $row, number_format($data['netto'] / 24, 2));
            $sheet->setCellValue('N' . $row, number_format($data['bs_mesin'], 2));
            $sheet->setCellValue('O' . $row, number_format($data['bs_setting'] / 24, 2));
            $sheet->setCellValue('P' . $row, number_format($data['pph'], 2));
            $sheet->setCellValue('Q' . $row, number_format($data['pph_persen'], 2) . '%');

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'PPH PER MODEL ' . $model . ' Area ' . $area . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function excelPPHDays($area, $tanggal)
    {
        $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getPPhPerhari/' . $area . '/' . $tanggal;

        $response = file_get_contents($apiUrl);
        if ($response === false) {
            log_message('error', "API tidak bisa diakses: $apiUrl");
            return $this->response->setJSON(["error" => "Gagal mengambil data dari API"]);
        }

        $data = json_decode($response, true);
        $result = [];
        $pphInisial = [];

        foreach ($data as $prod) {
            $key = $prod['mastermodel'] . '-' . $prod['size'];

            $material = $this->materialModel->getMU($prod['mastermodel'], $prod['size']);
            if (empty($material)) {
                $result[$prod['mastermodel']] = [
                    'mastermodel' => $prod['mastermodel'],
                    'item_type' => null,
                    'kode_warna' => null,
                    'warna' => null,
                    'pph' => 0,
                    'bruto' => $prod['prod'],
                    'bs_mesin' => $prod['bs_mesin'],
                ];
            } else {
                foreach ($material as $mtr) {
                    $gw = $mtr['gw'];
                    $comp = $mtr['composition'];
                    $gwpcs = ($gw * $comp) / 100;

                    $bruto = $prod['prod'] ?? 0;
                    $bs_mesin = $prod['bs_mesin'] ?? 0;
                    $pph = ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;


                    $pphInisial[] = [
                        'mastermodel'    => $prod['mastermodel'],
                        'style_size'  => $prod['size'],
                        'item_type'   => $mtr['item_type'] ?? null,
                        'kode_warna'  => $mtr['kode_warna'] ?? null,
                        'color'       => $mtr['color'] ?? null,
                        'gw'          => $gw,
                        'composition' => $comp,
                        'bruto'       => $bruto,
                        'qty'         => $prod['qty'] ?? 0,
                        'sisa'        => $prod['sisa'] ?? 0,
                        'bs_mesin'    => $bs_mesin,
                        'pph'         => $pph
                    ];
                }
            }
        }

        // Grouping & Summing Data
        foreach ($pphInisial as $item) {
            $key = $item['mastermodel'] . '-' . $item['item_type'] . '-' . $item['kode_warna'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'mastermodel' => $item['mastermodel'],
                    'item_type'   => $item['item_type'],
                    'kode_warna'  => $item['kode_warna'],
                    'warna'       => $item['color'],
                    'pph'         => 0,
                    'bruto'       => 0,
                    'bs_mesin'    => 0,
                ];
            }

            // Accumulate values correctly

            $result[$key]['bruto'] += $item['bruto'];
            $result[$key]['bs_mesin'] += $item['bs_mesin'];
            $result[$key]['pph'] += $item['pph'];
        }

        $dataToSort = array_filter($result, 'is_array');

        usort($dataToSort, function ($a, $b) {
            if ($a['mastermodel'] !== $b['mastermodel']) {
                return $a['mastermodel'] <=> $b['mastermodel'];
            }
            if ($a['item_type'] !== $b['item_type']) {
                return $a['item_type'] <=> $b['item_type'];
            }
            return $a['kode_warna'] <=> $b['kode_warna'];
        });

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // border
        $styleHeader = [
            'font' => [
                'bold' => true, // Tebalkan teks
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];
        $styleBody = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Alignment rata tengah
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN, // Gaya garis tipis
                    'color' => ['argb' => 'FF000000'],    // Warna garis hitam
                ],
            ],
        ];

        // Judul
        $sheet->setCellValue('A1', 'PPH Area ' . $area . ' Tanggal ' . $tanggal);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Tabel
        $row_header = 3;

        $sheet->setCellValue('A' . $row_header, 'No');
        $sheet->setCellValue('B' . $row_header, 'No Model');
        $sheet->setCellValue('C' . $row_header, 'Item Type');
        $sheet->setCellValue('D' . $row_header, 'Kode Warna');
        $sheet->setCellValue('E' . $row_header, 'Warna');
        $sheet->setCellValue('F' . $row_header, 'Bruto (Dz)');
        $sheet->setCellValue('G' . $row_header, 'Bs Mesin (Gram)');
        $sheet->setCellValue('H' . $row_header, 'PPH (Kg)');

        $sheet->getStyle('A' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('F' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('G' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('H' . $row_header)->applyFromArray($styleHeader);

        // Isi data
        $row = 4;
        $no = 1;

        foreach ($dataToSort as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['mastermodel']);
            $sheet->setCellValue('C' . $row, $data['item_type']);
            $sheet->setCellValue('D' . $row, $data['kode_warna']);
            $sheet->setCellValue('E' . $row, $data['warna']);
            $sheet->setCellValue('F' . $row, number_format($data['bruto'] / 24, 2));
            $sheet->setCellValue('G' . $row, number_format($data['bs_mesin'], 2));
            $sheet->setCellValue('H' . $row, number_format($data['pph'], 2));

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'PPH Area ' . $area . ' Tanggal ' . $tanggal . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportDatangBenang()
    {
        $key = $this->request->getGet('key');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemasukanModel->getFilterDatangBenang($key, $tanggal_awal, $tanggal_akhir);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Datang Benang');
        $sheet->mergeCells('A1:U1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Foll Up", "No Model", "No Order", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Kode Warna", "Warna", "KG Pesan", "Tanggal Datang", "Kgs Datang", "Cones Datang", "LOT Datang", "No Surat Jalan", "LMD", "GW", "Harga", "Nama Cluster"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:U3')->getFont()->setBold(true);
        $sheet->getStyle('A3:U3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:U3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['foll_up'],
                    $item['no_model'],
                    $item['no_order'],
                    $item['buyer'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['unit'],
                    $item['item_type'],
                    $item['kode_warna'],
                    $item['warna'],
                    $item['kg_po'],
                    $item['tgl_masuk'],
                    $item['kgs_kirim'],
                    $item['cones_kirim'],
                    $item['lot_kirim'],
                    $item['no_surat_jalan'],
                    $item['l_m_d'],
                    $item['gw_kirim'],
                    $item['harga'],
                    $item['nama_cluster']
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
        $sheet->getStyle('A3:U' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'U') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Datang_Benang_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportPoBenang()
    {
        $key = $this->request->getGet('key');

        $data = $this->openPoModel->getFilterPoBenang($key);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report PO Benang');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Waktu Input", "Tanggal PO", "Foll Up", "No Model", "No Order", "Keterangan", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Jenis", "Kode Warna", "Warna", "KG Pesan"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['created_at'],
                    $item['tgl_po'],
                    $item['foll_up'],
                    $item['no_model'],
                    $item['no_order'],
                    $item['keterangan'],
                    $item['buyer'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['unit'],
                    $item['item_type'],
                    $item['jenis'],
                    $item['kode_warna'],
                    $item['color'],
                    $item['kg_po'],
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
        $sheet->getStyle('A3:P' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Po_Benang_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportScheduleBenang()
    {
        $key = $this->request->getGet('key');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchBenang($key, $tanggal_schedule, $tanggal_awal, $tanggal_akhir);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Schedule Benang');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "Qty Celup", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item->no_mesin,
                    $item->ket_mesin,
                    $item->lot_urut,
                    $item->no_model,
                    $item->item_type,
                    $item->kode_warna,
                    $item->warna,
                    $item->start_mc,
                    $item->delivery_awal,
                    $item->delivery_akhir,
                    $item->tanggal_schedule,
                    $item->total_kgs,
                    $item->kg_celup,
                    $item->lot_celup,
                    $item->tanggal_celup,
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
        $sheet->getStyle('A3:P' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Schedule_Benang' . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportScheduleNylon()
    {
        $key = $this->request->getGet('key');
        $tanggal_schedule = $this->request->getGet('tanggal_schedule');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchNylon($key, $tanggal_schedule, $tanggal_awal, $tanggal_akhir);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Schedule Nylon');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "Qty Celup", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item->no_mesin,
                    $item->ket_mesin,
                    $item->lot_urut,
                    $item->no_model,
                    $item->item_type,
                    $item->kode_warna,
                    $item->warna,
                    $item->start_mc,
                    $item->delivery_awal,
                    $item->delivery_akhir,
                    $item->tanggal_schedule,
                    $item->total_kgs,
                    $item->kg_celup,
                    $item->lot_celup,
                    $item->tanggal_celup,
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
        $sheet->getStyle('A3:P' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Schedule_Nylon' . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function excelStockMaterial()
    {
        $noModel = $this->request->getGet('no_model');
        $warna = $this->request->getGet('warna');
        $filteredData = $this->stockModel->searchStock($noModel, $warna);

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $title = 'DATA STOCK MATERIAL';
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', $title);

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // === Header Kolom di Baris 2 === //
        $sheet->setCellValue('A3', 'No Model');
        $sheet->setCellValue('B3', 'Kode Warna');
        $sheet->setCellValue('C3', 'Warna');
        $sheet->setCellValue('D3', 'Item Type');
        $sheet->setCellValue('E3', 'Lot Stock');
        $sheet->setCellValue('F3', 'Nama Cluster');
        $sheet->setCellValue('G3', 'Kapasitas');
        $sheet->setCellValue('H3', 'Kgs');
        $sheet->setCellValue('I3', 'Krg');
        $sheet->setCellValue('J3', 'Cns');
        $sheet->setCellValue('K3', 'Kgs Stock Awal');
        $sheet->setCellValue('L3', 'Krg Stock Awal');
        $sheet->setCellValue('M3', 'Cns Stock Awal');
        $sheet->setCellValue('N3', 'Lot Awal');

        // === Isi Data mulai dari baris ke-3 === //
        $row = 4;
        foreach ($filteredData as $data) {
            if ($data->Kgs != 0 || $data->KgsStockAwal != 0) {
                $sheet->setCellValue('A' . $row, $data->no_model);
                $sheet->setCellValue('B' . $row, $data->kode_warna);
                $sheet->setCellValue('C' . $row, $data->warna);
                $sheet->setCellValue('D' . $row, $data->item_type);
                $sheet->setCellValue('E' . $row, $data->lot_stock);
                $sheet->setCellValue('F' . $row, $data->nama_cluster);
                $sheet->setCellValue('G' . $row, $data->kapasitas);
                $sheet->setCellValue('H' . $row, $data->Kgs);
                $sheet->setCellValue('I' . $row, $data->Krg);
                $sheet->setCellValue('J' . $row, $data->Cns);
                $sheet->setCellValue('K' . $row, $data->KgsStockAwal);
                $sheet->setCellValue('L' . $row, $data->KrgStockAwal);
                $sheet->setCellValue('M' . $row, $data->CnsStockAwal);
                $sheet->setCellValue('N' . $row, $data->lot_awal);
                $row++;
            }
        }

        // === Auto Size Kolom A - M === //
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Tambahkan Border (A2:M[row - 1]) === //
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $lastDataRow = $row - 1;
        $sheet->getStyle("A3:N{$lastDataRow}")->applyFromArray($styleArray);

        $filename = 'Data_Stock_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPemesananArea()
    {
        $key = $this->request->getGet('key');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        // Ambil data hasil filter dari model
        $filteredData = $this->pemesananModel->getFilterPemesananArea($key, $tanggal_awal, $tanggal_akhir);
        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === Tambahkan Judul Header di Tengah === //
        $title = 'DATA PEMESANAN AREA';
        $sheet->mergeCells('A1:V1'); // Gabungkan dari kolom A sampai M
        $sheet->setCellValue('A1', $title);

        // Format judul (bold + center)
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // === Header Kolom di Baris 2 === //
        $sheet->setCellValue('A3', 'Foll Up');
        $sheet->setCellValue('B3', 'No Model');
        $sheet->setCellValue('C3', 'No Order');
        $sheet->setCellValue('D3', 'Area');
        $sheet->setCellValue('E3', 'Buyer');
        $sheet->setCellValue('F3', 'Delivery Awal');
        $sheet->setCellValue('G3', 'Delivery Akhir');
        $sheet->setCellValue('H3', 'Order Type');
        $sheet->setCellValue('I3', 'Item Type');
        $sheet->setCellValue('J3', 'Kode Warna');
        $sheet->setCellValue('K3', 'Warna');
        $sheet->setCellValue('L3', 'Tanggal List');
        $sheet->setCellValue('M3', 'Tanggal Pesan');
        $sheet->setCellValue('N3', 'Tanggal Pakai');
        $sheet->setCellValue('O3', 'Jalan MC');
        $sheet->setCellValue('P3', 'Cones Pesan');
        $sheet->setCellValue('Q3', 'Kg Pesan');
        $sheet->setCellValue('R3', 'Sisa Kgs MC');
        $sheet->setCellValue('S3', 'Sisa Cones MC');
        $sheet->setCellValue('T3', 'LOT');
        $sheet->setCellValue('U3', 'PO(+)');
        $sheet->setCellValue('V3', 'Keterangan');
        $sheet->setCellValue('W3', 'Area');

        // === Isi Data mulai dari baris ke-3 === //
        $row = 4;
        foreach ($filteredData as $data) {
            $sheet->setCellValue('A' . $row, $data['foll_up']);
            $sheet->setCellValue('B' . $row, $data['no_model']);
            $sheet->setCellValue('C' . $row, $data['no_order']);
            $sheet->setCellValue('D' . $row, $data['area']);
            $sheet->setCellValue('E' . $row, $data['buyer']);
            $sheet->setCellValue('F' . $row, $data['delivery_awal']);
            $sheet->setCellValue('G' . $row, $data['delivery_akhir']);
            $sheet->setCellValue('H' . $row, $data['unit']);
            $sheet->setCellValue('I' . $row, $data['item_type']);
            $sheet->setCellValue('J' . $row, $data['kode_warna']);
            $sheet->setCellValue('K' . $row, $data['color']);
            $sheet->setCellValue('L' . $row, $data['tgl_list']);
            $sheet->setCellValue('M' . $row, $data['tgl_pesan']);
            $sheet->setCellValue('N' . $row, $data['tgl_pakai']);
            $sheet->setCellValue('O' . $row, $data['jl_mc']);
            $sheet->setCellValue('P' . $row, $data['ttl_qty_cones']);
            $sheet->setCellValue('Q' . $row, $data['ttl_berat_cones']);
            $sheet->setCellValue('R' . $row, $data['sisa_kgs_mc']);
            $sheet->setCellValue('S' . $row, $data['sisa_cones_mc']);
            $sheet->setCellValue('T' . $row, $data['lot']);
            $sheet->setCellValue('U' . $row, $data['po_tambahan']);
            $sheet->setCellValue('V' . $row, $data['keterangan']);
            $sheet->setCellValue('W' . $row, $data['admin']);
            $row++;
        }

        // === Auto Size Kolom A - V === //
        foreach (range('A', 'V') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // === Tambahkan Border (A2:M[row - 1]) === //
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $lastDataRow = $row - 1; // baris terakhir data
        $sheet->getStyle("A3:W{$lastDataRow}")->applyFromArray($styleArray);

        // === Export File Excel === //
        $filename = 'Data_Pemesanan_Area_' . date('YmdHis') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPemasukanCovering()
    {
        $date = $this->request->getGet('date');
        $data = $this->historyCoveringStockModel->getPemasukanByDate($date);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REPORT PEMASUKAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['jenis']);
            $sheet->setCellValue('B' . $row, $item['color']);
            $sheet->setCellValue('C' . $row, $item['code']);
            $sheet->setCellValue('D' . $row, $item['lmd']);
            $sheet->setCellValue('E' . $row, $item['ttl_cns']);
            $sheet->setCellValue('F' . $row, $item['ttl_kg']);
            $sheet->setCellValue('G' . $row, $item['keterangan']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:G{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pemasukan_Covering_' . $date . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPengeluaranCovering()
    {
        $date = $this->request->getGet('date');
        $data = $this->historyCoveringStockModel->getPengeluaranByDate($date);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'REPORT PENGELUARAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No Model', 'Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['no_model']);
            $sheet->setCellValue('B' . $row, $item['jenis']);
            $sheet->setCellValue('C' . $row, $item['color']);
            $sheet->setCellValue('D' . $row, $item['code']);
            $sheet->setCellValue('E' . $row, $item['lmd']);
            $sheet->setCellValue('F' . $row, $item['ttl_cns']);
            $sheet->setCellValue('G' . $row, $item['ttl_kg']);
            $sheet->setCellValue('H' . $row, $item['keterangan']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:H{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pengeluaran_Covering_' . $date . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPemesananKaretCovering()
    {
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $data = $this->pemesananModel->getFilterPemesananKaret($tanggal_awal, $tanggal_akhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Judul
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'REPORT PEMESANAN KARET COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Tanggal Pakai', 'Item Type', 'Warna', 'Kode Warna', 'No Model', 'Jalan MC', 'Total Pesan (Kg)', 'Cones', 'Area', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['tgl_pakai']);
            $sheet->setCellValue('C' . $row, $item['item_type']);
            $sheet->setCellValue('D' . $row, $item['color']);
            $sheet->setCellValue('E' . $row, $item['kode_warna']);
            $sheet->setCellValue('F' . $row, $item['no_model']);
            $sheet->setCellValue('G' . $row, $item['jl_mc']);
            $sheet->setCellValue('H' . $row, $item['ttl_berat_cones']);
            $sheet->setCellValue('I' . $row, $item['ttl_qty_cones']);
            $sheet->setCellValue('J' . $row, $item['admin']);
            $sheet->setCellValue('K' . $row, $item['keterangan']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:K{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pemesanan_Karet' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPemesananSpandexCovering()
    {
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $data = $this->pemesananModel->getFilterPemesananSpandex($tanggal_awal, $tanggal_akhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Judul
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'REPORT PEMESANAN SPANDEX COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Tanggal Pakai', 'Item Type', 'Warna', 'Kode Warna', 'No Model', 'Jalan MC', 'Total Pesan (Kg)', 'Cones', 'Area', 'Keterangan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['tgl_pakai']);
            $sheet->setCellValue('C' . $row, $item['item_type']);
            $sheet->setCellValue('D' . $row, $item['color']);
            $sheet->setCellValue('E' . $row, $item['kode_warna']);
            $sheet->setCellValue('F' . $row, $item['no_model']);
            $sheet->setCellValue('G' . $row, $item['jl_mc']);
            $sheet->setCellValue('H' . $row, $item['ttl_berat_cones']);
            $sheet->setCellValue('I' . $row, $item['ttl_qty_cones']);
            $sheet->setCellValue('J' . $row, $item['admin']);
            $sheet->setCellValue('K' . $row, $item['keterangan']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:K{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pemesanan_Spandex' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelMasterOrder()
    {
        $key = $this->request->getGet('key');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $data = $this->masterOrderModel->getFilterMasterOrder($key, $tanggal_awal, $tanggal_akhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'REPORT MASTER ORDER');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'No Order', 'No Model', 'Buyer', 'Foll Up', 'LCO Date', 'Memo', 'Delivery Awal', 'Delivery Akhir', 'Unit', 'Admin', 'Created At', 'Created By', 'Updated At'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['no_order']);
            $sheet->setCellValue('C' . $row, $item['no_model']);
            $sheet->setCellValue('D' . $row, $item['buyer']);
            $sheet->setCellValue('E' . $row, $item['foll_up']);
            $sheet->setCellValue('F' . $row, $item['lco_date']);
            $sheet->setCellValue('G' . $row, $item['memo']);
            $sheet->setCellValue('H' . $row, $item['delivery_awal']);
            $sheet->setCellValue('I' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('J' . $row, $item['unit']);
            $sheet->setCellValue('K' . $row, $item['admin']);
            $sheet->setCellValue('L' . $row, $item['created_at']);
            $sheet->setCellValue('M' . $row, $item['updated_at']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:N{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Master_Order' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPengiriman()
    {
        $key = $this->request->getGet('key');
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');
        $data = $this->pengeluaranModel->getFilterPengiriman($key, $tanggal_awal, $tanggal_akhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'REPORT PENGIRIMAN AREA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        //
        $sheet->setCellValue('A2', 'Tanggal Awal');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('C2', ': ' . $tanggal_awal);
        $sheet->getStyle('C2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('N2', 'Tanggal Akhir');
        $sheet->getStyle('N2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('N2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('O2', ': ' . $tanggal_akhir);
        $sheet->getStyle('O2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('O2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Header
        $headers = ['No', 'No Model', 'Area', 'Delivery Awal', 'Delivery Akhir', 'Item Type', 'Kode Warna', 'Warna', 'Kgs Pesan', 'Tanggal Keluar', 'Kgs Kirim', 'Cones Kirim', 'Karung Kirim', 'Lot Kirim', 'Nama Cluster'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['no_model']);
            $sheet->setCellValue('C' . $row, $item['area_out']);
            $sheet->setCellValue('D' . $row, $item['delivery_awal']);
            $sheet->setCellValue('E' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('F' . $row, $item['item_type']);
            $sheet->setCellValue('G' . $row, $item['kode_warna']);
            $sheet->setCellValue('H' . $row, $item['warna']);
            $sheet->setCellValue('I' . $row, $item['ttl_kg']);
            $sheet->setCellValue('J' . $row, $item['tgl_out']);
            $sheet->setCellValue('K' . $row, $item['kgs_out']);
            $sheet->setCellValue('L' . $row, $item['cns_out']);
            $sheet->setCellValue('M' . $row, $item['krg_out']);
            $sheet->setCellValue('N' . $row, $item['lot_out']);
            $sheet->setCellValue('O' . $row, $item['nama_cluster']);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:O{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pengiriman_Area' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportGlobalReport()
    {
        $key = $this->request->getGet('key');
        $data = $this->masterOrderModel->getFilterReportGlobal($key);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GLOBAL ALL ' . $key);

        // Judul
        $sheet->mergeCells('A1:AA1');
        $sheet->setCellValue('A1', 'REPORT GLOBAL ' . $key);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Loss', 'Qty PO', 'Qty PO(+)', 'Stock Awal', 'Stock Opname', 'Datang Solid', '(+) Datang Solid', 'Ganti Retur', 'Datang Lurex', '(+)Datang Lurex', 'Datang PB GBN', 'Retur PB Area', 'Pakai Area', 'Pakai Lain-Lain', 'Retur Stock', 'Retur Titip', 'Dipinjam', 'Pindah Order', 'Pindah Ke Stock Mati', 'Stock Akhir', 'Tagihan GBN', 'Jatah Area'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            // Format setiap nilai untuk memastikan nilai 0 dan angka dengan dua desimal
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['no_model'] ?: '-'); // no model
            $sheet->setCellValue('C' . $row, $item['item_type'] ?: '-'); // item type
            $sheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-'); //kode warna
            $sheet->setCellValue('E' . $row, $item['color'] ?: '-'); // color
            $sheet->setCellValue('F' . $row, isset($item['loss']) ? number_format($item['loss'], 2, '.', '') : 0); // loss
            $sheet->setCellValue('G' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0); // qty po
            $sheet->setCellValue('H' . $row, '-'); // qty po (+)
            $sheet->setCellValue('I' . $row, isset($item['kgs_stock_awal']) ? number_format($item['kgs_stock_awal'], 2, '.', '') : 0); // stock awal
            $sheet->setCellValue('J' . $row, '-'); // stock opname
            $sheet->setCellValue('K' . $row, isset($item['kgs_kirim']) ? number_format($item['kgs_kirim'], 2, '.', '') : 0); // datan solid
            $sheet->setCellValue('L' . $row, '-'); // (+) datang solid
            $sheet->setCellValue('M' . $row, '-'); // ganti retur
            $sheet->setCellValue('N' . $row, '-'); // datang lurex
            $sheet->setCellValue('O' . $row, '-'); // (+) datang lurex
            $sheet->setCellValue('P' . $row, '-'); // retur pb gbn
            $sheet->setCellValue('Q' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0); // retur bp area
            $sheet->setCellValue('R' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0); // pakai area
            $sheet->setCellValue('S' . $row, '-'); // pakai lain-lain
            $sheet->setCellValue('T' . $row, '-'); // retur stock
            $sheet->setCellValue('U' . $row, '-'); // retur titip
            $sheet->setCellValue('V' . $row, '-'); // dipinjam
            $sheet->setCellValue('W' . $row, '-'); // pindah order
            $sheet->setCellValue('X' . $row, '-'); // pindah ke stock mati
            $sheet->setCellValue('Y' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0); // stock akhir

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] - ($item['kgs_kirim'] + $item['kgs_stock_awal']) : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] - $item['kgs_out'] : 0;

            // Format Tagihan GBN dan Jatah Area
            $sheet->setCellValue('Z' . $row, number_format($tagihanGbn, 2, '.', '')); // tagihan gbn
            $sheet->setCellValue('AA' . $row, number_format($jatahArea, 2, '.', '')); // jatah area
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:AA{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'AA') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tambahkan sheet kosong lainnya
        $sheetNames = [
            'STOCK AWAL ' . $key,
            'DATANG SOLID ' . $key,
            '(+) DATANG SOLID ' . $key,
            'GANTI RETUR ' . $key,
            'DATANG LUREX ' . $key,
            '(+) DATANG LUREX ' . $key,
            'RETUR PERBAIKAN GBN ' . $key,
            'RETUR PERBAIKAN AREA ' . $key,
            'PAKAI AREA ' . $key,
            'PAKAI LAIN-LAIN ' . $key,
            'RETUR STOCK ' . $key,
            'RETUR TITIP ' . $key,
            'ORDER ' . $key . ' DIPINJAM',
            'PINDAH ORDER ' . $key
        ];

        foreach ($sheetNames as $name) {
            $newSheet = $spreadsheet->createSheet();
            $newSheet->setTitle($name);

            // Hanya atur judul dan header jika nama sheet mengandung 'STOCK AWAL'
            if (strpos($name, 'STOCK AWAL') !== false) {
                // Judul
                $newSheet->mergeCells('A1:K1');
                $newSheet->setCellValue('A1', 'REPORT STOCK AWAL ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Delivery', 'Item Type', 'Kode Warna', 'Warna', 'Qty', 'Cones', 'Lot', 'Cluster', 'Keterangan'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:K3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'DATANG SOLID'
            if (strpos($name, 'DATANG SOLID') !== false) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT DATANG SOLID ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung '(+) DATANG SOLID'
            if (strpos($name, '(+) DATANG SOLID') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT TAMBAHAN DATANG SOLID ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'GANTI RETUR'
            if (strpos($name, 'GANTI RETUR') !== false) {
                // Judul
                $newSheet->mergeCells('A1:Q1');
                $newSheet->setCellValue('A1', 'REPORT DATANG GANTI RETUR ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin', 'Ganti Retur'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:Q3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'DATANG LUREX'
            if (strpos($name, 'DATANG LUREX') !== false) {
                // Judul
                $newSheet->mergeCells('A1:O1');
                $newSheet->setCellValue('A1', 'REPORT DATANG LUREX ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:O3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung '(+) DATANG LUREX'
            if (strpos($name, '(+) DATANG LUREX') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT TAMBAHAN DATANG LUREX ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'PO (+)', 'Tgl Datang', 'Nama Cluster', 'Qty Datang', 'Cones Datang', 'Lot Datang', 'Tgl Penerimaan', 'No SJ', 'L/M/D', 'Ket Datang', 'Admin'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }

            // Hanya atur judul dan header jika nama sheet mengandung 'RETUR PERBAIKAN GBN'
            if (strpos($name, 'RETUR PERBAIKAN GBN') !== false) {
                // Judul
                $newSheet->mergeCells('A1:P1');
                $newSheet->setCellValue('A1', 'REPORT RETUR PERBAIKAN GBN ' . $key);
                $newSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Header
                $headerStockAwal = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Area', 'Tgl Retur', 'Nama Cluster', 'Qty Retur', 'Cones Retur', 'Krg / Pack Retur', 'Lot Retur', 'Kategori', 'Ket Area', 'Ket GBN', 'Note'];
                $col = 'A';
                foreach ($headerStockAwal as $header) {
                    $newSheet->setCellValue($col . '3', $header);
                    $newSheet->getStyle($col . '3')->getFont()->setBold(true);
                    $col++;
                }

                // Tambahkan border untuk header A3:K3
                $newSheet->getStyle('A3:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        }

        // Kembali ke sheet pertama sebelum menyimpan
        $spreadsheet->setActiveSheetIndex(0);

        // Download
        $filename = 'Report_Global_' . $key . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function exportListBarangKeluar()
    {
        // $area = $this->request->getGet('area');
        $jenis = $this->request->getGet('jenis');
        $tglPakai = $this->request->getGet('tglPakai');

        $dataPemesanan = $this->pengeluaranModel->getDataPemesananExport($jenis, $tglPakai);
        // Kelompokkan data berdasarkan 'group'
        $groupedData = [];
        foreach ($dataPemesanan as $row) {
            $groupedData[$row['group']][] = $row;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Format header
        $subHeaderStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        foreach ($groupedData as $group => $rows) {
            // Buat sheet untuk setiap grup
            $sheet = $spreadsheet->createSheet();
            if ($group == "barang_jln") {
                $group = "LAIN - LAIN";
            } else {
                $group;
            }
            $sheet->setTitle("Group $group");

            $sheet->setCellValue('A1', 'CLUSTER GROUP ' . $group);
            $sheet->setCellValue('A2', 'PAKAI ' . $tglPakai);

            // Merge sel untuk teks di A1 dan A2
            $sheet->mergeCells('A1:J1');
            $sheet->mergeCells('A2:J2');
            $sheet->getStyle('A1:J2')->applyFromArray($subHeaderStyle);


            // Set header
            $header = [
                'Area',
                'No Model',
                'Item Type',
                'Kode Warna',
                'Color',
                'No Karung',
                'Kgs',
                'Cns',
                'Lot',
                'Nama Cluster',
            ];
            $sheet->fromArray($header, null, 'A3');


            $sheet->getStyle('A3:J3')->applyFromArray($headerStyle);

            // Tambahkan data
            $rowNumber = 4;
            foreach ($rows as $row) {
                // Hapus kolom yang tidak ingin dimasukkan
                unset($row['tgl_pakai'], $row['group'], $row['jenis']);

                $sheet->fromArray(array_values($row), null, "A$rowNumber");
                $rowNumber++;
            }
            // Tambahkan border ke semua data
            $dataEndRow = $rowNumber - 1;
            $sheet->getStyle("A3:J$dataEndRow")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Atur lebar kolom otomatis
            foreach (range('A', 'K') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        // Hapus sheet default (Sheet1)
        $spreadsheet->removeSheetByIndex(0);

        // Simpan file Excel
        $filename = 'Persiapan Barang ' . $jenis . ' ' . $tglPakai . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $filePath = WRITEPATH . "uploads/$filename";
        $writer->save($filePath);

        // Unduh file
        return $this->response->download($filePath, null)->setFileName($filename);

        // dd($dataPemesanan);
    }

    public function exportPermintaanKaret()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');
        $data = $this->pemesananModel->getFilterPemesananKaret($tglAwal, $tglAkhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'DATA PERMINTAAN KARET ' . date('d-M-Y', strtotime($tglAwal)) . ' s/d ' . date('d-M-Y', strtotime($tglAkhir)));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['TANGGAL PAKAI', 'ITEM TYPE', 'WARNA', 'KODE WARNA', 'NO MODEL'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->mergeCells($col . '2:' . $col . '3');
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Data & Total Result Header
        $sheet->mergeCells('F2:F3')->setCellValue('F2', 'Data');
        $sheet->mergeCells('G2:G3')->setCellValue('G2', 'Total Result');
        $sheet->getStyle('F2:G2')->getFont()->setBold(true);
        $sheet->getStyle('F2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:G2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Header Area
        $areaHeaders = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11M'
        ];
        $sheet->mergeCells('H2:U2')->setCellValue('H2', 'AREA');
        $sheet->getStyle('H2')->getFont()->setBold(true);

        $col = 'H';
        foreach ($areaHeaders as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Menulis data
        $row = 4;
        foreach ($data as $item) {
            // Row 1: JALAN MC
            $sheet->setCellValue('A' . $row, $item['tgl_pakai']);
            $sheet->setCellValue('B' . $row, $item['item_type']);
            $sheet->setCellValue('C' . $row, $item['color']);
            $sheet->setCellValue('D' . $row, $item['kode_warna']);
            $sheet->setCellValue('E' . $row, $item['no_model']);
            $sheet->setCellValue('F' . $row, 'Sum - JALAN MC:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_jl_mc']);
            $row++;

            // Row 2: TOTAL PESAN (KG)
            $sheet->setCellValue('F' . $row, 'Sum - TOTAL PESAN (KG):');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_kg']);
            $row++;

            // Row 3: CONES
            $sheet->setCellValue('F' . $row, 'Sum - CONES:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_cns']);
            $row++;
        }

        // Total global
        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - JALAN MC');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 1) . ',"*JALAN MC*",G4:G' . ($row - 1) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - TOTAL PESAN (KG)');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 2) . ',"*TOTAL PESAN*",G4:G' . ($row - 2) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - CONES');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 3) . ',"*CONES*",G4:G' . ($row - 3) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        // Simpan baris awal total area
        $totalRowStart = 4;

        // Total Per Area Per Kategori
        $categories = [
            'JALAN MC' => '*JALAN MC*',
            'TOTAL PESAN (KG)' => '*TOTAL PESAN*',
            'CONES' => '*CONES*',
        ];

        $row = $row - 3;
        foreach ($categories as $label => $keyword) {
            // $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", "Total Per Area - {$label}");
            // $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            $colLetter = 'H';
            foreach ($areaHeaders as $_) {
                $formula = "=SUMIF(F{$totalRowStart}:F" . ($row - 1) . ",\"{$keyword}\",{$colLetter}{$totalRowStart}:{$colLetter}" . ($row - 1) . ")";
                $sheet->setCellValue("{$colLetter}{$row}", $formula);
                $sheet->getStyle("{$colLetter}{$row}")->getFont()->setBold(true);
                $colLetter++;
            }
            $row++;
        }

        // Border
        $sheet->getStyle("A2:U" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Autosize
        foreach (range('A', 'U') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Report_Permintaan_Karet_' . date('d-M-Y', strtotime($tglAwal)) . '_sd_' . date('d-M-Y', strtotime($tglAkhir)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPermintaanSpandex()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');
        $data = $this->pemesananModel->getFilterPemesananSpandex($tglAwal, $tglAkhir);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'DATA PERMINTAAN SPANDEX ' . date('d-M-Y', strtotime($tglAwal)) . ' s/d ' . date('d-M-Y', strtotime($tglAkhir)));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['TANGGAL PAKAI', 'ITEM TYPE', 'WARNA', 'KODE WARNA', 'NO MODEL'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->mergeCells($col . '2:' . $col . '3');
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Data & Total Result Header
        $sheet->mergeCells('F2:F3')->setCellValue('F2', 'Data');
        $sheet->mergeCells('G2:G3')->setCellValue('G2', 'Total Result');
        $sheet->getStyle('F2:G2')->getFont()->setBold(true);
        $sheet->getStyle('F2:G2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:G2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Header Area
        $areaHeaders = [
            'KK1A',
            'KK1B',
            'KK2A',
            'KK2B',
            'KK2C',
            'KK5',
            'KK7K',
            'KK7L',
            'KK8D',
            'KK8F',
            'KK8J',
            'KK9',
            'KK10',
            'KK11M'
        ];
        $sheet->mergeCells('H2:U2')->setCellValue('H2', 'AREA');
        $sheet->getStyle('H2')->getFont()->setBold(true);

        $col = 'H';
        foreach ($areaHeaders as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Menulis data
        $row = 4;
        foreach ($data as $item) {
            // Row 1: JALAN MC
            $sheet->setCellValue('A' . $row, $item['tgl_pakai']);
            $sheet->setCellValue('B' . $row, $item['item_type']);
            $sheet->setCellValue('C' . $row, $item['color']);
            $sheet->setCellValue('D' . $row, $item['kode_warna']);
            $sheet->setCellValue('E' . $row, $item['no_model']);
            $sheet->setCellValue('F' . $row, 'Sum - JALAN MC:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_jl_mc']);
            $row++;

            // Row 2: TOTAL PESAN (KG)
            $sheet->setCellValue('F' . $row, 'Sum - TOTAL PESAN (KG):');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_kg']);
            $row++;

            // Row 3: CONES
            $sheet->setCellValue('F' . $row, 'Sum - CONES:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $sheet->setCellValue('H' . $row, $item['ttl_cns']);
            $row++;
        }

        // Total global
        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - JALAN MC');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 1) . ',"*JALAN MC*",G4:G' . ($row - 1) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - TOTAL PESAN (KG)');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 2) . ',"*TOTAL PESAN*",G4:G' . ($row - 2) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", 'Total Sum - CONES');
        $sheet->setCellValue("G{$row}", '=SUMIF(F4:F' . ($row - 3) . ',"*CONES*",G4:G' . ($row - 3) . ')');
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $row++;

        // Simpan baris awal total area
        $totalRowStart = 4;

        // Total Per Area Per Kategori
        $categories = [
            'JALAN MC' => '*JALAN MC*',
            'TOTAL PESAN (KG)' => '*TOTAL PESAN*',
            'CONES' => '*CONES*',
        ];

        $row = $row - 3;
        foreach ($categories as $label => $keyword) {
            // $sheet->mergeCells("A{$row}:F{$row}")->setCellValue("A{$row}", "Total Per Area - {$label}");
            // $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            $colLetter = 'H';
            foreach ($areaHeaders as $_) {
                $formula = "=SUMIF(F{$totalRowStart}:F" . ($row - 1) . ",\"{$keyword}\",{$colLetter}{$totalRowStart}:{$colLetter}" . ($row - 1) . ")";
                $sheet->setCellValue("{$colLetter}{$row}", $formula);
                $sheet->getStyle("{$colLetter}{$row}")->getFont()->setBold(true);
                $colLetter++;
            }
            $row++;
        }

        // Border
        $sheet->getStyle("A2:U" . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Autosize
        foreach (range('A', 'U') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Report_Permintaan_Spandex_' . date('d-M-Y', strtotime($tglAwal)) . '_sd_' . date('d-M-Y', strtotime($tglAkhir)) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReturArea()
    {
        $area = $this->request->getGet('area');
        $kategori = $this->request->getGet('kategori');
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->returModel->getFilterReturArea($area, $kategori, $tglAwal, $tglAkhir);

        if (!empty($data)) {
            foreach ($data as $key => $dt) {
                $kirim = $this->outCelupModel->getDataKirim($dt['id_retur']);
                $data[$key]['kg_kirim'] = $kirim['kg_kirim'] ?? 0;
                $data[$key]['cns_kirim'] = $kirim['cns_kirim'] ?? 0;
                $data[$key]['krg_kirim'] = $kirim['krg_kirim'] ?? 0;
                $data[$key]['lot_out'] = $kirim['lot_out'] ?? '-';
            }
        }
        // dd($data);
        // dd($area, $kategori, $tglAwal, $tglAkhir, $data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Retur Area');
        $sheet->mergeCells('A1:X1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["NO", "JENIS BAHAN BAKU", "TANGGAL RETUR", "AREA", "NO MODEL", "ITEM TYPE", "KODE WARNA", "WARNA", "LOSS", "QTY PO", "QTY PO(+)", "QTY KIRIM", "CONES KIRIM", "KARUNG KIRIM", "LOT KIRIM", "QTY RETUR", "CONES RETUR", "KARUNG RETUR", "LOT RETUR", "KATEGORI", "KET AREA", "KET GBN", "WAKTU ACC RETUR", "USER"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:X3')->getFont()->setBold(true);
        $sheet->getStyle('A3:X3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:X3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['jenis'],
                    $item['tgl_retur'],
                    $item['area_retur'],
                    $item['no_model'],
                    $item['item_type'],
                    $item['kode_warna'],
                    $item['warna'],
                    $item['loss'] . '%',
                    $item['total_kgs'],
                    $item['qty_po_plus'] ?? 0,
                    $item['kg_kirim'],
                    $item['cns_kirim'],
                    $item['krg_kirim'],
                    $item['lot_out'],
                    $item['kg'],
                    $item['cns'],
                    $item['karung'],
                    $item['lot_retur'],
                    $item['kategori'],
                    $item['keterangan_area'],
                    $item['keterangan_gbn'],
                    $item['waktu_acc_retur'],
                    $item['admin'],
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
        $sheet->getStyle('A3:X' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'X') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:X' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:X' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Retur_Area' . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportScheduleWeekly()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchWeekly($tglAwal, $tglAkhir);
        $getMesin = $this->mesinCelupModel
            ->orderBy('no_mesin', 'ASC')
            ->findAll();

        // setelah $tglAwal, $tglAkhir ter-set
        $period = new \DatePeriod(
            new \DateTime($tglAwal),
            new \DateInterval('P1D'),
            (new \DateTime($tglAkhir))->add(new \DateInterval('P1D'))
        );
        $dates = [];
        foreach ($period as $dt) {
            $dates[] = $dt->format('d/m/Y'); // Format kunci array konsisten
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');

        $blockSize = 13; // Total blok: 2 logo + 13 kolom data
        $dataOffsetFromBlock = 2; // Offset data dari awal blok
        $widths = [18, 10, 9, 43, 46, 8, 22, 22, 10, 14, 9, 16, 41];
        $headers = [
            'Kapasitas',
            'No Mesin',
            'Lot Urut',
            'PO',
            'Jenis Benang',
            'QTY',
            'Kode Warna',
            'Warna',
            'Lot Celup',
            'Actual Celup',
            'Start MC',
            'Del Exp',
            'Ket'
        ];

        foreach ($dates as $i => $tgl) {
            $offset = $blockSize * $i;
            $blockStartIndex = 1 + $offset;

            $logoCol1Index = $blockStartIndex;
            $logoCol2Index = $blockStartIndex + 1;
            // Kolom data
            $dataStartIndex = $blockStartIndex + $dataOffsetFromBlock; // C, P, AC, ...
            $dataEndIndex = $dataStartIndex + 10; // 13 kolom
            $logoCol1 = Coordinate::stringFromColumnIndex($logoCol1Index);
            $logoCol2 = Coordinate::stringFromColumnIndex($logoCol2Index);
            $dataColStart = Coordinate::stringFromColumnIndex($dataStartIndex);
            $dataColEnd = Coordinate::stringFromColumnIndex($dataEndIndex);
            $startColTanggal = Coordinate::stringFromColumnIndex($blockStartIndex);

            // Merge untuk area logo (misalnya C1:D4, P1:Q4, dst)
            $sheet->mergeCells("{$logoCol1}1:{$logoCol2}4");

            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Perusahaan');
            $drawing->setPath('assets/img/logo-kahatex.png');
            $sheet->getRowDimension('1')->setRowHeight(20);
            $sheet->getRowDimension('2')->setRowHeight(20);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(20);
            $drawing->setHeight(45);
            $drawing->setCoordinates($logoCol2 . '1');
            $drawing->setOffsetX(0);
            $drawing->setOffsetY(40);
            $drawing->setWorksheet($sheet);

            // Set Lebar Kolom
            for ($j = 0; $j < count($widths); $j++) {
                $colLetter = Coordinate::stringFromColumnIndex($logoCol1Index + $j);
                $sheet->getColumnDimension($colLetter)->setWidth($widths[$j]);
            }

            // Header Baris 1–4
            $sheet->setCellValue("{$dataColStart}1", 'FORMULIR');
            $sheet->mergeCells("{$dataColStart}1:{$dataColEnd}1");

            $sheet->setCellValue("{$dataColStart}2", 'DEPARTEMEN CELUP CONES');
            $sheet->mergeCells("{$dataColStart}2:{$dataColEnd}2");

            $sheet->setCellValue("{$dataColStart}3", 'REPORT SCHEDULE CELUP MINGGUAN');
            $sheet->mergeCells("{$dataColStart}3:{$dataColEnd}3");

            $sheet->setCellValue("{$dataColStart}4", 'FOR-CC-151/REV_01/HAL_1/1');
            $sheet->mergeCells("{$dataColStart}4:" . Coordinate::stringFromColumnIndex($dataStartIndex + 2) . "4");

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($dataStartIndex + 3) . '4', 'TANGGAL REVISI');
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($dataStartIndex + 3) . '4:' . Coordinate::stringFromColumnIndex($dataStartIndex + 4) . '4');

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($dataStartIndex + 5) . '4', '05 Oktober 2019');
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($dataStartIndex + 5) . '4:' . $dataColEnd . '4');

            $sheet->getStyle("{$dataColStart}1:{$dataColEnd}4")->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle("{$dataColStart}1:{$dataColEnd}4")->getFont()->setSize(14);

            $sheet->mergeCells("{$startColTanggal}5:{$dataColEnd}5");
            $sheet->setCellValue("{$startColTanggal}5", $tgl);
            $sheet->getStyle("{$startColTanggal}5:{$dataColEnd}5")->getAlignment()->setHorizontal('center')->setVertical('center');
            $sheet->getStyle("{$startColTanggal}5:{$dataColEnd}5")->getFont()->setBold(true)->setSize(14);

            // Tambahkan border di seluruh area header tanggal (baris 1–5)
            $sheet->getStyle("{$logoCol1}1:{$dataColEnd}5")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);

            foreach ($headers as $j => $h) {

                $colStartVal = $blockStartIndex + $j;
                $col = Coordinate::stringFromColumnIndex($colStartVal);
                $cell = "{$col}6";
                $sheet->setCellValue($cell, $h);

                $sheet->getStyle($cell)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DCDCDC']
                    ],
                    'font' => [
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);
            }
        }

        $groupedData = [];
        foreach ($data as $d) {
            $keyTgl = date('d/m/Y', strtotime($d['tanggal_schedule']));
            $groupedData[$keyTgl][$d['id_mesin']][$d['lot_urut']] = $d;
        }

        // Hitung kolom awal per tanggal
        $dateColStartIndexes = [];
        foreach ($dates as $i => $tgl) {
            $colStartIndex = 1 + ($i * count($headers));
            $dateColStartIndexes[$tgl] = $colStartIndex;

            $row = 7;

            foreach ($getMesin as $m) {
                $idMesin = $m['id_mesin'];
                $noMesin = $m['no_mesin'];
                $kapasitas = $m['min_caps'] . ' - ' . $m['max_caps'];

                // Tampilkan 3 lot urut
                for ($lot = 1; $lot <= 3; $lot++) {
                    foreach ($dates as $i => $tgl) {
                        $colStartIndex = 1 + ($i * count($headers));
                        $dataRow = $groupedData[$tgl][$idMesin][$lot] ?? null;

                        if ($dataRow) {

                            //Ubah format tanggal start mc
                            $startMc = (!empty($dataRow['start_mc']) && $dataRow['start_mc'] !== '0000-00-00 00:00:00')
                                ? date('d-M', strtotime($dataRow['start_mc'])) : '';

                            $values = [
                                $lot === 1 ? $kapasitas : '',
                                $lot === 1 ? $noMesin : '',
                                $lot,
                                $dataRow['no_model'] ?? '',
                                $dataRow['item_type'] ?? '',
                                $dataRow['kg_celup'] ?? '',
                                $dataRow['kode_warna'] ?? '',
                                $dataRow['warna'] ?? '',
                                $dataRow['lot_celup'] ?? '',
                                $dataRow['actual_celup'] ?? '',
                                $startMc ?? '',
                                date('d-M', strtotime($dataRow['delivery_awal'])) ?? '',
                                $dataRow['ket_celup'] ?? ''
                            ];
                        } else {
                            // Jika tidak ada data, tetap isi dengan placeholder jumlah kolom = count($headers)
                            $values = [
                                $lot === 1 ? $kapasitas : '',
                                $lot === 1 ? $noMesin : '',
                                $lot
                            ];
                            for ($k = 3; $k < count($headers); $k++) {
                                $values[] = '';
                            }
                        }

                        foreach ($values as $j => $val) {
                            $col = Coordinate::stringFromColumnIndex($colStartIndex + $j);
                            $cell = "{$col}{$row}";
                            $sheet->setCellValue("{$col}{$row}", $val);
                            // Set alignment ke tengah
                            $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }

                        // Tambahkan border per baris
                        $colStart = Coordinate::stringFromColumnIndex($colStartIndex);
                        $colEnd = Coordinate::stringFromColumnIndex($colStartIndex + count($headers) - 1);
                        $sheet->getStyle("{$colStart}{$row}:{$colEnd}{$row}")
                            ->getBorders()->getAllBorders()
                            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }
                    $row++;
                }
            }

            // Export
            $filename = 'Schedule_Benang_Nylon_' . date('Ymd_His') . '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }
    }

    public function exportReportGlobalBenang()
    {
        $key = $this->request->getGet('key');
        // Daftar judul sheet—juga dipakai sebagai filter ke model
        $sheetTitles = [
            'GLOBAL BENANG ' . $key,
            'STOCK AWAL ' . $key,
            'DATANG SOLID ' . $key,
            '(+) DATANG SOLID ' . $key,
            'GANTI RETUR ' . $key,
            'DATANG LUREX ' . $key,
            '(+) DATANG LUREX ' . $key,
            'RETUR PERBAIKAN GBN ' . $key,
            'RETUR PERBAIKAN AREA ' . $key,
            'PAKAI AREA ' . $key,
            'PAKAI LAIN-LAIN ' . $key,
            'RETUR STOCK ' . $key,
            'RETUR TITIP ' . $key,
            'ORDER ' . $key . ' DIPINJAM',
            'PINDAH ORDER ' . $key,
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Hapus sheet default kosong
        $spreadsheet->removeSheetByIndex(0);

        foreach ($sheetTitles as $title) {
            $data = $this->stockModel->getFilterReportGlobalBenang($key);

            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($title);

            // Judul di baris 1
            $sheet->mergeCells('A1:AA1');
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Header di baris 3
            $headers = [
                'No',
                'No Model',
                'Item Type',
                'Kode Warna',
                'Warna',
                'Loss',
                'Qty PO',
                'Qty PO(+)',
                'Stock Awal',
                'Stock Opname',
                'Datang Solid',
                '(+)Datang Solid',
                'Ganti Retur',
                'Datang Lurex',
                '(+)Datang Lurex',
                'Retur PB Gbn',
                'Retur Pb Area',
                'Pakai Area',
                'Pakai Lain-Lain',
                'Retur Stock',
                'Retur Titip',
                'Dipinjam',
                'Pindah Order',
                'Pindah Stock Mati',
                'Stock Akhir',
                'Tagihan Gbn',
                'Jatah Area'
            ];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '3', $header);
                $sheet->getStyle($col . '3')->getFont()->setBold(true);
                $sheet->getStyle($col . '3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $col++;
            }

            // Isi Data mulai baris 4
            $row = 4;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                $sheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
                $sheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
                $sheet->setCellValue('E' . $row, $item['warna'] ?: '-');
                $sheet->setCellValue('F' . $row, $item['loss'] . '%' ?: '-');
                $sheet->setCellValue('G' . $row, $item['qty_po'] ?: 0);
                $sheet->setCellValue('I' . $row, $item['kgs_stock_awal'] ?: 0);
                $sheet->setCellValue('K' . $row, $item['datang_solid'] ?: 0);
                $sheet->setCellValue('M' . $row, $item['ganti_retur'] ?: 0);
                $sheet->setCellValue('R' . $row, $item['pakai_area'] ?: 0);

                if ($item['ganti_retur'] == 0) {
                    $tagihanGbn = ($item['kgs_stock_awal'] ?? 0)
                        + ($item['stock_opname'] ?? 0)
                        + ($item['datang_solid'] ?? 0)
                        + ($item['retur_stock'] ?? 0)
                        - ($item['qty_po'] ?? 0)
                        - ($item['qty_po_plus'] ?? 0);
                } else {
                    $tagihanGbn = ($item['kgs_stock_awal'] ?? 0)
                        + ($item['stock_opname'] ?? 0)
                        + ($item['datang_solid'] ?? 0)
                        + ($item['retur_stock'] ?? 0)
                        + ($item['ganti_retur'] ?? 0)
                        - ($item['qty_po'] ?? 0)
                        - ($item['qty_po_plus'] ?? 0)
                        - ($item['retur_belang_gbn'] ?? 0)
                        - ($item['retur_belang_area'] ?? 0);
                }
                $sheet->setCellValue('AA' . $row, number_format($tagihanGbn, 2, '.', ''));
                $row++;
            }

            $lastRow = $row - 1;

            // Border untuk semua cell
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:AA{$lastRow}")->applyFromArray($styleArray);

            // Center align untuk data
            $sheet->getStyle("A4:AA{$lastRow}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Manual column widths (karena A–Z autoSize, AA manual)
            foreach (range('A', 'Z') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
            $sheet->getColumnDimension('AA')->setWidth(14);
        }

        // Aktifkan sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        // Download semua sheet
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Report-Global-Benang-AllArea.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function exportStock()
    {
        $jenisCover = $this->request->getPost('jenis_cover');
        $jenisBenang = $this->request->getPost('jenis_benang');
        if (empty($jenisBenang) || empty($jenisCover)) {
            return redirect()->back()->with('error', 'Jenis Benang dan Jenis Cover tidak boleh kosong.');
        }

        $data = $this->coveringStockModel->getStockCover($jenisBenang, $jenisCover);
        // dd($data);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Merge area logo dan autosize kolom A dan B
        $sheet->mergeCells('A1:B2');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Logo kahatex di tengah area A1:B2
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        // Set posisi di tengah merge cell A1:B2
        $drawing->setCoordinates('A1');
        $drawing->setHeight(50);
        // Offset agar logo benar-benar di tengah
        $drawing->setOffsetX(40);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // Judul
        $sheet->mergeCells('A3:B3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


        $sheet->mergeCells('C1:Q1');
        $sheet->setCellValue('C1', 'FORMULIR');
        $sheet->mergeCells('C2:Q2');
        $sheet->setCellValue('C2', 'DEPARTEMEN COVERING');
        $sheet->mergeCells('C3:Q3');
        $sheet->setCellValue('C3', 'STOCK '. $jenisCover.' COVER DI GUDANG COVERING');
        $sheet->getStyle('C1:Q3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C1:Q3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C1:Q3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // warna background
        $sheet->getStyle('C1:Q1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('C1:Q1')->getFill()->getStartColor()->setARGB('99FFFF');
        // Border kiri A1:A3
        $sheet->getStyle('A1:A3')->getBorders()->getLeft()->setBorderStyle(Border::BORDER_DOUBLE);

        // Border atas A1:Q1
        $sheet->getStyle('A1:Q1')->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

        // Border kanan B1:B3
        $sheet->getStyle('B1:B3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_DOUBLE);

        // Border kanan Q1:Q3
        $sheet->getStyle('Q1:Q3')->getBorders()->getRight()->setBorderStyle(Border::BORDER_DOUBLE);

        $sheet->mergeCells('A4:B4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->mergeCells('C4:K4');
        $sheet->setCellValue('C4', 'FOR-CC-151/REV_01/HAL_../..');
        $sheet->mergeCells('L4:N4');
        $sheet->setCellValue('L4', 'Tanggal Revisi');
        $sheet->mergeCells('O4:Q4');
        $sheet->setCellValue('O4', '11 November 2019');

        $sheet->mergeCells('A5:B5')
            ->setCellValue('A5', 'Jenis Benang');
        $sheet->mergeCells('C5:K5')
            ->setCellValue('C5', $jenisBenang);
        $sheet->mergeCells('L5:N5')
            ->setCellValue('L5', 'Tanggal');
        // Format tanggal menjadi 23-Mei-2025
        $bulanIndo = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        ];
        $tanggalSekarang = date('Y-m-d');
        $tglArr = explode('-', $tanggalSekarang);
        $tglIndo = $tglArr[2] . '-' . $bulanIndo[$tglArr[1]] . '-' . $tglArr[0];
        $sheet->mergeCells('O5:Q5')
            ->setCellValue('O5', $tglIndo);
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true);
        $sheet->getStyle('A4:Q5')->getFont()->setSize(12);

        $sheet->getStyle('A4:Q4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
        $sheet->getStyle('A5:Q5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A4:Q5')->getBorders()->getAllBorders()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
        $sheet->getStyle('A4:Q5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);


        // Header
        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'Jenis');
        $sheet->mergeCells('B6:B7');
        $sheet->setCellValue('B6', 'Color');
        $sheet->mergeCells('C6:C7');
        $sheet->setCellValue('C6', 'Code');
        $sheet->mergeCells('D6:D7');
        $sheet->setCellValue('D6', 'LMD');
        $sheet->getColumnDimension('D')->setWidth(7);
        $sheet->mergeCells('E6:I6');
        $sheet->setCellValue('E6', 'Total');
        $sheet->mergeCells('J6:K6');
        $sheet->setCellValue('J6', 'Stock');
        $sheet->mergeCells('L6:Q6');
        $sheet->setCellValue('L6', 'Keterangan');
        $sheet->mergeCells('E7:F7');
        $sheet->setCellValue('E7', 'Cones');
        $sheet->mergeCells('G7:H7');
        $sheet->setCellValue('G7', 'Kg');
        $sheet->getColumnDimension('I')->setWidth(7);
        $sheet->setCellValue('I7', 'Box');
        $sheet->getColumnDimension('J')->setWidth(7);
        $sheet->setCellValue('J7', 'Ada');
        $sheet->getColumnDimension('K')->setWidth(7);
        $sheet->setCellValue('K7', 'Habis');
        $sheet->setCellValue('L7', 'Rak No');
        $sheet->setCellValue('M7', 'Kanan');
        $sheet->setCellValue('N7', 'Kiri');
        $sheet->setCellValue('O7', 'Atas');
        $sheet->setCellValue('P7', 'Bawah');
        $sheet->setCellValue('Q7', 'Palet No');
        $sheet->getStyle('A6:Q7')->getFont()->setBold(true);
        $sheet->getStyle('A6:Q7')->getFont()->setSize(11);
        $sheet->getStyle('A6:Q7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6:Q7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A6:Q7')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A6:Q7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A6:Q7')->getBorders()->getAllBorders()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
        $sheet->getStyle('A6:Q7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        // set background color white
        $sheet->getStyle('A2:Q7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A2:Q7')->getFill()->getStartColor()->setARGB('FFFFFF');
        // $sheet->getStyle('J7:Q7')->getFont()->setBold(true);
        $sheet->getStyle('J7:Q7')->getFont()->setSize(10);
        
        // Data
        $row            = 8;
        // Untuk merge fullJenis (level-1)
        $fullStartRow   = $row;
        $prevFullJenis  = null;
        // Untuk subtotal baseJenis (level-2)
        $mergeStartBase = $row;
        $prevBaseJenis  = null;
        $subtotalCns    = 0;
        $subtotalKg     = 0;

        foreach ($data as $item) {
            // normalisasi fullJenis
            $fullJenisNorm = trim(strtoupper($item['jenis']));
            // strip “DR xx” untuk grouping subtotal
            $baseJenis = trim(
                preg_replace('/\s*DR\s*\.?(\d+(\.\d+)*)$/i', '', $fullJenisNorm)
            );

            /// 1) detect change pada fullJenis => lakukan merge level-1
            if ($prevFullJenis !== null && $fullJenisNorm !== $prevFullJenis) {
                if ($row - 1 > $fullStartRow) {
                    $sheet->mergeCells("A{$fullStartRow}:A" . ($row - 1));
                }
                $fullStartRow = $row;
            }
            // 2) detect change pada baseJenis => merge level-2 + subtotal
            if ($prevBaseJenis !== null && $baseJenis !== $prevBaseJenis) {

                // tulis subtotal
                $sheet->mergeCells("B{$row}:D{$row}")->setCellValue("B{$row}", 'Subtotal ' . $prevBaseJenis);
                $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", $subtotalCns);
                $sheet->mergeCells("G{$row}:H{$row}")->setCellValue("G{$row}", $subtotalKg);
                $sheet->getStyle("A{$row}:Q{$row}")->getFont()->setBold(true);
                $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("A{$row}:Q{$row}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
                $sheet->getStyle("B{$row}:H{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle("B{$row}:H{$row}")->getFill()->getStartColor()->setARGB('DCDCDC');
                $mergeStartBase = $row + 1;
                $row++;
                $subtotalCns = $subtotalKg = 0;
            }

            if ($fullJenisNorm === $prevFullJenis) {
                // merge kolom A grup full
                if ($row - 1 > $fullStartRow) {
                    $sheet->mergeCells("A{$fullStartRow}:A" . ($row - 1));
                }
            } else {
                // reset pointer
                $fullStartRow = $row;
            }
            // Isi baris data
            $sheet->setCellValue("A{$row}", $fullJenisNorm);
            $sheet->setCellValue("B{$row}", $item['color']);
            $sheet->setCellValue("C{$row}", $item['code']);
            $sheet->setCellValue("D{$row}", $item['lmd']);
            $sheet->setCellValue("E{$row}", $item['ttl_cns']);
            $sheet->setCellValue("G{$row}", $item['ttl_kg']);
            $sheet->setCellValue("I{$row}", '');

            if ($item['ttl_kg'] > 0) {
                $sheet->setCellValue('J' . $row, '✓');
            } else {
                $sheet->setCellValue('K' . $row, '✓');
            }

            foreach (range('L', 'Q') as $col) {
                $sheet->setCellValue($col . $row, '');
            }

            $sheet->getStyle("A{$row}:Q{$row}")
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}:Q{$row}")
                ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:Q{$row}")
                ->getAlignment()->setWrapText(true);
            $sheet->getStyle("A{$row}:Q{$row}")
                ->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);

            $subtotalCns += $item['ttl_cns'];
            $subtotalKg  += $item['ttl_kg'];

            $prevFullJenis = $fullJenisNorm;
            $prevBaseJenis = $baseJenis;
            // dd ($prevBaseJenis, $prevFullJenis);
            $row++;
        }

        if ($row - 1 > $fullStartRow) {
            $sheet->mergeCells("A{$fullStartRow}:A" . ($row - 1));
        }
        if ($row - 1 >= $mergeStartBase) {

            $sheet->mergeCells("B{$row}:D{$row}")->setCellValue("B{$row}", 'Subtotal ' . $prevBaseJenis);
            $sheet->mergeCells("E{$row}:F{$row}")->setCellValue("E{$row}", $subtotalCns);
            $sheet->mergeCells("G{$row}:H{$row}")->setCellValue("G{$row}", $subtotalKg);
            $sheet->getStyle("A{$row}:Q{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}:Q{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A{$row}:Q{$row}")
                ->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
            // warna background abu-abu
            $sheet->getStyle("B{$row}:H{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle("B{$row}:H{$row}")->getFill()->getStartColor()->setARGB('DCDCDC');
        }

        // 1) Hitung subtotal per jenis benang utama
        $totalPerBenang = [];
        foreach ($data as $item) {
            // asumsikan ada kolom `jenis_benang`; 
            // kalau nggak ada, bisa ganti dengan strtok($item['jenis'], ' ')
            $mainJenis = $item['jenis_benang'];
            $kg       = $item['ttl_kg'];
            if (! isset($totalPerBenang[$mainJenis])) {
                $totalPerBenang[$mainJenis] = 0;
            }
            $totalPerBenang[$mainJenis] += $kg;
        }

        // 2) Tulis summary di sheet
        $row += 2;  // spasi antara data dan summary
        foreach ($totalPerBenang as $jenis => $kg) {
            // merge A–D untuk menampung teks “Nylon : xxx KG”
            $sheet->mergeCells("C{$row}:D{$row}")
                ->setCellValue("B{$row}", "{$jenis}");
            $sheet->setCellValue("C{$row}", ": {$kg} KG");
            $row++;
        }

        // 3) Total keseluruhan
        $totalKg = array_sum($totalPerBenang);
        $sheet->mergeCells("C{$row}:D{$row}")
            ->setCellValue("B{$row}", "Total");
        $sheet->setCellValue("C{$row}", ": {$totalKg} KG");
        $rowstar = $row-1;
        $rowEnd = $row ;
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFont()->setBold(true);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->getStartColor()->setARGB('DCDCDC');
        $row += 2;

        // 4) Tanda tangan penanggung jawab
        $sheet->mergeCells("E{$row}:I{$row}")
            ->setCellValue("E{$row}", "Yang Bertanggung Jawab : ………......................");
        $sheet->getStyle("E{$row}")
            ->getFont()->setItalic(true);


        // Download
        $filename = 'Formulir_Stock_' . $jenisBenang . '_' . $jenisCover . '_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    
}
