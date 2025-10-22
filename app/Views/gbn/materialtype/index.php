<?php $this->extend($role . '/materialtype/header'); ?>
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

    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Master Material</h5>
                <!-- tambah data dengan modal -->
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus me-2"></i>Tambah Data
                </button>
            </div>
        </div>
    </div>


    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Created At</th>
                            <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ukuran</th> -->
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
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
                    <form id="updateForm">
                        <div class="mb-3">
                            <label for="update_material_type" class="form-label">Material Type</label>
                            <input type="text" class="form-control" name="material_type" id="update_material_type" required>
                            <input type="hidden" name="material_type_old" id="update_material_type_old">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Add DATA -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url($role . '/saveMasterMaterialType') ?>" method="post" id="addForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="material_type" class="form-label">Material Type</label>
                            <input type="text" class="form-control" name="material_type" id="add_material_type" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url($role . '/tampilMasterMaterialType') ?>',
                    type: 'POST'
                },
                columns: [{
                        data: 'no',
                        orderable: false
                    },
                    {
                        data: 'material_type'
                    },
                    {
                        data: 'admin'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // ========== EDIT ==========
            $('#example').on('click', '.btn-edit', function() {
                const materialType =
                    $(this).attr('data-material_type') ??
                    $(this).data('material_type') ??
                    $(this).attr('data-material-type') ??
                    $(this).data('material-type');

                if (!materialType) {
                    console.warn('data-material_type tidak ada di tombol');
                    return;
                }

                $.ajax({
                    url: '<?= base_url($role . "/getMasterMaterialTypeDetails") ?>',
                    type: 'GET',
                    data: {
                        materialType: materialType
                    },
                    success: function(response) {
                        // target ke ID di modal UPDATE (bukan yang di modal ADD)
                        $('#update_material_type').val(response.material_type);
                        $('#update_material_type_old').val(response.material_type);
                        $('#updateModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log('AJAX Error:', xhr.responseText);
                    }
                });
            });

            // submit UPDATE
            $('#updateForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: '<?= base_url($role . '/updateMasterMaterialType') ?>', // <- samakan dengan controller
                    type: 'POST',
                    data: formData,
                    success: function() {
                        Swal.fire('Berhasil!', 'Data berhasil diupdate.', 'success').then(() => {
                            $('#updateModal').modal('hide');
                            table.ajax.reload(null, false);
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengupdate data.', 'error');
                    }
                });
            });

            // ========== ADD ==========
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: '<?= base_url($role . '/saveMasterMaterialType') ?>', // <- samakan dengan controller
                    type: 'POST',
                    data: formData,
                    success: function() {
                        Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success').then(() => {
                            $('#addModal').modal('hide');
                            table.ajax.reload(null, false);
                            // reset form
                            $('#add_material_type').val('');
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menambahkan data.', 'error');
                    }
                });
            });

            // ========== DELETE ==========
            $('#example').on('click', '.btn-delete', function() {
                const materialType =
                    $(this).attr('data-material_type') ??
                    $(this).data('material_type') ??
                    $(this).attr('data-material-type') ??
                    $(this).data('material-type');

                if (!materialType) {
                    console.warn('data-material_type tidak ada di tombol');
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url($role . '/deleteMasterMaterialType') ?>', // <- samakan dengan controller
                            type: 'GET',
                            data: {
                                materialType: materialType
                            },
                            success: function() {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success').then(() => {
                                    table.ajax.reload(null, false);
                                });
                            },
                            error: function() {
                                Swal.fire('Error!', 'Gagal menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <?php $this->endSection(); ?>