<?php $this->extend($role . '/retur/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    .ui-state-active {
        background: rgb(230, 153, 233) !important;
        color: #fff !important
    }

    .select2-container--default .select2-selection--single {
        border-radius: .5rem;
        height: 38px
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px
    }

    /* highlight baris yang kapasitasnya minus */
    .neg-cap {
        background-color: #ffe8e8 !important;
    }
</style>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Content utama -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="mb-0">Form Input Retur Sample</h3>
                </div>
                <div class="card-body">
                    <form action="<?= base_url($role . '/retur/saveReturSample') ?>" method="post">
                        <div id="kebutuhan-container">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label>Tanggal Retur</label>
                                    <input type="date" class="form-control" id="tgl_retur" name="tgl_retur" required>
                                </div>
                            </div>

                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home">
                                    <div class="kebutuhan-item">
                                        <!-- MASTER PICKERS -->
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>No Model</label>
                                                <input class="form-control" list="noModelOptions" id="no_model_input" placeholder="Ketik / pilih No Model" required>
                                                <datalist id="noModelOptions">
                                                    <!-- <?php foreach ($no_model as $item) : ?>
                                                        <option data-id="<?= $item['id_order'] ?>" value="<?= $item['no_model'] ?>"></option>
                                                        <?php endforeach; ?> -->
                                                    <option data-id="2911" value="SAMPLE"></option>
                                                </datalist>
                                                <input type="hidden" name="id_order" id="id_order">
                                                <input type="hidden" name="no_model" id="no_model">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Item Type</label>
                                                <input class="form-control" list="itemTypeOptions" id="item_type_input" placeholder="Pilih / ketik Item Type" required>
                                                <datalist id="itemTypeOptions"></datalist>
                                                <input type="hidden" name="item_type" id="item_type">
                                            </div>

                                            <div class="col-md-4">
                                                <label>Kode Warna</label>
                                                <input class="form-control" list="kodeWarnaOptions" id="kode_warna_input" placeholder="Pilih / ketik Kode Warna" required>
                                                <datalist id="kodeWarnaOptions"></datalist>
                                                <input type="hidden" name="kode_warna" id="kode_warna">
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label>Warna</label>
                                                <input class="form-control" list="warnaOptions" id="warna_input" placeholder="Pilih / ketik Warna" required>
                                                <datalist id="warnaOptions"></datalist>
                                                <input type="hidden" name="warna" id="warna">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Lot Retur</label>
                                                <input class="form-control" list="lot" id="lot" name="lot" placeholder="Lot" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Kategori</label>
                                                <input class="form-control" list="kategori" id="kategori" name="kategori" value="BB SISA SAMPLE" readonly>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label>Keterangan Gbn</label>
                                                <textarea class="form-control" list="Keterangan" id="keterangan" name="keterangan" placeholder="Keterangan"></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <h3 class="mb-3">Form Input Data Karung</h3>
                                        </div>

                                        <!-- TABEL KARUNG -->
                                        <div class="row g-3 mt-3">
                                            <div class="table-responsive">
                                                <table id="poTable" class="table table-bordered table-striped align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th width="100" class="text-center">No Karung</th>
                                                            <th class="text-center">Kg Retur</th>
                                                            <th class="text-center">Cones Retur</th>
                                                            <th class="text-center">Cluster</th>
                                                            <th class="text-center">Kapasitas</th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-info" id="addRow"><i class="fas fa-plus"></i></button>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><input type="text" class="form-control text-center" name="no_karung[0]" value="1" readonly></td>
                                                            <td><input type="number" step="0.01" class="form-control kgs" name="kgs[0]" value="0"></td>
                                                            <td><input type="number" min="0" class="form-control cones" name="cones[0]" value="0"></td>
                                                            <td style="width:180px"><select class="form-control cluster" name="cluster[0]" required></select></td>
                                                            <td><input type="text" class="form-control kapasitas" name="kapasitas[0]" readonly></td>
                                                            <td class="text-center"></td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th class="text-center">Total Karung</th>
                                                            <th class="text-center">Total Kg</th>
                                                            <th class="text-center">Total Cones</th>
                                                            <th colspan="3"></th>
                                                        </tr>
                                                        <tr>
                                                            <td><input type="number" class="form-control" id="total_karung" name="total_karung" readonly></td>
                                                            <!-- <td><input type="text" class="form-control" id="total_gw" name="total_gw" readonly></td> -->
                                                            <td><input type="text" class="form-control" id="total_kgs" name="total_kgs" readonly></td>
                                                            <td><input type="text" class="form-control" id="total_cones" name="total_cones" readonly></td>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" id="saveBtn" class="btn btn-info w-100">Save</button>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>
<!-- SCRIPTS -->
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (async function() {
        // =============== Utils ===============
        function debounce(fn, delay = 180) {
            let t;
            return function(...a) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, a), delay);
            };
        }
        const toNum = (v) => {
            const n = parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        };

        // ========== Cluster Cache ==========
        let CLUSTERS = null; // mapping name -> base kapasitas
        let CLUSTER_LIST = [];
        const CLUSTER_CACHE_KEY = 'cluster-cache-v1';
        const CLUSTER_TTL_MS = 5 * 60 * 1000; // optional

        function arrToObj(arr) {
            const o = {};
            (arr || []).forEach((c) => {
                o[c.nama_cluster] = parseFloat(c.sisa_kapasitas) || 0;
            });
            return o;
        }

        async function fetchClustersOnce(force = false) {
            if (!force) {
                try {
                    const cached = sessionStorage.getItem(CLUSTER_CACHE_KEY);
                    if (cached) {
                        const obj = JSON.parse(cached);
                        if (obj && obj.data && Array.isArray(obj.data) && obj.ts && Date.now() - obj.ts < CLUSTER_TTL_MS) {
                            CLUSTER_LIST = obj.data;
                            CLUSTERS = arrToObj(CLUSTER_LIST);
                            return CLUSTERS;
                        }
                    }
                } catch (e) {
                    // ignore cache parse error
                }
            }

            // ambil fresh dari server (endpoint sesuai aplikasi)
            const resp = await $.post('<?= base_url($role . "/getcluster") ?>', {
                kgs: 0
            }).then((r) => r);
            CLUSTER_LIST = resp || [];
            try {
                sessionStorage.setItem(CLUSTER_CACHE_KEY, JSON.stringify({
                    ts: Date.now(),
                    data: CLUSTER_LIST
                }));
            } catch (e) {}
            CLUSTERS = arrToObj(CLUSTER_LIST);
            return CLUSTERS;
        }

        // ========== Usage & Capacity ==========
        function computeUsage() {
            const usage = {};
            $('#poTable tbody tr').each(function() {
                const c = $(this).find('.cluster').val();
                const k = toNum($(this).find('.kgs').val());
                if (c) {
                    usage[c] = (usage[c] || 0) + k;
                }
            });
            return usage;
        }

        // sisa untuk tampilan global (base - used)
        function displayAvail(name, usage) {
            const base = CLUSTERS[name] || 0;
            const used = usage[name] || 0;
            return base - used;
        }

        function filterAvail(name, usage) {
            return displayAvail(name, usage);
        }

        // ========== Recalc & Render ==========
        function recalcAllFromLatest() {
            if (!CLUSTERS) return;
            const usage = computeUsage(); // total KGS per cluster

            $('#poTable tbody tr').each(function() {
                const $tr = $(this);
                const $sel = $tr.find('.cluster');
                const current = $sel.val() || '';
                const needed = toNum($tr.find('.kgs').val());

                // rebuild opsi — label menyertakan sisa
                let html = '<option value="">Pilih Cluster</option>';
                for (const name in CLUSTERS) {
                    const avail = filterAvail(name, usage);
                    if (name === current || avail >= needed) {
                        const label = `${name}`;
                        html += `<option value="${name}" data-sisa_kapasitas="${avail.toFixed(2)}">${label}</option>`;
                    }
                }

                const old = $sel.data('html-cache');
                if (old !== html) {
                    $sel.html(html);
                    $sel.data('html-cache', html);
                    $sel.val(current);
                    // jika select2 sudah aktif, refresh teksnya
                    if ($sel.hasClass('select2-hidden-accessible')) {
                        // trigger change agar select2 mengupdate label yang ditampilkan
                        $sel.trigger('change.select2');
                    }
                }

                // tampilkan sisa global di kolom kapasitas
                if (current) {
                    const eff = displayAvail(current, usage);
                    $tr.find('.kapasitas').val(eff.toFixed(2));
                    if (eff < 0) {
                        $tr.addClass('neg-cap').attr('data-neg', '1');
                    } else {
                        $tr.removeClass('neg-cap').removeAttr('data-neg');
                    }
                } else {
                    $tr.find('.kapasitas').val('');
                    $tr.removeClass('neg-cap').removeAttr('data-neg');
                }
            });

            // alert jika ada minus
            showNegativeAlertIfAny();
        }

        // ========== Totals Delta & Row State ==========
        function initRowState($tr) {
            $tr.data('prev-gw', toNum($tr.find('.gw').val()));
            $tr.data('prev-kgs', toNum($tr.find('.kgs').val()));
            $tr.data('prev-cones', toNum($tr.find('.cones').val()));
        }

        function updateTotalsDelta($tr) {
            const prevGw = $tr.data('prev-gw') || 0,
                prevK = $tr.data('prev-kgs') || 0,
                prevC = $tr.data('prev-cones') || 0;
            const nowGw = toNum($tr.find('.gw').val());
            const nowK = toNum($tr.find('.kgs').val());
            const nowC = toNum($tr.find('.cones').val());

            $('#total_gw').val((toNum($('#total_gw').val()) + nowGw - prevGw).toFixed(2));
            $('#total_kgs').val((toNum($('#total_kgs').val()) + nowK - prevK).toFixed(2));
            $('#total_cones').val((toNum($('#total_cones').val()) + nowC - prevC));

            $tr.data('prev-gw', nowGw);
            $tr.data('prev-kgs', nowK);
            $tr.data('prev-cones', nowC);
        }

        function renumberRows() {
            const $tbody = $('#poTable tbody');
            $tbody.find('tr').each(function(i) {
                const $tr = $(this);
                $tr.find("input[name^='no_karung']").val(i + 1);
                $tr.find("input[name^='no_karung']").attr('name', `no_karung[${i}]`);
                $tr.find('input.gw').attr('name', `gw[${i}]`);
                $tr.find('input.kgs').attr('name', `kgs[${i}]`);
                $tr.find('input.cones').attr('name', `cones[${i}]`);
                $tr.find('select.cluster').attr('name', `cluster[${i}]`);
                $tr.find('input.kapasitas').attr('name', `kapasitas[${i}]`);
            });
            $('#total_karung').val($tbody.find('tr').length);
        }

        const onRowInput = debounce(async function(e) {
            const $tr = $(e.target).closest('tr');
            updateTotalsDelta($tr);

            if (!CLUSTERS) await fetchClustersOnce();
            recalcAllFromLatest();
        }, 180);

        // ========== Alert Negative & Form Submit blocking ==========
        function showNegativeAlertIfAny() {
            const $negs = $('#poTable tbody tr[data-neg="1"]');
            const clusters = {};
            $negs.each(function() {
                const c = $(this).find('.cluster').val();
                if (c) clusters[c] = true;
            });
            if ($negs.length > 0) {
                const list = Object.keys(clusters).join(', ');
                Swal.fire({
                    icon: 'error',
                    title: 'Kapasitas Minus',
                    html: `Cluster berikut kapasitasnya <b>minus</b>: <b>${list}</b>.<br>Kurangi alokasi KGS atau pindahkan ke cluster lain.`,
                    confirmButtonText: 'OK'
                });
                $('#saveBtn').prop('disabled', true);
                return true;
            } else {
                $('#saveBtn').prop('disabled', false);
                return false;
            }
        }

        // ========== Chain picker helper (sinkron input -> hidden) ==========
        function syncInputToHidden(inputId, hiddenId, optionsId) {
            $(inputId).on('input', function() {
                const val = $(this).val();
                $(hiddenId).val(val);
                const exists = $(optionsId + ' option').filter(function() {
                    return $(this).val() === val;
                }).length > 0;
                if (!exists) {
                    $(hiddenId).val(val);
                }
            });
        }

        // ========== Init DOM & Events ==========
        $(document).ready(async function() {
            const $table = $('#poTable');
            const $tbody = $table.find('tbody');

            // init select2 row awal
            $tbody.find('.cluster').each(function() {
                $(this).select2({
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    width: '100%'
                });
            });

            // init totals & state awal
            $tbody.find('tr').each(function() {
                initRowState($(this));
            });
            $('#total_karung').val($tbody.find('tr').length);
            $('#total_gw').val('0.00');
            $('#total_kgs').val('0.00');
            $('#total_cones').val('0');

            // load clusters sekali & seed tampilan global (state terakhir)
            await fetchClustersOnce(true);
            recalcAllFromLatest();

            // input changes -> totals & recalc
            $table.on('input', '.gw, .kgs, .cones', onRowInput);

            // when cluster changes, update kapasitas cell & recalc
            $table.on('change', '.cluster', async function() {
                const $sel = $(this);
                const sisa = parseFloat($sel.find('option:selected').data('sisa_kapasitas'));
                if (!isNaN(sisa)) {
                    $sel.closest('tr').find('.kapasitas').val(sisa.toFixed(2));
                } else {
                    $sel.closest('tr').find('.kapasitas').val('');
                }

                if (!CLUSTERS) await fetchClustersOnce();
                recalcAllFromLatest();
            });

            // add row
            $('#addRow').on('click', async function() {
                const idx = $tbody.children().length;
                const $row = $(`
                    <tr>
                        <td><input type="text" class="form-control text-center" name="no_karung[${idx}]" value="${idx + 1}" readonly></td>
                        <td><input type="number" step="0.01" class="form-control kgs" name="kgs[${idx}]" value="0"></td>
                        <td><input type="number" min="0" class="form-control cones" name="cones[${idx}]" value="0"></td>
                        <td style="width:180px"><select class="form-control cluster" name="cluster[${idx}]" required></select></td>
                        <td><input type="text" class="form-control kapasitas" name="kapasitas[${idx}]" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                    </tr>
                `);

                $tbody.append($row);

                // init select2 for new row
                $row.find('.cluster').select2({
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    width: '100%',
                    placeholder: 'Pilih Cluster'
                });

                initRowState($row);
                renumberRows();

                if (!CLUSTERS) await fetchClustersOnce();
                recalcAllFromLatest();
            });

            // remove row
            $table.on('click', '.removeRow', function() {
                const $tr = $(this).closest('tr');
                const prevGw = $tr.data('prev-gw') || 0,
                    prevK = $tr.data('prev-kgs') || 0,
                    prevC = $tr.data('prev-cones') || 0;
                $('#total_gw').val((toNum($('#total_gw').val()) - prevGw).toFixed(2));
                $('#total_kgs').val((toNum($('#total_kgs').val()) - prevK).toFixed(2));
                $('#total_cones').val((toNum($('#total_cones').val()) - prevC).toFixed(2));

                // destroy select2 safely if active
                try {
                    $tr.find('.cluster').select2('destroy');
                } catch (e) {}

                $tr.remove();
                renumberRows();
                recalcAllFromLatest();
            });

            // chain picker (no_model -> item_type -> kode_warna -> warna)
            syncInputToHidden('#no_model_input', '#no_model', '#noModelOptions');
            syncInputToHidden('#item_type_input', '#item_type', '#itemTypeOptions');
            syncInputToHidden('#kode_warna_input', '#kode_warna', '#kodeWarnaOptions');
            syncInputToHidden('#warna_input', '#warna', '#warnaOptions');

            $('#no_model_input').on('input', function() {
                const val = $(this).val();
                $('#no_model').val(val);

                let id_order = null;
                $('#noModelOptions option').each(function() {
                    if ($(this).val() === val) id_order = $(this).data('id');
                });
                $('#id_order').val(id_order || '');

                if (!id_order) {
                    $('#itemTypeOptions, #kodeWarnaOptions, #warnaOptions').empty();
                    $('#item_type_input, #kode_warna_input, #warna_input').val('');
                    $('#item_type, #kode_warna, #warna').val('');
                    return;
                }

                $.ajax({
                    url: "<?= base_url($role . '/retur/getItemTypeForReturSample/') ?>" + id_order,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        const $dl = $('#itemTypeOptions').empty();

                        if (data.orderItemTypes?.length) {
                            data.orderItemTypes.forEach(item => {
                                $dl.append('<option value="' + item.item_type + '">');
                            });
                        }

                        if (data.masterItemTypes?.length) {
                            data.masterItemTypes.forEach(item => {
                                $dl.append('<option value="' + item.item_type + '">');
                            });
                        }

                        // reset input
                        $('#item_type_input, #kode_warna_input, #warna_input').val('');
                        $('#item_type, #kode_warna, #warna').val('');
                        $('#kodeWarnaOptions, #warnaOptions').empty();
                    }
                });
            });

            $('#item_type_input').on('input', function() {
                const val = $(this).val();
                $('#item_type').val(val);
                $.ajax({
                    url: "<?= base_url($role . '/otherIn/getKodeWarnaForOtherIn') ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_order: $('#id_order').val(),
                        item_type: val
                    },
                    success: function(data) {
                        const $dl = $('#kodeWarnaOptions').empty();
                        (data || []).forEach(item => $dl.append('<option value="' + item.kode_warna + '"></option>'));
                        $('#kode_warna_input, #warna_input').val('');
                        $('#kode_warna, #warna').val('');
                        $('#warnaOptions').empty();
                    }
                });
            });

            $('#kode_warna_input').on('input', function() {
                const val = $(this).val();
                $('#kode_warna').val(val);
                $.ajax({
                    url: "<?= base_url($role . '/otherIn/getWarnaForOtherIn') ?>",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_order: $('#id_order').val(),
                        item_type: $('#item_type').val(),
                        kode_warna: val
                    },
                    success: function(data) {
                        const $dl = $('#warnaOptions').empty();
                        let warnaVal = '';
                        if (Array.isArray(data)) {
                            data.forEach(item => $dl.append('<option value="' + item.color + '"></option>'));
                            if (data.length === 1) warnaVal = data[0].color;
                        } else if (data && data.color) {
                            $dl.append('<option value="' + data.color + '"></option>');
                            warnaVal = data.color;
                        }
                        if (warnaVal) {
                            $('#warna_input').val(warnaVal);
                            $('#warna').val(warnaVal);
                        } else {
                            $('#warna_input').val('');
                            $('#warna').val('');
                        }
                    }
                });
            });

            // block form submit if any negative
            $(document).on('submit', 'form', function(e) {
                if (showNegativeAlertIfAny()) {
                    e.preventDefault();
                }
            });

            // always invalidate cluster cache after load (safety)
            try {
                sessionStorage.removeItem(CLUSTER_CACHE_KEY);
            } catch (e) {}
        });

    })();
</script>

<!-- notifikasi -->
<?php
$status  = session()->getFlashdata('status');
$message = session()->getFlashdata('message');

if ($status): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const status = "<?= esc($status) ?>";
            const message = "<?= esc($message) ?>";

            if (status === 'success') {
                const adaTambahan = message.includes(':') || message.split('.').length > 2;

                if (adaTambahan) {
                    let countdown = 5;
                    let timerInterval;

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        html: `${message}<br><br><b>Tombol OK aktif dalam <span id="timer">${countdown}</span> detik...</b>`,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'OK',
                        didOpen: () => {
                            const timerEl = Swal.getHtmlContainer().querySelector('#timer');
                            Swal.disableButtons();

                            timerInterval = setInterval(() => {
                                countdown--;
                                timerEl.textContent = countdown;
                                if (countdown <= 0) {
                                    clearInterval(timerInterval);
                                    Swal.enableButtons();
                                    timerEl.textContent = '0';
                                }
                            }, 1000);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: message,
                    });
                }
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                });
            }
        });
    </script>
<?php endif; ?>

<?php $this->endSection(); ?>