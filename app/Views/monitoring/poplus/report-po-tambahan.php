<?php $this->extend($role . '/poplus/header'); ?>
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
                <!-- <h5 class="mb-0 font-weight-bolder">Filter Pengiriman</h5> -->
            </div>
            <div class="row mt-2">
                <div class="col-md-2">
                    <label for="">No Model</label>
                    <input type="text" class="form-control" name="no_model" id="no_model">
                </div>
                <div class="col-md-2">
                    <label for="">Area</label>
                    <input type="text" class="form-control" name="area" id="area">
                </div>
                <div class="col-md-2">
                    <label for="">Kode Warna</label>
                    <input type="text" class="form-control" name="kode_warna" id="kode_warna">
                </div>
                <div class="col-md-2">
                    <label for="">Tanggal PO Dari</label>
                    <input type="date" class="form-control" name="tgl_po_dari" id="tgl_po_dari">
                </div>
                <div class="col-md-2">
                    <label for="">Tanggal PO Sampai</label>
                    <input type="date" class="form-control" name="tgl_po_sampai" id="tgl_po_sampai">
                </div>
                <div class="col-md-2">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                    <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>

                    <button class="btn btn-primary" id="btnExport"><i class="fas fa-file-excel"></i></button>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($poPlus as $data) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['tgl_poplus'] ?></td>
                                <td><?= $data['area'] ?? '-' ?></td>
                                <td><?= $data['no_model'] ?? '-' ?></td>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= number_format($data['kg_poplus'], 2) ?></td>
                                <td><?= $data['cns_poplus'] ?></td>
                                <td><?= $data['keterangan'] ?></td>
                            </tr>
                        <?php
                        } ?>
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

        $('#btnExport').click(function() {
            const m = $('#no_model').val().trim();
            const a = $('#area').val().trim();
            const k = $('#kode_warna').val().trim();
            const td = $('#tgl_po_dari').val().trim();
            const ts = $('#tgl_po_sampai').val().trim();
            window.location.href = "<?= base_url("$role/poplus/exportPoTambahan") ?>" +
                "?model=" + encodeURIComponent(m) +
                "&area=" + encodeURIComponent(a) +
                "&kode_warna=" + encodeURIComponent(k) +
                "&tgl_po_dari=" + encodeURIComponent(td) +
                "&tgl_po_sampai=" + encodeURIComponent(ts);
        });

        $('#btnReset').click(function() {
            $('#no_model').val('');
            $('#area').val('');
            $('#kode_warna').val('');
            $('#tgl_po_dari').val('');
            $('#tgl_po_sampai').val('');
            $('input[type="date"]').val('');

            loadDefaultData();

            $('#btnExportAll').removeClass('d-none');
            $('#btnExport').addClass('d-none');
        });

        function loadData() {
            let no_model = $('input[name="no_model"]').val().trim();
            let area = $('input[name="area"]').val().trim();
            let kode_warna = $('input[name="kode_warna"]').val().trim();
            let tgl_po_dari = $('input[name="tgl_po_dari"]').val().trim();
            let tgl_po_sampai = $('input[name="tgl_po_sampai"]').val().trim();

            console.log({
                model: no_model,
                area: area,
                kode_warna: kode_warna,
                tgl_po_dari: tgl_po_dari,
                tgl_po_sampai: tgl_po_sampai
            }); // DEBUG

            if (no_model === '' && area === '' && kode_warna === '' && tgl_po_dari === '' && tgl_po_sampai === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi minimal salah satu kolom sebelum melakukan pencarian!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/poplus/reportPoTambahan') ?>",
                type: "POST",
                data: {
                    model: no_model,
                    area: area,
                    kode_warna: kode_warna,
                    tgl_po_dari: tgl_po_dari,
                    tgl_po_sampai: tgl_po_sampai
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
                            item.tgl_poplus || '-',
                            item.admin,
                            item.no_model,
                            item.item_type || '-',
                            item.kode_warna || '-',
                            item.color || '-',
                            parseFloat(item.kg_poplus).toFixed(2),
                            item.cns_poplus,
                            item.keterangan
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
                url: "<?= base_url($role . '/poplus/reportPoTambahan') ?>",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log("RESPONSE:", response); // DEBUG
                    dataTable.clear().draw();

                    $.each(response, function(index, item) {
                        dataTable.row.add([
                            index + 1,
                            item.tgl_poplus || '-',
                            item.admin || '-',
                            item.no_model,
                            item.item_type || '-',
                            item.kode_warna || '-',
                            item.color || '-',
                            parseFloat(item.kg_poplus).toFixed(2),
                            item.cns_poplus,
                            item.keterangan
                        ]).draw(false);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }
    });
</script>


<?php $this->endSection(); ?>