<?php $this->extend($role . '/pocovering/header'); ?>
<?php $this->section('content'); ?>

<!-- DataTables CSS -->
<style>
    #poTable tbody tr {
        height: 30px;
    }

    #poTable tbody td {
        vertical-align: middle;
        line-height: 30px;
    }
</style>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?= session()->getFlashdata('success') ?>'
                });
            });
        </script>
    <?php elseif (session()->getFlashdata('error')) : ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '<?= session()->getFlashdata('error') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url("{$role}/schedule") ?>">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <h3 class="mb-0"><?= esc($title) ?></h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="poTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No Model</th>
                            <th>Item Type</th>
                            <th>Kode Warna</th>
                            <th>Warna</th>
                            <th>Kg PO</th>
                            <th>Status</th>
                            <th>Keterangan</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($trackingPoCovering)) : ?>
                            <?php foreach ($trackingPoCovering as $po) : ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($po['created_at'])) ?></td>
                                    <td><?= esc($po['no_model_anak']) ?></td>
                                    <td><?= esc($po['item_type']) ?></td>
                                    <td><?= esc($po['kode_warna']) ?></td>
                                    <td><?= esc($po['color']) ?></td>
                                    <td><?= esc($po['kg_po']) ?> Kg</td>
                                    <td><?= esc($po['status']) ?></td>
                                    <td><?= esc($po['keterangan']) ?></td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="editModal<?= $po['id_tpc'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $po['id_tpc'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?= $po['id_tpc'] ?>">Edit PO</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="<?= base_url("{$role}/po/updateListTrackingPo/{$po['id_tpc']}") ?>">
                                                    <input type="hidden" class="form-control" id="createdAt<?= $po['id_tpc'] ?>" name="created_at" value="<?= esc($po['created_at']) ?>" readonly>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="itemType<?= $po['id_tpc'] ?>" class="form-label">Item Type</label>
                                                            <input type="text" class="form-control" id="itemType<?= $po['id_tpc'] ?>" name="item_type" value="<?= esc($po['item_type']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kodeWarna<?= $po['id_tpc'] ?>" class="form-label">Kode Warna</label>
                                                            <input type="text" class="form-control" id="kodeWarna<?= $po['id_tpc'] ?>" name="kode_warna" value="<?= esc($po['kode_warna']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="color<?= $po['id_tpc'] ?>" class="form-label">Warna</label>
                                                            <input type="text" class="form-control" id="color<?= $po['id_tpc'] ?>" name="color" value="<?= esc($po['color']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kgPo<?= $po['id_tpc'] ?>" class="form-label">Kg PO</label>
                                                            <input type="number" class="form-control" id="kgPo<?= $po['id_tpc'] ?>" name="kg_po" value="<?= esc($po['kg_po']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="status<?= $po['id_tpc'] ?>" class="form-label">Status<sup class="text-danger">*</sup></label>
                                                            <input type="text" class="form-control" id="status<?= $po['id_tpc'] ?>" name="status" value="<?= esc($po['status']) ?>" required>
                                                            <label class="text-danger">*Status PO Wajib Diisi</label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="keterangan<?= $po['id_tpc'] ?>" class="form-label">Keterangan</label>
                                                            <input type="text" class="form-control" id="keterangan<?= $po['id_tpc'] ?>" name="keterangan" value="<?= esc($po['keterangan']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach ($dataBooking as $po) : ?>
                                <tr>
                                    <td><?= date('d-m-Y', strtotime($po['created_at'])) ?></td>
                                    <td><?= esc($po['no_model']) ?></td>
                                    <td><?= esc($po['item_type']) ?></td>
                                    <td><?= esc($po['kode_warna']) ?></td>
                                    <td><?= esc($po['color']) ?></td>
                                    <td><?= esc($po['kg_po']) ?> Kg</td>
                                    <td><?= esc($po['status']) ?></td>
                                    <td><?= esc($po['keterangan']) ?></td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="editModal<?= $po['id_tpc'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $po['id_tpc'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?= $po['id_tpc'] ?>">Edit PO</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="<?= base_url("{$role}/po/updateListTrackingPo/{$po['id_tpc']}") ?>">
                                                    <input type="hidden" class="form-control" id="createdAt<?= $po['id_tpc'] ?>" name="created_at" value="<?= esc($po['created_at']) ?>" readonly>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="itemType<?= $po['id_tpc'] ?>" class="form-label">Item Type</label>
                                                            <input type="text" class="form-control" id="itemType<?= $po['id_tpc'] ?>" name="item_type" value="<?= esc($po['item_type']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kodeWarna<?= $po['id_tpc'] ?>" class="form-label">Kode Warna</label>
                                                            <input type="text" class="form-control" id="kodeWarna<?= $po['id_tpc'] ?>" name="kode_warna" value="<?= esc($po['kode_warna']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="color<?= $po['id_tpc'] ?>" class="form-label">Warna</label>
                                                            <input type="text" class="form-control" id="color<?= $po['id_tpc'] ?>" name="color" value="<?= esc($po['color']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kgPo<?= $po['id_tpc'] ?>" class="form-label">Kg PO</label>
                                                            <input type="number" class="form-control" id="kgPo<?= $po['id_tpc'] ?>" name="kg_po" value="<?= esc($po['kg_po']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="status<?= $po['id_tpc'] ?>" class="form-label">Status<sup class="text-danger">*</sup></label>
                                                            <input type="text" class="form-control" id="status<?= $po['id_tpc'] ?>" name="status" value="<?= esc($po['status']) ?>" required>
                                                            <label class="text-danger">*Status PO Wajib Diisi</label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="keterangan<?= $po['id_tpc'] ?>" class="form-label">Keterangan</label>
                                                            <input type="text" class="form-control" id="keterangan<?= $po['id_tpc'] ?>" name="keterangan" value="<?= esc($po['keterangan']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- DataTables JS -->
<script>
    $(document).ready(function() {
        $('#poTable').DataTable({
            responsive: true,
            searching: true,
            order: [
                [0, 'asc']
            ],
        });
    });
</script>

<?php $this->endSection(); ?>