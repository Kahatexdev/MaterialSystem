<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* =========================
     NAMESPACE: Halaman Warehouse
     ========================= */
    .wm-warehouse {
        --primary-color: #2e7d32;
        --secondary-color: #778899;
        --background-color: #f4f7fa;
        --card-background: #ffffff;
        --text-color: #333333;
        --gap: 1rem;
        --vh: 1vh;
        /* diupdate via JS (fix 100vh mobile) */
    }

    .wm-warehouse {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Arial', sans-serif;
    }

    .wm-warehouse.container-fluid {
        margin: 0 auto;
        padding: clamp(.75rem, 2vw, 2rem);
    }

    .wm-warehouse .card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, .1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    @media (max-width:576px) {
        .wm-warehouse .card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
            padding: 1rem;
        }
    }

    .wm-warehouse .form-control:focus {
        box-shadow: none;
        border-color: var(--secondary-color);
    }

    .wm-warehouse .form-control {
        min-height: 44px;
        font-size: clamp(14px, 2.8vw, 16px);
    }

    .wm-warehouse .btn {
        border-radius: 25px;
        padding: .75rem 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        transition: all .3s ease;
        min-height: 44px;
        font-size: clamp(14px, 2.8vw, 16px);
    }

    .wm-warehouse .btn-sm {
        min-height: 44px;
        padding: .5rem 1rem;
    }

    .wm-warehouse .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .wm-warehouse .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .wm-warehouse .result-card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all .3s ease;
    }

    .wm-warehouse .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
    }

    @media (hover:none) {
        .wm-warehouse .result-card:hover {
            transform: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }
    }

    .wm-warehouse .badge {
        padding: .5rem 1rem;
        border-radius: 20px;
        font-size: .9rem;
        display: inline-block;
        margin-bottom: .25rem;
    }

    .wm-warehouse .break-all {
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    /* tombol filter: stack di mobile, 3 kolom di >=md */
    .wm-warehouse .btn-stack {
        display: grid;
        grid-template-columns: 1fr;
        gap: .5rem;
    }

    @media (min-width:768px) {
        .wm-warehouse .btn-stack {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* ====== Responsive grid untuk hasil ====== */
    .wm-warehouse #result.grid-results {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    @media (min-width: 576px) {
        .wm-warehouse #result.grid-results {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    @media (min-width: 992px) {
        .wm-warehouse #result.grid-results {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    /* Select2 (dibuat aman hanya di halaman ini & di modal ini) */
    .wm-warehouse .select2-container {
        z-index: 2055;
        width: 100%;
    }

    .wm-warehouse .select2-dropdown {
        font-size: clamp(14px, 3vw, 16px);
    }

    .wm-warehouse .select2-selection--single {
        min-height: 44px;
        border-radius: 25px;
        padding: .5rem 1rem;
        font-size: clamp(14px, 2.8vw, 16px);
        display: flex;
        align-items: center;
    }

    /* Checkbox/radio lebih besar untuk touch */
    .wm-warehouse .form-check-input {
        width: 20px;
        height: 20px;
    }

    /* =========================
     NAMESPACE: Modal khusus halaman ini
     Tambah class .wm-modal pada <div class="modal ...">
     ========================= */
    .wm-modal .modal-dialog.modal-dialog-scrollable {
        height: calc(var(--vh) * 100);
        margin: 0.5rem auto;
    }

    .wm-modal .modal-content {
        display: flex;
        flex-direction: column;
        max-height: calc(var(--vh) * 100);
    }

    .wm-modal .modal-header,
    .wm-modal .modal-footer {
        flex: 0 0 auto;
    }

    .wm-modal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }

    /* Hilangkan constraint tinggi di dalam modal (hanya untuk kartu/page di modal ini) */
    .wm-modal .card.card-plain,
    .wm-modal .card.card-plain .card-body,
    .wm-modal .result-card.h-100,
    .wm-modal .card.card-plain.h-100 {
        max-height: none;
        height: auto;
        overflow: visible;
    }

    /* Reduce motion */
    @media (prefers-reduced-motion:reduce) {
        .wm-warehouse * {
            transition: none !important;
            animation: none !important;
        }
    }
</style>

<div class="container-fluid wm-warehouse">
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#4a90e2'
                });
            });
        </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?= session()->getFlashdata('error') ?>',
                    confirmButtonColor: '#4a90e2'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card">
        <h3 class="mb-4">Stock Material Search</h3>
        <form method="post" action="">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model / Cluster">
                </div>
                <div class="col-12 col-md-4">
                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
                </div>
                <div class="col-12 col-md-4">
                    <div class="btn-stack">
                        <button class="btn btn-info" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                        <button class="btn btn-secondary" id="reset_data"><i class="fas fa-redo"></i> Reset</button>
                        <button type="button" class="btn btn-success" id="export_excel"><i class="fas fa-file-excel"></i> Excel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- card loading -->
    <div class="card loading" id="loadingCard" style="display:none;">
        <div class="card-body text-center">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
    </div>

    <div id="result" class="grid-results"></div>

    <!-- Modal Pindah Order -->
    <div class="modal fade wm-modal" id="modalPindahOrder" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-md-down" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title text-white" id="modalPindahOrderLabel">Pindah Order</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPindahOrder">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label for="ModelSelect" class="form-label">Pilih No Model</label>
                                    <select id="ModelSelect" class="form-select" style="width:100%"></select>
                                </div>

                                <div class="row g-3" id="pindahOrderContainer"><!-- inject --></div>

                                <div class="row">
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label>Total Kgs</label>
                                        <input type="number" inputmode="decimal" step="0.01" class="form-control me-2" name="ttl_kgs" readonly placeholder="0">
                                    </div>
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label>Total Cones</label>
                                        <input type="number" inputmode="numeric" step="1" class="form-control mx-2" name="ttl_cns" readonly placeholder="0">
                                    </div>
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label>Total Karung</label>
                                        <input type="number" inputmode="numeric" step="1" class="form-control ms-2" name="ttl_krg" readonly placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pindah Cluster -->
    <div class="modal fade wm-modal" id="modalPindahCluster" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-md-down" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title text-white" id="modalPindahClusterLabel">Pindah Cluster</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPindahCluster">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-body">
                                <div class="row g-3" id="PindahClusterContainer"><!-- inject --></div>

                                <div class="row g-2 mb-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_kgs_pindah" readonly placeholder="Total Kgs"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_cns_pindah" readonly placeholder="Total Cns"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_krg_pindah" readonly placeholder="Total Krg"></div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label for="ClusterSelect" class="form-label">Pilih Cluster</label>
                                        <select id="ClusterSelect" class="form-select" style="width:100%" required></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="SisaKapasitas" class="form-label">Sisa Kapasitas</label>
                                        <input type="text" class="form-control" id="SisaKapasitas" required>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Pindah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pengeluaran Selain Order -->
    <div class="modal fade wm-modal" id="pengeluaranSelainOrder" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl modal-fullscreen-md-down" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title text-white" id="modalPengeluaranSelainOrderLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formpengeluaranSelainOrder">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="text" name="nama_cluster" id="inputNamaCluster" hidden>
                                    <input type="text" id="id_stock" hidden>
                                    <label for="kategoriSelect" class="form-label">Pilih Kategori</label>
                                    <select id="kategoriSelect" class="form-select" style="width:100%">
                                        <option value="">Pilih Kategori</option>
                                        <option value="Untuk Majalaya">Untuk Majalaya</option>
                                        <option value="Untuk Cover Lurex">Untuk Cover Lurex</option>
                                        <option value="Untuk Cover Lurex Majalaya">Untuk Cover Lurex Majalaya</option>
                                        <option value="Untuk Lokal">Untuk Lokal</option>
                                        <option value="Untuk Twist">Untuk Twist</option>
                                        <option value="Untuk Rosso">Untuk Rosso</option>
                                        <option value="Untuk Cover Spandex">Untuk Cover Spandex</option>
                                        <option value="Untuk Sample">Untuk Sample</option>
                                        <option value="Acrylic Kincir Cijerah">Acrylic Kincir Cijerah</option>
                                        <option value="Untuk Tali Ukur Elastik">Untuk Tali Ukur Elastik</option>
                                        <option value="Perbaikan Data Acrylic">Perbaikan Data Acrylic</option>
                                        <option value="Order Program">Order Program</option>
                                        <option value="Perbaikan Data Menumpuk">Perbaikan Data Menumpuk</option>
                                        <option value="Rombak Cylinder">Rombak Cylinder MC Area</option>
                                        <option value="Untuk Kelos Warna">Untuk Kelos Warna</option>
                                    </select>
                                </div>

                                <div class="row g-3" id="pengeluaranSelainOrderContainer"><!-- inject --></div>

                                <div class="row mt-3 g-2">
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_kgs" readonly placeholder="Total Kgs Terpilih" disabled></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_cns" readonly placeholder="Total Cns Terpilih" disabled></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="ttl_krg" readonly placeholder="Total Krg Terpilih" disabled></div>
                                </div>

                                <div class="row mt-4 g-2">
                                    <div class="col-md-4">
                                        <label for="inputKgs" class="form-label">Total Kgs</label>
                                        <input type="number" inputmode="decimal" step="0.01" min="0" class="form-control" id="inputKgs" name="input_kgs" placeholder="Masukkan Kgs" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inputCns" class="form-label">Total Cns</label>
                                        <input type="number" inputmode="numeric" step="1" min="0" class="form-control" id="inputCns" name="input_cns" placeholder="Masukkan Cns" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inputKrg" class="form-label">Total Krg</label>
                                        <input type="number" inputmode="numeric" step="1" min="0" class="form-control" id="inputKrg" name="input_krg" placeholder="Masukkan Krg" required>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    /* ===== 100vh akurat di mobile (tanpa mengubah body scroll global) ===== */
    (function() {
        function setVH() {
            document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
        }
        setVH();
        window.addEventListener('resize', setVH);

        // Hitung tinggi area scrollable pada modal (khusus .wm-modal)
        function sizeScrollableBody(modalEl) {
            const $m = $(modalEl);
            const $body = $m.find('.modal-body');
            const $header = $m.find('.modal-header');
            const $footer = $m.find('.modal-footer');

            const vh = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--vh')) || (window.innerHeight * 0.01);
            const viewportH = vh * 100;
            const hHeader = $header.outerHeight() || 0;
            const hFooter = $footer.outerHeight() || 0;
            const SAFE = 12;

            const available = Math.max(200, viewportH - hHeader - hFooter - SAFE);
            $body.css({
                height: available + 'px',
                maxHeight: available + 'px',
                overflowY: 'auto'
            });
        }

        // daftar modal yang diukur
        const MODALS = ['#modalPindahOrder', '#modalPindahCluster', '#pengeluaranSelainOrder'];

        MODALS.forEach(sel => {
            $(document).on('shown.bs.modal', sel, function() {
                sizeScrollableBody(this);
                setTimeout(() => sizeScrollableBody(this), 60);
                setTimeout(() => sizeScrollableBody(this), 200);
            });
        });

        // resize/rotate → re-measure kalau ada modal terbuka
        window.addEventListener('resize', function() {
            const openModal = document.querySelector('.wm-modal.show');
            if (openModal) sizeScrollableBody(openModal);
        });

        // Select2 open/close bisa ubah tinggi header → ukur ulang
        $(document).on('select2:open select2:close', function() {
            const openModal = document.querySelector('.wm-modal.show');
            if (openModal) sizeScrollableBody(openModal);
        });
    })();

    // aktifkan touchstart pasif (mengurangi delay pada mobile)
    document.addEventListener('touchstart', function() {}, {
        passive: true
    });

    let currentNoModelOld = '';
    let currentKodeWarna = '';

    $(function() {
        const isSmall = window.matchMedia('(max-width: 576px)').matches;

        $('#filter_data').on('click', function(e) {
            e.preventDefault();
            const noModel = $.trim($('input[name="noModel"]').val());
            const warna = $.trim($('input[name="warna"]').val());

            $.ajax({
                url: "<?= base_url(session()->get('role') . '/warehouse/search') ?>",
                method: "POST",
                dataType: "json",
                data: {
                    noModel,
                    warna
                },
                beforeSend: function() {
                    $('#loadingCard').show();
                    $('#filter_data,#reset_data,#export_excel').prop('disabled', true);
                },
                complete: function() {
                    $('#loadingCard').hide();
                    $('#filter_data,#reset_data,#export_excel').prop('disabled', false);
                },
                success: function(response) {
                    let output = "";
                    if (!response.length) {
                        output = `<div class="alert alert-warning text-center">Data tidak ditemukan</div>`;
                    } else {
                        response.forEach(item => {
                            const totalKgs = (item.Kgs && item.Kgs > 0) ? item.Kgs : item.KgsStockAwal;
                            const totalKrg = (item.Krg && item.Krg > 0) ? item.Krg : item.KrgStockAwal;
                            if (totalKgs == 0 && totalKrg == 0) return;

                            output += `
                <div class="result-card">
                  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h5 class="badge bg-info break-all me-2">Cluster: ${item.nama_cluster} | No Model: ${item.no_model}</h5>
                    <span class="badge bg-secondary break-all">Jenis: ${item.item_type}</span>
                  </div>
                  <div class="row g-3">
                    <div class="col-12 col-md-4">
                      <p class="break-all"><strong>Lot Jalur:</strong> ${item.lot_stock || item.lot_awal}</p>
                      <p><strong>Space:</strong> ${item.kapasitas || 0} KG</p>
                      <p><strong>Sisa Space:</strong> ${(item.sisa_space || 0).toFixed(2)} KG</p>
                    </div>
                    <div class="col-12 col-md-4">
                      <p class="break-all"><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                      <p class="break-all"><strong>Warna:</strong> ${item.warna}</p>
                      <p><strong>Total Kgs:</strong> ${(item.kgs_stock_awal && item.kgs_stock_awal>0)? item.kgs_stock_awal : item.kgs_in_out} KG
                         | ${(item.cns_stock_awal && item.cns_stock_awal>0)? item.cns_stock_awal : item.cns_in_out} Cones
                         | ${(item.krg_stock_awal && item.krg_stock_awal>0)? item.krg_stock_awal : item.krg_in_out} KRG</p>
                    </div>
                    <div class="col-12 col-md-4 gap-2">
                      <button class="btn btn-outline-info btn-sm PindahCluster"
                        data-id="${item.id_stock}" data-nama-cluster-old="${item.nama_cluster}">
                        Pindah Cluster
                      </button>
                      <button class="btn btn-outline-info btn-sm pindahOrder"
                        data-id="${item.id_stock}" data-no-model-old="${item.no_model}" data-kode-warna="${item.kode_warna}">
                        Pindah Order
                      </button>
                      <button class="btn btn-outline-info btn-sm pengeluaranSelainOrder"
                        data-id="${item.id_stock}" data-no-model="${item.no_model}" data-kode-warna="${item.kode_warna}" data-nama-cluster="${item.nama_cluster}">
                        Pengeluaran Selain Order
                      </button>
                    </div>
                  </div>
                </div>`;
                        });
                    }
                    $('#result').html(output);
                },
                error: function(_, __, error) {
                    $('#result').html(`<div class="alert alert-danger text-center">Terjadi kesalahan: ${error}</div>`);
                }
            });
        });

        $('#reset_data').on('click', function(e) {
            e.preventDefault();
            $('input[name="noModel"]').val('');
            $('input[name="warna"]').val('');
            $('#result').html('');
        });

        $('#export_excel').on('click', function() {
            const noModel = $('input[name="noModel"]').val();
            const warna = $('input[name="warna"]').val();
            const query = `?no_model=${encodeURIComponent(noModel)}&warna=${encodeURIComponent(warna)}`;
            window.location.href = "<?= base_url(session()->get('role') . '/warehouse/exportExcel') ?>" + query;
        });

        $('#ModelSelect').select2({
            placeholder: 'Cari No Model atau Kode Warna',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalPindahOrder'),
            dropdownAutoWidth: false,
            minimumInputLength: isSmall ? 1 : 3,
            ajax: {
                url: '<?= base_url() ?>/<?= session()->get('role') ?>/warehouse/getNoModel',
                dataType: 'json',
                delay: isSmall ? 100 : 250,
                data: function(params) {
                    return {
                        term: params.term,
                        noModelOld: currentNoModelOld,
                        kodeWarna: currentKodeWarna
                    };
                },
                processResults: function(res) {
                    if (!res.success) return {
                        results: []
                    };
                    return {
                        results: res.data.map(d => ({
                            id: `${d.no_model}|${d.item_type}|${d.kode_warna}|${d.color}`,
                            text: `${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.color}`
                        }))
                    };
                },
                cache: true
            }
        });

        $('#modalPindahOrder').on('shown.bs.modal', function() {
            setTimeout(() => {
                try {
                    $('#ModelSelect').select2('open');
                } catch (e) {}
            }, 150);
        });
    });

    // —— Modal Pindah Order
    $(document).on('click', '.pindahOrder', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        currentNoModelOld = $(this).data('no-model-old');
        currentKodeWarna = $(this).data('kode-warna');

        $('#ModelSelect').val(null).trigger('change');
        $('#modalPindahOrder').modal('show');
        const $container = $('#pindahOrderContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

        $.post(`${base}${role}/warehouse/getPindahOrderTest`, {
            id_stock: idStock
        }, res => {
            $container.empty();
            if (!res.success || !res.data.length) return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');

            res.data.forEach(d => {
                const lot = d.lot_stock || d.lot_awal;
                $container.append(`
          <div class="col-12">
            <div class="card result-card h-100">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 col-md-6">
                    <h5><strong>Pindah Per Karung</strong></h5>
                    <div class="form-check">
                      <input class="form-check-input row-check" type="checkbox" name="pindah[]" value="${d.id_out_celup}" id="chk${d.id_out_celup}">
                      <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                        <strong>No Model:</strong> ${d.no_model}<br>
                        <strong>Item Type:</strong> ${d.item_type}<br>
                        <strong>Kode Warna:</strong> ${d.kode_warna}<br>
                        <strong>Warna:</strong> ${d.warna}<br>
                        <strong>Lot Jalur:</strong> ${lot}<br>
                        <strong>No Karung:</strong> ${d.no_karung}<br>
                        <strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim||0).toFixed(2)} KG<br>
                        <strong>Cones:</strong> ${d.cones_kirim} Cns
                      </label>
                      <input type="hidden" name="id_stock[]" value="${d.id_stock}">
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <h5><strong>Pindah Perkones</strong></h5>
                    <div class="row gx-2">
                      <div class="col-6">
                        <label for="kgs_out_${d.id_out_celup}" class="form-label small mb-1">Kg Out Manual</label>
                        <input type="number" inputmode="decimal" step="0.01" min="0" class="form-control form-control-sm" name="kgs_out[${d.id_out_celup}]" id="kgs_out_${d.id_out_celup}" placeholder="Kg" disabled>
                      </div>
                      <div class="col-6">
                        <label for="cns_out_${d.id_out_celup}" class="form-label small mb-1">Cones Out Manual</label>
                        <input type="number" inputmode="numeric" step="1" min="0" class="form-control form-control-sm" name="cns_out[${d.id_out_celup}]" id="cns_out_${d.id_out_celup}" placeholder="CNS" disabled>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>`);
            });

            $container.on('change', '.row-check', function() {
                let totalKgs = 0,
                    totalCns = 0,
                    totalKrg = 0;
                $container.find('.row-check').each(function() {
                    const id = $(this).val();
                    const checked = $(this).is(':checked');
                    $(`#kgs_out_${id},#cns_out_${id}`).prop('disabled', !checked);
                });
                $container.find('.row-check:checked').each(function() {
                    const id = $(this).val();
                    const d = res.data.find(x => x.id_out_celup == id);
                    if (!d) return;
                    const rawKgs = ($('#kgs_out_' + id).val() || '').replace(',', '.').trim();
                    const rawCns = ($('#cns_out_' + id).val() || '').trim();
                    const useManual = rawKgs !== '';
                    const mKgs = parseFloat(rawKgs) || 0;
                    const mCns = parseInt(rawCns) || 0;
                    totalKgs += useManual ? mKgs : parseFloat(d.kgs_kirim || 0);
                    totalCns += useManual ? mCns : parseInt(d.cones_kirim || 0);
                    totalKrg += useManual ? 0 : 1;
                });
                $('input[name="ttl_kgs"]').val(totalKgs.toFixed(2));
                $('input[name="ttl_cns"]').val(totalCns);
                $('input[name="ttl_krg"]').val(totalKrg);
            });

            $container.on('input', 'input[name^="kgs_out"], input[name^="cns_out"]', function() {
                const el = $(this);
                const id = this.id.split('_')[2];
                const d = res.data.find(x => x.id_out_celup == id);
                if (!d) return;
                const maxKgs = parseFloat(d.kgs_kirim || 0);
                const maxCns = parseInt(d.cones_kirim || 0, 10);
                if (this.name.startsWith('kgs_out')) {
                    let v = parseFloat((el.val() || '0').replace(',', '.')) || 0;
                    if (v > maxKgs) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nilai Terlalu Besar',
                            text: `Kg Out Manual tidak boleh lebih dari ${maxKgs.toFixed(2)} KG`
                        });
                        el.val(0);
                    }
                } else {
                    let v = parseInt(el.val() || '0', 10) || 0;
                    if (v > maxCns) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nilai Terlalu Besar',
                            text: `Cones Out Manual tidak boleh lebih dari ${maxCns} Cns`
                        });
                        el.val(0);
                    }
                }
                $container.find('.row-check:checked').trigger('change');
            });

        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });
    });

    $('#modalPindahOrder').on('hidden.bs.modal', function() {
        $('input[name="ttl_kgs"]').val('');
        $('input[name="ttl_cns"]').val('');
        $('input[name="ttl_krg"]').val('');
        $('#SisaKapasitas').val('');
    });

    $('#formPindahOrder').on('submit', function(e) {
        e.preventDefault();
        const role = '<?= session()->get('role') ?>';
        const base = '<?= base_url() ?>';
        const model = $('#ModelSelect').val();
        const orders = $("input[name='pindah[]']:checked").map((_, el) => el.value).get();
        const stock = $("input[name='id_stock[]']").map((_, el) => el.value).get();
        if (!model) return Swal.fire({
            icon: 'warning',
            text: 'Pilih model tujuan terlebih dahulu!'
        });
        if (!orders.length) return Swal.fire({
            icon: 'warning',
            text: 'Pilih minimal satu order!'
        });

        const kgsOut = {},
            cnsOut = {};
        orders.forEach(id => {
            const rawKgs = ($('#kgs_out_' + id).val() || '0').replace(',', '.').trim();
            const rawCns = ($('#cns_out_' + id).val() || '0').trim();
            kgsOut[id] = parseFloat(rawKgs);
            cnsOut[id] = parseInt(rawCns, 10);
        });

        $.post(`${base}${role}/warehouse/savePindahOrderTest`, {
            no_model_tujuan: model,
            idOutCelup: orders,
            id_stock: stock,
            kgs_out: kgsOut,
            cns_out: cnsOut
        }, res => {
            const ok = !!res.success;
            Swal.fire({
                icon: ok ? 'success' : 'error',
                text: ok ? `Berhasil memindahkan ${orders.length} order.` : (res.message || 'Terjadi kesalahan saat memindahkan order.'),
                confirmButtonText: 'OK',
                willClose: () => {
                    $('#modalPindahOrder').modal('hide');
                    $('#formPindahOrder')[0].reset();
                    reloadSearchResult();
                }
            });
        }, 'json').fail((_, __, err) => {
            Swal.fire({
                icon: 'error',
                text: `Error: ${err}`
            });
        });
    });

    // —— Modal Pindah Cluster
    $(document).on('click', '.PindahCluster', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        const namaCluster = $(this).data('nama-cluster-old');

        $('#modalPindahCluster').modal('show');
        $('#modalPindahClusterLabel').text(`Pindah Cluster - ${namaCluster}`);
        const $select = $('#ClusterSelect').prop('disabled', true).empty().append('<option>Loading…</option>');
        const $container = $('#PindahClusterContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

        $.post(`${base}${role}/warehouse/getPindahCluster`, {
            id_stock: idStock
        }, res => {
            $container.empty();
            if (!res.success || !res.data.length) return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');
            res.data.forEach(d => {
                const lot = d.lot_stock || d.lot_awal;
                $container.append(`
          <div class="col-12">
            <div class="card result-card h-100">
              <div class="form-check">
                <input class="form-check-input row-check" type="checkbox" name="pindah[]" value="${d.id_out_celup}"
                  data-cluster-old="${d.nama_cluster}" data-kgs="${parseFloat(d.kgs_kirim||0).toFixed(2)}"
                  data-cns="${d.cones_kirim}" data-krg="1" data-no_model="${d.no_model}"
                  data-item_type="${d.item_type}" data-kode_warna="${d.kode_warna}" data-warna="${d.warna}"
                  data-lot="${lot}" data-id-stock="${d.id_stock}" id="chk${d.id_out_celup}">
                <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                  ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                </label>
              </div>
              <div class="card-body row">
                <div class="col-12 col-md-6">
                  <p><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                  <p><strong>Warna:</strong> ${d.warna}</p>
                  <p><strong>Lot Jalur:</strong> ${lot}</p>
                </div>
                <div class="col-12 col-md-6">
                  <p><strong>No Karung:</strong> ${d.no_karung}</p>
                  <p><strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim||0).toFixed(2)} KG</p>
                  <p><strong>Cones:</strong> ${d.cones_kirim} Cns</p>
                </div>
              </div>
            </div>
          </div>`);
            });

            $container.on('change', '.row-check', function() {
                let totalKgs = 0,
                    totalCns = 0,
                    totalKrg = 0;
                $container.find('.row-check:checked').each(function() {
                    totalKgs += parseFloat($(this).data('kgs'));
                    totalCns += parseInt($(this).data('cns'), 10);
                    totalKrg += 1;
                });
                $('input[name="ttl_kgs_pindah"]').val(totalKgs.toFixed(2));
                $('input[name="ttl_cns_pindah"]').val(totalCns);
                $('input[name="ttl_krg_pindah"]').val(totalKrg);

                const selectedClusterValue = $select.val();
                if (totalKgs > 0) {
                    fetchClusters(totalKgs, selectedClusterValue);
                    $select.prop('disabled', false);
                } else {
                    $select.prop('disabled', true).empty();
                    $('#SisaKapasitas').val('');
                }
            });
        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });

        function fetchClusters(totalKgs, previousCluster) {
            $.getJSON(`${base}${role}/warehouse/getNamaCluster`, {
                namaCluster,
                totalKgs
            }, res => {
                $select.empty();
                if (res.success && res.data.length) {
                    $select.append('<option value="" data-sisa-kapasitas="">Pilih Cluster</option>');
                    res.data.forEach(d => {
                        $select.append(`<option value="${d.nama_cluster}" data-sisa_kapasitas="${d.sisa_kapasitas}">${d.nama_cluster}</option>`);
                    });
                    if (previousCluster && $select.find(`option[value="${previousCluster}"]`).length) {
                        $select.val(previousCluster).trigger('change');
                    } else {
                        $('#SisaKapasitas').val('');
                    }
                    $select.off('change').on('change', function() {
                        const sisa = $(this).find('option:selected').data('sisa_kapasitas');
                        $('#SisaKapasitas').val($(this).val() ? parseFloat(sisa || 0).toFixed(2) : '');
                    });
                } else {
                    $select.append('<option>Tidak Ada Cluster</option>');
                    $('#SisaKapasitas').val('');
                }
            });
        }
    });

    $('#modalPindahCluster').on('hidden.bs.modal', function() {
        $('input[name="ttl_kgs_pindah"]').val('');
        $('input[name="ttl_cns_pindah"]').val('');
        $('input[name="ttl_krg_pindah"]').val('');
        $('#SisaKapasitas').val('');
    });

    $('#formPindahCluster').on('submit', function(e) {
        e.preventDefault();
        const role = '<?= session()->get("role") ?>';
        const base = '<?= base_url() ?>';
        const cluster = $('#ClusterSelect').val();
        const $checked = $("input[name='pindah[]']:checked");
        if (!$checked.length) return Swal.fire({
            icon: 'warning',
            text: 'Pilih setidaknya satu karung untuk dipindah!'
        });
        if (!cluster) return Swal.fire({
            icon: 'warning',
            text: 'Pilih cluster terlebih dahulu!'
        });

        const detail = $checked.map((_, chk) => {
            const $c = $(chk);
            return {
                id_out_celup: $c.val(),
                cluster_old: $c.data('cluster-old'),
                id_stock: $c.data('id-stock'),
                no_model: $c.data('no_model'),
                item_type: $c.data('item_type'),
                kode_warna: $c.data('kode_warna'),
                warna: $c.data('warna'),
                lot: $c.data('lot'),
                kgs: $c.data('kgs'),
                cns: $c.data('cns'),
                krg: $c.data('krg')
            };
        }).get();

        $.post(`${base}${role}/warehouse/savePindahCluster`, {
            cluster_tujuan: cluster,
            detail: detail
        }, res => {
            Swal.fire({
                title: 'Berhasil!',
                text: res.message || 'Pindah cluster berhasil',
                icon: 'success',
                confirmButtonText: 'OK',
                willClose: () => {
                    $('#modalPindahCluster').modal('hide');
                    $('#formPindahCluster')[0].reset();
                    reloadSearchResult();
                }
            });
        }, 'json').fail(xhr => {
            Swal.fire({
                title: 'Terjadi Kesalahan!',
                text: xhr.responseText || 'Ada masalah dengan permintaan Anda.',
                icon: 'error',
                confirmButtonText: 'OK',
                willClose: () => {
                    $('#modalPindahCluster').modal('hide');
                    $('#formPindahCluster')[0].reset();
                    reloadSearchResult();
                }
            });
        });
    });

    // —— Modal Pengeluaran Selain Order
    $(function() {
        let selectedData = [];
        $(document).on('click', '.pengeluaranSelainOrder', function() {
            const idStock = $(this).data('id');
            const base = '<?= base_url() ?>';
            const role = '<?= session()->get('role') ?>';
            const namaCluster = $(this).data('nama-cluster');
            const $container = $('#pengeluaranSelainOrderContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

            $('#pengeluaranSelainOrder').modal('show');
            $('#modalPengeluaranSelainOrderLabel').text(`Pengeluaran Selain Order - ${namaCluster}`);

            $.post(`${base}${role}/warehouse/getPindahOrder`, {
                id_stock: idStock
            }, res => {
                $container.empty();
                selectedData = res.data || [];
                if (!res.success || !selectedData.length) return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');

                selectedData.forEach(d => {
                    const lot = d.lot_stock || d.lot_awal;
                    $container.append(`
            <div class="col-12">
              <div class="card result-card h-100">
                <div class="form-check">
                  <input class="form-check-input row-check" type="radio" name="pilih_item" value="${d.id_out_celup}" id="radio${d.id_out_celup}" data-lot="${lot}">
                  <label class="form-check-label fw-bold" for="radio${d.id_out_celup}">
                    ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                  </label>
                </div>
                <div class="card-body row">
                  <div class="col-12 col-md-4">
                    <p class="break-all"><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                    <p class="break-all"><strong>Warna:</strong> ${d.warna}</p>
                  </div>
                  <div class="col-12 col-md-4">
                    <p><strong>Lot Jalur:</strong> ${lot}</p>
                    <p><strong>No Karung:</strong> ${d.no_karung}</p>
                  </div>
                  <div class="col-12 col-md-4">
                    <p><strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim||0).toFixed(2)} KG</p>
                    <p><strong>Cones:</strong> ${d.cones_kirim} Cns</p>
                  </div>
                </div>
              </div>
            </div>`);
                });
                calculateTotals();
            });

            $('#inputNamaCluster').val(namaCluster);
            $('#id_stock').val(idStock);
            $container.on('change', '.row-check', calculateTotals);
        });

        function calculateTotals() {
            let totalKgs = 0,
                totalCns = 0,
                totalKrg = 0;
            const selected = $('#pengeluaranSelainOrderContainer .row-check:checked').val();
            const item = selectedData.find(d => d.id_out_celup == selected);
            if (item) {
                totalKgs = parseFloat(item.kgs_kirim || 0);
                totalCns = parseInt(item.cones_kirim || 0);
                totalKrg = 1;
            }
            $('input[name="ttl_kgs"]').val(totalKgs.toFixed(2));
            $('input[name="ttl_cns"]').val(totalCns);
            $('input[name="ttl_krg"]').val(totalKrg);
        }

        $('#inputKgs,#inputCns,#inputKrg').on('input', function() {
            const maxKgs = parseFloat($('input[name="ttl_kgs"]').val()) || 0;
            const maxCns = parseInt($('input[name="ttl_cns"]').val()) || 0;
            const maxKrg = parseInt($('input[name="ttl_krg"]').val()) || 0;
            const iKgs = parseFloat($('#inputKgs').val()) || 0;
            const iCns = parseInt($('#inputCns').val()) || 0;
            const iKrg = parseInt($('#inputKrg').val()) || 0;
            if (iKgs > maxKgs) {
                alert(`Total Kgs tidak boleh melebihi ${maxKgs}`);
                $('#inputKgs').val(maxKgs);
            }
            if (iCns > maxCns) {
                alert(`Total Cns tidak boleh melebihi ${maxCns}`);
                $('#inputCns').val(maxCns);
            }
            if (iKrg > maxKrg) {
                alert(`Total Krg tidak boleh melebihi ${maxKrg}`);
                $('#inputKrg').val(maxKrg);
            }
        });
    });

    $('#formpengeluaranSelainOrder').on('submit', function(e) {
        e.preventDefault();
        const idOutCelup = $('input[name="pilih_item"]:checked').val();
        const kategori = $('#kategoriSelect').val();
        const kgsOtherOut = $('#inputKgs').val();
        const cnsOtherOut = $('#inputCns').val();
        const krgOtherOut = $('#inputKrg').val();
        const namaCluster = $('#inputNamaCluster').val();
        const lot = $('input[name="pilih_item"]:checked').data('lot');
        const idStock = $('#id_stock').val();

        if (!idOutCelup || !kategori) return alert('Silakan pilih item dan kategori terlebih dahulu.');

        $.ajax({
            url: '<?= base_url(session()->get("role") . "/warehouse/savePengeluaranSelainOrder") ?>',
            method: 'POST',
            data: {
                id_out_celup: idOutCelup,
                kategori,
                kgs_other_out: kgsOtherOut,
                cns_other_out: cnsOtherOut,
                krg_other_out: krgOtherOut,
                lot,
                nama_cluster: namaCluster,
                id_stock: idStock
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message || 'Data berhasil disimpan!',
                        icon: 'success',
                        confirmButtonColor: '#4a90e2',
                        willClose: () => {
                            $('#pengeluaranSelainOrder').modal('hide');
                            $('#formpengeluaranSelainOrder')[0].reset();
                            reloadSearchResult();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menyimpan data: ' + res.message,
                        icon: 'error',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: 'Ada masalah dengan server.',
                    icon: 'error',
                    confirmButtonColor: '#e74c3c'
                });
            }
        });
    });

    function reloadSearchResult() {
        $('#filter_data').click();
    }
</script>
<?php $this->endSection(); ?>