<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
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

    <!-- Modal Detail Stok -->
    <div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStockLabel">Detail Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <div id="stockData" class="row g-3"></div>
                        <button type="submit" class="btn bg-gradient-info mt-3 text-end">Pilih Stok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Data Pemesanan Area</h5>
            </div>
        </div>


        <!-- Tabel Data -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Kebutuhan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jalan Mc</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cns Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PO (+)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Terima</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Retur</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Jatah</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status Jatah</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($dataList as $key => $id) {
                                $ttl_kg_pesan = number_format($id['qty_pesan'] - $id['qty_sisa'], 2);
                                $ttl_cns_pesan = $id['cns_pesan'] - $id['cns_sisa'];
                            ?>
                                <tr>
                                    <td class="text-xs text-start"><?= $no++; ?></td>
                                    <td class="text-xs text-start"><?= $id['tgl_pakai']; ?></td>
                                    <td class="text-xs text-start"><?= $id['admin']; ?></td>
                                    <td class="text-xs text-start"><?= $id['no_model']; ?></td>
                                    <td class="text-xs text-start"><?= $id['item_type']; ?></td>
                                    <td class="text-xs text-start"><?= $id['kode_warna']; ?></td>
                                    <td class="text-xs text-start"><?= $id['color']; ?></td>
                                    <td class="text-xs text-start"><?= number_format($id['ttl_kebutuhan_bb'], 2); ?></td>
                                    <td class="text-xs text-start"><?= $id['jl_mc']; ?></td>
                                    <td class="text-xs text-start"><?= $ttl_kg_pesan; ?></td>
                                    <td class="text-xs text-start"><?= $ttl_cns_pesan; ?></td>
                                    <td class="text-xs text-start"><?= $id['lot']; ?></td>
                                    <td class="text-xs text-start"><?= $id['keterangan']; ?></td>
                                    <td class="text-xs text-start">
                                        <?php if ($id['po_tambahan'] == 1): ?>
                                            <span class="text-success fw-bold">✅</span>
                                        <?php else: ?>
                                            <!-- Biarkan kosong -->
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-xs text-start"><?= number_format($id['ttl_pengiriman'], 2); ?></td>
                                    <td class="text-xs text-start"></td>
                                    <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>"><?= number_format($id['sisa_jatah'], 2); ?></td>
                                    <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>">
                                        <?php if ($id['sisa_jatah'] > 0) {
                                            if ($ttl_kg_pesan >= $id['sisa_jatah']) { ?>
                                                <span style="color: red;">Pemesanan Melebihi Jatah</span>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <span style="color: red;">Habis Jatah</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-xs text-start">
                                        <button type="button" class="btn btn-warning update-btn" data-toggle="modal" data-target="#updateListModal" data-area="<?= $id['admin']; ?>" data-tgl="<?= $id['tgl_pakai']; ?>" data-model="<?= $id['no_model']; ?>" data-item="<?= $id['item_type']; ?>" data-kode="<?= $id['kode_warna']; ?>" data-color="<?= $id['color']; ?>" data-po-tambahan="<?= $id['po_tambahan']; ?>">
                                            <i class="fa fa-edit fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($dataPemesanan)) : ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <p>No data available in the table.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $('#dataTable').DataTable({
        "pageLength": 35,
        "order": []
    });
</script>
<?php $this->endSection(); ?>