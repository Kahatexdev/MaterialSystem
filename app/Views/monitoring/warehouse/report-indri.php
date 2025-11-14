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
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Report</h5>
                <button class="btn btn-secondary btn-block" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <label for="">Buyer</label>
                    <input type="text" class="form-control" name="buyer" id="buyer" placeholder="Buyer">
                </div>
                <div class="col-md-3">
                    <label for="">No Model</label>
                    <input type="text" class="form-control" name="no_model" id="no_model" placeholder="No Model">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                        <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                        <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3 font-weight-bolder">Tabel</h5>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Buyer</th>
                            <th class="text-center">No Model</th>
                            <th class="text-center">Area</th>
                            <th class="text-center">Item Type</th>
                            <th class="text-center">Kode Warna</th>
                            <th class="text-center">Warna</th>
                            <th class="text-center">Loss</th>
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
        let dataTable = $('#dataTable').DataTable();

        // function showLoading() {
        //     $('#loadingOverlay').addClass('active');
        //     $('#btnSearch').prop('disabled', true);
        //     // show DataTables processing indicator if available
        //     try {
        //         dataTable.processing(true);
        //     } catch (e) {}
        // }

        // function hideLoading() {
        //     $('#loadingOverlay').removeClass('active');
        //     $('#btnSearch').prop('disabled', false);
        //     try {
        //         dataTable.processing(false);
        //     } catch (e) {}
        // }

        // function updateProgress(percent) {
        //     $('#progressBar')
        //         .css('width', percent + '%')
        //         .attr('aria-valuenow', percent);
        //     $('#progressText').text(percent + '%');
        // }

        function loadData() {
            const buyer = $('#buyer').val();
            const no_model = $('#no_model').val();

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterReportIndri') ?>",
                type: "GET",
                data: {
                    buyer: buyer,
                    no_model: no_model
                },
                dataType: "json",

                success: function(response) {
                    console.log(response);
                    dataTable.clear(); // kosongkan dulu
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.buyer || '',
                                item.no_model || '',
                                item.area || '',
                                item.item_type || '',
                                item.kode_warna || '',
                                item.color || '',
                                item.loss || '',
                            ]);
                        });
                        $('#btnExport').removeClass('d-none');
                    } else {
                        $('#btnExport').addClass('d-none');
                    }
                    dataTable.draw(); // render ulang dengan pagination
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            const buyer = $('#buyer').val().trim();
            const no_model = $('#no_model').val().trim();

            const url = "<?= base_url($role . '/warehouse/exportReportIndri') ?>" + "?buyer=" + encodeURIComponent(buyer) + "&no_model=" + encodeURIComponent(no_model);

            window.location.href = url;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan buyer
        $('#buyer').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>
<script>
    $(document).ready(function() {
        // Trigger pencarian saat tombol Enter ditekan di input apa pun
        $('#buyer').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Hindari form submit default (jika ada form)
                $('#btnSearch').click(); // Trigger tombol Search
            }
        });
    });
</script>

<?php $this->endSection(); ?>