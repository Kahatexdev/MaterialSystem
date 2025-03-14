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
                        <h5>
                            Pengeluaran Jalur
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn bg-gradient-info" data-bs-toggle="modal" data-bs-target="#inputManual">Input</button>
                            <form action="<?= base_url($role . '/reset_pengeluaran') ?>" method="post">
                                <button type="submit" class="btn bg-gradient-secondary"><i class="fas fa-redo"></i> Reset Data</button>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12">
                            <form action="<?= base_url($role . '/pengeluaran_jalur') ?>" method="post">
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
                    <form action="<?= base_url($role . '/proses_pengeluaran_jalur') ?>" method="post">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="table-responsive">
                                    <table id="inTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width=30 class="text-center"><input type="checkbox" name="select_all" id="select_all" value=""></th>
                                                <th class="text-center"><i class="fas fa-cart-shopping"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $today = date('d-M-Y');
                                            $formated = trim(date('Y-m-d'));

                                            foreach ($dataOutJalur as $data) {
                                            ?>
                                                <tr>
                                                    <input type="hidden" name="id_out_celup[]" value="<?= $data['id_out_celup'] ?>">
                                                    <input type="hidden" name="lot_kirim[]" value="<?= $data['lot_kirim'] ?>">
                                                    <td align="center"><input type="checkbox" name="checked_id[]" class="checkbox" value="<?= $no - 1 ?>"> <?= $no++ ?></td>
                                                    <td>
                                                        <div class="form-group d-flex justify-content-end">
                                                            <label for="tgl_keluar">Tanggal Keluar : <?= $today ?></label>
                                                            <input type="date" class="form-control" name="tgl_keluar[]" value="<?= $formated ?>" hidden>
                                                        </div>
                                                        <div class="form-group d-flex justify-content-center">
                                                            <label for="nama_cluster"><?= $data['nama_cluster'] ?></label>
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
                                                                    <label for=""> Lot:</label>
                                                                    <input type="text" class="form-control" name="lot_out[]" value="<?= $data['lot_kirim'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for=""> Kgs Keluar:</label>
                                                                    <input type="number" class="form-control" name="kgs_keluar[]" value="<?= $data['kgs_masuk'] ?>" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label for="">Cones Keluar:</label>
                                                                    <input type="number" class="form-control" name="cns_keluar[]" value="<?= $data['cns_masuk'] ?>" readonly>
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
                            <div class="d-flex align-items-center gap-4 mt-2">
                                <button type="submit" class="btn bg-gradient-info w-100">Out Data</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Form Input Pengeluaran Jalur Manual</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?= base_url($role . '/proses_pengeluaran_manual'); ?>" method="POST">
                    <div class="modal-body align-items-center">
                        <?php
                        $no = 1;
                        $today = date('d-M-Y');
                        $formated = trim(date('Y-m-d'));
                        ?>
                        <div class="form-group d-flex justify-content-end">
                            <label for="tgl">Tanggal Keluar : <?= $today ?></label>
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
                                    <input type="number" class="form-control" id="no_karung" name="no_karung" value="" required>
                                    <input type="hidden" id="id_out_celup" name="id_out_celup" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for=""> Kgs Keluar:</label>
                                    <input type="number" class="form-control" name="kgs_kirim" value="" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Cones Keluar:</label>
                                    <input type="number" class="form-control" name="cns_kirim" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Cluster</label>
                                    <input type="text" class="form-control cluster" name="cluster" value="" readonly>
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
                $.post("<?= base_url($role . '/hapus_pengeluaran') ?>", {
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
                    url: '<?= base_url($role . "/getItemTypeForOut") ?>/' + encodeURIComponent(noModel),
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

            console.log(noModel);
            console.log(itemType);

            if (noModel && itemType) {
                var url = `<?= base_url($role . "/getKodeWarnaForOut") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}`;

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

                var url = `<?= base_url($role . "/getWarnaDanLotForOut") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&kodeWarna=${kodeWarna}`;

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
                        url: `<?= base_url($role . "/getKgsCnsClusterForOut") ?>?noModel=${noModel}&itemType=${encodeURIComponent(itemType)}&kodeWarna=${kodeWarna}&lotKirim=${lotKirim}&noKarung=${noKarung}`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response dari server:", response); // Debug response
                            if (response.success) {
                                $('input[name="id_out_celup"]').val(response.id_out_celup);
                                $('input[name="kgs_kirim"]').val(response.kgs_kirim).trigger('change');
                                $('input[name="cns_kirim"]').val(response.cones_kirim);
                                $('.cluster').val(response.nama_cluster);
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
    </script>

    <?php $this->endSection(); ?>