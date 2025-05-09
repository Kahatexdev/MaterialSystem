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
                        <th class="sticky text-center">Status</th>
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
                            <td class="text-center"><?= number_format($list['total_pesan'], 2); ?></td>
                            <td class="text-center"><?= $list['total_cones']; ?></td>
                            <td class="text-center"><?= $list['admin']; ?></td>
                            <td class="text-center"><span class="badge bg-gradient <?= $list['status'] == 'REQUEST' ? 'bg-warning' : ($list['status'] == 'SEDANG DISIAPKAN' ? 'bg-info' : 'bg-success') ?>"><?= $list['status']; ?></span></td>
                            <td class="text-center">
                                <!-- button modal edit -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $list['id_psk'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Pemesanan -->
<?php foreach ($listPemesanan as $list) : ?>
    <div class="modal fade" id="editModal<?= $list['id_psk'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editModalLabel">Update Status Pemesanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url($role. '/updatePemesanan/' . $list['id_psk']) ?>" method="POST">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jalan MC</label>
                                <input type="text" class="form-control" value="<?= $list['jl_mc'] ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pesan (Kg)</label>
                                <input type="text" class="form-control" value="<?= number_format($list['total_pesan'], 2) ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Cones</label>
                                <input type="text" class="form-control" value="<?= $list['total_cones'] ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" value="<?= $list['admin'] ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pakai</label>
                                <input type="date" class="form-control" value="<?= $list['tgl_pakai'] ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="REQUEST" <?= $list['status'] == 'REQUEST' ? 'selected' : '' ?>>REQUEST</option>
                                    <option value="SEDANG DISIAPKAN" <?= $list['status'] == 'SEDANG DISIAPKAN' ? 'selected' : '' ?>>SEDANG DISIAPKAN</option>
                                    <option value="DONE" <?= $list['status'] == 'DONE' ? 'selected' : '' ?>>SELESAI</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hidden values for reference -->
                        <input type="hidden" name="id_psk" value="<?= $list['id_psk'] ?>">
                        <input type="hidden" name="jenis" value="<?= $list['jenis'] ?>">
                        <input type="hidden" name="tgl_pakai" value="<?= $list['tgl_pakai'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->endSection(); ?>