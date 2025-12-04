<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<style>
    .custom-outline {
        color: #344767;
        border-color: #344767;
    }

    .custom-outline:hover {
        background-color: #344767;
        color: white;
    }
</style>

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
    <div class="card border-0 rounded-top-4 shadow-lg">
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767;">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Pemesanan Karet</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6">
                    <label for="">Tanggal Awal (Tanggal Pakai)</label>
                    <input type="date" class="form-control" id="tglAwal">
                </div>
                <div class="col-md-6">
                    <label for="">Tanggal Akhir (Tanggal Pakai)</label>
                    <input type="date" class="form-control" id="tglAkhir">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="btn-group" role="group">
                            <button class="btn text-white px-4" id="btnSearch" style="background-color: #344767;">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <button class="btn btn-outline custom-outline" id="btnReset">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                        <!-- <button class="btn btn-info d-none" id="btnExport">
                            <i class="fas fa-file-excel me-2"></i>Ekspor
                        </button> -->
                    </div>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Global</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Per Area</th>
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
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih tanggal terlebih dahulu',
                });
                return;
            }

            // Validasi 2: Salah satu tanggal doang yang diisi
            if ((tanggal_awal !== '' && tanggal_akhir === '') || (tanggal_awal === '' && tanggal_akhir !== '')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggalnya Belum Diisi',
                    text: 'Isi kedua tanggal kalau mau filter berdasarkan tanggal, jangan setengah-setengah cuy.',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/filterPemesananKaretCovering') ?>",
                type: "GET",
                data: {
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
                            var tgl = item;
                            var jenis = "KARET";

                            var link1 = '<a href="<?= base_url($role . '/exportPemesananSandexKaretCovering') ?>?tgl_pakai=' + tgl + '&jenis=' + jenis + '" target="_blank">' +
                                '<i class="fas fa-file-excel"></i>' +
                                '</a>';

                            var link2 = '<a href="<?= base_url($role . '/excelPemesananCoveringPerArea') ?>?tgl_pakai=' + tgl + '&jenis=' + jenis + '" target="_blank">' +
                                '<i class="fas fa-file-excel"></i>' +
                                '</a>';

                            dataTable.row.add([
                                index + 1,
                                tgl,
                                link1,
                                link2
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    // console.error("Error:", error);
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
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();
            window.location.href = "<?= base_url($role . '/excelPemesananKaretCovering') ?>?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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
</script>


<?php $this->endSection(); ?>