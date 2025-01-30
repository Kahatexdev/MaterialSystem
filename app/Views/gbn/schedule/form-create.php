<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Mengurangi ukuran font untuk catatan kecil */
    small.text-warning {
        font-size: 0.8em;
        color: #ffc107;
        /* Warna kuning */
    }

    .select2-container {
        width: 100% !important;
        /* Pastikan Select2 menyesuaikan dengan lebar container */
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        /* Sesuaikan dengan desain form lainnya */
        border: 1px solid #ced4da;
        /* Gaya default untuk input */
        border-radius: 0.25rem;
        /* Tambahkan border radius agar seragam */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        /* Tengah secara vertikal */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        /* Tinggi ikon panah */
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Input Schedule Celup</h3>
                    <!-- text keterangan -->
                    <div class="card-tools">
                        <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $tanggal_schedule ?> | Lot Urut : <?= $lot_urut ?></h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url(session('role') . '/schedule/saveSchedule') ?>" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <!-- No Mesin -->
                                    <div class="mb-3">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>
                                        <input type="hidden" name="tanggal_schedule" value="<?= $tanggal_schedule ?>">
                                        <input type="hidden" name="lot_urut" value="<?= $lot_urut ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- min caps -->
                                    <div class="mb-3">
                                        <label for="min_caps" class="form-label">Min Caps</label>
                                        <input type="number" class="form-control" id="min_caps" name="min_caps" value="<?= $min_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Max caps -->
                                    <div class="mb-3">
                                        <label for="max_caps" class="form-label">Max Caps</label>
                                        <input type="number" class="form-control" id="max_caps" name="max_caps" value="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Sisa Kapasitas hitung pakai JS -->
                                    <div class="mb-3">
                                        <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas</label>
                                        <input type="number" class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Jenis Bahan baku -->
                                    <div class="mb-3">
                                        <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                        <!-- select with search -->
                                        <select class="form-select" id="jenis_bahan_baku" name="jenis_bahan_baku" required>
                                            <option value="">Pilih Jenis Bahan Baku</option>
                                            <?php foreach ($jenis_bahan_baku as $bahan): ?>
                                                <option value="<?= $bahan['jenis'] ?>"><?= $bahan['jenis'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <!-- Item Type -->
                                    <div class="mb-3">
                                        <label for="item_type" class="form-label">Item Type</label>
                                        <select class="form-select" id="item_type" name="item_type" required>
                                            <option value="">Pilih Item Type</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Kode Warna -->
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Warna -->
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" maxlength="32" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- form input addmore-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="poTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">PO</th>
                                                        <th class="text-center">Tgl Start MC</th>
                                                        <th class="text-center">Delivery Awal</th>
                                                        <th class="text-center">Delivery Akhir</th>
                                                        <th class="text-center">Qty PO</th>
                                                        <th class="text-center">Qty PO(+)</th>
                                                        <th class="text-center">Kg Kebutuhan</th>
                                                        <th class="text-center">Tagihan SCH</th>
                                                        <th class="text-center">Qty Celup
                                                        </th>
                                                        <th class="text-center">PO(+)</th>
                                                        <th class="text-center">
                                                            <button type="button" class="btn btn-info" id="addRow">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select class="form-select po-select" name="po[]" required>
                                                                <option value="">Pilih PO</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="date" class="form-control" name="tgl_start_mc[]" readonly></td>
                                                        <td><input type="date" class="form-control" name="delivery_awal[]" readonly></td>
                                                        <td><input type="date" class="form-control" name="delivery_akhir[]" readonly></td>
                                                        <td><input type="number" class="form-control" name="qty_po[]" readonly></td>
                                                        <td><input type="number" class="form-control" name="qty_po_plus[]" readonly></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                <span class="kg_kebutuhan">0.00</span> KG <!-- Ganti id dengan class -->
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                <span class="sisa_jatah">0.00</span> KG <!-- Ganti id dengan class -->
                                                            </span>
                                                        </td>
                                                        <td><input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required></td>
                                                        <td>
                                                            <select class="form-select" name="po_plus[]" required>
                                                                <option value="">Pilih PO(+)</option>
                                                                <option value="1">Ya</option>
                                                                <option value="0">Tidak</option>
                                                            </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger removeRow">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-center">
                                                            <strong>Total Qty Celup</strong>
                                                        </td>
                                                        <td colspan="8" class="text-center">
                                                            <input type="number" class="form-control" id="total_qty_celup" name="total_qty_celup" value="0" readonly>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-info w-100">Simpan Jadwal</button>
                                </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Add JavaScript to initialize Select2 -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const jenisBahanBaku = document.getElementById('jenis_bahan_baku');
        const itemType = document.getElementById('item_type');
        const kodeWarna = document.getElementById('kode_warna');

        // Event listener for 'jenis_bahan_baku' dropdown change
        jenisBahanBaku.addEventListener('change', function() {
            const jenis = this.value;

            // Reset 'item_type' dropdown
            itemType.innerHTML = '<option value="">Pilih Item Type</option>';

            if (jenis) {
                // Fetch item types based on the selected 'jenis_bahan_baku'
                getItemType(jenis);
            } else {
                // If no 'jenis' is selected, leave the 'item_type' dropdown with default option
                itemType.innerHTML = '<option value="">Pilih Item Type</option>';
            }
        });

        // Function to fetch item types based on the selected 'jenis'
        function getItemType(jenis) {
            fetch('<?= base_url(session('role') . "/schedule/getItemType") ?>?jenis=' + jenis)
                .then(response => response.json())
                .then(data => {
                    // Add new options to the 'item_type' dropdown
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.item_type;
                        option.textContent = item.item_type;
                        itemType.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching item types:', error);
                });
        }
    });

    // DOM Elements and Event Listeners for fetching warna based on item_type and kode_warna
    document.addEventListener('DOMContentLoaded', function() {
        const kodeWarnaInput = document.getElementById('kode_warna'); // Input untuk kode warna
        const warnaInput = document.getElementById('warna'); // Input untuk menampilkan warna
        const itemTypeSelect = document.getElementById('item_type'); // Dropdown untuk item_type

        // Event listener untuk input kode warna
        kodeWarnaInput.addEventListener('input', function() {
            const kodeWarna = this.value.trim(); // Mengambil nilai kode warna
            const itemType = itemTypeSelect.value; // Mengambil nilai item_type yang dipilih

            if (kodeWarna && itemType) {
                // Mengirim permintaan ke server dengan item_type dan kode_warna
                fetch('<?= base_url(session('role') . "/schedule/getWarna") ?>?item_type=' + itemType + '&kode_warna=' + kodeWarna)
                    .then(response => response.json())
                    .then(data => {
                        // Jika warna ditemukan
                        if (data && data.color) {
                            warnaInput.value = data.color; // Menampilkan warna yang ditemukan
                        } else {
                            warnaInput.value = 'Warna tidak ditemukan'; // Pesan jika warna tidak ditemukan
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching warna:', error);
                        warnaInput.value = 'Error fetching warna'; // Menampilkan pesan error jika ada masalah
                    });
            } else {
                warnaInput.value = ''; // Kosongkan input warna jika kode warna atau item_type kosong
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const itemType = document.getElementById("item_type");
        const kodeWarna = document.getElementById("kode_warna");
        const poTable = document.getElementById("poTable");
        const minCaps = document.getElementById('min_caps');
        const maxCaps = document.getElementById('max_caps');
        const sisaKapasitas = document.getElementById('sisa_kapasitas');
        const totalQtyCelup = document.getElementById('total_qty_celup');
        const sisaJatahElement = document.getElementById('sisa_jatah'); // Elemen untuk menampilkan sisa_jatah

        // Fungsi untuk memperbarui dropdown PO dan input delivery setelah memilih PO
        function updatePODropdown() {
            const itemTypeValue = itemType.value.trim();
            const kodeWarnaValue = kodeWarna.value.trim();

            // Iterasi setiap baris dalam tabel
            const rows = poTable.querySelectorAll("tbody tr");
            rows.forEach((row) => {
                const poSelect = row.querySelector(".po-select");
                const selectedValue = poSelect.value; // Simpan nilai yang dipilih sebelumnya

                poSelect.innerHTML = '<option value="">Pilih PO</option>'; // Reset dropdown

                // Pastikan itemType dan kodeWarna ada
                if (itemTypeValue && kodeWarnaValue) {
                    // Gunakan jQuery AJAX untuk fetch data PO
                    $.ajax({
                        url: '<?= base_url(session('role') . "/schedule/getPO") ?>',
                        type: 'GET',
                        data: {
                            item_type: itemTypeValue,
                            kode_warna: kodeWarnaValue
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.length) {
                                data.forEach((po) => {
                                    const option = document.createElement("option");
                                    option.value = po.id_order;
                                    option.textContent = po.no_model;
                                    poSelect.appendChild(option);
                                });

                                // Setelah data di-fetch, setel kembali nilai yang dipilih
                                if (selectedValue) {
                                    poSelect.value = selectedValue;
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching PO data:", error);
                        }
                    });
                }

                // Menambahkan event listener untuk perubahan pilihan pada dropdown PO
                poSelect.addEventListener("change", function() {
                    const poId = poSelect.value;
                    const deliveryAwalInput = row.querySelector("input[name='delivery_awal[]']");
                    const deliveryAkhirInput = row.querySelector("input[name='delivery_akhir[]']");
                    const startMcInput = row.querySelector("input[name='tgl_start_mc[]']");
                    const qtyPOInput = row.querySelector("input[name='qty_po[]']");
                    const kgKebutuhanElement = row.querySelector(".kg_kebutuhan"); // Gunakan class
                    const sisaJatahElement = row.querySelector(".sisa_jatah"); // Gunakan class

                    if (poId) {
                        // Fetch data untuk PO yang dipilih
                        $.ajax({
                            url: '<?= base_url(session('role') . "/schedule/getPODetails") ?>',
                            type: 'GET',
                            data: {
                                id_order: poId,
                                item_type: itemTypeValue,
                                kode_warna: kodeWarnaValue
                            },
                            dataType: 'json',
                            success: function(poDetails) {
                                if (poDetails) {
                                    // Update delivery_awal dan delivery_akhir berdasarkan data yang diterima
                                    if (poDetails.delivery_awal && poDetails.delivery_akhir) {
                                        deliveryAwalInput.value = poDetails.delivery_awal;
                                        deliveryAkhirInput.value = poDetails.delivery_akhir;
                                        startMcInput.value = poDetails.start_mesin;
                                    }

                                    // Update sisa_jatah dari response API
                                    if (poDetails.sisa_jatah !== undefined) {
                                        sisaJatahElement.textContent = parseFloat(poDetails.sisa_jatah).toFixed(2);
                                    }

                                    // Update kg_kebutuhan dari response API
                                    if (poDetails.kg_kebutuhan !== undefined) {
                                        kgKebutuhanElement.textContent = parseFloat(poDetails.kg_kebutuhan).toFixed(2);
                                    }
                                } else {
                                    console.log("Data PO tidak ditemukan");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error fetching PO details:", error);
                            }
                        });

                        // Fetch data qty PO
                        $.ajax({
                            url: '<?= base_url(session('role') . "/schedule/getQtyPO") ?>',
                            type: 'GET',
                            data: {
                                id_order: poId,
                                item_type: itemTypeValue,
                                kode_warna: kodeWarnaValue
                            },
                            dataType: 'json',
                            success: function(qtyPO) {
                                if (qtyPO) {
                                    // Update qty PO berdasarkan data yang diterima
                                    if (qtyPO.kgs) {
                                        qtyPOInput.value = parseFloat(qtyPO.kgs).toFixed(2);
                                    }
                                } else {
                                    console.log("Data Qty PO tidak ditemukan");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error fetching Qty PO:", error);
                            }
                        });
                    }
                });
            });
        }

        // Event listener untuk perubahan item_type dan kode_warna
        [itemType, kodeWarna].forEach((input) => {
            input.addEventListener("input", updatePODropdown);
        });

        // Initial qty_celup inputs
        let qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');

        // Event Listeners for initial qty_celup inputs
        qtyCelupInputs.forEach(input => {
            input.addEventListener('input', calculateCapacity);
        });

        // Function to calculate capacity
        function calculateCapacity() {
            const min = parseFloat(minCaps.value);
            const max = parseFloat(maxCaps.value);
            let total = 0;

            // Ambil nilai sisa_jatah dari baris yang aktif
            const sisaJatahElement = document.querySelector(".sisa_jatah"); // Gunakan class
            const sisaJatah = parseFloat(sisaJatahElement.textContent) || 0;

            // Calculate total qty celup
            qtyCelupInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // Update total qty celup
            totalQtyCelup.value = total;

            // Calculate remaining capacity
            const sisa = max - total;
            sisaKapasitas.value = sisa;

            // Check if total qty_celup exceeds sisa_jatah
            if (total > sisaJatah) {
                alert(`Total Qty Celup (${total.toFixed(2)} KG) melebihi Tagihan SCH (${sisaJatah.toFixed(2)} KG). Harap periksa kembali!`);
            }

            // Validation for each qty_celup input
            qtyCelupInputs.forEach(input => {
                const kg = parseFloat(input.value);
                if (kg > max) {
                    input.setCustomValidity(`Qty Celup Melebihi Kapasitas ${max}`);
                } else {
                    input.setCustomValidity('');
                }
            });
        }

        // Event Listeners for initial qty_celup inputs
        qtyCelupInputs.forEach(input => {
            input.addEventListener('input', calculateCapacity);
        });

        // Event listener untuk baris baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = poTable.querySelector("tbody");
            const newRow = `
    <tr>
        <td>
            <select class="form-select po-select" name="po[]" required>
                <option value="">Pilih PO</option>
            </select>
        </td>
        <td><input type="date" class="form-control" name="tgl_start_mc[]" readonly></td>
        <td><input type="date" class="form-control" name="delivery_awal[]" readonly></td>
        <td><input type="date" class="form-control" name="delivery_akhir[]" readonly></td>
        <td><input type="number" class="form-control" name="qty_po[]" readonly></td>
        <td><input type="number" class="form-control" name="qty_po_plus[]" readonly></td>
        <td class="text-center"><span class="badge bg-info"><span class="kg_kebutuhan">0.00</span> KG</span></td>
        <td class="text-center"><span class="badge bg-info"><span class="sisa_jatah">0.00</span> KG</span></td>
        <td><input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required></td>
        <td>
            <select class="form-select" name="po_plus[]" required>
                <option value="">Pilih PO(+)</option>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger removeRow">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
            tbody.insertAdjacentHTML("beforeend", newRow);
            updatePODropdown(); // Perbarui dropdown PO di baris baru
            // Re-query qty_celup inputs after adding a new row
            qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
            // Add event listener for new qty_celup input fields
            qtyCelupInputs.forEach(input => {
                input.addEventListener('input', calculateCapacity);
            });

            // Recalculate capacity
            calculateCapacity(); // Update capacity when a new row is added
        });

        // Event delegation untuk menghapus baris
        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow") || e.target.closest(".removeRow")) {
                e.target.closest("tr").remove();
                calculateCapacity(); // Recalculate capacity after row removal
            }
        });
    });

    $(document).ready(function() {
        $('#item_type').select2({
            placeholder: 'Pilih Item Type',
            allowClear: true,
            width: '100%' // Pastikan elemen Select2 responsif
        });


    });
</script>
<!-- <script>
    // DOM Elements
    document.addEventListener('DOMContentLoaded', function() {
        const minCaps = document.getElementById('min_caps');
        const maxCaps = document.getElementById('max_caps');
        const sisaKapasitas = document.getElementById('sisa_kapasitas');
        const qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
        const totalQtyCelup = document.getElementById('total_qty_celup');

        // Event Listeners
        qtyCelupInputs.forEach(input => {
            input.addEventListener('input', calculateCapacity);
        });

        // Functions
        function calculateCapacity() {
            const min = parseFloat(minCaps.value);
            const max = parseFloat(maxCaps.value);
            let total = 0;

            // Calculate total qty celup
            qtyCelupInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // Update total qty celup
            totalQtyCelup.value = total;

            // Calculate remaining capacity
            const sisa = max - total;
            sisaKapasitas.value = sisa;

            // Validation for each qty_celup input
            qtyCelupInputs.forEach(input => {
                const kg = parseFloat(input.value);
                if (kg < min || kg > max) {
                    input.setCustomValidity(`Qty Celup harus diantara ${min} dan ${max}`);
                } else {
                    input.setCustomValidity('');
                }
            });
        }
    });
</script> -->

<?php $this->endSection(); ?>