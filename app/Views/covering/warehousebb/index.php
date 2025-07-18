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

    <div class="card card-frame">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 font-weight-bolder">Stock Bahan Baku</h5>
                </div>
                <div class="col-md-6">
                    <div class="row g-2 justify-content-md-end">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari jenis barang...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn bg-gradient-info w-100" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn bg-gradient-success w-100" data-bs-target="#modalExport" data-bs-toggle="modal">
                                <i class="fas fa-file-excel me-2"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="row g-3 mt-3" id="warehouseGrid">
        <?php if (empty($warehouseBB)) : ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Belum ada data stok.</div>
            </div>
        <?php else : ?>
            <?php foreach ($warehouseBB as $item) : ?>
                <div class="col-4 warehouse-card"
                    data-jenis="<?= $item['jenis_benang'] ?? '' ?>"
                    data-denier="<?= $item['denier'] ?? '' ?>"
                    data-warna="<?= $item['warna'] ?? '' ?>"
                    data-kode="<?= $item['kode'] ?? '' ?>"
                    data-kg="<?= $item['kg'] ?? '' ?>"
                    data-keterangan="<?= $item['keterangan'] ?? '' ?>">
                    <div class="card h-100 border">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 text-truncate"><?= $item['jenis_benang'] ?></h6>
                            <span class="badge <?= $item['status'] == 'ada' ? 'bg-gradient-info' : 'bg-gradient-secondary' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </div>
                        <div class="card-body small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Jenis:</span> <span class="fw-bold"><?= $item['jenis_benang'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Denier:</span> <span class="fw-bold"><?= $item['denier'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Warna:</span> <span class="fw-bold"><?= $item['warna'] ?? '-' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Kode Warna:</span> <span class="fw-bold"><?= $item['kode'] ?? '-' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Stock:</span> <span class="fw-bold"><?= $item['kg'] ?> Kg</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Keterangan:</span> <span class="fw-bold"><?= $item['keterangan'] ?? '' ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="row g-2 mb-2">
                                <div class="col-12 col-md-6 d-grid">
                                    <button type="button" class="btn bg-gradient-info btn-sm btn-custom" data-bs-toggle="modal" data-bs-target="#modalPemasukan<?= $item['idstockbb'] ?>">
                                        <i class="fas fa-plus me-1"></i> Pemasukan
                                    </button>
                                </div>
                                <div class="col-12 col-md-6 d-grid">
                                    <button type="button" class="btn bg-gradient-danger btn-sm btn-custom <?= $item['kg'] <= 0 ? 'disabled' : '' ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalPengeluaran<?= $item['idstockbb'] ?>" <?= $item['kg'] <= 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-minus me-1"></i> Pengeluaran
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button
                                    class="btn btn-outline-secondary btn-sm btn-custom"
                                    data-bs-target="#editModal<?= $item['idstockbb'] ?>" data-bs-toggle="modal">
                                    <i class="fas fa-edit me-1"></i> Edit Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="mt-3 d-flex justify-content-end">
        <?= $pager->links('warehouse', 'bootstrap_pagination') ?>
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

    <!-- Modal Pemasukan -->
    <?php foreach ($warehouseBB as $item) : ?>
        <div class="modal fade" id="modalPemasukan<?= $item['idstockbb'] ?>" tabindex="-1" aria-labelledby="modalPemasukanLabel<?= $item['idstockbb'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-info text-white">
                        <h5 class="modal-title" id="modalPemasukanLabel<?= $item['idstockbb'] ?>">Pemasukan Stok (Kg)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url($role . '/warehouseBB/pemasukan') ?>" method="POST">
                        <input type="hidden" class="form-control" name="denier" value="<?= $item['denier'] ?>" placeholder="Masukkan Denier">
                        <input type="hidden" name="jenis_benang" value="<?= $item['jenis_benang'] ?>">
                        <input type="hidden" class="form-control" name="warna" value="<?= $item['warna'] ?>" placeholder="Masukkan Warna">
                        <input type="hidden" class="form-control" name="kode" value="<?= $item['kode'] ?>" placeholder="Masukkan Kode Warna">
                        <input type="hidden" step="0.01" class="form-control" name="kg" value="<?= $item['kg'] ?>" placeholder="Masukkan Stock (Kg)">
                        <input type="hidden" name="keterangan" id="" class="form-control" placeholder="Masukkan Keterangan" value="<?= $item['keterangan'] ?>"></input>

                        <div class="modal-body">
                            <input type="hidden" name="idstockbb" value="<?= $item['idstockbb'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah (Kg)</label>
                                    <input type="number" step="0.01" min="0.01" name="kg" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Cones</label>
                                    <input type="number" name="ttl_cns" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-info">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Pengeluaran -->
        <div class="modal fade" id="modalPengeluaran<?= $item['idstockbb'] ?>" tabindex="-1" aria-labelledby="modalPengeluaranLabel<?= $item['idstockbb'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-gradient-danger text-white">
                        <h5 class="modal-title" id="modalPengeluaranLabel<?= $item['idstockbb'] ?>">Pengeluaran Stock (Kg)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url($role . '/warehouseBB/pengeluaran') ?>" method="POST">
                        <input type="hidden" class="form-control" name="denier" value="<?= $item['denier'] ?>" placeholder="Masukkan Denier">
                        <input type="hidden" name="jenis_benang" value="<?= $item['jenis_benang'] ?>">
                        <input type="hidden" class="form-control" name="warna" value="<?= $item['warna'] ?>" placeholder="Masukkan Warna">
                        <input type="hidden" class="form-control" name="kode" value="<?= $item['kode'] ?>" placeholder="Masukkan Kode Warna">
                        <input type="hidden" step="0.01" class="form-control" name="kg" value="<?= $item['kg'] ?>" placeholder="Masukkan Stock (Kg)">
                        <input type="hidden" name="keterangan" id="" class="form-control" placeholder="Masukkan Keterangan" value="<?= $item['keterangan'] ?>"></input>

                        <div class="modal-body">
                            <input type="hidden" name="idstockbb" value="<?= $item['idstockbb'] ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah (Kg)</label>
                                    <input type="number" step="0.01" min="0.01" name="kg" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Cones</label>
                                    <input type="number" name="ttl_cns" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                                        <option value="POLYESTER/SPDX/KRT">POLYESTER/SPDX/KRT</option>
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
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<?php $this->endSection(); ?>