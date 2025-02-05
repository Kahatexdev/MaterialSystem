<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
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
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5>
                            Pengeluaran
                        </h5>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <form action="<?= base_url($role . '/prosespemasukan') ?>" method="post">
                                <div class="form-group">
                                    <label for="" class="form-control-label">Scan Barcode</label>
                                    <input class="form-control" type="text" name="barcode" id="barcode" autofocus>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
            <div class="card my-1">
                <div class="card-body">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <h6>

                            </h6>
                            <button class="btn btn-danger">
                                Hapus
                            </button>
                        </div>

                    </div>
                    <div class="row">
                        <strong></strong>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="table-responsive">
                                <table id="poTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" name="select_all" id="select_all" value=""></th>
                                            <th width=30 class="text-center">No</th>
                                            <th class="text-center">Tgl Masuk</th>
                                            <th class="text-center">No Model</th>
                                            <th class="text-center">Item Type</th>
                                            <th class="text-center">Kode Warna</th>
                                            <th class="text-center">Warna</th>
                                            <th class="text-center">Kgs Masuk</th>
                                            <th class="text-center">Cones Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="center"><input type="checkbox" name="checked_id[]" class="checkbox" value=""></td>
                                            <td><input type="text" class="form-control text-center" name="no[]" readonly></td>
                                            <td><input type="date" class="form-control" name="tgl_masuk[]" readonly></td>
                                            <td><input type="text" class="form-control" name="no_model[]" readonly></td>
                                            <td><input type="text" class="form-control" name="item_type[]" readonly></td>
                                            <td><input type="text" class="form-control" name="kode_warna[]" readonly></td>
                                            <td><input type="text" class="form-control" name="warna[]" readonly></td>
                                            <td><input type="number" class="form-control" name="kgs_masuk[]" readonly></td>
                                            <td><input type="number" class="form-control" name="cns_masuk[]" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#select_all').on('click', function() {
                    if (this.checked) {
                        $('.checkbox').each(function() {
                            this.checked = true;
                        });
                    } else {
                        $('.checkbox').each(function() {
                            this.checked = false;
                        });
                    }
                });
            });
        </script>

        <?php $this->endSection(); ?>