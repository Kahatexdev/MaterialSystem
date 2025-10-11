<?php $this->extend($role . '/retur/header'); ?>
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

        background-color: rgb(8, 38, 83);
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgb(255, 255, 255);
    }

    .table td {
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 1rem 0.75rem;
    }

    .table tr:nth-child(even) {
        background-color: rgb(237, 237, 237);
    }

    .table th.sticky {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: rgb(4, 55, 91);
    }

    .table td.sticky {
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #e3f2fd;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 4px 8px rgba(33, 150, 243, 0.2);
    }

    .btn-filter {
        background: linear-gradient(135deg, #1e88e5, #64b5f6);
        color: white;
        border: none;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #1976d2, #42a5f5);
    }

    .date-navigation {
        background-color: white;
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(33, 150, 243, 0.1);
    }

    .date-navigation input[type="date"] {
        border: none;
        font-weight: 500;
        color: #1565c0;
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

    .job-item .btn {
        display: block;
        width: 100%;
        height: 100%;
        text-align: center;
    }

    .job-item .btn span {
        font-size: 0.9rem;
        color: black;
        font-weight: bold;
    }

    .job-item .btn .total-kg {
        font-size: 0.85rem;
    }

    .no-schedule .btn {
        background-color: #f8f9fa;
        border: 1px dashed #ccc;
        color: #6c757d;
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
            <form method="post" action="<?= base_url($role . '/retur') ?>">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <h3 class="mb-0 text-center text-md-start">Retur GBN</h3>
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-center">
                        <div class="d-flex flex-column">
                            <label for="filter_tglsch" class="form-label">Tanggal Schedule</label>
                            <input type="date" id="filter_tglsch" name="filter_tglsch" class="form-control" placeholder="Tanggal Schedule">
                        </div>
                        <div class="d-flex flex-column">
                            <label for="filter_tglsch" class="form-label">Tanggal Kirim</label>
                            <input type="date" id="filter_tglkirim" name="filter_tglkirim" class="form-control" placeholder="Tanggal Kirim">
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
                <table id="dataTable" class="table">
                    <thead>
                        <tr>
                            <th class="sticky">No</th>
                            <th class="sticky">PO</th>
                            <th class="sticky">Jenis Benang</th>
                            <th class="sticky">Kode Warna</th>
                            <th class="sticky">Warna</th>
                            <th class="sticky">Alasan</th>
                            <th class="sticky">Lot Celup</th>
                            <th class="sticky">Tanggal Kirim</th>
                            <th class="sticky">Surat Jalan</th>
                            <th class="sticky">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($uniqueData as $data) :
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $data['no_model']; ?></td>
                                <td><?= $data['item_type']; ?></td>
                                <td><?= $data['kode_warna']; ?></td>
                                <td><?= $data['warna']; ?></td>
                                <td><?= $data['ket_daily_cek']; ?></td>
                                <td><?= $data['lot_celup']; ?></td>
                                <td><?= $data['tgl_datang']; ?></td>
                                <td><?= $data['no_surat_jalan']; ?></td>
                                <td><a href="<?= base_url($role . '/createRetur/' . $data['id_celup']) ?>" class="btn btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="New Bon">
                                        <i class="fas fa-barcode"></i></a>
                                    <a href="<?= base_url($role . '/outCelup/editBon/' . $data['id_bon']) ?>" class="btn btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Bon">
                                        <i class="fas fa-file-alt"></i></a>
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
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "columns": [
                null, // No
                null, // PO
                null, // Jenis Benang
                null, // Kode Warna
                null, // Warna
                null, // Alasan
                null, // Lot Celup
                null, // Tanggal Kirim
                null, // Surat Jalan
                {
                    "orderable": false,
                    "searchable": false
                } // Action
            ],
            "language": {
                "emptyTable": "Data belum tersedia untuk saat ini."
            }
        });
    });
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
            const url = `<?= base_url($role . '/schedule') ?>?start_date=${startDate}&end_date=${endDate}`;
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
            const url = `<?= base_url($role . '/schedule') ?>?start_date=${startDate}&end_date=${endDate}`;
            window.location.href = url;
        });
    });
</script>
<?php $this->endSection(); ?>