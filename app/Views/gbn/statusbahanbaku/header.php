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



                </ul>
            </div>
        </div>
    </nav>
    <?= $this->renderSection('content'); ?>

    <?php $this->endSection(); ?>