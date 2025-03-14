<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    /* Custom styles for better responsiveness */
    .warehouse-card {
        transition: all 0.3s ease;
    }

    .warehouse-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        font-size: 0.8rem;
        padding: 0.35rem 0.65rem;
    }

    .card-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .card-info-label {
        color: #6c757d;
    }

    .action-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .filter-container {
            margin-bottom: 1rem;
        }

        .action-container {
            margin-top: 1rem;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Flash Message Notifications -->
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

    <!-- Header Card & Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="tittle-card">Warehouse Covering Management</h5>
            <p class="text-muted">Material System</p>
            <div class="row g-3">
                <div class="col-md-4 col-sm-6 filter-container">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari barang...">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 filter-container">
                    <select id="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="available">Tersedia</option>
                        <option value="out">Tidak Tersedia</option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 filter-container">
                    <select id="filterLocation" class="form-select">
                        <option value="">Semua Lokasi</option>
                        <option value="Gudang 1">Gudang 1</option>
                        <option value="Gudang 2">Gudang 2</option>
                        <option value="Gudang 3">Gudang 3</option>
                        <option value="Gudang 4">Gudang 4</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 action-container">
                    <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <!-- Info Summary & View Toggle -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body p-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="mb-2 mb-md-0">
                                    <span class="badge bg-info me-2 status-badge">Total: <span id="totalCount">0</span></span>
                                    <span class="badge bg-success me-2 status-badge">Tersedia: <span id="availableCount">0</span></span>
                                    <span class="badge bg-secondary status-badge">Tidak Tersedia: <span id="outCount">0</span></span>
                                </div>
                                <div>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" id="viewGrid">
                                            <i class="fas fa-th"></i> Grid
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" id="viewTable">
                                            <i class="fas fa-list"></i> Table
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dummy Data untuk Warehouse -->
    <?php
    $warehouseData = [
        [
            'id' => 1,
            'name' => 'Covering Plastik',
            'stock' => 100,
            'unit' => 'Roll',
            'location' => 'Gudang 1',
            'status' => 'available',
            'last_update' => '2023-05-15',
            'category' => 'Packaging'
        ],
        [
            'id' => 2,
            'name' => 'Covering Bubble Wrap',
            'stock' => 50,
            'unit' => 'Roll',
            'location' => 'Gudang 2',
            'status' => 'out',
            'last_update' => '2023-05-10',
            'category' => 'Packaging'
        ],
        [
            'id' => 3,
            'name' => 'Covering Karton',
            'stock' => 200,
            'unit' => 'Lembar',
            'location' => 'Gudang 3',
            'status' => 'available',
            'last_update' => '2023-05-12',
            'category' => 'Packaging'
        ],
        [
            'id' => 4,
            'name' => 'Covering Aluminium',
            'stock' => 0,
            'unit' => 'Roll',
            'location' => 'Gudang 4',
            'status' => 'out',
            'last_update' => '2023-05-08',
            'category' => 'Packaging'
        ],
    ];
    ?>

    <!-- Grid View -->
    <div class="row g-3" id="warehouseGrid">
        <?php foreach ($warehouseData as $item) : ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 warehouse-card"
                data-status="<?= $item['status'] ?>"
                data-location="<?= $item['location'] ?>"
                data-name="<?= $item['name'] ?>"
                data-category="<?= $item['category'] ?>">
                <div class="card h-100 border">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 text-truncate"><?= $item['name'] ?></h6>
                        <span class="badge status-badge <?= $item['status'] == 'available' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $item['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia' ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="card-info-row">
                            <span class="card-info-label">Stok:</span>
                            <span class="fw-bold"><?= $item['stock'] ?> <?= $item['unit'] ?></span>
                        </div>
                        <div class="card-info-row">
                            <span class="card-info-label">Lokasi:</span>
                            <span><?= $item['location'] ?></span>
                        </div>
                        <div class="card-info-row">
                            <span class="card-info-label">Kategori:</span>
                            <span><?= $item['category'] ?></span>
                        </div>
                        <div class="card-info-row">
                            <span class="card-info-label">Update:</span>
                            <span><?= $item['last_update'] ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-info action-btn" onclick="editItem(<?= $item['id'] ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <div>
                                <button class="btn btn-sm btn-outline-success action-btn me-1" onclick="addStock(<?= $item['id'] ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger action-btn" onclick="removeStock(<?= $item['id'] ?>)" <?= $item['stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Table View (Hidden by default) -->
    <div class="card mb-4" id="warehouseTable" style="display: none;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Update Terakhir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouseData as $item) : ?>
                            <tr class="warehouse-row"
                                data-status="<?= $item['status'] ?>"
                                data-location="<?= $item['location'] ?>"
                                data-name="<?= $item['name'] ?>"
                                data-category="<?= $item['category'] ?>">
                                <td><?= $item['id'] ?></td>
                                <td><?= $item['name'] ?></td>
                                <td><?= $item['stock'] ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td><?= $item['location'] ?></td>
                                <td><?= $item['category'] ?></td>
                                <td><?= $item['last_update'] ?></td>
                                <td>
                                    <span class="badge <?= $item['status'] == 'available' ? 'bg-success' : 'bg-secondary' ?> status-badge">
                                        <?= $item['status'] == 'available' ? 'Tersedia' : 'Tidak Tersedia' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info action-btn" onclick="editItem(<?= $item['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success action-btn" onclick="addStock(<?= $item['id'] ?>)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger action-btn" onclick="removeStock(<?= $item['id'] ?>)" <?= $item['stock'] <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Barang Covering Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="itemName" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemStock" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="itemStock" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="itemUnit" class="form-label">Satuan</label>
                            <select class="form-select" id="itemUnit" required>
                                <option value="Roll">Roll</option>
                                <option value="Lembar">Lembar</option>
                                <option value="Meter">Meter</option>
                                <option value="Pcs">Pcs</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="itemLocation" class="form-label">Lokasi</label>
                            <select class="form-select" id="itemLocation" required>
                                <option value="Gudang 1">Gudang 1</option>
                                <option value="Gudang 2">Gudang 2</option>
                                <option value="Gudang 3">Gudang 3</option>
                                <option value="Gudang 4">Gudang 4</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="itemCategory" class="form-label">Kategori</label>
                            <select class="form-select" id="itemCategory" required>
                                <option value="Packaging">Packaging</option>
                                <option value="Raw Material">Raw Material</option>
                                <option value="Finished Goods">Finished Goods</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="saveItemBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Transaction Modal -->
    <div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalLabel">Transaksi Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <input type="hidden" id="stockItemId">
                        <input type="hidden" id="stockAction">
                        <div class="mb-3">
                            <label for="stockAmount" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="stockAmount" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="stockNote" class="form-label">Catatan</label>
                            <textarea class="form-control" id="stockNote" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-info" id="saveStockBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Section -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script>
        // Fungsi untuk menghitung jumlah item sesuai status dan filter
        function updateCounts() {
            const totalItems = document.querySelectorAll('.warehouse-card:not([style*="display: none"])').length;
            const availableItems = document.querySelectorAll('.warehouse-card[data-status="available"]:not([style*="display: none"])').length;
            const outItems = document.querySelectorAll('.warehouse-card[data-status="out"]:not([style*="display: none"])').length;
            document.getElementById('totalCount').textContent = totalItems;
            document.getElementById('availableCount').textContent = availableItems;
            document.getElementById('outCount').textContent = outItems;
        }

        // Inisialisasi tampilan dan event listener
        document.addEventListener('DOMContentLoaded', function() {
            updateCounts();
            // Toggle antara Grid & Table view
            document.getElementById('viewGrid').addEventListener('click', function() {
                document.getElementById('warehouseGrid').style.display = '';
                document.getElementById('warehouseTable').style.display = 'none';
                this.classList.replace('btn-outline-info', 'btn-info');
                document.getElementById('viewTable').classList.replace('btn-info', 'btn-outline-info');
            });
            document.getElementById('viewTable').addEventListener('click', function() {
                document.getElementById('warehouseGrid').style.display = 'none';
                document.getElementById('warehouseTable').style.display = '';
                this.classList.replace('btn-outline-info', 'btn-info');
                document.getElementById('viewGrid').classList.replace('btn-info', 'btn-outline-info');
            });
            // Set tampilan default
            document.getElementById('viewGrid').click();
        });

        // Fungsi pencarian berdasarkan nama barang
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            // Filter untuk kartu
            document.querySelectorAll('.warehouse-card').forEach(card => {
                const itemName = card.getAttribute('data-name').toLowerCase();
                card.style.display = itemName.includes(query) ? '' : 'none';
            });
            // Filter untuk baris tabel
            document.querySelectorAll('.warehouse-row').forEach(row => {
                const itemName = row.getAttribute('data-name').toLowerCase();
                row.style.display = itemName.includes(query) ? '' : 'none';
            });
            updateCounts();
        });

        // Filter berdasarkan status
        document.getElementById('filterStatus').addEventListener('change', function() {
            const status = this.value;
            document.querySelectorAll('.warehouse-card').forEach(card => {
                const itemStatus = card.getAttribute('data-status');
                card.style.display = (status === '' || itemStatus === status) ? '' : 'none';
            });
            document.querySelectorAll('.warehouse-row').forEach(row => {
                const itemStatus = row.getAttribute('data-status');
                row.style.display = (status === '' || itemStatus === status) ? '' : 'none';
            });
            updateCounts();
        });

        // Filter berdasarkan lokasi
        document.getElementById('filterLocation').addEventListener('change', function() {
            const location = this.value;
            document.querySelectorAll('.warehouse-card').forEach(card => {
                const itemLocation = card.getAttribute('data-location');
                card.style.display = (location === '' || itemLocation === location) ? '' : 'none';
            });
            document.querySelectorAll('.warehouse-row').forEach(row => {
                const itemLocation = row.getAttribute('data-location');
                row.style.display = (location === '' || itemLocation === location) ? '' : 'none';
            });
            updateCounts();
        });

        // Tambah Barang Baru
        document.getElementById('saveItemBtn').addEventListener('click', function() {
            const form = document.getElementById('addItemForm');
            if (form.checkValidity()) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Barang covering baru telah ditambahkan.',
                }).then(() => {
                    $('#addItemModal').modal('hide');
                    form.reset();
                });
            } else {
                form.reportValidity();
            }
        });

        // Fungsi Edit Barang
        function editItem(id) {
            Swal.fire({
                title: 'Edit Barang',
                text: `Anda akan mengedit barang dengan ID: ${id}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ya, Edit',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Di sini biasanya modal edit akan ditampilkan dengan data item
                    Swal.fire('Info', 'Fitur edit akan segera tersedia', 'info');
                }
            });
        }

        // Fungsi Tambah Stok
        function addStock(id) {
            document.getElementById('stockItemId').value = id;
            document.getElementById('stockAction').value = 'add';
            document.getElementById('stockModalLabel').textContent = 'Tambah Stok';
            document.getElementById('stockForm').reset();
            new bootstrap.Modal(document.getElementById('stockModal')).show();
        }

        // Fungsi Kurangi Stok
        function removeStock(id) {
            document.getElementById('stockItemId').value = id;
            document.getElementById('stockAction').value = 'remove';
            document.getElementById('stockModalLabel').textContent = 'Kurangi Stok';
            document.getElementById('stockForm').reset();
            new bootstrap.Modal(document.getElementById('stockModal')).show();
        }

        // Simpan Transaksi Stok
        document.getElementById('saveStockBtn').addEventListener('click', function() {
            const form = document.getElementById('stockForm');
            if (form.checkValidity()) {
                const itemId = document.getElementById('stockItemId').value;
                const action = document.getElementById('stockAction').value;
                const amount = document.getElementById('stockAmount').value;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: `Stok telah ${action === 'add' ? 'ditambahkan' : 'dikurangi'} sebanyak ${amount} unit.`,
                }).then(() => {
                    $('#stockModal').modal('hide');
                    form.reset();
                });
            } else {
                form.reportValidity();
            }
        });
    </script>
</div>

<?php $this->endSection(); ?>