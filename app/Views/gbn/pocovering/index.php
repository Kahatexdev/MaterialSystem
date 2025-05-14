<?php $this->extend($role . '/pocovering/header'); ?>
<?php $this->section('content'); ?>

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
                    <h3 class="mb-0 text-center text-md-start">List PO Covering</h3>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th class="sticky text-center">No</th>
                            <th class="sticky text-center">Tanggal PO</th>
                            <th class="sticky text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($getData) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($getData as $po) : ?>
                                <tr>
                                    <td class="sticky text-center"><?= $no++ ?></td>
                                    <td class="sticky text-center"><?= $po['tgl_po'] ?></td>
                                    <td class="sticky text-center">
                                        <a class="btn bg-gradient-success" href="<?= base_url($role . '/po/exportPO/' . $po['tgl_po']) ?>">
                                            <i class="bi bi-file-earmark-arrow-down me-2"></i>Export PO
                                        </a>
                                        <a class="btn bg-gradient-warning" href="<?= base_url($role . '/po/listTrackingPo/' . $po['tgl_po']) ?>">
                                            <i class="far fa-question-circle"></i> Status PO
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
        });
    });
</script>

<?php $this->endSection(); ?>