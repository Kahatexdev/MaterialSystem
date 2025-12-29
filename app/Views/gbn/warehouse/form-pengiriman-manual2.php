<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<style>
    /* .select2-container--default .select2-selection--single {
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
    } */

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
        width: 20px;
    }

    #manualTable th:nth-child(11),
    #manualTable td:nth-child(11) {
        width: 60px;
    }

    #manualTable th:nth-child(12),
    #manualTable td:nth-child(12) {
        width: 1500px;
    }

    #manualTable th:nth-child(13),
    #manualTable td:nth-child(13) {
        width: 70px;
    }

    #manualTable th:nth-child(14),
    #manualTable td:nth-child(14) {
        width: 50px;
    }

    /* ====== FULLSCREEN LOADING OVERLAY FIX ====== */
    #loadingOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 9999;

        /* selalu flex, disembunyiin pakai opacity */
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);

        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease-in-out;
    }

    #loadingOverlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    .loading-overlay-content {
        background: #ffffff;
        padding: 20px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        text-align: center;
        min-width: 260px;
    }

    .loader-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 4px solid #e0e0e0;
        border-top-color: #27ae60;
        border-right-color: #27ae60;
        animation: spinLoader 0.9s linear infinite;
        margin: 0 auto;
    }

    .text-loading {
        font-size: 14px;
        color: #333;
    }

    @keyframes spinLoader {
        to {
            transform: rotate(360deg);
        }
    }

    /* Lot */
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div id="loadingOverlay">
    <div class="loading-overlay-content">
        <div class="loader-circle"></div>
        <div class="mt-3 text-loading">
            Sedang memproses data...<br>
            Mohon tunggu sebentar
        </div>
    </div>
</div>

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
                    <div class="col-md-4" id="col-tgl">
                        <label for="tgl_pakai" class="form-label">Tanggal Pakai</label>
                        <input type="date" id="tgl_pakai" class="form-control" name="tgl_pakai" required>
                    </div>
                    <div class="col-md-4" id="col-jenis">
                        <label for="jenis" class="form-label">Jenis</label>
                        <select id="jenis" class="form-control select2" name="jenis" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="BENANG">BENANG</option>
                            <option value="NYLON">NYLON</option>
                            <option value="KARET">KARET</option>
                            <option value="SPANDEX">SPANDEX</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="col-area">
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
                    <!-- Checkbox Kekurangan -->
                    <div class="col-md-1" id="kekurangan-container" style="display:none;">
                        <label for="kekurangan" class="form-label">Kekurangan (Pengiriman Area)</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="kekurangan" name="kekurangan">
                        </div>
                    </div>
                    <!-- input hidden untuk kirim default -->
                    <input type="hidden" name="status" id="status" value="Pengeluaran Jalur">
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
                                <th>No Krg</th>
                                <th>Kekurangan</th>
                                <th>Lot</th>
                                <th>Kgs</th>
                                <th>Max K</th>
                                <th>Cones</th>
                                <th>Max C</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sessionData = session()->get('manual_delivery') ?? []; ?>
                            <?php if (!empty($sessionData)): ?>
                                <script>
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'PERHATIAN',
                                        html: 'CEK KEMBALI DATA PENGELUARAN SEBELUM DISIMPAN.<BR>PASTIKAN DATA SUDAH BENAR!',
                                        confirmButtonText: 'OK'
                                    });
                                </script>
                            <?php endif; ?>
                            <?php foreach ($sessionData as $i => $row): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="selected[]" value="<?= esc($row['id_pengeluaran']) ?>" class="row-check">
                                        <input type="hidden" name="statusPengeluaran[<?= $row['id_pengeluaran'] ?>]" value="<?= $row['status_pengeluaran']  ?? null ?>">
                                    </td>
                                    <td>
                                        <?= esc(isset($row['tgl_pakai']) ? $row['tgl_pakai'] : '') ?>
                                    </td>
                                    <!-- hiden kolom -->
                                    <input type="hidden" name="jenis[<?= $row['id_pengeluaran'] ?>]" value="<?= $row['jenis'] ?>">
                                    <td class="text-center"><?= esc(isset($row['area_out']) ? $row['area_out'] : '') ?></td>
                                    <td class="text-center"><?= esc(isset($row['no_model']) ? $row['no_model'] : '') ?></td>
                                    <td><?= esc(isset($row['item_type']) ? $row['item_type'] : '') ?></td>
                                    <td><?= esc(isset($row['kode_warna']) ? $row['kode_warna'] : '') ?></td>
                                    <td><?= esc(isset($row['warna']) ? $row['warna'] : '') ?></td>
                                    <td><?= esc(isset($row['nama_cluster']) ? $row['nama_cluster'] : '') ?></td>
                                    <td><?= esc(isset($row['no_karung']) ? $row['no_karung'] : '') ?></td>
                                    <td class="text-center">
                                        <?= ($row['status_pengeluaran'] === 'Pengiriman Area')
                                            ? '<i class="fas fa-check-square fa-2x" style="color: #6fbf73;"></i>'
                                            : '' ?>
                                    </td>
                                    <td>
                                        <textarea name="lot_out[<?= $row['id_pengeluaran'] ?>]" class="form-control"><?= esc(isset($row['lot_out']) ? $row['lot_out'] : '') ?></textarea>
                                    </td>
                                    <td>
                                        <input type="number" name="kgs_out[<?= $row['id_pengeluaran'] ?>]" class="form-control kgs-val" value="<?= esc(isset($row['kgs_out']) ? $row['kgs_out'] : '') ?>" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="text" name="max_kgs[<?= $row['id_pengeluaran'] ?>]" class="form-control kgs-val" value="<?= esc(isset($row['max_kgs']) ? $row['max_kgs'] : '') ?>" step="0.01" min="0">
                                        <input type="text" name="kgs_kirim[<?= $row['id_pengeluaran'] ?>]" class="form-control kgs-val" value="<?= esc(isset($row['kgs_kirim']) ? $row['kgs_kirim'] : '') ?>" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="cns_out[<?= $row['id_pengeluaran'] ?>]" class="form-control cns-val" value="<?= esc(isset($row['cns_out']) ? $row['cns_out'] : '') ?>" step="1" min="0">
                                    </td>
                                    <td>
                                        <input type="text" name="max_cns[<?= $row['id_pengeluaran'] ?>]" class="form-control cns-val" value="<?= esc(isset($row['max_cns']) ? $row['max_cns'] : '') ?>" step="1" min="0">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-remove" data-index="<?= $i ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                    <?php if (!empty($sessionData)): ?>
                        <div class="w-100 text-center my-4 p-3 rounded-4 shadow-sm border"
                            style="background: linear-gradient(135deg, #fffef5, #fffdf0); border-color: #f1c40f; color: #3c3c3c;">
                            <i class="fas fa-triangle-exclamation me-2" style="color: #d4a017; font-size: 1.2rem;"></i>
                            <span style="font-weight: 600; font-size: 30px; color: #d4a017;">
                                PERIKSA KEMBALI SELURUH DATA SEBELUM MENYIMPAN!
                            </span>
                            <p class="mt-1 mb-0" style="font-size: 20px; ">
                                PASTIKAN NILAI <strong>KGS</strong>, <strong>CONES</strong>, dan <strong>LOT</strong> SUDAH BENAR.
                            </p>
                        </div>
                    <?php endif; ?>
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
    $('#jenis').on('change', function() {
        let val = $(this).val()?.toUpperCase(); // ambil value
        // console.log("Value sekarang:", val);

        if (val === 'SPANDEX' || val === 'KARET') {
            // tampilkan kolom kekurangan
            $('#kekurangan-container').show();

            // atur semua jadi col-md-3
            $('#col-tgl').attr('class', 'col-md-4');
            $('#col-jenis').attr('class', 'col-md-4');
            $('#col-area').attr('class', 'col-md-3');
        } else {
            // sembunyikan kolom kekurangan
            $('#kekurangan-container').hide();

            // balikin jadi 3 kolom (4-4-4)
            // atur semua jadi col-md-3
            $('#col-tgl').attr('class', 'col-md-4');
            $('#col-jenis').attr('class', 'col-md-4');
            $('#col-area').attr('class', 'col-md-4');
        }
    });

    const statusPengeluaran = document.getElementById('status');
    $('#kekurangan').on('change', function() {
        // ubah value hidden input sesuai checkbox
        if (this.checked) {
            statusPengeluaran.value = "Pengiriman Area";
        } else {
            statusPengeluaran.value = "Pengeluaran Jalur";
        }
    });
</script>
<script>
    $(function() {
        $('.select2').select2({
            width: '100%'
        });
        updateTotals();

        // === GLOBAL AJAX LOADING OVERLAY (FIX) ===
        let overlayTimer;

        $(document).ajaxStart(function() {
            clearTimeout(overlayTimer);
            $('#loadingOverlay').addClass('show');
        });

        $(document).ajaxStop(function() {
            overlayTimer = setTimeout(function() {
                $('#loadingOverlay').removeClass('show');
            }, 200);
        })

        // Select/Deselect semua
        $('#checkAll').on('change', function() {
            $('.row-check').prop('checked', this.checked).trigger('change');
        });

        // Checkbox per-baris
        $(document).on('change', '.row-check', function() {
            $('#checkAll').prop('checked', $('.row-check:checked').length === $('.row-check').length);
            updateTotals();
        });

        // Perubahan angka Kgs/Cones
        $('#manualTable').on('input change', '.kgs-val, .cns-val', function() {
            updateTotals();
        });

        // Hapus baris -> hitung ulang setelah request (untuk UX langsung juga hitung dulu)
        $(document).on('click', '.btn-remove', function() {
            updateTotals();
        });

        // Tombol hapus terpilih -> setelah sukses akan reload; sebelum itu hitung ulang juga oke
        $('#btnDeleteSelected').on('click', function() {
            updateTotals();
        });


        // VALIDASI submit: wajib ada yang dipilih
        $('#statusKirim').on('submit', function(e) {
            if ($('.row-check:checked').length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih minimal 1 data untuk dikirim!'
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
                        icon: 'warning',
                        title: 'Warning',
                        text: message = jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : 'Terjadi kesalahan saat menghubungi server: ' + textStatus
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

        // === HANYA HITUNG YANG DICHECK ===
        function updateTotals() {
            let totalKgs = 0;
            let totalCns = 0;

            $('#manualTable tbody tr').each(function() {
                const $tr = $(this);
                const checked = $tr.find('.row-check').is(':checked');
                if (!checked) return; // skip baris yang tidak dipilih

                // Ambil nilai dari input (fallback ke text kalau bukan input)
                const kgs = parseFloat($tr.find('.kgs-val').val() ?? $tr.find('.kgs-val').text()) || 0;
                const cns = parseInt($tr.find('.cns-val').val() ?? $tr.find('.cns-val').text()) || 0;

                totalKgs += kgs;
                totalCns += cns;
            });

            $('#ttl_kgs').val(totalKgs.toFixed(2));
            $('#ttl_cns').val(totalCns);
        }
    });

    let refreshTimer = null;
    document.addEventListener('input', function(e) {

        if (!e.target.closest('tr')) return;

        const row = e.target.closest('tr');

        // ambil jenis dari hidden input di row ini
        const jenisInput = row.querySelector('input[name^="jenis"]');
        if (!jenisInput) return;

        const jenis = jenisInput.value.toUpperCase();

        const idPeng = e.target.name.match(/\[(\d+)\]/)?.[1];
        const kgsOut = row.querySelector('input[name^="kgs_out"]')?.value || 0;
        const cnsOut = row.querySelector('input[name^="cns_out"]')?.value || 0;

        // JALANKAN VALIDASI HANYA UNTUK BENANG & NYLON
        if (jenis !== 'BENANG' && jenis !== 'NYLON') {
            return; // SPANDEX / KARET → lewati validasi
        }

        // VALIDASI KGS
        if (e.target.name && e.target.name.startsWith('kgs_out')) {
            const row = e.target.closest('tr');
            const maxInput = row.querySelector('input[name^="max_kgs"]');

            let kgs = parseFloat(e.target.value) || 0;
            let max = parseFloat(maxInput.value) || 0;

            if (kgs > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Valid',
                    text: 'Kgs melebihi stock per karung, sisa stock ' + max + 'kg',
                    confirmButtonText: 'OK'
                });
                e.target.value = max;
            }
        }

        // VALIDASI CNS
        if (e.target.name && e.target.name.startsWith('cns_out')) {
            const row = e.target.closest('tr');
            const maxInput = row.querySelector('input[name^="max_cns"]');

            let cns = parseInt(e.target.value) || 0;
            let max = parseInt(maxInput.value) || 0;

            if (cns > max) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Tidak Valid',
                    text: 'Cns melebihi stock per karung, sisa stock ' + max + 'cns',
                    confirmButtonText: 'OK'
                });
                e.target.value = max;
            }
        }

        // === DEBOUNCE AJAX (tunggu user berhenti ngetik) ===
        clearTimeout(refreshTimer);

        refreshTimer = setTimeout(function() {
            $.ajax({
                    url: '<?= base_url($role . '/pengiriman/refreshSessionDeliveryArea') ?>',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        id_pengeluaran: idPeng,
                        kgs_out: kgsOut,
                        cns_out: cnsOut
                    }
                })
                .done(function(res) {
                    if (!res.data) return;

                    res.data.forEach(function(row) {
                        const id = row.id_pengeluaran;

                        // update max kgs
                        $(`input[name="max_kgs[${id}]"]`).val(row.max_kgs).trigger('change');
                        // update max kgs
                        $(`input[name="max_cns[${id}]"]`).val(row.max_cns).trigger('change');
                    });

                    console.log('Session & input updated');
                    console.table(res.data);
                });
        }, 600); // 600ms setelah berhenti ngetik
    });
</script>

<?php $this->endSection(); ?>