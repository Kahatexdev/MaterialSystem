<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<style>
    /* ===== CELL STYLING ===== */
    .cell {
        border: none;
        padding: 8px 12px;
        margin: 2px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Cell Colors */
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

    /* Hover Effects */
    .cell:hover {
        opacity: 0.8;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* ===== TABLE STYLING ===== */
    .table-bordered th,
    .table-bordered td {
        border: 2px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
    }

    .table-bordered th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }

    /* ===== MODAL ENHANCEMENTS ===== */
    .modal-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body p {
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .modal-body p strong {
        color: #495057;
        min-width: 120px;
        display: inline-block;
    }

    .modal-body p span {
        color: #007bff;
        font-weight: 500;
    }

    /* ===== KARUNG LIST STYLING ===== */
    #modalKarungList {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        position: relative;
        overflow: hidden;
    }

    #modalKarungList ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    #modalKarungList li {
        margin-bottom: 0.5rem;
        color: #495057;
    }

    #modalKarungList em {
        color: #6c757d;
        font-style: italic;
    }

    /* ===== RESPONSIVE IMPROVEMENTS ===== */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }

        .modal-body {
            padding: 1rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }
    }

    /* ===== LOADING ANIMATION ===== */
    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Pemesanan/Hari</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $pemesanan['pemesanan_per_hari'] ?? 0 ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="ni ni-single-copy-04 text-lg opacity-10" aria-hidden="true"></i>
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
                                    <?= $schedule['total_done'] ?? 0 ?>
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
                                <i class="ni ni-bold-down text-lg opacity-10" aria-hidden="true"></i>
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
                                <i class="ni ni-bold-up text-lg opacity-10" aria-hidden="true"></i>
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
                                <option value="covering">Covering</option>
                                <option value="nylon">Nylon</option>
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
            <!-- Modal Header -->
            <div class="modal-header text-white">
                <h5 class="modal-title d-flex align-items-center" id="detailModalLabel">
                    <i class="fas fa-info-circle me-2"></i>
                    Detail Cluster
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Cluster Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Nama Cluster:</strong> <span id="modalNamaCluster"></span></p>
                        <p><strong>Kapasitas:</strong> <span id="modalKapasitas"></span> kg</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Terisi:</strong> <span id="modalTotalQty"></span> kg</p>
                        <p><strong>Sisa Kapasitas:</strong> <span id="modalSisaKapasitas"></span> kg</p>
                    </div>
                </div>

                <!-- Detail Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>No Model</th>
                                <th><i class="fas fa-palette me-1"></i>Kode Warna</th>
                                <th><i class="fas fa-calendar me-1"></i>Foll Up</th>
                                <th><i class="fas fa-truck me-1"></i>Delivery</th>
                                <th><i class="fas fa-weight me-1"></i>Kapasitas Terpakai</th>
                                <th><i class="fas fa-eye me-1"></i>Detail</th>
                            </tr>
                        </thead>
                        <tbody id="modalDetailTableBody">
                            <!-- Content will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Karung List -->
                <div id="modalKarungList" class="mt-3">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Tutup
                </button>
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
    // ===== MODAL DETAIL HANDLER =====
    document.addEventListener("DOMContentLoaded", function() {
        const modalDetail = document.getElementById("modalDetail");

        // Modal show event handler
        modalDetail.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget;
            const kapasitas = button.getAttribute("data-kapasitas");
            const total_qty = button.getAttribute("data-total_qty");
            const nama_cluster = button.getAttribute("data-nama_cluster");
            const detailData = JSON.parse(button.getAttribute("data-detail"));
            const detailKarung = JSON.parse(button.getAttribute("data-karung"));
            const totalTerisi = detailData.reduce((sum, item) => {
                return sum + (Number(item.qty) || 0);
            }, 0);
            const sisa = (Number(kapasitas) || 0) - totalTerisi;

            // Populate modal data
            document.getElementById("modalKapasitas").textContent = kapasitas;
            document.getElementById("modalTotalQty").textContent = totalTerisi;
            document.getElementById("modalNamaCluster").textContent = nama_cluster;
            document.getElementById("modalSisaKapasitas").textContent = sisa.toFixed(2);
            document.getElementById("modalKarungList").innerHTML = "";

            // Populate table
            const tableBody = document.getElementById("modalDetailTableBody");
            tableBody.innerHTML = "";

            detailData.forEach((item) => {
                const karungForThis = detailKarung.filter(k => k.no_model === item.no_model);
                const karungJSON = JSON.stringify(karungForThis);

                const row = `
                    <tr class="fade-in">
                        <td>${item.no_model || ''}</td>
                        <td>${item.kode_warna || ''}</td>
                        <td>${item.foll_up || ''}</td>
                        <td>${item.delivery || ''}</td>
                        <td>${item.qty || ''} kg</td>
                        <td>
                            <button class="btn btn-info btn-sm show-karung" data-karung='${karungJSON}'>
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;

                tableBody.innerHTML += row;
            });
        });

        // Karung detail click handler
        modalDetail.addEventListener("click", function(e) {
            const btn = e.target.closest(".show-karung");
            if (!btn) return;

            // Parse array no_karung
            const list = JSON.parse(btn.getAttribute("data-karung"));
            const karungListElement = document.getElementById("modalKarungList");

            if (list.length === 0) {
                karungListElement.innerHTML = `
                    <em><i class="fas fa-info-circle me-1"></i>Tidak ada karung untuk baris ini.</em>
                `;
            } else {
                // Generate karung list HTML
                const items = list.map(k =>
                    `<li><i class="fas fa-box me-1"></i>No Karung ${k.no_karung} = ${k.kgs_kirim} kg</li>`
                ).join("");

                const modelName = btn.closest("tr").children[0].textContent;

                karungListElement.innerHTML = `
                    <strong><i class="fas fa-list me-1"></i>Daftar No. Karung untuk ${modelName}:</strong>
                    <ul class="mt-2">${items}</ul>
                `;
            }

            // Add fade-in animation
            karungListElement.classList.add('fade-in');
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