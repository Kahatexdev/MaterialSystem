<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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

    .table th {
        background-color: #e8f5e9;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #2e7d32;
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: #f1f8e9;
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        /* Untuk tetap di bagian atas saat menggulir vertikal */
        z-index: 3;
        /* Pastikan header terlihat di atas elemen lain */
        background-color: #e8f5e9;
        /* Warna latar belakang */
    }

    .table td.sticky {
        position: sticky;
        left: 0;
        /* Untuk tetap di sisi kiri saat menggulir horizontal */
        z-index: 2;
        /* Prioritas lebih rendah dari header */
        background-color: #e8f5e9;
        /* Tambahkan warna latar belakang */
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
        /* Memberikan efek bayangan untuk memisahkan kolom */

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
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    .btn-filter {
        background: linear-gradient(135deg, #4caf50, #81c784);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #43a047, #66bb6a);
    }

    .date-navigation {
        background-color: white;
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(76, 175, 80, 0.1);
    }

    .date-navigation input[type="date"] {
        border: none;
        font-weight: 500;
        color: #2e7d32;
    }

    .machine-info {
        font-size: 0.85rem;
    }

    .machine-info strong {
        font-size: 1rem;
        color: #2e7d32;
    }

    .job-item {
        background-color: white;
        border-radius: 10px;
        padding: 0.7rem;
        margin-bottom: 0.7rem;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.1);
        transition: all 0.2s ease;
    }

    .job-item:hover {
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    .job-item span {
        font-size: 0.8rem;
        color: #558b2f;
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

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <h1 class="display-5 mb-4 text-center" style="color: #2e7d32; font-weight: 600;">Out Celup</h1>
    <a href="<?= base_url($role . '/insertBon') ?>" class="btn btn-info">Create BON</a>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sticky">No</th>
                            <!-- <th class="sticky">No Mc</th> -->
                            <th class="sticky">PO</th>
                            <th class="sticky">Jenis Benang</th>
                            <th class="sticky">Kode Warna</th>
                            <th class="sticky">Warna</th>
                            <th class="sticky">Start Mc</th>
                            <!-- <th class="sticky">Delivery Export Awal</th>
                            <th class="sticky">Delivery Export Akhir</th> -->
                            <!-- <th class="sticky">Tanggal Schedule</th> -->
                            <th class="sticky">Qty PO</th>
                            <th class="sticky">Qty PO(+)</th>
                            <!-- <th class="sticky">Tanggal Celup</th> -->
                            <th class="sticky">Qty Celup</th>
                            <th class="sticky">Qty Celup(+)</th>
                            <th class="sticky">Aksi</th>
                            <!-- <th class="sticky">LOT Celup</th>
                            <th class="sticky">Bon</th>
                            <th class="sticky">Celup</th>
                            <th class="sticky">Bongkar</th>
                            <th class="sticky">Press</th>
                            <th class="sticky">Oven</th>
                            <th class="sticky">Rajut Pagi</th>
                            <th class="sticky">ACC</th>
                            <th class="sticky">Kelos</th>
                            <th class="sticky">Reject</th>
                            <th class="sticky">Perbaikan</th>
                            <th class="sticky">Ket Daily Cek</th>
                            <th class="sticky">Edit</th>
                            <th class="sticky">Ket Sch</th>
                            <th class="sticky">Updated By</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $no = 1;
                            foreach ($schedule as $sch) { ?>
                                <td><?= $no++ ?></td>
                                <td><?= $sch['noModel'] ?></td>
                                <td><?= $sch['itemType'] ?></td>
                                <td><?= $sch['kodeWarna'] ?></td>
                                <td><?= $sch['warna'] ?></td>
                                <td><?= $sch['startMc'] ?></td>
                                <td><?= $sch['qtyPo'] ?></td>
                                <td></td>
                                <!-- <td><?= $sch['tanggalSchedule'] ?></td> -->
                                <td><?= $sch['qtyCelup'] ?></td>
                                <td><?= $sch['qtyCelupPlus'] ?></td>
                                <td><a href="<?= base_url($role . "/insertBon/" . $sch['idCelup']) ?>" class="btn btn-info">Bon</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<?php $this->endSection(); ?>