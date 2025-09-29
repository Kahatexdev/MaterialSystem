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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <?php foreach ($uniqueData as $data): ?>
                    <div class="card-header">
                        <div class="card-header d-flex justify-content-between">
                            <h3 class="card-title">Form Edit Status Celup PO <?= implode(', ', $po) ?></h3>
                            <a href="<?= base_url($role . '/reqschedule') ?>" class="btn btn-secondary ms-auto">Back</a>
                        </div>
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="badge bg-info text-white">Tanggal Schedule : <?= $data['tgl_schedule'] ?> | Lot Urut : <?= $data['lot_urut'] ?> </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formUpdate" action="<?= base_url(session('role') . '/updateSchedule/' . $data['id_celup']) ?>" method="post">
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
                                        <input type="number" class="form-control" name="qty_po" id="qty_po" value="<?= number_format($data['qty_po'], 2, '.') ?>" disabled>
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
                                        <input type="number" class="form-control" name="qty_celup" id="qty_celup" value="<?= number_format($data['qty_celup'], 2, '.') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Qty Celup (+) -->
                                    <div class="form-group" id="qtyCelupPlusGroup">
                                        <label for="qty_celup">Qty Celup (+)</label>
                                        <input type="number" class="form-control" name="qty_celup_plus" id="qty_celup_plus`" value="<?= $data['qty_celup_plus'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Lot Celup -->
                                    <div class="form-group" id="lotCelupGroup">
                                        <label for="qty_celup">Lot Celup</label>
                                        <input type="text" class="form-control" name="lot_celup" id="lot_celup" value="<?= $data['lot_celup'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Bon -->
                                    <div class="form-group" id="tglBonGroup">
                                        <label for="tgl_bon">Tanggal Bon</label>
                                        <!-- input type datetime -->
                                        <input type="datetime-local" class="form-control" name="tgl_bon" id="tgl_bon" value="<?= $data['tgl_bon'] ?>">

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Celup -->
                                    <div class="form-group" id="tglCelupGroup">
                                        <label for="tgl_celup">Tanggal Celup</label>
                                        <input type="datetime-local" class="form-control" name="tgl_celup" id="tgl_celup" value="<?= $data['tgl_celup'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Bongkar -->
                                    <div class="form-group" id="tglBongkarGroup">
                                        <label for="tgl_bongkar">Tanggal Bongkar</label>
                                        <input type="datetime-local" class="form-control" name="tgl_bongkar" id="tgl_bongkar" value="<?= $data['tgl_bongkar'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Press -->
                                    <div class="form-group" id="tglPressGroup">
                                        <label for="tgl_press">Tanggal Press/Oven</label>
                                        <input type="datetime-local" class="form-control" name="tgl_press_oven" id="tgl_press_oven" value="<?= $data['tgl_press_oven'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal TL -->
                                    <div class="form-group" id="tglTLGroup">
                                        <label for="tgl_tl">Tanggal TL(Tes Luntur)</label>
                                        <input type="datetime-local" class="form-control" name="tgl_tl" id="tgl_tl" value="<?= $data['tgl_tl'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal TL -->
                                    <div class="form-group" id="tglTLGroup">
                                        <label for="tgl_tl">Tanggal Tes Lab</label>
                                        <input type="datetime-local" class="form-control" name="tgl_teslab" id="tgl_teslab" value="<?= $data['tgl_teslab'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Rajut Pagi -->
                                    <div class="form-group" id="tglRajutGroup">
                                        <label for="tgl_rajut">Tanggal Rajut Pagi</label>
                                        <input type="datetime-local" class="form-control" name="tgl_rajut_pagi" id="tgl_rajut_pagi" value="<?= $data['tgl_rajut_pagi'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Serah Terima ACC -->
                                    <div class="form-group" id="tglSTGroup">
                                        <label for="serah_terima">Serah Terima ACC</label>
                                        <input type="datetime-local" class="form-control" name="serah_terima_acc" id="serah_terima_acc" value="<?= $data['serah_terima_acc'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal ACC -->
                                    <div class="form-group" id="tglACCGroup">
                                        <label for="tgl_acc">Tanggal ACC KK</label>
                                        <input type="datetime-local" class="form-control" name="tgl_acc" id="tgl_acc" value="<?= $data['tgl_acc'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Reject -->
                                    <div class="form-group" id="tglRejectGroup">
                                        <label for="tgl_reject">Tanggal Reject KK</label>
                                        <input type="datetime-local" class="form-control" name="tgl_reject" id="tgl_reject" value="<?= $data['tgl_reject'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Matching -->
                                    <div class="form-group" id="tglMatchingGroup">
                                        <label for="tgl_matching">Tanggal Matching</label>
                                        <input type="datetime-local" class="form-control" name="tgl_matching" id="tgl_matching" value="<?= $data['tgl_matching'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Perbaikan -->
                                    <div class="form-group" id="tglPBGroup">
                                        <label for="tgl_pb">Tanggal Perbaikan</label>
                                        <input type="datetime-local" class="form-control" name="tgl_pb" id="tgl_pb" value="<?= $data['tgl_pb'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Tanggal Kelos -->
                                    <div class="form-group" id="tglKelosGroup">
                                        <label for="tgl_kelos">Tanggal Kelos</label>
                                        <input type="datetime-local" class="form-control" name="tgl_kelos" id="tgl_kelos" value="<?= $data['tgl_kelos'] ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Last update by -->
                                    <div class="form-group" id="lastUpdateGroup">
                                        <label for="last_update">Di Update Oleh</label>
                                        <input type="text" class="form-control" name="last_update" id="last_update" value="<?= $data['admin'] ?> (<?= $data['updated_at'] ?>)" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Ket Daily Cek -->
                                        <div class="form-group" id="ketDailyCekGroup">
                                            <label for="ket_daily_cek">Ket Daily Cek</label>
                                            <textarea name="ket_daily_cek" id="ket_daily_cek" class="form-control" disabled><?= $data['ket_daily_cek'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Ket Daily Cek -->
                                        <div class="form-group" id="ketSchedule">
                                            <label for="ket_schedule">Ket Schedule</label>
                                            <textarea name="ket_schedule" id="ket_schedule" class="form-control"><?= $data['ket_schedule'] ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-info w-100">Simpan</button>
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
    document.getElementById('formUpdate').addEventListener('submit', function(e) {
        const lot = document.getElementById('lot_celup').value.trim();

        if (lot === '') {
            e.preventDefault(); // hentikan submit
            Swal.fire({
                icon: 'warning',
                title: 'Lot Kosong',
                text: 'Lot Celup Belum Diisi',
                confirmButtonText: 'OK'
            });
        }
    });
</script>

<?php $this->endSection(); ?>