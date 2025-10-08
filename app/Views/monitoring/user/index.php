<?php $this->extend($role . '/user/header'); ?>
<?php $this->section('content'); ?>

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


    <div class="card card-frame mb-2">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Users</h5>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="ni ni-single-02 me-2"></i>Tambah User
                </button>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-items-center mb-0" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Username</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Role</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($dataUser as $user) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= $user['username'] ?></td>
                                <td class="text-center"><?= $user['role'] ?></td>
                                <td class="text-center"><?= $user['area'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $user['id_user'] ?>">Edit</button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $user['id_user'] ?>">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url($role . '/tambahUser') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="add_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="add_password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="add_role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="gbn">GBN</option>
                            <option value="celup">Celup</option>
                            <option value="covering">Covering</option>
                            <option value="area">Area</option>
                            <option value="kantordepan">Kantor Depan</option>
                            <option value="monitoring">Monitoring</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="area" class="form-label">Area</label>
                        <input type="text" class="form-control" id="add_area" name="area">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url($role . '/updateUser') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="id_user">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="gbn">GBN</option>
                            <option value="celup">Celup</option>
                            <option value="covering">Covering</option>
                            <option value="area">Area</option>
                            <option value="kantordepan">Kantor Depan</option>
                            <option value="monitoring">Monitoring</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="area" class="form-label">Area</label>
                        <input type="text" class="form-control" id="area" name="area" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        // Event listener tombol Update
        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');

            // Lakukan AJAX request untuk mendapatkan data
            $.ajax({
                url: '<?= base_url($role . '/getUserDetails') ?>/' + id,
                type: 'GET',
                success: function(response) {
                    // Isi data ke dalam form modal
                    $('#id_user').val(response.id_user);
                    $('#username').val(response.username);
                    $('#password').val(response.password);
                    $('#role').val(response.role);
                    $('#area').val(response.area);
                    // Show modal dialog
                    $('#editModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Event listener untuk submit form update


        // Event listener tombol Delete
        $('#dataTable').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            // Tampilkan konfirmasi dialog
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
                    // Redirect ke link hapus
                    window.location = '<?= base_url($role . '/deleteUser') ?>/' + id;
                }
            });
        });

    });
</script>
<?php $this->endSection(); ?>