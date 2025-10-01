<?php $this->extend($role . '/warehouse/header'); ?>
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
                            <a href="<?= base_url($role . '/warehouse/generatePindahOrderBarcode/' . $detail[0]['no_model']) ?>" class="btn btn-info">
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
                        <table id="listPindahOrderBarcode" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No Karung</th>
                                    <th>No Model</th>
                                    <th>Item Type</th>
                                    <th>Kode Warna</th>
                                    <th>Warna</th>
                                    <th>Lot</th>
                                    <th>Kgs</th>
                                    <th>Cones</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detail as $det) : ?>
                                    <tr>
                                        <td><?= $det['no_karung'] ?></td>
                                        <td><?= $det['no_model'] ?></td>
                                        <td><?= $det['item_type'] ?></td>
                                        <td><?= $det['kode_warna'] ?></td>
                                        <td><?= $det['warna'] ?></td>
                                        <td><?= $det['lot_kirim'] ?></td>
                                        <td><?= $det['kgs_kirim'] ?></td>
                                        <td><?= $det['cones_kirim'] ?></td>
                                        <td><?= $det['admin'] ?></td>
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
        $('#listPindahOrderBarcode').DataTable();
    });
</script>
<?php $this->endSection(); ?>