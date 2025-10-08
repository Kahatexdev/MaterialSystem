<style>
    .cell {
        border: none;
        padding: 8px 12px;
        margin: 2px;
        border-radius: 8px;
        /* Membuat tombol rounded */
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    /* Warna cell */
    .gray-cell {
        background-color: #b0b0b0;
        color: white;
    }

    .blue-cell {
        background-color: #007bff;
        color: white;
    }

    .orange-cell {
        background-color: #ff851b;
        color: white;
    }

    .red-cell {
        background-color: #dc3545;
        color: white;
    }

    /* Hover effect */
    .cell:hover {
        opacity: 0.8;
    }

    /* Styling table */
    .table-bordered th,
    .table-bordered td {
        border: 2px solid #dee2e6;
        text-align: center;
    }
</style>
<?php
if (!function_exists('ms_makeDetailJson')) {
    function ms_makeDetailJson(string $detailDataStr): string
    {
        $list = [];
        if ($detailDataStr !== '') {
            foreach (explode(',', $detailDataStr) as $p) {
                $p = trim($p);
                if ($p === '') continue;
                // butuh 4 elemen karena ada qty di akhir
                [$noModel, $itemType, $kodeWarna,$follUp,$Deliv, $qty] = array_pad(explode('|', $p), 6, '');
                $list[] = [
                    'no_model'   => $noModel,
                    'item_type'  => $itemType,
                    'kode_warna' => $kodeWarna,
                    'foll_up'    => $follUp,
                    'delivery'   => $Deliv,
                    'kapasitas_pakai'        => is_numeric($qty) ? (float)$qty : $qty,
                ];
            }
        }
        return htmlspecialchars(json_encode($list, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
    }
}

?>

<?php
if (!function_exists('ms_makeKarungJson')) {
    function ms_makeKarungJson(string $karungStr): string
    {
        $list = [];
        if ($karungStr !== '') {
            foreach (explode(',', $karungStr) as $p) {
                $p = trim($p);
                if ($p === '') continue;
                [$noModel, $noKarung, $kgs, $lot] = array_pad(explode('|', $p), 4, '');
                $list[] = [
                    'no_model'  => $noModel,
                    'no_karung' => $noKarung,
                    'kgs_kirim' => is_numeric($kgs) ? (float)$kgs : $kgs,
                    'lot_kirim' => $lot,
                ];
            }
        }
        return htmlspecialchars(json_encode($list, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
    }
}

?>


<?php if (empty($groupData)): ?>
    <p class="text-center">Tidak ada data untuk Group <?= $group ?>.</p>
<?php else: ?>
    <div class="row mb-4 mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h3 style="color:rgb(255, 255, 255);" class="mb-0 text-center">GROUP <?= $group ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php for ($i = 'A'; $i <= 'L'; $i++): ?>
                                        <th class="header-cell"><?= $group . '.' . $i ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($row = 9; $row >= 1; $row--): ?>
                                    <tr>
                                        <?php for ($col = 'A'; $col <= 'L'; $col++): ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    // Nama cluster yang benar sesuai baris
                                                    $namaA = "$group.$col.0$row.A";
                                                    $namaB = "$group.$col.0$row.B";

                                                    // Cari data di $groupData yang sesuai
                                                    $clusterA = null;
                                                    $clusterB = null;
                                                    foreach ($groupData as $cluster) {
                                                        if ($cluster['nama_cluster'] == $namaA) {
                                                            $clusterA = $cluster;
                                                        } elseif ($cluster['nama_cluster'] == $namaB) {
                                                            $clusterB = $cluster;
                                                        }
                                                    }

                                                    if (!function_exists('getButtonColor')) {
                                                        function getButtonColor($cluster)
                                                        {
                                                            if (!$cluster || $cluster['kapasitas'] == 0) return 'gray-cell'; // Gray (0%)
                                                            $kapasitas = (float) $cluster['kapasitas'];
                                                            $total_qty = (float) $cluster['total_qty'];
                                                            $persentase = ($total_qty / $kapasitas) * 100;

                                                            if ($persentase == 0) return 'gray-cell'; // Gray
                                                            if ($persentase > 0 && $persentase <= 70) return 'blue-cell'; // Blue
                                                            if ($persentase > 70 && $persentase < 100) return 'orange-cell'; // Orange
                                                            return 'red-cell'; // Red (100%)
                                                        }
                                                    }

                                                    $colorA = getButtonColor($clusterA);
                                                    $colorB = getButtonColor($clusterB);
                                                    ?>


                                                    <?php
                                                    // siapkan JSON aman untuk A/B
                                                    $detailA  = ms_makeDetailJson($clusterA['detail_data'] ?? '');
                                                    $karungA  = ms_makeKarungJson($clusterA['detail_karung'] ?? '');

                                                    $detailB  = ms_makeDetailJson($clusterB['detail_data'] ?? '');
                                                    $karungB  = ms_makeKarungJson($clusterB['detail_karung'] ?? '');



                                                    // default nilai aman saat cluster null
                                                    $kapA   = (float)($clusterA['kapasitas']  ?? 0);
                                                    $totA   = (float)($clusterA['total_qty']  ?? 0);
                                                    $nameA  = $clusterA['nama_cluster']       ?? '';
                                                    $simA   = $clusterA['simbol_cluster']     ?? '-';

                                                    $kapB   = (float)($clusterB['kapasitas']  ?? 0);
                                                    $totB   = (float)($clusterB['total_qty']  ?? 0);
                                                    $nameB  = $clusterB['nama_cluster']       ?? '';
                                                    $simB   = $clusterB['simbol_cluster']     ?? '-';

                                                    // disable kalau cluster tidak ada
                                                    $disA = $clusterA ? '' : 'disabled';
                                                    $disB = $clusterB ? '' : 'disabled';
                                                    ?>

                                                    <!-- Button A -->
                                                    <button class="cell <?= $colorA ?>" <?= $disA ?>
                                                        data-bs-toggle="modal" data-bs-target="#modalDetail"
                                                        data-kapasitas="<?= $kapA ?>"
                                                        data-total_qty="<?= $totA ?>"
                                                        data-nama_cluster="<?= esc($nameA) ?>"
                                                        data-detail='<?= $detailA ?>'
                                                        data-karung='<?= $karungA ?>'>
                                                        <?= esc($simA) ?>
                                                    </button>

                                                    <!-- Button B -->
                                                    <button class="cell <?= $colorB ?>" <?= $disB ?>
                                                        data-bs-toggle="modal" data-bs-target="#modalDetail"
                                                        data-kapasitas="<?= $kapB ?>"
                                                        data-total_qty="<?= $totB ?>"
                                                        data-nama_cluster="<?= esc($nameB) ?>"
                                                        data-detail='<?= $detailB ?>'
                                                        data-karung='<?= $karungB ?>'>
                                                        <?= esc($simB) ?>
                                                    </button>
                                                </div>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>