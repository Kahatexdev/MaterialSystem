<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Sisa Kebutuhan Area</p>
                            <h5 class="font-weight-bolder mb-0">
                                Data Sisa Kebutuhan Area
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_area" class="form-label">Area</label>
                            <select class="form-control" name="filter_area" id="filter_area" required>
                                <option value="">Pilih Area</option>
                                <?php foreach ($allArea as $ar) {
                                ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control" placeholder="No Model" required>
                        </div>
                        <!-- Tombol Filter -->
                        <div class="col-md-2">
                            <label class="form-label d-block invisible">Filter</label>
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100">
                                <i class="fas fa-filter"></i> FILTER
                            </button>
                        </div>

                        <!-- Tombol Refresh -->
                        <div class="col-md-1">
                            <label class="form-label d-block invisible">Refresh</label>
                            <button type="button" class="btn btn-secondary w-100"
                                onclick="window.location.href='<?= base_url($role . 'pemesanan/sisaKebutuhanArea') ?>'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table align-items-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">NO MODEL</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOS</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">ITEM TYPE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KODE WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TOTAL KEBUTUHAN AREA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY PESAN (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">PO TAMBAHAN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY KIRIM (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY RETUR (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KET GBN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">SISA (KIRIM - KEBUTUHAN - RETUR)</th>
                                </tr>
                            </thead>
                            <tbody id="sisaKebutuhanTable">
                                <?php
                                if (empty($dataPemesanan) && !empty($area) && !empty($noModel)) { ?>
                                    <tr>
                                        <th colspan="16">Tidak Ada Data</th>
                                    </tr>
                                <?php
                                } elseif (empty($dataPemesanan) && empty($area) && empty($noModel)) { ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Silakan pilih area dan isi no model untuk menampilkan data.</td>
                                    </tr>
                                    <?php
                                } elseif (!empty($dataPemesanan) && !empty($area) && !empty($noModel)) {

                                    $prevKey = null;
                                    $ttlKgPesan = 0;
                                    $ttlKgOut = 0;
                                    $ttlKgRetur = 0;
                                    $ttlKebTotal = 0;
                                    $sisa = 0;

                                    foreach ($dataPemesanan as $key => $id) {
                                        // Buat key unik untuk kombinasi
                                        $currentKey = $id['item_type'] . '|' . $id['kode_warna'] . '|' . $id['color'];

                                        if ($prevKey !== null && $currentKey !== $prevKey) {
                                    ?>
                                            <tr style="font-weight: bold; background-color: #f0f0f0;">
                                                <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                                <th></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                                <th colspan="2"></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                            </tr>
                                        <?php
                                            // Reset total untuk grup berikutnya
                                            $ttlKgPesan = 0;
                                            $ttlKgOut = 0;
                                            $ttlKgRetur = 0;
                                            $ttlKebTotal = 0;
                                            $sisa = 0;
                                        }
                                        // Hitung total sementara
                                        $ttlKgPesan += floatval($id['ttl_kg']);
                                        $ttlKgOut += floatval($id['kg_out']);
                                        $ttlKgRetur += floatval($id['kgs_retur']);
                                        // Ambil ttl_keb satu kali per grup
                                        if (!isset($shownKebutuhan[$currentKey])) {
                                            $ttlKebTotal = floatval($id['ttl_keb']); // Ambil hanya sekali
                                            $shownKebutuhan[$currentKey] = true;
                                        }
                                        $sisa = $ttlKebTotal - $ttlKgOut + $ttlKgRetur;
                                        if ($sisa < 0) {
                                            $color = "red";
                                        } else {
                                            $color = "green";
                                        }
                                        ?>
                                        <tr>
                                            <td class="text-xs text-center"><?= $id['tgl_pakai']; ?></td>
                                            <td class="text-xs text-center"><?= $id['tgl_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['no_model']; ?></td>
                                            <td class="text-xs text-center"><?= $id['max_loss'] ?? ''; ?></td>
                                            <td class="text-xs text-center"><?= $id['item_type']; ?></td>
                                            <td class="text-xs text-center"><?= $id['kode_warna']; ?></td>
                                            <td class="text-xs text-center"><?= $id['color']; ?></td>
                                            <td></td>
                                            <td class="text-xs text-center"><?= number_format($id['ttl_kg'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['po_tambahan'] == 1 ? 'YA' : ''; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kg_out'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_out']; ?></td>
                                            <td class="text-xs text-center"><?= number_format($id['kgs_retur'], 2) ?></td>
                                            <td class="text-xs text-center"><?= $id['lot_retur']; ?></td>
                                            <td class="text-xs text-center"><?= $id['ket_gbn']; ?></td>
                                            <td></td>
                                        </tr>
                                    <?php
                                        $prevKey = $currentKey;
                                    }

                                    // Tampilkan total untuk grup terakhir
                                    if ($prevKey !== null) {
                                    ?>
                                        <tr style="font-weight: bold; background-color: #f0f0f0;">
                                            <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                                            <th></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                                            <th colspan="2"></th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" style="color: <?= $color; ?>"><?= number_format($sisa, 2) ?></th>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //Filter data pemesanan
    document.getElementById('filterButton').addEventListener('click', function() {
        const filterArea = document.getElementById('filter_area').value.trim();
        const filterModel = document.getElementById('filter_model').value.trim();

        // Validasi input
        if (!filterArea || !filterModel) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Area dan No Model harus diisi!',
                confirmButtonText: 'OK',
            });
            return; // Hentikan eksekusi jika input kosong
        }

        // Redirect ke controller dengan parameter
        let url = '<?= base_url($role . '/pemesanan/sisaKebutuhanArea') ?>?filter_model=' + encodeURIComponent(filterModel) + '&filter_area=' + encodeURIComponent(filterArea);
        window.location.href = url;
    });
</script>

<?php $this->endSection(); ?>