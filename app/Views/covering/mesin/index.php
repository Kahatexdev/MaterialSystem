<?php $this->extend($role . '/memo/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    /* Positive feedback */
    .feedback-success {
        color: green;
        font-weight: bold;
        animation: fadeIn 0.5s ease-in-out;
        font-size: 12px;
    }

    /* Negative feedback */
    .feedback-error {
        color: red;
        font-weight: bold;
        animation: fadeIn 0.5s ease-in-out;
        font-size: 12px;
    }
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

    <!-- Button Tambah -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Mesin Celup</h5>
                <button type="button" class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fas fa-plus me-2"></i>Tambah Mesin
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="row mt-4">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-striped table-hover table-bordered text-xs font-bolder" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Nama</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buatan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Merk</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah Spindle</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tahun</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah Unit</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mesinCovering as $data) : ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $data['no_mesin'] ?></td>
                                    <td class="text-center align-middle"><?= $data['nama'] ?></td>
                                    <td class="text-center align-middle"><?= $data['jenis'] ?></td>
                                    <td class="text-center align-middle"><?= $data['buatan'] ?></td>
                                    <td class="text-center align-middle"><?= $data['merk'] ?></td>
                                    <td class="text-center align-middle"><?= $data['type'] ?></td>
                                    <td class="text-center align-middle"><?= $data['jml_spindle'] ?></td>
                                    <td class="text-center align-middle"><?= $data['tahun'] ?></td>
                                    <td class="text-center align-middle"><?= $data['jml_unit'] ?></td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-warning btn-edit" data-id="<?= $data['id_mesin'] ?>">Update</button>
                                        <button class="btn btn-danger btn-delete" data-id="<?= $data['id_mesin'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($mesinCovering)) : ?>
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
    </div>

    <!-- Modal Tambah Data -->
    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Data Mesin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addForm" action="<?= base_url($role . '/mesin/addDataMesin') ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="no_mesin" class="form-label">No Mesin</label>
                            <input type="text" class="form-control" id="no_mesin" name="no_mesin" required>
                            <small id="no_mesin_feedback" class="form-text"></small>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <!-- Tambahkan input lainnya sesuai kebutuhan -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary add-btn">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Edit Data Mesin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateForm" action="<?= base_url($role . '/mesin/updateDataMesin') ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="id_mesin" name="id_mesin">
                        <div class="mb-3">
                            <label for="no_mesinE" class="form-label">No Mesin</label>
                            <input type="text" class="form-control" id="no_mesinE" name="no_mesin" required>
                        </div>
                        <div class="mb-3">
                            <label for="namaCov" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="namaCov" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <input type="text" class="form-control" id="jenis" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="buatan" class="form-label">Buatan</label>
                            <input type="text" class="form-control" id="buatan" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="merk" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="jmlSpindle" class="form-label">Jumlah Spindle</label>
                            <input type="text" class="form-control" id="jmlSpindle" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="tahun" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="jmlUnit" class="form-label">Jumlah Unit</label>
                            <input type="text" class="form-control" id="jmlUnit" name="nama" required>
                        </div>
                        <!-- Tambahkan input lainnya sesuai kebutuhan -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const baseUrl = '<?= base_url($role) ?>';

            // Initialize DataTable
            $('#dataTable').DataTable({
                "pageLength": 35,
                "order": []
            });

            // Edit Button Handler
            $('#dataTable').on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                $.ajax({
                    url: `${baseUrl}/mesinCov/getMesinCovDetails/${id}`,
                    type: 'GET',
                    success: function(response) {
                        $('#id_mesin').val(response.id_mesin);
                        $('#no_mesinE').val(response.no_mesin);
                        $('#namaCov').val(response.nama);
                        $('#jenis').val(response.jenis);
                        $('#buatan').val(response.buatan);
                        $('#merk').val(response.merk);
                        $('#type').val(response.type);
                        $('#jmlSpindle').val(response.jml_spindle);
                        $('#tahun').val(response.tahun);
                        $('#jmlUnit').val(response.jml_unit);
                        // Isi field lainnya sesuai response
                        $('#updateModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // Delete Button Handler
            $('#dataTable').on('click', '.btn-delete', function() {
                const id = $(this).data('id');

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
                        window.location = `${baseUrl}/deleteDataMesin/${id}`;
                    }
                });
            });

            // Validate No Mesin Input
            $('#no_mesin').on('input', function() {
                const noMesinInput = $(this).val();
                const noMesinFeedback = $('#no_mesin_feedback');
                const addButton = $('.add-btn');

                $.ajax({
                    url: `${baseUrl}/cekNoMesin`,
                    type: 'POST',
                    data: {
                        no_mesin: noMesinInput
                    },
                    success: function(response) {
                        if (response.error) {
                            noMesinFeedback.text('Nomor Mesin Bisa Digunakan').addClass('feedback-success').removeClass('feedback-error');
                            addButton.prop('disabled', false);
                        } else {
                            noMesinFeedback.text('Nomor Mesin Sudah Ada Di Database').addClass('feedback-error').removeClass('feedback-success');
                            addButton.prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>


    <?php $this->endSection(); ?>