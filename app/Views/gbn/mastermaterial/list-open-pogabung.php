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
                                <td><?= $data['ket_celup'] ?></td>
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
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditGabungan">
            <input type="hidden" name="id_po" id="edit_id_po">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit POGABUNGAN + Anak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- ===== Parent Fields ===== -->
                    <h6>Data Induk No Model</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label for="edit_item_type" class="form-label">Item Type</label>
                            <input type="text" class="form-control" name="item_type" id="edit_item_type">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_kode_warna" class="form-label">Kode Warna</label>
                            <input type="text" class="form-control" name="kode_warna" id="edit_kode_warna">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_color" class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" id="edit_color">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_color" class="form-label">Kg Stock</label>
                            <input type="text" class="form-control" name="kg_stock" id="edit_kg_stock">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_kg_percones" class="form-label">Permintaan Kelos (Kg)</label>
                            <input type="number" step="0.01" class="form-control" name="kg_percones" id="edit_kg_percones">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_jumlah_cones" class="form-label">Permintaan Kelos (Total Cones)</label>
                            <input type="number" class="form-control" name="jumlah_cones" id="edit_jumlah_cones">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_bentuk_celup" class="form-label">Bentuk Celup</label>
                            <select class="form-select" name="bentuk_celup" id="edit_bentuk_celup">
                                <option value="Cones">Cones</option>
                                <option value="Hank">Hank</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jenis_produksi" class="form-label">Jenis Produksi</label>
                            <input type="text" class="form-control" name="jenis_produksi" id="edit_jenis_produksi">
                        </div>
                    </div>

                    <!-- ===== Children Fields ===== -->
                    <h6>Data Anak No Model</h6>
                    <div id="childrenContainer">
                        <!-- akan diisi via JS -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Semua</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Export Data PO -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data PO Gabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="exportForm" action="#" method="get" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportStartDate" class="form-label">Filter Tanggal (Awal)</label>
                        <input
                            type="date"
                            class="form-control"
                            id="exportStartDate"
                            name="start_date"
                            placeholder="Filter Tanggal Awal"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="exportEndDate" class="form-label">Filter Tanggal (Akhir)</label>
                        <input
                            type="date"
                            class="form-control"
                            id="exportEndDate"
                            name="end_date"
                            placeholder="Filter Tanggal Akhir"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="season" class="form-label">Season</label>
                        <input
                            type="text"
                            class="form-control"
                            id="season"
                            name="season"
                            placeholder="Masukkan Season">
                    </div>
                    <div class="mb-3">
                        <label for="material_type" class="form-label">Material Type</label>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button
                        type="button"
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

<script>
    $(document).ready(function() {
        $('.btn-edit').on('click', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= base_url("$role/getPoGabungan") ?>/' + id,
                dataType: 'json',
                success: function(res) {
                    console.log(res);
                    // --- Parent ---
                    $('#edit_id_po').val(res.parent.id_po);
                    $('#edit_item_type').val(res.parent.item_type);
                    $('#edit_kode_warna').val(res.parent.kode_warna);
                    $('#edit_color').val(res.parent.color);
                    $('#edit_bentuk_celup').val(res.parent.bentuk_celup);
                    $('#edit_kg_percones').val(res.parent.kg_percones);
                    $('#edit_jumlah_cones').val(res.parent.jumlah_cones);
                    $('#edit_jenis_produksi').val(res.parent.jenis_produksi);

                    // --- Children ---
                    const $cont = $('#childrenContainer').empty();
                    res.children.forEach(child => {
                        const field = `
            <div class="row g-2 mb-2 align-items-end child-row">
              <input type="hidden" name="children[${child.id_po}][id_po]" value="${child.id_po}">
              <div class="col-md-4">
                <label class="form-label">No Model</label>
                <input type="text" class="form-control" value="${child.no_model}" disabled>
              </div>
              <div class="col-md-4">
                <label class="form-label">KG Kebutuhan</label>
                <input type="number" step="0.01" class="form-control"
                       name="children[${child.id_po}][kg_po]" value="${child.kg_po}">
              </div>
            </div>`;
                        $cont.append(field);
                    });

                    $('#editModal').modal('show');
                }
            });
        });

        // Submit semua perubahan
        $('#formEditGabungan').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("$role/updatePoGabungan") ?>',
                method: 'post',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp.status === 'ok') {
                        // Tampilkan SweetAlert sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data POGABUNGAN berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#editModal').modal('hide');
                            // reload atau refresh datatable
                            location.reload();
                        });
                    } else {
                        // SweetAlert error jika ada kendala
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: resp.message || 'Terjadi kesalahan saat memperbarui data.',
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat terhubung ke server.',
                    });
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        // Handler tombol delete
        $('.btn-delete').on('click', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Yakin ingin dihapus?',
                text: "Data PO GABUNGAN akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika user confirm, panggil AJAX delete
                    $.ajax({
                        url: '<?= base_url("$role/deletePoGabungan") ?>/' + id,
                        method: 'post',
                        data: {
                            id_po: id
                        },
                        success: function(res) {
                            if (res.status === 'ok') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Data berhasil dihapus.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    // refresh halaman atau reload DataTable
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Terjadi kesalahan saat menghapus.',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat terhubung ke server.',
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
            const startDate = document.getElementById('exportStartDate').value;
            const endDate = document.getElementById('exportEndDate').value;

            // 4) Tambahkan kalau user mengisi
            if (season) params.set('season', season);
            if (materialType) params.set('material_type', materialType);
            if (startDate) params.set('start_date', startDate);
            if (endDate) params.set('end_date', endDate);

            // 5) Bentuk URL akhir & buka di tab baru
            const finalUrl = base + '?' + params.toString();
            window.open(finalUrl, '_blank');
        });
</script>

<?php $this->endSection(); ?>