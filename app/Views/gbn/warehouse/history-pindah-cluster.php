<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

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

    .gap-2>* {
        margin-right: .4rem;
    }

    .progress {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .progress-bar {
        transition: width .3s ease;
    }
</style>

<div class="container-fluid py-4">
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
            <div class="row mt-2 align-items-end">

                <div class="col-md-8">
                    <label>Filter (No Model / Itemtype / Kode Warna / Lot / Cluster)</label>
                    <input type="text" class="form-control" name="key" id="key" placeholder="Contoh: PDK001 atau III.A.03.A atau 25I451">
                </div>

                <div class="col-md-4">
                    <label>Aksi</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-info" id="btnSearch"><i class="fas fa-search"></i></button>
                        <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                        <button class="btn btn-success" id="btnExportAll"><i class="fas fa-file-excel"></i></button>
                        <button class="btn btn-success d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cluster Old</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cluster New</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--  -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            paging: true,
            searching: false,
            ordering: true,
            info: true,
            responsive: true,
            processing: true,
            serverSide: false
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

        // Ambil data awal pas page load
        loadDefaultData();

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExportAll').click(function() {
            let key = $('input[name="key"]').val().trim();
            if (key === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi key sebelum melakukan export!',
                });
                return;
            }
        });

        $('#btnExport').click(function() {
            const key = $('#key').val().trim();
            window.location.href = "<?= base_url("$role/warehouse/exportHistoryPindahCluster") ?>" +
                "?key=" + encodeURIComponent(key);
        });

        $('#btnReset').click(function() {
            $('#key').val('');

            loadDefaultData();

            $('#btnExportAll').removeClass('d-none');
            $('#btnExport').addClass('d-none');
        });

        function loadData() {
            let key = $('input[name="key"]').val().trim();

            if (key === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/warehouse/historyPindahCluster') ?>",
                type: "GET",
                data: {
                    key: key
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

                    $.each(response, function(index, item) {
                        dataTable.row.add([
                            index + 1,
                            item.no_model || '-',
                            item.item_type || '-',
                            item.kode_warna || '-',
                            item.warna || '-',
                            item.kgs,
                            item.cns,
                            item.lot,
                            item.cluster_old,
                            item.cluster_new,
                            item.created_at + ' ' + item.keterangan + ' dari ' + item.cluster_old + ' ke ' + item.cluster_new,
                            item.admin,
                        ]).draw(false);
                    });

                    // Atur tombol export
                    $('#btnExportAll').addClass('d-none'); // Hilangkan tombol export all
                    $('#btnExport').removeClass('d-none');
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    updateProgress(100); // pastikan full
                    setTimeout(() => hideLoading(), 400); // kasih jeda biar animasi progress keliatan
                }
            });
        };

        function loadDefaultData() {
            $.ajax({
                url: "<?= base_url($role . '/warehouse/historyPindahCluster') ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();

                    $.each(response, function(index, item) {
                        dataTable.row.add([
                            index + 1,
                            item.no_model || '-',
                            item.item_type || '-',
                            item.kode_warna || '-',
                            item.warna || '-',
                            item.kgs,
                            item.cns,
                            item.lot,
                            item.cluster_old,
                            item.cluster_new,
                            item.created_at + ' ' + item.keterangan + ' ke ' + item.no_model + ' kode ' + item.kode_warna,
                            item.admin
                        ]).draw(false);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }
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

    // Trigger pencarian saat tombol Enter ditekan di input apa pun
    $('input[type="text"]').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Hindari form submit default (jika ada form)
            $('#btnSearch').click(); // Trigger tombol Search
        }
    });
</script>


<?php $this->endSection(); ?>