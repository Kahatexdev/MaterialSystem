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

    <!-- Modal untuk Upload File Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Upload File Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="import/mu" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Input File -->
                        <div class="mb-3">
                            <label for="excelFile" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Hanya file dengan format .xlsx, .xls, atau .csv yang didukung.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Order</h5>
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import me-2"></i>Import MU
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Foll Up</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LCO Date</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Memo</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Awal</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery Akhir</th>
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
                        <input type="hidden" name="id_order" id="id_order">
                        <div class="mb-3">
                            <label for="foll_up" class="form-label">Follow Up</label>
                            <input type="text" class="form-control" name="foll_up" id="foll_up" required>
                        </div>
                        <div class="mb-3">
                            <label for="lco_date" class="form-label">LCO Date</label>
                            <input type="date" class="form-control" name="lco_date" id="lco_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_model" class="form-label">No Model</label>
                            <input type="text" class="form-control" name="no_model" id="no_model" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_order" class="form-label">No Order</label>
                            <input type="text" class="form-control" name="no_order" id="no_order" required>
                        </div>
                        <div class="mb-3">
                            <label for="buyer" class="form-label">Buyer</label>
                            <input type="text" class="form-control" name="buyer" id="buyer" required>
                        </div>
                        <div class="mb-3">
                            <label for="memo" class="form-label">Memo</label>
                            <input type="text" class="form-control" name="memo" id="memo" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_awal" class="form-label">Delivery Awal</label>
                            <input type="date" class="form-control" name="delivery_awal" id="delivery_awal" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_akhir" class="form-label">Delivery Akhir</label>
                            <input type="date" class="form-control" name="delivery_akhir" id="delivery_akhir" required>
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
                    url: '<?= base_url($role . '/tampilMasterOrder') ?>',
                    type: 'POST'
                },
                "columns": [{
                        "data": "no",
                        "orderable": false
                    },
                    {
                        "data": "foll_up",
                    },
                    {
                        "data": "lco_date",

                    },
                    {
                        "data": "no_model",

                    },
                    {
                        "data": "no_order",
                    },
                    {
                        "data": "buyer",
                    },
                    {
                        "data": "memo",
                    },
                    {
                        "data": "delivery_awal",
                    },
                    {
                        "data": "delivery_akhir",
                    },
                    {
                        "data": "admin",
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
                ] // Urutkan berdasarkan kolom foll_up
            });
            // Event listener untuk tombol Detail Master Order ke halaman Material berdasarkan id_order
            $('#example').on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                window.location.href = '<?= base_url($role . '/material' . '/') ?>' + id;

            });

            // Event listener tombol Update
            $('#example').on('click', '.btn-edit', function() {
                const id = $(this).data('id');

                // Lakukan AJAX request untuk mendapatkan data
                $.ajax({
                    url: '<?= base_url($role . '/getOrderDetails') ?>/' + id,
                    type: 'GET',
                    success: function(response) {
                        // Isi data ke dalam form modal
                        $('#id_order').val(response.id_order);
                        $('#foll_up').val(response.foll_up);
                        $('#lco_date').val(response.lco_date);
                        $('#no_model').val(response.no_model);
                        $('#no_order').val(response.no_order);
                        $('#buyer').val(response.buyer);
                        $('#memo').val(response.memo);
                        $('#delivery_awal').val(response.delivery_awal);
                        $('#delivery_akhir').val(response.delivery_akhir);

                        // Tambahkan data lain sesuai kebutuhan

                        // Tampilkan modal
                        $('#updateModal').modal('show');
                    },
                    error: function() {
                        alert('Gagal memuat data.');
                    }
                });
            });

            // Event listener untuk submit form update
            $('#updateForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah form reload

                const formData = $(this).serialize(); // Serialisasi data form

                // Lakukan AJAX request untuk update data
                $.ajax({
                    url: '<?= base_url($role . '/updateOrder') ?>',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Tampilkan pesan sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data berhasil diupdate.',
                        });
                        $('#updateModal').modal('hide'); // Sembunyikan modal
                        table.ajax.reload(); // Reload tabel
                    },
                    error: function() {
                        alert('Gagal mengupdate data.');
                    }
                });
            });

            // // Event listener untuk tombol delete
            // $('#example').on('click', '.btn-delete', function() {
            //     const id = $(this).data('id');
            //     // Tampilkan konfirmasi
            //     Swal.fire({
            //         title: 'Apakah Anda yakin?',
            //         text: "Data yang dihapus tidak dapat dikembalikan!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Ya, hapus!',
            //         cancelButtonText: 'Batal'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             // Kirim request ke server
            //             $.ajax({
            //                 url: '<?= base_url($role . '/deleteOrder') ?>',
            //                 type: 'POST',
            //                 data: {
            //                     id: id
            //                 },
            //                 success: function(response) {
            //                     // Tampilkan pesan sukses
            //                     Swal.fire({
            //                         icon: 'success',
            //                         title: 'Berhasil!',
            //                         text: response,
            //                     });
            //                     // Refresh tabel
            //                     $('#example').DataTable().ajax.reload();
            //                 },
            //                 error: function(xhr, status, error) {
            //                     // Tampilkan pesan error
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Error!',
            //                         text: xhr.responseText,
            //                     });
            //                 }
            //             });
            //         }
            //     });
            // });
        });
    </script>

    <?php $this->endSection(); ?>