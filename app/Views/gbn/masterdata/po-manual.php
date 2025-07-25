<?php $this->extend($role . '/masterdata/header'); ?>
<?php $this->section('content'); ?>
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#4a90e2'
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
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>

<div class="container-fluid py-4">
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">List PO Manual</h5>
                <a href="<?= base_url($role . '/masterdata/poManual/create') ?>" class="btn bg-gradient-info"> Buat PO Manual</a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="table-po" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Tanggal Po</th>
                            <th>No Model</th>
                            <th>Keterangan</th>
                            <th class="text-center">Penerima</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1 ?>
                        <?php foreach ($poManual as $data) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= date('Y-m-d', strtotime($data['created_at'])) ?></td>
                                <td><?= $data['no_model'] ?></td>
                                <td><?= $data['keterangan'] ?></td>
                                <td class="text-center"><?= $data['penerima'] ?></td>
                                <td>
                                    <!-- <a
                                        href="<?= base_url("$role/masterdata/poManual/exportPoManual?no_model=" . rawurlencode($data['no_model'])) ?>"
                                        class="btn btn-success">
                                        <i class="fas fa-file me-2"></i>Export
                                    </a> -->
                                    <a
                                        href="<?= base_url("$role/masterdata/poManual/detail?no_model=" . rawurlencode($data['no_model'])) ?>"
                                        class="btn btn-info">
                                        <i class="fas fa-eye me-2"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Pastikan jQuery load pertama -->

<script>
    $(document).ready(function() {
        $('#table-po').DataTable({
            "pageLength": 10,
        });
    });
</script>


<?php $this->endSection(); ?>