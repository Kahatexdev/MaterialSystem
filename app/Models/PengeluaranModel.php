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
    public function searchPengiriman2($noModel)
    {
        return $this->db->table('pengeluaran')
            ->select('pengeluaran.*, SUM(pengeluaran.kgs_out) AS kgs_out, pengeluaran.lot_out AS lot_kirim, master_order.no_model, material.kode_warna, material.color AS warna, material.item_type')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan')
            ->join('material', 'material.id_material = pemesanan.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $noModel)
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->groupBy('master_order.no_model, material.kode_warna, material.color, material.item_type, pengeluaran.lot_out')
            ->get()
            ->getResultArray();
    }
    // public function getTotalPengiriman($data)
    // {
    //     // return $this->select('SUM(pengeluaran.kgs_out) AS kgs_out')
    //     //     ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
    //     //     ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
    //     //     ->join('material', 'material.id_material = pemesanan.id_material', 'left')
    //     //     ->join('master_order', 'master_order.id_order = material.id_order', 'left')
    //     //     ->where('pengeluaran.area_out', $data['area'])
    //     //     ->where('pengeluaran.status', 'Pengiriman Area')
    //     //     ->where('master_order.no_model', $data['no_model'])
    //     //     ->where('material.item_type', $data['item_type'])
    //     //     ->where('material.kode_warna', $data['kode_warna'])
    //     //     ->first();
    //     $area       = $data['area'] ?? '';
    //     $noModel    = $data['no_model'] ?? '';
    //     $itemType   = $data['item_type'] ?? '';
    //     $kodeWarna  = $data['kode_warna'] ?? '';

    //     $sql = "
    //         SELECT 
    //             SUM(sub.total_kgs_out) AS kgs_out
    //         FROM (
    //             SELECT 
    //                 p.id_total_pemesanan,
    //                 SUM(p.kgs_out) AS total_kgs_out
    //             FROM pengeluaran p
    //             WHERE p.area_out = ?
    //               AND p.status = 'Pengiriman Area'
    //             GROUP BY p.id_total_pemesanan
    //         ) AS sub
    //         WHERE EXISTS (
    //             SELECT 1
    //             FROM total_pemesanan tp
    //             JOIN pemesanan pm ON pm.id_total_pemesanan = tp.id_total_pemesanan
    //             JOIN material m ON m.id_material = pm.id_material
    //             JOIN master_order mo ON mo.id_order = m.id_order
    //             WHERE tp.id_total_pemesanan = sub.id_total_pemesanan
    //               AND mo.no_model = ?
    //               AND m.item_type = ?
    //               AND m.kode_warna = ?
    //         )
    //     ";

    //     return $this->db->query($sql, [$area, $noModel, $itemType, $kodeWarna])->getRowArray();
    // }

    public function getTotalPengiriman(array $data): array
    {
        $area      = $data['area'] ?? '';
        $idTotal   = $data['id_total_pemesanan'] ?? null;
        $tglMax    = $data['tgl_max'] ?? null; // opsional (pakai jika ingin cut-off hingga tgl_pakai)

        if (!$area || !$idTotal) {
            return ['kgs_out' => 0];
        }

        $params = [$area, $idTotal];
        $tglSql = '';
        if ($tglMax) {
            $tglSql = ' AND p.tgl_out <= ?';
            $params[] = $tglMax;
        }

        $sql = "
        SELECT COALESCE(SUM(CAST(p.kgs_out AS DECIMAL(15,3))), 0) AS kgs_out
        FROM pengeluaran p
        WHERE p.area_out = ?
          AND p.status   = 'Pengiriman Area'
          AND p.id_total_pemesanan = ?
          $tglSql
    ";

        return $this->db->query($sql, $params)->getRowArray();
    }

    public function getFilterPengiriman($key = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        // 1) Siapkan keempat builder tanpa filter tanggal
        // -- Subquery 1: pengeluaran --
        $b1 = $this->db->table('pengeluaran')
            ->select([
                'master_order.no_model',
                'material.item_type',
                'material.kode_warna',
                'material.color',
                'material.loss',
                // 'po_tambahan.no_po',
                // 'NULL AS qty_po_plus',
                'pemesanan.tgl_pakai',
                'pengeluaran.tgl_out',
                'pengeluaran.nama_cluster',
                'pengeluaran.area_out',
                'pengeluaran.kgs_out AS kgs_pakai',
                'pengeluaran.cns_out AS cones_pakai',
                'pengeluaran.krg_out AS krg_pakai',
                'pengeluaran.lot_out AS lot_pakai',
                'pengeluaran.keterangan_gbn',
                'pengeluaran.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg AS kgs_pesan',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
                'open_po.created_at AS tgl_po',
                '(po_tambahan.poplus_mc_kg + plus_pck_kg) AS qty_po_plus',
                'stock.kgs_stock_awal',
                'stock.lot_awal',
                'stock.kgs_in_out',
                'stock.lot_stock'
            ])
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('pemesanan_spandex_karet psk',    'psk.id_psk = pengeluaran.id_psk',                                        'left')
            ->join('total_pemesanan',                'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan',    'left')
            ->join('pemesanan',                      'pemesanan.id_total_pemesanan = psk.id_total_pemesanan',                 'left')
            ->join('material',                       'material.id_material = pemesanan.id_material',                           'left')
            ->join('po_tambahan',                   'po_tambahan.id_material = material.id_material',                        'left')
            ->join('master_material',                'master_material.item_type = material.item_type',                         'left')
            ->join('master_order',                   'master_order.id_order = material.id_order',                              'left')
            ->join('open_po',                        'open_po.no_model = master_order.no_model AND open_po.item_type = material.item_type AND open_po.kode_warna = material.kode_warna', 'left')
            ->where('open_po.id_induk IS NOT NULL')
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('pengeluaran.id_out_celup IS NULL')
            ->where('pengeluaran.id_psk IS NOT NULL')
            ->groupBy('pengeluaran.id_pengeluaran');

        // -- Subquery 2: pengeluaran via out_celup/schedule_celup --
        $b2 = $this->db->table('pengeluaran')
            ->select([
                'master_order.no_model',
                'material.item_type',
                'material.kode_warna',
                'material.color',
                'material.loss',
                'pemesanan.tgl_pakai',
                'pengeluaran.tgl_out',
                'pengeluaran.nama_cluster',
                'pengeluaran.area_out',
                'pengeluaran.kgs_out AS kgs_pakai',
                'pengeluaran.cns_out AS cones_pakai',
                'pengeluaran.krg_out AS krg_pakai',
                'pengeluaran.lot_out AS lot_pakai',
                'pengeluaran.keterangan_gbn',
                'pengeluaran.admin',
                // 'NULL AS qty_po_plus',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg AS kgs_pesan',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
                'open_po.created_at AS tgl_po',
                '(po_tambahan.poplus_mc_kg + plus_pck_kg) AS qty_po_plus',
                'stock.kgs_stock_awal',
                'stock.lot_awal',
                'stock.kgs_in_out',
                'stock.lot_stock'
            ])
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('out_celup',      'out_celup.id_out_celup = pengeluaran.id_out_celup',                   'left')
            ->join('schedule_celup', 'schedule_celup.id_celup     = out_celup.id_celup',                 'left')
            ->join('open_po',       'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->join('pemesanan',      'pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan',      'left')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material',       'material.id_material = pemesanan.id_material',                       'left')
            ->join('po_tambahan',                   'po_tambahan.id_material = material.id_material',                        'left')
            ->join('master_material', 'master_material.item_type = material.item_type',     'left')
            ->join('master_order',   'master_order.id_order = material.id_order',                          'left')
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('pengeluaran.id_out_celup IS NOT NULL')
            ->where('pengeluaran.id_psk IS NULL')
            ->groupBy('pengeluaran.id_pengeluaran');

        // -- Subquery 3: other_out via schedule_celup --
        $b3 = $this->db->table('other_out')
            ->select([
                'master_order.no_model',
                'material.item_type',
                'material.kode_warna',
                'material.color',
                'material.loss',
                'NULL AS tgl_pakai',
                // 'NULL AS qty_po_plus',
                'other_out.tgl_other_out   AS tgl_out',
                'other_out.nama_cluster',
                'NULL                      AS area_out',
                'other_out.kgs_other_out   AS kgs_pakai',
                'other_out.cns_other_out   AS cones_pakai',
                'other_out.krg_other_out   AS krg_pakai',
                'out_celup.lot_kirim       AS lot_pakai',
                'other_out.kategori        AS keterangan_gbn',
                'other_out.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                // 'material.loss',
                'NULL                       AS kgs_pesan',
                'NULL                      AS ttl_cns',
                'master_material.jenis     AS jenis',
                'open_po.created_at AS tgl_po',
                'NULL AS qty_po_plus',
                'NULL   AS kgs_stock_awal',
                'NULL   AS lot_awal',
                'NULL   AS kgs_in_out',
                'NULL   AS lot_stock'
            ])
            ->join('out_celup',      'out_celup.id_out_celup = other_out.id_out_celup',                      'left')
            ->join('schedule_celup', 'schedule_celup.id_celup     = out_celup.id_celup',                'left')
            ->join('open_po',       'open_po.no_model = schedule_celup.no_model AND open_po.item_type = schedule_celup.item_type AND open_po.kode_warna = schedule_celup.kode_warna', 'left')
            ->join(
                'material',
                'material.item_type = schedule_celup.item_type 
       AND material.kode_warna = schedule_celup.kode_warna',
                'left'
            )

            ->join('master_material', 'master_material.item_type   = schedule_celup.item_type',            'left')
            ->join('master_order',   'master_order.id_order       = material.id_order',                       'left')
            ->where('out_celup.id_celup IS NOT NULL')
            ->where('out_celup.id_other_bon IS NULL')
            ->groupBy('other_out.id_other_out');

        // -- Subquery 4: other_out via other_bon --
        $b4 = $this->db->table('other_out')
            ->select([
                'other_bon.no_model',
                'other_bon.item_type',
                'other_bon.kode_warna',
                'other_bon.warna AS color',
                'material.loss',
                // 'NULL AS qty_po_plus',
                'NULL AS tgl_pakai',
                'other_out.tgl_other_out   AS tgl_out',
                'other_out.nama_cluster',
                'NULL                      AS area_out',
                'other_out.kgs_other_out   AS kgs_pakai',
                'other_out.cns_other_out   AS cones_pakai',
                'other_out.krg_other_out   AS krg_pakai',
                'out_celup.lot_kirim       AS lot_pakai',
                'other_out.kategori        AS keterangan_gbn',
                'other_out.admin',
                'master_order.foll_up',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                // 'material.loss',
                'NULL                      AS kgs_pesan',
                'NULL                      AS ttl_cns',
                'master_material.jenis     AS jenis',
                'NULL                      AS tgl_po',
                'NULL AS qty_po_plus',
                'NULL   AS kgs_stock_awal',
                'NULL   AS lot_awal',
                'NULL   AS kgs_in_out',
                'NULL   AS lot_stock'
            ])
            ->join('out_celup',      'out_celup.id_out_celup = other_out.id_out_celup',                      'left')
            ->join('other_bon',      'other_bon.id_other_bon = out_celup.id_other_bon',                      'left')
            ->join('material',       'material.item_type          = other_bon.item_type AND other_bon.kode_warna = material.kode_warna', 'left')
            ->join('master_material', 'master_material.item_type   = other_bon.item_type',               'left')
            ->join('master_order',   'master_order.id_order       = material.id_order',                       'left')
            ->where('out_celup.id_other_bon IS NOT NULL')
            ->where('out_celup.id_celup IS NULL')
            ->where('other_out.kategori !=', 'Perbaikan Data Menumpuk')
            ->groupBy('other_out.id_other_out');

        // // 2) Jika ada keyword, apply LIKE di tiap builder
        // if (!empty($key)) {
        //     foreach ([$b1, $b2, $b3, $b4] as $sub) {
        //         $sub->groupStart()
        //             ->like('no_model', $key)
        //             ->orLike('item_type', $key)
        //             ->orLike('kode_warna', $key)
        //             ->orLike('color', $key)
        //             ->groupEnd();
        //     }
        // }

        // 3) Compile masing-masing subquery & bangun UNION ALL di dalam derived table
        $sqlUnion  = '(' . $b1->getCompiledSelect() . ') UNION ALL '
            . '(' . $b2->getCompiledSelect() . ') UNION ALL '
            . '(' . $b3->getCompiledSelect() . ') UNION ALL '
            . '(' . $b4->getCompiledSelect() . ')';

        $sql = "SELECT * 
            FROM ( $sqlUnion ) AS all_data";

        // 3) Siapkan kondisi filter tanggal & keyword
        $conds = [];

        // filter tanggal sama seperti sebelumnya
        if ($tanggal_awal)  $conds[] = "all_data.tgl_out >= '$tanggal_awal'";
        if ($tanggal_akhir) $conds[] = "all_data.tgl_out <= '$tanggal_akhir'";

        // filter keyword, QUALIFIED dengan alias derived table
        if ($key) {
            $k = $this->db->escapeLikeString($key);
            $conds[] = "( all_data.no_model   LIKE '%{$k}%'
                OR all_data.item_type  LIKE '%{$k}%'
                OR all_data.kode_warna LIKE '%{$k}%'
                OR all_data.color      LIKE '%{$k}%')";
        }

        if (count($conds)) {
            $sql .= ' WHERE ' . implode(' AND ', $conds);
        }
        $sql .= ' ORDER BY all_data.tgl_out DESC';


        // 5) Eksekusi dan return
        $result = $this->db->query($sql);


        // dd($this->db->getLastQuery());
        return $result->getResultArray();
    }
    public function getDataPemesananExport($jenis, $tglPakai, $noModel = null)
    {
        $builder = $this->db->table('pemesanan');  // ini yang benar
        $builder->select("
            pemesanan.tgl_pakai,
            pemesanan.admin,
            master_order.no_model,
            master_material.jenis,
            material.item_type,
            material.kode_warna,
            material.color,
            CONCAT_WS('/', total_pemesanan.ttl_kg, total_pemesanan.ttl_cns) AS pesanan,
            pengeluaran.lot_out,
            out_celup.no_karung,
            pengeluaran.id_pengeluaran,
            pengeluaran.id_stock,
            pengeluaran.id_out_celup,
            total_pemesanan.id_total_pemesanan,
            pengeluaran.kgs_out,
            pengeluaran.cns_out,
            pengeluaran.krg_out,
            pengeluaran.nama_cluster,
            CONCAT(
                COALESCE(pengeluaran.keterangan_gbn, ''),
                CASE WHEN pemesanan.keterangan_gbn IS NOT NULL THEN ' - ' ELSE '' END,
                COALESCE(pemesanan.keterangan_gbn, '')
            ) AS keterangan_gbn,
            cluster.group
        ")

            ->join('pengeluaran', 'pengeluaran.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup', 'left')
            ->join('cluster', 'cluster.nama_cluster = pengeluaran.nama_cluster', 'left')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tglPakai)
            ->where('pemesanan.status_kirim', 'YA');
        if (!empty($noModel)) {
            $builder->where('master_order.no_model', $noModel);
        }
        $builder->groupStart()
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->orWhere('pengeluaran.id_pengeluaran IS NULL', null, false)
            ->groupEnd();
        return $builder
            // ->where('pengeluaran.status', 'Pengeluaran Jalur')
            // ->where('pengeluaran.status = "Pengeluaran Jalur" OR pengeluaran.id_pengeluaran IS NULL')
            ->groupBy('pengeluaran.id_pengeluaran')
            ->groupBy('pemesanan.id_total_pemesanan')
            ->orderBy('pengeluaran.area_out, pengeluaran.nama_cluster', 'ASC')
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
    public function getKgPengiriman($id_total_pemesanan)
    {
        return $this->select('SUM(kgs_out) AS kgs_out')
            ->where('id_total_pemesanan', $id_total_pemesanan)
            ->where('status', 'Pengiriman Area')
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
            ->groupStart()
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

    // public function validateDeliveryData(array $data): array
    // {
    //     $builder = $this->db
    //         ->table('pengeluaran as p')
    //         ->distinct()
    //         ->select([
    //             'p.*',
    //             'pe.tgl_pakai',
    //             // jika ada di schedule_celup ambil sc.no_model, jika tidak ambil mo.no_model
    //             'COALESCE(sc.no_model, mo.no_model) AS no_model',
    //             'COALESCE(sc.item_type, m.item_type) AS item_type',
    //             'COALESCE(sc.kode_warna, m.kode_warna)    AS kode_warna',
    //             'COALESCE(sc.warna, m.color)             AS warna',
    //             'mm.jenis',
    //             'tp.ttl_kg',
    //             'tp.ttl_cns',
    //             'oc.no_karung',
    //         ])
    //         // JOIN semua tabel
    //         ->join('out_celup oc',                'oc.id_out_celup    = p.id_out_celup',            'left')
    //         ->join('schedule_celup sc',           'sc.id_celup        = oc.id_celup',               'left')
    //         ->join('pemesanan_spandex_karet psk', 'psk.id_psk         = p.id_psk',                 'left')
    //         ->join(
    //             'pemesanan pe',
    //             '(
    //             pe.id_total_pemesanan = psk.id_total_pemesanan
    //             OR pe.id_total_pemesanan = p.id_total_pemesanan
    //         )',
    //             'left'
    //         )
    //         ->join('total_pemesanan tp',         'tp.id_total_pemesanan = p.id_total_pemesanan',   'left')
    //         ->join('material m',                  'm.id_material      = pe.id_material',            'left')
    //         ->join('master_material mm',          'mm.item_type     = m.item_type',             'left')
    //         ->join('master_order mo',             'mo.id_order        = m.id_order',                'left')
    //         // Filter status sebelum grouping
    //         ->where('p.status', 'Pengeluaran Jalur')
    //         // Filter no_model
    //         ->groupStart()
    //         ->where('sc.no_model', $data['no_model'])
    //         ->orWhere('mo.no_model', $data['no_model'])
    //         ->groupEnd()
    //         // Filter item_type
    //         ->groupStart()
    //         ->where('sc.item_type', $data['item_type'])
    //         ->orWhere('m.item_type', $data['item_type'])
    //         ->groupEnd()
    //         // Filter kode_warna
    //         ->groupStart()
    //         ->where('sc.kode_warna', $data['kode_warna'])
    //         ->orWhere('m.kode_warna', $data['kode_warna'])
    //         ->groupEnd()
    //         // Group by id_pengeluaran untuk distinct
    //         ->groupBy('p.id_pengeluaran');

    //     return $builder
    //         ->get()
    //         ->getResultArray();
    // }
    public function validateDeliveryData(array $data): array
    {
        $builder = $this->db
            ->table('pengeluaran as p')
            ->distinct()
            ->select([
                'p.*',
                'pe.tgl_pakai',
                // jika ada di schedule_celup ambil sc.no_model, jika tidak ambil mo.no_model
                'COALESCE(mo.no_model, sc.no_model) AS no_model',
                'COALESCE(m.item_type, sc.item_type) AS item_type',
                'COALESCE(m.kode_warna, sc.kode_warna)    AS kode_warna',
                'COALESCE(m.color, sc.warna)             AS warna',
                'mm.jenis',
                'tp.ttl_kg',
                'tp.ttl_cns',
                'oc.no_karung',
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
            ->where('pe.tgl_pakai', $data['tgl_pakai'])
            ->where('mm.jenis', $data['jenis'])
            ->where('p.area_out', $data['area'])
            // Filter no_model
            // ->groupStart()
            // ->where('sc.no_model', $data['no_model'])
            // ->orWhere('mo.no_model', $data['no_model'])
            // ->groupEnd()d
            // Filter item_type
            // ->groupStart()
            // ->where('sc.item_type', $data['item_type'])
            // ->orWhere('m.item_type', $data['item_type'])
            // ->groupEnd()
            // // Filter kode_warna
            // ->groupStart()
            // ->where('sc.kode_warna', $data['kode_warna'])
            // ->orWhere('m.kode_warna', $data['kode_warna'])
            // ->groupEnd()
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
    public function getTtlPersiapan($jenis, $tglPakai)
    {
        return $this->select('SUM(pengeluaran.kgs_out) AS kgs_out, SUM(pengeluaran.cns_out) AS cns_out')
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('master_material', 'master_material.item_type=stock.item_type', 'left')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan=pengeluaran.id_total_pemesanan', 'left')
            ->join('pemesanan', 'total_pemesanan.id_total_pemesanan=pemesanan.id_total_pemesanan', 'left')
            ->where('master_material.jenis', $jenis)
            ->where('pemesanan.tgl_pakai', $tglPakai)
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->groupBy('master_material.jenis')
            ->first();
    }
    public function getPakaiArea($key, $jenis = null)
    {
        $builder = $this->db->table('pengeluaran p')
            ->select('p.*, oc.no_model, sc.item_type, sc.kode_warna, sc.warna, mm.jenis, sc.po_plus')
            ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
            ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup', 'left')
            ->join('master_material mm', 'sc.item_type = mm.item_type', 'left')
            ->where('oc.no_model', $key);

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('p.id_pengeluaran')
            ->orderBy('p.tgl_out, sc.item_type, sc.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFilterPemakaianNylonByBuyer($buyer = null)
    {
        $builder = $this->select('
            master_order.buyer,
            master_material.jenis,
            SUM(pengeluaran.kgs_out) AS pemakaian_kgs, 
            SUM(pengeluaran.cns_out) AS pemakaian_cns, 
            SUM(pengeluaran.krg_out) AS pemakaian_krg
        ')
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->join('master_material', 'master_material.item_type = stock.item_type', 'left')
            ->where('pengeluaran.status', 'Pengiriman Area')
            ->where('master_material.jenis', 'NYLON');

        if (!empty($buyer)) {
            $builder->where('master_order.buyer', $buyer);
        }

        return $builder->groupBy(['master_order.buyer', 'master_material.jenis'])
            ->findAll();
    }

    public function getDataDipinjam($key, $jenis = null)
    {
        $builder = $this->db->table('pengeluaran p')
            ->select('s.no_model as no_model_old, s.item_type, s.kode_warna, s.warna, p.kgs_out, p.cns_out, p.lot_out, p.nama_cluster, mo.no_model as no_model_new, p.admin, p.area_out, pm.tgl_pakai, pm.po_tambahan, hs.keterangan')
            ->join('stock s', 's.id_stock = p.id_stock', 'left')
            ->join('history_stock hs', 's.id_stock = hs.id_stock_old', 'left')
            ->join('out_celup oc', 'p.id_out_celup = oc.id_out_celup', 'left')
            ->join('pemesanan pm', 'p.id_total_pemesanan = pm.id_total_pemesanan', 'left')
            ->join('material m', 'pm.id_material = pm.id_material', 'left')
            ->join('master_order mo', 'm.id_order = mo.id_order', 'left')
            ->join('master_material mm ', 'mm.item_type = s.item_type', 'left')
            ->where('s.no_model', $key)
            ->where('hs.keterangan', 'Pinjam Order');

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }
        return $builder->groupBy('hs.id_history_pindah')
            ->orderBy('s.no_model', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function sumKgsByStatus(int $id_total_pemesanan): array
    {
        return (array) $this->select("
            SUM(CASE WHEN status = 'Pengeluaran Jalur' THEN kgs_out ELSE 0 END) AS kgs_persiapan,
            SUM(CASE WHEN status = 'Pengiriman Area' THEN kgs_out ELSE 0 END)    AS kgs_pengiriman
        ")
            ->where('id_total_pemesanan', $id_total_pemesanan)
            ->first();
    }
}
