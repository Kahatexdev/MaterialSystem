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
            width: 9.6cm;
            height: 9.6cm;
            border: 2.8px solid #000;
            margin: auto;
            margin-top: 3px;
            padding: 0.3;
        }

        .label-header {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            color: #000;
            /* padding: 5px 0; */
            border-bottom: 1px solid #000;
        }

        .l-header {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #000;
            border-bottom: 1px solid #000;
        }

        .l-desc {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #000;
            /* padding: 5px 0; */
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
            height: 30px;
        }

        .nums {
            font-size: 30px;
            font-weight: bold;
        }

        .data-section {
            line-height: 1.5;
            padding: 5px;
            font-size: 16px;
        }

        .operator-info {
            margin-top: 10px;
            padding-left: 5px;
            text-align: left;
        }

        .footer {
            position: relative;
            width: 100%;
            margin-top: 30px;
            padding-left: 5px;
            padding-right: 5px;
            font-size: 11px;
            height: 50px;
        }

        .footer-left {
            position: absolute;
            left: 5px;
            bottom: 0;
            text-align: left;
            max-width: 60%;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .footer-left>div:first-child {
            margin-bottom: 0;
        }

        .footer-right {
            position: absolute;
            right: 10px;
            bottom: 0;
            text-align: right;
            margin-top: 20px;
            max-width: 35%;
        }

        .lot-label {
            font-size: 11px;
            line-height: 1;
            margin-bottom: 2px;
            /* Jarak tetap antara LOT dan value */
        }

        .lot-value {
            margin: 0;
            font-size: 40px;
            font-weight: bold;
            line-height: 0.9;
            word-wrap: break-word;
            overflow-wrap: break-word;
            display: inline-block;
            vertical-align: bottom;
        }

        .no-karung-value {
            margin: 0;
            font-size: 40px;
            font-weight: bold;
            line-height: 1;
        }

        .headerRow {
            margin-top: 6px;

            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .boxx {
            border: 1px solid black;
            /* full border */
            box-sizing: border-box;
            /* biar border masuk hitungan layout */
        }

        .tgl-datang {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            padding-left: 5px;
            /* padding-right: 5px; */
            text-align: right;
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
                        <td colspan="3">
                            <div class="barcode-section">
                                <img src="<?= $barcodeImages[$i] ?>" alt="barcode">
                                <?php
                                $noModel = $row['no_model'] ?? '-';
                                if (($row['po_plus'] ?? '') === '1') {
                                    $noModel = '(+)' . $noModel;
                                }
                                ?>
                                <div style="margin-top: 1px;" class="l-header"> <?= $noModel ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="data-section">
                                <div>Item : <?= $row['item_type'] ?></div>
                                <div>Kode : <?= $row['kode_warna'] ?>/ <?= $row['warna'] ?></div>
                                <?= !empty($row['bentuk_celup']) ? '(' . $row['bentuk_celup'] . ')' : '' ?>
                                <!-- <div class="nums">GW : <?= $row['gw_kirim'] ?> kg</div>
                                <div class="nums">NW : <?= $row['kgs_kirim'] ?> kg</div>
                                <div class="nums">Cones : <?= $row['cones_kirim'] ?></div> -->
                            </div>
                        </td>
                    </tr>
                    <tr class="headerRow">
                        <td class="boxx">GW (kg)</td>
                        <td class="boxx">NW (kg)</td>
                        <td class="boxx">Cones</td>
                    </tr>
                    <tr class="headerRow">
                        <td class="nums boxx"> <?= $row['gw_kirim'] ?> </td>
                        <td class="nums boxx"><?= $row['kgs_kirim'] ?> </td>
                        <td class="nums boxx"><?= $row['cones_kirim'] ?></td>
                    </tr>
                </table>

                <div class="footer">
                    <div class="footer-left">
                        <!-- <div class="lot-label">LOT</div> -->
                        <div class="lot-value"> <?= $row['lot_kirim'] ?></div>

                    </div>
                    <div class="footer-right">
                        No Karung
                        <div class="no-karung-value"><?= htmlspecialchars($row['no_karung'] ?? '-', ENT_QUOTES) ?></div>
                        <?= htmlspecialchars($row['operator_packing'] ?? '-', ENT_QUOTES) ?> | <?= htmlspecialchars($row['shift'] ?? '-', ENT_QUOTES) ?>
                    </div>
                </div>
                <div class="tgl-datang"><?= $dataBon['tgl_datang'] ?></div>
            </div>
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