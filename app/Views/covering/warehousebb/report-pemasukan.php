<?php $this->extend($role . '/warehouse/header'); ?>
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

    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted">Material System</h6>
                <h3 class="mb-0">Report Pemasukan</h3>
            </div>
            <i class="fas fa-download fa-2x text-white p-2 rounded bg-gradient-info"></i>
            <!-- <i class="fas fa-download"></i> -->
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filter Tanggal -->
            <div class="row mb-3">
                <!-- Kolom Input Tanggal -->
                <div class="col-md-4">
                    <label for="filterDate" class="form-label fw-bold text-dark">Pilih Tanggal (Awal):</label>
                    <input type="date" id="filterDate" class="form-control shadow-sm" value="<?= esc($selectedDate) ?>">
                </div>
                <div class="col-md-4">
                    <label for="filterDate" class="form-label fw-bold text-dark">Pilih Tanggal (Akhir):</label>
                    <input type="date" id="filterDate2" class="form-control shadow-sm" value="<?= esc($selectedDate2) ?>">
                </div>

                <!-- Kolom Aksi -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark">Aksi:</label>
                    <div class="d-flex gap-2">
                        <button id="filterButton" class="btn bg-gradient-info text-white fw-bold shadow-sm">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                        <button id="resetButton" class="btn bg-gradient-danger text-white fw-bold shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i> Reset
                        </button>
                        <button id="exportButton" class="btn bg-gradient-success text-white fw-bold shadow-sm d-none">
                            <i class="fas fa-file-excel me-2"></i> Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel -->
            <div class="table-responsive" id="tableContainer" style="display: none;">
                <table id="pemasukanTable" class="table table-striped table-hover table-bordered text-xs font-bolder" style="width: 100%;">
                    <thead>
                        <tr class="text-center align-middle">
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Denier</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Jenis Benang</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Code</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">TTL CNS</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">TTL KG</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($pemasukan as $row) : ?>
                            <tr class="text-center align-middle">
                                <td class="text-center align-middle"><?= esc($no++) ?></td>
                                <td class="text-center align-middle"><?= esc($row['denier']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['jenis_benang']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['color']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['code']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['ttl_cns']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['ttl_kg']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['admin']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['keterangan']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pesan Jika Tidak Ada Data -->
            <?php if (empty($pemasukan)) : ?>
                <div class="alert alert-info text-center text-white mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Silakan pilih tanggal terlebih dahulu untuk melihat data.
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let table = $('#pemasukanTable').DataTable({
            "paging": true,
            "searching": false,
            "info": false
        });

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

        // Filter button
        $('#filterButton').click(function() {
            let d1 = $('#filterDate').val();
            let d2 = $('#filterDate2').val();
            showLoading();
            updateProgress(30);
            if (d1 && d2) {
                // Tampilkan tombol export
                $('#exportButton').removeClass('d-none');
                // Redirect dengan dua parameter GET
                const base = "<?= base_url($role . '/warehouse/reportPemasukanBb') ?>";
                window.location.href = `${base}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
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
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });

        // Reset button
        $('#resetButton').click(function() {
            $('#filterDate, #filterDate2').val('');
            window.location.href = "<?= base_url($role . '/warehouse/reportPemasukanBb') ?>";
        });

        // Jika sudah ada data dari server, tampilkan tabel + export
        <?php if (!empty($pemasukan)) : ?>
            $('#tableContainer').show();
            $('#exportButton').removeClass('d-none');
        <?php endif; ?>

        // Tombol export ke Excel, sertakan kedua tanggal
        $('#exportButton').click(function() {
            let d1 = $('#filterDate').val();
            let d2 = $('#filterDate2').val();
            if (d1 && d2) {
                const url = "<?= base_url($role . '/warehouse/excelPemasukanBb') ?>";
                window.location.href = `${url}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });
    });
</script>


<?php $this->endSection(); ?>