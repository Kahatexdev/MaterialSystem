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
        'id_pengeluaran',
        'id_retur',
        'status_kirim',
        'admin',
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
            ->select("p.id_pemesanan, p.tgl_pakai, m.area, mo.no_model, m.item_type, m.kode_warna, m.color, SUM(p.jl_mc) AS jl_mc, (SUM(COALESCE(p.ttl_berat_cones, 0)) - SUM(COALESCE(p.sisa_kgs_mc, 0))) AS kgs_pesan, (SUM(COALESCE(p.ttl_qty_cones, 0)) - SUM(COALESCE(p.sisa_cones_mc, 0))) AS cns_pesan, CASE WHEN p.po_tambahan = '1' THEN 'YA' ELSE '' END AS po_tambahan")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('m.area', $area)
            ->where('mm.jenis', $jenis)
            ->groupBy('p.tgl_pakai')
            ->groupBy('m.area')
            ->groupBy('m.item_type')
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
        log_message('debug', "Query Parameters - Area: {$area}, Jenis: {$jenis}, Tanggal: {$filterDate}");

        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan, p.tgl_pakai, m.area, m.item_type")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('m.area', $area)
            ->where('mm.jenis', $jenis)
            ->where('p.tgl_pakai', $filterDate)
            ->groupBy('p.tgl_pakai, m.area, m.item_type')
            ->get();

        if (!$query) {
            log_message('error', 'SQL Error: ' . json_encode($this->db->error()));
            return [];
        }

        $result = $query->getResultArray();
        log_message('debug', 'Query Result: ' . json_encode($result));

        return $result;
    }


    public function getListPemesananByArea($area)
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
                SUM(material.kgs) AS kg_keb,
                SUM(pemesanan.jl_mc) AS jl_mc,
                SUM(pemesanan.ttl_qty_cones) AS cns_pesan,
                SUM(pemesanan.ttl_berat_cones) AS qty_pesan,
                AVG(pemesanan.sisa_kgs_mc) AS qty_sisa,
                AVG(pemesanan.sisa_cones_mc) AS cns_sisa,
                pemesanan.lot,
                pemesanan.keterangan,
                (SUM(material.kgs) - SUM(DISTINCT COALESCE(pengeluaran.id_pengeluaran, 0) * COALESCE(pengeluaran.kgs_out, 0) / NULLIF(COALESCE(pengeluaran.id_pengeluaran, 1), 0))) AS sisa_jatah
            ")
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('pengeluaran', 'pengeluaran.id_pengeluaran = pemesanan.id_pengeluaran', 'left')
            ->where('pemesanan.admin', $area)
            ->where('pemesanan.status_kirim', '')
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai')
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC');
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
                material.qty_cns,
                material.qty_berat_cns,
                pemesanan.*
                ')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.admin', $data['area'])
            ->where('pemesanan.tgl_pakai', $data['tgl_pakai'])
            ->where('master_order.no_model', $data['no_model'])
            ->where('material.item_type', $data['item_type'])
            ->where('material.kode_warna', $data['kode_warna'])
            ->where('material.color', $data['color'])
            ->groupBy('pemesanan.id_pemesanan')
            ->orderBy('pemesanan.id_pemesanan');
        return $data->get()->getResultArray();
    }
    public function kirimPemesanan($data)
    {
        // Langkah 1: Ambil semua data yang relevan dengan JOIN
        $data = $this->db->table('pemesanan')
            ->select('pemesanan.id_pemesanan')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $data['no_model'])
            ->where('material.item_type', $data['item_type'])
            ->where('material.kode_warna', $data['kode_warna'])
            ->where('material.color', $data['color'])
            ->where('pemesanan.tgl_pakai', $data['tgl_pakai'])
            ->get()
            ->getResultArray(); // Ambil semua baris sebagai array

        if (empty($data)) {
            return [
                'status'  => 'error',
                'message' => 'Data tidak ditemukan untuk parameter yang diberikan',
            ];
        }

        // Ubah hasil menjadi array ID saja
        $idList = array_column($data, 'id_pemesanan');

        $success = 0;
        $failure = 0;

        foreach ($data as $row) {
            $update = $this->db->table('pemesanan')
                ->where('id_pemesanan', $row['id_pemesanan'])
                ->update([
                    'tgl_pesan' => date('Y-m-d H:i:s'),
                    'status_kirim' => 'YA',
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
                'message' => "$success baris berhasil diperbarui, $failure gagal",
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
            pemesanan.id_pengeluaran,
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
        $this->select('pemesanan.*, master_order.foll_up, master_order.no_model, master_order.no_order, material.area, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, material.item_type, material.kode_warna, material.color')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', '');

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

        return $this->findAll();
    }
}
