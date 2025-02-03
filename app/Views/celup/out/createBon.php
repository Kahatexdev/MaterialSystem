<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Form Input Bon Pengiriman</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url($role . '/outCelup/saveBon/') ?>" method="post">
                        <div id="kebutuhan-container">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <input type="hidden" name="id_celup">
                                    <label>Detail Surat Jalan</label>
                                    <select class="form-control" name="detail_sj" id="detail_sj" required>
                                        <option value="">Pilih Surat Jalan</option>
                                        <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                        <option value="IMPORT DARI KOREA">IMPORT DARI KOREA</option>
                                        <option value="JS MISTY">JS MISTY</option>
                                        <option value="JS SOLID">JS SOLID</option>
                                        <option value="JS KHTEX">JS KHTEX</option>
                                        <option value="PO(+)">PO(+)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>No Surat Jalan</label>
                                    <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Tanggal Datang</label>
                                    <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
                                </div>
                            </div>
                            <!--  -->
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                    <!-- Form Items -->
                                    <div class="kebutuhan-item">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>No Model</label>
                                                <select class="form-control" name="items[0][no_model]" id="no_model" required>
                                                    <option value="">Pilih No Model</option>
                                                    <?php foreach ($no_model as $model) { ?>
                                                        <option value="<?= $model['no_model'] ?>"><?= $model['no_model'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Item Type</label>
                                                <select class="form-control" name="items[0][item_type]" id="item_type" required>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Kode Warna</label>
                                                <select class="form-control text-uppercase" name="items[0][kode_warna]" id="kode_warna" required>
                                                </select>
                                            </div>

                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <label>Warna</label>
                                                <input type="text" class="form-control" name="items[0][warna]" id="warna" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>LMD</label>
                                                <select class="form-control" name="l_m_d" id="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Harga</label>
                                                <input type="number" class="form-control" name="harga" id="harga" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>
                                                            <input type="checkbox" name="ganti_retur" id=" ganti_retur">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Ya</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <h3>Form Barcode</h3>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="poTable" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width=100 class="text-center">No</th>
                                                            <th class="text-center">GW Kirim</th>
                                                            <th class="text-center">Kgs Kirim</th>
                                                            <th class="text-center">Cones Kirim</th>
                                                            <th class="text-center">Lot Kirim</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="1" readonly></td>
                                                            <td><input type="number" class="form-control gw_kirim_input" name="gw_kirim[]"></td>
                                                            <td><input type="number" class="form-control kgs_kirim_input" name="kgs_kirim[]"></td>
                                                            <td><input type="number" class="form-control cones_kirim_input" name="cones_kirim[]"></td>
                                                            <td><input type="number" class="form-control lot_kirim_input" name="lot_kirim[]"></td>
                                                            <td class="text-center">
                                                                <!-- <button type="button" class="btn btn-danger removeRow">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button> -->
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <!-- Baris Total -->
                                                    <tfoot>
                                                        <tr>
                                                            <th class="text-center">Total Karung</th>
                                                            <th class="text-center">Total GW</th>
                                                            <th class="text-center">Total NW</th>
                                                            <th class="text-center">Total Cones</th>
                                                            <th class="text-center">Total Lot</th>
                                                            <th></th>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="number" class="form-control" id="total_karung" placeholder="Total Karung" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_gw_kirim" placeholder="GW" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_kgs_kirim" placeholder="NW" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_cones_kirim" placeholder="Cones" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_lot_kirim" placeholder="Lot" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="row mt-3">
                                            <div class="col-12 text-center mt-2">
                                                <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info w-100">Save</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Ambil item type berdasarkan no model
    document.addEventListener('change', function(event) {
        if (event.target && event.target.id === 'no_model') {
            const noModel = event.target.value; // Ambil nilai no_model yang dipilih
            const itemTypeDropdown = event.target.closest('.row').querySelector('#item_type');

            if (noModel) {
                // Kirim request AJAX ke controller CodeIgniter
                $.ajax({
                    url: '<?= base_url($role . '/createBon/getItemType') ?>',
                    type: 'POST',
                    data: {
                        no_model: noModel
                    },
                    dataType: 'json',
                    success: function(response) {
                        itemTypeDropdown.innerHTML = '<option value="">Pilih Item Type</option>';
                        if (response.length > 0) {
                            response.forEach(item => {
                                itemTypeDropdown.innerHTML += `<option value="${item.item_type}">${item.item_type}</option>`;
                            });
                        } else {
                            itemTypeDropdown.innerHTML = '<option value="">No items found</option>';
                        }
                    },
                    error: function() {
                        alert('Error fetching data. Please try again.');
                    }
                });
            } else {
                itemTypeDropdown.innerHTML = '';
            }
        }
    });

    // Ambil kode warna berdasarkan no_model dan item_type
    document.addEventListener('change', function(event) {
        if (event.target && event.target.id === 'item_type') {
            const itemType = event.target.value;
            const noModel = event.target.closest('.row').querySelector('#no_model').value;
            const kodeWarnaDropdown = event.target.closest('.row').querySelector('#kode_warna');

            if (noModel && itemType) {
                $.ajax({
                    url: '<?= base_url($role . '/createBon/getKodeWarna') ?>',
                    type: 'POST',
                    data: {
                        no_model: noModel,
                        item_type: itemType
                    },
                    dataType: 'json',
                    success: function(response) {
                        kodeWarnaDropdown.innerHTML = '<option value="">Pilih Kode Warna</option>';
                        if (response.length > 0) {
                            response.forEach(color => {
                                kodeWarnaDropdown.innerHTML += `<option value="${color.kode_warna}">${color.kode_warna}</option>`;
                            });
                        } else {
                            kodeWarnaDropdown.innerHTML = '<option value="">No color codes found</option>';
                        }
                    },
                    error: function() {
                        alert('Error fetching color codes. Please try again.');
                    }
                });
            } else {
                kodeWarnaDropdown.innerHTML = '<option value="">Pilih Kode Warna</option>';
            }
        }
    });

    // Ambil item type berdasarkan no model, item_type dan kode warna
    document.addEventListener('change', function(event) {
        if (event.target && event.target.id === 'kode_warna') {
            const kodeWarna = event.target.value;
            const itemType = event.target.closest('.row').querySelector('#item_type').value;
            const noModel = event.target.closest('.row').querySelector('#no_model').value;
            const warnaInput = event.target.closest('.kebutuhan-item').querySelector('[id="warna"]');

            if (noModel && itemType && kodeWarna) {
                $.ajax({
                    url: '<?= base_url($role . '/createBon/getWarna') ?>',
                    type: 'POST',
                    data: {
                        no_model: noModel,
                        item_type: itemType,
                        kode_warna: kodeWarna
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.warna) {
                            warnaInput.value = response.warna; // Tetapkan nilai
                        } else {
                            console.error('Warna tidak ditemukan di response:', response);
                            warnaInput.value = ''; // Kosongkan jika tidak ada
                        }
                    },
                    error: function() {
                        alert('Error fetching warna. Please try again.');
                    }
                });
            } else {
                warnaInput.value = '';
            }
        }
    });
    // 
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
            const newPoTableId = `poTable-${tabIndex}`;

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
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>No Model</label>
                                                <select class="form-control" name="items[${tabIndex - 1}][no_model]" id="no_model" required>
                                                    <option value="">Pilih No Model</option>
                                                    <?php foreach ($no_model as $model) { ?>
                                                        <option value="<?= $model['no_model'] ?>"><?= $model['no_model'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Item Type</label>
                                                <select class="form-control" name="items[${tabIndex - 1}][item_type]" id="item_type" required>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Kode Warna</label>
                                                <select class="form-control" name="items[${tabIndex - 1}][kode_warna]" id="kode_warna" required>
                                                </select>
                                            </div>

                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <label>Warna</label>
                                                <input type="text" class="form-control" name="items[${tabIndex - 1}][warna]" id="warna" required readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label>LMD</label>
                                                <select class="form-control" name="items[${tabIndex - 1}][l_m_d]" id="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Harga</label>
                                                <input type="number" class="form-control" name="items[${tabIndex - 1}][harga]" id="harga" required>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>
                                                            <input type="checkbox" name="items[${tabIndex - 1}][ganti_retur" id=" ganti_retur">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Ya</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-5">
                                            <h3>Form Barcode</h3>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="${newPoTableId}" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width=100 class="text-center">No</th>
                                                            <th class="text-center">GW Kirim</th>
                                                            <th class="text-center">Kgs Kirim</th>
                                                            <th class="text-center">Cones Kirim</th>
                                                            <th class="text-center">Lot Kirim</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="1" readonly></td>
                                                            <td><input type="number" class="form-control gw_kirim_input" name="gw_kirim[]"></td>
                                                            <td><input type="number" class="form-control kgs_kirim_input" name="kgs_kirim[]"></td>
                                                            <td><input type="number" class="form-control cones_kirim_input" name="cones_kirim[]"></td>
                                                            <td><input type="number" class="form-control lot_kirim_input" name="lot_kirim[]"></td>

                                                            <td class="text-center">
                                                                <!-- <button type="button" class="btn btn-danger removeRow">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button> -->
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <!-- Baris Total -->
                                                    <tfoot>
                                                        <tr>
                                                            <th class="text-center">Total Karung</th>
                                                            <th class="text-center">Total GW</th>
                                                            <th class="text-center">Total NW</th>
                                                            <th class="text-center">Total Cones</th>
                                                            <th class="text-center">Total Lot</th>
                                                            <th></th>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="number" class="form-control" id="total_karung" placeholder="Total Karung" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_gw_kirim" placeholder="GW" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_kgs_kirim" placeholder="NW" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_cones_kirim" placeholder="Cones" readonly></td>
                                                            <td><input type="number" class="form-control" id="total_lot_kirim" placeholder="Lot" readonly></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="row mt-3">
                                            <div class="col-12 text-center mt-2">
                                                <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                </button>
                                                <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                                    <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                </button>
                                            </div>
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
            // Add row functionality
            const addRowButton = newTabPane.querySelector("#addRow");
            const removeRowButton = newTabPane.querySelector("#removeRow");
            const newPoTable = newTabPane.querySelector(`#${newPoTableId}`);

            addRowButton.addEventListener("click", function() {
                const rowCount = newPoTable.querySelectorAll("tbody tr").length + 1;
                const newRow = document.createElement("tr");

                newRow.innerHTML = `
                <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="${rowCount}" readonly></td>
                <td><input type="number" class="form-control gw_kirim_input" name="gw_kirim[]"></td>
                <td><input type="number" class="form-control kgs_kirim_input" name="kgs_kirim[]"></td>
                <td><input type="number" class="form-control cones_kirim_input" name="cones_kirim[]"></td>
                <td><input type="number" class="form-control lot_kirim_input" name="lot_kirim[]"></td>
                <td class="text-center">
                <button type="button" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></button>
                </td>
                `;

                newPoTable.querySelector("tbody").appendChild(newRow);

                // Tambahkan event listener untuk tombol hapus (removeRow) pada baris baru
                newRow.querySelector(".removeRow").addEventListener("click", function() {
                    newRow.remove();
                    updateRowNumbers(newPoTable);
                    calculateTotals(newPoTable); // Perbarui total setelah baris dihapus
                });
                // Recalculate totals when new row is added
                newRow.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', function() {
                        calculateTotals(newPoTable);
                    });
                });
                calculateTotals(newPoTable);

            });

            // Event listeners for input changes
            newPoTable.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function() {
                    calculateTotals(newPoTable);
                });
            });

            calculateTotals(newPoTable);
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

        // Event listener untuk tombol "Add More" di tab pertama
        const addMoreButton = document.querySelector(".add-more");
        addMoreButton.addEventListener("click", addNewTab);

        const removeButton = document.querySelector(".remove-tab");
        removeButton.addEventListener("click", function() {
            const firstTabButton = navTab.querySelector(".nav-link");
            const firstTabPane = navTabContent.querySelector(".tab-pane");
            removeTab(firstTabButton, firstTabPane);
        });
        updateTabNumbers();
    });

    document.addEventListener('DOMContentLoaded', () => {
        const poTable = document.getElementById('poTable');

        // Tambahkan event listener pada semua input di tbody
        poTable.querySelectorAll('tbody input').forEach(input => {
            input.addEventListener('input', () => {
                calculateTotals(poTable);
            });
        });

        // Tombol tambah baris
        document.getElementById('addRow').addEventListener('click', () => {
            const tbody = poTable.querySelector('tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input type="text" class="form-control text-center" name="identitas_krg[]" value="" readonly></td>
            <td><input type="number" class="form-control" name="gw_kirim[]"></td>
            <td><input type="number" class="form-control" name="kgs_kirim[]"></td>
            <td><input type="number" class="form-control" name="cones_kirim[]"></td>
            <td><input type="number" class="form-control" name="lot_kirim[]"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></button>
            </td>
        `;
            tbody.appendChild(newRow);

            // Update nomor baris
            updateRowNumbers(poTable);

            // Tambahkan event listener ke input baru
            newRow.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => {
                    calculateTotals(poTable);
                });
            });

            // Event listener untuk tombol hapus baris
            newRow.querySelector('.removeRow').addEventListener('click', () => {
                newRow.remove();
                updateRowNumbers(poTable);
                calculateTotals(poTable);
            });
        });

        // Panggil pertama kali untuk inisialisasi
        calculateTotals(poTable);
    });


    function updateRowNumbers(poTable) {
        const rows = poTable.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('input[name="identitas_krg[]"]').value = index + 1;
        });
    }

    function calculateTotals(poTable) {
        let totalGW = 0;
        let totalKgs = 0;
        let totalCones = 0;
        let totalLot = 0;

        const totalRows = poTable.querySelectorAll('tbody tr').length;

        poTable.querySelectorAll('tbody tr').forEach(row => {
            const gw = parseFloat(row.querySelector('input[name="gw_kirim[]"]').value) || 0;
            const kgs = parseFloat(row.querySelector('input[name="kgs_kirim[]"]').value) || 0;
            const cones = parseFloat(row.querySelector('input[name="cones_kirim[]"]').value) || 0;
            const lot = parseFloat(row.querySelector('input[name="lot_kirim[]"]').value) || 0;

            totalGW += gw;
            totalKgs += kgs;
            totalCones += cones;
            totalLot += lot;
        });

        // Update hanya dalam tabel yang sesuai
        poTable.querySelector('#total_karung').value = totalRows;
        poTable.querySelector('#total_gw_kirim').value = totalGW;
        poTable.querySelector('#total_kgs_kirim').value = totalKgs;
        poTable.querySelector('#total_cones_kirim').value = totalCones;
        poTable.querySelector('#total_lot_kirim').value = totalLot;
    }


    addNewTab();
    // Hitung total saat halaman dimuat
    // document.addEventListener("DOMContentLoaded", function() {
    //     calculateTotals();
    // });
</script>



<?php $this->endSection(); ?>