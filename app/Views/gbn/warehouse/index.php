<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root {
        --primary-color: #2e7d32;
        /* secondary color is abu-abu*/
        --secondary-color: #778899;
        --background-color: #f4f7fa;
        --card-background: #ffffff;
        --text-color: #333333;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Arial', sans-serif;
    }

    .container-fluid {
        /* max-width: 1200px; */
        margin: 0 auto;
        padding: 2rem;
    }

    .card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-control {
        border: none;
        border-bottom: 2px solid var(--primary-color);
        border-radius: 0;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: var(--secondary-color);
    }

    .btn {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .result-card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#4a90e2'
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
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model / Cluster">
                </div>
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
                </div>
                <div class="col-lg-4 col-sm-12 d-flex gap-2">
                    <button class="btn btn-info flex-grow-1" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                    <button class="btn btn-secondary flex-grow-1" id="reset_data"><i class="fas fa-redo"></i> Reset</button>
                    <button type="button" class="btn btn-success flex-grow-1" id="export_excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>

                </div>
            </div>
        </form>
    </div>

    <div id="result"></div>
    <!-- Modal pindah order -->
    <div class="modal fade" id="modalPindahOrder" tabindex="-1" aria-labelledby="modalPindahOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formPindahOrder" class="needs-validation" novalidate>
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header bg-info text-white border-0">
                        <h5 class="modal-title text-white" id="modalPindahOrderLabel">Pindah Order</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Body -->
                    <div class="modal-body">
                        <!-- SELECT2 FILTER -->
                        <div class="mb-3">
                            <label for="ModelSelect" class="form-label">Pilih No Model</label>
                            <select id="ModelSelect" class="form-select" style="width: 100%"></select>
                        </div>

                        <div class="row g-3" id="pindahOrderContainer">
                            <!-- Isi kartu akan di‑inject via JS -->
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- modal pindah order end -->
    <!-- modal pindah palet -->
    <div class="modal fade" id="modalPindahPalet" tabindex="-1" aria-labelledby="modalPindahPaletLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formPindahPalet" class="needs-validation" novalidate>
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header bg-info text-white border-0">
                        <h5 class="modal-title text-white" id="modalPindahOrderLabel">Pindah Palet</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Body -->
                    <div class="modal-body">
                        <div class="row g-3" id="pindahPaletContainer">
                            <!-- Isi kartu akan di‑inject via JS -->
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <input type="text" class="form-control me-2" name="ttl_kgs_pindah" readonly placeholder="Total Kgs">
                            <input type="text" class="form-control mx-2" name="ttl_cns_pindah" readonly placeholder="Total Cns">
                            <input type="text" class="form-control ms-2" name="ttl_krg_pindah" readonly placeholder="Total Krg">
                        </div>
                        <!-- SELECT2 FILTER -->
                        <div class="mb-3">
                            <label for="ClusterSelect" class="form-label">Pilih Cluster</label>
                            <select id="ClusterSelect" class="form-select" style="width: 100%"></select>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-info">Pindah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- modal pindah palet end -->

</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#filter_data').click(function(e) {
            e.preventDefault();
            let noModel = $.trim($('input[name="noModel"]').val());
            let warna = $.trim($('input[name="warna"]').val());

            $.ajax({
                url: "<?= base_url(session()->get('role') . '/warehouse/search') ?>",
                method: "POST",
                dataType: "json",
                data: {
                    noModel,
                    warna
                },
                success: function(response) {
                    let output = "";
                    if (response.length === 0) {
                        output = `<div class="alert alert-warning text-center">Data tidak ditemukan</div>`;
                    } else {
                        response.forEach(item => {
                            let totalKgs = item.Kgs && item.Kgs > 0 ? item.Kgs : item.KgsStockAwal;
                            let totalKrg = item.Krg && item.Krg > 0 ? item.Krg : item.KrgStockAwal;
                            if (totalKgs == 0 && totalKrg == 0) return;

                            output += `
                            <div class="result-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="badge bg-info">Cluster: ${item.nama_cluster} | No Model: ${item.no_model}</h5>
                                    <span class="badge bg-secondary">Jenis: ${item.item_type}</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <p><strong>Lot Jalur:</strong> ${item.lot_stock || item.lot_awal}</p>
                                        <p><strong>Space:</strong> ${item.kapasitas || 0} KG</p>
                                        <p><strong>Sisa Space:</strong> ${(item.sisa_space || 0).toFixed(2)} KG</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                                        <p><strong>Warna:</strong> ${item.warna}</p>
                                        <p><strong>Total Kgs:</strong> ${(parseFloat(totalKgs) || 0).toFixed(2)} KG | ${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out} Cones | ${totalKrg} KRG</p>
                                    </div>
                                    <div class="col-md-4 d-flex flex-column gap-2">
                                        <button class="btn btn-outline-info btn-sm pindahPalet" 
                                            data-id="${item.id_stock}"
                                            data-nama-cluster="${item.nama_cluster}"
                                            >
                                        Pindah Palet
                                        </button>
                                        <button 
                                            class="btn btn-outline-info btn-sm pindahOrder"
                                            data-id="${item.id_stock}"
                                            data-no-model-old="${item.no_model}"
                                            data-kode-warna="${item.kode_warna}"
                                            >
                                            Pindah Order
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                        });
                    }

                    $('#result').html(output);
                },
                error: function(xhr, status, error) {
                    $('#result').html(`<div class="alert alert-danger text-center">Terjadi kesalahan: ${error}</div>`);
                }
            });
        });

        // Reset filter
        $('#reset_data').click(function(e) {
            e.preventDefault();
            $('input[name="noModel"]').val('');
            $('input[name="warna"]').val('');
            $('#result').html('');
        });

        // Export Excel
        $('#export_excel').on('click', function() {
            const noModel = $('input[name="noModel"]').val();
            const warna = $('input[name="warna"]').val();

            const query = `?no_model=${encodeURIComponent(noModel)}&warna=${encodeURIComponent(warna)}`;
            window.location.href = "<?= base_url(session()->get('role') . '/warehouse/exportExcel') ?>" + query;
        });

    });

    // modal pindah order
    // ketika tombol “Pindah Order” diklik
    $(document).on('click', '.pindahOrder', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        const noModelOld = $(this).data('no-model-old');
        const kodeWarna = $(this).data('kode-warna');

        $('#modalPindahOrder').modal('show');
        const $select = $('#ModelSelect').prop('disabled', true).empty().append('<option>Loading…</option>');
        const $container = $('#pindahOrderContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

        // Fetch model tujuan
        $.getJSON(`${base}/${role}/warehouse/getNoModel`, {
            noModelOld,
            kodeWarna
        }, res => {
            $select.empty();
            if (res.success && res.data.length) {
                $select.append('<option></option>');
                res.data.forEach(d => {
                    $select.append(`<option value="${d.no_model}|${d.item_type}|${d.kode_warna}|${d.color}">${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.color}</option>`);
                });
            } else {
                $select.append('<option>Tidak ada model</option>');
            }
            $select.prop('disabled', false).select2({
                placeholder: 'Pilih Model Tujuan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPindahOrder')
            });
        });

        // Fetch detail order
        $.post(`${base}/${role}/warehouse/getPindahOrder`, {
            id_stock: idStock
        }, res => {
            $container.empty();
            if (!res.success || !res.data.length) {
                return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');
            }

            res.data.forEach(d => {
                const lot = d.lot_stock || d.lot_awal;
                $container.append(`
                <div class="col-md-12">
                    <div class="card result-card h-100">
                        <div class="form-check">
                            <input class="form-check-input row-check" type="checkbox" name="pindah[]" value="${d.id_out_celup}" id="chk${d.id_out_celup}">
                            <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                                ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                            </label>
                            <input type="hidden" name="id_stock[]" value="${d.id_stock}">
                        </div>
                        <div class="card-body row">
                            <div class="col-md-6">
                                <p><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                                <p><strong>Warna:</strong> ${d.warna}</p>
                                <p><strong>Lot Jalur:</strong> ${lot}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>No Karung:</strong> ${d.no_karung}</p>
                                <p><strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim || 0).toFixed(2)} KG</p>
                                <p><strong>Cones:</strong> ${d.cones_kirim} Cns</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            });
        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });
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

        $.post(`${base}/${role}/warehouse/savePindahOrder`, {
            no_model_tujuan: model,
            idOutCelup: orders,
            id_stock: stock
        }, res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    text: `Berhasil memindahkan ${orders.length} order.`
                }).then(() => {
                    $('#modalPindahOrder').modal('hide');
                    $('#filter_data').click(); // reload
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: res.message || 'Terjadi kesalahan saat memindahkan order.'
                });
            }
        }, 'json').fail((_, __, err) => {
            Swal.fire({
                icon: 'error',
                text: `Error: ${err}`
            });
        });
    });

    // modal pindah palet
    // ketika tombol “Pindah Palet diklik
    $(document).on('click', '.pindahPalet', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        const namaCluster = $(this).data('nama-cluster-old');

        $('#modalPindahPalet').modal('show');
        const $select = $('#ClusterSelect').prop('disabled', true).empty().append('<option>Loading…</option>');
        const $container = $('#pindahPaletContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

        // Fetch detail palet
        $.post(`${base}/${role}/warehouse/getPindahPalet`, {
            id_stock: idStock
        }, res => {
            $container.empty();
            if (!res.success || !res.data.length) {
                return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');
            }

            res.data.forEach(d => {
                const lot = d.lot_stock || d.lot_awal;
                $container.append(`
                <div class="col-md-12">
                    <div class="card result-card h-100">
                        <div class="form-check">
                            <input class="form-check-input row-check" type="checkbox" 
                                   name="pindah[]" 
                                   value="${d.id_out_celup}" 
                                   data-kgs="${parseFloat(d.kgs_kirim || 0).toFixed(2)}" 
                                   data-cns="${d.cones_kirim}" 
                                   data-krg="1" 
                                   id="chk${d.id_out_celup}">
                            <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                                ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                            </label>
                            <input type="hidden" name="id_stock[]" value="${d.id_stock}">
                        </div>
                        <div class="card-body row">
                            <div class="col-md-6">
                                <p><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                                <p><strong>Warna:</strong> ${d.warna}</p>
                                <p><strong>Lot Jalur:</strong> ${lot}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>No Karung:</strong> ${d.no_karung}</p>
                                <p><strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim || 0).toFixed(2)} KG</p>
                                <p><strong>Cones:</strong> ${d.cones_kirim} Cns</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            });

            // Recalculate totals when checkboxes change
            $container.on('change', '.row-check', function() {
                let totalKgs = 0,
                    totalCns = 0,
                    totalKrg = 0;
                $container.find('.row-check:checked').each(function() {
                    totalKgs += parseFloat($(this).data('kgs'));
                    totalCns += parseInt($(this).data('cns'), 10);
                    totalKrg += parseInt($(this).data('krg'), 10);
                });
                $('input[name="ttl_kgs_pindah"]').val(totalKgs.toFixed(2));
                $('input[name="ttl_cns_pindah"]').val(totalCns);
                $('input[name="ttl_krg_pindah"]').val(totalKrg);
            });
        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });

        // Fetch model tujuan
        $.getJSON(`${base}/${role}/warehouse/getCluster`, {
            namaCluster,
            totalKgs
        }, res => {
            $select.empty();
            if (res.success && res.data.length) {
                $select.append('<option></option>');
                res.data.forEach(d => {
                    $select.append(`<option value="${d.nama_cluster}">${d.nama_cluster}</option>`);
                });
            } else {
                $select.append('<option>Tidak ada model</option>');
            }
            $select.prop('disabled', false).select2({
                placeholder: 'Pilih Model Tujuan',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPindahPalet')
            });
        });
    });
</script>
<?php $this->endSection(); ?>