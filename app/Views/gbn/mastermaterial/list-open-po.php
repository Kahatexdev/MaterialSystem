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
                    <h4 class="mb-0 font-weight-bolder">List Buka PO <?= $no_model ?></h5>
                </div>
                <div class="group">
                    <!-- <a href="<?= base_url($role . '/exportOpenPO/' . $no_model . '?tujuan=' . $tujuan . '&jenis=' . $jenis . '&jenis2=' . $jenis2) ?>"
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
                        data-base-url="<?= base_url("$role/exportOpenPO/$no_model") ?>">
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Kebutuhan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Order</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Delivery</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($openPo as $data): ?>
                            <tr>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= $data['kg_po'] ?></td>
                                <td><?= $data['buyer'] ?></td>
                                <td><?= $data['no_order'] ?></td>
                                <td><?= $data['delivery_awal'] ?></td>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Data PO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="updateForm" action="<?= base_url($role . '/updatePo') ?>" method="post">
                    <input type="hidden" name="id_po" id="id_po">

                    <div class="mb-3">
                        <label for="itemType">Item Type</label>
                        <select id="add_item_type" name="item_type" required>
                            <option value=""><?= $openPo['item_type'] ?? 'Pilih Item Type' ?></option>

                            <?php foreach ($itemType as $item): ?>
                                <option value="<?= $item['item_type'] ?>"><?= $item['item_type'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="kode_warna" class="form-label">Kode Warna</label>
                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" required>
                    </div>

                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="color" name="color" required>
                    </div>

                    <div class="mb-3">
                        <label for="kg_po" class="form-label">Kg Kebutuhan</label>
                        <input type="text" class="form-control" id="kg_po" name="kg_po" required>
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
                <h5 class="modal-title" id="exportModalLabel">Export Data PO</h5>
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
                width: '100%'
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

            // Lakukan AJAX request untuk mendapatkan data
            $.ajax({
                url: '<?= base_url($role . '/getPoDetails') ?>/' + id,
                type: 'GET',
                success: function(response) {
                    // Isi data ke dalam form modal
                    $('#id_po').val(response.id_po);
                    $('#add_item_type').val(response.item_type);
                    $('#kode_warna').val(response.kode_warna);
                    $('#color').val(response.color);
                    $('#kg_po').val(response.kg_po);
                    // Show modal dialog
                    $('#updateModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#dataTable').on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url($role . '/deletePo') ?>/' + id,
                        type: 'DELETE',
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Refresh halaman
                                });
                            } else {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: response.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error!",
                                text: "Terjadi kesalahan saat menghapus data.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
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


<!-- <script>
    document.getElementById('btnSubmitExport')
        .addEventListener('click', function() {
            // 1) Ambil URL penuh saat ini (path + query)
            let currentUrl = window.location.href.split('#')[0];
            // (buang fragment jika ada)

            // 2) Pisahkan base + existing search
            let [base, search = ''] = currentUrl.split('?');
            let params = new URLSearchParams(search);

            // 3) Ambil nilai modal
            const season = document.getElementById('season').value.trim();
            const materialType = document.getElementById('material_type').value;

            // 4) Jika terisi, tambahkan ke params
            if (season) params.set('season', season);
            if (materialType) params.set('material_type', materialType);

            // 5) Rekonstuksi URL baru
            let newUrl = base +
                (params.toString() ? '?' + params.toString() : '');

            // 6) Buka di tab baru
            window.open(newUrl, '_blank');
        });
</script> -->


<?php $this->endSection(); ?>