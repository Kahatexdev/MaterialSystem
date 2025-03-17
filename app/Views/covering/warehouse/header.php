<?php $this->extend($role . '/layout'); ?>
<?php $this->section('header'); ?>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0"><?= $title ?></h6>
                </nav>
                <div class="d-flex align-items-center">
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url($role . '/mesinCov') ?>" class="nav-link text-body font-weight-bold px-2">
                                <i class="fas fa-database"></i>
                                <span class="d-lg-inline-block d-none ms-1">Data Mesin Covering</span>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url($role . '/warehouse/pemasukan') ?>" class="nav-link text-body font-weight-bold px-2" title="Pemasukan" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="fas fa-arrow-down"></i>
                                <span class="d-lg-inline-block d-none ms-1">Pemasukan</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-arrow-up"></i>
                                <span class="d-lg-inline-block d-none ms-1">Pengeluaran</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/pengeluaran_jalur') ?>">Pengeluaran Jalur</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/pengiriman_area') ?>">Pengiriman Area</a></li>
                            </ul>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="" data-bs-toggle="modal" data-bs-target="#LogoutModal" class="nav-link text-body font-weight-bold px-2">
                                <i class="fa fa-user"></i>
                                <span class="d-lg-inline-block d-none ms-1"><?= session()->get('username') ?></span>
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
        </div>
    </nav>
    <?= $this->renderSection('content'); ?>

    <?php $this->endSection(); ?>