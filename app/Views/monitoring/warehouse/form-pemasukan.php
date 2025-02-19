<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
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
                            Pemasukan
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#inputManual">Input</button>
                            <form action="<?= base_url($role . '/reset_pemasukan') ?>" method="post">
                                <button type="submit" class="btn bg-gradient-warning">Reset Data</button>
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
                                        <thead>
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
                                                            <label for="tgl">Tanggal Masuk : <?= $today ?></label>
                                                            <input type="date" class="form-control" name="tgl_masuk[]" value="<?= $formated ?>" hidden>
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
                                                                    <label for=""> Lot:</label>
                                                                    <input type="text" class="form-control" name="lot_kirim[]" value="<?= $data['lot_kirim'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for=""> Kgs Kirim:</label>
                                                                    <input type="number" class="form-control" name="kgs_kirim[]" value="<?= $data['kgs_kirim'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Cones:</label>
                                                                    <input type="number" class="form-control" name="cns_kirim[]" value="<?= $data['cones_kirim'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group d-flex justify-content-end">
                                                            <button type="button" class="btn btn-block  bg-gradient-danger btn-hapus" data-id="<?= $data['id_out_celup'] ?>">Hapus</button>
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
                            <div class="d-flex align-items-center gap-4 mt-2">
                                <label for="cluster" class="mb-0">Pilih Cluster :</label>
                                <select class="form-select" type="text" name="cluster" id="cluster" required>
                                    <option value="0"></option>
                                    <?php foreach ($cluster as $id) : ?>
                                        <option value="<?= $id['nama_cluster'] ?>"><?= $id['nama_cluster'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn bg-gradient-success">Save</button>
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
                        <div class="form-group d-flex justify-content-end">
                            <label for="tgl">Tanggal Masuk : <?= $today ?></label>
                            <input type="date" class="form-control" name="tgl_kirim" value="<?= $formated ?>" hidden>
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
                                    <select class="form-select item-type" id="item_type" name="item_type" required>
                                        <option value="">Pilih Item Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Kode Warna:</label>
                                    <select class="form-select kode-warna" name="kode_warna" required>
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
                                    <select class="form-select lot-kirim" id="lot_kirim" name="lot_kirim" required>
                                        <option value="">Pilih Lot</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> No Karung:</label>
                                    <input type="number" class="form-control" id="no_karung" name="no_karung" value="" required>
                                    <input type="text" id="id_out_celup" name="id_out_celup" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Kgs Kirim:</label>
                                    <input type="number" class="form-control" name="kgs_kirim" value="" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Cones:</label>
                                    <input type="number" class="form-control" name="cns_kirim" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Cluster</label>
                                    <select class="form-select cluster" id="cluster" name="cluster" required>
                                        <option value="">Pilih Cluster</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Sisa Kapasitas</label>
                                    <input type="number" class="form-control" name="sisa_kapasitas" id="sisa_kapasitas" value="" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn bg-gradient-info">Save</button>
                    </div>
                </form>
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
            $('#cluster').select2({
                placeholder: "Pilih Cluster",
                allowClear: true
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
            if (noModel) {
                $.ajax({
                    url: '<?= base_url($role . "/getItemTypeByModel") ?>/' + encodeURIComponent(noModel),
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
                    },
                    error: function(xhr, status, error) {
                        console.error("Terjadi kesalahan: " + error);
                    }
                });
            }
        }

        // Fungsi untuk load Kode Warna
        function loadKodeWarna() {
            var $row = $(".row");
            var noModel = $('#no_model').val().trim();
            var itemType = $('#item_type').val().trim(); // Dapatkan itemType dengan benar

            if (noModel && itemType) {
                var url = '<?= base_url($role . "/getKodeWarnaByModelAndItemType") ?>/' + encodeURIComponent(noModel) + '/' + encodeURIComponent(itemType);

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
            }
        }

        // Fungsi untuk load Warna dan Lot berdasarkan Kode Warna
        $(document).ready(function() {
            $(".kode-warna").on("change", function() {
                var noModel = $("#no_model").val().trim();
                var itemType = $("#item_type").val().trim();
                var kodeWarna = $(this).val()?.trim();
                var $warnaInput = $('input[name="warna"]'); // Cari input warna terkait
                var $lotSelect = $(".lot-kirim");

                console.log("No Model:", noModel);
                console.log("Item Type:", itemType);
                console.log("Kode Warna:", kodeWarna);

                if (!noModel || !itemType || !kodeWarna) {
                    console.warn("Pastikan no_model, item_type, dan kode_warna sudah dipilih!");
                    return;
                }

                var url = '<?= base_url($role . "/getWarnaDanLot") ?>/' + encodeURIComponent(noModel) + '/' + encodeURIComponent(itemType) + '/' + encodeURIComponent(kodeWarna);

                console.log("URL request:", url);

                $.ajax({
                    url: url,
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Respons dari server:", response);
                        //set warna
                        if (response.warna) {
                            $warnaInput.val(response.warna);
                        } else {
                            alert("Warna tidak ditemukan!");
                            $warnaInput.val("");
                        }

                        // Set lot
                        $lotSelect.empty(); // Kosongkan dulu
                        $lotSelect.append('<option value="">Pilih Lot</option>'); // Tambahin opsi default
                        if (response.lot && response.lot.length > 0) {
                            response.lot.forEach(function(lot) {
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
            });
        });
        // Fungsi untuk Load Kgs Kirim dan Cones Kirim
        $(document).ready(function() {
            $('#no_karung').change(function() {
                var noModel = $('#no_model').val();
                var itemType = $('#item_type').val();
                var kodeWarna = $('.kode-warna').val();
                var lotKirim = $('#lot_kirim').val();
                var noKarung = $(this).val();

                console.log("No Model:", noModel);
                console.log("Item Type:", itemType);
                console.log("Kode Warna:", kodeWarna);
                console.log("Lot Kirim:", lotKirim);
                console.log("No Karung:", noKarung);

                if (lotKirim) {
                    $.ajax({
                        url: '<?= base_url($role . "/getKgsDanCones") ?>/' + encodeURIComponent(noModel) + '/' + encodeURIComponent(itemType) + '/' + encodeURIComponent(kodeWarna) + '/' + encodeURIComponent(lotKirim) + '/' + encodeURIComponent(noKarung),
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response dari server:", response); // Debug response
                            if (response.success) {
                                $('input[name="id_out_celup"]').val(response.id_out_celup);
                                $('input[name="kgs_kirim"]').val(response.kgs_kirim).trigger('change');
                                $('input[name="cns_kirim"]').val(response.cones_kirim);
                            } else {
                                console.log("Data tidak ditemukan");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", error);
                        }
                    });
                }
            });
        });
        //fungsi untuk memilih cluster
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
                console.log("Sisa Kapasitas:", sisaKapasitas);
                $("#sisa_kapasitas").val(sisaKapasitas);
            });
        });
    </script>

    <?php $this->endSection(); ?>