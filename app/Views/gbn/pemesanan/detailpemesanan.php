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
                <a href="<?= base_url($role . '/pemesanan/' . $area . '/' . $jenis) ?>" class="btn bg-gradient-info"> Kembali</a>
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Gbn</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Admin</th>
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
                                        <td><?= $data['ket_gbn'] ?></td>
                                        <td><?= $data['admin'] ?></td>
                                        <td>

                                            <!-- button pesan ke covering -->

                                            <?php if ($data['jenis'] === 'SPANDEX' || $data['jenis'] === 'KARET'): ?>
                                                <?php if (!$data['sudah_pesan_spandex']): ?>
                                                    <button
                                                        type="button"
                                                        class="btn bg-gradient-info btn-open-modal-pesan"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalPesan"
                                                        data-id="<?= $data['id_total_pemesanan'] ?>"
                                                        data-jenis="<?= $data['jenis'] ?>"
                                                        data-ketGbn="<?= $data['ket_gbn'] ?>"
                                                        data-action="<?= base_url($role . '/pesanKeCovering/' . $data['id_total_pemesanan']) ?>">
                                                        <i class="fas fa-layer-group"></i> Pesan <?= ucfirst($data['jenis']) ?>
                                                    </button>
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

<!-- Modal Pesan ke Covering -->
<div class="modal fade" id="modalPesan" tabindex="-1" aria-labelledby="modalPesanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPesanLabel">Pesan ke Covering</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPesan" method="GET" action="<?= base_url($role . '/pesanKeCovering/'. $data['id_total_pemesanan']) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_total_pemesanan" id="pesan_id_total_pemesanan">
                <input type="hidden" name="jenis" id="pesan_jenis">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pesan_keterangan_gbn" class="form-label">Keterangan GBN</label>
                        <textarea class="form-control text-uppercase" id="pesan_keterangan_gbn" name="keterangan_gbn" rows="3" placeholder="Tulis keterangan GBN di sini..."></textarea>
                    </div>

                    <!-- (Opsional) tambahkan field lain kalau perlu -->
                    <!--
          <div class="mb-3">
            <label class="form-label">PO Tambahan</label>
            <input type="text" class="form-control" name="po_tambahan" placeholder="Contoh: PO-12345">
          </div>
          -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn bg-gradient-info">
                        <i class="fas fa-paper-plane"></i> Kirim Pesanan
                    </button>
                </div>
            </form>

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
    // ketika tombol "Pesan" diklik, set form modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-modal-pesan');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');
        const jenis = btn.getAttribute('data-jenis');

        // set form action & hidden inputs
        const form = document.getElementById('formPesan');
        form.setAttribute('action', action);
        document.getElementById('pesan_id_total_pemesanan').value = id;
        document.getElementById('pesan_jenis').value = jenis;

        // optional: reset textarea tiap buka
        document.getElementById('pesan_keterangan_gbn').value = btn.getAttribute('data-ketGbn') || '';
    });

    // opsional: UX kecil saat submit
    document.getElementById('formPesan').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Mengirim...';
    });
</script>


<?php $this->endSection(); ?>