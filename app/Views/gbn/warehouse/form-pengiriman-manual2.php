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

    #manualTable th,
    #manualTable td {
        white-space: normal;
        /* biar teks panjang bisa turun */
        word-wrap: break-word;
        /* pecah kata panjang */
        vertical-align: middle;
    }

    #manualTable th:nth-child(1),
    #manualTable td:nth-child(1) {
        width: 20px;
    }

    #manualTable th:nth-child(2),
    #manualTable td:nth-child(2) {
        width: 0px;
    }

    #manualTable th:nth-child(3),
    #manualTable td:nth-child(3) {
        width: 30px;
    }

    #manualTable th:nth-child(4),
    #manualTable td:nth-child(4) {
        width: 50px;
    }

    #manualTable th:nth-child(5),
    #manualTable td:nth-child(5) {
        width: 60px;
    }

    #manualTable th:nth-child(6),
    #manualTable td:nth-child(6) {
        width: 60px;
    }

    #manualTable th:nth-child(7),
    #manualTable td:nth-child(7) {
        width: 50px;
    }

    #manualTable th:nth-child(8),
    #manualTable td:nth-child(8) {
        width: 30px;
    }

    #manualTable th:nth-child(9),
    #manualTable td:nth-child(9) {
        width: 9px;
    }

    #manualTable th:nth-child(10),
    #manualTable td:nth-child(10) {
        width: 60px;
    }

    #manualTable th:nth-child(11),
    #manualTable td:nth-child(11) {
        width: 150px;
    }

    #manualTable th:nth-child(12),
    #manualTable td:nth-child(12) {
        width: 70px;
    }

    #manualTable th:nth-child(13),
    #manualTable td:nth-child(13) {
        width: 50px;
    }

    /* Lot */
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
                    <div class="col-md-4">
                        <label for="tgl_pakai" class="form-label">Tanggal Pakai</label>
                        <input type="date" id="tgl_pakai" class="form-control" name="tgl_pakai" required>
                    </div>
                    <div class="col-md-4">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select id="jenis" class="form-control select2" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="BENANG">BENANG</option>
                            <option value="NLON">NLON</option>
                            <option value="KARET">KARET</option>
                            <option value="SPANDEX">SPANDEX</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="area" class="form-label">Area</label>
                        <select id="area" class="form-control select2" name="area" required>
                            <option value="">-- Pilih Area --</option>
                            <?php foreach ($area as $ar) {
                            ?>
                                <option value="<?= $ar; ?>"><?= $ar; ?></option>
                            <?php
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" id="btn-saveSession" class="btn btn-md bg-gradient-info w-100">
                            <h5 class="text-white"><i class="fas fa-save"></i>
                                Cek Detail Pengiriman</h5>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="<?= base_url($role . '/updateStatusKirim') ?>" method="post" id="statusKirim">
                <div class="table-responsive">
                    <table id="manualTable" class="table table-bordered table-striped table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>Tgl Pakai</th>
                                <th>Area</th>
                                <th>Model</th>
                                <th>Item Type</th>
                                <th>Kode Warna</th>
                                <th>Warna</th>
                                <th>Cluster</th>
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
                                    <td>
                                        <input type="checkbox" name="selected[]" value="<?= esc($row['id_pengeluaran']) ?>" class="row-check">
                                    </td>
                                    <td><?= esc(isset($row['tgl_pakai']) ? $row['tgl_pakai'] : '') ?></td>
                                    <!-- hiden kolom -->
                                    <input type="hidden" name="jenis[]" value="<?= $row['jenis'] ?>">
                                    <td><?= esc(isset($row['area_out']) ? $row['area_out'] : '') ?></td>
                                    <td><?= esc(isset($row['no_model']) ? $row['no_model'] : '') ?></td>
                                    <td><?= esc(isset($row['item_type']) ? $row['item_type'] : '') ?></td>
                                    <td><?= esc(isset($row['kode_warna']) ? $row['kode_warna'] : '') ?></td>
                                    <td><?= esc(isset($row['warna']) ? $row['warna'] : '') ?></td>
                                    <td><?= esc(isset($row['nama_cluster']) ? $row['nama_cluster'] : '') ?></td>
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
                    <div class="col-md-3">
                        <label for="ttl_kgs" class="form-label">Total Kgs:</label>
                        <input type="text" id="ttl_kgs" name="ttl_kgs" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="ttl_cns" class="form-label">Total Cones:</label>
                        <input type="text" id="ttl_cns" name="ttl_cns" class="form-control" readonly>
                    </div>
                    <div class="col-md-3 d-flex align-items-end mt-3">
                        <button type="submit" class="btn bg-gradient-success w-100">
                            <i class="fas fa-save"></i> Simpan Pengiriman Terpilih
                        </button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end mt-3">
                        <button type="button" id="btnDeleteSelected" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Hapus Terpilih
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

        // Select/Deselect semua
        $('#checkAll').on('click', function() {
            $('.row-check').prop('checked', this.checked);
        });

        // Kalau salah satu uncheck -> header ikut update
        $('.row-check').on('change', function() {
            $('#checkAll').prop('checked', $('.row-check:checked').length === $('.row-check').length);
        });

        // Validasi: harus ada yg dipilih sebelum submit
        $('#statusKirim').on('submit', function(e) {
            if ($('.row-check:checked').length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih minimal 1 data untuk dikirim!',
                    confirmButtonText: 'OK'
                });
            }
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

        $('#btnDeleteSelected').on('click', function() {
            let selected = [];
            $('.row-check:checked').each(function() {
                selected.push($(this).closest('tr').find('.btn-remove').data('index'));
            });

            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak ada data!',
                    text: 'Pilih minimal 1 data untuk dihapus.'
                });
                return;
            }

            Swal.fire({
                title: 'Yakin?',
                text: "Data terpilih akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('<?= base_url($role . "/pengiriman/removeSessionDelivery") ?>', {
                        indexes: selected
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
                }
            });
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