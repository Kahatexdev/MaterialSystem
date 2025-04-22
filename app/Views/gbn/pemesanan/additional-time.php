<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Detail Stok -->
    <div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStockLabel">Detail Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <div id="stockData" class="row g-3"></div>
                        <button type="submit" class="btn bg-gradient-info mt-3 text-end">Pilih Stok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Permintaan Tambahan Waktu Pemesanan</h5>
            </div>
        </div>


        <!-- Tabel Data -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($dataRequest as $data) { ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['admin']; ?></td>
                                    <td><?= $data['tgl_pakai']; ?></td>
                                    <td><?= $data['jenis']; ?></td>
                                    <?php if ($data['status_kirim'] == "request") { ?>
                                        <td>
                                            <button type="button" class="btn btn-xs bg-gradient-success" data-bs-toggle="modal" data-bs-target="#acceptModal"
                                                data-admin="<?= $data['admin'] ?>"
                                                data-tgl_pakai="<?= $data['tgl_pakai'] ?>"
                                                data-jenis="<?= $data['jenis'] ?>">
                                                <i class="fas fa-layer-group"></i> Accept
                                            </button>
                                            <!-- modal -->
                                            <div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="acceptModalLabel">Accept Request Additional Time</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="<?= base_url($role . '/pemesanan/additional-time/accept') ?>" method="post" id="acceptForm">
                                                                <input type="hidden" name="admin" id="admin">
                                                                <input type="hidden" name="tgl_pakai" id="tglPakai">
                                                                <input type="hidden" name="jenis" id="jenis">

                                                                <label for="maxTime" class="form-label">Waktu Maksimum</label>
                                                                <input type="time" id="maxTime" name="max_time" class="form-control" format="H:i:s" required>

                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- modal end -->
                                        </td>
                                        <td>
                                            <form action="<?= base_url($role . '/pemesanan/additional-time/reject') ?>" method="post" class="d-inline">
                                                <input type="hidden" name="admin" value="<?= $data['admin'] ?>">
                                                <input type="hidden" name="tgl_pakai" value="<?= $data['tgl_pakai'] ?>">
                                                <input type="hidden" name="jenis" value="<?= $data['jenis'] ?>">
                                                <button type="submit" class="btn btn-xs bg-gradient-danger">
                                                    <i class="fas fa-layer-group"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    <?php } else { ?>
                                        <td colspan="2"><strong><?= $data['status_kirim'] ?></strong></td>
                                        <td></td>
                                    <?php } ?>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $('#dataTable').DataTable({
        "pageLength": 35,
        "order": []
    });
</script>
<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const acceptButtons = document.querySelectorAll('[data-bs-target="#acceptModal"]');

        acceptButtons.forEach(button => {
            button.addEventListener('click', function() {
                const admin = button.getAttribute('data-admin');
                const tglPakai = button.getAttribute('data-tgl_pakai');
                const jenis = button.getAttribute('data-jenis');

                document.getElementById('admin').value = admin;
                document.getElementById('tglPakai').value = tglPakai;
                document.getElementById('jenis').value = jenis;
            });
        });
    });
</script>
<?php $this->endSection(); ?>