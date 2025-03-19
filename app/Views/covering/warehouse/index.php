<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
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
    <?php foreach (['success' => 'success', 'error' => 'error'] as $type => $icon) : ?>
        <?php if (session()->getFlashdata($type)) : ?>
            <script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: '<?= $icon ?>',
                        title: '<?= ucfirst($type) ?>!',
                        html: '<?= session()->getFlashdata($type) ?>',
                    });
                });
            </script>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Header Card & Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="tittle-card">Warehouse Covering Management</h5>
            <p class="text-muted">Material System</p>
            <div class="row g-3">
                <?php $filters = [
                    ['id' => 'searchInput', 'icon' => 'search', 'type' => 'text', 'placeholder' => 'Cari jenis...'],
                    ['id' => 'filterStatus', 'options' => ['' => 'Semua Status', 'ada' => 'Tersedia', 'tidak ada' => 'Tidak Tersedia']],
                    ['id' => 'filterLocation', 'options' => ['' => 'Semua Rak', '1' => 'Rak 1', '2' => 'Rak 2', '3' => 'Rak 3', '4' => 'Rak 4', '5' => 'Rak 5', '6' => 'Rak 6', '7' => 'Rak 7', '8' => 'Rak 8']],
                ]; ?>

                <?php foreach ($filters as $filter) : ?>
                    <div class="col-md-3 col-sm-6 filter-container">
                        <?php if (isset($filter['icon'])) : ?>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-<?= $filter['icon'] ?>"></i></span>
                                <input type="<?= $filter['type'] ?>" id="<?= $filter['id'] ?>" class="form-control" placeholder="<?= $filter['placeholder'] ?>">
                            </div>
                        <?php else : ?>
                            <select id="<?= $filter['id'] ?>" class="form-select">
                                <?php foreach ($filter['options'] as $value => $label) : ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="col-md-3 col-sm-6 action-container">
                    <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div class="row g-3" id="warehouseGrid">
        <?php foreach ($stok as $item) : ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 warehouse-card" data-status="<?= $item['status'] ?>" data-location="<?= $item['no_rak'] ?>" data-name="<?= $item['jenis'] ?>" data-category="<?= $item['color'] ?>">
                <div class="card h-100 border">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <h6 class="mb-0 text-truncate"> <?= $item['jenis'] ?> </h6>
                        <span class="badge status-badge <?= $item['status'] == 'ada' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= ucfirst($item['status']) ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php $infoFields = [
                            'Color' => $item['color'],
                            'Code' => $item['code'],
                            'LMD' => $item['lmd'],
                            'Stok' => $item['ttl_kg'] . ' Kg',
                            'Cones' => $item['ttl_cns'] . ' Cns',
                            'No Rak' => $item['no_rak'],
                            'Posisi Rak' => $item['posisi_rak'],
                            'Update' => $item['updated_at'] ?? 'N/A'
                        ]; ?>

                        <?php foreach ($infoFields as $label => $value) : ?>
                            <div class="card-info-row">
                                <span class="card-info-label"> <?= $label ?>: </span>
                                <span class="fw-bold"> <?= $value ?> </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="card-footer bg-white align-items-center">
                        <button class="btn  btn-info action-btn w-100" onclick="editItem(<?= $item['id_covering_stock'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <div class="row">
                            <div class="col-6">
                                <button class="btn  btn-success action-btn w-100" onclick="addStock(<?= $item['id_covering_stock'] ?>)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-6">

                                <button class="btn  btn-danger action-btn w-100" onclick="removeStock(<?= $item['id_covering_stock'] ?>)" <?= $item['ttl_kg'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
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
                        <input type="hidden" id="stockItemId" name="stockItemId">
                        <input type="hidden" id="stockAction">
                        <div class="mb-3">
                            <label for="no_model" class="form-label">No Model</label>
                            <input type="text" class="form-control" id="no_model" name="no_model" required>
                        </div>
                        <div class="mb-3">
                            <label for="stockAmount" class="form-label">Jumlah KG</label>
                            <input type="number" class="form-control" id="stockAmount" required>
                        </div>
                        <div class="mb-3">
                            <label for="amountcones" class="form-label">Jumlah Cones</label>
                            <input type="number" class="form-control" id="amountcones" name="amountcones" required>
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

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Lebih lebar agar lebih nyaman -->
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="" method="POST" enctype="multipart/form-data" action="<?= base_url('covering/warehouse/tambahStock') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis" class="form-label">Jenis Barang</label>
                                    <input type="text" class="form-control" id="jenis" name="jenis" required>
                                </div>
                                <div class="mb-3">
                                    <label for="color" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="color" name="color" required>
                                </div>
                                <div class="mb-3">
                                    <label for="code" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                                <div class="mb-3">
                                    <label for="box" class="form-label">Box</label>
                                    <input type="text" class="form-control" id="box" name="box">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">LMD</label><br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="lmd1" name="lmd[]" value="L">
                                        <label class="form-check-label" for="lmd1">L</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="lmd2" name="lmd[]" value="M">
                                        <label class="form-check-label" for="lmd2">M</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="lmd3" name="lmd[]" value="D">
                                        <label class="form-check-label" for="lmd3">D</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ttl_kg" class="form-label">Jumlah Stok (Kg)</label>
                                    <input type="number" class="form-control" id="ttl_kg" name="ttl_kg" required>
                                </div>
                                <div class="mb-3">
                                    <label for="ttl_cns" class="form-label">Jumlah Cones</label>
                                    <input type="number" class="form-control" id="ttl_cns" name="ttl_cns" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_palet" class="form-label">No Palet</label>
                                    <input type="text" class="form-control" id="no_palet" name="no_palet">
                                </div>
                                <div class="mb-3">
                                    <label for="no_rak" class="form-label">Nomor Rak</label>
                                    <select name="no_rak" id="no_rak" class="form-select" required>
                                        <option value="" disabled selected>Pilih Nomor Rak</option>
                                        <option value="1">Rak 1</option>
                                        <option value="2">Rak 2</option>
                                        <option value="3">Rak 3</option>
                                        <option value="4">Rak 4</option>
                                        <option value="5">Rak 5</option>
                                        <option value="6">Rak 6</option>
                                        <option value="7">Rak 7</option>
                                        <option value="8">Rak 8</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Posisi Rak</label><br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="posisiRak1" name="posisi_rak[]" value="Kiri">
                                        <label class="form-check-label" for="posisiRak1">Kiri</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="posisiRak2" name="posisi_rak[]" value="Kanan">
                                        <label class="form-check-label" for="posisiRak2">Kanan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="posisiRak3" name="posisi_rak[]" value="Atas">
                                        <label class="form-check-label" for="posisiRak3">Atas</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="posisiRak4" name="posisi_rak[]" value="Bawah">
                                        <label class="form-check-label" for="posisiRak4">Bawah</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Edit Data -->
    <!-- Modal Edit Data -->
    <div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="editStockItemId" name="stockItemId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editJenis" class="form-label">Jenis Barang</label>
                                    <input type="text" class="form-control" id="editJenis" name="jenis" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editColor" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="editColor" name="color" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCode" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="editCode" name="code" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editBox" class="form-label">Box</label>
                                    <input type="text" class="form-control" id="editBox" name="box" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">LMD</label><br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editLmd1" name="lmd[]" value="L">
                                        <label class="form-check-label" for="editLmd1">L</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editLmd2" name="lmd[]" value="M">
                                        <label class="form-check-label" for="editLmd2">M</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editLmd3" name="lmd[]" value="D">
                                        <label class="form-check-label" for="editLmd3">D</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editTtlKg" class="form-label">Jumlah Stok (Kg)</label>
                                    <input type="number" class="form-control" id="editTtlKg" name="ttl_kg" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editTtlCns" class="form-label">Jumlah Cones</label>
                                    <input type="number" class="form-control" id="editTtlCns" name="ttl_cns" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editNoPalet" class="form-label">No Palet</label>
                                    <input type="text" class="form-control" id="editNoPalet" name="no_palet" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editNoRak" class="form-label">Nomor Rak</label>
                                    <select name="no_rak" id="editNoRak" class="form-select" required>
                                        <option value="" disabled selected>Pilih Nomor Rak</option>
                                        <option value="1">Rak 1</option>
                                        <option value="2">Rak 2</option>
                                        <option value="3">Rak 3</option>
                                        <option value="4">Rak 4</option>
                                        <option value="5">Rak 5</option>
                                        <option value="6">Rak 6</option>
                                        <option value="7">Rak 7</option>
                                        <option value="8">Rak 8</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Posisi Rak</label><br>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editPosisiRak1" name="posisi_rak[]" value="Kiri">
                                        <label class="form-check-label" for="editPosisiRak1">Kiri</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editPosisiRak2" name="posisi_rak[]" value="Kanan">
                                        <label class="form-check-label" for="editPosisiRak2">Kanan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editPosisiRak3" name="posisi_rak[]" value="Atas">
                                        <label class="form-check-label" for="editPosisiRak3">Atas</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" id="editPosisiRak4" name="posisi_rak[]" value="Bawah">
                                        <label class="form-check-label" for="editPosisiRak4">Bawah</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                        </div>
                    </form>
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
            const adaItems = document.querySelectorAll('.warehouse-card[data-status="ada"]:not([style*="display: none"])').length;
            const outItems = document.querySelectorAll('.warehouse-card[data-status="out"]:not([style*="display: none"])').length;
            document.getElementById('totalCount').textContent = totalItems;
            document.getElementById('adaCount').textContent = adaItems;
            document.getElementById('outCount').textContent = outItems;
        }

        // Inisialisasi tampilan dan event listener
        document.addEventListener('DOMContentLoaded', function() {
            updateCounts();
            // Toggle antara Grid & Table view
            document.getElementById('viewGrid').addEventListener('click', function() {
                document.getElementById('warehouseGrid').style.display = '';
                document.getElementById('warehouseTable').style.display = 'none';
                this.classList.replace('btn-info', 'btn-info');
                document.getElementById('viewTable').classList.replace('btn-info', 'btn-info');
            });
            document.getElementById('viewTable').addEventListener('click', function() {
                document.getElementById('warehouseGrid').style.display = 'none';
                document.getElementById('warehouseTable').style.display = '';
                this.classList.replace('btn-info', 'btn-info');
                document.getElementById('viewGrid').classList.replace('btn-info', 'btn-info');
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


        document.getElementById("editStockForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah reload halaman

            const stockId = document.getElementById("editStockItemId").value;
            const jenis = document.getElementById("editJenis").value;
            const color = document.getElementById("editColor").value;
            const code = document.getElementById("editCode").value;
            const box = document.getElementById("editBox").value;
            const ttlKg = document.getElementById("editTtlKg").value;
            const ttlCns = document.getElementById("editTtlCns").value;
            const noPalet = document.getElementById("editNoPalet").value;
            const noRak = document.getElementById("editNoRak").value;

            // Ambil data checkbox LMD
            let lmdValues = [];
            document.querySelectorAll("input[name='lmd[]']:checked").forEach((checkbox) => {
                lmdValues.push(checkbox.value);
            });

            // Ambil data checkbox Posisi Rak
            let posisiRakValues = [];
            document.querySelectorAll("input[name='posisi_rak[]']:checked").forEach((checkbox) => {
                posisiRakValues.push(checkbox.value);
            });

            const BASE_URL = "<?= base_url(); ?>";

            // Kirim data ke backend menggunakan fetch
            fetch(`${BASE_URL}covering/warehouse/updateEditStock`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id_covering_stock: stockId,
                        jenis: jenis,
                        color: color,
                        code: code,
                        box: box,
                        ttl_kg: ttlKg,
                        ttl_cns: ttlCns,
                        no_palet: noPalet,
                        no_rak: noRak,
                        lmd: lmdValues,
                        posisi_rak: posisiRakValues
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Stok berhasil diperbarui!",
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload(); // Reload halaman setelah update berhasil
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Gagal memperbarui stok: " + result.message
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Terjadi kesalahan saat mengupdate stok"
                    });
                });

            // Tutup modal setelah submit
            $("#editStockModal").modal("hide");
        });


        // Fungsi Edit Barang
        function editItem(id) {
            fetch(`<?= base_url(); ?>covering/warehouse/getStock/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const stock = data.stock;

                        // Isi modal dengan data stok yang diterima
                        document.getElementById("editStockItemId").value = stock.id_covering_stock;
                        document.getElementById("editJenis").value = stock.jenis;
                        document.getElementById("editColor").value = stock.color;
                        document.getElementById("editCode").value = stock.code;
                        document.getElementById("editBox").value = stock.box;
                        document.getElementById("editTtlKg").value = stock.ttl_kg;
                        document.getElementById("editTtlCns").value = stock.ttl_cns;
                        document.getElementById("editNoPalet").value = stock.no_palet;
                        document.getElementById("editNoRak").value = stock.no_rak;

                        // Cek dan tandai checkbox LMD
                        ["L", "M", "D"].forEach((val, index) => {
                            document.getElementById(`editLmd${index + 1}`).checked = stock.lmd?.includes(val) || false;
                        });


                        // Cek dan tandai checkbox Posisi Rak
                        ["Kiri", "Kanan", "Atas", "Bawah"].forEach((val, index) => {
                            document.getElementById(`editPosisiRak${index + 1}`).checked = stock.posisi_rak.includes(val);
                        });

                        // Tampilkan modal edit
                        $("#editStockModal").modal("show");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: "Data barang tidak ditemukan"
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Terjadi kesalahan saat mengambil data"
                    });
                });
        }




        // Fungsi Tambah Stok
        function addStock(stockId) {
            document.getElementById("stockItemId").value = stockId; // Set ID stok
            document.getElementById("stockAction").value = "add"; // Set aksi tambah
            document.getElementById("stockModalLabel").innerText = "Tambah Stok"; // Ubah judul modal
            document.getElementById("saveStockBtn").classList.remove("btn-danger"); // Hapus kelas merah
            document.getElementById("saveStockBtn").classList.add("btn-success"); // Tambah kelas hijau
            document.getElementById("saveStockBtn").innerText = "Tambah Stok"; // Ubah teks tombol
            $("#stockModal").modal("show"); // Tampilkan modal
        }

        function removeStock(stockId) {
            document.getElementById("stockItemId").value = stockId; // Set ID stok
            document.getElementById("stockAction").value = "remove"; // Set aksi hapus
            document.getElementById("stockModalLabel").innerText = "Kurangi Stok"; // Ubah judul modal
            document.getElementById("saveStockBtn").classList.remove("btn-success"); // Hapus kelas hijau
            document.getElementById("saveStockBtn").classList.add("btn-danger"); // Tambah kelas merah
            document.getElementById("saveStockBtn").innerText = "Kurangi Stok"; // Ubah teks tombol
            $("#stockModal").modal("show"); // Tampilkan modal
        }

        // Event listener tombol "Simpan"
        document.getElementById("saveStockBtn").addEventListener("click", function() {
            const stockId = document.getElementById("stockItemId").value;
            const action = document.getElementById("stockAction").value;
            const noModel = document.getElementById("no_model").value;
            const amount = document.getElementById("stockAmount").value;
            const cones = document.getElementById("amountcones").value;
            const note = document.getElementById("stockNote").value;
            const BASE_URL = "<?= base_url(); ?>";

            // Validasi input
            if (!stockId || !stockAmount || !amountcones || !action) {
                Swal.fire({
                    icon: "warning",
                    title: "Oops...",
                    text: "Semua data harus diisi!"
                });
                return;
            }

            Swal.fire({
                title: "Konfirmasi",
                text: `Anda yakin ingin ${action === "remove" ? "mengurangi" : "menambah"} stok ini?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Lanjutkan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${BASE_URL}covering/warehouse/updateStock`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                stockItemId: stockId,
                                action: action,
                                no_model: noModel,
                                stockAmount: amount,
                                amountcones: cones,
                                stockNote: note
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Stok berhasil diperbarui.",
                                    showConfirmButton: true
                                }).then(() => {
                                    $("#stockModal").modal("hide"); // Tutup modal
                                    setTimeout(() => {
                                        location.reload(); // Refresh halaman
                                    }, 500);
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    text: "Gagal memperbarui stok: " + result.message
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "Terjadi kesalahan pada server."
                            });
                            console.error("Error:", error);
                        });
                }
            });
            // }
        });



        // Simpan Transaksi Stok
        // document.getElementById('saveStockBtn').addEventListener('click', function() {
        //     const form = document.getElementById('stockForm');
        //     if (form.checkValidity()) {
        //         const itemId = document.getElementById('stockItemId').value;
        //         const action = document.getElementById('stockAction').value;
        //         const amount = document.getElementById('stockAmount').value;
        //         Swal.fire({
        //             icon: 'success',
        //             title: 'Berhasil!',
        //             text: `Stok telah ${action === 'add' ? 'ditambahkan' : 'dikurangi'} sebanyak ${amount} unit.`,
        //         }).then(() => {
        //             $('#stockModal').modal('hide');
        //             form.reset();
        //         });
        //     } else {
        //         form.reportValidity();
        //     }
        // });
    </script>

    <?php $this->endSection(); ?>