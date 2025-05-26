<?php $this->extend($role . '/mastermaterial/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
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
                });
            });
        </script>
    <?php endif; ?>

    <!--  -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO</h5>
                <a href="<?= base_url($role . '/material/' . $id_order) ?>" class="btn bg-gradient-info"> Kembali</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/openPO/saveOpenPO/' . $id_order) ?>" method="post">
                <div class="form-group">
                    <label>Tujuan</label>
                    <select class="form-control" name="tujuan_po" id="selectTujuan" onchange="tujuan()" required>
                        <option value="">Pilih Tujuan</option>
                        <option value="Celup Cones">Celup Cones</option>
                        <option value="Covering">Covering</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>No Model</label>
                    <input type="text" class="form-control" name="no_model" value="<?= $model ?>" readonly required>
                </div>
                <div id="kebutuhan-container">
                    <label>Pilih Bahan Baku</label>
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                        </div>
                    </nav>
                    <!-- Tab Konten Item Type -->
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="kebutuhan-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="itemType">Item Type</label>
                                            <select class="form-control item-type" name="items[0][item_type]" required>
                                                <option value="">Pilih Item Type</option>
                                                <?php foreach ($order as $type): ?>
                                                    <option value="<?= $type['item_type'] ?>">
                                                        <?= $type['item_type'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jenis Benang</label>
                                            <select class="form-control texture" name="items[0][jenis_benang]">
                                                <option value="">Pilih Jenis Benang</option>
                                                <option value="DTY">DTY</option>
                                                <option value="FDY">FDY</option>
                                                <option value="NFY">NFY</option>
                                                <option value="POY">POY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Spesifikasi Benang</label>
                                            <select class="form-control fillamen" name="items[0][spesifikasi_benang]">
                                                <option value="">Pilih Spesifikasi Benang</option>
                                                <option value="SIM DH">SIM DH</option>
                                                <option value="NIM DH">NIM DH</option>
                                                <option value="LIM DH">LIM DH</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
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
                                        <div class="form-group">
                                            <label>Color</label>
                                            <input type="text" class="form-control color" name="items[0][color]" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kgMU">Kg MU</label>
                                            <input type="float" class="form-control kg-mu" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class=" col-md-6">
                                        <div class="form-group">
                                            <label for="kgStok">Kg Stok</label>
                                            <input type="float" class="form-control kg-stok" readonly required>
                                        </div>
                                    </div>
                                    <div class=" col-md-6">
                                        <div class="form-group">
                                            <label for="kgKebutuhan">Kg Kebutuhan</label>
                                            <input type="float" class="form-control kg-po" name="items[0][kg_po]" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kg_percones">Permintan Kelos (Kg Cones)</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control kg-percones" name="items[0][kg_percones]" placeholder="Kg">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah_cones">Permintan Kelos (Total Cones)</label>
                                        <input type="text" class="form-control jumlah-cones" name="items[0][jumlah_cones]" placeholder="Cns" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="bentuk_celup">Bentuk Celup</label>
                                        <select class="form-control bentuk-celup" name="items[0][bentuk_celup]">
                                            <option value="">Pilih Bentuk Celup</option>
                                            <option value="Cones">Cones</option>
                                            <option value="Hank">Hank</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jenis_produksi">Untuk Produksi</label>
                                        <input type="text" class="form-control jenis-produksi" name="items[0][jenis_produksi]">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="contoh_warna">Contoh Warna</label>
                                        <input type="text" class="form-control contoh-warna" name="items[0][contoh_warna]">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ket_celup">Keterangan Celup</label>
                                        <textarea class="form-control ket-celup" name="items[0][ket_celup]"></textarea>
                                    </div>
                                </div>

                                <div style="width: 100%; text-align: center; margin-top: 10px; margin-bottom:10px;">
                                    <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    </button>
                                    <button class="btn btn-icon btn-3 btn-outline-danger remove" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Penerima</label>
                            <input type="text" class="form-control" id="penerima" name="penerima" readonly required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
                            <select class="form-control" name="penanggung_jawab" required>
                                <option value="">Pilih</option>
                                <option value="HARTANTO">Hartanto</option>
                                <option value="Megah">Megah</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">PO(+)</label>
                            <select class="form-control" name="po_plus" id="po_plus">
                                <option value="">Pilih</option>
                                <option value="1">YA</option>
                                <option value="0">TIDAK</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows=""></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-info w-100">Save</button>
        </div>
        </form>
    </div>
</div>

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const navTab = document.getElementById("nav-tab");
        const navTabContent = document.getElementById("nav-tabContent");
        let tabIndex = 2;

        function updateTabNumbers() {
            // Update nomor pada setiap tab
            const tabButtons = navTab.querySelectorAll(".nav-link");
            const tabPanes = navTabContent.querySelectorAll(".tab-pane");

            tabButtons.forEach((button, index) => {
                const newNumber = index + 1;
                button.textContent = newNumber; // Update nomor tab
                button.dataset.bsTarget = `#nav-content-${newNumber}`;
                button.id = `nav-tab-${newNumber}-button`;

                const relatedPane = tabPanes[index];
                relatedPane.id = `nav-content-${newNumber}`;
                relatedPane.ariaLabelledby = `nav-tab-${newNumber}-button`;

                // Update nama atribut input agar sinkron
                relatedPane.querySelectorAll("[name]").forEach((input) => {
                    const name = input.name.replace(/\d+/, newNumber - 1);
                    input.name = name;
                });
            });

            // Perbarui indeks tab berikutnya
            tabIndex = tabButtons.length + 1;
        }

        // Fungsi untuk membuat tab baru
        function addNewTab() {
            // ID untuk tab dan konten baru
            const newTabId = `nav-tab-${tabIndex}`;
            const newContentId = `nav-content-${tabIndex}`;

            // Tambahkan tab baru ke nav-tab
            const newTabButton = document.createElement("button");
            newTabButton.className = "nav-link";
            newTabButton.id = `${newTabId}-button`;
            newTabButton.dataset.bsToggle = "tab";
            newTabButton.dataset.bsTarget = `#${newContentId}`;
            newTabButton.type = "button";
            newTabButton.role = "tab";
            newTabButton.ariaControls = newContentId;
            newTabButton.ariaSelected = "false";
            newTabButton.textContent = tabIndex;

            // Tambahkan tab button ke nav-tab
            navTab.appendChild(newTabButton);

            // Tambahkan konten baru ke tab-content
            const newTabPane = document.createElement("div");
            newTabPane.className = "tab-pane fade";
            newTabPane.id = newContentId;
            newTabPane.role = "tabpanel";
            newTabPane.ariaLabelledby = `${newTabId}-button`;

            // Tambahkan elemen `input-group` ke tab baru
            newTabPane.innerHTML = `
                            <div class="kebutuhan-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="itemType">Item Type</label>
                                            <select class="form-control item-type" name="items[${tabIndex - 1}][item_type]" required>
                                                <option value="">Pilih Item Type</option>
                                                <?php foreach ($order as $type): ?>
                                                    <option value="<?= $type['item_type'] ?>">
                                                        <?= $type['item_type'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jenis Benang</label>
                                            <select class="form-control texture" name="items[${tabIndex - 1}][jenis_benang]" required>
                                                <option value="">Pilih Jenis Benang</option>
                                                <option value="DTY">DTY</option>
                                                <option value="FDY">FDY</option>
                                                <option value="NFY">NFY</option>
                                                <option value="POY">POY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Spesifikasi Benang</label>
                                            <select class="form-control fillamen" name="items[0][spesifikasi_benang]" required>
                                                <option value="">Pilih Spesifikasi Benang</option>
                                                <option value="SIM DH">SIM DH</option>
                                                <option value="NIM DH">NIM DH</option>
                                                <option value="LIM DH">LIM DH</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Kode Warna</label>
                                            <select class="form-control kode-warna" name="items[${tabIndex - 1}][kode_warna]" required>
                                                <option value="">Pilih Kode Warna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Color</label>
                                            <input type="text" class="form-control color" name="items[${tabIndex - 1}][color]" readonly required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kgMU">Kg MU</label>
                                            <input type="float" class="form-control kg-mu" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class=" col-md-6">
                                        <div class="form-group">
                                            <label for="kgStok">Kg Stok</label>
                                            <input type="float" class="form-control kg-stok" readonly required>
                                        </div>
                                    </div>
                                    <div class=" col-md-6">
                                        <div class="form-group">
                                            <label for="kgKebutuhan">Kg Kebutuhan</label>
                                            <input type="float" class="form-control kg-po" name="items[${tabIndex - 1}][kg_po]" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kg_percones">Permintan Kelos (Kg Cones)</label>
                                        <input type="text" class="form-control kg-percones" name="items[${tabIndex - 1}][kg_percones]" placeholder="Kg">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah_cones">Permintan Kelos (Total Cones)</label>
                                        <input type="text" class="form-control jumlah-cones" name="items[${tabIndex - 1}][jumlah_cones]" placeholder="Cns" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="bentuk_celup">Bentuk Celup</label>
                                        <select class="form-control bentuk-celup" name="items[${tabIndex - 1}][bentuk_celup]">
                                            <option value="">Pilih Bentuk Celup</option>
                                            <option value="Cones">Cones</option>
                                            <option value="Hank">Hank</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jenis_produksi">Untuk Produksi</label>
                                        <input type="text" class="form-control jenis-produksi" name="items[${tabIndex - 1}][jenis_produksi]">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="contoh_warna">Contoh Warna</label>
                                        <input type="text" class="form-control contoh-warna" name="items[${tabIndex - 1}][contoh_warna]">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="ket_celup">Keterangan Celup</label>
                                        <textarea class="form-control ket-celup" name="items[${tabIndex - 1}][ket_celup]"></textarea>
                                    </div>
                                </div>
                                <div style="width: 100%; text-align: center; margin-top: 10px; margin-bottom:10px;">
                                    <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                    </button>
                                    <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                        <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                    </button>
                                </div>
                            </div>
        `;

            navTabContent.appendChild(newTabPane);

            // Pindahkan ke tab baru
            const bootstrapTab = new bootstrap.Tab(newTabButton);
            bootstrapTab.show();

            // Event listener tombol
            newTabPane.querySelector(".add-more").addEventListener("click", addNewTab);
            newTabPane.querySelector(".remove-tab").addEventListener("click", function() {
                removeTab(newTabButton, newTabPane);
            });

            tabIndex++;
        }

        // Fungsi untuk menghapus tab dan kontennya
        function removeTab(tabButton, tabPane) {
            if (navTab.children.length > 1) {
                tabButton.remove();
                tabPane.remove();
                updateTabNumbers();
                // Pindahkan ke tab pertama jika tab aktif dihapus
                const firstTab = navTab.querySelector("button");
                if (firstTab) {
                    const bootstrapTab = new bootstrap.Tab(firstTab);
                    bootstrapTab.show();
                }
            } else {
                alert("Minimal harus ada satu tab.");
            }
        }


        document.getElementById('nav-tabContent').addEventListener('input', function(event) {
            // Periksa apakah event berasal dari input kg-percones atau kg-po
            if (event.target.classList.contains('kg-percones') || event.target.classList.contains('kg-po')) {
                const parentTab = event.target.closest('.kebutuhan-item'); // Cari parent container
                const kgPoInput = parentTab.querySelector('.kg-po'); // Input kg-po di tab terkait
                const kgPerconesInput = parentTab.querySelector('.kg-percones'); // Input kg-percones di tab terkait
                const jumlahConesInput = parentTab.querySelector('.jumlah-cones'); // Output jumlah-cones di tab terkait

                const kg_po = parseFloat(kgPoInput.value);
                const kg_percones = parseFloat(kgPerconesInput.value);

                // Validasi nilai input
                if (isNaN(kg_po) || isNaN(kg_percones) || kg_percones <= 0) {
                    jumlahConesInput.value = '-';
                    return;
                }

                // Hitung jumlah cones
                const jumlah_cones = kg_po / kg_percones;

                // Tampilkan hasil
                jumlahConesInput.value = Math.ceil(jumlah_cones);
            }
        });


        // Event listener untuk tombol "Add More" di tab pertama
        const addMoreButton = document.querySelector(".add-more");
        addMoreButton.addEventListener("click", addNewTab);

        const removeButton = document.querySelector(".remove-tab");
        removeButton.addEventListener("click", function() {
            const firstTabButton = navTab.querySelector(".nav-link");
            const firstTabPane = navTabContent.querySelector(".tab-pane");
            removeTab(firstTabButton, firstTabPane);
        });

    });

    let data = <?= json_encode($order) ?>;

    $(document).on('change', '.item-type', function() {
        let selectedItemType = $(this).val();
        let kodeWarnaDropdown = $(this).closest('.kebutuhan-item').find('.kode-warna');
        let container = $(this).closest('.kebutuhan-item');
        let colorInput = container.find('.color');
        let kgMuInput = container.find('.kg-mu');
        let kgStokInput = container.find('.kg-stok');
        let kgPOInput = container.find('.kg-po');

        kodeWarnaDropdown.empty();
        kodeWarnaDropdown.append('<option value="">Pilih Kode Warna</option>');

        // Reset input Color dan Kg MU
        colorInput.val('');
        kgMuInput.val('');
        kgStokInput.val('');
        kgPOInput.val('');

        if (data[selectedItemType]) {
            data[selectedItemType].kode_warna.forEach(function(warna) {
                kodeWarnaDropdown.append(
                    `<option value="${warna.kode_warna}" data-color="${warna.color}" data-kg-mu="${warna.total_kg}" data-kg-stok="${warna.kg_stok}" data-kg-po="${warna.kg_po}">
                    ${warna.kode_warna}
                </option>`
                );
            });
        }
    });

    $(document).on('change', '.kode-warna', function() {
        let selectedOption = $(this).find(':selected');
        let color = selectedOption.data('color');
        let kgMU = selectedOption.data('kg-mu');
        let kgStok = selectedOption.data('kg-stok');
        let kgPO = selectedOption.data('kg-po');

        kgMU = parseFloat(kgMU).toFixed(2);
        kgStok = parseFloat(kgStok).toFixed(2);
        kgPO = parseFloat(kgPO).toFixed(2);

        let container = $(this).closest('.kebutuhan-item');
        container.find('.color').val(color);
        container.find('.kg-mu').val(kgMU);
        container.find('.kg-stok').val(kgStok);
        container.find('.kg-po').val(kgPO);
        // console.log(kgPO);
    });

    function tujuan() {
        let select = document.getElementById('selectTujuan');
        let tujuan = select.value;
        let penerima = document.getElementById('penerima');
        if (tujuan === 'Covering') {
            penerima.value = 'Paryanti';
        } else {
            penerima.value = 'Retno';
        }
    }
</script>

<?php $this->endSection(); ?>