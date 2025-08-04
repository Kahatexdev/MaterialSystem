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
use App\Models\PoTambahanModel;
use App\Models\HistoryStock;
use App\Models\PemesananSpandexKaretModel;
use App\Models\WarehouseBBModel;
use App\Models\MasterWarnaBenangModel;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpParser\Node\Stmt\Else_;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\PageMargins;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use DateTime;

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
    protected $poPlusModel;
    protected $historyStock;
    protected $pemesananSpandexKaretModel;
    protected $warehouseBBModel;
    protected $masterWarnaBenangModel;

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
        $this->poPlusModel = new PoTambahanModel();
        $this->historyStock = new HistoryStock();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->warehouseBBModel = new WarehouseBBModel();
        $this->masterWarnaBenangModel = new MasterWarnaBenangModel();

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

            if (!empty($material)) {
                foreach ($material as $mtr) {
                    // Cek dulu apakah size cocok
                    if ($prod['size'] !== $mtr['style_size']) {
                        continue; // Lewati jika tidak cocok
                    }

                    $gw = $mtr['gw'];
                    $comp = $mtr['composition'];
                    $gwpcs = ($gw * $comp) / 100;

                    $bruto = $prod['prod'] ?? 0;
                    $bs_mesin = $prod['bs_mesin'] ?? 0;

                    $pph = ($gw == 0) ? 0 : ((($bruto + ($bs_mesin / $gw)) * $comp * $gw) / 100) / 1000;

                    $pphInisial[] = [
                        'mastermodel'    => $prod['mastermodel'],
                        'style_size'     => $prod['size'],
                        'item_type'      => $mtr['item_type'] ?? null,
                        'kode_warna'     => $mtr['kode_warna'] ?? null,
                        'color'          => $mtr['color'] ?? null,
                        'gw'             => $gw,
                        'composition'    => $comp,
                        'bruto'          => $bruto,
                        'qty'            => $prod['qty'] ?? 0,
                        'sisa'           => $prod['sisa'] ?? 0,
                        'bs_mesin'       => $bs_mesin,
                        'pph'            => $pph
                    ];
                }
            } else {
                $result[$prod['mastermodel']] = [
                    'mastermodel' => $prod['mastermodel'],
                    'item_type'   => null,
                    'kode_warna'  => null,
                    'warna'       => null,
                    'pph'         => 0,
                    'bruto'       => $prod['prod'],
                    'bs_mesin'    => $prod['bs_mesin'],
                ];
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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Foll Up", "No Model", "No Order", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Kode Warna", "Warna", "KG Pesan", "Tanggal Datang", "Kgs Datang", "Cones Datang", "LOT Datang", "No Surat Jalan", "LMD", "GW", "Harga", "Nama Cluster"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:U3')->getFont()->setBold(true);
        $sheet->getStyle('A3:U3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:U3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
                    number_format($item['kgs_material'], 2),
                    $item['tgl_masuk'],
                    number_format($item['kgs_kirim'], 2),
                    $item['cones_kirim'],
                    $item['lot_kirim'],
                    $item['no_surat_jalan'],
                    $item['l_m_d'],
                    number_format($item['gw_kirim'], 2),
                    number_format($item['harga'], 2),
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
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:U' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "Waktu Input", "Tanggal PO", "Foll Up", "No Model", "No Order", "Keterangan", "Buyer", "Delivery Awal", "Delivery Akhir", "Order Type", "Item Type", "Jenis", "Kode Warna", "Warna", "KG Pesan"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $key = $this->request->getGet('key') ?? '';
        $tanggal_schedule = $this->request->getGet('tanggal_schedule') ?? '';
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchBenang($tanggal_awal, $tanggal_akhir, $key, $tanggal_schedule);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Schedule Benang');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "Qty Celup", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $key = $this->request->getGet('key') ?? '';
        $tanggal_schedule = $this->request->getGet('tanggal_schedule') ?? '';
        $tanggal_awal = $this->request->getGet('tanggal_awal');
        $tanggal_akhir = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchNylon($tanggal_awal, $tanggal_akhir, $key, $tanggal_schedule);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Report Schedule Nylon');
        $sheet->mergeCells('A1:P1'); // Menggabungkan sel untuk judul
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["No", "No Mesin", "Ket Mesin", "Lot Urut", "No Model", "Item Type", "Kode Warna", "Warna", "Start Mc", "Delivery Awal", "Delivery Akhir", "Tgl Schedule", "Qty PO", "Qty Celup", "LOT Sch", "Tgl Celup"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:P3')->getFont()->setBold(true);
        $sheet->getStyle('A3:P3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:P3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:P' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
        $date2 = $this->request->getGet('date2');
        $data = $this->historyCoveringStockModel->getPemasukanByDate($date, $date2);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'REPORT PEMASUKAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal: ' . $date . ' - ' . $date2);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2:H2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFont()->setSize(12);
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2:H2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A2:H2')->getBorders()->getBottom()->getColor()->setARGB('FF000000');
        // Header
        $headers = ['Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Keterangan', 'Tanggal'];
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
            $sheet->setCellValue('H' . $row, $item['created_at']);
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
        $date2 = $this->request->getGet('date2');
        $data = $this->historyCoveringStockModel->getPengeluaranByDate($date, $date2);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'REPORT PENGELUARAN COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal: ' . $date . ' - ' . $date2);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2:H2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFont()->setSize(12);
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2:H2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A2:H2')->getBorders()->getBottom()->getColor()->setARGB('FF000000');
        // Header
        $headers = ['No Model', 'Jenis', 'Warna', 'Kode', 'LMD', 'Total Cones', 'Total Kg', 'Keterangan', 'Tanggal'];
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
            $sheet->setCellValue('I' . $row, $item['created_at']);
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
        $sheet->getStyle("A3:I{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'I') as $col) {
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

    public function excelPemesananCovering()
    {
        $tglPakai = $this->request->getGet('tgl_pakai');
        $jenis = $this->request->getGet('jenis');

        $data = $this->pemesananModel->getDataPemesananCovering($tglPakai, $jenis);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Judul
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'REPORT PEMESANAN ' . $jenis . ' COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Tanggal Pakai', 'Item Type', 'Warna', 'Kode Warna', 'No Model', 'Jalan MC', 'Total Pesan (Kg)', 'Cones', 'Keterangan'];
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
            $sheet->setCellValue('H' . $row, $item['ttl_kg']);
            $sheet->setCellValue('I' . $row, $item['ttl_cns']);
            $sheet->setCellValue('J' . $row, $item['keterangan']);
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
        $sheet->getStyle("A3:J{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report_Pemesanan_' . $jenis . '_Tgl_Pakai_' . $tglPakai . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function excelPemesananCoveringPerArea()
    {
        $tglPakai = $this->request->getGet('tgl_pakai');
        $jenis = $this->request->getGet('jenis');

        $data = $this->pemesananModel->getDataPemesananCoveringPerArea($tglPakai, $jenis);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Judul
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'REPORT PEMESANAN ' . $jenis . ' COVERING');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
            $sheet->setCellValue('H' . $row, $item['ttl_kg']);
            $sheet->setCellValue('I' . $row, $item['ttl_cns']);
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
        $filename = 'Report_Pemesanan_' . $jenis . '_Per_Area' . '.xlsx';
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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
        $key            = $this->request->getGet('key');
        $tanggal_awal   = $this->request->getGet('tanggal_awal');
        $tanggal_akhir  = $this->request->getGet('tanggal_akhir');
        $data           = $this->pengeluaranModel->getFilterPengiriman($key, $tanggal_awal, $tanggal_akhir);

        // 1. Kelompokkan data berdasarkan 'jenis'
        $grouped = [];
        foreach ($data as $item) {
            $jenis = $item['jenis'] ?? 'Undefined';
            $grouped[$jenis][] = $item;
        }

        // 2. Inisialisasi Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Header kolom static
        $headers = [
            'No',
            'No Model',
            'Area',
            'Delivery Awal',
            'Delivery Akhir',
            'Item Type',
            'Kode Warna',
            'Warna',
            'Kgs Pesan',
            'Tanggal Keluar',
            'Kgs Kirim',
            'Cones Kirim',
            'Karung Kirim',
            'Lot Kirim',
            'Nama Cluster',
            'Keterangan GBN',
            'Admin'
        ];

        $sheetIndex = 0;
        foreach ($grouped as $jenis => $rows) {
            // Pilih atau buat sheet baru
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            // Rename sheet
            $sheet->setTitle(substr($jenis, 0, 31)); // batas 31 karakter

            // -- Judul --
            $sheet->mergeCells('A1:Q1');
            $sheet->setCellValue('A1', "REPORT PENGIRIMAN AREA - {$jenis}");
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // -- Tanggal Awal/Akhir --
            $sheet->setCellValue('A2', 'Tanggal Awal');
            $sheet->setCellValue('C2', ": {$tanggal_awal}");
            $sheet->setCellValue('N2', 'Tanggal Akhir');
            $sheet->setCellValue('Q2', ": {$tanggal_akhir}");
            foreach (['A2', 'C2', 'N2', 'Q2'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }

            // -- Header Kolom --
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '3', $header);
                $sheet->getStyle($col . '3')->getFont()->setBold(true);
                $col++;
            }

            // -- Isi Data --
            $rowNum = 4;
            $no = 1;
            foreach ($rows as $item) {
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $item['no_model']);
                $sheet->setCellValue("C{$rowNum}", $item['area_out']);
                $sheet->setCellValue("D{$rowNum}", $item['delivery_awal']);
                $sheet->setCellValue("E{$rowNum}", $item['delivery_akhir']);
                $sheet->setCellValue("F{$rowNum}", $item['item_type']);
                $sheet->setCellValue("G{$rowNum}", $item['kode_warna']);
                $sheet->setCellValue("H{$rowNum}", $item['color']);
                $sheet->setCellValue("I{$rowNum}", $item['ttl_kg']);
                $sheet->setCellValue("J{$rowNum}", $item['tgl_out']);
                $sheet->setCellValue("K{$rowNum}", $item['kgs_out']);
                $sheet->setCellValue("L{$rowNum}", $item['cns_out']);
                $sheet->setCellValue("M{$rowNum}", $item['krg_out']);
                $sheet->setCellValue("N{$rowNum}", $item['lot_out']);
                $sheet->setCellValue("O{$rowNum}", $item['nama_cluster']);
                $sheet->setCellValue("P{$rowNum}", $item['keterangan_gbn']);
                $sheet->setCellValue("Q{$rowNum}", $item['admin']);
                $rowNum++;
            }

            // -- Border Tabel --
            $lastRow = $rowNum - 1;
            $sheet->getStyle("A3:Q{$lastRow}")
                ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color'       => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

            // -- Auto-size Kolom --
            foreach (range('A', 'Q') as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        // Kembalikan ke sheet pertama sebelum download
        $spreadsheet->setActiveSheetIndex(0);

        // Download seperti biasa
        $filename = 'Report_Pengiriman_Area.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }


    public function exportGlobalReport()
    {
        $key = $this->request->getGet('key');
        $data = $this->masterOrderModel->getFilterReportGlobal($key);

        $getDeliv = 'http://172.23.44.14/CapacityApps/public/api/getDeliv/' . $key;
        $response = file_get_contents($getDeliv);
        $delivery = json_decode($response, true);
        $totalDel  = count($delivery);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GLOBAL ALL ' . $key);

        // Judul
        $sheet->mergeCells('A1:AA1');
        $sheet->setCellValue('A1', 'REPORT GLOBAL ' . $key);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Buyer', 'No Model', 'Delivery', 'Area', 'Item Type', 'Kode Warna', 'Warna', 'Loss', 'Qty PO', 'Qty PO(+)', 'Stock Awal', 'Stock Opname', 'Datang Solid', '(+) Datang Solid', 'Ganti Retur', 'Datang Lurex', '(+)Datang Lurex', 'Datang PB GBN', 'Retur PB Area', 'Pakai Area', 'Pakai Lain-Lain', 'Retur Stock', 'Retur Titip', 'Dipinjam', 'Pindah Order', 'Pindah Ke Stock Mati', 'Stock Akhir', 'Tagihan GBN', 'Jatah Area'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        $delIndex = 0;
        foreach ($data as $item) {
            // Format setiap nilai untuk memastikan nilai 0 dan angka dengan dua desimal
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['buyer'] ?: '-'); // no model
            $sheet->setCellValue('C' . $row, $item['no_model'] ?: '-'); // no model
            if ($delIndex < $totalDel) {
                $sheet->setCellValue('D' . $row, $delivery[$delIndex]['delivery']);
                $delIndex++;
            } else {
                $sheet->setCellValue('D' . $row, '');  // atau '-' sesuai preferensi
            }
            $sheet->setCellValue('E' . $row, $item['area'] ?: '-');
            $sheet->setCellValue('F' . $row, $item['item_type'] ?: '-'); // item type
            $sheet->setCellValue('G' . $row, $item['kode_warna'] ?: '-'); //kode warna
            $sheet->setCellValue('H' . $row, $item['color'] ?: '-'); // color
            $sheet->setCellValue('I' . $row, isset($item['loss']) ? number_format($item['loss'], 2, '.', '') : 0); // loss
            $sheet->setCellValue('J' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0); // qty po
            $sheet->setCellValue('K' . $row, '-'); // qty po (+)
            $sheet->setCellValue('L' . $row, isset($item['kgs_stock_awal']) ? number_format($item['kgs_stock_awal'], 2, '.', '') : 0); // stock awal
            $sheet->setCellValue('M' . $row, '-'); // stock opname
            $sheet->setCellValue('N' . $row, isset($item['kgs_kirim']) ? number_format($item['kgs_kirim'], 2, '.', '') : 0); // datan solid
            $sheet->setCellValue('O' . $row, '-'); // (+) datang solid
            $sheet->setCellValue('P' . $row, '-'); // ganti retur
            $sheet->setCellValue('Q' . $row, '-'); // datang lurex
            $sheet->setCellValue('R' . $row, '-'); // (+) datang lurex
            $sheet->setCellValue('S' . $row, '-'); // retur pb gbn
            $sheet->setCellValue('T' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0); // retur bp area
            $sheet->setCellValue('U' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0); // pakai area
            $sheet->setCellValue('V' . $row, '-'); // pakai lain-lain
            $sheet->setCellValue('W' . $row, '-'); // retur stock
            $sheet->setCellValue('X' . $row, '-'); // retur titip
            $sheet->setCellValue('Y' . $row, '-'); // dipinjam
            $sheet->setCellValue('Z' . $row, '-'); // pindah order
            $sheet->setCellValue('AA' . $row, '-'); // pindah ke stock mati
            $sheet->setCellValue('AB' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0); // stock akhir

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] - ($item['kgs_kirim'] + $item['kgs_stock_awal']) : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] - $item['kgs_out'] : 0;

            // Format Tagihan GBN dan Jatah Area
            $sheet->setCellValue('AC' . $row, number_format($tagihanGbn, 2, '.', '')); // tagihan gbn
            $sheet->setCellValue('AD' . $row, number_format($jatahArea, 2, '.', '')); // jatah area
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
        $sheet->getStyle("A3:AD{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'AD') as $col) {
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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['TANGGAL PAKAI', 'ITEM TYPE', 'WARNA', 'KODE WARNA', 'NO MODEL'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->mergeCells($col . '2:' . $col . '3');
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Data & Total Result Header
        $sheet->mergeCells('F2:F3')->setCellValue('F2', 'Data');
        $sheet->mergeCells('G2:G3')->setCellValue('G2', 'Total Result');
        $sheet->getStyle('F2:G2')->getFont()->setBold(true);
        $sheet->getStyle('F2:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:G2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
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
            // Isi per area
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_jl_mc']) ? $item['ttl_jl_mc'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
            $row++;

            // Row 2: TOTAL PESAN (KG)
            $sheet->setCellValue('F' . $row, 'Sum - TOTAL PESAN (KG):');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_kg']) ? $item['ttl_kg'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
            $row++;

            // Row 3: CONES
            $sheet->setCellValue('F' . $row, 'Sum - CONES:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_cns']) ? $item['ttl_cns'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['TANGGAL PAKAI', 'ITEM TYPE', 'WARNA', 'KODE WARNA', 'NO MODEL'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->mergeCells($col . '2:' . $col . '3');
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }

        // Data & Total Result Header
        $sheet->mergeCells('F2:F3')->setCellValue('F2', 'Data');
        $sheet->mergeCells('G2:G3')->setCellValue('G2', 'Total Result');
        $sheet->getStyle('F2:G2')->getFont()->setBold(true);
        $sheet->getStyle('F2:G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:G2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
            $sheet->getStyle($col . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $col++;
        }
        // dd ($data);
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
            // Isi per area
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_jl_mc']) ? $item['ttl_jl_mc'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
            $row++;

            // Row 2: TOTAL PESAN (KG)
            $sheet->setCellValue('F' . $row, 'Sum - TOTAL PESAN (KG):');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_kg']) ? $item['ttl_kg'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
            $row++;

            // Row 3: CONES
            $sheet->setCellValue('F' . $row, 'Sum - CONES:');
            $sheet->setCellValue('G' . $row, '=SUM(H' . $row . ':U' . $row . ')');
            $col = 'H';
            foreach ($areaHeaders as $area) {
                if ($item['admin'] == $area) {
                    $sheet->setCellValue($col . $row, isset($item['ttl_cns']) ? $item['ttl_cns'] : 0);
                } else {
                    $sheet->setCellValue($col . $row, 0);
                }
                $col++;
            }
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

        // Untuk setiap kategori, hitung total per area sesuai areaHeaders
        $row = $row - 3;
        foreach ($categories as $label => $keyword) {
            $colLetter = 'H';
            foreach ($areaHeaders as $area) {
                // SUMIF untuk kolom area spesifik
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
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $header = ["NO", "JENIS BAHAN BAKU", "TANGGAL RETUR", "AREA", "NO MODEL", "ITEM TYPE", "KODE WARNA", "WARNA", "LOSS", "QTY PO", "QTY PO(+)", "QTY KIRIM", "CONES KIRIM", "KARUNG KIRIM", "LOT KIRIM", "QTY RETUR", "CONES RETUR", "KARUNG RETUR", "LOT RETUR", "KATEGORI", "KET AREA", "KET GBN", "WAKTU ACC RETUR", "USER"];
        $sheet->fromArray([$header], NULL, 'A3');

        // Styling Header
        $sheet->getStyle('A3:X3')->getFont()->setBold(true);
        $sheet->getStyle('A3:X3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:X3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $sheet->getStyle('A4:X' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:X' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

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
        $jenis = $this->request->getGet('jenis');

        $data = $this->scheduleCelupModel->getFilterSchWeekly($tglAwal, $tglAkhir, $jenis);

        $getMesin = $this->mesinCelupModel
            ->orderBy('no_mesin', 'ASC')
            ->findAll();
        $getMesin = array_values(array_filter($getMesin, function ($m) use ($jenis) {
            if ($jenis === 'BENANG') {
                return $m['no_mesin'] >= 1 && $m['no_mesin'] <= 38;
            } else if ($jenis === 'ACRYLIC') {
                return $m['no_mesin'] >= 39 && $m['no_mesin'] <= 43;
            } else {
                return $m['no_mesin'] >= 1 && $m['no_mesin'] <= 43;
            }
        }));
        // dd($getMesin);
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
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
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
                            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
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
        $getDeliv = 'http://172.23.44.14/CapacityApps/public/api/getDeliv/' . $key;
        $response = file_get_contents($getDeliv);

        // $data = $this->stockModel->getFilterReportGlobalBenang($key);
        // dd($data);
        // dd($key);
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
            $delivery = json_decode($response, true);
            $totalDel  = count($delivery);

            // dd($data, $delivery);
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($title);

            // Judul di baris 1
            $sheet->mergeCells('A1:AA1');
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Header di baris 3
            $headers = [
                'No',
                'No Model',
                'Delivery',
                'Area',
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
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $col++;
            }

            // Isi Data mulai baris 4
            $row = 4;
            $no = 1;
            $delIndex  = 0;
            foreach ($data as $item) {
                // dd($delivery);
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['no_model'] ?: '-');
                if ($delIndex < $totalDel) {
                    $sheet->setCellValue('C' . $row, $delivery[$delIndex]['delivery']);
                    $delIndex++;
                } else {
                    $sheet->setCellValue('C' . $row, '');  // atau '-' sesuai preferensi
                }
                $sheet->setCellValue('D' . $row, $item['area'] ?: '-');
                $sheet->setCellValue('E' . $row, $item['item_type'] ?: '-');
                $sheet->setCellValue('F' . $row, $item['kode_warna'] ?: '-');
                $sheet->setCellValue('G' . $row, $item['warna'] ?: '-');
                $sheet->setCellValue('H' . $row, $item['loss'] . '%' ?: '-');
                $sheet->setCellValue('I' . $row, $item['qty_po'] ?: 0);
                $sheet->setCellValue('J' . $row, $item['kgs_stock_awal'] ?: 0);
                $sheet->setCellValue('K' . $row, $item['datang_solid'] ?: 0);
                $sheet->setCellValue('L' . $row, $item['ganti_retur'] ?: 0);
                $sheet->setCellValue('M' . $row, $item['pakai_area'] ?: 0);
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
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

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
        // Ambil input
        $jenisMesin = $this->request->getPost('jenis_mesin');
        $jenisCover  = $this->request->getPost('jenis_cover');
        $jenisBenang = $this->request->getPost('jenis_benang');
        if (empty($jenisMesin || empty($jenisBenang))) {
            return redirect()->back()->with('error', 'Jenis Mesin dan Jenis Benang tidak boleh kosong.');
        }

        // Data stok
        $data = $this->coveringStockModel->getStockCover($jenisMesin, $jenisBenang, $jenisCover);

        // Inisialisasi spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Setup kertas A4 portrait dan margin
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setFitToPage(true);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.4)->setRight(0.2);

        // ----- Header Statis (Baris 1-5) -----
        // Logo
        $sheet->mergeCells('A1:B2');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $drawing = new Drawing();
        $drawing->setName('Logo')->setDescription('Logo')->setPath('assets/img/logo-kahatex.png')
            ->setCoordinates('A1')->setHeight(50)->setOffsetX(40)->setOffsetY(5)->setWorksheet($sheet);

        // Judul Perusahaan
        $sheet->mergeCells('A3:B3')->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // FORMULIR & Departemen
        $sheet->mergeCells('C1:Q1')->setCellValue('C1', 'FORMULIR');
        $sheet->mergeCells('C2:Q2')->setCellValue('C2', 'DEPARTEMEN COVERING');
        $sheet->mergeCells('C3:Q3')->setCellValue('C3', 'STOCK ' . $jenisCover . ' COVER DI GUDANG COVERING');
        $sheet->getStyle('C1:Q3')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('C1:Q3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C1:Q1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('99FFFF');

        // Dokumen & Tanggal Revisi - TAMBAHKAN PLACEHOLDER UNTUK HALAMAN
        $sheet->mergeCells('A4:B4')->setCellValue('A4', 'No. Dokumen');
        $sheet->mergeCells('C4:K4')->setCellValue('C4', 'FOR-CC-151/REV_01/HAL_?/?'); // Placeholder
        $sheet->mergeCells('L4:N4')->setCellValue('L4', 'Tanggal Revisi');
        $sheet->mergeCells('O4:Q4')->setCellValue('O4', '11 November 2019');

        // Jenis Benang & Tanggal Cetak
        $sheet->mergeCells('A5:B5')->setCellValue('A5', 'Jenis Benang');
        $sheet->mergeCells('C5:K5')->setCellValue('C5', $jenisBenang);
        $sheet->mergeCells('L5:N5')->setCellValue('L5', 'Tanggal');
        $sheet->mergeCells('O5:Q5')->setCellValue('O5', date('d-M-Y'));
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A4:Q5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);



        // ----- Header Dinamis (Baris 8-9) -----
        $renderHeader = function (Worksheet $sheet, int $row) {
            // Baris utama header
            $sheet->mergeCells("A{$row}:A" . ($row + 1))->setCellValue("A{$row}", 'Jenis');
            $sheet->mergeCells("B{$row}:B" . ($row + 1))->setCellValue("B{$row}", 'Color');
            $sheet->mergeCells("C{$row}:C" . ($row + 1))->setCellValue("C{$row}", 'Code');
            $sheet->mergeCells("D{$row}:D" . ($row + 1))->setCellValue("D{$row}", 'LMD');
            $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", 'Total');
            $sheet->mergeCells("J{$row}:K{$row}")->setCellValue("J{$row}", 'Stock');
            $sheet->mergeCells("L{$row}:Q{$row}")->setCellValue("L{$row}", 'Keterangan');

            // Sub-header
            $sheet->mergeCells("E" . ($row + 1) . ":F" . ($row + 1))->setCellValue("E" . ($row + 1), 'Cones');
            $sheet->mergeCells("G" . ($row + 1) . ":H" . ($row + 1))->setCellValue("G" . ($row + 1), 'Kg');
            $sheet->setCellValue("I" . ($row + 1), 'Box');
            $sheet->setCellValue("J" . ($row + 1), 'Ada');
            $sheet->setCellValue("K" . ($row + 1), 'Habis');
            $sheet->setCellValue("L" . ($row + 1), 'Rak No');
            $sheet->setCellValue("M" . ($row + 1), 'Kanan');
            $sheet->setCellValue("N" . ($row + 1), 'Kiri');
            $sheet->setCellValue("O" . ($row + 1), 'Atas');
            $sheet->setCellValue("P" . ($row + 1), 'Bawah');
            $sheet->setCellValue("Q" . ($row + 1), 'Palet No');

            // Style header
            $sheet->getStyle("A{$row}:Q" . ($row + 1))->applyFromArray([
                'font' => ['bold' => true, 'size' => 11],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $row += 2;
        };

        // Mulai baris data
        $startRow    = 8;
        $row         = $startRow;
        $rowsPerPage = 70; // Jumlah baris per halaman
        // Set baris yang akan diulang (header statis + dinamis)
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 7);

        // Render header dinamis pertama
        $renderHeader($sheet, $row - 2);

        // Urutkan data berdasarkan jenis (denier)
        usort($data, function ($a, $b) {
            return strcmp($a['jenis'], $b['jenis']);
        });

        $groupStartRow = $row;     // baris data pertama dari grup
        $currentJenis = null;
        $currentDr    = null;
        $subtotalCones = 0;
        $subtotalKg = 0;

        // Loop data dan atur page break
        foreach ($data as $item) {
            // Jika jenis berubah (kecuali data pertama)
            // Jika jenis atau dr berubah (kecuali data pertama)
            // Deteksi pergantian grup (jenis/dr)
            if (
                $currentJenis !== null
                && ($currentJenis !== $item['jenis'] || $currentDr !== $item['dr'])
            ) {
                // 1) Merge cell jenis untuk grup lama
                $sheet->mergeCells("A{$groupStartRow}:A" . ($row));

                // 2) Tulis subtotal grup lama
                $sheet->mergeCells("B{$row}:C{$row}")
                    ->setCellValue("B{$row}", "SUBTOTAL");
                $sheet->mergeCells("E{$row}:F{$row}")
                    ->setCellValue("E{$row}", $subtotalCones);
                $sheet->mergeCells("G{$row}:H{$row}")
                    ->setCellValue("G{$row}", $subtotalKg);
                // Style subtotal
                $sheet->getStyle("B{$row}:H{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'D3D3D3'] // Abu-abu muda
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER
                    ]
                ]);
                $row++;

                // Reset subtotal untuk jenis baru
                $subtotalCones = 0;
                $subtotalKg = 0;

                $groupStartRow = $row;
            }

            $currentJenis = $item['jenis'];
            $currentDr    = $item['dr'];

            // Cek jika perlu page break (SETELAH subtotal)
            if (($row - $startRow) % $rowsPerPage === 0 && $row > $startRow) {
                $sheet->setBreak("A{$row}", Worksheet::BREAK_ROW);
            }


            // Isi data
            $sheet->setCellValue("A{$row}", strtoupper($item['jenis']) . ' DR ' . strtoupper($item['dr']));
            $sheet->setCellValue("B{$row}", $item['color']);
            $sheet->setCellValue("C{$row}", $item['code']);
            $sheet->setCellValue("D{$row}", $item['lmd']);
            $sheet->setCellValue("E{$row}", $item['ttl_cns']);
            $sheet->setCellValue("G{$row}", $item['ttl_kg']);
            $sheet->setCellValue("J{$row}", $item['ttl_kg'] > 0 ? '✓' : '');
            $sheet->setCellValue("K{$row}", $item['ttl_kg'] <= 0 ? '✓' : '');

            // Akumulasi subtotal
            $subtotalCones += $item['ttl_cns'];
            $subtotalKg += $item['ttl_kg'];

            // Style data
            $sheet->getStyle("A{$row}:Q" . ($row + 1))->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER
                ]
            ]);
            // sheet A wraptext
            $sheet->getStyle("A{$row}")->getAlignment()->setWrapText(true);

            $row++;
        }

        // Setelah loop, tambahkan subtotal untuk grup terakhir
        if ($currentJenis !== null) {
            // Merge kolom jenis untuk grup terakhir
            $sheet->mergeCells("A{$groupStartRow}:A" . ($row));

            // Subtotal akhir
            $sheet->mergeCells("B{$row}:C{$row}")
                ->setCellValue("B{$row}", "SUBTOTAL");
            $sheet->mergeCells("E{$row}:F{$row}")
                ->setCellValue("E{$row}", $subtotalCones);
            $sheet->mergeCells("G{$row}:H{$row}")
                ->setCellValue("G{$row}", $subtotalKg);
            $sheet->getStyle("B{$row}:H{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'D3D3D3']
                ],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $row++;
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
        $row += 2;

        // 2) Tulis summary di sheet
        // spasi antara data dan summary
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
        $rowstar = $row - 1;
        $rowEnd = $row;
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFont()->setBold(true);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->getStartColor()->setARGB('DCDCDC');
        $row += 2;

        // 4) Tanda tangan penanggung jawab
        $sheet->mergeCells("E{$row}:I{$row}")->setCellValue("E{$row}", 'Yang Bertanggung Jawab : __________');
        $sheet->getStyle("E{$row}")
            ->getFont()->setItalic(true);

        // HITUNG TOTAL HALAMAN
        $totalDataRows = $row - $startRow;
        $totalPages = max(1, ceil($totalDataRows / $rowsPerPage));

        // UPDATE PLACEHOLDER DI CONTENT (opsional)
        $sheet->setCellValue('C4', "FOR-CC-151/REV_01/HAL_1/$totalPages");
        // Download
        $filename = 'Formulir_Stock_' . $jenisBenang . '_' . $jenisCover . '_' . date('Ymd') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportListPemesananSpdxKaretPertgl()
    {
        $key   = $this->request->getGet('tglPakai');
        $jenis = $this->request->getGet('jenis');

        // 1. Ambil semua data, lalu group per admin
        $allData = $this->pemesananModel->getDataPemesananPerArea($key, $jenis);

        $grouped = [];
        foreach ($allData as $item) {
            $admin = $item['admin'] ?: 'Unknown';
            $grouped[$admin][] = $item;
        }

        // 2. Inisialisasi Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $filename = 'Report_PEMESANAN_BAHAN_BAKU_' . $jenis . '_' . $key . '.xlsx';
        $sheetIndex = 0;
        foreach ($grouped as $adminName => $items) {
            // Untuk sheet pertama, pakai sheet default; selebihnya createSheet()
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            $sheet->setTitle($adminName);

            // --- Judul & keterangan ---
            $sheet->mergeCells('A1:O1');
            $sheet->setCellValue('A1', 'REPORT PERMINTAAN BAHAN BAKU');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A3:C3');
            $sheet->setCellValue('A3', 'JENIS BAHAN BAKU');
            $sheet->setCellValue('D3', ': ' . $jenis);
            $sheet->setCellValue('H3', 'AREA');
            $sheet->setCellValue('I3', ': ' . $adminName);
            $sheet->setCellValue('M3', 'TANGGAL PAKAI');
            $sheet->mergeCells('N3:O3');
            $sheet->setCellValue('N3', ': ' . $key);
            foreach (['A3', 'C3', 'H3', 'I3', 'M3', 'N3'] as $cell) {
                $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }

            // --- Header kolom ---
            $headers = [
                'NO',
                'JAM',
                'TGL PESAN',
                'NO MODEL',
                'ITEM TYPE',
                'KODE WARNA',
                'WARNA',
                'LOT',
                'JL MC',
                'TOTAL',
                'CONES',
                'KETERANGAN',
                'BAGIAN PERSIAPAN',
                'QTY OUT',
                'CNS OUT'
            ];
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '4', $h);
                $sheet->getStyle($col . '4')->getFont()->setBold(true);
                $col++;
            }

            // --- Data baris ---
            $row = 5;
            $no  = 1;
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['jam_pesan'] ?: '-');
                $sheet->setCellValue('C' . $row, $item['tgl_pesan'] ?: '-');
                $sheet->setCellValue('D' . $row, $item['no_model'] ?: '-');
                $sheet->setCellValue('E' . $row, $item['item_type'] ?: '-');
                $sheet->setCellValue('F' . $row, $item['kode_warna'] ?: '-');
                $sheet->setCellValue('G' . $row, $item['color'] ?: '-');
                $sheet->setCellValue('H' . $row, '');
                $sheet->setCellValue('I' . $row, $item['ttl_jl_mc'] ?? 0);
                $sheet->setCellValue('J' . $row, $item['ttl_kg']    ?? 0);
                $sheet->setCellValue('K' . $row, $item['ttl_cns']   ?? 0);
                $sheet->setCellValue('L' . $row, '');
                $sheet->setCellValue('M' . $row, '');
                $sheet->setCellValue('N' . $row, '');
                $sheet->setCellValue('O' . $row, '');
                $row++;
            }

            // Buat bold untuk total
            foreach (['I', 'J', 'K', 'N', 'O'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
            }

            // Merge kolom A:H di baris total
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Set total dengan SUM formula
            $sheet->setCellValue("I{$row}", "=SUM(I4:I" . ($row - 1) . ")");
            $sheet->setCellValue("J{$row}", "=SUM(J4:J" . ($row - 1) . ")");
            $sheet->setCellValue("K{$row}", "=SUM(K4:K" . ($row - 1) . ")");
            $sheet->setCellValue("N{$row}", "=SUM(N4:N" . ($row - 1) . ")");
            $sheet->setCellValue("O{$row}", "=SUM(O4:O" . ($row - 1) . ")");

            // Bold untuk angka total
            foreach (['I', 'J', 'K', 'N', 'O'] as $col) {
                $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
            }

            // Tambahkan border baris total
            $borderStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle("A{$row}:O{$row}")->applyFromArray($borderStyle);

            // --- Border & wrap text ---
            $lastRow = $row;

            // Border untuk semua kolom dari A3 sampai O baris terakhir
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];

            $sheet->getStyle("A4:O{$lastRow}")->applyFromArray($styleArray);

            // Atur lebar kolom agar pas 1 halaman
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 10,  // JAM
                'C' => 12,  // TGL PESAN
                'D' => 12,  // NO MODEL
                'E' => 18,  // ITEM TYPE
                'F' => 15,  // KODE WARNA
                'G' => 15,  // WARNA
                'H' => 15,  // LOT
                'I' => 10,  // JL MC
                'J' => 10,  // TOTAL
                'K' => 10,  // CONES
                'L' => 18,  // KETERANGAN
                'M' => 20,  // BAGIAN PERSIAPAN
                'N' => 10,  // QTY OUT
                'O' => 10,  // CNS OUT
            ];

            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Aktifkan wrap text untuk kolom tertentu dari baris 2 sampai baris terakhir
            $wrapColumns = ['E', 'F', 'G', 'L', 'M'];
            foreach ($wrapColumns as $col) {
                $sheet->getStyle("{$col}2:{$col}{$lastRow}")
                    ->getAlignment()->setWrapText(true);
            }

            // Pastikan muat 1 halaman saat cetak
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // Set orientasi halaman menjadi landscape
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

            // Ukuran kertas A4 (bisa juga A3, Legal, dll)
            $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            // Margin halaman (opsional, bisa disesuaikan)
            $sheet->getPageMargins()->setTop(0.5);
            $sheet->getPageMargins()->setRight(0.4);
            $sheet->getPageMargins()->setLeft(0.4);
            $sheet->getPageMargins()->setBottom(0.5);

            // Aktifkan fit to width (agar tidak kepotong saat print)
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);

            // Header/Footer cetakan (opsional)
            // $sheet->getHeaderFooter()->setOddFooter('&L&B' . $filename . '&RPage &P of &N');

            $sheetIndex++;
        }

        // kembali ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        // --- Output ke browser ---
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaPakaiBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiBenang($bulan, $noModel, $kodeWarna);
        // dd($data);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA PAKAI BENANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // Pakai
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'PAKAI');

        // (+) Pakai
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) PAKAI');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:Y3');
        $sheet->setCellValue('X3', 'RETUR');
        $sheet->setCellValue('X4', 'KGS');
        $sheet->setCellValue('Y4', 'LOT');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            // dd($item);
            $sisa = (($item['kgs_out'] ?? 0 + 0) - $item['kgs_retur'] - ($item['kg_po'] + 0));
            // $sisa = (($item['kgs_out'] ?? 0 + $item['kgs_out_plus'] ?? 0) - $item['kgs_retur'] - ($item['kg_po'] + $item['kg_po_plus']));

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area_out']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['warna']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_out'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_out_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['lot_retur'] ?? '');
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Pakai Benang' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaPakaiNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiNylon($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA PAKAI NYLON');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // Pakai
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'PAKAI');

        // (+) Pakai
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) PAKAI');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:Y3');
        $sheet->setCellValue('X3', 'RETUR');
        $sheet->setCellValue('X4', 'KGS');
        $sheet->setCellValue('Y4', 'LOT');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $sisa = (($item['kgs_out'] ?? 0 + 0) - $item['kgs_retur'] - ($item['kg_po'] + 0));
            // $sisa = (($item['kgs_out'] ?? 0 + $item['kgs_out_plus'] ?? 0) - $item['kgs_retur'] - ($item['kg_po'] + $item['kg_po_plus']));

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area_out']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['warna']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_out'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_out_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['lot_retur'] ?? '');
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Pakai Nylon' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaPakaiSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiSpandex($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA PAKAI SPANDEX');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // Pakai
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'PAKAI');

        // (+) Pakai
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) PAKAI');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:Y3');
        $sheet->setCellValue('X3', 'RETUR');
        $sheet->setCellValue('X4', 'KGS');
        $sheet->setCellValue('Y4', 'LOT');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            // dd($item);
            $sisa = (($item['kgs_out'] ?? 0 + 0) - $item['kgs_retur'] - ($item['kg_po'] + 0));
            // $sisa = (($item['kgs_out'] ?? 0 + $item['kgs_out_plus'] ?? 0) - $item['kgs_retur'] - ($item['kg_po'] + $item['kg_po_plus']));

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area_out']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['warna']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_out'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_out_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['lot_retur'] ?? '');
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Pakai Spandex' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaPakaiKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->stockModel->getFilterSisaPakaiKaret($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA PAKAI KARET');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // Pakai
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'PAKAI');

        // (+) Pakai
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) PAKAI');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:Y3');
        $sheet->setCellValue('X3', 'RETUR');
        $sheet->setCellValue('X4', 'KGS');
        $sheet->setCellValue('Y4', 'LOT');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            // dd($item);
            $sisa = (($item['kgs_out'] ?? 0 + 0) - $item['kgs_retur'] - ($item['kg_po'] + 0));
            // $sisa = (($item['kgs_out'] ?? 0 + $item['kgs_out_plus'] ?? 0) - $item['kgs_retur'] - ($item['kg_po'] + $item['kg_po_plus']));

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area_out']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['warna']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_out'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_out_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['lot_retur'] ?? '');
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Pakai Karet' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function apiexportGlobalReport($key)
    {
        $data = $this->masterOrderModel->getFilterReportGlobal($key);

        $getDeliv = 'http://172.23.44.14/CapacityApps/public/api/getDeliv/' . $key;
        $response = file_get_contents($getDeliv);
        $delivery = json_decode($response, true);
        $totalDel  = count($delivery);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GLOBAL ALL ' . $key);

        // Judul
        $sheet->mergeCells('A1:AA1');
        $sheet->setCellValue('A1', 'REPORT GLOBAL ' . $key);
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header
        $headers = ['No', 'Buyer', 'No Model', 'Delivery', 'Area', 'Item Type', 'Kode Warna', 'Warna', 'Loss', 'Qty PO', 'Qty PO(+)', 'Stock Awal', 'Stock Opname', 'Datang Solid', '(+) Datang Solid', 'Ganti Retur', 'Datang Lurex', '(+)Datang Lurex', 'Datang PB GBN', 'Retur PB Area', 'Pakai Area', 'Pakai Lain-Lain', 'Retur Stock', 'Retur Titip', 'Dipinjam', 'Pindah Order', 'Pindah Ke Stock Mati', 'Stock Akhir', 'Tagihan GBN', 'Jatah Area'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $col++;
        }

        // Data
        $row = 4;
        $no = 1;
        $delIndex = 0;
        foreach ($data as $item) {
            // Format setiap nilai untuk memastikan nilai 0 dan angka dengan dua desimal
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['buyer'] ?: '-'); // no model
            $sheet->setCellValue('C' . $row, $item['no_model'] ?: '-'); // no model
            if ($delIndex < $totalDel) {
                $sheet->setCellValue('D' . $row, $delivery[$delIndex]['delivery']);
                $delIndex++;
            } else {
                $sheet->setCellValue('D' . $row, '');  // atau '-' sesuai preferensi
            }
            $sheet->setCellValue('E' . $row, $item['area'] ?: '-');
            $sheet->setCellValue('F' . $row, $item['item_type'] ?: '-'); // item type
            $sheet->setCellValue('G' . $row, $item['kode_warna'] ?: '-'); //kode warna
            $sheet->setCellValue('H' . $row, $item['color'] ?: '-'); // color
            $sheet->setCellValue('I' . $row, isset($item['loss']) ? number_format($item['loss'], 2, '.', '') : 0); // loss
            $sheet->setCellValue('J' . $row, isset($item['kgs']) ? number_format($item['kgs'], 2, '.', '') : 0); // qty po
            $sheet->setCellValue('K' . $row, '-'); // qty po (+)
            $sheet->setCellValue('L' . $row, isset($item['kgs_stock_awal']) ? number_format($item['kgs_stock_awal'], 2, '.', '') : 0); // stock awal
            $sheet->setCellValue('M' . $row, '-'); // stock opname
            $sheet->setCellValue('N' . $row, isset($item['kgs_kirim']) ? number_format($item['kgs_kirim'], 2, '.', '') : 0); // datan solid
            $sheet->setCellValue('O' . $row, '-'); // (+) datang solid
            $sheet->setCellValue('P' . $row, '-'); // ganti retur
            $sheet->setCellValue('Q' . $row, '-'); // datang lurex
            $sheet->setCellValue('R' . $row, '-'); // (+) datang lurex
            $sheet->setCellValue('S' . $row, '-'); // retur pb gbn
            $sheet->setCellValue('T' . $row, isset($item['kgs_retur']) ? number_format($item['kgs_retur'], 2, '.', '') : 0); // retur bp area
            $sheet->setCellValue('U' . $row, isset($item['kgs_out']) ? number_format($item['kgs_out'], 2, '.', '') : 0); // pakai area
            $sheet->setCellValue('V' . $row, '-'); // pakai lain-lain
            $sheet->setCellValue('W' . $row, '-'); // retur stock
            $sheet->setCellValue('X' . $row, '-'); // retur titip
            $sheet->setCellValue('Y' . $row, '-'); // dipinjam
            $sheet->setCellValue('Z' . $row, '-'); // pindah order
            $sheet->setCellValue('AA' . $row, '-'); // pindah ke stock mati
            $sheet->setCellValue('AB' . $row, isset($item['kgs_in_out']) ? number_format($item['kgs_in_out'], 2, '.', '') : 0); // stock akhir

            // Tagihan GBN dan Jatah Area perhitungan
            $tagihanGbn = isset($item['kgs']) ? $item['kgs'] - ($item['kgs_kirim'] + $item['kgs_stock_awal']) : 0;
            $jatahArea = isset($item['kgs']) ? $item['kgs'] - $item['kgs_out'] : 0;

            // Format Tagihan GBN dan Jatah Area
            $sheet->setCellValue('AC' . $row, number_format($tagihanGbn, 2, '.', '')); // tagihan gbn
            $sheet->setCellValue('AD' . $row, number_format($jatahArea, 2, '.', '')); // jatah area
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
        $sheet->getStyle("A3:AD{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'AD') as $col) {
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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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
                $newSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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

    public function exportPoBooking()
    {
        $noModel = $this->request->getGet('no_model');
        $data = $this->openPoModel->getPoBookingByNoModel($noModel);
        // dd($noModel);
        function applyBorders($style, $borders)
        {
            $style['borders'] = $borders;
            return $style;
        }
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Base styles
        $baseBold = [
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $baseNormal = [
            'font' => ['size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $centerTop = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
        ];
        $centerMiddle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $centerMiddleWrap = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $leftMiddle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $justify = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // ===== Drawing logo =====
        $drawing = new Drawing();
        $drawing->setName('Logo')
            ->setDescription('PT. KAHATEX Logo')
            ->setPath(FCPATH . 'assets/img/logo-kahatex.png')
            ->setWorksheet($sheet)
            ->setCoordinates('B1')
            ->setOffsetX(20)
            ->setOffsetY(10)
            ->setHeight(1.25 * 37.7952755906)
            ->setWidth(1.25 * 37.7952755906);

        // Define outline style for full document
        $outlineStyle = ['borders' => ['outline' => ['borderStyle' => Border::BORDER_DOUBLE]]];
        // $lineStyle = ['AllBorder' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]];

        // ===== Header blocks =====
        $headers = [
            [
                'range'   => 'A1:B3',
                'value'   => 'PT. KAHATEX',
                'style'   => array_merge($baseBold, $centerTop),
                'borders' => ['left' => ['borderStyle' => Border::BORDER_DOUBLE], 'right' => ['borderStyle' => Border::BORDER_DOUBLE], 'top' => ['borderStyle' => Border::BORDER_DOUBLE], 'bottom' => ['borderStyle' => Border::BORDER_DOUBLE]],
            ],
            [
                'range'   => 'C1:P1',
                'value'   => 'FORMULIR',
                'style'   => array_merge(
                    ['font' => ['bold' => true, 'size' => 16, 'name' => 'Arial']],
                    $centerMiddle,
                    ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '99FFFF']]]
                ),
                'borders' => ['left' => ['borderStyle' => Border::BORDER_DOUBLE], 'right' => ['borderStyle' => Border::BORDER_DOUBLE], 'top' => ['borderStyle' => Border::BORDER_DOUBLE], 'bottom' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];

        foreach ($headers as $h) {
            $sheet->mergeCells($h['range']);
            $sheet->setCellValue(explode(':', $h['range'])[0], $h['value']);
            $sheet->getStyle($h['range'])->applyFromArray(applyBorders($h['style'], $h['borders']));
        }

        // ===== Column widths =====n
        $colWidths = ['A' => 5, 'B' => 17, 'C' => 14, 'D' => 7, 'E' => 7, 'F' => 7, 'G' => 7, 'H' => 7, 'I' => 5, 'J' => 17, 'K' => 14, 'L' => 7, 'M' => 7, 'N' => 7, 'O' => 7, 'P' => 7];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ===== Row heights (1-26) =====
        $rowHeightPts = 0.7 / 0.0352778;
        for ($row = 1; $row <= 26; $row++) {
            $sheet->getRowDimension((string) $row)->setRowHeight($rowHeightPts);
        }

        $fields = [
            ['range' => 'C2:P2', 'value' => 'DEPARTEMEN KAOS KAKI',  'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'C3:P3', 'value' => 'BAGIAN PENCELUPAN',    'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'A4:B4', 'value' => 'No. Dokumen',          'style' => array_merge($baseNormal, $leftMiddle)],
            ['range' => 'C4:I4', 'value' => 'FOR - KK - 013',       'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'J4:K4', 'value' => 'Halaman',              'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'L4:P4', 'value' => '1/1',                  'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'A5:B5', 'value' => 'Tanggal Efektif',      'style' => array_merge($baseNormal, $leftMiddle)],
            ['range' => 'C5:I5', 'value' => '01 Desember 2016',     'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'J5:K5', 'value' => 'Revisi',               'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'L5:P5', 'value' => '00',                   'style' => array_merge($baseBold, $centerMiddle)],
            // Tambahan kolom baris 6 (tanpa border)
            ['range' => 'A6:E6', 'value' => 'PEMESANAN :', 'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],
            ['range' => 'F6:K6', 'value' => 'TANGGAL :',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],
            ['range' => 'L6:P6', 'value' => 'NO. :',       'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],

            // Tambahan kolom baris 7
            ['range' => 'A7:B7', 'value' => 'NAMA',                 'style' => array_merge($baseBold, $centerMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'C7:E7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F7:H7', 'value' => 'JUMLAH PESANAN',       'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I7:J7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K7:K7', 'value' => 'JENIS',                'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L7:M7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N7:P7', 'value' => 'TGL. PENYERAHAN',      'style' => array_merge($baseBold, $leftMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_THIN],
                'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
            ]],
            // Tambahan kolom baris 8
            ['range' => 'A8:B9', 'value' => 'TEMPAT PENYERAHAN',    'style' => array_merge($baseBold, $justify), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'C8:E9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F8:H9', 'value' => 'JUMLAH PIECE',         'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I8:J9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K8:K9', 'value' => 'CODE',                 'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L8:M9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N8:P9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_THIN],
                'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
            ]],

            // Header Isi Tabel
            ['range' => 'A10:A11', 'value' => 'NO.',                     'style' => array_merge($baseBold, $centerMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'B10:B11', 'value' => 'WARNA',                   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'C10:C11', 'value' => 'JUMLAH PEMESANAN',        'style' => array_merge($baseBold, $centerMiddleWrap), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'D10:H10', 'value' => 'JUMLAH YANG DISERAHKAN',   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'D11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'E11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'G11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'H11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I10:I11', 'value' => 'NO.',                   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'J10:J11', 'value' => 'WARNA',        'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K10:K11', 'value' => 'JUMLAH PESANAN',   'style' => array_merge($baseBold, $centerMiddleWrap), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L10:P10', 'value' => 'JUMLAH YANG DISERAHKAN',   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'M11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'O11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'P11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
        ];

        // loop apply fields with conditional borders with conditional borders
        foreach ($fields as $f) {
            $sheet->mergeCells($f['range']);
            $sheet->setCellValue(explode(':', $f['range'])[0], $f['value']);
            $borders = $f['borders'] ?? ['outline' => ['borderStyle' => Border::BORDER_THIN]];
            $sheet->getStyle($f['range'])->applyFromArray(applyBorders($f['style'], $borders));
        }

        // Terapkan gaya
        // $sheet->getStyle('A12:P26')->applyFromArray($lineStyle);

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        // dd($data);
        $tglArr = explode('-', date('Y-m-d', strtotime($data[0]['created_at'])));
        $tgl = $tglArr[2] . '-' . $bulan[$tglArr[1]] . '-' . $tglArr[0];
        $sheet->setCellValue('F6', 'TANGGAL : ' . $tgl);
        $sheet->setCellValue('L7', $data[0]['jenis']);
        $sheet->getStyle('L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L8', $data[0]['ukuran']);
        $sheet->getStyle('L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if (session()->get('role') === 'gbn') {
            $sheet->setCellValue('C8', 'Gudang Benang');
            $sheet->getStyle('C8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('C8', '');
        }

        $no = 1;
        $row = 16;

        $sheet->setCellValue('B12', $data[0]['jenis'] . ' ' . $data[0]['spesifikasi_benang'] . ' ' . $data[0]['keterangan']);
        $sheet->setCellValue('B13', 'Order :');
        $sheet->setCellValue('B14', 'Model :');

        // $sheet->setCellValue('C13', 'Ini adalah Order');
        $sheet->setCellValue('C14', $data[0]['no_model']);

        foreach ($data as $po) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, $po['color']);
            $sheet->setCellValue('C' . $row, $po['kode_warna']);
            $sheet->setCellValue('K' . $row, $po['kg_po'] . ' kg');
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->setCellValue('A27', 'Yang Bertanggung Jawab / Tanda tangan : (.......................................)');
        $sheet->mergeCells('A27:P27');

        // Terapkan gaya
        $sheet->getStyle('A27:P27')->applyFromArray([
            'font' => $baseBold['font'],
            'alignment' => $leftMiddle['alignment'],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Warna garis, opsional
                ],
            ],
        ]);

        // ===== Outline border for full document 
        $sheet->getStyle('A1:P27')->applyFromArray($outlineStyle);

        // ===== Double bottom border on row 3 =====
        $sheet->getStyle('A3:P3')->applyFromArray(['borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE]]]);

        for ($row = 12; $row <= 26; $row++) {
            $sheet->getStyle('A' . $row)->applyFromArray([
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                    'right'  => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
        }

        for ($row = 12; $row <= 26; $row++) {
            $sheet->getStyle('P' . $row)->applyFromArray([
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left'   => ['borderStyle' => Border::BORDER_THIN],
                    'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
                ],
            ]);
        }

        // Border TENGAH B12–O26
        $sheet->getStyle('B12:O26')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set judul file dan header untuk download
        $filename = 'Export PO Booking.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPoManual($noModel)
    {
        $data = $this->openPoModel->getPoManualByNoModel($noModel);
        // dd($data);
        function applyBorders($style, $borders)
        {
            $style['borders'] = $borders;
            return $style;
        }
        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Base styles
        $baseBold = [
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $baseNormal = [
            'font' => ['size' => 11, 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $centerTop = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM],
        ];
        $centerMiddle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $centerMiddleWrap = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
        $leftMiddle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $justify = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        // ===== Drawing logo =====
        $drawing = new Drawing();
        $drawing->setName('Logo')
            ->setDescription('PT. KAHATEX Logo')
            ->setPath(FCPATH . 'assets/img/logo-kahatex.png')
            ->setWorksheet($sheet)
            ->setCoordinates('B1')
            ->setOffsetX(20)
            ->setOffsetY(10)
            ->setHeight(1.25 * 37.7952755906)
            ->setWidth(1.25 * 37.7952755906);

        // Define outline style for full document
        $outlineStyle = ['borders' => ['outline' => ['borderStyle' => Border::BORDER_DOUBLE]]];
        // $lineStyle = ['AllBorder' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]];

        // ===== Header blocks =====
        $headers = [
            [
                'range'   => 'A1:B3',
                'value'   => 'PT. KAHATEX',
                'style'   => array_merge($baseBold, $centerTop),
                'borders' => ['left' => ['borderStyle' => Border::BORDER_DOUBLE], 'right' => ['borderStyle' => Border::BORDER_DOUBLE], 'top' => ['borderStyle' => Border::BORDER_DOUBLE], 'bottom' => ['borderStyle' => Border::BORDER_DOUBLE]],
            ],
            [
                'range'   => 'C1:P1',
                'value'   => 'FORMULIR',
                'style'   => array_merge(
                    ['font' => ['bold' => true, 'size' => 16, 'name' => 'Arial']],
                    $centerMiddle,
                    ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '99FFFF']]]
                ),
                'borders' => ['left' => ['borderStyle' => Border::BORDER_DOUBLE], 'right' => ['borderStyle' => Border::BORDER_DOUBLE], 'top' => ['borderStyle' => Border::BORDER_DOUBLE], 'bottom' => ['borderStyle' => Border::BORDER_THIN]],
            ],
        ];

        foreach ($headers as $h) {
            $sheet->mergeCells($h['range']);
            $sheet->setCellValue(explode(':', $h['range'])[0], $h['value']);
            $sheet->getStyle($h['range'])->applyFromArray(applyBorders($h['style'], $h['borders']));
        }

        // ===== Column widths =====n
        $colWidths = ['A' => 5, 'B' => 17, 'C' => 14, 'D' => 7, 'E' => 7, 'F' => 7, 'G' => 7, 'H' => 7, 'I' => 5, 'J' => 17, 'K' => 14, 'L' => 7, 'M' => 7, 'N' => 7, 'O' => 7, 'P' => 7];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ===== Row heights (1-26) =====
        $rowHeightPts = 0.7 / 0.0352778;
        for ($row = 1; $row <= 26; $row++) {
            $sheet->getRowDimension((string) $row)->setRowHeight($rowHeightPts);
        }

        $fields = [
            ['range' => 'C2:P2', 'value' => 'DEPARTEMEN KAOS KAKI',  'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'C3:P3', 'value' => 'BAGIAN PENCELUPAN',    'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'A4:B4', 'value' => 'No. Dokumen',          'style' => array_merge($baseNormal, $leftMiddle)],
            ['range' => 'C4:I4', 'value' => 'FOR - KK - 013',       'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'J4:K4', 'value' => 'Halaman',              'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'L4:P4', 'value' => '1/1',                  'style' => array_merge($baseBold, $centerMiddle)],
            ['range' => 'A5:B5', 'value' => 'Tanggal Efektif',      'style' => array_merge($baseNormal, $leftMiddle)],
            ['range' => 'C5:I5', 'value' => '01 Desember 2016',     'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'J5:K5', 'value' => 'Revisi',               'style' => array_merge($baseBold, $leftMiddle)],
            ['range' => 'L5:P5', 'value' => '00',                   'style' => array_merge($baseBold, $centerMiddle)],
            // Tambahan kolom baris 6 (tanpa border)
            ['range' => 'A6:E6', 'value' => 'PEMESANAN :', 'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],
            ['range' => 'F6:K6', 'value' => 'TANGGAL :',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],
            ['range' => 'L6:P6', 'value' => 'NO. :',       'style' => array_merge($baseBold, $leftMiddle), 'borders' => []],

            // Tambahan kolom baris 7
            ['range' => 'A7:B7', 'value' => 'NAMA',                 'style' => array_merge($baseBold, $centerMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'C7:E7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F7:H7', 'value' => 'JUMLAH PESANAN',       'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I7:J7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K7:K7', 'value' => 'JENIS',                'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L7:M7', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N7:P7', 'value' => 'TGL. PENYERAHAN',      'style' => array_merge($baseBold, $leftMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_THIN],
                'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
            ]],
            // Tambahan kolom baris 8
            ['range' => 'A8:B9', 'value' => 'TEMPAT PENYERAHAN',    'style' => array_merge($baseBold, $justify), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'C8:E9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F8:H9', 'value' => 'JUMLAH PIECE',         'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I8:J9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K8:K9', 'value' => 'CODE',                 'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L8:M9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N8:P9', 'value' => '',                     'style' => array_merge($baseBold, $leftMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_THIN],
                'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
            ]],

            // Header Isi Tabel
            ['range' => 'A10:A11', 'value' => 'NO.',                     'style' => array_merge($baseBold, $centerMiddle), 'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                'right'  => ['borderStyle' => Border::BORDER_THIN],
            ]],
            ['range' => 'B10:B11', 'value' => 'WARNA',                   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'C10:C11', 'value' => 'JUMLAH PEMESANAN',        'style' => array_merge($baseBold, $centerMiddleWrap), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'D10:H10', 'value' => 'JUMLAH YANG DISERAHKAN',   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'D11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'E11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'F11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'G11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'H11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'I10:I11', 'value' => 'NO.',                   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'J10:J11', 'value' => 'WARNA',        'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'K10:K11', 'value' => 'JUMLAH PESANAN',   'style' => array_merge($baseBold, $centerMiddleWrap), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L10:P10', 'value' => 'JUMLAH YANG DISERAHKAN',   'style' => array_merge($baseBold, $centerMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'L11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'M11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'N11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'O11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
            ['range' => 'P11', 'value' => '',   'style' => array_merge($baseBold, $leftMiddle), 'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]]],
        ];

        // loop apply fields with conditional borders with conditional borders
        foreach ($fields as $f) {
            $sheet->mergeCells($f['range']);
            $sheet->setCellValue(explode(':', $f['range'])[0], $f['value']);
            $borders = $f['borders'] ?? ['outline' => ['borderStyle' => Border::BORDER_THIN]];
            $sheet->getStyle($f['range'])->applyFromArray(applyBorders($f['style'], $borders));
        }

        // Terapkan gaya
        // $sheet->getStyle('A12:P26')->applyFromArray($lineStyle);

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $tglArr = explode('-', date('Y-m-d', strtotime($data[0]['created_at'])));
        $tgl = $tglArr[2] . '-' . $bulan[$tglArr[1]] . '-' . $tglArr[0];
        $sheet->setCellValue('F6', 'TANGGAL : ' . $tgl);
        $sheet->setCellValue('L7', $data[0]['jenis']);
        $sheet->getStyle('L7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if (session()->get('role') === 'gbn') {
            $sheet->setCellValue('C8', 'Gudang Benang');
            $sheet->getStyle('C8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('C8', '');
        }

        $no = 1;
        $row = 16;

        $sheet->setCellValue('B12', $data[0]['jenis'] . ' ' . $data[0]['spesifikasi_benang'] . ' ' . $data[0]['keterangan']);
        $sheet->setCellValue('B13', 'Order :');
        $sheet->setCellValue('B14', 'Model :');

        // $sheet->setCellValue('C13', 'Ini adalah Order');
        $sheet->setCellValue('C14', $data[0]['no_model']);

        foreach ($data as $po) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('B' . $row, $po['color']);
            $sheet->setCellValue('C' . $row, $po['kode_warna']);
            $sheet->setCellValue('K' . $row, $po['kg_po'] . ' kg');
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $sheet->setCellValue('A27', 'Yang Bertanggung Jawab / Tanda tangan : (.......................................)');
        $sheet->mergeCells('A27:P27');

        // Terapkan gaya
        $sheet->getStyle('A27:P27')->applyFromArray([
            'font' => $baseBold['font'],
            'alignment' => $leftMiddle['alignment'],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'], // Warna garis, opsional
                ],
            ],
        ]);

        // ===== Outline border for full document 
        $sheet->getStyle('A1:P27')->applyFromArray($outlineStyle);

        // ===== Double bottom border on row 3 =====
        $sheet->getStyle('A3:P3')->applyFromArray(['borders' => ['bottom' => ['borderStyle' => Border::BORDER_DOUBLE]]]);

        for ($row = 12; $row <= 26; $row++) {
            $sheet->getStyle('A' . $row)->applyFromArray([
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left'   => ['borderStyle' => Border::BORDER_DOUBLE],
                    'right'  => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
        }

        for ($row = 12; $row <= 26; $row++) {
            $sheet->getStyle('P' . $row)->applyFromArray([
                'borders' => [
                    'top'    => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    'left'   => ['borderStyle' => Border::BORDER_THIN],
                    'right'  => ['borderStyle' => Border::BORDER_DOUBLE],
                ],
            ]);
        }

        // Border TENGAH B12–O26
        $sheet->getStyle('B12:O26')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set judul file dan header untuk download
        $filename = 'Export PO Manual ' . $noModel . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportTagihanBenang()
    {
        $noModel       = $this->request->getGet('no_model');
        $kodeWarna     = $this->request->getGet('kode_warna');
        $deliveryAwal  = $this->request->getGet('delivery_awal');
        $deliveryAkhir = $this->request->getGet('delivery_akhir');
        $tglAwal       = $this->request->getGet('tanggal_awal');
        $tglAkhir      = $this->request->getGet('tanggal_akhir');

        $data = $this->scheduleCelupModel->getFilterSchTagihanBenang($noModel, $kodeWarna, $deliveryAwal, $deliveryAkhir, $tglAwal, $tglAkhir);
        // dd($data);
        foreach ($data as &$row) {
            $stockAwal    = (float) $row['stock_awal'];
            $datangSolid  = (float) $row['qty_datang_solid'];
            $gantiRetur   = (float) $row['qty_ganti_retur_solid']; // =0 jika null
            $qtyPo        = (float) $row['qty_po'];
            $poPlus       = (float) ($row['po_plus'] ?? 0);
            $returBelang  = (float) ($row['retur_belang'] ?? 0);

            if ($gantiRetur > 0) {
                $tagihanDatang = ($stockAwal + $datangSolid + $gantiRetur) - $qtyPo - $poPlus - $returBelang;
            } else {
                $tagihanDatang = ($stockAwal + $datangSolid) - $qtyPo - $poPlus;
            }
            // tambahkan ke array
            $row['tagihan_datang'] = $tagihanDatang;
        }
        unset($row);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'REPORT DATA TAGIHAN BENANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');
        $sheet->mergeCells('B3:B4');
        $sheet->mergeCells('C3:C4');
        $sheet->mergeCells('D3:D4');
        $sheet->mergeCells('E3:E4');
        $sheet->mergeCells('F3:F4');
        $sheet->mergeCells('G3:G4');
        $sheet->mergeCells('H3:H4');
        $sheet->mergeCells('I3:I4');
        $sheet->mergeCells('J3:J4');
        $sheet->mergeCells('K3:K4');
        $sheet->mergeCells('L3:L4');
        $sheet->mergeCells('M3:M4');
        $sheet->mergeCells('N3:N4');
        $sheet->mergeCells('O3:O4');
        $sheet->mergeCells('P3:P4');
        $sheet->mergeCells('Q3:Q4');
        $sheet->mergeCells('R3:R4');

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'NO MODEL');
        $sheet->setCellValue('C3', 'ITEM TYPE');
        $sheet->setCellValue('D3', 'KODE WARNA');
        $sheet->setCellValue('E3', 'WARNA');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'START MC');
        $sheet->setCellValue('H3', 'DELIVERY AWAL');
        $sheet->setCellValue('I3', 'DELIVERY AKHIR');
        $sheet->setCellValue('J3', 'QTY PO');
        $sheet->setCellValue('K3', 'QTY PO(+)');
        $sheet->setCellValue('L3', 'STOCK AWAL');
        $sheet->setCellValue('M3', 'RETUR STOCK');
        $sheet->setCellValue('N3', 'TOTAL QTY SCHEDULE');
        $sheet->setCellValue('O3', 'TOTAL QTY DATANG SOLID');
        $sheet->setCellValue('P3', 'QTY GANTI RETUR SOLID');
        $sheet->setCellValue('Q3', 'QTY RETUR BELANG');
        $sheet->setCellValue('R3', 'TAGIHAN DATANG SOLID');

        // Format semua header
        $sheet->getStyle('A3:R4')->getFont()->setBold(true);
        $sheet->getStyle('A3:R4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:R4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:R4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['no_model']);
            $sheet->setCellValue('C' . $row, $item['item_type']);
            $sheet->setCellValue('D' . $row, $item['kode_warna']);
            $sheet->setCellValue('E' . $row, $item['warna']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['start_mc']);
            $sheet->setCellValue('H' . $row, $item['delivery_awal'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_akhir']) ?? '';
            $sheet->setCellValue('J' . $row, isset($item['qty_po']) ? number_format($item['qty_po'], 2, '.', '') : 0);
            $sheet->setCellValue('K' . $row, isset($item['po_plus']) ? number_format($item['po_plus'], 2, '.', '') : 0);
            $sheet->setCellValue('L' . $row, isset($item['stock_awal']) ? number_format($item['stock_awal'], 2, '.', '') : 0);
            $sheet->setCellValue('M' . $row, isset($item['retur_stock']) ? number_format($item['retur_stock'], 2, '.', '') : 0);
            $sheet->setCellValue('N' . $row, isset($item['qty_sch']) ? number_format($item['qty_sch'], 2, '.', '') : 0);
            $sheet->setCellValue('O' . $row, isset($item['qty_datang_solid']) ? number_format($item['qty_datang_solid'], 2, '.', '') : 0);
            $sheet->setCellValue('P' . $row, isset($item['qty_ganti_retur_solid']) ? number_format($item['qty_ganti_retur_solid'], 2, '.', '') : 0);
            $sheet->setCellValue('Q' . $row, isset($item['retur_belang']) ? number_format($item['retur_belang'], 2, '.', '') : 0);
            $sheet->setCellValue('R' . $row, isset($item['tagihan_datang']) ? number_format($item['tagihan_datang'], 2, '.', '') : 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Y{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:R{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Data Tagihan Benang' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportHistoryPindahOrder()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        // 1) Ambil data
        $dataPindah = $this->historyStock->getHistoryPindahOrder($noModel, $kodeWarna);

        // 2) Siapkan HTTP client
        $client = \Config\Services::curlrequest([
            'baseURI' => 'http://172.23.44.14/CapacityApps/public/api/',
            'timeout' => 5
        ]);

        // 3) Loop dan merge API result
        foreach ($dataPindah as &$row) {
            try {
                $res = $client->get('getDeliveryAwalAkhir', [
                    'query' => ['model' => $row['no_model_new']]
                ]);
                $body = json_decode($res->getBody(), true);
                $row['delivery_awal']  = $body['delivery_awal']  ?? '-';
                $row['delivery_akhir'] = $body['delivery_akhir'] ?? '-';
            } catch (\Exception $e) {
                $row['delivery_awal']  = '-';
                $row['delivery_akhir'] = '-';
            }
        }
        unset($row);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT HISTORY PINDAH ORDER');

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

        $dataFilter = '';

        if (!empty($noModel) && !empty($kodeWarna)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' KODE WARNA ' . $kodeWarna;
        } elseif (!empty($noModel)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel;
        } elseif (!empty($kodeWarna)) {
            $dataFilter = ' KODE WARNA ' . $kodeWarna;
        }

        // Judul
        $sheet->setCellValue('A1', 'REPORT HISTORY PINDAH ORDER' . $dataFilter);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;

        $headers = [
            'A' => 'NO',
            'B' => 'NO MODEL',
            'C' => 'DELIVERY AWAL',
            'D' => 'DELIVERY AKHIR',
            'E' => 'ITEM TYPE',
            'F' => 'KODE WARNA',
            'G' => 'WARNA',
            'H' => 'QTY',
            'I' => 'CONES',
            'J' => 'LOT',
            'K' => 'CLUSTER',
            'L' => 'KETERANGAN'
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . $row_header, $title);
            $sheet->getStyle($col . $row_header)->applyFromArray($styleHeader);
        }


        // Isi data
        $row = 4;
        $no = 1;

        foreach ($dataPindah as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['no_model_old']);
            $sheet->setCellValue('C' . $row, $data['delivery_awal']);
            $sheet->setCellValue('D' . $row, $data['delivery_akhir']);
            $sheet->setCellValue('E' . $row, $data['item_type']);
            $sheet->setCellValue('F' . $row, $data['kode_warna']);
            $sheet->setCellValue('G' . $row, $data['warna']);
            $sheet->setCellValue('H' . $row, $data['kgs']);
            $sheet->setCellValue('I' . $row, $data['cns']);
            $sheet->setCellValue('J' . $row, $data['lot']);
            $sheet->setCellValue('K' . $row, $data['cluster_old']);
            $sheet->setCellValue('L' . $row, $data['created_at'] . ' ' . $data['keterangan'] . ' KE ' . $data['no_model_new'] . ' KODE ' . $data['kode_warna']);

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'REPORT HISTORY PINDAH ORDER' . $dataFilter . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPoTambahan()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';
        $tglPo = $this->request->getGet('tgl_po') ?? date('Y-m-d', strtotime('-1 day'));

        // 1) Ambil data
        $dataPoPlus = $this->poPlusModel->getDataPoPlus($tglPo, $noModel, $kodeWarna);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT PO TAMBAHAN');

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

        $dataFilter = '';

        if (!empty($noModel) && !empty($kodeWarna) && !empty($tglPo)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' KODE WARNA ' . $kodeWarna . ' TANGGAL PO ' . $tglPo;
        } elseif (!empty($noModel) && !empty($kodeWarna)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' KODE WARNA ' . $kodeWarna;
        } elseif (!empty($noModel) && !empty($tglPo)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' TANGGAL PO ' . $tglPo;
        } elseif (!empty($kodeWarna) && !empty($tglPo)) {
            $dataFilter = ' KODE WARNA ' . $kodeWarna . ' TANGGAL PO ' . $tglPo;
        } elseif (!empty($noModel)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel;
        } elseif (!empty($kodeWarna)) {
            $dataFilter = ' KODE WARNA ' . $kodeWarna;
        } elseif (!empty($tglPo)) {
            $dataFilter = ' TANGGAL PO ' . $tglPo;
        }

        // Judul
        $sheet->setCellValue('A1', 'REPORT PO TAMBAHAN' . $dataFilter);
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;

        $headers = [
            'A' => 'NO',
            'B' => 'TANGGAL PO(+)',
            'C' => 'AREA',
            'D' => 'NO MODEL',
            'E' => 'ITEM TYPE',
            'F' => 'KODE WARNA',
            'G' => 'WARNA',
            'H' => 'KG PO(+)',
            'I' => 'CONES PO(+)',
            'J' => 'KETERANGAN',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . $row_header, $title);
            $sheet->getStyle($col . $row_header)->applyFromArray($styleHeader);
        }


        // Isi data
        $row = 4;
        $no = 1;

        foreach ($dataPoPlus as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['tgl_poplus']);
            $sheet->setCellValue('C' . $row, $data['area']);
            $sheet->setCellValue('D' . $row, $data['no_model']);
            $sheet->setCellValue('E' . $row, $data['item_type']);
            $sheet->setCellValue('F' . $row, $data['kode_warna']);
            $sheet->setCellValue('G' . $row, $data['color']);
            $sheet->setCellValue('H' . $row, number_format($data['kg_poplus'], 2));
            $sheet->setCellValue('I' . $row, $data['cns_poplus']);
            $sheet->setCellValue('J' . $row, $data['keterangan']);

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'REPORT PO TAMBAHAN' . $dataFilter . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function generateOpenPOExcel($no_model)
    {
        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');
        $season = $this->request->getGet('season');
        $poPlus = $this->request->getGet('po_plus');
        $materialType = $this->request->getGet('material_type');
        $delivery = $this->request->getGet('delivery');

        // Ambil data
        $result = ($poPlus == 'TIDAK')
            ? $this->openPoModel->getDataPo($no_model, $jenis, $jenis2)
            : $this->openPoModel->getDataPoPlus($no_model, $jenis, $jenis2);

        $groups = [
            'RECYCLE' => [],  // semua yang mengandung RECY/RECYCLE/RECYCLED
            'OTHER'   => [],  // sisanya
        ];
        $patternRecycle = '/RECY(CL(E|ED))?/i';

        foreach ($result as $row) {
            if (preg_match($patternRecycle, $row['item_type'])) {
                $groups['RECYCLE'][] = $row;
            } else {
                $groups['OTHER'][] = $row;
            }
        }

        $noModel =  $result[0]['no_model'] ?? '';

        $unit = $this->masterOrderModel->getUnit($no_model);
        $rawUnit = $unit['unit'];
        $rawUnit = strtoupper(trim($rawUnit));

        $pemesanan = 'KAOS KAKI';
        if ($rawUnit === 'MAJALAYA') {
            $pemesanan .= ' / ' . $rawUnit;
        }

        // Ambil buyer dari API
        $buyerApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getDataBuyer?no_model=' . urlencode($noModel);
        $buyerName = json_decode(file_get_contents($buyerApiUrl), true);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        if (!empty($delivery)) {
            // Cek jika delivery sudah berupa tanggal, jika tidak, tetap tampilkan apa adanya
            $timestamp = strtotime($delivery);
            if ($timestamp !== false) {
                $bulanIndo = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];
                $day = date('j', $timestamp);
                $month = $bulanIndo[(int)date('n', $timestamp)];
                $year = date('Y', $timestamp);
                $delivery = "{$day} {$month} {$year}";
            }
        }
        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $firstSheet = true;
        foreach ($groups as $title => $rows) {
            if ($firstSheet) {
                $sheet = $spreadsheet->getActiveSheet();
                $firstSheet = false;
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            $sheet->setTitle($title);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

            // 1. Atur ukuran kertas jadi A4
            $sheet->getPageSetup()
                ->setPaperSize(PageSetup::PAPERSIZE_A4);

            // 2. Atur orientasi jadi landscape
            $sheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
            $sheet->getPageSetup()
                ->setFitToWidth(1)
                ->setFitToHeight(0)    // 0 artinya auto height
                ->setFitToPage(true); // aktifkan fitting

            // 4. (Opsional) Atur margin supaya tidak terlalu sempit
            $sheet->getPageMargins()->setTop(0.4)
                ->setBottom(0.4)
                ->setLeft(0.4)
                ->setRight(0.2);
            //Outline Border
            // 1. Top double border dari A1 ke Q1
            $sheet->getStyle('A1:Q1')->applyFromArray([
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // 2. Right double border dari Q1 ke Q50
            $sheet->getStyle('Q1:Q50')->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // 3. Bottom double border dari A50 ke Q50
            $sheet->getStyle('A50:Q50')->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // 4. Left double border dari A1 ke A50
            $sheet->getStyle('A1:A50')->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            //Border Thin
            $sheet->getStyle('C1:C3')->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getStyle('C4')->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getStyle('N4:O4')->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getStyle('N5:O5')->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Double border baris 4 dan 5
            $sheet->getStyle('A4:Q4')->applyFromArray([
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);
            $sheet->getStyle('A5:Q5')->applyFromArray([
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $thinInside = [
                'borders' => [
                    // border antar kolom (vertical lines) di dalam range
                    'vertical' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    // border antar baris (horizontal lines) di dalam range
                    'horizontal' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            $thinInside = [
                'borders' => [
                    'vertical' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'horizontal' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];
            $sheet->getStyle('A11:Q28')->applyFromArray($thinInside);

            // 2) Border tipis atas untuk baris header tabel (A11:Q11)
            $sheet->getStyle('A11:Q11')->applyFromArray([
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // 3) Border tipis bawah untuk baris total (A28:Q28)
            $sheet->getStyle('A28:Q28')->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            // Aktifkan wrap text di A11:Q28
            $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

            // Atur lebar kolom dalam satuan pt
            $columnWidths = [
                'A' => 20,
                'B' => 120,
                'C' => 40,
                'D' => 50,
                'E' => 100,
                'F' => 100,
                'G' => 100,
                'H' => 100,
                'I' => 100,
                'J' => 50,
                'K' => 25,
                'L' => 25,
                'M' => 40,
                'N' => 40,
                'O' => 100,
                'P' => 100,
                'Q' => 100,
            ];

            $rowHeightsPt = [
                11 => 50, // misal 25 pt untuk header tabel
                12 => 50, // misal 20 pt untuk baris pertama data
                13 => 36,
                14 => 36,
                15 => 36,
                16 => 36,
                17 => 36,
                18 => 36,
                19 => 36,
                20 => 36,
                21 => 36,
                22 => 36,
                23 => 36,
                24 => 36,
                25 => 36,
                26 => 36,
                27 => 36,
                28 => 36,
            ];

            //Atur Tinggi Baris dan Lebar Kolom
            foreach ($rowHeightsPt as $row => $heightPt) {
                $sheet->getRowDimension($row)
                    ->setRowHeight($heightPt);
            }

            foreach ($columnWidths as $col => $widthPt) {
                $charWidth = round($widthPt / 5.25, 2);
                $sheet->getColumnDimension($col)
                    ->setWidth($charWidth)
                    ->setAutoSize(false);
            }

            // Header Form
            $sheet->mergeCells('A1:B2');
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getRowDimension(1)->setRowHeight(30);

            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Perusahaan');
            $drawing->setPath('assets/img/logo-kahatex.png');
            $drawing->setCoordinates('B1');
            $drawing->setHeight(50);
            $drawing->setOffsetX(55);
            $drawing->setOffsetY(10);
            $drawing->setWorksheet($sheet);
            $sheet->mergeCells('A3:C3');
            $sheet->setCellValue('A3', 'PT. KAHATEX');
            $sheet->getStyle('A3')->getFont()->setSize(11);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('D1', 'FORMULIR');
            $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
            $sheet->mergeCells('D1:Q1');
            $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('D2:Q2');
            $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
            $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('D3:Q3');
            $sheet->setCellValue('D3', 'FORMULIR PO');
            $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A4:C4');
            $sheet->setCellValue('A4', 'No. Dokumen');
            $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

            $sheet->mergeCells('N4:O4');
            $sheet->setCellValue('N4', 'Tanggal Revisi');
            $sheet->mergeCells('P4:Q4');
            $sheet->setCellValue('P4', '17 Maret 2025');
            $sheet->getStyle('P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('N5:O5');
            $sheet->setCellValue('N5', 'Klasifikasi');
            $sheet->mergeCells('P5:Q5');
            $sheet->setCellValue('P5', 'Internal');
            $sheet->getStyle('P5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->mergeCells('A5:M5');
            $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

            $sheet->mergeCells('A6:A7');
            $sheet->setCellValue('A6', 'PO');
            $sheet->getStyle('D1')->getFont()->setSize(18);
            $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('C6:E7');
            if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
                $sheet->setCellValue('C6', ': ' . $no_model);
            } elseif (!empty($result) && isset($result[0]['po_plus'])) {
                $sheet->setCellValue('C6', ': ' . '(+) ' . $no_model);
            } else {
                $sheet->setCellValue('C6', ': ' . $no_model);
            }
            // $sheet->setCellValue('C6', ': ' . $no_model);
            $sheet->getStyle('C6')->getFont()->setSize(24);
            $sheet->getStyle('C6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('A8', 'Pemesan');
            $sheet->setCellValue('C8', ': ' . $pemesanan);

            $sheet->setCellValue('A9', 'Tgl');
            $sheet->setCellValue('C9', ': ' . (isset($result[0]['tgl_po']) ? date('d/m/Y', strtotime($result[0]['tgl_po'])) : ''));

            $sheet->setCellValue('F7', $season);
            $sheet->mergeCells('F7:F9');
            $sheet->getStyle('F7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            $sheet->setCellValue('G7', $materialType);
            $sheet->mergeCells('G7:G9');
            $sheet->getStyle('G7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getStyle('G7')->getFont()->setUnderline(true);

            // Header utama dan sub-header
            $sheet->setCellValue('A11', 'No');
            $sheet->mergeCells('A11:A12');
            $sheet->getStyle('A11:A12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('B11', 'Benang');
            $sheet->mergeCells('B11:C11');
            $sheet->getStyle('B11:C11')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('B12', 'Jenis');
            $sheet->getStyle('B12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('C12', 'Kode');
            $sheet->getStyle('C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('D11', 'Bentuk Celup');
            $sheet->mergeCells('D11:D12');
            $sheet->getStyle('D11:D12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);

            $sheet->setCellValue('E11', 'Warna');
            $sheet->mergeCells('E11:E12');
            $sheet->getStyle('E11:E12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('F11', 'Kode Warna');
            $sheet->mergeCells('F11:F12');
            $sheet->getStyle('F11:F12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('G11', 'Buyer');
            $sheet->mergeCells('G11:G12');
            $sheet->getStyle('G11:G12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('H11', 'Nomor Order');
            $sheet->mergeCells('H11:H12');
            $sheet->getStyle('H11:H12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('I11', 'Delivery');
            $sheet->mergeCells('I11:I12');
            $sheet->getStyle('I11:I12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('J11', 'Qty Pesanan');
            $sheet->mergeCells('J11:J11');
            $sheet->getStyle('J11')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->setCellValue('J12', 'Kg');
            $sheet->getStyle('J12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('K11', 'Permintaan Cones');
            $sheet->mergeCells('K11:N11');
            $sheet->getStyle('K11:N11')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('K12', 'Kg');
            $sheet->getStyle('K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('L12', 'Yard');
            $sheet->getStyle('L12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('M12', 'Total Cones');
            $sheet->getStyle('M12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);
            $sheet->setCellValue('N12', 'Jenis Cones');
            $sheet->getStyle('N12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setWrapText(true);

            $sheet->setCellValue('O11', 'Untuk Produksi');
            $sheet->mergeCells('O11:O12');
            $sheet->getStyle('O11:O12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('P11', 'Contoh Warna');
            $sheet->mergeCells('P11:P12');
            $sheet->getStyle('P11:P12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue('Q11', 'Keterangan Celup');
            $sheet->mergeCells('Q11:Q12');
            $sheet->getStyle('Q11:Q12')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Isi tabel
            $rowNum = 13;
            $no = 1;
            $totalKg = $totalCones = $totalYard = $totalKgPerCones = 0;
            $firstRow = true;
            $itemTypes = [];

            foreach ($rows as $row) {
                // dd($groups);
                $spesifikasiBenang = trim($row['spesifikasi_benang'] ?? '');
                if ($spesifikasiBenang === '- -') {
                    $spesifikasiBenang = '';
                }

                if ($firstRow) {
                    $buyerDisplay   = $row['buyer'] . ' (' . ($buyerName['kd_buyer_order'] ?? '') . ')';
                    $noOrderDisplay = $row['no_order'];
                    $deliveryDisplay = $delivery;
                    $firstRow = false; // reset flag setelah baris pertama
                } else {
                    $buyerDisplay   = '';
                    $noOrderDisplay = '';
                    $deliveryDisplay = '';
                }

                // Buat key berdasarkan item_type + spesifikasi untuk memisahkan grup
                // $groupKey = $row['item_type'] . '|' . $spesifikasiBenang;

                // if (!in_array($groupKey, $itemTypes)) {
                //     // Kemunculan pertama untuk grup ini
                //     $tampilItemType = $row['item_type'] . ($spesifikasiBenang ? " {$spesifikasiBenang}" : '');
                //     $tampilUkuran   = $row['ukuran'];
                //     $itemTypes[]    = $groupKey;
                // } else {
                //     // Baris selanjutnya dalam grup sama
                //     $tampilItemType = '';
                //     $tampilUkuran   = '';
                // }

                $sheet->fromArray([
                    $no++,
                    $row['item_type'] . ' ' . $spesifikasiBenang,
                    $row['ukuran'],
                    $row['bentuk_celup'],
                    $row['color'],
                    $row['kode_warna'],
                    $buyerDisplay,
                    $noOrderDisplay,
                    $deliveryDisplay,
                    $row['kg_po'],
                    $row['kg_percones'] ?? '',
                    '', // yard belum ada
                    ($row['jumlah_cones'] > 0) ? $row['jumlah_cones'] : '',
                    '', // jenis cones belum ada
                    $row['jenis_produksi'],
                    $row['contoh_warna'],
                    $row['ket_celup']
                ], null, 'A' . $rowNum);

                $totalKg += floatval($row['kg_po']);
                $totalKgPerCones += floatval($row['kg_percones']);
                $totalCones += floatval($row['jumlah_cones']);
                $rowNum++;
            }

            // Total
            $sheet->setCellValue('A28', 'TOTAL');
            $sheet->mergeCells('A28:I28');
            $sheet->getStyle('A28:I28')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('J28', $totalKg);
            $sheet->setCellValue('K28', $totalKgPerCones);
            $sheet->setCellValue('M28', ($totalCones > 0) ? $totalCones : '');

            //Keterangan
            $sheet->setCellValue('F30', $result[0]['keterangan'] ?? '');
            $sheet->mergeCells('F30:J30');
            $sheet->getStyle('F30:J30')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            //Tanda Tangan
            $sheet->setCellValue('E45', 'Pemesan');
            $sheet->getStyle('E45')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H45', 'Mengetahui');
            $sheet->getStyle('H45')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('J45', 'Tanda terima');
            $sheet->mergeCells('J45:L45');
            $sheet->getStyle('J45:L45')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            if ($tujuan == 'CELUP') {
                $sheet->setCellValue('J46', 'Celup Cones');
                $sheet->mergeCells('J46:L46');
                $sheet->getStyle('J46:L46')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } else {
                $sheet->setCellValue('J46', 'Covering');
                $sheet->mergeCells('J46:L46');
                $sheet->getStyle('J46:L46')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }


            $sheet->setCellValue('E49', '(   ' . $result[0]['admin'] . '   )');
            $sheet->getStyle('E49')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H49', '(   ' . $result[0]['penanggung_jawab'] . '   )');
            $sheet->getStyle('H49')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('J49', '(   ' . $penerima . '   )');
            $sheet->mergeCells('J49:L49');
            $sheet->getStyle('J49:L49')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K49')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A11:Q28")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
        // Output Excel
        $filename = 'Open PO_' . $no_model . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }

    public function generateOpenPOCovering($jenis, $tgl_po)
    {
        // Ambil data
        $data = $this->pemesananSpandexKaretModel
            ->getDataForPdf($jenis, $tgl_po);

        // Buat objek Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);
        // -- HEADER LOGO & JUDUL --
        // Sisipkan logo jika diperlukan (path relatif ke public/)
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kahatex');
        $drawing->setPath(FCPATH . 'assets/img/logo-kahatex.png');
        $drawing->setCoordinates('A1');
        $drawing->setHeight(50);
        $drawing->setWorksheet($sheet);

        // Judul Form
        $sheet->mergeCells('B2:G2');
        $sheet->setCellValue('B2', 'FORMULIR');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('B3:G3');
        $sheet->setCellValue('B3', 'DEPARTEMEN COVERING');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('B4:G4');
        $sheet->setCellValue('B4', 'SURAT PENGELUARAN BARANG');
        $sheet->getStyle('B4')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B4')->getAlignment()->setHorizontal('center');

        // -- META INFO (No Dokumen, Halaman, Tgl Efektif, Revisi) --
        $sheet->setCellValue('A6', 'No. Dokumen');
        $sheet->setCellValue('B6', 'FOR-COV-631');
        $sheet->setCellValue('D6', 'Halaman');
        $sheet->setCellValue('E6', '1 dari 1');

        $sheet->setCellValue('A7', 'Tanggal Efektif');
        $sheet->setCellValue('B7', '01 Mei 2017');
        $sheet->setCellValue('D7', 'Revisi');
        $sheet->setCellValue('E7', '00');

        $sheet->setCellValue('D8', 'Tanggal Revisi');

        // -- CUSTOMER & NO/TANGGAL PO --
        $sheet->setCellValue('A10', 'CUSTOMER:');
        $sheet->setCellValue('D10', 'NO:');
        $sheet->setCellValue('E10', $tgl_po);
        $sheet->setCellValue('D11', 'TANGGAL:');
        $sheet->setCellValue('E11', $tgl_po);

        // -- HEADER TABEL --
        $startRow = 13;
        $sheet->setCellValue('A' . $startRow, 'No');
        $sheet->setCellValue('B' . $startRow, 'JENIS BARANG');
        $sheet->setCellValue('C' . $startRow, 'DR/TPM');
        $sheet->setCellValue('D' . $startRow, 'WARNA/CODE');
        $sheet->setCellValue('E' . $startRow, 'KG');
        $sheet->setCellValue('F' . $startRow, 'CONES');
        $sheet->setCellValue('G' . $startRow, 'KETERANGAN');

        // Styling header tabel
        $headerRange = 'A' . $startRow . ':G' . $startRow;
        $sheet->getStyle($headerRange)
            ->getFont()->setBold(true);
        $sheet->getStyle($headerRange)
            ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // -- LOOP DATA --
        $row = $startRow + 1;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['jenis'] . ' (' . $item['no_model'] . ')');
            $sheet->setCellValue('C' . $row, ''); // kolom DR/TPM kosong
            $sheet->setCellValue('D' . $row, $item['color'] . '/' . $item['code']);
            $sheet->setCellValue('E' . $row, number_format($item['total_pesan'], 2, '.', ''));
            $sheet->setCellValue('F' . $row, $item['total_cones']);
            $sheet->setCellValue('G' . $row, $item['keterangan']);

            // border per baris
            $sheet->getStyle("A{$row}:G{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $row++;
        }

        // Jika kurang dari 18 baris, tambahkan row kosong
        $minRows = 18;
        for ($i = $no; $i <= $minRows; $i++) {
            $sheet->getStyle("A{$row}:G{$row}")
                ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $row++;
        }

        // -- TANDA TANGAN --
        $row += 2;
        $sheet->setCellValue("A{$row}", 'YANG BUKA BON');
        $sheet->setCellValue("C{$row}", 'GUDANG ANGKUTAN');
        $sheet->setCellValue("E{$row}", 'PENERIMA');
        $row += 4;
        $sheet->setCellValue("A{$row}", '(       PARYANTI       )');
        $sheet->setCellValue("C{$row}", '(                     )');
        $sheet->setCellValue("E{$row}", '(       HARTANTO       )');

        // Atur lebar kolom agar pas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output ke browser
        $writer = new Xlsx($spreadsheet);
        return service('response')
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="Pengeluaran_Covering_' . $tgl_po . '.xlsx"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody((string) $writer->save('php://output'));
    }

    public function generateOpenPOCoveringExcel($tgl_po)
    {
        // Ambil data PO
        $poCovering = $this->openPoModel->getPoForCelup($tgl_po);
        if (empty($poCovering) || empty($poCovering[0]->no_model)) {
            session()->setFlashdata('error', 'PO Tidak Ditemukan. Open PO Terlebih Dahulu');
            return redirect()->back();
        }

        // Hilangkan kata POCOVERING pada induk_no_model
        foreach ($poCovering as $i => $row) {
            $poCovering[$i]->induk_no_model = preg_replace('/POCOVERING\s*/i', '', $row->induk_no_model);
        }

        $noModel = $poCovering[0]->no_model;

        // 1) Kelompokkan array berdasarkan no_model
        $groups = [];
        foreach ($poCovering as $row) {
            $groups[$row->no_model][] = $row;
        }

        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        ///////////////
        $first = true;
        foreach ($groups as $noModel => $rows) {
            // jika bukan sheet pertama, buat sheet baru
            if ($first) {
                $sheet = $spreadsheet->getActiveSheet();
                // 1. Atur ukuran kertas jadi A4
                $sheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A4);

                // 2. Atur orientasi jadi landscape
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
                $sheet->getPageSetup()
                    ->setFitToWidth(1)
                    ->setFitToHeight(0)    // 0 artinya auto height
                    ->setFitToPage(true); // aktifkan fitting

                // 4. (Opsional) Atur margin supaya tidak terlalu sempit
                $sheet->getPageMargins()->setTop(0.4)
                    ->setBottom(0.4)
                    ->setLeft(0.4)
                    ->setRight(0.2);
                $first = false;
            } else {
                $sheet = $spreadsheet->createSheet();
                // 1. Atur ukuran kertas jadi A4
                $sheet->getPageSetup()
                    ->setPaperSize(PageSetup::PAPERSIZE_A4);

                // 2. Atur orientasi jadi landscape
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
                $sheet->getPageSetup()
                    ->setFitToWidth(1)
                    ->setFitToHeight(0)    // 0 artinya auto height
                    ->setFitToPage(true); // aktifkan fitting

                // 4. (Opsional) Atur margin supaya tidak terlalu sempit
                $sheet->getPageMargins()->setTop(0.4)
                    ->setBottom(0.4)
                    ->setLeft(0.4)
                    ->setRight(0.2);
            }

            // 3) Set judul sheet sesuai no_model
            $title = substr($noModel, 0, 31); // maksimal 31 karakter
            $sheet->setTitle($title);

            // -- Sekarang panggil fungsi/potongan kode untuk mencetak header, styling, dsb --
            // Misalnya:
            $this->applyBordersAndStyles($sheet);
            $this->writeHeaderForm($sheet, $noModel, $rows[0]->created_at);

            // 4) Tuliskan data per‐baris
            // Mulai menulis data dari baris 13
            $rowNum = 13;
            $no = 1;
            $totalKg = 0;
            $totalPermCones = 0;
            $totalYard = 0;
            $totalCones = 0;
            // dd ($rows);
            foreach ($rows as $row) {
                // dd($poCovering);
                // if ($row->jenis === 'NYLON') {
                //     $row->jenis = 'POLYESTER';
                // }
                $sheet->setCellValue("A{$rowNum}", $no++);
                $sheet->setCellValue("B{$rowNum}", $row->jenis);
                $sheet->setCellValue("C{$rowNum}", $row->ukuran);
                $sheet->setCellValue("D{$rowNum}", $row->bentuk_celup);
                $sheet->setCellValue("E{$rowNum}", $row->color);
                $sheet->setCellValue("F{$rowNum}", $row->kode_warna);
                $sheet->setCellValue("G{$rowNum}", $row->buyer);
                $sheet->setCellValue("H{$rowNum}", $row->induk_no_model);
                $sheet->setCellValue("I{$rowNum}", $row->delivery_awal);
                $sheet->setCellValue("J{$rowNum}", $row->kg_po);
                $sheet->setCellValue("K{$rowNum}", $row->kg_percones);
                $sheet->setCellValue("L{$rowNum}", $row->yard ?? '');
                $sheet->setCellValue("M{$rowNum}", $row->jumlah_cones);
                $sheet->setCellValue("N{$rowNum}", '');
                $sheet->setCellValue("O{$rowNum}", $row->jenis_produksi);
                $sheet->setCellValue("P{$rowNum}", $row->contoh_warna);
                $sheet->setCellValue("Q{$rowNum}", $row->ket_celup);

                // Borders untuk kolom A–Q
                foreach (range('A', 'Q') as $col) {
                    $sheet->getStyle("{$col}{$rowNum}")
                        ->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);
                }

                // Border kiri kolom A = double
                $sheet->getStyle("A{$rowNum}")
                    ->getBorders()->getLeft()
                    ->setBorderStyle(Border::BORDER_DOUBLE);

                // Border kiri kolom A = double
                $sheet->getStyle("Q{$rowNum}")
                    ->getBorders()->getRight()
                    ->setBorderStyle(Border::BORDER_DOUBLE);

                $totalKg += $row->kg_po;
                $totalPermCones += $row->kg_percones;
                $totalCones += $row->jumlah_cones;
                $totalYard += $row->yard ?? 0;
                $rowNum++;
            }

            // Baris Total (sama layout seperti PDF)
            // Gabungkan A–I untuk label "Total"
            $sheet->setCellValue("A38", 'Total');
            $sheet->mergeCells("A38:I38");
            $sheet->getStyle("A38")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // J: Total Kg PO
            $sheet->setCellValue("J38", number_format($totalKg, 2));
            // K–L kosong
            $sheet->setCellValue("K38", '');
            $sheet->setCellValue("L38", '');
            // M: Total Cones
            $sheet->setCellValue("M38", $totalCones ?: '');
            // N–Q kosong
            foreach (range('N', 'Q') as $col) {
                $sheet->setCellValue("{$col}38", '');
            }
            // Tambahkan border untuk seluruh baris total A38:Q38
            $sheet->getStyle('A38:Q38')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
            // Kolom A (border kiri double)
            $sheet->getStyle('A38')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

            // Kolom Q (border kanan double)
            $sheet->getStyle('Q38')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

            $pemesanCov = 'IIS RAHAYU';
            // (Opsional) footer keterangan dan tanda tangan...
            $this->writeFooter($sheet, $rows[0], $pemesanCov, $rows[0]->penanggung_jawab, $rows[0]->penerima);
        }
        ///////

        // Header respons
        $filename = 'OpenPOCovering_' . $tgl_po . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function applyBordersAndStyles($sheet)
    {
        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle('Q1:Q50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A50:Q50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N4:O4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N5:O5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:Q4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:Q37')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:Q11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:Q28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

        // Atur lebar kolom dalam satuan pt
        $columnWidths = [
            'A' => 20,
            'B' => 120,
            'C' => 40,
            'D' => 50,
            'E' => 100,
            'F' => 100,
            'G' => 100,
            'H' => 100,
            'I' => 100,
            'J' => 50,
            'K' => 25,
            'L' => 25,
            'M' => 40,
            'N' => 40,
            'O' => 100,
            'P' => 100,
            'Q' => 100,
        ];

        $rowHeightsPt = [
            11 => 50, // misal 25 pt untuk header tabel
            12 => 50, // misal 20 pt untuk baris pertama data
        ];

        //Atur Tinggi Baris dan Lebar Kolom
        foreach ($rowHeightsPt as $row => $heightPt) {
            $sheet->getRowDimension($row)
                ->setRowHeight($heightPt);
        }

        foreach ($columnWidths as $col => $widthPt) {
            $charWidth = round($widthPt / 5.25, 2);
            $sheet->getColumnDimension($col)
                ->setWidth($charWidth)
                ->setAutoSize(false);
        }
    }

    private function writeHeaderForm($sheet, $noModel, $createdAt)
    {
        // Header Form
        // Logo dan judul perusahaan di bawah logo
        $sheet->mergeCells('A1:C3');
        $sheet->setCellValue('A1', 'PT. KAHATEX');
        $sheet->getStyle('A1')->getFont()->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $drawing = new Drawing();
        $drawing->setName('Logo')
            ->setDescription('PT. KAHATEX Logo')
            ->setPath(FCPATH . 'assets/img/logo-kahatex.png')
            ->setWorksheet($sheet)
            ->setCoordinates('B1')
            ->setOffsetX(150)
            ->setOffsetY(7)
            ->setHeight(40)
            ->setWidth(40);

        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D1:Q1');
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D2:Q2');
        $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D3:Q3');
        $sheet->setCellValue('D3', 'FORMULIR PO');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

        $sheet->mergeCells('N4:O4');
        $sheet->setCellValue('N4', 'Tanggal Revisi');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '17 Maret 2025');
        $sheet->getStyle('P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('N5:O5');
        $sheet->setCellValue('N5', 'Klasifikasi');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', 'Internal');
        $sheet->getStyle('P5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:M5');
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'PO');
        $sheet->getStyle('D1')->getFont()->setSize(18);

        $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('D6', ': ' . $noModel ?? '-');
        $sheet->mergeCells('D6:F7');
        $sheet->getStyle('D6')->getFont()->setSize(24);
        $sheet->getStyle('D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A8', 'Pemesan');
        $sheet->setCellValue('D8', ': COVERING');

        $sheet->setCellValue('A9', 'Tgl');
        $sheet->setCellValue('D9', ': ' . ($createdAt ? date('d/m/Y', strtotime($createdAt)) : ''));

        $sheet->setCellValue('G7', '');
        $sheet->mergeCells('G7:G9');
        $sheet->getStyle('G7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('H7', '');
        $sheet->mergeCells('H7:H9');
        $sheet->getStyle('H7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('H7')->getFont()->setUnderline(true);
        $sheet->getStyle('A6:H9')->getFont()->setBold(true);

        // Header utama dan sub-header
        $sheet->setCellValue('A11', 'No');
        $sheet->mergeCells('A11:A12');
        $sheet->getStyle('A11:A12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('B11', 'Benang');
        $sheet->mergeCells('B11:C11');
        $sheet->getStyle('B11:C11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B12', 'Jenis');
        $sheet->getStyle('B12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C12', 'Kode');
        $sheet->getStyle('C12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D11', 'Bentuk Celup');
        $sheet->mergeCells('D11:D12');
        $sheet->getStyle('D11:D12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('E11', 'Warna');
        $sheet->mergeCells('E11:E12');
        $sheet->getStyle('E11:E12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'Kode Warna');
        $sheet->mergeCells('F11:F12');
        $sheet->getStyle('F11:F12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'Buyer');
        $sheet->mergeCells('G11:G12');
        $sheet->getStyle('G11:G12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H11', 'Nomor Order');
        $sheet->mergeCells('H11:H12');
        $sheet->getStyle('H11:H12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I11', 'Delivery');
        $sheet->mergeCells('I11:I12');
        $sheet->getStyle('I11:I12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J11', 'Qty Pesanan');
        $sheet->mergeCells('J11:J11');
        $sheet->getStyle('J11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('J12', 'Kg');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('K11', 'Permintaan Cones');
        $sheet->mergeCells('K11:N11');
        $sheet->getStyle('K11:N11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('K12', 'Kg');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L12', 'Yard');
        $sheet->getStyle('L12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M12', 'Total Cones');
        $sheet->getStyle('M12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('N12', 'Jenis Cones');
        $sheet->getStyle('N12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('O11', 'Untuk Produksi');
        $sheet->mergeCells('O11:O12');
        $sheet->getStyle('O11:O12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P11', 'Contoh Warna');
        $sheet->mergeCells('P11:P12');
        $sheet->getStyle('P11:P12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q11', 'Keterangan Celup');
        $sheet->mergeCells('Q11:Q12');
        $sheet->getStyle('Q11:Q12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A11:Q12')->getFont()->setBold(true);
    }

    private function writeFooter($sheet, $row, $admin, $pj, $penerima)
    {
        //Keterangan
        $sheet->setCellValue('F39', $openPoGabung[0]['keterangan'] ?? '');
        $sheet->mergeCells('F39:J39');
        $sheet->getStyle('F39:J39')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F39:J39')->getFont()->setBold(true);

        //Tanda Tangan
        $sheet->setCellValue('E43', 'Pemesan');
        $sheet->getStyle('E43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H43', 'Mengetahui');
        $sheet->mergeCells('H43:I43');
        $sheet->getStyle('H43:I43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N43', 'Tanda terima');
        $sheet->mergeCells('N43:P43');
        $sheet->getStyle('N43:P43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N44', 'Celup Cones');
        $sheet->mergeCells('N44:P44');
        $sheet->getStyle('N44:P44')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E49', '(   ' . $admin . '   )');
        $sheet->getStyle('E49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H49', '(   ' . $pj . '   )');
        $sheet->mergeCells('H49:I49');
        $sheet->getStyle('H49:I49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N49', '(   ' . $penerima . '   )');
        $sheet->mergeCells('N49:P49');
        $sheet->getStyle('N49:P49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A11:Q38")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    public function exportOpenPOGabung()
    {
        $tujuan    = $this->request->getGet('tujuan');
        $jenis     = $this->request->getGet('jenis');
        $jenis2    = $this->request->getGet('jenis2');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date') ?? null;
        $season = $this->request->getGet('season');
        $materialType = $this->request->getGet('material_type');

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } elseif ($tujuan == 'COVERING') {
            $penerima = 'Paryanti';
        } else {
            return redirect()->back()->with('error', 'Tujuan tidak valid.');
        }

        // Ambil data sama seperti di PDF
        $openPoGabung = $this->openPoModel->listOpenPoGabungbyDate($jenis, $jenis2, $penerima, $startDate, $endDate);

        foreach ($openPoGabung as &$po) {
            $buyersData = $this->openPoModel->getBuyer($po['id_po']);
            if (!empty($buyersData)) {
                $buyers    = array_column($buyersData, 'buyer');
                $noOrders  = array_column($buyersData, 'no_order');
                $deliv     = array_column($buyersData, 'delivery_awal');
                $po['buyer']         = count(array_unique($buyers)) === 1 ? $buyers[0] : '';
                $idx                 = array_keys($deliv, min($deliv))[0];
                $po['delivery_awal'] = $deliv[$idx];
                $po['no_order']      = $noOrders[$idx];
            } else {
                $po['buyer']         = '';
                $po['delivery_awal'] = '';
                $po['no_order']      = '';
            }
        }
        unset($po);

        $noModel =  $openPoGabung[0]['no_model'] ?? '';

        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PO Gabungan');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);

        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle('Q1:Q50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A50:Q50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N4:O4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N5:O5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:Q4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:Q37')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:Q11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:Q28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

        // Atur lebar kolom dalam satuan pt
        $columnWidths = [
            'A' => 20,
            'B' => 120,
            'C' => 40,
            'D' => 50,
            'E' => 100,
            'F' => 100,
            'G' => 100,
            'H' => 100,
            'I' => 100,
            'J' => 50,
            'K' => 25,
            'L' => 25,
            'M' => 40,
            'N' => 40,
            'O' => 100,
            'P' => 100,
            'Q' => 100,
        ];

        $rowHeightsPt = [
            11 => 50, // misal 25 pt untuk header tabel
            12 => 50, // misal 20 pt untuk baris pertama data
        ];

        //Atur Tinggi Baris dan Lebar Kolom
        foreach ($rowHeightsPt as $row => $heightPt) {
            $sheet->getRowDimension($row)
                ->setRowHeight($heightPt);
        }

        foreach ($columnWidths as $col => $widthPt) {
            $charWidth = round($widthPt / 5.25, 2);
            $sheet->getColumnDimension($col)
                ->setWidth($charWidth)
                ->setAutoSize(false);
        }

        // Header Form
        // Logo dan judul perusahaan di bawah logo
        $sheet->mergeCells('A1:C3');
        $sheet->setCellValue('A1', 'PT. KAHATEX');
        $sheet->getStyle('A1')->getFont()->setSize(11);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $drawing = new Drawing();
        $drawing->setName('Logo')
            ->setDescription('PT. KAHATEX Logo')
            ->setPath(FCPATH . 'assets/img/logo-kahatex.png')
            ->setWorksheet($sheet)
            ->setCoordinates('B1')
            ->setOffsetX(150)
            ->setOffsetY(7)
            ->setHeight(40)
            ->setWidth(40);

        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D1:Q1');
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D2:Q2');
        $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D3:Q3');
        $sheet->setCellValue('D3', 'FORMULIR PO');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

        $sheet->mergeCells('N4:O4');
        $sheet->setCellValue('N4', 'Tanggal Revisi');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '17 Maret 2025');
        $sheet->getStyle('P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('N5:O5');
        $sheet->setCellValue('N5', 'Klasifikasi');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', 'Internal');
        $sheet->getStyle('P5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:M5');
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'PO');
        $sheet->getStyle('D1')->getFont()->setSize(18);

        $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('D6', ': ' . $noModel ?? '-');
        $sheet->mergeCells('D6:F7');
        $sheet->getStyle('D6')->getFont()->setSize(24);
        $sheet->getStyle('D6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A8', 'Pemesan');
        $sheet->setCellValue('D8', ': KK');

        $createdAt = $openPoGabung[0]['created_at'] ?? null;
        $sheet->setCellValue('A9', 'Tgl');
        $sheet->setCellValue('D9', ': ' . ($createdAt ? date('d/m/Y', strtotime($createdAt)) : '-'));

        $sheet->setCellValue('G7', $season);
        $sheet->mergeCells('G7:G9');
        $sheet->getStyle('G7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('H7', $materialType);
        $sheet->mergeCells('H7:H9');
        $sheet->getStyle('H7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('H7')->getFont()->setUnderline(true);
        $sheet->getStyle('A6:H9')->getFont()->setBold(true);

        // Header utama dan sub-header
        $sheet->setCellValue('A11', 'No');
        $sheet->mergeCells('A11:A12');
        $sheet->getStyle('A11:A12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('B11', 'Benang');
        $sheet->mergeCells('B11:C11');
        $sheet->getStyle('B11:C11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B12', 'Jenis');
        $sheet->getStyle('B12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C12', 'Kode');
        $sheet->getStyle('C12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D11', 'Bentuk Celup');
        $sheet->mergeCells('D11:D12');
        $sheet->getStyle('D11:D12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('E11', 'Warna');
        $sheet->mergeCells('E11:E12');
        $sheet->getStyle('E11:E12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'Kode Warna');
        $sheet->mergeCells('F11:F12');
        $sheet->getStyle('F11:F12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'Buyer');
        $sheet->mergeCells('G11:G12');
        $sheet->getStyle('G11:G12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H11', 'Nomor Order');
        $sheet->mergeCells('H11:H12');
        $sheet->getStyle('H11:H12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I11', 'Delivery');
        $sheet->mergeCells('I11:I12');
        $sheet->getStyle('I11:I12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J11', 'Qty Pesanan');
        $sheet->mergeCells('J11:J11');
        $sheet->getStyle('J11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('J12', 'Kg');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('K11', 'Permintaan Cones');
        $sheet->mergeCells('K11:N11');
        $sheet->getStyle('K11:N11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('K12', 'Kg');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L12', 'Yard');
        $sheet->getStyle('L12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M12', 'Total Cones');
        $sheet->getStyle('M12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('N12', 'Jenis Cones');
        $sheet->getStyle('N12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('O11', 'Untuk Produksi');
        $sheet->mergeCells('O11:O12');
        $sheet->getStyle('O11:O12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P11', 'Contoh Warna');
        $sheet->mergeCells('P11:P12');
        $sheet->getStyle('P11:P12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q11', 'Keterangan Celup');
        $sheet->mergeCells('Q11:Q12');
        $sheet->getStyle('Q11:Q12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A11:Q12')->getFont()->setBold(true);

        // Mulai menulis data dari baris 13
        $rowNum = 13;
        $no = 1;
        $totalKgPo = 0;
        $totalCones = 0;

        foreach ($openPoGabung as $po) {
            // Kolom A: No
            $sheet->setCellValue('A' . $rowNum, $no++);

            // Kolom B: item_type + spesifikasi, MERGE 2 baris (baris data + baris kosong)
            $itemTypeText = $po['item_type'] . ' (' . $po['spesifikasi_benang'] . ')';
            $sheet->setCellValue('B' . $rowNum, $itemTypeText);
            $sheet->mergeCells('B' . $rowNum . ':B' . ($rowNum + 1));

            // Kolom C: Ukuran
            $sheet->setCellValue('C' . $rowNum, $po['ukuran']);

            // Kolom D: bentuk_celup, MERGE 2 baris
            $sheet->setCellValue('D' . $rowNum, $po['bentuk_celup']);
            $sheet->mergeCells('D' . $rowNum . ':D' . ($rowNum + 1));

            // Kolom E–Q: hanya isi di baris pertama
            $sheet->setCellValue('E' . $rowNum, $po['color']);
            $sheet->setCellValue('F' . $rowNum, $po['kode_warna']);
            $sheet->setCellValue('G' . $rowNum, $po['buyer']);
            $sheet->setCellValue('H' . $rowNum, $po['no_order']);
            $sheet->setCellValue('I' . $rowNum, $po['delivery_awal']);
            $sheet->setCellValue('J' . $rowNum, number_format($po['kg_po'], 2));
            $sheet->setCellValue('K' . $rowNum, $po['kg_percones']);
            $sheet->setCellValue('L' . $rowNum, '');
            $sheet->setCellValue('M' . $rowNum, $po['jumlah_cones']);
            $sheet->setCellValue('N' . $rowNum, '');
            $sheet->setCellValue('O' . $rowNum, $po['jenis_produksi']);
            $sheet->setCellValue('P' . $rowNum, '');
            $sheet->setCellValue('Q' . $rowNum, $po['ket_celup']);

            // Akumulasi total
            $totalKgPo   += $po['kg_po'];
            $totalCones  += $po['jumlah_cones'];

            // Baris kosong sebagai pemisah
            $rowNum += 2;
        }

        // Baris Total (sama layout seperti PDF)
        // Gabungkan A–I untuk label "Total"
        $sheet->setCellValue("A38", 'Total');
        $sheet->mergeCells("A38:I38");
        $sheet->getStyle("A38")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // J: Total Kg PO
        $sheet->setCellValue("J38", number_format($totalKgPo, 2));
        // K–L kosong
        $sheet->setCellValue("K38", '');
        $sheet->setCellValue("L38", '');
        // M: Total Cones
        $sheet->setCellValue("M38", $totalCones ?: '');
        // N–Q kosong
        foreach (range('N', 'Q') as $col) {
            $sheet->setCellValue("{$col}38", '');
        }
        // Tambahkan border untuk seluruh baris total A38:Q38
        $sheet->getStyle('A38:Q38')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'font' => [
                'bold' => true,
            ],
        ]);
        // Kolom A (border kiri double)
        $sheet->getStyle('A38')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

        // Kolom Q (border kanan double)
        $sheet->getStyle('Q38')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

        //Keterangan
        $sheet->setCellValue('F39', $openPoGabung[0]['keterangan'] ?? '');
        $sheet->mergeCells('F39:J39');
        $sheet->getStyle('F39:J39')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F39:J39')->getFont()->setBold(true);

        //Tanda Tangan
        $sheet->setCellValue('E43', 'Pemesan');
        $sheet->getStyle('E43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H43', 'Mengetahui');
        $sheet->mergeCells('H43:I43');
        $sheet->getStyle('H43:I43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N43', 'Tanda terima');
        $sheet->mergeCells('N43:P43');
        $sheet->getStyle('N43:P43')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N44', 'Celup Cones');
        $sheet->mergeCells('N44:P44');
        $sheet->getStyle('N44:P44')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E49', '(   ' . $openPoGabung[0]['admin'] . '   )');
        $sheet->getStyle('E49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H49', '(   ' . $openPoGabung[0]['penanggung_jawab'] . '   )');
        $sheet->mergeCells('H49:I49');
        $sheet->getStyle('H49:I49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('N49', '(   ' . $penerima . '   )');
        $sheet->mergeCells('N49:P49');
        $sheet->getStyle('N49:P49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A11:Q38")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Header respons
        $filename = 'PO Gabungan' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function generateOpenPONylon()
    {
        $tujuan = $this->request->getPost('tujuan');
        $season = $this->request->getPost('season');
        $materialType = $this->request->getPost('material_type');
        $tanggal = $this->request->getPost('tanggal');
        $result = $this->openPoModel->getDataPoNylon($tanggal);
        $pemesanan = '';
        foreach ($result as &$res) {
            // Ambil unit dengan pengecekan
            $unit = $this->masterOrderModel->getUnit($res['no_model']);
            $rawUnit = strtoupper(trim($unit['unit'] ?? 'TIDAK DIKETAHUI'));

            // Default pemesanan
            $pemesanan = 'KAOS KAKI';
            if ($rawUnit === 'MAJALAYA') {
                $pemesanan .= ' / ' . $rawUnit;
            }

            // API URL dari config
            $apiUrl = 'http://172.23.44.14/CapacityApps/public/api/getDataBuyer?no_model=' . urlencode($res['no_model']);

            // Pakai stream context untuk timeout dan error handling
            $context = stream_context_create(['http' => ['timeout' => 3]]); // 3 detik timeout

            $buyerResponse = @file_get_contents($apiUrl, false, $context);
            $buyerName = $buyerResponse ? json_decode($buyerResponse, true) : 'Unknown Buyer';
            // Simpan hasil
            $res['unit'] = $rawUnit;
            $res['buyer'] = is_array($buyerName) ? ($buyerName['kd_buyer_order'] ?? 'N/A') : $buyerName;
            // Cek jika delivery sudah berupa tanggal, jika tidak, tetap tampilkan apa adanya
            $timestamp = strtotime($res['delivery_awal']);
            if ($timestamp !== false) {
                $bulanIndo = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];
                $day = date('j', $timestamp);
                $month = $bulanIndo[(int)date('n', $timestamp)];
                $year = date('Y', $timestamp);
                $res['delivery_awal'] = "{$day} {$month} {$year}";
            }
        }



        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // dd($result);

        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Open PO Nylon ' . $tanggal);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);

        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle('Q1:Q50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A50:Q50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N4:O4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N5:O5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:Q4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:Q28')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:Q11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:Q28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

        // Atur lebar kolom dalam satuan pt
        $columnWidths = [
            'A' => 20,
            'B' => 120,
            'C' => 40,
            'D' => 50,
            'E' => 100,
            'F' => 100,
            'G' => 100,
            'H' => 100,
            'I' => 100,
            'J' => 50,
            'K' => 25,
            'L' => 25,
            'M' => 40,
            'N' => 40,
            'O' => 100,
            'P' => 100,
            'Q' => 100,
        ];

        $rowHeightsPt = [
            11 => 50, // misal 25 pt untuk header tabel
            12 => 50, // misal 20 pt untuk baris pertama data
        ];

        //Atur Tinggi Baris dan Lebar Kolom
        foreach ($rowHeightsPt as $row => $heightPt) {
            $sheet->getRowDimension($row)
                ->setRowHeight($heightPt);
        }

        foreach ($columnWidths as $col => $widthPt) {
            $charWidth = round($widthPt / 5.25, 2);
            $sheet->getColumnDimension($col)
                ->setWidth($charWidth)
                ->setAutoSize(false);
        }

        // Header Form
        $sheet->mergeCells('A1:B2');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(55);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D1:Q1');
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D2:Q2');
        $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D3:Q3');
        $sheet->setCellValue('D3', 'FORMULIR PO');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

        $sheet->mergeCells('N4:O4');
        $sheet->setCellValue('N4', 'Tanggal Revisi');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '17 Maret 2025');
        $sheet->getStyle('P4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('N5:O5');
        $sheet->setCellValue('N5', 'Klasifikasi');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', 'Internal');
        $sheet->getStyle('P5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:M5');
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'PO');
        $sheet->getStyle('D1')->getFont()->setSize(18);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells('C6:E7');
        $sheet->setCellValue('C6', ': ');
        $sheet->getStyle('C6')->getFont()->setSize(24);
        $sheet->getStyle('C6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A8', 'Pemesan');
        $sheet->setCellValue('C8', ': ' . $pemesanan);

        $sheet->setCellValue('A9', 'Tgl');
        $sheet->setCellValue('C9', ': ' . (isset($result[0]['tgl_po']) ? date('d/m/Y', strtotime($result[0]['tgl_po'])) : ''));

        $sheet->setCellValue('F7', $season);
        $sheet->mergeCells('F7:F9');
        $sheet->getStyle('F7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('G7', $materialType);
        $sheet->mergeCells('G7:G9');
        $sheet->getStyle('G7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('G7')->getFont()->setUnderline(true);

        // Header utama dan sub-header
        $sheet->setCellValue('A11', 'No');
        $sheet->mergeCells('A11:A12');
        $sheet->getStyle('A11:A12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('B11', 'Benang');
        $sheet->mergeCells('B11:C11');
        $sheet->getStyle('B11:C11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B12', 'Jenis');
        $sheet->getStyle('B12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C12', 'Kode');
        $sheet->getStyle('C12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D11', 'Bentuk Celup');
        $sheet->mergeCells('D11:D12');
        $sheet->getStyle('D11:D12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('E11', 'Warna');
        $sheet->mergeCells('E11:E12');
        $sheet->getStyle('E11:E12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'Kode Warna');
        $sheet->mergeCells('F11:F12');
        $sheet->getStyle('F11:F12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'Buyer');
        $sheet->mergeCells('G11:G12');
        $sheet->getStyle('G11:G12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H11', 'Nomor Order');
        $sheet->mergeCells('H11:H12');
        $sheet->getStyle('H11:H12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I11', 'Delivery');
        $sheet->mergeCells('I11:I12');
        $sheet->getStyle('I11:I12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J11', 'Qty Pesanan');
        $sheet->mergeCells('J11:J11');
        $sheet->getStyle('J11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('J12', 'Kg');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('K11', 'Permintaan Cones');
        $sheet->mergeCells('K11:N11');
        $sheet->getStyle('K11:N11')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('K12', 'Kg');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L12', 'Yard');
        $sheet->getStyle('L12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M12', 'Total Cones');
        $sheet->getStyle('M12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('N12', 'Jenis Cones');
        $sheet->getStyle('N12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('O11', 'Untuk Produksi');
        $sheet->mergeCells('O11:O12');
        $sheet->getStyle('O11:O12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P11', 'Contoh Warna');
        $sheet->mergeCells('P11:P12');
        $sheet->getStyle('P11:P12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q11', 'Keterangan Celup');
        $sheet->mergeCells('Q11:Q12');
        $sheet->getStyle('Q11:Q12')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Isi tabel
        $rowNum = 13;
        $no = 1;
        $totalKg = $totalCones = $totalYard = $totalKgPerCones = 0;
        $firstRow = true;
        foreach ($result as $row) {
            $spesifikasiBenang = trim($row['spesifikasi_benang'] ?? ''); // pakai null coalescing dan trim untuk keamanan
            if ($spesifikasiBenang === '- -') {
                $spesifikasiBenang = '';
            }

            if ($firstRow) {
                $buyerDisplay   = $row['buyer'] . ' (' . ($buyerName['kd_buyer_order'] ?? '') . ')';
                $noOrderDisplay = $row['no_order'];
                $deliveryDisplay = $row['delivery_awal'];
                $firstRow = false; // reset flag setelah baris pertama
            } else {
                $buyerDisplay   = $row['buyer'] . ' (' . ($buyerName['kd_buyer_order'] ?? '') . ')';
                $noOrderDisplay = $row['no_order'];
                $deliveryDisplay = $row['delivery_awal'];
            }

            $sheet->fromArray([
                $no++,
                $row['item_type'] . ' ' . $spesifikasiBenang,
                $row['ukuran'],
                $row['bentuk_celup'],
                $row['color'],
                $row['kode_warna'],
                $buyerDisplay,
                $noOrderDisplay,
                $deliveryDisplay,
                $row['kg_po'],
                $row['kg_percones'],
                '', // yard belum ada
                $row['jumlah_cones'],
                '', // jenis cones belum ada
                $row['jenis_produksi'],
                $row['contoh_warna'],
                $row['ket_celup']
            ], null, 'A' . $rowNum);

            $totalKg += floatval($row['kg_po']);
            $totalKgPerCones += floatval($row['kg_percones']);
            $totalCones += floatval($row['jumlah_cones']);
            $rowNum++;
        }

        // Total
        $sheet->setCellValue('A28', 'TOTAL');
        $sheet->mergeCells('A28:I28');
        $sheet->getStyle('A28:I28')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('J28', $totalKg);
        $sheet->setCellValue('K28', $totalKgPerCones);
        $sheet->setCellValue('M28', $totalCones);

        //Keterangan
        $sheet->setCellValue('F30', $result[0]['keterangan'] ?? '');
        $sheet->mergeCells('F30:J30');
        $sheet->getStyle('F30:J30')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        //Tanda Tangan
        $sheet->setCellValue('E45', 'Pemesan');
        $sheet->getStyle('E45')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H45', 'Mengetahui');
        $sheet->getStyle('H45')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J45', 'Tanda terima');
        $sheet->mergeCells('J45:L45');
        $sheet->getStyle('J45:L45')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J46', 'Celup Cones');
        $sheet->mergeCells('J46:L46');
        $sheet->getStyle('J46:L46')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E49', '(   ' . $result[0]['admin'] . '   )');
        $sheet->getStyle('E49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H49', '(   ' . $result[0]['penanggung_jawab'] . '   )');
        $sheet->getStyle('H49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J49', '(   ' . $penerima . '   )');
        $sheet->mergeCells('J49:L49');
        $sheet->getStyle('J49:L49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K49')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A11:Q28")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Output Excel
        $filename = 'Open PO Nylon' . $tanggal . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }

    public function generateOpenPOBookingExcel()
    {
        $tujuan = $this->request->getGet('tujuan');
        $noModel = $this->request->getGet('no_model');
        $delivery = $this->request->getGet('delivery');
        $materialType = $this->request->getGet('material_type');
        $noOrder = $this->request->getGet('no_order');

        $result = $this->openPoModel->getPoBookingByNoModel($noModel);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        if (strlen($noModel) > 16) {
            $noModel = substr($noModel, 0, 16);
        }

        // $noModel =  $result[0]['no_model'] ?? '';

        // Ambil buyer dari API
        // $buyerApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getDataBuyer?no_model=' . urlencode($noModel);
        // $buyerName = json_decode(file_get_contents($buyerApiUrl), true);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // dd($result);
        if (!empty($delivery)) {
            // Cek jika delivery sudah berupa tanggal, jika tidak, tetap tampilkan apa adanya
            $timestamp = strtotime($delivery);
            if ($timestamp !== false) {
                $bulanIndo = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];
                $day = date('j', $timestamp);
                $month = $bulanIndo[(int)date('n', $timestamp)];
                $year = date('Y', $timestamp);
                $delivery = "{$day} {$month} {$year}";
            }
        }
        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Open PO Booking' . $noModel);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);

        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle('Q1:Q50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A50:Q50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N4:O4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N5:O5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:Q4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:Q28')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:Q11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:Q28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

        // Atur lebar kolom dalam satuan pt
        $columnWidths = [
            'A' => 20,
            'B' => 120,
            'C' => 40,
            'D' => 50,
            'E' => 100,
            'F' => 100,
            'G' => 100,
            'H' => 100,
            'I' => 100,
            'J' => 50,
            'K' => 25,
            'L' => 25,
            'M' => 40,
            'N' => 40,
            'O' => 100,
            'P' => 100,
            'Q' => 100,
        ];

        $rowHeightsPt = [
            11 => 50, // misal 25 pt untuk header tabel
            12 => 50, // misal 20 pt untuk baris pertama data
        ];

        //Atur Tinggi Baris dan Lebar Kolom
        foreach ($rowHeightsPt as $row => $heightPt) {
            $sheet->getRowDimension($row)
                ->setRowHeight($heightPt);
        }

        foreach ($columnWidths as $col => $widthPt) {
            $charWidth = round($widthPt / 5.25, 2);
            $sheet->getColumnDimension($col)
                ->setWidth($charWidth)
                ->setAutoSize(false);
        }

        // Header Form
        $sheet->mergeCells('A1:B2');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(55);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D1:Q1');
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D2:Q2');
        $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D3:Q3');
        $sheet->setCellValue('D3', 'FORMULIR PO');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

        $sheet->mergeCells('N4:O4');
        $sheet->setCellValue('N4', 'Tanggal Revisi');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '17 Maret 2025');
        $sheet->getStyle('P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('N5:O5');
        $sheet->setCellValue('N5', 'Klasifikasi');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', 'Internal');
        $sheet->getStyle('P5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:M5');
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'PO');
        $sheet->getStyle('D1')->getFont()->setSize(18);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells('C6:E7');
        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $sheet->setCellValue('C6', ': ' . $noModel);
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $sheet->setCellValue('C6', ': ' . '(+) ' . $noModel);
        } else {
            $sheet->setCellValue('C6', ': ' . $noModel);
        }
        // $sheet->setCellValue('C6', ': ' . $no_model);
        $sheet->getStyle('C6')->getFont()->setSize(24);
        $sheet->getStyle('C6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A8', 'Pemesan');
        $sheet->setCellValue('C8', ': KAOS KAKI');

        $sheet->setCellValue('A9', 'Tgl');
        $sheet->setCellValue('C9', ': ' . (isset($result[0]['tgl_po']) ? date('d/m/Y', strtotime($result[0]['tgl_po'])) : ''));

        $sheet->setCellValue('G7', $materialType);
        $sheet->mergeCells('G7:G9');
        $sheet->getStyle('G7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('G7')->getFont()->setUnderline(true);

        // Header utama dan sub-header
        $sheet->setCellValue('A11', 'No');
        $sheet->mergeCells('A11:A12');
        $sheet->getStyle('A11:A12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('B11', 'Benang');
        $sheet->mergeCells('B11:C11');
        $sheet->getStyle('B11:C11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B12', 'Jenis');
        $sheet->getStyle('B12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C12', 'Kode');
        $sheet->getStyle('C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D11', 'Bentuk Celup');
        $sheet->mergeCells('D11:D12');
        $sheet->getStyle('D11:D12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('E11', 'Warna');
        $sheet->mergeCells('E11:E12');
        $sheet->getStyle('E11:E12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'Kode Warna');
        $sheet->mergeCells('F11:F12');
        $sheet->getStyle('F11:F12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'Buyer');
        $sheet->mergeCells('G11:G12');
        $sheet->getStyle('G11:G12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H11', 'Nomor Order');
        $sheet->mergeCells('H11:H12');
        $sheet->getStyle('H11:H12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I11', 'Delivery');
        $sheet->mergeCells('I11:I12');
        $sheet->getStyle('I11:I12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J11', 'Qty Pesanan');
        $sheet->mergeCells('J11:J11');
        $sheet->getStyle('J11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('J12', 'Kg');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('K11', 'Permintaan Cones');
        $sheet->mergeCells('K11:N11');
        $sheet->getStyle('K11:N11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('K12', 'Kg');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L12', 'Yard');
        $sheet->getStyle('L12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M12', 'Total Cones');
        $sheet->getStyle('M12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('N12', 'Jenis Cones');
        $sheet->getStyle('N12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('O11', 'Untuk Produksi');
        $sheet->mergeCells('O11:O12');
        $sheet->getStyle('O11:O12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P11', 'Contoh Warna');
        $sheet->mergeCells('P11:P12');
        $sheet->getStyle('P11:P12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q11', 'Keterangan Celup');
        $sheet->mergeCells('Q11:Q12');
        $sheet->getStyle('Q11:Q12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Isi tabel
        $rowNum = 13;
        $no = 1;
        $totalKg = $totalCones = $totalYard = $totalKgPerCones = 0;
        $firstRow = true;

        foreach ($result as $row) {
            $spesifikasiBenang = trim($row['spesifikasi_benang'] ?? ''); // pakai null coalescing dan trim untuk keamanan
            if ($spesifikasiBenang === '- -') {
                $spesifikasiBenang = '';
            }

            if ($firstRow) {
                $buyerDisplay   = $row['buyer'] ?? '';
                $noOrderDisplay = $noOrder;
                $deliveryDisplay = $delivery;
                $firstRow = false; // reset flag setelah baris pertama
            } else {
                $buyerDisplay   = '';
                $noOrderDisplay = '';
                $deliveryDisplay = '';
            }

            $sheet->fromArray([
                $no++,
                $row['item_type'] . ' ' . $spesifikasiBenang,
                $row['ukuran'],
                $row['bentuk_celup'],
                $row['color'],
                $row['kode_warna'],
                $buyerDisplay,
                $noOrderDisplay,
                $deliveryDisplay,
                $row['kg_po'],
                $row['kg_percones'] ?? '',
                '', // yard belum ada
                ($row['jumlah_cones'] > 0) ? $row['jumlah_cones'] : '',
                '', // jenis cones belum ada
                $row['jenis_produksi'],
                $row['contoh_warna'],
                $row['ket_celup']
            ], null, 'A' . $rowNum);

            $totalKg += floatval($row['kg_po']);
            $totalKgPerCones += floatval($row['kg_percones']);
            $totalCones += floatval($row['jumlah_cones']);
            $rowNum++;
        }

        // Total
        $sheet->setCellValue('A28', 'TOTAL');
        $sheet->mergeCells('A28:I28');
        $sheet->getStyle('A28:I28')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('J28', $totalKg);
        $sheet->setCellValue('K28', $totalKgPerCones);
        $sheet->setCellValue('M28', ($totalCones > 0) ? $totalCones : '');

        //Keterangan
        $sheet->setCellValue('F30', $result[0]['keterangan'] ?? '');
        $sheet->mergeCells('F30:J30');
        $sheet->getStyle('F30:J30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        //Tanda Tangan
        $sheet->setCellValue('E45', 'Pemesan');
        $sheet->getStyle('E45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H45', 'Mengetahui');
        $sheet->getStyle('H45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J45', 'Tanda terima');
        $sheet->mergeCells('J45:L45');
        $sheet->getStyle('J45:L45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if ($tujuan == 'CELUP') {
            $sheet->setCellValue('J46', 'Celup Cones');
            $sheet->mergeCells('J46:L46');
            $sheet->getStyle('J46:L46')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('J46', 'Covering');
            $sheet->mergeCells('J46:L46');
            $sheet->getStyle('J46:L46')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }


        $sheet->setCellValue('E49', '(   ' . $result[0]['admin'] . '   )');
        $sheet->getStyle('E49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H49', '(   ' . $result[0]['penanggung_jawab'] . '   )');
        $sheet->getStyle('H49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J49', '(   ' . $penerima . '   )');
        $sheet->mergeCells('J49:L49');
        $sheet->getStyle('J49:L49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A11:Q28")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Output Excel
        $filename = 'Open PO Booking_' . $noModel . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }

    public function generateOpenPOManualExcel()
    {
        $tujuan = $this->request->getGet('tujuan');
        $noModel = $this->request->getGet('no_model');
        $delivery = $this->request->getGet('delivery');
        $materialType = $this->request->getGet('material_type');
        $result = $this->openPoModel->getPoManualByNoModel($noModel);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // $noModel =  $result[0]['no_model'] ?? '';

        // Ambil buyer dari API
        // $buyerApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getDataBuyer?no_model=' . urlencode($noModel);
        // $buyerName = json_decode(file_get_contents($buyerApiUrl), true);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // dd($result);
        if (!empty($delivery)) {
            // Cek jika delivery sudah berupa tanggal, jika tidak, tetap tampilkan apa adanya
            $timestamp = strtotime($delivery);
            if ($timestamp !== false) {
                $bulanIndo = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];
                $day = date('j', $timestamp);
                $month = $bulanIndo[(int)date('n', $timestamp)];
                $year = date('Y', $timestamp);
                $delivery = "{$day} {$month} {$year}";
            }
        }
        // Buat Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Open PO Manual ' . $noModel);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(16);

        // 1. Atur ukuran kertas jadi A4
        $sheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        // 2. Atur orientasi jadi landscape
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        // 3. (Opsional) Atur scaling, agar muat ke 1 halaman
        $sheet->getPageSetup()
            ->setFitToWidth(1)
            ->setFitToHeight(0)    // 0 artinya auto height
            ->setFitToPage(true); // aktifkan fitting

        // 4. (Opsional) Atur margin supaya tidak terlalu sempit
        $sheet->getPageMargins()->setTop(0.4)
            ->setBottom(0.4)
            ->setLeft(0.4)
            ->setRight(0.2);

        //Outline Border
        // 1. Top double border dari A1 ke Q1
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari Q1 ke Q50
        $sheet->getStyle('Q1:Q50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke Q50
        $sheet->getStyle('A50:Q50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N4:O4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('N5:O5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Double border baris 4 dan 5
        $sheet->getStyle('A4:Q4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        $sheet->getStyle('A5:Q5')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $thinInside = [
            'borders' => [
                // border antar kolom (vertical lines) di dalam range
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                // border antar baris (horizontal lines) di dalam range
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A11:Q28')->applyFromArray($thinInside);

        // 2) Border tipis atas untuk baris header tabel (A11:Q11)
        $sheet->getStyle('A11:Q11')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Border tipis bawah untuk baris total (A28:Q28)
        $sheet->getStyle('A28:Q28')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aktifkan wrap text di A11:Q28
        $sheet->getStyle('A11:Q28')->getAlignment()->setWrapText(true);

        // Atur lebar kolom dalam satuan pt
        $columnWidths = [
            'A' => 20,
            'B' => 120,
            'C' => 40,
            'D' => 50,
            'E' => 100,
            'F' => 100,
            'G' => 100,
            'H' => 100,
            'I' => 100,
            'J' => 50,
            'K' => 25,
            'L' => 25,
            'M' => 40,
            'N' => 40,
            'O' => 100,
            'P' => 100,
            'Q' => 100,
        ];

        $rowHeightsPt = [
            11 => 50, // misal 25 pt untuk header tabel
            12 => 50, // misal 20 pt untuk baris pertama data
        ];

        //Atur Tinggi Baris dan Lebar Kolom
        foreach ($rowHeightsPt as $row => $heightPt) {
            $sheet->getRowDimension($row)
                ->setRowHeight($heightPt);
        }

        foreach ($columnWidths as $col => $widthPt) {
            $charWidth = round($widthPt / 5.25, 2);
            $sheet->getColumnDimension($col)
                ->setWidth($charWidth)
                ->setAutoSize(false);
        }

        // Header Form
        $sheet->mergeCells('A1:B2');
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Logo kahatex
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(50);
        $drawing->setOffsetX(55);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D1:Q1');
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D2:Q2');
        $sheet->setCellValue('D2', 'DEPARTEMEN CELUP CONES');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('D3:Q3');
        $sheet->setCellValue('D3', 'FORMULIR PO');
        $sheet->getStyle('D3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-CC-087/REV_02/HAL_1/1');

        $sheet->mergeCells('N4:O4');
        $sheet->setCellValue('N4', 'Tanggal Revisi');
        $sheet->mergeCells('P4:Q4');
        $sheet->setCellValue('P4', '17 Maret 2025');
        $sheet->getStyle('P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('N5:O5');
        $sheet->setCellValue('N5', 'Klasifikasi');
        $sheet->mergeCells('P5:Q5');
        $sheet->setCellValue('P5', 'Internal');
        $sheet->getStyle('P5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:M5');
        $sheet->getStyle('A4:Q5')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('A6:A7');
        $sheet->setCellValue('A6', 'PO');
        $sheet->getStyle('D1')->getFont()->setSize(18);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells('C6:E7');
        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $sheet->setCellValue('C6', ': ' . $noModel);
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $sheet->setCellValue('C6', ': ' . '(+) ' . $noModel);
        } else {
            $sheet->setCellValue('C6', ': ' . $noModel);
        }
        // $sheet->setCellValue('C6', ': ' . $no_model);
        $sheet->getStyle('C6')->getFont()->setSize(24);
        $sheet->getStyle('C6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('A8', 'Pemesan');
        $sheet->setCellValue('C8', ': KAOS KAKI');

        $sheet->setCellValue('A9', 'Tgl');
        $sheet->setCellValue('C9', ': ' . (isset($result[0]['tgl_po']) ? date('d/m/Y', strtotime($result[0]['tgl_po'])) : ''));

        $sheet->setCellValue('G7', $materialType);
        $sheet->mergeCells('G7:G9');
        $sheet->getStyle('G7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('G7')->getFont()->setUnderline(true);

        // Header utama dan sub-header
        $sheet->setCellValue('A11', 'No');
        $sheet->mergeCells('A11:A12');
        $sheet->getStyle('A11:A12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('B11', 'Benang');
        $sheet->mergeCells('B11:C11');
        $sheet->getStyle('B11:C11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B12', 'Jenis');
        $sheet->getStyle('B12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C12', 'Kode');
        $sheet->getStyle('C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('D11', 'Bentuk Celup');
        $sheet->mergeCells('D11:D12');
        $sheet->getStyle('D11:D12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('E11', 'Warna');
        $sheet->mergeCells('E11:E12');
        $sheet->getStyle('E11:E12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('F11', 'Kode Warna');
        $sheet->mergeCells('F11:F12');
        $sheet->getStyle('F11:F12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G11', 'Buyer');
        $sheet->mergeCells('G11:G12');
        $sheet->getStyle('G11:G12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('H11', 'Nomor Order');
        $sheet->mergeCells('H11:H12');
        $sheet->getStyle('H11:H12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('I11', 'Delivery');
        $sheet->mergeCells('I11:I12');
        $sheet->getStyle('I11:I12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('J11', 'Qty Pesanan');
        $sheet->mergeCells('J11:J11');
        $sheet->getStyle('J11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('J12', 'Kg');
        $sheet->getStyle('J12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('K11', 'Permintaan Cones');
        $sheet->mergeCells('K11:N11');
        $sheet->getStyle('K11:N11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('K12', 'Kg');
        $sheet->getStyle('K12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('L12', 'Yard');
        $sheet->getStyle('L12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('M12', 'Total Cones');
        $sheet->getStyle('M12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);
        $sheet->setCellValue('N12', 'Jenis Cones');
        $sheet->getStyle('N12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        $sheet->setCellValue('O11', 'Untuk Produksi');
        $sheet->mergeCells('O11:O12');
        $sheet->getStyle('O11:O12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('P11', 'Contoh Warna');
        $sheet->mergeCells('P11:P12');
        $sheet->getStyle('P11:P12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('Q11', 'Keterangan Celup');
        $sheet->mergeCells('Q11:Q12');
        $sheet->getStyle('Q11:Q12')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Isi tabel
        $rowNum = 13;
        $no = 1;
        $totalKg = $totalCones = $totalYard = $totalKgPerCones = 0;
        $firstRow = true;

        foreach ($result as $row) {
            $spesifikasiBenang = trim($row['spesifikasi_benang'] ?? ''); // pakai null coalescing dan trim untuk keamanan
            if ($spesifikasiBenang === '- -') {
                $spesifikasiBenang = '';
            }

            if ($firstRow) {
                $buyerDisplay   = $row['buyer'] ?? '';
                $noOrderDisplay = $row['no_order'] ?? '';
                $deliveryDisplay = $delivery;
                $firstRow = false; // reset flag setelah baris pertama
            } else {
                $buyerDisplay   = '';
                $noOrderDisplay = '';
                $deliveryDisplay = '';
            }

            $sheet->fromArray([
                $no++,
                $row['item_type'] . ' ' . $spesifikasiBenang,
                $row['ukuran'],
                $row['bentuk_celup'],
                $row['color'],
                $row['kode_warna'],
                $buyerDisplay,
                $noOrderDisplay,
                $deliveryDisplay,
                $row['kg_po'],
                $row['kg_percones'] ?? '',
                '', // yard belum ada
                ($row['jumlah_cones'] > 0) ? $row['jumlah_cones'] : '',
                '', // jenis cones belum ada
                $row['jenis_produksi'],
                $row['contoh_warna'],
                $row['ket_celup']
            ], null, 'A' . $rowNum);

            $totalKg += floatval($row['kg_po']);
            $totalKgPerCones += floatval($row['kg_percones']);
            $totalCones += floatval($row['jumlah_cones']);
            $rowNum++;
        }

        // Total
        $sheet->setCellValue('A28', 'TOTAL');
        $sheet->mergeCells('A28:I28');
        $sheet->getStyle('A28:I28')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('J28', $totalKg);
        $sheet->setCellValue('K28', $totalKgPerCones);
        $sheet->setCellValue('M28', ($totalCones > 0) ? $totalCones : '');

        //Keterangan
        $sheet->setCellValue('F30', $result[0]['keterangan'] ?? '');
        $sheet->mergeCells('F30:J30');
        $sheet->getStyle('F30:J30')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        //Tanda Tangan
        $sheet->setCellValue('E45', 'Pemesan');
        $sheet->getStyle('E45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H45', 'Mengetahui');
        $sheet->getStyle('H45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J45', 'Tanda terima');
        $sheet->mergeCells('J45:L45');
        $sheet->getStyle('J45:L45')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if ($tujuan == 'CELUP') {
            $sheet->setCellValue('J46', 'Celup Cones');
            $sheet->mergeCells('J46:L46');
            $sheet->getStyle('J46:L46')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue('J46', 'Covering');
            $sheet->mergeCells('J46:L46');
            $sheet->getStyle('J46:L46')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->setCellValue('E49', '(   ' . $result[0]['admin'] . '   )');
        $sheet->getStyle('E49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('H49', '(   ' . $result[0]['penanggung_jawab'] . '   )');
        $sheet->getStyle('H49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('J49', '(   ' . $penerima . '   )');
        $sheet->mergeCells('J49:L49');
        $sheet->getStyle('J49:L49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A11:Q28")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Output Excel
        $filename = 'Open PO Manual_' . $noModel . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }
    public function exportHistoryPinjamOrder()
    {
        $noModel   = $this->request->getGet('model')     ?? '';
        $kodeWarna = $this->request->getGet('kode_warna') ?? '';

        // 1) Ambil data
        $dataPinjam = $this->historyStock->getHistoryPinjamOrder($noModel, $kodeWarna);

        // // 2) Siapkan HTTP client
        // $client = \Config\Services::curlrequest([
        //     'baseURI' => 'http://172.23.44.14/CapacityApps/public/api/',
        //     'timeout' => 5
        // ]);

        // // 3) Loop dan merge API result
        // foreach ($dataPinjam as &$row) {
        //     try {
        //         $res = $client->get('getDeliveryAwalAkhir', [
        //             'query' => ['model' => $row['no_model_new']]
        //         ]);
        //         $body = json_decode($res->getBody(), true);
        //         $row['delivery_awal']  = $body['delivery_awal']  ?? '-';
        //         $row['delivery_akhir'] = $body['delivery_akhir'] ?? '-';
        //     } catch (\Exception $e) {
        //         $row['delivery_awal']  = '-';
        //         $row['delivery_akhir'] = '-';
        //     }
        // }
        // unset($row);

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('REPORT HISTORY PINJAM ORDER');

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

        $dataFilter = '';

        if (!empty($noModel) && !empty($kodeWarna)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel . ' KODE WARNA ' . $kodeWarna;
        } elseif (!empty($noModel)) {
            $dataFilter = ' NOMOR MODEL ' . $noModel;
        } elseif (!empty($kodeWarna)) {
            $dataFilter = ' KODE WARNA ' . $kodeWarna;
        }

        // Judul
        $sheet->setCellValue('A1', 'REPORT HISTORY PINJAM ORDER' . $dataFilter);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row_header = 3;

        $headers = [
            'A' => 'NO',
            'B' => 'NO MODEL',
            // 'C' => 'DELIVERY AWAL',
            // 'D' => 'DELIVERY AKHIR',
            'C' => 'ITEM TYPE',
            'D' => 'KODE WARNA',
            'E' => 'WARNA',
            'F' => 'QTY',
            'G' => 'CONES',
            'H' => 'LOT',
            'I' => 'CLUSTER',
            'J' => 'KETERANGAN'
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . $row_header, $title);
            $sheet->getStyle($col . $row_header)->applyFromArray($styleHeader);
        }

        // Isi data
        $row = 4;
        $no = 1;

        foreach ($dataPinjam as $key => $data) {
            if (!is_array($data)) {
                continue; // Lewati nilai akumulasi di $result
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data['no_model_dipinjam']);
            // $sheet->setCellValue('C' . $row, $data['delivery_awal']);
            // $sheet->setCellValue('D' . $row, $data['delivery_akhir']);
            $sheet->setCellValue('C' . $row, $data['item_type']);
            $sheet->setCellValue('D' . $row, $data['kode_warna']);
            $sheet->setCellValue('E' . $row, $data['warna']);
            $sheet->setCellValue('F' . $row, $data['kgs']);
            $sheet->setCellValue('G' . $row, $data['cns']);
            $sheet->setCellValue('H' . $row, $data['lot']);
            $sheet->setCellValue('I' . $row, $data['cluster_old']);
            $sheet->setCellValue('J' . $row, strtoupper($data['created_at'] . ' Di' . $data['keterangan'] . ' ' . $data['no_model_meminjam'] . ' KODE ' . $data['kode_warna'] . '(' . $data['admin'] . ')'));

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }

            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set judul file dan header untuk download
        $filename = 'REPORT HISTORY PINJAM ORDER' . $dataFilter . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Tulis file excel ke output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function BahanBakuCovExcel()
    {
        // 1. Ambil data dan grup
        $jenis = $this->request->getGet('jenis_benang');
        $data = $this->warehouseBBModel->getDataByJenis($jenis);
        if (!$data) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tidak ada data.');
        }

        // 2. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Bahan Baku');

        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getStyle('D1:D3')->getFont()->setSize(8);
        $sheet->getStyle('A4:K50')->getFont()->setSize(7);

        // 3. Header umum
        $sheet->mergeCells('A1:C2');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo Perusahaan');
        $drawing->setPath('assets/img/logo-kahatex.png');
        $drawing->setCoordinates('B1');
        $drawing->setHeight(35);
        $drawing->setOffsetX(25);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('A3', 'PT. KAHATEX');
        $sheet->getStyle('A3')->getFont()->setSize(8);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Tanggal:');
        $sheet->setCellValue('B2', date('d M Y'));

        $sheet->mergeCells('D1:K1');
        $sheet->setCellValue('D1', 'FORMULIR');
        $sheet->getStyle('D1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('D1')->getFill()->getStartColor()->setRGB('99FFFF');
        $sheet->mergeCells('D2:K2');
        $sheet->setCellValue('D2', 'DEPARTEMEN COVERING');
        $sheet->mergeCells('D3:K3');
        $sheet->setCellValue('D3', 'STOCK BAHAN BAKU PER HARI');
        $sheet->getStyle('A1:K3')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('A4', 'No. Dokumen');
        $sheet->setCellValue('D4', 'FOR-COV-092/REV_00/HAL_1/3');
        $sheet->getStyle('A4:K4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('G4:H4');
        $sheet->setCellValue('G4', 'Tanggal Revisi');
        $sheet->mergeCells('I4:K4');
        $sheet->setCellValue('I4', '07 Januari 2019');
        $sheet->getStyle('I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Format tanggal: Rabu, 16 Jul 2025
        $sheet->mergeCells('B5:K5');
        $timestamp = strtotime($data[0]['created_at']);
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des'
        ];
        $hari = $hari[date('w', $timestamp)];
        $tgl = date('j', $timestamp);
        $bln = $bulan[(int)date('n', $timestamp)];
        $thn = date('Y', $timestamp);
        $sheet->setCellValue('A5', 'Tanggal');
        $sheet->setCellValue('B5', ': ' . "{$hari}, {$tgl} {$bln} {$thn}");
        $sheet->mergeCells('B6:K6');
        $sheet->setCellValue('A6', 'Jenis');
        $sheet->setCellValue('B6', ': ' . $data[0]['jenis_benang']);

        $sheet->getStyle('A1:K4')->getFont()->setBold(true);

        $columnWidths = [
            'A' => 10,  // Denier
            'B' => 10,  // Warna
            'C' => 10,  // Code
            'D' => 10,   // Stock (Kg)
            'E' => 15,  // Keterangan
            'F' => 2,   // Kosong atau dipakai untuk merge
            'G' => 10,  // Denier
            'H' => 10,  // Warna
            'I' => 10,  // Code
            'J' => 10,   // Stock (Kg)
            'K' => 15,  // Keterangan
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        //Outline Border
        // 1. Top double border dari A1 ke K1
        $sheet->getStyle('A1:K1')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 2. Right double border dari K1 ke K50
        $sheet->getStyle('K1:K50')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3. Bottom double border dari A50 ke K50 
        $sheet->getStyle('A50:K50')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 4. Left double border dari A1 ke A50
        $sheet->getStyle('A1:A50')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        //Border Thin
        //Title Departemen Covering
        $sheet->getStyle('D2:K2')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Logo Kahatex
        $sheet->getStyle('C1:C3')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border Atas Bawah No Dokumen
        $sheet->getStyle('A4:K4')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '000000'],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border Kanan No Dokumen
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border Kiri Kanan Tanggal Revisi
        $sheet->getStyle('G4:H4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border Isi Tabel
        $thinInside = [
            'borders' => [
                'vertical' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
                'horizontal' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        //Tanpa Border
        $noBorder = [
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ];
        //Border Atas 
        $sheet->getStyle("A7:E7")->getBorders()->applyFromArray([
            'top' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ]
        ]);
        $sheet->getStyle("G7:K7")->getBorders()->applyFromArray([
            'top' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ]
        ]);
        // Border kanan untuk kolom E
        $sheet->getStyle('E7:E42')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        // Border kiri untuk kolom G
        $sheet->getStyle('G7:G42')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border bawah untuk baris 42
        $sheet->getStyle('A42:K42')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        //Border Isi Tabel
        $sheet->getStyle('A7:E42')->applyFromArray($thinInside);
        $sheet->getStyle('G7:K42')->applyFromArray($thinInside);

        //Merge dan Hilangkan Border Atas dan Bawah di kolom F
        $sheet->mergeCells('F7:F42');
        $sheet->getStyle('F7:F42')->applyFromArray($noBorder);

        //Penulisan header kolom
        $firstHeaderRow = 7;
        $secondHeaderRow = 8;
        $endRow = 42;
        $sheet->fromArray(['Denier', 'Warna', 'Code', 'Stock', 'Keterangan'], null, "A{$firstHeaderRow}");
        $sheet->setCellValue("D{$secondHeaderRow}", 'Kg');
        $sheet->setCellValue("J{$secondHeaderRow}", 'Kg');
        $sheet->fromArray(['Denier', 'Warna', 'Code', 'Stock', 'Keterangan'], null, "G{$firstHeaderRow}");
        // Merge kolom A, B, C, dan E baris 7-8
        $sheet->mergeCells("A{$firstHeaderRow}:A{$secondHeaderRow}");
        $sheet->mergeCells("B{$firstHeaderRow}:B{$secondHeaderRow}");
        $sheet->mergeCells("C{$firstHeaderRow}:C{$secondHeaderRow}");
        $sheet->mergeCells("E{$firstHeaderRow}:E{$secondHeaderRow}");
        // Merge kolom G, H, I, dan K baris 7-8
        $sheet->mergeCells("G{$firstHeaderRow}:G{$secondHeaderRow}");
        $sheet->mergeCells("H{$firstHeaderRow}:H{$secondHeaderRow}");
        $sheet->mergeCells("I{$firstHeaderRow}:I{$secondHeaderRow}");
        $sheet->mergeCells("K{$firstHeaderRow}:K{$secondHeaderRow}");

        $sheet->getStyle("A{$firstHeaderRow}:K{$firstHeaderRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$secondHeaderRow}:K{$secondHeaderRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        //Mulai loop data
        $currentRow = $secondHeaderRow + 1;
        $grouped = [];
        foreach ($data as $item) {
            // asumsikan tiap $item berisi keys: denier, warna, kode, kg, keterangan
            $grouped[$item['denier']][] = $item;
        }

        // Mulai loop data (tetap pakai $currentRow = $secondHeaderRow + 1;)
        foreach ($grouped as $denierValue => $rows) {
            $startRow = $currentRow;
            $first = true;

            foreach ($rows as $entry) {
                // Tentukan target kolom & baris
                if ($currentRow <= $endRow) {
                    // sisi kiri: A–E
                    $cols = ['A', 'B', 'C', 'D', 'E'];
                    $row  = $currentRow;
                } else {
                    // sisi kanan: G–K, mulai dari baris 9
                    $cols = ['G', 'H', 'I', 'J', 'K'];
                    $row  = $secondHeaderRow + ($currentRow - $endRow);
                }

                // Cetak Denier hanya di kolom pertama ($cols[0]) & hanya sekali per group
                if ($first) {
                    $sheet->setCellValue("{$cols[0]}{$row}", $denierValue);
                    $first = false;
                }
                // Cetak sisa fields
                $sheet->setCellValue("{$cols[1]}{$row}", $entry['warna']);
                $sheet->setCellValue("{$cols[2]}{$row}", $entry['kode']);
                $sheet->setCellValue("{$cols[3]}{$row}", $entry['kg']);
                $sheet->setCellValue("{$cols[4]}{$row}", $entry['keterangan']);

                // Alignment center untuk A–E atau G–K
                $sheet->getStyle("{$cols[0]}{$row}:{$cols[4]}{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $currentRow++;
            }

            $endGroupRow = $currentRow - 1;
            // Merge Denier di kolom pertama kelompok, hanya jika lebih dari 1 baris
            if ($endGroupRow > $startRow) {
                // 1) Jika seluruh group masih di kiri (≤ $endRow)
                if ($endGroupRow <= $endRow) {
                    $sheet->mergeCells("A{$startRow}:A{$endGroupRow}");
                }
                // 2) Jika seluruh group sudah di kanan (> $endRow)
                elseif ($startRow > $endRow) {
                    // hitung baris kanan
                    $rightStart = $secondHeaderRow + ($startRow  - $endRow);
                    $rightEnd   = $secondHeaderRow + ($endGroupRow - $endRow);
                    $sheet->mergeCells("G{$rightStart}:G{$rightEnd}");
                }
                // 3) Jika group terpotong batas (sisi kiri & sisi kanan)
                else {
                    // kiri: dari startRow sampai endRow
                    $sheet->mergeCells("A{$startRow}:A{$endRow}");
                    // kanan: dari baris 9 (secondHeaderRow+1) sampai mapped end
                    $rightEnd   = $secondHeaderRow + ($endGroupRow - $endRow);
                    $sheet->mergeCells("G" . ($secondHeaderRow + 1) . ":G{$rightEnd}");
                }

                // styling alignment untuk kedua merge
                // kiri
                if ($startRow <= $endRow) {
                    $mergeEnd = min($endGroupRow, $endRow);
                    $sheet->getStyle("A{$startRow}:A{$mergeEnd}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
                // kanan
                if ($endGroupRow > $endRow) {
                    $rightStart = max($startRow, $endRow + 1);
                    $rightStart = $secondHeaderRow + ($rightStart - $endRow);
                    $rightEnd   = $secondHeaderRow + ($endGroupRow - $endRow);
                    $sheet->getStyle("G{$rightStart}:G{$rightEnd}")
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            }

            // Hitung total kg untuk grup ini
            $sumKg = array_sum(array_column($rows, 'kg'));

            // Tentukan target kolom & baris untuk TOTAL, sama seperti di data loop
            if ($currentRow <= $endRow) {
                // sisi kiri: A–E, kita pakai A–D untuk total
                $cols = ['A', 'B', 'C', 'D', 'E'];
                $row  = $currentRow;
            } else {
                // sisi kanan: G–K, kita pakai G–J untuk total
                $cols = ['G', 'H', 'I', 'J', 'K'];
                $row  = $secondHeaderRow + ($currentRow - $endRow);
            }

            // Merge 3 kolom pertama untuk teks "Total"
            $sheet->mergeCells("{$cols[0]}{$row}:{$cols[2]}{$row}");
            $sheet->setCellValue("{$cols[0]}{$row}", 'Total');
            $sheet->getStyle("{$cols[0]}{$row}:{$cols[2]}{$row}")->getFont()->setBold(true);

            // Tulis total kg di kolom ke-4 dari array $cols
            $sheet->setCellValue("{$cols[3]}{$row}", $sumKg);
            $sheet->getStyle("{$cols[3]}{$row}")->getFont()->setBold(true);

            // Alignment center
            $sheet->getStyle("{$cols[0]}{$row}:{$cols[3]}{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            // Fill color #99FFFF
            $sheet->getStyle("{$cols[0]}{$row}:{$cols[4]}{$row}")
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('99FFFF');

            $currentRow++;
        }

        //Penanggung Jawab
        $sheet->setCellValue('J44', 'Yang Bertanggung Jawab');
        $sheet->setCellValue('J47', '( IIS RAHAYU )');
        $sheet->getStyle('J44:J47')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 7. Output ke browser
        $filename = 'Stock Bahan Baku ' . $jenis . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangBenang()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->materialModel->getFilterSisaDatangBenang($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG BENANG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_datang'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_datang_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['qty_retur'] ?? 0);
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Benang' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangNylon()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->materialModel->getFilterSisaDatangNylon($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG NYLON');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_datang'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_datang_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['qty_retur'] ?? 0);
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Nylon' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangSpandex()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->materialModel->getFilterSisaDatangSpandex($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG SPANDEX');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_datang'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_datang_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['qty_retur'] ?? 0);
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Spandex' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportSisaDatangKaret()
    {
        $delivery = $this->request->getGet('delivery');
        $noModel = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');
        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12
        ];
        $bulan = $bulanMap[$delivery] ?? null;
        $data = $this->materialModel->getFilterSisaDatangKaret($bulan, $noModel, $kodeWarna);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'REPORT SISA DATANG KARET');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Buat header dengan sub-header
        $sheet->mergeCells('A3:A4');  // NO
        $sheet->mergeCells('B3:B4');  // TANGGAL PO
        $sheet->mergeCells('C3:C4');  // FOLL UP
        $sheet->mergeCells('D3:D4');  // NO MODEL
        $sheet->mergeCells('E3:E4');  // NO ORDER
        $sheet->mergeCells('F3:F4');  // AREA
        $sheet->mergeCells('G3:G4');  // BUYER
        $sheet->mergeCells('H3:H4');  // START MC
        $sheet->mergeCells('I3:I4');  // DELIVERY AWAL
        $sheet->mergeCells('J3:J4');  // DELIVERY AKHIR
        $sheet->mergeCells('K3:K4');  // ORDER TYPE
        $sheet->mergeCells('L3:L4');  // ITEM TYPE
        $sheet->mergeCells('M3:M4');  // KODE WARNA
        $sheet->mergeCells('N3:N4');  // WARNA
        $sheet->mergeCells('Q3:Q4');  // PESAN KG

        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'TANGGAL PO');
        $sheet->setCellValue('C3', 'FOLL UP');
        $sheet->setCellValue('D3', 'NO MODEL');
        $sheet->setCellValue('E3', 'NO ORDER');
        $sheet->setCellValue('F3', 'AREA');
        $sheet->setCellValue('G3', 'BUYER');
        $sheet->setCellValue('H3', 'START MC');
        $sheet->setCellValue('I3', 'DELIVERY AWAL');
        $sheet->setCellValue('J3', 'DELIVERY AKHIR');
        $sheet->setCellValue('K3', 'ORDER TYPE');
        $sheet->setCellValue('L3', 'ITEM TYPE');
        $sheet->setCellValue('M3', 'KODE WARNA');
        $sheet->setCellValue('N3', 'WARNA');
        $sheet->setCellValue('Q3', 'PESAN KG');

        // Stock Awal: Header + Sub-header
        $sheet->mergeCells('O3:P3'); // STOCK AWAL
        $sheet->setCellValue('O3', 'STOCK AWAL');
        $sheet->setCellValue('O4', 'KG');
        $sheet->setCellValue('P4', 'LOT');

        // Po Tambahan Gbn: Header + Sub-header
        $sheet->mergeCells('R3:U3');
        $sheet->setCellValue('R3', 'PO TAMBAHAN GBN');
        $sheet->setCellValue('R4', 'TGL TERIMA PO(+) GBN');
        $sheet->setCellValue('S4', 'TGL PO(+) AREA');
        $sheet->setCellValue('T4', 'DELIVERY PO(+)');
        $sheet->setCellValue('U4', 'KG PO (+)');

        // DATANG
        $sheet->mergeCells('V3:V4');
        $sheet->setCellValue('V3', 'DATANG');

        // (+) DATANG
        $sheet->mergeCells('W3:W4');
        $sheet->setCellValue('W3', '(+) DATANG');

        // Retur: Header + Sub-header
        $sheet->mergeCells('X3:X4');
        $sheet->setCellValue('X3', 'GANTI RETUR');

        $sheet->mergeCells('Y3:Y4');
        $sheet->setCellValue('Y3', 'RETUR');

        // Sisa
        $sheet->mergeCells('Z3:Z4');
        $sheet->setCellValue('Z3', 'SISA');

        // Format semua header
        $sheet->getStyle('A3:Z4')->getFont()->setBold(true);
        $sheet->getStyle('A3:Z4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:Z4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:Z4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 5;
        $no = 1;
        foreach ($data as $item) {
            $kgsAwal        = $item['kgs_stock_awal']  ?? 0;
            $kgsDatang      = $item['kgs_datang']      ?? 0;
            $kgsDatangPlus  = $item['kgs_datang_plus'] ?? 0;
            $kgsRetur       = $item['kgs_retur']       ?? 0;
            $kgPo           = $item['kg_po']           ?? 0;
            $kgPoPlus       = $item['kg_po_plus']      ?? 0;
            $qtyRetur       = $item['qty_retur']       ?? 0;

            if ($kgsRetur > 0) {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus - $qtyRetur));
            } else {
                $sisa = (($kgsAwal + $kgsDatang + $kgsDatangPlus + $kgsRetur) - ($kgPo - $kgPoPlus));
            }

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item['lco_date']);
            $sheet->setCellValue('C' . $row, $item['foll_up']);
            $sheet->setCellValue('D' . $row, $item['no_model']);
            $sheet->setCellValue('E' . $row, $item['no_order']);
            $sheet->setCellValue('F' . $row, $item['area']);
            $sheet->setCellValue('G' . $row, $item['buyer']);
            $sheet->setCellValue('H' . $row, $item['start_mc'] ?? '');
            $sheet->setCellValue('I' . $row, $item['delivery_awal']);
            $sheet->setCellValue('J' . $row, $item['delivery_akhir']);
            $sheet->setCellValue('K' . $row, $item['unit']);
            $sheet->setCellValue('L' . $row, $item['item_type']);
            $sheet->setCellValue('M' . $row, $item['kode_warna']);
            $sheet->setCellValue('N' . $row, $item['color']);
            $sheet->setCellValue('O' . $row, $item['kgs_stock_awal']);
            $sheet->setCellValue('P' . $row, $item['lot_awal']);
            $sheet->setCellValue('Q' . $row, $item['kg_po']);
            $sheet->setCellValue('R' . $row, $item['tgl_terima_po_plus_gbn'] ?? '');
            $sheet->setCellValue('S' . $row, $item['tgl_po_plus_area'] ?? '');
            $sheet->setCellValue('T' . $row, $item['delivery_awal_plus'] ?? '');
            $sheet->setCellValue('U' . $row, $item['kg_po_plus'] ?? 0);
            $sheet->setCellValue('V' . $row, $item['kgs_datang'] ?? 0);
            $sheet->setCellValue('W' . $row, $item['kgs_datang_plus'] ?? 0);
            $sheet->setCellValue('X' . $row, $item['kgs_retur'] ?? 0);
            $sheet->setCellValue('Y' . $row, $item['qty_retur'] ?? 0);
            $sheet->setCellValue('Z' . $row, $sisa ?? 0);
            $row++;
        }

        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A5:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:Z{$lastRow}")->applyFromArray($styleArray);

        // Auto-size
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download
        $filename = 'Report Sisa Datang Karet' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function reportPermintaanBahanBaku()
    {
        $jenis = $this->request->getGet('jenis');
        $area = $this->request->getGet('area');
        $tgl = $this->request->getGet('tgl');

        $data = $this->pemesananSpandexKaretModel->getPermintaanBahanBaku($jenis, $area, $tgl);
        // dd ($data);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('A1:O1');
        $sheet->setCellValue('A1', 'REPORT PERMINTAAN BAHAN BAKU');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'JENIS BAHAN BAKU: ' . strtoupper($jenis));
        $sheet->setCellValue('H2', 'AREA: ' . strtoupper($area));
        $sheet->mergeCells('M2:O2');
        $sheet->setCellValue('M2', 'TANGGAL PAKAI: ' . date('d-m-Y', strtotime($tgl)));
        $sheet->getStyle('A2:O2')->getFont()->setBold(true);
        $sheet->getStyle('A2:O2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:O2')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);


        // Buat header
        $sheet->setCellValue('A3', 'NO');
        $sheet->setCellValue('B3', 'JAM');
        $sheet->setCellValue('C3', 'TGL PSN');
        $sheet->setCellValue('D3', 'MODEL');
        $sheet->setCellValue('E3', 'ITEM TYPE');
        $sheet->setCellValue('F3', 'WARNA');
        $sheet->setCellValue('G3', 'KODE WARNA');
        $sheet->setCellValue('H3', 'LOT');
        $sheet->setCellValue('I3', 'JL MC');
        $sheet->setCellValue('J3', 'TOTAL');
        $sheet->setCellValue('K3', 'CONES');
        $sheet->setCellValue('L3', 'KETERANGAN');
        $sheet->setCellValue('M3', 'BAGIAN PERSIAPAN');
        $sheet->setCellValue('N3', 'QTY OUT');
        $sheet->setCellValue('O3', 'CNS OUT');

        // Format header
        $sheet->getStyle('A3:O3')->getFont()->setBold(true);
        $sheet->getStyle('A3:O3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3:O3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Data
        $row = 4;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['jam_pesan']);
            $sheet->setCellValue('C' . $row, $item['tanggal_pesan']);
            $sheet->setCellValue('D' . $row, strtoupper($item['no_model']));
            $sheet->setCellValue('E' . $row, strtoupper($item['item_type']));
            $sheet->setCellValue('F' . $row, strtoupper($item['color']));
            $sheet->setCellValue('G' . $row, strtoupper($item['kode_warna']));
            $sheet->setCellValue('H' . $row, '');
            $sheet->setCellValue('I' . $row, $item['ttl_jl_mc']);
            $sheet->setCellValue('J' . $row, $item['ttl_kg']);
            $sheet->setCellValue('K' . $row, $item['ttl_cns']);
            $sheet->setCellValue('L' . $row, '');
            $sheet->setCellValue('M' . $row, '');
            $sheet->setCellValue('N' . $row, '');
            $sheet->setCellValue('O' . $row, '');
            $row++;
        }
        // Border
        $lastRow = $row - 1;
        $sheet->getStyle("A4:O{$lastRow}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A4:O{$lastRow}")
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle("A3:O{$lastRow}")->applyFromArray($styleArray);
        // manual COLUMN DIMENSION
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(8);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(8);
        $sheet->getColumnDimension('J')->setWidth(8);
        $sheet->getColumnDimension('K')->setWidth(8);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(10);


        // // footer FOR_KK_369/TGL_REV_13_07_20/REV_02/HAL1/2
        // $sheet->mergeCells('A' . ($lastRow + 2) . ':O' . ($lastRow + 2));
        // $sheet->setCellValue('A' . ($lastRow + 2), 'FOR_KK_369/TGL_REV_13_07_20/REV_02/HAL 1/2');
        // $sheet->getStyle('A' . ($lastRow + 2))->getFont()->setBold(true)->setSize(8);
        // $sheet->getStyle('A' . ($lastRow + 2))->getAlignment()
        //     ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // wraptext form D4
        $sheet->getStyle('D4:O' . $lastRow)->getAlignment()->setWrapText(true);
        // Download
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            // optionally fit to width
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        // 2) Put your “FOR_KK_369/…/HAL x/y” in the center footer, with &P = current page, &N = total pages
        $footerText = 'FOR_KK_369/TGL_REV_13_07_20/REV_02/HAL &P/&N';
        $sheet->getHeaderFooter()
            ->setOddFooter('&C' . $footerText)
            ->setEvenFooter('&C' . $footerText);
        $filename = 'Report Permintaan Bahan Baku' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportReportBenang()
    {
        $tglAwal = $this->request->getGet('tanggal_awal');
        $tglAkhir = $this->request->getGet('tanggal_akhir');
        if (empty($tglAwal) && empty($tglAkhir)) {
            $bulan = $this->request->getGet('bulan');
            if (empty($bulan) || ! preg_match('/^\d{4}\-\d{2}$/', $bulan)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['error' => 'Parameter “bulan” harus dalam format YYYY-MM']);
            }

            $timestamp     = strtotime($bulan . '-01');
            $tglAwal   = date('Y-m-01', $timestamp);
            $tglAkhir  = date('Y-m-t', $timestamp);
        }

        $data = $this->pemasukanModel->getFilterBenang($tglAwal, $tglAkhir);
        $tanggal = $data[0]['tgl_input'];
        $date = new DateTime($tanggal);
        $angkaBulan = (int) $date->format('m');
        $angkaTahun = (int) $date->format('Y');

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $bulan = $namaBulan[$angkaBulan] . ' ' . $angkaTahun;

        $groups = [
            'COTTON' => [],
            'ACRYLIC' => [],
            'SPUN POLYESTER'   => [],
            'COTTON X LUREX'   => [],
            'ACRYLIC X LUREX'   => [],
            'Surat Jalan Tidak Masuk'   => [], //Misty
        ];

        foreach ($data as $row) {
            $it = strtoupper($row['item_type']);        // bahan_baku.jenis
            $sj = strtoupper($row['no_surat_jalan']);   // datang.no_suratjalan

            // 1) Surat Jalan Tidak Masuk
            // jenis BUKAN Acrylic, BUKAN Lurex; SJ tidak diawali KWS, bukan '', bukan SL*, bukan SC*
            if (
                stripos($it, 'ACRYLIC') === false
                && stripos($it, 'LUREX') === false
                && stripos($sj, 'KWS') !== 0
                && $sj !== ''
                && stripos($sj, 'SL') !== 0
                && stripos($sj, 'SC') !== 0
            ) {
                $groups['Surat Jalan Tidak Masuk'][] = $row;
                continue;
            }

            // 2) Cotton
            // bukan Lurex, bukan Polyester, bukan Spun, bukan ACR; SJ diawali KWS atau ''
            if (
                stripos($it, 'LUREX') === false
                && stripos($it, 'POLYESTER') === false
                && stripos($it, 'SPUN') === false
                && stripos($it, 'ACR') === false
                && (stripos($sj, 'KWS') === 0 || $sj === '')
            ) {
                $groups['COTTON'][] = $row;
                continue;
            }

            // 3) Acrylic
            // mengandung ACR; bukan Spun, bukan Polyester, bukan pola Lurex-Acr/Lurex-Acrylic
            if (
                stripos($it, 'ACR') !== false
                && stripos($sj, 'KWS') !== false
                && stripos($it, 'SPUN') === false
                && stripos($it, 'POLYESTER') === false
                && stripos($it, 'LUREX ACR') === false
                && stripos($it, 'ACRYLIC LUREX') === false
                && stripos($it, 'ACR LUREX') === false
            ) {
                $groups['ACRYLIC'][] = $row;
                continue;
            }

            // 4) Spun Polyester
            // bukan ACR, bukan Lurex; mengandung SPUN atau POLYESTER; SJ diawali KWS atau ''
            if (
                stripos($it, 'ACR') === false
                && stripos($it, 'LUREX') === false
                && (stripos($it, 'SPUN') !== false || stripos($it, 'POLYESTER') !== false)
                && (stripos($sj, 'KWS') === 0 || $sj === '')
            ) {
                $groups['SPUN POLYESTER'][] = $row;
                continue;
            }

            // 5) Cotton X Lurex
            // bukan ACR; mengandung LUREX
            if (
                stripos($it, 'ACR') === false
                && stripos($it, 'LUREX') !== false
            ) {
                $groups['COTTON X LUREX'][] = $row;
                continue;
            }

            // 6) Acrylic X Lurex
            // mengandung "ACRYLIC LUREX" atau "LUREX ACR"; SJ diawali KWS atau '' atau mengandung LRX
            if (
                (stripos($it, 'LUREX') !== false
                    || stripos($it, 'LUREX ACR') !== false)
                && (
                    stripos($sj, 'KWS') === 0
                    || $sj === ''
                    || stripos($sj, 'LRX') !== false
                )
            ) {
                $groups['ACRYLIC X LUREX'][] = $row;
                continue;
            }

            // kalau tidak masuk salah satu, bisa skip atau taruh di default
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $firstSheet = true;
        foreach ($groups as $title => $rows) {
            if ($firstSheet) {
                $sheet = $spreadsheet->getActiveSheet();
                $firstSheet = false;
            } else {
                $sheet = $spreadsheet->createSheet();
            }
            $sheet->setTitle($title);
            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
            $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

            // Header Form
            $sheet->mergeCells('A1:D2');
            $sheet->getColumnDimension('A')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(15);

            // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            // $drawing->setName('Logo');
            // $drawing->setDescription('Logo Perusahaan');
            // $drawing->setPath('assets/img/logo-kahatex.png');
            // $drawing->setCoordinates('C1');
            // $drawing->setHeight(25);
            // $drawing->setOffsetX(55);
            // $drawing->setOffsetY(10);
            // $drawing->setWorksheet($sheet);
            $sheet->mergeCells('A3:D3');
            $sheet->setCellValue('A3', 'PT. KAHATEX');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            //Merge
            $sheet->mergeCells('E1:O1');
            $sheet->mergeCells('E2:O2');
            $sheet->mergeCells('E3:O3');
            $sheet->mergeCells('E4:O4');
            $sheet->mergeCells('A4:D4');
            $sheet->mergeCells('B5:O5');

            $sheet->setCellValue('E1', 'FORMULIR');
            $sheet->setCellValue('E2', 'DEPARTEMENT KAOS KAKI');
            $sheet->setCellValue('E3', 'REKAP PENERIMAAN BENANG KAOS KAKI DARI DEPARTEMEN (KELOS WARNA)');
            $sheet->setCellValue('A4', 'No. Dokumen');
            $sheet->setCellValue('A5', 'Bulan : ');
            $sheet->setCellValue('B5', $bulan);

            // Buat header dengan sub-header
            $sheet->mergeCells('A6:A7');  // NO
            $sheet->mergeCells('B6:B7');  // NO SJ
            $sheet->mergeCells('C6:C7');  // TANGGAL SJ
            $sheet->mergeCells('D6:D7');  // TANGGAL PENERIMAAN
            $sheet->mergeCells('E6:E7');  // JENIS BARANG
            $sheet->mergeCells('F6:F7');  // KODE BENANG
            $sheet->mergeCells('G6:G7');  // WARNA
            $sheet->mergeCells('H6:H7');  // KODE WARNA
            $sheet->mergeCells('I6:I7');  // L/M/D
            $sheet->mergeCells('J6:J7');  // CONES
            $sheet->mergeCells('M6:M7');  // HARGA PER KG (USD)
            $sheet->mergeCells('N6:N7');  // TOTAL (USD)
            $sheet->mergeCells('O6:O7');  // KETERANGAN
            $sheet->mergeCells('P6:P7');  // DETAIL SJ
            $sheet->mergeCells('Q6:Q7');  // KELOMPOK
            $sheet->mergeCells('R6:R7');  // UKURAN
            $sheet->mergeCells('S6:S7');  // WARNA DASAR
            $sheet->mergeCells('T6:T7');  // NW

            $sheet->setCellValue('A6', 'NO');
            $sheet->setCellValue('B6', 'NO SJ');
            $sheet->setCellValue('C6', 'TANGGAL SJ');
            $sheet->setCellValue('D6', 'TANGGAL PENERIMAAN');
            $sheet->setCellValue('E6', 'JENIS BARANG');
            $sheet->setCellValue('F6', 'KODE BENANG');
            $sheet->setCellValue('G6', 'WARNA');
            $sheet->setCellValue('H6', 'KODE WARNA');
            $sheet->setCellValue('I6', 'L/M/D');
            $sheet->setCellValue('J6', 'CONES');
            $sheet->setCellValue('M6', 'HARGA PER KG (USD)');
            $sheet->setCellValue('N6', 'TOTAL (USD)');
            $sheet->setCellValue('O6', 'KETERANGAN');
            $sheet->setCellValue('P6', 'DETAIL SJ');
            $sheet->setCellValue('Q6', 'KELOMPOK');
            $sheet->setCellValue('R6', 'UKURAN');
            $sheet->setCellValue('S6', 'WARNA DASAR');
            $sheet->setCellValue('T6', 'NW');

            // Stock Awal: Header + Sub-header
            $sheet->mergeCells('K6:L6'); // STOCK AWAL
            $sheet->setCellValue('K6', 'QTY (KG)');
            $sheet->setCellValue('K7', 'GW');
            $sheet->setCellValue('L7', 'NW');

            // Format semua header
            $sheet->getStyle('A6:O6')->getFont()->setBold(true);
            $sheet->getStyle('A6:O6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A6:O6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A6:O6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A6:O6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A1:O5")->applyFromArray($styleArray);
            $sheet->getStyle('E1:O3')->getFont()->setSize(16);

            $sheet->getStyle('A1:O4')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Data
            $row = 8;
            $no = 1;
            $groupTanggal = [];
            foreach ($rows as $item) {
                $tgl = $item['tgl_masuk'];
                $groupTanggal[$tgl][] = $item;
            }

            foreach ($groupTanggal as $tgl => $items) {
                $subtotal = ['cones' => 0, 'gw' => 0, 'kgs_kirim' => 0, 'usd' => 0];

                foreach ($items as $item) {
                    $kgsKirim = $item['kgs_kirim'];
                    $harga = $item['harga'];
                    $totalUsd = $kgsKirim * $harga;

                    $tgl = $item['tgl_masuk'];
                    $cones = (float)$item['cones'];
                    $gw    = (float)$item['gw'];
                    $kgs_kirim    = (float)$item['kgs_kirim'];
                    $usd   = $kgs_kirim * (float)$item['harga'];
                    $warnaDasar = $item['warna_dasar'] ?? null;

                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $item['no_surat_jalan']);
                    $sheet->setCellValue('C' . $row, $item['tgl_masuk']);
                    $sheet->setCellValue('D' . $row, $item['tgl_input']);
                    $sheet->setCellValue('E' . $row, $item['item_type']);
                    $sheet->setCellValue('F' . $row, $item['ukuran']);
                    if ($title === 'COTTON') {
                        $sheet->setCellValue('G' . $row, $item['warna'] . ' ' . ($item['kode_warna'] ?? ''));
                    } else {
                        $sheet->setCellValue('G' . $row, $item['warna']);
                    }
                    $sheet->setCellValue('H' . $row, $item['kode_warna'] ?? '');
                    $sheet->setCellValue('I' . $row, $item['l_m_d']);
                    $sheet->setCellValue('J' . $row, $item['cones'] ?? 0);
                    $sheet->setCellValue('K' . $row, number_format($item['gw'], 2));
                    $sheet->setCellValue('L' . $row, number_format($kgsKirim, 2));
                    $sheet->setCellValue('M' . $row, number_format($harga, 2));
                    $sheet->setCellValue('N' . $row, number_format($totalUsd, 2));
                    $sheet->setCellValue('O' . $row, ''); // Keterangan
                    $sheet->setCellValue('P' . $row, $item['detail_sj']);
                    $sheet->setCellValue('Q' . $row, $item['jenis']);
                    $sheet->setCellValue('R' . $row, $item['ukuran'] ?? '');
                    if ($warnaDasar === null || $warnaDasar === 'Kode warna belum ada di database') {
                        $sheet->setCellValue('S' . $row, 'Kode Warna Tidak Ada di Database');
                        $sheet->getStyle('S' . $row)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                    } else {
                        $sheet->setCellValue('S' . $row, $warnaDasar);
                    }
                    $sheet->setCellValue('T' . $row, $kgsKirim ?? 0);
                    $row++;

                    // Hitung subtotal
                    $subtotal['cones'] += $cones;
                    $subtotal['gw'] += $gw;
                    $subtotal['kgs_kirim'] += $kgs_kirim;
                    $subtotal['usd'] += $usd;
                }

                // Tulis total setelah data tanggal itu
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue("A{$row}", "TOTAL");
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue("J{$row}", $subtotal['cones']);
                $sheet->setCellValue("K{$row}", number_format($subtotal['gw'], 2));
                $sheet->setCellValue("L{$row}", number_format($subtotal['kgs_kirim'], 2));
                $sheet->setCellValue("N{$row}", number_format($subtotal['usd'], 2));
                $row++;
            }

            // Border
            $lastRow = $row - 1;
            $sheet->getStyle("A6:T{$lastRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A6:T{$lastRow}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle("A6:O{$lastRow}")->applyFromArray($styleArray);

            // Auto-size
            foreach (range('A', 'T') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Download
        if ($this->request->getGet('tanggal_awal') && $this->request->getGet('tanggal_akhir')) {
            $filename = 'Report Benang Mingguan ' . $tglAwal . ' - ' . $tglAkhir . '.xlsx';
        } else {
            $filename = 'Report Benang Bulan ' . $bulan . '.xlsx';
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
