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
                <div class="col-md-12">
                    <label for="filterDate" class="form-label fw-bold text-dark">Pilih Tanggal:</label>
                    <div class="input-group">
                        <input type="date" id="filterDate" class="form-control shadow-sm" value="<?= esc($selectedDate) ?>">
                        <button id="filterButton" class="btn bg-gradient-info text-white fw-bold shadow-sm ms-2">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                        <button id="resetButton" class="btn bg-gradient-danger text-white fw-bold shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i> Reset
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
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Box</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No Rak</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">Posisi Rak</th>
                            <th text-center class="text-uppercase text-secondary text-xxs font-weight-bolder">No Palet</th>
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
                                <td class="text-center align-middle"><?= esc($row['box']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['no_rak']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['posisi_rak']) ?></td>
                                <td class="text-center align-middle"><?= esc($row['no_palet']) ?></td>
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

        $('#filterButton').click(function() {
            let selectedDate = $('#filterDate').val();
            if (selectedDate) {
                window.location.href = "<?= base_url($role . '/warehouse/reportPemasukan') ?>?date=" + selectedDate;
            } else {
                alert('Silakan pilih tanggal terlebih dahulu.');
            }
        });

        $('#resetButton').click(function() {
            $('#filterDate').val(''); // Kosongkan input tanggal
            window.location.href = "<?= base_url($role . '/warehouse/reportPemasukan') ?>"; // Refresh ke halaman awal
        });

        // Tampilkan tabel hanya jika ada data
        <?php if (!empty($pemasukan)) : ?>
            $('#tableContainer').show();
        <?php endif; ?>
    });
</script>

<?php $this->endSection(); ?>