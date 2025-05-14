<?php $this->extend($role . '/warehouse/header'); ?>
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
                    <h5 class="modal-title" id="modalStockLabel">Detail Pemasukan Lain - Lain</h5>
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
                <h5 class="mb-0 font-weight-bolder">Data </h5>
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Datang</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No SJ</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Gw</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ganti Retur</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                                <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Waktu Input</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataBon)): ?>
                                <?php
                                $no = 1;
                                foreach ($dataBon as $data): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $data['tgl_datang'] ?></td>
                                        <td><?= $data['no_model'] ?></td>
                                        <td><?= $data['item_type'] ?></td>
                                        <td><?= $data['kode_warna'] ?></td>
                                        <td><?= $data['warna'] ?></td>
                                        <td><?= $data['no_surat_jalan'] ?></td>
                                        <td><?= number_format($data['gw'], 2) ?></td>
                                        <td><?= number_format($data['kgs'], 2) ?></td>
                                        <td><?= $data['lot_kirim'] ?></td>
                                        <td><?= $data['ganti_retur'] == 1 ? "Ya" : "" ?></td>
                                        <td><?= $data['admin'] ?></td>
                                        <td>
                                            <a href="<?= base_url($role . '/otherIn/printBarcode/' . $data['id_other_bon']) ?>" class="btn btn-danger btn-xs">
                                                <i class="fa fa-file-pdf fa-xl" style="font-size: 19px !important;"></i>
                                            </a>
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