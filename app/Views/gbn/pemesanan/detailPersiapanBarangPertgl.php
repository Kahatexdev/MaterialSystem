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
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Kg Pesan</h6>
                                <?php $kgPesan = isset($ttlPesan['ttl_pesan_kg']) ? $ttlPesan['ttl_pesan_kg'] : 0; ?>
                                <h4 class="fw-semibold mb-0"> <?= number_format($kgPesan, 2) ?? 0 ?>Kg</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Cns Pesan</h6>
                                <?php $cnsPesan = isset($ttlPesan['ttl_pesan_cns']) ? $ttlPesan['ttl_pesan_cns'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= $cnsPesan ?> Cns</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Kg Persiapan</h6>
                                <?php $kgPersiapan = isset($ttlPersiapan['kgs_out']) ? $ttlPersiapan['kgs_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= number_format($kgPersiapan, 2) ?? 0 ?> Kg</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                                <h6 class="text-muted mb-1 small">Cns Persiapan</h6>
                                <?php $cnsPersiapan = isset($ttlPersiapan['cns_out']) ? $ttlPersiapan['cns_out'] : 0; ?>
                                <h4 class="fw-semibold mb-0"><?= $cnsPersiapan ?> Kg</h4>
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
                <div class="card-header">
                    <h6 class="mb-0"></h6>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-8">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100">
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="<?= base_url($role . '/pemesanan/exportListBarangKeluar?jenis=' . $jenis . '&tglPakai=' . $tglPakai) ?>"
                                class="btn bg-gradient-success w-100" target="_blank">
                                <i class="fas fa-file-excel"></i> Export
                            </a>
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
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['area_out'] ?></p>
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
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['kgs_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['cns_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['lot_out'] ?></p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0"><?= $id['nama_cluster'] ?></p>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-hapus" data-id="<?= $id['id_pengeluaran'] ?>" data-id-out-celup="<?= $id['id_out_celup'] ?>" data-id-stock="<?= $id['id_stock'] ?>" data-kgs-out="<?= $id['kgs_out'] ?>" data-cns-out="<?= $id['cns_out'] ?>" data-krg-out="<?= $id['krg_out'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    document.getElementById('filterButton').addEventListener('click', function() {
        const filterModel = document.getElementById('filter_model').value;

        if (!filterModel) {
            alert('Nomodel filter tidak boleh kosong.');
            return;
        }

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

                data.forEach(id => {
                    const row = `
            <tr>
                <td><p class="text-sm font-weight-bold mb-0">${id.tgl_pakai}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.area_out}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.no_model}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.item_type}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.kode_warna}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.color}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.no_karung}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.kgs_out}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.cns_out}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.lot_out}</p></td>
                <td><p class="text-sm font-weight-bold mb-0">${id.nama_cluster}</p></td>
                <td><a class="btn btn-danger"><i class="fas fa-trash"></i></a></td>
            </tr>
        `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            })
            .catch(error => console.error('Fetch Error:', error));
    });

    let idPengeluaran = null;
    let idOutCelup = null;
    let idStock = null;
    let kgsOut = null;
    let cnsOut = null;
    let krgOut = null;
    const BASE_URL = "<?= base_url(); ?>";
    const role = "<?= $role ?>";

    $(document).on('click', '.btn-hapus', function() {
        idPengeluaran = $(this).data('id');
        idOutCelup = $(this).data('id-out-celup');
        idStock = $(this).data('id-stock');
        kgsOut = $(this).data('kgs-out');
        cnsOut = $(this).data('cns-out');
        krgOut = $(this).data('krg-out');

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