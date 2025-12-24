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
        foreach ($rows as &$row) {
            $id = $row['id_out_celup'];

            $other   = $agg['other'][$id]   ?? ['kgs_other' => 0, 'cns_other' => 0];
            $obc     = $agg['obc'][$id]     ?? ['kgs_out_by_cns' => 0, 'cns_out_by_cns' => 0];
            $history = $agg['history'][$id] ?? [
                'kgs_pindah' => 0,
                'kgs_pinjam' => 0,
                'kgs_retur'  => 0,
                'cns_pindah' => 0,
                'cns_pinjam' => 0,
                'cns_retur'  => 0
            ];

            $row['max_kgs_kirim'] = max(0, round(
                $row['kgs_kirim']
                - $other['kgs_other']
                - $obc['kgs_out_by_cns']
                - $history['kgs_pindah']
                - $history['kgs_pinjam']
                - $history['kgs_retur'],
                2
            ));

            $row['max_cones_kirim'] = max(0,
                $row['cones_kirim']
                - $other['cns_other']
                - $obc['cns_out_by_cns']
                - $history['cns_pindah']
                - $history['cns_pinjam']
                - $history['cns_retur']
            );
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
            return ['other' => [], 'obc' => [], 'history' => []];
        }

        $other = $this->db->table('other_out')
            ->select('id_out_celup, SUM(kgs_other_out) kgs_other, SUM(cns_other_out) cns_other')
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        $obc = $this->db->table('pengeluaran')
            ->select('id_out_celup, SUM(kgs_out) kgs_out_by_cns, SUM(cns_out) cns_out_by_cns')
            ->where('krg_out', 0)
            ->where('status', 'Pengiriman Area')
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        $history = $this->db->table('history_stock')
            ->select("
                id_out_celup,
                SUM(CASE WHEN keterangan = 'Pindah Order' THEN kgs ELSE 0 END) AS kgs_pindah,
                SUM(CASE WHEN keterangan = 'Pinjam Order' THEN kgs ELSE 0 END) AS kgs_pinjam,
                SUM(CASE WHEN keterangan LIKE 'Retur Celup%' THEN kgs ELSE 0 END) AS kgs_retur,
                SUM(CASE WHEN keterangan = 'Pindah Order' THEN cns ELSE 0 END) AS cns_pindah,
                SUM(CASE WHEN keterangan = 'Pinjam Order' THEN cns ELSE 0 END) AS cns_pinjam,
                SUM(CASE WHEN keterangan LIKE 'Retur Celup%' THEN cns ELSE 0 END) AS cns_retur
            ")
            ->whereIn('id_out_celup', $idOutCelups)
            ->groupBy('id_out_celup')
            ->get()->getResultArray();

        return [
            'other'   => array_column($other, null, 'id_out_celup'),
            'obc'     => array_column($obc, null, 'id_out_celup'),
            'history' => array_column($history, null, 'id_out_celup'),
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

}
