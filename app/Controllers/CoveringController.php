<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterOrderModel;
use App\Models\MaterialModel;
use App\Models\MasterMaterialModel;
use App\Models\OpenPoModel;
use App\Models\ScheduleCelupModel;
use App\Models\OutCelupModel;
use App\Models\BonCelupModel;
use App\Models\MesinCelupModel;

class CoveringController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $mesinCelupModel;
    protected $masterOrderModel;
    protected $materialModel;
    protected $masterMaterialModel;
    protected $openPoModel;
    protected $scheduleCelupModel;
    protected $outCelupModel;
    protected $bonCelupModel;

    public function __construct()
    {
        $this->masterOrderModel = new MasterOrderModel();
        $this->materialModel = new MaterialModel();
        $this->mesinCelupModel = new MesinCelupModel();
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->openPoModel = new OpenPoModel();
        $this->scheduleCelupModel = new ScheduleCelupModel();
        $this->outCelupModel = new OutCelupModel();
        $this->bonCelupModel = new BonCelupModel();

        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
        if ($this->filters   = ['role' => ['covering']] != session()->get('role')) {
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

        $data = [
            'active' => $this->active,
            'title' => 'Covering',
            'role' => $this->role,
        ];
        return view($this->role . '/dashboard/index', $data);
    }

    public function po()
    {
        $poCovering = $this->openPoModel->getPOCovering();
        // dd ($poCovering);
        $data = [
            'active' => $this->active,
            'title' => 'PO Celup',
            'role' => $this->role,
            'poCovering' => $poCovering,
        ];

        // dd($data);
        return view($this->role . '/po/index', $data);
    }

    public function schedule()
    {
        // Ambil parameter filter dari query string
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if ($startDate == null && $endDate == null) {
            // Jika startdate tidak tersedia, gunakan tanggal 3 hari ke belakang
            $startDate = date('Y-m-d', strtotime('-3 days'));
            // end date 7 hari ke depan
            $endDate = date('Y-m-d', strtotime('+6 days'));
        }

        // Konversi tanggal ke format DateTime jika tersedia
        $startDateObj = $startDate ? new \DateTime($startDate) : null;
        $endDateObj = $endDate ? new \DateTime($endDate) : null;

        // Ambil data jadwal dari model (filter berdasarkan tanggal jika tersedia)
        $scheduleData = $this->scheduleCelupModel->getScheduleCelupbyDate($startDateObj, $endDateObj);

        // dd ($scheduleData);
        // Ambil data mesin celup
        $mesin_celup = $this->mesinCelupModel->orderBy('no_mesin', 'ASC')->findAll();

        // Hitung total kapasitas yang sudah digunakan
        $totalCapacityUsed = array_sum(array_column($scheduleData, 'weight'));

        // Hitung total kapasitas maksimum dari semua mesin celup
        $totalCapacityMax = array_sum(array_column($mesin_celup, 'max_caps'));

        // Siapkan data untuk dikirimkan ke view
        $today = date('Y-m-d', strtotime('today'));
        $data = [
            'active' => $this->active,
            'title' => 'Schedule',
            'role' => $this->role,
            'scheduleData' => $scheduleData,
            'mesin_celup' => $mesin_celup,
            'totalCapacityUsed' => $totalCapacityUsed,
            'totalCapacityMax' => $totalCapacityMax,
            'currentDate' => new \DateTime(),
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        // Render view dengan data yang sudah disiapkan
        return view($this->role . '/schedule/index', $data);
    }

    public function poDetail($tgl_po)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $poDetail = $this->openPoModel->getPODetailCovering($tgl_po);
        $coveringData = session()->get('covering_data');
        if (empty($coveringData)) {
            $coveringData[0] = [
                'no_model' => '',
                'no_po' => '',
                'id_po' => '',
                'itemTypeCovering' => '',
                'kodeWarnaCovering' => '',
                'warnaCovering' => '',
                'keterangan' => '',
                'qty_covering' => ''
            ];
        }
        $data = [
            'active' => $this->active,
            'title' => 'PO Celup',
            'role' => $this->role,
            'tgl_po' => $tgl_po,
            'poDetail' => $poDetail,
            'coveringData' => $coveringData,
        ];
        return view($this->role . '/po/detail', $data);
    }

    public function getDetailByNoModel($tgl_po, $noModel)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $noModel = urldecode($noModel);
        $detail = $this->openPoModel->getDetailByNoModel($tgl_po, $noModel);
        return $this->response->setJSON($detail);
    }

    public function simpanKeSession()
    {
        // Ambil data dari POST
        $items = $this->request->getPost('items');

        // Ambil data lama dari session jika ada
        $existingData = session()->get('covering_data') ?? [];

        // Gabungkan data baru dengan data lama
        $updatedData = array_merge($existingData, $items);

        // Simpan ke session
        session()->set('covering_data', $updatedData);

        // Beri response atau redirect
        return redirect()->back()->with('success', 'Data berhasil disimpan di session');
    }

    public function savePOCovering()
    {
        $data = $this->request->getPost();
        // $no_po = $data['no_po'];
        // dd ($data);
        $coveringData = session()->get('covering_data') ?? [];
        $data['covering_data'] = $coveringData;
        // dd ($data);
        // Pastikan selected_items ada dan merupakan array
        $selectedItems = $data['selected_items'] ?? [];
        if (empty($selectedItems)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }

        $existingSelectedItems = session()->get('selected_items') ?? [];
        $data['selected_items'] = [];

        // Load model
        $openPoModel = new \App\Models\OpenPoModel();
        $db = \Config\Database::connect();
        $db->transBegin(); // Mulai transaksi database

        try {
            foreach ($selectedItems as $selectedIndex) {
                if (isset($coveringData[$selectedIndex])) {
                    $selectedItem = $coveringData[$selectedIndex];
                    $data['selected_items'][] = $selectedItem;
                    $existingSelectedItems[$selectedIndex] = $selectedItem;
                    // dd ($selectedItem);
                    // Siapkan data untuk disimpan ke database
                    $insertData = [
                        'no_model' => $data['no_po'],
                        'item_type' => $selectedItem['itemTypeCovering'],
                        'kode_warna' => $selectedItem['kodeWarnaCovering'],
                        'color' => $selectedItem['warnaCovering'], // Sesuaikan jika ada data warna
                        'kg_po' => $selectedItem['qty_covering'],
                        'keterangan' => $selectedItem['keterangan'], // Sesuaikan jika ada keterangan
                        'penerima' => 'Retno', // Sesuaikan dengan pengguna yang login
                        'penanggung_jawab' => 'Paryanti', // Sesuaikan dengan pengguna yang login
                        'admin' => session()->get('username') ?? '', // Ambil admin dari session
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'id_induk' => $selectedItem['id_po'] // Sesuaikan jika ada ID induk
                    ];
                    // Simpan ke database
                    if (!$openPoModel->insert($insertData)) {
                        throw new \Exception('Gagal menyimpan data ke database.');
                    }

                    // Hapus item yang telah dipilih dari coveringData hanya jika berhasil disimpan
                    unset($coveringData[$selectedIndex]);
                }
            }

            $db->transCommit(); // Commit transaksi jika semua berhasil
        } catch (\Exception $e) {
            $db->transRollback(); // Rollback transaksi jika ada kegagalan
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        // Simpan kembali array yang telah diperbarui ke sesi
        session()->set('covering_data', array_values($coveringData));
        session()->set('selected_items', $existingSelectedItems);

        return redirect('covering/po')->with('success', 'Data berhasil disimpan.');
    }

    // function unset session by index
    public function unsetSession($index)
    {
        $coveringData = session()->get('covering_data') ?? [];
        unset($coveringData[$index]);
        session()->set('covering_data', $coveringData);
        return redirect()->back()->with('success', 'Data berhasil dihapus dari session');
    }

    public function exportOpenPO($tgl_po)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $poDetail = $this->openPoModel->getPoForCelup($tgl_po);
        dd($poDetail);
    }

    public function memo()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Memo',
            'role' => $this->role,
        ];

        return view($this->role . '/memo/index', $data);
    }

    public function mesinCov()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Mesin Covering',
            'role' => $this->role,
        ];

        return view($this->role . '/mesin/index', $data);
    }
    public function warehouse()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Warehouse',
            'role' => $this->role,
        ];

        return view($this->role . '/warehouse/index', $data);
    }
}
