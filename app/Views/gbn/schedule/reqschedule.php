<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card {
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: none;
        background-color: white;
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    

    .btn-filter {
        background: linear-gradient(135deg, #4caf50, #81c784);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #43a047, #66bb6a);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
    }

    @media (max-width: 768px) {
        .d-flex.flex-md-row {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .btn-filter {
            width: 100%;
        }
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

</style>

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

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" action="<?= base_url($role . '/schedule/reqschedule') ?>">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <h3 class="mb-0 text-center text-md-start">Request Schedule Mesin Celup</h3>
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-center">
                        <div class="d-flex flex-column">
                            <label for="filter_tglsch" class="form-label">Tanggal Schedule</label>
                            <input type="date" id="filter_tglsch" name="filter_tglsch" class="form-control" placeholder="Tanggal Schedule">
                        </div>
                        <div class="d-flex flex-column">
                            <label for="filter_nomodel" class="form-label">No Model / Kode Warna</label>
                            <input type="text" id="filter_nomodel" name="filter_nomodel" class="form-control" placeholder="No Model / Kode Warna">
                        </div>
                        <button class="btn btn-filter mt-md-4" id="filter_date_range" type="submit">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sticky">No</th>
                            <th class="sticky">No Mc</th>
                            <th class="sticky">PO</th>
                            <th class="sticky">Jenis Benang</th>
                            <th class="sticky">Kode Warna</th>
                            <th class="sticky">Warna</th>
                            <th class="sticky">Start Mc</th>
                            <th class="sticky">Tanggal Schedule</th>
                            <th class="sticky">Status</th>
                            <th class="sticky">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($uniqueData as $data):
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td class="text-center"><?= $data['no_mesin']; ?></td>
                                <td class="text-center"><?= $data['no_model']; ?></td>
                                <td class="text-center"><?= $data['item_type']; ?></td>
                                <td class="text-center"><?= $data['kode_warna']; ?></td>
                                <td class="text-center"><?= $data['warna']; ?></td>
                                <td class="text-center"><?= $data['start_mc']; ?></td>
                                <td class="text-center"><?= $data['tgl_schedule']; ?></td>
                                <td class="text-center">
                                    <?php
                                    if ($data['last_status'] == 'scheduled' || $data['last_status'] == 'reschedule') {
                                        echo '<span class="badge bg-info">scheduled</span>';
                                    } else if ($data['last_status'] == 'celup' || $data['last_status'] == 'sent') {
                                        echo '<span class="badge bg-warning">celup</span>';
                                    } else if ($data['last_status'] == 'retur') {
                                        echo '<span class="badge bg-danger">retur</span>';
                                    } else {
                                        echo '<span class="badge bg-success">done</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url($role . '/schedule/reqschedule/show/' . $data['id_celup']) ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
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
                                    <input type="text" class="form-control" id="no_po" value="${item.no_model}" readonly>
                                    <input type="hidden" id="id_celup" value="${item.id_celup}">
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="item_type" class="form-label">Jenis Benang(Item Type)</label>
                                    <input type="text" class="form-control" id="item_type" value="${item.item_type}" readonly>
                                </div>
                                 </div>
                                <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kode_warna" class="form-label">Kode Warna</label>
                                    <input type="text" class="form-control" id="kode_warna" value="${item.kode_warna}" readonly>
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-4">
                            <div class="mb-3">
                            <label for="warna" class="form-label">Warna</label>
                            <input type="text" class="form-control" id="warna" value="${item.warna}" readonly>
                            </div>
                             </div>
                                <div class="col-md-4">
                            <div class="mb-3">
                            <label for="lot_celup" class="form-label">Lot Celup</label>
                            <input type="text" class="form-control" id="lot_celup" value="${item.lot_celup}" readonly>
                            </div>
                            </div>
                                <div class="col-md-4">
                            <div class="mb-3">
                            <label for="tgl_celup" class="form-label">Tanggal Celup</label>
                            <input type="text" class="form-control" id="tgl_celup" value="${item.tanggal_celup}" readonly>
                            </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_kg" class="form-label">Total Kg Celup</label>
                                    <input type="text" class="form-control" id="total_kg" value="${totalKg}" readonly>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="start_mc" class="form-label">Start MC</label>
                                    <input type="text" class="form-control" id="start_mc" value="${item.start_mc}" readonly>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="last_status" class="form-label">Last Status</label>
                                    <input type="text" class="form-control" id="last_status" value="${item.last_status}" readonly>
                                </div>
                            </div>
                            </div>
                        </div>
                        `;
                    });

                    modalBody.innerHTML = htmlContent;

                    // Show the modal after content is loaded
                    const modal = new bootstrap.Modal(document.getElementById("modalSchedule"));
                    const idCelup = document.getElementById("id_celup").value;
                    modal.show();


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
            const url = `<?= base_url($role . '/schedule/reqschedule') ?>?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });

        // reset filter tanggal
        document.getElementById('reset_date_range').addEventListener('click', function() {
            // Redirect ke URL dengan parameter filter menampilkan data 2 hari kebelakang dan 7 hari kedepan
            const start_date = new Date();
            const end_date = new Date();
            start_date.setDate(start_date.getDate() - 2);
            end_date.setDate(end_date.getDate() + 7);

            const startDate = start_date.toISOString().split('T')[0];

            const endDate = end_date.toISOString().split('T')[0];

            // Redirect ke URL dengan parameter filter
            const url = `<?= base_url($role . '/schedule/reqschedule') ?>?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });
    });
</script>
<?php $this->endSection(); ?>