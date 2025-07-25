<?php $this->extend($role . '/masterdata/header'); ?>
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
    <div class="modal fade" id="modalPoNylon" tabindex="-1" aria-labelledby="modalPoNylonLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPoNylonLabel">Export PO Nylon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <!-- Konten form/modal kamu di sini -->
                    <form action="<?= base_url($role . '/exportPoNylon') ?>" method="post">
                        <div class="mb-3">
                            <label for="nomorPo" class="form-label">Tujuan</label>

                            <select class="form-control tujuan" name="tujuan" required>
                                <option value="">Pilih Tujuan</option>
                                <option value="CELUP">CELUP</option>
                                <option value="COVERING">COVERING</option>
                            </select>
                            <label for="nomorPo" class="form-label">Tanggal Buka PO</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            <label for="season" class="form-label">Season</label>
                            <input type="text" class="form-control" id="season" name="season">
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
                        <!-- Tambah field lain kalau perlu -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    <!-- Modal untuk Revise MU -->
    <div class="modal fade" id="reviseModal" tabindex="-1" aria-labelledby="reviseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviseModalLabel">Revise MU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="revise/mu" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- Input File -->
                        <div class="mb-3">
                            <label for="reviseFile" class="form-label">Pilih File Revisi MU</label>
                            <input type="file" class="form-control" id="reviseFile" name="file" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Hanya file dengan format .xlsx, .xls, atau .csv yang didukung.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">Upload Revisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h5 class="mb-0 font-weight-bolder">Data Order</h5>
                </div>
                <div class="col-6 text-end">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-2"></i>Import MU
                    </button>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#reviseModal">
                        <i class="fas fa-sync-alt me-2"></i>Revise MU
                    </button>
                </div>
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
                            <th>Foll Up</th>
                            <th>LCO Date</th>
                            <th>No Model</th>
                            <th>No Order</th>
                            <th>Buyer</th>
                            <th>Memo</th>
                            <th>Delivery Awal</th>
                            <th>Delivery Akhir</th>
                            <th>Unit</th>
                            <th>Tanggal Buka PO</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTables AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Kalau butuh info tambahan -->
            <div id="no-data-message" class="text-center mt-4 d-none">
                <p>No data available in the table.</p>
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
                    <form id="updateForm" action="<?= base_url($role . '/updateOrder') ?>" method="post">
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
                            <input type="date" class="form-control" name="delivery_awal" id="delivery_awal" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_akhir" class="form-label">Delivery Akhir</label>
                            <input type="date" class="form-control" name="delivery_akhir" id="delivery_akhir" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" name="unit" id="unit" required readonly>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 10,
            "ajax": {
                "url": "<?= base_url($role . '/getMasterData') ?>",
                "type": "POST"
            },
            "columns": [{
                    "data": "foll_up"
                },
                {
                    "data": "lco_date"
                },
                {
                    "data": "no_model"
                },
                {
                    "data": "no_order"
                },
                {
                    "data": "buyer"
                },
                {
                    "data": "memo"
                },
                {
                    "data": "delivery_awal"
                },
                {
                    "data": "delivery_akhir"
                },
                {
                    "data": "unit"
                },
                {
                    "data": "tanggal_po"
                },
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "language": {
                "emptyTable": "Data belum tersedia untuk saat ini."
            }
        });


        // Event listener untuk submit form update

        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            console.log(id);

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
                    $('#unit').val(response.unit);

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