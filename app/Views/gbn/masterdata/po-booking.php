<?php $this->extend($role . '/masterdata/header'); ?>
<?php $this->section('content'); ?>
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">List PO Booking</h5>
                <a href="<?= base_url($role . '/masterdata/poBooking/create') ?>" class="btn bg-gradient-info"> Buat PO Booking</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="table-po" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Po</th>
                            <th>No Model</th>
                            <th>Keterangan</th>
                            <th>Penerima</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1 ?>
                        <?php foreach ($poBooking as $data) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('Y-m-d', strtotime($data['created_at'])) ?></td>
                                <td><?= $data['no_model'] ?></td>
                                <td><?= $data['keterangan'] ?></td>
                                <td><?= $data['penerima'] ?></td>
                                <td>
                                    <!-- <a
                                        href="<?= base_url("$role/masterdata/poBooking/exportPoBooking?no_model=" . rawurlencode($data['no_model'])) ?>"
                                        class="btn btn-success">
                                        <i class="fas fa-file me-2"></i>Export
                                    </a> -->
                                    <a
                                        href="<?= base_url("$role/masterdata/poBooking/detail?no_model=" . rawurlencode($data['no_model'])) ?>"
                                        class="btn btn-info">
                                        <i class="fas fa-eye me-2"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            <!-- Modal Export PO Booking -->
                            <div class="modal fade" id="modalExportPO" tabindex="-1" aria-labelledby="modalExportPOLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formExportPO" method="get">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalExportPOLabel">Export PO <?= $data['no_model'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="delivery" class="form-label">Delivery</label>
                                                    <input type="date" class="form-control" id="delivery" name="delivery">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Export</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Tangkap klik tombol export
        $('#table-po').on('click', '.btn-success', function(e) {
            e.preventDefault();
            var exportUrl = $(this).attr('href');
            $('#formExportPO').attr('action', exportUrl);
            $('#modalExportPO').modal('show');
        });

        // Submit form export
        $('#formExportPO').on('submit', function(e) {
            e.preventDefault();
            var action = $(this).attr('action');
            var date = $('#delivery').val();

            let baseUrl = $(this).attr('action');
            if (date) {
                // Append delivery param, handle if URL already has query params
                baseUrl += (baseUrl.indexOf('?') === -1 ? '?' : '&') + 'delivery=' + encodeURIComponent(date);
            }

            window.open(baseUrl, '_blank');
        });
    });
</script>

<!-- Pastikan jQuery load pertama -->

<script>
    $(document).ready(function() {
        $('#table-po').DataTable({
            "pageLength": 10,
        });
    });
</script>


<?php $this->endSection(); ?>