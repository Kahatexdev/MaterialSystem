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

    .group-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        position: relative;
        overflow: hidden;
    }

    .group-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4b6cb7, #182848);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .group-card:hover {
        border-color: #182848;
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        transform: translateY(-2px);
    }

    .group-card:hover::before {
        transform: scaleX(1);
    }

    .group-card.selected {
        border-color: #007bff;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.2);
    }

    .group-card.selected::before {
        transform: scaleX(1);
    }

    .group-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .group-card:hover .group-icon {
        transform: scale(1.1);
    }

    .group-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #2c3e50;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }

    .group-description {
        font-size: 0.9rem;
        color: #6c757d;
        line-height: 1.4;
    }

    .group-card.selected .group-title {
        color: #0056b3;
    }

    .cards-container {
        gap: 20px;
    }

    .apply-button {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    }

    .apply-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }

    .apply-button:active {
        transform: translateY(0);
    }

    .capacity-legend .btn {
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 8px 16px;
        margin: 0 2px;
        border: none;
        transition: all 0.3s ease;
    }

    .capacity-legend .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .main-card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .main-card .card-body {
        padding: 2rem;
    }

    @media (max-width: 768px) {
        .cards-container {
            flex-direction: column;
        }

        .capacity-legend .btn {
            margin: 2px;
            font-size: 0.8rem;
            padding: 6px 12px;
        }
    }

    /* ===== MODAL ENHANCEMENTS ===== */
    .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        /* biar header rounded */
        box-shadow: 0 20px 50px rgba(13, 110, 253, 0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #0d6efd 0%, #2b6cb0 100%);
        color: #fff;
        border-bottom: none;
        align-items: center;
        padding: 1rem 1.25rem;
    }

    .modal-header .btn-close {
        filter: invert(1);
        /* jadi ikon close putih */
        opacity: .9;
    }

    .modal-header .btn-close:focus {
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .35);
    }

    .modal-body {
        padding: 1.5rem;
        background: #ffffff;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: .75rem 1.25rem;
    }

    /* Tabel dalam modal biar lebih rapi */
    .table.modal-table>thead th {
        background: #f3f6fb;
        color: #344767;
        font-weight: 600;
    }

    .table.modal-table>tbody tr:hover {
        background: #f8fbff;
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
            <div class="card main-card">
                <div class="mt-3 ms-3 text-center">
                    <h3><strong>LAYOUT STOCK ORDER</strong></h3>
                </div>
                <div class="card mt-2">
                    <!-- Keterangan kapasitas -->
                    <div class="mb-4 text-center capacity-legend">
                        <button class="btn text-white" style="background-color: #b0b0b0;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Kosong">0%</button>
                        <button class="btn text-white" style="background-color: #007bff;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Rendah">1-70%</button>
                        <button class="btn text-white" style="background-color: #ff851b;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Sedang">71-99%</button>
                        <button class="btn text-white" style="background-color: #dc3545;" data-bs-toggle="tooltip" data-bs-placement="top" title="Stok Penuh">100%</button>
                    </div>

                    <form id="groupForm">
                        <div class="mb-2">
                            <!-- <h5 class="mb-3 text-center">Select Group</h5> -->
                            <div class="row cards-container justify-content-center">
                                <!-- Group I Card -->
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="group-card text-center p-3 h-100" data-value="I">
                                        <div class="group-icon mx-auto" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
                                            <i class="fas fa-warehouse" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="group-title">Group I</div>
                                    </div>
                                </div>

                                <!-- Group II Card -->
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="group-card text-center p-3 h-100" data-value="II">
                                        <div class="group-icon mx-auto" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
                                            <i class="fas fa-warehouse" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="group-title">Group II</div>
                                    </div>
                                </div>

                                <!-- Group III Card -->
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="group-card text-center p-3 h-100" data-value="III">
                                        <div class="group-icon mx-auto" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
                                            <i class="fas fa-warehouse" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="group-title">Group III</div>
                                    </div>
                                </div>

                                <!-- Covering Card -->
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="group-card text-center p-3 h-100" data-value="covering">
                                        <div class="group-icon mx-auto" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
                                            <i class="fas fa-warehouse" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="group-title">Covering</div>
                                    </div>
                                </div>

                                <!-- Nylon Card -->
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <div class="group-card text-center p-3 h-100" data-value="nylon">
                                        <div class="group-icon mx-auto" style="background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);">
                                            <i class="fas fa-warehouse" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div class="group-title">Nylon</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden input to store selected value -->
                            <input type="hidden" id="groupSelect" name="group" value="I">
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary apply-button">
                                <i class="fas fa-check me-2"></i>Apply
                            </button>
                        </div>
                    </form>

                    <!-- card loading -->
                    <div class="card loading" id="loadingCard" style="display: none;">
                        <div class="card-body text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tempat untuk Menampilkan Tabel -->
                    <div id="groupTable" class="mt-0 px-5"></div>
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
                    <table class="table table-bordered table-hover modal-table">
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
                <!-- Karung List (improved) -->
                <div id="modalKarungList" class="mt-3 d-none">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Detail Karung
                            <span class="badge bg-primary ms-2" id="karungCount">0</span>
                        </h6>

                        <div class="ms-auto input-group input-group-sm" style="max-width: 320px;">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input id="karungSearch" type="text" class="form-control" placeholder="Cari no karung / lot..." autocomplete="off">
                        </div>
                    </div>

                    <div id="karungSummary" class="small text-muted mt-1"></div>

                    <ul id="karungItems" class="mt-2 ps-3 mb-0"></ul>
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
    document.addEventListener("DOMContentLoaded", function() {
        const modalDetail = document.getElementById("modalDetail");
        if (!modalDetail) return;

        // Helpers
        const encodeDataAttr = (obj) => encodeURIComponent(JSON.stringify(obj ?? []));
        const decodeDataAttr = (str) => {
            try {
                return JSON.parse(decodeURIComponent(str || ""));
            } catch {
                return [];
            }
        };
        const esc = (s) => String(s ?? "").replace(/[&<>"']/g, m => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        } [m]));
        const fmtKg = (n) => (Number(n || 0)).toFixed(2);
        const slug = (s) => String(s ?? "").toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, "");

        // State
        let currentKarungList = [];
        let currentModelName = "";
        let currentModelKey = ""; // untuk deteksi klik "mata" model yang sama

        function summarize(list) {
            const totalKarung = list.length;

            // total IN dari pemasukan (oc.kgs_kirim)
            const totalIn = list.reduce((a, k) => a + (Number(k.kgs_kirim) || 0), 0);

            // total OUT dari pengeluaran (pag.kgs_out)
            const totalOut = list.reduce((a, k) => {
                const p = k.pengeluaran || {};
                return a + (Number(p.kgs_out) || 0);
            }, 0);

            const net = totalIn - totalOut;

            document.getElementById("karungCount").textContent = totalKarung;
            document.getElementById("karungSummary").innerHTML =
                `<span class="me-3"><i class="fas fa-arrow-down me-1"></i>Total IN: <strong>${fmtKg(totalIn)}</strong> kg</span>` +
                `<span class="me-3"><i class="fas fa-arrow-up me-1"></i>Total OUT: <strong>${fmtKg(totalOut)}</strong> kg</span>` +
                `<span><i class="fas fa-equals me-1"></i>NET: <strong>${fmtKg(net)}</strong> kg</span>`;
        }

        // Buat ID collapse yang STABIL per item
        function buildCollapseId(modelName, k) {
            const base = slug(modelName || "model");
            const noK = slug(k.no_karung || "-");
            const lot = slug(k.lot_kirim || "-");
            return `krg-${base}-${noK}-${lot}`;
        }

        function renderKarung(list, modelName, preserveOpenIds = []) {
            const wrap = document.getElementById("modalKarungList");
            const ul = document.getElementById("karungItems");
            wrap.classList.remove("d-none");
            wrap.classList.add("fade-in");

            if (!list.length) {
                ul.innerHTML = `<li class="mt-2"><em><i class="fas fa-info-circle me-1"></i>Tidak ada karung untuk ${esc(modelName || '(tanpa model)')}.</em></li>`;
                summarize([]);
                return;
            }

            ul.innerHTML = list.map((k) => {
                const id = buildCollapseId(modelName, k);
                // console.log(k);

                // --- sumber dari detail_pernokarung ---
                const kgsIn = fmtKg(k.kgs_kirim || 0);
                const lotIn = esc(k.lot_kirim || "-");

                const pm = k.pemasukan || {};
                const pg = k.pengeluaran || {};
                // console.log(pm.kgs_kirim ,pg.kgs_out);
                const idOut = pm.id_out_celup != null ? String(pm.id_out_celup) : "";
                const idStock = pm.id_stock != null ? String(pm.id_stock) : "";
                const outJlr = pm.out_jalur != null ? String(pm.out_jalur) : "";

                const kgsOut = fmtKg(pg.kgs_out || 0);
                const cnsOut = (pg.cns_out != null) ? String(pg.cns_out) : "";
                const lotOut = pg.lot_out ? esc(pg.lot_out) : "";
                const status = pg.status ? esc(pg.status) : "";
                const area = pg.area_out ? esc(pg.area_out) : "";

                const net = (Number(k.kgs_kirim) || 0) - (Number(pg.kgs_out) || 0);
                const netStr = fmtKg(net);

                return `
<li class="mb-2">
  <div class="d-flex align-items-center justify-content-between gap-2">
    <span class="text-truncate">
      <i class="fas fa-box me-1"></i>No Karung <strong>${esc(k.no_karung || "-")}</strong>
      <span class="ms-2"><i class="fas fa-weight-hanging me-1"></i>IN: <strong>${kgsIn}</strong></span>
      <span class="ms-2"><i class="fas fa-hashtag me-1"></i>Lot IN: <strong>${lotIn}</strong></span>
      <span class="ms-2"><i class="fas fa-balance-scale me-1"></i>NET: <strong>${netStr}</strong></span>
    </span>
    <button class="btn btn-sm btn-outline-secondary krg-toggle" type="button" data-target-id="${id}">
      Detail
    </button>
  </div>

  <div id="${id}" class="collapse mt-2">
    <div class="border rounded p-2">
      <div class="row g-3">
        <!-- Pemasukan -->
        <div class="col-md-6">
          <div class="fw-semibold mb-1"><i class="fas fa-arrow-down me-1"></i>Pemasukan</div>
          <div><i class="fas fa-weight-hanging me-1"></i>Kgs kirim: <strong>${kgsIn}</strong> kg</div>
          <div><i class="fas fa-hashtag me-1"></i>Lot kirim: <strong>${lotIn}</strong></div>
          ${idOut   ? `<div class="mt-1"><i class="fas fa-id-card me-1"></i>id_out_celup: <strong>${esc(idOut)}</strong></div>` : ""}
          ${idStock ? `<div><i class="fas fa-cubes me-1"></i>id_stock: <strong>${esc(idStock)}</strong></div>` : ""}
          ${outJlr  ? `<div><i class="fas fa-random me-1"></i>out_jalur: <strong>${esc(outJlr)}</strong></div>` : ""}
          ${k.keterangan ? `<div class="mt-1"><i class="fas fa-sticky-note me-1"></i>Ket: <em>${esc(k.keterangan)}</em></div>` : ""}
        </div>

        <!-- Pengeluaran -->
        <div class="col-md-6">
          <div class="fw-semibold mb-1"><i class="fas fa-arrow-up me-1"></i>Pengeluaran</div>
          <div><i class="fas fa-weight-hanging me-1"></i>Kgs out: <strong>${kgsOut}</strong> kg</div>
          <div><i class="fas fa-circle me-1"></i>Cones out: <strong>${esc(cnsOut)}</strong></div>
          <div><i class="fas fa-hashtag me-1"></i>Lot out: <strong>${lotOut}</strong></div>
          <div><i class="fas fa-tag me-1"></i>Status: <strong>${status}</strong></div>
          <div><i class="fas fa-map-marker-alt me-1"></i>Area: <strong>${area}</strong></div>
        </div>
      </div>
    </div>
  </div>
</li>`;
            }).join("");


            // Pulihkan panel yang tadinya terbuka
            preserveOpenIds.forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.add("show");
                    const trigger = document.querySelector(`[data-bs-target="#${id}"]`);
                    if (trigger) trigger.setAttribute("aria-expanded", "true");
                }
            });

            summarize(list);
        }

        // Saat modal dibuka
        // === handler saat modal dibuka: TETAPI sekarang ambil dari detail_pernokarung ===
        modalDetail.addEventListener("show.bs.modal", function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;

            const kapasitas = Number(btn.getAttribute("data-kapasitas") || 0);
            const nama_cluster = btn.getAttribute("data-nama_cluster") || "-";
            let detailData = [],
                perKarung = [];

            try {
                detailData = JSON.parse(btn.getAttribute("data-detail") || "[]");
            } catch {
                detailData = [];
            }
            try {
                perKarung = JSON.parse(btn.getAttribute("data-karung") || "[]");
            } catch {
                perKarung = [];
            }

            const totalTerisi = detailData.reduce((s, it) => s + (Number(it.qty) || 0), 0);
            const sisa = kapasitas - totalTerisi;

            document.getElementById("modalKapasitas").textContent = fmtKg(kapasitas);
            document.getElementById("modalTotalQty").textContent = fmtKg(totalTerisi);
            document.getElementById("modalNamaCluster").textContent = nama_cluster;
            document.getElementById("modalSisaKapasitas").textContent = fmtKg(sisa);

            // Reset list karung
            document.getElementById("modalKarungList").classList.add("d-none");
            document.getElementById("karungItems").innerHTML = "";
            document.getElementById("karungCount").textContent = "0";
            document.getElementById("karungSummary").textContent = "";

            // Render tabel ringkas per model
            const tbody = document.getElementById("modalDetailTableBody");
            tbody.innerHTML = "";

            detailData.forEach((it) => {
                const noModel = it.no_model || "";
                const listPerModel = perKarung.filter(k => (k.no_model || "") === noModel);
                const karungAttr = encodeDataAttr(listPerModel);

                const tr = document.createElement("tr");
                tr.className = "fade-in";
                tr.innerHTML = `
                <td>${esc(it.no_model || "")}</td>
                <td>${esc(it.kode_warna || "")}</td>
                <td>${esc(it.foll_up || "")}</td>
                <td>${esc(it.delivery || "")}</td>
                <td>${fmtKg(it.qty)} kg</td>
                <td>
                  <button class="btn btn-info btn-sm show-karung"
                          data-model="${esc(noModel)}"
                          data-karung="${karungAttr}">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>`;
                tbody.appendChild(tr);
            });

            const search = document.getElementById("karungSearch");
            if (search) search.value = "";
            currentModelKey = ""; // reset
        });

        // Klik tombol “mata” → tampilkan daftar karung per baris
        modalDetail.addEventListener("click", function(e) {
            const btn = e.target.closest(".show-karung");
            if (!btn) return;

            const clickedModel = btn.getAttribute("data-model") || "(tanpa model)";
            const clickedList = decodeDataAttr(btn.getAttribute("data-karung"));

            const wrap = document.getElementById("modalKarungList");

            // 1) Kalau klik model yang sama & list sudah tampil → JANGAN re-render (biar collapse tetap)
            if (currentModelKey === clickedModel && !wrap.classList.contains("d-none")) {
                // OPTIONAL: kalau mau jadikan toggle tampil/sembunyi tanpa re-render:
                // wrap.classList.toggle('d-none');
                return;
            }

            // 2) Simpan id yang sedang "show" sebelum render
            const openIds = Array.from(document.querySelectorAll('#karungItems .collapse.show')).map(n => n.id);

            currentModelName = clickedModel;
            currentModelKey = clickedModel;
            currentKarungList = clickedList;

            const title = modalDetail.querySelector('#modalKarungList h6');
            if (title) {
                title.innerHTML =
                    `<i class="fas fa-list me-2"></i>Daftar No. Karung untuk <span class="text-primary">${esc(currentModelName)}</span> ` +
                    `<span class="badge bg-primary ms-2" id="karungCount">0</span>`;
            }

            // 3) Render dengan memulihkan panel yang terbuka
            renderKarung(currentKarungList, currentModelName, openIds);
        });

        // Pencarian client-side (no karung / lot)
        const searchInput = document.getElementById("karungSearch");
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const q = this.value.trim().toLowerCase();
                if (!q) return renderKarung(currentKarungList, currentModelName);
                const filtered = currentKarungList.filter(k => {
                    const lotOut = (k.pengeluaran && k.pengeluaran.lot_out) ? String(k.pengeluaran.lot_out) : "";
                    return String(k.no_karung || "").toLowerCase().includes(q) ||
                        String(k.lot_kirim || "").toLowerCase().includes(q) ||
                        lotOut.toLowerCase().includes(q);
                });
                renderKarung(filtered, currentModelName);
            });
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalDetail = document.getElementById("modalDetail");
        if (!modalDetail) return;

        // Delegated click khusus tombol Detail karung
        modalDetail.addEventListener("click", function(e) {
            const btn = e.target.closest(".krg-toggle");
            if (!btn) return;

            const targetId = btn.getAttribute("data-target-id");
            const targetEl = document.getElementById(targetId);
            if (!targetEl) return;

            // Buat / ambil instance tanpa auto toggle
            const inst = bootstrap.Collapse.getOrCreateInstance(targetEl, {
                toggle: false
            });

            // Toggle manual pasti: jika sudah show → hide, kalau tidak → show
            if (targetEl.classList.contains("show")) {
                inst.hide();
                btn.setAttribute("aria-expanded", "false");
            } else {
                inst.show();
                btn.setAttribute("aria-expanded", "true");
            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        const cards = $('.group-card');
        const hiddenInput = $('#groupSelect');

        // Set default selection (Group I)
        // cards.first().addClass('selected');

        // Add click event listeners to cards
        cards.on('click', function() {
            // Remove selected class from all cards
            cards.removeClass('selected');

            // Add selected class to clicked card
            $(this).addClass('selected');

            // Update hidden input value
            hiddenInput.val($(this).data('value'));
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Fungsi untuk memuat data berdasarkan grup yang dipilih
        function loadGroupData(group) {
            $.ajax({
                url: "<?= base_url($role . '/getGroupData') ?>",
                type: "POST",
                data: {
                    group: group
                },
                beforeSend: function() {
                    $("#loadingCard").show(); // Tampilkan loading
                    $("#groupTable").html(""); // Kosongkan tabel sebelum memuat data baru
                },
                complete: function() {
                    $("#loadingCard").hide(); // Sembunyikan loading setelah selesai
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
        // loadGroupData("I");
    });
</script>
<?php $this->endSection(); ?>