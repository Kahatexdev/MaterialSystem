<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\DompdfService;
use App\Models\BonCelupModel;
use App\Models\OutCelupModel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Dompdf\Options;

class DomPdfController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $bonCelupModel;
    protected $outCelupModel;

    public function __construct()
    {
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

    public function index()
    {
        //
    }

    public function printBarcode($idBon)
    {
        $dompdf = new DompdfService();
        $dataBon = $this->bonCelupModel->getDataById($idBon);
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon);
        $path = FCPATH . 'assets/img/logo-kahatexbw.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $option = new Options();
        $generator = new BarcodeGeneratorPNG();
        $barcodeImages = [];
        foreach ($detailBon as $i => &$row) {
            $id = $row['id_out_celup'];
            $bin = $generator->getBarcode($id, $generator::TYPE_CODE_128);
            $barcodeImages[$i] = 'data:image/png;base64,' . base64_encode($bin);
            $operator = $row['operator_packing'];
            $operatorParts = explode(' ', trim($operator));
            $firstName = $operatorParts[0] ?? '';
            $secondInitial = isset($operatorParts[1]) ? strtoupper(substr($operatorParts[1], 0, 1)) : '';
            $operatorShort = $firstName . ($secondInitial ? ' ' . $secondInitial : '');
            $noModel = $row['no_model'];
            $maxNoModelLength = 15;
            if (strlen($noModel) > $maxNoModelLength) {
                $noModel = substr($noModel, 0, $maxNoModelLength);
            }
            $row['operator_packing'] = $operatorShort;
            $row['no_model'] = $noModel;
            $lot = $row['lot_kirim'];
            $lotClass = (strlen($lot) > 10) ? 'lot-small' : 'lot-normal';
            $row['lotClass'] = (strlen($lot) > 10) ? 'lot-small' : 'lot-normal';
        }
        unset($row); // break the reference

        // Ambil data barcode sesuai $id
        $html = view($this->role . '/out/barcode', [
            'id' => $idBon,
            'dataBon' => $dataBon,
            'detailBon' => $detailBon,
            'img' => $img,
            'barcodeImages' => $barcodeImages,
            'operatorShort' => $operatorShort,
            'lotClass' => $operatorShort,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '10cm');
        $option->set('dpi', 203);
        $dompdf->render();
        return $dompdf->stream("Barcode_$idBon.pdf", ['Attachment' => false]);
    }

    public function generateBarcodeRetur($tglRetur)
    {
        $dompdf = new DompdfService();
        $dataList = $this->outCelupModel->getDataReturByTgl($tglRetur);
        if (empty($dataList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tidak ada retur pada tanggal {$tglRetur}");
        }

        $path = FCPATH . 'assets/img/logo-kahatexbw.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $option = new Options();
        $generator = new BarcodeGeneratorPNG();
        $barcodeImages = [];
        foreach ($dataList as $i => &$row) {
            $id = $row['id_out_celup'];
            $bin = $generator->getBarcode($id, $generator::TYPE_CODE_128);
            $barcodeImages[$i] = 'data:image/png;base64,' . base64_encode($bin);
            $lot = $row['lot_kirim'];
            $row['lotClass'] = (strlen($lot) > 10) ? 'lot-small' : 'lot-normal';
        }

        // Ambil data barcode sesuai $id
        $html = view($this->role . '/retur/barcode', [
            'tgl' => $tglRetur,
            'dataList' => $dataList,
            'img' => $img,
            'barcodeImages' => $barcodeImages,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '10cm');
        $dompdf->render();
        $option->set('dpi', 203);

        return $dompdf->stream("Barcode_$tglRetur.pdf", ['Attachment' => false]);
    }

    public function generateOtherBarcode($tglDatang)
    {
        $dompdf = new DompdfService();
        $dataList = $this->outCelupModel->getDataOtherBarcode($tglDatang);
        if (empty($dataList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tidak ada pemasukan lain-lain pada tanggal {$tglDatang}");
        }

        $path = FCPATH . 'assets/img/logo-kahatex.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $option = new Options();
        $generator = new BarcodeGeneratorPNG();
        $barcodeImages = [];
        foreach ($dataList as $i => &$row) {
            $id = $row['id_out_celup'];
            $bin = $generator->getBarcode($id, $generator::TYPE_CODE_128);
            $barcodeImages[$i] = 'data:image/png;base64,' . base64_encode($bin);
            $lot = $row['lot_kirim'];
            $row['lotClass'] = (strlen($lot) > 10) ? 'lot-small' : 'lot-normal';
        }

        // Ambil data barcode sesuai $id
        $html = view($this->role . '/warehouse/other-barcode', [
            'tgl' => $tglDatang,
            'dataList' => $dataList,
            'img' => $img,
            'barcodeImages' => $barcodeImages,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '10cm');
        $dompdf->render();
        $option->set('dpi', 203);

        return $dompdf->stream("Barcode_$tglDatang.pdf", ['Attachment' => false]);
    }

    public function generatePindahOrderBarcode($noModel)
    {
        $dompdf = new DompdfService();
        $dataList = $this->outCelupModel->getDataPindahOrderBarcode($noModel);
        if (empty($dataList)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Tidak ada pemasukan lain-lain pada tanggal {$noModel}");
        }

        $path = FCPATH . 'assets/img/logo-kahatex.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $option = new Options();
        $generator = new BarcodeGeneratorPNG();
        $barcodeImages = [];
        foreach ($dataList as $i => &$row) {
            $id = $row['id_out_celup'];
            $bin = $generator->getBarcode($id, $generator::TYPE_CODE_128);
            $barcodeImages[$i] = 'data:image/png;base64,' . base64_encode($bin);
        }

        // Ambil data barcode sesuai $id
        $html = view($this->role . '/warehouse/pindah-order-barcode', [
            'noModel' => $noModel,
            'dataList' => $dataList,
            'img' => $img,
            'barcodeImages' => $barcodeImages,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '10cm');
        $dompdf->render();
        $option->set('dpi', 203);

        return $dompdf->stream("Barcode $noModel.pdf", ['Attachment' => false]);
    }
}
