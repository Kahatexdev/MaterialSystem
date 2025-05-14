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
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
        $sheet->mergeCells('A1:O1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A3:O3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:O3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['no_mesin'],
                    $item['ket_mesin'],
                    $item['lot_urut'],
                    $item['no_model'],
                    $item['item_type'],
                    $item['kode_warna'],
                    $item['warna'],
                    $item['start_mc'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['tanggal_schedule'],
                    $item['kg_celup'],
                    $item['lot_celup'],
                    $item['tanggal_celup'],
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
        $sheet->getStyle('A3:O' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:O' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:O' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

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
        $sheet->mergeCells('A1:O1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A3:O3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:O3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                [
                    $index + 1,
                    $item['no_mesin'],
                    $item['ket_mesin'],
                    $item['lot_urut'],
                    $item['no_model'],
                    $item['item_type'],
                    $item['kode_warna'],
                    $item['warna'],
                    $item['start_mc'],
                    $item['delivery_awal'],
                    $item['delivery_akhir'],
                    $item['tanggal_schedule'],
                    $item['kg_celup'],
                    $item['lot_celup'],
                    $item['tanggal_celup'],
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
        $sheet->getStyle('A3:O' . ($row - 1))->applyFromArray($styleArray);

        // Set auto width untuk setiap kolom
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set isi tabel agar rata tengah
        $sheet->getStyle('A4:O' . ($row - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:O' . ($row - 1))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Report_Schedule_Benang' . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function excelStockMaterial()
    {
        // Ambil filter dari query string
        $noModel = $this->request->getGet('no_model');
        $warna = $this->request->getGet('warna');
        // Ambil data hasil filter dari model
        $filteredData = $this->stockModel->searchStock($noModel, $warna);
        // dd($filteredData);
        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === Tambahkan Judul Header di Tengah === //
        $title = 'DATA STOCK MATERIAL';
        $sheet->mergeCells('A1:M1'); // Gabungkan dari kolom A sampai M
        $sheet->setCellValue('A1', $title);

        // Format judul (bold + center)
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

        $lastDataRow = $row - 1; // baris terakhir data
        $sheet->getStyle("A3:N{$lastDataRow}")->applyFromArray($styleArray);

        // === Export File Excel === //
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
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'REPORT PEMASUKAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Box', 'No Rak', 'Posisi Rak', 'No Palet', 'Keterangan'];
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
            $sheet->setCellValue('G' . $row, $item['box']);
            $sheet->setCellValue('H' . $row, $item['no_rak']);
            $sheet->setCellValue('I' . $row, $item['posisi_rak']);
            $sheet->setCellValue('J' . $row, $item['no_palet']);
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
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'REPORT PENGELUARAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No Model', 'Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Box', 'No Rak', 'Posisi Rak', 'No Palet', 'Keterangan'];
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
            $sheet->setCellValue('H' . $row, $item['box']);
            $sheet->setCellValue('I' . $row, $item['no_rak']);
            $sheet->setCellValue('J' . $row, $item['posisi_rak']);
            $sheet->setCellValue('K' . $row, $item['no_palet']);
            $sheet->setCellValue('L' . $row, $item['keterangan']);
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
        $sheet->getStyle("A3:L{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'L') as $col) {
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

        // Judul
        $sheet->mergeCells('A1:Q1');
        $sheet->setCellValue('A1', 'REPORT GLOBAL ' . $key);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'No Model', 'Item Type', 'Kode Warna', 'Warna', 'Loss', 'Qty PO', 'Qty PO(+)', 'Stock Awal', 'Qty Datang', 'Retur GBN', 'Retur Area', 'Kirim Area', 'Kirim Lain-Lain', 'Stock Akhir', 'Tagihan GBN', 'Jatah Area'];
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
            $sheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
            $sheet->setCellValue('C' . $row, $item['item_type'] ?: '-');
            $sheet->setCellValue('D' . $row, $item['kode_warna'] ?: '-');
            $sheet->setCellValue('E' . $row, $item['color'] ?: '-');
            $sheet->setCellValue('F' . $row, isset($item['loss']) ? number_format($item['loss'], 2, '.', '') : 0);
            $sheet->setCellValue('G' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0);
            $sheet->setCellValue('H' . $row, '-');
            $sheet->setCellValue('I' . $row, isset($item['kgs_stock_awal']) ? number_format($item['kgs_stock_awal'], 2, '.', '') : 0);
            $sheet->setCellValue('J' . $row, isset($item['kgs_kirim']) ? number_format($item['kgs_kirim'], 2, '.', '') : 0);
            $sheet->setCellValue('K' . $row, '-');
            $sheet->setCellValue('L' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0);
            $sheet->setCellValue('M' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0);
            $sheet->setCellValue('N' . $row, '-');
            $sheet->setCellValue('O' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0);

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] - $item['kgs_out'] : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] - $item['kgs_kirim'] : 0;

            // Format Tagihan GBN dan Jatah Area
            $sheet->setCellValue('P' . $row, number_format($tagihanGbn, 2, '.', ''));
            $sheet->setCellValue('Q' . $row, number_format($jatahArea, 2, '.', ''));
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
        $sheet->getStyle("A3:Q{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

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
        $area = $this->request->getGet('area');
        $jenis = $this->request->getGet('jenis');
        $tglPakai = $this->request->getGet('tglPakai');

        $dataPemesanan = $this->pengeluaranModel->getDataPemesananExport($area, $jenis, $tglPakai);
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
        $filename = 'List Pengeluaran Barang ' . $tglPakai . '.xlsx';
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
}
