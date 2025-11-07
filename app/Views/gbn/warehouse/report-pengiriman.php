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
                <h5 class="mb-0 font-weight-bolder">Filter Pengiriman</h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">Jenis</label>
                    <select class="form-select" name="jenis" id="jenis" required>
                        <option value="">Pilih Jenis</option>
                        <option value="BENANG">BENANG</option>
                        <option value="NYLON">NYLON</option>
                        <option value="SPANDEX">SPANDEX</option>
                        <option value="KARET">KARET</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="">Key</label>
                    <input id="key" type="text" class="form-control" placeholder="PDK / Item Type / Kode Warna / Warna / Lot">
                </div>
                <div class="col-md-2">
                    <label for="">Tanggal Awal (Tanggal Pakai)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="">Tanggal Akhir (Tanggal Pakai)</label>
                    <input type="date" class="form-control">
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
                <table id="dataTable" class="table table-bordered table-hover table-striped text-center text-uppercase" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase">No</th>
                            <th class="text-center text-uppercase">No Model</th>
                            <th class="text-center text-uppercase">Area</th>
                            <th class="text-center text-uppercase">Delivery Awal</th>
                            <th class="text-center text-uppercase">Delivery Akhir</th>
                            <th class="text-center text-uppercase">Item Type</th>
                            <th class="text-center text-uppercase">Kode Warna</th>
                            <th class="text-center text-uppercase">Warna</th>
                            <th class="text-center text-uppercase">Kgs Pesan</th>
                            <th class="text-center text-uppercase">Tanggal Pakai</th>
                            <th class="text-center text-uppercase">Tanggal Keluar</th>
                            <th class="text-center text-uppercase">Kgs Kirim</th>
                            <th class="text-center text-uppercase">Cones Kirim</th>
                            <th class="text-center text-uppercase">Karung Kirim</th>
                            <th class="text-center text-uppercase">LOT Kirim</th>
                            <th class="text-center text-uppercase">Nama Cluster</th>
                            <th class="text-center text-uppercase">Keterangan</th>
                            <th class="text-center text-uppercase">Admin</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
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
            let jenis = $('#jenis').val().trim();
            let key = $('input[type="text"]').val().trim();
            let tanggal_awal = $('input[type="date"]').eq(0).val().trim();
            let tanggal_akhir = $('input[type="date"]').eq(1).val().trim();

            if (jenis === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jenis wajib dipilih!',
                    text: 'Silakan pilih jenis sebelum melakukan pencarian.',
                });
                return;
            }

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi key atau tanggal pakai sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterPengiriman') ?>",
                type: "GET",
                data: {
                    jenis: jenis,
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
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
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_model,
                                item.area_out,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.item_type,
                                item.kode_warna,
                                item.color,
                                item.kgs_pesan,
                                item.tgl_pakai,
                                item.tgl_out,
                                item.kgs_pakai,
                                item.cones_pakai,
                                item.krg_pakai,
                                item.lot_pakai,
                                item.nama_cluster,
                                item.keterangan_gbn,
                                item.admin
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
            let jenis = $('#jenis').val();
            let key = $('input[type="text"]').val();
            let tanggal_awal = $('input[type="date"]').eq(0).val();
            let tanggal_akhir = $('input[type="date"]').eq(1).val();
            window.location.href = "<?= base_url($role . '/warehouse/exportPengiriman') ?>?jenis=" + jenis + "&key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

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


<?php $this->endSection(); ?>