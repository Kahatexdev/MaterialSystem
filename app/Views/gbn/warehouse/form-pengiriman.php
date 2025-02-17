<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<style>
    /* Menghilangkan border default Select2 */
    .select2-container--default .select2-selection--single {
        border: none;
        /* Hilangkan border samping & atas */
        border-bottom: 2px solid rgb(34, 121, 37);
        /* Garis bawah hijau */
        border-radius: 0 0 10px 10px;
        /* Sudut bawah melengkung */
        height: 38px;
        /* Sesuaikan tinggi */
        padding-left: 8px;
        background-color: #fff;
        /* Warna latar belakang */
    }

    /* Warna garis bawah saat fokus */
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:active {
        border-bottom: 2px solid rgb(34, 121, 37);
        /* Warna lebih gelap saat aktif */
        outline: none;
        box-shadow: none;
    }

    /* Styling teks di dalam Select2 */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        font-size: 16px;
    }

    /* Mengatur posisi ikon dropdown */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
    }
</style>
<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
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
                    text: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <div class="row">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">
                            Pengiriman Area
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#inputManual">Input</button>
                            <form action="<?= base_url($role . '/reset_pengiriman') ?>" method="post">
                                <button type="submit" class="btn bg-gradient-secondary"><i class="fas fa-redo"></i> Reset Data</button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <!-- <?= session()->get('pengirimanForm') ? 'Form data exists' : 'Form data does not exist'; ?> -->
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">No Model: <?= $no_model ?></label>
                            </div>
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">Item Type: <?= $item_type ?></label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">Kode Warna: <?= $kode_warna ?></label>
                            </div>
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">Warna: <?= $warna ?></label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">Kgs Pesan: <?= $kgs_pesan ?> Kg</label>
                            </div>
                            <div class="form-group">
                                <label class="badge bg-secondary text-white" for="">Cns Pesan: <?= $cns_pesan ?> Cns</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <form action="<?= base_url($role . '/pengiriman_area') ?>" method="post">
                                <input type="text" name="area" value="<?= $area ?>">
                                <input type="date" name="tgl_pakai" value="<?= $tgl_pakai ?>">
                                <input type="text" name="no_model" value="<?= $no_model ?>">
                                <input type="text" name="item_type" value="<?= $item_type ?>">
                                <input type="text" name="kode_warna" value="<?= $kode_warna ?>">
                                <input type="text" name="warna" value="<?= $warna ?>">
                                <input type="text" name="kgs_pesan" value="<?= $kgs_pesan ?>">
                                <input type="text" name="cns_pesan" value="<?= $cns_pesan ?>">

                                <div class="form-group">
                                    <label for="barcode" class="form-control-label">Scan Barcode</label>
                                    <input class="form-control" type="text" name="barcode" id="barcode" autofocus>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-1">
                <div class="card-body">
                    <div class="row">
                        <div class="d-flex justify-content-between">
                            <h6>

                            </h6>
                        </div>
                    </div>
                    <form action="<?= base_url($role . '/proses_pengiriman') ?>" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="table-responsive">
                                    <table id="inTable" class="table table-bordered table-striped">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th width=30 class="text-center"><input type="checkbox" name="select_all" id="select_all" value=""></th>
                                                <th class="text-center">Orders</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $today = date('d-M-Y');
                                            $formated = trim(date('Y-m-d'));

                                            foreach ($dataOut as $data) {
                                            ?>
                                                <tr>
                                                    <input type="hidden" name="id_out_celup[]" value="<?= $data['id_out_celup'] ?>">
                                                    <td align="center"><input type="checkbox" name="checked_id[]" class="checkbox" value="<?= $no - 1 ?>"> <?= $no++ ?></td>
                                                    <td>
                                                        <div class="form-group d-flex justify-content-end">
                                                            <label for="tgl">Tanggal Kirim : <?= $today ?></label>
                                                            <input type="date" class="form-control" name="tgl_kirim[]" value="<?= $formated ?>" hidden>
                                                            <input type="text" class="form-control" name="nama_cluster[]" value="<?= $data['nama_cluster'] ?>" hidden>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tgl">Model : </label>
                                                            <input type="text" class="form-control" name="no_model[]" value="<?= $data['no_model'] ?>" readonly>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Kode Benang:</label>
                                                                    <input type="text" class="form-control" name="item_type[]" value="<?= $data['item_type'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Kode Warna:</label>
                                                                    <input type="text" class="form-control" name="kode_warna[]" value="<?= $data['kode_warna'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for=""> Warna:</label>
                                                                    <input type="text" class="form-control" name="warna[]" value="<?= $data['warna'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for=""> Lot Kirim:</label>
                                                                    <input type="text" class="form-control" name="lot_kirim[]" value="<?= $data['lot_kirim'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for=""> Kgs Kirim:</label>
                                                                    <input type="number" class="form-control kgs_kirim" name="kgs_kirim[]" value="<?= $data['kgs_masuk'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Cones Kirim:</label>
                                                                    <input type="number" class="form-control" name="cns_kirim[]" value="<?= $data['cns_masuk'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group d-flex justify-content-end">
                                                            <button type="button" class="btn btn-danger removeRow btn-hapus" data-id="<?= $data['id_out_celup'] ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label for="ttl_kgs" class="form-label">Total Kgs:</label>
                                    <input type="text" class="form-control" name="ttl_kgs" id="ttl_kgs" readonly>
                                </div>
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="submit" class="btn bg-gradient-info w-100">Simpan Pengiriman</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        let isSubmitting = false;

        document.getElementById('barcode').addEventListener('input', function() {
            if (isSubmitting) return; // Cegah double submission
            setTimeout(() => {
                if (this.value.trim() !== '') {
                    isSubmitting = true;
                    this.form.submit();
                }
            }, 300);
        });

        $(document).ready(function() {
            $('#select_all').on('click', function() {
                if (this.checked) {
                    $('.checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });
        });

        $(document).ready(function() {
            // Event listener untuk tombol "Hapus"
            $('button.btn-hapus').on('click', function() {
                var id = $(this).data('id'); // Ambil ID yang ingin dihapus
                var row = $(this).closest('tr'); // Ambil baris tabel yang akan dihapus

                // Kirim ID ke controller untuk dihapus dari session
                $.post("<?= base_url($role . '/hapus_pengiriman') ?>", {
                    id: id
                }, function(response) {
                    if (response.success) {
                        row.remove(); // Hapus baris dari tabel
                    } else {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                }, 'json');
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Ambil semua checkbox dan input total
            let checkboxes = document.querySelectorAll(".checkbox");
            let ttlKgsInput = document.getElementById("ttl_kgs");

            function updateTotalKgs() {
                let total = 0;
                checkboxes.forEach((checkbox, index) => {
                    if (checkbox.checked) {
                        let kgsInput = document.querySelectorAll(".kgs_kirim")[index];
                        total += parseFloat(kgsInput.value) || 0;
                    }
                });

                // Set value dan trigger event change
                let ttlKgsInput = document.getElementById("ttl_kgs");
                ttlKgsInput.value = total.toFixed(2);

                // Trigger event change secara manual
                let event = new Event("change", {
                    bubbles: true
                });
                ttlKgsInput.dispatchEvent(event);
            }

            // Tambahkan event listener ke setiap checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", updateTotalKgs);
            });

            // Untuk fitur "Select All"
            document.getElementById("select_all").addEventListener("change", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateTotalKgs();
            });
        });
    </script>
    <?php $this->endSection(); ?>