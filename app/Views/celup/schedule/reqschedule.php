<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">


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
                        <div class="d-flex flex-column">
                            <label for="filter_nomodel" class="form-label">LOT / Model / Kode Warna</label>
                            <input type="text" id="filter_nomodel" name="filter_nomodel" class="form-control" placeholder="LOT / Model / Kode Warna">
                        </div>
                        <button class="btn btn-filter mt-md-4" id="filter_date_range" type="submit">
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
                <table class="table" id="ReqScheduleTable">
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
        $('#ReqScheduleTable').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 10,
            "ajax": {
                "url": "<?= base_url($role . '/getDataSchedule') ?>",
                "type": "POST"
            },
            "columns": [{
                    "data": "no"
                }, // No
                {
                    "data": "no_mc"
                }, // No Mc
                {
                    "data": "no_model"
                }, // PO
                {
                    "data": "item_type"
                }, // Jenis Benang
                {
                    "data": "lot_celup"
                }, // LOT
                {
                    "data": "kode_warna"
                }, // Kode Warna
                {
                    "data": "warna"
                }, // Warna
                {
                    "data": "start_mc"
                }, // Start Mc
                {
                    "data": "tanggal_schedule"
                }, // Tanggal Schedule
                { // Action
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "language": {
                "emptyTable": "Data belum tersedia untuk saat ini."
            }
        });
    });

    document.getElementById('filter_date_range').addEventListener('click', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih rentang tanggal.',
            });
            return;
        }

        // Redirect ke URL dengan parameter filter
        const url = `<?= base_url($role . '/reqschedule') ?>?start_date=${startDate}&end_date=${endDate}`;
        window.location.href = url;
    });

    // reset filter tanggal
    document.getElementById('reset_date_range').addEventListener('click', function() {
        // Hapus nilai input tanggal
        document.getElementById('filter_tglsch').value = '';
        document.getElementById('filter_tglschsampai').value = '';
        document.getElementById('filter_nomodel').value = '';

        // Redirect ke URL tanpa parameter filter
        const url = `<?= base_url($role . '/reqschedule') ?>`;
        window.location.href = url;
    });
</script>
<?php $this->endSection(); ?>