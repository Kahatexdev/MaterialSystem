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
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Schedule Celup</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="">Tanggal Schedule Dari</label>
                    <input type="date" class="form-control" id="schDateStart" required>
                </div>
                <div class="col-md-4">
                    <label for="">Tanggal Schedule Sampai</label>
                    <input type="date" class="form-control" id="schDateEnd" required>
                </div>
                <div class="col-md-4">
                    <label for="">Jenis</label>
                    <select name="jenis" id="jenis" class="form-select">
                        <option value="">PILIH JENIS</option>
                        <option value="BENANG">BENANG</option>
                        <option value="ACRYLIC">ACRYLIC</option>
                    </select>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kapasitas</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Urut</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Schedule</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Celup</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Actual Celup</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Start Mc</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
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
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
            "serverSide": false
        });

        function loadData() {
            let tanggal_awal = $('#schDateStart').val().trim();
            let tanggal_akhir = $('#schDateEnd').val().trim();
            let jenis = $('#jenis').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Filter Tanggal Terlebih Dahulu!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/schedule/filterSchWeekly') ?>",
                type: "GET",
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    jenis: jenis
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
                                item.min_caps + ' - ' + item.max_caps, // item.min_caps + '-' + item.max_caps,
                                item.no_mesin,
                                item.lot_urut,
                                item.no_model,
                                item.tanggal_schedule,
                                parseFloat(item.kg_celup).toFixed(2),
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
                        Swal.fire({
                            icon: 'info',
                            title: 'Data Tidak Ditemukan',
                            text: 'Pencarian dengan filter: Tanggal Awal Schedule "' + tanggal_awal + '", Tanggal Akhir Schedule "' + tanggal_akhir + '", Jenis "' + jenis + '" menghasilkan 0 data.',
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
            let tanggal_awal = $('#schDateStart').val().trim();
            let tanggal_akhir = $('#schDateEnd').val().trim();
            let jenis = $('#jenis').val().trim();
            window.location.href = "<?= base_url($role . '/schedule/exportScheduleWeekly') ?>?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&jenis=" + jenis;
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
<!-- <script>
    const startDateInput = document.getElementById('schDateStart');
    const endDateInput = document.getElementById('schDateEnd');

    function validateDateRange() {
        const startValue = startDateInput.value;
        const endValue = endDateInput.value;

        if (startValue && endValue) {
            const start = new Date(startValue);
            const end = new Date(endValue);

            const dayDiff = (end - start) / (1000 * 60 * 60 * 24);

            if (dayDiff < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Tidak Valid',
                    text: 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.',
                });
                endDateInput.value = ''; // reset input
            } else if (dayDiff > 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Range Melebihi Batas',
                    text: 'Rentang tanggal maksimal hanya 4 hari.',
                });
                endDateInput.value = ''; // reset input
            }
        }
    }

    startDateInput.addEventListener('change', validateDateRange);
    endDateInput.addEventListener('change', validateDateRange);
</script> -->


<?php $this->endSection(); ?>