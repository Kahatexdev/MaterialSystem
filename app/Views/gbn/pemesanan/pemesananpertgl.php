<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $title; ?></p>
                            <h5 class="font-weight-bolder mb-0">
                                Data Pemesanan Berdasarkan Tanggal Pakai Area <?= $area ?>
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
                <div class="card-header">
                    <h6 class="mb-0">Data Pemesanan</h6>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-10">
                            <label for="filter_date" class="form-label">Tanggal</label>
                            <input type="date" id="filter_date" name="filter_date" class="form-control">
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center" colspan="2">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pemesananTable">
                                <?php foreach ($pemesananPertgl as $psn) : ?>
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $psn['tgl_pakai'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url($role . '/detailpemesanan/' . $area . '/' . $jenis . '/' . $psn['tgl_pakai']) ?>" class="btn bg-gradient-info">
                                                <i class="fas fa-eye"></i>
                                                Detail
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url($role . '/pemesanan/exportListBarangKeluar?area=' . $area . '&jenis=' . $jenis . '&tglPakai=' . $psn['tgl_pakai']) ?>" class="btn bg-gradient-success" target="_blank">
                                                <i class="fas fa-file-excel fa-2x"></i>
                                            </a>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <input type="hidden" id="area" value="<?= $area ?>">
                        <input type="hidden" id="jenis" value="<?= $jenis ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('filterButton').addEventListener('click', function() {
        const filterDate = document.getElementById('filter_date').value;
        const area = document.getElementById('area').value;
        const jenis = document.getElementById('jenis').value;

        const formData = new FormData();
        formData.append('area', area);
        formData.append('jenis', jenis);
        formData.append('filter_date', filterDate);

        // console.log("Data dikirim ke API:", {
        //     area,
        //     jenis,
        //     filterDate
        // });

        fetch('<?= base_url($role . "/pemesanan/filter") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // console.log("Response dari API:", data);

                const tableBody = document.getElementById('pemesananTable');
                tableBody.innerHTML = ''; // Clear existing table rows

                if (data.length > 0) {
                    data.forEach(psn => {
                        const row = document.createElement('tr');

                        const tglPakaiCell = document.createElement('td');
                        tglPakaiCell.innerHTML = `<p class="text-sm font-weight-bold mb-0">${psn.tgl_pakai}</p>`;
                        row.appendChild(tglPakaiCell);

                        const actionCell = document.createElement('td');
                        actionCell.classList.add('text-center');
                        actionCell.innerHTML = `
                            <a href="<?= base_url($role . '/detailpemesanan/' . $area . '/' . $jenis) ?>/${psn.tgl_pakai}" class="btn bg-gradient-info">
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
                    noDataCell.setAttribute('colspan', '2');
                    noDataCell.classList.add('text-center');
                    noDataCell.textContent = 'Tidak ada data yang ditemukan.';
                    row.appendChild(noDataCell);
                    tableBody.appendChild(row);
                }
            })
            .catch(error => console.error('Fetch Error:', error));
    });
</script>

<?php $this->endSection(); ?>