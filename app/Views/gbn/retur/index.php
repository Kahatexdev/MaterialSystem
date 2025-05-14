<?php $this->extend($role . '/retur/header'); ?>
<?php $this->section('content'); ?>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <!-- Content utama -->
    <div class="container-fluid py-4">
        <div class="row my-4">

            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">

                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                    <h5 class="font-weight-bolder mb-0">Data <?= $title ?></h5>
                                </div>
                            </div>
                            <div>

                                <form method="get" action="<?= base_url($role . '/retur') ?>">
                                    <div class="d-flex align-items-center gap-3">
                                        <select name="jenis" id="jenis" class="form-control">
                                            <option value="">Jenis Bahan Baku</option>
                                            <?php foreach ($jenis as $row): ?>
                                                <option value="<?= $row['jenis'] ?>">
                                                    <?= $row['jenis'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <select name="area" id="area" class="form-control">
                                            <option value="">Area</option>
                                            <?php foreach ($area as $row): ?>
                                                <option value="<?= $row ?>">
                                                    <?= $row ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <input type="date" name="tgl_retur" id="tgl_retur" class="form-control"
                                            value="" placeholder="Tanggal Retur" />
                                        <input type="text" name="no_model" id="no_model" class="form-control"
                                            value="" placeholder="No Model" />
                                        <input type="text" name="kode_warna" id="kode_warna" class="form-control"
                                            value="" placeholder="Kode Warna" />
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <a href="<?= base_url($role . '/retur') ?>" class="btn btn-secondary ms-2">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    </div>
                                </form>


                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row my-4">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->

                        <!-- Table Section -->
                        <?php if (!$isFiltered): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i> Silakan pilih minimal satu filter untuk menampilkan data retur.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table id="returTable" class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Model</th>
                                            <th>Item Type</th>
                                            <th>Kode Warna</th>
                                            <th>Warna</th>
                                            <th>Kgs Retur</th>
                                            <th>Cns Retur</th>
                                            <th>Area Retur</th>
                                            <th>Tgl Retur</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php $i = 1; ?>
                                        <?php foreach ($retur as $row): ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $row['no_model'] ?></td>
                                                <td><?= $row['item_type'] ?></td>
                                                <td><?= $row['kode_warna'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 20px; height: 20px; background-color: <?= $row['kode_warna'] ?>; border-radius: 4px; margin-right: 8px;"></div>
                                                        <?= $row['warna'] ?>
                                                    </div>
                                                </td>
                                                <td><?= $row['kgs_retur'] ?></td>
                                                <td><?= $row['cns_retur'] ?></td>
                                                <td><?= $row['area_retur'] ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tgl_retur'])) ?></td>
                                                <td>
                                                    <!-- Modal buttons -->
                                                    <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#acceptModal<?= $row['id_retur'] ?>">
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                    <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#rejectModal<?= $row['id_retur'] ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Accept -->
<?php foreach ($retur as $row): ?>
    <div class="modal fade" id="acceptModal<?= $row['id_retur'] ?>" tabindex="-1" aria-labelledby="acceptModalLabel<?= $row['id_retur'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url($role . '/retur/approve') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_retur" value="<?= $row['id_retur'] ?>">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="acceptModalLabel<?= $row['id_retur'] ?>">Konfirmasi Approve</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin <strong>menerima</strong> retur ini?
                        <div class="form-group mt-3">
                            <label for="catatan_accept<?= $row['id_retur'] ?>">Keterangan</label>
                            <textarea name="catatan" id="catatan_accept<?= $row['id_retur'] ?>" class="form-control" rows="3" placeholder="Tulis keterangan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white">Ya, Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>


<!-- Modal Reject -->
<?php foreach ($retur as $row): ?>
    <div class="modal fade" id="rejectModal<?= $row['id_retur'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $row['id_retur'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url($role . '/retur/reject') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_retur" value="<?= $row['id_retur'] ?>">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="rejectModalLabel<?= $row['id_retur'] ?>">Konfirmasi Reject</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin <strong>menolak</strong> retur ini?
                        <div class="form-group mt-3">
                            <label for="catatan_reject<?= $row['id_retur'] ?>">Catatan Penolakan</label>
                            <textarea name="catatan" id="catatan_reject<?= $row['id_retur'] ?>" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
<script>
    $(document).ready(function() {
        $('#returTable').DataTable({
            "ordering": true,
            "paging": true,
            "searching": true,
            "info": true,
            "language": {
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
            }
        });
    });
</script>



<?php $this->endSection(); ?>