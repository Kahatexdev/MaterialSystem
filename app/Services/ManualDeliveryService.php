<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Session\Session;

class ManualDeliveryService
{
    protected ConnectionInterface $db;
    protected Session $session;

    public function __construct(ConnectionInterface $db, Session $session)
    {
        $this->db      = $db;
        $this->session = $session;
    }

    /* =====================================================
     * PUBLIC API
     * ===================================================== */
    public function addToSession(array $validatedRows, string $status, string $username): array
    {
        if (empty($validatedRows)) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Tidak ada data valid'
            ];
        }

        $idOutCelups = $this->extractUniqueIds($validatedRows, 'id_out_celup');
        $aggregates  = $this->getAggregatedData($idOutCelups);

        $validatedRows = $this->applyMaxCalculation($validatedRows, $aggregates);

        $sessionData = $this->session->get('manual_delivery') ?? [];

        /** BUILD HASH MAP DARI SESSION */
        $existingMap = [];
        foreach ($sessionData as $item) {
            $key = $this->makeDuplicateKey($item);
            $existingMap[$key] = true;
        }

        $added = 0;

        foreach ($validatedRows as $row) {
            $key = $this->makeDuplicateKey($row);

            if (isset($existingMap[$key])) {
                continue; // DUPLIKAT
            }

            $sessionData[] = $this->buildSessionRow($row, $status, $username);
            $existingMap[$key] = true; // tandai sudah ada
            $added++;
        }

        $this->session->set('manual_delivery', $sessionData);


        if ($added === 0) {
            return [
                'success' => false,
                'code'    => 409,
                'message' => 'Semua data sudah ada di session'
            ];
        }

        return [
            'success' => true,
            'code'    => 200,
            'message' => "{$added} record berhasil ditambahkan"
        ];
    }

    /* =====================================================
     * CORE LOGIC
     * ===================================================== */

    protected function applyMaxCalculation(array $rows, array $agg): array
    {
        // foreach ($rows as &$row) {
        //     $id = $row['id_out_celup'];

        //     $other   = $agg['other'][$id]   ?? ['kgs_other' => 0, 'cns_other' => 0];
        //     $obc     = $agg['obc'][$id]     ?? ['kgs_out_by_cns' => 0, 'cns_out_by_cns' => 0];
        //     $history = $agg['history'][$id] ?? [
        //         'kgs_pindah' => 0,
        //         'kgs_retur'  => 0,
        //         'cns_pindah' => 0,
        //         'cns_retur'  => 0
        //     ];

        //     $row['max_kgs_kirim'] = max(0, round(
        //         $row['kgs_kirim']
        //             - $other['kgs_other']
        //             - $obc['kgs_out_by_cns']
        //             - $history['kgs_pindah']
        //             - $history['kgs_retur'],
        //         2
        //     ));

        //     $row['max_cones_kirim'] = max(
        //         0,
        //         $row['cones_kirim']
        //             - $other['cns_other']
        //             - $obc['cns_out_by_cns']
        //             - $history['cns_pindah']
        //             - $history['cns_retur']
        //     );
        // }

        foreach ($rows as &$row) {
            $idOut = $row['id_out_celup'];
            $idPeng = $row['id_pengeluaran'] ?? null;
            $other   = $agg['other'][$idOut]   ?? ['kgs_other' => 0, 'cns_other' => 0];
            // $obc     = $agg['obc'][$idOut]     ?? ['kgs_out_by_cns' => 0, 'cns_out_by_cns' => 0];
            $history = $agg['history'][$idOut] ?? [
                'kgs_pindah' => 0,
                'kgs_retur'  => 0,
                'cns_pindah' => 0,
                'cns_retur'  => 0
            ];

            $baseKgs = (float) $row['kgs_kirim'];
            $baseCns = (float) $row['cones_kirim'];

            // 🔹 TOTAL semua pengeluaran (DB)
            $totalKgs = $agg['obc_total'][$idOut]['total_kgs_out'] ?? 0;
            $totalCns = $agg['obc_total'][$idOut]['total_cns_out'] ?? 0;

            // 🔹 KGS row ini (kalau ada di DB)
            $rowKgs = 0;
            if ($idPeng && isset($agg['obc_detail'][$idPeng])) {
                $rowKgs = (float) $agg['obc_detail'][$idPeng]['kgs_out'];
            }
            $rowCns = 0;
            if ($idPeng && isset($agg['obc_detail'][$idPeng])) {
                $rowCns = $agg['obc_detail'][$idPeng]['cns_out'];
            }


            $row['max_kgs_kirim'] = max(0, round(
                $baseKgs - $totalKgs  - $other['kgs_other'] - $history['kgs_pindah'] - $history['kgs_retur']  + $rowKgs,
                2
            ));

            $row['max_cones_kirim'] = max(0, round(
                $baseCns - ($totalCns  - $other['cns_other'] - $history['cns_pindah'] - $history['cns_retur'])  + $rowCns,
                2
            ));
        }

        return $rows;
    }

    protected function buildSessionRow(array $row, string $status, string $username): array
    {
        return [
            '_dup_key'           => $this->makeDuplicateKey($row),
            'id_pengeluaran'     => $row['id_pengeluaran'] ?? null,
            'id_out_celup'       => $row['id_out_celup'],
            'tgl_pakai'          => $row['tgl_pakai'],
            'no_model'           => $row['no_model'] ?? '',
            'item_type'          => $row['item_type'] ?? '',
            'jenis'              => $row['jenis'] ?? '',
            'kode_warna'         => $row['kode_warna'] ?? '',
            'warna'              => $row['warna'] ?? '',
            'area_out'           => $row['area_out'],
            'no_karung'          => $row['no_karung'],
            'tgl_out'            => $row['tgl_out'],
            'max_kgs'            => $row['max_kgs_kirim'],
            'max_cns'            => $row['max_cones_kirim'],
            'kgs_out'            => ($row['status'] ?? '') === 'Pengiriman Area' ? 0 : ($row['kgs_out'] ?? 0),
            'cns_out'            => ($row['status'] ?? '') === 'Pengiriman Area' ? 0 : ($row['cns_out'] ?? 0),
            'krg_out'            => 0,
            'lot_out'            => ($row['status'] ?? '') === 'Pengiriman Area' ? '' : ($row['lot_out'] ?? ''),
            'nama_cluster'       => $row['nama_cluster'] ?? '',
            'admin'              => $username,
            'status_pengeluaran' => $status,
            // BASE (TIDAK PERNAH BERUBAH)
            'kgs_kirim' => $row['kgs_kirim'],     // ASLI DARI DB
            'cones_kirim' => $row['cones_kirim'],   // ASLI DARI DB
        ];
    }

    /* =====================================================
     * HELPERS
     * ===================================================== */

    protected function extractUniqueIds(array $rows, string $key): array
    {
        return array_values(array_unique(array_column($rows, $key)));
    }

    /* =====================================================
     * AGGREGATE QUERIES
     * ===================================================== */

    protected function getAggregatedData(array $idOutCelups): array
    {
        if (empty($idOutCelups)) {
            return ['other' => [], 'obc_total' => [], 'obc_detail' => [], 'history' => []];
        }

        $other = $this->db->table('other_out')
            ->select('id_out_celup, SUM(kgs_other_out) kgs_other, SUM(cns_other_out) cns_other')
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        $obcTotal = $this->db->table('pengeluaran')
            ->select('id_out_celup, SUM(kgs_out) total_kgs_out, SUM(cns_out) total_cns_out')
            ->where('krg_out', 0)
            // ->where('status', 'Pengiriman Area')
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        $obcDetail = $this->db->table('pengeluaran')
            ->select('
            id_pengeluaran,
            id_out_celup,
            kgs_out,
            cns_out
        ')
            ->where('krg_out', 0)
            ->where('status', 'Pengiriman Area')
            ->whereIn('id_out_celup', $idOutCelups)
            ->get()->getResultArray();

        $history = $this->db->table('history_stock')
            ->select("
                id_out_celup,
                SUM(CASE WHEN keterangan = 'Pindah Order' THEN kgs ELSE 0 END) AS kgs_pindah,
                SUM(CASE WHEN keterangan LIKE 'Retur Celup%' THEN kgs ELSE 0 END) AS kgs_retur,
                SUM(CASE WHEN keterangan = 'Pindah Order' THEN cns ELSE 0 END) AS cns_pindah,
                SUM(CASE WHEN keterangan LIKE 'Retur Celup%' THEN cns ELSE 0 END) AS cns_retur
            ")
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        return [
            'other'      => array_column($other, null, 'id_out_celup'),
            'obc_total'  => array_column($obcTotal, null, 'id_out_celup'),
            'obc_detail' => array_column($obcDetail, null, 'id_pengeluaran'),
            'history'    => array_column($history, null, 'id_out_celup'),
        ];
    }

    protected function makeDuplicateKey(array $row): string
    {
        return implode('|', [
            $row['id_out_celup'],
            $row['id_pengeluaran'] ?? 0,
            $row['area_out'],
            $row['tgl_out'],
        ]);
    }

    public function updateKgsOut($idPeng, float $kgsOut, int $cnsOut): void
    {
        $sessionData = $this->session->get('manual_delivery') ?? [];

        foreach ($sessionData as &$row) {
            if (
                isset($row['id_pengeluaran']) &&
                (string)$row['id_pengeluaran'] === (string)$idPeng
            ) {
                $row['kgs_out'] = $kgsOut;
                $row['cns_out'] = $cnsOut;
                break;
            }
        }
        unset($row);

        $this->session->set('manual_delivery', $sessionData);
    }

    public function recalculateMax(): void
    {
        $sessionData = session()->get('manual_delivery') ?? [];
        if (empty($sessionData)) return;

        $idOutCelups = $this->extractUniqueIds($sessionData, 'id_out_celup');
        $agg = $this->getAggregatedData($idOutCelups);

        $sessionIndex = [];
        foreach ($sessionData as $i => $row) {
            if (isset($row['_dup_key'])) {
                $sessionIndex[$row['_dup_key']] = $i;
            }
        }

        $sessionTotal = [];
        foreach ($sessionData as $row) {
            if (!isset($row['id_out_celup'])) continue;

            $idOut = $row['id_out_celup'];

            if (!isset($sessionTotal[$idOut])) {
                $sessionTotal[$idOut] = [
                    'kgs' => 0,
                    'cns' => 0
                ];
            }

            $sessionTotal[$idOut]['kgs'] += (float) ($row['kgs_out'] ?? 0);
            $sessionTotal[$idOut]['cns'] += (int)   ($row['cns_out'] ?? 0);
        }

        foreach ($sessionData as $row) {

            if (!isset($row['id_out_celup'], $row['_dup_key'])) {
                continue;
            }

            $idOut = $row['id_out_celup'];
            $dupKey = $row['_dup_key'];

            $other   = $agg['other'][$idOut]   ?? ['kgs_other' => 0, 'cns_other' => 0];
            $history = $agg['history'][$idOut] ?? [
                'kgs_pindah' => 0,
                'kgs_retur'  => 0,
                'cns_pindah' => 0,
                'cns_retur'  => 0
            ];

            $baseKgs = (float) $row['kgs_kirim'];
            $baseCns = (float) $row['cones_kirim'];

            $rowKgs = (float) $row['kgs_out'];
            $rowCns = (int) $row['cns_out'];

            $totalSessionKgs = $sessionTotal[$idOut]['kgs'] ?? 0;
            $totalSessionCns = $sessionTotal[$idOut]['cns'] ?? 0;

            $maxKgs = max(0, round(
                ($baseKgs - $totalSessionKgs  - $other['kgs_other'] - $history['kgs_pindah'] - $history['kgs_retur']) + $rowKgs,
                2
            ));

            $maxCns = max(
                0,
                ($baseCns - $totalSessionCns - $other['cns_other'] - $history['cns_pindah'] - $history['cns_retur']) + $rowCns
            );

            $idx = $sessionIndex[$dupKey] ?? null;
            if ($idx !== null) {
                $sessionData[$idx]['max_kgs'] = $maxKgs;
                $sessionData[$idx]['max_cns'] = $maxCns;
            }
        }

        session()->set('manual_delivery', $sessionData);
    }
}
