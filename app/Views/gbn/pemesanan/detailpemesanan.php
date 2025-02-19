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
                            <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx, .xls, .csv" required>
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
                <h5 class="mb-0 font-weight-bolder">Data Pemesanan <?= $jenis; ?> <?= $area; ?></h5>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-2"></i>Import MU
                </button>
            </div>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jalan Mc</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Kgs Pesan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Cns Pesan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataPemesanan)): ?>
                            <?php
                            $no = 1;
                            foreach ($dataPemesanan as $data): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['tgl_pakai'] ?></td>
                                    <td><?= $data['area'] ?></td>
                                    <td><?= $data['no_model'] ?></td>
                                    <td><?= $data['item_type'] ?></td>
                                    <td><?= $data['kode_warna'] ?></td>
                                    <td><?= $data['color'] ?></td>
                                    <td><?= $data['jl_mc'] ?></td>
                                    <td><?= $data['kgs_pesan'] ?></td>
                                    <td><?= $data['cns_pesan'] ?></td>
                                    <td><?= $data['po_tambahan'] ?></td>
                                    <td>
                                        <form action="<?= base_url($role . '/pengiriman_area') ?>" method="post">
                                            <input type="hidden" name="tgl_pakai" value="<?= $data['tgl_pakai'] ?>">
                                            <input type="hidden" name="area" value="<?= $data['area'] ?>">
                                            <input type="hidden" name="no_model" value="<?= $data['no_model'] ?>">
                                            <input type="hidden" name="item_type" value="<?= $data['item_type'] ?>">
                                            <input type="hidden" name="kode_warna" value="<?= $data['kode_warna'] ?>">
                                            <input type="hidden" name="warna" value="<?= $data['color'] ?>">
                                            <input type="hidden" name="kgs_pesan" value="<?= $data['kgs_pesan'] ?>">
                                            <input type="hidden" name="cns_pesan" value="<?= $data['cns_pesan'] ?>">
                                            <button type="submit" class="btn bg-gradient-info btn-sm">
                                                Kirim
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($dataPemesanan)) : ?>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
    </script>

    <?php $this->endSection(); ?>