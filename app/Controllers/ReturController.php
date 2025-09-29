<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\MasterOrderModel;
use App\Models\MasterMaterialModel;
use App\Models\MaterialModel;
use App\Models\ReturModel;
use App\Models\PemasukanModel;
use App\Models\OutCelupModel;
use App\Models\KategoriReturModel;
use App\Models\ScheduleCelupModel;



class ReturController extends BaseController
{
    use ResponseTrait;

    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterOrderModel;
    protected $masterMaterial;
    protected $materialModel;
    protected $returModel;
    protected $pemasukanModel;
    protected $outCelupModel;
    protected $kategoriReturModel;
    protected $scheduleCelupModel;


    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->masterMaterial = new MasterMaterialModel();
        $this->masterOrderModel = new MasterOrderModel();
        $this->returModel = new ReturModel();
        $this->pemasukanModel = new PemasukanModel();
        $this->outCelupModel = new OutCelupModel();
        $this->kategoriReturModel = new KategoriReturModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();

        $this->role = session()->get('role');
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
        // Ambil data retur
        $dataRetur = $this->returModel->findAll();
        // dd ($dataRetur);
        $getJenisBb = $this->masterMaterial->getJenisBahanBaku();
        // $urlApi = 'http://172.23.39.117/CapacityApps/public/api/getDataArea';
        $urlApi = 'http://172.23.39.117/CapacityApps/public/api/getDataArea';
        $getArea = json_decode(file_get_contents($urlApi), true);
        // dd ($getArea);
        $jenis = $this->request->getGet('jenis');
        $area = $this->request->getGet('area');
        $tgl = $this->request->getGet('tgl_retur');
        $model = $this->request->getGet('no_model');
        $kodeWarna = $this->request->getGet('kode_warna');

        // Logika untuk menentukan apakah ada filter
        $isFiltered = $jenis || $area || $tgl || $model || $kodeWarna;
        $isFiltered = urlencode($isFiltered);
        // Ambil data hanya jika ada filter
        $retur = $isFiltered ? $this->returModel->getFilteredData($this->request->getGet()) : $dataRetur;
        $data = [
            'title' => 'Retur',
            'retur' => $retur,
            'jenis' => $getJenisBb,
            'area' => $getArea,
            'active' => $this->active,
            'role' => $this->role,
            'filters' => $this->filters,
            'isFiltered' => $isFiltered
        ];

        return view($data['role'] . '/retur/index', $data);
    }

    public function listRetur()
    {
        $area = $this->request->getGet('area');
        // $noModel = $this->request->getGet('model') ?? '';
        // $tglBuat = $this->request->getGet('tglBuat') ?? '';

        $listRetur = $this->returModel->getListRetur($area);
        return $this->response->setJSON($listRetur);
    }

    public function cekBahanBaku()
    {
        $model = $this->request->getGet('noModel') ?? '';

        $search = '';
        $material = $this->materialModel->MaterialPerOrder($model);
        $res = [];
        foreach ($material as &$row) {
            $schedule = $this->scheduleCelupModel->schedulePerArea($row['no_model'], $row['item_type'], $row['kode_warna'], $search);

            $scheduleData = !empty($schedule) ? $schedule[0] : [];

            $fields = [
                'start_mc',
                'kg_celup',
                'lot_urut',
                'lot_celup',
                'tanggal_schedule',
                'tanggal_bon',
                'tanggal_celup',
                'tanggal_bongkar',
                'tanggal_press',
                'tanggal_oven',
                'tanggal_tl',
                'tanggal_rajut_pagi',
                'tanggal_kelos',
                'tanggal_acc',
                'tanggal_reject',
                'tanggal_perbaikan',
                'last_status',
                'ket_daily_cek',
                'po_plus'
            ];

            foreach ($fields as $field) {
                $row[$field] = $scheduleData[$field] ?? ''; // Isi dengan data jadwal atau kosong jika tidak ada
            }

            $res[] = $row;
        }
        return $this->respond($res, 200);
    }

    public function getPengirimanArea()
    {
        $noModel = $this->request->getGet('noModel') ?? '';
        // $results = $this->pengeluaranModel->searchPengiriman($noModel);
        $results = $this->pengeluaranModel->searchPengiriman2($noModel);

        // Konversi stdClass menjadi array
        $resultsArray = json_decode(json_encode($results), true);

        return $this->respond($resultsArray, 200);
    }

    public function approve()
    {
        $post = $this->request->getPost();
        $data = $this->returModel->find($post['id_retur']);

        $no_model   = $data['no_model'] ?? null;
        $item_type  = $data['item_type'] ?? null;
        $kode_warna = $data['kode_warna'] ?? null;
        $warna      = $data['warna'] ?? null;
        $area       = $data['area_retur'] ?? null;
        $tgl_retur  = $data['tgl_retur'] ?? null;
        $lot_retur  = $data['lot_retur'] ?? null;
        $kategori   = $data['kategori'] ?? null;
        $catatan    = $post['catatan'] ?? '';

        // ambil semua retur matching yang belum di-acc
        $builder = $this->returModel->table('retur');
        $builder->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->where('warna', $warna)
            ->where('area_retur', $area)
            ->where('tgl_retur', $tgl_retur)
            ->where('lot_retur', $lot_retur)
            ->where('kategori', $kategori)
            ->where('waktu_acc_retur IS NULL', null, false);

        $rows = $builder->get()->getResultArray();

        if (empty($rows)) {
            session()->setFlashdata('error', 'Tidak ada retur yang harus di-approve pada grup ini.');
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($rows as $r) {

            try {
                $idCelup = $this->scheduleCelupModel->getIdCelups($r);
            } catch (\Throwable $e) {
                log_message('error', 'getIdCelups error: ' . $e->getMessage());
                $idCelup = null;
            }

            $outData = [
                'id_retur'    => $r['id_retur'],
                'id_celup'    => $idCelup ?? null,
                'no_model'    => $r['no_model'],
                'l_m_d'       => '',
                'no_karung'   => $r['krg_retur'],
                'kgs_kirim'   => (float)$r['kgs_retur'],
                'cones_kirim' => (int)$r['cns_retur'],
                'lot_kirim'   => $r['lot_retur'],
                'admin'       => session()->get('username'),
                'created_at'  => date('Y-m-d H:i:s')
            ];

            // insert ke out celup
            $this->outCelupModel->insert($outData);

            // update retur
            $updateRetur = [
                'keterangan_gbn' => 'Approve: ' . $catatan,
                'waktu_acc_retur' => date('Y-m-d H:i:s'),
                'admin'          => session()->get('username'),
            ];
            $this->returModel->update($r['id_retur'], $updateRetur);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Transaksi approve per-row gagal untuk grup: ' . json_encode([
                'group' => [$no_model, $item_type, $kode_warna, $lot_retur, $area]
            ]));
            session()->setFlashdata('error', 'Gagal meng-approve retur (transaksi gagal).');
            return redirect()->back();
        }

        session()->setFlashdata('success', 'Data berhasil di approve ' . count($rows));
        return redirect()->to(base_url(session()->get('role') . '/retur'));
    }

    public function reject()
    {
        $post = $this->request->getPost();
        $reject = $post['catatan'] ?? '';
        $text = 'Reject: ' . $reject;

        // ambil baris referensi
        $data = $this->returModel->find($post['id_retur']);

        // ambil key grup dari baris referensi
        $no_model   = $data['no_model'] ?? null;
        $item_type  = $data['item_type'] ?? null;
        $kode_warna = $data['kode_warna'] ?? null;
        $warna      = $data['warna'] ?? null;
        $area       = $data['area_retur'] ?? null;
        $tgl_retur  = $data['tgl_retur'] ?? null;
        $lot_retur  = $data['lot_retur'] ?? null;
        $kategori   = $data['kategori'] ?? null;

        // cari semua retur matching grup yang belum di-acc
        $builder = $this->returModel->table('retur');
        $builder->where('no_model', $no_model)
            ->where('item_type', $item_type)
            ->where('kode_warna', $kode_warna)
            ->where('warna', $warna)
            ->where('area_retur', $area)
            ->where('tgl_retur', $tgl_retur)
            ->where('lot_retur', $lot_retur)
            ->where('kategori', $kategori)
            ->where('waktu_acc_retur IS NULL', null, false);

        $rows = $builder->get()->getResultArray();

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($rows as $r) {
            $updateRetur = [
                'keterangan_gbn'  => $text,
                'waktu_acc_retur' => date('Y-m-d H:i:s'),
                'admin'           => session()->get('username'),
            ];

            $returBuilder = $db->table('retur');
            $returBuilder->where('id_retur', $r['id_retur'])->update($updateRetur);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Transaksi reject gagal untuk grup: ' . json_encode([
                'group' => [$no_model, $item_type, $kode_warna, $lot_retur, $area]
            ]));
            session()->setFlashdata('error', 'Gagal memproses reject (transaksi gagal).');
            return redirect()->back();
        }

        session()->setFlashdata('success', 'Data Berhasil Di Reject');
        return redirect()->to(base_url(session()->get('role') . '/retur'));
    }


    public function returArea()
    {
        $data = $this->kategoriReturModel->getKategoriRetur();
        $kategoriRetur = [];
        foreach ($data as $item) {
            $kategoriRetur[] = [
                'nama_kategori' => $item['nama_kategori'],
                'tipe_kategori' => $item['tipe_kategori']
            ];
        }
        $apiUrl  = 'http://172.23.39.117/CapacityApps/public/api/getDataArea';
        $response = file_get_contents($apiUrl);

        $area = json_decode($response, true);

        return view($this->role . '/retur/index', [
            'active'     => $this->active,
            'title'      => 'Retur Area',
            'role'       => $this->role,
            'area'       => $area,
            'kategori' => $kategoriRetur,
        ]);
    }

    public function listBarcodeRetur()
    {
        $listRetur = $this->returModel->listBarcodeRetur();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "List Barcode Retur",
            'listRetur' => $listRetur,
        ];
        // dd($data);
        return view($this->role . '/retur/list-barcode-retur', $data);
    }

    public function detailBarcodeRetur($tglRetur)
    {
        $detailRetur = $this->returModel->detailBarcodeRetur($tglRetur);
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Detail Barcode Retur",
            'detailRetur' => $detailRetur,
            'tglRetur' => $tglRetur
        ];
        return view($this->role . '/retur/detail-barcode-retur', $data);
    }

    public function reportReturArea()
    {
        $getKategori = $this->kategoriReturModel->getKategoriRetur();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Report Retur Area",
            'getKategori' => $getKategori
        ];
        return view($this->role . '/retur/report-retur-area', $data);
    }

    public function filterReturArea()
    {
        $area = $this->request->getGet('area');
        $kategori = $this->request->getGet('kategori');
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->returModel->getFilterReturArea($area, $kategori, $tanggalAwal, $tanggalAkhir);

        if (!empty($data)) {
            foreach ($data as $key => $dt) {
                $kirim = $this->outCelupModel->getDataKirim($dt['id_retur']);
                $data[$key]['kg_kirim'] = $kirim['kg_kirim'] ?? 0;
                $data[$key]['cns_kirim'] = $kirim['cns_kirim'] ?? 0;
                $data[$key]['krg_kirim'] = $kirim['krg_kirim'] ?? 0;
                $data[$key]['lot_out'] = $kirim['lot_out'] ?? '-';
            }
        }

        return $this->response->setJSON($data);
    }
}
