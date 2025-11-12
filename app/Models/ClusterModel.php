<?php

namespace App\Models;

use CodeIgniter\Model;

class ClusterModel extends Model
{
    protected $table            = 'cluster';
    protected $primaryKey       = 'nama_cluster';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_cluster',
        'kapasitas',
        'keterangan',
        'group',
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

    public function getDataCluster()
    {
        return $this->findAll();
    }

    // public function getCluster($kgs)
    // {
    //     return $this->db->table('cluster') // Gunakan nama tabel langsung
    //         ->select('cluster.nama_cluster, (cluster.kapasitas - IFNULL(SUM(stock.kgs_stock_awal), 0) - IFNULL(SUM(stock.kgs_in_out), 0)) AS sisa_kapasitas', false)
    //         ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
    //         ->groupBy('cluster.nama_cluster')
    //         ->having('sisa_kapasitas >=', $kgs, false) // Filter kapasitas lebih dari $kgs
    //         ->orderBy('cluster.nama_cluster', 'ASC')
    //         ->get()
    //         ->getResultArray();
    // }

    public function getCluster($kgs)
    {
        // Buat subquery manual sebagai string
        $subquery = '(SELECT pemasukan.id_stock, SUM(COALESCE(other_out.kgs_other_out, 0)) as total_other_out 
                  FROM pemasukan 
                  LEFT JOIN other_out ON other_out.id_out_celup = pemasukan.id_out_celup 
                  GROUP BY pemasukan.id_stock) AS sub_other_out';

        return $this->db->table('cluster')
            ->select('
            cluster.nama_cluster,
            (
                cluster.kapasitas 
                - IFNULL(SUM(stock.kgs_stock_awal), 0)
                + IFNULL(SUM(sub_other_out.total_other_out), 0)
                - IFNULL(SUM(stock.kgs_in_out), 0)
            ) AS sisa_kapasitas', false)
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join($subquery, 'sub_other_out.id_stock = stock.id_stock', 'left') // Subquery join manual
            ->groupBy('cluster.nama_cluster')
            ->having('sisa_kapasitas >=', $kgs, false)
            ->orderBy('cluster.nama_cluster', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getClusterGroupI()
    {
        // --- 1) QTY terpakai dari stock_awal + in_out per cluster ---
        $qtySub = $this->db->table('stock s')
            ->select('
                s.nama_cluster,
                SUM(GREATEST(s.kgs_stock_awal,0) + GREATEST(s.kgs_in_out,0)) AS qty
            ', false)
            ->groupBy('s.nama_cluster')
            ->getCompiledSelect();

        // --- 2) RAW: kapasitas terpakai per (cluster, no_model, item_type, kode_warna) ---
        // SUM(kgs_kirim) sudah per-pasangan agar tidak dobel.
        $kapakaiRaw = $this->db->table('pemasukan pms')
            ->select('s.nama_cluster, oc.no_model, s.item_type, s.kode_warna, SUM(oc.kgs_kirim) AS kgs_model, mo.foll_up,mo.delivery_akhir', false)
            ->join('stock s', 's.id_stock = pms.id_stock', 'inner')
            ->join('out_celup oc', 'oc.id_out_celup = pms.id_out_celup', 'inner')
            ->join('master_order mo', 'mo.no_model = s.no_model', 'left')
            ->where('pms.out_jalur', '0')
            ->groupBy('s.nama_cluster, oc.no_model, s.item_type, s.kode_warna')
            ->getCompiledSelect();

        // --- 3) AGG: total kapasitas_terpakai per cluster (SUM dari raw) ---
        $kapakaiSum = $this->db->table("({$kapakaiRaw}) kmr")
            ->select('kmr.nama_cluster, SUM(kmr.kgs_model) AS kapasitas_pakai', false)
            ->groupBy('kmr.nama_cluster')
            ->getCompiledSelect();

        // --- 4) DETAIL_DATA: concat per cluster dari raw yang sama (no_model|item_type|kode_warna|kgs_model) ---
        $detailDataSub = $this->db->table("({$kapakaiRaw}) kmr")
            ->select("
            kmr.nama_cluster,
            GROUP_CONCAT(
                CONCAT_WS('|', kmr.no_model, kmr.item_type, kmr.kode_warna, kmr.foll_up, kmr.delivery_akhir,ROUND(kmr.kgs_model,2))
                ORDER BY kmr.no_model
                SEPARATOR ','
            ) AS detail_data
        ", false)
            ->groupBy('kmr.nama_cluster')
            ->getCompiledSelect();

        // --- 5) DETAIL_KARUNG: pre-aggregate per cluster agar tidak gandakan ---
        $detailKarungSub = $this->db->table('pemasukan pms')
            ->select("
            s.nama_cluster,
            GROUP_CONCAT(
                DISTINCT CONCAT_WS('|', oc.no_model, oc.no_karung, ROUND(oc.kgs_kirim,2), oc.lot_kirim)
                ORDER BY oc.no_model, oc.no_karung
                SEPARATOR ','
            ) AS detail_karung
        ", false)
            ->join('stock s', 's.id_stock = pms.id_stock', 'inner')
            ->join('out_celup oc', 'oc.id_out_celup = pms.id_out_celup', 'inner')
            ->where('pms.out_jalur', '0')
            ->groupBy('s.nama_cluster')
            ->getCompiledSelect();

        // --- 6) QUERY UTAMA: join hasil agregat per cluster ---
        return $this->db->table('cluster')
            ->select("
            cluster.nama_cluster,
            cluster.kapasitas,
            COALESCE(ROUND(q.qty,2), 0) AS total_qty,
            COALESCE(ROUND(kms.kapasitas_pakai, 2), 0) AS kapasitas_pakai,

            /* simbol: COL.ROW.SIDE -> A.09.A */
            CONCAT(
                SUBSTRING_INDEX(cluster.nama_cluster, '.', 0),             /* COL */
                LPAD(
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(cluster.nama_cluster, '.', 3), '.', -1) AS UNSIGNED),
                    1, '0'                                                /* ROW 01..09 */
                ),
                '.',
                SUBSTRING_INDEX(cluster.nama_cluster, '.', -1)            /* SIDE A/B */
            ) AS simbol_cluster,

            COALESCE(dd.detail_data, '')   AS detail_data,
            COALESCE(dk.detail_karung, '') AS detail_karung
        ", false)
            ->join("({$qtySub}) q",  'q.nama_cluster  = cluster.nama_cluster', 'left', false)
            ->join("({$kapakaiSum}) kms", 'kms.nama_cluster = cluster.nama_cluster', 'left', false)
            ->join("({$detailDataSub}) dd", 'dd.nama_cluster = cluster.nama_cluster', 'left', false)
            ->join("({$detailKarungSub}) dk", 'dk.nama_cluster = cluster.nama_cluster', 'left', false)
            ->where('cluster.`group`', 'I')
            // aman untuk ONLY_FULL_GROUP_BY
            ->groupBy('cluster.nama_cluster, cluster.kapasitas, q.qty, kms.kapasitas_pakai, dd.detail_data, dk.detail_karung')
            ->orderBy('cluster.nama_cluster', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getClusterGroupII()
    {
        $detailKarungSub = "(
        SELECT GROUP_CONCAT(
                    JSON_OBJECT(
                       'no_model', oc.no_model,
                       'no_karung', oc.no_karung,
                       'kgs_kirim', oc.kgs_kirim,
                       'lot_kirim', oc.lot_kirim
                   ) ORDER BY oc.no_karung SEPARATOR ','
               )
        FROM out_celup oc
        JOIN pemasukan pm ON pm.id_out_celup = oc.id_out_celup
        JOIN stock st2 ON st2.id_stock = pm.id_stock
        WHERE st2.nama_cluster = cluster.nama_cluster
        AND pm.out_jalur = '0'
        )";
        $totalQty = "ROUND(COALESCE(SUM(
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM pemasukan pm2 
                    WHERE pm2.id_stock = stock.id_stock 
                    AND pm2.out_jalur = '0'
                )
                THEN (stock.kgs_stock_awal + stock.kgs_in_out)
                ELSE 0
            END
        ), 0), 2) AS total_qty";

        // Untuk detail_data: sertakan JSON_OBJECT hanya jika ada pemasukan out_jalur='0'
        $detailData = "GROUP_CONCAT(DISTINCT
            IF(
                EXISTS (
                    SELECT 1 FROM pemasukan pm3 
                    WHERE pm3.id_stock = stock.id_stock 
                      AND pm3.out_jalur = '0'
                ),
                JSON_OBJECT(
                    'no_model', stock.no_model,
                    'kode_warna', stock.kode_warna,
                    'foll_up', master_order.foll_up,
                    'delivery', master_order.delivery_awal,
                    'qty', ROUND(stock.kgs_stock_awal + stock.kgs_in_out, 2)
                ),
                NULL
            )
            ORDER BY stock.no_model SEPARATOR ','
        ) AS detail_data";

        return $this->select("cluster.kapasitas, 
        ROUND(COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0), 2) AS total_qty, 
        cluster.nama_cluster,
        {$totalQty},
            CONCAT('[', COALESCE({$detailKarungSub}, ''), ']') AS detail_karung,
            CASE
                    WHEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2) REGEXP '^(10|11|12|13|14|15|16)\\.[AB]$'
                        THEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2)
                    WHEN cluster.nama_cluster REGEXP '^II\\.B\\.(10|11|12|13|14|15|16)\\.B\\.[0-9]{2}$'
                        THEN CONCAT('b.', SUBSTRING_INDEX(cluster.nama_cluster, '.', -1))
                    ELSE RIGHT(cluster.nama_cluster, 3)
                END AS simbol_cluster,
            {$detailData}")
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->GroupStart()
            ->like('cluster.nama_cluster', 'II.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.08.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.09.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.10.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.11.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.12.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.13.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.14.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.15.%', 'after')
            ->orLike('cluster.nama_cluster', 'II.%.16.%', 'after')
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }

    public function getClusterGroupIII()
    {
        // --- 0) Filter cluster group III sejak awal (early filter)
        //     Ini akan memangkas baris di stock/pemasukan/out_celup yang tidak perlu.
        $clusterIII = $this->db->table('cluster c3')
            ->select('c3.nama_cluster')
            ->where('c3.`group`', 'III')
            ->getCompiledSelect();

        // --- 1) QTY terpakai: stock_awal + in_out per cluster (hanya cluster group III)
        $qtySub = $this->db->table('stock s')
            ->select("
            s.nama_cluster,
            SUM(GREATEST(s.kgs_stock_awal,0) + GREATEST(s.kgs_in_out,0)) AS qty
        ", false)
            ->join("({$clusterIII}) c3", 'c3.nama_cluster = s.nama_cluster', 'inner', false)
            ->groupBy('s.nama_cluster')
            ->getCompiledSelect();

        // --- 2) RAW kapasitas terpakai per (cluster, no_model, item_type, kode_warna)
        //     SUM(kgs_kirim) sudah per pasangan pms-oc, batasi hanya out_jalur='0'.
        $kapakaiRaw = $this->db->table('pemasukan pms')
            ->select("
            s.nama_cluster,
            oc.no_model,
            s.item_type,
            s.kode_warna,
            SUM(oc.kgs_kirim) AS kgs_model,
            mo.foll_up,
            mo.delivery_akhir
        ", false)
            ->join('stock s', 's.id_stock = pms.id_stock', 'inner')
            ->join("({$clusterIII}) c3", 'c3.nama_cluster = s.nama_cluster', 'inner', false)
            ->join('out_celup oc', 'oc.id_out_celup = pms.id_out_celup', 'inner')
            ->join('master_order mo', 'mo.no_model = s.no_model', 'left')
            ->where('pms.out_jalur', '0')
            ->groupBy('s.nama_cluster, oc.no_model, s.item_type, s.kode_warna, mo.foll_up, mo.delivery_akhir')
            ->getCompiledSelect();

        // --- 3) AGG total kapasitas_terpakai per cluster
        $kapakaiSum = $this->db->table("({$kapakaiRaw}) kmr")
            ->select('kmr.nama_cluster, SUM(kmr.kgs_model) AS kapasitas_pakai', false)
            ->groupBy('kmr.nama_cluster')
            ->getCompiledSelect();

        // --- 4) DETAIL_DATA: concat per cluster dari raw yang sama
        $detailDataSub = $this->db->table("({$kapakaiRaw}) kmr")
            ->select("
            kmr.nama_cluster,
            GROUP_CONCAT(
                CONCAT_WS('|',
                    kmr.no_model,
                    kmr.item_type,
                    kmr.kode_warna,
                    kmr.foll_up,
                    kmr.delivery_akhir,
                    ROUND(kmr.kgs_model,2)
                )
                ORDER BY kmr.no_model
                SEPARATOR ','
            ) AS detail_data
        ", false)
            ->groupBy('kmr.nama_cluster')
            ->getCompiledSelect();

        // --- 5) DETAIL_KARUNG: pre-aggregate baris unik dulu, baru GROUP_CONCAT (tanpa DISTINCT)
        $detailKarungRaw = $this->db->table('pemasukan pms')
            ->select("
            s.nama_cluster,
            oc.no_model,
            oc.no_karung,
            ROUND(oc.kgs_kirim,2) AS kgs_kirim,
            oc.lot_kirim
        ", false)
            ->join('stock s', 's.id_stock = pms.id_stock', 'inner')
            ->join("({$clusterIII}) c3", 'c3.nama_cluster = s.nama_cluster', 'inner', false)
            ->join('out_celup oc', 'oc.id_out_celup = pms.id_out_celup', 'inner')
            ->where('pms.out_jalur', '0')
            // hilangkan duplikasi jika ada multi-join tak sengaja
            ->groupBy('s.nama_cluster, oc.no_model, oc.no_karung, oc.kgs_kirim, oc.lot_kirim')
            ->getCompiledSelect();

        $detailKarungSub = $this->db->table("({$detailKarungRaw}) dkr")
            ->select("
            dkr.nama_cluster,
            GROUP_CONCAT(
                CONCAT_WS('|', dkr.no_model, dkr.no_karung, dkr.kgs_kirim, dkr.lot_kirim)
                ORDER BY dkr.no_model, dkr.no_karung
                SEPARATOR ','
            ) AS detail_karung
        ", false)
            ->groupBy('dkr.nama_cluster')
            ->getCompiledSelect();

        // --- 6) QUERY UTAMA
        return $this->db->table('cluster')
            ->select("
            cluster.nama_cluster,
            cluster.kapasitas,
            COALESCE(ROUND(q.qty,2), 0) AS total_qty,
            COALESCE(ROUND(kms.kapasitas_pakai, 2), 0) AS kapasitas_pakai,

            /* simbol: COL.ROW.SIDE -> A.09.A */
            CONCAT(
                SUBSTRING_INDEX(cluster.nama_cluster, '.', 0),  /* COL */
                LPAD(
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(cluster.nama_cluster, '.', 3), '.', -1) AS UNSIGNED),
                    2, '0'
                ),                                               /* ROW 01..09 */
                '.',
                SUBSTRING_INDEX(cluster.nama_cluster, '.', -1)   /* SIDE A/B */
            ) AS simbol_cluster,

            COALESCE(dd.detail_data, '')   AS detail_data,
            COALESCE(dk.detail_karung, '') AS detail_karung
        ", false)
            ->where('cluster.`group`', 'III')
            ->join("({$qtySub}) q",   'q.nama_cluster   = cluster.nama_cluster', 'left',  false)
            ->join("({$kapakaiSum}) kms", 'kms.nama_cluster = cluster.nama_cluster', 'left',  false)
            ->join("({$detailDataSub}) dd", 'dd.nama_cluster = cluster.nama_cluster', 'left',  false)
            ->join("({$detailKarungSub}) dk", 'dk.nama_cluster = cluster.nama_cluster', 'left',  false)
            // ONLY_FULL_GROUP_BY safe
            ->groupBy('cluster.nama_cluster, cluster.kapasitas, q.qty, kms.kapasitas_pakai, dd.detail_data, dk.detail_karung')
            ->orderBy('cluster.nama_cluster', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getNamaCluster($cluster, $kgs)
    {
        return $this->db->table('cluster') // Gunakan nama tabel langsung
            ->select('cluster.nama_cluster, (cluster.kapasitas - IFNULL(SUM(stock.kgs_stock_awal), 0) - IFNULL(SUM(stock.kgs_in_out), 0)) AS sisa_kapasitas', false)
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->where('cluster.nama_cluster !=', $cluster)
            ->groupBy('cluster.nama_cluster')
            ->having('sisa_kapasitas >=', $kgs, false) // Filter kapasitas lebih dari $kgs
            ->orderBy('cluster.nama_cluster', 'ASC')
            ->get()
            ->getResultArray();
    }
    public function getClusterNylon()
    {
        $detailKarungSub = "(
        SELECT GROUP_CONCAT(
                   DISTINCT JSON_OBJECT(
                       'no_model', oc.no_model,
                       'no_karung', oc.no_karung,
                       'kgs_kirim', oc.kgs_kirim,
                       'lot_kirim', oc.lot_kirim
                   ) ORDER BY oc.no_karung SEPARATOR ','
               )
        FROM out_celup oc
        JOIN pemasukan pm ON pm.id_out_celup = oc.id_out_celup
        JOIN stock st2 ON st2.id_stock = pm.id_stock
        WHERE st2.nama_cluster = cluster.nama_cluster
        AND pm.out_jalur = '0'
    )";

        $totalQty = "ROUND(COALESCE(SUM(
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM pemasukan pm2 
                    WHERE pm2.id_stock = stock.id_stock 
                    AND pm2.out_jalur = '0'
                )
                THEN (stock.kgs_stock_awal + stock.kgs_in_out)
                ELSE 0
            END
        ), 0), 2) AS total_qty";

        // Untuk detail_data: sertakan JSON_OBJECT hanya jika ada pemasukan out_jalur='0'
        $detailData = "GROUP_CONCAT(DISTINCT
            IF(
                EXISTS (
                    SELECT 1 FROM pemasukan pm3 
                    WHERE pm3.id_stock = stock.id_stock 
                      AND pm3.out_jalur = '0'
                ),
                JSON_OBJECT(
                    'no_model', stock.no_model,
                    'kode_warna', stock.kode_warna,
                    'foll_up', master_order.foll_up,
                    'delivery', master_order.delivery_awal,
                    'qty', ROUND(stock.kgs_stock_awal + stock.kgs_in_out, 2)
                ),
                NULL
            )
            ORDER BY stock.no_model SEPARATOR ','
        ) AS detail_data";

        return $this->select(
            "cluster.kapasitas, 
                      ROUND(COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0), 2) AS total_qty, 
                      cluster.nama_cluster, 
                      {$totalQty},
                      CONCAT('[', COALESCE({$detailKarungSub}, ''), ']') AS detail_karung,
                        CASE
                        WHEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2) REGEXP '^(10|11|12|13|14|15|16|17|18|19|20|21|22)\\.[ABCD]$'
                        THEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2)
                        ELSE RIGHT(cluster.nama_cluster, 3)
                        END AS simbol_cluster,
                      {$detailData}"
        )
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            // ->join('out_celup', 'out_celup.no_model = stock.no_model', 'left')
            ->where('cluster.group', 'NYLON')
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }
    public function getClusterCov()
    {
        $detailKarungSub = "(
        SELECT GROUP_CONCAT(
                    JSON_OBJECT(
                       'no_model', oc.no_model,
                       'no_karung', oc.no_karung,
                       'kgs_kirim', oc.kgs_kirim,
                       'lot_kirim', oc.lot_kirim
                   ) ORDER BY oc.no_karung SEPARATOR ','
               )
        FROM out_celup oc
        JOIN pemasukan pm ON pm.id_out_celup = oc.id_out_celup
        JOIN stock st2 ON st2.id_stock = pm.id_stock
        WHERE st2.nama_cluster = cluster.nama_cluster
        AND pm.out_jalur = '0'
    )";
        $totalQty = "ROUND(COALESCE(SUM(
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM pemasukan pm2 
                    WHERE pm2.id_stock = stock.id_stock 
                    AND pm2.out_jalur = '0'
                )
                THEN (stock.kgs_stock_awal + stock.kgs_in_out)
                ELSE 0
            END
        ), 0), 2) AS total_qty";

        // Untuk detail_data: sertakan JSON_OBJECT hanya jika ada pemasukan out_jalur='0'
        $detailData = "GROUP_CONCAT(DISTINCT
            IF(
                EXISTS (
                    SELECT 1 FROM pemasukan pm3 
                    WHERE pm3.id_stock = stock.id_stock 
                      AND pm3.out_jalur = '0'
                ),
                JSON_OBJECT(
                    'no_model', stock.no_model,
                    'kode_warna', stock.kode_warna,
                    'foll_up', master_order.foll_up,
                    'delivery', master_order.delivery_awal,
                    'qty', ROUND(stock.kgs_stock_awal + stock.kgs_in_out, 2)
                ),
                NULL
            )
            ORDER BY stock.no_model SEPARATOR ','
        ) AS detail_data";

        return $this->select(
            "cluster.kapasitas, 
            cluster.nama_cluster, 
            {$totalQty},
            CONCAT('[', COALESCE({$detailKarungSub}, ''), ']') AS detail_karung,
                      CASE
                WHEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2) REGEXP '^(10|11)\\.[AB]$' 
                THEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2)
                ELSE RIGHT(cluster.nama_cluster, 3)
            END AS simbol_cluster,
          {$detailData}"
        )
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->where('cluster.group', 'COV')
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }
}
