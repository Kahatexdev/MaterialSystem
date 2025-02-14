<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">



<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>',
                });
            });
        </script>
    <?php endif; ?>
    <h1 class="display-5 mb-4 text-center" style="color:rgb(0, 85, 124); font-weight: 600;">Schedule Mesin Celup</br>Nylon & Polyester</h1>

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
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end gap-2">
                        <input type="date" id="start_date" class="form-control" placeholder="Start Date">
                        <input type="date" id="end_date" class="form-control" placeholder="End Date">
                        <button class="btn btn-filter" id="filter_date_range">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <!-- reset tamggal -->
                        <button class="btn btn-filter" id="reset_date_range">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive" style="width:auto; height: 780px; overflow-y: auto; overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sticky">Mesin</th>
                            <?php
                            // Set startDate dan endDate
                            $startDate = new DateTime($filter['start_date']);
                            $endDate = new DateTime($filter['end_date']);

                            // Tambahkan satu hari pada $endDateClone untuk mencakup tanggal terakhir
                            $endDateClone = clone $endDate;
                            $endDateClone->add(new DateInterval('P1D'));

                            // Interval 1 hari
                            $dateInterval = new DateInterval('P1D');
                            $datePeriod = new DatePeriod($startDate, $dateInterval, $endDateClone);

                            // Tanggal hari ini
                            $today = new DateTime();

                            // Tanggal 3 hari ke belakang
                            $threeDaysAgo = (clone $today)->sub(new DateInterval('P4D'));

                            // Tanggal 6 hari ke depan
                            $sixDaysAhead = (clone $today)->add(new DateInterval('P6D'));

                            foreach ($datePeriod as $date) {
                                // Misalnya tambahkan class sticky ke semua th
                                if ($date->format('w') == 0) {
                                    echo "<th class='sticky' style='color: red;'>" . $date->format('D, d M') . "</th>";
                                } elseif ($date->format('Y-m-d') === $today->format('Y-m-d')) {
                                    echo "<th class='sticky' style='background-color: #ffeb3b; color: #000;'>" . $date->format('D, d M') . "</th>";
                                } elseif ($date >= $threeDaysAgo && $date < $today) {
                                    echo "<th class='sticky' style='color: #6c757d;'>" . $date->format('D, d M') . "</th>";
                                } elseif ($date > $today && $date <= $sixDaysAhead) {
                                    echo "<th class='sticky' style='color: rgb(31, 193, 199);'>" . $date->format('D, d M') . "</th>";
                                } else {
                                    echo "<th class='sticky'>" . $date->format('D, d M') . "</th>";
                                }
                            } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Kelompokkan scheduleData untuk mempercepat akses
                        $scheduleGrouped = [];
                        foreach ($scheduleData as $job) {
                            // Pastikan tanggal disimpan dengan format yang sesuai (Y-m-d)
                            $key = "{$job['no_mesin']} | " . (new DateTime($job['tanggal_schedule']))->format('Y-m-d') . " | {$job['lot_urut']}";
                            // Jika key sudah ada, gabungkan total_kg-nya
                            if (isset($scheduleGrouped[$key])) {
                                $scheduleGrouped[$key]['total_kg'] += $job['total_kg'];
                            } else {
                                $scheduleGrouped[$key] = $job;
                            }
                        }
                        // echo '<pre>';
                        // print_r($scheduleData); // Debug untuk melihat seluruh data schedule
                        // echo '</pre>';
                        // Menentukan threshold kapasitas mesin
                        function getCapacityColor($kgCelup, $maxCaps)
                        {
                            $lowThreshold = $maxCaps * 0.69; // 69%
                            $midThreshold = $maxCaps * 0.70; // 70%
                            $highThreshold = $maxCaps;       // 100%

                            if ($kgCelup < $lowThreshold) {
                                return 'bg-success';
                            } elseif ($kgCelup < $highThreshold) {
                                return 'bg-warning';
                            } else {
                                return 'bg-danger';
                            }
                        }

                        // Fungsi untuk menampilkan tombol jadwal
                        function renderJobButton($job, $mesin, $capacityColor, $capacityPercentage)
                        {
                            $kgCelup = number_format($job['total_kg'], 2);
                            return "
                                <button class='btn btn-link {$capacityColor}' 
                                    data-bs-toggle='modal' 
                                    data-bs-target='#modalSchedule'
                                    data-no-mesin='{$job['no_mesin']}'
                                    data-tanggal-schedule='{$job['tanggal_schedule']}'
                                    data-lot-urut='{$job['lot_urut']}'
                                    title='{$job['total_kg']} kg ({$capacityPercentage}%)'>
                                    <div class='d-flex flex-column align-items-center justify-content-center' style='height: 100%;'>
                                        <span style='font-size: 0.9rem; color: black; font-weight: bold;'>{$job['kode_warna']}</span>
                                        <span style='font-size: 0.85rem; color: black;'>{$kgCelup} KG</span>
                                    </div>
                                </button>";
                        }

                        ?>
                        <!-- Tabel Mesin -->
                        <?php foreach ($mesin_celup as $mesin): ?>
                            <tr>
                                <!-- Kolom informasi mesin -->
                                <td class="sticky machine-info">
                                    <strong>Mesin <?= $mesin['no_mesin'] ?></strong><br>
                                    <input type="hidden" id="no_mesin" value="<?= $mesin['no_mesin'] ?>">
                                    <small>Kapasitas: <?= number_format($mesin['min_caps'], 1) ?> - <?= number_format($mesin['max_caps'], 1) ?> kg </small><br>
                                    <small>L/M/D : (<?= $mesin['lmd'] ?>)</small>
                                </td>

                                <!-- Kolom tanggal -->
                                <?php foreach ($datePeriod as $date): ?>
                                    <td>
                                        <?php
                                        // Loop untuk menampilkan kartu sesuai jumlah lot
                                        for ($lot = 0; $lot < $mesin['jml_lot']; $lot++) {
                                            $num = $lot + 1;
                                            // Periksa apakah tanggal dan lot sudah sesuai dengan jadwal
                                            $key = "{$mesin['no_mesin']} | " . $date->format('Y-m-d') . " | " . $num;
                                            $job = $scheduleGrouped[$key] ?? null;

                                            if ($job) {
                                                // Menghitung kapasitas dan warna berdasarkan kapasitas
                                                $capacityPercentage = ($job['total_kg'] / $mesin['max_caps']) * 100;
                                                $capacityColor = getCapacityColor($job['total_kg'], $mesin['max_caps']);

                                                // Render button untuk lot yang ada jadwalnya
                                                echo "<div class='job-item'>";
                                                echo renderJobButton($job, $mesin, $capacityColor, number_format($capacityPercentage, 1));
                                                echo "</div>";
                                            } else {
                                                // Tampilkan kartu kosong jika tidak ada jadwal
                                                echo "<div class='job-item no-schedule'>";
                                                echo "<button class='btn btn-light text-muted text-decoration-none'
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#modalSchedule'
                                                        data-no-mesin='{$mesin['no_mesin']}'
                                                        data-tanggal-schedule='{$date->format('Y-m-d')}'
                                                        data-lot-urut='{$num}'>";
                                                echo "<div class='text-muted'>Tidak ada jadwal</div>";
                                                echo "</button></div>";
                                            }
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
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
                    var totalKg = parseFloat(tes[0].total_kg).toFixed(2);
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
                                    <input type="text" class="form-control" id="total_kg" value="${totalKg}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="start_mc" class="form-label">Start MC</label>
                                    <input type="text" class="form-control" id="start_mc" value="${item.start_mc}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="last_status" class="form-label">Last Status</label>
                                    <input type="text" class="form-control" id="last_status" value="${item.last_status}" readonly>
                                </div>
                            </div>
                        </div>
                        `;
                    });

                    htmlContent += `
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" id="editSchedule">Edit Jadwal</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>`;

                    modalBody.innerHTML = htmlContent;

                    // Show the modal after content is loaded
                    const modal = new bootstrap.Modal(document.getElementById("modalSchedule"));
                    const idCelup = document.getElementById("id_celup").value;
                    modal.show();

                    // Tambahkan event listener untuk tombol "Edit Jadwal"
                    document.getElementById("editSchedule").addEventListener("click", function() {
                        redirectToEditSchedule(machine, date, lotUrut);
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
                        if (isSunday(date)) {
                            alert("⚠️ Tidak dapat menambahkan jadwal pada hari Minggu.");
                            return; // Hentikan proses jika hari Minggu
                        }
                        redirectToAddSchedule(machine, date, lotUrut);
                    });
                });
        }

        // Fungsi untuk mengecek apakah tanggal tertentu adalah hari Minggu
        function isSunday(date) {
            const sunday = 0; // 0 adalah kode untuk hari Minggu
            const tgl = new Date(date); // Konversi string date ke objek Date
            return tgl.getDay() === sunday;
        }

        // Fungsi untuk redirect ke halaman tambah jadwal
        function redirectToAddSchedule(machine, date, lotUrut) {
            const url = `<?= base_url($role . '/schedule/form') ?>?no_mesin=${machine}&tanggal_schedule=${date}&lot_urut=${lotUrut}`;
            window.location.href = url;
        }

        // Fungsi untuk redirect ke halaman edit jadwal
        function redirectToEditSchedule(machine, date, lotUrut) {
            const url = `<?= base_url($role . '/schedule/editSchedule') ?>?no_mesin=${machine}&tanggal_schedule=${date}&lot_urut=${lotUrut}`;
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


        document.getElementById('filter_date_range').addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Silakan pilih rentang tanggal.',
                });
                return;
            }

            // Redirect ke URL dengan parameter filter
            const url = `<?= base_url($role . '/schedule/nylon') ?>?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });

        // reset filter tanggal
        document.getElementById('reset_date_range').addEventListener('click', function() {
            // Redirect ke URL dengan parameter filter menampilkan data 2 hari kebelakang dan 7 hari kedepan
            const start_date = new Date();
            const end_date = new Date();
            start_date.setDate(start_date.getDate() - 3);
            end_date.setDate(end_date.getDate() + 7);

            const startDate = start_date.toISOString().split('T')[0];

            const endDate = end_date.toISOString().split('T')[0];

            // Redirect ke URL dengan parameter filter
            const url = `<?= base_url($role . '/schedule/nylon') ?>?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });



    });
</script>


<?php $this->endSection(); ?>