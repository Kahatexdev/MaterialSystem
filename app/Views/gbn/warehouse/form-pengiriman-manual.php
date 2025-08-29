<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<style>
    .select2-container--default .select2-selection--single {
        border: none;
        border-bottom: 2px solid rgb(34, 121, 37);
        border-radius: 0 0 10px 10px;
        height: 38px;
        padding-left: 8px;
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:active {
        border-bottom: 2px solid rgb(34, 121, 37);
        outline: none;
        box-shadow: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        font-size: 16px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="container-fluid py-4">
    <!-- alert swal -->
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                html: '<?= session()->getFlashdata('success') ?>'
            });
        </script>
    <?php elseif (session()->getFlashdata('error')): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: '<?= session()->getFlashdata('error') ?>'
            });
        </script>
    <?php endif; ?>
    <div class="row mb-3">
        <div class="col-6">
            <h5>Pengiriman Manual</h5>
        </div>
        <div class="col-6 text-end">
            <a href="<?= base_url($role . '/pengiriman_area') ?>" class="btn bg-gradient-dark">
                <i class="fas fa-qrcode"></i> Scan Barcode
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Form Pengeluaran Manual</h5>
            <p class="text-sm mb-0">
                Silakan isi form di bawah ini untuk melakukan pengiriman manual. Pastikan semua data yang dimasukkan sudah benar.
            </p>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="no_model" class="form-label">No. Model</label>
                        <input type="text" id="no_model" class="form-control" name="no_model" required>
                    </div>
                    <div class="col-md-3">
                        <label for="item_type" class="form-label">Item Type</label>
                        <select id="item_type" class="form-control select2" name="item_type" disabled>
                            <option value="">-- Pilih Item Type --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="kode_warna" class="form-label">Kode Warna</label>
                        <select id="kode_warna" class="form-control select2" name="kode_warna" disabled>
                            <option value="">-- Pilih Kode Warna --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="warna" class="form-label">Warna</label>
                        <select id="warna" class="form-control select2" name="warna" disabled>
                            <option value="">-- Pilih Warna --</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" id="btn-saveSession" class="btn bg-gradient-info w-100" disabled>
                            <i class="fas fa-save"></i> Simpan ke tabel Session
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url($role . '/updateStatusKirim') ?>" method="post">
                <div class="table-responsive">
                    <table id="manualTable" class="table table-bordered table-striped table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>Cluster</th>
                                <th>Model</th>
                                <th>Item Type</th>
                                <th>Kode Warna</th>
                                <th>Warna</th>
                                <th>Area</th>
                                <th>No Karung</th>
                                <th>Lot</th>
                                <th>Kgs</th>
                                <th>Cones</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sessionData = session()->get('manual_delivery') ?? []; ?>
                            <?php foreach ($sessionData as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <!-- hiden id_pengeluaran -->
                                    <input type="hidden" name="id_pengeluaran[]" value="<?= esc($row['id_pengeluaran']) ?>">
                                    <td><?= esc(isset($row['nama_cluster']) ? $row['nama_cluster'] : '') ?></td>
                                    <td><?= esc(isset($row['no_model']) ? $row['no_model'] : '') ?></td>
                                    <td><?= esc(isset($row['item_type']) ? $row['item_type'] : '') ?></td>
                                    <td><?= esc(isset($row['kode_warna']) ? $row['kode_warna'] : '') ?></td>
                                    <td><?= esc(isset($row['warna']) ? $row['warna'] : '') ?></td>
                                    <td><?= esc(isset($row['area_out']) ? $row['area_out'] : '') ?></td>
                                    <td><?= esc(isset($row['no_karung']) ? $row['no_karung'] : '') ?></td>

                                    <td>
                                        <textarea name="lot_out[<?= $i ?>]" class="form-control"><?= esc(isset($row['lot_out']) ? $row['lot_out'] : '') ?></textarea>
                                    </td>
                                    <td>
                                        <input type="number" name="kgs_out[<?= $i ?>]" class="form-control kgs-val" value="<?= esc(isset($row['kgs_out']) ? $row['kgs_out'] : '') ?>" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="cns_out[<?= $i ?>]" class="form-control cns-val" value="<?= esc(isset($row['cns_out']) ? $row['cns_out'] : '') ?>" step="1" min="0">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-remove" data-index="<?= $i ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="ttl_kgs" class="form-label">Total Kgs:</label>
                        <input type="text" id="ttl_kgs" name="ttl_kgs" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="ttl_cns" class="form-label">Total Cones:</label>
                        <input type="text" id="ttl_cns" name="ttl_cns" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 d-flex align-items-end mt-3">
                        <button type="submit" class="btn bg-gradient-success w-100">
                            <i class="fas fa-save"></i> Simpan Pengiriman
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(function() {
        updateTotals();
        $('.select2').select2();

        // jika no_model diisi
        $('#no_model').on('change', function() {
            let noModel = $(this).val();
            $('#item_type').prop('disabled', !noModel).val(null).trigger('change');
            if (noModel) {
                // AJAX load item types
                $.getJSON('<?= base_url($role . "/pengiriman/getItemTypes") ?>', {
                    no_model: noModel
                }, function(data) {
                    let $it = $('#item_type').empty().append('<option value="">-- Pilih Item Type --</option>');
                    $.each(data, function(i, v) {
                        $it.append(`<option value="${v.item_type}">${v.item_type}</option>`);
                    });
                });
            }
        });

        // Saat item_type dipilih
        $('#item_type').on('change', function() {
            let model = $('#no_model').val();
            let type = $(this).val();
            $('#kode_warna').prop('disabled', !type).val(null).trigger('change');
            $('#warna').prop('disabled', true).val(null).trigger('change');
            $('#btn-saveSession').prop('disabled', true);
            if (type) {
                // AJAX load kode warna
                $.getJSON('<?= base_url($role . "/pengiriman/getKodeWarna") ?>', {
                    no_model: model,
                    item_type: type
                }, function(data) {
                    let $kw = $('#kode_warna').empty().append('<option value="">-- Pilih Kode Warna --</option>');
                    $.each(data, function(i, v) {
                        $kw.append(`<option value="${v.kode_warna}">${v.kode_warna}</option>`);
                    });
                });
            }
        });

        // Saat kode_warna dipilih
        $('#kode_warna').on('change', function() {
            let model = $('#no_model').val();
            let type = $('#item_type').val();
            let kode = $(this).val();
            $('#warna').prop('disabled', !kode).val(null).trigger('change');
            $('#btn-saveSession').prop('disabled', true);
            if (kode) {
                $.getJSON('<?= base_url($role . "/pengiriman/getWarna") ?>', {
                    no_model: model,
                    item_type: type,
                    kode_warna: kode
                }, function(data) {
                    let $w = $('#warna').empty().append('<option value="">-- Pilih Warna --</option>');
                    $.each(data, function(i, v) {
                        // Support both array of string or array of object with 'warna' key
                        let warna = typeof v === 'object' && v.warna ? v.warna : v;
                        $w.append(`<option value="${warna}">${warna}</option>`);
                    });
                });
            }
        });

        // Saat warna dipilih
        $('#warna').on('change', function() {
            $('#btn-saveSession').prop('disabled', !$(this).val());
        });

        // Button Cari Order
        $('#btn-saveSession').on('click', function() {
            let params = $('#filter-form').serialize();
            $.post('<?= base_url($role . "/pengiriman/saveSessionDeliveryArea") ?>', params, function(response) {
                    // Update totals setelah data session berubah
                    updateTotals();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: 'Data berhasil disimpan ke session.',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                        });
                    }
                }, 'json')
                .fail(function(jqXHR, textStatus, errorThrown) {
                    // Tangani gagal request (misal server 500, network error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + textStatus
                    });
                });
        });


        // Hapus baris dan session
        $(document).on('click', '.btn-remove', function() {
            let idx = $(this).data('index');
            if (typeof idx === 'undefined') {
                idx = $(this).closest('tr').data('index');
            }
            $.post('<?= base_url($role . "/pengiriman/removeSessionDelivery") ?>', {
                index: idx
            }, function(resp) {
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data berhasil dihapus.',
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: resp.message
                    });
                }
            }, 'json');
            updateTotals();
        });

        // Update total Kgs and Cones
        $('#manualTable').on('change', '.kgs-val, .cns-val', function() {
            updateTotals();
        });

        function updateTotals() {
            let totalKgs = 0,
                totalCns = 0;
            $('#manualTable tbody tr').each(function() {
                totalKgs += parseFloat($(this).find('.kgs-val').text()) || parseFloat($(this).find('.kgs-val').val()) || 0;
                totalCns += parseInt($(this).find('.cns-val').text()) || parseInt($(this).find('.cns-val').val()) || 0;
            });
            $('#ttl_kgs').val(totalKgs.toFixed(2));
            $('#ttl_cns').val(totalCns);
        }
    });
</script>

<?php $this->endSection(); ?>