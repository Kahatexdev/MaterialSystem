<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\WarehouseBBModel;
use App\Models\HistoryStockBBModel;

class CoveringWarehouseBBController extends BaseController
{
    protected $warehouseBBModel;
    protected $historyStockBBModel;
    protected $role;
    protected $active;

    public function __construct()
    {
        $this->warehouseBBModel = new WarehouseBBModel();
        $this->historyStockBBModel = new HistoryStockBBModel();
        $this->role = session()->get('role');
        $this->active = '/index.php/' . session()->get('role');
    }

    public function index()
    {
        $perPage      = 9;
        $warehouseBB  = $this->warehouseBBModel->getWarehouseBB($perPage);
        $pager        = $this->warehouseBBModel->pager;

        // dd($warehouseBB);
        $data = [
            'title' => 'Warehouse Bahan Baku Covering',
            'active' => $this->active,
            'role' => $this->role,
            'warehouseBB' => $warehouseBB,
            'pager'       => $pager,
        ];
        return view($this->role . '/warehousebb/index', $data);
    }

    public function store()
    {
        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $this->request->getPost('kg'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        // Validate required fields
        if (
            empty($data['denier']) ||
            empty($data['jenis_benang']) ||
            empty($data['warna']) ||
            empty($data['kode']) ||
            empty($data['kg']) ||
            empty($data['admin'])
        ) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi.');
        }

        // Cek duplikasi berdasarkan denier, jenis_benang, warna, kode
        $existing = $this->warehouseBBModel
            ->where('denier', $data['denier'])
            ->where('jenis_benang', $data['jenis_benang'])
            ->where('warna', $data['warna'])
            ->where('kode', $data['kode'])
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Data dengan kombinasi Denier, Jenis Benang, Warna, dan Kode tersebut sudah ada.');
        }

        // Insert into history stock
        $historyData = [
            'denier' => $data['denier'],
            'jenis' => $data['jenis_benang'],
            'jenis_benang' => $data['jenis_benang'],
            'color' => $data['warna'],
            'code' => $data['kode'],
            'ttl_cns' => 0, // Assuming this is not used in covering
            'ttl_kg' => $data['kg'],
            'admin' => $data['admin'],
            'keterangan' => $data['keterangan']
        ];

        if (!$this->historyStockBBModel->insert($historyData)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah data ke history stock.');
        }

        if (!$this->warehouseBBModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambah data ke warehouse.');
        }

        return redirect()->back()->with('success', 'Data berhasil ditambahkan ke warehouse Bahan Baku Covering.');
    }

    public function update($id)
    {
        $kgBaru = (float) $this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];
        // dd($data, $id);
        // Ambil data lama
        $dataLama = $this->warehouseBBModel->find($id);
        if (!$dataLama) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Cek apakah kg lama < 0
        if ($dataLama['kg'] < 0) {
            return redirect()->back()->with('error', 'Stok bahan baku saat ini minus! Silakan periksa data sebelumnya.');
        }

        // Hitung selisih untuk history
        $selisihKg = $kgBaru - $dataLama['kg'];
        // dd($selisihKg);
        if ($selisihKg != 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => 0, // Assuming this is not used in covering
                'ttl_kg' => $selisihKg,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        $this->warehouseBBModel->update($id, $data);

        return redirect()->to($this->role . '/warehouseBB')->with('success', 'Data berhasil diupdate.');
    }

    public function pemasukan()
    {
        $idstockbb = $this->request->getPost('idstockbb');
        $kgBaru = (float)$this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'ttl_cns' => $this->request->getPost('ttl_cns'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        if ($kgBaru > 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => $data['ttl_cns'],
                'ttl_kg' => $kgBaru,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        if ($idstockbb && $kgBaru > 0) {
            $stokLama = $this->warehouseBBModel->find($idstockbb);

            if ($stokLama) {
                $kgBaru = $stokLama['kg'] + $kgBaru;
                $this->warehouseBBModel->update($idstockbb, ['kg' => $kgBaru]);

                return redirect()->to($this->role . '/warehouseBB')->with('success', 'Data pemasukan berhasil ditambahkan');
            }

            return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data tidak ditemukan');
        }

        return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data pemasukan gagal ditambahkan');
    }

    public function pengeluaran()
    {
        $idstockbb = $this->request->getPost('idstockbb');
        $kgBaru = (float)$this->request->getPost('kg');

        $data = [
            'denier' => $this->request->getPost('denier'),
            'jenis_benang' => $this->request->getPost('jenis_benang'),
            'warna' => $this->request->getPost('warna'),
            'kode' => $this->request->getPost('kode'),
            'kg' => $kgBaru,
            'ttl_cns' => $this->request->getPost('ttl_cns'),
            'keterangan' => $this->request->getPost('keterangan'),
            'admin' => session()->get('username')
        ];

        if ($kgBaru > 0) {
            $this->historyStockBBModel->insert([
                'denier' => $data['denier'],
                'jenis' => $data['jenis_benang'],
                'jenis_benang' => $data['jenis_benang'],
                'color' => $data['warna'],
                'code' => $data['kode'],
                'ttl_cns' => $data['ttl_cns'],
                'ttl_kg' => -$kgBaru,
                'admin' => $data['admin'],
                'keterangan' => $data['keterangan']
            ]);
        }

        if ($idstockbb && $kgBaru > 0) {
            // Ambil stok sekarang dulu
            $stok = $this->warehouseBBModel->find($idstockbb);

            if ($stok && $stok['kg'] >= $kgBaru) {
                $this->warehouseBBModel->update($idstockbb, ['kg' => $stok['kg'] - $kgBaru]);

                return redirect()->to($this->role . '/warehouseBB')->with('success', 'Pengeluaran berhasil dikurangi.');
            }
            return redirect()->to($this->role . '/warehouseBB')->with('error', 'Stok tidak mencukupi untuk pengeluaran');
        }

        return redirect()->to($this->role . '/warehouseBB')->with('error', 'Data tidak valid untuk pengeluaran');
    }

    public function importStokBahanBaku()
    {
        $file = $this->request->getFile('file_excel');
        // Validasi file
        if (!$file || !$file->isValid()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'File tidak valid atau tidak ditemukan.');
        }

        // Cek ekstensi dan ukuran maksimal (misal max 5MB)
        $allowedExt = ['xlsx', 'xls'];
        $ext = $file->getExtension();
        if (!in_array($ext, $allowedExt)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Format file tidak didukung. Gunakan .xlsx atau .xls');
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
        }

        // Simpan sementara
        $uploadDir = WRITEPATH . 'uploads/';
        $filePath = $uploadDir . $file->getName();
        try {
            $file->move($uploadDir, null, true);
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memindahkan file. ' . $e->getMessage());
        }

        // Jalankan import
        try {
            list($successCount, $failures) = $this->importStock($filePath);
        } catch (Exception $e) {
            unlink($filePath);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saat proses import: ' . $e->getMessage());
        }

        // Hapus file setelah selesai
        unlink($filePath);

        // Siapkan pesan
        if ($failures) {
            // Gagal sebagian
            $msg  = "Berhasil import: {$successCount}. Gagal import: " . count($failures) . ".";
            $msg .= '<ul>';
            foreach ($failures as $rowNum => $reason) {
                $msg .= "<li>Baris {$rowNum}: {$reason}</li>";
            }
            $msg .= '</ul>';
            return redirect()->to(base_url($this->role . '/warehouse'))->with('warning', $msg);
        }

        // Semua berhasil
        return redirect()->to(base_url($this->role . '/warehouse'))
            ->with('success', "Import berhasil seluruhnya ({$successCount} baris)");
    }

    private function importStock(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $successCount = 0;
        $failures = [];

        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            $rowNum = $row->getRowIndex();

            // Validasi minimal
            if (
                count($rowData) < 10
                || empty($rowData[0])
                || empty($rowData[2])
            ) {
                $failures[$rowNum] = 'Data kolom kurang lengkap';
                continue;
            }

            // Mapping data
            try {
                $item = [
                    'jenis'          => $rowData[0],
                    'color'          => $rowData[1],
                    'code'           => $rowData[2],
                    'jenis_mesin'    => $rowData[3],
                    'dr'             => $rowData[4],
                    'jenis_cover'    => $rowData[5],
                    'jenis_benang'   => $rowData[6],
                    'lmd'            => isset($rowData[7]) ? implode(', ', explode(',', $rowData[7])) : null,
                    'ttl_kg'         => (float) ($rowData[8] ?? 0),
                    'ttl_cns'        => (int) ($rowData[9] ?? 0),
                    'admin'          => session()->get('role'),
                ];

                // Cek duplikat
                if ($this->coveringStockModel->getStockByJenisColorCodeMesin(
                    $item['jenis'],
                    $item['color'],
                    $item['code'],
                    $item['jenis_mesin'],
                    $item['dr'],
                    $item['jenis_cover'],
                    $item['jenis_benang']
                )) {
                    $failures[$rowNum] = 'Duplikat data di database';
                    $failures[$rowNum] .= ' (Jenis: ' . $item['jenis'] . ', Color: ' . $item['color'] . ', Code: ' . $item['code'] . ')';
                    continue;
                }

                // Insert
                $this->coveringStockModel->insert($item);
                $successCount++;
            } catch (\Exception $e) {
                $failures[$rowNum] = 'Error: ' . $e->getMessage();
            }
        }

        return [$successCount, $failures];
    }
}
