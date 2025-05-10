<?php $this->extend($role . '/layout'); ?>
<?php $this->section('header'); ?>

<style>
    .btn-filter {
        background: linear-gradient(135deg, #1e88e5, #64b5f6);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
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
                            <a href="<?= base_url($role . '/pemesanan/requestAdditionalTime') ?>" class="nav-link text-body font-weight-bold px-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="15">
                                    <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com -->
                                    <path d="M432 304c0 114.9-93.1 208-208 208S16 418.9 16 304c0-104 76.3-190.2 176-205.5V64h-28c-6.6 0-12-5.4-12-12V12c0-6.6 5.4-12 12-12h120c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-28v34.5c37.5 5.8 71.7 21.6 99.7 44.6l27.5-27.5c4.7-4.7 12.3-4.7 17 0l28.3 28.3c4.7 4.7 4.7 12.3 0 17l-29.4 29.4-.6 .6C419.7 223.3 432 262.2 432 304z" fill="#67748e" />

                                    <!-- Tanda plus putih besar dan sedikit turun -->
                                    <line x1="224" y1="244" x2="224" y2="364" stroke="#FFFFFF" stroke-width="50" stroke-linecap="round" />
                                    <line x1="164" y1="304" x2="284" y2="304" stroke="#FFFFFF" stroke-width="50" stroke-linecap="round" />
                                </svg>
                                <!-- <i class="fas fa-clock"></i> -->
                                <span class="d-lg-inline-block d-none ms-1">Waktu Pemesanan <sup style="color: red;" id="countWt"></sup></span>
                            </a>
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
    <script>
        function updateCount() {
            fetch('<?= base_url($role . '/pemesanan/getCountStatusRequest') ?>')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.querySelector('sup');
                    if (countElement) {
                        countElement.textContent = data.count;
                    }
                })
                .catch(error => console.error('Error fetching count:', error));
        }

        // Update count setiap 5 detik
        setInterval(updateCount, 5000);

        // Panggil fungsi pertama kali saat halaman dimuat
        updateCount();
    </script>
    <?= $this->renderSection('content'); ?>

    <?php $this->endSection(); ?>