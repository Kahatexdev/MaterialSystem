<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

<style>
    th.sticky {
        position: sticky;
        top: 0;
        background: #f9f9f9;
        z-index: 10;
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h3 class="mb-0 text-center text-md-start">Request PO GBN</h3>
                <a href="<?= base_url($role . '/po/bukaPoCovering') ?>" class="btn btn-outline-info">
                    <i class="fas fa-file-import me-2"></i>Buka PO
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="tablePo">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Tanggal PO</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($poCovering) : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($poCovering as $po) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?? '-' ?></td>
                                    <td class="text-center"><?= $po['tgl_po'] ?? '-' ?></td>
                                    <td class="text-center">
                                        <a class="btn bg-gradient-success" href="<?= base_url($role . '/po/exportPO/' . $po['tgl_po']) ?>">
                                            <i class="fas fa-file-pdf me-2"></i></i>Export PO
                                        </a>
                                        <a class="btn bg-gradient-warning" href="<?= base_url($role . '/po/listTrackingPo/' . $po['tgl_po']) ?>">
                                            <i class="far fa-question-circle"></i> Status PO
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="text-center">-</td>
                                <td class="text-center">Tidak ada data</td>
                                <td class="text-center">-</td>
                            </tr>
                        <?php endif; ?>
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
        $('#tablePo').DataTable({
            responsive: true,
            ordering: true,
            paging: true,
            searching: true,
            columnDefs: [{
                orderable: false,
                targets: 2
            }]
        });
    });
</script>


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