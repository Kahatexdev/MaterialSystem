<?php $this->extend($role . '/masterdata/header'); ?>
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

    <!-- Button Filter -->
    <div class="card border-0 rounded-top-4 shadow-lg">
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Master Order</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-4">
                    <label for="keyInput">Key</label>
                    <input type="text" class="form-control" id="keyInput" placeholder="Buyer/Foll Up">
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Awal (Tanggal Delivery Awal)</label>
                    <input type="date" class="form-control" id="tglAwal">
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Akhir (Tanggal Delivery Awal)</label>
                    <input type="date" class="form-control" id="tglAkhir">
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
            <!-- card loading -->
            <div class="card loading" id="loadingCard" style="display: none;">
                <div class="card-body text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LCO Date</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Memo</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Unit</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Updated At</th>
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

        function loadData() {
            let key = $('#keyInput').val().trim();
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (key === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap isi filter terlebih dahulu!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/masterdata/filterMasterOrder') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                beforeSend: function() {
                    $('#loadingCard').show();
                    // disable btn
                    $('#btnSearch').prop('disabled', true);
                    $('#btnReset').prop('disabled', true);
                    $('#btnExport').prop('disabled', true);
                },
                complete: function() {
                    $('#loadingCard').hide();
                    // enable btn
                    $('#btnSearch').prop('disabled', false);
                    $('#btnReset').prop('disabled', false);
                    $('#btnExport').prop('disabled', false);
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_order,
                                item.no_model,
                                item.buyer,
                                item.foll_up,
                                item.lco_date,
                                item.memo,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.unit,
                                item.admin,
                                item.created_at,
                                item.updated_at
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
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
            let key = $('#keyInput').val().trim();
            key = encodeURIComponent(key);
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();
            window.location.href = "<?= base_url($role . '/masterdata/excelMasterOrder') ?>?key=" + key + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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