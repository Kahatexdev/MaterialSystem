<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Schedule Celup</h3>
                    <div class="card-tools">
                        <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $scheduleData['tanggal_schedule'] ?> | Lot Urut : <?= $scheduleData['lot_urut'] ?></h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url(session('role') . '/schedule/updateSchedule/' . $scheduleData['id_celup']) ?>" method="post">
                        <input type="hidden" name="id_schedule" value="<?= $scheduleData['id_celup'] ?>">
                        <input type="hidden" name="tanggal_schedule" value="<?= $scheduleData['tanggal_schedule'] ?>">
                        <input type="hidden" name="lot_urut" value="<?= $scheduleData['lot_urut'] ?>">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="no_mesin" class="form-label">No Mesin</label>
                                    <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="min_caps" class="form-label">Min Caps</label>
                                    <input type="number" class="form-control" id="min_caps" name="min_caps" value="<?= $min_caps ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="max_caps" class="form-label">Max Caps</label>
                                    <input type="number" class="form-control" id="max_caps" name="max_caps" value="<?= $max_caps ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas</label>
                                    <input type="number" class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                    <select class="form-select select2" id="jenis_bahan_baku" name="jenis_bahan_baku" required readonly>
                                        <option value="">Pilih Jenis Bahan Baku</option>
                                        <?php foreach ($jenis_bahan_baku as $bahan): ?>
                                            <option value="<?= $bahan['jenis'] ?>" <?= $jenis == $bahan['jenis'] ? 'selected' : '' ?>><?= $bahan['jenis'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Item Type</label>
                                    <select class="form-select select2" id="item_type" name="item_type" required readonly>
                                        <option value="">Pilih Item Type</option>
                                        <?php foreach ($item_type as $type): ?>
                                            <option value="<?= $type['item_type'] ?>" <?= $scheduleData['item_type'] == $type['item_type'] ? 'selected' : '' ?>><?= $type['item_type'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="kode_warna" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="kode_warna" name="kode_warna" value="<?= $scheduleData['kode_warna'] ?>" maxlength="32" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="warna" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="warna" name="warna" value="<?= $scheduleData['warna'] ?>" maxlength="32" readonly>
                                </div>
                            </div>
                        </div>

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
                                                <th class="text-center">Qty Celup</th>
                                                <th class="text-center">PO(+)</th>
                                                <th class="text-center">
                                                    <button type="button" class="btn btn-info" id="addRow">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically populated rows -->
                                            <tr>
                                                <td>
                                                    <select class="form-select po" name="po[]" id="po" required readonly>
                                                        <option value="">Pilih PO</option>
                                                        <?php foreach ($poData as $option): ?>
                                                            <option value="<?= $option['no_model'] ?>" <?= $scheduleData['no_model'] == $option['no_model'] ? 'selected' : '' ?>><?= $option['no_model'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control start_mc" name="start_mc[]" value="<?= $scheduleData['start_mc'] ?>" id="start_mc" required readonly>
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control delivery_awal" name="delivery_awal[]" value="" id="delivery_awal" required readonly>
                                                </td>
                                                <td>
                                                    <input type="date" class="form-control delivery_akhir" name="delivery_akhir[]" value="" id="delivery_akhir" required readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control qty_po" name="qty_po[]" value="" id="qty_po" required readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control qty_po_plus" name="qty_po_plus[]" value="" id="qty_po_plus" required readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control qty_celup" name="qty_celup[]" value="<?= $scheduleData['kg_celup'] ?>" id="qty_celup" required>
                                                </td>
                                                <td>
                                                    <select class="form-select po_plus" name="po_plus[]" id="po_plus" required>
                                                        <option value="">Pilih PO(+)</option>
                                                        <option value="1">Iya</option>
                                                        <option value="0">Bukan</option>
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
                                                <td class="text-center"><strong>Total Qty Celup</strong></td>
                                                <td colspan="8" class="text-center">
                                                    <input type="number" class="form-control" id="total_qty_celup" name="total_qty_celup" value="0" readonly>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-info w-100">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const itemType = document.getElementById("item_type");
        const kodeWarna = document.getElementById("kode_warna");
        const poTable = document.getElementById("poTable");
        const minCaps = document.getElementById('min_caps');
        const maxCaps = document.getElementById('max_caps');
        const sisaKapasitas = document.getElementById('sisa_kapasitas');
        const totalQtyCelup = document.getElementById('total_qty_celup');

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
                    const qtyPOInput = row.querySelector("input[name='qty_po[]']");

                    if (poId) {
                        // Fetch data untuk PO yang dipilih
                        $.ajax({
                            url: '<?= base_url(session('role') . "/schedule/getPODetails") ?>', // Ganti dengan URL yang sesuai untuk mengambil detail PO
                            type: 'GET',
                            data: {
                                id_order: poId
                            },
                            dataType: 'json',
                            success: function(poDetails) {
                                if (poDetails) {
                                    // Update delivery_awal dan delivery_akhir berdasarkan data yang diterima
                                    if (poDetails.delivery_awal && poDetails.delivery_akhir) {
                                        deliveryAwalInput.value = poDetails.delivery_awal;
                                        deliveryAkhirInput.value = poDetails.delivery_akhir;
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
                if (kg > max) {
                    input.setCustomValidity(`Qty Celup Melebihi Kapasitas ${max}`);
                } else {
                    input.setCustomValidity('');
                }
            });
        }

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
                <td><input type="number" class="form-control" name="qty_celup[]" required></td>
                <td>
                    <select class="form-select" name="po_plus[]" required>
                        <option value="0">Pilih PO(+)</option>
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
</script>

<?php $this->endSection(); ?>