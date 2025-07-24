<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    /* Positive feedback */
    .feedback-success {
        color: green;
        font-weight: bold;
        animation: fadeIn 0.5s ease-in-out;
        font-size: 12px;
    }

    /* Negative feedback */
    .feedback-error {
        color: red;
        font-weight: bold;
        animation: fadeIn 0.5s ease-in-out;
        font-size: 12px;
    }
</style>
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

    <!-- Button Tambah -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Mesin Celup</h5>
                <button type="button" class="btn btn-outline-info mb-0" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="fas fa-plus me-2"></i>Tambah Mesin
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="row mt-4">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table table-striped table-hover table-bordered text-xs font-bolder" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Mesin</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Min Capacity</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Max Capacity</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jumlah LOT</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LMD</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Mesin</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mesinCelup as $data) : ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $data['no_mesin'] ?></td>
                                    <td class="text-center align-middle"><?= $data['min_caps'] ?></td>
                                    <td class="text-center align-middle"><?= $data['max_caps'] ?></td>
                                    <td class="text-center align-middle"><?= $data['jml_lot'] ?></td>
                                    <td class="text-center align-middle"><?= $data['lmd'] ?></td>
                                    <td class="text-center align-middle"><?= $data['ket_mesin'] ?></td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-warning btn-edit" data-id="<?= $data['id_mesin'] ?>" data-bs-target="#updateModal" data-bs-toggle="modal">Update</button>
                                        <button class="btn btn-danger btn-delete" data-id="<?= $data['id_mesin'] ?>">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($mesinCelup)) : ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <p>No data available in the table.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url($role . '/mesin/saveDataMesin') ?>" method="post" id="form-mesin">
                        <div class="mb-3">
                            <label for="no_mesin" class="form-label">No Mesin</label>
                            <input type="number" class="form-control" name="no_mesin" id="no_mesin" required>
                            <div id="no_mesin_feedback" class=""></div>
                        </div>
                        <div class="mb-3">
                            <label for="min_caps" class="form-label">Min Capacity</label>
                            <input type="number" class="form-control" name="min_caps" id="min_caps" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_caps" class="form-label">Max Capacity</label>
                            <input type="number" class="form-control" name="max_caps" id="max_caps" required>
                        </div>
                        <div class="mb-3">
                            <label for="jml_lot" class="form-label">Jumlah LOT</label>
                            <input type="number" class="form-control" name="jml_lot" id="jml_lot" required>
                        </div>
                        <div class="mb-3">
                            <label for="lmd" class="form-label">LMD</label>
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="lmd[]" value="L">
                                        <label class="form-check-label" for="L">L</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="lmd[]" value="M">
                                        <label class="form-check-label" for="M">M</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="lmd[]" value="D">
                                        <label class="form-check-label" for="D">D</label>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="lmd[]" value="WHITE">
                                        <label class="form-check-label" for="WHITE">WHITE</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="lmd[]" value="BLACK">
                                        <label class="form-check-label" for="BLACK">BLACK</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ket_mesin" class="form-label">Keterangan Mesin</label>
                            <!-- <input type="text" class="form-control" name="ket_mesin" id="ket_mesin" required> -->
                            <select class="form-select" name="ket_mesin" id="ket_mesin" required>
                                <option value="">Pilih Keterangan Mesin</option>
                                <option value="BENANG">BENANG</option>
                                <option value="MC BENANG SAMPLE">MC BENANG SAMPLE</option>
                                <option value="ACRYLIC">ACRYLIC</option>
                                <option value="NYLON">NYLON</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-info add-btn">Tambah</button>
                        </div>
                    </form>
                </div>
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
                    <form action="<?= base_url($role . '/mesin/updateDataMesin') ?>" method="post">
                        <input type="hidden" name="id_mesin" id="id_mesin">
                        <div class="mb-3">
                            <label for="no_mesin" class="form-label">No Mesin</label>
                            <input type="text" class="form-control" name="no_mesin" id="no_mesinE" required>
                            <div id="no_mesin_feedbackE" class=""></div>
                        </div>
                        <div class="mb-3">
                            <label for="min_caps" class="form-label">Min Capacity</label>
                            <input type="text" class="form-control" name="min_caps" id="min_capsE" required>
                        </div>
                        <div class="mb-3">
                            <label for="max_caps" class="form-label">Max Capacity</label>
                            <input type="text" class="form-control" name="max_caps" id="max_capsE" required>
                        </div>
                        <div class="mb-3">
                            <label for="jml_lot" class="form-label">Jumlah LOT</label>
                            <input type="text" class="form-control" name="jml_lot" id="jml_lotE" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">LMD</label>
                            <div class="row">
                                <?php foreach (['L', 'M', 'D', 'WHITE', 'BLACK'] as $val): ?>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input
                                                type="checkbox" class="form-check-input" name="lmd[]" value="<?= $val ?>" id="lmd_<?= $val ?>">
                                            <label class="form-check-label" for="lmd_<?= $val ?>"><?= $val ?></label>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ket_mesinE" class="form-label">Keterangan Mesin</label>
                            <select class="form-select" name="ket_mesin" id="ket_mesinE" required>
                                <option value="">-- Pilih Keterangan Mesin --</option>
                                <option value="BENANG">BENANG</option>
                                <option value="MC BENANG SAMPLE">MC BENANG SAMPLE</option>
                                <option value="ACRYLIC">ACRYLIC</option>
                                <option value="NYLON">NYLON</option>
                            </select>
                        </div>
                </div>
                <!-- Action Button -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info update-btn">Update</button>
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
            "pageLength": 35,
            "order": []
        });
        $(document).ready(function() {

            $('#dataTable').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                $.getJSON('<?= base_url($role . "/mesin/getMesinDetails") ?>/' + id, function(resp) {
                    $('#id_mesin').val(resp.id_mesin);
                    $('#no_mesinE').val(resp.no_mesin);
                    $('#min_capsE').val(resp.min_caps);
                    $('#max_capsE').val(resp.max_caps);
                    $('#jml_lotE').val(resp.jml_lot);
                    // reset LMD
                    $('input[name="lmd[]"]').prop('checked', false);
                    if (resp.lmd) {
                        // misal resp.lmd = "L,M,WHITE"
                        resp.lmd.split(',').forEach(function(val) {
                            $(`#lmd_${val}`).prop('checked', true);
                        });
                    }
                    $('#ket_mesinE').val(resp.ket_mesin);

                    $('#updateModal').modal('show');
                });
            });

            // Event listener untuk tombol delete
            $('#dataTable').on('click', '.btn-delete', function() {
                const id = $(this).data('id');

                // Tampilkan konfirmasi
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
                        // Kirim request ke server
                        window.location = '<?= base_url($role . '/mesin/deleteDataMesin') ?>/' + id;
                    }
                });
            });
        });
    });

    document.getElementById('no_mesin').addEventListener('input', function() {
        const noMesinInput = this.value;
        const noMesinFeedback = document.getElementById('no_mesin_feedback');
        const addButton = document.querySelector('.add-btn');

        $.ajax({
            url: '<?= base_url($role . '/mesin/cekNoMesin') ?>',
            type: 'POST',
            data: {
                no_mesin: noMesinInput
            },
            success: function(response) {
                if (response.error) {
                    noMesinFeedback.textContent = 'Nomor Mesin Bisa Digunakan';
                    noMesinFeedback.classList.remove('feedback-error');
                    noMesinFeedback.classList.add('feedback-success');
                    addButton.removeAttribute('disabled');
                } else {
                    noMesinFeedback.textContent = 'Nomor Mesin Sudah Ada Di Database';
                    noMesinFeedback.classList.remove('feedback-success');
                    noMesinFeedback.classList.add('feedback-error');
                    addButton.setAttribute('disabled', 'disabled');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        })
    });
</script>

<?php $this->endSection(); ?>