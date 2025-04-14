<?php $this->extend($role . '/retur/header'); ?>
<?php $this->section('content'); ?>

<style>
    .btn-filter {
        background: linear-gradient(135deg, #1e88e5, #64b5f6);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
    }

    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
    }

    .action-buttons .btn {
        margin-right: 5px;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

    <!-- Content utama -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><?= $title ?></h4>
                        <a href="<?= base_url('retur/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Data
                        </a>
                    </div>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success mx-3 mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger mx-3 mt-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Filter Data</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url('retur') ?>" method="get">
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="no_model" class="form-label">No Model</label>
                                                    <input type="text" class="form-control" id="no_model" name="no_model" value="<?= $filters['no_model'] ?? '' ?>">
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="item_type" class="form-label">Item Type</label>
                                                    <select class="form-select" id="item_type" name="item_type">
                                                        
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="area_retur" class="form-label">Area Retur</label>
                                                    <select class="form-select" id="area_retur" name="area_retur">
                                                        
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label for="tgl_retur" class="form-label">Tanggal Retur</label>
                                                    <input type="date" class="form-control" id="tgl_retur" name="tgl_retur" value="<?= $filters['tgl_retur'] ?? '' ?>">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-filter me-2">
                                                    <i class="fas fa-filter me-2"></i>Filter
                                                </button>
                                                <a href="<?= base_url('retur') ?>" class="btn btn-outline-secondary">
                                                    <i class="fas fa-redo me-2"></i>Reset
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Section -->
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>No Model</th>
                                        <th>Item Type</th>
                                        <th>Kode Warna</th>
                                        <th>Warna</th>
                                        <th>Area Retur</th>
                                        <th>Tgl Retur</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($retur)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-4">Tidak ada data yang ditemukan</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($retur as $row): ?>
                                            <tr>
                                                <td><?= $row['id_retur'] ?></td>
                                                <td><?= $row['no_model'] ?></td>
                                                <td><?= $row['item_type'] ?></td>
                                                <td><?= $row['kode_warna'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 20px; height: 20px; background-color: <?= $row['kode_warna'] ?>; border-radius: 4px; margin-right: 8px;"></div>
                                                        <?= $row['warna'] ?>
                                                    </div>
                                                </td>
                                                <td><?= $row['area_retur'] ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tgl_retur'])) ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = '';
                                                    switch ($row['status'] ?? 'pending') {
                                                        case 'approved':
                                                            $statusClass = 'bg-success';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'bg-danger';
                                                            break;
                                                        case 'processing':
                                                            $statusClass = 'bg-warning';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-info';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $statusClass ?> badge-status">
                                                        <?= ucfirst($row['status'] ?? 'Pending') ?>
                                                    </span>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="<?= base_url('retur/view/' . $row['id_retur']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('retur/edit/' . $row['id_retur']) ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['id_retur'] ?>" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <a href="<?= base_url('retur/print/' . $row['id_retur']) ?>" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Cetak">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (isset($pager)): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?= $pager->links() ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle delete button clicks
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = '<?= base_url('retur/delete/') ?>' + id;

                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
    });
</script>

<?php $this->endSection(); ?>