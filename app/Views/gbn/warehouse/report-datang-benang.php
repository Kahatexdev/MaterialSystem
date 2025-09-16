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
                    <input type="text" class="form-control" placeholder="No Model/Item Type/Kode Warna/Warna" style="font-size: 11px;">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Awal (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="">Tanggal Akhir (Tanggal Datang)</label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-3">
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
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Terima</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Order Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">KG Pesan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Datang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Surat Jalan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Harga</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama Cluster</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
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

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '') {
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
                    dataTable.clear();

                    if (Array.isArray(response) && response.length > 0) {
                        let rows = [];
                        $.each(response, function(index, item) {
                            rows.push([
                                index + 1,
                                item.tgl_masuk || '',
                                item.foll_up || '',
                                item.no_model || '',
                                item.no_order || '',
                                item.buyer || '',
                                item.delivery_awal || '',
                                item.delivery_akhir || '',
                                item.unit || '',
                                item.item_type || '',
                                item.kode_warna || '',
                                item.warna || '',
                                (item.kgs_material ? parseFloat(item.kgs_material).toFixed(2) : ''),
                                item.tgl_datang || '',
                                (item.kgs_kirim ? parseFloat(item.kgs_kirim).toFixed(2) : ''),
                                item.cones_kirim || '',
                                item.lot_kirim || '',
                                item.no_surat_jalan || '',
                                item.l_m_d || '',
                                (item.gw_kirim ? parseFloat(item.gw_kirim).toFixed(2) : ''),
                                item.harga || '',
                                item.nama_cluster || '',
                                item.admin || '',
                            ]);
                        });

                        dataTable.rows.add(rows).draw(false);
                        $('#btnExport').removeClass('d-none');
                    } else {
                        // kosongkan tabel jika tidak ada data
                        dataTable.rows().remove().draw(false);
                        $('#btnExport').addClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    // bisa tampilkan notifikasi kalau perlu
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal mendapatkan data. Coba lagi.',
                    });
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
            window.location.href = "<?= base_url($role . '/warehouse/exportDatangBenang') ?>?key=" + encodeURIComponent(key) + "&tanggal_awal=" + encodeURIComponent(tanggal_awal) + "&tanggal_akhir=" + encodeURIComponent(tanggal_akhir);
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
</script>

<?php $this->endSection(); ?>