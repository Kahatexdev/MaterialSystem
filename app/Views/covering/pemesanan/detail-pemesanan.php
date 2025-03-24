<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Success!",
                html: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                width: 600,
                padding: "3em",
                color: "#716add",
                background: "#fff url(/images/trees.png)",
                backdrop: `
                    rgba(0,0,123,0.4)
                    url("/images/nyan-cat.gif")
                    left top
                    no-repeat
                `
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Error!",
                html: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                width: 600,
                padding: "3em",
                color: "#716add",
                background: "#fff url(/images/trees.png)",
                backdrop: `
                    rgba(0,0,123,0.4)
                    url("/images/nyan-cat.gif")
                    left top
                    no-repeat
                `
            });
        });
    </script>
<?php endif; ?>


<style>
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
</style>

<div class="card card-frame">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bolder">Detail Pemesanan <?= $listPemesanan[0]['jenis'] ?></h5>

        </div>
    </div>
</div>

<!-- Tabel Data -->
<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th class="sticky text-center">No</th>
                        <th class="sticky text-center">Tanggal Pakai</th>
                        <th class="sticky text-center">Item Type</th>
                        <th class="sticky text-center">Warna</th>
                        <th class="sticky text-center">Kode Warna</th>
                        <th class="sticky text-center">No Model</th>
                        <th class="sticky text-center">Jalan MC</th>
                        <th class="sticky text-center">Total Pesan (Kg)</th>
                        <th class="sticky text-center">Cones</th>
                        <th class="sticky text-center">Area</th>
                        <th class="sticky text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($listPemesanan as $list) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= $list['tgl_pakai']; ?></td>
                            <td class="text-center"><?= $list['item_type']; ?></td>
                            <td class="text-center"><?= $list['color']; ?></td>
                            <td class="text-center"><?= $list['kode_warna']; ?></td>
                            <td class="text-center"><?= $list['no_model']; ?></td>
                            <td class="text-center"><?= $list['jl_mc']; ?></td>
                            <td class="text-center"><?= $list['total_pesan']; ?> Kg</td>
                            <td class="text-center"><?= $list['total_cones']; ?></td>
                            <td class="text-center"><?= $list['admin']; ?></td>
                            <td class="text-center">
                                <a href="" class="btn btn-sm btn-info">Kirim</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>