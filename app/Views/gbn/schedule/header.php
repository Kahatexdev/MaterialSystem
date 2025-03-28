<?php $this->extend($role . '/layout'); ?>
<?php $this->section('header'); ?>
<style>
    .card {
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.1);
        border: none;
        background-color: white;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 15px 30px rgba(76, 175, 80, 0.15);
        transform: translateY(-5px);
    }

    .table {
        border-radius: 15px;
        /* overflow: hidden; */
        border-collapse: separate;
        /* Ganti dari collapse ke separate */
        border-spacing: 0;
        /* Pastikan jarak antar sel tetap rapat */
        overflow: auto;
        position: relative;
    }

    .table thead {
        position: sticky;
        top: 0;
        z-index: 4;
        /* z-index lebih tinggi dari baris konten */
    }

    .table th {

        background-color: rgb(8, 38, 83);
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgb(255, 255, 255);
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: rgb(237, 237, 237);
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: rgb(4, 55, 91);
    }


    .table td.sticky {
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #e3f2fd;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
    }

    .capacity-bar {
        height: 6px;
        border-radius: 3px;
        margin-bottom: 5px;
    }

    .btn {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
    }

    .btn-filter {
        background: linear-gradient(135deg, #1e88e5, #64b5f6);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
    }

    .date-navigation {
        background-color: white;
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(33, 150, 243, 0.1);
    }

    .date-navigation input[type="date"] {
        border: none;
        font-weight: 500;
        color: #1565c0;
    }

    .machine-info {
        font-size: 0.85rem;
    }

    .machine-info strong {
        font-size: 1rem;
        color: #1565c0;
    }

    .job-item {
        background-color: white;
        border-radius: 10px;
        padding: 0.7rem;
        margin-bottom: 0.7rem;
        box-shadow: 0 2px 4px rgba(33, 150, 243, 0.1);
        transition: all 0.2s ease;
    }

    .job-item:hover {
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
    }

    .job-item span {
        font-size: 0.8rem;
        color: #0d47a1;
    }

    .job-item .btn {
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .job-item .btn span {
        font-size: 0.9rem;
        color: black;
        font-weight: bold;
    }

    .job-item .btn .total-kg {
        font-size: 0.85rem;
    }

    .no-schedule .btn {
        background-color: #f8f9fa;
        border: 1px dashed #ccc;
        color: #6c757d;
    }


    .bg-success {
        background-color: #66bb6a !important;
    }

    .bg-warning {
        background-color: #ffd54f !important;
    }

    .bg-danger {
        background-color: #ef5350 !important;
    }

    .text-success {
        color: #43a047 !important;
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
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url($role . '/mesin/mesinCelup') ?>" class="nav-link text-body font-weight-bold px-2">
                                <i class="fas fa-database"></i>
                                <span class="d-lg-inline-block d-none ms-1">Data Mesin Celup</span>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url($role . '/schedule/reqschedule') ?>" class="nav-link text-body font-weight-bold px-2">
                                <i class="fas fa-calendar-check"></i>
                                <span class="d-lg-inline-block d-none ms-1">ReqSchedule</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="d-lg-inline-block d-none ms-1">Schedule</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownMenuLink">
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule/acrylic') ?>">Acrylic</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule') ?>">Benang</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule/nylon') ?>">Nylon</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule/sample') ?>">MC Sample</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt"></i>
                                <span class="d-lg-inline-block d-none ms-1">Reports</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule/reportSchBenang') ?>">Benang</a></li>
                                <li><a class="dropdown-item" href="<?= base_url($role . '/schedule/reportSchNylon') ?>">Nylon</a></li>
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