<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
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
                                            <option value="">Pilih Detail Surat Jalan</option> <!-- Opsi default -->
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
                <?php if (!empty($item)): ?>
                    <?php foreach ($item as $index => $data): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h2><?= esc($data['model']); ?></h2>
                            </div>
                            <div class="card-body">
                                <div class="row mt-2">
                                    <div class="col-4">
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
                                    <div class="col-md-1">
                                        <div class="row">
                                            <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                            <div class="col-md-4">
                                                <label>
                                                    <input type="hidden" name="ganti_retur[<?= $index ?>]" value="0">
                                                    <input type="checkbox" name="ganti_retur[<?= $index ?>]" id="ganti_retur" value="1"
                                                        <?= isset($data['ganti_retur']) && $data['ganti_retur'] == 1 ? 'checked' : '' ?>>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($data['karung'])): ?>
                                    <?php foreach ($data['karung'] as $karungIndex => $karung): ?>
                                        <div class="row mt-2  p-2">
                                            <div class="col-4">
                                                <input type="hidden" name="id_out_celup[<?= $index ?>][]" value="<?= $karung['id_out_celup'] ?>">
                                                <label for=""> No Karung</label>
                                                <input type="number" name="no_karung[<?= $index ?>][]" id="" value="<?= $karung['no_karung'] ?>" class="form-control" readonly>
                                            </div>
                                            <div class="col-4">
                                                <label for="">GW Kirim</label>
                                                <input type="float" name="gw_kirim[<?= $index ?>][]" id="" value="<?= $karung['gw_kirim'] ?>" class="form-control">
                                            </div>
                                            <div class="col-4">
                                                <label for="">Kgs Kirim</label>
                                                <input type="float" name="kgs_kirim[<?= $index ?>][]" value="<?= esc($karung['kgs_kirim']) ?>" class="form-control">
                                            </div>
                                            <div class="col-4">
                                                <label for="">Cones Kirim</label>
                                                <input type="float" name="cones_kirim[<?= $index ?>][]" id="" value="<?= $karung['cones_kirim'] ?>" class="form-control">
                                            </div>
                                            <div class="col-4">
                                                <label for="">Lot Kirim</label>
                                                <input type="text" name="lot_kirim[<?= $index ?>][]" id="" value="<?= $karung['lot_kirim'] ?>" class="form-control">
                                            </div>

                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada data yang tersedia.</p>
                <?php endif; ?>

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-info w-100">Save</button>
                    </div>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>
</div>

<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php $this->endSection(); ?>