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
            font-size: 25px;
            font-weight: bold;
            color: #000;
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
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-size: 11px;
        }

        .footer-right {
            text-align: right;
        }


        .no-karung-value {
            margin: 0;
            font-size: 40px;
            font-weight: bold;
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
    </style>
</head>

<?php foreach ($dataList as $i => $row) : ?>

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
                                <div style="margin-top: 1px;" class="l-header"> <?= $row['no_model'] ?? '-' ?></div>
                                <div class="l-desc">Lot: <?= $row['lot_kirim'] ?></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="data-section">
                                <div>Item : <?= $row['item_type'] ?></div>
                                <div>Kode : <?= $row['kode_warna'] ?>/ <?= $row['warna'] ?></div>
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

                    <div class="footer-right">
                        No Karung
                        <div class="no-karung-value"><?= htmlspecialchars($row['no_karung'] ?? '-', ENT_QUOTES) ?></div>
                        <div class="operator-info">No SJ : <?= $row['no_surat_jalan'] ?? '-' ?></div>

                    </div>
                </div>
            </div>
        </div>
    </body>
<?php endforeach; ?>

</html>