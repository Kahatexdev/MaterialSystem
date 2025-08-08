<?php $this->extend($role . '/warehousebb/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .summary-card {
        border-left: 4px solid #007bff;
    }

    .summary-card.total-stock {
        border-left-color: #28a745;
    }

    .summary-card.items-available {
        border-left-color: #17a2b8;
    }

    .summary-card.items-out-of-stock {
        border-left-color: #dc3545;
    }

    .warehouse-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    /* Optional: subtle hover effect */
    .btn-sm.btn-custom {
        transition: transform 0.1s ease-in-out;
    }

    .btn-sm.btn-custom:hover {
        transform: scale(1.02);
    }
</style>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => Swal.fire('Success!', '<?= session()->getFlashdata('success') ?>', 'success'));
        </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => Swal.fire('Error!', '<?= session()->getFlashdata('error') ?>', 'error'));
        </script>
    <?php endif; ?>

    <!-- ALERT FLASHDATA -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jenis Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalJenis['total_jenis'] ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card total-stock h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stok (Kg)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalKg['total_kg'], 2) ?> Kg</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-weight-hanging fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card items-available h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Barang Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $tersedia['jenis_benang_tersedia'] ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card summary-card items-out-of-stock h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Barang Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $kosong['jenis_benang_kosong'] ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-frame">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-4">
                    <h5 class="font-weight-bolder mb-0">Stock Bahan Baku</h5>
                </div>
                <div class="col-md-6 col-lg-8 d-flex justify-content-end flex-wrap gap-2">
                    <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                    <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-1"></i> Import Stok
                    </button>
                    <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#importJenisModal">
                        <i class="fas fa-file-import me-1"></i> Import Jenis
                    </button>
                    <button class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#modalExport">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- centered vertically -->
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title text-white" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Data Stok
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="importForm"
                    action="<?= base_url(session()->get('role') . '/warehouse/importStokBahanBaku') ?>"
                    method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="file_excel" class="form-label fw-semibold">Pilih File Excel</label>
                            <div class="input-group">
                                <input id="file_excel"
                                    type="file"
                                    class="form-control border-info rounded-pill px-3 py-2"
                                    name="file_excel"
                                    accept=".xlsx, .xls"
                                    required
                                    style="background-color: #f8f9fa; border-width: 2px;">
                            </div>
                            <small class="form-text text-muted ms-1">Hanya file Excel (.xlsx, .xls) yang diterima.</small>
                        </div>

                        <div class="alert alert-light border-info small">
                            <p class="mb-1">Pastikan file yang diupload sesuai dengan template yang telah disediakan. Jika belum memiliki template, silakan download:</p>
                            <a href="<?= base_url('covering/warehouse/templateStokBahanBaku') ?>"
                                class="text-white text-decoration-none fw-semibold badge bg-gradient-info"
                                style="font-size: 1rem;"
                                download>
                                <i class="fas fa-download me-1"></i>Template Import Stok Bahan Baku
                            </a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button id="importSubmit"
                            type="submit"
                            class="btn bg-gradient-info position-relative">
                            <span class="spinner-border spinner-border-sm text-white position-absolute top-50 start-50 translate-middle d-none"
                                role="status"
                                aria-hidden="true"></span>
                            <span class="btn-text">Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importJenisModal" tabindex="-1" aria-labelledby="importJenisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- centered vertically -->
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title text-white" id="importJenisModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Data Jenis
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="importForm"
                    action="<?= base_url(session()->get('role') . '/warehouse/importStokBahanBakuJenis') ?>"
                    method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="file_excel" class="form-label fw-semibold">Pilih File Excel</label>
                            <div class="input-group">
                                <input id="file_excel"
                                    type="file"
                                    class="form-control border-info rounded-pill px-3 py-2"
                                    name="file_excel"
                                    accept=".xlsx, .xls"
                                    required
                                    style="background-color: #f8f9fa; border-width: 2px;">
                            </div>
                            <small class="form-text text-muted ms-1">Hanya file Excel (.xlsx, .xls) yang diterima.</small>
                        </div>

                        <div class="alert alert-light border-info small">
                            <p class="mb-1">Pastikan file yang diupload sesuai dengan template yang telah disediakan. Jika belum memiliki template, silakan download:</p>
                            <a href="<?= base_url('template/CONTOH_FORMAT_IMPORT_STOK_JENIS BAHAN_BAKU_COVERING.xlsx') ?>"
                                class="text-white text-decoration-none fw-semibold badge bg-gradient-info"
                                style="font-size: 1rem;"
                                download>
                                <i class="fas fa-download me-1"></i>Template Import Stok Bahan Baku
                            </a>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button id="importSubmit"
                            type="submit"
                            class="btn bg-gradient-info position-relative">
                            <span class="spinner-border spinner-border-sm text-white position-absolute top-50 start-50 translate-middle d-none"
                                role="status"
                                aria-hidden="true"></span>
                            <span class="btn-text">Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/warehouseBB/nylon') ?>">
                <div class="card summary-card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">NYLON
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">

                                    <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/warehouseBB/polyester') ?>">
                <div class="card summary-card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">POLYESTER
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">

                                    <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/warehouseBB/recycledPolyester') ?>">
                <div class="card summary-card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">RECYCLED POLYESTER
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">

                                    <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/warehouseBB/spandex') ?>">
                <div class="card summary-card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">SPANDEX
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">

                                    <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
            <a href="<?= base_url($role . '/warehouseBB/rubber') ?>">
                <div class="card summary-card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-0">RUBBER
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">

                                    <i class="fas fa-folder text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
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
                                    <option value="POLYESTER">POLYESTER</option>
                                    <option value="SPANDEX">SPANDEX</option>
                                    <option value="KARET">KARET</option>
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

    <!-- Edit Item Modal -->
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
                        <input type="hidden" step="0.01" class="form-control" name="kg" value="<?= $list['kg'] ?>" placeholder="Masukkan Stock (Kg)" required>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Benang</label>
                                    <select class="form-select" name="jenis_benang" required>
                                        <option value="NYLON" <?= $list['jenis_benang'] == 'NYLON' ? 'selected' : '' ?>>NYLON</option>
                                        <option value="POLYESTER" <?= $list['jenis_benang'] == 'POLYESTER' ? 'selected' : '' ?>>POLYESTER</option>
                                        <option value="SPANDEX" <?= $list['jenis_benang'] == 'SPANDEX' ? 'selected' : '' ?>>SPANDEX</option>
                                        <option value="KARET" <?= $list['jenis_benang'] == 'KARET' ? 'selected' : '' ?>>KARET</option>
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
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="keterangan" id="" class="form-control" placeholder="Masukkan Keterangan"><?= $list['keterangan'] ?></textarea>
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

    <!-- Modal Export warehouseBB -->
    <div class="modal fade" id="modalExport" tabindex="-1" aria-labelledby="modalExportLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="modalExportLabel">Export Bahan Baku</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url($role . '/warehouseBB/BahanBakuCovExcel') ?>" method="GET">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Jenis Benang</label>
                                <select class="form-control" name="jenis_benang" required>
                                    <option value="">Pilih Jenis Benang</option>
                                    <option value="NYLON">NYLON</option>
                                    <option value="POLYESTER">POLYESTER</option>
                                    <option value="SPANDEX">SPANDEX</option>
                                    <option value="KARET">KARET</option>
                                    <option value="RECYCLED POLYESTER">RECYCLED POLYESTER</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-info">Export</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#stockTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            info: true
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.warehouse-card');

        searchInput.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            cards.forEach(card => {
                const jenis = (card.getAttribute('data-jenis') || '').toLowerCase();
                // tampilkan card jika jenis mengandung term, selain itu sembunyikan
                card.style.display = jenis.includes(term) ? '' : 'none';
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini akan hilang permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url($role . '/warehouseBB/deleteBahanBakuCov/') ?>' + id;
                }
            });
        });
    });
</script>

<?php $this->endSection(); ?>