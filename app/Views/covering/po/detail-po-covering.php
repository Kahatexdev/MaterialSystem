<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

<style>
    .alert-section {
        margin-bottom: 30px;
    }

    .alert-glass {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px 0 rgba(180, 184, 235, 0.37);
        text-align: center;
    }

    .alert-glass .icon-bg {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(147, 197, 253, 0.3) 100%);
        border-radius: 50%;
        margin-bottom: 16px;
        backdrop-filter: blur(10px);
    }

    .alert-glass i {
        color: #3b82f6;
        font-size: 24px;
    }

    .alert-glass h4 {
        margin: 0 0 8px 0;
        color: #1e293b;
        font-size: 18px;
        font-weight: 600;
    }

    .alert-glass p {
        margin: 0;
        color: #64748b;
        font-size: 15px;
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
    <?php if (!empty($getData)) : ?>
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
                            <button type="button" class="btn bg-gradient-danger" id="deleteSelected">
                                <i class="fas fa-trash"></i> Hapus Item Terpilih
                            </button>
                        </div>
                        <div class="col-md-6 text-md-end">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="poTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <label for="">Pilih Semua</label><br>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="text-center">No PO</th>
                                <th class="text-center">Item Type</th>
                                <th class="text-center">Kode Warna</th>
                                <th class="text-center">Warna</th>
                                <th class="text-center">Kg PO</th>
                                <th class="text-center">Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($getData)) : ?>
                                <?php foreach ($getData as $po) : ?>
                                    <tr>
                                        <td class="text-center"><input type="checkbox" class="select-item" value="<?= $po['id_po'] ?>"></td>
                                        <td class="text-center"><?= esc($po['no_model']) ?></td>
                                        <td class="text-center"><?= esc($po['item_type']) ?></td>
                                        <td class="text-center"><?= esc($po['kode_warna']) ?></td>
                                        <td class="text-center"><?= esc($po['color']) ?></td>
                                        <td class="text-center"><?= esc($po['total_kg_po']) ?> Kg</td>
                                        <td class="text-center"><?= esc($po['keterangan']) ?></td>
                                        <td>
                                            <button type="button" class="btn bg-gradient-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $po['id_po'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn bg-gradient-danger delete-button" data-id="<?= $po['id_po'] ?>">
                                                <i class="fas fa-trash"></i>
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
                                                    <form method="post" action="<?= base_url("{$role}/po/updateDetailPoCovering/{$po['id_po']}") ?>">
                                                        <input type="hidden" class="form-control" id="createdAt<?= $po['id_po'] ?>" name="created_at" value="<?= esc($po['created_at']) ?>" readonly>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="itemType<?= $po['id_po'] ?>" class="form-label">Item Type</label>
                                                                <input type="text" class="form-control" id="itemType<?= $po['id_po'] ?>" name="item_type" value="<?= esc($po['item_type']) ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="kodeWarna<?= $po['id_po'] ?>" class="form-label">Kode Warna</label>
                                                                <input type="text" class="form-control" id="kodeWarna<?= $po['id_po'] ?>" name="kode_warna" value="<?= esc($po['kode_warna']) ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="color<?= $po['id_po'] ?>" class="form-label">Warna</label>
                                                                <input type="text" class="form-control" id="color<?= $po['id_po'] ?>" name="color" value="<?= esc($po['color']) ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="kgPo<?= $po['id_po'] ?>" class="form-label">Kg PO</label>
                                                                <input type="number" step="0.01" class="form-control" id="kgPo<?= $po['id_po'] ?>" name="kg_po" value="<?= esc($po['total_kg_po']) ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <label for="keterangan<?= $po['id_po'] ?>" class="form-label">Keterangan</label>
                                                                <input type="text" class="form-control" id="keterangan<?= $po['id_po'] ?>" name="keterangan" value="<?= esc($po['keterangan']) ?>">
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
    <?php else : ?>
        <div class="alert-section">
            <div class="alert-glass">
                <div class="icon-bg">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4>Tidak ada data tersedia</h4>
                <p>Silahkan Buat Kembali Po Baru</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const idPo = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Hapus Data',
                    text: 'Data akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "<?= base_url("{$role}/po/deleteDetailPoCovering/") ?>" + idPo;
                    }
                });
            });
        });

        // Select All
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select-item');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Hapus multiple
        document.getElementById('deleteSelected').addEventListener('click', function() {
            const selected = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);

            if (selected.length === 0) {
                Swal.fire('Tidak ada data terpilih!', '', 'info');
                return;
            }

            Swal.fire({
                title: 'Hapus Data Terpilih',
                text: `Yakin hapus ${selected.length} data?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect dengan query string (atau bisa ajax POST)
                    window.location.href = "<?= base_url("{$role}/po/deleteMultipleDetailPoCovering") ?>?ids=" + selected.join(',');
                }
            });
        });
    });
</script>

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