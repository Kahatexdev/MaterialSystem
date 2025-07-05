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
                <h5 class="mb-0 font-weight-bolder">Material PO <?= $detail[0]['no_model'] ?></h5>
                <a
                    href="<?= base_url("$role/masterdata/poBooking/exportPoBooking?no_model=" . rawurlencode($detail[0]['no_model'])) ?>"
                    class="btn btn-success" id="exportPo">
                    <i class="fas fa-file me-2"></i>Export
                </a>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="table-po" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item Type</th>
                            <th>Kode Warna</th>
                            <th>Warna</th>
                            <th>Buyer</th>
                            <th>Kg Kebutuhan</th>
                            <th>Keterangan Celup</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1 ?>
                        <?php foreach ($detail as $data) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= $data['buyer'] ?></td>
                                <td><?= $data['kg_po'] ?></td>
                                <td><?= $data['ket_celup'] ?></td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-warning btn-edit"
                                        data-id="<?= $data['id_po'] ?>"
                                        data-item_type="<?= htmlspecialchars($data['item_type']) ?>"
                                        data-kode_warna="<?= htmlspecialchars($data['kode_warna']) ?>"
                                        data-color="<?= htmlspecialchars($data['color']) ?>"
                                        data-buyer="<?= htmlspecialchars($data['buyer']) ?>"
                                        data-kg_po="<?= htmlspecialchars($data['kg_po']) ?>"
                                        data-ket_celup="<?= htmlspecialchars($data['ket_celup']) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-danger btn-delete"
                                        data-id="<?= $data['id_po'] ?>"
                                        data-item_type="<?= htmlspecialchars($data['item_type']) ?>">
                                        <i class="fas fa-trash"></i>
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

<!-- Modal Export PO Booking -->
<div class="modal fade" id="modalExportPO" tabindex="-1" aria-labelledby="modalExportPOLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formExportPO" method="get">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExportPOLabel">Export PO <?= $data['no_model'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="delivery" class="form-label">Delivery</label>
                        <input type="date" class="form-control" id="delivery" name="delivery">
                    </div>
                    <div class="mb-3">
                        <label for="jenis" class="form-label">Material Type</label>
                        <select name="material_type" id="material_type" class="form-control">
                            <option value="">Pilih Material Type</option>
                            <option value="OCS BLENDED">OCS BLENDED</option>
                            <option value="GOTS">GOTS</option>
                            <option value="RCS BLENDED POST-CONSUMER">RCS BLENDED POST-CONSUMER</option>
                            <option value="BCI">BCI</option>
                            <option value="BCI-7">BCI-7</option>
                            <option value="BCI, ALOEVERA">BCI, ALOEVERA</option>
                            <option value="OCS BLENDED, ALOEVERA">OCS BLENDED, ALOEVERA</option>
                            <option value="GRS BLENDED POST-CONSUMER">GRS BLENDED POST-CONSUMER</option>
                            <option value="ORGANIC IC2">ORGANIC IC2</option>
                            <option value="RCS BLENDED PRE-CONSUMER">RCS BLENDED PRE-CONSUMER</option>
                            <option value="GRS BLENDED PRE-CONSUMER">GRS BLENDED PRE-CONSUMER</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Modal Edit PO -->
<div class="modal fade" id="modalEditPO" tabindex="-1" aria-labelledby="modalEditPOLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditPO" method="post" action="<?= base_url("$role/masterdata/poBooking/updatePoBooking") ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPOLabel">Edit PO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_po" id="edit-id_po">
                    <div class="mb-3">
                        <label for="edit-item_type" class="form-label">Item Type</label>
                        <input type="text" class="form-control" id="edit-item_type" name="item_type" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kode_warna" class="form-label">Kode Warna</label>
                        <input type="text" class="form-control" id="edit-kode_warna" name="kode_warna" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-color" class="form-label">Warna</label>
                        <input type="text" class="form-control" id="edit-color" name="color" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-buyer" class="form-label">Buyer</label>
                        <input type="text" class="form-control" id="edit-buyer" name="buyer" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit-kg_po" class="form-label">Kg Kebutuhan</label>
                        <input type="number" step="0.01" class="form-control" id="edit-kg_po" name="kg_po" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-ket_celup" class="form-label">Keterangan Celup</label>
                        <input type="text" class="form-control" id="edit-ket_celup" name="ket_celup">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus PO -->
<div class="modal fade" id="modalDeletePO" tabindex="-1" aria-labelledby="modalDeletePOLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formDeletePO" method="post" action="<?= base_url("$role/masterdata/poBooking/deletePoBooking") ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDeletePOLabel">Hapus PO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_po" id="delete-id_po">
                    <p>Apakah Anda yakin ingin menghapus PO <span id="delete-item_type"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit button click
        $('.btn-edit').on('click', function() {
            $('#edit-id_po').val($(this).data('id'));
            $('#edit-item_type').val($(this).data('item_type'));
            $('#edit-kode_warna').val($(this).data('kode_warna'));
            $('#edit-color').val($(this).data('color'));
            $('#edit-buyer').val($(this).data('buyer'));
            $('#edit-kg_po').val($(this).data('kg_po'));
            $('#edit-ket_celup').val($(this).data('ket_celup'));
            $('#modalEditPO').modal('show');
        });

        // Delete button click
        $('.btn-delete').on('click', function() {
            $('#delete-id_po').val($(this).data('id'));
            $('#delete-item_type').text($(this).data('item_type'));
            $('#modalDeletePO').modal('show');
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Tangkap klik tombol export
        $('#exportPo').on('click', function(e) {
            e.preventDefault();
            var exportUrl = $(this).attr('href');
            $('#formExportPO').attr('action', exportUrl);
            $('#modalExportPO').modal('show');
        });

        // Submit form export
        $('#formExportPO').on('submit', function(e) {
            e.preventDefault();
            var action = $(this).attr('action');
            var date = $('#delivery').val();
            var material_type = $('#material_type').val();
            let baseUrl = $(this).attr('action');

            if (date || material_type) {
                // Append delivery and material params, handle if URL already has query params
                baseUrl += (baseUrl.indexOf('?') === -1 ? '?' : '&') + 'delivery=' + encodeURIComponent(date) + '&material_type=' + encodeURIComponent(material_type);
            }

            window.open(baseUrl, '_blank');
        });
    });
</script>

<!-- Pastikan jQuery load pertama -->

<script>
    $(document).ready(function() {
        $('#table-po').DataTable({
            "pageLength": 10,
        });
    });
</script>


<?php $this->endSection(); ?>