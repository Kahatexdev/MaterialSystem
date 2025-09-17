<?php

namespace App\Models;

use CodeIgniter\Model;

class PemesananModel extends Model
{
    protected $table            = 'pemesanan';
    protected $primaryKey       = 'id_pemesanan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pemesanan',
        'id_material',
        'tgl_list',
        'tgl_pesan',
        'tgl_pakai',
        'jl_mc',
        'ttl_qty_cones',
        'ttl_berat_cones',
        'sisa_kgs_mc',
        'sisa_cones_mc',
        'lot',
        'keterangan',
        'po_tambahan',
        'id_total_pemesanan',
        'id_retur',
        'status_kirim',
        'admin',
        'additional_time',
        'alasan_tambahan_waktu',
        'keterangan_gbn',
        'hak_akses',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getDataPemesanan($area, $jenis, $tgl_pakai)
    {
        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan, p.tgl_pakai, m.area, mo.no_model, m.item_type, m.kode_warna, m.color, SUM(p.jl_mc) AS jl_mc, (SUM(COALESCE(p.ttl_berat_cones, 0)) - SUM(COALESCE(p.sisa_kgs_mc, 0))) AS kgs_pesan, (SUM(COALESCE(p.ttl_qty_cones, 0)) - SUM(COALESCE(p.sisa_cones_mc, 0))) AS cns_pesan, CASE WHEN p.po_tambahan = '1' THEN 'YA' ELSE '' END AS po_tambahan")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('m.area', $area)
            ->where('mm.jenis', $jenis)
            ->where('p.tgl_pakai', $tgl_pakai)
            ->groupBy('p.tgl_pakai')
            ->groupBy('m.area')
            ->groupBy('mo.no_model')
            ->groupBy('m.item_type')
            ->groupBy('m.kode_warna')
            ->groupBy('p.po_tambahan')
            ->get();
        if (!$query) {
            // Cek error pada query
            print_r($this->db->error());
            return false;
        }

        return $query->getResultArray();
    }

    public function getDataPemesananperTgl($area, $jenis)
    {
        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan,mm.jenis, p.tgl_pakai, p.admin AS area, mo.no_model, m.item_type, m.kode_warna, m.color,  tp.ttl_jl_mc, tp.ttl_kg , tp.ttl_cns, CASE WHEN p.po_tambahan = '1' THEN 'YA' ELSE '' END AS po_tambahan")
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('p.admin', $area)
            ->where('mm.jenis', $jenis)
            ->where('p.status_kirim', 'YA')
            ->groupBy('p.tgl_pakai')
            ->orderBy('p.tgl_pakai', 'DESC')
            ->get();
        if (!$query) {
            // Cek error pada query
            print_r($this->db->error());
            return false;
        }

        return $query->getResultArray();
    }

    public function getDataPemesananfiltered($area, $jenis, $filterDate)
    {
        // log_message('debug', "Query Parameters - Area: {$area}, Jenis: {$jenis}, Tanggal: {$filterDate}");

        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan, p.tgl_pakai, p.admin AS area, m.item_type")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('p.admin', $area)
            ->where('mm.jenis', $jenis)
            ->where('p.tgl_pakai', $filterDate)
            ->groupBy('p.tgl_pakai')
            ->get();

        if (!$query) {
            // log_message('error', 'SQL Error: ' . json_encode($this->db->error()));
            return [];
        }

        $result = $query->getResultArray();
        // log_message('debug', 'Query Result: ' . json_encode($result));

        return $result;
    }


    // Model
    public function getListPemesananByArea(string $area, ?string $pdk = null)
    {
        $builder = $this->db->table('pemesanan')
            ->select("
            pemesanan.admin,
            pemesanan.tgl_pakai,
            master_order.no_model,
            material.item_type,
            master_material.jenis,
            material.kode_warna,
            material.color,
            SUM(material.kgs) AS kgs,
            SUM(pemesanan.jl_mc) AS jl_mc,
            SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
            SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
            SUM(pemesanan.sisa_kgs_mc) AS qty_sisa,
            SUM(pemesanan.sisa_cones_mc) AS cns_sisa,
            pemesanan.lot,
            pemesanan.keterangan,
            pemesanan.status_kirim,
            pemesanan.additional_time,
            pemesanan.po_tambahan
        ")
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.admin', $area)
            // tidak termasuk yang sudah terkirim
            ->where('pemesanan.status_kirim !=', 'YA');

        // Filter no_model hanya jika $pdk TIDAK kosong
        if (!empty($pdk)) {
            $builder->where('master_order.no_model', $pdk);
        }

        $builder->groupBy([
            'master_order.no_model',
            'material.item_type',
            'material.kode_warna',
            'material.color',
            'pemesanan.tgl_pakai',
            'pemesanan.po_tambahan',
        ]);

        $builder->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->orderBy('master_order.no_model', 'ASC')
            ->orderBy('material.item_type', 'ASC')
            ->orderBy('material.kode_warna', 'ASC')
            ->orderBy('material.color', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getListReportPemesananByArea($area, $tgl_pakai)
    {
        $query = $this->db->table('pemesanan')
            ->select("
                pemesanan.admin,
                pemesanan.tgl_pakai,
                master_order.no_model,
                material.item_type,
                master_material.jenis,
                material.kode_warna,
                material.color,
                SUM(material.kgs) AS kgs,
                SUM(pemesanan.jl_mc) AS jl_mc,
                SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
                SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
                SUM(pemesanan.sisa_kgs_mc) AS qty_sisa,
                SUM(pemesanan.sisa_cones_mc) AS cns_sisa,
                pemesanan.lot,
                pemesanan.keterangan,
                pemesanan.status_kirim,
                pemesanan.additional_time,
                pemesanan.po_tambahan
            ")
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.admin', $area)
            ->where('pemesanan.status_kirim', 'YA');
        // filter tgl_pakai kalau dikirim
        if (!empty($tgl_pakai)) {
            $query->where('pemesanan.tgl_pakai', $tgl_pakai);
        }

        $query->groupBy([
            'master_order.no_model',
            'material.item_type',
            'material.kode_warna',
            'material.color',
            'pemesanan.tgl_pakai',
            'pemesanan.po_tambahan'
        ])
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->orderBy('master_order.no_model', 'ASC')
            ->orderBy('material.item_type', 'ASC')
            ->orderBy('material.kode_warna', 'ASC')
            ->orderBy('material.color', 'ASC');
        // ->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan')
        // ->orderBy('pemesanan.tgl_pakai', 'DESC')
        // ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC');
        return $query->get()->getResultArray();
    }

    public function getJenisPemesananCovering($jenis)
    {
        return $this->select('pemesanan.tgl_pakai, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->groupBy('pemesanan.tgl_pakai')
            ->findAll();
    }

    public function getListPemesananCovering($jenis, $tgl_pakai)
    {
        return $this->select('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_berat_cones) AS total_pesan, SUM(pemesanan.ttl_qty_cones) AS total_cones, pemesanan.admin')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->groupBy('pemesanan.tgl_pakai, master_material.jenis, material.item_type, material.color, material.kode_warna, master_order.no_model, pemesanan.admin')
            ->findAll();
    }

    public function totalPemesananPerHari()
    {
        return $this->select('COUNT(pemesanan.tgl_pesan) as pemesanan_per_hari')
            ->where('DATE(pemesanan.tgl_pesan)', date('Y-m-d'))
            ->first();
    }
    public function getListPemesananByUpdate($data)
    {
        $data = $this->db->table('pemesanan')
            ->select('
                master_order.no_model,
                material.id_material,
                material.item_type,
                material.kode_warna,
                material.color,
                material.style_size,
                IFNULL(kebutuhan_cones.qty_cns, 0) AS qty_cns,
                IFNULL(kebutuhan_cones.qty_berat_cns, 0) AS qty_berat_cns,
                pemesanan.*
                ')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('kebutuhan_cones', 'material.id_material = kebutuhan_cones.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.admin', $data['area'])
            ->where('pemesanan.tgl_pakai', $data['tgl_pakai'])
            ->where('master_order.no_model', $data['no_model'])
            ->where('material.item_type', $data['item_type'])
            ->where('material.kode_warna', $data['kode_warna'])
            ->where('material.color', $data['color'])
            ->where('pemesanan.po_tambahan', $data['po_tambahan'])
            ->where('pemesanan.status_kirim!=', 'YA')
            ->groupBy('pemesanan.id_pemesanan')
            ->orderBy('pemesanan.id_pemesanan');
        return $data->get()->getResultArray();
    }
    public function kirimPemesanan($id)
    {
        // Langkah 1: Ambil semua data yang relevan dengan JOIN
        $data = $this->db->table('pemesanan')
            ->select('master_order.no_model, material.item_type, material.kode_Warna, material.color, pemesanan.id_pemesanan, pemesanan.jl_mc, pemesanan.ttl_berat_cones, pemesanan.ttl_qty_cones, pemesanan.sisa_kgs_mc, pemesanan.sisa_cones_mc')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $id['no_model'])
            ->where('material.item_type', $id['item_type'])
            ->where('material.kode_warna', $id['kode_warna'])
            ->where('material.color', $id['color'])
            ->where('pemesanan.tgl_pakai', $id['tgl_pakai'])
            ->where('pemesanan.po_tambahan', $id['po_tambahan'])
            ->where('pemesanan.admin', $id['area'])
            ->where('pemesanan.status_kirim !=', 'YA')
            ->get()
            ->getResultArray(); // Ambil semua baris sebagai array

        if (empty($data)) {
            return [
                'status'  => 'error',
                'message' => 'Data tidak ditemukan untuk parameter yang diberikan',
            ];
        } else {
            log_message('info', 'Data ditemukan: ' . json_encode($data));
        }

        $totalBeratCones = array_sum(array_column($data, 'ttl_berat_cones'));
        $totalQtyCones = array_sum(array_column($data, 'ttl_qty_cones'));
        $sisaKgsMc = array_sum(array_column($data, 'sisa_kgs_mc'));
        $sisaConesMc = array_sum(array_column($data, 'sisa_cones_mc'));

        $totalData = [
            'ttl_jl_mc' => array_sum(array_column($data, 'jl_mc')),
            'ttl_kg'    => $totalBeratCones - $sisaKgsMc,
            'ttl_cns'   => $totalQtyCones - $sisaConesMc,
        ];

        // Langkah 4: Cek Apakah sebelumnya sudah ada pemesann yg di kirim
        $existing = $this->db->table('pemesanan')
            ->select('total_pemesanan.id_total_pemesanan')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->where('master_order.no_model', $id['no_model'])
            ->where('material.item_type', $id['item_type'])
            ->where('material.kode_warna', $id['kode_warna'])
            ->where('material.color', $id['color'])
            ->where('pemesanan.tgl_pakai', $id['tgl_pakai'])
            ->where('pemesanan.po_tambahan', $id['po_tambahan'])
            ->where('pemesanan.admin', $id['area'])
            ->limit(1)
            ->get()
            ->getRowArray();

        // Kalau sudah ada, ambil ID-nya
        if ($existing && !empty($existing['id_total_pemesanan'])) {
            $idTotalPemesanan = $existing['id_total_pemesanan'];

            // Update nilai total dengan penambahan
            $this->db->table('total_pemesanan')
                ->set('ttl_jl_mc', 'ttl_jl_mc + ' . (int) $totalData['ttl_jl_mc'], false)
                ->set('ttl_kg', 'ttl_kg + ' . (float) $totalData['ttl_kg'], false)
                ->set('ttl_cns', 'ttl_cns + ' . (float) $totalData['ttl_cns'], false)
                ->where('id_total_pemesanan', $idTotalPemesanan)
                ->update();
        } else {
            // Insert baru kalau belum ada
            $this->db->table('total_pemesanan')->insert($totalData);
            $idTotalPemesanan = $this->db->insertID();
        }

        // Langkah 4: Update data di tabel pemesanan
        $success = 0;
        $failure = 0;

        foreach ($data as $row) {
            $update = $this->db->table('pemesanan')
                ->where('id_pemesanan', $row['id_pemesanan'])
                ->update([
                    'tgl_pesan'       => date('Y-m-d H:i:s'),
                    'status_kirim'    => 'YA',
                    'id_total_pemesanan' => $idTotalPemesanan, // Update ID total pemesanan
                ]);

            if ($this->db->affectedRows() > 0) {
                $success++;
            } else {
                $failure++;
            }
        }

        // Jika ada pembaruan yang berhasil
        if ($success > 0) {
            return [
                'status' => 'success',
                'message' => "$success pemesanan berhasil dikirim, $failure gagal",
                'success_count' => $success,
                'failure_count' => $failure,
            ];
        }

        // Jika semua pembaruan gagal
        return [
            'status'  => 'error',
            'message' => 'Tidak ada data yang berhasil diperbarui',
            'success_count' => $success,
            'failure_count' => $failure,
        ];
    }

    public function getDataPemesananbyId($id)
    {
        return $this->select('
            pemesanan.id_pemesanan,
            pemesanan.tgl_pakai,
            pemesanan.jl_mc,
            pemesanan.ttl_qty_cones,
            pemesanan.ttl_berat_cones,
            pemesanan.sisa_kgs_mc,
            pemesanan.sisa_cones_mc,
            pemesanan.lot,
            pemesanan.keterangan,
            pemesanan.po_tambahan,
            pemesanan.id_total_pemesanan,
            pemesanan.id_retur,
            pemesanan.status_kirim,
            pemesanan.admin,
            material.id_material,
            material.item_type,
            material.kode_warna,
            material.color,
            material.style_size,
            material.qty_cns,
            material.qty_berat_cns,
            master_order.no_model
        ')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.id_pemesanan', $id)
            ->first();
    }
    public function deleteListPemesananOtomatis($data)
    {
        // Pastikan parameter data memiliki 'tgl_pakai' dan 'admin'
        if (!isset($data['tgl_pakai']) || !isset($data['admin'])) {
            return false; // Tidak dapat melanjutkan jika parameter tidak lengkap
        }

        // Jalankan query untuk menghapus data
        $this->db
            ->table('pemesanan') // Ganti dengan nama tabel Anda
            ->where('tgl_pakai <', $data['tgl_pakai'])
            ->where('admin', $data['admin'])
            ->where('status_kirim', '')
            ->delete();

        // Kembalikan jumlah baris yang terhapus
        return $this->db->affectedRows(); // Mengembalikan jumlah baris yang dihapus
    }

    public function getFilterPemesananArea($key, $tanggal_awal, $tanggal_akhir)
    {
        $this->select('pemesanan.tgl_pakai, pemesanan.tgl_pesan, pemesanan.tgl_list, SUM(pemesanan.jl_mc) AS jl_mc, SUM(pemesanan.ttl_qty_cones) AS ttl_qty_cones, SUM(pemesanan.ttl_berat_cones) AS ttl_berat_cones, SUM(pemesanan.sisa_cones_mc) AS sisa_cones_mc, SUM(pemesanan.sisa_kgs_mc) AS sisa_kgs_mc, pemesanan.admin AS area, pemesanan.admin,  GROUP_CONCAT(DISTINCT pemesanan.keterangan SEPARATOR ", ") AS keterangan, GROUP_CONCAT(DISTINCT pemesanan.lot SEPARATOR ", ") AS lot, pemesanan.po_tambahan, master_order.foll_up, master_order.no_model, master_order.no_order, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, material.item_type, material.kode_warna, material.color')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', 'YA');

        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $this->groupStart()
                ->like('pemesanan.admin', $key)
                ->groupEnd();
        }

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal)
                    ->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }

        // $this->groupBy('pemesanan.id_total_pemesanan');
        $this->groupBy([
            'pemesanan.tgl_pakai',
            'material.item_type',
            'material.kode_warna',
            'material.color',
            'master_order.no_model',
            'pemesanan.admin',
        ]);

        return $this->findAll();
    }
    public function reqAdditionalTime($data)
    {

        // 1. Cari dulu apakah ada data
        $checkSql = "SELECT pemesanan.id_pemesanan
                FROM pemesanan
                JOIN material ON material.id_material = pemesanan.id_material
                JOIN master_material ON material.item_type = master_material.item_type
                WHERE pemesanan.admin = ?
                AND pemesanan.status_kirim != 'YA'
                AND master_material.jenis = ?
                AND pemesanan.tgl_pakai = ?";
        $check = $this->db->query($checkSql, [$data['area'], $data['jenis'], $data['tanggal_pakai']])->getResultArray();

        if (empty($check)) {
            // Tidak ada data sama sekali
            return false;
        }

        // 2. Kalau ada, baru update
        $sql = "UPDATE pemesanan 
            JOIN material ON material.id_material = pemesanan.id_material 
            JOIN master_material ON material.item_type = master_material.item_type 
            SET pemesanan.status_kirim = 'request',
            pemesanan.alasan_tambahan_waktu = ?
            WHERE pemesanan.admin = ? 
              AND pemesanan.status_kirim != 'YA' 
              AND master_material.jenis = ? 
              AND pemesanan.tgl_pakai = ?";

        $this->db->query($sql, [$data['alasan_tambahan_waktu'], $data['area'], $data['jenis'], $data['tanggal_pakai']]);

        // 3. Hitung affectedRows
        $affected = $this->db->affectedRows();

        // Kalau 0 tapi data ada → tetap return 1 (anggap sukses)
        return $affected > 0 ? $affected : 1;

        // $sql = "UPDATE pemesanan 
        //     JOIN material ON material.id_material = pemesanan.id_material 
        //     JOIN master_material ON material.item_type = master_material.item_type 
        //     SET pemesanan.status_kirim = 'request',
        //         pemesanan.alasan_tambahan_waktu = ?
        //     WHERE pemesanan.admin = ? 
        //       AND pemesanan.status_kirim != 'YA' 
        //       AND master_material.jenis = ? 
        //       AND pemesanan.tgl_pakai = ?";

        // $result = $this->db->query($sql, [
        //     $data['alasan_tambahan_waktu'],
        //     $data['area'],
        //     $data['jenis'],
        //     $data['tanggal_pakai']
        // ]);

        // log_message('debug', "Rows affected: " . $this->db->affectedRows());

        // return $result ? $this->db->affectedRows() : false;
    }

    // Model (mis. PemesananModel.php)
    public function getFilterPemesananKaret($tanggal_awal, $tanggal_akhir)
    {
        $this->select("
        pemesanan.tgl_pakai,
        material.item_type,
        material.color,
        material.kode_warna,
        pemesanan.admin,
        master_order.no_model,
        tp.ttl_jl_mc,
        tp.ttl_kg,
        tp.ttl_cns,
        pemesanan.po_tambahan,
        pemesanan.keterangan
    ")
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', 'YA')
            ->where('master_material.jenis', 'KARET');

        // Filter tanggal pakai
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal)
                    ->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal);
            } else { // hanya $tanggal_akhir
                $this->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }

        // Penting: group by kunci penggabungan + admin (area)
        $this->groupBy([
            'pemesanan.tgl_pakai',
            'material.item_type',
            'material.color',
            'material.kode_warna',
            'master_order.no_model',
            'pemesanan.admin',
        ]);

        return $this->findAll();
    }


    public function getFilterPemesananSpandex($tanggal_awal, $tanggal_akhir)
    {
        $this->select("
        pemesanan.tgl_pakai,
        material.item_type,
        material.color,
        material.kode_warna,
        pemesanan.admin,
        master_order.no_model,
        tp.ttl_jl_mc,
        tp.ttl_kg,
        tp.ttl_cns,
        pemesanan.po_tambahan,
        pemesanan.keterangan
    ")
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', 'YA')
            ->where('master_material.jenis', 'SPANDEX');

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal)
                    ->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('pemesanan.tgl_pakai >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('pemesanan.tgl_pakai <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }
        // $this->groupBy('tp.id_total_pemesanan');
        //ganti jadi:
        // Penting: group by kunci penggabungan + admin (area)
        $this->groupBy([
            'pemesanan.tgl_pakai',
            'material.item_type',
            'material.color',
            'material.kode_warna',
            'master_order.no_model',
            'pemesanan.admin',
        ]);
        return $this->findAll();
    }
    public function countStatusRequest()
    {
        // Buat subquery
        $subquery = $this->db->table('pemesanan')
            ->select('pemesanan.status_kirim, pemesanan.admin, pemesanan.tgl_pakai, master_material.jenis')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_material', 'master_material.item_type = material.item_type')
            ->where('pemesanan.status_kirim', 'request')
            ->groupBy(['pemesanan.admin', 'pemesanan.tgl_pakai', 'master_material.jenis'])
            ->getCompiledSelect();

        // Gunakan subquery dalam query utama
        $builder = $this->db->table("($subquery) AS grouped_data");
        $result = $builder->select('COUNT(*) AS total')->get()->getRow();
        // / Pastikan hasil diubah menjadi integer
        $total = $result ? intval($result->total) : 0;
        // dd($total);
        return $total;
    }
    public function getStatusRequest()
    {
        return $this->select('pemesanan.status_kirim, pemesanan.alasan_tambahan_waktu,pemesanan.admin, pemesanan.tgl_pakai, master_material.jenis')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_material', 'master_material.item_type = material.item_type')
            ->like('pemesanan.status_kirim', 'request')
            ->groupBy('pemesanan.admin, pemesanan.tgl_pakai, master_material.jenis')
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->findAll();
    }
    public function additionalTimeAccept($data)
    {
        $query = "
            UPDATE pemesanan
            JOIN material ON material.id_material = pemesanan.id_material
            JOIN master_material ON master_material.item_type = material.item_type
            SET pemesanan.status_kirim = 'request accept', pemesanan.additional_time = ?, pemesanan.hak_akses = ?
            WHERE pemesanan.admin = ?
            AND pemesanan.status_kirim = 'request' 
            AND pemesanan.tgl_pakai = ?
            AND master_material.jenis = ?
        ";

        $this->db->query($query, [$data['max_time'], $data['username'], $data['area'], $data['tgl_pakai'], $data['jenis']]);
        return $this->db->affectedRows() > 0;
    }
    public function additionalTimeReject($area, $tgl_pakai, $jenis)
    {

        $query = "
            UPDATE pemesanan
            JOIN material ON material.id_material = pemesanan.id_material
            JOIN master_material ON master_material.item_type = material.item_type
            SET pemesanan.status_kirim = 'request reject'
            WHERE pemesanan.admin = ?
            AND pemesanan.tgl_pakai = ?
            AND master_material.jenis = ?
            AND pemesanan.status_kirim = 'request' 

        ";

        $this->db->query($query, [$area, $tgl_pakai, $jenis]);
        return $this->db->affectedRows() > 0;
    }

    public function pemesananBelumDikirim()
    {
        $date = date('Y-m-d');
        return $this->select('COUNT(id_pemesanan)')
            ->where('tgl_pakai', $date)
            ->where('status_kirim', '')
            ->findAll();
    }

    public function getPemesananSpandex($id)
    {
        return $this->where('id_total_pemesanan', $id)->first();
    }
    public function getTglPemesananByJenis($jenis, $tglPakai = null)
    {
        $builder = $this->db->table('pemesanan p')
            ->select("p.tgl_pakai")
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('mm.jenis', $jenis)
            ->where('p.status_kirim', 'YA');
        if (!empty($tglPakai)) {
            $builder->where('p.tgl_pakai', $tglPakai);
        }
        $builder->groupBy('p.tgl_pakai')
            ->orderBy('p.tgl_pakai', 'DESC');
        $query = $builder->get();
        if (!$query) {
            // Cek error pada query
            print_r($this->db->error());
            return false;
        }

        return $query->getResultArray();
    }
    public function getTglPakai($area, $tgl_awal, $tgl_akhir)
    {
        return $this->select('tgl_pakai')
            ->distinct()
            ->where('admin', $area)
            ->where('tgl_pakai >=', $tgl_awal)
            ->where('tgl_pakai <=', $tgl_akhir)
            ->where('status_kirim', 'YA')
            ->findAll();
    }
    public function getreportPemesanan($area, $jenis, $tgl_pakai)
    {
        // Subquery Pemesanan
        $subPemesanan = $this->db->table('pemesanan')
            ->select("
            pemesanan.tgl_pesan,
            pemesanan.tgl_pakai,
            pemesanan.id_total_pemesanan,
            pemesanan.po_tambahan,
            master_order.no_model,
            material.item_type,
            master_material.jenis,
            material.kode_warna,
            material.color,
            total_pemesanan.ttl_jl_mc AS jl_mc,
            total_pemesanan.ttl_cns AS cns_pesan,
            total_pemesanan.ttl_kg AS qty_pesan,
            GROUP_CONCAT(DISTINCT pemesanan.lot) AS lot_pesan,
            GROUP_CONCAT(DISTINCT pemesanan.keterangan) AS ket_area,
            GROUP_CONCAT(DISTINCT pemesanan.keterangan_gbn) AS ket_gbn,
            MAX(pemesanan.status_kirim) AS status_kirim,
            MAX(pemesanan.additional_time) AS additional_time
        ")
            ->join('total_pemesanan', 'pemesanan.id_total_pemesanan=total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.admin', $area)
            ->where('pemesanan.tgl_pakai', $tgl_pakai)
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.status_kirim', 'YA')
            ->groupBy('pemesanan.tgl_pakai, pemesanan.id_total_pemesanan, master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.po_tambahan')
            ->getCompiledSelect();

        // Subquery Pengeluaran
        $subPengeluaran = $this->db->table('pengeluaran')
            ->select("
            id_total_pemesanan,
            SUM(kgs_out) AS kgs_out,
            SUM(cns_out) AS cns_out,
            SUM(krg_out) AS krg_out,
            GROUP_CONCAT(DISTINCT lot_out) AS lot_out,
            status
        ")
            ->where('status', 'Pengiriman Area') // <-- dipindah ke sini
            ->groupBy('id_total_pemesanan')
            ->getCompiledSelect();

        // Main Query
        $query = $this->db->table("({$subPemesanan}) p")
            ->join("({$subPengeluaran}) x", 'x.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->select("
            p.tgl_pesan,
            p.tgl_pakai,
            p.no_model,
            p.item_type,
            p.jenis,
            p.kode_warna,
            p.color,
            p.jl_mc,
            p.cns_pesan,
            p.qty_pesan,
            p.po_tambahan,
            p.lot_pesan,
            p.ket_gbn,
            p.ket_area,
            p.status_kirim,
            p.additional_time,
            COALESCE(x.kgs_out, 0) AS kgs_out,
            COALESCE(x.cns_out, 0) AS cns_out,
            COALESCE(x.krg_out, 0) AS krg_out,
            x.lot_out,
            x.status
        ")
            // ->where('x.status IS NOT NULL')
            ->orderBy('p.no_model, p.item_type, p.kode_warna, p.color', 'ASC');

        return $query->get()->getResultArray();
    }

    public function getDataPemesananCovering($tanggal_pakai, $jenis)
    {
        $this->select('pemesanan.*, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, material.item_type, material.color, material.kode_warna, master_order.no_model, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            // ->where('tp.ttl_jl_mc >', 0)
            ->where('pemesanan.status_kirim', 'YA')
            ->where('pemesanan.tgl_pakai', $tanggal_pakai)
            ->where('master_material.jenis', $jenis)
            ->groupBy('material.item_type, material.kode_warna');

        return $this->findAll();
    }
    public function getDataPemesananCoveringPerArea($tanggal_pakai, $jenis)
    {
        $this->select('pemesanan.*, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, material.item_type, material.color, material.kode_warna, master_order.no_model, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            // ->where('tp.ttl_jl_mc >', 0)
            ->where('pemesanan.status_kirim', 'YA')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tanggal_pakai)
            ->groupBy('material.item_type, material.kode_warna, pemesanan.admin');

        return $this->findAll();
    }
    public function getDataPemesananPerArea($tanggal_pakai, $jenis)
    {
        $this->select('pemesanan.*, TIME(pemesanan.created_at) AS jam_pesan, DATE(pemesanan.created_at) AS tgl_pesan, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, material.item_type, material.color, material.kode_warna, master_order.no_model, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->where('pemesanan.status_kirim', 'YA')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tanggal_pakai)
            ->groupBy('pemesanan.admin, master_order.no_model, material.item_type, material.kode_warna');

        return $this->findAll();
    }

    // public function getDataPemesananArea($tglPakai, $noModel = null, $role)
    // {
    //     $builder = $this->db->table('pemesanan')
    //         ->select("
    //             pemesanan.admin,
    //             pemesanan.tgl_pakai,
    //             master_order.no_model,
    //             material.item_type,
    //             master_material.jenis,
    //             material.kode_warna,
    //             material.color,
    //             SUM(pemesanan.jl_mc) AS jl_mc,
    //             SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
    //             SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
    //             SUM(pemesanan.sisa_kgs_mc) AS qty_sisa,
    //             SUM(pemesanan.sisa_cones_mc) AS cns_sisa,
    //             pemesanan.lot,
    //             pemesanan.keterangan,
    //             pemesanan.status_kirim,
    //             pemesanan.additional_time,
    //             pemesanan.po_tambahan
    //         ")
    //         ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
    //         ->join('material', 'material.id_material = pemesanan.id_material', 'left')
    //         ->join('master_material', 'master_material.item_type = material.item_type', 'left')
    //         ->join('master_order', 'master_order.id_order = material.id_order', 'left')

    //         ->where('pemesanan.tgl_pakai', $tglPakai);
    //     if (!empty($noModel)) {
    //         $builder->where('master_order.no_model', $noModel);
    //     }
    //     if ($role != 'monitoring') {
    //         $builder->where('pemesanan.status_kirim', 'YA');
    //     }

    //     $builder->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan')
    //         ->orderBy('pemesanan.tgl_pakai', 'DESC')
    //         ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC');

    //     return $builder->get()->getResultArray();
    // }

    public function getDataPemesananArea($tglPakai, $noModel = null, $role = 'user', $area = '', $search = '')
    {
        $base = $this->db->table('pemesanan')
            ->select("
            pemesanan.admin,
            pemesanan.tgl_pakai,
            master_order.no_model,
            material.item_type,
            master_material.jenis,
            material.kode_warna,
            material.color,
            SUM(pemesanan.jl_mc) AS jl_mc,
            SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
            SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
            SUM(pemesanan.sisa_kgs_mc) AS qty_sisa,
            SUM(pemesanan.sisa_cones_mc) AS cns_sisa,
            pemesanan.lot,
            pemesanan.keterangan,
            pemesanan.status_kirim,
            pemesanan.additional_time,
            pemesanan.po_tambahan
        ")
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.tgl_pakai', $tglPakai);

        if (!empty($noModel)) {
            $base->where('master_order.no_model', $noModel);
        }
        if (!empty($area)) {
            $base->where('pemesanan.admin', $area);
        }
        if ($role != 'monitoring') {
            $base->where('pemesanan.status_kirim', 'YA');
        }

        // global search (opsional)
        if ($search !== '') {
            $base->groupStart()
                ->like('pemesanan.admin', $search)
                ->orLike('master_order.no_model', $search)
                ->orLike('material.item_type', $search)
                ->orLike('material.kode_warna', $search)
                ->orLike('material.color', $search)
                ->groupEnd();
        }

        // total & filtered (tanpa groupBy)
        $forCount = clone $base;
        $recordsFiltered = $forCount->countAllResults(false); // false agar builder tidak direset

        $base->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan, pemesanan.admin');

        // order
        $base->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC');

        $data = $base->get()->getResultArray();

        // recordsTotal (tanpa search)
        $baseNoSearch = $this->db->table('pemesanan')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.tgl_pakai', $tglPakai);

        if (!empty($noModel)) $baseNoSearch->where('master_order.no_model', $noModel);
        if (!empty($area))    $baseNoSearch->where('pemesanan.admin', $area);
        if ($role != 'monitoring') $baseNoSearch->where('pemesanan.status_kirim', 'YA');

        $baseNoSearch->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan, pemesanan.admin');
        $recordsTotal = $baseNoSearch->get()->getNumRows();

        return [
            'meta' => [
                'total'    => $recordsTotal,
                'filtered' => $recordsFiltered,
            ],
            'data' => $data,
        ];
    }



    public function getPemesananByAreaModel($area, $model)
    {
        $this->select('master_order.no_model, material.item_type, material.kode_warna, material.color, MAX(material.loss) AS max_loss,pemesanan.tgl_pakai, total_pemesanan.id_total_pemesanan, total_pemesanan.ttl_jl_mc, total_pemesanan.ttl_kg,total_pemesanan.ttl_cns, pemesanan.po_tambahan,pemesanan.keterangan_gbn, IFNULL(p.kgs_out, 0) AS kgs_out, IFNULL(p.cns_out,0) AS cns_out, p.lot_out')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('(SELECT id_total_pemesanan, SUM(kgs_out) AS kgs_out,SUM(cns_out) AS cns_out, GROUP_CONCAT(DISTINCT lot_out) AS lot_out FROM pengeluaran WHERE status="Pengiriman Area" GROUP BY id_total_pemesanan) p', 'p.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', 'YA')
            ->where('pemesanan.admin', $area)
            ->where('master_order.no_model', $model)
            ->groupBy('total_pemesanan.id_total_pemesanan')
            ->orderBy('material.item_type, material.kode_warna, pemesanan.tgl_pakai', 'ASC');

        // jangan di hapus
        // $this->select('master_order.no_model, material.item_type, material.kode_warna, material.color,  SUM(material.kgs) AS total_kgs, MAX(material.loss) AS max_loss,pemesanan.tgl_pakai, total_pemesanan.id_total_pemesanan, total_pemesanan.ttl_jl_mc, total_pemesanan.ttl_kg,total_pemesanan.ttl_cns, pemesanan.po_tambahan,pemesanan.keterangan_gbn, IFNULL(p.kgs_out, 0) AS kgs_out, IFNULL(p.cns_out,0) AS cns_out, p.lot_out')
        //     ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
        //     ->join('(SELECT id_total_pemesanan, SUM(kgs_out) AS kgs_out,SUM(cns_out) AS cns_out, GROUP_CONCAT(DISTINCT lot_out) AS lot_out FROM pengeluaran WHERE status="Pengiriman Area" GROUP BY id_total_pemesanan) p', 'p.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
        //     ->join(
        //         '(
        //         SELECT id_order, item_type, kode_warna, color, SUM(kgs) AS total_kgs 
        //         FROM material 
        //         GROUP BY id_order, item_type, kode_warna, color
        //         ) m',
        //         'm.id_order = master_order.id_order 
        //         AND m.item_type = material.item_type 
        //         AND m.kode_warna = material.kode_warna 
        //         AND m.color = material.color',
        //         'left'
        //     )
        //     ->join('master_order', 'master_order.id_order = material.id_order', 'left')
        //     ->where('pemesanan.status_kirim', 'YA')
        //     ->where('pemesanan.admin', $area)
        //     ->where('master_order.no_model', $model)
        //     ->groupBy('total_pemesanan.id_total_pemesanan')
        //     ->orderBy('material.item_type, material.kode_warna, pemesanan.tgl_pakai', 'ASC');

        return $this->findAll();
    }

    public function getJenisPemesananbyIdTtlPesan($idTtlPesan)
    {
        $query = $this->db->table('pemesanan p')
            ->select("mm.jenis")
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('p.status_kirim', 'YA')
            ->where('tp.id_total_pemesanan', $idTtlPesan)
            ->groupBy('p.tgl_pakai, mm.jenis')
            ->get();

        if (!$query) {
            // Cek error pada query
            print_r($this->db->error());
            return false;
        }

        return $query->getResultArray();
    }
}
