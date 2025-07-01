<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Sisa Kebutuhan Area</p>
                            <h5 class="font-weight-bolder mb-0">
                                Data Sisa Kebutuhan Area
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_area" class="form-label">Area</label>
                            <select class="form-control" name="filter_area" id="filter_area" required>
                                <option value="">Pilih Area</option>
                                <?php foreach ($area as $ar) {
                                ?>
                                    <option value="<?= $ar ?>"><?= $ar ?></option>
                                <?php
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control" placeholder="No Model" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100">
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive mt-4">
                        <table class="table  align-items-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">TANGGAL PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">TGL RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">NO MODEL</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOS</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">ITEM TYPE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KODE WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TOTAL KEBUTUHAN AREA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY PESAN (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">PO TAMBAHAN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY KIRIM (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY RETUR (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KET GBN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">SISA (KIRIM - KEBUTUHAN - RETUR)</th>
                                </tr>
                            </thead>
                            <tbody id="sisaKebutuhanTable">
                                <tr>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('filterButton').addEventListener('click', function() {
        const filterArea = document.getElementById('filter_area').value.trim();
        const filterModel = document.getElementById('filter_model').value.trim();

        // Validasi input
        if (!filterArea || !filterModel) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Area dan No Model harus diisi!',
                confirmButtonText: 'OK',
            });
            return; // Hentikan eksekusi jika input kosong
        }

        // Buat URL dengan query parameters
        const url = `<?= base_url($role . "/pemesanan/sisaKebutuhanArea_filter") ?>?area=${encodeURIComponent(filterArea)}&model=${encodeURIComponent(filterModel)}`;

        fetch(url, {
                method: 'GET',
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const tableBody = document.getElementById('sisaKebutuhanTable');
                tableBody.innerHTML = ''; // Clear existing table rows

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(psn => {
                        const row = document.createElement('tr');

                        const tglPakaiCell = document.createElement('td');
                        tglPakaiCell.innerHTML = `<p class="text-sm font-weight-bold mb-0">${psn.tgl_pakai || '-'}</p>`;
                        row.appendChild(tglPakaiCell);

                        const actionCell = document.createElement('td');
                        actionCell.classList.add('text-center');
                        actionCell.innerHTML = `
                        <a href="/${psn.tgl_pakai}" class="btn bg-gradient-info">
                            <i class="fas fa-eye"></i>
                            Detail
                        </a>
                    `;
                        row.appendChild(actionCell);

                        tableBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    const noDataCell = document.createElement('td');
                    noDataCell.setAttribute('colspan', '16'); // Sesuaikan jumlah kolom tabel
                    noDataCell.classList.add('text-center');
                    noDataCell.textContent = 'Tidak ada data yang ditemukan.';
                    row.appendChild(noDataCell);
                    tableBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);

                // Tampilkan pesan error di tabel
                const tableBody = document.getElementById('sisaKebutuhanTable');
                tableBody.innerHTML = ''; // Bersihkan tabel

                const row = document.createElement('tr');
                const errorCell = document.createElement('td');
                errorCell.setAttribute('colspan', '16'); // Sesuaikan jumlah kolom tabel
                errorCell.classList.add('text-center', 'text-danger');
                errorCell.textContent = 'Terjadi kesalahan saat memuat data. Periksa kembali koneksi atau coba lagi nanti.';
                row.appendChild(errorCell);
                tableBody.appendChild(row);
            });
    });
</script>

<?php $this->endSection(); ?>