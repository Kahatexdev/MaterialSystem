<?php $this->extend($role . '/retur/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                            <h5 class="font-weight-bolder mb-0">Data <?= $title ?></h5>
                        </div>
                        <div>
                            <a href="<?= base_url($role . '/retur/generateBarcodeRetur/' . $detailRetur[0]['tgl_retur']) ?>" class="btn btn-info">
                                Generate <i class="fas fa-barcode ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="listBarcodeRetur" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Model</th>
                                    <th>Item Type</th>
                                    <th>Kode Warna</th>
                                    <th>Warna</th>
                                    <th>Lot</th>
                                    <th>Kgs Retur</th>
                                    <th>Cones Retur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($detailRetur as $retur) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $retur['no_model'] ?></td>
                                        <td><?= $retur['item_type'] ?></td>
                                        <td><?= $retur['kode_warna'] ?></td>
                                        <td><?= $retur['warna'] ?></td>
                                        <td><?= $retur['lot_retur'] ?></td>
                                        <td><?= $retur['kgs_retur'] ?></td>
                                        <td><?= $retur['cns_retur'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#listBarcodeRetur').DataTable();
    });
</script>
<?php $this->endSection(); ?>