<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">

<div class="container-fluid py-4">
    <!-- Flash Messages -->
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

    <div class="container-fluid">

        <!-- Header Form -->
        <div class="card">
            <div class="card-header">
                <h3>Form Edit Bon Pengiriman</h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url($role . '/outCelup/updateBon/' . $bon['id_bon']) ?>" method="post">
                    <div id="kebutuhan-container">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <input type="hidden" name="id_bon" value="<?= esc($bon['id_bon']) ?>">
                                <label>Detail Surat Jalan</label>
                                <select class="form-control" name="detail_sj" id="detail_sj">
                                    <option value="">Pilih Detail Surat Jalan</option>
                                    <?php
                                    $options = [
                                        "COVER MAJALAYA",
                                        "IMPORT DARI KOREA",
                                        "JS MISTY",
                                        "JS SOLID",
                                        "KHTEX",
                                        "PO(+)"
                                    ];
                                    foreach ($options as $option) :
                                    ?>
                                        <option value="<?= esc($option) ?>" <?= ($bon['detail_sj'] == $option) ? 'selected' : '' ?>>
                                            <?= esc($option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>No Surat Jalan</label>
                                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" value="<?= $bon['no_surat_jalan'] ?>">
                            </div>
                            <div class="col-md-4">
                                <label>Tanggal Kirim</label>
                                <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" value="<?= $bon['tgl_datang'] ?>">
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Data Item Loop -->
        <?php if (!empty($item)): ?>
            <?php foreach ($item as $index => $data): ?>
                <div class="card mt-3">
                    <div class="card-header bg-dark">
                        <h4 class="mb-0 text-white"><?= esc($data['model']); ?></h4>
                    </div>
                    <div class="card-body">
                        <!-- Item Information -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="">Item Type</label>
                                <input type="text" class="form-control" name="itemType[<?= $index ?>]" value="<?= esc($data['itemType']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="">Kode Warna</label>
                                <input type="text" class="form-control" name="kodeWarna[<?= $index ?>]" value="<?= esc($data['kodeWarna']) ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="">Warna</label>
                                <input type="text" class="form-control" name="warna[<?= $index ?>]" value="<?= esc($data['warna']) ?>" readonly>
                            </div>
                        </div>

                        <!-- Item Settings -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>LMD</label>
                                <select class="form-control" name="l_m_d[<?= $index ?>]" id="l_m_d" required>
                                    <option value="">Pilih LMD</option>
                                    <?php
                                    $options = ["L", "M", "D"];
                                    foreach ($options as $option) :
                                    ?>
                                        <option value="<?= esc($option) ?>" <?= ($data['l_m_d'] == $option) ? 'selected' : '' ?>>
                                            <?= esc($option) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Harga</label>
                                <input type="float" class="form-control" name="harga[<?= $index ?>]" id="harga" value="<?= $data['harga'] ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="ganti-retur">Ganti Retur</label>
                                <div class="form-check mt-2">
                                    <input type="hidden" name="ganti_retur[<?= $index ?>]" value="0">
                                    <input type="checkbox" class="form-check-input" name="ganti_retur[<?= $index ?>]" id="ganti_retur_<?= $index ?>" value="1"
                                        <?= isset($data['ganti_retur']) && $data['ganti_retur'] == 1 ? 'checked' : '' ?>>
                                </div>
                            </div>
                        </div>

                        <!-- Karung Data -->
                        <?php if (!empty($data['karung'])): ?>
                            <hr>
                            <h5 class="mb-3">Data Karung</h5>
                            <?php foreach ($data['karung'] as $karungIndex => $karung): ?>
                                <!-- Tambahkan class karung-row dan id unik untuk setiap karung -->
                                <div class="row mb-3 ms-1 me-1 p-3 border rounded bg-light karung-row" id="karung-row-<?= $karung['id_out_celup'] ?>">
                                    <div class="col-12 mb-2">
                                        <small class="text-muted">Karung #<?= $karungIndex + 1 ?></small>
                                    </div>

                                    <input type="hidden" name="id_out_celup[<?= $index ?>][]" value="<?= $karung['id_out_celup'] ?>">

                                    <div class="col-md-2 mb-2">
                                        <label for="">No Karung</label>
                                        <input type="number" name="no_karung[<?= $index ?>][]" value="<?= $karung['no_karung'] ?>" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="">GW Kirim</label>
                                        <input type="text" name="gw_kirim[<?= $index ?>][]" value="<?= $karung['gw_kirim'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="">Kgs Kirim</label>
                                        <input type="text" name="kgs_kirim[<?= $index ?>][]" value="<?= esc($karung['kgs_kirim']) ?>" class="form-control">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="">Cones Kirim</label>
                                        <input type="text" name="cones_kirim[<?= $index ?>][]" value="<?= $karung['cones_kirim'] ?>" class="form-control">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label for="">Lot Kirim</label>
                                        <input type="text" name="lot_kirim[<?= $index ?>][]" value="<?= $karung['lot_kirim'] ?>" class="form-control">
                                    </div>

                                    <?php $karungCount = is_array($data['karung']) ? count($data['karung']) : 0; ?>
                                    <?php if ($karungCount > 1): ?>
                                        <div class="col-md-2 mb-2 text-center">
                                            <label for="">Aksi</label><br>
                                            <!-- tombol tanpa id duplikat, pakai class .btn-delete -->
                                            <button type="button"
                                                class="btn btn-danger btn-md w-100 btn-delete"
                                                data-id="<?= $karung['id_out_celup'] ?>"
                                                data-id-bon="<?= $karung['id_bon'] ?>"
                                                data-no="<?= esc($karung['no_karung']) ?>">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-2 mb-2 text-center"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card mt-3">
                <div class="card-body text-center">
                    <p class="mb-0">Tidak ada data yang tersedia.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Submit Button -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center">
                    <button type="submit" class="btn btn-info btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
        </form>

    </div>
</div>

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Pastikan jQuery & Swal sudah ter-include sebelum ini script dijalankan.

    function updateRemoveButtons() {
        // Untuk setiap card (atau container item) cek berapa .karung-row di dalamnya
        document.querySelectorAll('.card').forEach(function(card) {
            const karungs = card.querySelectorAll('.karung-row');
            const deleteButtons = card.querySelectorAll('.btn-delete');

            if (karungs.length <= 1) {
                // sembunyikan tombol hapus
                deleteButtons.forEach(btn => btn.style.display = 'none');
            } else {
                // tampilkan tombol hapus
                deleteButtons.forEach(btn => btn.style.display = '');
            }
        });
    }

    // Jalankan saat DOM ready (jQuery)
    $(document).ready(function() {
        updateRemoveButtons();
    });

    // Delegated event handler: lebih aman daripada mengikat ke id yang duplikat
    $(document).on('click', '.btn-delete', function() {
        const idOut = $(this).data('id');
        const idBon = $(this).data('id-bon');
        const noKarung = $(this).data('no') || '';
        const confirmMsg = noKarung ? `Yakin hapus karung #${noKarung}?` : 'Yakin hapus karung ini?';

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
                // redirect ke controller delete (GET)
                window.location = '<?= base_url($role . "/outCelup/deleteKarung") ?>/' + idOut + '?id_bon=' + encodeURIComponent(idBon);
            }
        });
    });
</script>


<?php $this->endSection(); ?>