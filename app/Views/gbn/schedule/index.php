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
                                    <input type="hidden" id="no_mesin" value="<?= $mesin['no_mesin'] ?>">
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
                                            return $job['no_mesin'] == $mesin['no_mesin'] && $job['tanggal_schedule'] == $date->format('Y-m-d') && $job['lot_urut'] == $lot + 1;
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

                                                echo "<div class='job-item {$capacityColor}' style='width: {$capacityPercentage}%; text-align: center;'>"; // Memastikan tombol berada di tengah
                                                echo "<button class='btn btn-link' 
                                                        style='display: block; width: 100%; height: 100%; text-align: center;' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#modalSchedule' 
                                                        data-no-mesin='{$job['no_mesin']}'
                                                        data-tanggal-schedule='{$job['tanggal_schedule']}'
                                                        data-lot-urut='{$job['lot_urut']}'
                                                        onclick='showScheduleModal(\"{$job['no_mesin']}\", \"{$job['tanggal_schedule']}\", \"{$job['lot_urut']}\")' 
                                                        data-bs-toggle='tooltip' 
                                                        data-bs-placement='top' 
                                                        title='{$job['total_kg']} kg ({$capacityPercentage}%)'>";
                                                echo "<div class='d-flex flex-column align-items-center justify-content-center' style='height: 100%;'>"; // Flexbox untuk pusat vertikal dan horizontal
                                                echo "<span style='font-size: 0.9rem; color: black; font-weight: bold; text-align: center;'>{$job['kode_warna']}</span>"; // Menampilkan kode warna di tengah
                                                echo "<span style='font-size: 0.85rem; color: black;'>{$job['total_kg']} kg</span>"; // Berat juga di tengah
                                                echo "</div>";
                                                echo "</button>";
                                                echo "</div>";
                                            }
                                        } else {
                                            // Tampilkan kartu kosong jika tidak ada jadwal
                                            echo "<div class='job-item'>";
                                            echo "<button class='btn btn-link text-decoration-none'
                                                    data-bs-toggle='modal' data-bs-target='#modalSchedule'
                                                    data-no-mesin='{$mesin['no_mesin']}'
                                                    data-tanggal-schedule='{$date->format('Y-m-d')}'
                                                    data-lot-urut='{$num}' 
                                                    onclick='showScheduleModal(\"{$mesin['no_mesin']}\", \"{$date->format('Y-m-d')}\", \"{$num}\")'>";
                                            echo "<div class='text-muted'>Tidak ada jadwal</div>";
                                            echo "</button></div>";
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

<div class="modal fade" id="modalSchedule" tabindex="-1" aria-labelledby="modalScheduleLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalScheduleLabel">Jadwal Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalScheduleBody">
                <!-- Isi modal dengan JS -->


            </div>



        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipList = [].slice
            .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            .map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
    });

    function sendDataToController(button) {
        // Ambil data dari atribut data-*
        const noMesin = button.getAttribute("data-no-mesin");
        const tanggalSchedule = button.getAttribute("data-tanggal-schedule");
        const lotUrut = button.getAttribute("data-lot-urut");

        // Validasi data untuk memastikan nilainya tidak null atau undefined
        if (!noMesin || !tanggalSchedule || !lotUrut) {
            console.error("Data tidak lengkap!");
            return;
        }

        // Susun URL dengan parameter GET
        const url = `<?= base_url($role . '/schedule/form') ?>?no_mesin=${noMesin}&tanggal_schedule=${tanggalSchedule}&lot_urut=${lotUrut}`;

        // Redirect ke URL tersebut
        window.location.href = url;
    }

    // Tambahkan event listener pada tombol "Tambah Jadwal"
    document.addEventListener("click", function(event) {
        if (event.target.id === "addSchedule") {
            sendDataToController(event.target);
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Definisikan fungsi showScheduleModal terlebih dahulu
        function showScheduleModal(machine, date, lotUrut) {
            const modalTitle = document.querySelector("#modalSchedule .modal-title");
            const modalBody = document.querySelector("#modalScheduleBody");

            // Update modal title
            modalTitle.textContent = `Mesin-${machine} | ${date} | Lot ${lotUrut}`;

            // Show loading message while fetching data
            modalBody.innerHTML = `<div class="text-center text-muted">Loading...</div>`;

            // URL for the request
            const url = `<?= base_url($role . '/schedule/getScheduleDetails') ?>/${machine}/${date}/${lotUrut}`;
            // Fetch schedule details from the server
            fetch(url)

                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Tidak Ada Jadwal');
                    }
                    return response.text(); // Assuming the server returns HTML content (as in your `modal_details` view)
                })
                .then((data) => {
                    // Insert the fetched HTML into the modal body
                    var tes = JSON.parse(data);
                    var htmlContent = '';
                    tes.forEach(function(item) {
                        htmlContent += `<div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="no_po" class="form-label">No. PO</label>
                                    <input type="text" class="form-control" id="no_po" value="${item.no_po}" readonly>
                                    <input type="hidden" id="id_celup" value="${item.id_celup}">
                                </div>
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Jenis Benang(Item Type)</label>
                                    <input type="text" class="form-control" id="item_type" value="${item.item_type}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="kode_warna" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="kode_warna" value="${item.kode_warna}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="warna" class="form-label">Warna</label>
                                    <input type="text" class="form-control" id="warna" value="${item.warna}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="lot_celup" class="form-label">Lot Celup</label>
                                    <input type="text" class="form-control" id="lot_celup" value="${item.lot_celup}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="tgl_celup" class="form-label">Tanggal Celup</label>
                                    <input type="text" class="form-control" id="tgl_celup" value="${item.tanggal_celup}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_kg" class="form-label">Total Kg Celup</label>
                                    <input type="text" class="form-control" id="total_kg" value="${item.total_kg} KG" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="start_mc" class="form-label">Start MC</label>
                                    <input type="text" class="form-control" id="start_mc" value="${item.start_mc}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="deliery" class="form-label">Delivery</label>
                                    <input type="text" class="form-control" id="deliery" value="" readonly>
                                </div>
                            </div>
                        </div>
                        `;
                    });

                    htmlContent += `
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="deleteSchedule">Hapus Jadwal</button>
                        <button type="button" class="btn btn-warning text-black" id="editSchedule">Edit Jadwal</button>
                    </div>`;

                    modalBody.innerHTML = htmlContent;

                    // Show the modal after content is loaded
                    const modal = new bootstrap.Modal(document.getElementById("modalSchedule"));
                    const idCelup = document.getElementById("id_celup").value;
                    modal.show();

                    // Tambahkan event listener untuk tombol "Edit Jadwal"
                    document.getElementById("editSchedule").addEventListener("click", function() {
                        redirectToEditSchedule(idCelup);
                    });
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                    // Jika data tidak ditemukan, tambahkan tombol "Tambah Jadwal"
                    modalBody.innerHTML = `
                    <div class="text-center text-danger">${error.message}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" id="addSchedule">Tambah Jadwal</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>`;

                    // Tambahkan event listener untuk tombol "Tambah Jadwal"
                    document.getElementById("addSchedule").addEventListener("click", function() {
                        redirectToAddSchedule(machine, date, lotUrut);
                    });
                });
        }

        // Fungsi untuk redirect ke halaman tambah jadwal
        function redirectToAddSchedule(machine, date, lotUrut) {
            const url = `<?= base_url($role . '/schedule/form') ?>?no_mesin=${machine}&tanggal_schedule=${date}&lot_urut=${lotUrut}`;
            window.location.href = url;
        }

        // Fungsi untuk redirect ke halaman edit jadwal
        function redirectToEditSchedule(idCelup) {
            const url = `<?= base_url($role . '/schedule/editSchedule') ?>/${idCelup}`;
            window.location.href = url;
        }

        // Seleksi elemen modal
        const modalSchedule = document.getElementById("modalSchedule");
        const modalTitle = modalSchedule.querySelector(".modal-title");

        // Tambahkan event listener untuk tombol yang membuka modal
        document.querySelectorAll("[data-bs-target='#modalSchedule']").forEach((button) => {
            button.addEventListener("click", function() {
                const noMesin = this.getAttribute("data-no-mesin");
                const tanggalSchedule = this.getAttribute("data-tanggal-schedule");
                const lotUrut = this.getAttribute("data-lot-urut");

                // Panggil fungsi untuk menampilkan modal
                showScheduleModal(noMesin, tanggalSchedule, lotUrut);
            });
        });


        // // Tambahkan event listener untuk tombol Tambah Jadwal di dalam modal JS
        // document.getElementById("addSchedule").addEventListener("click", function() {
        //     // Ambil data dari atribut modal
        //     const noMesin = modalTitle.textContent.split(" | ")[0].split("-")[1];
        //     const tanggalSchedule = modalTitle.textContent.split(" | ")[1];
        //     const lotUrut = modalTitle.textContent.split(" | ")[2].split(" ")[1];

        //     // Redirect ke URL tambah jadwal
        //     const url = `<?= base_url($role . '/schedule/form') ?>?no_mesin=${noMesin}&tanggal_schedule=${tanggalSchedule}&lot_urut=${lotUrut}`;
        //     window.location.href = url;
        // });

        // // Tambahkan event listener untuk tombol Edit Jadwal
        // document.getElementById("editSchedule").addEventListener("click", function() {
        //     // Ambil data dari atribut modal
        //     const noMesin = modalTitle.textContent.split(" | ")[0].split("-")[1];
        //     const tanggalSchedule = modalTitle.textContent.split(" | ")[1];
        //     const lotUrut = modalTitle.textContent.split(" | ")[2].split(" ")[1];

        //     // Redirect ke URL edit jadwal
        //     const url = `<?= base_url($role . '/schedule/form') ?>?no_mesin=${noMesin}&tanggal_schedule=${tanggalSchedule}&lot_urut=${lotUrut}`;
        //     window.location.href = url;
        // });
    });
</script>


<?php $this->endSection(); ?>