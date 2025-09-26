<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<style>
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
            <form method="post" action="<?= base_url($role . '/schedule') ?>">
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
                        <!-- <div class="d-flex flex-column">
                            <label for="filter_nomodel" class="form-label">LOT / Model / Kode Warna</label>
                            <input type="text" id="filter_nomodel" name="filter_nomodel" class="form-control" placeholder="LOT / Model / Kode Warna">
                        </div> -->
                        <button class="btn btn-filter mt-md-4" id="filter_date_range" type="button">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <!-- btn reset -->
                        <button class="btn btn-secondary mt-md-4" id="reset_date_range" type="button">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="ReqScheduleTable" style="width:100%">
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

<script>
    $(document).ready(function() {
        var table = $('#ReqScheduleTable').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 10,
            "ajax": {
                "url": "<?= base_url($role . '/getDataEditSchedule') ?>",
                "type": "POST",
                "data": function(d) {
                    d.filter_tglsch = $('#filter_tglsch').val();
                    d.filter_tglschsampai = $('#filter_tglschsampai').val();
                    d.filter_nomodel = $('#filter_nomodel').val();
                }
            },
            "columns": [{
                    "data": "no"
                },
                {
                    "data": "no_mc"
                },
                {
                    "data": "no_model"
                },
                {
                    "data": "item_type"
                },
                {
                    "data": "lot_celup"
                },
                {
                    "data": "kode_warna"
                },
                {
                    "data": "warna"
                },
                {
                    "data": "start_mc"
                },
                {
                    "data": "tanggal_schedule"
                },
                {
                    "data": "ket_schedule",
                    className: "wrap-text"
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "language": {
                "emptyTable": "Data belum tersedia untuk saat ini."
            }
        });

        // tombol filter
        $('#filter_date_range').on('click', function() {
            const startDate = $('#filter_tglsch').val();
            const endDate = $('#filter_tglschsampai').val();

            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silakan pilih rentang tanggal.',
                });
                return;
            }

            table.ajax.reload();
        });

        // tombol reset
        $('#reset_date_range').on('click', function() {
            $('#filter_tglsch').val('');
            $('#filter_tglschsampai').val('');
            $('#filter_nomodel').val('');
            table.ajax.reload();
        });
    });
</script>

<?php $this->endSection(); ?>