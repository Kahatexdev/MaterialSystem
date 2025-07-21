<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.21.1/dist/bootstrap-table.min.css" />
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

    <?php
    // Calculate summary and filter data
    $total_items = count($stok);
    $total_kg = array_sum(array_column($stok, 'ttl_kg'));
    $items_available = count(array_filter($stok, fn($item) => $item['status'] == 'ada'));
    $items_out_of_stock = $total_items - $items_available;

    ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jenis Barang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_items ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-boxes fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card total-stock h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stok (Kg)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_kg, 2) ?> Kg</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-weight-hanging fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card items-available h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Barang Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $items_available ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card items-out-of-stock h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Barang Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $items_out_of_stock ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-times-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Header Card & Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mt-2 align-items-end">
                <div class="col-md-5">
                    <h5 class="card-title mb-3">Warehouse Barang Jadi Covering</h5>

                </div>
                <div class="col-md-7 d-flex justify-content-center align-items-center gap-2">
                    <button class="btn bg-gradient-info" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-1"></i> Import Jenis
                    </button>
                    <button class="btn bg-gradient-info" type="button" data-bs-toggle="modal" data-bs-target="#importStok">
                        <i class="fas fa-file-import me-1"></i> Import Stok
                    </button>
                    <button class="btn bg-gradient-primary" type="button" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus me-1"></i> Jenis
                    </button>
                    <button class="btn bg-gradient-success" type="button" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="filterJenis" class="form-label">Filter Jenis Barang</label>
                    <select id="filterJenis" name="jenis" class="form-select">
                        <option value="">-- Semua Jenis --</option>
                        <?php foreach ($jenisOptions as $j): ?>
                            <option value="<?= esc($j) ?>" <?= $fJenis === $j ? 'selected' : '' ?>>
                                <?= esc($j) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterBenang" class="form-label">Filter Jenis Benang</label>
                    <select id="filterBenang" name="jenis_benang" class="form-select">
                        <option value="">-- Semua Benang --</option>
                        <?php foreach ($benangOptions as $b): ?>
                            <option value="<?= esc($b) ?>" <?= $fBenang === $b ? 'selected' : '' ?>>
                                <?= esc($b) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterMesin" class="form-label">Filter Jenis Mesin</label>
                    <select id="filterMesin" name="jenis_mesin" class="form-select">
                        <option value="">-- Semua Mesin --</option>
                        <?php foreach ($mesinOptions as $m): ?>
                            <option value="<?= esc($m) ?>" <?= $fMesin === $m ? 'selected' : '' ?>>
                                <?= esc($m) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex justify-content-center align-items-end">
                    <button type="submit" class="btn bg-gradient-info mt-2 me-2">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="<?= current_url() ?>" class="btn bg-gradient-secondary mt-2">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="stokTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis Mesin</th>
                        <th>Jenis Barang</th>
                        <th>Color</th>
                        <th>Code</th>
                        <th>LMD</th>
                        <th>Cones</th>
                        <th>Kg</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stok as $i => $item): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($item['jenis_mesin']) ?></td>
                            <td><?= esc($item['jenis']) ?></td>
                            <td><?= esc($item['color']) ?></td>
                            <td><?= esc($item['code']) ?></td>
                            <td><?= esc($item['lmd']) ?></td>
                            <td><?= esc($item['ttl_cns'] ?? '-') ?></td>
                            <td><?= esc(number_format($item['ttl_kg'], 2)) ?> Kg</td>
                            <td>
                                <button class="btn btn-warning" onclick="editItem(<?= $item['id_covering_stock'] ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $item['id_covering_stock'] ?>)">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <!-- Modals -->
    <!-- Add New Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Barang Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?= base_url('covering/warehouse/tambahStock') ?>">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Jenis Barang</label><input type="text" class="form-control" name="jenis" required></div>
                                <div class="mb-3"><label class="form-label">Jenis Mesin</label><input type="text" class="form-control" name="jenis_mesin"></div>
                                <div class="mb-3"><label class="form-label">DR (Draw Rasio)</label><input type="text" class="form-control" name="dr"></div>
                                <div class="mb-3"><label class="form-label">Jenis Cover</label><select class="form-select" name="jenis_cover" required>
                                        <option value="">Pilih...</option>
                                        <option value="SINGLE">SINGLE</option>
                                        <option value="DOUBLE">DOUBLE</option>
                                    </select></div>
                                <div class="mb-3"><label class="form-label">Jenis Benang</label>
                                    <select class="form-select" name="jenis_benang" required>
                                        <option value="">Pilih...</option>
                                        <option value="MYSTY">MYSTY</option>
                                        <option value="POLYESTER">POLYESTER</option>
                                        <option value="NYLON">NYLON</option>
                                        <option value="RECYCLED">RECYCLED</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Warna</label><input type="text" class="form-control" name="color" required></div>
                                <div class="mb-3"><label class="form-label">Kode</label><input type="text" class="form-control" name="code" required></div>
                                <div class="mb-3"><label class="form-label">Stok Awal (Kg)</label><input type="number" class="form-control" name="ttl_kg" step="0.01" value="0" required></div>
                                <div class="mb-3"><label class="form-label">Stok Awal (Cones)</label><input type="number" class="form-control" name="ttl_cns" value="0" required></div>
                                <div class="mb-3"><label class="form-label">LMD</label><br>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" name="lmd[]" value="L"><label>L</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" name="lmd[]" value="M"><label>M</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" name="lmd[]" value="D"><label>D</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn bg-gradient-info">Simpan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Detail Barang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm">
                        <input type="hidden" id="editStockItemId" name="id_covering_stock">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Jenis Barang</label><input type="text" class="form-control" id="editJenis" name="jenis" required></div>
                                <div class="mb-3"><label class="form-label">Jenis Mesin</label><input type="text" class="form-control" id="editJenisMesin" name="jenis_mesin"></div>
                                <div class="mb-3"><label class="form-label">DR (Daurasio)</label><input type="text" class="form-control" id="editDr" name="dr"></div>
                                <div class="mb-3"><label class="form-label">Jenis Cover</label><select class="form-select" id="editJenisCover" name="jenis_cover" required>
                                        <option value="">Pilih...</option>
                                        <option value="SINGLE">SINGLE</option>
                                        <option value="DOUBLE">DOUBLE</option>
                                    </select></div>
                                <div class="mb-3"><label class="form-label">Jenis Benang</label><input type="text" class="form-control" id="editJenisBenang" name="jenis_benang" required></div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3"><label class="form-label">Warna</label><input type="text" class="form-control" id="editColor" name="color" required></div>
                                <div class="mb-3"><label class="form-label">Kode</label><input type="text" class="form-control" id="editCode" name="code" required></div>
                                <div class="mb-3"><label class="form-label">Stok (Kg)</label><input type="number" class="form-control" id="editTtlKg" readonly></div>
                                <div class="mb-3"><label class="form-label">Cones</label><input type="number" class="form-control" id="editTtlCns" readonly></div>
                                <div class="mb-3"><label class="form-label">LMD</label><br>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd1" name="lmd[]" value="L"><label>L</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd2" name="lmd[]" value="M"><label>M</label></div>
                                    <div class="form-check form-check-inline"><input type="checkbox" class="form-check-input" id="editLmd3" name="lmd[]" value="D"><label>D</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn bg-gradient-info">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Data Stok</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('covering/warehouse/exportStock') ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Jenis Cover</label><select class="form-select" name="jenis_cover">
                                <option value="">Semua</option>
                                <option value="SINGLE">SINGLE</option>
                                <option value="DOUBLE">DOUBLE</option>
                            </select></div>
                        <div class="mb-3"><label class="form-label">Jenis Benang</label><select class="form-select" name="jenis_benang">
                                <option value="">Semua</option>
                                <option value="NYLON">NYLON</option>
                                <option value="POLYESTER">POLYESTER</option>
                                <option value="MYSTY">MYSTY</option>
                                <option value="RECYCLED">RECYCLED</option>
                            </select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success">Export Excel</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal import -->
    <!-- modal import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- centered vertically -->
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title text-white" id="importModalLabel">
                        <i class="fas fa-file-import me-2"></i>Import Jenis Barang
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="importForm"
                    action="<?= base_url(session()->get('role') . '/warehouse/importStokBarangJadi') ?>"
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
                            <a href="<?= base_url('template/CONTOH_FORMAT_IMPORT_STOK BARANG_JADI_COVERING.xlsx') ?>"
                                class="text-white text-decoration-none fw-semibold badge bg-gradient-info"
                                style="font-size: 1rem;"
                                download>
                                <i class="fas fa-download me-1"></i>Template Import Stok Barang Jadi
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

    <!-- modal import stok -->
    <div class="modal fade" id="importStok" tabindex="-1" aria-labelledby="importStokLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title text-white" id="importStokLabel">
                        <i class="fas fa-file-import me-2"></i>Import Data Stok
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="importForm"
                    action="<?= base_url(session()->get('role') . '/warehouse/importStokCovering') ?>"
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
                            <a href="<?= base_url('template/FORMAT_IMPORT_STOK_COVERING.xlsx') ?>"
                                class="text-white text-decoration-none fw-semibold badge bg-gradient-info"
                                style="font-size: 1rem;"
                                download>
                                <i class="fas fa-download me-1"></i>Template Import Stok Barang Jadi
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

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.21.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#stokTable').DataTable({
            // contoh opsi:
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            order: [
                [1, 'asc']
            ], // default sort kolom Jenis Barang
            columnDefs: [{
                targets: [0, 7], // kolom No & Aksi tidak bisa di-sort
                orderable: false
            }],
        });
    });
</script>
<script>
    const BASE_URL = "<?= base_url() ?>";

    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById("editStockForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const form = e.target;
            const payload = {
                id_covering_stock: form.id_covering_stock.value,
                jenis: form.jenis.value,
                jenis_mesin: form.jenis_mesin.value,
                dr: form.dr.value,
                jenis_cover: form.jenis_cover.value,
                jenis_benang: form.jenis_benang.value,
                color: form.color.value,
                code: form.code.value,
                lmd: Array.from(form.querySelectorAll("input[name='lmd[]']:checked")).map(cb => cb.value)
            };

            fetch(`${BASE_URL}covering/warehouse/updateEditStock`, {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload),
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire('Berhasil', 'Data stok berhasil diupdate!', 'success').then(() => location.reload());
                    } else {
                        Swal.fire("Gagal!", result.message || "Tidak dapat memperbarui.", "error");
                    }
                })
                .catch(() => Swal.fire("Error!", "Gagal mengupdate data.", "error"));
        });
    });

    function editItem(id) {
        fetch(`${BASE_URL}covering/warehouse/getStock/${id}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return Swal.fire("Gagal!", "Data tidak ditemukan", "error");

                const stock = data.stock;
                const form = document.getElementById('editStockForm');
                form.id_covering_stock.value = stock.id_covering_stock;
                form.jenis.value = stock.jenis;
                form.jenis_mesin.value = stock.jenis_mesin || '';
                form.dr.value = stock.dr || '';
                form.jenis_cover.value = stock.jenis_cover || '';
                form.jenis_benang.value = stock.jenis_benang || '';
                form.color.value = stock.color;
                form.code.value = stock.code;
                form.querySelector('#editTtlKg').value = stock.ttl_kg;
                form.querySelector('#editTtlCns').value = stock.ttl_cns;

                form.querySelectorAll("input[name='lmd[]']").forEach(cb => cb.checked = false);
                if (stock.lmd) {
                    stock.lmd.split(',').forEach(val => {
                        const cb = form.querySelector(`input[value='${val.trim()}']`);
                        if (cb) cb.checked = true;
                    });
                }

                const modal = new bootstrap.Modal(document.getElementById('editStockModal'));
                modal.show();
            })
            .catch(() => Swal.fire("Error!", "Gagal mengambil data.", "error"));
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Apakah Anda yakin ingin menghapus item ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const tokenName = '<?= csrf_token() ?>'; // misal: csrf_token()
            const tokenValue = '<?= csrf_hash() ?>'; // misal: csrf_hash()

            fetch(`${BASE_URL}covering/warehouse/deleteStokBarangJadi/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        [tokenName]: tokenValue
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (res.ok && data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message || 'Gagal menghapus item.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'Gagal menghapus item.', 'error');
                });
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('importForm');
        const btn = document.getElementById('importSubmit');
        const spinner = btn.querySelector('.spinner-border');
        const text = btn.querySelector('.btn-text');

        form.addEventListener('submit', function() {
            // disable tombol agar user tidak klik dua kali
            btn.disabled = true;
            // tampilkan spinner
            spinner.classList.remove('d-none');
            // ubah teks tombol
            text.textContent = ' Importing…';
            // biarkan form submit berjalan normal
        });
    });
</script>





<?php $this->endSection(); ?>