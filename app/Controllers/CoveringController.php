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
use App\Models\HistoryStockCoveringModel;
use App\Models\TrackingPoCovering;

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
    protected $HistoryStockCoveringModel;
    protected $trackingPoCoveringModel;

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
        $this->HistoryStockCoveringModel = new HistoryStockCoveringModel();
        $this->trackingPoCoveringModel = new TrackingPoCovering();

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

    /**
     * Private: sinkron tracking PO covering dari data schedule
     * @param array $trackingData
     * @param array $scheduleData
     * @return array summary (rows_total_tracking, rows_matched, rows_updated, details)
     */
    private function syncTrackingFromSchedule(array $trackingData, array $scheduleData): array
    {
        // helper normalisasi (hapus non-alphanum + lowercase)
        $normalize = function ($v) {
            return $v;
        };

        // 1) build schedule map (simpan entri terbaru per key)
        $scheduleMap = [];
        foreach ($scheduleData as $s) {
            $model = isset($s['no_model']) ? trim($s['no_model']) : '';
            $kode  = isset($s['kode_warna']) ? trim($s['kode_warna']) : '';
            $it    = isset($s['item_type']) ? trim($s['item_type']) : '';
            if ($kode === '') continue;

            $normModel = $normalize($model);
            $normKode  = $normalize($kode);
            $normIt    = $normalize($it);

            // pilih timestamp yang tersedia
            $ts = '1970-01-01 00:00:00';
            if (!empty($s['updated_at']) && $s['updated_at'] !== '0000-00-00 00:00:00') $ts = $s['updated_at'];
            elseif (!empty($s['created_at']) && $s['created_at'] !== '0000-00-00 00:00:00') $ts = $s['created_at'];

            // keys: prefer more specific first
            $keys = [];
            if ($normModel !== '' && $normIt !== '') $keys[] = $normModel . '||' . $normKode . '||' . $normIt; // model||kode||it
            if ($normIt !== '')                           $keys[] = $normKode . '||' . $normIt;                  // kode||it
            if ($normModel !== '')                        $keys[] = $normModel . '||' . $normKode;              // model||kode
            $keys[] = $normKode;                                                       // kode

            foreach ($keys as $k) {
                // menyimpan seluruh row plus metadata
                $payload = array_merge($s, [
                    '_ts' => $ts,
                    '_norm_kode' => $normKode,
                    '_norm_it' => $normIt,
                    '_norm_model' => $normModel,
                ]);

                if (!isset($scheduleMap[$k]) || strtotime($ts) > strtotime($scheduleMap[$k]['_ts'])) {
                    $scheduleMap[$k] = $payload;
                }
            }
        }

        // 2) loop trackingData dan cari match, update via model
        $rowsMatched = 0;
        $rowsUpdated = 0;
        $details = [];

        foreach ($trackingData as $t) {
            $id    = $t['id_tpc'] ?? null; // ubah kalau pk beda
            $modelT = trim($t['no_model_anak'] ?? $t['no_model'] ?? '');
            $kodeT  = trim($t['kode_warna'] ?? '');
            $itT    = trim($t['item_type'] ?? '');

            if ($kodeT === '') {
                $details[] = ['id_tpc' => $id, 'no_model' => $modelT, 'kode_warna' => $kodeT, 'matched' => false, 'reason' => 'no kode_warna'];
                continue;
            }

            $normModelT = $normalize($modelT);
            $normKodeT  = $normalize($kodeT);
            $normItT    = $normalize($itT);
            $found = null;
            $matchedKey = null;

            // 1) exact model||kode||item_type
            if ($normModelT !== '' && $normItT !== '') {
                $k = $normModelT . '||' . $normKodeT . '||' . $normItT;
                if (isset($scheduleMap[$k])) {
                    $found = $scheduleMap[$k];
                    $matchedKey = $k;
                }
            }

            // 2) exact kode||item_type
            if (!$found && $normItT !== '') {
                $k = $normKodeT . '||' . $normItT;
                if (isset($scheduleMap[$k])) {
                    $found = $scheduleMap[$k];
                    $matchedKey = $k;
                }
            }

            // 3) exact model||kode
            if (!$found && $normModelT !== '') {
                $k = $normModelT . '||' . $normKodeT;
                if (isset($scheduleMap[$k])) {
                    $found = $scheduleMap[$k];
                    $matchedKey = $k;
                }
            }

            // 4) exact kode
            if (!$found && isset($scheduleMap[$normKodeT])) {
                $found = $scheduleMap[$normKodeT];
                $matchedKey = $normKodeT;
            }

            // 5) fallback: partial match (konservatif)
            if (!$found) {
                foreach ($scheduleMap as $k => $s) {
                    if (empty($s['_norm_kode'])) continue;
                    if (strpos($s['_norm_kode'], $normKodeT) !== false || strpos($normKodeT, $s['_norm_kode']) !== false) {
                        // jika both punya item_type, prefer sama item_type
                        if ($normItT !== '' && !empty($s['_norm_it'])) {
                            if ($s['_norm_it'] === $normItT) {
                                $found = $s;
                                $matchedKey = $k;
                                break;
                            } else {
                                // beda item_type → skip
                                continue;
                            }
                        } else {
                            // terima meski item_type kosong pada salah satu sisi
                            $found = $s;
                            $matchedKey = $k;
                            break;
                        }
                    }
                }
            }

            if ($found) {
                $rowsMatched++;
                $newStatus = $found['last_status'] ?? null;
                $newKet    = $found['ket_daily_cek'] ?? null;

                if ($newStatus === null && $newKet === null) {
                    $details[] = [
                        'id_tpc' => $id,
                        'no_model' => $modelT,
                        'kode_warna' => $kodeT,
                        'matched' => true,
                        'updated' => false,
                        'matched_with' => $matchedKey,
                        'reason' => 'no status/ket in schedule',
                    ];
                    continue;
                }

                $updateData = [
                    'status' => '(CELUP - ' . $newStatus . ')',
                    'keterangan' => '(CELUP - ' . $newKet . ')',
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($id) {
                    // update by primary key via model
                    $this->trackingPoCoveringModel->update($id, $updateData);
                } else {
                    // update by where (hati-hati: bisa update banyak baris)
                    $where = ['kode_warna' => $kodeT];
                    if ($itT !== '') $where['item_type'] = $itT;
                    $this->trackingPoCoveringModel->where($where)->set($updateData)->update();
                }

                $rowsUpdated++;
                $details[] = [
                    'id_tpc' => $id,
                    'no_model' => $modelT,
                    'kode_warna' => $kodeT,
                    'matched_with' => $matchedKey . ' (model:' . ($found['_norm_model'] ?? '') . ', kode:' . ($found['_norm_kode'] ?? '') . ')',
                    'status_after' => $newStatus,
                    'keterangan_after' => $newKet,
                    'updated' => true,
                ];
            } else {
                $details[] = [
                    'id_tpc' => $id,
                    'no_model' => $modelT,
                    'kode_warna' => $kodeT,
                    'matched' => false,
                ];
            }
        }

        return [
            'rows_total_tracking' => count($trackingData),
            'rows_matched' => $rowsMatched,
            'rows_updated' => $rowsUpdated,
            'details' => $details,
        ];
    }


    public function index()
    {

        // Ambil data dari model
        $poCount = $this->openPoModel->poCoveringCount();
        $ttlQtyPO = $this->openPoModel->poCoveringQty();
        $incomeToday = $this->HistoryStockCoveringModel->getIncomeToday();
        $expenseToday = $this->HistoryStockCoveringModel->getExpenseToday();

        // Ambil data pemasukan dan pengeluaran
        $incomeRaw = $this->HistoryStockCoveringModel->getPemasukan();
        $expenseRaw = $this->HistoryStockCoveringModel->getPengeluaran();

        // Ambil tanggal unik dari pemasukan dan pengeluaran
        $dateLabels = [];
        foreach (array_merge($incomeRaw, $expenseRaw) as $row) {
            $dateLabels[] = date('Y-m-d', strtotime($row['created_at']));
        }
        $dateLabels = array_values(array_unique($dateLabels));
        sort($dateLabels);

        // Format data pemasukan & pengeluaran berdasarkan tanggal
        $incomeData = [];
        $expenseData = [];


        foreach ($incomeRaw as $row) {
            $date = date('Y-m-d', strtotime($row['created_at']));
            if (!isset($incomeData[$date])) {
                $incomeData[$date] = 0;
            }
            $incomeData[$date] += $row['ttl_kg'];
        }

        foreach ($expenseRaw as $row) {
            $date = date('Y-m-d', strtotime($row['created_at']));
            if (!isset($expenseData[$date])) {
                $expenseData[$date] = 0;
            }
            $expenseData[$date] += $row['ttl_kg'];
        }


        $incomeDataFinal = [];
        $expenseDataFinal = [];

        foreach ($dateLabels as $date) {
            $incomeDataFinal[] = isset($incomeData[$date]) ? $incomeData[$date] : 0;
            $expenseDataFinal[] = isset($expenseData[$date]) ? $expenseData[$date] : 0;
        }
        $rawAll = array_merge(
            array_map(fn($r) => ['type' => 'in',  'date' => date('Y-m-d', strtotime($r['created_at'])), 'rec' => $r], $incomeRaw),
            array_map(fn($r) => ['type' => 'out', 'date' => date('Y-m-d', strtotime($r['created_at'])), 'rec' => $r], $expenseRaw)
        );

        // Inisialisasi detail per tanggal
        $detailData = array_fill(0, count($dateLabels), []);
        foreach ($rawAll as $item) {
            $i = array_search($item['date'], $dateLabels);
            if ($i !== false) {
                // ambil hanya field yg penting
                $r = $item['rec'];
                $detailData[$i][] = [
                    'type'      => $item['type'],       // in / out
                    'no_model'  => $r['no_model'],
                    'jenis'     => $r['jenis'],
                    'color'     => $r['color'],
                    'code'      => $r['code'],
                    'ttl_kg'    => $r['ttl_kg'],
                    'keterangan' => $r['keterangan'],
                    'time'      => date('H:i', strtotime($r['created_at'])),
                ];
            }
        }


        // fetch data automatically from database for tracking po covering
        // panggil private function untuk sinkronisasi
        $trackingData = $this->trackingPoCoveringModel->dailyUpdateTrackingPO();
        $scheduleData = $this->scheduleCelupModel->where('tanggal_schedule >=', date('Y-m-01', strtotime('-1 month')))
            ->where('tanggal_schedule <', date('Y-m-01', strtotime('+2 month')))
            ->findAll();
        $summary = $this->syncTrackingFromSchedule($trackingData, $scheduleData);

        // debug / inspect: ganti dengan return view/json jika perlu
        // dd($summary);

        $data = [
            'active' =>  $this->active,
            'title' => 'Covering Dashboard',
            'role' => $this->role,
            'poCount' => $poCount,
            'ttlQtyPO' => $ttlQtyPO,
            'incomeToday' => $incomeToday,
            'expenseToday' => $expenseToday,
            'dateLabels' => $dateLabels,
            'incomeData' => $incomeDataFinal, // Data yang sudah sesuai urutan tanggal
            'expenseData' => $expenseDataFinal, // Data yang sudah sesuai urutan tanggal
            'detailData'    => $detailData,
        ];


        return view($data['role'] . '/dashboard/index', $data);
    }

    public function po()
    {
        $poCovering = $this->openPoModel->getPOCovering();
        // dd($poCovering);
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
        // 1. Fetch the details for today’s POs
        $poDetail = $this->openPoModel->getPODetailCovering($tgl_po);

        // 2. Fetch all POs for this person (so we can see who is a child of whom)
        $poAll = $this->openPoModel
            ->select('id_po, id_induk, penanggung_jawab')
            ->where('penanggung_jawab', 'Paryanti')
            ->groupBy('id_po')
            ->findAll();

        // 3. Extract only the non-null parent-IDs that we want to exclude
        $indukIds = array_filter(array_column($poAll, 'id_induk'), function ($v) {
            return $v !== null;
        });

        // 4. Loop through your details and skip any whose id_po is in the $indukIds list
        $filteredDetails = [];
        foreach ($poDetail as $item) {
            if (in_array($item['id_po'], $indukIds, true)) {
                // this PO has already “parented” another one, so skip it
                continue;
            }
            $filteredDetails[] = $item;
        }

        // 5. Inspect what’s left
        // dd($poDetail,$poAll,$filteredDetails);
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
            'poDetail' => $filteredDetails,
            'coveringData' => $coveringData,
        ];
        return view($this->role . '/po/detail', $data);
    }

    public function getDetailByNoModel($tgl_po, $noModel)
    {
        $tgl_po = urldecode($tgl_po);
        $tgl_po = date('Y-m-d', strtotime($tgl_po));
        $noModel = $noModel;

        // Parse noModel into an array
        // $noModel = str_replace(' ', '', $noModel); // Remove spaces
        $noModelArray = str_replace('', '', $noModel);
        // dd ($noModelArray);
        // $idInduk = $this->request->getGet('id_induk');
        // $data = $this->openPoModel->getPODetailCovering($tgl_po);

        $data = $this->openPoModel->getDetailByNoModel($tgl_po, $noModelArray);
        log_message('debug', 'Data from getDetailByNoModel: ' . json_encode($data));

        $detail = [];
        foreach ($data as $item) {
            $id_induk = $item['id_induk'];
            $noModelPO = 'POCOVERING ' . $item['no_model'];
            $detail[] = $this->openPoModel->getDetailByNoModelAndIdInduk($tgl_po, $noModelPO, $id_induk);
        }
        // var_dump($id_induk);
        // $detail = $this->openPoModel->getDetailByNoModelAndIdInduk($tgl_po, $id_induk);
        // var_dump($detail);
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
        $tgl_po = $data['tgl_po'];
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
                        'created_at' => $tgl_po . ' ' . date('H:i:s'),
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
    public function pemasukan()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Pemasukan',
            'role' => $this->role,
        ];

        return view($this->role . '/warehouse/pemasukan', $data);
    }
    public function pengeluaranJalur()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Pengeluaran Jalur',
            'role' => $this->role,
        ];

        return view($this->role . '/warehouse/pengeluaran_jalur', $data);
    }
    public function pengirimanArea()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Pengiriman Area',
            'role' => $this->role,
        ];

        return view($this->role . '/warehouse/pengiriman_area', $data);
    }

    public function reportPemasukan()
    {
        $getPemasukan = $this->HistoryStockCoveringModel->getPemasukan();
        dd($getPemasukan);
    }

    public function bukaPoCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Buka PO',
            'role' => $this->role,
        ];
        return view($this->role . '/po/open-po-covering', $data);
    }

    public function getDetailByTglPO()
    {
        $tgl_po = $this->request->getPost('tgl_po');
        $data = $this->openPoModel->getPODetailCovering($tgl_po);
        return $this->response->setJSON($data);
    }

    public function saveOpenPOCovering()
    {
        $data = $this->request->getPost();

        if (isset($data['detail']) && is_array($data['detail'])) {
            foreach ($data['detail'] as $row) {
                $this->openPoModel->save([
                    'id_induk'        => $row['id_induk'],
                    'no_model'        => trim($data['no_model']),
                    'item_type'       => trim($row['item_type']),
                    'kode_warna'      => trim($row['kode_warna']),
                    'color'           => trim($row['color']),
                    'kg_po'           => $row['kg_po'] ?? null,
                    'bentuk_celup'    => $data['bentuk_celup'] ?? null,
                    'jenis_produksi'  => $data['jenis_produksi'] ?? null,
                    'ket_celup'       => $data['ket_celup'] ?? null,
                    'penerima'        => 'Retno',
                    'penanggung_jawab' => 'Paryanti',
                    'admin'           => session()->get('username') ?? '',
                    'created_at'      => $data['tgl_po_covering'] ?? null
                ]);
            }
        }

        return redirect()->to(base_url($this->role . '/po'))->with('success', 'Data Open PO Celup berhasil disimpan.');
    }

    public function detailPoCovering($tgl_po)
    {
        $getData = $this->openPoModel->getDetailPoCovering($tgl_po);
        // dd($getData);
        $data = [
            'active' => $this->active,
            'title' => 'Detail PO Covering',
            'role' => $this->role,
            'getData' => $getData,
        ];

        return view($this->role . '/po/detail-po-covering', $data);
    }

    public function updateDetailPoCovering($id_po)
    {
        $post = $this->request->getPost();

        $updateData = [
            'item_type'       => $post['item_type'] ?? null,
            'kode_warna'      => $post['kode_warna'] ?? null,
            'color'           => $post['color'] ?? null,
            'kg_po'           => $post['kg_po'] ?? null,
            'keterangan'      => $post['keterangan'] ?? null,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $result = $this->openPoModel->update($id_po, $updateData);

        if ($result) {
            return redirect()->back()->with('success', 'Data PO Covering berhasil diupdate.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate data PO Covering.');
        }
    }

    public function deleteDetailPoCovering($id_po)
    {
        $result = $this->openPoModel->delete($id_po);

        if ($result) {
            return redirect()->back()->with('success', 'Data PO Covering berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data PO Covering.');
        }
    }
}
