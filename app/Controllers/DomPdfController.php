<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\DompdfService;
use App\Models\BonCelupModel;
use App\Models\OutCelupModel;
use Picqer\Barcode\BarcodeGeneratorPNG;

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
        $path = FCPATH . 'assets/img/logo-kahatex.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);

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
            $row['operator_packing'] = $operatorShort;
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
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '10cm');
        $dompdf->render();

        return $dompdf->stream("Barcode_$idBon.pdf", ['Attachment' => false]);
    }
}
