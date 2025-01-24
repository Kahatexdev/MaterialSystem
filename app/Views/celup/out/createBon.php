<?php $this->extend($role . '/out/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Input Bon dan Generate Barcode <?= $no_model ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url($role . '/outCelup/saveBon/' . $id_celup) ?>" method="post">
                            <div class="row">
                                <!-- Bon Celup -->
                                <div class="col-md-3">
                                    <input type="hidden" name="id_celup" value="<?= $id_celup ?>">
                                    <div class="mb-3">
                                        <label>Tanggal Datang</label>
                                        <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>LMD</label>
                                        <select name="l_m_d" id="l_m_~d" class="form-control" required>
                                            <option value="">Pilih LMD</option>
                                            <option value="L">L</option>
                                            <option value="M">M</option>
                                            <option value="D">D</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="harga">Harga</label>
                                        <input type="number" class="form-control" id="harga" name="harga" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label>GW</label>
                                        <input type="number" class="form-control" id="gw" name="gw" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label>NW</label>
                                        <input type="number" class="form-control" id="nw" name="nw" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label>Cones</label>
                                        <input type="number" class="form-control" id="cones" name="cones" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label>Karung</label>
                                        <input type="number" class="form-control" id="karung" name="karung" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label>No Surat Jalan</label>
                                        <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Detail Surat Jalan</label>
                                        <select class="form-control" name="detail_sj" id="detail_sj" required>
                                            <option value="">Pilih Surat Jalan</option>
                                            <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                            <option value="IMPOR DARI KOREA">IMPOR DARI KOREA</option>
                                            <option value="JS MISTY">JS MISTY</option>
                                            <option value="JS SOLID">JS SOLID</option>
                                            <option value="KHTEX">KHTEX</option>
                                            <option value="PO PLUS">PO(+)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>
                                            Ganti Retur
                                        </label>
                                        <select class="form-control" name="ganti_retur" id="ganti_retur">
                                            <option value="">Pilih</option>
                                            <option value="1">Ya</option>
                                            <option value="0">Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label>Admin</label> -->
                                <input type="hidden" class="form-control" id="admin" name="admin" required>
                            </div>
                            <!-- Out Celup -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>GW Kirim</label>
                                        <input type="number" class="form-control" id="gw_kirim" name="gw_kirim" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Kgs Kirim</label>
                                        <input type="number" class="form-control" id="kgs_kirim" name="kgs_kirim" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Cones Kirim</label>
                                        <input type="number" class="form-control" id="cones_kirim" name="cones_kirim" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label>Lot Kirim</label>
                                        <input type="text" class="form-control" id="lot_kirim" name="lot_kirim" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" style="width: 100%;">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>