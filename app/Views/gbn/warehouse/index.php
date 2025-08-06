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

    /* .form-control {
        border: none;
        border-bottom: 2px solid var(--primary-color);
        border-radius: 0;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    } */

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
    <div class="modal fade" id="modalPindahOrder" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
                                    <select id="ModelSelect" class="form-select" style="width: 100%"></select>
                                </div>

                                <div class="row g-3" id="pindahOrderContainer">
                                    <!-- Isi kartu akan di‑inject via JS -->
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label for="">Total Kgs</label>
                                        <input type="text" class="form-control me-2" name="ttl_kgs" readonly placeholder="0">
                                    </div>
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label for="">Total Cones</label>
                                        <input type="text" class="form-control mx-2" name="ttl_cns" readonly placeholder="0">
                                    </div>
                                    <div class="col-md-4 mb-3 mt-2">
                                        <label for="">Total Karung</label>
                                        <input type="text" class="form-control ms-2" name="ttl_krg" readonly placeholder="0">
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
    <!-- modal pindah order end -->
    <!-- modal Pindah Cluster -->
    <div class="modal fade" id="modalPindahCluster" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title text-white" id="modalPindahClusterLabel">Pindah Cluster</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPindahCluster">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-body">
                                <div class="row g-3" id="PindahClusterContainer">
                                    <!-- Isi kartu akan di‑inject via JS -->
                                </div>
                                <div class="mb-3 d-flex justify-content-between">
                                    <input type="text" class="form-control me-2" name="ttl_kgs_pindah" readonly placeholder="Total Kgs">
                                    <input type="text" class="form-control mx-2" name="ttl_cns_pindah" readonly placeholder="Total Cns">
                                    <input type="text" class="form-control ms-2" name="ttl_krg_pindah" readonly placeholder="Total Krg">
                                </div>
                                <!-- SELECT2 FILTER -->
                                <div class="mb-3 row">
                                    <!-- Kolom Pilih Cluster -->
                                    <div class="col-md-8">
                                        <label for="ClusterSelect" class="form-label">Pilih Cluster</label>
                                        <select id="ClusterSelect" class="form-select" style="width: 100%" required></select>
                                    </div>
                                    <!-- Kolom Sisa Kapasitas -->
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
    <div class="modal fade" id="pengeluaranSelainOrder" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title text-white" id="modalPengeluaranSelainOrderLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formpengeluaranSelainOrder">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-body">
                                <!-- Select Kategori -->
                                <div class="mb-3">
                                    <input type="text" name="nama_cluster" id="inputNamaCluster" hidden>
                                    <input type="text" id="id_stock" hidden>
                                    <label for="kategoriSelect" class="form-label">Pilih Kategori</label>
                                    <select id="kategoriSelect" class="form-select" style="width: 100%">
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

                                <!-- Container Data -->
                                <div class="row g-3" id="pengeluaranSelainOrderContainer">
                                    <!-- Data akan di-inject JS -->
                                </div>

                                <!-- Display Total dari Checkbox -->
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="ttl_kgs" readonly placeholder="Total Kgs Terpilih" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="ttl_cns" readonly placeholder="Total Cns Terpilih" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="ttl_krg" readonly placeholder="Total Krg Terpilih" disabled>
                                    </div>
                                </div>

                                <!-- Input Total -->
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <label for="inputKgs" class="form-label">Total Kgs</label>
                                        <input type="number" step="0.01" class="form-control" id="inputKgs" name="input_kgs" placeholder="Masukkan Kgs" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inputCns" class="form-label">Total Cns</label>
                                        <input type="number" class="form-control" id="inputCns" name="input_cns" placeholder="Masukkan Cns" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="inputKrg" class="form-label">Total Krg</label>
                                        <input type="number" class="form-control" id="inputKrg" name="input_krg" placeholder="Masukkan Krg" required>
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
    <!-- modal Pengeluaran Selain Order end -->
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    let currentNoModelOld = '';
    let currentKodeWarna = '';

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
                                        <p><strong>Total Kgs:</strong> ${item.kgs_stock_awal && item.kgs_stock_awal > 0 ? item.kgs_stock_awal : item.kgs_in_out} KG | ${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out} Cones | ${item.krg_stock_awal && item.krg_stock_awal > 0 ? item.krg_stock_awal : item.krg_in_out} KRG</p>
                                    </div>
                                    <div class="col-md-4 d-flex flex-column gap-2">
                                        <button class="btn btn-outline-info btn-sm PindahCluster" 
                                            data-id="${item.id_stock}"
                                            data-nama-cluster-old="${item.nama_cluster}"
                                            >
                                        Pindah Cluster
                                        </button>
                                        <button 
                                            class="btn btn-outline-info btn-sm pindahOrder"
                                            data-id="${item.id_stock}"
                                            data-no-model-old="${item.no_model}"
                                            data-kode-warna="${item.kode_warna}"
                                            >
                                            Pindah Order
                                        </button>
                                        <button 
                                            class="btn btn-outline-info btn-sm pengeluaranSelainOrder"
                                            data-id="${item.id_stock}"
                                            data-no-model="${item.no_model}"
                                            data-kode-warna="${item.kode_warna}"
                                            data-nama-cluster="${item.nama_cluster}"
                                            >
                                            Pengeluaran Selain Order
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

        // Inisialisasi Select2 (jalankan sekali saja, misalnya di document.ready)
        $('#ModelSelect').select2({
            placeholder: 'Ketik untuk cari Model Tujuan',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalPindahOrder'),

            // Mulai cari setelah minimal 1 karakter
            minimumInputLength: 1,

            ajax: {
                url: '<?= base_url() ?>/<?= session()->get('role') ?>/warehouse/getNoModel',
                dataType: 'json',
                delay: 250, // debounce 250ms
                data: function(params) {
                    // kirim term pencarian + parameter tambahan
                    return {
                        term: params.term, // kata yang diketikan user
                        noModelOld: currentNoModelOld,
                        kodeWarna: currentKodeWarna
                    };
                },
                processResults: function(res) {
                    // map server response ke format Select2
                    if (!res.success) {
                        return {
                            results: []
                        };
                    }
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
    });

    // modal pindah order
    // ketika tombol “Pindah Order” diklik
    $(document).on('click', '.pindahOrder', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        currentNoModelOld = $(this).data('no-model-old');
        currentKodeWarna = $(this).data('kode-warna');

        $('#ModelSelect')
            .val(null) // kosongkan pilihan
            .trigger('change');
        $('#modalPindahOrder').modal('show');
        // const $select = $('#ModelSelect').prop('disabled', true).empty().append('<option>Loading…</option>');
        const $container = $('#pindahOrderContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');



        // Fetch model tujuan
        // $.getJSON(`${base}/${role}/warehouse/getNoModel`, {
        //     noModelOld,
        //     kodeWarna
        // }, res => {
        //     $select.empty();
        //     if (res.success && res.data.length) {
        //         $select.append('<option></option>');
        //         res.data.forEach(d => {
        //             $select.append(`<option value="${d.no_model}|${d.item_type}|${d.kode_warna}|${d.color}">${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.color}</option>`);
        //         });
        //     } else {
        //         $select.append('<option>Tidak ada model</option>');
        //     }
        //     $select.prop('disabled', false).select2({
        //         placeholder: 'Pilih Model Tujuan',
        //         allowClear: true,
        //         width: '100%',
        //         dropdownParent: $('#modalPindahOrder')
        //     });
        // });

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
                            <div class="card-body">
                                <div class="row">
                                    <!-- Kiri: detail label -->
                                    <div class="col-md-6">
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
                                                <strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim || 0).toFixed(2)} KG<br>
                                                <strong>Cones:</strong> ${d.cones_kirim} Cns
                                            </label>
                                            <input type="hidden" name="id_stock[]" value="${d.id_stock}">
                                        </div>
                                    </div>

                                    <!-- Kanan: input manual -->
                                    <div class="col-md-6">
                                        <h5><strong>Pindah Perkones</strong></h5>
                                        <div class="row gx-2">
                                            <div class="col-6">
                                                <label for="kgs_out_${d.id_out_celup}" class="form-label small mb-1">Kg Out Manual</label>
                                                <input type="number"
                                                    step="0.01"
                                                    class="form-control form-control-sm"
                                                    name="kgs_out[${d.id_out_celup}]"
                                                    id="kgs_out_${d.id_out_celup}"
                                                    placeholder="Kg"
                                                    disabled>
                                            </div>
                                            <div class="col-6">
                                                <label for="cns_out_${d.id_out_celup}" class="form-label small mb-1">Cones Out Manual</label>
                                                <input type="number"
                                                    step="1"
                                                    class="form-control form-control-sm"
                                                    name="cns_out[${d.id_out_celup}]"
                                                    id="cns_out_${d.id_out_celup}"
                                                    placeholder="CNS"
                                                    disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            });

            //  Event ketika checkbox diubah
            $container.on('change', '.row-check', function() {
                let totalKgs = 0,
                    totalCns = 0,
                    totalKrg = 0;

                $container.find('.row-check:checked, .row-check:not(:checked)').each(function() {
                    const id = $(this).val();
                    const isChecked = $(this).is(':checked');

                    // Aktifkan/Nonaktifkan input manual
                    $(`#kgs_out_${id}, #cns_out_${id}, #keterangan_${id}`).prop('disabled', !isChecked);
                });

                // Loop setiap baris yang dipilih
                $container.find('.row-check:checked').each(function() {
                    const id = $(this).val();
                    const selectedData = res.data.find(d => d.id_out_celup == id);

                    if (selectedData) {
                        // Ambil nilai dari input manual
                        const rawManualKgs = $(`#kgs_out_${id}`).val().replace(',', '.').trim();
                        const rawManualCns = $(`#cns_out_${id}`).val().trim();

                        const useManualKgs = rawManualKgs !== '';
                        // const useManualCns = rawManualCns !== '';

                        const manualKgs = parseFloat(rawManualKgs) || 0;
                        const manualCns = parseInt(rawManualCns) || 0;

                        // Pakai input manual jika diisi, kalau tidak pakai data asli
                        totalKgs += useManualKgs ? manualKgs : parseFloat(selectedData.kgs_kirim || 0);
                        totalCns += useManualKgs ? manualCns : parseInt(selectedData.cones_kirim || 0);
                        totalKrg += useManualKgs ? 0 : 1;
                    }
                });

                // Tampilkan total ke input
                $('input[name="ttl_kgs"]').val(totalKgs.toFixed(2));
                $('input[name="ttl_cns"]').val(totalCns);
                $('input[name="ttl_krg"]').val(totalKrg);

                // Dropdown cluster
                const selectedClusterValue = $select.val();

                if (totalKgs > 0) {
                    fetchClusters(totalKgs, selectedClusterValue);
                    $select.prop('disabled', false);
                } else {
                    $select.prop('disabled', true).empty();
                    $('#SisaKapasitas').val('');
                }
            });

            // Event ketika input manual diubah
            $container.on('input', 'input[name^="kgs_out"], input[name^="cns_out"]', function() {
                $container.find('.row-check:checked').trigger('change');
            });

        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });
    });

    // Reset total fields when modal is closed
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

        $.post(`${base}/${role}/warehouse/savePindahOrder`, {
            no_model_tujuan: model,
            idOutCelup: orders,
            id_stock: stock
        }, res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    text: `Berhasil memindahkan ${orders.length} order.`,
                    confirmButtonText: 'OK',
                    willClose: () => {
                        // Reload halaman setelah modal ditutup
                        location.reload();
                    }
                }).then(() => {
                    $('#modalPindahOrder').modal('hide');
                    $('#filter_data').click(); // Reload data filter
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: res.message || 'Terjadi kesalahan saat memindahkan order.',
                    confirmButtonText: 'OK',
                    willClose: () => {
                        // Reload halaman setelah modal ditutup
                        location.reload();
                    }
                });
            }
        }, 'json').fail((_, __, err) => {
            Swal.fire({
                icon: 'error',
                text: `Error: ${err}`
            });
        });
    });

    // modal Pindah Cluster
    // ketika tombol “Pindah Cluster diklik
    $(document).on('click', '.PindahCluster', function() {
        const idStock = $(this).data('id');
        const base = '<?= base_url() ?>';
        const role = '<?= session()->get('role') ?>';
        const namaCluster = $(this).data('nama-cluster-old');

        $('#modalPindahCluster').modal('show');
        // Perbarui judul modal dengan nama cluster
        $('#modalPindahClusterLabel').text(`Pindah Cluster - ${namaCluster}`);

        const $select = $('#ClusterSelect').prop('disabled', true).empty().append('<option>Loading…</option>');
        const $container = $('#PindahClusterContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

        // Fetch detail palet
        $.post(`${base}/${role}/warehouse/getPindahCluster`, {
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
                                    data-cluster-old="${d.nama_cluster}"
                                    data-kgs="${parseFloat(d.kgs_kirim||0).toFixed(2)}"
                                    data-cns="${d.cones_kirim}"
                                    data-krg="1"
                                    data-no_model="${d.no_model}"
                                    data-item_type="${d.item_type}"
                                    data-kode_warna="${d.kode_warna}"
                                    data-warna="${d.warna}"
                                    data-lot="${lot}"
                                    data-id-stock="${d.id_stock}"
                                    id="chk${d.id_out_celup}">
                                <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                                    ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                                </label>
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

            $container.on('change', '.row-check', function() {
                let totalKgs = 0,
                    totalCns = 0,
                    totalKrg = 0;
                let totalSelectedKgs = 0;

                // Hitung total Kgs, Cns, dan Krg untuk yang dipilih
                $container.find('.row-check:checked').each(function() {
                    totalKgs += parseFloat($(this).data('kgs'));
                    totalCns += parseInt($(this).data('cns'), 10);
                    totalKrg += parseInt($(this).data('krg'), 10);
                });

                // Perbarui nilai total Kgs, Cns, dan Krg di input
                $('input[name="ttl_kgs_pindah"]').val(totalKgs.toFixed(2));
                $('input[name="ttl_cns_pindah"]').val(totalCns);
                $('input[name="ttl_krg_pindah"]').val(totalKrg);

                // Simpan cluster yang saat ini dipilih
                const selectedClusterValue = $select.val();

                // Aktifkan atau nonaktifkan dropdown berdasarkan total
                if (totalKgs > 0) {
                    fetchClusters(totalKgs, selectedClusterValue); // Ambil cluster sesuai totalKgs
                    $select.prop('disabled', false);
                } else {
                    $select.prop('disabled', true).empty();
                    $('#SisaKapasitas').val('');
                }
            });
        }).fail((_, __, err) => {
            $container.html(`<div class="alert alert-danger text-center">Error: ${err}</div>`);
        });

        // Fungsi untuk mengambil cluster berdasarkan totalKgs
        function fetchClusters(totalKgs, previousCluster) {
            console.log("Fetching clusters with parameters:", {
                namaCluster,
                totalKgs,
            });
            $.getJSON(`${base}/${role}/warehouse/getNamaCluster`, {
                namaCluster,
                totalKgs,
            }, res => {
                $select.empty();
                if (res.success && res.data.length) {
                    $select.append('<option value="" data-sisa-kapasitas="">Pilih Cluster</option>');
                    res.data.forEach(d => {
                        $select.append(`<option value="${d.nama_cluster}" data-sisa-kapasitas="${d.sisa_kapasitas}">${d.nama_cluster}</option>`);
                    });

                    // Pilih kembali cluster sebelumnya jika masih ada dalam opsi
                    if (previousCluster && $select.find(`option[value="${previousCluster}"]`).length) {
                        $select.val(previousCluster).trigger('change');
                    } else {
                        $('#SisaKapasitas').val(''); // Kosongkan kapasitas jika cluster sebelumnya tidak tersedia
                    }

                    // Update Sisa Kapasitas berdasarkan pilihan dropdown
                    $select.off('change').on('change', function() {
                        const selectedOption = $select.find('option:selected');
                        const sisaKapasitas = selectedOption.data('sisa-kapasitas');
                        $('#SisaKapasitas').val(selectedOption.val() ? parseFloat(sisaKapasitas || 0).toFixed(2) : '');
                    });
                } else {
                    $select.append('<option>Tidak Ada Cluster</option>');
                    $('#SisaKapasitas').val(''); // Kosongkan jika tidak ada cluster
                }
            });
        }
    });

    // Reset total fields when modal is closed
    $('#modalPindahCluster').on('hidden.bs.modal', function() {
        $('input[name="ttl_kgs_pindah"]').val('');
        $('input[name="ttl_cns_pindah"]').val('');
        $('input[name="ttl_krg_pindah"]').val('');
        $('#SisaKapasitas').val('');
    });
    // simpan data Pindah Cluster
    $('#formPindahCluster').on('submit', function(e) {
        e.preventDefault();

        const role = '<?= session()->get("role") ?>';
        const base = '<?= base_url() ?>';
        const cluster = $('#ClusterSelect').val();

        // Ambil semua checkbox ter-centang
        const $checked = $("input[name='pindah[]']:checked");

        // Jika tidak ada yang dipilih, abort
        if (!$checked.length) {
            return Swal.fire({
                icon: 'warning',
                text: 'Pilih setidaknya satu karung untuk dipindah!'
            });
        }

        // Jika tidak ada yang dipilih, abort
        if (!cluster) {
            return Swal.fire({
                icon: 'warning',
                text: 'Pilih cluster terlebih dahulu!'
            });
        }

        // Bangun array detail lengkap
        const detail = $checked.map((_, chk) => {
            const $chk = $(chk);
            return {
                id_out_celup: $chk.val(),
                cluster_old: $chk.data('cluster-old'),
                id_stock: $chk.data('id-stock'),
                no_model: $chk.data('no_model'),
                item_type: $chk.data('item_type'),
                kode_warna: $chk.data('kode_warna'),
                warna: $chk.data('warna'),
                lot: $chk.data('lot'),
                kgs: $chk.data('kgs'),
                cns: $chk.data('cns'),
                krg: $chk.data('krg')
            };
        }).get();

        // Sekarang kirim ke server
        $.post(`${base}/${role}/warehouse/savePindahCluster`, {
                cluster_tujuan: cluster,
                detail: detail
            }, res => {
                // Menampilkan SweetAlert2 saat respons berhasil
                Swal.fire({
                    title: 'Berhasil!',
                    text: res.message || 'Pindah cluster berhasil',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    willClose: () => {
                        // Reload halaman setelah modal ditutup
                        location.reload();
                    }
                });
            }, 'json')
            .fail(xhr => {
                // Menampilkan SweetAlert2 saat terjadi error
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: xhr.responseText || 'Ada masalah dengan permintaan Anda.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    willClose: () => {
                        // Reload halaman setelah modal ditutup
                        location.reload();
                    }
                });
            });
    });

    // modal Pengeluaran Selain Order
    $(document).ready(function() {
        let selectedData = [];

        $(document).on('click', '.pengeluaranSelainOrder', function() {
            const idStock = $(this).data('id');
            const base = '<?= base_url() ?>';
            const role = '<?= session()->get('role') ?>';
            const namaCluster = $(this).data('nama-cluster');
            const $container = $('#pengeluaranSelainOrderContainer').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>');

            $('#pengeluaranSelainOrder').modal('show');

            // Perbarui judul modal dengan nama cluster
            $('#modalPengeluaranSelainOrderLabel').text(`Pengeluaran Selain Order - ${namaCluster}`);

            $.post(`${base}/${role}/warehouse/getPindahOrder`, {
                id_stock: idStock
            }, res => {
                $container.empty();
                selectedData = res.data || [];

                if (!res.success || !selectedData.length) {
                    return $container.html('<div class="alert alert-warning text-center">Data tidak ditemukan</div>');
                }

                selectedData.forEach(d => {
                    const lot = d.lot_stock || d.lot_awal;
                    $container.append(`
                    <div class="col-md-12">
                        <div class="card result-card h-100">
                        <div class="form-check">
                            <input class="form-check-input row-check" type="radio" name="pilih_item" value="${d.id_out_celup}" id="radio${d.id_out_celup}" data-lot="${lot}">
                            <label class="form-check-label fw-bold" for="chk${d.id_out_celup}">
                            ${d.no_model} | ${d.item_type} | ${d.kode_warna} | ${d.warna}
                            </label>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-4">
                            <p><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                            <p><strong>Warna:</strong> ${d.warna}</p>
                            </div>
                            <div class="col-md-4">
                            <p><strong>Lot Jalur:</strong> ${lot}</p>
                            <p><strong>No Karung:</strong> ${d.no_karung}</p>
                            </div>
                            <div class="col-md-4">
                            <p><strong>Total Kgs:</strong> ${parseFloat(d.kgs_kirim || 0).toFixed(2)} KG</p>
                            <p><strong>Cones:</strong> ${d.cones_kirim} Cns</p>
                            </div>
                        </div>
                        </div>
                    </div>
                    `);
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

        // Validasi Input Manual
        $('#inputKgs, #inputCns, #inputKrg').on('input', function() {
            const maxKgs = parseFloat($('input[name="ttl_kgs"]').val()) || 0;
            const maxCns = parseInt($('input[name="ttl_cns"]').val()) || 0;
            const maxKrg = parseInt($('input[name="ttl_krg"]').val()) || 0;

            const inputKgs = parseFloat($('#inputKgs').val()) || 0;
            const inputCns = parseInt($('#inputCns').val()) || 0;
            const inputKrg = parseInt($('#inputKrg').val()) || 0;

            if (inputKgs > maxKgs) {
                alert(`Total Kgs tidak boleh melebihi ${maxKgs}`);
                $('#inputKgs').val(maxKgs);
            }
            if (inputCns > maxCns) {
                alert(`Total Cns tidak boleh melebihi ${maxCns}`);
                $('#inputCns').val(maxCns);
            }
            if (inputKrg > maxKrg) {
                alert(`Total Krg tidak boleh melebihi ${maxKrg}`);
                $('#inputKrg').val(maxKrg);
            }
        });
    });
    // Simpan data dari modal Pengeluaran Selain Order
    $('#formpengeluaranSelainOrder').on('submit', function(e) {
        e.preventDefault(); // penting agar tidak reload halaman

        const idOutCelup = $('input[name="pilih_item"]:checked').val();
        const kategori = $('#kategoriSelect').val();
        const kgsOtherOut = $('#inputKgs').val();
        const cnsOtherOut = $('#inputCns').val();
        const krgOtherOut = $('#inputKrg').val();
        const namaCluster = $('#inputNamaCluster').val();
        const lot = $('input[name="pilih_item"]:checked').data('lot');
        const idStock = $('#id_stock').val(); // atau sesuaikan jika beda

        if (!idOutCelup || !kategori) {
            return alert('Silakan pilih item dan kategori terlebih dahulu.');
        }

        $.ajax({
            url: '<?= base_url(session()->get("role") . "/warehouse/savePengeluaranSelainOrder") ?>',
            method: 'POST',
            data: {
                id_out_celup: idOutCelup,
                kategori: kategori,
                kgs_other_out: kgsOtherOut,
                cns_other_out: cnsOtherOut,
                krg_other_out: krgOtherOut,
                lot: lot,
                nama_cluster: namaCluster,
                id_stock: idStock // sesuaikan dengan controller kamu yang menerima array
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message || 'Data berhasil disimpan!',
                        icon: 'success',
                        confirmButtonColor: '#4a90e2',
                        willClose: () => {
                            // Menutup modal dan reset form jika diperlukan
                            $('#pengeluaranSelainOrder').modal('hide');
                            $('#formpengeluaranSelainOrder')[0].reset();
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
            error: function(xhr, status, error) {
                console.error(error);
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: 'Ada masalah dengan server.',
                    icon: 'error',
                    confirmButtonColor: '#e74c3c'
                });
            }
        });
    });
</script>
<?php $this->endSection(); ?>