<?php $this->extend($role . '/retur/header'); ?>
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
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Retur Area</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-3">
                    <label for="area">Area</label>
                    <select class="form-select mt-2" name="area" id="area">
                        <option value="">Pilih Area</option>
                        <option value="KK1A">KK1A</option>
                        <option value="KK1B">KK1B</option>
                        <option value="KK2A">KK2A</option>
                        <option value="KK2B">KK2B</option>
                        <option value="KK2C">KK2C</option>
                        <option value="KK5G">KK5G</option>
                        <option value="KK7K">KK7K</option>
                        <option value="KK7L">KK7L</option>
                        <option value="KK8D">KK8D</option>
                        <option value="KK8F">KK8F</option>
                        <option value="KK8J">KK8J</option>
                        <option value="KK9D">KK9D</option>
                        <option value="KK10">KK10</option>
                        <option value="KK11M">KK11M</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="kategoriRetur">Nama Kategori</label>
                    <select class="form-select mt-2" name="kategori_retur" id="kategori_retur">
                        <option value="">Pilih Kategori Retur</option>
                        <?php foreach ($getKategori as $kategori) : ?>
                            <option value="<?= $kategori['nama_kategori'] ?>">
                                <?= $kategori['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Retur Dari</label>
                    <input type="date" class="form-control" id="tgl_retur_dari">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Retur Sampai</label>
                    <input type="date" class="form-control" id="tgl_retur_sampai">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="btn-group" role="group">
                            <button class="btn text-white px-4" id="btnSearch" style="background-color: #344767">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <button class="btn btn-outline custom-outline" id="btnReset">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                        <button class="btn btn-info d-none" id="btnExport">
                            <i class="fas fa-file-excel me-2"></i>Ekspor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Tanggal Retur</th>
                            <th class="text-center">Area</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">Loss</th>
                            <th class="text-center">Qty PO</th>
                            <th class="text-center">Qty PO(+)</th>
                            <th class="text-center">Qty Kirim</th>
                            <th class="text-center">Cones Kirim</th>
                            <th class="text-center">Karung Kirim</th>
                            <th class="text-center">LOT Kirim</th>
                            <th class="text-center">Qty Retur</th>
                            <th class="text-center">Cones Retur</th>
                            <th class="text-center">Karung Retur</th>
                            <th class="text-center">LOT Retur</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Keterangan Area</th>
                            <th class="text-center">Keterangan Gbn</th>
                            <th class="text-center">Waktu Acc</th>
                            <th class="text-center">User</th>
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
            let area = $('#area').val().trim();
            let kategori = $('#kategori_retur').val().trim();
            let tanggal_awal = $('#tgl_retur_dari').val().trim();
            let tanggal_akhir = $('#tgl_retur_sampai').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (area === '' && kategori === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih salah satu filter untuk menampilkan data retur!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/retur/filterReturArea') ?>",
                type: "GET",
                data: {
                    area: area,
                    kategori: kategori,
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
                                item.jenis,
                                item.tgl_retur,
                                item.area_retur,
                                item.no_model,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                item.loss,
                                item.total_kgs,
                                item.qty_po_plus ?? 0,
                                parseFloat(item.kg_kirim).toFixed(2) ?? 0,
                                item.cns_kirim ?? 0,
                                item.krg_kirim ?? 0,
                                shortLot(item.lot_out),
                                parseFloat(item.kg).toFixed(2) ?? 0,
                                item.cns,
                                item.karung,
                                item.lot_retur,
                                item.kategori,
                                item.keterangan_area,
                                item.keterangan_gbn,
                                item.waktu_acc_retur,
                                item.admin
                            ]);
                        });
                        dataTable.draw();
                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
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
            let area = $('#area').val().trim();
            let kategori = $('#kategori_retur').val().trim();
            let tanggal_awal = $('#tgl_retur_dari').val().trim();
            let tanggal_akhir = $('#tgl_retur_sampai').val().trim();
            window.location.href = "<?= base_url($role . '/retur/exportReturArea') ?>?area=" + area + "&kategori=" + kategori + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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

    function shortLot(lotString, maxLength = 35) {
        if (!lotString) return '';

        if (lotString.length <= maxLength) {
            return lotString;
        }

        return `
        <span title="${lotString}">
            ${lotString.substring(0, maxLength)}...
        </span>
    `;
    }
</script>


<?php $this->endSection(); ?>