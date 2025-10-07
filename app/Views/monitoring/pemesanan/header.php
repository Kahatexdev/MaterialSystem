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
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import"></i>
                            <span class="d-lg-inline-block d-none ms-1">IMPORT GOD<i class="bi bi-caret-down-fill"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                            <li><a class="dropdown-item" href="<?= base_url($role . '/importPemesanan') ?>">IMPORT PEMESANAN</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-clock"></i>
                            <span class="d-lg-inline-block d-none ms-1">Batas Waktu<i class="bi bi-caret-down-fill"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/ubahTanggalPemesanan') ?>">Ubah Tanggal Pemesanan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link text-body font-weight-bold px-2" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-alt"></i>
                            <span class="d-lg-inline-block d-none ms-1">Report <i class="bi bi-caret-down-fill"></i></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="navbarDropdownReports">
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/reportPemesananArea') ?>">Pemesanan Area</a></li>
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/permintaanKaretCovering') ?>">Permintaan Karet</a></li>
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/permintaanSpandexCovering') ?>">Permintaan Spandex</a></li>
                            <li><a class="dropdown-item" href="#" id="showModalButton">Persiapan Pengeluaran Barang</a></li>
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/sisaKebutuhanArea') ?>" id="showModalButton">Sisa Kebutuhan Area</a></li>
                            <li><a class="dropdown-item" href="<?= base_url($role . '/pemesanan/historyPinjamOrder') ?>" id="showModalButton">History Pinjam Order</a></li>
                        </ul>
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
        <!-- Modal persiapan barang -->
        <div class="modal fade" id="threadModal1" tabindex="-1" aria-labelledby="threadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="threadModalLabel">Pilih Jenis Benang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/pemesanan/listBarangKeluarPertgl') ?>" method="post">
                            <div class="mb-3">
                                <label for="threadType" class="form-label">Jenis Benang</label>
                                <select class="form-select" id="jenis_report" name="jenis" required>
                                    <option value="" selected disabled>Pilih Jenis Benang</option>
                                    <option value="BENANG">BENANG</option>
                                    <option value="NYLON">NYLON</option>
                                    <option value="SPANDEX">SPANDEX</option>
                                    <option value="KARET">KARET</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-info">Lanjutkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <script>
        $(document).ready(function() {
            // Tampilkan modal saat "List Barcode" diklik
            $('#showModalButton').on('click', function(e) {
                e.preventDefault(); // Mencegah redirect langsung
                $('#threadModal1').modal('show');
            });
        });
    </script>
    <?= $this->renderSection('content'); ?>

    <?php $this->endSection(); ?>