<?php $this->extend($role . '/warehousebb/header'); ?>
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
<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Stock Bahan Baku </h5>
                <div class="d-flex">
                    <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Bahan Baku
                    </button>
                    <button class="btn bg-gradient-success ms-2" onclick="window.location.href='<?= base_url($role . '/warehouseBB/BahanBakuCovPdf') ?>'">
                        <i class="fas fa-file-excel me-2"></i> Export
                    </button>
                </div>
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
                            <th class="sticky text-center">Jenis</th>
                            <th class="sticky text-center">Denier</th>
                            <th class="sticky text-center">Warna</th>
                            <th class="sticky text-center">Kode Warna</th>
                            <th class="sticky text-center">Stock</th>
                            <th class="sticky text-center">Keterangan</th>
                            <th class="sticky text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($warehouseBB as $list) : ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= $list['jenis_benang']; ?></td>
                                <td class="text-center"><?= $list['denier']; ?></td>
                                <td class="text-center"><?= $list['warna']; ?></td>
                                <td class="text-center"><?= $list['kode']; ?></td>
                                <td class="text-center"><?= $list['kg']; ?></td>
                                <td class="text-center"><?= $list['keterangan']; ?></td>
                                <td class="text-center">
                                    <!-- button modal edit -->
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $list['idstockbb'] ?>">
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

    <!-- Modal Tambah warehouseBB -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addModalLabel">Tambah Stock Bahan Baku</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url($role . '/warehouseBB/store') ?>" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Benang</label>
                                <select class="form-select" name="jenis_benang" required>
                                    <option value="">Pilih Jenis Benang</option>
                                    <option value="NYLON">NYLON</option>
                                    <option value="POLYESTER/SPDX/KRT">POLYESTER/SPDX/KRT</option>
                                    <option value="RECYCLED POLYESTER">RECYCLED POLYESTER</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Denier</label>
                                <input type="text" class="form-control" name="denier" placeholder="Masukkan Denier" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Warna</label>
                                <input type="text" class="form-control" name="warna" placeholder="Masukkan Warna" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Warna</label>
                                <input type="text" class="form-control" name="kode" placeholder="Masukkan Kode Warna" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock (Kg)</label>
                                <input type="number" min="0.01" step="0.01" class="form-control" name="kg" placeholder="Masukkan Stock (Kg)" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan" placeholder="Masukkan Keterangan">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-info">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pemesanan -->
    <?php foreach ($warehouseBB as $list) : ?>
        <div class="modal fade" id="editModal<?= $list['idstockbb'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="editModalLabel">Update Stock Bahan Baku</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url($role . '/warehouseBB/update/' . $list['idstockbb']) ?>" method="POST">
                        <?= csrf_field(); ?>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Benang</label>
                                    <select class="form-select" name="jenis_benang" required>
                                        <option value="NYLON" <?= $list['jenis_benang'] == 'NYLON' ? 'selected' : '' ?>>NYLON</option>
                                        <option value="POLYESTER/SPDX/KRT" <?= $list['jenis_benang'] == 'POLYESTER/SPDX/KRT' ? 'selected' : '' ?>>POLYESTER/SPDX/KRT</option>
                                        <option value="RECYCLED POLYESTER" <?= $list['jenis_benang'] == 'RECYCLED POLYESTER' ? 'selected' : '' ?>>RECYCLED POLYESTER</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Denier</label>
                                    <input type="text" class="form-control" name="denier" value="<?= $list['denier'] ?>" placeholder="Masukkan Denier" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Warna</label>
                                    <input type="text" class="form-control" name="warna" value="<?= $list['warna'] ?>" placeholder="Masukkan Warna" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" name="kode" value="<?= $list['kode'] ?>" placeholder="Masukkan Kode Warna" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock (Kg)</label>
                                    <input type="number" class="form-control" name="kg" value="<?= $list['kg'] ?>" placeholder="Masukkan Stock (Kg)" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" value="<?= $list['keterangan'] ?>" placeholder="Masukkan Keterangan">
                                </div>

                                <!-- Hidden values for reference -->
                                <input type="hidden" name="idstockbb" value="<?= $list['idstockbb'] ?>">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-info">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "responsive": true,
        });
    });
</script>

<?php $this->endSection(); ?>