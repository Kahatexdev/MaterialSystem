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
    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->outCelupModel = new OutCelupModel();


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
        $models = $this->materialModel->getMaterialForPPH($area, $model);
        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
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


                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'      => $items['kode_warna'],
                    'color'      => $items['color'],
                    'gw'         => $items['gw'],
                    'composition' => $items['composition'],
                    'jarum'      => $data['machinetypeid'] ?? null,
                    'bruto'      => $bruto,
                    'qty'        => $data['qty'] ?? 0,
                    'sisa'       => $data['sisa'] ?? 0,
                    'po_plus'    => $data['po_plus'] ?? 0,
                    'bs_setting' => $data['bs_setting'] ?? 0,
                    'bs_mesin'   => $bs_mesin,
                    'pph'        => (($bruto * $gwpcs) + $bs_mesin) / 1000,
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

        foreach ($pphInisial as $item) {
            $key = $item['item_type'] . '-' . $item['color'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'item_type' => null,
                    'kode_warna' => null,
                    'warna' => null,
                    'pph' => 0,
                    'jarum' => null,
                    'area' => null
                ];
            }

            // Akumulasi total qty, sisa, bruto, bs_setting, dan bs_mesin
            $result['qty'] += $item['qty'];
            $result['sisa'] += $item['sisa'];
            $result['bruto'] += $item['bruto'];
            $result['bs_setting'] += $item['bs_setting'];
            $result['bs_mesin'] += $item['bs_mesin'];

            // Simpan detail per type-color
            $result[$key]['item_type'] = $item['item_type'];
            $result[$key]['kode_warna'] = $item['kode_warna'];
            $result[$key]['warna'] = $item['color'];
            $result[$key]['pph'] += $item['pph'];
            $result[$key]['jarum'] = $item['jarum'];
            $result[$key]['area'] = $item['area'];
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
        $sheet->setCellValue('E' . $row_header, 'PPH');

        $sheet->getStyle('A' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('B' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('C' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('D' . $row_header)->applyFromArray($styleHeader);
        $sheet->getStyle('E' . $row_header)->applyFromArray($styleHeader);

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
            $sheet->setCellValue('E' . $row, number_format($data['pph'], 2));

            // style body
            $columns = ['A', 'B', 'C', 'D', 'E'];

            foreach ($columns as $column) {
                $sheet->getStyle($column . $row)->applyFromArray($styleBody);
            }
            
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $column) {
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
        $models = $this->materialModel->getMaterialForPPH($area, $model);
        $pphInisial = [];

        foreach ($models as $items) {
            $styleSize = $items['style_size'];
            $gw = $items['gw'];
            $comp = $items['composition'];
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
                $pph = (($bruto * $gwpcs) + $bs_mesin) / 1000;


                $pphInisial[] = [
                    'area'  => $items['area'],
                    'style_size'  => $items['style_size'],
                    'inisial'  => $data['inisial'],
                    'item_type'  => $items['item_type'],
                    'kode_warna'  => $items['kode_warna'],
                    'color'      => $items['color'],
                    'ttl_kebutuhan' => $items['ttl_kebutuhan'],
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
                    'pph_persen' => ($pph / $items['ttl_kebutuhan'])*100,
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
            $sheet->setCellValue('Q' . $row, number_format($data['pph_persen'], 2));

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
                    $pph = (($bruto * $gwpcs) + $bs_mesin) / 1000;

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
}
