<?php $this->extend($role . '/pph/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <!-- Filter Data -->
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">PPH: Per Model</h5>
                            </div>
                        </div>
                        <div>
                            <!-- Ruang tambahan bila diperlukan -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Komposisi (%)</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Produksi</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Kebutuhan</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Pemakaian</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">% Pemakaian</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php $no = 1;
                                foreach ($models as $item) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $item['no_model'] ?></td>
                                        <td><?= $item['area'] ?></td>
                                        <td><?= $item['delivery_awal'] ?></td>
                                        <td><?= $item['item_type'] ?></td>
                                        <td><?= $item['color'] ?></td>
                                        <td><?= $item['kode_warna'] ?></td>
                                        <td><?= $item['loss'] ?></td>
                                        <td><?= $item['composition'] ?></td>
                                        <td><?= $item['gw'] ?></td>
                                        <td><?= $item['qty_pcs'] ?></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= number_format($item['ttl_kebutuhan'], 2) ?></td>
                                        <td>rumus</td>
                                        <td></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Notifikasi Flashdata -->
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

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <!-- Script AJAX untuk Filter -->
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Inisialisasi DataTable pada Tabel yang memiliki id dataTable
            $('#dataTable').DataTable();

        });
    </script>
</div>
<?php $this->endSection(); ?>