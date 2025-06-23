<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bon Pengiriman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            float: left;
            width: 80px;
            height: auto;
        }

        .judul-form {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .no-border td {
            border: none;
        }

        .signature td {
            height: 60px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div style="margin-left: 90px;">
            <div class="judul-form">FORMULIR<br>DEPARTEMEN KELOS WARNA<br>BON PENGIRIMAN</div>
            <table class="no-border">
                <tr>
                    <td style="text-align: left;">No. Dokumen: FDR-KWA-006/REV.03</td>
                    <td style="text-align: right;">Tanggal Revisi: 07 Januari 2021</td>
                </tr>
            </table>
        </div>
    </div>

    <table>
        <tr>
            <td style="text-align: left;">NAMA LANGGANAN: <?= $dataBon['detail_sj'] ?? '-' ?></td>
            <td style="text-align: left;">NO SURAT JALAN: <?= $dataBon['no_surat_jalan'] ?? '-' ?></td>
            <td style="text-align: left;">TANGGAL: <?= date('Y-m-d', strtotime($dataBon['tgl_datang'])) ?></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>NO PO</th>
                <th>JENIS BENANG</th>
                <th>KODE BENANG</th>
                <th>KODE WARNA</th>
                <th>WARNA</th>
                <th>LOT CELUP</th>
                <th>L/M/D</th>
                <th>HARGA PER KG</th>
                <th colspan="2">QTY</th>
                <th colspan="2">TOTAL</th>
                <th>KETERANGAN</th>
            </tr>
            <tr>
                <th colspan="8"></th>
                <th>CONES</th>
                <th>KG</th>
                <th>GW</th>
                <th>NW</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    <p><b>KETERANGAN:</b> GW = GROSS WEIGHT, NW = NET WEIGHT, L = LIGHT, M = MEDIUM, D = DARK</p>

    <table class="no-border signature">
        <tr>
            <td style="text-align:center;">PENGIRIM</td>
            <td style="text-align:center;">PENERIMA</td>
        </tr>
    </table>

    <div class="footer">
        <p><?= $dataBon['admin'] ?? 'Admin' ?></p>
    </div>

</body>

</html>