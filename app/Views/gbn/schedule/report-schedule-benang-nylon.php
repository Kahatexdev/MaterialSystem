<?php $this->extend($role . '/schedule/header'); ?>
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
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Schedule Benang & Nylon</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Schedule Dari</label>
                    <input type="date" class="form-control" id="schDateStart">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Schedule Sampai</label>
                    <input type="date" class="form-control" id="schDateEnd">
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
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kapasitas</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Urut</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Celup</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Actual Celup</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Start Mc</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
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
            let tanggal_awal = $('#schDateStart').val().trim();
            let tanggal_akhir = $('#schDateEnd').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Apa yang mau dicari cuy?',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/schedule/filterSchBenangNylon') ?>",
                type: "GET",
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.min_caps + ' - ' + item.max_caps, // item.min_caps + '-' + item.max_caps,
                                item.no_mesin,
                                item.lot_urut,
                                item.no_model,
                                item.kg_celup,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                item.lot_celup,
                                item.actual_celup !== undefined && item.actual_celup !== null ? item.actual_celup : 0,
                                item.start_mc,
                                item.delivery_awal,
                                item.ket_celup ? item.ket_celup : '-',
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
            let tanggal_awal = $('#schDateStart').val().trim();
            let tanggal_akhir = $('#schDateEnd').val().trim();
            window.location.href = "<?= base_url($role . '/schedule/exportScheduleBenangNylon') ?>?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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