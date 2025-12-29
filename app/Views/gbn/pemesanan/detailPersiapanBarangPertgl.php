<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<?php if (session()->getFlashdata('success')) : ?>
    <script>
        $(document).ready(function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= session()->getFlashdata('success') ?>',
                confirmButtonColor: '#4a90e2'
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
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4a90e2'
            });
        });
    </script>
<?php endif; ?>
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
                    <!-- Sub Title -->
                    <h5 class="text-bold fw-semibold mb-3">📦 Informasi Pemesanan</h5>

                    <!-- Grid Cards -->
                    <div class="row g-2">
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Kg Pesan</h6>
                                <?php $kgPesan = isset($ttlPesan['ttl_pesan_kg']) ? $ttlPesan['ttl_pesan_kg'] : 0; ?>
                                <h4 class="fw-semibold mb-0"> <?= number_format($kgPesan, 2) ?? 0 ?>Kg</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Cns Pesan</h6>
                                <?php $cnsPesan = isset($ttlPesan['ttl_pesan_cns']) ? $ttlPesan['ttl_pesan_cns'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= $cnsPesan ?> Cns</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Kg Persiapan</h6>
                                <?php $kgPersiapan = isset($ttlPersiapan['kgs_out']) ? $ttlPersiapan['kgs_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= number_format($kgPersiapan, 2) ?? 0 ?> Kg</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Cns Persiapan</h6>
                                <?php $cnsPersiapan = isset($ttlPersiapan['cns_out']) ? $ttlPersiapan['cns_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= $cnsPersiapan ?> Cns</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Kg Pengiriman</h6>
                                <?php $kgPengiriman = isset($ttlPengiriman['kgs_out']) ? $ttlPengiriman['kgs_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= number_format($kgPengiriman, 2) ?? 0 ?> Kg</h4>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Cns Pengiriman</h6>
                                <?php $cnsPengiriman = isset($ttlPengiriman['cns_out']) ? $ttlPengiriman['cns_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= $cnsPengiriman ?> Cns</h4>
                            </div>
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
                        <div class="col-md-8">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end" style="gap:10px;">
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100" style="margin-top: 31px;">
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                            <?php if ($jenis === "BENANG" || $jenis === "NYLON") { ?>
                                <a href="<?= base_url($role . '/pemesanan/exportListBarangKeluar?jenis=' . $jenis . '&tglPakai=' . $tglPakai) ?>"
                                    class="btn bg-gradient-success w-100" target="_blank">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>
                                <a href="<?= base_url($role . '/pemesanan/exportPdfListBarangKeluar?jenis=' . $jenis . '&tglPakai=' . $tglPakai) ?>"
                                    class="btn bg-gradient-danger w-100" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Export
                                </a>
                            <?php } ?>
                        </div>
                    </form>
                    <div class="table-responsive mt-4">
                        <table class="table  align-items-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tgl Pakai</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Color</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">No Karung</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Kgs</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cns</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Lot</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nama Cluster</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Ket Pindah Order</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="pemesananTable">
                                <?php foreach ($detail as $id) : ?>
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['tgl_pakai'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['admin'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['no_model'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['item_type'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['kode_warna'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['color'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['no_karung'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0 text-center"><?= $id['kgs_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0 text-center"><?= $id['cns_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['lot_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['nama_cluster'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <?= !empty($id['model_dipinjam'])
                                                    ? 'Pindah Order dari ' . $id['model_dipinjam'] . ' / ' .
                                                    ($id['item_type_dipinjam'] ?? '') . ' / ' .
                                                    ($id['kode_warna_dipinjam'] ?? '') . ' / ' .
                                                    ($id['warna_dipinjam'] ?? '')
                                                    : '' ?>
                                            </p>
                                        </td>
                                        <td>
                                            <?php if ($jenis === "BENANG" || $jenis === "NYLON") { ?>
                                                <?php if (!empty($id['id_pengeluaran'])) { ?>
                                                    <button type="button" class="btn btn-danger btn-hapus" data-id="<?= $id['id_pengeluaran'] ?>" data-id-out-celup="<?= $id['id_out_celup'] ?>" data-id-stock="<?= $id['id_stock'] ?>" data-kgs-out="<?= $id['kgs_out'] ?>" data-cns-out="<?= $id['cns_out'] ?>" data-krg-out="<?= $id['krg_out'] ?>" data-lot-out="<?= $id['lot_out'] ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php }  ?>
                                            <?php } else { ?>
                                                <span class="text-sm font-weight-bold mb-0 text-success">Sudah di pesan ke Cov</span>
                                            <?php } ?>
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
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmHapus">Ya, Hapus</button>
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
        // agar isi kolom tidak null
        function safe(val) {
            return (val === null || val === undefined) ? '' : val;
        }
        const filterModel = document.getElementById('filter_model').value;

        if (!filterModel) {
            alert('Nomodel filter tidak boleh kosong.');
            return;
        }
        showLoading();
        updateProgress(0);

        fetch(`<?= base_url($role . "/pemesanan/detailListBarangKeluar?jenis={$jenis}&tglPakai={$tglPakai}") ?>&noModel=${encodeURIComponent(filterModel)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // <— penting untuk CI4
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                const tableBody = document.getElementById('pemesananTable');
                tableBody.innerHTML = ''; // Clear isi tabel sebelum render ulang

                if (data.length > 0) {
                    data.forEach(id => {
                        const row = `
                        <tr>
                            <td><p class="text-sm font-weight-bold mb-0">${id.tgl_pakai}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${id.admin}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${id.no_model}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${id.item_type}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${id.kode_warna}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${id.color}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.no_karung)}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.kgs_out)}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.cns_out)}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.lot_out)}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.nama_cluster)}</p></td>
                            <td><p class="text-sm font-weight-bold mb-0">${safe(id.keterangan_gbn)}${id?.model_dipinjam ? `/ Pindah Order dari ${id.model_dipinjam} / ${id.item_type_dipinjam || ''} / ${id.kode_warna_dipinjam || ''} / ${id.warna_dipinjam || ''}` : ''}</p></td>
                            <td>
                                <button type="button" 
                                        class="btn btn-danger btn-hapus" 
                                        data-id="${id.id_pengeluaran}" 
                                        data-id-out-celup="${id.id_out_celup}" 
                                        data-id-stock="${id.id_stock}" 
                                        data-kgs-out="${id.kgs_out}" 
                                        data-cns-out="${id.cns_out}" 
                                        data-krg-out="${id.krg_out}"
                                        data-lot-out="${id.lot_out}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                        tableBody.insertAdjacentHTML('beforeend', row);
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

    let idPengeluaran = null;
    let idOutCelup = null;
    let idStock = null;
    let kgsOut = null;
    let cnsOut = null;
    let krgOut = null;
    let lotOut = null;
    const BASE_URL = "<?= base_url(); ?>";
    const role = "<?= $role ?>";

    $(document).on('click', '.btn-hapus', function() {
        idPengeluaran = $(this).data('id');
        idOutCelup = $(this).data('id-out-celup');
        idStock = $(this).data('id-stock');
        kgsOut = $(this).data('kgs-out');
        cnsOut = $(this).data('cns-out');
        krgOut = $(this).data('krg-out');
        lotOut = $(this).data('lot-out');

        $('#modalHapus').modal('show');
    });

    $('#btnConfirmHapus').on('click', function() {
        console.log('Tombol hapus diklik');
        if (idPengeluaran && idOutCelup && idStock) {
            $.ajax({
                url: `${BASE_URL}${role}/hapusPengeluaranJalur`, // Ganti sesuai route kamu
                type: 'POST',
                data: {
                    id_pengeluaran: idPengeluaran,
                    id_out_celup: idOutCelup,
                    id_stock: idStock,
                    kgs_out: kgsOut,
                    cns_out: cnsOut,
                    krg_out: krgOut,
                    lot_out: lotOut,
                },
                success: function(res) {
                    $('#modalHapus').modal('hide');
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: res.message
                        });
                    }
                }
            });
        }
    });
</script>

<?php $this->endSection(); ?>