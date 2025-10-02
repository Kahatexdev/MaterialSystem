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
use App\Models\OtherBonModel;
use App\Models\WarehouseBBModel;
use FPDF;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\PemesananSpandexKaretModel;
use App\Models\CoveringStockModel;
use App\Models\PemesananModel;
use PhpOffice\PhpSpreadsheet\Style\{Border, Alignment, Fill};
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpParser\Node\Stmt\Else_;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use App\Models\PengeluaranModel;

class PdfController extends BaseController
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
    protected $otherBonModel;
    protected $pemesananSpandexKaretModel;
    protected $coveringStockModel;
    protected $warehouseBBModel;
    protected $pemesananModel;
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->bonCelupModel = new BonCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->otherBonModel = new OtherBonModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->coveringStockModel = new CoveringStockModel();
        $this->warehouseBBModel = new WarehouseBBModel();
        $this->pemesananModel = new PemesananModel();
        $this->pengeluaranModel = new PengeluaranModel();

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
    public function generateOpenPO($no_model)
    {
        function fetchApiData($url)
        {
            try {
                $response = file_get_contents($url);
                if ($response === false) {
                    throw new \Exception("Error fetching data from $url");
                }
                $data = json_decode($response, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Invalid JSON response from $url");
                }
                return $data;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return null;
            }
        }

        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');
        $season = $this->request->getGet('season');
        $poPlus = $this->request->getGet('po_plus');
        $materialType = $this->request->getGet('material_type');
        $newDel = $this->request->getGet('delivery');


        if ($poPlus == 'TIDAK') {
            $result = $this->openPoModel->getDataPo($no_model, $jenis, $jenis2);
        } else {
            $result = $this->openPoModel->getDataPoPlus($no_model, $jenis, $jenis2);
        }
        // dd($result);
        $noModel =  $result[0]['no_model'] ?? '';

        $buyerApiUrl = 'http://172.23.44.14/CapacityApps/public/api/getDataBuyer?no_model=' . urlencode($noModel);

        $buyerName = fetchApiData($buyerApiUrl);
        // dd($buyerName);
        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        $unit = $this->masterOrderModel->getUnit($no_model);

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 7);  // KITA MATIKAN AUTO PAGE BREAK
        $pdf->AddPage();


        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        $rawUnit = $unit['unit'];
        $rawUnit = strtoupper(trim($rawUnit));

        $pemesanan = 'KAOS KAKI';
        if ($rawUnit === 'MAJALAYA') {
            $pemesanan .= ' / ' . $rawUnit;
        }

        // CETAK HEADER halaman pertama
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $no_model, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $no_model, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $no_model, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . $pemesanan, 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");

        $startYAfterHeader = $pdf->GetY();  // posisi Y tepat setelah header
        // $pdf->SetY($startYAfterHeader);
        $pdf->SetY($pdf->GetY());

        // == START ISI TABEL ==
        $pdf->SetFont('Arial', '', 6);
        $no                 = 1;
        $yLimit             = 145;
        $lineHeight         = 3;      // tinggi dasar setiap baris MultiCell

        // Total tinggi baris untuk memastikan 15 baris jika perlu kosong
        $totalKg                = 0;
        $totalPermintaanCones   = 0;
        $totalYard              = 0;
        $totalCones             = 0;

        $prevDelivery = '';
        $prevBuyer    = '';
        $prevNoOrder  = '';

        foreach ($result as $row) {
            // dd($row['spesifikasi_benang']);
            // 1. tentukan text yang mau ditampilkan, atau kosong jika sama dgn sebelumnya
            $delivery = $newDel ?? '';
            if ($delivery === $prevDelivery) {
                $displayDelivery = '';
            } else {
                $displayDelivery = $delivery;
                $prevDelivery    = $delivery;
            }

            $buyer = ($row['buyer'] ?? '') . ' (' . $buyerName['kd_buyer_order'] . ')';
            if ($buyer === $prevBuyer) {
                $displayBuyer = '';
            } else {
                $displayBuyer = $buyer;
                $prevBuyer    = $buyer;
            }

            $noOrder = $row['no_order'] ?? '';
            if ($noOrder === $prevNoOrder) {
                $displayNoOrder = '';
            } else {
                $displayNoOrder = $noOrder;
                $prevNoOrder    = $noOrder;
            }

            // Cek dulu apakah nambah baris ini bakal lewat batas
            if ($pdf->GetY() + $lineHeight > $yLimit) {
                $this->generateFooterOpenPO($pdf, $tujuan, $result, $penerima);
                $pdf->AddPage();
                $this->generateHeaderOpenPO($pdf, $result, $no_model, $pemesanan);

                // Gambar ulang margin & header halaman
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2); // Lebih tebal
                $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam
                $pdf->SetLineWidth(0.1); // Lebih tipis
                $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

                $pdf->SetFont('Arial', '', 6);
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // 1. SIMULASI: Hitung tinggi maksimum yang dibutuhkan
            $heights = [];
            $tempX = $startX;

            $pdf->SetTextColor(255, 255, 255); // putih agar tidak terlihat

            $multiCellData = [
                ['w' => 25, 'text' => $row['spesifikasi_benang']
                    ? $row['item_type'] . ' (' . $row['spesifikasi_benang'] . ')'
                    : $row['item_type']],
                ['w' => 12, 'text' => $row['ukuran']],
                ['w' => 12, 'text' => $row['bentuk_celup']],
                ['w' => 20, 'text' => $row['color']],
                ['w' => 20, 'text' => $row['kode_warna']],
                ['w' => 20, 'text' => $row['buyer'] ? $row['buyer'] . '(' . $buyerName['kd_buyer_order'] . ')' : $buyerName['kd_buyer_order']],
                // ['w' => 20, 'text' => $row['buyer']],
                ['w' => 20, 'text' => $displayNoOrder],
                ['w' => 22, 'text' => $row['jenis_produksi']],
                ['w' => 22, 'text' => $row['contoh_warna']],
                ['w' => 22, 'text' => $row['ket_celup']],
            ];

            // 1. Tentukan lineHeight: 5 pt jika semuanya muat satu baris, 3 pt kalau ada yg meluber
            $singleLine = true;
            foreach ($multiCellData as $cell) {
                // GetStringWidth menghasilkan lebar dalam satuan point
                if ($pdf->GetStringWidth($cell['text']) > $cell['w']) {
                    $singleLine = false;
                    break;
                }
            }
            $lineHeight = $singleLine ? 5 : 3;

            foreach ($multiCellData as $data) {
                $pdf->SetXY($tempX, $startY);
                $y0 = $pdf->GetY();
                $pdf->MultiCell($data['w'], $lineHeight, $data['text'], 0, 'C');
                $heights[] = $pdf->GetY() - $y0;
                $tempX += $data['w'];
            }
            // dd($data);
            $pdf->SetTextColor(0, 0, 0); // kembali ke hitam
            $maxHeight = max($heights);

            // 2. RENDER: Gambar semua cell dengan tinggi yang sama
            $pdf->SetXY($startX, $startY);

            // Buat semua cell dengan border dan tinggi yang sama, tapi isi kosong dulu
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            $cellData = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $displayDelivery,
                number_format($row['kg_po'], 2) ?? 0,
                number_format($row['kg_percones'], 2) ?? 0,
                '',
                $row['jumlah_cones'] ?? 0,
                '',
                '',
                '',
                ''
            ];

            // Gambar semua cell dengan border
            for ($i = 0; $i < count($cellWidths); $i++) {
                $pdf->Cell($cellWidths[$i], $maxHeight, $cellData[$i], 1, 0, 'C');
            }

            // 3. ISI TEXT untuk kolom multiCell (overlay tanpa border)
            $currentX = $startX;

            // No
            $textCenterY = $startY + ($maxHeight / 2) - ($lineHeight / 2);
            $pdf->SetXY($currentX, $textCenterY);
            $pdf->Cell(6, $lineHeight, $no, 0, 0, 'C');
            $currentX += 6;

            // Item Type (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . $row['spesifikasi_benang'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . ' ' . $row['spesifikasi_benang'], 0, 'C');
            $currentX += 25;

            // Ukuran (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $currentX += 12;

            // Bentuk (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $currentX += 12;

            // Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $currentX += 20;

            // Kode Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $currentX += 20;

            // Buyer (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['buyer'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight,  $row['buyer'] ? $row['buyer'] . '(' . $buyerName['kd_buyer_order'] . ')' : $buyerName['kd_buyer_order'], 0, 'C');
            $currentX += 20;

            // No Order (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $displayNoOrder, 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $displayNoOrder, 0, 'C');
            $currentX += 20;

            // Skip kolom yang sudah terisi dengan Cell biasa
            $currentX += 16 + 15 + 13 + 13 + 13 + 13;

            // Untuk Produksi (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $currentX += 22;

            // Contoh Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $currentX += 22;

            // Keterangan (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');

            // Pindah ke baris berikutnya
            $pdf->SetY($startY + $maxHeight);

            $no++;

            $totalKg              += floatval($row['kg_po'] ?? 0);
            $totalPermintaanCones += floatval($row['kg_percones'] ?? 0);
            $totalCones           += floatval($row['jumlah_cones'] ?? 0);
        }

        $currentY = $pdf->GetY();
        $footerY = 145; // batas sebelum footer (tergantung desain kamu)

        // Tinggi standar baris kosong (bisa sesuaikan ke $maxHeight rata-rata atau tetap 6 misal)
        $emptyRowHeight = 5;

        // Selama posisi Y masih di atas footer, tambahkan baris kosong
        while ($currentY + $emptyRowHeight < $footerY) {
            $startX = $pdf->GetX();
            $pdf->SetXY($startX, $currentY);

            // Gambar semua cell border kosong
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            foreach ($cellWidths as $width) {
                $pdf->Cell($width, $emptyRowHeight, '', 1, 0, 'C');
            }
            $pdf->Ln($emptyRowHeight);

            $currentY = $pdf->GetY();
        }

        //  Baris TOTAL (baris ke-16)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(6 + 25 + 12 + 17 + 20 + 20 + 10 + 25 + 16, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(15, 6, number_format($totalKg, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalPermintaanCones, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalYard, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalCones), 1, 0, 'C');
        $pdf->Cell(13, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 1, 'C');


        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        $pdf->SetFillColor(255, 255, 255); // Atur warna latar belakang menjadi putih
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->MultiCell(117, 5, ': ' . $result[0]['keterangan'], 0, 1, 'L');
        } else {
            $pdf->MultiCell(117, 5, ': ', 0, 1, 'L');
        }

        // FOOTER
        $this->generateFooterOpenPO($pdf, $tujuan, $result, $penerima);

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function generateHeaderOpenPO($pdf, $result, $no_model, $pemesanan)
    {
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $no_model, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $no_model, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $no_model, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . $pemesanan, 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");
        $pdf->SetY(53);
    }

    private function generateFooterOpenPO($pdf, $tujuan, $result, $penerima)
    {
        // konfigurasi
        $bottomMargin = 7;      // harus sama dengan SetAutoPageBreak
        $footerHeight = 35;     // tinggi area footer secara keseluruhan

        // hitung Y = (tinggi halaman) - margin bawah - footer block
        $y = $pdf->GetPageHeight() - $bottomMargin - $footerHeight;
        $pdf->SetY($y);
        $pdf->SetFont('Arial', '', 6);

        // Baris kosong
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // Judul departemen
        $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        // Judul kolom tanda tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        // Spasi untuk tanda tangan
        $pdf->Cell(55, 12, '', 0, 1, 'C');

        $admin = $result[0]['admin'] ?? '';

        // Garis tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(       ' . $admin . '       )', 0, 0, 'C');
        if (!empty($result)) {
            $pdf->Cell(55, 5, '(       ' . $result[0]['penanggung_jawab'] . '      )', 0, 0, 'C');
        } else {
            $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . $penerima . '       )', 0, 1, 'C');
    }

    public function printBon($idBon)
    {
        $username = session()->get('username');
        // data ALL BON
        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon

        // Mengelompokkan data detailBon berdasarkan no_model, item_type, dan kode_warna
        $groupedDetails = [];
        foreach ($detailBon as $detail) {
            $key = $detail['no_model'] . '|' . $detail['item_type'] . '|' . $detail['kode_warna'];
            $gantiRetur = ($detail['ganti_retur'] == 1) ? ' / GANTI RETUR' : '';
            if (!isset($groupedDetails[$key])) {
                $groupedDetails[$key] = [
                    'no_model' => $detail['no_model'],
                    'item_type' => $detail['item_type'],
                    'spesifikasi_benang' => $detail['spesifikasi_benang'],
                    'kode_warna' => $detail['kode_warna'],
                    'warna' => $detail['warna'],
                    'buyer' => $detail['buyer'],
                    'ukuran' => $detail['ukuran'],
                    'lot_kirim' => $detail['lot_kirim'],
                    'l_m_d' => $detail['l_m_d'],
                    'harga' => $detail['harga'],
                    'detailPengiriman' =>  [],
                    'totals' => [
                        'cones_kirim' => 0,
                        'gw_kirim' => 0,
                        'kgs_kirim' => 0,
                    ],
                    'ganti_retur' => $gantiRetur,
                    'jmlKarung' => 0,
                    'barcodes' => [], // Untuk menyimpan barcode
                ];
            }
            // Menambahkan data pengiriman untuk grup ini tanpa dijumlahkan
            $groupedDetails[$key]['detailPengiriman'][] = [
                'id_out_celup' => $detail['id_out_celup'],
                'cones_kirim' => $detail['cones_kirim'],
                'gw_kirim' => $detail['gw_kirim'],
                'kgs_kirim' => $detail['kgs_kirim'],
                'lot_kirim' => $detail['lot_kirim'],
                'no_karung' => $detail['no_karung'],
            ];
            // Menambahkan nilai ke total
            $groupedDetails[$key]['totals']['gw_kirim'] += $detail['gw_kirim'];
            $groupedDetails[$key]['totals']['kgs_kirim'] += $detail['kgs_kirim'];
            $groupedDetails[$key]['totals']['cones_kirim'] += $detail['cones_kirim'];

            // Menghitung jumlah baris data detailBon pada grup ini (jumlah karung)
            $groupedDetails[$key]['jmlKarung'] = count($groupedDetails[$key]['detailPengiriman']);

            // Tambahkan ID outCelup
            $groupedDetails[$key]['idsOutCelup'][] = $detail['id_out_celup'];
        }

        // Buat instance Barcode Generator
        $generator = new BarcodeGeneratorPNG();

        // Hasilkan barcode untuk setiap ID outCelup di grup
        foreach ($groupedDetails as &$group) {
            foreach ($group['detailPengiriman'] as $outCelup => $id) {
                // Hasilkan barcode dan encode sebagai base64
                $id_out_celup = $id['id_out_celup'];
                $barcode = $generator->getBarcode($id_out_celup, $generator::TYPE_CODE_128);
                $group['barcodes'][] = [
                    'no_model' => $group['no_model'],
                    'item_type' => $group['item_type'],
                    'kode_warna' => $group['kode_warna'],
                    'warna' => $group['warna'],
                    'id_out_celup' => $id['id_out_celup'],
                    'gw' => $id['gw_kirim'],
                    'kgs' => $id['kgs_kirim'],
                    'cones' => $id['cones_kirim'],
                    'lot' => $id['lot_kirim'],
                    'no_karung' => $id['no_karung'],
                    'barcode' => base64_encode($barcode),
                ];
            }
        }

        // Menggabungkan data utama dan detail yang sudah dikelompokkan
        $dataBon['groupedDetails'] = array_values($groupedDetails);

        // dd($dataBon);

        $pdf = new FPDF('P', 'mm', 'A4');

        for ($b = 1; $b <= 3; $b++) {

            if ($b === 2) {
                $yBorder = 150;
                $yPosition = 151; // Posisi untuk bon kedua                
            } else {
                $yBorder = 3;
                $yPosition = 4; // Posisi dinamis untuk bon pertama dan ketiga
                $pdf->AddPage();
            }
            // Inisialisasi FPDF
            $pdf->SetAutoPageBreak(true, 5); // Atur margin bawah saat halaman penuh

            // Tambahkan border margin
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.4);
            $pdf->Rect(3, $yBorder, 204, 142);

            // Tambahkan double border margin
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.4);
            $pdf->Rect(4, $yPosition, 202, 140);

            // Kembalikan ke properti default untuk border
            $pdf->SetDrawColor(0, 0, 0); // Tetap hitam jika digunakan pada elemen lain
            $pdf->SetLineWidth(0.2);    // Kembali ke garis default
            $pdf->SetMargins(4, 4, 4, 4); // Margin kiri, atas, kanan
            $pdf->SetXY(4, $yPosition); // Mulai di margin kiri (X=5) dan sedikit di bawah border (Y=5)
            $pdf->SetAutoPageBreak(true,); // Aktifkan auto page break dengan margin bawah 10

            // Menambahkan gambar
            $pdf->Image('assets/img/logo-kahatex.png', 20, $yPosition + 1, 8, 7); // X=10 untuk margin, Y=10 untuk margin atas
            // Header
            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(40, 12, '', 1, 0, 'C');

            // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
            $pdf->SetFillColor(170, 255, 255);
            $pdf->Cell(162, 4, 'FORMULIR', 0, 1, 'C', 1);

            $pdf->SetFillColor(255, 255, 255); // Ubah latar belakang menjadi putih

            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(40, 4, '', 0, 0, 'L');
            $pdf->Cell(162, 4, 'DEPARTMEN KELOS WARNA', 0, 1, 'C');

            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(40, 4, 'PT. KAHATEX', 0, 0, 'C');

            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(162, 4, 'BON PENGIRIMAN', 1, 1, 'C');

            // Tabel Header Atas
            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell(40, 3, 'No. Dokumen', 1, 0, 'L');
            $pdf->Cell(107, 3, 'FOR-KWA-006/REV_03/HAL_1/1', 1, 0, 'L');
            $pdf->Cell(27, 3, 'Tanggal Revisi', 1, 0, 'L');
            $pdf->Cell(28, 3, '07 Januari 2021', 1, 1, 'L');

            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->Cell(40, 3, 'NAMA LANGGANAN', 0, 0, 'L');
            $pdf->Cell(61, 3, 'KAOS KAKI', 0, 0, 'L');
            $pdf->Cell(62, 3, 'NO SURAT JALAN : ' . $dataBon['no_surat_jalan'], 0, 0, 'L');
            $pdf->Cell(36, 3, 'TANGGAL : ' . date('d-m-Y', strtotime($dataBon['tgl_datang'])), 0, 1, 'L');

            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell(18, 8, 'NO PO', 1, 0, 'C');
            $pdf->Cell(22, 8, 'JENIS BENANG', 1, 0, 'C');
            // Menentukan posisi awal untuk KODE BENANG
            $xKB = $pdf->GetX();
            $yKB = $pdf->GetY();
            $pdf->MultiCell(11, 4, 'KODE BENANG', 1, 'C', false); // wrap text
            // Mengembalikan posisi setelah MultiCell
            $pdf->SetXY($xKB + 11, $yKB);

            $pdf->Cell(29, 8, 'KODE WARNA', 1, 0, 'C');
            $pdf->Cell(18, 8, 'WARNA', 1, 0, 'C');
            $pdf->Cell(13, 8, 'LOT CELUP', 1, 0, 'C');
            $pdf->Cell(7, 8, 'L/M/D', 1, 0, 'C');
            // Membagi kolom "HARGA" menjadi dua baris
            $xHarga = $pdf->GetX();
            $yHarga = $pdf->GetY();
            $pdf->Cell(9, 3, 'HARGA', 1, 2, 'C'); // Baris pertama (HARGA)
            $pdf->SetXY($xHarga, $yHarga  + 3);
            $pdf->Cell(9, 5, 'PER KG', 1, 0, 'C'); // Baris kedua (PER KG)

            // Kolom "CONES" dengan tinggi penuh sejajar kolom paling awal
            $pdf->SetXY($xHarga + 9, $yHarga); // Mengatur posisi kolom "CONES" kembali ke baris awal
            $pdf->Cell(8, 8, 'CONES', 1, 0, 'C');

            $xQty = $pdf->GetX();
            $yQty = $pdf->GetY();
            $pdf->Cell(18, 3, 'QTY', 1, 2, 'C');
            $pdf->SetXY($xQty, $yQty + 3);
            $xGw = $pdf->GetX();
            $yGw = $pdf->GetY();
            $pdf->MultiCell(9, 2.5, 'GW (KG)', 1, 'C', false); // wrap text
            // Mengembalikan posisi setelah MultiCell
            $pdf->SetXY($xGw + 9, $yGw);
            $pdf->MultiCell(9, 2.5, 'NW (KG)', 1, 'C', false); // wrap text
            // Mengembalikan posisi setelah MultiCell
            $pdf->SetXY($xGw + 9, $yGw);
            $pdf->SetXY($xQty + 18, $yQty);

            $xTotal = $pdf->GetX();
            $yTotal = $pdf->GetY();
            $pdf->Cell(27, 3, 'TOTAL', 1, 1, 'C');
            $pdf->SetXY($xTotal, $yTotal + 3);
            $pdf->Cell(9, 5, 'CONES', 1, 0, 'C');
            $xGw = $pdf->GetX();
            $yGw = $pdf->GetY();
            $pdf->MultiCell(9, 2.5, 'GW (KG)', 1, 'C', false); // wrap text
            // Mengembalikan posisi setelah MultiCell
            $pdf->SetXY($xGw + 9, $yGw);
            $pdf->MultiCell(9, 2.5, 'NW (KG)', 1, 'C', false); // wrap text
            // Mengembalikan posisi setelah MultiCell
            $pdf->SetXY($xGw + 9, $yGw);
            $pdf->SetXY($xQty + 18, $yQty);

            $pdf->SetXY($xTotal + 27, $yTotal);
            $pdf->Cell(22, 8, 'KETERANGAN', 1, 1, 'C');


            $counter = [];
            $missing = [];
            $prevNoModel = null; // Variabel untuk menyimpan no_model sebelumnya
            $prevItemType = null; // Variabel untuk menyimpan item_type sebelumnya
            $prevKodeWarna = null; // Variabel untuk menyimpan kode_warna sebelumnya
            $totalRows = 32; // Total baris yang diinginkan
            $row = 0;
            $currentRow = 0; // Variabel untuk menghitung jumlah baris yang sudah tercetak

            foreach ($dataBon['groupedDetails'] as $bon) {
                $getDeskripsi = $this->masterMaterialModel->where('item_type', $bon['item_type'])->select('deskripsi')->first();
                if (!$getDeskripsi || empty($getDeskripsi['deskripsi'] ?? '')) {
                    $missing[] = $bon['item_type'];
                }
                if (!empty($missing)) {
                    $msg = 'Tidak Ada Item Type: ' . implode(', ', $missing);
                    session()->setFlashdata('deskripsi_missing', $msg);

                    // hentikan proses selanjutnya dan kembalikan user
                    return redirect()->back()->with('error', $msg);
                }

                $pdf->SetFont('Arial', '', 6);
                // Mengelompokkan berdasarkan no_model, item_type, dan kode_warna
                $key = $bon['no_model'] . '_' . $bon['item_type'] . '_' . $bon['kode_warna'];

                // Jika kombinasi tersebut belum ada di array counter, buat entri baru
                if (!isset($counter[$key])) {
                    $counter[$key] = 0;
                }
                foreach ($bon['detailPengiriman'] as $detail) {
                    $counter[$key]++;
                }

                // $itemTypeAsli = $bon['item_type'];
                $deskripsiItemType = $getDeskripsi['deskripsi'];
                // dd($deskripsiItemType);
                // $deskripsiItemType = ;
                $ukuranBenang = strtoupper($bon['ukuran']);
                $itemTypeBaru = '';

                // Jika $ukuranBenang ada di $itemTypeAsli, hapus dan simpan hasilnya di $itemTypeBaru
                if (!empty($ukuranBenang) && strpos($deskripsiItemType, $ukuranBenang) !== false) {
                    $itemTypeBaru = trim(str_replace($ukuranBenang, '', $deskripsiItemType));
                } else {
                    $itemTypeBaru = $deskripsiItemType;
                }

                // Ambil isi dalam tanda kurung pertama (jika ada)
                $ketLipatan = '';
                if (preg_match('/\(([^)]*)\)/', $itemTypeBaru, $matches)) {
                    $ketLipatan = trim($matches[1]); // hasil "KHUSUS LIPATAN"
                }
                // Hapus teks di dalam tanda kurung (beserta kurungnya)
                $itemTypeBaru = preg_replace('/\([^)]*\)/', '', $itemTypeBaru);

                // Hapus kata "LIPATAN" (case insensitive)
                $itemTypeBaru = preg_replace('/\bLIPATAN\b/i', '', $itemTypeBaru);

                // Rapikan spasi ganda jadi satu
                $itemTypeBaru = trim(preg_replace('/\s+/', ' ', $itemTypeBaru));
                // dd($itemTypeBaru);
                // Mapping singkatan ke bentuk lengkap
                $mapItemType = [
                    'CTN' => 'COTTON',
                    'CD'  => 'COMBED',
                    'ORG' => 'ORGANIC',
                    'CB'  => 'COMBED',
                    'SPDX'  => 'SPANDEX'
                ];

                // Replace jika ada singkatan menjadi bentuk lengkap
                foreach ($mapItemType as $singkatan => $lengkap) {
                    $itemTypeBaru = preg_replace('/\b' . preg_quote($singkatan, '/') . '\b/i', $lengkap, $itemTypeBaru);
                }

                $ket = $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'] . (!empty($ketLipatan) ? ' / ' . $ketLipatan : "");

                // Hitung jumlah detail untuk grup saat ini
                $jmlDetail = count($bon['detailPengiriman']);
                // $jmlBaris = ($jmlDetail === 1) ? 3 : 2;

                $lineHeight = 3;

                // 1. Hitung kebutuhan baris tiap kolom
                $noModelWidth = 18; // lebar kolom no_model
                $itemTypeWidth = 22; // lebar kolom buyer
                $ketWidth = 22; // lebar kolom buyer

                // hitung panjang teks
                $noModelTextWidth = $pdf->GetStringWidth($bon['no_model']);
                $buyerTextWidth = $pdf->GetStringWidth($bon['buyer'] . ' KK');
                $itemTypeTextWidth   = $pdf->GetStringWidth($itemTypeBaru);
                $ketTextWidth   = $pdf->GetStringWidth($ket);

                // kira-kira 1 baris = lebarnya kolom
                $buyerLines   = ceil($buyerTextWidth / $itemTypeWidth);
                $noModelLines = ceil($noModelTextWidth / $noModelWidth);
                $itemTypeLines   = ceil($itemTypeTextWidth / $itemTypeWidth);
                $ketLines   = ceil($ketTextWidth / $ketWidth);

                // detail tetap dari jumlah data
                $detailLines  = count($bon['detailPengiriman']);

                // ambil baris paling banyak
                $maxLines = max(($noModelLines + $buyerLines), $itemTypeLines, $detailLines, $ketLines);

                // jumlah baris (ceil supaya dibulatkan ke atas)
                $noModelHeight = $noModelLines * $lineHeight;
                $buyerHeight = $buyerLines * $lineHeight;

                // Simpan posisi awal
                $x1 = $pdf->GetX();
                $y1 = $pdf->GetY();

                // Cetak No Model
                $pdf->MultiCell(18, 3, $bon['no_model'], 1, 'C', false);

                // Posisi untuk buyer (langsung di bawah No Model, jadi Y naik setinggi cell no_model)
                $pdf->SetXY($x1, $y1 + $noModelHeight);
                $pdf->MultiCell(18, 3, $bon['buyer'] . ' KK', 1, 'C', false);

                // Balikin posisi X,Y untuk lanjut ke kolom berikutnya
                $pdf->SetXY($x1 + 18, $y1);
                $x2 = $pdf->GetX();
                $y2 = $pdf->GetY();
                // MultiCell untuk kolom item_type (tinggi fleksibel)
                $pdf->MultiCell(22, 3, $itemTypeBaru . ' ' . $bon['spesifikasi_benang'], 1, 'C', false);
                // Kembalikan posisi untuk kolom berikutnya
                $pdf->SetXY($x2 + 22, $y2);

                // MultiCell untuk kolom ukuran (tinggi fleksibel)
                $x3 = $pdf->GetX();
                $pdf->MultiCell(11, 3, $bon['ukuran'], 1, 'C', false);
                // Kembalikan posisi untuk kolom berikutnya
                $pdf->SetXY($x3 + 11, $y2);

                // MultiCell untuk kolom kode warna (tinggi fleksibel)
                $x4 = $pdf->GetX();
                $pdf->MultiCell(29, 3, $bon['kode_warna'], 1, 'C', false);
                // Kembalikan posisi untuk kolom berikutnya
                $pdf->SetXY($x4 + 29, $y2);

                // MultiCell untuk kolom warna (tinggi fleksibel)
                $x5 = $pdf->GetX();
                $pdf->MultiCell(18, 3, $bon['warna'], 1, 'C', false);
                // Kembalikan posisi untuk kolom berikutnya
                $pdf->SetXY($x5 + 18, $y2);

                // MultiCell untuk kolom lot_kirim (tinggi fleksibel)
                $x6 = $pdf->GetX();
                $pdf->MultiCell(13, 3, $bon['lot_kirim'], 1, 'C', false);

                // // Hitung tinggi maksimum dari semua kolom
                // $maxHeight = max($multiCellHeight1, $multiCellHeight2, $multiCellHeight3, $multiCellHeight4, $multiCellHeight5, 8);

                // Kembalikan posisi untuk kolom berikutnya
                $pdf->SetXY($x6 + 13, $y2);
                $pdf->Cell(7, 3, $bon['l_m_d'], 1, 0, 'C');
                $pdf->Cell(9, 3, $bon['harga'], 1, 0, 'C');
                foreach ($bon['detailPengiriman'] as $detail) {
                    // var_dump($row);
                    // dd($detail);
                    $row++;
                    if ($counter[$key] == 1) {
                        $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, number_format((float)($detail['gw_kirim'] ?? 0), 2), 1, 0, 'C');
                        $pdf->Cell(9, 3, number_format((float)($detail['kgs_kirim'] ?? 0), 2), 1, 0, 'C');
                        $pdf->Cell(9, 3, $bon['totals']['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, number_format((float)($bon['totals']['gw_kirim'] ?? 0), 2), 1, 0, 'C');
                        $pdf->Cell(9, 3, number_format((float)($bon['totals']['kgs_kirim'] ?? 0), 2), 1, 0, 'C');
                        $xKet = $pdf->GetX();
                        $yKet = $pdf->GetY();
                        $pdf->MultiCell(22, 3, $ket, 1, 'L', false);
                        $pdf->SetY($yKet + 3); // Kembalikan posisi untuk kolom berikutnya
                        $currentRow++;
                        // baris baru
                    } else {
                        if ($row == 1) {
                            $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($detail['gw_kirim'] ?? 0), 2), 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($detail['kgs_kirim'] ?? 0), 2), 1, 0, 'C');
                            $pdf->Cell(9, 3, $bon['totals']['cones_kirim'], 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($bon['totals']['gw_kirim'] ?? 0), 2), 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($bon['totals']['kgs_kirim'] ?? 0), 2), 1, 0, 'C');
                            // MultiCell untuk 'jmlKarung' dan 'ganti_retur'
                            $xKet = $pdf->GetX();
                            $yKet = $pdf->GetY();
                            $pdf->MultiCell(22, 3, $ket, 1, 'L', false);

                            // Kembali ke posisi X untuk melanjutkan dari bawah MultiCell
                            $pdf->SetXY($xKet, $yKet + 3);
                            $currentRow++; // Update baris yang sudah dicetak
                        } else {
                            $pdf->SetX(4); // Pastikan posisi X sejajar margin

                            $pdf->Cell(18, 3, '', 1, 0, 'C');
                            $pdf->Cell(22, 3, '', 1, 0, 'C');
                            $pdf->Cell(11, 3, '', 1, 0, 'C');
                            $pdf->Cell(29, 3, '', 1, 0, 'C');
                            $pdf->Cell(18, 3, '', 1, 0, 'C');
                            $pdf->Cell(13, 3, '', 1, 0, 'C');
                            $pdf->Cell(7, 3, '', 1, 0, 'C');
                            $pdf->Cell(9, 3, '', 1, 0, 'C');
                            $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($detail['gw_kirim'] ?? 0), 2), 1, 0, 'C');
                            $pdf->Cell(9, 3, number_format((float)($detail['kgs_kirim'] ?? 0), 2), 1, 0, 'C');
                            $pdf->Cell(9, 3, '', 1, 0, 'C');
                            $pdf->Cell(9, 3, '', 1, 0, 'C');
                            $pdf->Cell(9, 3, '', 1, 0, 'C');
                            $pdf->Cell(22, 3, '', 1, 1, 'L');
                            $currentRow++; // Update baris yang sudah dicetak
                        }
                    }
                }
                // dd($maxLines);

                if ($row <= $maxLines) {
                    $emptyLines = $maxLines - $row;
                    for ($i = 0; $i < $emptyLines; $i++) {
                        $pdf->SetX(4); // Pastikan posisi X sejajar margin
                        $pdf->Cell(18, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 0, 'C');
                        $pdf->Cell(11, 3, '', 1, 0, 'C');
                        $pdf->Cell(29, 3, '', 1, 0, 'C');
                        $pdf->Cell(18, 3, '', 1, 0, 'C');
                        $pdf->Cell(13, 3, '', 1, 0, 'C');
                        $pdf->Cell(7, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(8, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 1, 'L');
                        $currentRow++;
                    }
                }


                if (
                    $prevNoModel === null || // artinya data pertama
                    ($bon['no_model'] !== $prevNoModel) ||
                    ($bon['item_type'] !== $prevItemType) ||
                    ($bon['kode_warna'] !== $prevKodeWarna)
                ) {
                    // Tentukan jumlah baris kosong yang ingin ditambahkan
                    for ($i = 0; $i < 2; $i++) {
                        $pdf->SetX(4);
                        // Cetak baris kosong dengan format sel yang sesuai
                        $pdf->Cell(18, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 0, 'C');
                        $pdf->Cell(11, 3, '', 1, 0, 'C');
                        $pdf->Cell(29, 3, '', 1, 0, 'C');
                        $pdf->Cell(18, 3, '', 1, 0, 'C');
                        $pdf->Cell(13, 3, '', 1, 0, 'C');
                        $pdf->Cell(7, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(8, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 1, 'L'); // Pindah ke baris baru
                        $currentRow++; // Update baris yang sudah dicetak
                    }
                    $row = 0; // Reset posisi baris saat ini
                }

                // Perbarui nilai sebelumnya
                $prevNoModel = $bon['no_model'];
                $prevItemType = $bon['item_type'];
                $prevKodeWarna = $bon['kode_warna'];
            }

            // Tambahkan baris kosong jika jumlah baris yang dicetak kurang dari 28
            while ($currentRow <= $totalRows) {
                $pdf->SetX(4);
                $pdf->Cell(18, 3, '', 1, 0, 'C');
                $pdf->Cell(22, 3, '', 1, 0, 'C');
                $pdf->Cell(11, 3, '', 1, 0, 'C');
                $pdf->Cell(29, 3, '', 1, 0, 'C');
                $pdf->Cell(18, 3, '', 1, 0, 'C');
                $pdf->Cell(13, 3, '', 1, 0, 'C');
                $pdf->Cell(7, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(8, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(9, 3, '', 1, 0, 'C');
                $pdf->Cell(22, 3, '', 1, 1, 'L');
                $currentRow++; // Update baris yang sudah dicetak
            }
            // Data keterangan
            $keterangan = [
                'KETERANGAN :' => 'GW = GROSS WEIGHT',
                '1' => 'NW = NET WEIGHT',
                '2' => 'L = LIGHT',
                '3' => 'M = MEDIUM',
                '4' => 'D = DARK',
            ];

            // Looping untuk mencetak kolom keterangan
            foreach ($keterangan as $key => $value) {
                $pdf->SetX(4); // Pastikan posisi X sejajar margin
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(18, 3, ($key == "KETERANGAN :") ? $key : '', 0, 0, 'L'); // Kolom pertama (key)
                $pdf->Cell(37, 3, $value, 0, 0, 'L'); // Kolom kedua (value)
                $pdf->Cell(72, 3, '', 0, 0, 'L'); // Kosong
                $pdf->Cell(17, 3, $key === 'KETERANGAN :' ? 'PENGIRIM' : ($key === 4 ? strtoupper($detailBon[0]['admin']) : ''), 0, 0, 'C');
                $pdf->Cell(23, 3, '', 0, 0, 'L'); // Kosong
                $pdf->Cell(17, 3, $key === 'KETERANGAN :' ? 'PENERIMA' : '', 0, 0, 'C'); // Hanya baris pertama ada "PENERIMA"
                $pdf->Cell(18, 3, '', 0, 1, 'L'); // Kolom terakhir kosong
            }

            $pdf->Ln();  // Fungsi PageNo() untuk mendapatkan nomor halaman

            $pageNo = $pdf->PageNo();  // Fungsi PageNo() untuk mendapatkan nomor halaman
            // jika halaman pertama hitung tinggi
            if ($pageNo >= 2) {
                $startX_ = 2.5;
                $startY_ = 157;
            } else {
                $startX_ = 2.5;
                $startY_ = 14;
            }
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('Bon Pengiriman.pdf', 'I'));
    }

    private function generateHeaderPOCovering($pdf, $tgl_po)
    {
        $poCovering = $this->openPoModel->getPoForCelup($tgl_po);

        //Hapus Kata POCOVERING
        foreach ($poCovering as $key => $row) {
            $poCovering[$key]->induk_no_model = preg_replace('/POCOVERING\s*/i', '', $row->induk_no_model);
        }
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(10, 10, 277, 190);

        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.4); // Lebih tebal
        $pdf->Rect(9, 9, 279, 192); // Sedikit lebih besar dari margin dalam

        $pdf->Image('assets/img/logo-kahatex.png', 26, 11, 10, 8);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(43, 13, '', 1, 0, 'C');
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(234, 4, 'FORMULIR', 1, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L');
        $pdf->Cell(234, 5, 'DEPARTMEN CELUP CONES', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C');
        $pdf->Cell(234, 4, 'FORMULIR PO', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_01/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(41, 4, '04 Desember 2019', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');
        $pdf->Cell(234, 5, ': ' . $poCovering[0]->anak_no_model, 0, 1, 'L');
        $pdf->Cell(43, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 5, ': COVERING', 0, 1, 'L');
        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        if (!empty($tgl_po)) {
            $pdf->Cell(234, 5, ': ' . $tgl_po, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 9);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 16, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 8, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        $pdf->MultiCell(17, 8, 'Bentuk Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(60, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(20, 16, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 16, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(10, 16, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(25, 16, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 16, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        $pdf->MultiCell(15, 4, 'Qty Pesanan', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 8);
        $pdf->Cell(166, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(52, 8, 'Permintaan Kelos', 1, 0, 'C'); // Merge 4 kolom
        $pdf->MultiCell(18, 8, 'Untuk Produksi', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(236, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(18, 8, 'Contoh Warna', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(254, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(23, 8, 'Keterangan Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(277, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(23, 16, '', 0, 1, 'C'); // Merge 2 baris

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -8, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(12, -8, 'Jenis', 1, 0, 'C');
        $pdf->Cell(25, -8, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -8, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -8, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -8, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -8, 'Yard', 1, 0, 'C');
        $pdf->MultiCell(13, -4, 'Cones Total', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 8);
        $pdf->Cell(205, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(13, -4, 'Cones Jenis', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 8);
        $pdf->Cell(218, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, -8, '', 0, 2, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, 8, '', 0, 1, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->SetFont('Arial', '', 7);
    }

    public function generateOpenPOCovering($tgl_po)
    {
        $poCovering = $this->openPoModel->getPoForCelup($tgl_po);

        //Cek PO
        if (empty($poCovering) || empty($poCovering[0]->no_model)) {
            session()->setFlashdata('error', 'PO Tidak Ditemukan. Open PO Terlebih Dahulu');
            return redirect()->back();
        }

        //Hapus Kata POCOVERING
        foreach ($poCovering as $key => $row) {
            $poCovering[$key]->induk_no_model = preg_replace('/POCOVERING\s*/i', '', $row->induk_no_model);
        }

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Tambahkan border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(10, 10, 277, 190);

        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.4); // Lebih tebal
        $pdf->Rect(9, 9, 279, 192); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.2); // Lebih tipis
        $pdf->Rect(10, 10, 277, 190); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(43, 13, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(234, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(234, 5, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(234, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini


        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_01/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(41, 4, '04 Desember 2019', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');
        $pdf->Cell(234, 5, ': ' . $poCovering[0]->anak_no_model, 0, 1, 'L');

        $pdf->Cell(43, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 5, ': COVERING', 0, 1, 'L');

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($poCovering)) {
            $pdf->Cell(234, 5, ': ' . date('Y-m-d', strtotime($poCovering[0]->created_at)), 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 9);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 16, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 8, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        $pdf->MultiCell(17, 8, 'Bentuk Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(60, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(20, 16, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 16, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(10, 16, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(25, 16, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 16, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        $pdf->MultiCell(15, 4, 'Qty Pesanan', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 8);
        $pdf->Cell(166, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(52, 8, 'Permintaan Kelos', 1, 0, 'C'); // Merge 4 kolom
        $pdf->MultiCell(18, 8, 'Untuk Produksi', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(236, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(18, 8, 'Contoh Warna', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(254, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(23, 8, 'Keterangan Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 16);
        $pdf->Cell(277, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(23, 16, '', 0, 1, 'C'); // Merge 2 baris

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -8, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(12, -8, 'Jenis', 1, 0, 'C');
        $pdf->Cell(25, -8, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -8, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -8, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -8, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -8, 'Yard', 1, 0, 'C');
        $pdf->MultiCell(13, -4, 'Cones Total', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 8);
        $pdf->Cell(205, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(13, -4, 'Cones Jenis', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 8);
        $pdf->Cell(218, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, -8, '', 0, 2, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, 8, '', 0, 1, 'C'); // Kosong untuk menyesuaikan posisi

        $lineHeight = 4;      // tinggi dasar setiap baris MultiCell
        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        $yLimit = 180;

        // Total tinggi baris untuk memastikan 15 baris jika perlu kosong
        $totalKg     = 0;
        $totalPermintaanCones  = 0;
        $totalYard = 0;
        $totalCones  = 0;
        $totalRowHeight     = 0;
        $barisNormalHeight  = 4;
        $maxTotalHeight     = 15 * $barisNormalHeight;


        for ($i = 0; $i < count($poCovering); $i++) {
            $currentY = $pdf->GetY();
            if ($currentY + 6 > $yLimit) {
                // Tambah halaman baru dan cetak header + footer
                $pdf->AddPage();
                $this->generateHeaderPOCovering($pdf, $tgl_po);
            }

            $row = $poCovering[$i];
            $pdf->SetFont('Arial', '', 7);

            // Simpan posisi awal (X, Y) sebelum mencetak baris
            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // Ambil teks tiap kolom
            $jenis     = $row->jenis       ?? '';
            $itemType   = $row->spesifikasi_benang
                ? $row->item_type . ' (' . $row->spesifikasi_benang . ')'
                : $row->item_type;
            $bentuk     = $row->bentuk_celup ?? '';
            $warna      = $row->color        ?? '';
            $kodeWarna  = $row->kode_warna   ?? '';
            $buyer      = $row->buyer        ?? '';
            $noOrder    = $row->no_order     ?? '';

            // Tentukan lebar masing-masing kolom agar sesuai header
            $widths = [
                'jenis'      => 12,
                'itemType'   => 25,
                'bentuk'     => 17,
                'warna'      => 20,
                'kodeWarna'  => 20,
                'buyer'      => 10,
                'noOrder'    => 25,
            ];

            // 1) Simulasikan MultiCell() untuk masing-masing kolom, tanpa border,
            //    untuk mengukur deltaY (berapa tinggi sebenarnya).
            $heights = [];
            $tempX = $startX + 6; // kolom pertama setelah kolom No

            foreach (['jenis', 'itemType', 'bentuk', 'warna', 'kodeWarna', 'buyer', 'noOrder'] as $key) {
                $text = ${$key};
                $w    = $widths[$key];

                // Simpan posisi Y sebelum sim
                $pdf->SetXY($tempX, $startY);
                $simStartY = $pdf->GetY();

                // Panggil MultiCell() simulasi tanpa border (parameter border=0)
                $pdf->MultiCell($w, $lineHeight, $text, 0, 'C');

                // Hitung delta tinggi
                $heights[$key] = $pdf->GetY() - $simStartY;

                // Kembalikan posisi Y ke start untuk simulasi berikutnya
                $pdf->SetXY($tempX, $startY);

                // Geser ke kolom berikutnya
                $tempX += $w;
            }

            // 2) ambil tinggi maksimum dari semua kolom
            $maxHeight = max($heights);

            // Track totalRowHeight agar kelak kita bisa tambahkan baris kosong sisa
            $totalRowHeight += $maxHeight;

            // 3) Sekarang cetak seluruh kolom dengan tinggi maxHeight, 
            //    memastikan semua posisi X, Y direset ke start tiap kolom.
            // Cetak kolom No (menggunakan Cell, karena hanya angka satu baris)
            $pdf->SetXY($startX, $startY);
            $pdf->Cell(6, $maxHeight, $no++, 1, 0, 'C');

            // Kolom-kolom MultiCell: kita gunakan SetXY sebelum tiap MultiCell,
            // lalu kembalikan X ke posisi awal + lebar kolom setelah mencetak.
            $x = $startX + 6;

            // itemType
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['jenis'], $maxHeight, '', 1, 'C');
            $x += $widths['jenis'];

            // itemType
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['itemType'], $maxHeight, '', 1, 'C');
            $x += $widths['itemType'];

            // bentuk
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['bentuk'], $maxHeight, '', 1, 'C');
            $x += $widths['bentuk'];

            // warna
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['warna'], $maxHeight, '', 1, 'C');
            $x += $widths['warna'];

            // kodeWarna
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['kodeWarna'], $maxHeight, '', 1, 'C');
            $x += $widths['kodeWarna'];

            // buyer
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['buyer'], $maxHeight, '', 1, 'C');
            $x += $widths['buyer'];

            // noOrder
            $pdf->SetXY($x, $startY);
            $pdf->MultiCell($widths['noOrder'], $maxHeight, '', 1, 'C');
            $x += $widths['noOrder'];

            // 4) Kolom lain yang tidak perlu MultiCell, tapi tetap ikut maxHeight
            $pdf->SetXY($x, $startY);
            $pdf->Cell(16, $maxHeight, $row->delivery_awal ?? '', 1, 0, 'C');
            $x += 16;

            $kg = number_format($row->kg_po ?? 0, 2);
            $pdf->Cell(15, $maxHeight, $kg, 1, 0, 'C');
            $x += 15;

            $permintaanCones = number_format($row->kg_percones ?? 0, 2);
            $pdf->Cell(13, $maxHeight, $permintaanCones, 1, 0, 'C');
            $x += 13;
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $x += 13;
            $pdf->Cell(13, $maxHeight, $row->jumlah_cones ?? '', 1, 0, 'C');
            $x += 13;
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $x += 13;
            $pdf->Cell(18, $maxHeight, $row->jenis_produksi ?? '', 1, 0, 'C');
            $x += 18;
            $pdf->Cell(18, $maxHeight, $row->contoh_warna ?? '', 1, 0, 'C');
            $x += 18;
            $pdf->Cell(23, $maxHeight, $row->ket_celup ?? '', 1, 0, 'C');
            $x += 23;

            // Pindah ke baris berikutnya
            $pdf->Ln($maxHeight);
            $totalKg += $kg;
            $totalPermintaanCones += $permintaanCones;
            $totalCones += $row->jumlah_cones;
        }

        // Setelah semua data dicetak, tambahkan baris kosong jika totalRowHeight < maxTotalHeight
        $remainingHeight = $maxTotalHeight - $totalRowHeight;
        $remainingRows   = floor($remainingHeight / $barisNormalHeight);

        for ($j = 0; $j < $remainingRows; $j++) {
            $pdf->Cell(6, 4, '', 1, 0, 'C');
            $pdf->Cell(12, 4, '', 1);
            $pdf->Cell(25, 4, '', 1);
            $pdf->Cell(17, 4, '', 1);
            $pdf->Cell(20, 4, '', 1);
            $pdf->Cell(20, 4, '', 1);
            $pdf->Cell(10, 4, '', 1);
            $pdf->Cell(25, 4, '', 1);
            $pdf->Cell(16, 4, '', 1);
            $pdf->Cell(15, 4, '', 1);
            $pdf->Cell(13, 4, '', 1);
            $pdf->Cell(13, 4, '', 1);
            $pdf->Cell(13, 4, '', 1);
            $pdf->Cell(13, 4, '', 1);
            $pdf->Cell(18, 4, '', 1);
            $pdf->Cell(18, 4, '', 1);
            $pdf->Cell(23, 4, '', 1);
            $pdf->Ln(4);
        }

        // Baris TOTAL (baris ke-16)
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(6 + 25 + 12 + 17 + 20 + 20 + 10 + 25 + 16, 6, 'TOTAL', 1, 0, 'R');
        // Lebar merge = jumlah lebar kolom No sampai Delivery = 6+25+12+17+20+20+10+25+16 = 151

        $pdf->Cell(15, 6, number_format($totalKg, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalPermintaanCones, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalYard, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalCones), 1, 0, 'C');
        $pdf->Cell(13, 6, '', 1, 0, 'C');
        $pdf->Cell(18, 6, '', 1, 0, 'C');
        $pdf->Cell(18, 6, '', 1, 0, 'C');
        $pdf->Cell(23, 6, '', 1, 1, 'C');

        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        // $pdf->SetFillColor(255, 255, 255); // Atur warna latar belakang menjadi putih
        // // Check if the result array is not empty and display only the first delivery_awal
        // if (!empty($poCovering)) {
        //     $pdf->MultiCell(117, 5, ': ' . $poCovering[0]->keterangan, 0, 1, 'L');
        // } else {
        //     $pdf->MultiCell(117, 5, ': ', 0, 1, 'L');
        // }

        $pdf->SetY(-55); // Atur jarak dari bawah halaman, sesuaikan jika terlalu atas/bawah
        $pdf->SetFont('Arial', '', 7);

        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . 'Celup Cones', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . 'Celup Cones', 0, 1, 'C');

        $pdf->Cell(55, 12, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        if (!empty($poCovering)) {
            $pdf->Cell(55, 5, '(       ' . $poCovering[0]->penanggung_jawab . '      )', 0, 0, 'C');
        } else {
            $pdf->Cell(234, 5, ': No penanggung_jawab available', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . $poCovering[0]->penerima  . '       )', 0, 1, 'C');

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function exportOpenPOGabung()
    {
        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        // dd($tujuan, $jenis, $jenis2, $startDate, $endDate);

        // Tentukan penerima berdasarkan tujuan
        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } elseif ($tujuan == 'COVERING') {
            $penerima = 'Paryanti';
        } else {
            return redirect()->back()->with('error', 'Tujuan tidak valid.');
        }

        $buyer = [];
        $openPoGabung = $this->openPoModel->listOpenPoGabungbyDate($jenis, $jenis2, $penerima, $startDate, $endDate);
        // dd ($openPoGabung);
        foreach ($openPoGabung as &$po) {
            $buyersData = $this->openPoModel->getBuyer($po['id_po']); // Ambil semua data buyer terkait
            if (is_array($buyersData) && count($buyersData) > 0) {
                // Ambil semua buyer, no_order, dan delivery_awal
                $buyers = array_column($buyersData, 'buyer');
                $noOrders = array_column($buyersData, 'no_order');
                $deliveries = array_column($buyersData, 'delivery_awal');

                // Tentukan buyer: kosong jika lebih dari satu jenis
                $po['buyer'] = count(array_unique($buyers)) === 1 ? $buyers[0] : null;

                // Tentukan delivery_awal paling awal
                $earliestDeliveryIndex = array_keys($deliveries, min($deliveries))[0];
                $po['delivery_awal'] = $deliveries[$earliestDeliveryIndex];

                // Tentukan no_order yang berhubungan dengan delivery_awal paling awal
                $po['no_order'] = $noOrders[$earliestDeliveryIndex];
            } else {
                // Jika tidak ada data buyersData
                $po['buyer'] = null;
                $po['no_order'] = null;
                $po['delivery_awal'] = null;
            }
        }
        // Pastikan untuk tidak menggunakan referensi lagi setelah loop selesai
        unset($po);

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 5); // Atur margin bawah saat halaman penuh
        $pdf->AddPage();

        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.4); // Lebih tebal
        $pdf->Rect(9, 9, 279, 192); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.2); // Lebih tipis
        $pdf->Rect(10, 10, 277, 190); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 13, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(234, 4, 'FORMULIR', 'LTR', 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(234, 5, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 4, 'PT. KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(234, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini


        // Tabel Header Atas
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(41, 4, '04 Desember 2019', 1, 1, 'C');

        $pdf->Cell(43, 4, '', 1, 0, 'L');
        $pdf->Cell(162, 4, '', 1, 0, 'L');
        $pdf->Cell(31, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(41, 4, 'Internal', 1, 1, 'C');


        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 4, 'PO', 0, 0, 'L');
        $pdf->Cell(234, 4, ':', 0, 1, 'L');

        $pdf->Cell(43, 4, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 4, ': KAOS KAKI', 0, 1, 'L');

        $pdf->Cell(43, 4, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($openPoGabung)) {
            $pdf->Cell(234, 4, ': ' . date('d-m-Y', strtotime($openPoGabung[0]['tgl_po'])), 0, 1, 'L');
        } else {
            $pdf->Cell(234, 4, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', 'B', 7);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 14, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(38, 7, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        $pdf->MultiCell(12, 7, 'Bentuk Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(56, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(20, 14, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(18, 14, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(12, 14, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(25, 14, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(15, 14, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        $pdf->MultiCell(13, 3.5, 'Qty Pesanan', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 7);
        $pdf->Cell(159, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(43, 7, 'Permintaan Kelos', 1, 0, 'C'); // Merge 4 kolom
        $pdf->MultiCell(15, 7, 'Untuk Produksi', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(217, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(12, 7, 'Contoh Warna', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(229, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(48, 14, 'Keterangan Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(277, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(23, 14, '', 0, 1, 'C'); // Merge 2 baris

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -7, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(26, -7, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -7, 'Kode', 1, 0, 'C');
        $pdf->Cell(102, -7, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(13, -7, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(8, -7, 'Kg', 1, 0, 'C');
        $pdf->Cell(10, -7, 'Yard', 1, 0, 'C');
        $pdf->MultiCell(12, -3.5, 'Cones Total', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 7);
        $pdf->Cell(189, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(13, -3.5, 'Cones Jenis', 1, 'C', false);
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() + 8);
        $pdf->Cell(218, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, -7, '', 0, 2, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, 7, '', 0, 1, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(23, -1, '', 0, 1, 'C'); // Merge 2 baris

        $no = 1;
        // Inisialisasi variabel total
        $totalKgPo = 0;
        $totalCones = 0;
        foreach ($openPoGabung as $po) {
            $pdf->SetFont('Arial', '', 6);

            // Posisi awal baris
            $yStart = $pdf->GetY();

            // Hitung tinggi maksimum dalam satu baris
            $rowHeight = 4; // Tinggi default
            $heights = [];

            // hitung jumlah baris per kolom
            $heights = [
                'item_type'      => ceil($pdf->GetStringWidth($po['item_type'] . ' (' . $po['spesifikasi_benang'] . ')') / 26) * $rowHeight,
                'ukuran'         => ceil($pdf->GetStringWidth($po['ukuran']) / 12) * $rowHeight,
                'bentuk_celup'   => ceil($pdf->GetStringWidth($po['bentuk_celup']) / 12) * $rowHeight,
                'buyer'          => ceil($pdf->GetStringWidth($po['buyer']) / 10) * $rowHeight,
                'color'          => ceil($pdf->GetStringWidth($po['color']) / 20) * $rowHeight,
                'kode_warna'     => ceil($pdf->GetStringWidth($po['kode_warna']) / 20) * $rowHeight,
                'no_order'       => ceil($pdf->GetStringWidth($po['no_order']) / 25) * $rowHeight,
                'jenis_produksi' => ceil($pdf->GetStringWidth($po['jenis_produksi']) / 15) * $rowHeight,
                'ket_celup'      => ceil($pdf->GetStringWidth($po['ket_celup']) / 48) * $rowHeight,
            ];

            $rowHeight = max($heights);

            // Tulis data dengan MultiCell untuk kolom yang membutuhkan wrap text
            $pdf->Cell(6, $rowHeight, $no++, 1, 0, 'C'); // No
            $xNow = $pdf->GetX();
            $rowItem = $heights['item_type'] / 4 > 1 ? 4 : $rowHeight;
            $pdf->MultiCell(26, $rowItem, $po['item_type'] . ' (' . $po['spesifikasi_benang'] . ')', 1, 'C'); // Jenis
            $pdf->SetXY($xNow + 26, $yStart);

            $xNow = $pdf->GetX();
            $rowUkuran = $heights['ukuran'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(12, $rowUkuran, $po['ukuran'], 1, 'C'); // Kode
            $pdf->SetXY($xNow + 12, $yStart);

            $xNow = $pdf->GetX();
            $rowBc = $heights['bentuk_celup'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(12, $rowBc, $po['bentuk_celup'], 1, 'C'); // Bentuk Celup
            $pdf->SetXY($xNow + 12, $yStart);

            $xNow = $pdf->GetX();
            $rowColor = $heights['color'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(20, $rowColor, $po['color'], 1, 'C'); // Warna
            $pdf->SetXY($xNow + 20, $yStart);

            $xNow = $pdf->GetX();
            $rowKode = $heights['kode_warna'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(18, $rowKode, $po['kode_warna'], 1, 'C'); // Kode Warna
            $pdf->SetXY($xNow + 18, $yStart);

            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(12, $rowHeight, $po['buyer'], 1, 0, 'C'); // Buyer

            $xNow = $pdf->GetX();
            $rowNoOrder = $heights['no_order'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(25, $rowNoOrder, $po['no_order'], 1, 'C'); // Nomor Order
            $pdf->SetXY($xNow + 25, $yStart);

            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell(15, $rowHeight, $po['delivery_awal'], 1, 0, 'C'); // Delivery
            $pdf->Cell(13, $rowHeight, number_format($po['kg_po'], 2), 1, 0, 'C'); // Qty Pesanan (Kg)
            $pdf->Cell(8, $rowHeight, $po['kg_percones'], 1, 0, 'C'); // Kg Per Cones
            $pdf->Cell(10, $rowHeight, '', 1, 0, 'C'); // Yard
            $pdf->Cell(12, $rowHeight, $po['jumlah_cones'], 1, 0, 'C'); // Cones Total
            $pdf->Cell(13, $rowHeight, '', 1, 0, 'C'); // Cones Jenis

            $xNow = $pdf->GetX();
            $rowJp = $heights['jenis_produksi'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(15, $rowJp, $po['jenis_produksi'], 1, 'C'); // Untuk Produksi
            $pdf->SetXY($xNow + 15, $yStart);

            $xNow = $pdf->GetX();
            $pdf->MultiCell(12, $rowHeight, '', 1, 'C'); // Contoh Warna
            $pdf->SetXY($xNow + 12, $yStart);

            $xNow = $pdf->GetX();
            $rowKc = $heights['ket_celup'] / 4 > 1 ?  4 : $rowHeight;
            $pdf->MultiCell(48, $rowKc, $po['ket_celup'], 1, 'C'); // Keterangan Celup
            $pdf->SetXY($xNow + 48, $yStart);

            $pdf->Ln($rowHeight); // Pindah ke baris berikutnya

            // Tambahkan nilai ke total
            $totalKgPo += $po['kg_po'];
            $totalCones += $po['jumlah_cones'];
        }

        // Tambahkan baris total
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(146, 5, 'Total', 1, 0, 'R'); // Gabungkan sel sebelum kolom "Qty Pemesanan"
        $pdf->Cell(13, 5, number_format($totalKgPo, 2), 1, 0, 'C'); // Total Qty Pemesanan (kg)
        $pdf->Cell(8, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya
        $pdf->Cell(10, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya
        $pdf->Cell(12, 5, $totalCones == 0 ? '' : $totalCones, 1, 0, 'C'); // Total Cones
        $pdf->Cell(13, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya
        $pdf->Cell(15, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya
        $pdf->Cell(12, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya
        $pdf->Cell(48, 5, '', 1, 0, 'C'); // Kosong untuk "Kg Per Cones" dan lainnya

        // KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($openPoGabung)) {
            $pdf->Cell(117, 5, ': ' . $openPoGabung[0]['keterangan'], 0, 1, 'L');
        } else {
            $pdf->Cell(117, 5, ': ', 0, 1, 'L');
        }

        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 15, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        if (!empty($openPoGabung)) {
            $pdf->Cell(55, 5, '(       ' . $openPoGabung[0]['penanggung_jawab'] . '      )', 0, 0, 'C');
        } else {
            $pdf->Cell(234, 5, ': No penanggung_jawab available', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . $penerima . '       )', 0, 1, 'C');

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('PO Gabungan.pdf', 'I'));
    }

    public function generatePengeluaranSpandexKaretCovering($jenis, $tgl_po)
    {
        // Ambil data dari model
        $data = $this->pemesananSpandexKaretModel->getDataForPdf($jenis, $tgl_po);
        // dd ($data);
        // Inisialisasi FPDF (portrait A4)
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Garis tepi luar (margin 10mm → konten 190×277)
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(9, 9, 192, 132);    // sedikit lebih besar untuk border luar
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(10, 10, 190, 130);  // border dalam

        // Logo
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8);

        // Header
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(43, 13, '', 1, 0, 'C');
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(147, 4, 'FORMULIR', 1, 1, 'C', 1);

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 5, '', 0, 0, 'L');
        $pdf->Cell(147, 5, 'DEPARTEMEN COVERING', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C');
        $pdf->Cell(147, 4, 'SURAT PENGELUARAN BARANG', 0, 1, 'C');

        // Tabel Header Atas (total lebar 190)
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(60, 4, 'FOR-COV-631', 1, 0, 'L');
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(24, 4, 'Halaman', 1, 0, 'L');
        $pdf->Cell(63, 4, '1 dari 1', 1, 1, 'C');

        // Tanggal
        $pdf->Cell(43, 4, 'Tanggal Efektif', 1, 0, 'L');
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(60, 4, '01 Mei 2017', 1, 0, 'L');
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(24, 4, 'Revisi', 1, 0, 'L');
        $pdf->Cell(63, 4, '00', 1, 1, 'C');

        // kosongkan sel
        $pdf->Cell(103, 4, '', 1, 0, 'L');
        $pdf->Cell(24, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(63, 4, '', 1, 1, 'C');

        // garis double
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 36, 200, 36); // Garis horizontal
        $pdf->Cell(0, 1, '', 0, 1); // Pindah ke baris berikutnya

        // customer
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(103, 8, 'CUSTOMER: ', 0, 0, 'L', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(24, 4, 'NO: ', 0, 0, 'L', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(63, 4, '', 0, 1, 'C', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(103, 4, '', 0, 0, 'L', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(24, 4, 'TANGGAL: ', 0, 0, 'L', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(63, 4, '', 0, 1, 'L', false); // Tinggi cell diatur menjadi 8 agar teks berada di tengah

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(10, 8,  'No',           1, 0, 'C');
        $pdf->Cell(55, 8,  'JENIS BARANG', 1, 0, 'C');
        $pdf->Cell(15, 8,  'DR/TPM',       1, 0, 'C');
        $pdf->Cell(30, 8,  'WARNA/CODE',   1, 0, 'C');
        $pdf->Cell(20, 4,  'JUMLAH',       1, 0, 'C');
        // Keterangan merge dua baris (8 + 4 mm = 12 mm)
        $pdf->Cell(60, 8, 'KETERANGAN',   1, 1, 'C');

        // Baris Kedua: hanya untuk sub‐kolom JUMLAH (KG + CONES)
        $pdf->SetX(10 /* left margin */ + 10 + 55 + 15 + 30); // pos X setelah No+Jenis+DRTPM+Warna
        $pdf->Cell(10, -4, 'KG',    1, 0, 'C');
        $pdf->Cell(10, -4, 'CONES', 1, 0, 'C');
        $pdf->Ln();  // turun ke baris data
        $pdf->Cell(190, 4, '', 0, 1, 'C'); // Kosongkan sel untuk No
        // foreach data.
        $urut = 18; // untuk mengatur posisi Y dari baris ke 2
        $no = 1;
        if (count($data) > 0) {
            foreach ($data as $row) {
                $pdf->Cell(10, 4,  $row['area'],           1, 0, 'C');
                $pdf->Cell(55, 4,  $row['jenis'] . ' (' . $row['no_model'] . ')', 1, 0, 'C');
                $pdf->Cell(15, 4,  "",        1, 0, 'C');
                $pdf->Cell(30, 4,  $row['color'] . '/' . $row['code'],    1, 0, 'C');
                $pdf->Cell(10, 4,  number_format($row['total_pesan'], 2),   1, 0, 'C');
                $pdf->Cell(10, 4,  $row['total_cones'],   1, 0, 'C');
                // Keterangan merge dua baris (8 + 4 mm = 12 mm)
                $pdf->Cell(60, 4, $row['keterangan'],   1, 1, 'C');
                $no++;
            }
            if ($no < $urut) {
                // Jika tidak ada data yang ditemukan
                for ($i = $no; $i < $urut; $i++) {
                    $pdf->Cell(10, 4,  '', 1, 0, 'C');
                    $pdf->Cell(55, 4,  '', 1, 0, 'C');
                    $pdf->Cell(15, 4,  '',        1, 0, 'C');
                    $pdf->Cell(30, 4,  '',    1, 0, 'C');
                    $pdf->Cell(10, 4,  '',   1, 0, 'C');
                    $pdf->Cell(10, 4,  '',   1, 0, 'C');
                    // Keterangan merge dua baris (8 + 4 mm = 12 mm)
                    $pdf->Cell(60, 4, '',   1, 1, 'C');
                }
            }
        } else {
            // Jika ada data yang ditemukan, tetapi kurang dari 5 baris
            for ($i = 0; $i < $urut; $i++) {
                $pdf->Cell(10, 4,  '',           1, 0, 'C');
                $pdf->Cell(55, 4,  '', 1, 0, 'C');
                $pdf->Cell(15, 4,  '',        1, 0, 'C');
                $pdf->Cell(30, 4,  '',    1, 0, 'C');
                $pdf->Cell(10, 4,  '',   1, 0, 'C');
                $pdf->Cell(10, 4,  '',   1, 0, 'C');
                // Keterangan merge dua baris (8 + 4 mm = 12 mm)
                $pdf->Cell(60, 4, '',   1, 1, 'C');
            }
        }

        // tanda tangan
        // $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(63, 5, 'YANG BUKA BON', 0, 0, 'C');
        $pdf->Cell(64, 5, 'GUDANG ANGKUTAN', 0, 0, 'C');
        $pdf->Cell(63, 5, 'PENERIMA', 0, 1, 'C');
        $pdf->Cell(190, 5, '', 0, 1, 'C');
        $pdf->Cell(190, 5, '', 0, 1, 'C');
        $pdf->Cell(63, 5, '(       ' . 'PARYANTI' . '       )', 0, 0, 'C');
        $pdf->Cell(64, 5, '(                               )', 0, 0, 'C');
        $pdf->Cell(63, 5, '(       ' . 'HARTANTO' . '       )', 0, 1, 'C');
        $pdf->Cell(55, 5, '', 0, 1, 'C');
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '', 0, 1, 'C');
        $pdf->Cell(55, 5, '', 0, 0, 'C');






        // … di sini loop $data dan tampilkan isi tabel sesuai style-mu …

        // Output PDF
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function generateBarcodeRetur($tglRetur)
    {
        // 1) Ambil data
        $dataList = $this->outCelupModel->getDataReturByTgl($tglRetur);
        if (empty($dataList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tidak ada retur pada tanggal {$tglRetur}");
        }

        // 2) Inisialisasi PDF
        $pdf       = new FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $generator = new BarcodeGeneratorPNG();

        // 3) Konfigurasi grid
        $cols    = 3;
        $boxW    = 63;
        $boxH    = 60;
        $gapX    = 5;
        $gapY    = 5;
        $marginX = 10;
        $marginY = 15;
        $lineH   = 4;

        foreach ($dataList as $i => $dataRetur) {
            // Hitung posisi kolom & baris
            $col = $i % $cols;
            $row = floor($i / $cols);
            $x   = $marginX + $col * ($boxW + $gapX);
            $y   = $marginY + $row * ($boxH + $gapY);

            // Gambar kotak putih + border
            $pdf->SetFillColor(255);
            $pdf->Rect($x, $y, $boxW, $boxH, 'F');
            $pdf->Rect($x, $y, $boxW, $boxH);

            // 4) Generate barcode (tanpa padding, CODE-128)
            $idOut       = (string)$dataRetur['id_out_celup'];
            $barcodeData  = $generator->getBarcode($idOut, $generator::TYPE_CODE_128);

            // Simpan & tampilkan PNG
            $tmpFile = tempnam(sys_get_temp_dir(), 'bc_') . '.png';
            file_put_contents($tmpFile, $barcodeData);
            $pdf->Image($tmpFile, $x + 5, $y + 3, $boxW - 10, 12);
            @unlink($tmpFile);

            // (Opsional) Tampilkan kode asli di bawah barcode
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY($x + 5, $y + 16);
            // $pdf->Cell($boxW - 10, 4, $code, 0, 1, 'C'); // Menampilkan id out celup di barcode

            // 5) Tampilkan teks dengan wrapping
            $pdf->SetFont('Arial', '', 7);
            $textX  = $x + 3;
            $textY  = $y + 22;
            $textW  = $boxW - 6;  // sisakan 3mm margin kiri & kanan
            $pdf->SetXY($textX, $textY);

            $fields = [
                'Model'       => $dataRetur['no_model'],
                'Item Type'   => $dataRetur['item_type'],
                'Kode Warna'  => $dataRetur['kode_warna'],
                'Warna'       => $dataRetur['warna'],
                'Kgs Retur'   => $dataRetur['kgs_retur'],
                'Cones Retur' => $dataRetur['cns_retur'],
                'Lot Retur'   => $dataRetur['lot_retur'],
                'No Karung'   => $dataRetur['no_karung'],
            ];

            $labelWidth = 20; // Lebar tetap untuk label
            $valueWidth = $textW - $labelWidth;

            foreach ($fields as $label => $value) {
                $pdf->SetX($textX);
                $pdf->Cell($labelWidth, $lineH, $label, 0, 0); // Kolom label
                $pdf->MultiCell($valueWidth, $lineH, ': ' . $value, 0, 'L'); // Kolom isi
            }

            // 6) Jika penuh ke bawah, tambahkan halaman
            if (($y + $boxH + $gapY > 280) && ($col === $cols - 1)) {
                $pdf->AddPage();
            }
        }

        // 7) Output PDF
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader(
                'Content-Disposition',
                "inline; filename=\"Barcode_Retur_{$tglRetur}.pdf\""
            )
            ->setBody($pdf->Output('', 'S'));
    }
    public function printBarcodeOtherBon($idOtherBon)
    {
        $data = $this->otherBonModel->getDataById($idOtherBon);
        $generator = new BarcodeGeneratorPNG();

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255); // Warna latar putih
        $pdf->SetTextColor(0, 0, 0);      // Warna teks hitam
        $pdf->SetDrawColor(0, 0, 0);      // Warna garis hitam
        // $pdf->Ln();  // Fungsi PageNo() untuk mendapatkan nomor halaman
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, 'BARCODE ' . $data[0]['no_model'], 0, 1, 'C');

        $startX_ = 2.5;
        $startY_ = 14;
        $barcodeCount = 0; // Counter untuk jumlah barcode di halaman saat ini
        $barcodeWidth = 67; // Lebar kotak barcode
        $barcodeHeight = 67; // Tinggi kotak barcode
        $jarakKolom = 2; // Jarak horizontal antar kolom
        $jarakBaris = 2; // Jarak vertikal antar baris

        foreach ($data as $barcode) {
            // Buat instance Barcode Generator
            $generate = $generator->getBarcode($barcode['id_out_celup'], $generator::TYPE_CODE_128);
            $generate = base64_encode($generate);
            // Menghitung posisi X dan Y untuk 12 barcode per halaman (3 kolom × 2 baris)
            $mod = 12;
            $baris = 4;
            // Jika sudah mencapai batas (6 barcode), tambah halaman baru
            if ($barcodeCount > 0 && $barcodeCount % $mod === 0) {
                $pdf->AddPage(); // Tambahkan halaman baru
                $startX_ = 2.5; // Reset posisi X untuk halaman baru
                $startY_ = 14; // Reset posisi Y untuk halaman baru
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(0, 8, 'BARCODE', 0, 1, 'C');
                $barcodeCount = 0; // Reset counter per halaman
            }
            $colIndex = $barcodeCount % 3; // 3 kolom per baris
            $rowIndex = floor($barcodeCount / 3) % $baris; // 2 baris per halaman

            // Menghitung posisi untuk setiap barcode
            $startX = $startX_ + ($colIndex * ($barcodeWidth + $jarakKolom)); // Posisi horizontal
            $startY = $startY_ + ($rowIndex * ($barcodeHeight + $jarakBaris)); // Posisi vertikal

            // Menggambar kotak di sekitar detail
            $pdf->Rect($startX, $startY, 67, 67); // Kotak barcode

            // Menyimpan gambar barcode
            $imageData = base64_decode($generate);
            $tempImagePath = WRITEPATH . 'uploads/barcode_temp' . $barcodeCount . '.png'; // Path file sementara
            file_put_contents($tempImagePath, $imageData);

            // Menentukan posisi X agar gambar berada di tengah kotak secara horizontal
            $imageWidth = 40; // Lebar gambar
            $centerX = $startX + (67 - $imageWidth) / 2; // Menyesuaikan posisi
            $pdf->Image($tempImagePath, $centerX, $startY + 3, $imageWidth); // Tambahkan gambar

            unlink($tempImagePath); // Menghapus file gambar sementara

            // Menghitung berapa banyak baris yang sudah tercetak di dalam MultiCell

            // Menambahkan detail teks di dalam kotak
            $pdf->SetFont('Arial', 'B', 8);
            // Teks detail
            $pdf->SetXY($startX + 2, $startY + 20);
            $pdf->Cell(20, 3, 'No Model', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(70, 3, $barcode['no_model'], 0, 1, 'L');

            $pdf->SetXY($startX + 2, $pdf->getY());
            $pdf->Cell(20, 3, 'Item Type', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->MultiCell(39, 3, $barcode['item_type'], 0, 1, 'L');
            // Menyimpan posisi Y setelah MultiCell
            // dd($currentY, $nextY, $totalHeight, $lineCount);
            $pdf->SetXY($startX + 2, $pdf->GetY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->SetXY($startX + 2, $pdf->GetY()); // Menambah jarak berdasarkan jumlah baris yang tercetak
            $pdf->Cell(20, 3, 'Kode Warna', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->MultiCell(39, 3, $barcode['kode_warna'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $pdf->getY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->Cell(20, 3, 'Warna', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->MultiCell(39, 3, $barcode['warna'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $pdf->getY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $currentY = $pdf->GetY();
            $pdf->Cell(20, 3, 'GW', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(39, 3, $barcode['gw_kirim'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $currentY + 3); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->Cell(20, 3, 'NW', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(39, 3, $barcode['kgs_kirim'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $currentY + 6); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->Cell(20, 3, 'Cones', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(39, 3, $barcode['cones_kirim'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $currentY + 9); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->Cell(20, 3, 'Lot', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(39, 3, $barcode['lot_kirim'], 0, 0, 'L');
            $pdf->SetXY($startX + 2, $currentY + 12); // Menambah jarak berdasarkan jumlah baris yang tercetak

            $pdf->Cell(20, 3, 'No Karung', 0, 0, 'L');
            $pdf->Cell(5, 3, ':', 0, 0, 'C');
            $pdf->Cell(39, 3, $barcode['no_karung'], 0, 1, 'L');
            $pdf->SetXY($startX + 2, $currentY + 15); // Menambah jarak berdasarkan jumlah baris yang tercetak

            // Counter untuk jumlah barcode
            $barcodeCount++;
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('Barcode Pemasukan Lain-lain.pdf', 'I'));
    }

    public function exportStockPdf()
    {
        $jenisCover = $this->request->getPost('jenis_cover');
        $jenisBenang = $this->request->getPost('jenis_benang');
        if (empty($jenisBenang) || empty($jenisCover)) {
            return redirect()->back()->with('error', 'Jenis Benang dan Jenis Cover tidak boleh kosong.');
        }

        $data = $this->coveringStockModel->getStockCover($jenisBenang, $jenisCover);

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
        $sheet->setCellValue('C3', 'STOCK ' . $jenisCover . ' COVER DI GUDANG COVERING');
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
            if (!isset($totalPerBenang[$mainJenis])) {
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
        $rowstar = $row - 1;
        $rowEnd = $row;
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFont()->setBold(true);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle("B{$rowstar}:D{$rowEnd}")->getFill()->getStartColor()->setARGB('DCDCDC');
        $row += 2;

        // 4) Tanda tangan penanggung jawab
        $sheet->mergeCells("E{$row}:I{$row}")
            ->setCellValue("E{$row}", "Yang Bertanggung Jawab : ………......................");
        $sheet->getStyle("E{$row}")
            ->getFont()->setItalic(true);


        // Export ke PDF
        // Pastikan Dompdf writer di-import
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf')) {
            throw new \RuntimeException('Dompdf writer class not found. Make sure dompdf/dompdf is installed via Composer.');
        }
        \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf::class);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

        // Set orientasi dan ukuran halaman
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // Output langsung ke browser
        $filename = 'stock_covering_' . date('Ymd_His') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        $writer->save('php://output');
        $writer->save('php://output');
        exit;
    }

    public function BahanBakuCovPdf()
    {
        $data = $this->warehouseBBModel
            ->orderBy('jenis_benang', 'ASC')
            ->orderBy('denier', 'ASC')
            ->orderBy('warna', 'ASC')
            ->orderBy('kode', 'ASC')
            ->findAll();
        if (!$data) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tidak ada data.');
        }

        // Kelompokkan per jenis_benang, lalu per denier
        $groups = [];
        foreach ($data as $item) {
            $groups[$item['jenis_benang']][$item['denier']][] = $item;
        }
        ksort($groups);

        $pdf = new FPDF('P', 'mm', 'A4');
        $leftX = 10;
        $tableHalfWidth = 90;
        $gap = 10;
        $rightX = $leftX + $tableHalfWidth + $gap;
        $rowH = 5;
        $maxRows = 40;
        $colWidths = array_fill(0, 5, $tableHalfWidth / 5);

        $drawTableHeader = function ($pdf, $x, $y, $colWidths) {
            $pdf->SetXY($x, $y);
            $pdf->Cell($colWidths[0], 6, 'Denier', 1, 0, 'C');
            $pdf->Cell($colWidths[1], 6, 'Warna', 1, 0, 'C');
            $pdf->Cell($colWidths[2], 6, 'Code', 1, 0, 'C');
            $pdf->Cell($colWidths[3], 3, 'Stock', 1, 0, 'C');
            $pdf->Cell($colWidths[4], 6, 'Keterangan', 1, 1, 'C');
            $pdf->SetX($x);
            $pdf->Cell($colWidths[0], 3, '', 0, 0, 'C');
            $pdf->Cell($colWidths[1], 3, '', 0, 0, 'C');
            $pdf->Cell($colWidths[2], 3, '', 0, 0, 'C');
            $pdf->Cell($colWidths[3], -3, 'Kg', 1, 0, 'C');
            $pdf->Cell($colWidths[4], 3, '', 0, 1, 'C');
        };

        $pageNo = 1;
        foreach ($groups as $jenis_benang => $denierGroups) {
            // Setiap jenis_benang mulai halaman baru
            $pdf->AddPage();

            // Border & header
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.4);
            $pdf->Rect(9, 9, 192, 272);
            $pdf->SetLineWidth(0.2);
            $pdf->Rect(10, 10, 190, 270);

            $pdf->Image('assets/img/logo-kahatex.png', 26, 11, 10, 8);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(43, 13, '', 1, 0, 'C');
            $pdf->SetFillColor(170, 255, 255);
            $pdf->Cell(147, 4, 'FORMULIR', 1, 1, 'C', 1);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell(43, 5, '', 0, 0);
            $pdf->Cell(147, 5, 'DEPARTEMEN COVERING', 0, 1, 'C');
            $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C');
            $pdf->Cell(147, 4, 'STOCK BAHAN BAKU PER HARI', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 5);
            $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
            $pdf->Cell(60, 4, 'FOR-COV-092/REV_00/HAL_' . $pageNo . '/3', 1, 0, 'L');
            $pdf->Cell(24, 4, 'Tanggal Revisi', 1, 0, 'L');
            $pdf->Cell(63, 4, '07 Januari 2019', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(43, 4, 'Tanggal : ' . date('D, d M Y'), 0, 1, 'L');
            $pdf->Cell(43, 4, 'Jenis : ' . $jenis_benang, 0, 1, 'L');
            $pdf->Cell(0, 1, '', 0, 1);
            $pdf->Line(10, 28, 200, 28);

            // Table header
            $baseY = $pdf->GetY();
            $drawTableHeader($pdf, $leftX, $baseY, $colWidths);
            $drawTableHeader($pdf, $rightX, $baseY, $colWidths);

            $xPos = ['left' => $leftX, 'right' => $rightX];
            $yPos = ['left' => $baseY + 6, 'right' => $baseY + 6];
            $rowCount = ['left' => 0, 'right' => 0];
            $col = 'left';

            foreach ($denierGroups as $denier => $rows) {
                $sumKg = array_sum(array_column($rows, 'kg'));
                $needed = count($rows) + 1;

                if ($rowCount[$col] + $needed > $maxRows) {
                    $col = $col === 'left' ? 'right' : 'left';

                    if ($rowCount[$col] + $needed > $maxRows) {
                        // Halaman baru untuk jenis_benang yang sama
                        $pdf->AddPage();

                        // Border & header ulang
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetLineWidth(0.4);
                        $pdf->Rect(9, 9, 192, 272);
                        $pdf->SetLineWidth(0.2);
                        $pdf->Rect(10, 10, 190, 270);

                        $pdf->Image('assets/img/logo-kahatex.png', 26, 11, 10, 8);
                        $pdf->SetFont('Arial', 'B', 7);
                        $pdf->Cell(43, 13, '', 1, 0, 'C');
                        $pdf->SetFillColor(170, 255, 255);
                        $pdf->Cell(147, 4, 'FORMULIR', 1, 1, 'C', 1);
                        $pdf->SetFont('Arial', 'B', 6);
                        $pdf->Cell(43, 5, '', 0, 0);
                        $pdf->Cell(147, 5, 'DEPARTEMEN COVERING', 0, 1, 'C');
                        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C');
                        $pdf->Cell(147, 4, 'STOCK BAHAN BAKU PER HARI', 0, 1, 'C');

                        $pdf->SetFont('Arial', 'B', 5);
                        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
                        $pdf->Cell(60, 4, 'FOR-COV-092/REV_00/HAL_1/3', 1, 0, 'L');
                        $pdf->Cell(24, 4, 'Tanggal Revisi', 1, 0, 'L');
                        $pdf->Cell(63, 4, '07 Januari 2019', 1, 1, 'C');
                        $pdf->SetFont('Arial', '', 5);
                        $pdf->Cell(43, 4, 'Tanggal : ' . date('D, d M Y'), 0, 1, 'L');
                        $pdf->Cell(43, 4, 'Jenis : ' . $jenis_benang, 0, 1, 'L');
                        $pdf->Cell(0, 1, '', 0, 1);
                        $pdf->Line(10, 28, 200, 28);

                        // Nomor halaman dinamis
                        $pageNo++;
                        $pdf->SetFont('Arial', 'I', 6);
                        $pdf->SetXY(170, 280);
                        $pdf->Cell(30, 4, 'Halaman: ' . $pageNo, 0, 0, 'R');
                        $pdf->SetFont('Arial', '', 5);

                        $drawTableHeader($pdf, $leftX, $pdf->GetY(), $colWidths);
                        $drawTableHeader($pdf, $rightX, $pdf->GetY(), $colWidths);
                        $xPos = ['left' => $leftX, 'right' => $rightX];
                        $yPos = ['left' => $pdf->GetY() + 6, 'right' => $pdf->GetY() + 6];
                        $rowCount = ['left' => 0, 'right' => 0];
                        $col = 'left';
                    }
                }

                $firstRow = true;
                foreach ($rows as $item) {
                    $pdf->SetXY($xPos[$col], $yPos[$col]);

                    if ($firstRow) {
                        $pdf->Cell($colWidths[0], $rowH * count($rows), $denier, 1, 0, 'C');
                        $firstRow = false;
                    } else {
                        $pdf->Cell($colWidths[0], $rowH, '', 0, 0);
                    }

                    $pdf->Cell($colWidths[1], $rowH, $item['warna'], 1, 0, 'C');
                    $pdf->Cell($colWidths[2], $rowH, $item['kode'], 1, 0, 'C');
                    $pdf->Cell($colWidths[3], $rowH, number_format($item['kg'], 2), 1, 0, 'C');
                    $pdf->Cell($colWidths[4], $rowH, '', 1, 1);

                    $yPos[$col] += $rowH;
                    $rowCount[$col]++;
                }

                $pdf->SetXY($xPos[$col], $yPos[$col]);
                $pdf->SetFont('Arial', 'B', 5);
                $pdf->SetFillColor(170, 255, 255);
                $pdf->Cell($colWidths[0] + $colWidths[1] + $colWidths[2], $rowH, 'Total ' . $denier, 1, 0, 'C', true);
                $pdf->Cell($colWidths[3], $rowH, number_format($sumKg, 2), 1, 0, 'C', true);
                $pdf->Cell($colWidths[4], $rowH, '', 1, 1, 'C', true);
                $pdf->SetFont('Arial', '', 5);
                $pdf->SetFillColor(255, 255, 255);

                $yPos[$col] += $rowH;
                $rowCount[$col]++;
            }

            // Tambahkan baris kosong jika data tidak memenuhi halaman
            foreach (['left', 'right'] as $side) {
                while ($rowCount[$side] < $maxRows) {
                    $pdf->SetXY($xPos[$side], $yPos[$side]);
                    for ($i = 0; $i < 5; $i++) {
                        $pdf->Cell($colWidths[$i], $rowH, '', 1, $i === 4 ? 1 : 0, 'C');
                    }
                    $yPos[$side] += $rowH;
                    $rowCount[$side]++;
                }
            }

            $pdf->SetXY($rightX, $yPos['right'] + 2);
            $pdf->Cell(array_sum($colWidths), 4, 'Yang Bertanggung Jawab', 0, 1, 'C');
            $pdf->SetXY($rightX, $yPos['right'] + 14);
            $pdf->Cell(array_sum($colWidths), 4, '( IIS RAHAYU )', 0, 1, 'C');

            $pageNo++;
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('stock.pdf', 'I'));
    }

    public function exportPemesananSandexKaretCovering()
    {
        $tglPakai = $this->request->getGet('tgl_pakai');
        $jenis = $this->request->getGet('jenis');

        $data = $this->pemesananModel->getDataPemesananCovering($tglPakai, $jenis);
        // dd ($data);
        // Inisialisasi FPDF (portrait A4)
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Garis tepi luar (margin 10mm → konten 190×277)
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(9, 9, 192, 279);    // sedikit lebih besar untuk border luar
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(10, 10, 190, 277);  // border dalam

        // Logo
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Image('assets/img/logo-kahatex.png', $x + 12, $y + 1, 10, 8);

        // Header
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(35, 13, '', 1, 0, 'C');
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(155, 4, 'FORMULIR', 1, 1, 'C', 1);

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(35, 5, '', 0, 0, 'L');
        $pdf->Cell(155, 5, 'DEPARTEMEN COVERING', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(35, 4, 'PT.KAHATEX', 0, 0, 'C');
        $pdf->Cell(155, 4, 'RANGKUMAN PEMESANAN BAHAN BAKU ' . $jenis . ' (KAOS KAKI)', 0, 1, 'C');

        // Tabel Header Atas (total lebar 190)
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(35, 4, 'No.Dokumen', 1, 0, 'C');
        $pdf->SetFont('Arial', 'B', 5);
        if ($jenis == 'SPANDEX') {
            $pdf->Cell(85, 4, 'FOR-COV-059/REV_01/HAL_1/1', 1, 0, 'L');
        } else {
            $pdf->Cell(85, 4, 'FOR-COV-060/REV_01/HAL_1/1', 1, 0, 'L');
        }
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(30, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(40, 4, '07 November 2019', 1, 1, 'C');

        // garis double
        $pdf->SetLineWidth(0.2);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(10, 27.5, 200, 27.5); // Garis horizontal
        $pdf->Cell(0, 1, '', 0, 1); // Pindah ke baris berikutnya
        $pdf->Line(10, 23.5, 200, 23.5); // Garis horizontal
        $pdf->Cell(0, 1, '', 0, 1); // Pindah ke baris berikutnya

        // customer
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(190, 4, '', 0, 1, 'C'); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(100, 4, 'TGL: ' . $tglPakai, 0, 0, 'L'); // Tinggi cell diatur menjadi 8 agar teks berada di tengah
        $pdf->Cell(85, 4, 'TGL:', 0, 1, 'L'); // Tinggi cell diatur menjadi 8 agar teks berada di tengah

        //Simpan posisi awal Season & MaterialType
        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Header Tabel Pertama
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(20, 6,  'JENIS', 1, 0, 'C');
        $pdf->Cell(15, 6,  'COLOUR', 1, 0, 'C');
        $pdf->Cell(15, 6,  'CODE', 1, 0, 'C');
        MultiCellFit($pdf, 10, 3, "JLN\nMC KK", 1, 'C');
        $pdf->Cell(10, 6,  'CONES', 1, 0, 'C');
        $pdf->Cell(10, 6,  'KG', 1, 0, 'C');
        $pdf->Cell(10, 6,  'KG', 1, 0, 'C');

        // pemisah
        $pdf->Cell(10, 6,  '', 0, 0, 'C');

        // Header Tabel Kedua
        $pdf->Cell(20, 6,  'JENIS', 1, 0, 'C');
        $pdf->Cell(15, 6,  'COLOUR', 1, 0, 'C');
        $pdf->Cell(15, 6,  'CODE', 1, 0, 'C');
        MultiCellFit($pdf, 10, 3, "JLN\nMC KK");
        $pdf->Cell(10, 6,  'CONES', 1, 0, 'C');
        $pdf->Cell(10, 6,  'KG', 1, 0, 'C');
        $pdf->Cell(10, 6,  'KG', 1, 1, 'C');

        //body
        $pdf->SetFont('Arial', '', 5);
        $jumlahBaris = 50;
        $jumlahData = count($data);
        $dataIndex = 0;

        // Tambahkan variabel untuk total
        $totalJlMcKiri = 0;
        $totalConesKiri = 0;
        $totalKgKiri = 0;

        $totalJlMcKanan = 0;
        $totalConesKanan = 0;
        $totalKgKanan = 0;

        for ($i = 0; $i < $jumlahBaris; $i++) {
            // Tabel Pertama (baris 0–24)
            if ($i < 50) {
                if ($dataIndex < $jumlahData) {
                    $row = $data[$dataIndex++];
                    $jenisItem = $row['item_type'];
                    if ($jenis == 'KARET') {
                        $jenisItem = substr($row['item_type'], 6); // Buang 6 huruf pertama
                    } elseif ($jenis == 'SPANDEX') {
                        $jenisItem = substr($row['item_type'], 5); // Buang 5 huruf pertama
                    }

                    // Hitung total kiri
                    $totalJlMcKiri += $row['ttl_jl_mc'];
                    $totalConesKiri += $row['ttl_cns'];
                    $totalKgKiri += $row['ttl_kg'];

                    $pdf->Cell(20, 4, $jenisItem, 1);
                    $pdf->Cell(15, 4, $row['color'], 1);
                    $pdf->Cell(15, 4, $row['kode_warna'], 1);
                    $pdf->Cell(10, 4, $row['ttl_jl_mc'], 1);
                    $pdf->Cell(10, 4, $row['ttl_cns'], 1);
                    $pdf->Cell(10, 4, $row['ttl_kg'], 1);
                    $pdf->Cell(10, 4, '', 1);
                } else {
                    // Baris kosong
                    $pdf->Cell(20, 4, '', 1);
                    $pdf->Cell(15, 4, '', 1);
                    $pdf->Cell(15, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                }

                $pdf->Cell(10, 4, '', 0); // pemisah

                // Tabel Kedua (baris 0–24 tidak diisi)
                $pdf->Cell(20, 4, '', 1);
                $pdf->Cell(15, 4, '', 1);
                $pdf->Cell(15, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
            } else {
                // Tabel Pertama (baris 25–49 tidak diisi)
                $pdf->Cell(20, 4, '', 1);
                $pdf->Cell(15, 4, '', 1);
                $pdf->Cell(15, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);
                $pdf->Cell(10, 4, '', 1);

                $pdf->Cell(10, 4, '', 0); // pemisah

                // Tabel Kedua (baris 25–49)
                if ($dataIndex < $jumlahData) {
                    $row = $data[$dataIndex++];
                    $jenisItem = $row['item_type'];
                    if ($jenis == 'KARET') {
                        $jenisItem = substr($row['item_type'], 6); // Buang 6 huruf pertama
                    } elseif ($jenis == 'SPANDEX') {
                        $jenisItem = substr($row['item_type'], 5); // Buang 5 huruf pertama
                    }

                    // Hitung total kanan
                    $totalJlMcKanan += $row['ttl_jl_mc'];
                    $totalConesKanan += $row['ttl_cns'];
                    $totalKgKanan += $row['ttl_kg'];

                    $pdf->Cell(20, 4, $jenisItem, 1);
                    $pdf->Cell(15, 4, $row['color'], 1);
                    $pdf->Cell(15, 4, $row['kode_warna'], 1);
                    $pdf->Cell(10, 4, $row['ttl_jl_mc'], 1);
                    $pdf->Cell(10, 4, $row['ttl_cns'], 1);
                    $pdf->Cell(10, 4, $row['ttl_kg'], 1);
                    $pdf->Cell(10, 4, '', 1);
                } else {
                    // Baris kosong
                    $pdf->Cell(20, 4, '', 1);
                    $pdf->Cell(15, 4, '', 1);
                    $pdf->Cell(15, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                    $pdf->Cell(10, 4, '', 1);
                }
            }

            $pdf->Ln(); // pindah baris
        }

        // BARIS TOTAL – TABEL KIRI
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->Cell(20, 5, '', 0);
        $pdf->Cell(15, 5, '', 0);
        $pdf->Cell(15, 5, '', 0);
        $pdf->Cell(10, 5, $totalJlMcKiri, 0, 0, 'C'); // total jln mc KK
        $pdf->Cell(10, 5, $totalConesKiri, 0, 0, 'C'); // total cones
        $pdf->Cell(10, 5, $totalKgKiri, 0, 0, 'C'); // total kg
        $pdf->Cell(10, 5, '', 0, 0, 'C'); // total kg

        // Pemisah antar tabel
        $pdf->Cell(10, 5, '', 0);

        // BARIS TOTAL – TABEL KANAN
        $pdf->Cell(20, 5, '', 0);
        $pdf->Cell(15, 5, '', 0);
        $pdf->Cell(15, 5, '', 0);
        $pdf->Cell(10, 5, $totalJlMcKanan, 0, 0, 'C'); // total jln mc KK
        $pdf->Cell(10, 5, $totalConesKanan, 0, 0, 'C'); // total cones
        $pdf->Cell(10, 5, $totalKgKanan, 0, 0, 'C'); // total kg
        $pdf->Cell(10, 5, '', 0, 0, 'C'); // total kg

        $pdf->Ln(8); // spasi setelah tabel

        // FOOTER
        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(190, 5, 'PERMINTAAN U/ SINGLE COVER 1 CNS : 0.80 KG', 0, 1, 'L');

        $pdf->Cell(50, 5, '', 0, 0, 'L');
        $pdf->Cell(10, 5, '', 0, 0, 'L');
        $pdf->Cell(10, 5, 'MC', 0, 0, 'L');
        $pdf->Cell(10, 5, $totalJlMcKiri + $totalJlMcKanan, 0, 0, 'R'); // TOTAL MC
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(90, 5, 'Yang Bertanggung jawab', 0, 1, 'C');

        $pdf->Cell(50, 5, '', 0, 0, 'L');
        $pdf->Cell(10, 5, 'TOTAL', 0, 0, 'L');
        $pdf->Cell(10, 5, 'CONES', 0, 0, 'L');
        $pdf->Cell(10, 5, $totalConesKiri +  $totalConesKanan, 0, 0, 'R'); // TOTAL CONES
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(90, 5, '', 0, 1, 'C');

        $pdf->Cell(50, 5, '', 0, 0, 'L');
        $pdf->Cell(10, 5, '', 0, 0, 'L');
        $pdf->Cell(10, 5, 'KG', 0, 0, 'L');
        $pdf->Cell(10, 5, $totalKgKiri + $totalKgKanan, 0, 0, 'R'); // TOTAL KG
        $pdf->Cell(20, 5, '', 0, 0, 'L');
        $pdf->Cell(90, 5, '', 0, 1, 'C');

        $pdf->Cell(100, 5, '', 0, 0, 'C');
        $pdf->Cell(90, 5, '(__________________)', 0, 1, 'C');


        // Output PDF
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function generateOpenPOBooking()
    {
        $tujuan = $this->request->getGet('tujuan');

        $noModel = $this->request->getGet('no_model');
        $delivery = $this->request->getGet('delivery');
        $materialType = $this->request->getGet('material_type');
        $noOrder = $this->request->getGet('no_order');
        // dd($noOrder);
        $result = $this->openPoModel->getPoBookingByNoModel($noModel);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 7);  // KITA MATIKAN AUTO PAGE BREAK
        $pdf->AddPage();
        $pdf->SetTitle($noModel);

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // CETAK HEADER halaman pertama
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $noModel, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . 'KAOS KAKI', 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");

        $startYAfterHeader = $pdf->GetY();  // posisi Y tepat setelah header
        // $pdf->SetY($startYAfterHeader);
        $pdf->SetY($pdf->GetY());

        // == START ISI TABEL ==
        $pdf->SetFont('Arial', '', 6);
        $no                 = 1;
        $yLimit             = 145;
        $lineHeight         = 3;      // tinggi dasar setiap baris MultiCell

        // Total tinggi baris untuk memastikan 15 baris jika perlu kosong
        $totalKg                = 0;
        $totalPermintaanCones   = 0;
        $totalYard              = 0;
        $totalCones             = 0;

        $prevDelivery = '';
        $prevBuyer    = '';
        $prevNoOrder  = '';

        foreach ($result as $row) {
            // Cek dulu apakah nambah baris ini bakal lewat batas
            if ($pdf->GetY() + $lineHeight > $yLimit) {
                $this->generateFooterOpenPOBooking($pdf, $tujuan, $result, $penerima);
                $pdf->AddPage();
                $this->generateHeaderOpenPOBooking($pdf, $result, $noModel);

                // Gambar ulang margin & header halaman
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2); // Lebih tebal
                $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam
                $pdf->SetLineWidth(0.1); // Lebih tipis
                $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

                $pdf->SetFont('Arial', '', 6);
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // 1. SIMULASI: Hitung tinggi maksimum yang dibutuhkan
            $heights = [];
            $tempX = $startX;

            $pdf->SetTextColor(255, 255, 255); // putih agar tidak terlihat

            $multiCellData = [
                ['w' => 25, 'text' => $row['spesifikasi_benang']
                    ? $row['item_type'] . ' (' . $row['spesifikasi_benang'] . ')'
                    : $row['item_type']],
                ['w' => 12, 'text' => $row['ukuran']],
                ['w' => 12, 'text' => $row['bentuk_celup']],
                ['w' => 20, 'text' => $row['color']],
                ['w' => 20, 'text' => $row['kode_warna']],
                // ['w' => 20, 'text' => $row['buyer']] ?? '',
                // ['w' => 20, 'text' => $row['buyer']],
                ['w' => 20, 'text' => ''], //No Order
                ['w' => 22, 'text' => $row['jenis_produksi']],
                ['w' => 22, 'text' => $row['contoh_warna']],
                ['w' => 22, 'text' => $row['ket_celup']],
            ];

            // 1. Tentukan lineHeight: 5 pt jika semuanya muat satu baris, 3 pt kalau ada yg meluber
            $singleLine = true;
            foreach ($multiCellData as $cell) {
                // GetStringWidth menghasilkan lebar dalam satuan point
                if ($pdf->GetStringWidth($cell['text']) > $cell['w']) {
                    $singleLine = false;
                    break;
                }
            }
            $lineHeight = $singleLine ? 5 : 3;

            foreach ($multiCellData as $data) {
                $pdf->SetXY($tempX, $startY);
                $y0 = $pdf->GetY();
                $pdf->MultiCell($data['w'], $lineHeight, $data['text'], 0, 'C');
                $heights[] = $pdf->GetY() - $y0;
                $tempX += $data['w'];
            }

            $pdf->SetTextColor(0, 0, 0); // kembali ke hitam
            $maxHeight = max($heights);

            // 2. RENDER: Gambar semua cell dengan tinggi yang sama
            $pdf->SetXY($startX, $startY);

            // Buat semua cell dengan border dan tinggi yang sama, tapi isi kosong dulu
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            $cellData = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $delivery, // Delivery
                number_format($row['kg_po'], 2) ?? 0,
                number_format($row['kg_percones'], 2) ?? 0,
                '',
                $row['jumlah_cones'] ?? 0,
                '',
                '',
                '',
                ''
            ];

            // Gambar semua cell dengan border
            for ($i = 0; $i < count($cellWidths); $i++) {
                $pdf->Cell($cellWidths[$i], $maxHeight, $cellData[$i], 1, 0, 'C');
            }

            // 3. ISI TEXT untuk kolom multiCell (overlay tanpa border)
            $currentX = $startX;

            // No
            $textCenterY = $startY + ($maxHeight / 2) - ($lineHeight / 2);
            $pdf->SetXY($currentX, $textCenterY);
            $pdf->Cell(6, $lineHeight, $no, 0, 0, 'C');
            $currentX += 6;

            // Item Type (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . $row['spesifikasi_benang'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . ' ' . $row['spesifikasi_benang'], 0, 'C');
            $currentX += 25;

            // Ukuran (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $currentX += 12;

            // Bentuk (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $currentX += 12;

            // Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $currentX += 20;

            // Kode Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $currentX += 20;

            // Buyer (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['buyer'] ?? '', 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight,  $row['buyer'], 0, 'C');
            $currentX += 20;

            // No Order (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $noOrder, 0, 'C'); //No Order
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $noOrder, 0, 'C'); // No Order
            $currentX += 20;

            // Skip kolom yang sudah terisi dengan Cell biasa
            $currentX += 16 + 15 + 13 + 13 + 13 + 13;

            // Untuk Produksi (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $currentX += 22;

            // Contoh Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $currentX += 22;

            // Keterangan (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');

            // Pindah ke baris berikutnya
            $pdf->SetY($startY + $maxHeight);

            $no++;

            $totalKg              += floatval($row['kg_po'] ?? 0);
            $totalPermintaanCones += floatval($row['kg_percones'] ?? 0);
            $totalCones           += floatval($row['jumlah_cones'] ?? 0);
        }

        $currentY = $pdf->GetY();
        $footerY = 145; // batas sebelum footer (tergantung desain kamu)

        // Tinggi standar baris kosong (bisa sesuaikan ke $maxHeight rata-rata atau tetap 6 misal)
        $emptyRowHeight = 5;

        // Selama posisi Y masih di atas footer, tambahkan baris kosong
        while ($currentY + $emptyRowHeight < $footerY) {
            $startX = $pdf->GetX();
            $pdf->SetXY($startX, $currentY);

            // Gambar semua cell border kosong
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            foreach ($cellWidths as $width) {
                $pdf->Cell($width, $emptyRowHeight, '', 1, 0, 'C');
            }
            $pdf->Ln($emptyRowHeight);

            $currentY = $pdf->GetY();
        }

        //  Baris TOTAL (baris ke-16)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(6 + 25 + 12 + 17 + 20 + 20 + 10 + 25 + 16, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(15, 6, number_format($totalKg, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalPermintaanCones, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalYard, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalCones), 1, 0, 'C');
        $pdf->Cell(13, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 1, 'C');


        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        $pdf->SetFillColor(255, 255, 255); // Atur warna latar belakang menjadi putih
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->MultiCell(117, 5, ': ' . $result[0]['keterangan'], 0, 1, 'L');
        } else {
            $pdf->MultiCell(117, 5, ': ', 0, 1, 'L');
        }

        // FOOTER
        $this->generateFooterOpenPOBooking($pdf, $tujuan, $result, $penerima);

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function generateHeaderOpenPOBooking($pdf, $result, $noModel)
    {
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $noModel, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . 'Kaos Kaki', 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");
        $pdf->SetY(53);
    }

    private function generateFooterOpenPOBooking($pdf, $tujuan, $result)
    {
        // konfigurasi
        $bottomMargin = 7;      // harus sama dengan SetAutoPageBreak
        $footerHeight = 35;     // tinggi area footer secara keseluruhan

        // hitung Y = (tinggi halaman) - margin bawah - footer block
        $y = $pdf->GetPageHeight() - $bottomMargin - $footerHeight;
        $pdf->SetY($y);
        $pdf->SetFont('Arial', '', 6);

        // Baris kosong
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // Judul departemen
        $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        // Judul kolom tanda tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        // Spasi untuk tanda tangan
        $pdf->Cell(55, 12, '', 0, 1, 'C');

        $admin = $result[0]['admin'] ?? '';

        // Garis tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(       ' . $admin . '       )', 0, 0, 'C');
        if (!empty($result)) {
            $pdf->Cell(55, 5, '(       ' . $result[0]['penanggung_jawab'] . '      )', 0, 0, 'C');
        } else {
            $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . 'Retno' . '       )', 0, 1, 'C');
    }

    public function generateOpenPOManual()
    {
        $tujuan = $this->request->getGet('tujuan');
        $noModel = $this->request->getGet('no_model');
        $newDel = $this->request->getGet('delivery');
        $materialType = $this->request->getGet('material_type');
        $result = $this->openPoModel->getPoManualByNoModel($noModel);

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 7);  // KITA MATIKAN AUTO PAGE BREAK
        $pdf->AddPage();
        $pdf->SetTitle($noModel);

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // CETAK HEADER halaman pertama
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $noModel, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . 'KAOS KAKI', 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        function MultiCellFit($pdf, $w, $h, $txt, $border = 1, $align = 'C')
        {
            // Simpan posisi awal
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // Simulasikan MultiCell tetapi tetap pakai tinggi tetap (12)
            $pdf->MultiCell($w, $h, $txt, $border, $align);

            // Kembalikan ke kanan cell agar sejajar
            $pdf->SetXY($x + $w, $y);
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");

        $startYAfterHeader = $pdf->GetY();  // posisi Y tepat setelah header
        // $pdf->SetY($startYAfterHeader);
        $pdf->SetY($pdf->GetY());

        // == START ISI TABEL ==
        $pdf->SetFont('Arial', '', 6);
        $no                 = 1;
        $yLimit             = 145;
        $lineHeight         = 3;      // tinggi dasar setiap baris MultiCell

        // Total tinggi baris untuk memastikan 15 baris jika perlu kosong
        $totalKg                = 0;
        $totalPermintaanCones   = 0;
        $totalYard              = 0;
        $totalCones             = 0;
        $prevDelivery = '';
        // dd($result);
        foreach ($result as $row) {
            $delivery = $newDel ?? '';
            if ($delivery === $prevDelivery) {
                $displayDelivery = '';
            } else {
                $displayDelivery = $delivery;
                $prevDelivery    = $delivery;
            }
            // Cek dulu apakah nambah baris ini bakal lewat batas
            if ($pdf->GetY() + $lineHeight > $yLimit) {
                $this->generateFooterOpenPOManual($pdf, $tujuan, $result, $penerima);
                $pdf->AddPage();
                $this->generateHeaderOpenPOManual($pdf, $result, $noModel);

                // Gambar ulang margin & header halaman
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2); // Lebih tebal
                $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam
                $pdf->SetLineWidth(0.1); // Lebih tipis
                $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

                $pdf->SetFont('Arial', '', 6);
            }

            $startX = $pdf->GetX();
            $startY = $pdf->GetY();

            // 1. SIMULASI: Hitung tinggi maksimum yang dibutuhkan
            $heights = [];
            $tempX = $startX;

            $pdf->SetTextColor(255, 255, 255); // putih agar tidak terlihat

            $multiCellData = [
                ['w' => 25, 'text' => $row['spesifikasi_benang']
                    ? $row['item_type'] . ' (' . $row['spesifikasi_benang'] . ')'
                    : $row['item_type']],
                ['w' => 12, 'text' => $row['ukuran']],
                ['w' => 12, 'text' => $row['bentuk_celup']],
                ['w' => 20, 'text' => $row['color']],
                ['w' => 20, 'text' => $row['kode_warna']],
                ['w' => 20, 'text' => $row['buyer']],
                // ['w' => 20, 'text' => $row['buyer']],
                ['w' => 20, 'text' => $row['no_order']], //No Order
                ['w' => 22, 'text' => $row['jenis_produksi']],
                ['w' => 22, 'text' => $row['contoh_warna']],
                ['w' => 22, 'text' => $row['ket_celup']],
            ];

            // 1. Tentukan lineHeight: 5 pt jika semuanya muat satu baris, 3 pt kalau ada yg meluber
            $singleLine = true;
            foreach ($multiCellData as $cell) {
                // GetStringWidth menghasilkan lebar dalam satuan point
                if ($pdf->GetStringWidth($cell['text']) > $cell['w']) {
                    $singleLine = false;
                    break;
                }
            }
            $lineHeight = $singleLine ? 5 : 3;

            foreach ($multiCellData as $data) {
                $pdf->SetXY($tempX, $startY);
                $y0 = $pdf->GetY();
                $pdf->MultiCell($data['w'], $lineHeight, $data['text'], 0, 'C');
                $heights[] = $pdf->GetY() - $y0;
                $tempX += $data['w'];
            }
            // dd($data);
            $pdf->SetTextColor(0, 0, 0); // kembali ke hitam
            $maxHeight = max($heights);

            // 2. RENDER: Gambar semua cell dengan tinggi yang sama
            $pdf->SetXY($startX, $startY);

            // Buat semua cell dengan border dan tinggi yang sama, tapi isi kosong dulu
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            $cellData = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $displayDelivery, // Delivery
                number_format($row['kg_po'], 2) ?? 0,
                number_format($row['kg_percones'], 2) ?? 0,
                '',
                $row['jumlah_cones'] ?? 0,
                '',
                '',
                '',
                ''
            ];

            // Gambar semua cell dengan border
            for ($i = 0; $i < count($cellWidths); $i++) {
                $pdf->Cell($cellWidths[$i], $maxHeight, $cellData[$i], 1, 0, 'C');
            }

            // 3. ISI TEXT untuk kolom multiCell (overlay tanpa border)
            $currentX = $startX;

            // No
            $textCenterY = $startY + ($maxHeight / 2) - ($lineHeight / 2);
            $pdf->SetXY($currentX, $textCenterY);
            $pdf->Cell(6, $lineHeight, $no, 0, 0, 'C');
            $currentX += 6;

            // Item Type (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . $row['spesifikasi_benang'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(25, $lineHeight, $row['item_type'] . ' ' . $row['spesifikasi_benang'], 0, 'C');
            $currentX += 25;

            // Ukuran (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['ukuran'], 0, 'C');
            $currentX += 12;

            // Bentuk (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(12, $lineHeight, $row['bentuk_celup'], 0, 'C');
            $currentX += 12;

            // Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['color'], 0, 'C');
            $currentX += 20;

            // Kode Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['kode_warna'], 0, 'C');
            $currentX += 20;

            // Buyer (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['buyer'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight,  $row['buyer'], 0, 'C');
            $currentX += 20;

            // No Order (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(20, $lineHeight, $row['no_order'], 0, 'C'); //No Order
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(20, $lineHeight, $row['no_order'], 0, 'C'); // No Order
            $currentX += 20;

            // Skip kolom yang sudah terisi dengan Cell biasa
            $currentX += 16 + 15 + 13 + 13 + 13 + 13;

            // Untuk Produksi (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['jenis_produksi'], 0, 'C');
            $currentX += 22;

            // Contoh Warna (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['contoh_warna'], 0, 'C');
            $currentX += 22;

            // Keterangan (mungkin multiline)
            $pdf->SetXY($currentX, $startY);
            $pdf->SetTextColor(255, 255, 255);
            $y0 = $pdf->GetY();
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');
            $textHeight = $pdf->GetY() - $y0;
            $pdf->SetTextColor(0, 0, 0);

            $centerY = $startY + ($maxHeight - $textHeight) / 2;
            $pdf->SetXY($currentX, $centerY);
            $pdf->MultiCell(22, $lineHeight, $row['ket_celup'], 0, 'C');

            // Pindah ke baris berikutnya
            $pdf->SetY($startY + $maxHeight);

            $no++;

            $totalKg              += floatval($row['kg_po'] ?? 0);
            $totalPermintaanCones += floatval($row['kg_percones'] ?? 0);
            $totalCones           += floatval($row['jumlah_cones'] ?? 0);
        }

        $currentY = $pdf->GetY();
        $footerY = 145; // batas sebelum footer (tergantung desain kamu)

        // Tinggi standar baris kosong (bisa sesuaikan ke $maxHeight rata-rata atau tetap 6 misal)
        $emptyRowHeight = 5;

        // Selama posisi Y masih di atas footer, tambahkan baris kosong
        while ($currentY + $emptyRowHeight < $footerY) {
            $startX = $pdf->GetX();
            $pdf->SetXY($startX, $currentY);

            // Gambar semua cell border kosong
            $cellWidths = [6, 25, 12, 12, 20, 20, 20, 20, 16, 15, 13, 13, 13, 13, 22, 22, 22];
            foreach ($cellWidths as $width) {
                $pdf->Cell($width, $emptyRowHeight, '', 1, 0, 'C');
            }
            $pdf->Ln($emptyRowHeight);

            $currentY = $pdf->GetY();
        }

        //  Baris TOTAL (baris ke-16)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(6 + 25 + 12 + 17 + 20 + 20 + 10 + 25 + 16, 6, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(15, 6, number_format($totalKg, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalPermintaanCones, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalYard, 2), 1, 0, 'C');
        $pdf->Cell(13, 6, number_format($totalCones), 1, 0, 'C');
        $pdf->Cell(13, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 0, 'C');
        $pdf->Cell(22, 6, '', 1, 1, 'C');


        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        $pdf->SetFillColor(255, 255, 255); // Atur warna latar belakang menjadi putih
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->MultiCell(117, 5, ': ' . $result[0]['keterangan'], 0, 1, 'L');
        } else {
            $pdf->MultiCell(117, 5, ': ', 0, 1, 'L');
        }

        // FOOTER
        $this->generateFooterOpenPOManual($pdf, $tujuan, $result, $penerima);

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function generateHeaderOpenPOManual($pdf, $result, $noModel)
    {
        // Garis margin luar (lebih tebal)
        $pdf->SetDrawColor(0, 0, 0); // Warna hitam
        $pdf->SetLineWidth(0.2); // Lebih tebal
        $pdf->Rect(6.5, 6.5, 285, 197); // Sedikit lebih besar dari margin dalam

        // Garis margin dalam (lebih tipis)
        $pdf->SetLineWidth(0.1); // Lebih tipis
        $pdf->Rect(7, 7, 284, 196); // Ukuran aslinya

        // Masukkan gambar di dalam kolom
        $x = $pdf->GetX(); // Simpan posisi X saat ini
        $y = $pdf->GetY(); // Simpan posisi Y saat ini

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', $x + 16, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(43, 12, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(241, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(241, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(43, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(162, 4, 'FOR-CC-087/REV_02/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(44, 4, '17 Maret 2025', 1, 1, 'C');

        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(205, 4, '', 1, 0, 'L');
        $pdf->Cell(35, 4, 'Klasifikasi', 1, 0, 'L');
        $pdf->Cell(44, 4, 'Internal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(43, 5, 'PO', 0, 0, 'L');

        $pdf->SetFont('Arial', '', 10);

        if (!empty($result) && isset($result[0]['po_plus']) && $result[0]['po_plus'] == '0') {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        } elseif (!empty($result) && isset($result[0]['po_plus'])) {
            $pdf->Cell(30, 5, ': ' . '(+) ' . $noModel, 0, 1, 'L');
        } else {
            $pdf->Cell(30, 5, ': ' . $noModel, 0, 1, 'L');
        }

        $cellW1 = 20;  // lebar season
        $cellW2 = 30;  // lebar materialType
        $lineH  = 4;   // tinggi tiap baris wrap

        $seasonText = $season ?? '';
        $mtText     = $materialType ?? '';

        // Hitung tinggi masing-masing
        $nb1   = ceil($pdf->GetStringWidth($seasonText) / $cellW1);
        $nb1   = max(1, $nb1);
        $rowH1 = $nb1 * $lineH;

        $nb2   = ceil($pdf->GetStringWidth($mtText) / $cellW2);
        $nb2   = max(1, $nb2);
        $rowH2 = $nb2 * $lineH;

        $rowH = max($rowH1, $rowH2);

        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->SetFont('Arial', '', 6);

        $pdf->Cell(43, 5, 'Pemesan', 0, 0, 'L');
        $pdf->Cell(50, 5, ': ' . 'Kaos Kaki', 0, 0, 'L');

        //Simpan posisi awal Season & MaterialType
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        //Season
        $pdf->MultiCell($cellW1, $lineH, $seasonText, 0, 'C');
        $pdf->SetXY($x + $cellW1, $y);

        //Material Type
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->MultiCell($cellW2, $lineH, $mtText, 0, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($startX, $startY + $rowH);

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $tgl_po = $result[0]['tgl_po'];
            $tgl_formatted = date('d-m-Y', strtotime($tgl_po));
            $pdf->Cell(234, 5, ': ' . $tgl_formatted, 0, 1, 'L');
        } else {
            $pdf->Cell(234, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 6);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 12, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 6, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        MultiCellFit($pdf, 12, 6, "Bentuk\nCelup");
        $pdf->Cell(20, 12, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 12, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 12, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        MultiCellFit($pdf, 15, 3, "Qty\nPesanan");
        $pdf->Cell(52, 6, 'Permintaan Kelos', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Untuk Produksi', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Contoh Warna', 1, 0, 'C');
        $pdf->Cell(22, 12, 'Keterangan Celup', 1, 1, 'C');

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(25, -6, 'Jenis', 1, 0, 'C');
        $pdf->Cell(12, -6, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -6, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -6, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -6, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -6, 'Yard', 1, 0, 'C');
        MultiCellFit($pdf, 13, -3, "Cones\nTotal");
        MultiCellFit($pdf, 13, -3, "Jenis\nCones");
        $pdf->SetY(53);
    }

    private function generateFooterOpenPOManual($pdf, $tujuan, $result)
    {
        // konfigurasi
        $bottomMargin = 7;      // harus sama dengan SetAutoPageBreak
        $footerHeight = 35;     // tinggi area footer secara keseluruhan

        // hitung Y = (tinggi halaman) - margin bawah - footer block
        $y = $pdf->GetPageHeight() - $bottomMargin - $footerHeight;
        $pdf->SetY($y);
        $pdf->SetFont('Arial', '', 6);

        // Baris kosong
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        // Judul departemen
        $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        // Judul kolom tanda tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        // Spasi untuk tanda tangan
        $pdf->Cell(55, 12, '', 0, 1, 'C');

        $admin = $result[0]['admin'] ?? '';

        // Garis tangan
        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(       ' . $admin . '       )', 0, 0, 'C');
        if (!empty($result)) {
            $pdf->Cell(55, 5, '(       ' . $result[0]['penanggung_jawab'] . '      )', 0, 0, 'C');
        } else {
            $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . 'Retno' . '       )', 0, 1, 'C');
    }

    // PERSIAPAN BARANG KELUAR BENANG / NYLON 
    public function exportListBarangKeluar()
    {
        $jenis = $this->request->getGet('jenis');
        $tglPakai = $this->request->getGet('tglPakai');

        $dataPemesanan = $this->pengeluaranModel->getDataPemesananExport($jenis, $tglPakai);
        // dd($dataPemesanan);
        // Grouping data
        $groupedData = [];
        foreach ($dataPemesanan as $row) {
            $groupedData[$row['group']][] = $row;
        }
        // dd($groupedData);

        $pdf = new FPDF('L', 'mm', 'A4'); // Landscape
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak(true, 10);

        foreach ($groupedData as $group => $rows) {

            $pdf->AddPage();

            if ($group == "barang_jln") {
                $group = "LAIN - LAIN";
            }

            // === HEADER TITLE ===
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, "CLUSTER GROUP " . strtoupper($group), 0, 1, 'C');
            $pdf->Cell(0, 8, "PAKAI " . $tglPakai, 0, 1, 'C');
            $pdf->Ln(3);

            // === HEADER TABEL ===
            $pdf->SetFont('Arial', 'B', 9);
            $header = [
                'Area',
                'No Model',
                'Item Type',
                'Kode Warna',
                'Color',
                "Qty/Cns\nPesan",
                'Lot',
                'No Karung',
                'Kgs',
                'Cns',
                'Krg',
                'Nama Cluster',
                'Kgs Out',
                'Cns Out',
                'Keterangan'
            ];
            $widths = [13, 17, 30, 23, 23, 20, 18, 15, 15, 12, 12, 22, 14, 14, 30];

            foreach ($header as $i => $col) {
                if ($i == 5) { // index ke-5 adalah Qty/Cns Pesan
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($widths[$i], 4, $col, 1, 'C');
                    $pdf->SetXY($x + $widths[$i], $y);
                } else {
                    $pdf->Cell($widths[$i], 8, $col, 1, 0, 'C');
                }
            }
            $pdf->Ln();

            // === ISI DATA ===
            $pdf->SetFont('Arial', '', 8);
            $currentCluster = '';
            $clusterRows = [];

            foreach ($rows as $row) {
                // Kalau nama_cluster kosong/null → langsung cetak satu baris biasa
                if (empty($row['nama_cluster'])) {
                    // Jika sebelumnya ada blok yang pending, render dulu
                    if (!empty($clusterRows)) {
                        $this->renderClusterBlock($pdf, $clusterRows, $widths);
                        $clusterRows = [];
                    }

                    // Cetak sebagai baris normal (tanpa merge)
                    $this->renderSingleRow($pdf, $row, $widths);
                    continue;
                }

                // === Kalau nama_cluster tidak null → lanjutkan proses merge ===
                if ($currentCluster !== '' && $currentCluster !== $row['nama_cluster']) {
                    // Render block untuk cluster sebelumnya
                    $this->renderClusterBlock($pdf, $clusterRows, $widths);
                    $clusterRows = [];
                }

                $clusterRows[] = $row;
                $currentCluster = $row['nama_cluster'];
            }
            // Render blok terakhir
            if (!empty($clusterRows)) {
                $this->renderClusterBlock($pdf, $clusterRows, $widths);
            }
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    private function renderTableHeader($pdf, $widths)
    {
        $pdf->SetFont('Arial', 'B', 9);
        $header = [
            'Area',
            'No Model',
            'Item Type',
            'Kode Warna',
            'Color',
            "Qty/Cns\nPesan",
            'Lot',
            'No Karung',
            'Kgs',
            'Cns',
            'Krg',
            'Nama Cluster',
            'Kgs Out',
            'Cns Out',
            'Keterangan'
        ];

        // simpan posisi awal X untuk memastikan konsistensi
        $pdf->SetX(10); // jika kamu punya margin kiri tertentu, sesuaikan
        foreach ($header as $i => $col) {
            if ($i == 5) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($widths[$i], 4, $col, 1, 'C');
                $pdf->SetXY($x + $widths[$i], $y);
            } else {
                $pdf->Cell($widths[$i], 8, $col, 1, 0, 'C');
            }
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8);
    }

    function renderSingleRow($pdf, $row, $widths)
    {
        $rowHeight = 6;
        $pageLimit = 200;

        // Cek posisi Y sebelum render row
        if ($pdf->GetY() + 10 > $pageLimit) { // +10 margin
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 9);
            $header = [
                'Area',
                'No Model',
                'Item Type',
                'Kode Warna',
                'Color',
                "Qty/Cns\nPesan",
                'Lot',
                'No Karung',
                'Kgs',
                'Cns',
                'Krg',
                'Nama Cluster',
                'Kgs Out',
                'Cns Out',
                'Keterangan'
            ];
            $widths = [13, 17, 30, 23, 23, 20, 18, 15, 15, 12, 12, 22, 14, 14, 30];

            foreach ($header as $i => $col) {
                if ($i == 5) { // index ke-5 adalah Qty/Cns Pesan
                    $x = $pdf->GetX();
                    $y = $pdf->GetY();
                    $pdf->MultiCell($widths[$i], 4, $col, 1, 'C');
                    $pdf->SetXY($x + $widths[$i], $y);
                } else {
                    $pdf->Cell($widths[$i], 8, $col, 1, 0, 'C');
                }
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        }

        $columns = [
            ['text' => $row['admin'],           'w' => $widths[0],  'multi' => true],
            ['text' => $row['no_model'],        'w' => $widths[1],  'multi' => false],
            ['text' => $row['item_type'],       'w' => $widths[2],  'multi' => true],
            ['text' => $row['kode_warna'],      'w' => $widths[3],  'multi' => true],
            ['text' => $row['color'],           'w' => $widths[4],  'multi' => true],
            ['text' => $row['pesanan'],         'w' => $widths[5],  'multi' => true],
            ['text' => $row['lot_out'],         'w' => $widths[6],  'multi' => true],
            ['text' => $row['no_karung'],       'w' => $widths[7],  'multi' => false],
            ['text' => $row['kgs_out'],         'w' => $widths[8],  'multi' => false],
            ['text' => $row['cns_out'],         'w' => $widths[9],  'multi' => false],
            ['text' => $row['krg_out'],         'w' => $widths[10], 'multi' => false],
            ['text' => $row['nama_cluster'],    'w' => $widths[11], 'multi' => false],
            ['text' => '',                       'w' => $widths[12], 'multi' => false],
            ['text' => '',                       'w' => $widths[13], 'multi' => false],
            ['text' => $row['keterangan_gbn'],  'w' => $widths[14], 'multi' => true],
        ];

        // Hitung maxHeight per row
        $lineHeight = 4;
        $maxHeight = 0;
        foreach ($columns as $col) {
            if ($col['multi']) {
                $nb = ceil($pdf->GetStringWidth($col['text']) / $col['w']);
                $height = max($nb * $lineHeight, 6);
            } else {
                $height = 6;
            }
            if ($height > $maxHeight) $maxHeight = $height;
        }

        // Auto page break
        if ($pdf->GetY() + $maxHeight > $pdf->GetPageHeight() - 10) {
            $pdf->AddPage();
        }

        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        // Border
        foreach ($columns as $col) {
            $pdf->Cell($col['w'], $maxHeight, '', 1, 0, 'C');
        }
        $pdf->Ln();

        // Isi teks
        $x = $xStart;
        $y = $yStart;
        foreach ($columns as $col) {
            $pdf->SetXY($x, $y);
            if ($col['multi']) {
                $pdf->MultiCell($col['w'], $lineHeight, $col['text'], 0, 'C');
            } else {
                $pdf->Cell($col['w'], $maxHeight, $col['text'], 0, 0, 'C');
            }
            $x += $col['w'];
        }

        $pdf->SetXY($xStart, $yStart + $maxHeight);
    }

    private function renderClusterBlock($pdf, $rows, $widths, $lineHeight = 4)
    {
        if (empty($rows)) return;

        $pageHeight = $pdf->GetPageHeight();
        $bottomMargin = 10;

        // Hitung rowHeight masing-masing row
        $rowHeights = [];
        foreach ($rows as $r) {
            $nbLot = $this->getMultiCellHeight($pdf, $widths[6], $lineHeight, $r['lot_out']);
            $nbKet = $this->getMultiCellHeight($pdf, $widths[14], $lineHeight, $r['keterangan_gbn']);
            $rowHeights[] = max($nbLot, $nbKet, 6);
        }

        // Hitung total tinggi cluster
        $mergeHeight = array_sum($rowHeights);

        // 🚨 Cek dulu: apakah cluster muat di halaman sekarang?
        if ($pdf->GetY() + $mergeHeight > $pageHeight - $bottomMargin) {
            $pdf->AddPage();
            $this->renderTableHeader($pdf, $widths); // header ulang
        }

        // Posisi awal cluster
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // --- Render kolom 0–5 merge (sekali saja) ---
        $pdf->SetXY($x, $y);
        $pdf->Cell($widths[0], $mergeHeight, $rows[0]['admin'], 1, 0, 'C');
        $pdf->Cell($widths[1], $mergeHeight, $rows[0]['no_model'], 1, 0, 'C');
        $pdf->Cell($widths[2], $mergeHeight, $rows[0]['item_type'], 1, 0, 'C');
        $pdf->Cell($widths[3], $mergeHeight, $rows[0]['kode_warna'], 1, 0, 'C');
        $pdf->Cell($widths[4], $mergeHeight, $rows[0]['color'], 1, 0, 'C');
        $pdf->Cell($widths[5], $mergeHeight, $rows[0]['pesanan'], 1, 0, 'C');

        // --- Render kolom 6–14 per row ---
        $xStart = $x + array_sum(array_slice($widths, 0, 6));
        $yStart = $y;

        foreach ($rows as $i => $r) {
            $rowHeight = $rowHeights[$i];
            $curX = $xStart;
            $curY = $yStart;

            // Lot
            $pdf->SetXY($curX, $curY);
            $pdf->MultiCell($widths[6], $lineHeight, $r['lot_out'], 1, 'C');
            $curX += $widths[6];

            // Angka-angka
            $pdf->SetXY($curX, $curY);
            $pdf->Cell($widths[7], $rowHeight, $r['no_karung'], 1, 0, 'C');
            $curX += $widths[7];
            $pdf->Cell($widths[8], $rowHeight, $r['kgs_out'], 1, 0, 'C');
            $curX += $widths[8];
            $pdf->Cell($widths[9], $rowHeight, $r['cns_out'], 1, 0, 'C');
            $curX += $widths[9];
            $pdf->Cell($widths[10], $rowHeight, $r['krg_out'], 1, 0, 'C');
            $curX += $widths[10];
            $pdf->Cell($widths[11], $rowHeight, $r['nama_cluster'], 1, 0, 'C');
            $curX += $widths[11];
            $pdf->Cell($widths[12], $rowHeight, '', 1, 0, 'C');
            $curX += $widths[12];
            $pdf->Cell($widths[13], $rowHeight, '', 1, 0, 'C');
            $curX += $widths[13];

            // Keterangan
            $pdf->SetXY($curX, $curY);
            $pdf->MultiCell($widths[14], $lineHeight, $r['keterangan_gbn'], 1, 'L');

            // Geser pointer ke row berikut
            $yStart += $rowHeight;
            $pdf->SetXY($x, $yStart);
        }
    }

    private function getMultiCellHeight($pdf, $w, $h, $txt,  $cMargin = 2)
    {
        $wmax = $w - 2 * $cMargin;  // pakai default margin 2
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;

        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }

            $l += $pdf->GetStringWidth($c);

            if ($c == ' ') {
                $sep = $i;
            }

            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl * $h;
    }

    // function renderClusterBlock($pdf, $rows, $widths)
    // {
    //     $rowHeight = 6;
    //     $blockHeight = $rowHeight * count($rows);

    //     $first = $rows[0];

    //     // Kolom yang di-merge (multi row)
    //     $pdf->MultiCell($widths[0], $blockHeight, $first['admin'], 1, 'C');
    //     $x = $pdf->GetX();
    //     $y = $pdf->GetY() - $blockHeight;
    //     $pdf->SetXY($x + $widths[0], $y);
    //     $pdf->MultiCell($widths[1], $blockHeight, $first['no_model'], 1, 'C'); //multi cell
    //     $pdf->SetXY($x + $widths[0] + $widths[1], $y);
    //     $pdf->MultiCell($widths[2], $blockHeight, $first['item_type'], 1, 'C'); //multi cell
    //     $pdf->SetXY($x + array_sum(array_slice($widths, 0, 3)), $y);
    //     $pdf->MultiCell($widths[3], $blockHeight, $first['kode_warna'], 1, 'C'); //multi cell
    //     $pdf->SetXY($x + array_sum(array_slice($widths, 0, 4)), $y);
    //     $pdf->MultiCell($widths[4], $blockHeight, $first['color'], 1, 'C'); //multi cell
    //     $pdf->SetXY($x + array_sum(array_slice($widths, 0, 5)), $y);
    //     $pdf->MultiCell($widths[5], $blockHeight, $first['pesanan'], 1, 'C');

    //     // Kolom lainnya per baris
    //     $xStart = $x + array_sum(array_slice($widths, 0, 6));
    //     $yStart = $y;

    //     foreach ($rows as $row) {
    //         $pdf->SetXY($xStart, $yStart);
    //         $pdf->Cell($widths[6], $rowHeight, $row['lot_out'], 1, 0, 'C'); //multi cell
    //         $pdf->Cell($widths[7], $rowHeight, $row['no_karung'], 1, 0, 'C');
    //         $pdf->Cell($widths[8], $rowHeight, $row['kgs_out'], 1, 0, 'C');
    //         $pdf->Cell($widths[9], $rowHeight, $row['cns_out'], 1, 0, 'C');
    //         $pdf->Cell($widths[10], $rowHeight, $row['krg_out'], 1, 0, 'C');
    //         $pdf->Cell($widths[11], $rowHeight, $row['nama_cluster'], 1, 0, 'C');
    //         $pdf->Cell($widths[12], $rowHeight, '', 1, 0, 'C');
    //         $pdf->Cell($widths[13], $rowHeight, '', 1, 0, 'C');
    //         $pdf->Cell($widths[14], $rowHeight, $row['keterangan_gbn'], 1, 0, 'C'); //multi cell
    //         $pdf->Ln();
    //         $yStart += $rowHeight;
    //     }
    // }

    // PERSIAPAN BARANG KELUAR SPANDEX / KARET
    public function exportListPemesananSpdxKaretPertgl()
    {
        $key   = $this->request->getGet('tglPakai');
        $jenis = $this->request->getGet('jenis');

        $allData = $this->pemesananModel->getDataPemesananPerArea($key, $jenis);

        // Grouping per admin
        $grouped = [];
        foreach ($allData as $item) {
            $admin = $item['admin'] ?: 'Unknown';
            $grouped[$admin][] = $item;
        }

        $pdf = new FPDF('L', 'mm', 'A4'); // Landscape
        $pdf->SetAutoPageBreak(true, 10);

        foreach ($grouped as $adminName => $items) {
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 10, 'REPORT PERMINTAAN BAHAN BAKU', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(120, 8, 'Jenis Bahan Baku: ' . $jenis, 0, 0);
            $pdf->Cell(120, 8, 'Area: ' . $adminName, 0, 0);
            $pdf->Cell(60, 8, 'Tanggal Pakai: ' . $key, 0, 1);

            // Header kolom
            $headers = ['NO', 'JAM', 'TGL PESAN', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'LOT', 'JL MC', 'TOTAL', 'CONES', 'KETERANGAN', 'BAGIAN PERSIAPAN', 'QTY OUT', 'CNS OUT'];
            $widths  = [8, 13, 18, 17, 35, 25, 25, 10, 12, 15, 12, 30, 30, 15, 15];
            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $h) {
                $pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
            }
            $pdf->Ln();

            // Data
            $pdf->SetFont('Arial', '', 8);
            $no = 1;
            foreach ($items as $item) {
                if ($item['po_tambahan']) {
                    $item['no_model'] = $item['no_model'] . ' (+)';
                }
                $row = $item;
                $row['no'] = $no++;

                $this->renderSingle($pdf, $row, $widths);
            }
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    /** Render satu baris (private helper) */
    private function renderSingle($pdf, $row, $widths)
    {
        $lineHeight = 4; // sesuai request
        $pageLimit = 200;

        // Cek posisi Y sebelum render row
        if ($pdf->GetY() + 10 > $pageLimit) { // +10 margin
            $pdf->AddPage();

            // Header judul tabel (ulang)
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 10, 'REPORT PERMINTAAN BAHAN BAKU', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(120, 8, 'Jenis Bahan Baku: ' . ($row['jenis'] ?? ''), 0, 0);
            $pdf->Cell(120, 8, 'Area: ' . ($row['admin'] ?? ''), 0, 0);
            $pdf->Cell(60, 8, 'Tanggal Pakai: ' . ($row['tgl_pakai'] ?? ''), 0, 1);

            // Header kolom
            $headers = ['NO', 'JAM', 'TGL PESAN', 'NO MODEL', 'ITEM TYPE', 'KODE WARNA', 'WARNA', 'LOT', 'JL MC', 'TOTAL', 'CONES', 'KETERANGAN', 'BAGIAN PERSIAPAN', 'QTY OUT', 'CNS OUT'];
            $pdf->SetFont('Arial', 'B', 8);
            foreach ($headers as $i => $h) {
                $pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 8);
        }

        $columns = [
            ['text' => $row['no'],            'w' => $widths[0],  'multi' => false],
            ['text' => $row['jam_pesan'],     'w' => $widths[1],  'multi' => false],
            ['text' => $row['tgl_pesan'],     'w' => $widths[2],  'multi' => false],
            ['text' => $row['no_model'],      'w' => $widths[3],  'multi' => false],
            ['text' => $row['item_type'],     'w' => $widths[4],  'multi' => true],  // ✅ MultiCell
            ['text' => $row['kode_warna'],    'w' => $widths[5],  'multi' => true],  // ✅ MultiCell
            ['text' => $row['color'],         'w' => $widths[6],  'multi' => true],  // ✅ MultiCell
            ['text' => $row['lot'],           'w' => $widths[7],  'multi' => false],
            ['text' => $row['ttl_jl_mc'],     'w' => $widths[8],  'multi' => false],
            ['text' => $row['ttl_kg'],        'w' => $widths[9],  'multi' => false],
            ['text' => $row['ttl_cns'],       'w' => $widths[10], 'multi' => false],
            ['text' => $row['keterangan_gbn'], 'w' => $widths[11], 'multi' => true],  // ✅ MultiCell
            ['text' => '',                    'w' => $widths[12], 'multi' => false],
            ['text' => '',                    'w' => $widths[13], 'multi' => false],
            ['text' => '',                    'w' => $widths[14], 'multi' => false],
        ];

        // STEP 1: Simulasikan tinggi masing-masing kolom
        $maxHeight = 0;
        foreach ($columns as $col) {
            if ($col['multi']) {
                $nb = ceil($pdf->GetStringWidth($col['text']) / $col['w']); // Perkiraan jumlah baris
                $height = max($nb * $lineHeight, 7);
            } else {
                $height = 6; // Fixed height untuk Cell biasa
            }
            if ($height > $maxHeight) $maxHeight = $height;
        }

        $xStart = $pdf->GetX();
        $yStart = $pdf->GetY();

        // STEP 2: Gambar border dulu
        foreach ($columns as $col) {
            $pdf->Cell($col['w'], $maxHeight, '', 1, 0, 'C');
        }
        $pdf->Ln();

        // STEP 3: Isi teks
        $x = $xStart;
        $y = $yStart;

        foreach ($columns as $col) {
            $pdf->SetXY($x, $y);
            if ($col['multi']) {
                $pdf->MultiCell($col['w'], $lineHeight, $col['text'], 0, 'C');
            } else {
                $pdf->Cell($col['w'], $maxHeight, $col['text'], 0, 0, 'C');
            }
            $x += $col['w'];
        }

        $pdf->SetXY($xStart, $yStart + $maxHeight);
    }
}
