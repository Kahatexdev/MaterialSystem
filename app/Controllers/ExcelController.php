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
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Data Header
        $sheet->setCellValue('A2', 'Area');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $sheet->setCellValue('A3', 'Qty');
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('A4', 'Sisa');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B2', ': ' . $area);
        $sheet->getStyle('B2')->getFont();
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B3', ': ' . $result['qty']);
        $sheet->getStyle('B3')->getFont();
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B4', ': ' . $result['sisa']);
        $sheet->getStyle('B4')->getFont();
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D2', 'Produksi');
        $sheet->getStyle('D2')->getFont()->setBold(true);
        $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D3', 'Bs Setting');
        $sheet->getStyle('D3')->getFont()->setBold(true);
        $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('D4', 'Bs Mesin');
        $sheet->getStyle('D4')->getFont()->setBold(true);
        $sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E2', ': ' . $result['bruto']);
        $sheet->getStyle('E2')->getFont();
        $sheet->getStyle('E2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E3', ': ' . $result['bs_setting']);
        $sheet->getStyle('E3')->getFont();
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('E4', ': ' . $result['bs_mesin']);
        $sheet->getStyle('E4')->getFont();
        $sheet->getStyle('E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $row_header = 5;

        $sheet->setCellValue('A' . $row_header, 'No');
        $sheet->setCellValue('B' . $row_header, 'Jenis');
        $sheet->setCellValue('C' . $row_header, 'Kode Warna');
        $sheet->setCellValue('D' . $row_header, 'Warna');
        $sheet->setCellValue('E' . $row_header, 'PPH');

        // // Isi data
        $row = 5;
        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, $result['item_type']);
        $sheet->setCellValue('C' . $row, $result['kode_warna']);
        $sheet->setCellValue('D' . $row, $result['warna']);
        $sheet->setCellValue('E' . $row, $result['pph']);

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
}
