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
                                    <?php for ($i = 'A'; $i <= 'N'; $i++): ?>
                                        <th class="header-cell"><?= $group . '.' . $i ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($row = 16; $row >= 1; $row--): ?>
                                    <tr>
                                        <?php for ($col = 'A'; $col <= 'N'; $col++): ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    // Jika kolom B, gunakan format berbeda
                                                    if ($col == 'B') {
                                                        $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT); // Buat 01-09 tetap dua digit
                                                        $subClusters = [
                                                            "$group.B.$rowFormatted.A",
                                                            "$group.B.$rowFormatted.B",
                                                            "$group.B.$rowFormatted.B.01",
                                                            "$group.B.$rowFormatted.B.02",
                                                            "$group.B.$rowFormatted.B.03",
                                                            "$group.B.$rowFormatted.B.04"
                                                        ];


                                                        // Ambil semua nama_cluster dari $groupData
                                                        $clusterNames = array_column($groupData, 'nama_cluster');

                                                        foreach ($subClusters as $namaCluster) {
                                                            // Cari index dalam array
                                                            $index = array_search($namaCluster, $clusterNames);
                                                            $cluster = ($index !== false) ? $groupData[$index] : null;

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
                                                    } else {

                                                        $rowFormatted = ($row < 10) ? '0' . $row : $row; // Tetap '01-09', tapi '10-16' tanpa nol
                                                        $namaA = "$group.$col.$rowFormatted.A";
                                                        $namaB = "$group.$col.$rowFormatted.B";

                                                        // Cari data di $groupData
                                                        $clusterA = null;
                                                        $clusterB = null;
                                                        foreach ($groupData as $cluster) {
                                                            if ($cluster['nama_cluster'] == $namaA) {
                                                                $clusterA = $cluster;
                                                            } elseif ($cluster['nama_cluster'] == $namaB) {
                                                                $clusterB = $cluster;
                                                            }
                                                        }

                                                        $colorA = getButtonColor($clusterA);
                                                        $colorB = getButtonColor($clusterB);
                                                        ?>

                                                        <!-- Button A -->
                                                        <button class="cell <?= $colorA ?>" data-bs-toggle="modal" data-bs-target="#modalDetail"
                                                            data-kapasitas="<?= $clusterA['kapasitas'] ?? '' ?>"
                                                            data-total_qty="<?= $clusterA['total_qty'] ?? '' ?>"
                                                            data-nama_cluster="<?= $clusterA['nama_cluster'] ?? '' ?>"
                                                            data-detail='[<?= $clusterA['detail_data'] ?? '' ?>]'>
                                                            <?= $clusterA ? $clusterA['simbol_cluster'] : '-' ?>
                                                        </button>

                                                        <!-- Button B -->
                                                        <button class="cell <?= $colorB ?>" data-bs-toggle="modal" data-bs-target="#modalDetail"
                                                            data-kapasitas="<?= $clusterB['kapasitas'] ?? '' ?>"
                                                            data-total_qty="<?= $clusterB['total_qty'] ?? '' ?>"
                                                            data-nama_cluster="<?= $clusterB['nama_cluster'] ?? '' ?>"
                                                            data-detail='[<?= $clusterB['detail_data'] ?? '' ?>]'>
                                                            <?= $clusterB ? $clusterB['simbol_cluster'] : '-' ?>
                                                        </button>

                                                    <?php
                                                    }
                                                    ?>
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