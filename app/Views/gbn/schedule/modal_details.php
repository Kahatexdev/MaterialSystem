<?php foreach ($scheduleDetails as $schedule): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="no_po" class="form-label">No. PO</label>
                <input type="text" class="form-control" id="no_po" value="<?= $schedule['no_po'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="item_type" class="form-label">Jenis Benang(Item Type)</label>
                <input type="text" class="form-control" id="item_type" value="<?= $schedule['item_type'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="kode_warna" class="form-label">Kode Warna</label>
                <input type="text" class="form-control" id="kode_warna" value="<?= $schedule['kode_warna'] ?>" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="warna" class="form-label">Warna</label>
                <input type="text" class="form-control" id="warna" value="<?= $schedule['warna'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="lot_celup" class="form-label">Lot Celup</label>
                <input type="text" class="form-control" id="lot_celup" value="<?= $schedule['lot_celup'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="tgl_celup" class="form-label">Tanggal Celup</label>
                <input type="text" class="form-control" id="tgl_celup" value="<?= $schedule['tanggal_celup'] ?>" readonly>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="kg_celup" class="form-label">Kg Celup</label>
                <input type="text" class="form-control" id="kg_celup" value="<?= $schedule['kg_celup'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="start_mc" class="form-label">Start MC</label>
                <input type="text" class="form-control" id="start_mc" value="<?= $schedule['start_mc'] ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="deliery" class="form-label">Delivery</label>
                <input type="text" class="form-control" id="deliery" value="" readonly>
            </div>
        </div>
    </div>
<?php endforeach; ?>