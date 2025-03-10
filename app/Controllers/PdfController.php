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
        $pdf->Cell(234, 5, ': ' . $no_model, 0, 1, 'L');

        $pdf->Cell(43, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 5, ': KAOS KAKI', 0, 1, 'L');

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->Cell(234, 5, ': ' . $result[0]['tgl_po'], 0, 1, 'L');
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
            $pdf->Cell(55, 5, '(       '.$result[0]['penanggung_jawab'].'      )', 0, 0, 'C');
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
        $username = session()->get('username');
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
                $id_out_celup = str_pad($id['id_out_celup'], 12, '0', STR_PAD_LEFT);
                $barcode = $generator->getBarcode($id_out_celup, $generator::TYPE_EAN_13);
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
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 5); // Atur margin bawah saat halaman penuh
        $pdf->AddPage();

        // Tambahkan border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(3, 3, 204, 142);

        // Tambahkan double border margin
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.4);
        $pdf->Rect(4, 4, 202, 140);

        // Kembalikan ke properti default untuk border
        $pdf->SetDrawColor(0, 0, 0); // Tetap hitam jika digunakan pada elemen lain
        $pdf->SetLineWidth(0.2);    // Kembali ke garis default

        $pdf->SetMargins(4, 4, 4, 4); // Margin kiri, atas, kanan
        $pdf->SetXY(4, 4); // Mulai di margin kiri (X=5) dan sedikit di bawah border (Y=5)
        $pdf->SetAutoPageBreak(true,); // Aktifkan auto page break dengan margin bawah 10

        // Menambahkan gambar
        $pdf->Image('assets/img/logo-kahatex.png', 20, 5, 8, 7); // X=10 untuk margin, Y=10 untuk margin atas

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
        $pdf->Cell(61, 3, 'KAOS KAKI', 1, 0, 'L');
        $pdf->Cell(62, 3, 'NO SURAT JALAN : ' . $dataBon['no_surat_jalan'], 1, 0, 'L');
        $pdf->Cell(36, 3, 'TANGGAL : ' . $dataBon['tgl_datang'], 0, 1, 'L');

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
        $prevNoModel = null; // Variabel untuk menyimpan no_model sebelumnya
        $prevItemType = null; // Variabel untuk menyimpan item_type sebelumnya
        $prevKodeWarna = null; // Variabel untuk menyimpan kode_warna sebelumnya
        $totalRows = 32; // Total baris yang diinginkan
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
            // $jmlBaris = ($jmlDetail === 1) ? 3 : 2;

            $pdf->SetX(4); // Pastikan posisi X sejajar margin
            $pdf->Cell(18, 3, $bon['no_model'], 1, 0, 'C');
            $x2 = $pdf->GetX();
            $y2 = $pdf->GetY();

            // MultiCell untuk kolom item_type (tinggi fleksibel)
            $pdf->MultiCell(22, 3, $bon['item_type'], 1, 'C', false);
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
                $row++;
                if ($counter[$key] == 1) {
                    $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                    $pdf->Cell(9, 3, $detail['gw_kirim'], 1, 0, 'C');
                    $pdf->Cell(9, 3, $detail['kgs_kirim'], 1, 0, 'C');
                    $pdf->Cell(9, 3, $bon['totals']['cones_kirim'], 1, 0, 'C');
                    $pdf->Cell(9, 3, $bon['totals']['gw_kirim'], 1, 0, 'C');
                    $pdf->Cell(9, 3, $bon['totals']['kgs_kirim'], 1, 0, 'C');
                    $xKet = $pdf->GetX();
                    $yKet = $pdf->GetY();
                    $pdf->MultiCell(22, 3, $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'], 1, 'L', false);
                    $pdf->SetY($yKet + 3); // Kembalikan posisi untuk kolom berikutnya
                    $currentRow++;
                    // baris baru
                    $xBuyer = $pdf->GetX();
                    $yBuyer = $pdf->GetY();
                    $pdf->SetX($xBuyer); // Kembali ke posisi X tempat sebelumnya
                    $pdf->MultiCell(18, 3, $bon['buyer'] . ' KK', 1, 'C', false); // Menerapkan MultiCell untuk 'buyer'
                    $pdf->SetXY($xBuyer + 18, $yBuyer - 3); // Kembalikan posisi untuk kolom berikutnya
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
                } else {
                    if ($row == 1) {
                        $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $bon['totals']['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $bon['totals']['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $bon['totals']['kgs_kirim'], 1, 0, 'C');
                        // MultiCell untuk 'jmlKarung' dan 'ganti_retur'
                        $xKet = $pdf->GetX();
                        $yKet = $pdf->GetY();
                        $pdf->MultiCell(22, 3, $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'], 1, 'L', false);

                        // Kembali ke posisi X untuk melanjutkan dari bawah MultiCell
                        $pdf->SetXY($xKet, $yKet + 3);
                        $currentRow++; // Update baris yang sudah dicetak
                    } elseif ($row == 2) {
                        // Baris kedua
                        $pdf->SetX(4); // Set posisi X kembali ke margin
                        $xBuyer = $pdf->GetX(); // Simpan posisi X saat ini
                        $yBuyer = $pdf->GetY(); // Simpan posisi Y saat ini

                        // MultiCell untuk 'buyer'
                        $pdf->MultiCell(18, 3, $bon['buyer'] . ' KK', 1, 'C', false);

                        // Perbarui posisi kursor setelah MultiCell selesai
                        $maxHeight = $pdf->GetY() - $yBuyer; // Hitung tinggi yang digunakan oleh MultiCell
                        $pdf->SetXY($xBuyer + 18, $yBuyer); // Geser posisi X sejajar setelah MultiCell
                        // dd($xBuyer, $yBuyer);

                        $pdf->Cell(22, 3, '', 1, 0, 'C');
                        $pdf->Cell(11, 3, '', 1, 0, 'C');
                        $pdf->Cell(29, 3, '', 1, 0, 'C');
                        $pdf->Cell(18, 3, '', 1, 0, 'C');
                        $pdf->Cell(13, 3, '', 1, 0, 'C');
                        $pdf->Cell(7, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(8, 3, $detail['cones_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 1, 'L');
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
                        $pdf->Cell(9, 3, $detail['gw_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, $detail['kgs_kirim'], 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(9, 3, '', 1, 0, 'C');
                        $pdf->Cell(22, 3, '', 1, 1, 'L');
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

                // Reset posisi baris saat ini
                $row = 0;
            }

            // Perbarui nilai sebelumnya
            $prevNoModel = $bon['no_model'];
            $prevItemType = $bon['item_type'];
            $prevKodeWarna = $bon['kode_warna'];
            // var_dump($prevNoModel, $prevItemType, $prevKodeWarna);
        }
        // dd($currentRow, $totalRows);
        // var_dump($prevNoModel);
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
            $pdf->Cell(17, 3, $key === 'KETERANGAN :' ? 'PENGIRIM' : ($key === 4 ? $username : ''), 0, 0, 'C');
            $pdf->Cell(23, 3, '', 0, 0, 'L'); // Kosong
            $pdf->Cell(17, 3, $key === 'KETERANGAN :' ? 'PENERIMA' : '', 0, 0, 'C'); // Hanya baris pertama ada "PENERIMA"
            $pdf->Cell(18, 3, '', 0, 1, 'L'); // Kolom terakhir kosong
        }

        $pdf->Ln();  // Fungsi PageNo() untuk mendapatkan nomor halaman

        $pageNo = $pdf->PageNo();  // Fungsi PageNo() untuk mendapatkan nomor halaman
        // jika halaman pertama hitung tinggi
        if ($pageNo == 1) {
            $startX_ = 2.5;
            $startY_ = 157;
        } else {
            $startX_ = 2.5;
            $startY_ = 14;
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'BARCODE', 0, 1, 'C');

        $barcodeCount = 0; // Counter untuk jumlah barcode di halaman saat ini
        $barcodeWidth = 67; // Lebar kotak barcode
        $barcodeHeight = 67; // Tinggi kotak barcode
        $jarakKolom = 2; // Jarak horizontal antar kolom
        $jarakBaris = 2; // Jarak vertikal antar baris

        foreach ($dataBon['groupedDetails'] as $groups) {
            foreach ($groups['barcodes'] as $barcode) {
                // Menghitung posisi X dan Y untuk 6 barcode per halaman (3 kolom × 2 baris)
                if ($pageNo == 1) {
                    $mod = 6;
                    $baris = 2;
                } else {
                    $mod = 12;
                    $baris = 4;
                }
                // Jika sudah mencapai batas (6 barcode), tambah halaman baru
                if ($barcodeCount > 0 && $barcodeCount % $mod === 0) {
                    $pdf->AddPage(); // Tambahkan halaman baru
                    $startX_ = 2.5; // Reset posisi X untuk halaman baru
                    $startY_ = 14; // Reset posisi Y untuk halaman baru
                    $pdf->SetFont('Arial', 'B', 12);
                    $pdf->Cell(0, 8, 'BARCODE', 0, 1, 'C');
                    $pageNo++;
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
                $imageData = base64_decode($barcode['barcode']);
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
                $pdf->SetXY($startX + 2, $startY + 10);
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
                $pdf->Cell(39, 3, $barcode['gw'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 3); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 3, 'NW', 0, 0, 'L');
                $pdf->Cell(5, 3, ':', 0, 0, 'C');
                $pdf->Cell(39, 3, $barcode['kgs'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 6); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 3, 'Cones', 0, 0, 'L');
                $pdf->Cell(5, 3, ':', 0, 0, 'C');
                $pdf->Cell(39, 3, $barcode['cones'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 9); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 3, 'Lot', 0, 0, 'L');
                $pdf->Cell(5, 3, ':', 0, 0, 'C');
                $pdf->Cell(39, 3, $barcode['lot'], 0, 0, 'L');
                $pdf->SetXY($startX + 2, $currentY + 12); // Menambah jarak berdasarkan jumlah baris yang tercetak

                $pdf->Cell(20, 3, 'No Karung', 0, 0, 'L');
                $pdf->Cell(5, 3, ':', 0, 0, 'C');
                $pdf->Cell(39, 3, $barcode['no_karung'], 0, 1, 'L');
                $pdf->SetXY($startX + 2, $currentY + 15); // Menambah jarak berdasarkan jumlah baris yang tercetak

                // Counter untuk jumlah barcode
                $barcodeCount++;
            }
        }

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('Bon Pengiriman.pdf', 'I'));
    }

    private function generateHeaderPOCovering($pdf, $tgl_po)
    {
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
        $pdf->Cell(234, 5, ': ', 0, 1, 'L');
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
        $getMasterOrder = $this->openPoModel->getDeliveryAwalNoOrderBuyer();

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
        $pdf->Cell(234, 5, ': ' . $poCovering[0]['no_model'], 0, 1, 'L');

        $pdf->Cell(43, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(234, 5, ': COVERING', 0, 1, 'L');

        $pdf->Cell(43, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($poCovering)) {
            $pdf->Cell(234, 5, ': ' . date('Y-m-d', strtotime($poCovering[0]['created_at'])), 0, 1, 'L');
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
        $maxHeight = 8; // Default tinggi baris
        $yLimit = 180;

        foreach ($poCovering as $index => $row) {
            $getMasterOrder = $this->openPoModel->getDeliveryAwalNoOrderBuyer();
            $no_model = isset($getMasterOrder[$index]['no_model']) ? $getMasterOrder[$index]['no_model'] : '-';
            $buyer = isset($getMasterOrder[$index]['buyer']) ? $getMasterOrder[$index]['buyer'] : '-';
            $delivery_awal = isset($getMasterOrder[$index]['delivery_awal']) ? $getMasterOrder[$index]['delivery_awal'] : '-';

            // Cek apakah masih cukup ruang dalam halaman, jika tidak, buat halaman baru
            if ($pdf->GetY() + $maxHeight > $yLimit) {
                $pdf->AddPage(); // Tambah halaman baru
                // Ulangi Header Formulir
                $this->generateHeaderPOCovering($pdf, $tgl_po);
            }

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
           
            // Lanjutkan dengan sel lainnya yang juga menyesuaikan tinggi
            $pdf->Cell(17, $maxHeight, '', 1, 0, 'C'); // Bentuk Celup
            $pdf->Cell(20, $maxHeight, $row['color'], 1, 0, 'C');
            $pdf->Cell(20, $maxHeight, $row['kode_warna'], 1, 0, 'C');
            $pdf->Cell(10, $maxHeight, $buyer, 1, 0, 'C');
            $pdf->Cell(25, $maxHeight, $no_model, 1, 0, 'C');
            $pdf->Cell(16, $maxHeight, $delivery_awal, 1, 0, 'C');
            $pdf->Cell(15, $maxHeight, $row['kg_po'], 1, 0, 'C');
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $pdf->Cell(13, $maxHeight, '', 1, 0, 'C');
            $pdf->Cell(18, $maxHeight, $row['jenis'], 1, 0, 'C');
            $pdf->Cell(18, $maxHeight, '', 1, 0, 'C');
            $pdf->Cell(23, $maxHeight, '', 1, 1, 'C'); 
        }

        //KETERANGAN
        $pdf->Cell(277, 5, '', 0, 1, 'C');

        $pdf->Cell(277, 5, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . 'Celup Cones', 0, 1, 'C');

        $pdf->Cell(55, 9, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        if (!empty($poCovering)) {
            $pdf->Cell(55, 5, $poCovering[0]['penanggung_jawab'], 0, 0, 'C');
        } else {
            $pdf->Cell(234, 5, ': No penanggung_jawab available', 0, 0, 'C');
        }
        $pdf->Cell(55, 5, '(       ' . $poCovering[0]['penerima'] . '       )', 0, 1, 'C');

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }
}
