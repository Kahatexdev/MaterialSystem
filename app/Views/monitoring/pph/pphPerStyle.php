<?php $this->extend($role . '/pph/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<div class="container-fluid py-4">
    <div class="row my-4">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">
                                    PPH: Per Style Area <?= $area ?>
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <!-- Form Pencarian -->
                            <form action="<?= base_url($role . '/tampilPerStyle/' . $area) ?>" method="get" class="form-inline">
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
    <div class="row mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Jarum</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Inisial</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Style Size</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Item Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Los</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Komposisi (%)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">GW</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Qty PO (Pcs)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Kebutuhan (Kg)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Netto (Pcs)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Bs Mc (Pcs)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Bs Setting (Pcs)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Sisa</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Pemakaian</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">% Pemakaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                                foreach ($mergedData as $item): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td></td>
                                        <td><?= esc($item['no_model']) ?></td>
                                        <td><?= esc($item['area']) ?></td>
                                        <td><?= esc($item['inisial']) ?></td>
                                        <td><?= esc($item['style_size']) ?></td>
                                        <td><?= esc($item['item_type']) ?></td>
                                        <td><?= esc($item['color']) ?></td>
                                        <td><?= esc($item['kode_warna']) ?></td>
                                        <td><?= esc($item['loss']) ?></td>
                                        <td><?= esc($item['composition']) ?></td>
                                        <td><?= esc($item['gw']) ?></td>
                                        <td><?= esc($item['qty_pcs']) ?></td>
                                        <td><?= esc(number_format($item['kgs'], 2)) ?></td>
                                        <td><?= isset($item['bruto']) ? esc($item['bruto']-$item['bs_setting']) : '-' ?></td>
                                        <td><?= isset($item['bs_pcs']) ? esc($item['bs_pcs']) : '-' ?></td>
                                        <td><?= isset($item['bs_setting']) ? esc($item['bs_setting']) : '-' ?></td>
                                        <td><?= isset($item['sisa']) ? esc($item['sisa']) : '-' ?></td>
                                        <td>
                                            <?= isset($item['bruto'], $item['bs_pcs'], $item['composition'], $item['gw'])
                                                ? esc(round((($item['bruto'] + $item['bs_pcs']) * ($item['composition'] / 100) * $item['gw']) / 1000, 2))
                                                : '-'
                                            ?>
                                        </td>
                                        <td>
                                            <?= isset($item['bruto'], $item['bs_pcs'], $item['composition'], $item['gw'], $item['qty_pcs'])
                                                ? esc(round(((($item['bruto'] + $item['bs_pcs']) * ($item['composition'] / 100) * $item['gw']) / 1000) / $item['qty_pcs'] * 100, 2))
                                                : '-'
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
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
                });
            });
        </script>
    <?php endif; ?>
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>

    <?php $this->endSection(); ?>