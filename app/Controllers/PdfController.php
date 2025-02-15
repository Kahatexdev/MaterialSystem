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
use FPDF;
use Picqer\Barcode\BarcodeGeneratorPNG;

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
        $tujuan = $this->request->getGet('tujuan');
        $jenis = $this->request->getGet('jenis');
        $jenis2 = $this->request->getGet('jenis2');

        if ($tujuan == 'CELUP') {
            $penerima = 'Retno';
        } else {
            $penerima = 'Paryanti';
        }

        $result = $this->openPoModel->getData($no_model, $jenis, $jenis2);

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();

        // Tambahkan border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
        $pdf->Rect(10, 10, 277, 190);

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
        $pdf->Cell(234, 5, ': ' . $no_model, 0, 1, 'L');

        $pdf->Cell(43, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 5, ': KAOS KAKI', 0, 1, 'L');

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->Cell(234, 5, ': ' . $result[0]['delivery_awal'], 0, 1, 'L');
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
        $no = 1;
        foreach ($result as $row) {
            $pdf->Cell(6, 8, $no++, 1, 0, 'C'); // Align center
            $pdf->Cell(12, 8, $row['jenis'], 1, 0, 'C'); // Align center

            //Wrap text jika melebihi space
            $itemTypeWidth = 25; // Lebar kolom item_type
            $lineHeight = 4; // Tinggi per baris untuk MultiCell
            $textWidth = $pdf->GetStringWidth($row['item_type']); // Panjang teks

            if ($textWidth > $itemTypeWidth) {
                $pdf->MultiCell($itemTypeWidth, $lineHeight, $row['item_type'], 1, 'C', false);
                $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 8);
                $pdf->Cell(43, -8, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
            } else {
                $adjustedHeight = 8; // Tinggi standar jika cukup 1 baris
                $pdf->Cell($itemTypeWidth, $adjustedHeight, $row['item_type'], 1, 0, 'C');
            }

            $pdf->Cell(17, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(20, 8, $row['color'], 1, 0, 'C'); // Align center
            $pdf->Cell(20, 8, $row['kode_warna'], 1, 0, 'C'); // Align center
            $pdf->Cell(10, 8, $row['buyer'], 1, 0, 'C'); // Align center
            $pdf->Cell(25, 8, $row['no_order'], 1, 0, 'C'); // Align center
            $pdf->Cell(16, 8, $row['delivery_awal'], 1, 0, 'C'); // Align center
            $pdf->Cell(15, 8, $row['kg_po'], 1, 0, 'C'); // Align center
            $pdf->Cell(13, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(18, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(18, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(23, 8, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Ln();
        }

        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(85, 5, 'KET', 0, 0, 'R');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->Cell(117, 5, ': ' . $result[0]['keterangan'], 0, 1, 'L');
        } else {
            $pdf->Cell(117, 5, ': ', 0, 1, 'L');
        }

        $pdf->Cell(277, 5, '', 0, 1, 'C');
        $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 9, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        if (!empty($result)) {
            $pdf->Cell(55, 5, $result[0]['penanggung_jawab'], 0, 0, 'C');
        } else {
            $pdf->Cell(234, 5, ': No penanggung_jawab available', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . $penerima . '       )', 0, 1, 'C');

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }

    public function printBon($idBon)
    {
        // data ALL BON
        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon
        // dd($detailBon);

        // Mengelompokkan data detailBon berdasarkan no_model, item_type, dan kode_warna
        $groupedDetails = [];
        foreach ($detailBon as $detail) {
            $key = $detail['no_model'] . '|' . $detail['item_type'] . '|' . $detail['kode_warna'];
            $jmlKarung =
                $gantiRetur = ($detail['ganti_retur'] == 1) ? ' / GANTI RETUR' : '';
            if (!isset($groupedDetails[$key])) {
                $groupedDetails[$key] = [
                    'no_model' => $detail['no_model'],
                    'item_type' => $detail['item_type'],
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
                $barcode = $generator->getBarcode($id['id_out_celup'], $generator::TYPE_EAN_13);
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

        // Inisialisasi FPDF
        $pdf = new FPDF('L', 'mm', [176, 250]); // Mengatur ukuran kertas menjadi B5
        $pdf->AddPage();

        // Tambahkan border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(5, 5, 240, 165);

        // Tambahkan double border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(6, 6, 238, 163);

        // Kembalikan ke properti default untuk border
        $pdf->SetDrawColor(0, 0, 0); // Tetap hitam jika digunakan pada elemen lain
        $pdf->SetLineWidth(0.2);    // Kembali ke garis default

        $pdf->SetMargins(6, 6, 6, 6); // Margin kiri, atas, kanan
        $pdf->SetXY(6, 6); // Mulai di margin kiri (X=5) dan sedikit di bawah border (Y=5)
        $pdf->SetAutoPageBreak(true,); // Aktifkan auto page break dengan margin bawah 10

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', 21, 7, 12, 10); // X=10 untuk margin, Y=10 untuk margin atas

        // Header
        $pdf->SetX(6); // Pastikan posisi X sejajar margin
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(44, 15, '', 1, 0, 'C');

        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(194, 5, 'FORMULIR', 0, 1, 'C', 1);

        $pdf->SetFillColor(255, 255, 255); // Ubah latar belakang menjadi putih

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 5, '', 0, 0, 'L');
        $pdf->Cell(194, 5, 'DEPARTMEN KELOS WARNA', 0, 1, 'C');

        $pdf->SetX(6); // Pastikan posisi X sejajar margin
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(44, 6, 'PT. KAHATEX', 0, 0, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(194, 5, 'BON PENGIRIMAN', 1, 1, 'C');


        // Tabel Header Atas
        $pdf->SetX(6); // Pastikan posisi X sejajar margin
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(44, 5, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(123, 5, 'FOR-KWA-006/REV_03/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(28, 5, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(43, 5, '07 Januari 2021', 1, 1, 'L');

        $pdf->SetX(6); // Pastikan posisi X sejajar margin
        $pdf->Cell(44, 4, 'NAMA LANGGANAN', 0, 0, 'L');
        $pdf->Cell(69, 4, 'KAOS KAKI', 0, 0, 'L');
        $pdf->Cell(62, 4, 'NO SURAT JALAN : ' . $dataBon['no_surat_jalan'], 0, 0, 'L');
        $pdf->Cell(36, 4, 'TANGGAL : ' . $dataBon['tgl_datang'], 0, 1, 'L');

        $pdf->SetX(6); // Pastikan posisi X sejajar margin
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(20, 12, 'NO PO', 1, 0, 'C');
        $pdf->Cell(24, 12, 'JENIS BENANG', 1, 0, 'C');
        // Menentukan posisi awal untuk KODE BENANG
        $xKB = $pdf->GetX();
        $yKB = $pdf->GetY();
        $pdf->MultiCell(13, 6, 'KODE BENANG', 1, 'C', false); // wrap text
        // Mengembalikan posisi setelah MultiCell
        $pdf->SetXY($xKB + 13, $yKB);

        $pdf->Cell(38, 12, 'KODE WARNA', 1, 0, 'C');
        $pdf->Cell(20, 12, 'WARNA', 1, 0, 'C');
        $pdf->Cell(15, 12, 'LOT CELUP', 1, 0, 'C');
        $pdf->Cell(8, 12, 'L/M/D', 1, 0, 'C');
        // Membagi kolom "HARGA" menjadi dua baris
        $xHarga = $pdf->GetX();
        $yHarga = $pdf->GetY();
        $pdf->Cell(10, 6, 'HARGA', 1, 2, 'C'); // Baris pertama (HARGA)
        $pdf->SetXY($xHarga, $yHarga + 6);
        $pdf->Cell(10, 6, 'PER KG', 1, 0, 'C'); // Baris kedua (PER KG)

        // Kolom "CONES" dengan tinggi penuh sejajar kolom paling awal
        $pdf->SetXY($xHarga + 10, $yHarga); // Mengatur posisi kolom "CONES" kembali ke baris awal
        $pdf->Cell(10, 12, 'CONES', 1, 0, 'C');

        $xQty = $pdf->GetX();
        $yQty = $pdf->GetY();
        $pdf->Cell(20, 4, 'QTY', 1, 2, 'C');
        $pdf->SetXY($xQty, $yQty + 4);
        $xGw = $pdf->GetX();
        $yGw = $pdf->GetY();
        $pdf->MultiCell(10, 4, 'GW (KG)', 1, 'C', false); // wrap text
        // Mengembalikan posisi setelah MultiCell
        $pdf->SetXY($xGw + 10, $yGw);
        $pdf->MultiCell(10, 4, 'NW (KG)', 1, 'C', false); // wrap text
        // Mengembalikan posisi setelah MultiCell
        $pdf->SetXY($xGw + 10, $yGw);
        $pdf->SetXY($xQty + 20, $yQty);

        $xTotal = $pdf->GetX();
        $yTotal = $pdf->GetY();
        $pdf->Cell(30, 4, 'TOTAL', 1, 1, 'C');
        $pdf->SetXY($xTotal, $yTotal + 4);
        $pdf->Cell(10, 8, 'CONES', 1, 0, 'C');
        $xGw = $pdf->GetX();
        $yGw = $pdf->GetY();
        $pdf->MultiCell(10, 4, 'GW (KG)', 1, 'C', false); // wrap text
        // Mengembalikan posisi setelah MultiCell
        $pdf->SetXY($xGw + 10, $yGw);
        $pdf->MultiCell(10, 4, 'NW (KG)', 1, 'C', false); // wrap text
        // Mengembalikan posisi setelah MultiCell
        $pdf->SetXY($xGw + 10, $yGw);
        $pdf->SetXY($xQty + 20, $yQty);

        $pdf->SetXY($xTotal + 30, $yTotal);
        $pdf->Cell(30, 12, 'KETERANGAN', 1, 1, 'C');


        $counter = [];
        $prevNoModel = null; // Variabel untuk menyimpan no_model sebelumnya
        $prevItemType = null; // Variabel untuk menyimpan item_type sebelumnya
        $prevKodeWarna = null; // Variabel untuk menyimpan kode_warna sebelumnya
        $totalRows = 28; // Total baris yang diinginkan
        $row = 0;
        $currentRow = 0; // Variabel untuk menghitung jumlah baris yang sudah tercetak

        foreach ($dataBon['groupedDetails'] as $bon) {
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

            // Hitung jumlah detail untuk grup saat ini
            $jmlDetail = count($bon['detailPengiriman']);
            $jmlBaris = ($jmlDetail === 1) ? 3 : 2;

            $pdf->SetX(6); // Pastikan posisi X sejajar margin
            $pdf->Cell(20, 4, $bon['no_model'], 1, 0, 'C');
            $x2 = $pdf->GetX();
            $y2 = $pdf->GetY();

            // MultiCell untuk kolom item_type (tinggi fleksibel)
            $pdf->MultiCell(24, 4, $bon['item_type'], 1, 'C', false);
            // Kembalikan posisi untuk kolom berikutnya
            $pdf->SetXY($x2 + 24, $y2);

            // MultiCell untuk kolom ukuran (tinggi fleksibel)
            $x3 = $pdf->GetX();
            $pdf->MultiCell(13, 4, $bon['ukuran'], 1, 'C', false);
            // Kembalikan posisi untuk kolom berikutnya
            $pdf->SetXY($x3 + 13, $y2);

            // MultiCell untuk kolom kode warna (tinggi fleksibel)
            $x4 = $pdf->GetX();
            $pdf->MultiCell(38, 4, $bon['kode_warna'], 1, 'C', false);
            // Kembalikan posisi untuk kolom berikutnya
            $pdf->SetXY($x4 + 38, $y2);

            // MultiCell untuk kolom warna (tinggi fleksibel)
            $x5 = $pdf->GetX();
            $pdf->MultiCell(20, 4, $bon['warna'], 1, 'C', false);
            // Kembalikan posisi untuk kolom berikutnya
            $pdf->SetXY($x5 + 20, $y2);

            // MultiCell untuk kolom lot_kirim (tinggi fleksibel)
            $x6 = $pdf->GetX();
            $pdf->MultiCell(15, 4, $bon['lot_kirim'], 1, 'C', false);

            // // Hitung tinggi maksimum dari semua kolom
            // $maxHeight = max($multiCellHeight1, $multiCellHeight2, $multiCellHeight3, $multiCellHeight4, $multiCellHeight5, 8);

            // Kembalikan posisi untuk kolom berikutnya
            $pdf->SetXY($x6 + 15, $y2);
            $pdf->Cell(8, 4, $bon['l_m_d'], 1, 0, 'C');
            $pdf->Cell(10, 4, $bon['harga'], 1, 0, 'C');
            foreach ($bon['detailPengiriman'] as $detail) {
                // var_dump($row);
                $row++;
                if ($counter[$key] == 1) {
                    $pdf->Cell(10, 4, $detail['cones_kirim'], 1, 0, 'C');
                    $pdf->Cell(10, 4, $detail['gw_kirim'], 1, 0, 'C');
                    $pdf->Cell(10, 4, $detail['kgs_kirim'], 1, 0, 'C');
                    $pdf->Cell(10, 4, $bon['totals']['cones_kirim'], 1, 0, 'C');
                    $pdf->Cell(10, 4, $bon['totals']['gw_kirim'], 1, 0, 'C');
                    $pdf->Cell(10, 4, $bon['totals']['kgs_kirim'], 1, 0, 'C');

                    $pdf->MultiCell(30, 4, $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'], 1, 'L', false);

                    $currentRow++;
                    $xBuyer = $pdf->GetX();
                    $yBuyer = $pdf->GetY();
                    $pdf->SetX($xBuyer); // Kembali ke posisi X tempat sebelumnya
                    $pdf->MultiCell(20, 4, $bon['buyer'] . ' KK', 1, 'C', false); // Menerapkan MultiCell untuk 'buyer'
                    $pdf->SetXY($xBuyer + 20, $yBuyer - 4); // Kembalikan posisi untuk kolom berikutnya
                    $pdf->Cell(24, 4, '', 1, 0, 'C');
                    $pdf->Cell(13, 4, '', 1, 0, 'C');
                    $pdf->Cell(38, 4, '', 1, 0, 'C');
                    $pdf->Cell(20, 4, '', 1, 0, 'C');
                    $pdf->Cell(15, 4, '', 1, 0, 'C');
                    $pdf->Cell(8, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(30, 4, '', 1, 1, 'L');
                    $currentRow++; // Update baris yang sudah dicetak
                } else {
                    if ($row == 1) {
                        $pdf->Cell(10, 4, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $bon['totals']['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $bon['totals']['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $bon['totals']['kgs_kirim'], 1, 0, 'C');
                        // MultiCell untuk 'jmlKarung' dan 'ganti_retur'
                        $xKet = $pdf->GetX();
                        $yKet = $pdf->GetY();
                        $pdf->MultiCell(30, 4, $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'], 1, 'L', false);

                        // Kembali ke posisi X untuk melanjutkan dari bawah MultiCell
                        $pdf->SetXY($xKet, $yKet + 4);
                        $currentRow++; // Update baris yang sudah dicetak
                    } elseif ($row == 2) {
                        // Baris kedua
                        $pdf->SetX(6); // Set posisi X kembali ke margin
                        $xBuyer = $pdf->GetX(); // Simpan posisi X saat ini
                        $yBuyer = $pdf->GetY(); // Simpan posisi Y saat ini

                        // MultiCell untuk 'buyer'
                        $pdf->MultiCell(20, 4, $bon['buyer'] . ' KK', 1, 'C', false);

                        // Perbarui posisi kursor setelah MultiCell selesai
                        $maxHeight = $pdf->GetY() - $yBuyer; // Hitung tinggi yang digunakan oleh MultiCell
                        $pdf->SetXY($xBuyer + 20, $yBuyer); // Geser posisi X sejajar setelah MultiCell
                        // dd($xBuyer, $yBuyer);

                        $pdf->Cell(24, 4, '', 1, 0, 'C');
                        $pdf->Cell(13, 4, '', 1, 0, 'C');
                        $pdf->Cell(38, 4, '', 1, 0, 'C');
                        $pdf->Cell(20, 4, '', 1, 0, 'C');
                        $pdf->Cell(15, 4, '', 1, 0, 'C');
                        $pdf->Cell(8, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(30, 4, '', 1, 1, 'L');
                        $currentRow++; // Update baris yang sudah dicetak
                    } else {
                        $pdf->SetX(6); // Pastikan posisi X sejajar margin
                        $pdf->Cell(20, 4, '', 1, 0, 'C');
                        $pdf->Cell(24, 4, '', 1, 0, 'C');
                        $pdf->Cell(13, 4, '', 1, 0, 'C');
                        $pdf->Cell(38, 4, '', 1, 0, 'C');
                        $pdf->Cell(20, 4, '', 1, 0, 'C');
                        $pdf->Cell(15, 4, '', 1, 0, 'C');
                        $pdf->Cell(8, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(10, 4, '', 1, 0, 'C');
                        $pdf->Cell(30, 4, '', 1, 1, 'L');
                        $currentRow++; // Update baris yang sudah dicetak
                    }
                }
            }

            if (
                $prevNoModel === null || // artinya data pertama
                ($bon['no_model'] !== $prevNoModel) ||
                ($bon['item_type'] !== $prevItemType) ||
                ($bon['kode_warna'] !== $prevKodeWarna)
            ) {
                // Tentukan jumlah baris kosong yang ingin ditambahkan
                for ($i = 0; $i < $jmlBaris; $i++) {

                    $pdf->SetX(6);
                    // Cetak baris kosong dengan format sel yang sesuai
                    $pdf->Cell(20, 4, '', 1, 0, 'C');
                    $pdf->Cell(24, 4, '', 1, 0, 'C');
                    $pdf->Cell(13, 4, '', 1, 0, 'C');
                    $pdf->Cell(38, 4, '', 1, 0, 'C');
                    $pdf->Cell(20, 4, '', 1, 0, 'C');
                    $pdf->Cell(15, 4, '', 1, 0, 'C');
                    $pdf->Cell(8, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(10, 4, '', 1, 0, 'C');
                    $pdf->Cell(30, 4, '', 1, 1, 'L'); // Pindah ke baris baru
                    $currentRow++; // Update baris yang sudah dicetak
                }

                // Reset posisi baris saat ini
                $row = 0;
            }

            // Perbarui nilai sebelumnya
            $prevNoModel = $bon['no_model'];
            $prevItemType = $bon['item_type'];
            $prevKodeWarna = $bon['kode_warna'];
            // var_dump($prevNoModel, $prevItemType, $prevKodeWarna);
        }
        // var_dump($prevNoModel);
        // Tambahkan baris kosong jika jumlah baris yang dicetak kurang dari 28
        while ($currentRow <= $totalRows) {
            $pdf->SetX(6);
            $pdf->Cell(20, 4, '', 1, 0, 'C');
            $pdf->Cell(24, 4, '', 1, 0, 'C');
            $pdf->Cell(13, 4, '', 1, 0, 'C');
            $pdf->Cell(38, 4, '', 1, 0, 'C');
            $pdf->Cell(20, 4, '', 1, 0, 'C');
            $pdf->Cell(15, 4, '', 1, 0, 'C');
            $pdf->Cell(8, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(10, 4, '', 1, 0, 'C');
            $pdf->Cell(30, 4, '', 1, 1, 'L');
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
            $pdf->SetX(6); // Pastikan posisi X sejajar margin
            $pdf->Cell(20, 3, ($key == "KETERANGAN :") ? $key : '', 0, 0, 'L'); // Kolom pertama (key)
            $pdf->Cell(37, 3, $value, 0, 0, 'L'); // Kolom kedua (value)
            $pdf->Cell(90, 3, '', 0, 0, 'L'); // Kosong
            $pdf->Cell(30, 3, $key === 'KETERANGAN :' ? 'PENGIRIM' : '', 0, 0, 'C'); // Hanya baris pertama ada "PENGIRIM"
            $pdf->Cell(20, 3, '', 0, 0, 'L'); // Kosong
            $pdf->Cell(30, 3, $key === 'KETERANGAN :' ? 'PENERIMA' : '', 0, 0, 'C'); // Hanya baris pertama ada "PENERIMA"
            $pdf->Cell(11, 3, '', 0, 1, 'L'); // Kolom terakhir kosong
        }


        // Menambahkan halaman baru
        $pdf->AddPage();
        $pdf->SetY(1);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'BARCODE', 0, 1, 'C');

        $barcodeCount = 0; // Counter untuk jumlah barcode di halaman saat ini
        foreach ($dataBon['groupedDetails'] as $groups) {

            foreach ($groups['barcodes'] as $barcode) {
                // Konfigurasi jarak antar kolom dan baris
                $jarakKolom = 2; // Jarak horizontal antar kolom
                $jarakBaris = 2; // Jarak vertikal antar baris

                // Perhitungan posisi X dan Y untuk 6 barcode per halaman (3 kolom × 2 baris)
                $colIndex = $barcodeCount % 3; // Tiga kolom per baris
                $rowIndex = floor($barcodeCount / 3) % 2; // Dua baris per halaman

                $startX = 10 + ($colIndex * (75 + $jarakKolom)); // Posisi horizontal
                $startY = 8 + ($rowIndex * (81 + $jarakBaris));  // Posisi vertikal

                // Jika jumlah barcode mencapai batas (6 per halaman), tambahkan halaman baru
                if ($barcodeCount > 0 && $barcodeCount % 6 === 0) {
                    $pdf->AddPage(); // Tambahkan halaman baru
                    $startX = 10; // Reset posisi X
                    $startY = 8; // Reset posisi Y
                }

                // Menggambar kotak di sekitar detail
                $pdf->Rect($startX, $startY, 75, 81); // Kotak barcode

                // Menyimpan gambar barcode
                $imageData = base64_decode($barcode['barcode']);
                $tempImagePath = WRITEPATH . 'uploads/barcode_temp.png'; // Path file sementara
                file_put_contents($tempImagePath, $imageData);

                // Menentukan posisi X agar gambar berada di tengah kotak secara horizontal
                $imageWidth = 40; // Lebar gambar
                $centerX = $startX + (75 - $imageWidth) / 2; // Menyesuaikan posisi
                $pdf->Image($tempImagePath, $centerX, $startY + 3, $imageWidth); // Tambahkan gambar

                // Menghapus file gambar sementara
                unlink($tempImagePath);

                // Menghitung berapa banyak baris yang sudah tercetak di dalam MultiCell
                $lineHeight = 5; // Tinggi per baris dalam MultiCell (diatur di atas sebagai 5)

                // Menambahkan detail teks di dalam kotak
                $pdf->SetFont('Arial', 'B', 8);
                // Teks detail
                $pdf->SetXY($startX + 2, $startY + 10);
                $pdf->Cell(20, 5, 'No Model', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['no_model'], 0, 1, 'L');

                $pdf->SetXY($startX + 2, $pdf->getY());
                $pdf->Cell(20, 5, 'Item Type', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->MultiCell(46, 5, $barcode['item_type'], 0, 1, 'L');
                // Menyimpan posisi Y setelah MultiCell
                // dd($currentY, $nextY, $totalHeight, $lineCount);
                $pdf->SetXY($startX + 2, $pdf->GetY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->SetXY($startX + 2, $pdf->GetY()); // Menambah jarak berdasarkan jumlah baris yang tercetak
                $pdf->Cell(20, 5, 'Kode Warna', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->MultiCell(46, 5, $barcode['kode_warna'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $pdf->getY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 5, 'Warna', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->MultiCell(46, 5, $barcode['warna'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $pdf->getY()); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $currentY = $pdf->GetY();
                $pdf->Cell(20, 5, 'GW', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['gw'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 5); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 5, 'NW', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['kgs'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 10); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 5, 'Cones', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['cones'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 15); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 5, 'Lot', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['lot'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 20); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 5, 'No Karung', 0, 0, 'L');
                $pdf->Cell(5, 5, ':', 0, 0, 'C');
                $pdf->Cell(70, 5, $barcode['no_karung'], 0, 1, 'L');
                $pdf->SetXY($startX + 2, $currentY + 25); // Menambah jarak berdasarkan jumlah baris yang tercetak

                // Counter untuk jumlah barcode
                $barcodeCount++;
            }
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('Bon Pengiriman.pdf', 'I'));
    }
}
