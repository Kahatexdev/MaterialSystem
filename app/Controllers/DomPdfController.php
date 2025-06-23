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

    public function printBon($idBon)
    {
        $dompdf = new DompdfService();

        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon

        // Ambil data dari database sesuai $id jika perlu
        $html = view($this->role . '/out/bon', [
            'id' => $idBon,
            'dataBon' => $dataBon,
            'detailBon' => $detailBon
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream preview atau download
        return $dompdf->stream("bon_$idBon.pdf", ['Attachment' => false]);
    }

    public function printBarcode($idBon)
    {
        $dompdf = new DompdfService();
        $dataBon = $this->bonCelupModel->getDataById($idBon); // get data by id_bon
        $detailBon = $this->outCelupModel->getDetailBonByIdBon($idBon); // get data detail bon by id_bon
        $path = FCPATH . 'assets/img/logo-kahatex.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $img = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $generator = new BarcodeGeneratorPNG();
        $barcodeImages = [];
        foreach ($detailBon as $i => $row) {
            $id = $row['id_out_celup'];
            $bin = $generator->getBarcode($id, $generator::TYPE_CODE_128);
            $barcodeImages[$i] = 'data:image/png;base64,' . base64_encode($bin);
        }
        // Ambil data barcode sesuai $id
        $html = view($this->role . '/out/barcode', [
            'id' => $idBon,
            'dataBon' => $dataBon,
            'detailBon' => $detailBon,
            'img' => $img,
            'barcodeImages' => $barcodeImages,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('10cm', '5cm');
        $dompdf->render();

        return $dompdf->stream("barcode_$idBon.pdf", ['Attachment' => false]);
    }
}
