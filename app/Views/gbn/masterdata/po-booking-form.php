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
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Booking</h5>
                <a href="<?= base_url($role . '/masterdata') ?>" class="btn bg-gradient-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/masterdata/poBooking/saveOpenPoBooking') ?>" method="post">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tujuan</label>
                            <select class="form-control" name="tujuan_po" id="selectTujuan" onchange="tujuan()" required>
                                <option value="">Pilih Tujuan</option>
                                <option value="Celup Cones">Celup Cones</option>
                                <option value="Covering">Covering</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Buyer</label>
                            <select class="form-control buyer" name="items[0][buyer]" required>
                                <option value="">Pilih Buyer</option>
                                <?php foreach ($buyer as $b) : ?>
                                    <option value="<?= $b['buyer'] ?>"><?= $b['buyer'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Benang</label>
                                <select class="form-control texture" name="jenis_benang" required>
                                    <option value="">Pilih Jenis Benang</option>
                                    <option value="DTY">DTY</option>
                                    <option value="FDY">FDY</option>
                                    <option value="NFY">NFY</option>
                                    <option value="POY">POY</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Spesifikasi Benang</label>
                                <select class="form-control fillamen" name="spesifikasi_benang" required>
                                    <option value="">Pilih Spesifikasi Benang</option>
                                    <option value="SIM DH">SIM DH</option>
                                    <option value="NIM DH">NIM DH</option>
                                    <option value="LIM DH">LIM DH</option>
                                </select>
                            </div>
                        </div>
                    </div>
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
                                    <input type="text" class="form-control select-no-model" name="no_model[0][no_model]" id="no_model" required>
                                </div>
                                <div class=" row">
                                    <div class="col-md-6">
                                        <!-- Item Type -->
                                        <div class="form-group">
                                            <label>Item Type</label>
                                            <select class="form-control item-type" name="items[0][item_type]" id="item-type" required>
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
                                                <input type="text" class="form-control color" name="items[0][color]" id="color" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Kode Warna -->
                                        <div class="form-group">
                                            <div class="col"><label>Kg Kebutuhan</label>
                                                <input type="number" step="0.01" class="form-control kg-po" name="items[0][kg_po]" required>
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
                <!-- <div class="form-group">
                    <label for="ttl_keb">Total Kg Kebutuhan</label>
                    <input type="text" class="form-control" name="ttl_keb" id="ttl_keb" readonly>

                </div> -->
                <div class=" form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="ttl_keb">Total Kg Kebutuhan</label>
                            <input type="text" class="form-control" name="ttl_keb" id="ttl_keb" readonly>

                        </div>
                        <div class="col-md-4">
                            <label for="kg_stock">Permintan Kelos (Kg Cones)</label>
                            <input type="number" step="0.01" class="form-control" name="kg_percones" id="kg_percones" placeholder="Kg">
                        </div>
                        <div class="col-md-4">
                            <label for="ttl_keb">Permintan Kelos (Total Cones)</label>
                            <input type="number" step="0.01" class="form-control" name="jumlah_cones" id="jumlah_cones" placeholder="Cns">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="kg_stock">Bentuk Celup</label>
                            <select class="form-control" name="bentuk_celup" id="bentuk_celup">
                                <option value="">Pilih Bentuk Celup</option>
                                <option value="Cones">Cones</option>
                                <option value="Hank">Hank</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ttl_keb">Untuk Produksi</label>
                            <input type="text" class="form-control" name="jenis_produksi" id="jenis_produksi">
                        </div>
                        <div class="col-md-4">
                            <label for="ttl_keb">Contoh Warna</label>
                            <input type="text" class="form-control" name="contoh_warna" id="contoh_warna">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Keterangan</label>
                    <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Penerima</label>
                            <input type="text" class="form-control" id="penerima" name="penerima" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label>Penanggung Jawab</label>
                            <select class="form-control" name="penanggung_jawab" required>
                                <option value="">Pilih</option>
                                <option value="Hartanto">Hartanto</option>
                                <option value="Megah">Megah</option>
                            </select>
                        </div>
                    </div>
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

        // Inisialisasi Select2 pada konteks tertentu
        function initSelect2(ctx) {
            // inisialisasi semua select2 dasar
            $(ctx).find('.buyer, .item-type, .kode-warna')
                .select2({
                    width: '100%',
                    allowClear: true
                });

            // khusus untuk item-type: pakai AJAX, kirim GET parameter buyer
            $(ctx).find('.item-type').select2({
                width: '100%',
                placeholder: 'Pilih Item Type',
                allowClear: true,
                ajax: {
                    url: `<?= base_url() ?>/gbn/masterdata/poBooking/getItemType`,
                    dataType: 'json',
                    delay: 250, // debounce biar nggak kebanjiran request
                    data: function(params) {
                        return {
                            buyer: $('.buyer').val(), // ambil buyer dari select utama
                            q: params.term || '' // optional: kalau endpoint butuh search term
                        };
                    },
                    processResults: function(data) {
                        // sudah dalam format [{id, text}, …]
                        return {
                            results: data
                        };
                    },
                    error: function() {
                        console.error('Gagal load item type');
                    }
                }
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
                         <input type="text" class="form-control select-no-model" name="no_model[${idx}][no_model]" id="no_model" required>
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
                            <label>Kg Kebutuhan</label>
                            <input type="number" step="0.01" class="form-control kg-po" name="items[${idx}][kg_po]" required>
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

        // Fungsi untuk menghitung jumlah cones
        function hitungCones() {
            // Ambil nilai input
            const ttl_keb = parseFloat(document.getElementById('ttl_keb').value);
            const kg_percones = parseFloat(document.getElementById('kg_percones').value);

            // Validasi nilai input
            if (isNaN(ttl_keb) || isNaN(kg_percones) || kg_percones < 0) {
                document.getElementById('jumlah_cones').innerText = '-';
                alert('Pastikan TTL KEB dan KG PERCONES diisi dengan angka valid, dan KG PERCONES lebih besar dari nol!');
                return;
            }

            // Hitung jumlah cones
            const jumlah_cones = ttl_keb / kg_percones;

            // Tampilkan hasil
            document.getElementById('jumlah_cones').value = Math.ceil(jumlah_cones);
        }

        // Trigger calculation on input changes
        $('#kg_stock, .kg-po').on('input', calculateTotal);
        $('#kg_percones, #ttl_keb').on('input', hitungCones);

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

        // Kalau buyer berubah, clear item-type
        $('.buyer').on('change', function() {
            $('.item-type').val(null).trigger('change');
        });

        // Listener untuk populate kode-warna
        $(document).on('change', '.item-type', function() {
            const $pane = $(this).closest('.kebutuhan-item');
            const buyer = $('.buyer').val();
            const itemType = $(this).val();
            const $kode = $pane.find('.kode-warna');

            $kode.empty().append('<option value="">Pilih Kode Warna</option>');
            if (!buyer || !itemType) return;

            $.getJSON(
                '<?= base_url($role . "/masterdata/poBooking/getKodeWarna") ?>', {
                    buyer,
                    item_type: itemType
                },
                function(options) {
                    options.forEach(opt =>
                        $kode.append($('<option>').val(opt.id).text(opt.text))
                    );
                    // jika kode-warna pakai Select2: perlu trigger update
                    $kode.trigger('change.select2');
                }
            );
        });

        // Listener untuk ambil color
        $(document).on('change', '.kode-warna', function() {
            const $pane = $(this).closest('.kebutuhan-item');
            const buyer = $('.buyer').val();
            const itemType = $pane.find('.item-type').val();
            const kodeWarna = $(this).val();
            const $color = $pane.find('.color');

            if (buyer && itemType && kodeWarna) {
                $.getJSON(
                    '<?= base_url($role . "/masterdata/poBooking/getColor") ?>', {
                        buyer,
                        item_type: itemType,
                        kode_warna: kodeWarna
                    },
                    function(resp) {
                        $color.val(resp.color);
                    }
                ).fail(function() {
                    $color.val('Error ambil color');
                });
            } else {
                $color.val('');
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