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

<?php if (empty($groupData)): ?>
    <p class="text-center">Tidak ada data untuk Cluster <?= ucwords(strtolower($group)) ?>.</p>
<?php else: ?>
    <div class="row mb-4 mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h3 style="color:rgb(255, 255, 255);" class="mb-0 text-center">CLUSTER <?= strtoupper($group) ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        <?php
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

                        function renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD)
                        {
                            $clusters = ['A' => null, 'B' => null, 'C' => null, 'D' => null];

                            // Cari data cluster
                            foreach ($groupData as $cluster) {
                                switch ($cluster['nama_cluster'] ?? '') {
                                    case $namaA:
                                        $clusters['A'] = $cluster;
                                        break;
                                    case $namaB:
                                        $clusters['B'] = $cluster;
                                        break;
                                    case $namaC:
                                        $clusters['C'] = $cluster;
                                        break;
                                    case $namaD:
                                        $clusters['D'] = $cluster;
                                        break;
                                }
                            }

                            // Render hanya yang ada datanya
                            foreach ($clusters as $suffix => $cluster) {
                                if (! $cluster) {
                                    // skip kalau null atau kosong
                                    continue;
                                }

                                $color = getButtonColor($cluster);
                        ?>
                                <button class="cell <?= $color ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalDetail"
                                    data-kapasitas="<?= $cluster['kapasitas'] ?>"
                                    data-total_qty="<?= $cluster['total_qty'] ?>"
                                    data-nama_cluster="<?= $cluster['nama_cluster'] ?>"
                                    data-detail='[<?= $cluster['detail_data'] ?>]'>
                                    <?= $cluster['simbol_cluster'] ?>
                                </button>
                        <?php
                            }
                        }
                        ?>

                        <table class="table table-bordered">
                            <thead>
                                <?php foreach (range('Q', 'O') as $col): ?>
                                    <tr>
                                        <th colspan="14"></th>
                                        <th><?= $col ?></th>
                                        <?php for ($row = 1; $row <= 6; $row += 2): ?>
                                            <td class="p-0">
                                                <div class="d-grid gap-1"
                                                    style="grid-template-columns: repeat(2, auto); grid-template-rows: repeat(2, auto);">
                                                    <?php
                                                    for ($sub = 0; $sub < 2; $sub++) {
                                                        $r = $row + $sub;
                                                        $rowF = str_pad($r, 2, '0', STR_PAD_LEFT);

                                                        // Baris A dulu (1A, 2A)
                                                        $namaA = "$col.{$rowF}.A";
                                                        renderClusterButtons($groupData, $namaA, null, null, null);
                                                    }

                                                    for ($sub = 0; $sub < 2; $sub++) {
                                                        $r = $row + $sub;
                                                        $rowF = str_pad($r, 2, '0', STR_PAD_LEFT);

                                                        // Lalu baris B (1B, 2B)
                                                        $namaB = "$col.{$rowF}.B";
                                                        renderClusterButtons($groupData, $namaB, null, null, null);
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        <?php endfor; ?>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <?php for ($i = 'A'; $i <= 'U'; $i++): ?>
                                        <?php
                                        // Loncat dari N langsung ke U
                                        if ($i == 'O') {
                                            $i = 'U';
                                        }
                                        ?>

                                        <?php if ($i == 'M' || $i == 'N'): ?>
                                            <th class="header-cell" colspan=2><?= $i ?></th>
                                        <?php elseif ($i == 'U'): ?>
                                            <th class="header-cell" colspan=3><?= $i ?></th>
                                        <?php else: ?>
                                            <th class="header-cell"><?= $i ?></th>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($row = 1; $row <= 11; $row++): ?>
                                    <tr>
                                        <?php for ($col = 'A'; $col <= 'L'; $col++): ?>
                                            <?php
                                            // Jika kolom bukan M/N dan row > 11, stop looping col berikutnya
                                            if ($row > 11) {
                                                continue; // skip kolom A-L untuk row > 11
                                            } ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-start">
                                                    <?php
                                                    $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                    // 1) Set default dulu
                                                    $namaA = "$col.{$rowFormatted}.A";
                                                    $namaB = "$col.{$rowFormatted}.B";
                                                    $namaC = null;
                                                    $namaD = null;

                                                    // 2) Untuk kolom A–J rak c
                                                    if ($col >= 'A' && $col <= 'J') {
                                                        if ($row >= 8 && $row <= 11) {
                                                            $namaC = "$col.{$rowFormatted}.C";
                                                        }
                                                        if ($row >= 10 && $row <= 11) {
                                                            $namaD = "$col.{$rowFormatted}.D";
                                                        }
                                                        // 3) Untuk kolom K (karena datanya pasti ada), langsung set C & D
                                                    } elseif ($col === 'K') {
                                                        $namaC = "$col.{$rowFormatted}.C";
                                                        $namaD = "$col.{$rowFormatted}.D";

                                                        // 4) Kalau kolom L, cukup A & B saja (C/D tetap null)
                                                    } elseif ($col === 'L') {
                                                        if ($row >= 1 && $row <= 7) {
                                                            $namaC = "$col.{$rowFormatted}.C";
                                                            $namaD = "$col.{$rowFormatted}.D";
                                                        } else {
                                                            $namaC = "$col.{$rowFormatted}.C";
                                                            $namaD = "";
                                                        }
                                                    }
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            </td>
                                        <?php endfor; ?>
                                        <!-- Tambahkan Kolom M1 (M.01–M.09) -->
                                        <td class="p-1">
                                            <?php if ($row <= 9): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "M.$rowFormatted.A";
                                                    $namaB = "M.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-center"></div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Tambahkan Kolom M2 (M.10–M.18) -->
                                        <td class="p-1">
                                            <?php if ($row + 9 <= 18): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowM2 = $row + 9;
                                                    $rowFormatted = str_pad($rowM2, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "M.$rowFormatted.A";
                                                    $namaB = "M.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-center"></div>
                                            <?php endif; ?>

                                        </td>

                                        <!-- Tambahkan Kolom N1 (N.01–N.11) -->
                                        <td class="p-1">
                                            <div class="d-flex justify-content-center">
                                                <?php
                                                $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                $namaA = "N.$rowFormatted.A";
                                                $namaB = "N.$rowFormatted.B";
                                                $namaC = null;
                                                $namaD = null;
                                                renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                ?>
                                            </div>
                                        </td>
                                        <!-- BARIS N2 (N.12-N.22) -->
                                        <td class="p-1">
                                            <?php if ($row + 11 <= 22): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowN2 = 23 - $row;
                                                    $rowFormatted = str_pad($rowN2, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "N.$rowFormatted.A";
                                                    $namaB = "N.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <!-- BARIS U1 (U.1-U.6) -->
                                        <td class="p-1">
                                            <?php if ($row <= 6): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "U.$rowFormatted.A";
                                                    $namaB = "U.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <!-- BARIS U2 (U.7-U.12) -->
                                        <td class="p-1">
                                            <?php if ($row + 6 <= 12): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowU2 = $row + 6;
                                                    $rowFormatted = str_pad($rowU2, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "U.$rowFormatted.A";
                                                    $namaB = "U.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <!-- BARIS U3 (U.13-U.18) -->
                                        <td class="p-1">
                                            <?php if ($row + 12 <= 18): ?>
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowU3 = 19 - $row;
                                                    $rowFormatted = str_pad($rowU3, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "U.$rowFormatted.A";
                                                    $namaB = "U.$rowFormatted.B";
                                                    $namaC = null;
                                                    $namaD = null;
                                                    renderClusterButtons($groupData, $namaA, $namaB, $namaC, $namaD);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
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