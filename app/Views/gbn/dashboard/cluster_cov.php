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
    <p class="text-center">Tidak ada data untuk Cluster <?= ucfirst(strtolower($group)); ?>.</p>
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
                        ?>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php for ($i = '1'; $i <= '10'; $i++): ?>
                                        <th class="header-cell"><?= $i ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                function renderClusterButtons($groupData, $namaA, $namaB)
                                {
                                    $clusterA = $clusterB = null;

                                    foreach ($groupData as $cluster) {
                                        // dd($cluster);
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
                                            data-karung='<?= $cluster['detail_karung'] ?? '[]' ?>'
                                            data-detail='[<?= $cluster['detail_data'] ?? '' ?>]'>
                                            <?= $cluster ? $cluster['simbol_cluster'] : '-' ?>
                                        </button>
                                <?php
                                    }
                                }
                                ?>
                                <?php for ($row = 1; $row <= 11; $row++): ?>
                                    <tr>
                                        <?php for ($col = '1'; $col <= '10'; $col++): ?>
                                            <?php
                                            // Jika kolom bukan M/N dan row > 11, stop looping col berikutnya
                                            if ($row > 11) {
                                                continue; // skip kolom A-L untuk row > 11
                                            } ?>
                                            <td class="p-1">
                                                <div class="d-flex justify-content-center">
                                                    <?php
                                                    $rowFormatted = str_pad($row, 2, '0', STR_PAD_LEFT);
                                                    $colFormatted = str_pad($col, 2, '0', STR_PAD_LEFT);
                                                    $namaA = "$colFormatted.$rowFormatted.A";
                                                    $namaB = "$colFormatted.$rowFormatted.B";
                                                    renderClusterButtons($groupData, $namaA, $namaB);
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