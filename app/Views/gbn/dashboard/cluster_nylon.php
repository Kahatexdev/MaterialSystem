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
    <p class="text-center">Tidak ada data untuk Cluster <?= $group ?>.</p>
<?php else: ?>
    <div class="row mb-4 mt-3">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h3 style="color:rgb(255, 255, 255);" class="mb-0 text-center">CLUSTER <?= $group ?></h5>
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
                        ?>

                        <table class="table table-bordered">
                            <thead>
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
                                <?php
                                function renderClusterButtons($groupData, $namaA, $namaB)
                                {
                                    $clusterA = $clusterB = null;

                                    foreach ($groupData as $cluster) {
                                        if ($cluster['nama_cluster'] == $namaA) $clusterA = $cluster;
                                        if ($cluster['nama_cluster'] == $namaB) $clusterB = $cluster;
                                    }

                                    foreach (['A' => $clusterA, 'B' => $clusterB] as $cluster) {
                                        $color = getButtonColor($cluster);
                                ?>
                                        <button class="cell <?= $color ?>" data-bs-toggle="modal" data-bs-target="#modalDetail"
                                            data-kapasitas="<?= $cluster['kapasitas'] ?? '' ?>"
                                            data-total_qty="<?= $cluster['total_qty'] ?? '' ?>"
                                            data-nama_cluster="<?= $cluster['nama_cluster'] ?? '' ?>"
                                            data-detail='[<?= $cluster['detail_data'] ?? '' ?>]'>
                                            <?= $cluster ? $cluster['simbol_cluster'] : '-' ?>
                                        </button>
                                <?php
                                    }
                                }
                                ?>
                                <?php for ($row = 1; $row <= 11; $row++): ?>
                                    <tr>
                                        <?php for ($col = 'A'; $col <= 'L'; $col++): ?>
                                            <?php
                                            // Jika kolom bukan M/N dan row > 11, stop looping col berikutnya
                                            if ($row > 11) {
                                                continue; // skip kolom A-L untuk row > 11
                                            } ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "$col.$rowFormatted.A";
                                                    $namaB = "$col.$rowFormatted.B";
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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
                                                    renderClusterButtons($groupData, $namaA, $namaB);
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-center">-</div>
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
                                                    renderClusterButtons($groupData, $namaA, $namaB);
                                                    ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="d-flex justify-content-center">-</div>
                                            <?php endif; ?>

                                        </td>

                                        <!-- Tambahkan Kolom N1 (N.01–N.11) -->
                                        <td class="p-1">
                                            <div class="d-flex justify-content-center">
                                                <?php
                                                $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                $namaA = "N.$rowFormatted.A";
                                                $namaB = "N.$rowFormatted.B";
                                                renderClusterButtons($groupData, $namaA, $namaB);
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
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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
                                                    $namaA = "N.$rowFormatted.A";
                                                    $namaB = "N.$rowFormatted.B";
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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