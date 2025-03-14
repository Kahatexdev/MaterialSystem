<?php $this->extend($role . '/memo/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">

<style>
    
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

    <div class="card">
        <div class="card-header">
            <h5>Planning Jalan Mesin Covering Benang - MEMO</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('memo/planning/save') ?>" method="post">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="machine" class="form-label">Mesin</label>
                        <input type="text" name="machine" id="machine" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="schedule_date" class="form-label">Tanggal</label>
                        <input type="date" name="schedule_date" id="schedule_date" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="operator" class="form-label">Operator</label>
                        <input type="text" name="operator" id="operator" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="shift" class="form-label">Shift</label>
                        <select name="shift" id="shift" class="form-control" required>
                            <option value="">Pilih Shift</option>
                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="thread_type" class="form-label">Jenis Benang</label>
                        <input type="text" name="thread_type" id="thread_type" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="covering_material" class="form-label">Material Covering</label>
                        <input type="text" name="covering_material" id="covering_material" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="production_speed" class="form-label">Kecepatan Produksi (RPM)</label>
                        <input type="number" name="production_speed" id="production_speed" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="quality_target" class="form-label">Target Kualitas (%)</label>
                        <input type="number" name="quality_target" id="quality_target" min="0" max="100" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="target_output" class="form-label">Target Output (kg)</label>
                        <input type="number" name="target_output" id="target_output" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label for="actual_output" class="form-label">Output Aktual (kg)</label>
                        <input type="number" name="actual_output" id="actual_output" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Pilih Status</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-info w-100">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Daftar Perencanaan Mesin Covering Benang</h5>
            <div>
                <button class="btn btn-outline-primary btn-sm" id="btnExport">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <button class="btn btn-outline-success btn-sm" id="btnPrint">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered text-xs font-bolder w-100" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Operator</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Shift</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis Benang</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Covering</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kecepatan (RPM)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Target Output</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Output Aktual</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        // Contoh data, pada implementasi sebenarnya ini akan diambil dari database
                        $plans = [
                            [
                                'id' => 1,
                                'machine' => 'Covering Machine 1',
                                'schedule_date' => '2024-03-10',
                                'operator' => 'Ahmad Sulaiman',
                                'shift' => 'Pagi',
                                'thread_type' => 'Polyester',
                                'covering_material' => 'Cotton',
                                'production_speed' => 1200,
                                'quality_target' => 98,
                                'target_output' => 1000,
                                'actual_output' => 950,
                                'status' => 'Completed',
                            ],
                            [
                                'id' => 2,
                                'machine' => 'Covering Machine 2',
                                'schedule_date' => '2024-03-11',
                                'operator' => 'Budi Santoso',
                                'shift' => 'Siang',
                                'thread_type' => 'Nylon',
                                'covering_material' => 'Polyester',
                                'production_speed' => 1100,
                                'quality_target' => 95,
                                'target_output' => 900,
                                'actual_output' => 850,
                                'status' => 'In Progress',
                            ],
                            [
                                'id' => 3,
                                'machine' => 'Covering Machine 3',
                                'schedule_date' => '2024-03-12',
                                'operator' => 'Citra Dewi',
                                'shift' => 'Malam',
                                'thread_type' => 'Cotton',
                                'covering_material' => 'Silk',
                                'production_speed' => 950,
                                'quality_target' => 99,
                                'target_output' => 800,
                                'actual_output' => 0,
                                'status' => 'Scheduled',
                            ],
                        ];
                        foreach ($plans as $plan) :
                            $statusBadge = '';
                            switch ($plan['status']) {
                                case 'Completed':
                                    $statusBadge = 'bg-success';
                                    break;
                                case 'In Progress':
                                    $statusBadge = 'bg-warning';
                                    break;
                                case 'Scheduled':
                                    $statusBadge = 'bg-info';
                                    break;
                                default:
                                    $statusBadge = '';
                            }
                        ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="text-center align-middle"><?= esc($plan['machine']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['schedule_date']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['operator']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['shift']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['thread_type']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['covering_material']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['production_speed']) ?></td>
                                <td class="text-center align-middle"><?= esc($plan['target_output']) ?> kg</td>
                                <td class="text-center align-middle"><?= esc($plan['actual_output']) ?> kg</td>
                                <td class="text-center align-middle"><span class="badge <?= $statusBadge ?>"><?= esc($plan['status']) ?></span></td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('memo/planning/edit/' . $plan['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $plan['id'] ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
        // Konfirmasi hapus dengan SweetAlert2
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data planning ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('memo/planning/delete/') ?>' + id;
                }
            });
        });

        // Export ke Excel
        $('#btnExport').on('click', function() {
            window.location.href = '<?= base_url('memo/planning/export') ?>';
        });

        // Print
        $('#btnPrint').on('click', function() {
            window.print();
        });
    });
</script>

<?php $this->endSection(); ?>