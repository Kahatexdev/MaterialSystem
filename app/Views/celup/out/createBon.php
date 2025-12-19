<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/flatpickr/flatpickr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/js/plugins/flatpickr/style.css') ?>">
<style>
    /* Auto Complete */
    .ui-state-active {
        background: rgb(230, 153, 233) !important;
        color: #fff !important;
    }

    /* Tambahkan CSS ini */
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f8f9fa !important;
        opacity: 1 !important;
        color: #212529 !important;
        cursor: not-allowed;
        border-color: #dee2e6;
    }

    .form-check-input:disabled {
        opacity: 1 !important;
        cursor: not-allowed;
    }

    .form-check-input:disabled~label {
        opacity: 1 !important;
        color: #212529 !important;
    }
</style>
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Form Input Bon Pengiriman</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url($role . '/outCelup/saveBon') ?>" method="post">
                            <div id="kebutuhan-container">
                                <div class="row mb-4">
                                    <div class="col-md-4">

                                        <label>Detail Surat Jalan</label>
                                        <select class="form-control" name="detail_sj" id="detail_sj" required>
                                            <option value="">Pilih Surat Jalan</option>
                                            <!-- <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                            <option value="IMPORT DARI KOREA">IMPORT DARI KOREA</option>
                                            <option value="JS MISTY">JS MISTY</option>
                                            <option value="JS SOLID">JS SOLID</option> -->
                                            <option value="KHTEX">KHTEX</option>
                                            <option value="PO(+)">PO(+)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>No Surat Jalan</label>
                                        <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" required>
                                        <div id="no_surat_jalan_error" class="text-danger mt-1 small"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tanggal Kirim</label>
                                        <input type="text" class="form-control" id="tgl_datang" name="tgl_datang" required placeholder="Pilih Tanggal">
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
                                            <div class="row g-3 mb-2">
                                                <div class="col-md-12">
                                                    <label for="itemType">Done Celup</label>
                                                    <select class="form-control" id="add_item" name="add_item" required>
                                                        <option value="">Pilih Item </option>
                                                        <?php foreach ($no_model as $item) : ?>
                                                            <option value="<?= $item['id_celup'] ?>"><?= $item['no_model'] ?> | <?= $item['item_type'] ?> |<?= $item['kode_warna'] ?> | <?= $item['warna']  ?> | Lot <?= $item['lot_celup'] ?> | <?= $item['kg_celup'] ?>kg</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row g-3">

                                                <div class="col-md-4">
                                                    <label>No Model</label>
                                                    <input type="text" class="form-control" name="items[0][id_celup]" id="id_celup" required placeholder="Pilih No Model" readonly hidden>
                                                    <input type="text" class="form-control" name="items[0][no_model]" id="no_model" required placeholder="No Model">
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Item Type</label>
                                                    <input type="text" class="form-control" name="items[0][item_type]" id="item_type" required placeholder="Item Type" readonly>

                                                </div>
                                                <div class="col-md-3">
                                                    <label>Kode Warna</label>
                                                    <input type="text" class="form-control" name="items[0][kode_warna]" id="kode_warna" required placeholder="Kode Warna" readonly>
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="po-plus" class="text-center">Po Tambahan</label>
                                                    <div class="row">
                                                        <div class="col-md-3 form-check">
                                                            <label>
                                                                <input type="checkbox" name="po_plus" id="po_plus" class="form-check-input" disabled>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Ya</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Surat Jalan Section -->
                                            <div class="row g-3 mt-3">
                                                <div class="col-md-4">
                                                    <label>Warna</label>
                                                    <input type="text" class="form-control" name="items[0][warna]" id="warna" required placeholder="Warna" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>LMD</label>
                                                    <select class="form-control" name="l_m_d[0]" id="l_m_d" required>
                                                        <option value="">Pilih LMD</option>
                                                        <option value="L">L</option>
                                                        <option value="M">M</option>
                                                        <option value="D">D</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Harga</label>
                                                    <input type="number" step="0.01" class="form-control" name="harga[0]" id="harga" required>
                                                </div>
                                                <!-- <div class="col-md-1">
                                                    <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                    <div class="row">
                                                        <div class="col-md-3 form-check">
                                                            <label>
                                                                <input type="hidden" name="ganti_retur[0]" value="0">
                                                                <input type="checkbox" name="ganti_retur[0]" id="ganti_retur" value="1" class="form-check-input"
                                                                    <?= isset($data['ganti_retur']) && $data['ganti_retur'] == 1 ? 'checked' : '' ?>>
                                                            </label>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label for="">Ya</label>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>

                                            <div class="mt-5">
                                                <h3>Form Input Data Karung</h3>
                                            </div>

                                            <!-- Out Celup Section -->
                                            <div class="row g-3 mt-3">
                                                <div class="table-responsive">
                                                    <table id="poTable" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th width=100 class="text-center">No Karung</th>
                                                                <th class="text-center">Cones Kirim</th>
                                                                <th class="text-center">GW Kirim</th>
                                                                <th class="text-center">NW Kirim</th>
                                                                <th class="text-center">Lot Kirim</th>
                                                                <th class="text-center">Operator Packing</th>
                                                                <th class="text-center">Shift</th>
                                                                <th class="text-center">
                                                                    <button type="button" class="btn btn-info" id="addRow">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><input type="text" class="form-control text-center" name="no_karung[0][0]" value="1" readonly></td>
                                                                <td><input type="number" step="0.01" class="form-control cones_kirim_input" name="cones_kirim[0][0]" required></td>
                                                                <td><input type="number" step="0.01" class="form-control gw_kirim_input" name="gw_kirim[0][0]" required></td>
                                                                <td><input type="number" step="0.01" class="form-control kgs_kirim_input" name="kgs_kirim[0][0]" required></td>
                                                                <td><input type="text" class="form-control lot_celup_input" name="lot_celup[0][0]" required></td>
                                                                <td><input type="text" class="form-control operator_packing_input" name="operator_packing[0][0]" id="operator_packing" required></td>
                                                                <td><select class="form-control" name="shift[0][0]" id="shift" required>
                                                                        <option value="">Pilih Shift</option>
                                                                        <option value="SHIFT A">Shift A</option>
                                                                        <option value="SHIFT B">Shift B</option>
                                                                        <option value="SHIFT C">Shift C</option>
                                                                    </select></td>
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
                                                                <th class="text-center">Total Cones</th>
                                                                <th class="text-center">Total GW</th>
                                                                <th class="text-center">Total NW</th>
                                                                <th class="text-center">Total Lot</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr>
                                                                <td><input type="number" class="form-control" id="total_karung" name="total_karung" placeholder="Total Karung" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_cones_kirim" name="total_cones_kirim" placeholder="Cones" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_gw_kirim" name="total_gw_kirim" placeholder="GW" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_kgs_kirim" name="total_kgs_kirim" placeholder="NW" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_lot_kirim" name="total_lot_kirim" placeholder="Lot" readonly></td>
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
</div>
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="<?= base_url('assets/css/flatpickr/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/plugins/flatpickr/index.js') ?>"></script>
<script>
    flatpickr("#tgl_datang", {
        dateFormat: "d-m-Y",
        altInput: true,
        altFormat: "d-m-Y",
        locale: "id",
        allowInput: true
        // disableMobile: true
    });
</script>
<script>
    const input = document.getElementById('no_surat_jalan');
    const error = document.getElementById('no_surat_jalan_error');

    input.addEventListener('input', function() {
        const val = this.value.trim();

        if (val === '-') {
            error.textContent = 'Tidak boleh hanya tanda "-" saja';
            this.value = '';
        } else if (val === '0') {
            error.textContent = 'Tidak boleh hanya  "0" saja';
            this.value = '';

        } else {
            error.textContent = '';
        }
    });

    // document.getElementById('no_model').addEventListener('input', function(e) {
    //     // hanya huruf (A-Z, a-z) dan spasi yang boleh
    //     this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');
    // });
    document.getElementById('no_model').addEventListener('input', function() {
        this.value = this.value.replace(/[^a-zA-Z0-9 &]/g, '');
    });
</script>
<script>
    $(document).ready(function() {

        $('#add_item').select2();
        $('#add_item').on("select2:select", function() {
            let idcelup = $(this).val(); // Ambil value yang dipilih di select2

            $.ajax({
                url: "<?= base_url($role . '/createBon/getItem/') ?>" + idcelup,
                type: "POST",
                data: {
                    id: idcelup
                }, // Kirim dalam format object
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    $('#no_model').val(data.no_model);
                    $('#item_type').val(data.item_type);
                    $('#kode_warna').val(data.kode_warna);
                    $('#warna').val(data.warna);
                    $('#id_celup').val(idcelup);
                    // update semua lot di tab aktif (tab pertama)
                    $('#poTable').find('.lot_celup_input').val(data.lot_celup);
                    lotCelupValue = data.lot_celup;
                }
            });
        });

    });

    document.addEventListener("DOMContentLoaded", function() {
        const navTab = document.getElementById("nav-tab");
        const navTabContent = document.getElementById("nav-tabContent");
        let tabIndex = 2;
        let valueLot = "";

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

        function updateRowNumbers(table) {
            const rows = table.querySelectorAll("tbody tr");
            rows.forEach((row, index) => {
                row.querySelector("input[name^='no_karung']").value = index + 1;
            });
        }

        // Event delegation untuk menghapus baris
        document.addEventListener("click", function(event) {
            if (event.target.closest(".removeRow")) {
                const row = event.target.closest("tr");
                const table = row.closest("table");
                row.remove();
                updateRowNumbers(table);
                calculateTotals(table);
            }
        });

        // Fungsi untuk membuat tab baru
        function addNewTab() {
            // ID untuk tab dan konten baru
            const newTabId = `nav-tab-${tabIndex}`;
            const newContentId = `nav-content-${tabIndex}`;
            const newPoTableId = `poTable-${tabIndex}`;
            const totalKarungId = `total_karung_${tabIndex}`;
            const totalGwId = `total_gw_kirim_${tabIndex}`;
            const totalKgsId = `total_kgs_kirim_${tabIndex}`;
            const totalConesId = `total_cones_kirim_${tabIndex}`;
            const totalLotId = `total_lot_kirim_${tabIndex}`;

            const newInputId = `no_model_${tabIndex}`;
            const id_celup = `id_celup_${tabIndex}`;
            const itemTypeId = `item_type_${tabIndex}`;
            const kodeWarnaId = `kode_warna_${tabIndex}`;
            const warnaId = `warna_${tabIndex}`;
            const poPlusId = `po_plus_${tabIndex}`;
            const lotCelupId = `lot_celup_${tabIndex}`;

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
                                        <div class="row g-3 mb-2">
                                                <div class="col-md-12">
                                                    <label for="itemType">Done Celup</label>
                                                    <select class="form-control slc2" id="add_item_${tabIndex}" name="add_item" required>
                                                        <option value="">Pilih Item </option>
                                                        <?php foreach ($no_model as $item) : ?>
                                                            <option value="<?= $item['id_celup'] ?>"><?= $item['no_model'] ?> | <?= $item['item_type'] ?> |<?= $item['kode_warna'] ?> | <?= $item['warna']  ?> | Lot <?= $item['lot_celup'] ?> | <?= $item['kg_celup'] ?>kg</option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                           <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>No Model</label>
                                                <input type="text" class="form-control no-model" name="items[${tabIndex - 1}][id_celup]" id="${id_celup}" required placeholder="Pilih No Model" hidden>
                                                <input type="text" class="form-control no-model" name="items[${tabIndex - 1}][no_model]" id="${newInputId}" required placeholder="Pilih No Model">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Item Type</label>
                                                <select class="form-control item-type" name="items[${tabIndex - 1}][item_type]" id="${itemTypeId}" required>
                                                    <option value="">Pilih Item Type</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Kode Warna</label>
                                                <select class="form-control kode-warna" name="items[${tabIndex - 1}][kode_warna]" id="${kodeWarnaId}" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="po-plus" class="text-center">Po Tambahan</label>
                                                <div class="row">
                                                    <div class="col-md-3 form-check">
                                                        <label>
                                                            <input type="checkbox" name="po_plus" id="${poPlusId}" class="form-check-input" disabled>
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Ya</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Surat Jalan Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="col-md-4">
                                                <label>Warna</label>
                                                   <select class="form-control kode-warna" name="items[${tabIndex - 1}][kode_warna]" id="${warnaId}" required>
                                                    <option value="">Pilih Kode Warna</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>LMD</label>
                                                <select class="form-control" name="l_m_d[${tabIndex - 1}]" id="l_m_d" required>
                                                    <option value="">Pilih LMD</option>
                                                    <option value="L">L</option>
                                                    <option value="M">M</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Harga</label>
                                                <input type="number" step="0.01" class="form-control" name="harga[${tabIndex - 1}]" id="harga" required>
                                            </div>
                                            <!-- <div class="col-md-1">
                                                <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>
                                                            <input type="hidden" name="ganti_retur[${tabIndex - 1}]" value="0">
                                                            <input type="checkbox" name="ganti_retur[${tabIndex - 1}]" value="1">
                                                        </label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="">Ya</label>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="mt-5">
                                            <h3>Form Input Data Karung</h3>
                                        </div>

                                        <!-- Out Celup Section -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="${newPoTableId}" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width=100 class="text-center">No</th>
                                                            <th class="text-center">Cones Kirim</th>
                                                            <th class="text-center">GW Kirim</th>
                                                            <th class="text-center">Kgs Kirim</th>
                                                            <th class="text-center">Lot Kirim</th>
                                                            <th class="text-center">Operator Packing</th>
                                                            <th class="text-center">Shift</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="no_karung[${tabIndex - 1}][0]" value="1" readonly></td>
                                                            <td><input type="number" step="0.01" class="form-control cones_kirim_input" name="cones_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="number" step="0.01" class="form-control gw_kirim_input" name="gw_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="number" step="0.01" class="form-control kgs_kirim_input" name="kgs_kirim[${tabIndex - 1}][0]" required></td>
                                                            <td><input type="text" class="form-control lot_celup_input" name="lot_celup[${tabIndex - 1}][0]" id="${lotCelupId}" required></td>
                                                            <td><input type="text" class="form-control operator_packing_input" name="operator_packing[${tabIndex - 1}][0]" required></td>
                                                                <td><select class="form-control" name="shift[${tabIndex - 1}][0]" id="shift" required>
                                                                        <option value="">Pilih Shift</option>
                                                                        <option value="SHIFT A">Shift A</option>
                                                                        <option value="SHIFT B">Shift B</option>
                                                                        <option value="SHIFT C">Shift C</option>
                                                                    </select></td>
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
                                                            <th class="text-center">Total Cones</th>
                                                            <th class="text-center">Total GW</th>
                                                            <th class="text-center">Total NW</th>
                                                            <th class="text-center">Total Lot</th>
                                                            <th></th>
                                                        </tr>
                                                         <tr>
                                                            <td><input type="number" class="form-control" id="${totalKarungId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalConesId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalGwId}" readonly></td>
                                                            <td><input type="float" class="form-control" id="${totalKgsId}" readonly></td>
                                                            <td><input type="text" class="form-control" id="${totalLotId}" readonly></td>
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
            document.getElementById(newContentId).querySelectorAll('.slc2').forEach(el => {
                $(el).select2({
                    width: '100%'
                });

                $(el).on("select2:select", function() {
                    let idcelup = $(this).val(); // Ambil value yang dipilih di select2

                    $.ajax({
                        url: "<?= base_url($role . '/createBon/getItem/') ?>" + idcelup,
                        type: "POST",
                        data: {
                            id: idcelup
                        }, // Kirim dalam format object
                        dataType: "json",
                        success: function(data) {
                            console.log(data);
                            // Update elemen input dan select di dalam tab baru
                            document.getElementById(id_celup).value = idcelup;
                            document.getElementById(newInputId).value = data.no_model;
                            document.getElementById(itemTypeId).innerHTML = `<option value="${data.item_type}" selected>${data.item_type}</option>`;
                            document.getElementById(kodeWarnaId).innerHTML = `<option value="${data.kode_warna}" selected>${data.kode_warna}</option>`;
                            document.getElementById(warnaId).innerHTML = `<option value="${data.warna}" selected>${data.warna}</option>`;
                            document.getElementById(warnaId).innerHTML = `<option value="${data.warna}" selected>${data.warna}</option>`;
                            document.getElementById(poPlusId).checked = (data.po_plus == 1);
                            const pane = document.getElementById(newContentId); // tab pane yang sedang dibuat
                            pane.querySelectorAll('.lot_celup_input').forEach(el => el.value = data.lot_celup);

                            // simpan lot untuk addRow tab ini (jangan global 1 doang)
                            pane.dataset.lot = data.lot_celup;
                        }
                    });
                });
            });

            // Pindahkan ke tab baru
            const bootstrapTab = new bootstrap.Tab(newTabButton);
            bootstrapTab.show();

            // Event listener tombol
            newTabPane.querySelector(".add-more").addEventListener("click", addNewTab);

            newTabPane.querySelector(".remove-tab").addEventListener("click", function() {
                removeTab(newTabButton, newTabPane);
            });
            // Pasang event listener pada input baru
            newTabPane.querySelectorAll("input").forEach(input => {
                input.addEventListener("input", () => {
                    calculateTotals(newTabPane.querySelector(`#${newPoTableId}`));
                });
            });
            // Add row functionality
            const addRowButton = newTabPane.querySelector("#addRow");
            const removeRowButton = newTabPane.querySelector("#removeRow");
            const newPoTable = newTabPane.querySelector(`#${newPoTableId}`);
            const makan = tabIndex - 1;
            console.log(makan);
            addRowButton.addEventListener("click", function() {
                const rowCount = newPoTable.querySelectorAll("tbody tr").length + 1;
                const newRow = document.createElement("tr");
                const lot = newTabPane.dataset.lot || '';

                newRow.innerHTML = `
                    <td><input type="text" class="form-control text-center" name="no_karung[${tabIndex-2}][${rowCount-1}]" value="${rowCount}" readonly></td>
                    <td><input type="number" step="0.01" class="form-control cones_kirim_input" name="cones_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="number" step="0.01" class="form-control gw_kirim_input" name="gw_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="number" step="0.01" class="form-control kgs_kirim_input" name="kgs_kirim[${tabIndex-2}][${rowCount-1}]" required></td>
                    <td><input type="text" class="form-control lot_celup_input" name="lot_celup[${tabIndex-2}][${rowCount-1}]" value="${lot}" required></td>
                    <td><input type="text" class="form-control operator_packing_input" name="operator_packing[${tabIndex-2}][${rowCount-1}]" disabled></td>
                        <td><select class="form-control" name="shift[${tabIndex-2}][${rowCount-1}]" id="shift" disabled>
                        <option value=""></option>
                        <option value="SHIFT A">Shift A</option>
                        <option value="SHIFT B">Shift B</option>
                        <option value="SHIFT C">Shift C</option>
                        </select></td>
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
                // calculateTotals(newPoTable);
                calculateTotals(newTabPane.querySelector(`#${newPoTableId}`));
            });

            // Event listeners for input changes
            newPoTable.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', function() {
                    calculateTotals(newPoTable);
                });
            });



            tabIndex++;
            calculateTotals(newPoTable);

            // Validasi Karakter No Model
            setTimeout(() => {
                const noModelInput = document.getElementById(newInputId);
                if (noModelInput) {
                    noModelInput.addEventListener('input', function(e) {
                        this.value = this.value.replace(/[^a-zA-Z0-9 &]/g, '');
                    });
                }
            }, 300);
        }


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
        let lotCelupValue = ""; // Variabel untuk menyimpan lot_celup dari select2

        // Tambahkan event listener pada semua input di tbody
        poTable.querySelectorAll('tbody input').forEach(input => {
            input.addEventListener('input', () => {
                calculateTotals(poTable);
            });
        });

        // Event listener untuk select2
        $(document).ready(function() {
            $('#add_item').select2();
            $('#add_item').on("select2:select", function() {
                let idcelup = $(this).val(); // Ambil value yang dipilih di select2

                $.ajax({
                    url: "<?= base_url($role . '/createBon/getItem/') ?>" + idcelup,
                    type: "POST",
                    data: {
                        id: idcelup
                    }, // Kirim dalam format object
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        $('#no_model').val(data.no_model);
                        $('#item_type').val(data.item_type);
                        $('#kode_warna').val(data.kode_warna);
                        $('#warna').val(data.warna);
                        $('#id_celup').val(idcelup);
                        // update semua lot di tab aktif (tab pertama)
                        $('#poTable').find('.lot_celup_input').val(data.lot_celup);
                        lotCelupValue = data.lot_celup;
                        lotCelupValue = data.lot_celup; // Simpan nilai lot_celup
                        $('#po_plus').prop('checked', data.po_plus == 1);
                    }
                });
            });
        });

        // Tombol tambah baris
        document.getElementById('addRow').addEventListener('click', () => {
            const rowCount = poTable.querySelectorAll("tbody tr").length + 1;
            const tbody = poTable.querySelector('tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input type="text" class="form-control text-center" name="no_karung[0][${rowCount-1}]" value="${rowCount}" readonly></td>
            <td><input type="number" step="0.01" class="form-control" name="cones_kirim[0][${rowCount-1}]" required></td>
            <td><input type="number" step="0.01" class="form-control" name="gw_kirim[0][${rowCount-1}]" required></td>
            <td><input type="number" step="0.01" class="form-control" name="kgs_kirim[0][${rowCount-1}]" required></td>
            <td><input type="text" class="form-control lot_celup_input" name="lot_celup[0][${rowCount-1}]" id="lot_celup" value="${lotCelupValue}" required></td>
            <td><input type="text" class="form-control" name="operator_packing[0][${rowCount - 1}]" disabled></td>
            <td><select class="form-control" name="shift[0][${rowCount - 1}]" id="shift" disabled>
                <option value=""></option>
                <option value="SHIFT A">Shift A</option>
                <option value="SHIFT B">Shift B</option>
                <option value="SHIFT C">Shift C</option>
            </select></td>
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

        });

        // Panggil pertama kali untuk inisialisasi
        calculateTotals(poTable);
    });

    function updateRowNumbers(poTable) {
        const rows = poTable.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            const karungInput = row.querySelector('input[name^="no_karung"]');
            if (karungInput) {
                karungInput.value = index + 1;
            }
        });
    }

    function calculateTotals(poTable) {
        let totalGW = 0;
        let totalKgs = 0;
        let totalCones = 0;
        let totalLot = 0;
        let totalRows = 0;

        // Hitung total berdasarkan input di dalam poTable
        poTable.querySelectorAll("tbody tr").forEach(row => {
            totalGW += parseFloat(row.querySelector("input[name^='gw_kirim']")?.value || 0);
            totalGW = parseFloat(totalGW.toFixed(2));
            totalKgs += parseFloat(row.querySelector("input[name^='kgs_kirim']")?.value || 0);
            totalKgs = parseFloat(totalKgs.toFixed(2));
            totalCones += parseFloat(row.querySelector("input[name^='cones_kirim']")?.value || 0);
            totalLot += parseFloat(row.querySelector("input[name^='lot_kirim']")?.value || 0);
        });

        totalRows = poTable.querySelectorAll("tbody tr").length;

        // Perbarui nilai di <tfoot>
        const tfoot = poTable.querySelector("tfoot");
        if (tfoot) {
            tfoot.querySelector("input[id^='total_karung']").value = totalRows;
            tfoot.querySelector("input[id^='total_gw_kirim']").value = totalGW;
            tfoot.querySelector("input[id^='total_kgs_kirim']").value = totalKgs;
            tfoot.querySelector("input[id^='total_cones_kirim']").value = totalCones;
            tfoot.querySelector("input[id^='total_lot_kirim']").value = totalRows;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Untuk tab pertama
        const firstPoTable = document.querySelector("#poTable-1");
        if (firstPoTable) {
            firstPoTable.querySelectorAll("input").forEach(input => {
                input.addEventListener("input", function() {
                    calculateTotals(firstPoTable);
                });
            });
            calculateTotals(firstPoTable); // Hitung total awal
        }
    });

    // addNewTab();
</script>

<?php $this->endSection(); ?>