<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
            </nav>
            <div class="collgbne navbar-collgbne mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-flex align-items-center px-1">
                        <a href="">
                            <span class="badge bg-gradient-info">Submenu1</span>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center px-1">
                        <a href="">
                            <span class="badge bg-gradient-info">Submenu2</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown pe-2 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge bg-gradient-info">Dropdown <i class="ni ni-bold-down"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                            <li class="mb-2">
                                <a class="dropdown-item border-radius-md" href="javascript:;">
                                    <div class="d-flex py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="text-sm font-weight-normal mb-1">
                                                <span class="font-weight-bold">Dropdown</span>
                                            </h6>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="" data-bs-toggle="modal" data-bs-target="#LogoutModal" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none"><?= session()->get('username') ?></span>
                        </a>
                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
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
                                        PPH: Per Model
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
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jarum</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">No Model</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Area</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Jenis</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Kode Warna</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Los</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Komposisi (%)</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">GW</th>
                                    <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Qty PO</th>
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
                        url: '<?= base_url($role . '/tampilPerModel') ?>',
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
                        [4, "asc"]
                    ] // Urutkan berdasarkan kolom Delivery
                });
            });
        </script>


        <?php $this->endSection(); ?>