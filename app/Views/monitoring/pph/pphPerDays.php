2<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>
<!-- End Navbar -->
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
                                    PPH: Per Days
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
                    <table id="example" class="display compact" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">TGL PROD</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Inisial</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Warna</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Komposisi (%)</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">GW</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">TOTAL PPH</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Produksi</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Sisa</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Kebutuhan</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Total Pemakaian</th>
                                <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">% Pemakaian</th>

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
                    url: '<?= base_url($role . '/tampilPerDays') ?>',
                    type: 'POST'
                },
                "columns": [{
                        "data": "no",
                        "orderable": false
                    },
                    {
                        "data": "tgl_prod"
                    },
                    {
                        "data": "no_model"
                    },
                    {
                        "data": "inisial"
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
                        "data": "komposisi"
                    },
                    {
                        "data": "gw"
                    },
                    {
                        "data": "total_pph"
                    },
                ],
                "order": [
                    [1, "asc"]
                ] // Urutkan berdasarkan kolom TGL PROD
            });
        });
    </script>


    <?php $this->endSection(); ?>