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

<?php if (!empty($detail) && is_array($detail)) : ?>
    <div class="container-fluid py-4">
        <div class="card card-frame">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bolder">Material PO <?= $detail[0]['no_model'] ?></h5>
                    <a
                        href="<?= base_url("$role/masterdata/poManual/exportPoManual?no_model=" . rawurlencode($detail[0]['no_model'])) ?>"
                        class="btn btn-success" id="exportPo">
                        <i class="fa-solid fa-file-pdf me-2"></i>Export
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
                                            data-no_model="<?= htmlspecialchars($data['no_model']) ?>"
                                            data-item_type="<?= htmlspecialchars($data['item_type']) ?>"
                                            data-kode_warna="<?= htmlspecialchars($data['kode_warna']) ?>"
                                            data-color="<?= htmlspecialchars($data['color']) ?>"
                                            data-buyer="<?= htmlspecialchars($data['buyer']) ?>"
                                            data-kg_po="<?= htmlspecialchars($data['kg_po']) ?>"
                                            data-ket_celup="<?= htmlspecialchars($data['ket_celup']) ?>"
                                            data-spesifikasi_benang="<?= htmlspecialchars(isset($data['spesifikasi_benang']) ? $data['spesifikasi_benang'] : '') ?>"
                                            data-keterangan="<?= htmlspecialchars(isset($data['keterangan']) ? $data['keterangan'] : '') ?>"
                                            data-bentuk_celup="<?= htmlspecialchars(isset($data['bentuk_celup']) ? $data['bentuk_celup'] : '') ?>"
                                            data-kg_percones="<?= htmlspecialchars(isset($data['kg_percones']) ? $data['kg_percones'] : '') ?>"
                                            data-jumlah_cones="<?= htmlspecialchars(isset($data['jumlah_cones']) ? $data['jumlah_cones'] : '') ?>"
                                            data-jenis_produksi="<?= htmlspecialchars(isset($data['jenis_produksi']) ? $data['jenis_produksi'] : '') ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- Tombol Hapus -->
                                        <button class="btn btn-danger btn-delete"
                                            data-id="<?= $data['id_po'] ?>"
                                            data-no_model="<?= htmlspecialchars($data['no_model']) ?>">
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

    <!-- Modal Export PO Manual -->
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
                            <input type="date" class="form-control" id="delivery" name="delivery" value="<?= $del ?>">
                        </div>
                        <!-- <div class="mb-3">
                            <label for="no_order" class="form-label">No Order</label>
                            <input type="text" class="form-control" id="no_order" name="no_order">
                        </div> -->
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
            <form id="formEditPO" method="post" action="<?= base_url("$role/masterdata/poManual/updatePoManual") ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditPOLabel">Edit PO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_po" id="edit-id_po">
                        <input type="hidden" name="no_model" id="edit-no_model">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-buyer" class="form-label">Buyer</label>
                                    <input type="text" class="form-control" id="edit-buyer" name="buyer" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-item_type" class="form-label">Item Type</label>
                                    <input type="text" class="form-control" id="edit-item_type" name="item_type" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-kode_warna" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="edit-kode_warna" name="kode_warna" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-color" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="edit-color" name="color" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-kg_po" class="form-label">Kg Kebutuhan</label>
                                    <input type="number" step="0.01" class="form-control" id="edit-kg_po" name="kg_po" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-spesifikasi_benang" class="form-label">Spesifikasi Benang</label>
                                    <input type="text" class="form-control" id="edit-spesifikasi_benang" name="spesifikasi_benang" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-bentuk_celup" class="form-label">Bentuk Celup</label>
                                    <input type="text" class="form-control" id="edit-bentuk_celup" name="bentuk_celup">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-kg_percones" class="form-label">Permintan Kelos (Kg Cones)</label>
                                    <input type="text" class="form-control" id="edit-kg_percones" name="kg_percones">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-jumlah_cones" class="form-label">Permintan Kelos (Total Cones)</label>
                                    <input type="text" class="form-control" id="edit-jumlah_cones" name="jumlah_cones">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-jenis_produksi" class="form-label">Untuk Produksi</label>
                                    <input type="text" class="form-control" id="edit-jenis_produksi" name="jenis_produksi">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-keterangan" class="form-label">Keterangan</label>
                                    <textarea type="text" class="form-control" id="edit-keterangan" name="keterangan"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-ket_celup" class="form-label">Keterangan Celup</label>
                                    <textarea type="text" class="form-control" id="edit-ket_celup" name="ket_celup"></textarea>
                                </div>
                            </div>
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
            <form id="formDeletePO" method="post" action="<?= base_url("$role/masterdata/poManual/deletePoManual") ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDeletePOLabel">Hapus PO</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_po" id="delete-id_po">
                        <input type="hidden" name="no_model" id="delete-no_model">
                        <p>Apakah Anda yakin ingin menghapus PO <span id="delete-no_model"></span>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else : ?>
    <div class="container-fluid py-4 d-flex justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="card-body text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No Data" style="width: 80px; opacity: 0.7;" class="mb-3">
            <h5 class="card-title mb-2 text-secondary">Data PO Tidak Ditemukan</h5>
            <p class="card-text text-muted mb-0">Belum ada data PO yang tersedia untuk ditampilkan.<br>Silakan cek kembali atau input data PO baru.</p>
        </div>
    </div>
<?php endif; ?>
<script>
    $(document).ready(function() {
        // Edit button click
        $('.btn-edit').on('click', function() {
            $('#edit-id_po').val($(this).data('id'));
            $('#edit-no_model').val($(this).data('no_model'));
            $('#edit-item_type').val($(this).data('item_type'));
            $('#edit-kode_warna').val($(this).data('kode_warna'));
            $('#edit-color').val($(this).data('color'));
            $('#edit-buyer').val($(this).data('buyer'));
            $('#edit-kg_po').val($(this).data('kg_po'));
            $('#edit-ket_celup').val($(this).data('ket_celup'));
            $('#edit-spesifikasi_benang').val($(this).data('spesifikasi_benang') || '');
            $('#edit-keterangan').val($(this).data('keterangan') || '');
            $('#edit-bentuk_celup').val($(this).data('bentuk_celup') || '');
            $('#edit-kg_percones').val($(this).data('kg_percones') || '');
            $('#edit-jumlah_cones').val($(this).data('jumlah_cones') || '');
            $('#edit-jenis_produksi').val($(this).data('jenis_produksi') || '');
            $('#modalEditPO').modal('show');
        });

        // Delete button click
        $('.btn-delete').on('click', function() {
            $('#delete-id_po').val($(this).data('id'));
            $('#delete-no_model').val($(this).data('no_model'));
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
            var no_order = $('#no_order').val();
            let baseUrl = $(this).attr('action');

            baseUrl += (baseUrl.indexOf('?') === -1 ? '?' : '&') +
                'delivery=' + encodeURIComponent(date) +
                '&material_type=' + encodeURIComponent(material_type) +
                '&no_order=' + encodeURIComponent(no_order);


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