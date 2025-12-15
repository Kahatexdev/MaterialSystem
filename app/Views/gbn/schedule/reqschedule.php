<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

<style>
    .card {
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.1);
        border: none;
        background-color: white;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 15px 30px rgba(76, 175, 80, 0.15);
        transform: translateY(-5px);
    }

    .table {
        border-radius: 15px;
        /* overflow: hidden; */
        border-collapse: separate;
        /* Ganti dari collapse ke separate */
        border-spacing: 0;
        /* Pastikan jarak antar sel tetap rapat */
        overflow: auto;
        position: relative;
    }

    .table th {

        background-color: rgb(8, 38, 83);
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgb(255, 255, 255);
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: rgb(237, 237, 237);
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: rgb(4, 55, 91);
    }

    .table td.sticky {
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #e3f2fd;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
    }


    .capacity-bar {
        height: 6px;
        border-radius: 3px;
        margin-bottom: 5px;
    }

    .btn {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
    }

    .btn-filter {
        background: linear-gradient(135deg, #1e88e5, #64b5f6);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
    }

    .date-navigation {
        background-color: white;
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(33, 150, 243, 0.1);
    }

    .date-navigation input[type="date"] {
        border: none;
        font-weight: 500;
        color: #1565c0;
    }

    .machine-info {
        font-size: 0.85rem;
    }

    .machine-info strong {
        font-size: 1rem;
        color: #2e7d32;
    }

    .job-item {
        background-color: white;
        border-radius: 10px;
        padding: 0.7rem;
        margin-bottom: 0.7rem;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.1);
        transition: all 0.2s ease;
    }

    .job-item:hover {
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    .job-item span {
        font-size: 0.8rem;
        color: #558b2f;
    }

    .job-item .btn {
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .job-item .btn span {
        font-size: 0.9rem;
        color: black;
        font-weight: bold;
    }

    .job-item .btn .total-kg {
        font-size: 0.85rem;
    }

    .no-schedule .btn {
        background-color: #f8f9fa;
        border: 1px dashed #ccc;
        color: #6c757d;
    }


    .bg-success {
        background-color: #66bb6a !important;
    }

    .bg-warning {
        background-color: #ffd54f !important;
    }

    .bg-danger {
        background-color: #ef5350 !important;
    }

    .text-success {
        color: #43a047 !important;
    }

    table.dataTable td.wrap-text {
        white-space: normal !important;
        word-break: break-word;
        max-width: 150px;
    }
</style>

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url($role . '/schedule/reqschedule') ?>">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <h3 class="mb-0 text-center text-md-start">Schedule Mesin Celup</h3>
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-center">
                        <div class="d-flex flex-column">
                            <label for="filter_tglsch" class="form-label">Tgl Schedule(Dari)</label>
                            <input type="date" id="filter_tglsch" name="filter_tglsch" class="form-control" value="<?= old('filter_tglsch') ?>">
                        </div>
                        <div class="d-flex flex-column">
                            <label for="filter_tglschsampai" class="form-label">Tgl Schedule(Sampai)</label>
                            <input type="date" id="filter_tglschsampai" name="filter_tglschsampai" class="form-control" value="<?= old('filter_tglschsampai') ?>">
                        </div>
                        <div class="d-flex flex-column">
                            <label for="filter_nomodel" class="form-label">LOT / Model / Kode Warna</label>
                            <input type="text" id="filter_nomodel" name="filter_nomodel" class="form-control" placeholder="LOT / Model / Kode Warna">
                        </div>
                        <!-- Buttons (ensure these IDs match the JS) -->
                        <button class="btn btn-filter mt-md-4" id="btnFilter" type="button">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <button class="btn btn-filter mt-md-4" id="btnReset" type="button">
                            <i class="fas fa-redo-alt"></i> Reset
                        </button>
                        <button type="button" class="btn btn-success mt-md-4 d-none" id="btnExcel">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="ReqScheduleTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="sticky">No</th>
                            <th class="sticky">No Mc</th>
                            <th class="sticky">PO</th>
                            <th class="sticky">Jenis Benang</th>
                            <th class="sticky">LOT</th>
                            <th class="sticky">Kode Warna</th>
                            <th class="sticky">Warna</th>
                            <th class="sticky">Start Mc</th>
                            <th class="sticky">Tanggal Schedule</th>
                            <th class="sticky">Last Status</th>
                            <th class="sticky">Kg Pesan</th>
                            <th class="sticky">Kg Celup</th>
                            <th class="sticky">Keterangan</th>
                            <th class="sticky">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script>
    $(function() {
        const table = $('#ReqScheduleTable').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true, // ✅ render baris saat perlu
            autoWidth: false, // ✅ hindari layout thrash
            pageLength: 10,
            ordering: true,
            ajax: {
                url: "<?= base_url($role . '/getDataSchedule') ?>",
                type: "POST",
                data: function(d) {
                    d.filter_tglsch = $('#filter_tglsch').val();
                    d.filter_tglschsampai = $('#filter_tglschsampai').val();
                    d.filter_nomodel = $('#filter_nomodel').val();
                }
            },
            columns: [{
                    data: "no"
                }, {
                    data: "no_mc"
                }, {
                    data: "no_model"
                }, {
                    data: "item_type"
                },
                {
                    data: "lot_celup"
                }, {
                    data: "kode_warna"
                }, {
                    data: "warna"
                },
                {
                    data: "start_mc"
                }, {
                    data: "tanggal_schedule"
                }, {
                    data: "last_status"
                },
                {
                    data: "kg_po"
                }, {
                    data: "kg_celup"
                },
                {
                    data: "ket_schedule",
                    className: "wrap-text"
                },
                {
                    data: "action",
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                emptyTable: "Data belum tersedia untuk saat ini.",
                infoFiltered: ""
            }
        });

        function buildExcelUrl() {
            const qs = $.param({
                filter_tglsch: $('#filter_tglsch').val() || '',
                filter_tglschsampai: $('#filter_tglschsampai').val() || '',
                filter_nomodel: $('#filter_nomodel').val() || ''
            });
            return "<?= base_url($role . '/schedule/exportReqSchedule') ?>?" + qs;
        }

        $('#btnFilter').on('click', function() {
            const start = $('#filter_tglsch').val();
            const end = $('#filter_tglschsampai').val();
            const key = $('#filter_nomodel').val();
            // 1) Minimal salah satu filter diisi
            if (!start && !end && !key) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silakan isi minimal salah satu filter (tanggal dari-sampai ATAU No Model/Lot/Kode).'
                });
                return;
            }
            table.ajax.reload();

            // Excel button
            const url = buildExcelUrl();
            $('#btnExcel').removeClass('d-none').off('click').on('click', function() {
                window.location.href = url;
            });
        });

        $('#btnReset').on('click', function() {
            $('#filter_tglsch').val('');
            $('#filter_tglschsampai').val('');
            $('#filter_nomodel').val('');
            table.ajax.reload();
            $('#btnExcel').addClass('d-none').off('click');
        });
    });
</script>

<?php $this->endSection(); ?>