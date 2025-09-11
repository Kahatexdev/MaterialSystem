<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <style>
        /* Overlay transparan */
        #loadingOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            background: rgba(0, 0, 0, 0.75);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            width: 260px;
            /* kecilkan modal */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .loader-text {
            margin-top: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 12px;
        }


        #loadingOverlay.active {
            display: block;
            opacity: 1;
        }

        .loader {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            position: relative;
        }

        .loader:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: loader-dual-ring 1.2s linear infinite;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }

        @keyframes loader-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>
    <!-- overlay -->
    <div id="loadingOverlay">
        <div class="loader-wrap">
            <div class="loading-card">
                <div class="loader" role="status" aria-hidden="true"></div>
                <div class="loader-text">Memuat data...</div>

                <!-- Progress bar -->
                <div class="progress mt-3" style="height: 6px; border-radius: 6px;">
                    <div id="progressBar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Sisa Kebutuhan Area</p>
                            <h5 class="font-weight-bolder mb-0">
                                Data Sisa Kebutuhan Area
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_area" class="form-label">Area</label>
                            <select class="form-control" name="filter_area" id="filter_area" required>
                                <option value="">Pilih Area</option>
                                <?php foreach ($allArea as $ar) {
                                ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control" placeholder="No Model" required>
                        </div>
                        <!-- Tombol Filter -->
                        <div class="col-md-2">
                            <label class="form-label d-block invisible">Filter</label>
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100">
                                <i class="fas fa-filter"></i> FILTER
                            </button>
                        </div>

                        <!-- Tombol Refresh -->
                        <div class="col-md-1">
                            <label class="form-label d-block invisible">Refresh</label>
                            <button type="button" class="btn btn-secondary w-100"
                                onclick="window.location.href='<?= base_url($role . 'pemesanan/sisaKebutuhanArea') ?>'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>

                        <div class="col-md-3 text-end">
                            <label class="form-label d-block invisible">Export</label>
                            <button type="button" class="btn btn-success w-100"
                                onclick="window.location.href='<?= base_url($role . '/pemesanan/reportSisaKebutuhanArea') . '?filter_model=' . ($noModel ?? '') . '&filter_area=' . ($area ?? '') ?>'">
                                <i class="fas fa-file-excel"></i> EXPORT EXCEL
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table align-items-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">NO MODEL</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOS</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">ITEM TYPE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KODE WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TOTAL KEBUTUHAN AREA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY PESAN (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">PO TAMBAHAN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY KIRIM (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY RETUR (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KET GBN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">SISA (KEBUTUHAN - KIRIM + RETUR)</th>
                                </tr>
                            </thead>
                            <tbody id="sisaKebutuhanTable">
                                <?php
                                if (empty($dataPemesanan) && !empty($area) && !empty($noModel)) { ?>
                                    <tr>
                                        <th colspan="16">Tidak Ada Data</th>
                                    </tr>
                                <?php
                                } elseif (empty($dataPemesanan) && empty($area) && empty($noModel)) { ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Silakan pilih area dan isi no model untuk menampilkan data.</td>
                                    </tr>
                                    <?php
                                } elseif (!empty($dataPemesanan) && !empty($area) && !empty($noModel)) {

                                    $prevKey = null;
                                    $ttlKgPesan = 0;
                                    $ttlKgOut = 0;
                                    $ttlKgRetur = 0;
                                    $ttlKebTotal = 0;
                                    $sisa = 0;

                                    foreach ($dataPemesanan as $key => $id) {
                                        // Buat key unik untuk kombinasi
                                        $currentKey = $id['item_type'] . '|' . $id['kode_warna'] . '|' . $id['color'];

                                        if ($prevKey !== null && $currentKey !== $prevKey) {
                                    ?>
                                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                                <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                                <th colspan="2"></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                            </tr>
                                        <?php
                                            // Reset total untuk grup berikutnya
                                            $ttlKgPesan = 0;
                                            $ttlKgOut = 0;
                                            $ttlKgRetur = 0;
                                            $ttlKebTotal = 0;
                                            $sisa = 0;
                                        }
                                        // Hitung total sementara
                                        $ttlKgPesan += $id['ttl_kg'];
                                        $ttlKgOut += $id['kg_out'];
                                        $ttlKgRetur += $id['kgs_retur'];
                                        // Ambil ttl_keb satu kali per grup
                                        if (!isset($shownKebutuhan[$currentKey])) {
                                            $ttlKebTotal = $id['ttl_keb']; // Ambil hanya sekali
                                            $shownKebutuhan[$currentKey] = true;
                                        }
                                        $sisa = $ttlKebTotal - $ttlKgOut + $ttlKgRetur;
                                        if ($sisa < 0) {
                                            $color = "red";
                                        } else {
                                            $color = "green";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-xs text-center"><?= $id['tgl_pakai']; ?></td>
                                            <td class="text-xs text-center"><?= $id['tgl_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['no_model']; ?></td>
                                            <td class="text-xs text-center"><?= $id['max_loss'] ?? ''; ?></td>
                                            <td class="text-xs text-center"><?= $id['item_type']; ?></td>
                                            <td class="text-xs text-center"><?= $id['kode_warna']; ?></td>
                                            <td class="text-xs text-center"><?= $id['color']; ?></td>
                                            <td></td>
                                            <td class="text-xs text-center"><?= number_format($id['ttl_kg'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['po_tambahan'] == 1 ? 'YA' : ''; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kg_out'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_out']; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kgs_retur'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['ket_gbn']; ?></td>
                                            <td></td>
                                        </tr>
                                    <?php
                                        $prevKey = $currentKey;
                                    }

                                    // Tampilkan total untuk grup terakhir
                                    if ($prevKey !== null) {
                                    ?>
                                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                                            <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                            <th colspan="2"></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showLoading() {
        $('#loadingOverlay').addClass('active');
        $('#btnSearch').prop('disabled', true);
        // show DataTables processing indicator if available
        try {
            dataTable.processing(true);
        } catch (e) {}
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('active');
        $('#btnSearch').prop('disabled', false);
        try {
            dataTable.processing(false);
        } catch (e) {}
    }

    function updateProgress(percent) {
        $('#progressBar')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent);
        $('#progressText').text(percent + '%');
    }

    //Filter data pemesanan
    document.getElementById('filterButton').addEventListener('click', function() {
        const filterArea = document.getElementById('filter_area').value.trim();
        const filterModel = document.getElementById('filter_model').value.trim();

        // Validasi input
        if (!filterArea || !filterModel) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Area dan No Model harus diisi!',
                confirmButtonText: 'OK',
            });
            return; // Hentikan eksekusi jika input kosong
        }

        showLoading();
        updateProgress(30);

        // Redirect ke controller dengan parameter
        let url = '<?= base_url($role . '/pemesanan/sisaKebutuhanArea') ?>?filter_model=' + encodeURIComponent(filterModel) + '&filter_area=' + encodeURIComponent(filterArea);
        window.location.href = url;
        // animasi progress naik pelan → lalu redirect
        let percent = 80;
        let interval = setInterval(() => {
            percent += 9;
            if (percent >= 99) {
                clearInterval(interval);
                window.location.href = url; // redirect ketika progress sudah 90%
            } else {
                updateProgress(percent);
            }
        }, 100);
    });
</script>

<?php $this->endSection(); ?>