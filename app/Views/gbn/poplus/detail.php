<?php $this->extend($role . '/poplus/header'); ?>
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

    <!-- Header Card -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h5 class="mb-0 font-weight-bolder">Detail Po Tambahan Tanggal <?= $tglPo ?></h5>
                    <h4><?= $noModel ?> || <?= $itemType ?> || <?= $kodeWarna ?> || <?= $warna ?></h4>
                </div>
                <div class="col-6 text-end">
                    <a href="<?= base_url($role . '/poplus') ?>" class="btn bg-gradient-info"> Kembali</a>
                </div>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Style Size</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Comp (%)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW / Pcs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty / Pcs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pakai (Kg)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PO(+) Kg</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($detail as $data):
                        ?>
                            <tr>
                                <td class="text-center text-seccondary text-xxs"><?= $no++ ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= $data['style_size'] ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= $data['composition'] ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= $data['gw'] ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= $data['qty'] ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= $data['loss'] ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= number_format($data['ttl_kebutuhan'], 2) ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= number_format($data['pph'], 2) ?></td>
                                <td class="text-center text-seccondary text-xxs"><?= number_format($data['po_plus'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($detail)) : ?>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
    });
</script>
<?php $this->endSection(); ?>