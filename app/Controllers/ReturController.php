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
use App\Models\PengeluaranModel;
use App\Models\ClusterModel;
use App\Models\StockModel;
use App\Models\HistoryStock;
use App\Models\TotalPemesananModel;
use App\Models\PemesananModel;
use App\Models\PemesananSpandexKaretModel;

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
    protected $clusterModel;
    protected $stockModel;
    protected $pengeluaranModel;
    protected $historyStock;
    protected $totalPemesananModel;
    protected $pemesananModel;
    protected $pemesananSpandexKaretModel;

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
        $this->clusterModel = new ClusterModel();
        $this->stockModel = new StockModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->historyStock = new HistoryStock();
        $this->totalPemesananModel = new TotalPemesananModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();

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
        // $urlApi = api_url('capacity').'getDataArea';
        $urlApi = api_url('capacity') . 'getDataArea';
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
        $tglReq = $this->returModel->getListTglRetur();
        // dd($tglReq);
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
            'isFiltered' => $isFiltered,
            'tglReq' => $tglReq
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
        $query = http_build_query([
            'jenis' => $post['jenis'] ?? '',
            'area' => $post['area'] ?? '',
            'tgl_retur' => $post['tgl_retur'] ?? '',
            'no_model' => $post['no_model'] ?? '',
            'kode_warna' => $post['kode_warna'] ?? ''
        ]);
        $url = base_url(session()->get('role') . '/retur' . (!empty($query) ? '?' . $query : ''));

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
        // return redirect()->to(base_url(session()->get('role') . '/retur'));

        return redirect()->to($url);
    }

    public function reject()
    {
        $post = $this->request->getPost();
        $reject = $post['catatan'] ?? '';
        $text = 'Reject: ' . $reject;

        // ambil baris referensi
        $data = $this->returModel->find($post['id_retur']);
        $query = http_build_query([
            'jenis' => $post['jenis'] ?? '',
            'area' => $post['area'] ?? '',
            'tgl_retur' => $post['tgl_retur'] ?? '',
            'no_model' => $post['no_model'] ?? '',
            'kode_warna' => $post['kode_warna'] ?? ''
        ]);
        $url = base_url(session()->get('role') . '/retur' . (!empty($query) ? '?' . $query : ''));

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
        // return redirect()->to(base_url(session()->get('role') . '/retur'));
        return redirect()->to($url);
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
        $apiUrl  = api_url('capacity') . 'getDataArea';
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

        $mapKey = [];
        $filteredData = [];
        $result = [];

        foreach ($data as $row) {
            $keyMap = implode('|', [
                $row['no_model'],
                $row['item_type'],
                $row['kode_warna'],
                $row['warna']
            ]);

            //JANGAN DIHAPUS, BUAT CEK DUPLIKASI
            if (isset($mapKey[$keyMap])) {
                continue;
            }

            $mapKey[$keyMap] = true;

            $filteredData[] = [
                'no_model'   => $row['no_model'],
                'item_type'  => $row['item_type'],
                'kode_warna' => $row['kode_warna'],
                'color'      => $row['warna']
            ];
        }
        $qtyMap = $this->qtyPcsService->getQtyPcs($filteredData);

        foreach ($data as $row) {
            $model = isset($row['no_model']) ? $row['no_model'] : '';
            $currentItemType = $row['item_type'];
            $currentKodeWarna = $row['kode_warna'];
            $currentColor = $row['warna'];

            $uniqueKey  = $model . '|' . $currentItemType . '|' . $currentKodeWarna . '|' . $currentColor;

            // Override kg_po & qty_po
            if (isset($qtyMap[$uniqueKey])) {
                $row['total_kgs']  = $qtyMap[$uniqueKey]['kg_po'];
            } else {
                $row['total_kgs']  = 0;
            }

            $result[] = $row;
        }

        return $this->response->setJSON($result);
    }
    public function returSample()
    {
        $no_model = $this->masterOrderModel->getAllNoModel();
        $cluster = $this->clusterModel->getDataCluster();
        $data = [
            'role' => $this->role,
            'active' => $this->active,
            'title' => "Retur Sample",
            'no_model' => $no_model,
            'cluster' => $cluster
        ];
        return view($this->role . '/retur/retur-sample', $data);
    }
    public function getItemTypeForReturSample()
    {
        $db = \Config\Database::connect();
        $idOrder = $this->request->getGet('id_order'); // ambil dari query string

        if (!$idOrder) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID Order tidak ditemukan.'
            ]);
        }

        // Data dari order (tabel material)
        $orderItemTypes = $db->table('material')
            ->distinct()
            ->select('item_type')
            ->where('id_order', $idOrder)
            ->groupBy('item_type')
            ->orderBy('item_type', 'ASC')
            ->get()
            ->getResultArray();

        // Data dari master material
        $masterItemTypes = $db->table('master_material')
            ->distinct()
            ->select('item_type')
            ->groupBy('item_type')
            ->orderBy('item_type', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'orderItemTypes'  => $orderItemTypes,
            'masterItemTypes' => $masterItemTypes
        ]);
    }
    public function saveReturSample()
    {
        $data = $this->request->getPost();
        // dd($data);

        $db = \Config\Database::connect();
        // mulai transaksi
        $db->transStart();

        try {
            $pesanTambahan = ''; // <-- flag pesan tambahan
            // cek apakah sudah ada item type di tabel master material
            $cekMasterMaterial = $this->masterMaterial
                ->select('item_type')
                ->where('item_type', $data['item_type'])
                ->first();

            // jika data material belum ada
            if (!$cekMasterMaterial) {
                // insert data material baru
                $newMasterMaterial = [
                    'item_type'      => $data['item_type'],
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                // lakukan insert
                if (!$this->masterMaterial->insert($newMasterMaterial)) {
                    throw new \Exception('Gagal menambahkan data master material baru.');
                }
                // tambahkan pesan tambahan
                $pesanTambahan = 'Jangan lupa lengkapi data master material.';
            }

            // cek apakah ada data material
            $cekMaterial = $this->materialModel
                ->select('item_type')
                ->where('id_order', $data['id_order'])
                ->where('item_type', $data['item_type'])
                ->where('kode_warna', $data['kode_warna'])
                ->where('color', $data['warna'])
                ->first();

            // jika data material belum ada
            if (!$cekMaterial) {
                // insert data material baru
                $newMaterial = [
                    'id_order'      => $data['id_order'],
                    'area'          => 'SAMPLE',
                    'color'         => $data['warna'],
                    'item_type'     => $data['item_type'],
                    'kode_warna'    => $data['kode_warna'],
                    'admin'         => session()->get('username'),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                // lakukan insert
                if (!$this->materialModel->insert($newMaterial)) {
                    throw new \Exception('Gagal menambahkan data material baru.');
                }
            }
            // insert data retur
            foreach ($data['no_karung'] as $index => $noKarung) {
                if (!isset($data['kgs'][$index]) || !isset($data['cones'][$index]) || !isset($data['cluster'][$index])) {
                    throw new \Exception("Data karung ke-$noKarung tidak lengkap (kgs atau cones kosong).");
                }
                $dataRetur = [
                    'no_model'          => $data['no_model'],
                    'item_type'         => $data['item_type'],
                    'kode_warna'        => $data['kode_warna'],
                    'warna'             => $data['warna'],
                    'area_retur'        => 'SAMPLE',
                    'tgl_retur'         => $data['tgl_retur'],
                    'kgs_retur'         => $data['kgs'][$index],
                    'cns_retur'         => $data['cones'][$index],
                    'krg_retur'         => 1,
                    'lot_retur'         => $data['lot'],
                    'kategori'          => $data['kategori'],
                    'keterangan_gbn'    => 'Approve:' . (!empty($data['keterangan']) ? $data['keterangan'] : ''),
                    'waktu_acc_retur'   => date('Y-m-d H:i:s'),
                    'admin'             => session()->get('username'),
                    'created_at'        => date('Y-m-d H:i:s')
                ];
                // lakukan insert
                if (!$this->returModel->insert($dataRetur)) {
                    throw new \Exception('Gagal menambahkan data retur.');
                }

                // ambil ID retur yang baru saja diinsert
                $idRetur = $this->returModel->getInsertID();

                // insert out celup
                $dataOutCelup = [
                    'id_retur'      => $idRetur,
                    'no_model'      => $data['no_model'],
                    'no_karung'     => $noKarung,
                    'kgs_kirim'     => $data['kgs'][$index],
                    'cns_kirim'     => $data['cones'][$index],
                    'lot_kirim'     => $data['lot'],
                    'l_m_d'         => '',
                    'admin'         => session()->get('username'),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                // lakukan insert
                if (!$this->outCelupModel->insert($dataOutCelup)) {
                    throw new \Exception('Gagal menambahkan data out celup.');
                }

                // ambil ID out celup yang baru saja diinsert
                $idOutCelup = $this->outCelupModel->getInsertID();

                // insert out celup
                $dataPemasukan = [
                    'id_out_celup'  => $idOutCelup,
                    'tgl_masuk'     => date('Y-m-d'),
                    'nama_cluster'  => $data['cluster'][$index],
                    'out_jalur'     => '0',
                    'admin'         => session()->get('username'),
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                // lakukan insert
                if (!$this->pemasukanModel->insert($dataPemasukan)) {
                    throw new \Exception('Gagal menambahkan data pemasukan.');
                }

                // ambil ID out celup yang baru saja diinsert
                $idPemasukan = $this->pemasukanModel->getInsertID();

                // cek apakah ada data stock
                $cekStock = $this->stockModel
                    ->select('*')
                    ->where('no_model', $data['no_model'])
                    ->where('item_type', $data['item_type'])
                    ->where('kode_warna', $data['kode_warna'])
                    ->where('warna', $data['warna'])
                    ->where('lot_stock', $data['lot'])
                    ->where('nama_cluster', $data['cluster'][$index])
                    ->first();

                // jika stock sudah ada 
                if ($cekStock) {
                    $updateStock = [
                        'kgs_in_out' => $cekStock['kgs_in_out'] + $data['kgs'][$index],
                        'cns_in_out' => $cekStock['cns_in_out'] + $data['cones'][$index],
                        'krg_in_out' => $cekStock['krg_in_out'] + 1,
                    ];

                    if (!$this->stockModel->update($cekStock['id_stock'], $updateStock)) {
                        throw new \Exception('Gagal memperbarui data stok.');
                    }

                    $idStock = $cekStock['id_stock'];
                } else {
                    // Insert stok baru
                    $newStock = [
                        'no_model'     => $data['no_model'],
                        'item_type'    => $data['item_type'],
                        'kode_warna'   => $data['kode_warna'],
                        'warna'        => $data['warna'],
                        'nama_cluster' => $data['cluster'][$index],
                        'kgs_in_out'   => $data['kgs'][$index],
                        'cns_in_out'   => $data['cones'][$index],
                        'krg_in_out'   => 1,
                        'lot_stock'    => $data['lot'],
                        'admin'        => session()->get('username'),
                        'created_at'   => date('Y-m-d H:i:s'),
                    ];
                    if (!$this->stockModel->insert($newStock)) {
                        throw new \Exception('Gagal menambahkan data stok baru.');
                    }
                    $idStock = $this->stockModel->getInsertID();
                }

                // update id stock di tabel pemasukan
                if (!$this->pemasukanModel->update($idPemasukan, ['id_stock' => $idStock])) {
                    throw new \Exception('Gagal mengupdate id_stock pada pemasukan.');
                }
            }
            // commit transaksi kalau semua sukses
            $db->transCommit();


            session()->setFlashdata([
                'status'  => 'success',
                'message' => 'Data retur berhasil disimpan.' . ($pesanTambahan ? ' ' . $pesanTambahan : '')
            ]);

            return redirect()->to(base_url($this->role . '/retur/returSample'));
        } catch (\Exception $e) {
            $db->transRollback();

            session()->setFlashdata([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);

            return redirect()->to(base_url($this->role . '/retur/returSample'));
        }
    }
    public function getDataRepeat()
    {
        $idRetur = $this->request->getGet('id_retur');

        $data = $this->returModel->find($idRetur);

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
        // ↑ ini data per karung sesuai kebutuhanmu

        return $this->response->setJSON($rows);
    }

    public function approveRepeat()
    {
        $data = $this->request->getPost();
        // dd($data);

        // data fillter
        $query = http_build_query([
            'jenis' => $data['jenis'] ?? '',
            'area' => $data['area'] ?? '',
            'tgl_retur' => $data['tgl_retur'] ?? '',
            'no_model' => $data['no_model'] ?? '',
            'kode_warna' => $data['kode_warna'] ?? ''
        ]);
        $url = base_url(session()->get('role') . '/retur' . (!empty($query) ? '?' . $query : ''));

        // data post
        $areaRetur      = $data['area_retur'];
        $noModelRetur   = $data['no_model_retur'];
        $itemTypeRetur  = $data['item_type_retur'];
        $kodeWarnaRetur = $data['kode_warna_retur'];
        $colorRetur     = $data['color_retur'];
        $lotRetur       = $data['lot_retur'];
        $kategori       = $data['kategori'];
        $ket            = $data['keterangan'];
        $allId          = $data['id_retur'];
        $kgRetur        = $data['kg_retur'];
        $cnsRetur       = $data['cns_retur'];
        $noKarungRetur  = $data['no_karung_retur'];
        $modelRepeat    = $data['model_repeat'];
        $kgRepeat       = $data['kg_repeat'];
        $conesRepeat    = $data['cones_repeat'];

        // hitung jumlah id
        $countId = count($allId);

        // pengumpulan data untuk update supermarket area
        $collectRepeat = [];   // kumpulan semua repeat + id pengeluaran
        $collectRetur = [
            'no_model'   => $noModelRetur,
            'item_type'  => $itemTypeRetur,
            'kode_warna' => $kodeWarnaRetur,
            'warna'      => $colorRetur,
            'lot'        => $lotRetur
        ];

        $db = \Config\Database::connect();
        $db->transBegin(); // pakai transBegin untuk manual control

        try {
            // acc retur berdasarkan id retur
            $updateRetur = [
                'keterangan_gbn'    => 'Approve: ' . $ket,
                'waktu_acc_retur'   => date('Y-m-d H:i:s'),
                'admin'             => session()->get('username'),
            ];

            // kalau array dan lebih dari 1 → update menggunakan whereIn
            if (is_array($allId) && $countId > 1) {
                // gabungkan semua id retur
                $this->returModel
                    ->whereIn('id_retur', $allId)
                    ->set($updateRetur)
                    ->update();
            } else {
                // hanya 1 id
                $this->returModel->update($allId[0], $updateRetur);
            }

            foreach ($allId as $id) {
                $r = [
                    'no_model'      => $noModelRetur,
                    'item_type'     => $itemTypeRetur,
                    'kode_warna'    => $kodeWarnaRetur,
                    'lot_retur'     => $lotRetur,
                ];

                try {
                    $idCelup = $this->scheduleCelupModel->getIdCelups($r);
                } catch (\Throwable $e) {
                    log_message('error', 'getIdCelups error: ' . $e->getMessage());
                    $idCelup = null;
                }
                // insert id out celup by id retur
                $dataOutCelup = [
                    'id_retur'    => $id,
                    'id_celup'    => $idCelup ?? null,
                    'no_model'    => $noModelRetur,
                    'l_m_d'       => '',
                    'no_karung'   => $noKarungRetur[$id],
                    'kgs_kirim'   => (float)$kgRetur[$id],
                    'cones_kirim' => (int)$cnsRetur[$id],
                    'lot_kirim'   => $lotRetur,
                    'admin'       => session()->get('username'),
                    'created_at'  => date('Y-m-d H:i:s')
                ];
                // insert ke out celup
                $this->outCelupModel->insert($dataOutCelup);

                // ambil id out celup untuk pemasukan
                $idOutCelup = $this->outCelupModel->insertID();

                // insert pemasukan 
                $dataPemasukan = [
                    'id_out_celup'  => $idOutCelup,
                    'tgl_masuk'     => date('Y-m-d'),
                    'nama_cluster'  => 'barang_Jln',
                    'out_jalur'     => '1',
                    'admin'         => session()->get('username'),
                    'created_at'    => date('Y-m-d H:i:s'),
                ];

                $this->pemasukanModel->insert($dataPemasukan);

                // ambil id stock untuk update pemasukan
                $idPemasukan = $this->pemasukanModel->insertID();

                // cek sudah ada stock atau belum
                $existingStock = $this->stockModel
                    ->where('no_model', $noModelRetur)
                    ->where('item_type', $itemTypeRetur)
                    ->where('kode_warna', $kodeWarnaRetur)
                    ->where('nama_cluster', 'barang_Jln')
                    ->where('lot_stock', $lotRetur)
                    ->first();

                // update jika sudah ada
                if ($existingStock) {
                    // ambil id stock
                    $idStok = $existingStock['id_stock'];
                } else {
                    // insert jika belum ada
                    $dataStock = [
                        'no_model'     => $noModelRetur,
                        'item_type'    => $itemTypeRetur,
                        'kode_warna'   => $kodeWarnaRetur,
                        'warna'        => $colorRetur,
                        'lot_stock'    => $lotRetur,
                        'nama_cluster' => 'barang_Jln',
                        'admin'        => session()->get('username'),
                        'created_at'   => date('Y-m-d H:i:s')
                    ];
                    $this->stockModel->insert($dataStock);
                    // get id stock
                    $idStok = $this->stockModel->getInsertID();
                }

                // update id stock di pemasukan
                $this->pemasukanModel
                    ->where('id_pemasukan', $idPemasukan)
                    ->set('id_stock', $idStok)
                    ->update();
            }

            // pindah stock
            foreach ($modelRepeat as $r => $repeat) {
                // untuk menentukan pengurangan karung
                $countModel = count($repeat);
                $lastIndex  = $countModel - 1;

                foreach ($repeat as $d => $detail) {
                    // tentukan krgRepeat
                    if ($countModel > 0) {
                        // kalau ada data repeat
                        $krgRepeat = ($d == $lastIndex) ? 1 : 0;
                    } else {
                        // kalau tidak ada repeat
                        $krgRepeat = 1;
                    }

                    $parts = explode(' | ', $detail);

                    $noModelNew  = $parts[0] ?? '';
                    $itemTypeNew = $parts[1] ?? '';
                    $kodeWarnaNew = $parts[2] ?? '';
                    $warnaNew = $parts[3] ?? '';

                    // cek jenis
                    $master = $this->masterMaterial
                        ->select('jenis')
                        ->where('item_type', $itemTypeNew)
                        ->first();
                    $jenis = $master['jenis'];

                    // out celup new
                    $dataOutCelupNew = [
                        'no_model'    => $noModelNew,
                        'l_m_d'       => '',
                        'no_karung'   => $noKarungRetur[$id],
                        'kgs_kirim'   => (float)$kgRepeat[$r][$d],
                        'cones_kirim' => (int)$conesRepeat[$r][$d],
                        'lot_kirim'   => $lotRetur,
                        'admin'       => session()->get('username'),
                        'created_at'  => date('Y-m-d H:i:s')
                    ];

                    $this->outCelupModel->insert($dataOutCelupNew);
                    $idOutCelupNew = $this->outCelupModel->getInsertID();


                    if ($jenis === "BENANG" || $jenis === "NYLON") {
                        // cek sudah ada stock new atau belum
                        $existingStockNew = $this->stockModel
                            ->where('no_model', $noModelNew)
                            ->where('item_type', $itemTypeNew)
                            ->where('kode_warna', $kodeWarnaNew)
                            ->where('nama_cluster', 'barang_Jln')
                            ->where('lot_stock', $lotRetur)
                            ->first();

                        // get id jika sudah ada new
                        if ($existingStockNew) {
                            // ambil id stock new
                            $idStokNew = $existingStockNew['id_stock'];
                        } else {
                            if ($kategori === "BB SISA EXPORT") {
                                // insert jika belum ada new
                                $dataStockNew = [
                                    'no_model'      => $noModelNew,
                                    'item_type'     => $itemTypeNew,
                                    'kode_warna'    => $kodeWarnaNew,
                                    'warna'         => $warnaNew,
                                    'lot_awal'      => $lotRetur,
                                    'nama_cluster'  => 'barang_Jln',
                                    'admin'         => session()->get('username'),
                                    'created_at'    => date('Y-m-d H:i:s')
                                ];
                            } else {
                                // insert jika belum ada new
                                $dataStockNew = [
                                    'no_model'      => $noModelNew,
                                    'item_type'     => $itemTypeNew,
                                    'kode_warna'    => $kodeWarnaNew,
                                    'warna'         => $warnaNew,
                                    'lot_stock'      => $lotRetur,
                                    'nama_cluster'  => 'barang_Jln',
                                    'admin'         => session()->get('username'),
                                    'created_at'    => date('Y-m-d H:i:s')
                                ];
                            }
                            $this->stockModel->insert($dataStockNew);
                            // get id stock new
                            $idStokNew = $this->stockModel->getInsertID();
                        }
                        // insert pemasukan new
                        $dataPemasukanNew = [
                            'id_out_celup'  => $idOutCelupNew,
                            'tgl_masuk'     => date('Y-m-d'),
                            'nama_cluster'  => 'barang_Jln',
                            'out_jalur'     => '1',
                            'admin'         => session()->get('username'),
                            'created_at'    => date('Y-m-d H:i:s'),
                            'id_stock'      => $idStokNew,
                        ];
                        $this->pemasukanModel->insert($dataPemasukanNew);

                        // jika sisa export masukan history pindah order
                        if ($kategori === "BB SISA EXPORT") {
                            // insert history stock
                            $this->historyStock->insert([
                                'id_stock_old'  => $idStok, // ID stok lama
                                'id_stock_new'  => $idStokNew, // ID stok baru
                                'id_out_celup'  => $idOutCelup, // ID stok baru
                                'cluster_old'   => 'barangJln', // Cluster lama
                                'cluster_new'   => 'barangJln', // Cluster baru
                                'kgs'           => (float)$kgRepeat[$r][$d], // Total kgs
                                'cns'           => (int)$conesRepeat[$r][$d], // Total cns
                                'krg'           => $krgRepeat, // Total krg
                                'lot'           => $lotRetur, // Lot stok lama
                                'keterangan'    => "Pindah Order", // Keterangan pemindahan
                                'admin'         => session()->get('username'), // Admin yang melakukan
                                'created_at'    => date('Y-m-d H:i:s'), // Waktu pemindahan
                                'updated_at'    => null, // Kolom updated_at bisa null karena belum ada perubahan
                            ]);
                        }
                    }

                    // pemesanan
                    // cari id material
                    $material = $this->materialModel
                        ->select('material.id_material, master_material.jenis')
                        ->join('master_order', 'master_order.id_order=material.id_order')
                        ->join('master_material', 'master_material.item_type=material.item_type')
                        ->where('master_order.no_model', $noModelNew)
                        ->where('material.item_type', $itemTypeNew)
                        ->where('material.kode_warna', $kodeWarnaNew)
                        ->where('material.color', $warnaNew)
                        ->first();

                    // insert data total pemesanan
                    $dataTotalPemesanan = [
                        'ttl_jl_mc' => 0,
                        'ttl_kg' => (float)$kgRepeat[$r][$d],
                        'ttl_cns' => (int)$conesRepeat[$r][$d],
                    ];
                    $this->totalPemesananModel->insert($dataTotalPemesanan);
                    $idTotalPemesanan = $this->totalPemesananModel->getInsertID();

                    // pemesanan
                    $dataPemesanan = [
                        'id_material'           => $material['id_material'],
                        'tgl_list'              => date('Y-m-d'),
                        'tgl_pesan'             => date('Y-m-d H:i:s'),
                        'tgl_pakai'             => date('Y-m-d'),
                        'jl_mc'                 => 0,
                        'ttl_qty_cones'         => (int)$conesRepeat[$r][$d],
                        'ttl_berat_cones'       => (float)$kgRepeat[$r][$d],
                        'lot'                   => $lotRetur,
                        'id_total_pemesanan'    => $idTotalPemesanan,
                        'status_kirim'          => 'YA',
                        'admin'                 => $areaRetur,
                        'created_at'            => date('Y-m-d H:i:s'), // Waktu pemindahan
                    ];
                    $this->pemesananModel->insert($dataPemesanan);

                    if ($jenis == "SPANDEX" || $jenis == "KARET") {
                        // insert pemesanan spandex karet
                        $dataPemesananSK = [
                            'id_total_pemesanan'    => $idTotalPemesanan,
                            'admin'                 => session()->get('username'),
                            'created_at'            => date('Y-m-d H:i:s'), // Waktu pemindahan
                        ];
                        $this->pemesananSpandexKaretModel->insert($dataPemesananSK);
                        $idPsk = $this->pemesananSpandexKaretModel->getInsertID();
                    }

                    $ketGbn = ($kategori === "BB SISA EXPORT") ? "Repeat dari retur" : "";
                    // insert pengeluaran
                    $dataPengeluaran = [
                        'id_total_pemesanan'    => $idTotalPemesanan,
                        'id_psk'                => $idPsk ?? NULL,
                        'id_stock'              => $idStokNew ?? 0,
                        'area_out'              => $areaRetur,
                        'tgl_out'               => date('Y-m-d'),
                        'kgs_out'               => (float)$kgRepeat[$r][$d],
                        'cns_out'               => (int)$conesRepeat[$r][$d],
                        'krg_out'               => $krgRepeat,
                        'lot_out'               => $lotRetur,
                        'nama_cluster'          => 'barang_Jln',
                        'status'                => 'Pengiriman Area',
                        'keterangan_gbn'        => $ketGbn,
                        'admin'                 => session()->get('username'),
                        'created_at'            => date('Y-m-d H:i:s'), // Waktu pemindahan
                        'terima_area'           => '1',
                    ];

                    $this->pengeluaranModel->insert($dataPengeluaran);
                    $idPengeluaran = $this->pengeluaranModel->getInsertID();

                    // collect data repeat
                    $collectRepeat[] = [
                        'id_pengeluaran' => $idPengeluaran,
                        'no_model'       => $noModelNew,
                        'item_type'      => $itemTypeNew,
                        'kode_warna'     => $kodeWarnaNew,
                        'warna'          => $warnaNew,
                        'no_karung'      => $noKarungRetur[$id],
                        'kgs'            => (float)$kgRepeat[$r][$d],
                        'cns'            => (int)$conesRepeat[$r][$d],
                        'lot'            => $lotRetur,
                        'area'           => $areaRetur,
                    ];
                }
            }

            // kirim data untuk update supermarket area
            $send = [
                'retur'  => $collectRetur,
                'repeat' => $collectRepeat,
            ];
            log_message('warning', 'Approve retur data: ' . json_encode($send));

            $client = \Config\Services::curlrequest();
            // Kirim ke API sebagai JSON
            $response = $client->post(
                'http://172.23.44.14/CapacityApps/public/api/repeatSupermarket',
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json'    => $send,
                    'timeout' => 5, // biar tidak ngegantung
                ]
            );
            // Ambil result dari response
            $body = json_decode($response->getBody(), true);

            // Cek API supermarket sukses atau tidak
            if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
                throw new \Exception("API supermarket gagal");
            }

            $db->transCommit();

            session()->setFlashdata('success', 'Retur berhasil diapprove & disimpan ke supermarket area');
            return redirect()->to($url);
        } catch (\Throwable $e) {

            // rollback semua!
            $db->transRollback();

            log_message('error', 'Approve retur gagal: ' . $e->getMessage());

            session()->setFlashdata('error', 'Gagal approve retur. Semua data dibatalkan.');
            return redirect()->to($url);
        }
    }
}
