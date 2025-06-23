<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Barcode Label PT. KAHATEX</title>
    <style>
        @page {
            size: 10cm 5cm;
            margin: 0;
        }
    </style>
</head>

<?php foreach ($detailBon as $i => $row): ?>

    <body>
        <table style="width: 9.9cm; height:4.7cm; border-collapse: collapse; border: 1px solid #000; margin:auto; margin-top:10px;">
            <tr style=" border: 1px solid #000;">
                <td colspan="2" class="header" style="text-align: center; font-size: 15px; font-weight: bold; color: #013182;">
                    <img src="<?= $img ?>" alt="" width="15" style="margin-top:5px;">
                    PT. KAHATEX
                </td>
            </tr>
            <tr>
                <td style="width: 3cm; vertical-align: top;">
                    <div class="label-box" style="padding: 5px;">
                        <div class="barcode-box" style="text-align: center; font-size: 11px;">
                            <img src="<?= $barcodeImages[$i] ?>" alt="barcode" style="max-width: 100%; height: 40px;">
                            <div style="margin-top:5px;">No Model : <?= $row['no_model'] ?? '-' ?></div>
                            <div style="margin-top: 59px; text-align:left;"><?= $row['admin'] ?> | SHIFT A</div>
                        </div>
                    </div>
                </td>
                <td style="width: 4cm; vertical-align: top;">
                    <div class="label-box" style="padding: 5px;">
                        <div class="data" style="font-size: 11px; line-height: 1.5;">
                            <div>Item Type : <?= $row['item_type'] ?></div>
                            <div>Kode Warna : <?= $row['kode_warna'] ?></div>
                            <div>Warna : <?= $row['warna'] ?></div>
                            <div>GW : <?= $row['gw_kirim'] ?></div>
                            <div>NW : <?= $row['kgs_kirim'] ?></div>
                            <div>Cones : <?= $row['cones_kirim'] ?></div>
                            <div>Lot : <?= $row['lot_kirim'] ?></div>
                            <div>No Karung : <?= $row['no_karung'] ?></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
<?php endforeach; ?>

</html>