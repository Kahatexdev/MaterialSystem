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
use FPDF;

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
    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();


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
}
