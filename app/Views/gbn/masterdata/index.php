<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

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

    <!-- Modal untuk Upload File Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="import/mu" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Input File -->
                        <div class="mb-3">
                            <label for="excelFile" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="excelFile" name="excelFile" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Hanya file dengan format .xlsx, .xls, atau .csv yang didukung.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Order</h5>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-2"></i>Import MU
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="table-responsive">
            <table class="table align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Order Type</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama Admin</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data Dummy -->
                    <tr>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">1</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">23/04/18</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">MM</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">MM123</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">NR987</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">KK1A</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">H&M</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">1-1-2026</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">Cijerah</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-secondary text-xs font-weight-bold">Mira</span>
                        </td>
                        <td class="align-middle">
                            <a href="#" class="btn btn-info btn-sm">Detail</a>
                        </td>
                    </tr>
                    <!-- Tambahkan data lainnya -->
                </tbody>
            </table>
        </div>
    </div>

    <?php $this->endSection(); ?>