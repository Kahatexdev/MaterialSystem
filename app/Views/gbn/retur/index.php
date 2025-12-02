<?php $this->extend($role . '/retur/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .btn-remove-repeat {
        background: transparent;
        border: none;
        font-size: 16px;
        line-height: 1;
        color: #dc3545;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-remove-repeat:hover {
        color: #b02a37;
        transform: scale(1.2);
    }

    .select2-container--open .select2-dropdown {
        position: fixed !important;
    }
</style>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
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
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <!-- Content utama -->
    <div class="container-fluid py-4">
        <div class="row my-4">

            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">

                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                    <h5 class="font-weight-bolder mb-0">Data <?= $title ?></h5>
                                </div>
                            </div>
                            <div>

                                <form method="get" action="<?= base_url($role . '/retur') ?>">
                                    <?php
                                    // Ambil semua query GET dari URL
                                    $currentFilters = $_GET ?? [];
                                    ?>
                                    <div class="d-flex align-items-center gap-3">
                                        <select name="jenis" id="jenis" class="form-control">
                                            <option value="">Jenis Bahan Baku</option>
                                            <?php foreach ($jenis as $row): ?>
                                                <option value="<?= $row['jenis'] ?>">
                                                    <?= $row['jenis'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <select name="area" id="area" class="form-control">
                                            <option value="">Area</option>
                                            <?php foreach ($area as $row): ?>
                                                <option value="<?= $row ?>">
                                                    <?= $row ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option value="SAMPLE">SAMPLE</option>
                                        </select>

                                        <input type="date" name="tgl_retur" id="tgl_retur" class="form-control"
                                            value="" placeholder="Tanggal Retur" />
                                        <input type="text" name="no_model" id="no_model" class="form-control"
                                            value="" placeholder="No Model" />
                                        <input type="text" name="kode_warna" id="kode_warna" class="form-control"
                                            value="" placeholder="Kode Warna" />
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-filter"></i>
                                        </button>
                                        <a href="<?= base_url($role . '/retur') ?>" class="btn btn-secondary ms-2">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    </div>
                                </form>


                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row my-4">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Section -->

                        <!-- Table Section -->
                        <?php if (!$isFiltered): ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i> Silakan pilih minimal satu filter untuk menampilkan data retur.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table id="returTable" class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Model</th>
                                            <th>Item Type</th>
                                            <th>Kode Warna</th>
                                            <th>Warna</th>
                                            <th>Kgs Retur</th>
                                            <th>Cns Retur</th>
                                            <th>Lot Retur</th>
                                            <th>Area Retur</th>
                                            <th>Tgl Retur</th>
                                            <th>Kategori Retur</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <?php $i = 1; ?>
                                        <?php foreach ($retur as $row): ?>
                                            <?php
                                            $allowRepeat = in_array($row['kategori'], [
                                                'BB SISA EXPORT',
                                                'KIRIM LEBIH GBN SEBELUM EXPORT'
                                            ]);
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $row['no_model'] ?></td>
                                                <td><?= $row['item_type'] ?></td>
                                                <td><?= $row['kode_warna'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 20px; height: 20px; background-color: <?= $row['kode_warna'] ?>; border-radius: 4px; margin-right: 8px;"></div>
                                                        <?= $row['warna'] ?>
                                                    </div>
                                                </td>
                                                <td><?= number_format($row['kgs_retur'], 2) ?></td>
                                                <td><?= $row['cns_retur'] ?></td>
                                                <td><?= $row['lot_retur'] ?></td>
                                                <td><?= $row['area_retur'] ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tgl_retur'])) ?></td>
                                                <td><?= $row['kategori'] ?></td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <!-- Modal buttons -->
                                                        <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#acceptModal<?= $row['id_retur'] ?>">
                                                            <i class="fas fa-check"></i> Accept
                                                        </button>
                                                        <?php if ($allowRepeat): ?>
                                                            <button type="button" class="btn btn-warning open-repeat-modal" data-id-retur="<?= $row['id_retur'] ?>" data-bs-toggle="modal" data-bs-target="#acceptRepeatModal<?= $row['id_retur'] ?>">
                                                                <i class="fas fa-refresh"></i> Repeat
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#rejectModal<?= $row['id_retur'] ?>">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; ?>

                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <?php if (!empty($tglReq)): ?>
                            <div class="alert alert-danger text-center text-white mb-0 py-3 shadow-sm">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong class="text-white">Perhatian!</strong> Beri tindakan untuk retur pada tanggal:
                                <br>
                                <span class="fw-semibold text-white">
                                    <?= implode(', ', array_map(fn($rq) => date('d M Y', strtotime($rq['tgl_retur'])), $tglReq)) ?>
                                </span>
                            </div>
                        <?php else: ?>

                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Accept -->
<?php foreach ($retur as $row): ?>
    <div class="modal fade" id="acceptModal<?= $row['id_retur'] ?>" tabindex="-1" aria-labelledby="acceptModalLabel<?= $row['id_retur'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url($role . '/retur/approve') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_retur" value="<?= $row['id_retur'] ?>">
                <?php foreach ($currentFilters as $key => $value): ?>
                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                <?php endforeach; ?>
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="acceptModalLabel<?= $row['id_retur'] ?>">Konfirmasi Approve</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin <strong>menerima</strong> retur ini?
                        <div class="form-group mt-3">
                            <label for="catatan_accept<?= $row['id_retur'] ?>">Keterangan</label>
                            <textarea name="catatan" id="catatan_accept<?= $row['id_retur'] ?>" class="form-control" rows="3" placeholder="Tulis keterangan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white">Ya, Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<!-- MODAL ACC REPEAT -->
<?php foreach ($retur as $row): ?>
    <div class="modal fade" id="acceptRepeatModal<?= $row['id_retur'] ?>" tabindex="-1" aria-labelledby="acceptRepeatModalLabel<?= $row['id_retur'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="<?= base_url($role . '/retur/approveRepeat') ?>" method="post" class="repeat-form">
                <?php foreach ($currentFilters as $key => $value): ?>
                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                <?php endforeach; ?>
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <?php
                        $sub = ($row['kategori'] === "BB SISA EXPORT") ? "Repeat" : "Kirim Bahan Baku";
                        ?>
                        <h5 class="modal-title" id="acceptRepeatModalLabel<?= $row['id_retur'] ?>">Konfirmasi Approve Untuk <?= $sub ?></h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-2">

                            <div id="headerReturData<?= $row['id_retur'] ?>" class="mb-3"></div>
                            <!-- Wrapper akan diisi AJAX. Data-max disimpan di wrapper -->
                            <div id="repeatWrapper<?= $row['id_retur'] ?>" data-maxkg="<?= $row['kgs_retur'] ?>" data-maxcns="<?= $row['cns_retur'] ?>" class="mt-4"></div>

                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info text-white submit-repeat-btn">Ya, Approve</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Reject -->
<?php foreach ($retur as $row): ?>
    <div class="modal fade" id="rejectModal<?= $row['id_retur'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $row['id_retur'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= base_url($role . '/retur/reject') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_retur" value="<?= $row['id_retur'] ?>">
                <?php foreach ($currentFilters as $key => $value): ?>
                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                <?php endforeach; ?>
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="rejectModalLabel<?= $row['id_retur'] ?>">Konfirmasi Reject</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin <strong>menolak</strong> retur ini?
                        <div class="form-group mt-3">
                            <label for="catatan_reject<?= $row['id_retur'] ?>">Catatan Penolakan</label>
                            <textarea name="catatan" id="catatan_reject<?= $row['id_retur'] ?>" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#returTable').DataTable({
            "ordering": true,
            "paging": true,
            "searching": true,
            "info": true,
            "width": "100%",
            "language": {
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
            }
        });

        // select2 init helper (for a given container/modal)
        function initSelect2In(container) {
            container.find('.select2-repeat').each(function() {

                let $select = $(this);

                // sudah init? skip
                if ($select.data('select2')) return;

                $select.select2({
                    dropdownParent: container.closest('.modal').length ?
                        container.closest('.modal') : container,
                    placeholder: 'Ketik minimal 3 karakter...',
                    minimumInputLength: 3,
                    width: '100%',
                    ajax: {
                        delay: 250,
                        url: "<?= base_url($role . '/warehouse/getNoModel') ?>",
                        dataType: 'json',
                        data: (params) => {
                            let item = $select.closest('.repeat-item');
                            return {
                                term: params.term,
                                old: item.find('.old-model').val() || '',
                                kode: item.find('.kode-warna').val() || ''
                            };
                        },
                        processResults: function(response) {
                            let arr = response.data || [];
                            return {
                                results: arr.map(item => ({
                                    id: `${item.no_model} | ${item.item_type} | ${item.kode_warna} | ${item.color}`,
                                    text: `${item.no_model} | ${item.item_type} | ${item.kode_warna} | ${item.color}`
                                }))
                            };
                        }
                    }
                }).on('select2:open', function() {
                    let $dropdown = $('.select2-container--open .select2-dropdown');
                    let pos = $select[0].getBoundingClientRect();

                    $dropdown.css({
                        position: 'fixed',
                        top: pos.bottom + "px",
                        left: pos.left + "px",
                        width: pos.width + "px"
                    });
                });
            });
        }


        // Open repeat modal and load data via AJAX
        $(document).on('click', '.open-repeat-modal', function() {
            let idRetur = $(this).data('id-retur');
            let modalId = '#acceptRepeatModal' + idRetur;
            let wrapper = $(modalId).find('#repeatWrapper' + idRetur);

            // kosongkan wrapper
            wrapper.html('');

            // ambil data karung/repeat dari server
            $.ajax({
                url: "<?= base_url($role . '/retur/getDataRepeat') ?>",
                data: {
                    id_retur: idRetur
                },
                dataType: 'json',
                success: function(res) {

                    // tampilkan header retur hanya sekali
                    if (res && res.length > 0) {
                        renderHeaderReturData(res[0], idRetur);
                    }

                    // res -> array of { id_karung, no_model, kg, cones, keterangan }
                    if (!Array.isArray(res) || res.length === 0) {
                        // buat 1 template kosong kalau data kosong
                        let emptyHtml = buildRepeatItem({
                            no_model: '',
                            kg: '',
                            cones: '',
                        }, 0, idRetur);
                        wrapper.append(emptyHtml);
                    } else {
                        res.forEach(function(item, idx) {
                            let html = buildRepeatItem(item, idx, idRetur);
                            wrapper.append(html);
                        });
                    }

                    // init select2 inside this modal
                    initSelect2In($(modalId));

                },
                error: function(err) {
                    console.error(err);
                    Swal.fire('Error', 'Gagal mengambil data repeat dari server', 'error');
                }
            });
        });

        function renderHeaderReturData(item, idRetur) {
            let areaVal = item.area_retur ?? '';
            let modelVal = item.no_model ?? '';
            let itemTypeVal = item.item_type ?? '';
            let kodeWarnaVal = item.kode_warna ?? '';
            let warnaVal = item.warna ?? '';
            let kategoriVal = item.kategori ?? '';

            $("#headerReturData" + idRetur).html(`
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label>No Model</label>
                        <input type="text" name="no_model_retur" class="form-control old-model" value="${modelVal}" readonly>
                        <input type="hidden" name="area_retur" class="form-control old-model" value="${areaVal}" readonly>
                        <input type="hidden" name="kategori" class="form-control old-model" value="${kategoriVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>Item Type</label>
                        <input type="text" name="item_type_retur" class="form-control" value="${itemTypeVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>Kode Warna</label>
                        <input type="text" name="kode_warna_retur" class="form-control kode-warna" value="${kodeWarnaVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>Warna</label>
                        <input type="text" name="color_retur" class="form-control" value="${warnaVal}" readonly>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-12">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
            `);
        }

        // helper build repeat item HTML (string)
        function buildRepeatItem(item, index, idRetur) {
            console.log(item);
            console.log("INDEX:", index);
            // item: { id_karung, no_model, kg, cones, keterangan }
            // index digunakan untuk menentukan tombol hapus (sembunyikan pada pertama)
            let noModelVal = item.no_model ?? '';
            let kodeWarnaVal = item.kode_warna ?? '';
            let kgVal = item.kgs_retur ?? item.kg ?? '';
            let conesVal = item.cns_retur ?? item.cones ?? '';
            let noKrgVal = item.krg_retur ?? item.no_karung ?? '';
            let lotVal = item.lot_retur ?? item.lot ?? '';

            return `
                <div class="repeat-item mb-3 border rounded p-3 position-relative">
                    <input type="hidden" name="id_retur[]" class="id-retur" value="${item.id_retur}">
                    <input type="hidden" class="old-model" value="${noModelVal}">
                    <input type="hidden" class="kode-warna" value="${kodeWarnaVal}">
                    <input type="hidden" class="no-krg" value="${noKrgVal}">

                    <div class="row">
                        <div class="col-lg-3">
                            <label>KG Retur</label>
                            <input type="text" name="kg_retur[${item.id_retur}]" class="form-control max-kg" value="${kgVal}" readonly>
                        </div>

                        <div class="col-lg-3">
                            <label>Cones Retur</label>
                            <input type="text" name="cns_retur[${item.id_retur}]" class="form-control max-cns" value="${conesVal}" readonly>
                        </div>

                        <div class="col-lg-3">
                            <label>No Karung Retur</label>
                            <input type="text" name="no_karung_retur[${item.id_retur}]" class="form-control" value="${noKrgVal}" readonly>
                        </div>

                        <div class="col-lg-3">
                            <label>Lot Retur</label>
                            <input type="text" name="lot_retur" class="form-control" value="${lotVal}" readonly>
                        </div>
                    </div>

                    <div class="repeat-lines mt-4" data-id-retur="${item.id_retur}">
                        ${buildRepeatLine(noModelVal, item.id_retur, 0)}
                    </div>
                </div>
            `;
        }

        function buildRepeatLine(noModelVal, idRetur, index) {
            let removeBtn = index > 0 ?
                `<button type="button" class="btn btn-danger w-100 btn-remove-line" style="border:none; background:#f8d7da; color:#a00; border-radius:4px; font-weight:bold;">
                    <i class="fas fa-times"></i>
                </button>` :
                `<button type="button" class="btn btn-success w-100 add-more-repeat-btn" style="margin-top: 1.8rem; border:none; background:#d4edda; color:#155724; border-radius:4px; font-weight:bold;" data-id-retur="${idRetur}">
                    <i class="fas fa-plus"></i>
                </button>`;

            return `
                <div class="repeat-line mb-3 border p-2 rounded">

                    <div class="form-group mb-2">
                        <label>No Model Repeat</label>
                        <select name="model_repeat[${idRetur}][]" class="form-control select2-repeat" required>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-lg-5">
                            <label>KG Repeat</label>
                            <input type="number" name="kg_repeat[${idRetur}][]" step="0.01" min="0.01" 
                                class="form-control kg-input" value="0" required>
                        </div>

                        <div class="col-lg-5">
                            <label>Cones Repeat</label>
                            <input type="number" name="cones_repeat[${idRetur}][]" step="1"
                                class="form-control cones-input" value="0">
                        </div>

                        <div class="col-lg-2 d-flex align-items-end">
                            ${removeBtn}
                        </div>
                    </div>

                </div>
            `;
        }

        // add more repeat - delegasi karena button dihasilkan dinamis
        $(document).on('click', '.add-more-repeat-btn', function() {
            let idRetur = $(this).data('id-retur');

            // Cari parent repeat-item
            let container = $(this).closest('.repeat-item').find('.repeat-lines');

            // Hitung baris yang sudah ada per ID-retur / per repeat-item
            let index = container.find('.repeat-line').length;

            // Baris baru
            container.append(buildRepeatLine("", idRetur, index));

            let modal = $(this).closest('.modal');
            initSelect2In(modal);

        });

        $(document).on('click', '.btn-remove-line', function() {
            $(this).closest('.repeat-line').remove();
        });


        // VALIDASI TOTAL PER ID_RETUR (menggunakan wrapper spesifik)
        $(document).on('input', '.kg-input', function() {

            // cari parent repeat-item
            let item = $(this).closest('.repeat-item');

            // ambil max kg untuk ID retur ini
            let maxKg = parseFloat(item.find('.max-kg').val()) || 0;
            let noKrg = parseFloat(item.find('.no-krg').val()) || 0;

            // hitung total semua baris kg di repeat-lines dalam item ini saja
            let totalKg = 0;
            item.find('.kg-input').each(function() {
                totalKg += parseFloat($(this).val()) || 0;
            });

            // kalau total melebihi batas
            if (totalKg > maxKg) {
                Swal.fire("Warning", "Total KG no karung " + noKrg + " tidak boleh melebihi " + maxKg + "kg", "warning");
                $(this).val(0);
            }
        });

        $(document).on('input', '.cones-input', function() {

            let item = $(this).closest('.repeat-item');
            let maxCns = parseFloat(item.find('.max-cns').val()) || 0;
            let noKrg = parseFloat(item.find('.no-krg').val()) || 0;

            let totalCns = 0;
            item.find('.cones-input').each(function() {
                totalCns += parseFloat($(this).val()) || 0;
            });

            if (totalCns > maxCns) {
                Swal.fire("Warning", "Total Cones no karung " + noKrg + " tidak boleh melebihi " + maxCns + "cns", "warning");
                $(this).val(0);
            }
        });


        // saat submit, double-check totals
        $(document).on('submit', '.repeat-form', function(e) {
            let isValid = true;

            let modal = $(this).closest('.modal');

            // Pastikan hanya modal yang AKTIF yang divalidasi
            if (!modal.is(':visible')) {
                return true; // biarkan modal yang tidak aktif skip validasi
            }

            // Validasi hanya repeat-item di modal ini
            modal.find('.repeat-item').each(function() {
                let item = $(this);

                let maxKg = parseFloat(item.find('.max-kg').val()) || 0;
                let maxCns = parseFloat(item.find('.max-cns').val()) || 0;
                let noKrg = item.find('.no-krg').val() || "-";

                let totalKg = 0;
                let totalCns = 0;

                item.find("input[name^='kg_repeat']").each(function() {
                    totalKg += parseFloat($(this).val()) || 0;
                });

                item.find("input[name^='cones_repeat']").each(function() {
                    totalCns += parseFloat($(this).val()) || 0;
                });

                // KG melebihi batas
                if (totalKg > maxKg) {
                    e.preventDefault();
                    Swal.fire(
                        "Error",
                        "Total KG no karung " + noKrg + " melebihi " + maxKg,
                        "error"
                    );
                    isValid = false;
                    return false;
                }

                // CNS melebihi batas
                if (totalCns > maxCns) {
                    e.preventDefault();
                    Swal.fire(
                        "Error",
                        "Total Cones no karung " + noKrg + " melebihi " + maxCns,
                        "error"
                    );
                    isValid = false;
                    return false;
                }

                // KG harus tepat sama
                if (maxKg > 0 && totalKg !== maxKg) {
                    e.preventDefault();
                    Swal.fire(
                        "Error",
                        "Total KG no karung " + noKrg + " kurang dari " + maxKg,
                        "error"
                    );
                    isValid = false;
                    return false;
                }

                // CNS harus tepat sama
                if (maxCns > 0 && totalCns !== maxCns) {
                    e.preventDefault();
                    Swal.fire(
                        "Error",
                        "Total Cones no karung " + noKrg + " kurang dari " + maxCns,
                        "error"
                    );
                    isValid = false;
                    return false;
                }
            });

            return isValid;
        });

    });
</script>

<?php $this->endSection(); ?>