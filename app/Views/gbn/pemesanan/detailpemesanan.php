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
                <h5 class="mb-0 font-weight-bolder">Data Pemesanan <?= $jenis; ?> <?= $area; ?> <?= $tglPakai; ?></h5>
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jalan Mc</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Kgs Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Cns Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Po Tambahan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg/Krg Kirim</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Kirim</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cluster Out</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cluster</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataPemesanan)): ?>
                                <?php
                                $no = 1;
                                foreach ($dataPemesanan as $data): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $data['tgl_pakai'] ?></td>
                                        <td><?= $data['area'] ?></td>
                                        <td><?= $data['no_model'] ?></td>
                                        <td><?= $data['item_type'] ?></td>
                                        <td><?= $data['kode_warna'] ?></td>
                                        <td><?= $data['color'] ?></td>
                                        <td><?= $data['ttl_jl_mc'] ?></td>
                                        <td><?= $data['ttl_kg'] ?></td>
                                        <td><?= $data['ttl_cns'] ?></td>
                                        <td><?= $data['lot_pesan'] ?></td>
                                        <td><?= $data['ket_pesan'] ?></td>
                                        <td><?= $data['po_tambahan'] ?></td>
                                        <td><?= !empty($data['kg_kirim']) ? number_format($data['kg_kirim'], 2) . ' / ' . $data['krg_kirim'] : '' ?></td>
                                        <td><?= $data['lot_kirim'] ?></td>
                                        <td><?= $data['cluster_kirim'] ?></td>
                                        <td>

                                            <!-- button pesan ke covering -->

                                            <?php if ($data['jenis'] === 'SPANDEX' || $data['jenis'] === 'KARET'): ?>
                                                <?php if (!$data['sudah_pesan_spandex']): ?>
                                                    <a href="<?= base_url($role . '/pesanKeCovering/' . $data['id_total_pemesanan']) ?>"
                                                        class="btn bg-gradient-info">
                                                        <i class="fas fa-layer-group"></i> Pesan <?= ucfirst($data['jenis']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-info"><?= $data['status'] ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="<?= base_url($role . '/selectClusterWarehouse/' . $data['id_total_pemesanan']) . '?Area=' . $area . '&KgsPesan=' . $data['ttl_kg'] . '&CnsPesan=' . $data['ttl_cns'] ?>"
                                                    class="btn bg-gradient-info">
                                                    <i class="fas fa-layer-group"></i>Pilih
                                                </a>
                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

<script>
    // $(document).ready(function() {
    //     $(".btn-detail").click(function() {
    //         let no_model = $(this).data("no_model");
    //         let item_type = $(this).data("item_type");
    //         let kode_warna = $(this).data("kode_warna");
    //         let warna = $(this).data("warna");

    //         $.ajax({
    //             url: "<?= base_url($role . '/getStockByParams') ?>",
    //             type: "POST",
    //             data: {
    //                 no_model: no_model,
    //                 item_type: item_type,
    //                 kode_warna: kode_warna,
    //                 warna: warna
    //             },
    //             dataType: "json",
    //             success: function(response) {
    //                 let stockContainer = $("#stockData");
    //                 stockContainer.empty();

    //                 if (response.length > 0) {
    //                     $.each(response, function(index, stock) {
    //                         stockContainer.append(`
    //                                 <div class="col-6 border p-3">
    //                                     <div class="form-check">
    //                                         <input class="form-check-input" type="checkbox" name="selectedStock[]" value="${stock.id_stock}">
    //                                         <label class="form-check-label">
    //                                             <strong>No:</strong> ${index + 1} <br>
    //                                             <strong>No Model:</strong> ${stock.no_model} <br>
    //                                             <strong>Item Type:</strong> ${stock.item_type} <br>
    //                                             <strong>Kode Warna:</strong> ${stock.kode_warna} <br>
    //                                             <strong>Warna:</strong> ${stock.warna} <br>
    //                                             <strong>Stok Kgs:</strong> ${stock.kgs_stock_awal} <br>
    //                                             <strong>Stok Cns:</strong> ${stock.cns_stock_awal}
    //                                         </label>
    //                                     </div>
    //                                 </div>
    //                             `);
    //                     });
    //                 } else {
    //                     stockContainer.append('<div class="col-12 text-center">Data stok tidak ditemukan</div>');
    //                 }
    //                 $("#modalStock").modal("show");
    //             },
    //             error: function() {
    //                 alert("Gagal mengambil data stok");
    //             }
    //         });
    //     });
    // });
</script>


<?php $this->endSection(); ?>