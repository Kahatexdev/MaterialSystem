<?php $this->extend($role . '/masterdata/header'); ?>
<?php $this->section('content'); ?>
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

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Gabungan <?= basename(current_url()) ?></h5>
                <a href="<?= base_url($role . '/masterdata/poGabungan') ?>" class="btn bg-gradient-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/openPO/saveOpenPOGabungan') ?>" method="post">
                <div class="form-group">
                    <label>Tujuan</label>
                    <select class="form-control" name="tujuan_po" id="selectTujuan" onchange="tujuan()" required>
                        <option value="">Pilih Tujuan</option>
                        <option value="Celup Cones">Celup Cones</option>
                        <!-- <option value="Covering">Covering</option> -->
                    </select>
                </div>

                <div id="kebutuhan-container">
                    <label>Pilih Bahan Baku</label>
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                        </div>
                    </nav>
                    <!-- Tab Konten Item Type -->
                    <!-- HTML Struktur (tab-content seperti di atas) -->
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel">
                            <div class="kebutuhan-item" data-index="0">
                                <!-- No Model -->
                                <div class="form-group">
                                    <label>No Model</label>
                                    <select class="form-control select-no-model" name="no_model[0][no_model]" required>
                                        <option value="">Pilih No Model</option>
                                        <?php foreach ($model as $m): ?>
                                            <option value="<?= $m['id_order'] ?>" data-id-order="<?= $m['id_order'] ?>" data-no-model="<?= $m['no_model'] ?>"><?= $m['no_model'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class=" row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <label>Item Type</label>
                                            <select class="form-control item-type" name="items[0][item_type]" required>
                                                <option value="">Pilih Item Type</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <label>Kode Warna</label>
                                            <select class="form-control kode-warna" name="items[0][kode_warna]" required>
                                                <option value="">Pilih Kode Warna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <div class="col"><label>Color</label>
                                                <input type="text" class="form-control color" name="items[0][color]" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <div class="col"><label>Kg MU</label>
                                                <input type="text" class="form-control kg-mu" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <div class="col"><label>Kg Stok</label>
                                                <input type="text" class="form-control kg-stok" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <div class="col"><label>Kg Kebutuhan</label>
                                                <input type="text" class="form-control kg-po" name="items[0][kg_po]" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="text-center my-2">
                                    <button type="button" class="btn btn-outline-info add-more"><i class="fas fa-plus"></i></button>
                                    <button type="button" class="btn btn-outline-danger remove"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label for="ttl_keb">Total Kg Kebutuhan</label>
                    <input type="text" class="form-control" name="ttl_keb" id="ttl_keb" readonly>

                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="kg_stock">Permintan Kelos (Kg)</label>
                            <input type="text" class="form-control" name="kg_percones" id="kg_percones" placeholder="Kg">
                        </div>
                        <div class="col-md-6">
                            <label for="ttl_keb">Permintan Kelos (Total Cones)</label>
                            <input type="text" class="form-control" name="jumlah_cones" id="jumlah_cones" placeholder="Cns">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="kg_stock">Bentuk Celup</label>
                            <select class="form-control" name="bentuk_celup" id="bentuk_celup">
                                <option value="">Pilih Bentuk Celup</option>
                                <option value="Cones">Cones</option>
                                <option value="Hank">Hank</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="ttl_keb">Untuk Produksi</label>
                            <input type="text" class="form-control" name="jenis_produksi" id="jenis_produksi">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Keterangan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                </div>
                <div class="form-group">
                    <label>Penerima</label>
                    <input type="text" class="form-control" id="penerima" name="penerima" readonly required>
                </div>
                <div class="form-group">
                    <label>Penanggung Jawab</label>
                    <select class="form-control" name="penanggung_jawab" required>
                        <option value="">Pilih</option>
                        <option value="HARTANTO">Hartanto</option>
                        <option value="Megah">Megah</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-info w-100">Save</button>
        </div>
        </form>
    </div>
</div>

<!-- Pastikan jQuery load pertama -->

<script>
    $(function() {
        const base = '<?= base_url() ?>';
        const role = '<?= $role ?>';
        const materialDataCache = {};
        let tabIndex = 2;

        const $navTab = $('#nav-tab');
        const $navTabContent = $('#nav-tabContent');
        // simpan opsi no-model awal untuk template tab baru
        const noModelOptions = $('.select-no-model').first().html();

        // Inisialisasi Select2 pada konteks tertentu
        function initSelect2(ctx) {
            $(ctx).find('.select-no-model, .item-type, .kode-warna')
                .select2({
                    width: '100%',
                    allowClear: true
                });
        }

        // Tambah tab baru
        function addNewTab() {
            const idx = tabIndex - 1; // 0-based index untuk nama array
            // buat tombol tab
            const $btn = $(`
            <button class="nav-link" id="nav-tab-${tabIndex}-button"
                    data-bs-toggle="tab" data-bs-target="#nav-content-${tabIndex}"
                    type="button" role="tab" aria-selected="false">
                ${tabIndex}
            </button>
        `);
            $navTab.append($btn);

            // buat pane
            const paneHtml = `
            <div class="tab-pane fade" id="nav-content-${tabIndex}" role="tabpanel"
                 aria-labelledby="nav-tab-${tabIndex}-button">
                <div class="kebutuhan-item" data-index="${idx}">
                    <div class="form-group">
                        <label>No Model</label>
                        <select class="form-control select-no-model" name="no_model[${idx}][no_model]" required>
                            ${noModelOptions}
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Type</label>
                                <select class="form-control item-type" name="items[${idx}][item_type]" required>
                                    <option value="">Pilih Item Type</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Warna</label>
                                <select class="form-control kode-warna" name="items[${idx}][kode_warna]" required>
                                    <option value="">Pilih Kode Warna</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Color</label>
                            <input type="text" class="form-control color" name="items[${idx}][color]" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Kg MU</label>
                            <input type="text" class="form-control kg-mu" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Kg Stok</label>
                            <input type="text" class="form-control kg-stok" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Kg Kebutuhan</label>
                            <input type="text" class="form-control kg-po" name="items[${idx}][kg_po]" required>
                        </div>
                    </div>
                    <div class="text-center my-2">
                        <button type="button" class="btn btn-outline-info add-more"><i class="fas fa-plus"></i></button>
                        <button type="button" class="btn btn-outline-danger remove"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
            const $pane = $(paneHtml);
            $navTabContent.append($pane);

            // re-init Select2 di tab baru
            initSelect2($pane);

            // tunjukkan tab baru
            new bootstrap.Tab($btn[0]).show();

            // Attach event listener for the new inputs
            $(`#nav-content-${tabIndex} .kg-po`).on('input', calculateTotal);

            tabIndex++;
        }

        // Hapus tab (tombol Remove baik di tab lama maupun baru)
        function removeTab($btn, $pane) {
            if ($navTab.children().length <= 1) {
                return alert('Minimal harus ada satu tab.');
            }
            $btn.remove();
            $pane.remove();
            // setelah hapus, selalu aktifkan tab pertama
            new bootstrap.Tab($navTab.find('button').first()[0]).show();
        }

        // kalkulasi kg kebutuhan
        function calculateTotal() {
            let totalKebutuhan = 0;

            // Loop semua input dengan class .kg-po, termasuk dari pane lain
            $('.kg-po').each(function() {
                totalKebutuhan += parseFloat($(this).val()) || 0;
            });

            // Update nilai Total Kg Kebutuhan
            $('#ttl_keb').val(totalKebutuhan);
        }

        // Trigger calculation on input changes
        $('#kg_stock, .kg-po').on('input', calculateTotal);

        // -----------------------
        // Binding awal
        // -----------------------
        initSelect2(document);
        $(document).on('click', '.add-more', addNewTab);
        $(document).on('click', '.remove', function() {
            const $pane = $(this).closest('.tab-pane');
            const target = '#' + $pane.attr('id');
            const $btn = $navTab.find(`[data-bs-target="${target}"]`);
            removeTab($btn, $pane);
        });

        // Change handler untuk No Model
        $(document).on('change', '.select-no-model', function() {
            const $this = $(this);
            const $row = $this.closest('.kebutuhan-item');
            const selected = $this.find(':selected');
            const idOrder = selected.data('id-order');
            const modelCode = selected.data('no-model');

            // reset dropdown & fields
            $row.find('.item-type, .kode-warna')
                .empty().append('<option value="">Pilih</option>')
                .trigger('change');
            $row.find('.color, .kg-mu, .kg-po').val('');

            if (!idOrder) return;

            // pakai cache dulu
            if (materialDataCache[idOrder]) {
                populateItemTypes(materialDataCache[idOrder], modelCode, $row);
            } else {
                fetch(`${base}/${role}/masterdata/poGabunganDetail/${idOrder}`)
                    .then(res => res.ok ? res.json() : Promise.reject('Error response'))
                    .then(json => {
                        if (!json.material) throw 'Material kosong';
                        materialDataCache[idOrder] = json.material;
                        populateItemTypes(json.material, modelCode, $row);
                    })
                    .catch(err => console.error('Fetch error:', err));
            }
        });

        // isi Item Type setelah No Model
        function populateItemTypes(matData, modelCode, $row) {
            const $it = $row.find('.item-type')
                .empty().append('<option value="">Pilih Item Type</option>');
            Object.entries(matData).forEach(([type, info]) => {
                if (info.no_model === modelCode) {
                    $it.append(`<option value="${type}">${type}</option>`);
                }
            });
            $it.trigger('change');
        }

        // ketika pilih Item Type → isi Kode Warna
        $(document).on('change', '.item-type', function() {
            const $this = $(this);
            const $row = $this.closest('.kebutuhan-item');
            const type = $this.val();
            const idOrder = $row.find('.select-no-model :selected').data('id-order');
            const matData = materialDataCache[idOrder] || {};

            const $kw = $row.find('.kode-warna')
                .empty().append('<option value="">Pilih Kode Warna</option>');
            $row.find('.color, .kg-mu, .kg-po').val('');

            if (matData[type]) {
                matData[type].kode_warna.forEach(w => {
                    $kw.append(`
                    <option value="${w.kode_warna}"
                            data-color="${w.color}"
                            data-kg-mu="${w.total_kg}"
                            ">
                        ${w.kode_warna}
                    </option>`);
                });
            }
            $kw.trigger('change');
        });

        // ketika pilih Kode Warna → fill Color, Kg MU, Kg Stok
        $(document).on('change', '.kode-warna', function() {
            const $opt = $(this).find(':selected');
            const $row = $(this).closest('.kebutuhan-item');
            $row.find('.color').val($opt.data('color') || '');
            $row.find('.kg-mu').val(parseFloat($opt.data('kg-mu') || 0).toFixed(2));
            $row.find('.kg-po').val('');
        });

        $(document).on('change', '.kode-warna', function() {
            const $opt = $(this).find(':selected');
            const $row = $(this).closest('.kebutuhan-item');
            const idOrder = $row.find('.select-no-model :selected').data('id-order');
            const itemType = $row.find('.item-type').val();
            const kodeWarna = $opt.val();

            $row.find('.color').val($opt.data('color') || '');
            $row.find('.kg-mu').val(parseFloat($opt.data('kg-mu') || 0).toFixed(2));
            $row.find('.kg-po').val('');

            if (idOrder && itemType && kodeWarna) {
                const url = `${base}/${role}/masterdata/cekStockOrder/${idOrder}/${itemType}/${kodeWarna}`;
                fetch(url)
                    .then(res => res.ok ? res.json() : Promise.reject('Error response'))
                    .then(json => {
                        $row.find('.kg-stok').val(parseFloat(json.kgs_stock || 0).toFixed(2));
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        $row.find('.kg-stok').val('0.00');
                    });
            } else {
                $row.find('.kg-stok').val('0.00');
            }
        });

        // fungsi Tujuan → isi penerima
        window.tujuan = function() {
            const val = $('#selectTujuan').val();
            $('#penerima').val(val === 'Covering' ? 'Paryanti' : 'Retno');
        };
    });
</script>


<?php $this->endSection(); ?>