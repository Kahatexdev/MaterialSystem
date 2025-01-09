<?php $this->extend($role . '/layout'); ?>
<?php $this->section('content'); ?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
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
                                    <h6 class="text-sm font-weight-normal mb-1">
                                        <span class="font-weight-bold">Dropdown</span>
                                    </h6>
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
        <div class="row mt-4">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        PPH
                                    </h5>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <!-- Kosong untuk saat ini -->
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">

            <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/tampilPerStyle/') ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-md mb-0 text-capitalize font-weight-bold">PPH: Per Style</p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </a>
            </div>

            <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/tampilPerDays/') ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-md mb-0 text-capitalize font-weight-bold">PPH: Per Days</p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </a>
            </div>

            <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4 mt-2">
                <a href="<?= base_url($role . '/tampilPerModel/') ?>">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-md mb-0 text-capitalize font-weight-bold">PPH: Per Model</p>
                                        <h5 class="font-weight-bolder mb-0">
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </a>
            </div>
        </div>
    </div>

    <?php $this->endSection(); ?>