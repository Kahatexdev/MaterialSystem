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
        'id_psk',
        'id_stock',
        'area_out',
        'tgl_out',
        'kgs_out',
        'cns_out',
        'krg_out',
        'lot_out',
        'nama_cluster',
        'status',
        'keterangan_gbn',
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
    public function getFilterPengiriman($key = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        // -- Subquery 1: pengeluaran --
        $b1 = $this->db->table('pengeluaran')
            ->select([
                'master_order.no_model',
                'material.item_type',
                'material.kode_warna',
                'material.color',
                'pengeluaran.tgl_out',
                'pengeluaran.nama_cluster',
                'pengeluaran.area_out',
                'pengeluaran.kgs_out',
                'pengeluaran.cns_out',
                'pengeluaran.krg_out',
                'pengeluaran.lot_out',
                'pengeluaran.keterangan_gbn',
                'pengeluaran.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
            ])
            ->join('pemesanan_spandex_karet psk',    'psk.id_psk = pengeluaran.id_psk',                                 'left')
            ->join('total_pemesanan',                'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('pemesanan',                      'pemesanan.id_total_pemesanan = psk.id_total_pemesanan', 'left')
            ->join('material',                       'material.id_material = pemesanan.id_material',                    'left')
            ->join('master_material',                'master_material.item_type = material.item_type',                 'left')
            ->join('master_order',                   'master_order.id_order = material.id_order',                       'left')
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('pengeluaran.id_out_celup IS NULL') // Ensure we only get valid out_celup
            ->where('pengeluaran.id_psk IS NOT NULL') // Ensure we only get valid schedule_celup
            ->groupBy('pengeluaran.id_pengeluaran');

        $b2 = $this->db->table('pengeluaran')
            ->select([
                'schedule_celup.no_model',
                'schedule_celup.item_type',
                'schedule_celup.kode_warna',
                'schedule_celup.warna AScolor',
                'pengeluaran.tgl_out',
                'pengeluaran.nama_cluster',
                'pengeluaran.area_out',
                'pengeluaran.kgs_out',
                'pengeluaran.cns_out',
                'pengeluaran.krg_out',
                'pengeluaran.lot_out',
                'pengeluaran.keterangan_gbn',
                'pengeluaran.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
            ])
            ->join('out_celup',                      'out_celup.id_out_celup = pengeluaran.id_out_celup',             'left')
            ->join('schedule_celup',                 'schedule_celup.id_celup = out_celup.id_celup',                     'left')
            ->join('pemesanan',                      'pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('total_pemesanan',                'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material',                       'material.id_material = pemesanan.id_material',                    'left')
            ->join('master_material',                'master_material.item_type = material.item_type',                 'left')
            ->join('master_order',                   'master_order.id_order = material.id_order',                       'left')
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('pengeluaran.id_out_celup IS NOT NULL') // Ensure we only get valid out_celup
            ->where('pengeluaran.id_psk IS NULL') // Ensure we only get valid schedule_celup
            ->groupBy('pengeluaran.id_pengeluaran');


        // -- Subquery 2: other_out via schedule_celup --
        $b3 = $this->db->table('other_out')
            ->select([
                'schedule_celup.no_model',
                'schedule_celup.item_type',
                'schedule_celup.kode_warna',
                'schedule_celup.warna AS color',
                'other_out.tgl_other_out AS tgl_out',
                'other_out.nama_cluster',
                'NULL          AS area_out',
                'other_out.kgs_other_out   AS kgs_out',
                'other_out.cns_other_out   AS cns_out',
                'other_out.krg_other_out   AS krg_out',
                'out_celup.lot_kirim       AS lot_out',
                'other_out.kategori        AS keterangan_gbn',
                'other_out.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'NULL          AS ttl_kg',
                'NULL          AS ttl_cns',
                'master_material.jenis     AS jenis',
            ])
            ->join('out_celup',      'out_celup.id_out_celup = other_out.id_out_celup',         'left')
            ->join('schedule_celup', 'schedule_celup.id_celup     = out_celup.id_celup',   'left')
            ->join('material',       'material.item_type          = schedule_celup.item_type', 'left')
            ->join('master_material', 'master_material.item_type   = schedule_celup.item_type', 'left')
            ->join('master_order',   'master_order.id_order       = material.id_order',       'left')
            ->where('out_celup.id_celup IS NOT NULL') // Ensure we only get valid out_celup
            ->where('out_celup.id_other_bon IS NULL') // Ensure we only get valid schedule_celup
            ->groupBy('other_out.id_other_out');

        // -- Subquery 3: other_out via other_bon --
        $b4 = $this->db->table('other_out')
            ->select([
                'other_bon.no_model',
                'other_bon.item_type',
                'other_bon.kode_warna',
                'other_bon.warna AS color',
                'other_out.tgl_other_out AS tgl_out',
                'other_out.nama_cluster',
                'NULL          AS area_out',
                'other_out.kgs_other_out   AS kgs_out',
                'other_out.cns_other_out   AS cns_out',
                'other_out.krg_other_out   AS krg_out',
                'out_celup.lot_kirim       AS lot_out',
                'other_out.kategori        AS keterangan_gbn',
                'other_out.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'NULL          AS ttl_kg',
                'NULL          AS ttl_cns',
                'master_material.jenis     AS jenis',
            ])
            ->join('out_celup',      'out_celup.id_out_celup = other_out.id_out_celup',       'left')
            ->join('other_bon',      'other_bon.id_other_bon = out_celup.id_other_bon',       'left')
            ->join('material',       'material.item_type          = other_bon.item_type',    'left')
            ->join('master_material', 'master_material.item_type   = other_bon.item_type',    'left')
            ->join('master_order',   'master_order.id_order       = material.id_order',       'left')
            ->where('out_celup.id_other_bon IS NOT NULL') // Ensure we only get valid other_bon
            ->where('out_celup.id_celup IS NULL') // Ensure we only get valid other_out
            ->groupBy('other_out.id_other_out');

        // -- Keyword filtering on each part --
        if (! empty($key)) {
            foreach ([$b1, $b2, $b3,$b4] as $sub) {
                $sub->groupStart()
                    ->like('no_model',   $key)
                    ->orLike('item_type', $key)
                    ->orLike('kode_warna', $key)
                    ->orLike('color',     $key)
                    ->groupEnd();
            }
        }

        // -- Build the UNION of all three queries --
        $union = $b1
            ->union($b2, true)  // true = union ALL
            ->union($b3, true)
            ->union($b4, true);

        // -- Date filters on the unified 'tgl_out' column --
        if (! empty($tanggal_awal)) {
            $union->where('tgl_out >=', $tanggal_awal);
        }
        if (! empty($tanggal_akhir)) {
            $union->where('tgl_out <=', $tanggal_akhir);
        }

        // -- Final sorting & execute --
        return $union
            ->orderBy('tgl_out', 'DESC')
            ->get()
            ->getResultArray();
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
    public function getKgPersiapanPengeluaran($id_total_pemesanan)
    {
        return $this->select('SUM(kgs_out) AS kgs_out')
            ->where('id_total_pemesanan', $id_total_pemesanan)
            ->where('status', 'Pengeluaran Jalur')
            ->first();
    }

    public function getItemTypes(string $no_model): array
    {
        $builder = $this->db
            ->table('pengeluaran')
            ->distinct()
            ->select('COALESCE(sc.item_type, m.item_type) AS item_type')
            ->join('out_celup oc', 'oc.id_out_celup = pengeluaran.id_out_celup', 'left')
            // Gabungkan schedule_celup
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            // Gabungkan pemesanan_spandex_karet
            ->join('pemesanan_spandex_karet psk', 'psk.id_psk = pengeluaran.id_psk', 'left')
            // Gabungkan pemesanan dengan kondisi OR di ON clause
            ->join(
                'pemesanan pe',
                '(
                pe.id_total_pemesanan = psk.id_total_pemesanan
                OR pe.id_total_pemesanan = pengeluaran.id_total_pemesanan
            )',
                'left'
            )
            ->join('material m', 'm.id_material = pe.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->groupStart()
            ->where('sc.no_model', $no_model)
            ->orWhere('mo.no_model', $no_model)
            ->groupEnd();

        return $builder->get()->getResultArray();
    }





    public function getKodeWarna($model, $item_type): array
    {
    $builder = $this->db
        ->table('pengeluaran')
        ->distinct()
        ->select('COALESCE(sc.kode_warna, m.kode_warna) AS kode_warna')
        ->join('out_celup oc', 'oc.id_out_celup = pengeluaran.id_out_celup', 'left')
        // Gabungkan schedule_celup
        ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
        // Gabungkan pemesanan_spandex_karet
        ->join('pemesanan_spandex_karet psk', 'psk.id_psk = pengeluaran.id_psk', 'left')
        // Gabungkan pemesanan dengan kondisi OR di ON clause
        ->join(
            'pemesanan pe',
            '(
                pe.id_total_pemesanan = psk.id_total_pemesanan
                OR pe.id_total_pemesanan = pengeluaran.id_total_pemesanan
            )',
            'left'
        )
        ->join('material m', 'm.id_material = pe.id_material', 'left')
        ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
        ->where('pengeluaran.status', 'Pengeluaran Jalur')
            -> groupStart()
            ->where('sc.no_model', $model)
            ->orWhere('mo.no_model', $model)
            ->groupEnd()
            ->groupStart()
            ->where('sc.item_type', $item_type)
            ->orWhere('m.item_type', $item_type)
            ->groupEnd();

    return $builder->get()->getResultArray();
    }

    public function getWarna($model, $item_type, $kode_warna): array
    {
        $builder = $this->db
            ->table('pengeluaran')
            ->distinct()
            ->select('COALESCE(sc.warna, m.color) AS warna')
            ->join('out_celup oc', 'oc.id_out_celup = pengeluaran.id_out_celup', 'left')
            // Gabungkan schedule_celup
            ->join('schedule_celup sc', 'sc.id_celup = oc.id_celup', 'left')
            // Gabungkan pemesanan_spandex_karet
            ->join('pemesanan_spandex_karet psk', 'psk.id_psk = pengeluaran.id_psk', 'left')
            // Gabungkan pemesanan dengan kondisi OR di ON clause
            ->join(
                'pemesanan pe',
                '(
                pe.id_total_pemesanan = psk.id_total_pemesanan
                OR pe.id_total_pemesanan = pengeluaran.id_total_pemesanan
            )',
                'left'
            )
            ->join('material m', 'm.id_material = pe.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->groupStart()
            ->where('sc.no_model', $model)
            ->orWhere('mo.no_model', $model)
            ->groupEnd()
            ->groupStart()
            ->where('sc.item_type', $item_type)
            ->orWhere('m.item_type', $item_type)
            ->groupEnd()
            ->groupStart()
            ->where('sc.kode_warna', $kode_warna)
            ->orWhere('m.kode_warna', $kode_warna)
            ->groupEnd();

        return $builder->get()->getResultArray();
    }

    public function validateDeliveryData(array $data): array
    {
        $builder = $this->db
            ->table('pengeluaran as p')
            ->distinct()
            ->select([
                'p.*',
                // jika ada di schedule_celup ambil sc.no_model, jika tidak ambil mo.no_model
                'COALESCE(sc.no_model, mo.no_model) AS no_model',
                'COALESCE(sc.item_type, m.item_type) AS item_type',
                'COALESCE(sc.kode_warna, m.kode_warna)    AS kode_warna',
                'COALESCE(sc.warna, m.color)             AS warna',
                'mm.jenis',
                'tp.ttl_kg',
                'tp.ttl_cns',
            ])
            // JOIN semua tabel
            ->join('out_celup oc',                'oc.id_out_celup    = p.id_out_celup',            'left')
            ->join('schedule_celup sc',           'sc.id_celup        = oc.id_celup',               'left')
            ->join('pemesanan_spandex_karet psk', 'psk.id_psk         = p.id_psk',                 'left')
            ->join(
                'pemesanan pe',
                '(
                pe.id_total_pemesanan = psk.id_total_pemesanan
                OR pe.id_total_pemesanan = p.id_total_pemesanan
            )',
                'left'
            )
            ->join('total_pemesanan tp',         'tp.id_total_pemesanan = p.id_total_pemesanan',   'left')
            ->join('material m',                  'm.id_material      = pe.id_material',            'left')
            ->join('master_material mm',          'mm.item_type     = m.item_type',             'left')
            ->join('master_order mo',             'mo.id_order        = m.id_order',                'left')
            // Filter status sebelum grouping
            ->where('p.status', 'Pengeluaran Jalur')
            // Filter no_model
            ->groupStart()
            ->where('sc.no_model', $data['no_model'])
            ->orWhere('mo.no_model', $data['no_model'])
            ->groupEnd()
            // Filter item_type
            ->groupStart()
            ->where('sc.item_type', $data['item_type'])
            ->orWhere('m.item_type', $data['item_type'])
            ->groupEnd()
            // Filter kode_warna
            ->groupStart()
            ->where('sc.kode_warna', $data['kode_warna'])
            ->orWhere('m.kode_warna', $data['kode_warna'])
            ->groupEnd()
            // Group by id_pengeluaran untuk distinct
            ->groupBy('p.id_pengeluaran');

        return $builder
            ->get()
            ->getResultArray();
    }
    public function getQtyOutByCns($id_out_celup)
    {
        return $this->select('SUM(kgs_out) AS kgs_out, SUM(cns_out) AS cns_out')
            ->where('id_out_celup', $id_out_celup)
            ->where('krg_out', 0)
            ->first();
    }
}
