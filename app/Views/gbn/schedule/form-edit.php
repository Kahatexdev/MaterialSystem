<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    small.text-warning {
        font-size: 0.8em;
        color: #ffc107;
    }

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

    .bg-custom-dark {
        background-color: rgb(0, 77, 94);
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Schedule Celup</h3>
                    <div class="card-tools">
                        <!-- Info tambahan kalau perlu -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url(session('role') . '/schedule/updateSchedule') ?>" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>
                                        <input type="hidden" name="start_date" value="<?= $start_date ?>">
                                        <input type="hidden" name="end_date" value="<?= $end_date ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Schedule</label>
                                        <input type="date" class="form-control" name="tanggal_schedule" value="<?= $tanggal_schedule ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Lot Urut</label>
                                        <select name="lot_urut" id="lot_urut" class="form-select">
                                            <?php for ($i = 1; $i <= $jmlLot; $i++): ?>
                                                <option value="<?= $i ?>" <?= $lot_urut == $i ? 'selected' : '' ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="min_caps" class="form-label">Min Caps</label>
                                        <input type="number" class="form-control" id="min_caps" name="min_caps" value="<?= $min_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="max_caps" class="form-label">Max Caps</label>
                                        <input type="number" class="form-control" id="max_caps" name="max_caps" value="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas</label>
                                        <input type="number" class="form-control" id="sisa_kapasitas" name="sisa_kapasitas" value="<?= $max_caps ?>" data-max-caps="<?= $max_caps ?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="jenis_bahan_bakuE" class="form-label">Jenis Bahan Baku</label>
                                        <select class="form-select" id="jenis_bahan_bakuE" name="jenis_bahan_baku" required>
                                            <option value="">Pilih Jenis Bahan Baku</option>
                                            <option value="BENANG" <?= ($jenis_bahan_baku ?? '') === 'BENANG' ? 'selected' : '' ?>>BENANG</option>
                                            <option value="NYLON" <?= ($jenis_bahan_baku ?? '') === 'NYLON' ? 'selected' : '' ?>>NYLON</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="kode_warnaE" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warnaE" name="kode_warna" value="<?= $kode_warna ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="warnaE" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warnaE" name="warna" value="<?= $warna ?>" maxlength="32" required>
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
                                                                        <label for="itemtype_<?= $detail['id_celup']; ?>">Item Type</label>
                                                                        <select
                                                                            id="itemtype_<?= $detail['id_celup']; ?>"
                                                                            class="form-select item-typeCus"
                                                                            name="item_type[]"
                                                                            data-selected="<?= esc($detail['item_type']); ?>"
                                                                            required
                                                                        >
                                                                            <option value="">Loading...</option>
                                                                        </select>
                                                                        <input type="hidden" name="id_celup[]" value="<?= esc($detail['id_celup']); ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="po_<?= $detail['id_celup']; ?>">PO</label>
                                                                        <select
                                                                            id="po_<?= $detail['id_celup']; ?>"
                                                                            class="form-select po-select"
                                                                            name="po[]"
                                                                            data-selected="<?= esc($detail['no_model']); ?>"
                                                                            required
                                                                        >
                                                                            <option value="">Loading...</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label>Tgl Start MC</label>
                                                                        <input type="date" class="form-control" name="tgl_start_mc[]" value="<?= $detail['start_mc'] ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label>Delivery Awal</label>
                                                                        <input type="date" class="form-control" name="delivery_awal[]" value="<?= $detail['delivery_awal'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label>Delivery Akhir</label>
                                                                        <input type="date" class="form-control" name="delivery_akhir[]" value="<?= $detail['delivery_akhir'] ?>" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label>Qty PO</label>
                                                                        <input type="number" class="form-control" name="qty_po[]" value="<?= ($detail['po_plus'] === '0' || $detail['po_plus'] === '' || is_null($detail['po_plus'])) ? number_format($detail['qty_po'], 2, '.', '') : '' ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="form-group">
                                                                        <label>Qty PO (+)</label>
                                                                        <input type="number" class="form-control" name="qty_po_plus[]" value="<?= number_format($detail['qty_po_plus'], 2, '.', '') ?>" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-4">
                                                                    <label>Qty Celup</label>
                                                                    <input type="number" class="form-control" step="0.01" min="0.01" name="qty_celup[]" value="<?= number_format($detail['qty_celup'], 2, '.', '') ?>" required>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label>KG Kebutuhan :</label><br />
                                                                        <span class="badge bg-info">
                                                                            <span class="kg_kebutuhan"><?= number_format($detail['kg_kebutuhan'], 2, '.', '') ?></span> KG
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label>Sisa Jatah :</label><br />
                                                                        <span class="badge bg-info">
                                                                            <span class="sisa_jatah" data-sisajatah="<?= number_format($detail['sisa_jatah'], 2, '.', '') ?>"><?= number_format($detail['sisa_jatah'], 2, '.', '') ?></span> KG
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-3">
                                                                    <div class="form-group">
                                                                        <label>Last Status</label><br />
                                                                        <?php
                                                                        $status = $detail['last_status'];
                                                                        if (in_array($status, ['scheduled', 'retur', 'reschedule'])) {
                                                                            $badgeColor = 'info';
                                                                        } elseif (in_array($status, ['bon', 'celup', 'bongkar', 'press_oven', 'tes_luntur', 'tes_lab', 'rajut', 'acc', 'reject', 'perbaikan', 'serah_terima_acc', 'matching'])) {
                                                                            $badgeColor = 'custom-dark';
                                                                        } else {
                                                                            $badgeColor = 'success';
                                                                        }
                                                                        ?>
                                                                        <span class="badge bg-<?= $badgeColor ?>"><?= esc($status) ?></span>
                                                                        <input type="hidden" class="form-control last_status" name="last_status[]" value="<?= esc($status) ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="col-3 d-flex align-items-center">
                                                                    <div class="form-group">
                                                                        <label>PO + :</label>
                                                                        <fieldset>
                                                                            <legend></legend>
                                                                            <div>
                                                                                <?php $key = $detail['id_celup']; ?>
                                                                                <?php $val = isset($detail['po_plus']) ? (string)$detail['po_plus'] : ''; ?>
                                                                                <input type="radio" id="po_plus_yes_<?= $key ?>" name="po_plus[<?= $key ?>]" value="1" <?= ($val === '1') ? 'checked' : '' ?>>
                                                                                <label for="po_plus_yes_<?= $key ?>">Iya</label>
                                                                                <input type="radio" id="po_plus_no_<?= $key ?>" name="po_plus[<?= $key ?>]" value="0" <?= ($val === '0' || $val === '') ? 'checked' : '' ?>>
                                                                                <label for="po_plus_no_<?= $key ?>">Tidak</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label>Keterangan PO</label><br />
                                                                        <textarea class="form-control" disabled><?= $detail['keterangan'] ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label>Keterangan Schedule</label><br />
                                                                        <textarea class="form-control ket_schedule[]" name="ket_schedule[]" id="ket_schedule"><?= $detail['ket_schedule'] ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="text-muted small fst-italic">
                                                                        Last updated: <?= $detail['last_update'] ?> at <?= $detail['jam_update'] ?> by <?= $detail['admin'] ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group-vertical w-100 gap-2" role="group" aria-label="Aksi schedule">
                                                                <button type="button"
                                                                    class="btn btn-warning editMesin"
                                                                    data-id-celup="<?= esc($detail['id_celup']) ?>"
                                                                    data-mesin-schedule="<?= esc($no_mesin) ?>"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="left"
                                                                    title="Edit mesin schedule (Mesin: <?= esc($no_mesin) ?>)">
                                                                    <i class="fas fa-cube" aria-hidden="true"></i>
                                                                    <span class="visually-hidden">Edit mesin</span>
                                                                </button>

                                                                <button type="button"
                                                                    class="btn btn-info editRow"
                                                                    data-id-celup="<?= esc($detail['id_celup']) ?>"
                                                                    data-tanggal-schedule="<?= esc($tanggal_schedule) ?>"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="left"
                                                                    title="Atur tanggal schedule (<?= esc($tanggal_schedule) ?>)">
                                                                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                                    <span class="visually-hidden">Edit tanggal</span>
                                                                </button>

                                                                <button type="button"
                                                                    class="btn btn-danger removeRow"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="left"
                                                                    title="Hapus baris ini">
                                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                                    <span class="visually-hidden">Hapus baris</span>
                                                                </button>
                                                            </div>
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

                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-info w-100">Update Jadwal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$history): ?>
        <div class="row mt-3">
            <div class="card">
                <div class="card-header text-center">
                    <h6 class="card-title">no history</h6>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row mt-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">History Schedule</h4>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="text-center table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Model</th>
                                    <th>Item Type</th>
                                    <th>Kode Warna</th>
                                    <th>Warna</th>
                                    <th>Qty Celup</th>
                                    <th>Ket Sch</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h): ?>
                                    <tr>
                                        <td><span class="badge bg-info"><?= esc($h['last_status']) ?></span></td>
                                        <td><?= esc($h['no_model']) ?></td>
                                        <td><?= esc($h['item_type']) ?></td>
                                        <td><?= esc($h['kode_warna']) ?></td>
                                        <td><?= esc($h['warna']) ?></td>
                                        <td><?= esc($h['qty_celup']) ?></td>
                                        <td><?= esc($h['ket_schedule']) ?></td>
                                        <td><?= esc($h['admin']) ?><br><?= esc($h['last_update']) ?> || <?= esc($h['jam_update']) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

</div>

<!-- Modal Edit Tanggal -->
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

<!-- Modal Edit MESIN -->
<div class="modal fade" id="editModalMesin" tabindex="-1" aria-labelledby="editModalMesinLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditMesin" action="<?= base_url(session('role') . '/schedule/updateMesinSchedule') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalMesinLabel">Edit Mesin Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id_celup" id="idCelupMesin">

                    <div class="form-group mb-3">
                        <label for="targetSlot" class="form-label">Mesin & Slot Tersedia</label>
                        <select name="target_slot" id="targetSlot" class="form-select" required>
                            <option value="" selected disabled>— Memuat… —</option>
                        </select>
                        <small class="text-muted d-block mt-1">
                            Format: Mesin X • Lot Y — status (existing / max)
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-info" id="btnSaveMesin">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(el) {
        new bootstrap.Tooltip(el, { container: 'body' });
    });

    const kodeWarna    = document.getElementById('kode_warnaE');
    const warnaInput   = document.getElementById('warnaE');
    const poTable      = document.getElementById("poTable");
    const lotSelect    = document.getElementById("lot_urut");

    if (!kodeWarna || !warnaInput || !poTable) {
        console.warn('kode_warnaE / warnaE / poTable tidak ditemukan di DOM.');
        return;
    }

    // Status yang ngelock row
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

    // Helper fetch (GET JSON)
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

    // Helper isi select
    function populateSelect(selectElement, data, valueKey, textKey) {
        if (!selectElement) return;

        selectElement.innerHTML = `<option value="">Pilih ${textKey}</option>`;

        if (Array.isArray(data) && data.length > 0) {
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

    // Fetch item type dan isi semua .item-typeCus
    function fetchAndFillItemTypes() {
        const kodeWarnaVal = kodeWarna.value.trim();
        const warnaVal     = warnaInput.value.trim();

        if (!kodeWarnaVal || !warnaVal) {
            console.warn('kode_warnaE atau warnaE kosong, skip fetch item type.');
            return;
        }

        fetchData('getItemType', {
            kode_warna: kodeWarnaVal,
            warna: warnaVal
        }, (data) => {
            const rows = poTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const select = row.querySelector('.item-typeCus');
                if (!select) return;

                const selectedFromAttr = select.dataset.selected || '';

                populateSelect(select, data, 'item_type', 'item_type');

                if (selectedFromAttr) {
                    const foundOption = Array.from(select.options).find(
                        opt => opt.value === selectedFromAttr
                    );
                    if (foundOption) {
                        select.value = selectedFromAttr;
                    }
                }
            });

            // Setelah item_type keisi, isi PO per baris
            initAllRowPO();

            // Pas item_type diubah, refetch PO baris tersebut
            rows.forEach(row => {
                const itemSelect = row.querySelector('.item-typeCus');
                if (!itemSelect) return;

                if (!itemSelect.dataset.boundChangePO) {
                    itemSelect.addEventListener('change', () => {
                        const poSelect = row.querySelector('.po-select');
                        if (poSelect) {
                            poSelect.dataset.selected = '';
                        }
                        initRowPO(row);
                    });
                    itemSelect.dataset.boundChangePO = '1';
                }
            });
        });
    }

    // Fetch PO berdasarkan kombinasi filter
    function fetchPOByKodeWarna(kodeWarnaVal, warnaVal, itemType, idInduk, poSelect) {
        fetchData('getPO', {
            kode_warna: kodeWarnaVal,
            warna: warnaVal,
            item_type: itemType,
            id_induk: idInduk
        }, (data) => {
            populateSelect(poSelect, data, 'no_model', 'no_model');

            const selectedPO = poSelect.dataset.selected || '';
            if (selectedPO) {
                const foundOption = Array.from(poSelect.options).find(
                    opt => opt.value === selectedPO
                );
                if (foundOption) {
                    poSelect.value = selectedPO;
                }
            }

            console.log('Data PO:', data);
        });
    }

    // Init PO untuk satu baris
    function initRowPO(row) {
        const kodeWarnaVal = kodeWarna.value.trim();
        const warnaVal     = warnaInput.value.trim();

        const itemSelect = row.querySelector('.item-typeCus');
        const poSelect   = row.querySelector('.po-select');

        if (!itemSelect || !poSelect) return;
        if (!kodeWarnaVal || !warnaVal) return;

        const itemType = itemSelect.value;
        if (!itemType) {
            poSelect.innerHTML = '<option value="">Pilih PO</option>';
            return;
        }

        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const idInduk        = selectedOption?.dataset.idInduk || '';

        fetchPOByKodeWarna(kodeWarnaVal, warnaVal, itemType, idInduk, poSelect);
    }

    // Init PO untuk semua baris
    function initAllRowPO() {
        const rows = poTable.querySelectorAll('tbody tr');
        rows.forEach(row => initRowPO(row));
    }

    // Hitung total qty & sisa kapasitas
    function calculateTotalAndRemainingCapacity() {
        const rows = poTable.querySelectorAll("tbody tr");
        let totalQtyCelup = 0;

        rows.forEach(row => {
            const lastStatusEl = row.querySelector("input[name='last_status[]']");
            const lastStatus = lastStatusEl ? lastStatusEl.value.trim().toLowerCase() : "";
            if (["scheduled", "bon", "celup", "bongkar", "press", "oven", "tes luntur", "rajut pagi", "reschedule"].includes(lastStatus)) {
                const qtyInput = row.querySelector("input[name='qty_celup[]']");
                const qtyCelup = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;
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
            if (sisaKapasitas <= -1) {
                alert("⚠️ Sisa Kapasitas negatif!");
                sisaKapasitasElement.classList.add("is-invalid");
                sisaKapasitasElement.focus();
            } else {
                sisaKapasitasElement.classList.remove("is-invalid");
            }
        }
    }

    // Modal Edit Tanggal
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.editRow');
        if (button) {
            const idCelup = button.dataset.idCelup;
            const modalEl = document.getElementById('editModal');
            if (!modalEl) {
                console.error('Modal edit tidak ditemukan!');
                return;
            }
            const modal = new bootstrap.Modal(modalEl);
            document.getElementById('idCelup').value = idCelup;
            document.getElementById('tanggal_schedule').value = button.dataset.tanggalSchedule;
            modal.show();
        }
    });

    $('#editModal form').submit(function(e) {
        e.preventDefault();
        const formData = {
            id_celup: $('#idCelup').val(),
            tanggal_schedule: $('#tanggal_schedule').val()
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

    // Modal Edit MESIN
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.editMesin');
        if (!btn) return;

        const idCelup = btn.dataset.idCelup;
        if (!idCelup) {
            console.error('id_celup tidak ditemukan di data-id-celup');
            return;
        }

        const modalEl     = document.getElementById('editModalMesin');
        const modal       = new bootstrap.Modal(modalEl);
        const slotSelect  = document.getElementById('targetSlot');
        const btnSave     = document.getElementById('btnSaveMesin');

        document.getElementById('idCelupMesin').value = idCelup;
        slotSelect.innerHTML = '<option value="" selected disabled>— Memuat… —</option>';
        btnSave.disabled = true;

        $.ajax({
            url: '<?= base_url(session("role") . "/schedule/getPindahMesin") ?>',
            type: 'POST',
            dataType: 'json',
            data: { id_celup: idCelup }
        })
        .done(function (res) {
            slotSelect.innerHTML = '<option value="" selected disabled>— Pilih Mesin & Slot —</option>';

            if (!res || !res.success) {
                console.error('Response tidak valid:', res);
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.textContent = 'Gagal memuat slot';
                slotSelect.appendChild(opt);
                btnSave.disabled = true;
                modal.show();
                return;
            }

            const schedule = res.schedule || {};
            const slotsRaw = Array.isArray(res.slots) ? res.slots : [];

            if (slotsRaw.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.textContent = 'Tidak ada slot tersedia';
                slotSelect.appendChild(opt);
                btnSave.disabled = true;
                modal.show();
                return;
            }

            const currentKg = parseFloat(schedule.kg_celup || 0) || 0;
            const currentId = String(schedule.id_celup || '');
            let adaSlotValid = false;

            slotsRaw.forEach(slot => {
                const idMesin     = slot.id_mesin;
                const lotUrut     = slot.lot_urut;
                const noMesin     = slot.no_mesin ?? '-';
                const maxCapsNum  = parseFloat(slot.max_caps || 0) || 0;
                const existingKg  = parseFloat(slot.kg_celup || 0) || 0;
                const slotIdCelup = String(slot.id_celup || '');
                const statusSlot  = slot.status_slot || 'unknown';

                if (!idMesin || !lotUrut) return;
                if (maxCapsNum <= 0) return;

                let totalIfMove;
                if (slotIdCelup === currentId) {
                    totalIfMove = existingKg;
                } else {
                    totalIfMove = existingKg + currentKg;
                }

                if (totalIfMove > maxCapsNum) {
                    return;
                }

                adaSlotValid = true;

                let statusText = statusSlot;
                if (slotIdCelup === currentId) {
                    statusText = 'Kapasitas Sekarang';
                } else if (statusSlot === 'terisi') {
                    statusText = 'Terisi Sch Lain';
                } else if (statusSlot === 'kosong') {
                    statusText = 'Kosong';
                }

                const opt = document.createElement('option');
                opt.value = idMesin + '|' + lotUrut;
                opt.textContent = `Mesin ${noMesin} • Lot ${lotUrut} — ${statusText} (${existingKg} / ${maxCapsNum} kg)`;

                if (
                    String(schedule.id_mesin || '') === String(idMesin) &&
                    String(schedule.lot_urut || '') === String(lotUrut)
                ) {
                    opt.selected = true;
                }

                slotSelect.appendChild(opt);
            });

            if (!adaSlotValid) {
                slotSelect.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.selected = true;
                opt.textContent = 'Tidak ada slot yang cukup kapasitas';
                slotSelect.appendChild(opt);
                btnSave.disabled = true;
            } else {
                btnSave.disabled = false;
            }

            modal.show();
        })
        .fail(function (xhr) {
            alert('Gagal memuat daftar slot mesin');
            console.error(xhr.responseText || xhr.statusText || xhr.status);
            slotSelect.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = 'Error memuat data';
            slotSelect.appendChild(opt);
            btnSave.disabled = true;
            modal.show();
        });
    });

    // Submit edit mesin
    $('#formEditMesin').on('submit', async function(e) {
        e.preventDefault();
        const form = this;

        const { isConfirmed } = await Swal.fire({
            title: 'Simpan perubahan?',
            text: 'Mesin schedule akan diperbarui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        });

        if (!isConfirmed) return;

        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: form.action,
            type: 'POST',
            data: $(form).serialize(),
            dataType: 'json'
        })
        .done(res => {
            Swal.close();

            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Mesin schedule berhasil diupdate',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: res.message || 'Gagal update mesin'
                });
            }
        })
        .fail(xhr => {
            Swal.close();

            let msg = 'Terjadi kesalahan server';
            try {
                const j = JSON.parse(xhr.responseText);
                if (j && j.message) msg = j.message;
            } catch (_) {}

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `<small>${msg}</small>`
            });
            console.error(xhr.responseText || xhr.statusText || xhr.status);
        });
    });

    // Event perubahan qty_celup
    poTable.addEventListener("input", function(e) {
        if (e.target.name === "qty_celup[]") {
            calculateTotalAndRemainingCapacity();
        }
    });

    // Change PO → fetch detail & qty
    if (poTable) {
        poTable.addEventListener("change", function(e) {
            if (e.target.classList.contains("po-select")) {
                const poSelect = e.target;
                const tr = poSelect.closest("tr");
                const itemTypeSelect = tr.querySelector(".item-typeCus");
                const itemTypeValue = itemTypeSelect ? itemTypeSelect.value : '';
                const kodeWarnaValue = kodeWarna.value;
                const warnaVal = warnaInput.value;
                const idIndukValue = itemTypeSelect && itemTypeSelect.selectedOptions[0]
                    ? (itemTypeSelect.selectedOptions[0].getAttribute("data-id-induk") || 0)
                    : 0;

                if (poSelect.value && itemTypeValue && kodeWarnaValue) {
                    fetchQtyAndKebutuhanPO(poSelect.value, kodeWarnaValue, tr, warnaVal, itemTypeValue, idIndukValue);
                    fetchPODetails(poSelect.value, tr, itemTypeValue, kodeWarnaValue);
                } else {
                    resetPODetails(tr);
                }
            }
        });
    }

    // Fetch detail PO (delivery, qty_po)
    function fetchPODetails(poNo, tr, itemType, kodeWarnaVal) {
        const url = `<?= base_url(session('role') . "/schedule/getPODetails") ?>?no_model=${poNo}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarnaVal)}`;
        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log("Data PO details:", data);
                if (data && !data.error) {
                    const startInput   = tr.querySelector("input[name='tgl_start_mc[]']");
                    const delAwalInput = tr.querySelector("input[name='delivery_awal[]']");
                    const delAkhirInput= tr.querySelector("input[name='delivery_akhir[]']");
                    const qtyPoInput   = tr.querySelector("input[name='qty_po[]']");

                    if (startInput)   startInput.value   = data.start_mesin || '';
                    if (delAwalInput) delAwalInput.value = data.delivery_awal || '';
                    if (delAkhirInput)delAkhirInput.value= data.delivery_akhir || '';
                    if (qtyPoInput)   qtyPoInput.value   = parseFloat(data.kg_po).toFixed(2);
                } else {
                    console.error('Error fetching PO details:', data.error || 'No data found');
                }
            })
            .catch(error => {
                console.error('Error fetching PO details:', error);
            });
    }

    // Fetch Qty & Kebutuhan PO
    function fetchQtyAndKebutuhanPO(noModel, kodeWarnaVal, tr, warnaVal, itemType, idInduk) {
        const itemTypeEncoded = encodeURIComponent(itemType);
        idInduk = idInduk || 0;
        const url = `<?= base_url(session('role') . "/schedule/getQtyPO") ?>?no_model=${noModel}&kode_warna=${encodeURIComponent(kodeWarnaVal)}&color=${encodeURIComponent(warnaVal)}&item_type=${itemTypeEncoded}&id_induk=${idInduk}`;
        console.log("Request URL QtyPO:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data && !data.error) {
                    const qtyPoInput      = tr.querySelector("input[name='qty_po[]']");
                    const qtyPoPlusInput  = tr.querySelector("input[name='qty_po_plus[]']");
                    const kgKebSpan       = tr.querySelector(".kg_kebutuhan");
                    const tagihanSpan     = tr.querySelector(".tagihan");

                    if (qtyPoInput)     qtyPoInput.value     = parseFloat(data.kg_po).toFixed(2);
                    if (qtyPoPlusInput) qtyPoPlusInput.value = data.qty_po_plus != null ? parseFloat(data.qty_po_plus).toFixed(2) : '';
                    if (kgKebSpan)      kgKebSpan.textContent= parseFloat(data.kg_po).toFixed(2);
                    if (tagihanSpan)    tagihanSpan.textContent = data.sisa_kg_po != null ? parseFloat(data.sisa_kg_po).toFixed(2) : '0.00';
                } else {
                    console.error('Error fetching Qty PO details:', data.error || 'No data found');
                }
            })
            .catch(error => {
                console.error('Error fetching Qty data:', error);
            });
    }

    // Reset detail PO
    function resetPODetails(tr) {
        const fields = ['tgl_start_mc[]', 'delivery_awal[]', 'delivery_akhir[]', 'qty_po[]', 'qty_po_plus[]'];
        fields.forEach(field => {
            const element = tr.querySelector(`input[name="${field}"]`);
            if (element) element.value = '';
        });
        tr.querySelectorAll('.kg_kebutuhan, .sisa_jatah, .tagihan').forEach(span => span.textContent = '0.00');
    }

    // Inisialisasi kapasitas di awal
    calculateTotalAndRemainingCapacity();

    // Tambah baris baru
    document.getElementById("addRow").addEventListener("click", function() {
        const tbody = poTable.querySelector("tbody");
        const newRow = document.createElement("tr");
        const rowNo = tbody.rows.length + 1;

        newRow.innerHTML = `
            <td class="text-center">${rowNo}</td>
            <td>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Item Type</label>
                            <select class="form-select item-typeCus" name="item_type[]" data-selected="" required>
                                <option value="">Pilih Item Type</option>
                            </select>
                            <input type="hidden" name="id_celup[]" value="">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>PO</label>
                            <select class="form-select po-select" name="po[]" data-selected="" required>
                                <option value="">Pilih PO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Tgl Start MC</label>
                            <input type="date" class="form-control" name="tgl_start_mc[]" required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Delivery Awal</label>
                            <input type="date" class="form-control" name="delivery_awal[]" readonly>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Delivery Akhir</label>
                            <input type="date" class="form-control" name="delivery_akhir[]" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Qty PO</label>
                            <input type="number" class="form-control" name="qty_po[]" readonly>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>Qty PO (+)</label>
                            <input type="number" class="form-control" name="qty_po_plus[]" readonly>
                        </div>
                    </div>
                    <div class="col-4">
                        <label>Qty Celup</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" name="qty_celup[]" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label>KG Kebutuhan :</label><br />
                            <span class="badge bg-info">
                                <span class="kg_kebutuhan">0.00</span> KG
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Tagihan :</label><br />
                            <span class="badge bg-info">
                                <span class="tagihan">0.00</span> KG
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Last Status</label><br />
                            <span class="badge bg-info">scheduled</span>
                            <input type="hidden" class="form-control last_status" name="last_status[]" value="scheduled">
                        </div>
                    </div>
                    <div class="col-3 d-flex align-items-center">
                        <div class="form-group">
                            <label>PO + :</label>
                            <fieldset>
                                <legend></legend>
                                <div>
                                    <input type="radio" name="po_plus_new_${rowNo}" value="1">
                                    <label>Iya</label>
                                    <input type="radio" name="po_plus_new_${rowNo}" value="0" checked>
                                    <label>Tidak</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Keterangan PO</label><br />
                            <textarea class="form-control keterangan" name="keterangan" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Keterangan Schedule</label><br />
                            <textarea class="form-control ket_schedule[]" name="ket_schedule[]" id="ket_schedule"></textarea>
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

        // Hitung kapasitas kalau qty_celup di baris baru diubah
        const qtyCelupInput = newRow.querySelector("input[name='qty_celup[]']");
        if (qtyCelupInput) {
            qtyCelupInput.addEventListener("input", function() {
                calculateTotalAndRemainingCapacity();
            });
        }

        // Refresh item_type & PO semua baris (termasuk baris baru)
        fetchAndFillItemTypes();
    });

    // Hapus baris
    poTable.addEventListener("click", function(event) {
        const removeBtn = event.target.closest(".removeRow");
        if (removeBtn) {
            const row = removeBtn.closest("tr");
            const tbody = poTable.querySelector("tbody");
            const idCelupInput = row.querySelector('input[name^="id_celup"]');
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
                            data: { id_celup: idCelup },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    window.location.href = '<?= base_url(session('role') . '/schedule') ?>';
                                } else {
                                    Swal.fire("Gagal!", "Data Schedule gagal dihapus.", "error");
                                }
                            },
                            error: function() {
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

    // Inisialisasi awal
    fetchAndFillItemTypes();
    kodeWarna.addEventListener('change', fetchAndFillItemTypes);
    warnaInput.addEventListener('change', fetchAndFillItemTypes);
});
</script>

<?php $this->endSection(); ?>
