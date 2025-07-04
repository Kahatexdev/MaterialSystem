<?php $this->extend($role . '/layout'); ?>
<?php $this->section('header'); ?>
<style>
    :root {
        --primary-color: #2e7d32;
        /* secondary color is abu-abu*/
        --secondary-color: #778899;
        --background-color: #f4f7fa;
        --card-background: #ffffff;
        --text-color: #333333;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Arial', sans-serif;
    }

    .container-fluid {
        /* max-width: 1200px; */
        margin: 0 auto;
        padding: 2rem;
    }

    .card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        /* padding: 0.1rem; */
        margin-bottom: 1rem;
    }

    /* .form-control {
        border: none;
        border-bottom: 2px solid var(--primary-color);
        border-radius: 0;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    } */

    .form-control:focus {
        box-shadow: none;
        border-color: var(--secondary-color);
    }

    .btn {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .result-card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }
</style>
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
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-arrow-down"></i>
                                <span class="d-lg-inline-block d-none ms-1">Pemasukan</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="<?= base_url($role . '/pemasukan') ?>">Pemasukan</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/otherIn') ?>">Pemasukan Lain-lain</a></li>
                            </ul>
                            <!-- <a href="" class="nav-link text-body font-weight-bold px-2" title="Pemasukan" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="fas fa-arrow-down"></i>
                                <span class="d-lg-inline-block d-none ms-1">Pemasukan</span>
                            </a> -->
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-arrow-up"></i>
                                <span class="d-lg-inline-block d-none ms-1">Pengeluaran</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="<?= base_url($role . '/pengiriman_area') ?>">Pengiriman Area</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt"></i>
                                <span class="d-lg-inline-block d-none ms-1">Reports</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownMenuLink">
                                <li><a href="<?= base_url($role . '/warehouse/reportPoBenang') ?>" class="dropdown-item" href="#">PO Benang</a></li>
                                <li><a href="<?= base_url($role . '/warehouse/reportDatangBenang') ?>" class="dropdown-item" href="#">Datang Benang</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportPengiriman') ?>">Report Pengiriman</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportGlobal') ?>">Report Global All BB</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportGlobal') ?>">Report Global Nylon</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportGlobalStockBenang') ?>">Report Global Benang</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportSisaPakaiBenang') ?>">Report Sisa Pakai Benang</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportSisaPakaiNylon') ?>">Report Sisa Pakai Nylon</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportSisaPakaiSpandex') ?>">Report Sisa Pakai Spandex</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/reportSisaPakaiKaret') ?>">Report Sisa Pakai Karet</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/otherIn/listBarcode') ?>">List Barcode</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/warehouse/historyPindahOrder') ?>">Report History Pindah Order</a></li>
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