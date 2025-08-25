<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted">Material System</h6>
                <h3 class="mb-0">Report Pengeluaran</h3>
            </div>
            <i class="fas fa-upload fa-2x text-white p-2 rounded bg-gradient-info"></i>
            <!-- <i class="fas fa-upload"></i> -->
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Filter Tanggal -->
            <div class="row mb-3">
                <!-- Kolom Input Tanggal -->
                <div class="col-md-4">
                    <label for="filterDate" class="form-label fw-bold text-dark">Pilih Tanggal:</label>
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
                        <?php foreach ($pengeluaran as $row) : ?>
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
            <?php if (empty($pengeluaran)) : ?>
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

        $('#filterButton').click(function() {
            let d1 = $('#filterDate').val();
            let d2 = $('#filterDate2').val();
            if (d1 && d2) {
                // Tampilkan tombol export
                $('#exportButton').removeClass('d-none');
                // Redirect dengan dua parameter GET
                const base = "<?= base_url($role . '/warehouse/reportPengeluaranBb') ?>";
                window.location.href = `${base}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });

        $('#resetButton').click(function() {
            $('#filterDate', '#filterDate2').val(''); // Kosongkan input tanggal 
            window.location.href = "<?= base_url($role . '/warehouse/reportPengeluaranBb') ?>"; // Refresh ke halaman awal
        });

        // Cek apakah ada data, jika ya, tampilkan tabel
        <?php if (!empty($pengeluaran)) : ?>
            $('#tableContainer').show();
            $('#exportButton').removeClass('d-none'); // Tampilkan tombol export jika sudah filter dan data ada
        <?php endif; ?>

        // Tombol export (aksi bisa diarahkan ke controller yang handle export)
        $('#exportButton').click(function() {
            let d1 = $('#filterDate').val();
            let d2 = $('#filterDate2').val();
            if (d1 && d2) {
                const url = "<?= base_url($role . '/warehouse/excelPengeluaranBb') ?>";
                window.location.href = `${url}?date=${encodeURIComponent(d1)}&date2=${encodeURIComponent(d2)}`;
            } else {
                alert('Silakan pilih tanggal awal dan akhir terlebih dahulu.');
            }
        });
    });
</script>

<?php $this->endSection(); ?>