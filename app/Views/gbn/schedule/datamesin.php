<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
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

    <!-- Button Tambah -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Order</h5>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fas fa-plus me-2"></i>Tambah Mesin
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Min Capacity</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Max Capacity</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah LOT</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mesinCelup as $data) : ?>
                            <tr>
                                <td><?= $data['no_mesin'] ?></td>
                                <td><?= $data['min_caps'] ?></td>
                                <td><?= $data['max_caps'] ?></td>
                                <td><?= $data['jml_lot'] ?></td>
                                <td><?= $data['lmd'] ?></td>
                                <td><?= $data['ket_mesin'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-edit" data-id="<?= $data['id_mesin'] ?>">Update</button>
                                    <button class="btn btn-danger btn-delete" data-id="<?= $data['id_mesin'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($mesinCelup)) : ?>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/mesin/saveDataMesin') ?>" method="post">
                        <div class="mb-3">
                            <label for="no_mesin" class="form-label">No Mesin</label>
                            <input type="number" class="form-control" name="no_mesin" id="no_mesin" required>
                        </div>
                        <div class="mb-3">
                            <label for="min_caps" class="form-label">Min Capacity</label>
                            <input type="number" class="form-control" name="min_caps" id="min_caps" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_caps" class="form-label">Max Capacity</label>
                            <input type="number" class="form-control" name="max_caps" id="max_caps" required>
                        </div>
                        <div class="mb-3">
                            <label for="jml_lot" class="form-label">Jumlah LOT</label>
                            <input type="number" class="form-control" name="jml_lot" id="jml_lot" required>
                        </div>
                        <div class="mb-3">
                            <label for="lmd" class="form-label">LMD</label>
                            <input type="text" class="form-control" name="lmd" id="lmd" required>
                        </div>
                        <div class="mb-3">
                            <label for="ket_mesin" class="form-label">Keterangan Mesin</label>
                            <input type="text" class="form-control" name="ket_mesin" id="ket_mesin" required>
                        </div>
                        <!-- Action Button -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/mesin/updateDataMesin') ?>" method="post">
                        <input type="hidden" name="id_mesin" id="id_mesin">
                        <div class="mb-3">
                            <label for="no_mesin" class="form-label">No Mesin</label>
                            <input type="text" class="form-control" name="no_mesin" id="no_mesinE" required>
                        </div>
                        <div class="mb-3">
                            <label for="min_caps" class="form-label">Min Capacity</label>
                            <input type="text" class="form-control" name="min_caps" id="min_capsE" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_caps" class="form-label">Max Capacity</label>
                            <input type="text" class="form-control" name="max_caps" id="max_capsE" required>
                        </div>
                        <div class="mb-3">
                            <label for="jml_lot" class="form-label">Jumlah LOT</label>
                            <input type="text" class="form-control" name="jml_lot" id="jml_lotE" required>
                        </div>
                        <div class="mb-3">
                            <label for="lmd" class="form-label">LMD</label>
                            <input type="text" class="form-control" name="lmd" id="lmdE" required>
                        </div>
                        <div class="mb-3">
                            <label for="ket_mesin" class="form-label">Keterangan Mesin</label>
                            <input type="text" class="form-control" name="ket_mesin" id="ket_mesinE" required>
                        </div>
                        <!-- Action Button -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 35,
                "order": []
            });
            $(document).ready(function() {

                // Event listener untuk submit form update
                $('#dataTable').on('click', '.btn-edit', function() {
                    const id = $(this).data('id');

                    // Lakukan AJAX request untuk mendapatkan data
                    $.ajax({
                        url: '<?= base_url($role . '/mesin/getMesinDetails') ?>/' + id,
                        type: 'GET',
                        success: function(response) {

                            // Isi data ke dalam form modal
                            $('#id_mesin').val(response.id_mesin);
                            $('#no_mesinE').val(response.no_mesin);
                            $('#min_capsE').val(response.min_caps);
                            $('#max_capsE').val(response.max_caps);
                            $('#jml_lotE').val(response.jml_lot);
                            $('#lmdE').val(response.lmd);
                            $('#ket_mesinE').val(response.ket_mesin);
                            // Show modal dialog
                            $('#updateModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });

                // Event listener untuk tombol delete
                $('#dataTable').on('click', '.btn-delete', function() {
                    const id = $(this).data('id');

                    // Tampilkan konfirmasi
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim request ke server
                            window.location = '<?= base_url($role . '/mesin/deleteDataMesin') ?>/' + id;

                        }
                    });
                });
            });
        });
    </script>

    <?php $this->endSection(); ?>