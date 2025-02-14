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

    <!--  -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-header">

                    <h5 class="mb-0 font-weight-bolder">Data Material <?= $no_model ?></h5>
                </div>
                <div class="group">
                    <button type="button" class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="fas fa-plus me-2"></i>Tambah Material
                    </button>
                    <a href="<?= base_url($role . '/openPO/' . $id_order) ?>" class="btn btn-outline-info">
                        <i class="fas fa-file-import me-2"></i>Buka PO
                    </a>
                    <button type="submit" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#ExportModal">
                        <i class="fas fa-file-export me-2"></i>EXPORT PO
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Style Size</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Inisial</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Composition (%)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">GW</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty(pcs)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderData as $data): ?>
                            <tr>
                                <td><?= $data['style_size'] ?></td>
                                <td><?= $data['area'] ?></td>
                                <td><?= $data['inisial'] ?></td>
                                <td><?= $data['item_type'] ?></td>
                                <td><?= $data['kode_warna'] ?></td>
                                <td><?= $data['color'] ?></td>
                                <td><?= $data['composition'] ?> %</td>
                                <td><?= $data['gw'] ?></td>
                                <td><?= $data['qty_pcs'] ?></td>
                                <td><?= $data['loss'] ?></td>
                                <td><?= $data['kgs'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $data['id_material'] ?>">
                                        Update
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $data['id_material'] ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($orderData)) : ?>
                <div class=" card-footer">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <p>No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/tambahMaterial') ?>" method="post" id="form-material">
                            <input type="hidden" name="id_order" value="<?= $id_order ?>">
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Style Size</label>
                                <input type="text" class="form-control" id="add_style_size" name="style_size" required>
                            </div>

                            <div class="mb-3">
                                <label for="area" class="form-label">Area</label>
                                <select class="form-control" name="area" id="add_area">
                                    <option value="">Pilih Area</option>
                                    <option value="KK1A">KK1A</option>
                                    <option value="KK1B">KK1B</option>
                                    <option value="KK2A">KK2A</option>
                                    <option value="KK2B">KK2B</option>
                                    <option value="KK2C">KK2C</option>
                                    <option value="KK5G">KK5G</option>
                                    <option value="KK7K">KK7K</option>
                                    <option value="KK7L">KK7L</option>
                                    <option value="KK8D">KK8D</option>
                                    <option value="KK8F">KK8F</option>
                                    <option value="KK8J">KK8J</option>
                                    <option value="KK9">KK9</option>
                                    <option value="KK10">KK10</option>
                                    <option value="KK11M">KK11M</option>
                                    <option value="Belum Ada Area">Belum Ada Area</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="inisial" class="form-label">Inisial</label>
                                <input type="text" class="form-control" id="add_inisial" name="inisial" required>
                            </div>

                            <div class="mb-3">
                                <label for="itemType">Item Type</label>
                                <select class="form-control" id="add_item_type" name="item_type">
                                    <option value="">Pilih Item Type</option>
                                    <?php foreach ($itemType as $item): ?>
                                        <option value="<?= $item['item_type'] ?>"><?= $item['item_type'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kode_warna" class="form-label">Kode Warna</label>
                                <input type="text" class="form-control" id="add_kode_warna" name="kode_warna" required>
                            </div>

                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="add_color" name="color" required>
                            </div>

                            <div class="mb-3">
                                <label for="composition" class="form-label">Composition (%)</label>
                                <input type="number" step="0.01" class="form-control" id="add_composition" name="composition" required>
                            </div>

                            <div class="mb-3">
                                <label for="gw" class="form-label">GW</label>
                                <input type="number" step="0.01" class="form-control" id="add_gw" name="gw" required>
                            </div>

                            <div class="mb-3">
                                <label for="qty_pcs" class="form-label">Qty(pcs)</label>
                                <input type="number" step="0.01" class="form-control" id="add_qty_pcs" name="qty_pcs" required>
                            </div>

                            <div class="mb-3">
                                <label for="qty_pcs" class="form-label">Loss</label>
                                <input type="number" step="0.01" class="form-control" id="add_loss" name="loss" required>
                            </div>

                            <div class="mb-3">
                                <label for="qty_pcs" class="form-label">Kgs</label>
                                <input type="number" step="0.01" class="form-control" id="add_kgs" name="kgs" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Tambah</button>
                            </div>
                        </form>
                    </div>
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
                        <form id="updateForm" action="<?= base_url($role . '/updateMaterial') ?>" method="post">
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
                                <input type="number" class="form-control" id="kgs" name="kgs" required>
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
        <!-- Modal Export PO -->
        <div class="modal fade" id="ExportModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?= base_url($role . '/exportOpenPO/' . $no_model) ?>" method="get" target="_blank">
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Tujuan</label>
                                <select class="form-control tujuan" name="tujuan" required>
                                    <option value="-">Pilih Tujuan</option>
                                    <option value="CELUP">CELUP</option>
                                    <option value="COVERING">COVERING</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="style_size" class="form-label">Jenis Bahan Baku</label>
                                <select class="form-control jenis" name="jenis" id="jenis" required>
                                    <option value="-">Pilih Jenis</option>
                                    <option value="BENANG">BENANG</option>
                                    <option value="NYLON">NYLON</option>
                                    <option value="SPANDEX">SPANDEX & KARET</option>
                                </select>
                                <input type="hidden" class="form-control" id="jenis2" name="jenis2">
                            </div>
                            <!-- Button update dan batal di sebelah kanan -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-info">Generate</button>
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
            $('#tambahModal').on('shown.bs.modal', function() {
                $('#add_item_type').select2({
                    dropdownParent: $('#tambahModal'),
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


            // Event listener tombol Delete
            $('#dataTable').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const idOrder = <?= $id_order ?>;
                console.log(idOrder, id);
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
                        window.location = '<?= base_url($role . '/deleteMaterial') ?>/' + id + '/' + idOrder;
                    }
                });
            });

        });

        // Ambil elemen select dan input Karet
        const jenisSelect = document.getElementById('jenis');
        const jenis2Input = document.getElementById('jenis2');

        // Tambahkan event listener untuk perubahan pada elemen select
        jenisSelect.addEventListener('change', function() {
            if (jenisSelect.value === 'SPANDEX') {
                jenis2Input.value = 'KARET'; // Jika SPANDEX, isi dengan 'KARET'
            } else {
                jenis2Input.value = ''; // Jika bukan SPANDEX, kosongkan input
            }
        });
    </script>

    <?php $this->endSection(); ?>