<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">Pemasukan</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <!-- Tombol Input -->
                            <button class="btn btn-sm bg-gradient-info d-flex align-items-center justify-content-center"
                                data-bs-toggle="modal" data-bs-target="#inputManual" title="Input Manual">
                                <i class="fas fa-plus"></i>
                                <span class="d-none d-sm-inline ms-1">Input</span>
                            </button>

                            <!-- Tombol Reset -->
                            <form action="<?= base_url($role . '/reset_pemasukan') ?>" method="post">
                                <button type="submit" class="btn btn-sm bg-gradient-secondary d-flex align-items-center justify-content-center"
                                    title="Reset Data">
                                    <i class="fas fa-redo"></i>
                                    <span class="d-none d-sm-inline ms-1">Reset</span>
                                </button>
                            </form>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <form action="<?= base_url($role . '/pemasukan') ?>" method="post">
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
                    <form action="<?= base_url($role . '/proses_pemasukan') ?>" method="post">
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
                                                        <div class="mb-2 d-flex justify-content-between flex-wrap">
                                                            <strong class="text-dark">Tanggal Masuk: <?= $today ?></strong>
                                                            <input type="date" class="form-control" name="tgl_masuk[]" value="<?= $formated ?>" hidden>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Model:</label>
                                                                <input type="text" class="form-control form-control-sm" name="no_model[]" value="<?= $data['no_model'] ?>" readonly>
                                                                <input type="hidden" name="id_retur[]" value="<?= $data['id_retur'] ?? NULL ?>">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="">No Karung</label>
                                                                <input type="text" class="form-control form-control-sm" name="no_karung[]" value="<?= $data['no_karung'] ?>" readonly>
                                                            </div>
                                                        </div>


                                                        <div class="row">
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Kode Benang:</label>
                                                                <input type="text" class="form-control form-control-sm" name="item_type[]" value="<?= $data['item_type'] ?>" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Kode Warna:</label>
                                                                <input type="text" class="form-control form-control-sm" name="kode_warna[]" value="<?= $data['kode_warna'] ?>" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Warna:</label>
                                                                <input type="text" class="form-control form-control-sm" name="warna[]" value="<?= $data['warna'] ?>" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Lot:</label>
                                                                <input type="text" class="form-control form-control-sm" name="lot_kirim[]" value="<?= $data['lot_kirim'] ?? $data['lot_retur'] ?>" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Kgs Kirim:</label>
                                                                <input type="number" class="form-control form-control-sm kgs_kirim" name="kgs_kirim[]" value="<?= $data['kgs_kirim'] ?? $data['kgs_retur'] ?>" readonly>
                                                            </div>
                                                            <div class="col-12 col-md-6 mb-2">
                                                                <label class="form-label">Cones:</label>
                                                                <input type="number" class="form-control form-control-sm" name="cns_kirim[]" value="<?= $data['cones_kirim'] ?? $data['cns_retur'] ?>" readonly>
                                                            </div>
                                                        </div>

                                                        <!-- Tombol Aksi -->
                                                        <div class="d-flex justify-content-end gap-2 mt-2 flex-wrap">
                                                            <button type="button" class="btn btn-sm btn-danger removeRow btn-hapus" title="Hapus Data" data-id="<?= $data['id_out_celup'] ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>

                                                            <button
                                                                type="button"
                                                                class="btn btn-sm bg-gradient-warning btn-komplain"
                                                                title="Komplain"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#complainModal"
                                                                data-id="<?= $data['id_out_celup'] ?>"
                                                                data-model="<?= htmlspecialchars($data['no_model']) ?>"
                                                                data-item_type="<?= htmlspecialchars($data['item_type']) ?>"
                                                                data-kode_warna="<?= htmlspecialchars($data['kode_warna']) ?>"
                                                                data-warna="<?= htmlspecialchars($data['warna']) ?>"
                                                                data-lot_kirim="<?= htmlspecialchars($data['lot_kirim'] ?? $data['lot_retur']) ?>"
                                                                data-kgs_kirim="<?= htmlspecialchars($data['kgs_kirim']  ?? $data['kgs_retur']) ?>"
                                                                data-cns_kirim="<?= htmlspecialchars($data['cones_kirim'] ?? $data['cns_retur']) ?>">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                <span class="d-none d-sm-inline">Komplain</span>
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
                                <div class="col-md-4 sm-12">
                                    <label for="ttl_kgs" class="form-label">Total Kgs:</label>
                                    <input type="text" class="form-control" name="ttl_kgs" id="ttl_kgs" readonly>
                                </div>

                                <div class="col-md-4 sm-12">
                                    <label for="cluster" class="form-label">Pilih Cluster:</label>
                                    <select class="form-control" name="cluster" id="cluster">
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="col-md-4 sm-12">
                                    <label for="sisa_kapasitas" class="form-label">Sisa Kapasitas:</label>
                                    <input type="number" class="form-control sisa_kapasitas" name="sisa_kapasitas" value="" readonly>
                                </div>

                                <p style="color: red; font-size: 12px; margin-top:10px;"> ⚠ Hanya diisi untuk komplain</p>
                                <div class="col-md-12 sm-12">
                                    <label for="">Kategori</label>
                                    <select name="kategori_retur" id="kategori_retur" class="form-control">
                                        <option value="">Pilih Kategori Retur</option>
                                        <?php foreach ($kategori as $k) : ?>
                                            <option value="<?= $k['nama_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12 sm-12" id="form_alasan">
                                    <!-- <div class="col-md-12 sm-12" id="form_alasan" style="display: none;"> -->
                                    <label for="alasan" class="form-label">Alasan:</label>
                                    <input type="text" class="form-control" name="alasan" id="complain_reason">
                                </div>

                                <div class="col-md-12 d-flex align-items-end mt-3">
                                    <button type="submit" name="action" value="simpan" class="btn bg-gradient-info w-100">Simpan Pemasukan</button>
                                </div>
                                <div class="col-md-12 d-flex align-items-end">
                                    <button type="submit" name="action" value="komplain" class="btn bg-gradient-warning w-100"><i class="fas fa-exclamation-triangle"></i> Komplain</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- modal input pemasukan manual -->
    <div class="modal fade" id="inputManual" tabindex="-1" role="dialog" aria-labelledby="inputManual" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form Input Manual</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?= base_url($role . '/proses_pemasukan_manual'); ?>" method="POST">
                    <div class="modal-body align-items-center">
                        <?php
                        $no = 1;
                        $today = date('d-M-Y');
                        $formated = trim(date('Y-m-d'));
                        ?>
                        <div class="form-group d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label for="tgl" class="mb-0">Tanggal Masuk : <?= $today ?></label>
                                <input type="date" class="form-control" name="tgl_kirim" value="<?= $formated ?>" hidden>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="retur" id="retur">
                                <label class="form-check-label" for="retur">Dari Retur</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="tgl">Model : </label>
                                    <input type="text" class="form-control" id="no_model" name="no_model" value="" autofocus>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Kode Benang:</label>
                                    <select class="form-control item-type" id="item_type" name="item_type" required>
                                        <option value="">Pilih Item Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Kode Warna:</label>
                                    <select class="form-control kode-warna" name="kode_warna" required>
                                        <option value="">Pilih Kode Warna</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Warna:</label>
                                    <input type="text" class="form-control warna" name="warna" value="" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Lot:</label>
                                    <select class="form-control lot-kirim" id="lot_kirim" name="lot_kirim" required>
                                        <option value="">Pilih Lot</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> No Karung:</label>
                                    <!-- <input type="number" class="form-control" id="no_karung" name="no_karung" value="" required> -->
                                    <select class="form-control no-karung" id="no_karung" name="no_karung" required>
                                        <option value="">Pilih No Karung</option>
                                    </select>
                                    <input type="hidden" id="id_out_celup" name="id_out_celup" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Gw:</label>
                                    <input type="number" class="form-control" name="gw_kirim" value="" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Kgs:</label>
                                    <input type="number" class="form-control" name="kgs_kirim" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Cones:</label>
                                    <input type="number" class="form-control" name="cns_kirim" value="" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Cluster</label>
                                    <select class="form-control cluster" id="cluster" name="cluster">
                                        <!-- <option value="">Pilih Cluster</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Sisa Kapasitas</label>
                                    <input type="number" class="form-control" name="sisa_kapasitas" id="sisa_kapasitas" value="" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Alasan</label>
                                    <p style="color: red; font-size: 12px;"> ⚠ Hanya diisi untuk komplain</p>
                                    <input type="text" class="form-control" name="alasan" id="alasan" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="action" id="action" value="">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-warning" onclick="setAction('komplain')"><i class="fas fa-exclamation-triangle"></i> Komplain</button>
                        <button type="submit" class="btn bg-gradient-info" onclick="setAction('simpan')">Simpan Pemasukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Komplain -->
    <div class="modal fade" id="complainModal" tabindex="-1" aria-labelledby="complainModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= base_url($role . '/komplain_pemasukan') ?>" method="post" id="complainForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="complainModalLabel">Komplain Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field untuk ID barang -->
                        <input type="hidden" name="id_out_celup" id="complain_id">
                        <input type="hidden" name="no_model" id="no_model_complain">
                        <input type="hidden" name="item_type" id="item_type_complain">
                        <input type="hidden" name="kode_warna" id="kode_warna_complain">
                        <input type="hidden" name="warna" id="warna_complain">
                        <input type="hidden" name="lot_kirim" id="lot_kirim_complain">
                        <input type="hidden" name="kgs_kirim" id="kgs_kirim_complain">
                        <input type="hidden" name="cns_kirim" id="cns_complain">
                        <div class="mb-3">
                            <label for="">Kategori</label>
                            <select name="kategori_retur" id="kategori_retur_modal" class="form-control" required>
                                <option value="">Pilih Kategori Retur</option>
                                <?php foreach ($kategori as $k) : ?>
                                    <option value="<?= $k['nama_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3" id="form_alasan_modal">
                            <!-- <div class="mb-3" id="form_alasan_modal" style="display: none;"> -->
                            <label for="complain_reason" class="form-label">Alasan Komplain</label>
                            <textarea class="form-control" id="complain_reason_modal" name="alasan" rows="3"></textarea>
                        </div>
                        <!-- Tambahkan field lain jika diperlukan -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Komplain</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            $('#cluster').select2({
                placeholder: "Pilih Cluster",
                allowClear: true,
                minimumResultsForSearch: 0
            });
        });

        // Fungsi hitung total kgs
        function updateTotalKgs() {
            const checkboxes = document.querySelectorAll(".checkbox");
            const kgsInputs = document.querySelectorAll(".kgs_kirim");
            let total = 0;

            checkboxes.forEach((checkbox, idx) => {
                if (checkbox.checked) {
                    total += parseFloat(kgsInputs[idx].value) || 0;
                }
            });

            const ttlKgsInput = document.getElementById("ttl_kgs");
            ttlKgsInput.value = total.toFixed(2);
            ttlKgsInput.dispatchEvent(new Event("change", {
                bubbles: true
            }));
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Delegasi untuk checkbox dan select_all
            document.getElementById("inTable").addEventListener("change", function(e) {
                if (e.target.matches(".checkbox")) {
                    updateTotalKgs();
                }
                if (e.target.matches("#select_all")) {
                    document.querySelectorAll(".checkbox").forEach(cb => cb.checked = e.target.checked);
                    updateTotalKgs();
                }
            });

            // Delegasi untuk tombol hapus
            document.getElementById("inTable").addEventListener("click", function(e) {
                const btn = e.target.closest(".btn-hapus");
                if (!btn) return;

                const id = btn.getAttribute("data-id");
                const row = btn.closest("tr");

                $.post("<?= base_url($role . '/hapus_pemasukan') ?>", {
                    id: id
                }, function(response) {
                    if (response.success) {
                        row.remove();
                        updateTotalKgs();
                    } else {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                }, 'json');
            });
            $('#ttl_kgs').on("change", function() {
                var kgsKirim = $(this).val()?.trim(); // Cari input warna terkait
                var $clusterSelect = $("#cluster");

                console.log("Kgs Kirim:", kgsKirim);
                console.log("Cluster:", cluster);

                var url = '<?= base_url($role . "/getcluster") ?>';

                console.log("URL request:", url);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        kgs: kgsKirim
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log("Respons dari server:", response);
                        // Set Cluster
                        $clusterSelect.empty(); // Kosongkan dulu
                        $clusterSelect.append('<option value="">Pilih Cluster</option>'); // Tambahin opsi default
                        if (response.length > 0) {
                            response.forEach(function(cluster) {
                                $clusterSelect.append('<option value="' + cluster.nama_cluster + '" data-sisa_kapasitas="' + cluster.sisa_kapasitas + '">' + cluster.nama_cluster + '</option>');
                            });
                        } else {
                            console.warn("Cluster tidak ditemukan!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan:", error);
                        console.error("Respons yang diterima:", xhr.responseText);
                    }
                });
            });
            // Event listener saat cluster dipilih
            $(document).on("change", "#cluster", function() {
                var sisaKapasitas = $(this).find("option:selected").attr("data-sisa_kapasitas") || "";
                console.log("Sisa Kapasitas:", sisaKapasitas);
                $(".sisa_kapasitas").val(parseFloat(sisaKapasitas).toFixed(2));
            });
        });
    </script>
    <script>
        // UNTUK MODAL
        // Tangani perubahan pada input no_model
        $('#no_model').on('change', function() {
            loadItemTypes();
        });

        $(document).on('change', '.item-type', function() {
            loadKodeWarna($(this));
        });

        // Fungsi untuk load Item Types
        function loadItemTypes() {
            var noModel = $('#no_model').val().trim();
            var retur = $('#retur').is(':checked') ? 1 : 0; // karena checkbox
            var $lotKirimSelect = $('.lot-kirim'); // langsung ambil select lot
            var $karungSelect = $('.no-karung'); // langsung ambil select lot

            if (noModel) {
                $.ajax({
                    url: '<?= base_url($role . "/getItemTypeByModel") ?>/' + encodeURIComponent(noModel) + '?retur=' + retur,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var $itemTypeSelect = $('.item-type');
                        $itemTypeSelect.empty().append('<option value="">Pilih Item Type</option>');
                        $.each(data, function(index, item) {
                            $itemTypeSelect.append('<option value="' + item.item_type + '">' + item.item_type + '</option>');
                        });

                        // Call loadKodeWarna after item types are loaded, in case it's the initial load
                        loadKodeWarna(); // Uncomment if you want to call after item types are loaded
                        $lotKirimSelect.empty().append('<option value="">Pilih Lot</option>');
                        $karungSelect.empty().append('<option value="">Pilih No Karung</option>');
                        $('input[name="no_karung"]').val('');
                        $('input[name="warna"]').val('');
                        $('input[name="id_out_celup"]').val('');
                        $('input[name="gw_kirim"]').val(0);
                        $('input[name="kgs_kirim"]').val(0).trigger('change');
                        $('input[name="cns_kirim"]').val(0);
                        $('input[name="sisa_kapasitas"]').val(0);
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan: " + error);
                    }
                });
            }
        }

        // Fungsi untuk load Kode Warna
        function loadKodeWarna(noModel, itemType) {
            var $row = $(".row");
            var noModel = $('#no_model').val().trim();
            var itemType = $('#item_type').val().trim(); // Dapatkan itemType dengan benar
            var retur = $('#retur').is(':checked') ? 1 : 0; // karena checkbox
            var $lotKirimSelect = $('.lot-kirim'); // langsung ambil select lot
            var $karungSelect = $('.no-karung'); // langsung ambil select lot

            if (noModel && itemType) {
                var url = `<?= base_url($role . "/getKodeWarnaByModelAndItemType") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&retur=${retur}`;

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var $kodeWarnaSelect = $row.find('.kode-warna');
                        $kodeWarnaSelect.empty().append('<option value="">Pilih Kode Warna</option>');
                        $.each(data, function(index, item) {
                            $kodeWarnaSelect.append('<option value="' + item.kode_warna + '">' + item.kode_warna + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan: " + error);
                    }
                });
            } else {
                $row.find('.kode-warna').empty().append('<option value="">Pilih Kode Warna</option>');
                $lotKirimSelect.empty().append('<option value="">Pilih Lot</option>');
                $karungSelect.empty().append('<option value="">Pilih No Karung</option>');
                $('input[name="no_karung"]').val('');
                $('input[name="warna"]').val('');
                $('input[name="id_out_celup"]').val('');
                $('input[name="gw_kirim"]').val(0);
                $('input[name="kgs_kirim"]').val(0).trigger('change');
                $('input[name="cns_kirim"]').val(0);
                $('input[name="sisa_kapasitas"]').val(0);
            }
        }

        // Fungsi untuk load Warna dan Lot berdasarkan Kode Warna
        $(document).ready(function() {
            $(".kode-warna").on("change", function() {
                // Ambil container/form terdekat (sesuaikan jika perlu)
                var $form = $(this).closest("form");
                var noModel = $("#no_model").val().trim();
                var itemType = $("#item_type").val().trim();
                var kodeWarna = $(this).val().trim();
                var $warnaInput = $form.find('input[name="warna"]'); // Cari input warna di dalam form terkait
                var $lotSelect = $form.find(".lot-kirim");
                var $karungSelect = $form.find(".no-karung");
                var retur = $('#retur').is(':checked') ? 1 : 0; // karena checkbox

                console.log("No Model:", noModel);
                console.log("Item Type:", itemType);
                console.log("Kode Warna:", kodeWarna);

                if (!noModel || !itemType || !kodeWarna) {
                    console.warn("Pastikan no_model, item_type, dan kode_warna sudah dipilih!");
                    $lotSelect.empty().append('<option value="">Pilih Lot</option>');
                    $karungSelect.empty().append('<option value="">Pilih No Karung</option>');
                    $('input[name="no_karung"]').val('');
                    $('input[name="warna"]').val('');
                    $('input[name="id_out_celup"]').val('');
                    $('input[name="gw_kirim"]').val(0);
                    $('input[name="kgs_kirim"]').val(0).trigger('change');
                    $('input[name="cns_kirim"]').val(0);
                    $('input[name="sisa_kapasitas"]').val(0);
                    return;
                }

                if (noModel && itemType && kodeWarna) {

                    var url = `<?= base_url($role . "/getWarnaDanLot") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&kodeWarna=${kodeWarna}&retur=${retur}`;
                    console.log("URL request:", url);

                    $.ajax({
                        url: url,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            console.log("Respons dari server:", response);

                            // Set warna jika ditemukan (cek juga jika string tidak hanya spasi)
                            if (response.warna && response.warna.trim() !== "") {
                                $warnaInput.val(response.warna);
                            } else {
                                alert("Warna tidak ditemukan!");
                                $warnaInput.val("");
                            }

                            // Set lot
                            $lotSelect.empty().append('<option value="">Pilih Lot</option>');
                            if (response.lot && response.lot.length > 0) {
                                $.each(response.lot, function(index, lot) {
                                    $lotSelect.append('<option value="' + lot.lot_kirim + '">' + lot.lot_kirim + '</option>');
                                });
                            } else {
                                console.warn("Lot tidak ditemukan!");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Terjadi kesalahan:", error);
                            console.error("Respons yang diterima:", xhr.responseText);
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            // Tambahan: ambil no karung saat lot berubah
            $(document).on("change", ".lot-kirim", function() {
                var $form = $(this).closest("form");
                var noModel = $("#no_model").val().trim();
                var itemType = $("#item_type").val().trim();
                var kodeWarna = $form.find(".kode-warna").val().trim();
                var lot = $(this).val();
                var $karungSelect = $form.find(".no-karung"); // pastikan ada select/input untuk no karung
                var retur = $('#retur').is(':checked') ? 1 : 0;
                console.log(lot);

                // kalau lot kosong → kosongkan dropdown no karung & keluar
                if (!lot) {
                    $karungSelect.empty().append('<option value="">Pilih No Karung</option>');
                    $('input[name="id_out_celup"]').val('');
                    $('input[name="gw_kirim"]').val(0);
                    $('input[name="kgs_kirim"]').val(0).trigger('change');
                    $('input[name="cns_kirim"]').val(0);
                    $('input[name="sisa_kapasitas"]').val(0);
                    return;
                }

                if (!noModel || !itemType || !kodeWarna || !lot) {
                    console.warn("Data tidak lengkap untuk ambil no karung!");
                    return;
                }

                var url = `<?= base_url($role . "/getNoKarung") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&kodeWarna=${kodeWarna}&lot=${lot}&retur=${retur}`;
                console.log("URL request no karung:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Respons no karung:", response);

                        $karungSelect.empty().append('<option value="">Pilih No Karung</option>');
                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function(index, k) {
                                // $karungSelect.append('<option value="' + k.no_karung + '">' + k.no_karung + ' (Gw:' + k.gw_kirim + ' / Kgs:' + k.kgs_kirim + ' / Cns:' + k.cones_kirim + ' / SJ:' + k.no_surat_jalan + ')</option>');
                                $karungSelect.append(
                                    '<option value="' + k.no_karung + '" ' +
                                    'data-id_out_celup="' + k.id_out_celup + '" ' +
                                    'data-gw="' + k.gw_kirim + '" ' +
                                    'data-kgs="' + k.kgs_kirim + '" ' +
                                    'data-cns="' + k.cones_kirim + '">' +
                                    k.no_karung + ' (Gw:' + k.gw_kirim + ' / Kgs:' + k.kgs_kirim + ' / Cns:' + k.cones_kirim + ' / SJ:' + k.no_surat_jalan + ')' +
                                    '</option>'
                                );
                            });

                        } else {
                            console.warn("No karung tidak ditemukan!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan ambil no karung:", error);
                        console.error("Respons yang diterima:", xhr.responseText);
                    }
                });
                $karungSelect.off("change").on("change", function() {
                    var selected = $(this).find(":selected");
                    var idOutCelup = selected.data("id_out_celup") || "";
                    var gw = selected.data("gw") || 0;
                    var kgs = selected.data("kgs") || 0;
                    var cns = selected.data("cns") || 0;

                    // isi ke input hidden
                    $('input[name="id_out_celup"]').val(idOutCelup);
                    $('input[name="gw_kirim"]').val(gw);
                    $('input[name="kgs_kirim"]').val(kgs).trigger('change');
                    $('input[name="cns_kirim"]').val(cns);
                });
            });
        });

        // // Fungsi untuk Load Kgs Kirim dan Cones Kirim
        // $(document).ready(function() {
        //     $('#no_karung').change(function() {
        //         var noModel = $('#no_model').val();
        //         var itemType = $('#item_type').val();
        //         var kodeWarna = $('.kode-warna').val();
        //         var lotKirim = $('#lot_kirim').val();
        //         var noKarung = $(this).val();
        //         var idOutCelup = $('#id_out_celup').val();
        //         var retur = $('#retur').is(':checked') ? 1 : 0; // karena checkbox

        //         console.log("No Model:", noModel);
        //         console.log("Item Type:", itemType);
        //         console.log("Kode Warna:", kodeWarna);
        //         console.log("Lot Kirim:", lotKirim);
        //         console.log("No Karung:", noKarung);

        //         if (lotKirim) {
        //             $.ajax({

        //                 // url: `<?= base_url($role . "/getKgsDanCones") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&kodeWarna=${kodeWarna}&lotKirim=${lotKirim}&noKarung=${noKarung}&retur=${retur}`,
        //                 url: `<?= base_url($role . "/getKgsDanCones") ?>?id_Out_celup=${idOutCelup}`,
        //                 type: 'GET',
        //                 dataType: 'json',
        //                 success: function(response) {
        //                     console.log("Response dari server:", response); // Debug response
        //                     if (response.success) {
        //                         $('input[name="id_out_celup"]').val(response.id_out_celup);
        //                         $('input[name="gw_kirim"]').val(response.gw_kirim);
        //                         $('input[name="kgs_kirim"]').val(response.kgs_kirim).trigger('change');
        //                         $('input[name="cns_kirim"]').val(response.cones_kirim);
        //                     } else {
        //                         $('input[name="id_out_celup"]').val('');
        //                         $('input[name="gw_kirim"]').val(0);
        //                         $('input[name="kgs_kirim"]').val(0).trigger('change');
        //                         $('input[name="cns_kirim"]').val(0);
        //                         alert("Data untuk nomor karung tersebut tidak ditemukan. Nilai Kgs dan CNS di-reset ke 0.");
        //                         console.log("Data tidak ditemukan");
        //                     }
        //                 },
        //                 error: function(xhr, status, error) {
        //                     console.error("AJAX Error: ", error);
        //                 }
        //             });
        //         }
        //     });
        // });
        //fungsi untuk memilih cluster di modal
        $(document).ready(function() {
            $('input[name="kgs_kirim"]').on("change", function() {
                var kgsKirim = $(this).val()?.trim(); // Cari input warna terkait
                var $clusterSelect = $(".cluster");

                console.log("Kgs Kirim:", kgsKirim);
                console.log("Cluster:", cluster);

                var url = '<?= base_url($role . "/getcluster") ?>';

                console.log("URL request:", url);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        kgs: kgsKirim
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log("Respons dari server:", response);
                        // Set Cluster
                        $clusterSelect.empty(); // Kosongkan dulu
                        $clusterSelect.append('<option value="">Pilih Cluster</option>'); // Tambahin opsi default
                        if (response.length > 0) {
                            response.forEach(function(cluster) {
                                $clusterSelect.append('<option value="' + cluster.nama_cluster + '" data-sisa_kapasitas="' + cluster.sisa_kapasitas + '">' + cluster.nama_cluster + '</option>');
                            });
                        } else {
                            console.warn("Cluster tidak ditemukan!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan:", error);
                        console.error("Respons yang diterima:", xhr.responseText);
                    }
                });
            });
            // Event listener saat cluster dipilih
            $(document).on("change", ".cluster", function() {
                var sisaKapasitas = $(this).find("option:selected").attr("data-sisa_kapasitas") || "";
                // console.log("Sisa Kapasitas:", (sisaKapasitas).toFixed(2));
                $("#sisa_kapasitas").val(parseFloat(sisaKapasitas).toFixed(2));
            });
        });

        //Untuk modal komplain
        document.addEventListener("DOMContentLoaded", function() {
            // Tangkap semua tombol komplain
            const komplainButtons = document.querySelectorAll(".btn-komplain");

            komplainButtons.forEach(button => {
                button.addEventListener("click", function() {
                    // Ambil data-id dari tombol yang ditekan
                    const idOutCelup = this.getAttribute("data-id");
                    const model = this.getAttribute("data-model");
                    const itemType = this.getAttribute("data-item_type");
                    const kodeWarna = this.getAttribute("data-kode_warna");
                    const warna = this.getAttribute("data-warna");
                    const lot = this.getAttribute("data-lot_kirim");
                    const kgs = this.getAttribute("data-kgs_kirim");
                    const cns = this.getAttribute("data-cns_kirim");


                    // Masukkan nilai id_out_celup ke dalam input di modal
                    document.getElementById("complain_id").value = idOutCelup;
                    document.getElementById("no_model_complain").value = model;
                    document.getElementById("item_type_complain").value = itemType;
                    document.getElementById("kode_warna_complain").value = kodeWarna;
                    document.getElementById("warna_complain").value = warna;
                    document.getElementById("lot_kirim_complain").value = lot;
                    document.getElementById("kgs_kirim_complain").value = kgs;
                    document.getElementById("cns_complain").value = cns;
                });
            });
        });

        function setAction(value) {
            document.getElementById('action').value = value;
        }

        //Ketika Bon Revisi Dipilih Akan Menampilkan Text Area
        // document.addEventListener('DOMContentLoaded', function() {
        //     const kategoriSelect = document.getElementById('kategori_retur');
        //     const formAlasan = document.getElementById('form_alasan');
        //     const alasanTextArea = document.getElementById('complain_reason');

        //     const kategoriSelectModal = document.getElementById('kategori_retur_modal');
        //     const formAlasanModal = document.getElementById('form_alasan_modal');
        //     const alasanTextAreaModal = document.getElementById('complain_reason_modal');

        //     kategoriSelect.addEventListener('change', function() {
        //         if (this.value === 'REVISI BON') {
        //             formAlasan.style.display = 'block';
        //             alasanTextArea.setAttribute('required', 'required');
        //         } else {
        //             formAlasan.style.display = 'none';
        //             alasanTextArea.removeAttribute('required');
        //             alasanTextArea.value = ''; // optional: reset value
        //         }
        //     });

        //     kategoriSelectModal.addEventListener('change', function() {
        //         if (this.value === 'REVISI BON') {
        //             formAlasanModal.style.display = 'block';
        //             alasanTextAreaModal.setAttribute('required', 'required');
        //         } else {
        //             formAlasanModal.style.display = 'none';
        //             alasanTextAreaModal.removeAttribute('required');
        //             alasanTextAreaModal.value = ''; // optional: reset value
        //         }
        //     });
        // });
    </script>
    <!-- Script Alin -->
    <!-- <script type="text/javascript">
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
            $('#cluster').select2({
                placeholder: "Pilih Cluster",
                allowClear: true,
                minimumResultsForSearch: 0
            });
        });

        $(document).ready(function() {
            // Event listener untuk tombol "Hapus"
            $('button.btn-hapus').on('click', function() {
                var id = $(this).data('id'); // Ambil ID yang ingin dihapus
                var row = $(this).closest('tr'); // Ambil baris tabel yang akan dihapus

                // Kirim ID ke controller untuk dihapus dari session
                $.post("<?= base_url($role . '/hapus_pemasukan') ?>", {
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

        //fungsi untuk memilih cluster
        $(document).ready(function() {
            $('#ttl_kgs').on("change", function() {
                var kgsKirim = $(this).val()?.trim(); // Cari input warna terkait
                var $clusterSelect = $("#cluster");

                console.log("Kgs Kirim:", kgsKirim);
                console.log("Cluster:", cluster);

                var url = '<?= base_url($role . "/getcluster") ?>';

                console.log("URL request:", url);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        kgs: kgsKirim
                    },
                    dataType: "json",
                    success: function(response) {
                        console.log("Respons dari server:", response);
                        // Set Cluster
                        $clusterSelect.empty(); // Kosongkan dulu
                        $clusterSelect.append('<option value="">Pilih Cluster</option>'); // Tambahin opsi default
                        if (response.length > 0) {
                            response.forEach(function(cluster) {
                                $clusterSelect.append('<option value="' + cluster.nama_cluster + '" data-sisa_kapasitas="' + cluster.sisa_kapasitas + '">' + cluster.nama_cluster + '</option>');
                            });
                        } else {
                            console.warn("Cluster tidak ditemukan!");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan:", error);
                        console.error("Respons yang diterima:", xhr.responseText);
                    }
                });
            });
            // Event listener saat cluster dipilih
            $(document).on("change", "#cluster", function() {
                var sisaKapasitas = $(this).find("option:selected").attr("data-sisa_kapasitas") || "";
                console.log("Sisa Kapasitas:", sisaKapasitas);
                $(".sisa_kapasitas").val(sisaKapasitas);
            });
        });
    </script> -->
    <?php $this->endSection(); ?>