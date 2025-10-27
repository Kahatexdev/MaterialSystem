<?php $this->extend($role . '/poplus/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
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
                html: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>
<style>
    .loading-spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        /* semi-transparent */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        /* biar di atas semuanya */
    }

    .loading-spinner-content img {
        max-width: 200px;
        margin-top: 10px;
    }

    .kebutuhan-item {
        position: relative;
        /* biar spinner-nya bisa absolute di dalam */
    }
</style>
<div class="loading-spinner-overlay d-none" id="loading-spinner">
    <div class="loading-spinner-content text-center">
        <h4>loading...</h4>
        <img src="<?= base_url('assets/newspin.gif') ?>" alt="Loading...">
    </div>
</div>
<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Tambahan Area</h5>
                <a href="<?= base_url($role . '/poplus') ?>" class="btn bg-gradient-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/savePoTambahan') ?>" method="post">

                <div id="kebutuhan-container">
                    <label>Pilih Bahan Baku</label>
                    <div class="kebutuhan-item">
                        <div class=" row">
                            <div class="col-md-12">
                                <!-- Area -->
                                <div class="form-group">
                                    <label>Area</label>
                                    <select class="form-control select-area" name="area[0][area]" required>
                                        <option value="">Pilih Area</option>
                                        <?php foreach ($areas as $area): ?>
                                            <option value="<?= $area ?>"><?= $area ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="col-md-6">
                                <!-- No Model -->
                                <div class="form-group">
                                    <label>No Model</label>
                                    <select class="form-control select-no-model" name="no_model[0][no_model]" required>
                                        <option value="">Pilih No Model</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Item Type -->
                                <div class="form-group">
                                    <label>Item Type</label>
                                    <select class="form-control item-type" name="items[0][item_type]" required>
                                        <option value="">Pilih Item Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class=" row">

                            <div class="col-md-6">
                                <!-- Kode Warna -->
                                <div class="form-group">
                                    <label>Kode Warna</label>
                                    <select class="form-control kode-warna" name="items[0][kode_warna]" required>
                                        <option value="">Pilih Kode Warna</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Color -->
                                <div class="form-group">
                                    <div class="col"><label>Color</label>
                                        <input type="text" class="form-control color" name="items[0][color]" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- PO Kg -->
                                <div class="form-group">
                                    <div class="col"><label>PO (Kg)</label>
                                        <input type="text" class="form-control po-kg" name="items[0][po_kg]" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Terima -->
                                <div class="form-group">
                                    <div class="col"><label>Terima (Kg)</label>
                                        <input type="text" class="form-control terima" name="items[0][terima]" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Sisa Jatah (Kg)     -->
                                <div class="form-group">
                                    <div class="col"><label>Sisa Jatah (Kg)</label>
                                        <input type="text" class="form-control sisa-jatah" name="items[0][sisa_jatah]" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Sisa BB di Mesin (Kg) -->
                                <div class="form-group">
                                    <label>Sisa BB di Mesin (Kg)</label>
                                    <input type="number" class="form-control sisa-mc-kg" name="items[0][sisa_mc_kg]" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- (+) Mesin (Cns) -->
                                <div class="form-group">
                                    <label>(+) Mesin (Cns)</label>
                                    <input type="number" class="form-control poplus-mc-cns" name="items[0][poplus_mc_cns]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- (+) Packing (Cns) -->
                                <div class="form-group">
                                    <label>(+) Packing (Cns)</label>
                                    <input type="number" class="form-control plus-pck-cns" name="items[0][plus_pck_cns]" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Loss Aktual -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Loss Aktual</label>
                                    <input type="number" class="form-control loss-aktual" name="items[0][loss_aktual]" readonly required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Loss Tambahan -->
                                <div class="form-group">
                                    <label>Loss Tambahan</label>
                                    <input type="number" class="form-control loss-tambahan" name="items[0][loss_tambahan]">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Delivery Po(+) -->
                                <div class="form-group">
                                    <label>Delivery Po(+)</label>
                                    <input type="date" class="form-control delivery-po-plus" name="items[0][delivery_po_plus]" id="delivery-po-plus" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Keterangan -->
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" id="keterangan">
                                </div>
                            </div>
                            <h6 class="text-danger">* Harap lengkapi dulu inputan sebelumnya sebelum mengubah style size !! </h6>
                        </div>
                        <!-- Container untuk style size  -->
                        <div class="row populate-size-wrapper">
                        </div>
                        <div class="form-group">
                            <div class="col"><label>Total Tambahan Kg</label>
                                <input type="number" class="form-control total-kg" name="items[0][total_kg_po]" readonly required>
                            </div>
                        </div>
                        <div class="form-group">
                            <!-- Total Tambahan Cones -->
                            <div class="col"><label>Total Tambahan Cones</label>
                                <input type="number" class="form-control total-cns" name="items[0][total_cns_po]" readonly required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-info w-100" id="btn-save">Save</button>
                <div class="d-none" id="populateSizeTemplate">
                    <div class="size-block mb-3 p-1 border rounded shadow-sm bg-white">
                        <!-- Judul Style Size -->
                        <div class="row">
                            <div class="col-12 text-center">
                                <hr class="mt-1 mb-2">
                                <h5 class="text-dark fw-bold label-style-size text-uppercase"></h5>
                                <hr class="mb-3">
                                <input type="hidden" class="form-control style-size-hidden" name="items[0][style_size]">
                                <input type="hidden" class="form-control color" name="items[0][color]">
                            </div>
                        </div>
                        <hr class="mb-3" style="border-color: #6c757d;">

                        <!-- Form Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Composition(%)</label>
                                    <input type="text" class="form-control composition-hidden" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Gw Aktual</label>
                                    <input type="text" class="form-control gw-hidden" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- Form Fields -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Pesanan<br>Kgs</label>
                                    <!-- <input type="text" class="form-control kg-mu" readonly> -->
                                    <input type="text" class="form-control po-kg-perstyle" name="items[0][po_kg_perstyle]" readonly>
                                    <input type="hidden" class="form-control po-kg-perstyle-tanpa-loss" name="items[0][po_kg_perstyle_tanpa_loss]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="hidden" class="form-control qty-order" name="items[0][qty_order]" readonly>
                                    <label>Sisa<br>Order</label>
                                    <input type="text" class="form-control sisa-order" name="items[0][sisa_order]">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BS Mesin<br>(Kg)</label>
                                    <input type="text" class="form-control bs-mesin" name="items[0][bs_mesin]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>BS<br>Setting</label>
                                    <input type="text" class="form-control bs-setting" name="items[0][bs_setting]" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Mesin<br>(Kg)</label>
                                    <input type="text" class="form-control poplus-mc-kg" name="items[0][poplus_mc_kg]" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Pcs<br>Packing</label>
                                    <input type="number" class="form-control plus-pck-pcs">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>(+) Kg<br>Packing</label>
                                    <input type="text" class="form-control plus-pck-kg" name="items[0][plus_pck_kg]" readonly required>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label>Lebih<br>Pakai(Kg)</label>
                                    <input type="text" class="form-control lebih-pakai" readonly>
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex align-items-end">
                                <button class="btn btn-danger remove-size-row w-100"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Pastikan jQuery load pertama -->
<!-- Tambahkan ini di layout HTML-mu -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        const base = '<?= base_url() ?>';
        const role = '<?= $role ?>';
        const materialDataCache = {};

        const noModelOptions = $('.select-no-model').first().html();

        // Inisialisasi Select2 pada konteks tertentu
        function initSelect2(ctx) {
            $(ctx).find('.select-area, .select-no-model, .select-style-size, .item-type, .kode-warna')
                .select2({
                    width: '100%',
                    allowClear: true
                });
        }

        // Tambah tab baru

        initSelect2(document);

        $('.select-area').on('change', function() {
            const area = $(this).val();
            const $container = $(this).closest('.form-group').parent().parent().parent();
            const $noModelSelect = $container.find('.select-no-model');

            $noModelSelect.empty().append('<option value="">Loading...</option>');

            if (!area) {
                $noModelSelect.html('<option value="">Pilih No Model</option>');
                return;
            }

            $.ajax({
                url: '<?= base_url() ?>' + role + '/poplus/getNoModelByArea',
                method: 'GET',
                data: {
                    area: area
                },
                dataType: 'json',
                success: function(data) {
                    $noModelSelect.empty().append('<option value="">Pilih No Model</option>');

                    if (Array.isArray(data)) {
                        data.forEach(function(item) {
                            if (item.mastermodel) {
                                $noModelSelect.append('<option value="' + item.mastermodel + '">' + item.mastermodel + '</option>');
                            }
                        });
                    }

                    $noModelSelect.trigger('change.select2'); // kalau kamu pakai select2
                },
                error: function() {
                    $noModelSelect.html('<option value="">Gagal memuat data</option>');
                }
            });
        });

        // Handler saat No Model dipilih
        $(document).on('change', '.select-no-model', function() {
            const $row = $(this).closest('.kebutuhan-item');
            const modelCode = $(this).val();
            const area = $row.find('.select-area').val();
            console.log('No Model', modelCode);
            console.log('Area', area);
            let loading = document.getElementById('loading-spinner');
            const $ss = $row.find('.item-type').empty().append('<option value="">Pilih Kode Benang</option>').trigger('change');
            $row.find('.item-type, .kode-warna').empty().append('<option value="">Pilih Item Type</option>').trigger('change');
            $row.find('.color, .po-kg-perstyle, .po-kg-perstyle-tanpa-loss, .kg-po, .pcs-po').val('');

            if (!modelCode) return;

            loading.classList.remove('d-none');
            fetch(`${base}${role}/poTambahanDetail/${modelCode}/${area}`)
                .then(r => r.ok ? r.json() : Promise.reject(r.statusText))
                .then(json => {
                    const itemTypes = json.item_types;
                    const materialData = json.material;

                    itemTypes.forEach(row => {
                        $ss.append(`<option value="${row.item_type}">${row.item_type}</option>`);
                    });

                    $row.data('material', materialData);
                    $row.data('qtyOrder', json.qty_order);
                    $row.data('sisaOrder', json.sisa_order);
                    $row.data('bsMesin', json.bs_mesin);
                    $row.data('bsSetting', json.bs_setting);
                    $row.data('bruto', json.bruto);
                    $row.data('plusPck', json.plusPck);
                    $ss.trigger('change');
                })
                .catch(err => console.error('Gagal load item_type:', err))
                .finally(() => {
                    // Sembunyikan loading spinner
                    loading.classList.add('d-none')
                });
        });
        // Event listener saat Item Type dipilih
        $(document).on('change', '.item-type', function() {
            const $row = $(this).closest('.kebutuhan-item');
            // const modelCode = $row.find('.select-no-model option:selected').data('no-model');
            const modelCode = $row.find('.select-no-model').val(); // <-- Ganti ini

            if (!modelCode) return;

            const materialData = $row.data('material'); // ambil dari data-attribute
            if (!materialData) return;

            populateKodeWarnas(materialData, modelCode, $row);
        });


        // Isi dropdown Kode Warna berdasarkan data item type
        function populateKodeWarnas(matData, modelCode, $row) {
            const selectedItemType = $row.find('.item-type').val();
            const $it = $row.find('.kode-warna').empty().append('<option value="">Pilih Kode Warna</option>');

            if (!selectedItemType || !matData[selectedItemType]) return;

            const kodeWarnas = matData[selectedItemType].kode_warna;

            Object.entries(kodeWarnas).forEach(([kode, detail]) => {
                const styleSizes = detail.style_size || [];

                const cocok = styleSizes.some(item => item.no_model === modelCode);
                if (cocok) {
                    $it.append(`<option value="${kode}" data-color="${detail.color}" data-terima="${detail.kgs_out}">${kode}</option>`);
                }
            });

            $it.trigger('change');
        }

        // Handler gabungan saat Kode Warna dipilih
        $(document).on('change', '.kode-warna', function() {
            const $opt = $(this).find(':selected');
            const $row = $(this).closest('.kebutuhan-item');

            const color = $opt.data('color') || '';
            $row.find('.color').val(color);

            const terima = parseFloat($opt.data('terima')) || 0;
            $row.find('.terima').val(terima.toFixed(2));

            const itemType = $row.find('.item-type').val();
            const kodeWarna = $(this).val();
            // const modelCode = $row.find('.select-no-model option:selected').data('no-model');
            const modelCode = $row.find('.select-no-model').val();

            const materialData = $row.data('material');
            const qtyOrderMap = $row.data('qtyOrder') || {};
            const sisaOrderMap = $row.data('sisaOrder') || {};
            const bsMesinMap = $row.data('bsMesin') || {};
            const bsSettingMap = $row.data('bsSetting') || {};
            const brutoMap = $row.data('bruto') || {};
            const plusPckMap = $row.data('plusPck') || {};

            if (!materialData || !materialData[itemType] || !materialData[itemType].kode_warna[kodeWarna]) {
                return;
            }

            const detail = materialData[itemType].kode_warna[kodeWarna];
            const allStyleSizes = (detail.style_size || []).filter(s => s.no_model === modelCode);

            // === 1) GROUP BY style_size ===
            const groupedSizes = {};

            allStyleSizes.forEach(style => {
                const size = style.style_size;

                if (!groupedSizes[size]) {
                    groupedSizes[size] = {
                        ...style
                    };
                    groupedSizes[size].composition = parseFloat(style.composition || 0);
                    groupedSizes[size].gw = parseFloat(style.gw || 0);
                    groupedSizes[size].gw_aktual = parseFloat(style.gw_aktual || 0);
                    groupedSizes[size].loss = parseFloat(style.loss || 0);
                } else {
                    groupedSizes[size].composition += parseFloat(style.composition || 0);
                    groupedSizes[size].gw = parseFloat(style.gw || 0);
                    groupedSizes[size].gw_aktual = parseFloat(style.gw_aktual || 0);
                    groupedSizes[size].loss = parseFloat(style.loss || 0);
                }
            });

            const mergedSizes = Object.values(groupedSizes);

            // === 2) HAPUS ISI LAMA & LOOPING HASIL GABUNG ===
            const $wrapper = $row.find('.populate-size-wrapper').empty();

            mergedSizes.forEach((style, i) => {
                const $template = $('#populateSizeTemplate').clone().removeAttr('id').removeClass('d-none');

                const size = style.style_size;
                const composition = parseFloat(style.composition || 0);
                const gw = parseFloat(style.gw || 0);
                const gwAktual = parseFloat(style.gw_aktual || 0);
                const gwFinal = gwAktual > 0 ? gwAktual : gw;

                // set hidden values AWAL (important: gwFinal sudah terdefinisi)
                $template.find('.composition-hidden').val(composition.toFixed(2));
                $template.find('.gw-hidden').val(gwFinal);

                $template.find('.color').val(size || '');

                // === Ambil nilai per map sesuai style size ===
                const qtyOrderVal = (parseFloat(qtyOrderMap[size]) || 0);
                const sisaOrderVal = (parseFloat(sisaOrderMap[size]) || 0) - (parseFloat(plusPckMap[size]) || 0);
                const bsMesinVal = (parseFloat(bsMesinMap[size]) || 0);
                const bsSettingVal = (parseFloat(bsSettingMap[size]) || 0);
                const plusPckVal = (parseFloat(plusPckMap[size]) || 0);

                // === Tampilkan nilai ke field ===
                $template.find('.qty-order').val(qtyOrderVal);
                $template.find('.sisa-order').val(sisaOrderVal);
                $template.find('.bs-mesin').val((bsMesinVal / 1000).toFixed(2));
                $template.find('.bs-setting').val(bsSettingVal);
                $template.find('.plus-pck-pcs').val(plusPckVal);

                // === Hitung Qty PO KG & Tanpa Loss ===
                const qtyPoKg = gw > 0 ?
                    qtyOrderVal * composition * gw / 100 / 1000 * (1 + (parseFloat(style.loss || 0) / 100)) :
                    0;
                const poKgTanpaLoss = gw > 0 ?
                    qtyOrderVal * composition * gw / 100 / 1000 :
                    0;

                $template.find('.po-kg-perstyle').val(qtyPoKg.toFixed(2));
                $template.find('.po-kg-perstyle-tanpa-loss').val(poKgTanpaLoss.toFixed(2));

                // === Simpan base sisa order untuk PO+ ===
                let baseSisaOrderKg = 0;
                if (gwFinal > 0 && sisaOrderVal > 0) {
                    baseSisaOrderKg = (sisaOrderVal * composition * gwFinal) / 100 / 1000;
                }
                $template.find('.poplus-mc-kg').data("baseSisaOrderKg", baseSisaOrderKg);

                // === Simpan base untuk plus-pck ===
                const basePlusPckKg = gwFinal > 0 ?
                    plusPckVal * composition * gwFinal / 100 / 1000 : 0;
                $template.find('.plus-pck-kg').data("basePlusPckKg", basePlusPckKg);

                // === BS Mesin & Setting dalam KG ===
                const bsMesinKg = composition > 0 ?
                    ((bsMesinVal / 1000) * (composition / 100)) : 0;
                const bsSettingKg = gwFinal > 0 ?
                    bsSettingVal * composition * gwFinal / 100 / 1000 : 0;

                $template.find('.bs-mesin').data("bsMesinKg", bsMesinKg);
                $template.find('.bs-setting').data("bsSettingKg", bsSettingKg);

                // === Label dan Name Index ===
                $template.find('.label-style-size').text(size);
                $template.find('.style-size-hidden').val(size);

                $template.find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace('[0]', `[${i}]`));
                    }
                });

                const $col = $('<div class="col-md-4 mb-3"></div>').append($template);
                $wrapper.append($col);
            });

            // 2) Setelah semua base tersimpan: update globalLossAktual dulu
            hitungPoKg(); // ini akan men-set lastLossAktual & globalLossAktual via updateGlobalLossAktual()

            // 3) Finalize poplus-mc-kg & plus-pck-kg berdasarkan base × (1 + globalLossAktual/100)
            $('.poplus-mc-kg').each(function() {
                const base = $(this).data("baseSisaOrderKg") || 0;
                $(this).val((base * (1 + (globalLossAktual / 100))).toFixed(2));
            });

            $('.plus-pck-kg').each(function() {
                const base = $(this).data("basePlusPckKg") || 0;
                $(this).val((base * (1 + (globalLossAktual / 100))).toFixed(2));
            });

            // 4) Sekarang update sisa & total (pakai nilai final yg baru)
            hitungSisaJatah();
            hitungTotalKg();
        });

        // Fungsi untuk hitung total Cns PO
        function hitungTotalCns() {
            let plusMcCns = 0;
            let plusPckCns = 0;

            $('.poplus-mc-cns').each(function() {
                const val = parseFloat($(this).val()) || 0;
                plusMcCns += val;
            });

            $('.plus-pck-cns').each(function() {
                const val = parseFloat($(this).val()) || 0;
                plusPckCns += val;
            });

            const total = plusMcCns + plusPckCns;

            $('.total-cns').val(total.toFixed(2));
        }

        // Saat nilai poplus-mc-cns berubah, hitung ulang total
        $(document).on('input', '.poplus-mc-cns', function() {
            hitungTotalCns();
        });

        // Saat nilai plus-pck-cns berubah, hitung ulang total
        $(document).on('input', '.plus-pck-cns', function() {
            hitungTotalCns();
        });

        // Fungsi untuk hitung total KG PO(+)
        function hitungTotalKg() {
            let plusPckTotal = 0;
            let poplusMcTotal = 0;
            let sisaMcTotal = 0;

            $('.plus-pck-kg').each(function() {
                const val = parseFloat($(this).val()) || 0;
                plusPckTotal += val;
            });

            $('.poplus-mc-kg').each(function() {
                const val = parseFloat($(this).val()) || 0;
                poplusMcTotal += val;
            });

            $('.sisa-mc-kg').each(function() {
                const val = parseFloat($(this).val()) || 0;
                sisaMcTotal += val;
            });

            const baseTotal = (plusPckTotal + poplusMcTotal) - sisaMcTotal;
            const sisaJatah = parseFloat($('.sisa-jatah').val()) || 0;

            let total;
            if (sisaJatah < 0) {
                // kalau minus → tambahkan nilai negatif itu (base - (-x))
                total = baseTotal - (sisaJatah);
                // total = baseTotal;
            } else {
                // kalau nol/positif → kurangi
                total = baseTotal - sisaJatah;
            }

            $('.total-kg').val(total.toFixed(2));
        }

        // Saat nilai sisa-mc-kg berubah, hitung ulang total
        $(document).on('input', '.sisa-mc-kg, .sisa-jatah, .loss-aktual', function() {
            hitungTotalKg();
        });

        let globalLossAktual = 0; // variabel global
        let lastLossAktual = 0;

        // Hitung Qty PO Kg All Style + Loss Aktual
        function hitungPoKg() {
            let totalPoKg = 0;
            let totalPoKgTanpaLoss = 0;

            // jumlahkan semua po-kg-perstyle
            $('.po-kg-perstyle').each(function() {
                const val = parseFloat($(this).val()) || 0;
                totalPoKg += val;
            });

            // jumlahkan semua po-kg-perstyle-tanpa-loss
            $('.po-kg-perstyle-tanpa-loss').each(function() {
                const val = parseFloat($(this).val()) || 0;
                totalPoKgTanpaLoss += val;
            });

            // update total PO Kg
            $('.po-kg').val(totalPoKg.toFixed(2));

            // hitung total BS (mesin + setting)
            let totalBs = 0;
            $('.bs-mesin').each(function() {
                const kg = parseFloat($(this).data('bsMesinKg')) || 0;
                totalBs += kg;
            });
            // setting pakai hasil per size
            $('.bs-setting').each(function() {
                const kg = parseFloat($(this).data('bsSettingKg')) || 0;
                totalBs += kg;
            });

            // hitung Loss Aktual %
            let lossAktual = 0;
            if (totalPoKg > 0) {
                lossAktual = (totalBs / totalPoKg) * 100;
            }

            lastLossAktual = lossAktual; // simpan untuk dipakai fungsi lain
            $('.loss-aktual').val(lossAktual.toFixed(2));

            updateGlobalLossAktual();
        }

        // Saat ada perubahan, hitung ulang
        $(document).on("change", ".po-kg-perstyle, .bs-mesin, .bs-setting, .loss-aktual", function() {
            reRenderSemua();
        });

        // Hitung Global Loss (gabungan lossAktual + lossTambahan)
        function updateGlobalLossAktual() {
            const lossTambahanRaw = $('.loss-tambahan').val();
            const lossTambahan = parseFloat(lossTambahanRaw) || 0;

            if (lossTambahanRaw === "" || lossTambahan === 0) {
                globalLossAktual = lastLossAktual;
            } else {
                globalLossAktual = lastLossAktual + lossTambahan;
            }
        }

        $(document).on("input", ".loss-tambahan", function() {
            updateGlobalLossAktual();
        });

        // Fungsi untuk hitung sisa jatah
        function hitungSisaJatah() {
            const poKg = parseFloat($('.po-kg').val()) || 0;
            const terima = parseFloat($('.terima').val()) || 0;

            const sisa = poKg - terima;

            $('.sisa-jatah').val(sisa.toFixed(2));

            hitungTotalKg();
        }
        // Saat total po-kg dihitung ulang → update sisa jatah juga
        $(document).on('input', '.po-kg', function() {
            hitungSisaJatah();
        });

        // Saat nilai terima berubah → update sisa jatah juga
        $(document).on('input', '.terima', function() {
            hitungSisaJatah();
        });

        // Saat user mengisi PO Pcs, hitung otomatis Plus Pck Kg
        $(document).on('input', '.plus-pck-pcs', function() {
            const $row = $(this).closest('.size-block');
            const $wrapper = $row.closest('.kebutuhan-item');
            const pcs = parseFloat($(this).val()) || 0;

            const itemType = $wrapper.find('.item-type').val();
            const kodeWarna = $wrapper.find('.kode-warna').val();
            const modelCode = $wrapper.find('.select-no-model option:selected').data('no-model');
            const styleSize = $row.find('.style-size-hidden').val();

            const materialData = $wrapper.data('material');

            let composition = 0,
                gw = 0,
                loss = 0;

            if (
                materialData &&
                materialData[itemType] &&
                materialData[itemType].kode_warna[kodeWarna]
            ) {
                const styleList = materialData[itemType].kode_warna[kodeWarna].style_size || [];

                const match = styleList.find(item => item.no_model === modelCode && item.style_size === styleSize);
                if (match) {
                    composition = parseFloat(match.composition) || 0;
                    gw = parseFloat(match.gw) || 0;
                    loss = parseFloat(match.loss) || 0;
                }
            }

            const pluspck = pcs * composition * gw / 100 / 1000;
            const kgPlusPck = pluspck * (1 + (globalLossAktual / 100));

            $row.find('.plus-pck-kg').val(kgPlusPck.toFixed(2));

            // Setelah update, hitung total
            hitungTotalKg();
        });

        // Saat user mengubah sisa-order secara manual → hitung ulang poplus-mc-kg
        $(document).on('input', '.sisa-order', function() {
            const $row = $(this).closest('.size-block'); // container untuk 1 size
            const $wrapper = $row.closest('.kebutuhan-item');

            const sisaOrderVal = parseFloat($(this).val()) || 0;
            const composition = parseFloat($row.find('.composition-hidden').val()) || 0; // tambahkan input hidden jika perlu
            const gwFinal = parseFloat($row.find('.gw-hidden').val()) || 0; // tambahkan input hidden jika perlu

            // Hitung ulang base KG (tanpa loss)
            const baseKg = gwFinal > 0 ? (sisaOrderVal * composition * gwFinal / 100 / 1000) : 0;

            // Simpan ulang base ke data
            $row.find('.poplus-mc-kg').data("baseSisaOrderKg", baseKg);

            // Hitung ulang final KG dengan loss
            const finalKg = baseKg * (1 + (globalLossAktual / 100));
            $row.find('.poplus-mc-kg').val(finalKg.toFixed(2));

            // Trigger update total
            hitungTotalKg();
        });

        // Fungsi untuk render ulang semua perhitungan
        function reRenderSemua() {
            $('.kebutuhan-item').each(function() {
                const $row = $(this);
                // trigger ulang kode warna agar semua size-block dihitung ulang
                const kodeWarna = $row.find('.kode-warna').val();
                if (kodeWarna) {
                    $row.find('.kode-warna').trigger('change');
                }
            });

            // setelah render ulang, hitung ulang loss & total
            hitungPoKg();
            hitungSisaJatah();
            hitungTotalKg();
        }

        // Saat nilai loss-tambahan berubah → re-render semua
        $(document).on('input', '.loss-tambahan', function() {
            reRenderSemua();
        });

        //Delete Row Size
        $(document).on('click', '.remove-size-row', function(e) {
            e.preventDefault();

            const $row = $(this).closest('.col-md-4');

            // Konfirmasi dulu, boleh juga langsung hapus tanpa konfirmasi
            if (confirm('Yakin ingin menghapus baris ini?')) {
                $row.remove();
                hitungTotalKg();
            }
        });

        //Save data
        $('#btn-save').on('click', function() {
            let formData = [];
            let loading = document.getElementById('loading-spinner');
            loading.classList.remove('d-none')

            $('.kebutuhan-item').each(function() {
                const area = $(this).find('.select-area').val();
                const no_model = $(this).find('.select-no-model').val();
                const item_type = $(this).find('.item-type').val();
                const kode_warna = $(this).find('.kode-warna').val();
                const color = $(this).find('.color').first().val(); // Ambil color utama
                const sisa_bb_mc = $(this).find('.sisa-mc-kg').val();
                const terima_kg = $(this).find('.terima').first().val(); // Ambil color utama
                const sisa_jatah = $(this).find('.sisa-jatah').first().val(); // Ambil color utama
                const poplus_mc_cns = $(this).find('.poplus-mc-cns').val();
                const plus_pck_cns = $(this).find('.plus-pck-cns').val();
                const delivery_po_plus = $('#delivery-po-plus').val();
                const keterangan = $('#keterangan').val();
                const total_kg_po = $(this).find('.total-kg').val();
                const total_cns_po = $(this).find('.total-cns').val();
                const loss_aktual = $(this).find('.loss-aktual').val();
                const loss_tambahan = $(this).find('.loss-tambahan').val();
                console.log(delivery_po_plus);
                $(this).find('.size-block').each(function() {
                    formData.push({
                        area: area,
                        no_model: no_model,
                        item_type: item_type,
                        kode_warna: kode_warna,
                        color: color,
                        sisa_bb_mc: sisa_bb_mc,
                        terima_kg: terima_kg,
                        sisa_jatah: sisa_jatah,
                        poplus_mc_cns: poplus_mc_cns,
                        plus_pck_cns: plus_pck_cns,
                        style_size: $(this).find('.style-size-hidden').val(),
                        sisa_order_pcs: $(this).find('.sisa-order').val(),
                        bs_mesin_kg: $(this).find('.bs-mesin').val(),
                        bs_st_pcs: $(this).find('.bs-setting').val(),
                        poplus_mc_kg: $(this).find('.poplus-mc-kg').val(),
                        plus_pck_pcs: $(this).find('.plus-pck-pcs').val(),
                        plus_pck_kg: $(this).find('.plus-pck-kg').val(),
                        total_kg_po: total_kg_po,
                        total_cns_po: total_cns_po,
                        delivery_po_plus: delivery_po_plus,
                        keterangan: keterangan,
                        loss_aktual: loss_aktual,
                        loss_tambahan: loss_tambahan
                    });
                });
            });

            console.log(formData); // Debug sebelum submit

            $.ajax({
                url: base + role + '/savePoTambahan',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(response) {
                    if (response.status === 'ok' || response.status === 'success') {
                        Swal.fire({
                            title: 'Berhasil!',
                            icon: 'success',
                            html: `
                        <strong>Sukses:</strong> ${response.sukses || 0}<br>
                        <strong>Gagal:</strong> ${response.gagal || 0}
                    `,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            icon: 'error',
                            text: response.message || 'Gagal menyimpan data.',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error!',
                        icon: 'error',
                        text: 'Terjadi kesalahan saat menyimpan data.',
                    });
                },
                complete: function() {
                    loading.classList.add('d-none'); // Sembunyikan loading setelah selesai
                }
            });
        });

    });
</script>


<?php $this->endSection(); ?>