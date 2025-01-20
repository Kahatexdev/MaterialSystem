<?php $this->extend($role . '/dashboard/header'); ?>
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

    <!--  -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-header">

                    <h5 class="mb-0 font-weight-bolder">Data Material <?= $no_model ?></h5>
                </div>
                <div class="group">
                    <a href="<?= base_url($role . '/openPO/' . $orderData['id_order']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-file-import me-2"></i>Buka PO
                    </a>
                    <form action="<?= base_url($role . '/exportOpenPO/' . $orderData['no_model']) ?>" method="get" target="_blank">
                        <button type="submit" class="btn btn-outline-info">
                            <i class="fas fa-file-export me-2"></i>EXPORT PO
                        </button>
                    </form>
                </div>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Style Size</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Inisial</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Composition (%)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty(pcs)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal Edit Data Material -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm">
                        <input type="hidden" name="id_material" id="id_material">
                        <input type="hidden" name="id_order" id="id_order">
                        <div class="mb-3">
                            <label for="style_size" class="form-label">Style Size</label>
                            <input type="text" class="form-control" id="style_size" name="style_size" required>
                        </div>

                        <div class="mb-3">
                            <label for="area" class="form-label">Area</label>
                            <input type="text" class="form-control" id="area" name="area" required>
                        </div>

                        <div class="mb-3">
                            <label for="inisial" class="form-label">Inisial</label>
                            <input type="text" class="form-control" id="inisial" name="inisial" required>
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="color" name="color" required>
                        </div>

                        <div class="mb-3">
                            <label for="item_type" class="form-label">Item Type</label>
                            <input type="text" class="form-control" id="item_type" name="item_type" required>
                        </div>

                        <div class="mb-3">
                            <label for="kode_warna" class="form-label">Kode Warna</label>
                            <input type="text" class="form-control" id="kode_warna" name="kode_warna" required>
                        </div>

                        <div class="mb-3">
                            <label for="composition" class="form-label">Composition (%)</label>
                            <input type="text" class="form-control" id="composition" name="composition" required>
                        </div>

                        <div class="mb-3">
                            <label for="gw" class="form-label">GW</label>
                            <input type="text" class="form-control" id="gw" name="gw" required>
                        </div>

                        <div class="mb-3">
                            <label for="qty_pcs" class="form-label">Qty(pcs)</label>
                            <input type="text" class="form-control" id="qty_pcs" name="qty_pcs" required>
                        </div>

                        <div class="mb-3">
                            <label for="loss" class="form-label">Loss</label>
                            <input type="text" class="form-control" id="loss" name="loss" required>
                        </div>

                        <div class="mb-3">
                            <label for="kgs" class="form-label">Kgs</label>
                            <input type="text" class="form-control" id="kgs" name="kgs" required>
                        </div>
                        <!-- Button update dan batal di sebelah kanan -->
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
            $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "<?= base_url($role . '/tampilMaterial') ?>",
                    "type": "POST"
                },
                "columns": [{
                        "data": "no"
                    },
                    {
                        "data": "style_size"
                    },
                    {
                        "data": "area"
                    },
                    {
                        "data": "inisial"
                    },
                    {
                        "data": "color"
                    },
                    {
                        "data": "item_type"
                    },
                    {
                        "data": "kode_warna"
                    },
                    {
                        "data": "composition"
                    },
                    {
                        "data": "gw"
                    },
                    {
                        "data": "qty_pcs"
                    },
                    {
                        "data": "loss"
                    },
                    {
                        "data": "kgs"
                    },
                    {
                        "data": "admin"
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
                ]
            });
            // Event listener tombol Update
            $('#example').on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Lakukan AJAX request untuk mendapatkan data
                $.ajax({
                    url: '<?= base_url($role . '/getMaterialDetails') ?>/' + id,
                    type: 'GET',
                    success: function(response) {
                        // Isi data ke dalam form modal
                        $('#id_material').val(response.id_material);
                        $('#id_order').val(response.id_order);
                        $('#style_size').val(response.style_size);
                        $('#area').val(response.area);
                        $('#inisial').val(response.inisial);
                        $('#color').val(response.color);
                        $('#item_type').val(response.item_type);
                        $('#kode_warna').val(response.kode_warna);
                        $('#composition').val(response.composition);
                        $('#gw').val(response.gw);
                        $('#qty_pcs').val(response.qty_pcs);
                        $('#loss').val(response.loss);
                        $('#kgs').val(response.kgs);
                        // Show modal dialog
                        $('#updateModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // Event listener untuk submit form update
            $('#updateForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form reload

                const formData = $(this).serialize(); // Serialisasi data form

                // Lakukan AJAX request untuk update data
                $.ajax({
                    url: '<?= base_url($role . '/updateMaterial') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Hide modal dialog
                        $('#updateModal').modal('hide');

                        // Reload datatables
                        $('#example').DataTable().ajax.reload();

                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            html: 'Data berhasil diupdate'
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });

            // Event listener tombol Delete
            $('#example').on('click', '.btn-delete', function() {
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
                        // Lakukan AJAX request untuk delete data
                        $.ajax({
                            url: '<?= base_url($role . '/deleteMaterial') ?>/' + id,
                            type: 'GET',
                            success: function(response) {
                                // Reload datatables
                                $('#example').DataTable().ajax.reload();

                                // Tampilkan pesan sukses
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    html: 'Data berhasil dihapus'
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });

        });
    </script>
    <?php $this->endSection(); ?>