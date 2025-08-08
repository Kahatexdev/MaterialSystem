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
        log_message('debug', "Query Parameters - Area: {$area}, Jenis: {$jenis}, Tanggal: {$filterDate}");

        $query = $this->db->table('pemesanan p')
            ->select("p.id_pemesanan, p.tgl_pakai, m.area, m.item_type")
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('p.admin', $area)
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
            ->where('pemesanan.status_kirim!=', 'YA')
            ->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan')
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
            ->where('pemesanan.po_tambahan', $data['po_tambahan'])
            ->groupBy('pemesanan.id_pemesanan')
            ->orderBy('pemesanan.id_pemesanan');
        return $data->get()->getResultArray();
    }
    public function kirimPemesanan($id)
    {
        // Langkah 1: Ambil semua data yang relevan dengan JOIN
        $data = $this->db->table('pemesanan')
            ->select('pemesanan.id_pemesanan, pemesanan.jl_mc, pemesanan.ttl_berat_cones, pemesanan.ttl_qty_cones, pemesanan.sisa_kgs_mc, pemesanan.sisa_cones_mc')
            ->join('material', 'pemesanan.id_material = material.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $id['no_model'])
            ->where('material.item_type', $id['item_type'])
            ->where('material.kode_warna', $id['kode_warna'])
            ->where('material.color', $id['color'])
            ->where('pemesanan.tgl_pakai', $id['tgl_pakai'])
            ->where('pemesanan.po_tambahan', $id['po_tambahan'])
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

        // Langkah 3: Insert ke tabel baru
        $insert = $this->db->table('total_pemesanan')->insert($totalData);

        if (!$insert) {
            return [
                'status'  => 'error',
                'message' => 'Gagal menyimpan data total',
            ];
        }
        // Ambil ID total pemesanan yang baru saja diinsert
        $idTotalPemesanan = $this->db->insertID();

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
        $this->select('pemesanan.*, master_order.foll_up, master_order.no_model, master_order.no_order, material.area, master_order.buyer, master_order.delivery_awal, master_order.delivery_akhir, master_order.unit, material.item_type, material.kode_warna, material.color')
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

        return $this->findAll();
    }
    public function reqAdditionalTime($data)
    {
        $sql = "UPDATE pemesanan 
            JOIN material ON material.id_material = pemesanan.id_material 
            JOIN master_material ON material.item_type = master_material.item_type 
            SET pemesanan.status_kirim = 'request' 
            WHERE pemesanan.admin = ? 
              AND pemesanan.status_kirim = '' 
              AND master_material.jenis = ? 
              AND pemesanan.tgl_pakai = ?";

        $result = $this->db->query($sql, [$data['area'], $data['jenis'], $data['tanggal_pakai']]);

        log_message('debug', "Rows affected: " . $this->db->affectedRows());
        return $result ? $this->db->affectedRows() : false; // Kembalikan jumlah baris yang terpengaruh atau false jika gagal
    }

    public function getFilterPemesananKaret($tanggal_awal, $tanggal_akhir)
    {
        $this->select('pemesanan.*, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, material.item_type, material.color, material.kode_warna, master_order.no_model, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            // ->where('tp.ttl_jl_mc >', 0)
            ->where('pemesanan.status_kirim', 'YA')
            ->where('master_material.jenis', 'KARET');

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
        $this->groupBy('tp.id_total_pemesanan');
        return $this->findAll();
    }

    public function getFilterPemesananSpandex($tanggal_awal, $tanggal_akhir)
    {
        $this->select('pemesanan.*, tp.ttl_jl_mc, tp.ttl_kg, tp.ttl_cns, material.item_type, material.color, material.kode_warna, master_order.no_model, master_material.jenis')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            // ->where('tp.ttl_jl_mc >', 0)
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
        return $this->select('pemesanan.status_kirim, pemesanan.admin, pemesanan.tgl_pakai, master_material.jenis')
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
    public function getTglPemesananByJenis($jenis)
    {
        $query = $this->db->table('pemesanan p')
            ->select("p.tgl_pakai")
            ->join('total_pemesanan tp', 'tp.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = p.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'mm.item_type = m.item_type', 'left')
            ->where('mm.jenis', $jenis)
            ->where('p.status_kirim', 'YA')
            ->groupBy('p.tgl_pakai')
            ->get();
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
            ->findAll();
    }
    public function getreportPemesanan($area, $jenis, $tgl_pakai)
    {
        $subPemesanan = $this->db->table('pemesanan')
            ->select("
                pemesanan.tgl_pesan,
                pemesanan.id_total_pemesanan,
                master_order.no_model,
                material.item_type,
                master_material.jenis,
                material.kode_warna,
                material.color,
                total_pemesanan.ttl_jl_mc AS jl_mc,
                total_pemesanan.ttl_cns AS cns_pesan,
                total_pemesanan.ttl_kg AS qty_pesan
            ")
            ->join('total_pemesanan', 'pemesanan.id_total_pemesanan=total_pemesanan.id_total_pemesanan', 'left')
            ->join('material',       'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order',   'master_order.id_order    = material.id_order',  'left')
            ->where('pemesanan.admin',     $area)
            ->where('tgl_pakai', $tgl_pakai)
            ->where('master_material.jenis', $jenis)
            ->where('tgl_pesan IS NOT NULL', null, false)
            ->groupBy('pemesanan.tgl_pesan, pemesanan.id_total_pemesanan, master_order.no_model, material.item_type, material.kode_warna, material.color')
            ->getCompiledSelect();

        $subPengeluaran = $this->db->table('pengeluaran')
            ->select("
                id_total_pemesanan,
                SUM(kgs_out) AS kgs_out,
                SUM(cns_out) AS cns_out,
                SUM(krg_out) AS krg_out,
                lot_out
            ")
            ->groupBy('id_total_pemesanan')
            ->getCompiledSelect();

        $query = $this->db->table("({$subPemesanan}) AS p")
            // join keterangan/lot/status dari tabel pemesanan (ambil 1 saja)
            ->join(
                'pemesanan pem',
                'pem.id_total_pemesanan = p.id_total_pemesanan AND pem.tgl_pesan = p.tgl_pesan',
                'left'
            )
            // join sub‐query pengeluaran
            ->join(
                "({$subPengeluaran}) AS x",
                'x.id_total_pemesanan = p.id_total_pemesanan',
                'left'
            )
            ->select("
                p.tgl_pesan,
                p.no_model,
                p.item_type,
                p.jenis,
                p.kode_warna,
                p.color,
                p.jl_mc,
                p.cns_pesan,
                p.qty_pesan,
                MAX(pem.lot) AS lot_pesan,
                MAX(pem.keterangan) AS ket_area,
                MAX(pem.status_kirim) AS status_kirim,
                MAX(pem.additional_time) AS additional_time,
                COALESCE(x.kgs_out, 0) AS kgs_out,
                COALESCE(x.cns_out, 0) AS cns_out,
                COALESCE(x.krg_out, 0) AS krg_out,
                x.lot_out
            ")
            ->groupBy('p.tgl_pesan, p.no_model, p.item_type, p.jenis, p.kode_warna, p.color')
            ->orderBy('p.tgl_pesan', 'DESC')
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

    public function getDataPemesananArea($tglPakai, $noModel = null)
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
            ->where('pemesanan.status_kirim', 'YA')
            ->where('pemesanan.tgl_pakai', $tglPakai);
        if (!empty($noModel)) {
            $builder->where('master_order.no_model', $noModel);
        }

        $builder->groupBy('master_order.no_model, material.item_type, material.kode_warna, material.color, pemesanan.tgl_pakai, pemesanan.po_tambahan')
            ->orderBy('pemesanan.tgl_pakai', 'DESC')
            ->orderBy('master_order.no_model, material.item_type, material.kode_warna, material.color', 'ASC');
        return $builder->get()->getResultArray();
    }
    public function getPemesananByAreaModel($area, $model)
    {
        $this->select('master_order.no_model, material.item_type, material.kode_warna, material.color, MAX(material.loss) AS max_loss,pemesanan.tgl_pakai, total_pemesanan.id_total_pemesanan, total_pemesanan.ttl_jl_mc, total_pemesanan.ttl_kg, pemesanan.po_tambahan, IFNULL(p.kgs_out, 0) AS kgs_out, p.lot_out')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('(SELECT id_total_pemesanan, SUM(kgs_out) AS kgs_out, GROUP_CONCAT(DISTINCT lot_out) AS lot_out FROM pengeluaran WHERE status="Pengiriman Area" GROUP BY id_total_pemesanan) p', 'p.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('pemesanan.status_kirim', 'YA')
            ->where('pemesanan.admin', $area)
            ->where('master_order.no_model', $model)
            ->groupBy('total_pemesanan.id_total_pemesanan')
            ->orderBy('material.item_type, material.kode_warna, pemesanan.tgl_pakai', 'ASC');

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
