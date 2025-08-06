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
                            <h5 class="font-weight-bolder mb-0">Data <?= $title . ' ' . $detailOtherBarcode[0]['tgl_datang'] ?></h5>
                        </div>
                        <div>
                            <a href="<?= base_url($role . '/warehouse/generateOtherBarcode/' . $detailOtherBarcode[0]['tgl_datang']) ?>" class="btn btn-info">
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
                        <table id="listOtherBarcode" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No SJ</th>
                                    <th>No Model</th>
                                    <th>Item Type</th>
                                    <th>Kode Warna</th>
                                    <th>Warna</th>
                                    <th>Lot</th>
                                    <th>Kgs Kirim</th>
                                    <th>Cones Kirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($detailOtherBarcode as $other) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $other['no_surat_jalan'] ?></td>
                                        <td><?= $other['no_model'] ?></td>
                                        <td><?= $other['item_type'] ?></td>
                                        <td><?= $other['kode_warna'] ?></td>
                                        <td><?= $other['warna'] ?></td>
                                        <td><?= $other['lot_kirim'] ?></td>
                                        <td><?= $other['kgs_kirim'] ?></td>
                                        <td><?= $other['cones_kirim'] ?></td>
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
        $('#listOtherBarcode').DataTable();
    });
</script>
<?php $this->endSection(); ?>