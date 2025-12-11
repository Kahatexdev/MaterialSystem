<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <style>
        /* Overlay transparan */
        #loadingOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            background: rgba(0, 0, 0, 0.75);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            width: 260px;
            /* kecilkan modal */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .loader-text {
            margin-top: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 12px;
        }


        #loadingOverlay.active {
            display: block;
            opacity: 1;
        }

        .loader {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            position: relative;
        }

        .loader:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: loader-dual-ring 1.2s linear infinite;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }

        @keyframes loader-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>
    <!-- overlay -->
    <div id="loadingOverlay">
        <div class="loader-wrap">
            <div class="loading-card">
                <div class="loader" role="status" aria-hidden="true"></div>
                <div class="loader-text">Memuat data...</div>

                <!-- Progress bar -->
                <div class="progress mt-3" style="height: 6px; border-radius: 6px;">
                    <div id="progressBar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold"><?= $title; ?></p>
                            <h5 class="font-weight-bolder mb-0">
                                Data Persiapan Pengeluaran Barang <?= $jenis ?>
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
                    <h6 class="mb-0"></h6>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-10">
                            <input type="hidden" id="jenis" value="<?= $jenis ?>">
                            <label for="filter_date" class="form-label">Tanggal Pakai</label>
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
                                <?php foreach ($tglPakai as $tgl) : ?>
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $tgl['tgl_pakai'] ?></p>
                                        </td>
                                        <td class="text-center"></td>
                                        <td class="text-center">
                                            <?php if ($jenis === 'SPANDEX' || $jenis === 'KARET') : ?>
                                                <a href="<?= base_url($role . '/pemesanan/exportListPemesananSpdxKaretPertgl?jenis=' . $jenis . '&tglPakai=' . $tgl['tgl_pakai']) ?>" class="btn bg-gradient-success" target="_blank">
                                                    <i class="fas fa-file-excel fa-2x"></i>
                                                </a>
                                                <a href="<?= base_url($role . '/pemesanan/exportPdfListPemesananSpdxKaretPertgl?jenis=' . $jenis . '&tglPakai=' . $tgl['tgl_pakai']) ?>" class="btn bg-gradient-danger" target="_blank">
                                                    <i class="fas fa-file-pdf fa-2x"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url($role . '/pemesanan/detailListBarangKeluar?jenis=' . $jenis . '&tglPakai=' . $tgl['tgl_pakai']) ?>" class="btn bg-gradient-info" target="_blank">
                                                <i class="fas fa-eye fa-2x"></i>
                                            </a>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showLoading() {
        $('#loadingOverlay').addClass('active');
        $('#btnSearch').prop('disabled', true);
        // show DataTables processing indicator if available
        try {
            dataTable.processing(true);
        } catch (e) {}
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('active');
        $('#btnSearch').prop('disabled', false);
        try {
            dataTable.processing(false);
        } catch (e) {}
    }

    function updateProgress(percent) {
        $('#progressBar')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent);
        $('#progressText').text(percent + '%');
    }

    document.getElementById('filterButton').addEventListener('click', function() {
        const filterDate = document.getElementById('filter_date').value;
        const jenis = document.getElementById('jenis').value;

        const formData = new FormData();
        formData.append('jenis', jenis);
        formData.append('filter_date', filterDate);

        showLoading();
        updateProgress(0);

        // console.log("Data dikirim ke API:", {
        //     jenis,
        //     filterDate
        // });

        fetch('<?= base_url($role . "/pemesanan/filterListBarangKeluarPertgl") ?>', {
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
                        // Buat link export/detail tergantung jenis
                        if (jenis === 'SPANDEX' || jenis === 'KARET') {
                            const detailUrl = `<?= base_url($role . '/pemesanan/detailListBarangKeluar') ?>?jenis=${jenis}&tglPakai=${psn.tgl_pakai}`;
                            const excelUrl = `<?= base_url($role . '/pemesanan/exportListPemesananSpdxKaretPertgl') ?>?jenis=${jenis}&tglPakai=${psn.tgl_pakai}`;
                            const pdfUrl = `<?= base_url($role . '/pemesanan/exportPdfListPemesananSpdxKaretPertgl') ?>?jenis=${jenis}&tglPakai=${psn.tgl_pakai}`;

                            actionCell.innerHTML = `
                            <a href="${detailUrl}" class="btn bg-gradient-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <a href="${excelUrl}" class="btn bg-gradient-success" target="_blank">
                            <i class="fas fa-file-excel fa-2x"></i>
                        </a>
                        <a href="${pdfUrl}" class="btn bg-gradient-danger" target="_blank">
                            <i class="fas fa-file-pdf fa-2x"></i>
                        </a>
                    `;
                        } else {
                            const detailUrl = `<?= base_url($role . '/pemesanan/detailListBarangKeluar') ?>?jenis=${jenis}&tglPakai=${psn.tgl_pakai}`;
                            actionCell.innerHTML = `
                        <a href="${detailUrl}" class="btn bg-gradient-info">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    `;
                        }
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

                updateProgress(100);
            })
            .catch(error => {
                console.error('Fetch Error:', error);
            })
            .finally(() => {
                setTimeout(() => hideLoading(), 400); // jeda agar progress bar terlihat
            });

    });
</script>

<?php $this->endSection(); ?>