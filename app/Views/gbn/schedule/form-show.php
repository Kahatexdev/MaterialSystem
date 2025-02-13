<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <?php foreach ($uniqueData as $data): ?>
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">Status Celup PO <?= implode(', ', $po) ?></h3>
                            <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $data['tgl_schedule'] ?> | Lot Urut : <?= $data['lot_urut'] ?> </h6>
                        </div>
                        <a href="<?= base_url($role . '/schedule/reqschedule') ?>" class="btn btn-secondary h-50">Back</a>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <!-- No Mesin -->
                                    <div class="form-group" id="noMesinGroup">
                                        <label for="no_mesin" class="form-label">No Mesin</label>
                                        <input type="text" class="form-control" id="no_mesin" name="no_mesin" value="<?= $data['no_mesin'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Jenis Benang -->
                                    <div class="form-group" id="jenisGroup">
                                        <label for="jenis" class="form-label">Jenis Benang</label>
                                        <input type="text" class="form-control" name="jenis" id="jenis" value="<?= $data['item_type'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Kode Warna -->
                                    <div class="form-group" id="kodeWarnaGroup">
                                        <label for="kode_warna" class="form-label">Kode Warna</label>
                                        <input type="text" class="form-control" name="kode_warna" id="kode_warna" value="<?= $data['kode_warna'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Warna -->
                                    <div class="form-group" id="warnaGroup">
                                        <label for="warna" class="form-label">Warna</label>
                                        <input type="text" class="form-control" name="warna" id="warna" value="<?= $data['warna'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Start MC -->
                                    <div class="form-group" id="startMcGroup">
                                        <label for="start_mc">Start MC</label>
                                        <input type="date" class="form-control" name="start_mc" id="start_mc" value="<?= $data['start_mc'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Delivery Export Awal -->
                                    <div class="form-group" id="deliveryAwalGroup">
                                        <label for="delivery_awal">Delivery Export Awal</label>
                                        <input type="date" class="form-control" name="delivery_awal" id="delivery_awal" value="<?= $data['del_awal'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Delivery Export Akhir -->
                                    <div class="form-group" id="deliveryAkhirGroup">
                                        <label for="delivery_awal">Delivery Export Akhir</label>
                                        <input type="date" class="form-control" name="delivery_akhir" id="delivery_akhir" value="<?= $data['del_akhir'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Schedule -->
                                    <div class="form-group" id="tglScheduleGroup">
                                        <label for="tanggal_schedule">Tanggal Schedule</label>
                                        <input type="date" class="form-control" name="tanggal_schedule" id="tgl_schedule" value="<?= $data['tgl_schedule'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Qty PO -->
                                    <div class="form-group" id="qtyPOGroup">
                                        <label for="qty_po">Qty PO</label>
                                        <input type="number" class="form-control" name="qty_po" id="qty_po" value="<?= $data['qty_po'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Qty PO (+) -->
                                    <div class="form-group" id="qtyPOPlusGroup">
                                        <label for="qty_po_plus">Qty PO (+)</label>
                                        <input type="number" class="form-control" name="qty_po_plus" id="qty_po_plus" value="<?= $data['qty_po_plus'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Qty Celup -->
                                    <div class="form-group" id="qtyCelupGroup">
                                        <label for="qty_celup">Qty Celup</label>
                                        <input type="number" class="form-control" name="qty_celup" id="qty_celup" value="<?= $data['qty_celup'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Qty Celup (+) -->
                                    <div class="form-group" id="qtyCelupPlusGroup">
                                        <label for="qty_celup">Qty Celup (+)</label>
                                        <input type="number" class="form-control" name="qty_celup_plus" id="qty_celup_plus`" value="<?= $data['qty_celup_plus'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Lot Celup -->
                                    <div class="form-group" id="lotCelupGroup">
                                        <label for="qty_celup">Lot Celup</label>
                                        <input type="text" class="form-control" name="lot_celup" id="lot_celup" value="<?= $data['lot_celup'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Bon -->
                                    <div class="form-group" id="tglBonGroup">
                                        <label for="tgl_bon">Tanggal Bon</label>
                                        <input type="datetime-local" class="form-control" name="tgl_bon" id="tgl_bon" value="<?= $data['tgl_bon'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Celup -->
                                    <div class="form-group" id="tglCelupGroup">
                                        <label for="tgl_celup">Tanggal Celup</label>
                                        <input type="datetime-local" class="form-control" name="tgl_celup" id="tgl_celup" value="<?= $data['tgl_celup'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Bongkar -->
                                    <div class="form-group" id="tglBongkarGroup">
                                        <label for="tgl_bongkar">Tanggal Bongkar</label>
                                        <input type="datetime-local" class="form-control" name="tgl_bongkar" id="tgl_bongkar" value="<?= $data['tgl_bongkar'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Press -->
                                    <div class="form-group" id="tglPressGroup">
                                        <label for="tgl_press">Tanggal Press</label>
                                        <input type="datetime-local" class="form-control" name="tgl_press" id="tgl_press" value="<?= $data['tgl_press'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Oven -->
                                    <div class="form-group" id="tglOvenGroup">
                                        <label for="tgl_oven">Tanggal Oven</label>
                                        <input type="datetime-local" class="form-control" name="tgl_oven" id="tgl_oven" value="<?= $data['tgl_oven'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal TL -->
                                    <div class="form-group" id="tglTLGroup">
                                        <label for="tgl_tl">Tanggal TL</label>
                                        <input type="datetime-local" class="form-control" name="tgl_tl" id="tgl_tl" value="<?= $data['tgl_tl'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Rajut Pagi -->
                                    <div class="form-group" id="tglRajutGroup">
                                        <label for="tgl_rajut">Tanggal Rajut Pagi</label>
                                        <input type="datetime-local" class="form-control" name="tgl_rajut_pagi" id="tgl_rajut_pagi" value="<?= $data['tgl_rajut_pagi'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal ACC -->
                                    <div class="form-group" id="tglACCGroup">
                                        <label for="tgl_acc">Tanggal ACC</label>
                                        <input type="datetime-local" class="form-control" name="tgl_acc" id="tgl_acc" value="<?= $data['tgl_acc'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Kelos -->
                                    <div class="form-group" id="tglKelosGroup">
                                        <label for="tgl_kelos">Tanggal Kelos</label>
                                        <input type="datetime-local" class="form-control" name="tgl_kelos" id="tgl_kelos" value="<?= $data['tgl_kelos'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Reject -->
                                    <div class="form-group" id="tglRejectGroup">
                                        <label for="tgl_reject">Tanggal Reject</label>
                                        <input type="datetime-local" class="form-control" name="tgl_reject" id="tgl_reject" value="<?= $data['tgl_reject'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Perbaikan -->
                                    <div class="form-group" id="tglPBGroup">
                                        <label for="tgl_pb">Tanggal Perbaikan</label>
                                        <input type="datetime-local" class="form-control" name="tgl_pb" id="tgl_pb" value="<?= $data['tgl_pb'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <!-- Ket Daily Cek -->
                                    <div class="form-group" id="ketDailyCekGroup">
                                        <label for="ket_daily_cek">Ket Daily Cek</label>
                                        <input type="text" class="form-control" name="ket_daily_cek" id="ket_daily_cek" value="<?= $data['ket_daily_cek'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>

</script>

<?php $this->endSection(); ?>