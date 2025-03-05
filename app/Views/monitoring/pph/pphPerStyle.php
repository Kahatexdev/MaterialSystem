<?php $this->extend($role . '/pph/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    PPH: Per Style
                                </h5>
                            </div>
                        </div>
                        <div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="example" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Jarum</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Delivery</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Jenis</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Los</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Komposisi (%)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">GW</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Qty PO</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Produksi</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Sisa</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Kebutuhan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Pemakaian</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">% Pemakaian</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?= base_url($role . '/tampilPerStyle') ?>',
                    type: 'POST'
                },
                "columns": [{
                        "data": "no",
                        "orderable": false
                    },
                    {
                        "data": "jarum"
                    },
                    {
                        "data": "no_model"
                    },
                    {
                        "data": "area"
                    },
                    {
                        "data": "delivery"
                    },
                    {
                        "data": "jenis"
                    },
                    {
                        "data": "warna"
                    },
                    {
                        "data": "kode_warna"
                    },
                    {
                        "data": "los"
                    },
                    {
                        "data": "komposisi"
                    },
                    {
                        "data": "gw"
                    },
                    {
                        "data": "qty_po"
                    },
                    {
                        "data": "total_produksi"
                    },
                    {
                        "data": "sisa"
                    },
                    {
                        "data": "total_kebutuhan"
                    },
                    {
                        "data": "total_pemakaian"
                    },
                    {
                        "data": "persen_pemakaian"
                    },
                ],
                "order": [
                    [1, "asc"]
                ] // Urutkan berdasarkan kolom jarum
            });
        });
    </script>

    <?php $this->endSection(); ?>