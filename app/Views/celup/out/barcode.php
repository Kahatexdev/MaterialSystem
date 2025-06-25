<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Barcode Label PT. KAHATEX</title>
    <style>
        @page {
            size: 10cm 10cm;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .label-container {
            width: 9.8cm;
            height: 9.8cm;
            border: 1px solid #000;
            margin: auto;
            margin-top: 3px;
            padding: 0;
        }

        .label-header {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            color: #013182;
            padding: 5px 0;
            border-bottom: 1px solid #000;
        }

        .label-body {
            padding: 5px;
            box-sizing: border-box;
            align-items: center;
        }

        .barcode-section {
            align-items: center;
            text-align: center;
            justify-content: center;
            font-size: 14px;
        }

        .barcode-section img {
            align-items: center;
            text-align: center;
            justify-content: center;
            text-align: center;
            height: 50px;
        }

        .data-section {
            line-height: 1.5;
            padding: 5px;
            font-size: 14px;
        }

        .operator-info {
            margin-top: 10px;
            padding-left: 5px;
            text-align: left;
        }
    </style>
</head>

<?php foreach ($detailBon as $i => $row): ?>

    <body>
        <div class="label-container">
            <div class="label-header">
                <img src="<?= $img ?>" alt="logo" width="25" style="vertical-align: middle; margin-right: 5px;">
                PT. KAHATEX
            </div>

            <div class="label-body">
                <table>
                    <tr>
                        <td colspan="2">
                            <div class="barcode-section">
                                <img src="<?= $barcodeImages[$i] ?>" alt="barcode">
                                <div style="margin-top: 5px;">No Model : <?= $row['no_model'] ?? '-' ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="data-section">
                                <div>Item Type : <?= $row['item_type'] ?></div>
                                <div>Kode Warna : <?= $row['kode_warna'] ?></div>
                                <div>Warna : <?= $row['warna'] ?></div>
                                <div>GW : <?= $row['gw_kirim'] ?></div>
                                <div>NW : <?= $row['kgs_kirim'] ?></div>
                                <div>Cones : <?= $row['cones_kirim'] ?></div>
                                <div>Lot : <?= $row['lot_kirim'] ?></div>
                                <div>No Karung : <?= $row['no_karung'] ?></div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="operator-info"><?= $row['operator_packing'] ?? '-' ?> | <?= $row['shift'] ?? '-' ?></div>
            </div>
        </div>
    </body>
<?php endforeach; ?>

</html>

<!-- <!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Barcode Label PT. KAHATEX</title>
    <style>
        @page {
            size: 10cm 5cm;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }
    </style>
</head>

<?php foreach ($detailBon as $i => $row): ?>

    <body>
        <table style="width: 9.9cm; height:4.7cm; border-collapse: collapse; border: 1px solid #000; margin:auto; margin-top:5px;">
            <tr style=" border: 1px solid #000;">
                <td colspan="2" class="header" style="text-align: center; font-size: 15px; font-weight: bold; color: #013182;">
                    <img src="<?= $img ?>" alt="" width="15" style="margin-top:5px;">
                    PT. KAHATEX
                </td>
            </tr>
            <tr>
                <td style="width: 3cm; vertical-align: top;">
                    <div class="label-box" style="padding: 5px;">
                        <div class="barcode-box" style="text-align: center; font-size: 10pt;">
                            <img src="<?= $barcodeImages[$i] ?>" alt="barcode" style="max-width: 100%; height: 40px;">
                            <div style="margin-top:5px;">No Model : <?= $row['no_model'] ?? '-' ?></div>
                            <div>GW : <?= $row['gw_kirim'] ?></div>
                            <div>NW : <?= $row['kgs_kirim'] ?></div>
                            <div>Cones : <?= $row['cones_kirim'] ?></div>
                            <div style="margin-top: 10px; text-align:left;"><?= $row['operator_packing'] ?? '-' ?> | <?= $row['shift'] ?? '-' ?></div>
                        </div>
                    </div>
                </td>
                <td style="width: 4cm; vertical-align: top;">
                    <div class="label-box" style="padding: 5px;">
                        <div class="data" style="font-size: 10pt; line-height: 1.5;">
                            <div>Item Type : <?= $row['item_type'] ?></div>
                            <div>Kode Warna : <?= $row['kode_warna'] ?></div>
                            <div>Warna : <?= $row['warna'] ?></div>
                            <div>Lot : <?= $row['lot_kirim'] ?></div>
                            <div>No Karung : <?= $row['no_karung'] ?></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </body>
<?php endforeach; ?>

</html> -->