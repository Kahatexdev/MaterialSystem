<?php $this->extend($role . '/layout'); ?>
<?php $this->section('header'); ?>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
            </nav>
            <div class="collgbne navbar-collgbne mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">

                <ul class="navbar-nav  justify-content-end">
                    <li class="nav-item d-flex align-items-center px-1">
                        <a href="<?= base_url(session()->get('role')) . '/masterMaterial' ?>" class="nav-link text-body font-weight-bold px-2">
                            <i class="fas fa-database"></i>
                            <span class="d-lg-inline-block d-none ms-1">
                                Master Material</span>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="" data-bs-toggle="modal" data-bs-target="#LogoutModal" class=" nav-link text-body font-weight-bold px-0">
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
    <?= $this->renderSection('content'); ?>

    <?php $this->endSection(); ?>