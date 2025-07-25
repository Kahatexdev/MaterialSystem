<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
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
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Code</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
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
                                <td class="text-center align-middle"><?= esc($row['jenis']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['color']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['code']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['lmd']) ?></td>
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

        // Filter button
        $('#filterButton').click(function() {
            let d1 = $('#filterDate').val();
            let d2 = $('#filterDate2').val();
            if (d1 && d2) {
                // Tampilkan tombol export
                $('#exportButton').removeClass('d-none');
                // Redirect dengan dua parameter GET
                const base = "<?= base_url($role . '/warehouse/reportPemasukan') ?>";
                window.location.href = `${base}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });

        // Reset button
        $('#resetButton').click(function() {
            $('#filterDate, #filterDate2').val('');
            window.location.href = "<?= base_url($role . '/warehouse/reportPemasukan') ?>";
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
                const url = "<?= base_url($role . '/warehouse/excelPemasukanCovering') ?>";
                window.location.href = `${url}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });
    });
</script>


<?php $this->endSection(); ?>