<?php $this->extend($role . '/warehouse/header'); ?>
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

    <?php
    // Calculate summary and filter data
    $total_items = count($stok);
    $total_kg = array_sum(array_column($stok, 'ttl_kg'));
    $items_available = count(array_filter($stok, fn($item) => $item['status'] == 'ada'));
    $items_out_of_stock = $total_items - $items_available;

    // Extract unique values for new filters, handling potential nulls
    $unique_jenis_mesin = array_unique(array_column(array_filter($stok, fn($item) => !empty($item['jenis_mesin'])), 'jenis_mesin'));
    $unique_dr = array_unique(array_column(array_filter($stok, fn($item) => !empty($item['dr'])), 'dr'));
    sort($unique_jenis_mesin);
    sort($unique_dr);
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
            <h5 class="card-title">Manajemen Stok Barang Jadi Covering</h5>
            <div class="row g-2 align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="input-group"><span class="input-group-text bg-white"><i class="fas fa-search"></i></span><input type="text" id="searchInput" class="form-control" placeholder="Cari jenis barang..."></div>
                </div>
                <div class="col-lg-2 col-md-6"><select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="ada">Tersedia</option>
                        <option value="habis">Tidak Tersedia</option>
                    </select></div>
                <div class="col-lg-2 col-md-6"><select id="filterMesin" class="form-select">
                        <option value="">Semua Mesin</option><?php foreach ($unique_jenis_mesin as $mesin) : ?><option value="<?= $mesin ?>"><?= $mesin ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-lg-2 col-md-6"><select id="filterDr" class="form-select">
                        <option value="">Semua DR</option><?php foreach ($unique_dr as $dr) : ?><option value="<?= $dr ?>"><?= $dr ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-lg-3 col-md-12 d-flex justify-content-lg-end justify-content-center gap-2 mt-2 mt-lg-0">
                    <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#addItemModal"><i class="fas fa-plus me-1"></i> Jenis</button>
                    <button class="btn bg-gradient-success" data-bs-toggle="modal" data-bs-target="#exportModal"><i class="fas fa-file-excel me-1"></i> Export</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="row g-3" id="warehouseGrid">
        <?php if (empty($stok)) : ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Belum ada data stok.</div>
            </div>
        <?php else : ?>
            <?php foreach ($stok as $item) : ?>
                <div class="col-4 warehouse-card"
                    data-status="<?= $item['status'] ?? '' ?>"
                    data-name="<?= strtolower($item['jenis'] ?? '') ?>"
                    data-mesin="<?= $item['jenis_mesin'] ?? '' ?>"
                    data-dr="<?= $item['dr'] ?? '' ?>">
                    <div class="card h-100 border">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 text-truncate"><?= $item['jenis'] ?></h6>
                            <span class="badge <?= $item['status'] == 'ada' ? 'bg-gradient-info' : 'bg-gradient-secondary' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </div>
                        <div class="card-body small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Color:</span> <span class="fw-bold"><?= $item['color'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Code:</span> <span class="fw-bold"><?= $item['code'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Jenis Mesin:</span> <span class="fw-bold"><?= $item['jenis_mesin'] ?? '-' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>DR:</span> <span class="fw-bold"><?= $item['dr'] ?? '-' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Stok:</span> <span class="fw-bold"><?= $item['ttl_kg'] ?> Kg</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Update:</span> <span class="fw-bold"><?= $item['updated_at'] ?? 'N/A' ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="row g-2 mb-2">
                                <div class="col-12 col-md-6 d-grid">
                                    <button
                                        class="btn bg-gradient-info btn-sm btn-custom"
                                        onclick="addStock(<?= $item['id_covering_stock'] ?>)">
                                        <i class="fas fa-plus me-1"></i> Pemasukan
                                    </button>
                                </div>
                                <div class="col-12 col-md-6 d-grid">
                                    <button
                                        class="btn bg-gradient-danger btn-sm btn-custom <?= $item['ttl_kg'] <= 0 ? 'disabled' : '' ?>"
                                        onclick="removeStock(<?= $item['id_covering_stock'] ?>)">
                                        <i class="fas fa-minus me-1"></i> Pengeluaran
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button
                                    class="btn btn-outline-secondary btn-sm btn-custom"
                                    onclick="editItem(<?= $item['id_covering_stock'] ?>)">
                                    <i class="fas fa-edit me-1"></i> Edit Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
                                <div class="mb-3"><label class="form-label">DR (Daurasio)</label><input type="text" class="form-control" name="dr"></div>
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

    <!-- Other Modals (Stock Transaction, Export) remain the same as before -->
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalLabel">Transaksi Stok</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm" autocomplete="off"><input type="hidden" id="stockItemId" name="stockItemId"><input type="hidden" id="stockAction" name="action">
                        <div class="mb-3 row">
                            <div class="col-6">
                                <label class="form-label">No Model</label><input type="text" class="form-control" id="no_model" name="no_model" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Keterangan</label><input type="text" class="form-control" id="stockNote" name="stockNote" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6"><label class="form-label">Jumlah KG</label><input type="number" class="form-control" id="stockAmount" name="stockAmount" step="0.01" required></div>
                            <div class="col-6"><label class="form-label">Jumlah Cones</label><input type="number" class="form-control" id="amountcones" name="amountcones" required></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn bg-gradient-info" id="saveStockBtn">Simpan</button></div>
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
                            </select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-success">Export Excel</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const BASE_URL = "<?= base_url() ?>";

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterStatus = document.getElementById('filterStatus');
        const filterMesin = document.getElementById('filterMesin');
        const filterDr = document.getElementById('filterDr');

        function filterGrid() {
            const query = searchInput.value.toLowerCase();
            const status = filterStatus.value;
            const mesin = filterMesin.value;
            const dr = filterDr.value;

            document.querySelectorAll('.warehouse-card').forEach(card => {
                const nameMatch = card.getAttribute('data-name').includes(query);
                const statusMatch = (status === '' || card.getAttribute('data-status') === status);
                const mesinMatch = (mesin === '' || card.getAttribute('data-mesin') === mesin);
                const drMatch = (dr === '' || card.getAttribute('data-dr') === dr);

                card.style.display = (nameMatch && statusMatch && mesinMatch && drMatch) ? '' : 'none';
            });
        }

        [searchInput, filterStatus, filterMesin, filterDr].forEach(el => el.addEventListener('input', filterGrid));
        [filterStatus, filterMesin, filterDr].forEach(el => el.addEventListener('change', filterGrid));

        document.getElementById("saveStockBtn").addEventListener("click", function() {
            const form = document.getElementById('stockForm');
            if (!form.checkValidity()) {
                return Swal.fire('Peringatan', 'Semua data transaksi harus diisi!', 'warning');
            }

            Swal.fire({
                    title: "Konfirmasi",
                    text: `Anda yakin ingin ${form.action.value === "remove" ? "mengurangi" : "menambah"} stok ini?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Batal"
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        fetch(`${BASE_URL}covering/warehouse/updateStock`, {
                                method: "POST",
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    stockItemId: form.stockItemId.value,
                                    action: form.action.value,
                                    no_model: form.no_model.value,
                                    stockNote: form.stockNote.value,
                                    stockAmount: form.stockAmount.value,
                                    amountcones: form.amountcones.value
                                })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire('Berhasil!', 'Stok berhasil diperbarui.', 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Gagal!', result.message || 'Gagal memperbarui stok.', 'error');
                                }
                            })
                            .catch(error => Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error'));
                    }
                });
        });

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

    function openStockModal(stockId, action) {
        const modal = new bootstrap.Modal(document.getElementById('stockModal'));
        const form = document.getElementById('stockForm');
        form.reset();
        form.stockItemId.value = stockId;
        form.action.value = action;
        document.getElementById('stockModalLabel').textContent = action === 'add' ? 'Tambah Stok (Pemasukan)' : 'Kurangi Stok (Pengeluaran)';
        modal.show();
    }

    function addStock(stockId) {
        openStockModal(stockId, 'add');
    }

    function removeStock(stockId) {
        openStockModal(stockId, 'remove');
    }

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
</script>

<?php $this->endSection(); ?>