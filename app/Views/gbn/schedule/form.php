<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Input Schedule Celup</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <form action="<?= base_url('/schedule/save') ?>" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- No Mesin -->
                                    <div class="mb-3">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $no_mesin ?>" readonly>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Tanggal Schedule -->
                                    <div class="mb-3">
                                        <label for="tanggal_schedule" class="form-label">Tanggal Schedule</label>
                                        <input type="date" class="form-control" id="tanggal_schedule" name="tanggal_schedule" readonly value="<?= $tanggal_schedule ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Lot Urut -->
                                    <div class="mb-3">
                                        <label for="lot_urut" class="form-label">Lot Urut</label>
                                        <input type="number" class="form-control" id="lot_urut" name="lot_urut" value="<?= $lot_urut ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Jenis Bahan baku -->
                                    <div class="mb-3">
                                        <label for="jenis_bahan_baku" class="form-label">Jenis Bahan Baku</label>
                                        <select class="form-select" id="jenis_bahan_baku" name="jenis_bahan_baku" required>
                                            <option value="">Pilih Jenis Bahan Baku</option>
                                            <?php foreach ($jenis_bahan_baku as $jenis): ?>
                                                <option value="<?= $jenis['jenis'] ?>"><?= $jenis['jenis'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Item Type -->
                                    <div class="mb-3">
                                        <label for="item_type" class="form-label">Item Type</label>
                                        <select class="form-select" id="item_type" name="item_type" required>
                                            <option value="">Pilih Item Type</option>
                                            <?php foreach ($item_type as $item): ?>
                                                <option value="<?= $item['item_type'] ?>"><?= $item['item_type'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Kode Warna -->
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Kode Warna -->
                                    <div class="mb-3">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" id="kode_warna" name="kode_warna" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Warna -->
                                    <div class="mb-3">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" id="warna" name="warna" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Tanggal Mulai Celup -->
                                    <div class="mb-3">
                                        <label for="start_mc" class="form-label">Tanggal Mulai Celup</label>
                                        <input type="date" class="form-control" id="start_mc" name="start_mc" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Berat Celup -->
                                    <div class="mb-3">
                                        <label for="kg_celup" class="form-label">Berat Celup (kg)</label>
                                        <input type="number" class="form-control" id="kg_celup" name="kg_celup" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Lot Celup -->
                                    <div class="mb-3">
                                        <label for="lot_celup" class="form-label">Lot Celup</label>
                                        <input type="text" class="form-control" id="lot_celup" name="lot_celup" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Tanggal Bon -->
                                    <div class="mb-3">
                                        <label for="tanggal_bon" class="form-label">Tanggal Bon</label>
                                        <input type="date" class="form-control" id="tanggal_bon" name="tanggal_bon">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Tanggal Celup -->
                                    <div class="mb-3">
                                        <label for="tanggal_celup" class="form-label">Tanggal Celup</label>
                                        <input type="date" class="form-control" id="tanggal_celup" name="tanggal_celup">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Status Akhir -->
                                    <div class="mb-3">
                                        <label for="last_status" class="form-label">Status Akhir</label>
                                        <input type="text" class="form-control" id="last_status" name="last_status" maxlength="32" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <!-- Keterangan Daily Check -->
                                    <div class="mb-3">
                                        <label for="ket_daily_cek" class="form-label">Keterangan Daily Check</label>
                                        <input type="text" class="form-control" id="ket_daily_cek" name="ket_daily_cek" maxlength="32">
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>