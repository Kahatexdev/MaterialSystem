<?php
// app/Views/{role}/pemesanan/partials/_sisa_kebutuhan_rows.php

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

    $prevKey      = null;
    $ttlKgPesan   = $ttlCnsPesan = $ttlKgOut = $ttlCnsOut = $ttlKgRetur = $ttlCnsRetur = $ttlKebTotal = $sisa = 0;
    $shownKebutuhan = [];

    foreach ($dataPemesanan as $key => $id) {
        $currentKey = $id['item_type'] . '|' . $id['kode_warna'] . '|' . $id['color'];

        if ($prevKey !== null && $currentKey !== $prevKey) { ?>
            <!-- baris total per group -->
            <tr style="font-weight:bold;background-color:#f0f0f0;">
                <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsPesan ?></th>
                <th></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsOut ?></th>
                <th></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsRetur ?></th>
                <th colspan="2"></th>
                <th class="text-uppercase text-xs font-weight-bolder text-center text-<?= $sisa < 0 ? 'danger' : 'success'; ?>"><?= number_format($sisa, 2) ?></th>
            </tr>
        <?php
            $ttlKgPesan = $ttlCnsPesan = $ttlKgOut = $ttlCnsOut = $ttlKgRetur = $ttlCnsRetur = $ttlKebTotal = $sisa = 0;
        }

        // hitung total2
        $ttlKgPesan   += $id['ttl_kg'];
        $ttlCnsPesan  += $id['ttl_cns'];
        $ttlKgOut     += $id['kg_out'];
        $ttlCnsOut    += $id['cns_out'];
        $ttlKgRetur   += $id['kgs_retur'];
        $ttlCnsRetur  += $id['cns_retur'];

        if (!isset($shownKebutuhan[$currentKey])) {
            $ttlKebTotal = $id['ttl_keb'];
            $shownKebutuhan[$currentKey] = true;
        }

        $sisa = $ttlKebTotal - $ttlKgOut + $ttlKgRetur;
        ?>

        <!-- baris detail -->
        <tr>
            <td class="text-xs text-center"><?= $id['tgl_pakai']; ?></td>
            <td class="text-xs text-center"><?= $id['tgl_retur']; ?></td>
            <td class="text-xs text-center"><?= $id['no_model']; ?></td>
            <td class="text-xs text-center"><?= $id['max_loss'] ?? ''; ?>%</td>
            <td class="text-xs text-center"><?= $id['item_type']; ?></td>
            <td class="text-xs text-center"><?= $id['kode_warna']; ?></td>
            <td class="text-xs text-center"><?= $id['color']; ?></td>
            <td></td>
            <td class="text-xs text-center"><?= number_format($id['ttl_kg'], 2) ?></td>
            <td class="text-xs text-center"><?= $id['ttl_cns']; ?></td>
            <td class="text-xs text-center"><?= $id['po_tambahan'] == 1 ? 'PO(+)' : ''; ?></td>
            <td class="text-xs text-center"><?= number_format($id['kg_out'], 2) ?></td>
            <td class="text-xs text-center"><?= $id['cns_out']; ?></td>
            <td class="text-xs text-center"><?= $id['lot_out']; ?></td>
            <td class="text-xs text-center"><?= number_format($id['kgs_retur'], 2) ?></td>
            <td class="text-xs text-center"><?= $id['cns_retur']; ?></td>
            <td class="text-xs text-center"><?= $id['lot_retur']; ?></td>
            <td class="text-xs text-center"><?= $id['ket_gbn']; ?></td>
            <td></td>
        </tr>
    <?php
        $prevKey = $currentKey;
    }

    if ($prevKey !== null) { ?>
        <!-- total terakhir -->
        <tr style="font-weight:bold;background-color:#f0f0f0;">
            <th colspan="7" class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Total Kebutuhan</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKebTotal, 2) ?></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgPesan, 2) ?></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsPesan ?></th>
            <th></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgOut, 2) ?></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsOut ?></th>
            <th></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= number_format($ttlKgRetur, 2) ?></th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center"><?= $ttlCnsRetur ?></th>
            <th colspan="2"></th>
            <th class="text-uppercase text-xs font-weight-bolder text-center text-<?= $sisa < 0 ? 'danger' : 'success'; ?>"><?= number_format($sisa, 2) ?></th>
        </tr>
    <?php }
}
