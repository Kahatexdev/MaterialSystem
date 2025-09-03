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

        // Gunakan EXISTS untuk mencegah duplikasi bila ada banyak pemasukan per stock
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

        return $this->select("
            cluster.kapasitas,
            {$totalQty},
            cluster.nama_cluster,
            CONCAT('[', COALESCE({$detailKarungSub}, ''), ']') AS detail_karung,
            RIGHT(cluster.nama_cluster, 3) AS simbol_cluster,
            {$detailData}
        ")
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->groupStart()
            ->groupStart()
            ->like('cluster.nama_cluster', 'I.%.09.%', 'after')
            ->where('cluster.nama_cluster >=', 'I.A.09.a')
            ->where('cluster.nama_cluster <=', 'I.B.09.b')
            ->groupEnd()
            ->orGroupStart()
            ->like('cluster.nama_cluster', 'I.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.08.%', 'after')
            ->orLike('cluster.nama_cluster', 'I.%.09.%', 'after')
            ->groupEnd()
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }

    // public function getClusterGroupI()
    // {
    //     $detailKarungSub = "(
    //     SELECT GROUP_CONCAT(
    //                DISTINCT JSON_OBJECT(
    //                    'no_model', oc.no_model,
    //                    'no_karung', oc.no_karung,
    //                    'kgs_kirim', oc.kgs_kirim,
    //                    'lot_kirim', oc.lot_kirim
    //                ) ORDER BY oc.no_karung SEPARATOR ','
    //            )
    //     FROM out_celup oc
    //     JOIN pemasukan pm ON pm.id_out_celup = oc.id_out_celup
    //     JOIN stock st2 ON st2.id_stock = pm.id_stock
    //     WHERE st2.nama_cluster = cluster.nama_cluster
    //     AND pm.out_jalur = '0'
    // )";
    //     return $this->select('
    //             cluster.kapasitas,
    //             ROUND(COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0), 2) AS total_qty,
    //             cluster.nama_cluster,
    //             CONCAT("[", COALESCE(' . $detailKarungSub . ', ""), "]") AS detail_karung,
    //             RIGHT(cluster.nama_cluster, 3) AS simbol_cluster,
    //             GROUP_CONCAT(DISTINCT
    //                 JSON_OBJECT(
    //                     "no_model", stock.no_model,
    //                     "kode_warna", stock.kode_warna,
    //                     "foll_up", master_order.foll_up,
    //                     "delivery", master_order.delivery_awal,
    //                     "qty", ROUND(stock.kgs_stock_awal + stock.kgs_in_out, 2)
    //                 ) ORDER BY stock.no_model SEPARATOR ","
    //             ) AS detail_data
    //         ')
    //         ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
    //         ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
    //         ->groupStart()
    //         ->groupStart()
    //         ->like('cluster.nama_cluster', 'I.%.09.%', 'after')
    //         ->where('cluster.nama_cluster >=', 'I.A.09.a')
    //         ->where('cluster.nama_cluster <=', 'I.B.09.b')
    //         ->groupEnd()
    //         ->orGroupStart()
    //         ->like('cluster.nama_cluster', 'I.%.01.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.02.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.03.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.04.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.05.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.06.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.07.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.08.%', 'after')
    //         ->orLike('cluster.nama_cluster', 'I.%.09.%', 'after')
    //         ->groupEnd()
    //         ->groupEnd()
    //         ->groupBy('cluster.nama_cluster')
    //         ->findAll();
    // }

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
                      ROUND(COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0), 2) AS total_qty, 
                      cluster.nama_cluster, 
                      {$totalQty},
                      CONCAT('[', COALESCE({$detailKarungSub}, ''), ']') AS detail_karung,
                        CASE
                        WHEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2) REGEXP '^(10|11|12|13|14|15|16)\\.[AB]$'
                        THEN SUBSTRING_INDEX(cluster.nama_cluster, '.', -2)
                        ELSE RIGHT(cluster.nama_cluster, 3)
                        END AS simbol_cluster,
                      {$detailData}"
        )
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->GroupStart()
            ->like('cluster.nama_cluster', 'III.%.01.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.02.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.03.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.04.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.05.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.06.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.07.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.08.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.09.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.10.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.11.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.12.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.13.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.14.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.15.%', 'after')
            ->orLike('cluster.nama_cluster', 'III.%.16.%', 'after')
            ->groupEnd()
            ->groupBy('cluster.nama_cluster')
            ->findAll();
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
        return $this->select(
            'cluster.kapasitas, 
                      ROUND(COALESCE(SUM(stock.kgs_stock_awal + stock.kgs_in_out), 0), 2) AS total_qty, 
                      cluster.nama_cluster, 
                      CASE
                      WHEN SUBSTRING_INDEX(cluster.nama_cluster, ".", -2) REGEXP "^(10|11)\\.[AB]$" 
                      THEN SUBSTRING_INDEX(cluster.nama_cluster, ".", -2)
                      ELSE RIGHT(cluster.nama_cluster, 3)
                      END AS simbol_cluster,
                      GROUP_CONCAT(DISTINCT 
            JSON_OBJECT(
                "no_model", stock.no_model,
                "kode_warna", stock.kode_warna,
                "foll_up", master_order.foll_up,
                "delivery", master_order.delivery_awal,
                "qty", ROUND(stock.kgs_stock_awal + stock.kgs_in_out, 2)
            ) ORDER BY stock.no_model SEPARATOR ","
        ) AS detail_data'
        )
            ->join('stock', 'stock.nama_cluster = cluster.nama_cluster', 'left')
            ->join('master_order', 'master_order.no_model = stock.no_model', 'left')
            ->where('cluster.group', 'COV')
            ->groupBy('cluster.nama_cluster')
            ->findAll();
    }
}
