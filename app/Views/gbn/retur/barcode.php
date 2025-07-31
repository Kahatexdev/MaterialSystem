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

<?php foreach ($dataList as $i => $row): ?>

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
                <div class="operator-info">Retur : <?= $row['kategori'] ?? '-' ?></div>
            </div>
        </div>
    </body>
<?php endforeach; ?>

</html>