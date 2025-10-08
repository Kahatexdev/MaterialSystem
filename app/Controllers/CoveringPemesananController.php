<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\PemesananModel;
use App\Models\PemesananSpandexKaretModel;
use App\Models\CoveringStockModel;
use App\Models\HistoryStockCoveringModel;
use App\Models\PengeluaranModel;
use App\Models\TrackingPoCovering;

class CoveringPemesananController extends BaseController
{
    protected $role;
    protected $active;
    protected $filters;
    protected $request;
    protected $masterMaterialModel;
    protected $pemesananModel;
    protected $pemesananSpandexKaretModel;
    protected $coveringStockModel;
    protected $historyCoveringStockModel;
    protected $pengeluaranModel;
    protected $trackingPoCoveringModel;

    public function __construct()
    {
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->coveringStockModel = new CoveringStockModel();
        $this->historyCoveringStockModel = new HistoryStockCoveringModel();
        $this->pengeluaranModel = new PengeluaranModel();
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

    public function index()
    {
        $jenis = $this->masterMaterialModel->getJenisSpandexKaret();
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'jenis' => $jenis,
        ];
        return view($this->role . '/pemesanan/index', $data);
    }

    public function pemesanan($jenis)
    {
        $dataPemesanan = $this->pemesananModel->getJenisPemesananCovering($jenis);
        // dd($dataPemesanan);
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
            'dataPemesanan' => $dataPemesanan,
        ];
        return view($this->role . '/pemesanan/pemesanan', $data);
    }

    public function detailPemesanan($jenis, $tgl_pakai)
    {
        // Ambil daftar pemesanan (query dari model Anda)
        $listPemesanan = $this->pemesananSpandexKaretModel
            ->getListPemesananCovering($jenis, $tgl_pakai);
        // dd ($listPemesanan);
        // Loop untuk men-set tombol enable/disable
        foreach ($listPemesanan as $key => $value) {
            $history = $this->pengeluaranModel
                ->where('id_total_pemesanan', $value['id_total_pemesanan'])
                ->where('status', 'Pengeluaran Jalur')
                ->first();
            // dd ($history);
            if (!empty($history)) {
                // kalau sudah pernah dikirim, tombol harus disable
                $listPemesanan[$key]['button'] = 'disable';
            } else {
                // kalau belum, tombol enable
                $listPemesanan[$key]['button'] = 'enable';
            }
        }
        // dd ($listPemesanan);

        // Ambil daftar tipe (jenis) unik dari covering_stock untuk select “Jenis” di modal
        $selectOptionData = $this->coveringStockModel
            ->select('jenis')
            ->distinct()
            ->findAll();

        $optionDataJenis = [];
        foreach ($selectOptionData as $row) {
            $optionDataJenis[] = $row['jenis'];
        }

        $data = [
            'active'         => $this->active,
            'title'          => 'Material System',
            'role'           => $this->role,
            'listPemesanan'  => $listPemesanan,
            'optionDataJenis' => $optionDataJenis
        ];
        return view("{$this->role}/pemesanan/detail-pemesanan", $data);
    }

    public function getCodePemesanan()
    {
        $itemType = $this->request->getGet('item_type');
        if (empty($itemType)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'item_type tidak boleh kosong']);
        }

        $data = $this->coveringStockModel
            ->select('code')
            ->where('jenis', $itemType)
            ->distinct()
            ->findAll();

        // Hasil: [ ['code' => '001'], ['code' => '002'], … ]
        return $this->response->setJSON($data);
    }

    public function getColorPemesanan()
    {
        $itemType   = $this->request->getGet('item_type');
        $kodeWarna  = $this->request->getGet('kode_warna');

        if (empty($itemType) || empty($kodeWarna)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'item_type atau kode_warna tidak boleh kosong']);
        }

        $data = $this->coveringStockModel
            ->select('color')
            ->where('jenis', $itemType)
            ->where('code', $kodeWarna)
            ->distinct()
            ->findAll();

        // Hasil: [ ['color' => 'Merah'], ['color' => 'Biru'], … ]
        return $this->response->setJSON($data);
    }

    public function reportPemesananKaretCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-pemesanan-karet', $data);
    }

    public function filterPemesananKaretCovering()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananKaret($tanggalAwal, $tanggalAkhir);

        // Ambil hanya kolom 'tgl_pakai' lalu buat jadi unik
        $tglPakaiList = array_column($data, 'tgl_pakai');
        $uniqTglPakai = array_values(array_unique($tglPakaiList));

        return $this->response->setJSON($uniqTglPakai);
    }

    public function reportPemesananSpandexCovering()
    {
        $data = [
            'active' => $this->active,
            'title' => 'Material System',
            'role' => $this->role,
        ];
        return view($this->role . '/pemesanan/report-pemesanan-spandex', $data);
    }

    public function filterPemesananSpandexCovering()
    {
        $tanggalAwal = $this->request->getGet('tanggal_awal');
        $tanggalAkhir = $this->request->getGet('tanggal_akhir');

        $data = $this->pemesananModel->getFilterPemesananSpandex($tanggalAwal, $tanggalAkhir);

        // Ambil hanya kolom 'tgl_pakai' lalu buat jadi unik
        $tglPakaiList = array_column($data, 'tgl_pakai');
        $uniqTglPakai = array_values(array_unique($tglPakaiList));

        return $this->response->setJSON($uniqTglPakai);
    }

    public function pesanKeCovering($id)
    {
        $ketGbn = $this->request->getGet('keterangan_gbn'); // string (boleh kosong)
        try {
            // 1) Ambil data pemesanan master
            $dataPemesanan = $this->pemesananModel->getPemesananSpandex($id);
            if (!$dataPemesanan) {
                return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
            }

            // 2) Siapkan payload untuk pemesanan_spandex_karet
            $dataSpandexKaret = [
                'id_total_pemesanan' => $dataPemesanan['id_total_pemesanan'],
                'admin'              => session()->get('username'),
            ];

            // 3) Insert & cek berhasil
            if (! $this->pemesananSpandexKaretModel->insert($dataSpandexKaret)) {
                return redirect()->back()->with('error', 'Gagal menyimpan data pemesanan.');
            }

            // 4) Ambil ID PK (id_psk) yang di‐generate
            $idPsk = $this->pemesananSpandexKaretModel->getInsertID();
            // dd ($idPsk);
            // dd ($dataPemesanan, $dataSpandexKaret, $idPsk, $ketGbn);
            $this->pemesananModel
                ->where('id_total_pemesanan', $dataPemesanan['id_total_pemesanan'])
                ->set(['keterangan_gbn' => $ketGbn])
                ->update();
            // 5) Update tabel pengeluaran agar punya referensi id_psk
            //    (sesuaikan nama model & kolom WHERE dengan struktur Anda)
            $this->pengeluaranModel->insert([
                'id_psk' => $idPsk,
                'id_total_pemesanan' => $dataPemesanan['id_total_pemesanan'],
                // 'status' => 'Pengeluaran Jalur',
                'admin' => session()->get('username'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // 6) Redirect sukses
            return redirect()->back()->with('success', 'Pemesanan berhasil dikirim ke Covering.');
        } catch (\Exception $e) {
            // tangkap error unexpected
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function updatePemesanan($id_psk)
    // {
    //     $postData = $this->request->getPost();
    //     // dd ($postData);
    //     // dd ($idttlPemesanan);
    //     $idStockCov = $this->coveringStockModel->select('id_covering_stock')
    //         ->where('jenis', $postData['itemtype'])
    //         ->where('code', $postData['kode_warna'])
    //         ->where('color', $postData['color'])
    //         ->first();
    //     $postData['stockItemId'] = $idStockCov['id_covering_stock'] ?? null;
    //     if (!$postData['stockItemId']) {
    //         return redirect()->back()->with('error', 'Stock tidak ditemukan.');
    //     }
    //     // dd ($idStockCov, $postData);
    //     // Ambil data stock lama berdasarkan ID
    //     $stockLama = $this->coveringStockModel->find($postData['stockItemId']);
    //     if (!$stockLama) {
    //         return redirect()->back()->with('error', 'Data stock lama tidak ditemukan.');
    //     }
    //     // Hitung perubahan jumlah cones dan kg
    //     $changeAmountCns = floatval($stockLama['ttl_cns']) - floatval($postData['total_cones']);
    //     $changeAmountKg = floatval($stockLama['ttl_kg']) - floatval($postData['total_pesan']);
    //     // dd ($changeAmountCns, $changeAmountKg);
    //     // Siapkan data untuk update stock
    //     $stockData = [
    //         'jenis'     => $postData['itemtype'],
    //         'code'   => $postData['kode_warna'],
    //         'color'        => $postData['color'],
    //         'ttl_cns'  => $changeAmountCns,
    //         'ttl_kg'  => $changeAmountKg,
    //         'jenis_cover'        => $stockLama['jenis_cover'],
    //         'jenis_benang' => $stockLama['jenis_benang'],
    //         'lmd'          => $stockLama['lmd'],
    //         'admin'        => session('username') // Atau siapa pun admin login saat ini
    //     ];
    //     // dd ($stockData);
    //     // Update stock di DB
    //     $this->coveringStockModel->update($postData['stockItemId'], $stockData);
    //     $noModel = $this->pemesananSpandexKaretModel->getNoModelById($id_psk);
    //     $idttlPemesanan = $this->pemesananSpandexKaretModel->select('id_total_pemesanan')
    //         ->where('id_psk', $id_psk)
    //         ->first();
    //     if (!$idttlPemesanan) {
    //         return redirect()->back()->with('error', 'ID Total Pemesanan tidak ditemukan.');
    //     }
    //     // dd ($noModel, $postData, $stockLama, $stockData);
    //     // Simpan ke history
    //     $historyStock = [
    //         'id_total_pemesanan' => $idttlPemesanan['id_total_pemesanan'] ?? null,
    //         'no_model'     => $noModel['no_model'] ?? '',
    //         'jenis'        => $stockLama['jenis'],
    //         'jenis_benang' => $stockLama['jenis_benang'],
    //         'jenis_cover'  => $stockLama['jenis_cover'],
    //         'color'        => $stockLama['color'],
    //         'code'         => $stockLama['code'],
    //         'lmd'          => $stockLama['lmd'],
    //         'ttl_cns'      => 0 - $postData['total_cones'],
    //         'ttl_kg'       => 0 - $postData['total_pesan'],
    //         'admin'        => $stockData['admin'],
    //         'keterangan'   => $postData['keterangan'],
    //         'created_at'   => date('Y-m-d H:i:s')
    //     ];
    //     // dd ($historyStock);
    //     $this->historyCoveringStockModel->insert($historyStock);

    //     // Update pengeluaran by id_psk
    //     $idPengeluaran = $this->pengeluaranModel->select('id_pengeluaran')
    //         ->where('id_psk', $id_psk)
    //         ->first();
    //     if (!$idPengeluaran) {
    //         return redirect()->back()->with('error', 'ID Pengeluaran tidak ditemukan.');
    //     }
    //     $dataPengeluaran = [
    //         'status' => 'Pengeluaran Jalur',
    //         'admin'        => session('username')
    //     ];
    //     $this->pengeluaranModel->update($idPengeluaran['id_pengeluaran'], $dataPengeluaran);

    //     return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    // }

    public function updatePemesanan($id_psk)
    {
        // Ambil data POST dan user
        $post = $this->request->getPost();
        // dd ($post);
        $admin = session('username');

        // Cari stock item
        // $stockItem = $this->coveringStockModel
        //     ->select('id_covering_stock, jenis_cover, jenis_benang, lmd')
        //     ->where([
        //         'jenis'  => $post['itemtype'],
        //         'code'   => $post['kode_warna'],
        //         'color'  => $post['color'],
        //     ])
        //     ->first();

        // if (!$stockItem) {
        //     return redirect()->back()->with('error', 'Stock tidak ditemukan.');
        // }

        // $stockId = $stockItem['id_covering_stock'];
        // $oldStock = $this->coveringStockModel->find($stockId);
        // if (!$oldStock) {
        //     return redirect()->back()->with('error', 'Data stock lama tidak ditemukan.');
        // }

        // // Hitung selisih
        // $conesDiff = floatval($oldStock['ttl_cns']) - floatval($post['total_cones']);
        // $kgDiff    = floatval($oldStock['ttl_kg'])  - floatval($post['total_pesan']);

        // Update stock
        // $this->coveringStockModel->update($stockId, [
        //     'ttl_cns'      => $conesDiff,
        //     'ttl_kg'       => $kgDiff,
        //     'admin'        => $admin,
        //     // tetap simpan field lain agar tidak null
        //     'jenis_cover'  => $stockItem['jenis_cover'],
        //     'jenis_benang' => $stockItem['jenis_benang'],
        //     'lmd'          => $stockItem['lmd'],
        // ]);

        // Ambil id_total_pemesanan dan no_model
        $total = $this->pemesananSpandexKaretModel
            ->select('id_total_pemesanan')
            ->where('id_psk', $id_psk)
            ->first();
        if (!$total) {
            return redirect()->back()->with('error', 'ID Total Pemesanan tidak ditemukan.');
        }
        $noModel = $this->pemesananSpandexKaretModel
            ->getNoModelById($id_psk)['no_model'] ?? '';

        // // Insert history
        // $this->historyCoveringStockModel->insert([
        //     'id_total_pemesanan' => $total['id_total_pemesanan'],
        //     'no_model'           => $noModel,
        //     'jenis'              => $oldStock['jenis'],
        //     'jenis_benang'       => $oldStock['jenis_benang'],
        //     'jenis_cover'        => $oldStock['jenis_cover'],
        //     'color'              => $oldStock['color'],
        //     'code'               => $oldStock['code'],
        //     'lmd'                => $oldStock['lmd'],
        //     'ttl_cns'            => -1 * floatval($post['total_cones']),
        //     'ttl_kg'             => -1 * floatval($post['total_pesan']),
        //     'admin'              => $admin,
        //     'keterangan'         => $post['keterangan'],
        //     'created_at'         => date('Y-m-d H:i:s'),
        // ]);

        // Update status pengeluaran
        $pengeluaran = $this->pengeluaranModel
            ->select('id_pengeluaran')
            ->where('id_psk', $id_psk)
            ->first();
        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'ID Pengeluaran tidak ditemukan.');
        }
        $areaOut = $this->pemesananModel
            ->select('admin')
            ->where('id_total_pemesanan', $total['id_total_pemesanan'])
            ->first();
        $this->pengeluaranModel->update($pengeluaran['id_pengeluaran'], [
            'area_out' => $areaOut['admin'] ?? '',
            'tgl_out' => date('Y-m-d'),
            'status' => 'Pengeluaran Jalur',
            'admin'  => $admin,
        ]);

        // update trackingpocovering
        $this->trackingPoCoveringModel->update($id_psk, [
            'status' => 'Pengeluaran Jalur',
            'admin'  => $admin,
        ]);


        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }

    public function updatePesanKeCovering($id)
    {
        $ketGbn = $this->request->getGet('keterangan_gbn'); // string (boleh kosong)
        try {
            // 1) Ambil data pemesanan master
            $dataPemesanan = $this->pemesananModel->getPemesananSpandex($id);
            if (!$dataPemesanan) {
                return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
            }

            $this->pemesananModel
                ->where('id_total_pemesanan', $dataPemesanan['id_total_pemesanan'])
                ->set(['keterangan_gbn' => $ketGbn])
                ->update();

            return redirect()->back()->with('success', 'Keterangan berhasil diperbarui.');
        } catch (\Exception $e) {
            // tangkap error unexpected
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
