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

    .add-more-repeat-btn {
        background: transparent;
        border: none;
        font-size: 16px;
        line-height: 1;
        color: #c8f7c5;
        cursor: pointer;
        font-weight: bold;
    }

    .add-more-repeat-btn:hover {
        color: #2d7a2d;
        transform: scale(1.2);
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
                                                    <!-- Modal buttons -->
                                                    <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#acceptModal<?= $row['id_retur'] ?>">
                                                        <i class="fas fa-check"></i> Accept
                                                    </button>
                                                    <!-- <button type="button" class="btn btn-warning open-repeat-modal" data-id-retur="<?= $row['id_retur'] ?>" data-bs-toggle="modal" data-bs-target="#acceptRepeatModal<?= $row['id_retur'] ?>">
                                                        <i class="fas fa-check"></i> Repeat
                                                    </button> -->
                                                    <button type="button" class="btn btn-danger " data-bs-toggle="modal" data-bs-target="#rejectModal<?= $row['id_retur'] ?>">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
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
                <?= csrf_field() ?>
                <input type="hidden" name="id_retur" value="<?= $row['id_retur'] ?>">
                <?php foreach ($currentFilters as $key => $value): ?>
                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                <?php endforeach; ?>
                <div class="modal-content">
                    <div class="modal-header text-white">
                        <h5 class="modal-title" id="acceptRepeatModalLabel<?= $row['id_retur'] ?>">Konfirmasi Approve Untuk Repeat</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-2">

                            <div id="headerReturData" class="mb-3"></div>
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
            "language": {
                "search": "Cari:",
                "zeroRecords": "Data tidak ditemukan",
            }
        });

        // select2 init helper (for a given container/modal)
        function initSelect2In(container) {
            container.find('.select2-repeat').each(function() {
                // if already initialized skip
                if ($(this).hasClass('select2-hidden-accessible')) return;

                $(this).select2({
                    dropdownParent: container.closest('.modal'),
                    placeholder: 'Ketik minimal 3 karakter...',
                    minimumInputLength: 3,
                    ajax: {
                        delay: 250,
                        url: "<?= base_url($role . '/warehouse/getNoModel') ?>",
                        dataType: 'json',
                        data: function(params) {
                            // try to give context from the same repeat-item
                            let item = $(this).closest('.repeat-item');
                            let old = item.find('.old-model').val() || '';
                            let kode = item.find('.kode-warna').val() || '';
                            return {
                                q: params.term,
                                old: old,
                                kode: kode
                            };
                        }.bind(this),
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    }
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
                        renderHeaderReturData(res[0]);
                    }

                    // res -> array of { id_karung, no_model, kg, cones, keterangan }
                    if (!Array.isArray(res) || res.length === 0) {
                        // buat 1 template kosong kalau data kosong
                        let emptyHtml = buildRepeatItem({
                            no_model: '',
                            kg: '',
                            cones: '',
                        }, 0);
                        wrapper.append(emptyHtml);
                    } else {
                        res.forEach(function(item, idx) {
                            let html = buildRepeatItem(item, idx);
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

        function renderHeaderReturData(item) {
            let kgVal = item.kgs_retur ?? item.kg ?? '';
            let conesVal = item.cns_retur ?? item.cones ?? '';
            let noKrgVal = item.krg_retur ?? item.no_karung ?? '';
            let lotVal = item.lot_retur ?? item.lot ?? '';

            $("#headerReturData").html(`
                <div class="row">
                    <div class="col-lg-3">
                        <label>KG Retur</label>
                        <input type="text" class="form-control" value="${kgVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>Cones Retur</label>
                        <input type="text" class="form-control" value="${conesVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>No Karung Retur</label>
                        <input type="text" class="form-control" value="${noKrgVal}" readonly>
                    </div>

                    <div class="col-lg-3">
                        <label>Lot Retur</label>
                        <input type="text" class="form-control" value="${lotVal}" readonly>
                    </div>
                </div>
            `);
        }

        // helper build repeat item HTML (string)
        function buildRepeatItem(item, index) {
            console.log(item);
            console.log("INDEX:", index);
            // item: { id_karung, no_model, kg, cones, keterangan }
            // index digunakan untuk menentukan tombol hapus (sembunyikan pada pertama)
            let noModelVal = item.no_model ?? '';
            let kgVal = item.kgs_retur ?? item.kg ?? '';
            let conesVal = item.cns_retur ?? item.cones ?? '';
            let noKrgVal = item.krg_retur ?? item.no_karung ?? '';
            let lotVal = item.lot_retur ?? item.lot ?? '';

            return `
                <div class="repeat-item mb-3 border rounded p-3 position-relative">
                    <input type="hidden" class="old-model" value="${noModelVal}">
                    <input type="hidden" class="kode-warna" value="">

                   ${index === 0 ? `
                        <button type="button" class="btn btn-success btn-sm mt-2 add-more-repeat-btn" data-id-retur="<?= $row['id_retur'] ?>">
                            +
                        </button>
                    ` : `
                        <button type="button" class="btn-remove-repeat position-absolute top-0 end-0 p-1 m-1"
                            style="border:none; background:#f8d7da; color:#a00; border-radius:4px; font-weight:bold;">
                            ×
                        </button>
                    `}

                    <div class="form-group mb-2">
                        <label>No Model Repeat</label>
                        <select name="model_repeat[]" class="form-control select2-repeat" required>
                            ${ noModelVal ? `<option value="${noModelVal}" selected>${noModelVal}</option>` : '' }
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <div class="row">
                            <div class="col-lg-4">
                                <label>KG Repeat</label>
                                <input type="number" name="kg_repeat[]" step="0.01" min="0"
                                       class="form-control kg-input"
                                       value=0 required>
                            </div>
                            <div class="col-lg-4">
                                <label>Cones Repeat</label>
                                <input type="number" name="cones_repeat[]" step="1" min="0"
                                       class="form-control cones-input"
                                       value=0 required>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // add more repeat - delegasi karena button dihasilkan dinamis
        $(document).on('click', '.add-more-repeat-btn', function() {
            let idRetur = $(this).data('id-retur');
            let wrapper = $('#repeatWrapper' + idRetur);

            // hitung jumlah existing items
            let newIndex = wrapper.find('.repeat-item').length;

            // generate row baru dengan index yg benar
            let newItemHtml = buildRepeatItem({
                no_model: '',
                kg: '',
                cones: '',
                keterangan: ''
            }, newIndex);

            wrapper.append(newItemHtml);
            initSelect2In(wrapper.closest('.modal'));
        });

        // remove repeat
        $(document).on('click', '.btn-remove-repeat', function() {
            $(this).closest('.repeat-item').remove();
        });

        // VALIDASI TOTAL KGS & CONES (delegated input)
        $(document).on('input', "#repeatWrapper[data-maxkg] input[name='kg_repeat[]'], #repeatWrapper[data-maxcns] input[name='cones_repeat[]']", function() {
            let modal = $(this).closest('.modal');
            let wrapper = modal.find("[id^=repeatWrapper]");

            let maxKg = parseFloat(wrapper.data('maxkg')) || 0;
            let maxCns = parseFloat(wrapper.data('maxcns')) || 0;

            let totalKg = 0;
            let totalCns = 0;

            wrapper.find("input[name='kg_repeat[]']").each(function() {
                totalKg += parseFloat($(this).val()) || 0;
            });

            wrapper.find("input[name='cones_repeat[]']").each(function() {
                totalCns += parseFloat($(this).val()) || 0;
            });

            // jika melebihi, batalkan perubahan terakhir
            if (totalKg > maxKg) {
                Swal.fire('Warning', 'Total KG tidak boleh melebihi ' + maxKg, 'warning');
                $(this).val('');
                return;
            }

            if (totalCns > maxCns) {
                Swal.fire('Warning', 'Total CONES tidak boleh melebihi ' + maxCns, 'warning');
                $(this).val('');
                return;
            }
        });

        // saat submit, double-check totals
        $(document).on('submit', '.repeat-form', function(e) {
            let form = $(this);
            let wrapper = form.find("[id^=repeatWrapper]");
            let maxKg = parseFloat(wrapper.data('maxkg')) || 0;
            let maxCns = parseFloat(wrapper.data('maxcns')) || 0;

            let totalKg = 0;
            let totalCns = 0;

            wrapper.find("input[name='kg_repeat[]']").each(function() {
                totalKg += parseFloat($(this).val()) || 0;
            });

            wrapper.find("input[name='cones_repeat[]']").each(function() {
                totalCns += parseFloat($(this).val()) || 0;
            });

            if (totalKg > maxKg) {
                e.preventDefault();
                Swal.fire('Error', 'Total KG melebihi batas: ' + maxKg, 'error');
                return false;
            }

            if (totalCns > maxCns) {
                e.preventDefault();
                Swal.fire('Error', 'Total CONES melebihi batas: ' + maxCns, 'error');
                return false;
            }

            // validasi ok -> submit
            return true;
        });

    });
</script>

<?php $this->endSection(); ?>