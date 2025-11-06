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
            ->select('pengeluaran.*, out_celup.lot_kirim, out_celup.no_model, stock.kode_warna, stock.warna, stock.item_type, out_celup.no_karung')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup')
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock')
            ->where('pengeluaran.id_out_celup', $id)
            ->where('pengeluaran.status', 'Pengeluaran Jalur')
            ->groupBy('pengeluaran.id_pengeluaran')
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
            ->select('pengeluaran.*, SUM(pengeluaran.kgs_out) AS kgs_out, master_order.no_model, material.kode_warna, material.color AS warna, material.item_type')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan')
            ->join('material', 'material.id_material = pemesanan.id_material')
            ->join('master_order', 'master_order.id_order = material.id_order')
            ->where('master_order.no_model', $noModel)
            ->where('pengeluaran.status', 'Pengiriman Area')
            // ->groupBy('master_order.no_model, material.kode_warna, material.color, material.item_type, pengeluaran.lot_out')
            ->groupBy('master_order.no_model, material.kode_warna, material.color, material.item_type')
            ->get()
            ->getResultArray();
    }
    public function getLotKirim($noModel)
    {
        return $this->db->table('pengeluaran')
            ->select('pengeluaran.lot_out AS lot_kirim, master_order.no_model, material.kode_warna, material.color AS warna, material.item_type')
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
    public function getTotalPengiriman($data)
    {
        // return $this->select('SUM(pengeluaran.kgs_out) AS kgs_out')
        //     ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
        //     ->join('pemesanan', 'pemesanan.id_total_pemesanan = total_pemesanan.id_total_pemesanan', 'left')
        //     ->join('material', 'material.id_material = pemesanan.id_material', 'left')
        //     ->join('master_order', 'master_order.id_order = material.id_order', 'left')
        //     ->where('pengeluaran.area_out', $data['area'])
        //     ->where('pengeluaran.status', 'Pengiriman Area')
        //     ->where('master_order.no_model', $data['no_model'])
        //     ->where('material.item_type', $data['item_type'])
        //     ->where('material.kode_warna', $data['kode_warna'])
        //     ->first();
        $area       = $data['area'] ?? '';
        $noModel    = $data['no_model'] ?? '';
        $itemType   = $data['item_type'] ?? '';
        $kodeWarna  = $data['kode_warna'] ?? '';

        $sql = "
            SELECT 
                COALESCE(SUM(p.kgs_out), 0) AS kgs_out
            FROM pengeluaran p
            WHERE p.area_out = ?
            AND p.status = 'Pengiriman Area'
            AND p.id_total_pemesanan IN (
                SELECT DISTINCT tp.id_total_pemesanan
                FROM total_pemesanan tp
                JOIN pemesanan pm ON pm.id_total_pemesanan = tp.id_total_pemesanan
                JOIN material m ON m.id_material = pm.id_material
                JOIN master_order mo ON mo.id_order = m.id_order
                WHERE mo.no_model = ?
                    AND m.item_type = ?
                    AND m.kode_warna = ?
            )
        ";

        return $this->db->query($sql, [$area, $noModel, $itemType, $kodeWarna])->getRowArray();
    }

    public function getFilterPengiriman($jenis = null, $key = null, $tanggal_awal = null, $tanggal_akhir = null)
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
                'pengeluaran.updated_at AS tgl_out',
                'pengeluaran.nama_cluster',
                'pengeluaran.area_out',
                'pengeluaran.kgs_out AS kgs_pakai',
                'pengeluaran.cns_out AS cones_pakai',
                'pengeluaran.krg_out AS krg_pakai',
                'pengeluaran.lot_out AS lot_pakai',
                'pengeluaran.keterangan_gbn',
                'pengeluaran.admin',
                'master_order.foll_up',
                'master_order.lco_date AS tgl_po',
                'master_order.no_order',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg AS kgs_pesan',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
                '(po_tambahan.poplus_mc_kg + plus_pck_kg) AS qty_po_plus',
                'stock.kgs_stock_awal',
                'stock.lot_awal',
                'stock.kgs_in_out',
                'stock.lot_stock'
            ])
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('pemesanan_spandex_karet psk',    'psk.id_psk = pengeluaran.id_psk',                                        'left')
            ->join('total_pemesanan',                'total_pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan',    'left')
            ->join('pemesanan',                      'pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan',                 'left')
            ->join('material',                       'material.id_material = pemesanan.id_material',                           'left')
            ->join('po_tambahan',                   'po_tambahan.id_material = material.id_material',                        'left')
            ->join('master_material',                'master_material.item_type = material.item_type',                         'left')
            ->join('master_order',                   'master_order.id_order = material.id_order',                              'left')
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
                'pengeluaran.updated_at AS tgl_out',
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
                'master_order.lco_date AS tgl_po',
                'master_order.buyer',
                'master_order.unit',
                'master_order.delivery_awal',
                'master_order.delivery_akhir',
                'total_pemesanan.ttl_kg AS kgs_pesan',
                'total_pemesanan.ttl_cns',
                'master_material.jenis',
                '(po_tambahan.poplus_mc_kg + plus_pck_kg) AS qty_po_plus',
                'stock.kgs_stock_awal',
                'stock.lot_awal',
                'stock.kgs_in_out',
                'stock.lot_stock'
            ])
            ->join('stock', 'stock.id_stock = pengeluaran.id_stock', 'left')
            ->join('out_celup',      'out_celup.id_out_celup = pengeluaran.id_out_celup',                   'left')
            ->join('schedule_celup', 'schedule_celup.id_celup     = out_celup.id_celup',                 'left')
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
                'master_order.lco_date AS tgl_po',
                'NULL AS qty_po_plus',
                'NULL   AS kgs_stock_awal',
                'NULL   AS lot_awal',
                'NULL   AS kgs_in_out',
                'NULL   AS lot_stock'
            ])
            ->join('out_celup',      'out_celup.id_out_celup = other_out.id_out_celup',                      'left')
            ->join('schedule_celup', 'schedule_celup.id_celup     = out_celup.id_celup',                'left')
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
        if ($jenis) {
            foreach ([$b1, $b2, $b3, $b4] as $sub) {
                $sub->where('master_material.jenis', $jenis);
            }

            if (in_array(strtoupper($jenis), ['SPANDEX', 'KARET'])) {
                // Untuk SPANDEX / KARET
                $b1->where('pengeluaran.id_out_celup', null)
                    ->where('pengeluaran.id_psk IS NOT NULL', null, false);
                $b2->where('pengeluaran.id_out_celup', null)
                    ->where('pengeluaran.id_psk IS NOT NULL', null, false);

                // other_out pakai out_celup
                $b3->where('out_celup.id_out_celup', null);
                // ->where('other_out.id_psk IS NOT NULL', null, false);
                $b4->where('out_celup.id_out_celup', null);
                // ->where('other_out.id_psk IS NOT NULL', null, false);
            } elseif (in_array(strtoupper($jenis), ['BENANG', 'NYLON'])) {
                // Untuk BENANG / NYLON
                $b1->where('pengeluaran.id_out_celup IS NOT NULL', null, false)
                    ->where('pengeluaran.id_psk', null);
                $b2->where('pengeluaran.id_out_celup IS NOT NULL', null, false)
                    ->where('pengeluaran.id_psk', null);

                // other_out pakai out_celup
                $b3->where('out_celup.id_out_celup IS NOT NULL', null, false);
                // ->where('other_out.id_psk', null);
                $b4->where('out_celup.id_out_celup IS NOT NULL', null, false);
                // ->where('other_out.id_psk', null);
            }
        }


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
        if ($tanggal_awal)  $conds[] = "all_data.tgl_pakai >= '$tanggal_awal'";
        if ($tanggal_akhir) $conds[] = "all_data.tgl_pakai <= '$tanggal_akhir'";

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
        $sql .= ' ORDER BY all_data.tgl_pakai DESC';


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
            cluster.group,
            stock_dipinjam.no_model AS model_dipinjam,
            stock_dipinjam.item_type AS item_type_dipinjam,
            stock_dipinjam.kode_warna AS kode_warna_dipinjam,
            stock_dipinjam.warna AS warna_dipinjam
        ")
            ->join('pengeluaran', 'pengeluaran.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('out_celup', 'out_celup.id_out_celup = pengeluaran.id_out_celup', 'left')
            ->join('cluster', 'cluster.nama_cluster = pengeluaran.nama_cluster', 'left')
            ->join('total_pemesanan', 'total_pemesanan.id_total_pemesanan = pemesanan.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_material', 'master_material.item_type = material.item_type', 'left')
            ->join('master_order', 'master_order.id_order = material.id_order', 'left')
            ->join('history_stock', "history_stock.id_stock_new = pengeluaran.id_stock AND history_stock.keterangan = 'Pindah Order'", 'left')
            ->join('pemasukan', 'pemasukan.id_stock = history_stock.id_stock_new AND pemasukan.id_out_celup = pengeluaran.id_out_celup', 'left')
            ->join('stock AS stock_dipinjam', 'stock_dipinjam.id_stock = history_stock.id_stock_old', 'left')
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

        $builder->groupBy('pengeluaran.id_pengeluaran')
            ->groupBy('pemesanan.id_total_pemesanan')
            ->orderBy('pengeluaran.area_out, pengeluaran.nama_cluster', 'ASC');

        // // 🔍 tampilkan query mentah (debug)
        // $sql = $builder->getCompiledSelect();
        // echo "<pre>$sql</pre>";
        // exit;

        // return hasil query (non-debug)
        return $builder->get()->getResultArray();
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

    public function validateDeliveryData(array $data): array
    {
        $builder = $this->db
            ->table('pengeluaran as p')
            ->distinct()
            ->select([
                'p.*',
                'pe.tgl_pakai',
                // jika ada di schedule_celup ambil sc.no_model, jika tidak ambil mo.no_model
                'mo.no_model',
                'm.item_type',
                'm.kode_warna',
                'm.color AS warna',
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
            ->where('p.status', $data['status'])
            ->where('pe.tgl_pakai', $data['tgl_pakai'])
            ->where('mm.jenis', $data['jenis'])
            ->where('p.area_out', $data['area']);
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

        // kondisi tambahan khusus status tertentu
        if ($data['status'] === 'Pengiriman Area') {
            $builder->where('p.kgs_out >', 0);
        }

        $builder->groupBy('p.id_pengeluaran')
            ->orderBy('mo.no_model');

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
            ->select('
            p.tgl_out,
            p.area_out,
            p.kgs_out,
            p.cns_out,
            p.krg_out,
            p.lot_out,
            p.nama_cluster,
            p.status,
            p.admin,
            p.keterangan_gbn,
            mo.no_model,
            m.item_type,
            m.kode_warna,
            m.color AS warna,
            mm.jenis,
            pem.po_tambahan
        ')
            ->join('pemesanan pem', 'pem.id_total_pemesanan = p.id_total_pemesanan', 'left')
            ->join('material m', 'm.id_material = pem.id_material', 'left')
            ->join('master_order mo', 'mo.id_order = m.id_order', 'left')
            ->join('master_material mm', 'm.item_type = mm.item_type', 'left')
            ->where('mo.no_model', $key);

        if (!empty($jenis)) {
            $builder->where('mm.jenis', $jenis);
        }

        return $builder
            ->groupBy('p.id_pengeluaran')
            ->orderBy('p.tgl_out, m.item_type, m.kode_warna', 'ASC')
            ->get()
            ->getResultArray();
    }

    // public function getPakaiArea($key, $jenis = null)
    // {
    //     $builder = $this->db->table('pengeluaran p')
    //         ->select('p.*, oc.no_model, s.item_type, s.kode_warna, s.warna, mm.jenis, pem.po_tambahan')
    //         ->join('out_celup oc', 'oc.id_out_celup = p.id_out_celup', 'left')
    //         ->join('master_order mo', 'mo.no_model = oc.no_model', 'left')
    //         // ->join('schedule_celup sc', 'oc.id_celup = sc.id_celup', 'left')
    //         ->join('stock s', 's.no_model = mo.no_model', 'left')
    //         ->join('master_material mm', 's.item_type = mm.item_type', 'left')
    //         ->join('pemesanan pem', 'pem.id_total_pemesanan = p.id_total_pemesanan', 'left')
    //         ->where('oc.no_model', $key);

    //     if (!empty($jenis)) {
    //         $builder->where('mm.jenis', $jenis);
    //     }

    //     return $builder
    //         ->groupBy('p.id_pengeluaran')
    //         ->orderBy('p.tgl_out, s.item_type, s.kode_warna', 'ASC')
    //         ->get()
    //         ->getResultArray();
    // }

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

    public function getFilterSisaPakaiNylon($bulan = null, $noModel = null, $kodeWarna = null)
    {
        $builder = $this->select('
        mo.no_model, material.item_type, material.kode_warna, material.color, stock.kgs_stock_awal, stock.lot_awal,
        mo.lco_date, mo.foll_up, mo.no_order, mo.buyer, mo.delivery_awal, mo.delivery_akhir, mo.unit,
        pengeluaran.area_out, pengeluaran.kgs_out,
        open_po.kg_po,
        retur.kgs_retur, retur.lot_retur,
        mm.jenis
    ')
            ->join('pemesanan', 'pemesanan.id_total_pemesanan = pengeluaran.id_total_pemesanan', 'left')
            ->join('material', 'material.id_material = pemesanan.id_material', 'left')
            ->join('master_order AS mo', 'mo.id_order = material.id_order')
            ->join('master_material AS mm', 'mm.item_type = material.item_type')
            ->join('open_po', 'open_po.no_model = mo.no_model AND open_po.item_type = material.item_type AND open_po.kode_warna = material.kode_warna', 'left')
            ->join('retur', 'retur.no_model = mo.no_model AND retur.item_type = material.item_type AND retur.kode_warna = material.kode_warna', 'left')
            ->where('mm.jenis', 'NYLON')
            ->groupBy('pengeluaran.id_pengeluaran');

        if (!empty($noModel)) {
            $builder->where('mo.no_model', $noModel);
        }

        if (!empty($kodeWarna)) {
            $builder->where('material.kode_warna', $kodeWarna);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(mo.delivery_awal)', $bulan);
        }

        return $builder->get()->getResultArray();
    }
    public function getQtyKirim($data)
    {
        $area       = $data['area'] ?? '';
        $noModels   = (array) ($data['no_model'] ?? []);
        $itemTypes  = (array) ($data['item_type'] ?? []);
        $kodeWarnas = (array) ($data['kode_warna'] ?? []);

        if (empty($noModels) || empty($itemTypes) || empty($kodeWarnas)) {
            return [];
        }

        $noModelPlaceholders   = implode(',', array_fill(0, count($noModels), '?'));
        $itemTypePlaceholders  = implode(',', array_fill(0, count($itemTypes), '?'));
        $kodeWarnaPlaceholders = implode(',', array_fill(0, count($kodeWarnas), '?'));

        $sql = "
        SELECT 
            mo.no_model,
            m.item_type,
            m.kode_warna,
            COALESCE(SUM(p_sub.total_kgs_out), 0) AS total_kgs_out
        FROM (
            SELECT 
                p.id_total_pemesanan,
                SUM(p.kgs_out) AS total_kgs_out
            FROM pengeluaran p
            WHERE p.area_out = ?
              AND p.status = 'Pengiriman Area'
            GROUP BY p.id_total_pemesanan
        ) AS p_sub
        JOIN total_pemesanan tp ON tp.id_total_pemesanan = p_sub.id_total_pemesanan
        JOIN pemesanan pm ON pm.id_total_pemesanan = tp.id_total_pemesanan
        JOIN material m ON m.id_material = pm.id_material
        JOIN master_order mo ON mo.id_order = m.id_order
        WHERE mo.no_model IN ($noModelPlaceholders)
          AND m.item_type IN ($itemTypePlaceholders)
          AND m.kode_warna IN ($kodeWarnaPlaceholders)
        GROUP BY mo.no_model, m.item_type, m.kode_warna
        ORDER BY mo.no_model, m.item_type, m.kode_warna
    ";

        $params = array_merge([$area], $noModels, $itemTypes, $kodeWarnas);

        return $this->db->query($sql, $params)->getResultArray();
    }
}
