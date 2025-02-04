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

    .locked-input {
        pointer-events: none;
        background-color: #e9ecef;
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
                    <div class="row">
                        <form action="<?= base_url(session('role') . '/schedule/updateSchedule') ?>" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>
                                        <input type="hidden" name="tanggal_schedule" value="<?= $tanggal_schedule ?>">
                                        <input type="hidden" name="lot_urut" value="<?= $lot_urut ?>">
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
                                        <input type="number" class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="<?= $max_caps ?>" data-max-caps="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                        <input type="text" class="form-control" id="jenis_bahan_baku" name="jenis_bahan_baku" value="<?= $jenis ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" value="<?= $kode_warna[0]['kode_warna'] ?>" required readonly>
                                        <div id="suggestionsKWarna" class="suggestions-box" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" value="<?= $warna[0]['warna'] ?>" maxlength="32" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table id="poTable" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Item Type</th>
                                                        <th class="text-center">PO</th>
                                                        <th class="text-center">Tgl Start MC</th>
                                                        <th class="text-center">Delivery Awal</th>
                                                        <th class="text-center">Delivery Akhir</th>
                                                        <th class="text-center">Qty PO</th>
                                                        <th class="text-center">Qty PO(+)</th>
                                                        <th class="text-center">Kg Kebutuhan</th>
                                                        <th class="text-center">Tagihan SCH</th>
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
                                                    <?php foreach ($scheduleData as $detail): ?>
                                                        <tr>
                                                            <td>
                                                                <select class="form-select item-type" name="item_type[]" required>
                                                                    <option value="">Pilih Item Type</option>
                                                                    <?php foreach ($scheduleData as $item): ?>
                                                                        <option value="<?= $item['item_type'] ?>" <?= ($item['item_type'] == $detail['item_type']) ? 'selected' : '' ?>><?= $item['item_type'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <input type="hidden" name="id_celup[]" value="<?= $detail['id_celup'] ?>">
                                                            </td>
                                                            <td>
                                                                <select class="form-select po-select" name="po[]" required>
                                                                    <option value="">Pilih PO</option>
                                                                    <?php foreach ($scheduleData as $po): ?>
                                                                        <option value="<?= $po['id_order'] ?>" <?= ($po['id_order'] == $detail['id_order']) ? 'selected' : '' ?>><?= $po['no_model'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </td>
                                                            <td><input type="date" class="form-control" name="tgl_start_mc[]" value="<?= $detail['start_mc'] ?>" readonly></td>
                                                            <td><input type="date" class="form-control" name="delivery_awal[]" value="<?= $detail['delivery_awal'] ?>" readonly></td>
                                                            <td><input type="date" class="form-control" name="delivery_akhir[]" value="<?= $detail['delivery_akhir'] ?>" readonly></td>
                                                            <td><input type="number" class="form-control" name="qty_po[]" value="<?= $detail['qty_po'] ?>" readonly></td>
                                                            <td><input type="number" class="form-control" name="qty_po_plus[]" value="" readonly></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info">
                                                                    <span class="kg_kebutuhan"><?= $kg_kebutuhan ?></span> KG
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info">
                                                                    <span class="sisa_jatah"><?= $sisa_jatah ?></span> KG
                                                                </span>
                                                            </td>
                                                            <td><input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" value="<?= $detail['kg_celup'] ?>" required></td>
                                                            <td>
                                                                <select class="form-select" name="po_plus[]" required>
                                                                    <option value="">Pilih PO(+)</option>
                                                                    <option value="1" <?= ($detail['po_plus'] == 1) ? 'selected' : '' ?>>Ya</option>
                                                                    <option value="0" <?= ($detail['po_plus'] == 0) ? 'selected' : '' ?>>Tidak</option>
                                                                </select>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-<?= $detail['last_status'] == 'scheduled' ? 'info' : ($detail['last_status'] == 'celup' ? 'warning' : 'success') ?>"><?= $detail['last_status'] ?></span>
                                                                <input type="hidden" class="form-control last_status" name="last_status[]" value="<?= $detail['last_status'] ?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-danger removeRow">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-center">
                                                            <strong>Total Qty Celup</strong>
                                                        </td>
                                                        <td colspan="8" class="text-center">
                                                            <input type="number" class="form-control" id="total_qty_celup" name="total_qty_celup" value="" readonly>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-info w-100">Update Jadwal</button>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const kodeWarna = document.getElementById('kode_warna');
        const suggestionsBoxKWarna = document.getElementById('suggestionsKWarna');
        const warnaInput = document.getElementById('warna');
        const poTable = document.getElementById("poTable");


        // buatlah semua input dan select jadi readonly jika last_status 'celup', 'done', 'sent'
        const lastStatuses = document.querySelectorAll('.last_status');
        lastStatuses.forEach(status => {
            if (status.value === 'celup' || status.value === 'done' || status.value === 'sent') {
                const row = status.closest('tr');
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.classList.add('locked-input');
                });
            }
        });



        // ✅ Event delegation untuk kode_warna input
        kodeWarna.addEventListener('input', function() {
            const query = kodeWarna.value.trim();
            if (query.length >= 3) {
                fetchData('getKodeWarna', {
                    query
                }, displayKodeWarnaSuggestions);
                fetchData('getWarna', {
                    kode_warna: query
                }, (data) => {
                    if (data.length > 0) {
                        warnaInput.value = data[0].color;
                        fetchItemType(query, data[0].color);
                    }
                });
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        });

        // ✅ Fungsi utilitas untuk fetching data
        function fetchData(endpoint, params, callback) {
            const url = new URL(`<?= base_url(session('role') . "/schedule/") ?>${endpoint}`);
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(callback)
                .catch(error => console.error(`Error fetching ${endpoint}:`, error));
        }

        // ✅ Fungsi menampilkan saran kode warna
        function displayKodeWarnaSuggestions(suggestions) {
            suggestionsBoxKWarna.innerHTML = '';
            if (suggestions.length > 0) {
                suggestionsBoxKWarna.style.display = 'block';
                suggestions.forEach(suggestion => {
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.textContent = suggestion;
                    suggestionDiv.addEventListener('click', () => {
                        kodeWarna.value = suggestion;
                        suggestionsBoxKWarna.style.display = 'none';
                    });
                    suggestionsBoxKWarna.appendChild(suggestionDiv);
                });
            } else {
                suggestionsBoxKWarna.style.display = 'none';
            }
        }

        // ✅ Fungsi fetch item type
        function fetchItemType(kodeWarna, warna, targetSelect) {
            fetchData('getItemType', {
                kode_warna: kodeWarna,
                warna
            }, (data) => {
                if (targetSelect) {
                    populateSelect(targetSelect, data, 'item_type', 'item_type');
                }
            });
        }

        // ✅ Fungsi utilitas untuk mengisi select dropdown
        function populateSelect(selectElement, data, valueKey, textKey) {
            selectElement.innerHTML = '<option value="">Pilih ' + textKey + '</option>';
            if (data.length > 0) {
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    selectElement.appendChild(option);
                });
            } else {
                selectElement.innerHTML = '<option value="">Tidak ada data</option>';
            }
        }

        // ✅ Fungsi fetch PO by kode warna, warna, dan item type
        function fetchPOByKodeWarna(kodeWarna, warna, itemType, poSelect) {
            fetchData('getPO', {
                kode_warna: kodeWarna,
                warna,
                item_type: itemType
            }, (data) => {
                populateSelect(poSelect, data, 'id_order', 'no_model');

                // ✅ Update sisa_jatah dari data yang diterima
                const row = poSelect.closest("tr");
                if (row && data.length > 0) {
                    row.querySelector(".sisa_jatah").textContent = data[0].sisa_jatah.toFixed(2);
                }
            });
        }

        // ✅ Event delegation untuk input qty_celup
        poTable.addEventListener("input", function(e) {
            if (e.target.name === "qty_celup[]") {
                const row = e.target.closest("tr");

                const qtyCelup = parseFloat(row.querySelector("input[name='qty_celup[]']").value) || 0;
                const kgKebutuhan = parseFloat(row.querySelector(".kg_kebutuhan").textContent) || 0;
                const sisaJatah = kgKebutuhan - qtyCelup; // Menghitung sisa_jatah

                // Update sisa_jatah di tampilan
                row.querySelector(".sisa_jatah").textContent = sisaJatah.toFixed(2);

                // Menyimpan nilai tagihan SCH di row jika diperlukan (optional, jika ingin diakses oleh baris lain)
                row.dataset.sisaJatah = sisaJatah.toFixed(2); // Menyimpan nilai sisa_jatah dalam data attribute

                // Hitung total qty celup dan sisa kapasitas
                calculateTotalAndRemainingCapacity();
            }
        });

        function calculateTotalAndRemainingCapacity() {
            let totalQtyCelup = 0;
            const poTable = document.getElementById("poTable");
            if (!poTable) {
                console.warn("⚠️ poTable tidak ditemukan di halaman.");
                return;
            }

            const rows = poTable.querySelectorAll("tbody tr");

            rows.forEach((row, index) => {
                const qtyCelupInput = row.querySelector('input[name^="qty_celup["]');
                const lastStatusInput = row.querySelector('input[name^="last_status["]');

                if (qtyCelupInput && lastStatusInput) {
                    const qtyCelup = parseFloat(qtyCelupInput.value) || 0;
                    const lastStatus = lastStatusInput.value;

                    if (lastStatus === 'scheduled' || lastStatus === 'celup' || lastStatus === 'reschedule') {
                        totalQtyCelup += qtyCelup;
                    }
                } else {
                    console.warn(`⚠️ Input qty_celup atau last_status tidak ditemukan di baris ke-${index}`);
                }
            });

            const totalQtyCelupElement = document.getElementById("total_qty_celup");
            if (totalQtyCelupElement) {
                totalQtyCelupElement.value = totalQtyCelup.toFixed(2);
            }

            const maxCaps = parseFloat(document.getElementById("max_caps").value) || 0;
            if (totalQtyCelup > maxCaps) {
                alert("⚠️ Total Qty Celup melebihi Max Caps!");
            }

            rows.forEach((row, index) => {
                const sisaJatahElement = row.querySelector(".sisa_jatah");
                if (sisaJatahElement) {
                    const sisaJatah = parseFloat(sisaJatahElement.textContent) || 0;
                    sisaJatahElement.style.color = sisaJatah < 0 ? "red" : "white";
                    // tampilkan 2 angka di belakang koma
                    sisaJatahElement.textContent = sisaJatah.toFixed(2);
                    if (sisaJatah < 0) {
                        alert(`⚠️ Sisa Jatah di baris ke-${index} negatif!`);
                    }
                }
            });

            const sisaKapasitasElement = document.getElementById("sisa_kapasitas");
            if (sisaKapasitasElement) {
                sisaKapasitasElement.value = (maxCaps - totalQtyCelup).toFixed(2);
            }
        }



        // ✅ Event delegation untuk perubahan PO
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const selectedOption = poSelect.options[poSelect.selectedIndex];
                const tr = poSelect.closest("tr");

                const itemTypeValue = tr.querySelector("select[name^='item_type']").value;
                const kodeWarnaValue = document.querySelector("input[name='kode_warna']").value;

                if (selectedOption.value && itemTypeValue && kodeWarnaValue) {
                    fetchPODetails(selectedOption.value, tr, itemTypeValue, kodeWarnaValue);
                } else {
                    resetPODetails(tr);
                }
            }
        });

        // ✅ Fungsi fetch detail PO
        // function fetchPODetails(poNo, tr, itemType, kodeWarna) {
        //     fetchData('getPODetails', {
        //         id_order: poNo,
        //         item_type: itemType,
        //         kode_warna: kodeWarna
        //     }, (data) => {
        //         if (data && !data.error) {
        //             const fields = {
        //                 'tgl_start_mc[]': data.start_mesin || '',
        //                 'delivery_awal[]': data.delivery_awal || '',
        //                 'delivery_akhir[]': data.delivery_akhir || '',
        //                 'qty_po[]': parseFloat(data.kg_kebutuhan).toFixed(2),
        //                 'qty_po_plus[]': parseFloat(data.qty_po_plus).toFixed(2),
        //                 '.kg_kebutuhan': parseFloat(data.kg_kebutuhan).toFixed(2),
        //                 '.sisa_jatah': parseFloat(data.sisa_jatah).toFixed(2)
        //             };

        //             Object.keys(fields).forEach(key => {
        //                 const element = tr.querySelector(key.startsWith('.') ? key : `input[name="${key}"]`);
        //                 if (element) {
        //                     if (key.startsWith('.')) {
        //                         element.textContent = fields[key];
        //                     } else {
        //                         element.value = fields[key];
        //                     }
        //                 }
        //             });
        //         }
        //     });
        // }

        function fetchPODetails(poNo, tr, itemType, kodeWarna) {
            fetchData('getPODetails', {
                id_order: poNo,
                item_type: itemType,
                kode_warna: kodeWarna
            }, (data) => {
                console.log("PO Details Fetched:", data); // Debugging log
                if (data && !data.error) {
                    const fields = {
                        'tgl_start_mc[]': data.start_mesin || '',
                        'delivery_awal[]': data.delivery_awal || '',
                        'delivery_akhir[]': data.delivery_akhir || '',
                        'qty_po[]': parseFloat(data.kg_kebutuhan).toFixed(2),
                        'qty_po_plus[]': parseFloat(data.qty_po_plus).toFixed(2),
                        '.kg_kebutuhan': parseFloat(data.kg_kebutuhan).toFixed(2),
                        '.sisa_jatah': parseFloat(data.sisa_jatah).toFixed(2)
                    };

                    Object.keys(fields).forEach(key => {
                        const element = tr.querySelector(key.startsWith('.') ? key : `input[name="${key}"]`);
                        if (element) {
                            if (key.startsWith('.')) {
                                element.textContent = fields[key];
                            } else {
                                element.value = fields[key];
                            }
                        }
                    });
                    calculateTotalAndRemainingCapacity();
                }
            });
        }

        // ✅ Fungsi reset detail PO
        function resetPODetails(tr) {
            const fields = ['tgl_start_mc[]', 'delivery_awal[]', 'delivery_akhir[]', 'qty_po[]', 'qty_po_plus[]'];
            fields.forEach(field => {
                const element = tr.querySelector(`input[name="${field}"]`);
                if (element) element.value = '';
            });

            const spans = tr.querySelectorAll('.kg_kebutuhan, .sisa_jatah');
            spans.forEach(span => span.textContent = '0.00');
        }

        // ✅ Event delegation untuk menambah baris baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = poTable.querySelector("tbody");
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
    <td>
        <select class="form-select item-type" name="item_type[]" required>
            <option value="">Pilih Item Type</option>
        </select>
    </td>
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
            <span class="kg_kebutuhan">0.00</span> KG
        </span>
    </td>
    <td class="text-center">
        <span class="badge bg-info">
            <span class="sisa_jatah">0.00</span> KG
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
    <td>
        <span class="badge bg-info">scheduled</span>
        <input type="hidden" class="form-control last_status" name="last_status[]" value="scheduled">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-danger removeRow">
            <i class="fas fa-trash"></i>
        </button>
    </td>
    `;
            tbody.appendChild(newRow);

            // Ambil elemen item-type di baris baru
            const itemTypeSelect = newRow.querySelector(".item-type");

            // Fetch item type hanya untuk item-type di baris baru
            fetchItemType(kodeWarna.value, warnaInput.value, itemTypeSelect);

            // Tambahkan event listener untuk perubahan item type
            itemTypeSelect.addEventListener("change", function() {
                const itemTypeValue = this.value;
                const poSelect = newRow.querySelector(".po-select");
                if (itemTypeValue) {
                    fetchPOByKodeWarna(kodeWarna.value, warnaInput.value, itemTypeValue, poSelect);
                }
            });
        });


        // ✅ Event delegation untuk menghapus baris
        poTable.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeRow")) {
                e.target.closest("tr").remove();
                calculateTotalAndRemainingCapacity();
            }
        });
        calculateTotalAndRemainingCapacity();
    });
</script>
<?php $this->endSection(); ?>