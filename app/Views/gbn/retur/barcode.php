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
            margin-top: 25px;
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
            margin-top: 10px;
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
            font-weight: bold;
            line-height: 0.9;
            word-wrap: break-word;
            overflow-wrap: break-word;
            display: inline-block;
            vertical-align: bottom;
        }

        .lot-normal {
            font-size: 40px;
        }

        .lot-small {
            font-size: 28px;
            /* adjust sesuai desain */
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
            bottom: 0;
            text-align: right;
        }
    </style>
</head>

<?php
foreach ($dataList as $i => $row): ?>

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
                                <div style="margin-top: 1px;" class="l-header">
                                    <div class="lot-label">PO :</div> <?= $noModel ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="data-section">
                                <div>Item : <?= $row['item_type'] ?></div>
                                <div>Kode : <?= $row['kode_warna'] ?>/ <?= $row['warna'] ?><?= !empty($row['bentuk_celup']) ? ' (' . $row['bentuk_celup'] . ')' : '' ?></div>
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
                        <div class="lot-label">LOT CELUP :</div>
                        <!-- <p> <sup> Lot Celup: </sup></p> -->
                        <div class="lot-value <?= $row['lotClass'] ?>" style="margin-top: 1px;"> <?= $row['lot_kirim'] ?></div>

                    </div>
                    <div class="footer-right">
                        No Karung
                        <div class="no-karung-value"><?= htmlspecialchars($row['no_karung'] ?? '-', ENT_QUOTES) ?></div>
                        <?= $row['kategori'] ?? '-' ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </body>
<?php endforeach; ?>

</html>