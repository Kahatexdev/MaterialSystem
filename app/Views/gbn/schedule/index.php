<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card {
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(76, 175, 80, 0.1);
        border: none;
        background-color: white;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 15px 30px rgba(76, 175, 80, 0.15);
        transform: translateY(-5px);
    }

    .table {
        border-radius: 15px;
        /* overflow: hidden; */
        border-collapse: separate;
        /* Ganti dari collapse ke separate */
        border-spacing: 0;
        /* Pastikan jarak antar sel tetap rapat */
        overflow: auto;
        position: relative;
    }

    .table th {
        background-color: #e8f5e9;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #2e7d32;
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: #f1f8e9;
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        /* Untuk tetap di bagian atas saat menggulir vertikal */
        z-index: 3;
        /* Pastikan header terlihat di atas elemen lain */
        background-color: #e8f5e9;
        /* Warna latar belakang */
    }

    .table td.sticky {
        position: sticky;
        left: 0;
        /* Untuk tetap di sisi kiri saat menggulir horizontal */
        z-index: 2;
        /* Prioritas lebih rendah dari header */
        background-color: #e8f5e9;
        /* Tambahkan warna latar belakang */
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
        /* Memberikan efek bayangan untuk memisahkan kolom */

    }


    .capacity-bar {
        height: 6px;
        border-radius: 3px;
        margin-bottom: 5px;
    }

    .btn {
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    .btn-filter {
        background: linear-gradient(135deg, #4caf50, #81c784);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #43a047, #66bb6a);
    }

    .date-navigation {
        background-color: white;
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(76, 175, 80, 0.1);
    }

    .date-navigation input[type="date"] {
        border: none;
        font-weight: 500;
        color: #2e7d32;
    }

    .machine-info {
        font-size: 0.85rem;
    }

    .machine-info strong {
        font-size: 1rem;
        color: #2e7d32;
    }

    .job-item {
        background-color: white;
        border-radius: 10px;
        padding: 0.7rem;
        margin-bottom: 0.7rem;
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.1);
        transition: all 0.2s ease;
    }

    .job-item:hover {
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    .job-item span {
        font-size: 0.8rem;
        color: #558b2f;
    }

    .bg-success {
        background-color: #66bb6a !important;
    }

    .bg-warning {
        background-color: #ffd54f !important;
    }

    .bg-danger {
        background-color: #ef5350 !important;
    }

    .text-success {
        color: #43a047 !important;
    }
</style>

<div class="container-fluid py-4">
    <h1 class="display-5 mb-4 text-center" style="color: #2e7d32; font-weight: 600;">Schedule Mesin Celup</h1>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3"><strong>Keterangan Kapasitas:</strong></h6>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="capacity-bar bg-secondary me-2" style="width: 30px; height: 12px;"></div>
                            <span class="text-muted">0%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="capacity-bar bg-success me-2" style="width: 30px; height: 12px;"></div>
                            <span class="text-muted">1% - 69%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="capacity-bar bg-warning me-2" style="width: 30px; height: 12px;"></div>
                            <span class="text-muted">70% - 99%</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="capacity-bar bg-danger me-2" style="width: 30px; height: 12px;"></div>
                            <span class="text-muted">100%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="date-navigation d-flex align-items-center">
                        <button class="btn btn-link text-decoration-none text-success">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <input type="date" class="form-control border-0" value="<?= $currentDate->format('Y-m-d') ?>">
                        <button class="btn btn-link text-decoration-none text-success">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex justify-content-md-end gap-2">
                        <button class="btn btn-filter">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sticky">Mesin</th>
                            <?php
                            for ($i = 0; $i < 7; $i++) {
                                $date = (clone $currentDate)->modify("+$i days");
                                echo "<th>" . $date->format('D, d M') . "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mesin_celup as $mesin): ?>
                            <tr>
                                <!-- Kolom informasi mesin -->
                                <td class="sticky machine-info">
                                    <strong>Mesin <?= $mesin['no_mesin'] ?></strong><br>
                                    <small>Kapasitas: <?= number_format($mesin['min_caps'], 1) ?> - <?= number_format($mesin['max_caps'], 1) ?> kg </small>
                                    <br>
                                    <small>L/M/D : (<?= $mesin['lmd'] ?>)</small>
                                </td>

                                <!-- Kolom tanggal -->
                                <?php
                                for ($i = 0; $i < 7; $i++) {
                                    $date = (clone $currentDate)->modify("+$i days");


                                    echo "<td>";
                                    // Loop untuk menampilkan kartu sesuai jumlah lot
                                    for ($lot = 0; $lot < $mesin['jml_lot']; $lot++) {
                                        $jobsForDay = array_filter($scheduleData, function ($job) use ($mesin, $date, $lot) {
                                            return $job['no_mesin'] == $mesin['no_mesin'] && $job['start_mc'] == $date->format('Y-m-d') && $job['lot_urut'] == $lot + 1;
                                        });
                                        $num = $lot + 1;
                                        if (!empty($jobsForDay)) {
                                            foreach ($jobsForDay as $job) {
                                                $capacityColor = 'bg-secondary';
                                                $capacityPercentage =
                                                    round((intval($job['kg_celup']) / intval(($mesin['max_caps']))) * 100);
                                                if (intval($job['kg_celup']) > 0 && intval($job['kg_celup']) < intval($mesin['max_caps'] * 69 / 100)) {
                                                    $capacityColor = 'bg-success';
                                                } elseif (intval($job['kg_celup']) >= intval($mesin['max_caps'] * 70 / 100) && intval($job['kg_celup']) < intval($mesin['max_caps'] * 100 / 100)) {
                                                    $capacityColor = 'bg-warning';
                                                } elseif (intval($job['kg_celup']) == intval($mesin['max_caps'] * 100 / 100)) {
                                                    $capacityColor = 'bg-danger';
                                                }

                                                echo "<div class='job-item {$capacityColor}' style='width: {$capacityPercentage}%;'>";
                                                echo "<button class='btn btn-link display: block;' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#modalSchedule' 
                                                        onclick='showScheduleModal(\"{$mesin['no_mesin']}\", \"{$date->format('Y-m-d')}\", \"{$job['lot_urut']}\")' 
                                                        data-bs-toggle='tooltip' 
                                                        data-bs-placement='top' 
                                                        title='{$job['kg_celup']} kg ({$capacityPercentage}%)'>";
                                                echo "<div class='d-flex flex-column text-center'>";
                                                echo "<strong style='font-size: 0.9rem;'>{$job['kode_warna']}</strong>"; // Menampilkan kode warna di dalam tombol
                                                echo "<span style='font-size: 0.85rem;'>{$job['kg_celup']} kg</span>"; // Menampilkan berat dalam tombol
                                                echo "</div>";
                                                echo "</button>";
                                                echo "</div>";
                                            }
                                        } else {
                                            // Tampilkan kartu kosong jika tidak ada jadwal
                                            echo "<div class='job-item'>";
                                            echo "<button class='btn btn-link text-decoration-none' data-bs-toggle='modal' data-bs-target='#modalSchedule' onclick='showScheduleModal(\"{$mesin['no_mesin']}\", \"{$date->format('Y-m-d')}\",\"{$num}\",)'>";
                                            echo "<div class='text-muted'>Tidak ada jadwal</div>";
                                            echo "</button> </div>";
                                        }
                                    }

                                    echo "</td>";
                                }
                                ?>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- modal schedule -->
<div class="modal fade" id="modalSchedule" tabindex="-1" aria-labelledby="modalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalScheduleLabel">Detail Jadwal Mesin Celup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalScheduleBody">
                <!-- Content here -->
                tes modal
                <input type="text" name="" id="tes">
            </div>
        </div>
    </div>
</div>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    function showScheduleModal(machine, date, lotUrut) {
        var modalBody = document.getElementById('modalScheduleBody');
        modalBody.innerHTML = 'Loading...';
        var tes = document.getElementById('tes');
        console.log(machine, date, lotUrut);
        fetch('<?= base_url($role . '/schedule/getScheduleDetails') ?>/' + machine + '/' + date + '/' + lotUrut)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data;
            });
    }
</script>


<?php $this->endSection(); ?>