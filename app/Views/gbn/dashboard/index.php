<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<style>
    .cell {
        border: none;
        padding: 8px 12px;
        margin: 2px;
        border-radius: 8px;
        /* Membuat tombol rounded */
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    /* Warna cell */
    .gray-cell {
        background-color: #b0b0b0;
        color: white;
    }

    .blue-cell {
        background-color: #007bff;
        color: white;
    }

    .orange-cell {
        background-color: #ff851b;
        color: white;
    }

    .red-cell {
        background-color: #dc3545;
        color: white;
    }

    /* Hover effect */
    .cell:hover {
        opacity: 0.8;
    }

    /* Styling table */
    .table-bordered th,
    .table-bordered td {
        border: 2px solid #dee2e6;
        text-align: center;
    }
</style>

<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    Dashboard
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">PDK</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pdk['total_pdk'] ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-book text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Sch Completed</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $schedule['total_done'] ?>
                                    <!-- Sesuaikan dengan last status sent dll. -->
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-tasks text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pemasukan/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pemasukan['total_karung_masuk'] ?? 0 ?> Karung
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-settings text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pengeluaran/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pengeluaran['total_karung_keluar'] ?? 0 ?> Karung
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="mt-3 ms-3">
                    <h4><strong>LAYOUT STOCK ORDER</strong></h4>
                </div>
                <div class="card-body">
                    <!-- Keterangan kapasitas -->
                    <div class="mb-2 text-center">
                        <button class="btn text-white w-10" style="background-color: #b0b0b0;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Kosong">0%</button>
                        <button class="btn text-white w-10" style="background-color: #007bff;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Rendah">1-70%</button>
                        <button class="btn text-white w-10" style="background-color: #ff851b;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Sedang">71-99%</button>
                        <button class="btn text-white w-10" style="background-color: #dc3545;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Penuh">100%</button>
                    </div>
                    <form id="groupForm">
                        <div class="mb-3">
                            <label for="groupSelect" class="form-label">Select Group</label>
                            <select class="form-select" id="groupSelect" name="group">
                                <option value="I" selected>Group I</option>
                                <option value="II">Group II</option>
                                <option value="III">Group III</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100">Apply</button>
                    </form>
                    <!-- Tempat untuk Menampilkan Tabel -->
                    <div id="groupTable"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Cluster</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nama Cluster:</strong> <span id="modalNamaCluster"></span></p>
                <p><strong>Kapasitas:</strong> <span id="modalKapasitas"></span> kg</p>
                <p><strong>Total Terisi:</strong> <span id="modalTotalQty"></span> kg</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No Model</th>
                                <th>Kode Warna</th>
                                <th>Foll Up</th>
                                <th>Delivery</th>
                                <th>Kapasitas Terpakai</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetailTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Initialize chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                datasets: [{
                    label: 'Distribution',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 9, 10, 11, 6],
                    backgroundColor: [
                        'rgba(51, 122, 183, 0.7)',
                    ],
                    borderColor: [
                        'rgba(51, 122, 183, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<script>
    // Script untuk menangani klik button dan menampilkan modal
    document.addEventListener("DOMContentLoaded", function() {
        let modalDetail = document.getElementById("modalDetail");
        modalDetail.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget;
            let kapasitas = button.getAttribute("data-kapasitas");
            let total_qty = button.getAttribute("data-total_qty");
            let nama_cluster = button.getAttribute("data-nama_cluster");
            let detailData = JSON.parse(button.getAttribute("data-detail"));

            document.getElementById("modalKapasitas").textContent = kapasitas;
            document.getElementById("modalTotalQty").textContent = total_qty;
            document.getElementById("modalNamaCluster").textContent = nama_cluster;

            let tableBody = document.getElementById("modalDetailTableBody");
            tableBody.innerHTML = "";

            detailData.forEach((item) => {
                let row = `<tr>
                <td>${item.no_model}</td>
                <td>${item.kode_warna}</td>
                <td>${item.foll_up || '-'}</td>
                <td>${item.delivery || '-'}</td>
                <td>${item.qty || '-'} kg</td>
            </tr>`;
                tableBody.innerHTML += row;
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Fungsi untuk memuat data berdasarkan grup yang dipilih
        function loadGroupData(group) {
            $.ajax({
                url: "<?= base_url($role . '/getGroupData') ?>",
                type: "POST",
                data: {
                    group: group
                },
                success: function(response) {
                    $("#groupTable").html(response); // Masukkan data ke dalam div
                },
                error: function() {
                    $("#groupTable").html("<p class='text-center text-danger'>Gagal memuat data. Silakan coba lagi.</p>");
                }
            });
        }

        // Event listener ketika form dikirim
        $("#groupForm").submit(function(e) {
            e.preventDefault(); // Mencegah reload halaman
            var selectedGroup = $("#groupSelect").val(); // Ambil nilai yang dipilih
            loadGroupData(selectedGroup); // Panggil fungsi AJAX
        });

        // Load data default untuk Group I saat halaman pertama kali dibuka
        loadGroupData("I");
    });
</script>
<?php $this->endSection(); ?>