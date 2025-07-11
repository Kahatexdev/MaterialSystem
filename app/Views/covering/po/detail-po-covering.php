<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

<!-- DataTables CSS -->

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
                        <a class="btn bg-gradient-success" href="<?= base_url($role . '/po/exportPO/' . $getData[0]['created_at']) ?>">
                            <i class="fas fa-file-excel me-2"></i>Export PO
                        </a>
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
                            <th>No</th>
                            <th>Item Type</th>
                            <th>Kode Warna</th>
                            <th>Warna</th>
                            <th>Kg PO</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($getData)) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($getData as $po) : ?>
                                <tr>
                                    <td><?= esc($no++) ?></td>
                                    <td><?= esc($po['item_type']) ?></td>
                                    <td><?= esc($po['kode_warna']) ?></td>
                                    <td><?= esc($po['color']) ?></td>
                                    <td><?= esc($po['total_kg_po']) ?> Kg</td>
                                    <td><?= esc($po['keterangan']) ?></td>
                                    <td>
                                        <button type="button" class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#editModal<?= $po['id_po'] ?>">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                <!-- Modal -->
                                <div class="modal fade" id="editModal<?= $po['id_po'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $po['id_po'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?= $po['id_po'] ?>">Edit PO</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" action="<?= base_url("{$role}/po/updateListTrackingPo/{$po['id_po']}") ?>">
                                                    <input type="hidden" class="form-control" id="createdAt<?= $po['id_po'] ?>" name="created_at" value="<?= esc($po['created_at']) ?>" readonly>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="itemType<?= $po['id_po'] ?>" class="form-label">Item Type</label>
                                                            <input type="text" class="form-control" id="itemType<?= $po['id_po'] ?>" name="item_type" value="<?= esc($po['item_type']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kodeWarna<?= $po['id_po'] ?>" class="form-label">Kode Warna</label>
                                                            <input type="text" class="form-control" id="kodeWarna<?= $po['id_po'] ?>" name="kode_warna" value="<?= esc($po['kode_warna']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="color<?= $po['id_po'] ?>" class="form-label">Warna</label>
                                                            <input type="text" class="form-control" id="color<?= $po['id_po'] ?>" name="color" value="<?= esc($po['color']) ?>" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="kgPo<?= $po['id_po'] ?>" class="form-label">Kg PO</label>
                                                            <input type="number" class="form-control" id="kgPo<?= $po['id_po'] ?>" name="kg_po" value="<?= esc($po['total_kg_po']) ?>" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="keterangan<?= $po['id_po'] ?>" class="form-label">Keterangan</label>
                                                            <input type="text" class="form-control" id="keterangan<?= $po['id_po'] ?>" name="keterangan" value="<?= esc($po['keterangan']) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <button type="submit" class="btn bg-gradient-info w-100">Update</button>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
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