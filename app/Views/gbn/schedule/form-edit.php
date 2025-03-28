<?php $this->extend($role . '/schedule/header'); ?>
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

    /* Table Styles */
    .table-responsive {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: rgb(0, 77, 94);
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    legend {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 8px;
    }

    fieldset {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .col-3.d-flex {
        gap: 10px;
    }

    .form-group div {
        display: flex;
        align-items: center;
        gap: 5px;
    }


    /* input[type="radio"] {
        margin-right: 5px;
    } */
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
                                        <select class="form-select" id="jenis_bahan_baku" name="jenis_bahan_baku" required>
                                            <option value="">Pilih Jenis Bahan Baku</option>
                                            <?php foreach ($jenis_bahan_baku as $jenis): ?>
                                                <option value="<?= $jenis['jenis'] ?>" <?= ($jenis['jenis'] == $jenis_bahan_baku) ? 'selected' : '' ?>><?= $jenis['jenis'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" value="<?= $kode_warna ?>" required readonly>
                                        <div id="suggestionsKWarna" class="suggestions-box" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" value="<?= $warna ?>" maxlength="32" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="poTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th class="text-center">Order</th>
                                                    <th class="text-center">
                                                        <button type="button" class="btn btn-info" id="addRow">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; ?>
                                                <?php foreach ($scheduleData as $detail): ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?= $no++ ?>
                                                        </td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="itemtype"> Item Type</label>
                                                                        <select class="form-select item-type" name="item_type[]" required>
                                                                            <?php foreach ($scheduleData as $item): ?>
                                                                                <option value="<?= $item['item_type'] ?>" <?= ($item['item_type'] == $detail['item_type']) ? 'selected' : '' ?>><?= $item['item_type'] ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <input type="hidden" name="id_celup[]" value="<?= $detail['id_celup'] ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="po"> PO</label>
                                                                        <select class="form-select po-select" name="po[]" required>
                                                                            <?php foreach ($scheduleData as $po): ?>
                                                                                <option value="<?= $detail['no_model'] ?>" ?><?= $po['no_model'] ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label for="tgl_start_mc"> Tgl Start MC</label>
                                                                        <input type="date" class="form-control" name="tgl_start_mc[]" value="<?= $detail['start_mc'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label for="delivery_awal"> Delivery Awal</label>
                                                                        <input type="date" class="form-control" name="delivery_awal[]" value="<?= $detail['delivery_awal'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label for="delivery_akhir"> Delivery Akhir</label>
                                                                        <input type="date" class="form-control" name="delivery_akhir[]" value="<?= $detail['delivery_akhir'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label for="qty_po"> Qty PO</label>
                                                                        <input type="number" class="form-control" name="qty_po[]" value="<?= number_format($detail['qty_po'], 2) ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label for="qty_po_plus"> Qty PO (+)</label>
                                                                        <input type="number" class="form-control" name="qty_po_plus[]" value="" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <label for="qty_celup">Qty Celup</label>
                                                                    <input type="number" class="form-control" step="0.01" min="0.01" name="qty_celup[]" value="<?= number_format($detail['qty_celup'], 2) ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label for="qty_celup">KG Kebutuhan :</label>
                                                                        <br />
                                                                        <span class="badge bg-info">
                                                                            <span class="kg_kebutuhan"><?= $detail['kg_kebutuhan'] ?></span> KG
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label for="sisa_jatah">Sisa Jatah :</label>
                                                                        <br />
                                                                        <span class="badge bg-info">
                                                                            <span class="sisa_jatah" data-sisajatah="<?= number_format($detail['sisa_jatah'], 2) ?>"><?= number_format($detail['sisa_jatah'], 2) ?></span> KG
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label for="last_status">Last Status</label>
                                                                        <br />
                                                                        <?php
                                                                        $status = $detail['last_status'];
                                                                        if (in_array($status, ['scheduled', 'retur', 'reschedule'])) {
                                                                            $badgeColor = 'info';
                                                                        } elseif (in_array($status, ['bon', 'celup', 'bongkar', 'press', 'oven', 'tl', 'rajut', 'acc', 'reject', 'perbaikan'])) {
                                                                            $badgeColor = 'warning';
                                                                        } else {
                                                                            in_array($status, ['done', 'sent']);
                                                                            $badgeColor = 'success';
                                                                        }
                                                                        ?>
                                                                        <span class="badge bg-<?= $badgeColor ?>"><?= htmlspecialchars($status) ?></span>
                                                                        <input type="hidden" class="form-control last_status" name="last_status[]" value="<?= htmlspecialchars($status) ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="col-3 d-flex align-items-center">
                                                                    <div class="form-group">
                                                                        <label for="qty_celup">PO + :</label>
                                                                        <fieldset>
                                                                            <legend></legend>
                                                                            <div>
                                                                                <input type="radio" id="po_plus" name="po_plus[]" value="1" <?= isset($detail['po_plus']) && $detail['po_plus'] == 1 ? 'checked' : '' ?>>
                                                                                <label for="iya">Iya</label>
                                                                                <input type="radio" id="po_plus" name="po_plus[]" value="0" <?= isset($detail['po_plus']) && $detail['po_plus'] == 0 ? 'checked' : '' ?>>
                                                                                <label for="tidak">Tidak</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-info editRow" data-id="<?= $detail['id_celup'] ?>" data-tanggalschedule="<?= $tanggal_schedule ?>">
                                                                <i class="fas fa-calendar-alt"></i>
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

                                <div class="text-end">
                                    <button type="submit" class="btn btn-info w-100">Update Jadwal</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Element referensi
        const kodeWarna = document.getElementById('kode_warna');
        const suggestionsBoxKWarna = document.getElementById('suggestionsKWarna');
        const warnaInput = document.getElementById('warna');
        const poTable = document.getElementById("poTable");

        // Inisialisasi locked statuses (status yang mengunci input)
        const lockedStatuses = ['bon', 'celup', 'bongkar', 'press', 'oven', 'tl', 'rajut', 'acc', 'done', 'reject', 'perbaikan', 'sent'];
        document.querySelectorAll('.last_status').forEach(status => {
            const statusValue = (status.value || status.textContent).trim().toLowerCase();
            if (lockedStatuses.includes(statusValue)) {
                const row = status.closest('tr');
                row.querySelectorAll('input, select, button').forEach(el => {
                    if (el.tagName.toLowerCase() === 'input') {
                        el.readOnly = true;
                    } else {
                        el.disabled = true;
                    }
                    el.classList.add('locked-input');
                });
            }
        });

        // Event delegation untuk tombol Edit (modal edit)
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.editRow');
            if (button) {
                const idCelup = button.dataset.id;
                const modalEl = document.getElementById('editModal');
                if (!modalEl) {
                    console.error('Modal edit tidak ditemukan!');
                    return;
                }
                const modal = new bootstrap.Modal(modalEl);
                document.getElementById('idCelup').value = idCelup;
                document.getElementById('tanggal_schedule').value = button.dataset.tanggalschedule;
                modal.show();
            }
        });

        // Ajax submit untuk modal edit menggunakan jQuery
        $('#editModal form').submit(function(e) {
            e.preventDefault();
            const formData = {
                id_celup: $('#idCelup').val(),
                tanggal_schedule: $('#tanggal_schedule').val(),
                no_mesin: $('#no_mesin').val(),
                lot_urut: $('#lot_urut').val()
            };

            $.ajax({
                    url: '<?= base_url(session('role') . '/schedule/updateTglSchedule') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json'
                })
                .done(response => {
                    if (response.success) {
                        alert('Update berhasil!');
                        window.location.href = '<?= base_url(session('role') . '/schedule') ?>';
                    } else {
                        alert('Gagal: ' + (response.message || 'Terjadi kesalahan'));
                    }
                })
                .fail((xhr, status, error) => {
                    alert(`Error: ${error}`);
                    console.error('Detail error:', xhr.responseText);
                });
        });

        // Event handler untuk input kode warna
        if (kodeWarna) {
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
                            // Update semua dropdown item-type jika ada lebih dari satu
                            document.querySelectorAll('.item-type').forEach(select => {
                                fetchItemType(query, data[0].color, select);
                            });
                        }
                    });
                } else {
                    suggestionsBoxKWarna.style.display = 'none';
                }
            });
        }

        // Fungsi utilitas untuk fetching data dengan URL building
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

        // Fungsi menampilkan saran kode warna
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

        // Fungsi fetch item type untuk mengisi dropdown targetSelect
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

        // Fungsi untuk mengisi select dropdown dengan data
        function populateSelect(selectElement, data, valueKey, textKey) {
            selectElement.innerHTML = `<option value="">Pilih ${textKey}</option>`;
            if (data.length > 0) {
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item[valueKey];
                    option.textContent = item[textKey];
                    if (item.id_induk) {
                        option.setAttribute("data-id-induk", item.id_induk);
                    }
                    selectElement.appendChild(option);
                });
            } else {
                selectElement.innerHTML = '<option value="">Tidak ada data</option>';
            }
        }

        // Fungsi fetch PO berdasarkan kode warna, warna, dan item type
        function fetchPOByKodeWarna(kodeWarna, warna, itemType, idInduk, poSelect) {
            fetchData('getPO', {
                kode_warna: kodeWarna,
                warna,
                item_type: itemType,
                id_induk: idInduk
            }, (data) => {
                populateSelect(poSelect, data, 'no_model', 'no_model');
                console.log(data);
            });
        }

        // Fungsi untuk menghitung total Qty Celup dan sisa kapasitas
        function calculateTotalAndRemainingCapacity() {
            const rows = poTable.querySelectorAll("tbody tr");
            let totalQtyCelup = 0;
            rows.forEach(row => {
                const lastStatusEl = row.querySelector("input[name='last_status[]']");
                const lastStatus = lastStatusEl ? lastStatusEl.value.trim().toLowerCase() : "";
                if (["scheduled", "bon", "celup", "bongkar", "press", "oven", "tes luntur", "rajut pagi", "reschedule"].includes(lastStatus)) {
                    const qtyCelup = parseFloat(row.querySelector("input[name='qty_celup[]']").value) || 0;
                    totalQtyCelup += qtyCelup;
                }
            });
            const totalQtyCelupElement = document.getElementById("total_qty_celup");
            if (totalQtyCelupElement) {
                totalQtyCelupElement.value = totalQtyCelup.toFixed(2);
            }
            const maxCaps = parseFloat(document.getElementById("max_caps").value) || 0;
            if (totalQtyCelup >= maxCaps) {
                alert("⚠️ Total Qty Celup melebihi Max Caps!");
                totalQtyCelupElement.classList.add("is-invalid");
                totalQtyCelupElement.focus();
            } else {
                totalQtyCelupElement.classList.remove("is-invalid");
            }
            const sisaKapasitasElement = document.getElementById("sisa_kapasitas");
            if (sisaKapasitasElement) {
                const sisaKapasitas = maxCaps - totalQtyCelup;
                sisaKapasitasElement.value = sisaKapasitas.toFixed(2);
                if (sisaKapasitas <= 0) {
                    alert("⚠️ Sisa Kapasitas negatif!");
                    sisaKapasitasElement.classList.add("is-invalid");
                    sisaKapasitasElement.focus();
                } else {
                    sisaKapasitasElement.classList.remove("is-invalid");
                }
            }
        }

        // Update perhitungan kapasitas saat input qty_celup berubah
        poTable.addEventListener("input", function(e) {
            if (e.target.name === "qty_celup[]") {
                calculateTotalAndRemainingCapacity();
            }
        });

        // Event delegation untuk perubahan pada dropdown PO
        if (poTable) {
            poTable.addEventListener("change", function(e) {
                if (e.target.classList.contains("po-select")) {
                    const poSelect = e.target;
                    const tr = poSelect.closest("tr");
                    const itemTypeSelect = tr.querySelector("select[name^='item_type']");
                    const itemTypeValue = itemTypeSelect.value;
                    const kodeWarnaValue = document.querySelector("input[name='kode_warna']").value;
                    const warna = document.querySelector("input[name='warna']").value;
                    const idIndukValue = itemTypeSelect.selectedOptions[0]?.getAttribute("data-id-induk") || 0;
                    if (poSelect.value && itemTypeValue && kodeWarnaValue) {
                        fetchQtyAndKebutuhanPO(kodeWarnaValue, tr, warna, itemTypeValue, idIndukValue);
                        fetchPODetails(poSelect.value, tr, itemTypeValue, kodeWarnaValue);
                    } else {
                        resetPODetails(tr);
                    }
                }
            });

        }

        // Fungsi Fetch Detail PO untuk update data schedule dan qty_po
        function fetchPODetails(poNo, tr, itemType, kodeWarna) {
            const url = `<?= base_url(session('role') . "/schedule/getPODetails") ?>?id_order=${poNo}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}`;
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    console.log("Data received from server:", data);
                    if (data && !data.error) {
                        tr.querySelector("input[name='tgl_start_mc[]']").value = data.start_mesin || '';
                        tr.querySelector("input[name='delivery_awal[]']").value = data.delivery_awal || '';
                        tr.querySelector("input[name='delivery_akhir[]']").value = data.delivery_akhir || '';
                        tr.querySelector("input[name='qty_po[]']").value = parseFloat(data.kg_po).toFixed(2);
                    } else {
                        console.error('Error fetching PO details:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching PO details:', error);
                });
        }

        // Fungsi untuk memanggil getQtyPO dan mengupdate qty_po_plus, KG Kebutuhan, serta sisa jatah
        function fetchQtyAndKebutuhanPO(kodeWarna, tr, warna, itemType, idInduk) {
            const itemTypeEncoded = encodeURIComponent(itemType);
            idInduk = idInduk || 0;
            const url = `<?= base_url(session('role') . "/schedule/getQtyPO") ?>?kode_warna=${kodeWarna}&color=${warna}&item_type=${itemTypeEncoded}&id_induk=${idInduk}`;
            console.log("Request URL:", url);
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data && !data.error) {
                        tr.querySelector("input[name='qty_po[]']").value = parseFloat(data.kg_po).toFixed(2);
                        tr.querySelector("input[name='qty_po_plus[]']").value = parseFloat(data.qty_po_plus).toFixed(2) || '';
                        tr.querySelector(".kg_kebutuhan").textContent = parseFloat(data.kg_po).toFixed(2);
                        tr.querySelector(".sisa_jatah").textContent = parseFloat(data.sisa_jatah).toFixed(2) || '0.00';
                    } else {
                        console.error('Error fetching Qty PO details:', data.error || 'No data found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching Qty data:', error);
                });
        }

        // Fungsi reset detail PO (jika dropdown PO tidak valid)
        function resetPODetails(tr) {
            const fields = ['tgl_start_mc[]', 'delivery_awal[]', 'delivery_akhir[]', 'qty_po[]', 'qty_po_plus[]'];
            fields.forEach(field => {
                const element = tr.querySelector(`input[name="${field}"]`);
                if (element) element.value = '';
            });
            tr.querySelectorAll('.kg_kebutuhan, .sisa_jatah').forEach(span => span.textContent = '0.00');
        }

        // Inisialisasi perhitungan kapasitas saat load halaman
        calculateTotalAndRemainingCapacity();

        // Event listener untuk menambah baris baru
        document.getElementById("addRow").addEventListener("click", function() {
            const tbody = poTable.querySelector("tbody");
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
      <td class="text-center">${tbody.rows.length + 1}</td>
      <td>
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label for="itemtype"> Item Type</label>
              <select class="form-select item-type" name="item_type[]" required>
                <option value="">Pilih Item Type</option>
              </select>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <label for="po">PO</label>
              <select class="form-select po-select" name="po[]" required>
                <option value="">Pilih PO</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label for="tgl_start_mc">Tgl Start MC</label>
              <input type="date" class="form-control" name="tgl_start_mc[]" readonly>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label for="delivery_awal">Delivery Awal</label>
              <input type="date" class="form-control" name="delivery_awal[]" readonly>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label for="delivery_akhir">Delivery Akhir</label>
              <input type="date" class="form-control" name="delivery_akhir[]" readonly>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <div class="form-group">
              <label for="qty_po">Qty PO</label>
              <input type="number" class="form-control" name="qty_po[]" readonly>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group">
              <label for="qty_po_plus">Qty PO (+)</label>
              <input type="number" class="form-control" name="qty_po_plus[]" readonly>
            </div>
          </div>
          <div class="col-4">
            <label for="qty_celup">Qty Celup</label>
            <input type="number" step=0.1 class="form-control" name="qty_celup[]" required>
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            <div class="form-group">
              <label for="qty_celup">KG Kebutuhan :</label>
              <br />
              <span class="badge bg-info">
                <span class="kg_kebutuhan">0.00</span> KG
              </span>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label for="last_status">Last Status</label>
              <br />
              <span class="badge bg-info">scheduled</span>
              <input type="hidden" class="form-control last_status" name="last_status[]" value="scheduled">
            </div>
          </div>
          <div class="col-3 d-flex align-items-center">
            <div class="form-group">
              <label for="qty_celup">PO + :</label>
              <fieldset>
                <legend></legend>
                <div>
                  <input type="radio" id="po_plus" name="po_plus[]" value="1">
                  <label for="iya">Iya</label>
                  <input type="radio" id="po_plus" name="po_plus[]" value="0">
                  <label for="tidak">Tidak</label>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-danger removeRow">
          <i class="fas fa-trash"></i>
        </button>
      </td>
    `;
            tbody.appendChild(newRow);
            // Isi opsi item_type di baris baru
            const itemTypeSelect = newRow.querySelector(".item-type");
            fetchItemTypeRow(kodeWarna.value, warnaInput.value, itemTypeSelect);

            $(itemTypeSelect).on('change', function() {
                const itemTypeValue = $(this).val();
                const poSelect = newRow.querySelector(".po-select");
                const idIndukValue = $(this).find(':selected').data('id-induk') || 0;
                // kodeWarna.value, warnaInput.value, itemTypeValue, idInduk, poSelect
                fetchPOByKodeWarna(kodeWarna.value, warnaInput.value, itemTypeValue, idIndukValue, poSelect);
                // fetchQtyAndKebutuhanPO(kodeWarna.value, newRow, warnaInput.value, itemTypeValue, idIndukValue);
            });

            newRow.querySelector("input[name='qty_celup[]']").addEventListener("input", function() {
                calculateTotalAndRemainingCapacity();
            });
        });

        function fetchItemTypeRow(kodeWarna, warna, itemTypeSelect) {
            fetch(`<?= base_url(session('role') . "/schedule/getItemType") ?>?kode_warna=${kodeWarna}&warna=${warna}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        itemTypeSelect.innerHTML = '<option value="">Pilih Item Type</option>';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_type;
                            option.textContent = item.item_type;
                            option.setAttribute("data-id-induk", item.id_induk);
                            itemTypeSelect.appendChild(option);
                        });
                    } else {
                        itemTypeSelect.innerHTML = '<option value="">Tidak ada Item Type</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching item type data:', error);
                });
        }
        // Event delegation untuk menghapus baris
        poTable.addEventListener("click", function(event) {
            const removeBtn = event.target.closest(".removeRow");
            if (removeBtn) {
                const row = removeBtn.closest("tr");
                const tbody = poTable.querySelector("tbody");
                const idCelupInput = row.querySelector('input[name^="id_celup["]');
                const idCelup = idCelupInput ? idCelupInput.value : null;
                if (idCelup) {
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
                                            if (tbody.rows.length === 0) {
                                                window.location.href = '<?= base_url(session('role') . '/schedule') ?>';
                                            } else {
                                                calculateTotalAndRemainingCapacity();
                                            }
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
                            if (tbody.rows.length === 0) {
                                window.location.href = '<?= base_url(session('role') . '/schedule') ?>';
                            } else {
                                calculateTotalAndRemainingCapacity();
                            }
                        }
                    });
                }
            }
        });
    });
</script>


<?php $this->endSection(); ?>