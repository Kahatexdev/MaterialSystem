<?php $this->extend($role . '/mastermaterial/header'); ?>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Deskripsi</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Ukuran</th>
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
                            <label for="item_type" class="form-label">Item Type</label>
                            <input type="text" class="form-control" name="item_type" id="item_type" required>
                            <input type="hidden" name="item_type_old" id="item_type_old">
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select class="form-select" name="jenis" id="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="BENANG">BENANG</option>
                                <option value="NYLON">NYLON</option>
                                <option value="SPANDEX">SPANDEX</option>
                                <option value="KARET">KARET</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" name="ukuran" id="ukuran" required>
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
                <form action="<?= base_url($role . '/saveMasterMaterial') ?>" method="post" id="addForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="item_type" class="form-label">Item Type</label>
                            <input type="text" class="form-control" name="item_type" id="item_type" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select class="form-select" name="jenis" id="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="BENANG">BENANG</option>
                                <option value="NYLON">NYLON</option>
                                <option value="SPANDEX">SPANDEX</option>
                                <option value="KARET">KARET</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" name="ukuran" id="ukuran" required>
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
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: '<?= base_url($role . '/tampilMasterMaterial') ?>',
                    type: 'POST'
                },
                "columns": [{
                        "data": "no",
                        "orderable": false
                    },
                    {
                        "data": "item_type"
                    },
                    {
                        "data": "deskripsi"
                    },
                    {
                        "data": "jenis"
                    },
                    {
                        "data": "ukuran"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    }
                ],
                "order": [
                    [1, "asc"]
                ] // Urutkan berdasarkan kolom ke-1 (paling kiri)
            });

            // Event listener untuk tombol Update
            $('#example').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                console.log('ID yang dikirim (encoded): ', id);

                $.ajax({
                    url: '<?= base_url($role . "/getMasterMaterialDetails") ?>',
                    type: 'GET',
                    data: {
                        id: id,
                    },
                    success: function(response) {
                        console.log('Response dari server: ', response);
                        // Isi form dengan data yang diterima
                        $('#item_type').val(response.item_type);
                        $('#item_type_old').val(response.item_type);
                        $('#deskripsi').val(response.deskripsi);
                        $('#jenis').val(response.jenis);
                        $('#ukuran').val(response.ukuran);

                        // Tampilkan modal
                        $('#updateModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error:', xhr.responseText);
                    }
                });
            });


            // Submit form update
            $('#updateForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?= base_url($role . '/updateMasterMaterial') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Data berhasil diupdate.', 'success').then(() => {
                            $('#updateModal').modal('hide');
                            $('#example').DataTable().ajax.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal mengupdate data.', 'error');
                    }
                });
            });

            // submit form tambah
            $('#addForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: '<?= base_url($role . '/saveMasterMaterial') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Data berhasil ditambahkan.', 'success').then(() => {
                            $('#addModal').modal('hide');
                            $('#example').DataTable().ajax.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menambahkan data.', 'error');
                    }
                });
            });

            // Event listener untuk tombol Delete
            $('#example').on('click', '.btn-delete', function() {
                const id = $(this).data('id'); // Ambil data-id dari tombol delete

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
                        // Lakukan AJAX request untuk menghapus data
                        $.ajax({
                            url: '<?= base_url($role . '/deleteMasterMaterial') ?>',
                            type: 'GET',
                            data: {
                                id: id
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success').then(() => {
                                    $('#example').DataTable().ajax.reload();
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