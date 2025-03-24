<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
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

    .btn {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
    }
</style>
<div class="container my-4">
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">Pilih Cluster</h2>
        </div>
        <div class="card-body">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <input type="text" id="searchCluster" class="form-control" placeholder="Cari Cluster...">
                </div>
                <div class="col-md-6 text-end">
                    <button id="viewSelectedStocks" class="btn bg-gradient-info" disabled>
                        <i class="fas fa-eye"></i> Lihat Stock Terpilih
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-info">
                        <tr>
                            <th style="width: 5%;">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th>Nama Cluster</th>
                            <th>KGS Stock Awal</th>
                            <th>CNS Stock Awal</th>
                            <th>KRg Stock Awal</th>
                            <th>Lot Awal</th>
                            <th>KGS In/Out</th>
                            <th>CNS In/Out</th>
                            <th>KRg In/Out</th>
                            <th>Lot Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cluster)): ?>
                            <?php foreach ($cluster as $c): ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input cluster-checkbox"
                                                data-cluster="<?= esc($c['nama_cluster']); ?>"
                                                data-kgs="<?= esc($c['kgs_stock_awal']); ?>"
                                                data-cns="<?= esc($c['cns_stock_awal']); ?>"
                                                data-krg="<?= esc($c['krg_stock_awal']); ?>"
                                                data-lot="<?= esc($c['lot_awal']); ?>"
                                                data-kgs-io="<?= esc($c['kgs_in_out']); ?>"
                                                data-cns-io="<?= esc($c['cns_in_out']); ?>"
                                                data-krg-io="<?= esc($c['krg_in_out']); ?>"
                                                data-lot-stock="<?= esc($c['lot_stock']); ?>">
                                        </div>
                                    </td>
                                    <td><?= esc($c['nama_cluster']); ?></td>
                                    <td><?= esc($c['kgs_stock_awal']); ?></td>
                                    <td><?= esc($c['cns_stock_awal']); ?></td>
                                    <td><?= esc($c['krg_stock_awal']); ?></td>
                                    <td><?= esc($c['lot_awal']); ?></td>
                                    <td><?= esc($c['kgs_in_out']); ?></td>
                                    <td><?= esc($c['cns_in_out']); ?></td>
                                    <td><?= esc($c['krg_in_out']); ?></td>
                                    <td><?= esc($c['lot_stock']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">Data cluster tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Mengurangi Stock Cluster -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="stockModalLabel">Kurangi Stock Cluster Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Cluster</th>
                                <th>Stock Actual (KGS)</th>
                                <th>Stock Actual (CNS)</th>
                                <th>Stock Actual (KRg)</th>
                                <th>Kurangi (KGS)</th>
                                <th>Kurangi (CNS)</th>
                                <th>Kurangi (KRg)</th>
                            </tr>
                        </thead>
                        <tbody id="modalStockBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="reduceSelectedStocks">Kurangi Stock</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fitur pencarian cluster
        const searchCluster = document.getElementById("searchCluster");
        searchCluster.addEventListener("keyup", function() {
            let value = this.value.toLowerCase();
            document.querySelectorAll("table tbody tr").forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
            });
        });

        // Checkbox "Select All"
        const selectAllCheckbox = document.getElementById("selectAll");
        selectAllCheckbox.addEventListener("change", function() {
            document.querySelectorAll(".cluster-checkbox").forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateViewButtonState();
        });

        // Perubahan checkbox individu
        document.querySelectorAll(".cluster-checkbox").forEach(function(checkbox) {
            checkbox.addEventListener("change", function() {
                updateViewButtonState();
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else if (document.querySelectorAll(".cluster-checkbox:checked").length === document.querySelectorAll(".cluster-checkbox").length) {
                    selectAllCheckbox.checked = true;
                }
            });
        });

        function updateViewButtonState() {
            document.getElementById("viewSelectedStocks").disabled = document.querySelectorAll(".cluster-checkbox:checked").length === 0;
        }

        // Tombol "Lihat Stock Terpilih"
        document.getElementById("viewSelectedStocks").addEventListener("click", function() {
            let selectedClusters = Array.from(document.querySelectorAll(".cluster-checkbox:checked")).map(function(checkbox) {
                return {
                    nama_cluster: checkbox.dataset.cluster,
                    kgs_stock_awal: checkbox.dataset.kgs,
                    cns_stock_awal: checkbox.dataset.cns,
                    krg_stock_awal: checkbox.dataset.krg,
                    lot_awal: checkbox.dataset.lot,
                    kgs_in_out: checkbox.dataset.kgsIo,
                    cns_in_out: checkbox.dataset.cnsIo,
                    krg_in_out: checkbox.dataset.krgIo,
                    lot_stock: checkbox.dataset.lotStock
                };
            });
            populateStockModal(selectedClusters);
            new bootstrap.Modal(document.getElementById("stockModal")).show();
        });

        function populateStockModal(clusters) {
            const modalBody = document.getElementById("modalStockBody");
            modalBody.innerHTML = "";
            clusters.forEach(function(cluster) {
                const row = document.createElement("tr");
                row.innerHTML = `
                <td>${cluster.nama_cluster}</td>
                <td>${cluster.kgs_stock_awal}</td>
                <td>${cluster.cns_stock_awal}</td>
                <td>${cluster.krg_stock_awal}</td>
                <td><input type="number" class="form-control" name="reduce_kgs[]" min="0" max="${cluster.kgs_stock_awal}" data-cluster="${cluster.nama_cluster}"></td>
                <td><input type="number" class="form-control" name="reduce_cns[]" min="0" max="${cluster.cns_stock_awal}" data-cluster="${cluster.nama_cluster}"></td>
                <td><input type="number" class="form-control" name="reduce_krg[]" min="0" max="${cluster.krg_stock_awal}" data-cluster="${cluster.nama_cluster}"></td>
            `;
                modalBody.appendChild(row);
            });
        }

        document.getElementById("reduceSelectedStocks").addEventListener("click", function() {
            const reducedStocks = [];
            document.querySelectorAll("#modalStockBody tr").forEach(function(row) {
                const clusterName = row.querySelector("input[name='reduce_kgs[]']").dataset.cluster;
                const reduceKGS = row.querySelector("input[name='reduce_kgs[]']").value;
                const reduceCNS = row.querySelector("input[name='reduce_cns[]']").value;
                const reduceKRg = row.querySelector("input[name='reduce_krg[]']").value;
                reducedStocks.push({
                    cluster: clusterName,
                    reduce_kgs: reduceKGS,
                    reduce_cns: reduceCNS,
                    reduce_krg: reduceKRg
                });
            });
            console.log(reducedStocks);
            // Implementasikan logika untuk mengirim data reducedStocks ke backend
        });
    });
</script>

<?php $this->endSection(); ?>