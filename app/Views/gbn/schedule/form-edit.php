<?php $this->extend($role . '/schedule/header'); ?>
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
                        <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $tanggal_schedule ?> | Lot Urut : <?= $lot_urut ?></h6>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url(session('role') . '/schedule/updateSchedule') ?>" method="post">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- No Mesin -->
                                <div class="form-group" id="noMesinGroup">
                                    <label for="no_mesin" class="form-label">No Mesin</label>
                                    <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly <?= $readonly ? 'readonly' : '' ?>>
                                    <input type="hidden" name="lot_urut" value="<?= $lot_urut ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Min Caps -->
                                <div class="form-group" id="minCapsGroup">
                                    <label for="min_caps" class="form-label">Minimum Kapasitas</label>
                                    <input type="number" class="form-control" name="min_caps" id="min_caps" value="<?= $min_caps ?>" required readonly <?= $readonly ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Max Caps -->
                                <div class="form-group" id="maxCapsGroup">
                                    <label for="max_caps" class="form-label">Maximum Kapasitas</label>
                                    <input type="number" class="form-control" name="max_caps" id="max_caps" value="<?= $max_caps ?>" required readonly <?= $readonly ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Sisa Kapasitas -->
                                <div class="form-group" id="sisaKapasitasGroup">
                                    <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas</label>
                                    <input type="number" class="form-control" name="sisa_kapasitas" id="sisa_kapasitas" value="" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Jenis Bahan Baku -->
                                <div class="form-group" id="jenisBahanBakuGroup">
                                    <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                    <select class="form-select" id="jenis_bahan_baku" name="jenis_bahan_baku" <?= $readonly ? 'readonly' : '' ?> required>
                                        <option value="">Pilih Jenis Bahan Baku</option>
                                        <?php foreach ($jenis_bahan_baku as $bahan): ?>
                                            <option value="<?= $bahan['jenis'] ?>" <?= $jenis == $bahan['jenis'] ? 'selected' : '' ?>><?= $bahan['jenis'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Item Type -->
                                <div class="form-group" id="itemTypeGroup">
                                    <label for="item_type">Item Type</label>
                                    <select class="form-select" name="item_type" id="item_type" <?= $readonly ? 'readonly' : '' ?> required>
                                        <option value="">Pilih Item Type</option>
                                        <?php foreach ($item_type as $option): ?>
                                            <option value="<?= $option['item_type'] ?>" <?= $item_type[0]['item_type'] == $option['item_type'] ? 'selected' : '' ?>><?= $option['item_type'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Kode Warna -->
                                <div class="form-group" id="kodeWarnaGroup">
                                    <label for="kode_warna">Kode Warna</label>
                                    <input type="text" class="form-control" name="kode_warna" id="kode_warna" value="<?= $kode_warna[0]['kode_warna'] ?>" required readonly <?= $readonly ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Warna -->
                                <div class="form-group" id="warnaGroup">
                                    <label for="warna" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="warna" name="warna" maxlength="32" value="<?= $warna[0]['warna'] ?>" readonly <?= $readonly ? 'readonly' : '' ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Tanggal Schedule -->
                                <div class="form-group" id="tanggalScheduleGroup">
                                    <label for="tanggal_schedule">Tanggal Schedule</label>
                                    <input type="date" class="form-control" name="tanggal_schedule" id="tanggal_schedule" value="<?= $tanggal_schedule ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Tanggal Aktual Celup -->
                                <div class="form-group" id="tanggalAktualCelupGroup">
                                    <label for="tanggal_celup">Tanggal Aktual Celup</label>
                                    <input type="date" class="form-control" name="tanggal_celup" id="tanggal_celup" value="<?= $tanggal_celup[0]['tanggal_celup'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Lot Celup -->
                                <div class="form-group" id="lotCelupGroup">
                                    <label for="lot_celup">Lot Celup</label>
                                    <input type="text" class="form-control" name="lot_celup" id="lot_celup"
                                        value="<?= $lot_celup[0]['lot_celup'] ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <!-- Lot Celup -->
                                <div class="form-group" id="ketDailyCekGroup">
                                    <label for="ket_daily_cek">Keterangan</label>
                                    <input type="text" class="form-control" name="ket_daily_cek" id="ket_daily_cek"
                                        value="<?= $ket_daily_cek[0]['ket_daily_cek'] ?>">
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
                                                <th class="text-center">Status</th>
                                                <th class="text-center">
                                                    <button type="button" class="btn btn-info" id="addRow">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($scheduleData as $index => $row): ?>
                                                <tr>
                                                    <td>
                                                        <select class="form-select po-select" name="po[<?= $index ?>]" <?= $readonly ? 'readonly' : '' ?> required>
                                                            <option value="">Pilih PO</option>
                                                            <?php foreach ($po as $option): ?>
                                                                <option value="<?= $option['id_order'] ?>" <?= $row['no_model'] == $option['no_model'] ? 'selected' : '' ?>><?= $option['no_model'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php if ($readonly): ?>
                                                            <input type="hidden" name="po[<?= $index ?>]" value="<?= $row['no_model'] ?>">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control start_mc" name="start_mc[<?= $index ?>]" value="<?= $row['start_mc'] ?>" required <?= $readonly ? 'readonly' : '' ?>>
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control delivery_awal" name="delivery_awal[<?= $index ?>]" value="<?= $row['delivery_awal'] ?>" required <?= $readonly ? 'readonly' : '' ?>>
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control delivery_akhir" name="delivery_akhir[<?= $index ?>]" value="<?= $row['delivery_akhir'] ?>" required <?= $readonly ? 'readonly' : '' ?>>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty_po" name="qty_po[<?= $index ?>]" value="<?= number_format((float)$row['qty_po'], 2, '.', '') ?>" required <?= $readonly ? 'readonly' : '' ?>>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control qty_po_plus" name="qty_po_plus[<?= $index ?>]" value="" required readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control qty_celup" name="qty_celup[<?= $index ?>]" value="<?= $row['kg_celup'] ?>" required>
                                                    </td>
                                                    <td>
                                                        <select class="form-select po_plus" name="po_plus[<?= $index ?>]" required>
                                                            <option value="">Pilih PO(+)</option>
                                                            <option value="1" <?= $row['po_plus'] == '1' ? 'selected' : '' ?>>Iya</option>
                                                            <option value="0" <?= $row['po_plus'] == '0' ? 'selected' : '' ?>>Bukan</option>
                                                        </select>
                                                        <input type="hidden" name="id_celup[<?= $index ?>]" value="<?= $row['id_celup'] ?>">
                                                    </td>

                                                    <td>
                                                        <!-- <label class="badge bg-<?= $row['last_status'] == 'sceduled' ? 'success' : 'info' ?>"><?= $row['last_status'] ?></label> -->
                                                        <input type="text" class="form-control last_status" name="last_status[<?= $index ?>]" value="<?= $row['last_status'] ?>" readonly>
                                                    </td>

                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger removeRow">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <!-- <pre>
                                            <?= print_r($scheduleData) ?>
                                        </pre> -->
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
            // console.log(itemTypeValue, kodeWarnaValue);

            // Iterasi setiap baris dalam tabel
            const rows = poTable.querySelectorAll("tbody tr");

            // console.log(rows);
            rows.forEach((row) => {
                const poSelect = row.querySelector(".po-select");
                const selectedValue = poSelect.value; // Simpan nilai yang dipilih sebelumnya

                // poSelect.innerHTML = '<option value="">Pilih PO</option>'; // Reset dropdown

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
                            // console.log(data);
                            if (data.length) {
                                data.forEach((po) => {
                                    const option = document.createElement("option");
                                    option.value = po.no_model;
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

                // Update delivery_awal dan delivery_akhir
                const startMcInput = row.querySelector(".start_mc");
                const deliveryAwalInput = row.querySelector(".delivery_awal");
                const deliveryAkhirInput = row.querySelector(".delivery_akhir");

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

        // Hitung kapasitas
        function calculateCapacity() {
            const min = parseFloat(minCaps.value) || 0;
            const max = parseFloat(maxCaps.value) || 0;

            let total = 0;
            const qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
            const lastStatusInputs = document.querySelectorAll('input[name="last_status[]"]'); // Ambil last_status berdasarkan index

            // Loop melalui setiap row dan pastikan hanya qty_celup yang statusnya bukan 'done' yang dihitung
            qtyCelupInputs.forEach((input) => {
                const row = input.closest("tr"); // Mendapatkan baris terdekat
                const lastStatus = row.querySelector('input[name^="last_status"]').value; // Mendapatkan status di baris tersebut
                console.log(lastStatus);
                if (lastStatus !== 'done') {
                    total += parseFloat(input.value) || 0; // Jika status bukan 'done', tambahkan qty_celup ke total
                }
            });

            // Update total qty celup
            totalQtyCelup.value = total.toFixed(2); // Update dengan 2 angka di belakang koma
            console.log(total);
            // Hitung sisa kapasitas
            const sisa = max - total;
            sisaKapasitas.value = sisa.toFixed(2); // Update dengan 2 angka di belakang koma

            // Validasi qty_celup
            qtyCelupInputs.forEach(input => {
                const kg = parseFloat(input.value);
                if (kg > max) {
                    input.setCustomValidity(`Qty Celup Melebihi Kapasitas ${max}`);
                } else {
                    input.setCustomValidity('');
                }
            });
        }

        // Pastikan elemen sudah ada sebelum memanggil fungsi
        calculateCapacity();

        // Menambahkan event listener untuk input qty_celup
        poTable.addEventListener("input", function(e) {
            if (e.target.classList.contains("qty_celup")) {
                calculateCapacity();
            }
        });

        // Event listener untuk menambah baris baru
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
                            <td><input type="number" step="0.01" min="0.01" class="form-control qty_celup" name="qty_celup[]" required></td>
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

            // Re-query qty_celup inputs dan tambahkan event listener baru
            const qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
            qtyCelupInputs.forEach(input => {
                input.addEventListener('input', calculateCapacity);
            });

            // Recalculate capacity and update PO dropdown for new row
            updatePODropdown();
            calculateCapacity();
        });

        // Event delegation untuk menghapus baris
        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow") || e.target.closest(".removeRow")) {
                e.target.closest("tr").remove();
                calculateCapacity(); // Recalculate capacity after row removal
            }
        });
        // Since itemType and kodeWarna are not editable in form edit, we don't add event listeners for changes
        // Instead, we just call updatePODropdown once to populate dropdown options based on initial values
        updatePODropdown();
    });
</script>

<?php $this->endSection(); ?>