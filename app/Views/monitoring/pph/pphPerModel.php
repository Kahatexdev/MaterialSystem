<?php $this->extend($role . '/pph/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <!-- Filter Data / Form Pencarian -->
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">PPH: Per Model</h5>
                            </div>
                        </div>
                        <div>
                            <!-- Form Pencarian -->
                            <form action="<?= base_url($role . '/tampilPerModel/' . $area) ?>" method="get" class="form-inline">
                                <div class="d-flex align-items-center">
                                    <input type="text" name="no_model" id="no_model" class="form-control mr-2" placeholder="Masukkan No Model" value="<?= isset($_GET['no_model']) ? esc($_GET['no_model']) : '' ?>">
                                    <button type="submit" class="btn bg-gradient-info text-white ms-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tampilkan Tabel Hanya Jika Data Tersedia -->
    <?php if (!empty($mergedData)) : ?>
        <div class="row mt-3">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Model</th>
                                        <th>Area</th>
                                        <th>Delivery</th>
                                        <th>Jenis</th>
                                        <th>Warna</th>
                                        <th>Kode Warna</th>
                                        <th>Loss</th>
                                        <th>Komposisi (%)</th>
                                        <th>GW</th>
                                        <th>Qty PO</th>
                                        <th>Total Produksi</th>
                                        <th>Sisa</th>
                                        <th>Total Kebutuhan</th>
                                        <th>Total Pemakaian</th>
                                        <th>% Pemakaian</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php $no = 1;
                                    foreach ($mergedData as $item): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($item['no_model']) ?></td>
                                            <td><?= esc($item['area']) ?></td>
                                            <td><?= esc($item['delivery_awal']) ?></td>
                                            <td><?= esc($item['item_type']) ?></td>
                                            <td><?= esc($item['color']) ?></td>
                                            <td><?= esc($item['kode_warna']) ?></td>
                                            <td><?= esc($item['loss']) ?></td>
                                            <td><?= esc($item['composition']) ?></td>
                                            <td><?= esc($item['gw']) ?></td>
                                            <td><?= esc($item['qty_pcs']) ?></td>
                                            <td><?= isset($item['bruto']) ? esc($item['bruto']) : '-' ?></td>
                                            <td><?= isset($item['sisa']) ? esc($item['sisa']) : '-' ?></td>
                                            <td><?= esc(number_format($item['ttl_kebutuhan'], 2)) ?></td>
                                            <td>
                                                <?= isset($item['bruto'], $item['composition'], $item['gw'])
                                                    ? esc(round(($item['grperpcs'] * $item['bruto']) / 1000, 2))
                                                    : '-'
                                                ?>
                                            </td>
                                            <td>
                                                <?= isset($item['bruto'], $item['composition'], $item['gw'], $item['qty_pcs'])
                                                    ? esc(round((($item['bruto'] * 24 * ($item['composition'] / 100) * $item['gw']) / 1000) / $item['qty_pcs'] * 100, 2))
                                                    : '-'
                                                ?>
                                            </td>
                                        <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <!-- Pesan Informasi Jika Data Belum Ada -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info text-center text-white" role="alert">
                    Silakan masukkan No Model untuk mencari data.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Notifikasi Flashdata -->
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

    <!-- Script untuk inisialisasi DataTable dan Chart.js -->
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
</div>
<?php $this->endSection(); ?>