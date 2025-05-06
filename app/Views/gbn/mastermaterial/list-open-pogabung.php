<?php $this->extend($role . '/mastermaterial/header'); ?>
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

    <!--  -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-header">
                    <h4 class="mb-0 font-weight-bolder">List Buka PO Gabungan</h5>
                </div>
                <div class="group">
                    <!-- <a href="<?= base_url($role . '/exportOpenPOGabung?tujuan=' . $tujuan . '&jenis=' . $jenis . '&jenis2=' . $jenis2) ?>"
                        class="btn btn-outline-danger" target="_blank">
                        <i class="ni ni-single-copy-04 me-2"></i>Export PO
                    </a> -->
                    <!-- <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="ni ni-single-copy-04 me-2"></i>Export PO
                    </button> -->
                    <button
                        class="btn btn-outline-info"
                        id="btnOpenModal"
                        data-bs-toggle="modal"
                        data-bs-target="#exportModal"
                        data-base-url="<?= base_url("$role/exportOpenPOGabung") ?>">
                        <i class="ni ni-single-copy-04 me-2"></i>Export PO
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-uppercase text-xs font-bolder text-center" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Kebutuhan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($openPoGabung as $data): ?>
                            <tr>
                                <td><?= $data['no_model'] ?></td>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= $data['kg_po'] ?></td>
                                <td><?= $data['buyer'] ?></td>
                                <td><?= $data['no_order'] ?></td>
                                <td><?= $data['delivery_awal'] ?></td>
                                <td><?= $data['keterangan'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $data['id_po'] ?>">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $data['id_po'] ?>">
                                        <i class="fas fa-trash text-lg"></i>
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

<!-- Modal Edit Data Material -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Data PO Gabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" action="<?= base_url($role . '/updatePo') ?>" method="post">
                    <div id="detailsPoGabung">
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

<!-- Modal Export Data PO -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data PO Gabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body ">
                <form id="exportForm" action="#" method="get" target="_blank">
                    <div class="mb-3">
                        <label for="tujuan" class="form-label">Season</label>
                        <input type="text" class="form-control" id="season" name="season">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button"
                            class="btn btn-info"
                            id="btnSubmitExport">
                            Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('#updateModal').on('shown.bs.modal', function() {
            $('#add_item_type').select2({
                dropdownParent: $('#updateModal'),
            });
        });
    });

    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 35,
            "order": []
        });
        // Event listener tombol Update
        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const masterOrderList = <?= json_encode($masterOrder); ?>;
            const poGabung = <?= json_encode($openPoGabung); ?>;

            // Lakukan AJAX request untuk mendapatkan data
            $.ajax({
                url: '<?= base_url($role . '/getPoDetailsGabungan') ?>/' + id,
                type: 'GET',
                success: function(response) {
                    console.log(response); // Debug respons API

                    // Tangani dataInduk utama
                    let dataInduk = response.dataInduk;
                    let dataDetails = `
                        <input type="hidden" class="form-control" id="id_po_gabungan" name="id_po_gabungan" value="${dataInduk.id_po}" readonly>
                    `;

                    // Tangani daftar item terkait
                    if (response.details && Array.isArray(response.details)) {
                        response.details.forEach(function(item, index) {
                            console.log(item);
                            dataDetails += `
                                <div class="row">
                                    <input type="hidden" class="form-control" id="id_po" name="id_po[]" value="${item.id_po}" readonly>
                                    <div class="col-md-4">
                                        <label for="itemType">No Model</label>
                                        <select class="form-control" id="no_model" name="no_model[]" required>
                                            <option value="">Pilih No Model</option>
                                            ${masterOrderList.map(data => `
                                                <option value="${data.no_model}" ${data.no_model === item.no_model ? 'selected' : ''}>
                                                    ${data.no_model}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="itemType">Jenis Item</label>
                                        <select class="form-control" id="item_type" name="item_type[]" required>
                                            <option value="">Pilih Jenis Item</option>
                                            ${item.item_type_list.map(type => `
                                                <option value="${type.item_type}" ${type.item_type === dataInduk.item_type ? 'selected' : ''}>
                                                    ${type.item_type}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="itemType">Kode Warna</label>
                                        <select class="form-control" id="kode_warna" name="kode_warna[]" required>
                                            <option value="">Pilih Jenis Item</option>
                                            ${item.kode_warna_list.map(kode => `
                                                <option value="${kode.kode_warna}" ${kode.kode_warna === dataInduk.kode_warna ? 'selected' : ''}>
                                                    ${kode.kode_warna}
                                                </option>
                                            `).join('')}
                                        </select>
                                    </div>
                                </div>
                            `;
                        });
                    }

                    // Render hasil ke dalam DOM
                    $('#detailsPoGabung').html(dataDetails);
                    // Tampilkan modal setelah data dimuat
                    $('#updateModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(`Error: ${error}, Status: ${status}`);
                    console.error(`Response Text: ${xhr.responseText}`);
                }
            });
        });

        // $('#dataTable').on('click', '.btn-delete', function() {
        //     let id = $(this).data('id');

        //     Swal.fire({
        //         title: "Apakah Anda yakin?",
        //         text: "Data yang dihapus tidak dapat dikembalikan!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#d33",
        //         cancelButtonColor: "#3085d6",
        //         confirmButtonText: "Ya, Hapus!",
        //         cancelButtonText: "Batal"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: '<?= base_url($role . '/deletePo') ?>/' + id,
        //                 type: 'DELETE',
        //                 success: function(response) {
        //                     if (response.status === 'success') {
        //                         Swal.fire({
        //                             title: "Deleted!",
        //                             text: response.message,
        //                             icon: "success",
        //                             timer: 1500,
        //                             showConfirmButton: false
        //                         }).then(() => {
        //                             location.reload(); // Refresh halaman
        //                         });
        //                     } else {
        //                         Swal.fire({
        //                             title: "Gagal!",
        //                             text: response.message,
        //                             icon: "error"
        //                         });
        //                     }
        //                 },
        //                 error: function() {
        //                     Swal.fire({
        //                         title: "Error!",
        //                         text: "Terjadi kesalahan saat menghapus data.",
        //                         icon: "error"
        //                     });
        //                 }
        //             });
        //         }
        //     });
        // });
    });
</script>
<script>
    document
        .getElementById('btnSubmitExport')
        .addEventListener('click', function() {
            // 1) Ambil URL dasar dari tombol trigger
            const base = document
                .getElementById('btnOpenModal')
                .getAttribute('data-base-url');

            // 2) Buat URLSearchParams dengan default params
            const params = new URLSearchParams({
                tujuan: "<?= $tujuan ?>",
                jenis: "<?= $jenis ?>",
                jenis2: "<?= $jenis2 ?>"
            });

            // 3) Ambil nilai modal
            const season = document.getElementById('season').value.trim();
            const materialType = document.getElementById('material_type').value;

            // 4) Tambahkan kalau user mengisi
            if (season) params.set('season', season);
            if (materialType) params.set('material_type', materialType);

            // 5) Bentuk URL akhir & buka di tab baru
            const finalUrl = base + '?' + params.toString();
            window.open(finalUrl, '_blank');
        });
</script>

<?php $this->endSection(); ?>