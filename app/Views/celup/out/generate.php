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

    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-header">
                    <h3>Out Celup</h3>
                </div>
                <div class="group">
                    <a href="<?= base_url($role . '/printBon/' . $id_bon) ?>" class="btn btn-info" target="_blank">
                        <i class="ni ni-single-copy-04 me-2"></i>
                        PRINT
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <!-- Bon Pengiriman -->
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sticky text-center align-middle" rowspan="3" colspan="2">
                                <div class="text-center">
                                    <img src="<?= base_url('assets/img/logo-kahatex.png') ?>" alt="Deskripsi Gambar" width="100">
                                    <div>PT.KAHATEX</div>
                                </div>
                            </th>
                            <th class="sticky text-center align-middle" colspan="13">FORMULIR</th>
                        </tr>
                        <tr>
                            <th class="sticky text-center align-middle" colspan="13">DEPARTEMEN KELOS WARNA</th>
                        </tr>
                        <tr>
                            <th class="sticky text-center align-middle" colspan="13">BON PENGIRIMAN</th>
                        </tr>
                        <tr>
                            <th class="sticky text-left align-middle" colspan="2">No. Dokumen</th>
                            <th class="sticky text-left align-middle" colspan="8">FOR-KWA-006/REV_03/HAL_1/1</th>
                            <th class="sticky text-center align-middle" colspan="3">TANGGAL REVISI</th>
                            <th class="sticky text-center align-middle" colspan="2">07 Januari 2021</th>
                        </tr>
                        <tr>
                            <th class="sticky text-left align-middle" colspan="2">NAMA LANGGANAN</th>
                            <th class="sticky text-left align-middle" colspan="3">KAOS KAKI</th>
                            <th class="sticky text-left align-middle" colspan="6">NO SURAT JALAN : <?= $dataBon['no_surat_jalan'] ?></th>
                            <th class="sticky text-left align-middle" colspan="4">TANGGAL : <?= $dataBon['tgl_datang'] ?></th>
                        </tr>
                        <tr>
                            <th class="sticky text-center align-middle" rowspan="2">NO PO</th>
                            <th class="sticky text-center align-middle" rowspan="2">JENIS BENANG</th>
                            <th class="sticky text-center align-middle" rowspan="2">KODE BENANG</th>
                            <th class="sticky text-center align-middle" rowspan="2">KODE WARNA</th>
                            <th class="sticky text-center align-middle" rowspan="2">WARNA</th>
                            <th class="sticky text-center align-middle" rowspan="2">LOT CELUP</th>
                            <th class="sticky text-center align-middle" rowspan="2">L/M/D</th>
                            <th class="sticky text-center align-middle">HARGA</th>
                            <th class="sticky text-center align-middle" rowspan="2">CONES</th>
                            <th class="sticky text-center align-middle" colspan="2">QTY</th>
                            <th class="sticky text-center align-middle" colspan="3">TOTAL</th>
                            <th class="sticky text-center align-middle" rowspan="2">KETERANGAN</th>
                        </tr>
                        <tr>
                            <th class="sticky text-center align-middle">PER KG</th>
                            <th class="sticky text-center align-middle">GW (KG)</th>
                            <th class="sticky text-center align-middle">NW (KG)</th>
                            <th class="sticky text-center align-middle">CONES</th>
                            <th class="sticky text-center align-middle">GW (KG)</th>
                            <th class="sticky text-center align-middle">NW (KG)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $counter = [];
                            $prevNoModel = null; // Variabel untuk menyimpan no_model sebelumnya
                            $prevItemType = null; // Variabel untuk menyimpan item_type sebelumnya
                            $prevKodeWarna = null; // Variabel untuk menyimpan kode_warna sebelumnya
                            foreach ($dataBon['groupedDetails'] as $bon) {
                                $key = $bon['no_model'] . '_' . $bon['item_type'] . '_' . $bon['kode_warna'];
                                // Jika kombinasi tersebut belum ada di array counter, buat entri baru
                                if (!isset($counter[$key])) {
                                    $counter[$key] = 0;
                                }
                                foreach ($bon['detailPengiriman'] as $detail) {
                                    $counter[$key]++;
                                }
                                // Cek jika item_type atau kode_warna berubah
                                if (($prevNoModel !== null && $bon['no_model'] !== $prevItemType) && ($prevItemType !== null && $bon['item_type'] !== $prevItemType) &&
                                    ($prevKodeWarna !== null && $bon['kode_warna'] !== $prevKodeWarna)
                                ) {
                                    // Tambahkan dua baris kosong
                                    for ($i = 0; $i < 3; $i++) {
                                        echo
                                        '<tr>';
                                        '<td class="text-center" colspan="15"></td>';
                                        '</tr>';
                                    }
                                }
                            ?>
                                <td class="text-center"><?= $bon['no_model'] ?></td>
                                <td class="text-center"><?= $bon['item_type'] ?></td>
                                <td class="text-center"><?= $bon['ukuran'] ?></td>
                                <td class="text-center"><?= $bon['kode_warna'] ?></td>
                                <td class="text-center"><?= $bon['warna'] ?></td>
                                <td class="text-center"><?= $bon['lot_kirim'] ?></td>
                                <td class="text-center"><?= $bon['l_m_d'] ?></td>
                                <td class="text-center"><?= $bon['harga'] ?></td>
                                <?php
                                $row = 0;
                                foreach ($bon['detailPengiriman'] as $detail) {
                                    $row++;
                                    if ($counter[$key] == 1) { ?>
                                        <td class="text-center"><?= $detail['cones_kirim'] ?></td>
                                        <td class="text-center"><?= $detail['gw_kirim'] ?></td>
                                        <td class="text-center"><?= $detail['kgs_kirim'] ?></td>
                                        <td class="text-center"><?= $bon['totals']['cones_kirim'] ?></td>
                                        <td class="text-center"><?= $bon['totals']['gw_kirim'] ?></td>
                                        <td class="text-center"><?= $bon['totals']['kgs_kirim'] ?></td>
                                        <td class="text-center"><?= $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-center"><?= $bon['buyer'] ?> KK</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php } else {
                                        if ($row == 1) { ?>
                            <td class="text-center"><?= $detail['cones_kirim'] ?></td>
                            <td class="text-center"><?= $detail['gw_kirim'] ?></td>
                            <td class="text-center"><?= $detail['kgs_kirim'] ?></td>
                            <td class="text-center"><?= $bon['totals']['cones_kirim'] ?></td>
                            <td class="text-center"><?= $bon['totals']['gw_kirim'] ?></td>
                            <td class="text-center"><?= $bon['totals']['kgs_kirim'] ?></td>
                            <td class="text-center"><?= $bon['jmlKarung'] . " KARUNG" . $bon['ganti_retur'] ?></td>
                            </tr>
                        <?php } elseif ($row == 2) { ?>
                            <td class="text-center"><?= $bon['buyer'] ?> KK</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><?= $detail['cones_kirim'] ?></td>
                            <td class="text-center"><?= $detail['gw_kirim'] ?></td>
                            <td class="text-center"><?= $detail['kgs_kirim'] ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tr>
                        <?php } else { ?>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><?= $detail['cones_kirim'] ?></td>
                            <td class="text-center"><?= $detail['gw_kirim'] ?></td>
                            <td class="text-center"><?= $detail['kgs_kirim'] ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            </tr>
                    <?php }
                                    } ?>
                    </tr>
            <?php }
                                $prevNoModel = $bon['no_model']; // Pindahkan pembaruan variabel ke sini
                                $prevItemType = $bon['item_type']; // Pindahkan pembaruan variabel ke sini
                                $prevKodeWarna = $bon['kode_warna']; // Pindahkan pembaruan variabel ke sini
                            } ?>
                    </tbody>
                    <footer>
                        <tr>
                            <th class="sticky text-center align-middle">KETERANGAN : </th>
                            <th class="sticky text-left align-middle" colspan="8">GW = GROSS WEIGHT</th>
                            <th class="sticky text-center align-middle" colspan="4">PENGIRIM</th>
                            <th class="sticky text-center align-middle" colspan="2">PENERIMA</th>
                        </tr>
                        <tr>
                            <th class="sticky text-left align-middle"></th>
                            <th class="sticky text-left align-middle" colspan="14">NW = NETT WEIGHT</th>
                        </tr>
                        <tr>
                            <th class="sticky text-left align-middle"></th>
                            <th class="sticky text-left align-middle" colspan="14">L = LIGHT</th>
                        <tr>
                            <th class="sticky text-left align-middle"></th>
                            <th class="sticky text-left align-middle" colspan="14">M = MEDIUM</th>
                        </tr>
                        <tr>
                            <th class="sticky text-left align-middle"></th>
                            <th class="sticky text-left align-middle" colspan="8">D = DARK</th>
                            <th class="sticky text-center align-middle" colspan="4"></th>
                            <th class="sticky text-center align-middle" colspan="2"></th>
                        </tr>
                    </footer>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-header">
                    <h3>Barcode</h3>
                    <div class="row">
                        <?php foreach ($dataBon['groupedDetails'] as $group) { ?>
                            <?php foreach ($group['barcodes'] as $barcode) { ?>
                                <div class="col-md-4 d-flex flex-column align-items-center mt-4">
                                    <div style="margin-top: 10px;">
                                        <img
                                            src="data:image/png;base64,<?= $barcode['barcode']; ?>"
                                            alt="Barcode for <?= $barcode['id_out_celup']; ?>"
                                            style="width: 370px; height: auto; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                                    </div>
                                    <div class="details w-100">

                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>No Model</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['no_model'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Item Type</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['item_type'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Kode Warna</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['kode_warna'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Warna</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['warna'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Gw</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['gw'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Nw</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['kgs'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Cones</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['cones'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>Lot</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['lot'] ?></label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4" style="text-align: left;">
                                                <label>No Karung</label>
                                            </div>
                                            <div class=" col-md-8 text-left" style="text-align: left;">
                                                <label>: <?= $barcode['no_karung'] ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>