<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <style>
        /* Overlay transparan */
        #loadingOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            background: rgba(0, 0, 0, 0.75);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            width: 260px;
            /* kecilkan modal */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .loader-text {
            margin-top: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 12px;
        }


        #loadingOverlay.active {
            display: block;
            opacity: 1;
        }

        .loader {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            position: relative;
        }

        .loader:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: loader-dual-ring 1.2s linear infinite;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }

        @keyframes loader-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>

    <!-- overlay -->
    <div id="loadingOverlay">
        <div class="loader-wrap">
            <div class="loading-card">
                <div class="loader" role="status" aria-hidden="true"></div>
                <div class="loader-text">Memuat data...</div>

                <!-- Progress bar -->
                <div class="progress mt-3" style="height: 6px; border-radius: 6px;">
                    <div id="progressBar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>


    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Datang Benang</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Key</label>
                    <input type="text" class="form-control" id="key" placeholder="No Model/Item Type/Kode Warna/Warna/Lot" style="font-size: 11px;">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Awal (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Akhir (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <div class="form-check mb-0">
                        <label class="form-check-label" for="po_plus">
                            PO(+)
                        </label>
                        <input class="form-check-input" type="checkbox" id="po_plus">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                    <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                    <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display table table-bordered table-hover text-center text-uppercase" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase">No</th>
                            <th class="text-center text-uppercase">Foll Up</th>
                            <th class="text-center text-uppercase">No Model</th>
                            <th class="text-center text-uppercase">No Order</th>
                            <th class="text-center text-uppercase">Buyer</th>
                            <th class="text-center text-uppercase">Delivery Awal</th>
                            <th class="text-center text-uppercase">Delivery Akhir</th>
                            <th class="text-center text-uppercase">Order Type</th>
                            <th class="text-center text-uppercase">Item Type</th>
                            <th class="text-center text-uppercase">Kode Warna</th>
                            <th class="text-center text-uppercase">Warna</th>
                            <th class="text-center text-uppercase">KG Pesan</th>
                            <th class="text-center text-uppercase">Tanggal Datang</th>
                            <th class="text-center text-uppercase">Kgs Datang</th>
                            <th class="text-center text-uppercase">Cones Datang</th>
                            <th class="text-center text-uppercase">LOT Datang</th>
                            <th class="text-center text-uppercase">No Surat Jalan</th>
                            <th class="text-center text-uppercase">LMD</th>
                            <th class="text-center text-uppercase">GW</th>
                            <th class="text-center text-uppercase">Harga</th>
                            <th class="text-center text-uppercase">Nama Cluster</th>
                            <th class="text-center text-uppercase">Po Tambahan</th>
                            <th class="text-center text-uppercase">Keterangan</th>
                            <th class="text-center text-uppercase">Waktu Input</th>
                            <th class="text-center text-uppercase">Admin</th>
                            <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th> -->
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<!-- Modal Detail / Edit -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formDetail">
                <div class="modal-header">
                    <h5 class="modal-title">Detail / Edit Datang Benang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- area loading -->
                    <div id="detailLoading" class="text-center py-4" style="display:none;">
                        <div class="spinner-border" role="status"></div>
                        <div class="mt-2">Memuat...</div>
                    </div>

                    <!-- form content -->
                    <div id="detailFormContent" style="display:none;">
                        <input type="hidden" name="id_out_celup" id="m_id_out_celup">

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Buyer</label>
                                <input type="text" class="form-control form-control-sm" name="buyer" id="m_buyer">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Foll Up</label>
                                <input type="text" class="form-control form-control-sm" name="foll_up" id="m_foll_up">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No Order</label>
                                <input type="text" class="form-control form-control-sm" name="no_order" id="m_no_order">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No Model</label>
                                <input type="text" class="form-control form-control-sm" name="no_model" id="m_no_model">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Item Type</label>
                                <input type="text" class="form-control form-control-sm" name="item_type" id="m_item_type">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kode Warna</label>
                                <input type="text" class="form-control form-control-sm" name="kode_warna" id="m_kode_warna">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Warna</label>
                                <input type="text" class="form-control form-control-sm" name="warna" id="m_warna">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">KGs Kirim</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="kgs_kirim" id="m_kgs_kirim">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Cones Kirim</label>
                                <input type="text" class="form-control form-control-sm" name="cones_kirim" id="m_cones_kirim">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Lot Kirim</label>
                                <input type="text" class="form-control form-control-sm" name="lot_kirim" id="m_lot_kirim">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">No Surat Jalan</label>
                                <input type="text" class="form-control form-control-sm" name="no_surat_jalan" id="m_no_surat_jalan">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tgl Datang</label>
                                <input type="date" class="form-control form-control-sm" name="tgl_datang" id="m_tgl_datang">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tgl Masuk</label>
                                <input type="date" class="form-control form-control-sm" name="tgl_masuk" id="m_tgl_masuk">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">GW Kirim</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="gw_kirim" id="m_gw_kirim">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="harga" id="m_harga">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nama Cluster</label>
                                <input type="text" class="form-control form-control-sm" name="nama_cluster" id="m_nama_cluster">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control form-control-sm" name="keterangan" id="m_keterangan" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info btn-sm" id="m_save_btn">
                        <span id="m_save_text">Simpan</span>
                        <span id="m_save_loading" style="display:none;" class="spinner-border spinner-border-sm ms-2" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            "paging": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": false, // <- ubah jadi false
            "serverSide": false
        });

        function showLoading() {
            $('#loadingOverlay').addClass('active');
            $('#btnSearch').prop('disabled', true);
            // show DataTables processing indicator if available
            try {
                dataTable.processing(true);
            } catch (e) {}
        }

        function hideLoading() {
            $('#loadingOverlay').removeClass('active');
            $('#btnSearch').prop('disabled', false);
            try {
                dataTable.processing(false);
            } catch (e) {}
        }

        function updateProgress(percent) {
            $('#progressBar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent);
            $('#progressText').text(percent + '%');
        }

        function loadData() {
            let key = $('input[type="text"]').val().trim();
            let tanggal_awal = $('input[type="date"]').eq(0).val().trim();
            let tanggal_akhir = $('input[type="date"]').eq(1).val().trim();
            let po_plus = $('#po_plus').is(':checked') ? 1 : 0;
            // Log data yang akan dikirim
            let dataToSend = {
                key: key,
                tanggal_awal: tanggal_awal,
                tanggal_akhir: tanggal_akhir,
                po_plus: po_plus
            };
            console.log('Data yang dikirim ke server:', dataToSend);
            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '' && po_plus === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterDatangBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    po_plus: po_plus
                },
                dataType: "json",
                beforeSend: function() {
                    showLoading();
                    updateProgress(0);
                },
                xhr: function() {
                    let xhr = new window.XMLHttpRequest();

                    // progress download data dari server
                    xhr.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            let percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            updateProgress(percentComplete);
                        }
                    }, false);

                    return xhr;
                },
                success: function(response) {
                    console.log(response);
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            let poPlus = (item.po_plus === "1") ? "Ya" : "";
                            dataTable.row.add([
                                index + 1,
                                item.foll_up,
                                item.no_model,
                                item.no_order,
                                item.buyer,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.unit,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                parseFloat(item.kgs_material ?? 0).toFixed(2),
                                item.tgl_datang,
                                parseFloat(item.kgs_kirim ?? 0).toFixed(2),
                                item.cones_kirim,
                                item.lot_kirim,
                                item.no_surat_jalan,
                                item.l_m_d,
                                parseFloat(item.gw_kirim ?? 0).toFixed(2),
                                item.harga,
                                item.nama_cluster,
                                poPlus,
                                item.keterangan,
                                item.created_at,
                                item.admin,
                                `<button class="btn btn-warning btn-update" 
                                    data-id_bon="${item.id_bon || ''}" 
                                    data-id_other="${item.id_other_bon || ''}" 
                                    title="Update">
                                    <i class="fa fa-edit"></i>
                                </button>`
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        let colCount = $('#dataTable thead th').length;
                        $('#dataTable tbody').html(`
                            <tr>
                                <td colspan="${colCount}" class="text-center text-danger font-weight-bold">
                                    ⚠ Tidak ada data ditemukan
                                </td>
                            </tr>
                        `);

                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    updateProgress(100); // pastikan full
                    setTimeout(() => hideLoading(), 400); // kasih jeda biar animasi progress keliatan
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let key = $('input[type="text"]').val();
            let tanggal_awal = $('input[type="date"]').eq(0).val();
            let tanggal_akhir = $('input[type="date"]').eq(1).val();
            let po_plus = $('#po_plus').is(':checked') ? 1 : 0;
            window.location.href = "<?= base_url($role . '/warehouse/exportDatangBenang') ?>?key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&po_plus=" + po_plus;
        });

        // inisialisasi kosong
        dataTable.clear().draw();
    });

    // Fitur Reset (tetap di luar document.ready atau pindahkan ke dalam jika mau)
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable').DataTable().clear().draw();

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });

    // Trigger pencarian saat tombol Enter ditekan di input apa pun
    $('#key, input[type="date"]').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Hindari form submit default (jika ada form)
            $('#btnSearch').click(); // Trigger tombol Search
        }
    });
</script>

<script>
    $(function() {

        // fungsi untuk mem-format angka; null-safe
        function maybeNum(v) {
            if (v === null || v === undefined || v === '') return '';
            let n = parseFloat(v);
            return isNaN(n) ? '' : n;
        }

        // buka modal saat tombol diklik
        $(document).on('click', '.btn-open-detail', function(e) {
            e.preventDefault();
            const id = $(this).data('id'); // id_out_celup

            // reset modal state
            $('#detailFormContent').hide();
            $('#detailLoading').show();
            $('#m_save_loading').hide();
            $('#m_save_text').show();
            $('#m_save_btn').prop('disabled', false);
            $('#detailModal').modal('show');

            // ambil detail via AJAX (endpoint return JSON)
            $.get("<?= base_url($role . '/warehouse/detailDatangBenang/') ?>" + id)
                .done(function(json) {
                    // contoh JSON structure sesuai data yang kamu tunjukkan
                    // map ke input modal
                    $('#m_id_out_celup').val(json.id_out_celup || '');
                    $('#m_buyer').val(json.buyer || '');
                    $('#m_no_model').val(json.no_model || '');
                    $('#m_no_order').val(json.no_order || '');
                    $('#m_item_type').val(json.item_type || '');
                    $('#m_kode_warna').val(json.kode_warna || '');
                    $('#m_warna').val(json.warna || json.color);
                    $('#m_kgs_kirim').val(maybeNum(json.kgs_kirim));
                    $('#m_cones_kirim').val(json.cones_kirim || '');
                    $('#m_lot_kirim').val(json.lot_kirim || '');
                    $('#m_no_surat_jalan').val(json.no_surat_jalan || '');
                    $('#m_tgl_datang').val(json.tgl_datang || '');
                    $('#m_tgl_masuk').val(json.tgl_masuk || '');
                    $('#m_foll_up').val(json.foll_up || '');
                    $('#m_gw_kirim').val(maybeNum(json.gw_kirim));
                    $('#m_harga').val(maybeNum(json.harga));
                    $('#m_nama_cluster').val(json.nama_cluster || '');
                    $('#m_keterangan').val(json.keterangan || '');

                    // tampilkan form, sembunyikan loading
                    $('#detailLoading').hide();
                    $('#detailFormContent').show();
                })
                .fail(function() {
                    $('#detailLoading').hide();
                    $('#detailFormContent').html('<div class="text-danger">Gagal memuat data. Refresh dan coba lagi.</div>').show();
                });
        });


        // submit form update via AJAX
        $(document).on('submit', '#formDetail', function(e) {
            e.preventDefault();

            const $btn = $('#m_save_btn');
            $btn.prop('disabled', true);
            $('#m_save_text').hide();
            $('#m_save_loading').show();

            const data = $(this).serialize(); // kirim semua field
            $.post("<?= base_url($role . '/warehouse/updateDatangBenang') ?>", data)
                .done(function(res) {
                    // Asumsikan res = { success: true, message: '...' }
                    if (res && res.success) {
                        $('#detailModal').modal('hide');
                        // refresh tabel: panggil search lagi atau dataTable reload
                        $('#btnSearch').trigger('click'); // karena kamu pake manual load
                        Swal.fire('Sukses', res.message || 'Data tersimpan', 'success');
                    } else {
                        // tampilkan error (bisa tampilkan per-field jika server kirim validation errors)
                        Swal.fire('Gagal', (res && res.message) || 'Terjadi kesalahan', 'error');
                    }
                })
                .fail(function(xhr) {
                    let msg = 'Gagal menyimpan data';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire('Error', msg, 'error');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                    $('#m_save_text').show();
                    $('#m_save_loading').hide();
                });
        });

    });
</script>


<?php $this->endSection(); ?>