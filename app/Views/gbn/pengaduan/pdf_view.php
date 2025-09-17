<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pengaduan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 5px;
            background: #6c757d;
            color: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        hr {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="card">
        <?php
        setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian_indonesia.1252');
        $timestamp = strtotime($pengaduan['created_at']);

        // fallback kalau locale ga jalan
        $days = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        ];
        $dayName = $days[date('l', $timestamp)] ?? date('l', $timestamp);

        $formattedDate = $dayName . ', ' . date('d/m/Y', $timestamp) . ' (' . date('H:i', $timestamp) . ')';

        $roleMap = [
            'sudo'     => 'Monitoring',
            'aps'      => 'Planner',
            'planning' => 'PPC',
            'user'     => 'Area'
        ];
        $displayRole = $roleMap[$pengaduan['target_role']] ?? $pengaduan['target_role'];
        ?>

        <div class="header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:16px;">
                Pengirim :
                <span class="badge"><?= esc($pengaduan['username']) ?></span>
            </h3>
            <small style="font-size:12px; color:#555;"><?= $formattedDate ?></small>
        </div>

        <p style="margin-top:10px; font-weight:bold;">Isi Pesan :</p>
        <p><?= nl2br(esc($pengaduan['isi'])) ?></p>
        <hr>
    </div>

</body>

</html>