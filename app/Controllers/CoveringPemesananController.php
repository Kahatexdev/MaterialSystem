<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MasterMaterialModel;
use App\Models\PemesananModel;
use App\Models\PemesananSpandexKaretModel;
use App\Models\CoveringStockModel;
use App\Models\HistoryStockCoveringModel;

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

    public function __construct()
    {
        $this->masterMaterialModel = new MasterMaterialModel();
        $this->pemesananModel = new PemesananModel();
        $this->pemesananSpandexKaretModel = new PemesananSpandexKaretModel();
        $this->coveringStockModel = new CoveringStockModel();
        $this->historyCoveringStockModel = new HistoryStockCoveringModel();

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

        // Loop untuk men-set tombol enable/disable
        foreach ($listPemesanan as $key => $value) {
            $history = $this->historyCoveringStockModel
                ->where('id_total_pemesanan', $value['id_total_pemesanan'])
                ->first();

            if (!empty($history)) {
                // Jika di‐history sudah ada record, disable tombol
                $listPemesanan[$key]['button'] = 'disable';
            } else {
                $listPemesanan[$key]['button'] = 'enable';
            }
        }

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
        try {
            $dataPemesanan = $this->pemesananModel->getPemesananSpandex($id);

            if (!$dataPemesanan) {
                return redirect()->back()->with('error', 'Data pemesanan tidak ditemukan.');
            }

            $dataSpandexKaret = [
                'id_total_pemesanan' => $dataPemesanan['id_total_pemesanan'],
                'status' => '',
                'admin' => session()->get('username'),
            ];

            if ($this->pemesananSpandexKaretModel->insert($dataSpandexKaret)) {
                return redirect()->back()->with('success', 'Pemesanan berhasil dikirim ke Covering.');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan data pemesanan.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatePemesanan($id_psk)
    {
        $postData = $this->request->getPost();
        // dd ($postData);
        // dd ($idttlPemesanan);
        $idStockCov = $this->coveringStockModel->select('id_covering_stock')
            ->where('jenis', $postData['itemtype'])
            ->where('code', $postData['kode_warna'])
            ->where('color', $postData['color'])
            ->first();
        $postData['stockItemId'] = $idStockCov['id_covering_stock'] ?? null;
        if (!$postData['stockItemId']) {
            return redirect()->back()->with('error', 'Stock tidak ditemukan.');
        }
        // dd ($idStockCov, $postData);
        // Ambil data stock lama berdasarkan ID
        $stockLama = $this->coveringStockModel->find($postData['stockItemId']);
        if (!$stockLama) {
            return redirect()->back()->with('error', 'Data stock lama tidak ditemukan.');
        }
        // Hitung perubahan jumlah cones dan kg
        $changeAmountCns = floatval($stockLama['ttl_cns']) - floatval($postData['total_cones']);
        $changeAmountKg = floatval($stockLama['ttl_kg']) - floatval($postData['total_pesan']);
        // dd ($changeAmountCns, $changeAmountKg);
        // Siapkan data untuk update stock
        $stockData = [
            'jenis'     => $postData['itemtype'],
            'code'   => $postData['kode_warna'],
            'color'        => $postData['color'],
            'ttl_cns'  => $changeAmountCns,
            'ttl_kg'  => $changeAmountKg,
            'jenis_cover'        => $stockLama['jenis_cover'],
            'jenis_benang' => $stockLama['jenis_benang'],
            'lmd'          => $stockLama['lmd'],
            'admin'        => session('username') // Atau siapa pun admin login saat ini
        ];
        // dd ($stockData);
        // Update stock di DB
        $this->coveringStockModel->update($postData['stockItemId'], $stockData);
        $noModel = $this->pemesananSpandexKaretModel->getNoModelById($id_psk);
        $idttlPemesanan = $this->pemesananSpandexKaretModel->select('id_total_pemesanan')
            ->where('id_psk', $id_psk)
            ->first();
        if (!$idttlPemesanan) {
            return redirect()->back()->with('error', 'ID Total Pemesanan tidak ditemukan.');
        }
        // dd ($noModel, $postData, $stockLama, $stockData);
        // Simpan ke history
        $historyStock = [
            'id_total_pemesanan' => $idttlPemesanan['id_total_pemesanan'] ?? null,
            'no_model'     => $noModel['no_model'] ?? '',
            'jenis'        => $stockLama['jenis'],
            'jenis_benang' => $stockLama['jenis_benang'],
            'jenis_cover'  => $stockLama['jenis_cover'],
            'color'        => $stockLama['color'],
            'code'         => $stockLama['code'],
            'lmd'          => $stockLama['lmd'],
            'ttl_cns'      => 0 - $postData['total_cones'],
            'ttl_kg'       => 0 - $postData['total_pesan'],
            'admin'        => $stockData['admin'],
            'keterangan'   => $postData['keterangan'],
            'created_at'   => date('Y-m-d H:i:s')
        ];
        // dd ($historyStock);
        $this->historyCoveringStockModel->insert($historyStock);


        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }
}
