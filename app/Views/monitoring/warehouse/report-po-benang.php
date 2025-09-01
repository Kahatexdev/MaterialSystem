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
                <h5 class="mb-0 font-weight-bolder">Filter PO <?= $jenis ?></h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-9">
                    <label for="">Key</label>
                    <input type="text" class="form-control" placeholder="No Model/Item Type/Kode Warna/Warna">
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
                <table id="dataTable" class="table table-bordered table-hover text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Waktu Input</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Memo</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Start MC</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Order Type</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock Awal</th>
                            <th colspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Pesan</th>
                            <th colspan="4" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PO Tambahan Gbn</th>
                            <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                        </tr>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>

                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>

                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Terima Po(+) Gbn</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Po(+) Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Po(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Po(+)</th>
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
            let key = $('input[type="text"]').val().trim();
            const jenis = "<?= $jenis ?>";

            // Validasi
            if (key === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Key tidak boleh kosong!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterPoBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    jenis: jenis
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
                                item.tgl_input,
                                item.lco_date,
                                item.foll_up,
                                item.no_model,
                                item.no_order,
                                item.area,
                                item.memo,
                                item.buyer,
                                item.start_mc,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.unit || '',
                                item.item_type,
                                item.kode_warna,
                                item.color,
                                parseFloat(item.kgs_stock || 0).toFixed(2),
                                item.lot_stock || '',
                                parseFloat(item.kg_po).toFixed(2) || '',
                                item.loss + '%' || '',
                                item.tanggal_approve || '',
                                item.tgl_po_plus_area || '',
                                item.delivery_po_plus || '',
                                parseFloat(item.kg_po_plus || 0).toFixed(2) || '',
                                item.admin || '',
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
            const jenis = "<?= $jenis ?>";
            window.location.href = "<?= base_url($role . '/warehouse/exportPoBenang') ?>?key=" + key + "&jenis=" + jenis;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>


<?php $this->endSection(); ?>