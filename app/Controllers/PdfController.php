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

        if ($jenis == 'BENANG') {
            $penanggung_jawab = 'CI MEGAH';
        } else {
            $penanggung_jawab = 'KO HARTANTO';
        }

        if ($tujuan == 'CELUP') {
            $penerima = 'RETNO';
        } else {
            $penerima = 'PARYANTI';
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
        $pdf->Image('assets/img/logo-kahatex.png', $x + 14, $y + 1, 10, 8); // Lokasi X, Y, lebar, tinggi

        // Header
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(38, 13, '', 1, 0, 'C'); // Tetap di baris yang sama
        // Set warna latar belakang menjadi biru telur asin (RGB: 170, 255, 255)
        $pdf->SetFillColor(170, 255, 255);
        $pdf->Cell(239, 4, 'FORMULIR', 1, 1, 'C', 1); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(38, 5, '', 0, 0, 'L'); // Tetap di baris yang sama
        $pdf->Cell(239, 5, 'DEPARTMEN CELUP CONES', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini

        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(38, 4, 'PT KAHATEX', 0, 0, 'C'); // Tetap di baris yang sama
        $pdf->Cell(239, 4, 'FORMULIR PO', 0, 1, 'C'); // Pindah ke baris berikutnya setelah ini


        // Tabel Header Atas
        $pdf->SetFont('Arial', '', 5);
        $pdf->Cell(38, 4, 'No. Dokumen', 1, 0, 'L');
        $pdf->Cell(160, 4, 'FOR-CC-087/REV_01/HAL_1/1', 1, 0, 'L');
        $pdf->Cell(33, 4, 'Tanggal Revisi', 1, 0, 'L');
        $pdf->Cell(46, 4, '04 Desember 2019', 1, 1, 'L');

        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(38, 5, 'PO', 0, 0, 'L');
        $pdf->Cell(239, 5, ': ' . $no_model, 0, 1, 'L');

        $pdf->Cell(38, 5, 'Pemesanan', 0, 0, 'L');
        $pdf->Cell(239, 5, ': KAOS KAKI', 0, 1, 'L');

        $pdf->Cell(38, 5, 'Tgl', 0, 0, 'L');
        // Check if the result array is not empty and display only the first delivery_awal
        if (!empty($result)) {
            $pdf->Cell(239, 5, ': ' . $result[0]['delivery_awal'], 0, 1, 'L');
        } else {
            $pdf->Cell(239, 5, ': No delivery date available', 0, 1, 'L');
        }

        // Tabel Header Baris Pertama
        $pdf->SetFont('Arial', '', 9);
        // Merge cells untuk kolom No, Bentuk Celup, Warna, Kode Warna, Buyer, Nomor Order, Delivery, Untuk Produksi, Contoh Warna, Keterangan Celup
        $pdf->Cell(6, 14, 'No', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(37, 7, 'Benang', 1, 0, 'C'); // Merge 2 kolom ke samping untuk baris pertama
        $pdf->MultiCell(17, 7, 'Bentuk Celup', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(60, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(20, 14, 'Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(20, 14, 'Kode Warna', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(10, 14, 'Buyer', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(25, 14, 'Nomor Order', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(16, 14, 'Delivery', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(15, 7, 'Qty Pesanan', 1, 0, 'C'); // Merge 2 baris
        $pdf->Cell(52, 7, 'Permintaan Kelos', 1, 0, 'C'); // Merge 4 kolom
        $pdf->MultiCell(18, 7, 'Untuk Produksi', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(236, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->MultiCell(18, 7, 'Contoh Warna', 1, 'C', false); // Merge 2 baris
        $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 14);
        $pdf->Cell(254, -7, '', 0, 0, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(23, 14, 'Keterangan Celup', 1, 1, 'C'); // Merge 2 baris

        // Sub-header untuk kolom "Benang" dan "Permintaan Kelos"
        $pdf->Cell(6, -7, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(12, -7, 'Jenis', 1, 0, 'C');
        $pdf->Cell(25, -7, 'Kode', 1, 0, 'C');
        $pdf->Cell(108, -7, '', 0, 0); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(15, -7, 'Kg', 1, 0, 'C'); // Merge 4 kolom untuk Permintaan Kelos
        $pdf->Cell(13, -7, 'Kg', 1, 0, 'C');
        $pdf->Cell(13, -7, 'Yard', 1, 0, 'C');
        $pdf->Cell(13, -7, 'Total Cones', 1, 0, 'C');
        $pdf->Cell(13, -7, 'Jenis Cones', 1, 0, 'C');
        $pdf->Cell(87, -7, '', 0, 2, 'C'); // Kosong untuk menyesuaikan posisi
        $pdf->Cell(87, 7, '', 0, 1, 'C'); // Kosong untuk menyesuaikan posisi

        $pdf->SetFont('Arial', '', 7);
        $no = 1;
        foreach ($result as $row) {
            $pdf->Cell(6, 6, $no++, 1, 0, 'C'); // Align center
            $pdf->Cell(12, 6, $row['jenis'], 1, 0, 'C'); // Align center
            $pdf->Cell(25, 6, $row['item_type'], 1, 0, 'C'); // Align center
            $pdf->Cell(17, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(20, 6, $row['color'], 1, 0, 'C'); // Align center
            $pdf->Cell(20, 6, $row['kode_warna'], 1, 0, 'C'); // Align center
            $pdf->Cell(10, 6, $row['buyer'], 1, 0, 'C'); // Align center
            $pdf->Cell(25, 6, $row['no_order'], 1, 0, 'C'); // Align center
            $pdf->Cell(16, 6, $row['delivery_awal'], 1, 0, 'C'); // Align center
            $pdf->Cell(15, 6, $row['kg_po'], 1, 0, 'C'); // Align center
            $pdf->Cell(13, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(13, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(18, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(18, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Cell(23, 6, '', 1, 0, 'C'); // Align center (empty)
            $pdf->Ln();
        }


        $pdf->Cell(150, 5, '', 0, 1, 'C');
        $pdf->Cell(170, 5, 'UNTUK DEPARTMEN ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Pemesanan', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Mengetahui', 0, 0, 'C');
        $pdf->Cell(55, 5, 'Tanda Terima ' . $tujuan, 0, 1, 'C');

        $pdf->Cell(55, 9, '', 0, 1, 'C');

        $pdf->Cell(55, 5, '', 0, 0, 'C');
        $pdf->Cell(55, 5, '(                               )', 0, 0, 'C');
        $pdf->Cell(55, 5, '(       ' . $penanggung_jawab . '       )', 0, 0, 'C');
        $pdf->Cell(55, 5, '(       ' . $penerima . '       )', 0, 1, 'C');

        // Output PDF
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setBody($pdf->Output('S'));
    }
}
