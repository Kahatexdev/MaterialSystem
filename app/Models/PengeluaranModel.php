<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id_pengeluaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_pengeluaran',
        'id_out_celup',
        'area_out',
        'tgl_out',
        'kgs_out',
        'cns_out',
        'krg_out',
        'lot_out',
        'nama_cluster',
        'status',
        'id_total_pemesanan',
        'admin',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    public function getDataForOut($id)
    {
        return $this->db->table('pengeluaran')
            ->select('pengeluaran.*, out_celup.lot_kirim, schedule_celup.no_model, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.item_type')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->where('pengeluaran.id_out_celup', $id)
            ->distinct()
            ->get()
            ->getResultArray();
    }

    public function searchPengiriman($noModel)
    {
        return $this->db->table('pengeluaran')
            ->select('pengeluaran.*, SUM(pengeluaran.kgs_out) AS kgs_out, out_celup.lot_kirim, schedule_celup.no_model, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.item_type')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup')
            ->join('bon_celup', 'bon_celup.id_bon = out_celup.id_bon')
            ->join('schedule_celup', 'schedule_celup.id_bon = bon_celup.id_bon')
            ->where('schedule_celup.no_model', $noModel)
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->groupBy('schedule_celup.no_model, schedule_celup.kode_warna, schedule_celup.warna, schedule_celup.item_type')
            ->get()
            ->getResultArray();
    }
    public function getTotalPengiriman($data)
    {
        return $this->select('SUM(pengeluaran.kgs_out) AS kgs_out')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup', 'left')
            ->join('schedule_celup', 'out_celup.id_celup = schedule_celup.id_celup', 'left')
            ->where('pengeluaran.area_out', $data['area'])
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('schedule_celup.no_model', $data['no_model'])
            ->where('schedule_celup.item_type', $data['item_type'])
            ->where('schedule_celup.kode_warna', $data['kode_warna'])
            ->first();
    }
    public function getFilterPengiriman($key, $tanggal_awal, $tanggal_akhir)
    {
        $this->select('DATE(open_po.created_at) AS tgl_po, schedule_celup.no_model, schedule_celup.item_type, schedule_celup.kode_warna, schedule_celup.warna, pengeluaran.tgl_out, pengeluaran.nama_cluster, pengeluaran.area_out, pengeluaran.kgs_out, pengeluaran.cns_out, pengeluaran.krg_out, pengeluaran.lot_out, master_order.foll_up, master_order.no_order, master_order.buyer, master_order.unit, master_order.delivery_awal, master_order.delivery_akhir, total_pemesanan.ttl_kg, total_pemesanan.ttl_cns')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup')
            ->join('schedule_celup', 'schedule_celup.id_celup = out_celup.id_celup')
            ->join('master_order', 'master_order.no_model = schedule_celup.no_model', 'left')
            ->join('open_po', 'open_po.no_model = master_order.no_model AND open_po.kode_warna = schedule_celup.kode_warna AND open_po.item_type = schedule_celup.item_type', 'left')
            ->where('pengeluaran.status', "Pengiriman Area")
            ->groupBy('pengeluaran.id_pengeluaran')
            ->orderBy('pengeluaran.tgl_out', 'DESC');


        // Cek apakah ada input key untuk pencarian
        if (!empty($key)) {
            $this->groupStart()

                ->like('schedule_celup.no_model', $key)
                ->orLike('schedule_celup.item_type', $key)
                ->orLike('schedule_celup.kode_warna', $key)
                ->orLike('schedule_celup.warna', $key)
                ->groupEnd();
        }

        // Filter berdasarkan tanggal
        if (!empty($tanggal_awal) || !empty($tanggal_akhir)) {
            $this->groupStart();
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $this->where('pengeluaran.tgl_out >=', $tanggal_awal)
                    ->where('pengeluaran.tgl_out <=', $tanggal_akhir);
            } elseif (!empty($tanggal_awal)) {
                $this->where('pengeluaran.tgl_out >=', $tanggal_awal);
            } elseif (!empty($tanggal_akhir)) {
                $this->where('pengeluaran.tgl_out <=', $tanggal_akhir);
            }
            $this->groupEnd();
        }


        return $this->findAll();
    }
    public function getDataPemesananExport($jenis, $tglPakai)
    {
        return $this->select("
            pemesanan.tgl_pakai,
            pengeluaran.area_out,
            master_order.no_model,
            master_material.jenis,
            material.item_type,
            material.kode_warna,
            material.color,
            out_celup.no_karung,
            pengeluaran.kgs_out,
            pengeluaran.cns_out,
            pengeluaran.lot_out,
            pengeluaran.nama_cluster,
            cluster.group
        ")
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup', 'left')
            ->join('cluster', 'cluster.nama_cluster=pengeluaran.nama_cluster')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tglPakai)
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->groupBy('pengeluaran.id_pengeluaran')
            ->orderBy('pengeluaran.nama_cluster, pengeluaran.area_out', 'ASC')
            ->get() // Dapatkan objek query
            ->getResultArray(); // Konversi ke array hasil
    }
}
