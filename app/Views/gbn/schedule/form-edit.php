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
                                                <th class="text-center">Kg Kebutuhan</th>
                                                <th class="text-center">Tagihan Schedule</th>
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
                                                <tr data-status="<?= $row['last_status'] ?>">
                                                    <td>
                                                        <select class="form-select po-select" name="po[<?= $index ?>]" <?= $readonly ? 'readonly' : '' ?> required>
                                                            <option value="">Pilih PO</option>
                                                            <?php foreach ($po as $option): ?>
                                                                <option value="<?= $row['no_model'] ?>" <?= $row['no_model'] == $option['no_model'] ? 'selected' : '' ?>><?= $option['no_model'] ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <?php if ($readonly): ?>
                                                            <input type="hidden" name="po[<?= $index ?>]" value="<?= $row['no_model'] ?>">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control start_mc" name="start_mc[<?= $index ?>]" value="<?= $start_mc ?>" required <?= $readonly ? 'readonly' : '' ?>>
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
                                                        <input type="number" class="form-control kg_kebutuhan" name="kg_kebutuhan[<?= $index ?>]" value="<?= $row['kg_kebutuhan'] ?? 0 ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control tagihan_schedule" name="tagihan_schedule[<?= $index ?>]" value="<?= $row['tagihan_schedule'] ?? 0 ?>" readonly>
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
                                                        <input type="hidden" name="tanggal_schedule" value="<?= $row['tanggal_schedule'] ?>">
                                                        <input type="hidden" name="item_type[<?= $index ?>]" value="<?= $row['item_type'] ?>">
                                                        <input type="hidden" name="kode_warna[<?= $index ?>]" value="<?= $row['kode_warna'] ?>">
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-<?= $row['last_status'] == 'scheduled' ? 'info' : ($row['last_status'] == 'celup' ? 'warning' : 'success') ?>"><?= $row['last_status'] ?></span>
                                                        <input type="hidden" class="form-control last_status" name="last_status[<?= $index ?>]" value="<?= $row['last_status'] ?>" ?>
                                                    </td>

                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-info editRow" data-toggle="modal" data-target="#editModal" data-id="<?= $row['id_celup'] ?>" data-tanggalSchedule="<?= $row['tanggal_schedule'] ?>" data-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-calendar"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger removeRow">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="text-center"><strong>Total Qty Celup</strong></td>
                                                <td colspan="4" class="text-center">
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

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Tanggal Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_celup" id="idCelup">
                    <div class="form-group mb-3">
                        <label for="tanggal_schedule" class="form-label">Tanggal Schedule</label>
                        <input type="date" class="form-control" id="tanggal_schedule" name="tanggal_schedule" value="<?= $tanggal_schedule ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Simpan</button>
                </div>
            </form>
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
        // const tglSchedule = document.getElementById('tanggal_schedule');

        // Loop melalui semua baris tabel
        document.querySelectorAll('tr[data-status]').forEach(function(row) {
            const status = row.getAttribute('data-status');
            // Jika status adalah 'celup', 'done', atau 'sent', buat semua input disabled
            if (['celup', 'done', 'sent'].includes(status)) {
                row.querySelectorAll('input, select, button').forEach(function(input) {
                    input.setAttribute('readonly', true); // Untuk input teks
                    input.setAttribute('disabled', true); // Untuk tombol dan select
                });
            }
        });

        // Event listener untuk tombol edit
        document.querySelectorAll('.editRow').forEach(function(button) {
            button.addEventListener('click', function() {
                const idCelup = button.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                const modalTglSchedule = document.getElementById('tanggal_schedule');
                const modalIdCelup = document.getElementById('idCelup');

                // Set value id_celup pada modal
                modalIdCelup.value = idCelup;

                // Set value status pada modal
                modalTglSchedule.value = button.getAttribute('data-tanggalSchedule');

                modal.show();
            });
        });

        // ajax update tanggal schedule
        $('#editModal form').submit(function(e) {
            e.preventDefault();
            const idCelup = $('#idCelup').val();
            const tanggalSchedule = $('#tanggal_schedule').val();

            $.ajax({
                url: '<?= base_url(session('role') . '/schedule/updateTglSchedule') ?>',
                type: 'POST',
                data: {
                    id_celup: idCelup,
                    tanggal_schedule: tanggalSchedule,
                    no_mesin: $('#no_mesin').val(),
                    lot_urut: $('#lot_urut').val()
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        alert('Tanggal Schedule berhasil diubah');
                        location.reload();
                    } else {
                        alert('Gagal mengubah tanggal schedule');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        // Fungsi untuk memperbarui dropdown PO dan input delivery setelah memilih PO
        function updatePODropdown() {
            const itemTypeValue = itemType.value.trim();
            const kodeWarnaValue = kodeWarna.value.trim();

            // Iterasi setiap baris dalam tabel
            const rows = document.querySelectorAll("#poTable tbody tr");

            rows.forEach((row) => {
                const poSelect = row.querySelector(".po-select");
                const startMcInput = row.querySelector(".start_mc");
                const deliveryAwalInput = row.querySelector(".delivery_awal");
                const deliveryAkhirInput = row.querySelector(".delivery_akhir");
                const qtyPOInput = row.querySelector(".qty_po");
                const kgKebutuhanInput = row.querySelector(".kg_kebutuhan");
                const tagihanSchInput = row.querySelector(".tagihan_schedule");

                // Tambahkan event listener untuk dropdown PO
                poSelect.addEventListener("change", function() {
                    const poId = poSelect.value;

                    if (poId) {
                        // Fetch data untuk PO yang dipilih
                        $.ajax({
                            url: '<?= base_url(session("role") . "/schedule/getPODetails") ?>',
                            type: 'GET',
                            data: {
                                id_order: poId,
                                itemType: itemTypeValue,
                                kodeWarna: kodeWarnaValue
                            },
                            dataType: 'json',
                            success: function(poDetails) {
                                console.log('PO Details:', poDetails);
                                if (poDetails) {
                                    startMcInput.value = poDetails.start_mesin || "";
                                    deliveryAwalInput.value = poDetails.delivery_awal || "";
                                    deliveryAkhirInput.value = poDetails.delivery_akhir || "";
                                    kgKebutuhanInput.value = parseFloat(poDetails.kg_kebutuhan || 0).toFixed(2);

                                    // Hitung tagihan schedule setelah data tersedia
                                    const kgKebutuhan = parseFloat(poDetails.kg_kebutuhan || 0);
                                    const qtyCelup = parseFloat(row.querySelector('input[name^="qty_celup["]').value) || 0;
                                    const tagihanSch = kgKebutuhan - qtyCelup;

                                    tagihanSchInput.value = tagihanSch.toFixed(2);

                                    console.log('kg kebutuhan:', kgKebutuhan);
                                    console.log('qty celup:', qtyCelup);
                                    console.log('tagihan schedule:', tagihanSch);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error fetching PO details:", error);
                            }
                        });

                        // Fetch data qty PO
                        $.ajax({
                            url: '<?= base_url(session("role") . "/schedule/getQtyPO") ?>',
                            type: 'GET',
                            data: {
                                id_order: poId,
                                item_type: itemTypeValue,
                                kode_warna: kodeWarnaValue,
                            },
                            dataType: 'json',
                            success: function(qtyPO) {
                                if (qtyPO) {
                                    qtyPOInput.value = parseFloat(qtyPO.kgs || 0).toFixed(2);
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



        // Hitung sisa kapasitas
        function calculateCapacity() {
            const max = parseFloat(maxCaps.value) || 0;
            let total = 0;

            const rows = poTable.querySelectorAll("tbody tr");

            rows.forEach((row, index) => {
                const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                const lastStatusInput = row.querySelector('input[name^="last_status["]');

                if (qtyCelupInput && lastStatusInput) {
                    const qtyCelup = parseFloat(qtyCelupInput.value) || 0;
                    const lastStatus = lastStatusInput.value;

                    if (lastStatus === 'scheduled' || lastStatus === 'celup' || lastStatus === 'reschedule') {
                        total += qtyCelup;
                    }
                } else {
                    console.warn(`Input qty_celup atau last_status tidak ditemukan di baris ke-${index}`);
                }
            });

            totalQtyCelup.value = total.toFixed(2); // Total qty celup
            const sisa = max - total;
            sisaKapasitas.value = sisa.toFixed(2); // Sisa kapasitas

            // Debugging log
            console.log(`Max Capacity: ${max}, Total: ${total}, Remaining: ${sisa}`);

            // Jika sisa kapasitas < 0, beri peringatan
            if (sisa < 0) {
                alert('Sisa kapasitas tidak mencukupi!');
            }

            validateQtyCelup(max); // Validasi untuk qty celup
            validateSisaJatah(); // Validasi sisa jatah
        }

        // Validasi qty_celup untuk setiap input
        function validateQtyCelup(max) {
            const qtyCelupInputs = document.querySelectorAll('input[name="qty_celup[]"]');
            let isOverCapacity = false;

            qtyCelupInputs.forEach(input => {
                const kg = parseFloat(input.value) || 0;

                if (kg > max) {
                    input.setCustomValidity(`Qty Celup Melebihi Kapasitas ${max}`);
                    isOverCapacity = true;
                } else if (parseFloat(sisaKapasitas.value) < 0) {
                    input.setCustomValidity(`Sisa Kapasitas Tidak Mencukupi`);
                    isOverCapacity = true;
                } else {
                    input.setCustomValidity('');
                }
            });

            // Jika over capacity, tampilkan pesan
            if (isOverCapacity) {
                alert('Sisa kapasitas tidak mencukupi atau melebihi kapasitas maksimum.');
            }
        }

        // Event Listener untuk Input `qty_celup`
        poTable.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty_celup')) {
                const row = e.target.closest('tr');
                validateSisaJatah(); // Validasi sisa jatah setelah input qty_celup
                calculateCapacity(); // Hitung ulang kapasitas setelah validasi
            }
        });

        function validateSisaJatah() {
            const rows = poTable.querySelectorAll("tbody tr");
            let isValid = true;

            // Collect all necessary data to send in one request
            let requestData = [];

            rows.forEach((row) => {
                const noModelInput = row.querySelector('input[name^="po["]');
                const itemTypeInput = row.querySelector('input[name^="item_type["]');
                const kodeWarnaInput = row.querySelector('input[name^="kode_warna["]');
                const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                const currentQtyCelupInput = row.querySelector('input[name^="current_qty_celup["]'); // Hidden input

                if (noModelInput && itemTypeInput && kodeWarnaInput && qtyCelupInput) {
                    const noModel = noModelInput.value;
                    const itemType = itemTypeInput.value;
                    const kodeWarna = kodeWarnaInput.value;
                    const qtyCelup = parseFloat(qtyCelupInput.value) || 0;
                    const currentQtyCelup = parseFloat(currentQtyCelupInput?.value) || 0;

                    // Collect the data to send
                    requestData.push({
                        no_model: noModel,
                        item_type: itemType,
                        kode_warna: kodeWarna,
                        qty_celup: qtyCelup ?? 0,
                        current_qty_celup: currentQtyCelup
                    });
                } else {
                    console.warn('Data tidak lengkap untuk validasi.', row);
                    isValid = false;
                }
            });

            // if (!isValid) {
            //     alert('Ada baris data yang tidak lengkap untuk validasi.');
            //     return;
            // }

            // Send a single AJAX request for all rows
            $.ajax({
                url: '<?= base_url("/schedule/validateSisaJatah") ?>',
                type: 'POST',
                data: {
                    rows: requestData
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.success) {
                        // Show validation error for each row
                        response.errors.forEach((error, index) => {
                            const qtyCelupInput = rows[index].querySelector('input[name^="qty_celup["]');
                            qtyCelupInput.setCustomValidity(error.message);
                            alert(error.message);
                        });
                    } else {
                        // If successful, reset custom validity for all rows
                        rows.forEach((row) => {
                            const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                            qtyCelupInput.setCustomValidity('');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memvalidasi sisa jatah.');
                }
            });
        }




        // Event listener untuk menambah baris baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = document.querySelector("#poTable tbody");
            const newIndex = tbody.querySelectorAll("tr").length;

            const newRow = `
        <tr>
            <td>
                <select class="form-select po-select" name="po-select[${newIndex}]" required>
                    <option value="">Pilih PO</option>
                    <?php foreach ($po as $option): ?>
                        <option value="<?= $option['id_order'] ?>"><?= $option['no_model'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="po[${newIndex}]" value="">
            </td>
            <td><input type="date" class="form-control start_mc" name="start_mc[${newIndex}]" readonly></td>
            <td><input type="date" class="form-control delivery_awal" name="delivery_awal[${newIndex}]" readonly></td>
            <td><input type="date" class="form-control delivery_akhir" name="delivery_akhir[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control qty_po" name="qty_po[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control qty_po_plus" name="qty_po_plus[${newIndex}]" required readonly></td>
            <td><input type="number" class="form-control kg_kebutuhan" name="kg_kebutuhan[${newIndex}]" readonly></td>
            <td><input type="number" class="form-control tagihan_schedule" name="tagihan_schedule[${newIndex}]" readonly></td>
            <td><input type="number" step="0.01" class="form-control qty_celup" name="qty_celup[${newIndex}]" required></td>
            <td>
                <select class="form-select po_plus" name="po_plus[${newIndex}]" required>
                    <option value="">Pilih PO(+)</option>
                    <option value="1">Iya</option>
                    <option value="0">Bukan</option>
                </select>
            </td>
            <td>
                <span class="badge bg-info">scheduled</span>
                <input type="hidden" class="form-control last_status" name="last_status[${newIndex}]" value="scheduled" required readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;

            tbody.insertAdjacentHTML("beforeend", newRow);

            // Ambil elemen select yang baru ditambahkan
            const newSelect = tbody.querySelector(`select[name="po-select[${newIndex}]"]`);

            // Event listener untuk mengatur PO
            newSelect.addEventListener('change', function() {
                const selectedValue = newSelect.value; // Ambil value yang dipilih

                // Ambil `no_model` berdasarkan `id_order`
                $.ajax({
                    url: '<?= base_url(session("role") . "/schedule/getNoModel") ?>',
                    type: 'GET',
                    data: {
                        id_order: selectedValue
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('No Model:', response.no_model);
                        newSelect.nextElementSibling.value = response.no_model;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching No Model:", error);
                    },
                });
            });

            // Perbarui dropdown dan event listener pada baris baru
            calculateCapacity();
            updatePODropdown();
        });


        // Event listener untuk menghapus baris
        document.querySelector("#poTable").addEventListener("click", function(event) {
            if (event.target.closest(".removeRow")) {
                const row = event.target.closest("tr");

                // Validasi jika ada input ID Celup pada baris
                const idCelupInput = row.querySelector('input[name^="id_celup["]');
                const idCelup = idCelupInput ? idCelupInput.value : null;

                if (idCelup) {
                    // SweetAlert konfirmasi sebelum menghapus
                    Swal.fire({
                        title: "Apakah Anda Yakin?",
                        text: "Data Schedule akan dihapus",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal",
                        dangerMode: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim permintaan AJAX untuk menghapus data
                            $.ajax({
                                url: '<?= base_url(session('role') . '/schedule/deleteSchedule') ?>',
                                type: 'POST',
                                data: {
                                    id_celup: idCelup
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire("Berhasil!", "Data Schedule berhasil dihapus.", "success").then(() => {
                                            row.remove();
                                            calculateCapacity();
                                        });
                                    } else {
                                        Swal.fire("Gagal!", "Data Schedule gagal dihapus.", "error");
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                                },
                            });
                        }
                    });
                } else {
                    // Jika id_celup tidak ditemukan atau null, cukup hapus barisnya saja
                    Swal.fire({
                        title: "Hapus Baris?",
                        text: "Baris ini akan dihapus tanpa mengirim data ke server.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal",
                        dangerMode: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            calculateCapacity();
                        }
                    });
                }
            }
        });

        // Panggil fungsi untuk memperbarui dropdown PO dan event listener pada baris yang sudah ada
        updatePODropdown();
        calculateCapacity();
    });
</script>

<?php $this->endSection(); ?>