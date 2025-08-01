<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Success!",
                html: '<?= session()->getFlashdata('success') ?>',
                icon: 'success',
                width: 600,
                padding: "3em",
            });
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                title: "Error!",
                html: '<?= session()->getFlashdata('error') ?>',
                icon: 'error',
                width: 600,
                padding: "3em",
            });
        });
    </script>
<?php endif; ?>

<style>
    .table {
        border-radius: 15px;
        border-collapse: separate;
        border-spacing: 0;
        overflow: auto;
        position: relative;
    }

    .table th {
        background-color: rgb(8, 38, 83);
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgb(255, 255, 255);
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: rgb(237, 237, 237);
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: rgb(4, 55, 91);
    }

    .table td.sticky {
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #e3f2fd;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="card card-frame">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bolder">Detail Pemesanan <?= esc($listPemesanan[0]['jenis'] ?? '') ?></h5>
        </div>
    </div>
</div>

<!-- Tabel Data -->
<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="dataTable">
                <thead>
                    <tr>
                        <th class="sticky text-center">No</th>
                        <th class="sticky text-center">Tanggal Pakai</th>
                        <th class="sticky text-center">Item Type</th>
                        <th class="sticky text-center">Warna</th>
                        <th class="sticky text-center">Kode Warna</th>
                        <th class="sticky text-center">No Model</th>
                        <th class="sticky text-center">Jalan MC</th>
                        <th class="sticky text-center">Total Pesan (Kg)</th>
                        <th class="sticky text-center">Cones</th>
                        <th class="sticky text-center">Area</th>
                        <th class="sticky text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($listPemesanan as $list) : ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= esc($list['tgl_pakai']); ?></td>
                            <td class="text-center"><?= esc($list['item_type']); ?></td>
                            <td class="text-center"><?= esc($list['color']); ?></td>
                            <td class="text-center"><?= esc($list['kode_warna']); ?></td>
                            <td class="text-center"><?= esc($list['no_model']); ?></td>
                            <td class="text-center"><?= esc($list['jl_mc']); ?></td>
                            <td class="text-center"><?= number_format($list['total_pesan'], 2); ?></td>
                            <td class="text-center"><?= esc($list['total_cones']); ?></td>
                            <td class="text-center"><?= esc($list['admin']); ?></td>
                            <td class="text-center">
                                <?php if ($list['button'] === 'disable') : ?>
                                    <button type="button" class="btn bg-gradient-secondary" disabled>
                                        <i class="fas fa-box"></i> Stock Terkirim
                                    </button>
                                <?php else : ?>
                                    <button
                                        type="button"
                                        class="btn bg-gradient-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= esc($list['id_psk']) ?>">
                                        <i class="fas fa-paper-plane"></i> Kirim Stock
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Pemesanan -->
<?php foreach ($listPemesanan as $list) : ?>
    <div
        class="modal fade"
        id="editModal<?= esc($list['id_psk']) ?>"
        tabindex="-1"
        aria-labelledby="editModalLabel<?= esc($list['id_psk']) ?>"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="editModalLabel<?= esc($list['id_psk']) ?>">
                        Detail Pemesanan U/ Pengeluaran Barang
                    </h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="<?= base_url("$role/updatePemesanan/{$list['id_psk']}") ?>" method="POST">
                    <?= csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jalan MC</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= esc($list['jl_mc']) ?>"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Area</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= esc($list['admin']) ?>"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pakai</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    value="<?= esc($list['tgl_pakai']) ?>"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis</label>
                                <!-- Ganti id="itemtype" jadi class="itemtype" -->
                                <select
                                    class="form-select itemtype"
                                    name="itemtype"
                                    required>
                                    <option value="" disabled selected>Pilih Jenis</option>
                                    <?php foreach ($optionDataJenis as $ojenis) : ?>
                                        <option
                                            value="<?= esc($ojenis) ?>"
                                            <?= ($list['jenis'] === $ojenis) ? 'selected' : '' ?>>
                                            <?= esc($ojenis) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code</label>
                                <!-- Ganti id="kodeWarna" jadi class="kode-warna" -->
                                <select
                                    class="form-select kode-warna"
                                    name="kode_warna"
                                    required>
                                    <option value="" disabled selected>Pilih Kode Warna</option>
                                    <!-- Opsi akan di-*populate* lewat AJAX -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Warna</label>
                                <!-- Ganti id="colour" jadi class="colour" -->
                                <select
                                    class="form-select colour"
                                    name="color"
                                    required>
                                    <option value="" disabled selected>Pilih Warna</option>
                                    <!-- Opsi akan di-*populate* lewat AJAX -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Cones</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="total_cones"
                                    value="<?= esc($list['total_cones']) ?>"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pesan (Kg)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    name="total_pesan"
                                    value="<?= number_format($list['total_pesan'], 2) ?>"
                                    required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea
                                    class="form-control"
                                    name="keterangan"
                                    rows="3"
                                    placeholder="Masukkan keterangan"
                                    required><?= esc($list['keterangan'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Hidden values for reference -->
                        <input type="hidden" name="id_psk" value="<?= esc($list['id_psk']) ?>">
                        <input type="hidden" name="jenis" value="<?= esc($list['jenis']) ?>">
                        <input type="hidden" name="tgl_pakai" value="<?= esc($list['tgl_pakai']) ?>">
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            Tutup
                        </button>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Script AJAX untuk setiap modal -->
<script>
    $(document).ready(function() {
        // Ketika .itemtype (jenis) berubah → ambil kode warna
        $('.itemtype').on('change', function() {
            let $thisModal = $(this).closest('.modal'); // Cari modal terdekat
            let itemType = $(this).val();
            let $kodeWarnaSelect = $thisModal.find('select.kode-warna');
            let $colourSelect = $thisModal.find('select.colour');

            // Kosongkan dulu semua opsi di kode-warna dan colour
            $kodeWarnaSelect.html('<option value="" disabled selected>Pilih Kode Warna</option>');
            $colourSelect.html('<option value="" disabled selected>Pilih Warna</option>');

            console.log("Item Type:", itemType);
            // AJAX untuk mendapatkan list kode warna dari controller
            $.ajax({
            url: '<?= base_url("$role/getCodePemesanan") ?>',
                type: 'GET',
                data: {
                    item_type: itemType
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Response:", response);
                    // response diasumsikan bentuk: [ { "code": "001" }, { "code": "002" }, … ]
                    $.each(response, function(index, valueObj) {
                        $kodeWarnaSelect.append(
                            '<option value="' + valueObj.code + '">' + valueObj.code + '</option>'
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error getCodePemesanan:", error);
                }
            });
        });

        // Ketika .kode-warna berubah → ambil daftar warna dari controller
        $('.kode-warna').on('change', function() {
            let $thisModal = $(this).closest('.modal');
            let itemType = $thisModal.find('select.itemtype').val();
            let kodeWarna = $(this).val();
            let $colourSelect = $thisModal.find('select.colour');

            // Bersihkan dulu
            $colourSelect.html('<option value="" disabled selected>Pilih Warna</option>');

            // AJAX untuk mendapatkan list warna berdasarkan jenis & kode warna
            $.ajax({
                url: '<?= base_url("$role/getColorPemesanan") ?>',
                type: 'GET',
                data: {
                    item_type: itemType,
                    kode_warna: kodeWarna
                },
                dataType: 'json',
                success: function(response) {
                    // response diasumsikan bentuk: [ { "color": "Merah" }, { "color": "Biru" }, … ]
                    $.each(response, function(index, valueObj) {
                        $colourSelect.append(
                            '<option value="' + valueObj.color + '">' + valueObj.color + '</option>'
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error getColorPemesanan:", error);
                }
            });
        });
    });
</script>

<?php $this->endSection(); ?>