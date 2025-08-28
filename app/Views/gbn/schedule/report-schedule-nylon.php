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
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767;">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4 text-white"></i>
                <h4 class="mb-0 fw-bold text-white">Filter Schedule Nylon</h4>
            </div>
        </div>

        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-3">
                    <label for="keyInput">Key</label>
                    <input type="text" class="form-control" id="keyInput" placeholder="No Model/Kode Warna">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="scheduleDate">Tanggal Schedule</label>
                    <input type="date" class="form-control" id="scheduleDate">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="startMcFrom">Tgl Start MC Dari</label>
                    <input type="date" class="form-control" id="startMcFrom" required>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="startMcTo">Tgl Start MC Sampai</label>
                    <input type="date" class="form-control" id="startMcTo" required>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ket Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Urut</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Start Mc</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Sch</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Sch</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Celup</th>
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
            let tanggal_schedule = $('#scheduleDate').val().trim();
            let tanggal_awal = $('#startMcFrom').val().trim();
            let tanggal_akhir = $('#startMcTo').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tgl Start MC Wajib Diisi Keduanya !',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/schedule/filterSchNylon') ?>",
                type: "GET",
                data: {
                    key: key,
                    tanggal_schedule: tanggal_schedule,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loadingCard").show(); // Tampilkan loading
                    // disable btn filter
                    $("#btnSearch").prop("disabled", true);
                    $('#btnReset').prop('disabled', true);
                    $('#btnExport').prop('disabled', true);
                },
                complete: function() {
                    $("#loadingCard").hide(); // Sembunyikan loading setelah selesai
                    // enable btn filter
                    $("#btnSearch").prop("disabled", false);
                    $('#btnReset').prop('disabled', false);
                    $('#btnExport').prop('disabled', false);
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_mesin,
                                item.ket_mesin,
                                item.lot_urut,
                                item.no_model,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                item.start_mc,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.tanggal_schedule,
                                item.kg_celup,
                                item.lot_celup,
                                item.tanggal_celup,
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                        Swal.fire({
                            icon: 'info',
                            title: 'Data Tidak Ditemukan',
                            text: 'Pencarian dengan filter: Key "' + key + '", Tanggal Schedule "' + tanggal_schedule + '", Tgl Start MC Dari "' + tanggal_awal + '", Tgl Start MC Sampai "' + tanggal_akhir + '" menghasilkan 0 data.',
                        });
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
            let tanggal_schedule = $('#scheduleDate').val().trim();
            let tanggal_awal = $('#startMcFrom').val().trim();
            let tanggal_akhir = $('#startMcTo').val().trim();
            window.location.href = "<?= base_url($role . '/schedule/exportScheduleNylon') ?>?key=" + key + "&tanggal_schedule=" + tanggal_schedule + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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