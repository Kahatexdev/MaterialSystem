<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">

<!-- DataTables + Responsive (tambahkan jika belum ada) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<style>
    /* Responsiveness tweaks */
    @media (max-width: 576px) {
        .dataTables_wrapper .dataTables_filter {
            float: none;
            text-align: left;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            white-space: nowrap;
            font-size: .75rem;
        }

        .btn {
            padding: .4rem .6rem;
            font-size: .8rem;
        }

        .card-body {
            padding: .75rem;
        }
    }

    /* Biar tombol submit di modal nempel kanan tapi tetap wrap di mobile */
    .text-end-mobile {
        text-align: right;
    }

    @media (max-width: 576px) {
        .text-end-mobile {
            text-align: left;
        }
    }
</style>

<div class="container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <script>
            $(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Detail Stok -->
    <div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
        <!-- fullscreen di mobile, lg di desktop -->
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStockLabel">Detail Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <div id="stockData" class="row g-3"></div>
                        <div class="text-end-mobile">
                            <button type="submit" class="btn bg-gradient-info mt-3">Pilih Stok</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Header + Back -->
    <div class="card card-frame">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 fw-bold">Data Pemesanan <?= $jenis; ?> <?= $area; ?> <?= $tglPakai; ?></h5>
            <a href="<?= base_url($role . '/pemesanan/' . $area . '/' . $jenis) ?>" class="btn bg-gradient-info">Kembali</a>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display nowrap text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th data-priority="5">No</th>
                            <th data-priority="3">Tanggal Pakai Area</th>
                            <th data-priority="4">Area</th>
                            <th data-priority="1">No Model</th>
                            <th data-priority="2">Item Type</th>
                            <th data-priority="6">Kode Warna</th>
                            <th data-priority="7">Warna</th>
                            <th data-priority="8">Jalan Mc</th>
                            <th data-priority="9">Total Kgs Pesan</th>
                            <th data-priority="10">Total Cns Pesan</th>
                            <th data-priority="11">Lot Pesan</th>
                            <th data-priority="12">Keterangan Area</th>
                            <th data-priority="13">Po Tambahan</th>
                            <th data-priority="2">Kg/Krg Kirim</th>
                            <th data-priority="4">Lot Kirim</th>
                            <th data-priority="5">Cluster Out</th>
                            <th data-priority="14">Keterangan Gbn</th>
                            <th data-priority="6">Admin</th>
                            <th data-priority="1">Cluster</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dataPemesanan)): $no = 1;
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
                                        <?php if ($data['jenis'] === 'SPANDEX' || $data['jenis'] === 'KARET'): ?>
                                            <?php if (!$data['sudah_pesan_spandex']): ?>
                                                <button
                                                    type="button"
                                                    class="btn bg-gradient-info btn-sm btn-open-modal-pesan"
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
                                                class="btn bg-gradient-info btn-sm">
                                                <i class="fas fa-layer-group"></i> Pilih
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($dataPemesanan)) : ?>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 text-center">
                            <p class="mb-0">No data available in the table.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Pesan ke Covering -->
<div class="modal fade" id="modalPesan" tabindex="-1" aria-labelledby="modalPesanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPesanLabel">Pesan ke Covering</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formPesan" method="GET" action="<?= base_url($role . '/pesanKeCovering/' . $data['id_total_pemesanan']) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id_total_pemesanan" id="pesan_id_total_pemesanan">
                <input type="hidden" name="jenis" id="pesan_jenis">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pesan_keterangan_gbn" class="form-label">Keterangan GBN</label>
                        <textarea class="form-control text-uppercase" id="pesan_keterangan_gbn" name="keterangan_gbn" rows="3" placeholder="Tulis keterangan GBN di sini..."></textarea>
                    </div>
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

<!-- JS (pastikan jQuery & Bootstrap sudah ada di header) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    // DataTables Responsive
    $('#dataTable').DataTable({
        responsive: true, // aktifkan responsive
        pageLength: 25,
        order: [],
        autoWidth: false,
        // Prioritas kolom (kecil = lebih penting untuk tetap tampil)
        columnDefs: [{
                responsivePriority: 1,
                targets: [3, 18]
            }, // No Model & Cluster (aksi)
            {
                responsivePriority: 2,
                targets: [4, 13]
            }, // Item Type & Kg/Krg Kirim
            {
                responsivePriority: 3,
                targets: [1, 2, 14]
            }, // Tgl Pakai, Area, Lot Kirim
            // sisanya biarkan collapsible
        ],
        // Optimisasi tampilan di mobile
        language: {
            lengthMenu: 'Tampil _MENU_',
            search: 'Cari:',
            info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
            paginate: {
                previous: '‹',
                next: '›'
            },
            emptyTable: 'Tidak ada data'
        }
    });

    // Set modal Pesan
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-open-modal-pesan');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');
        const jenis = btn.getAttribute('data-jenis');

        const form = document.getElementById('formPesan');
        form.setAttribute('action', action);
        document.getElementById('pesan_id_total_pemesanan').value = id;
        document.getElementById('pesan_jenis').value = jenis;
        document.getElementById('pesan_keterangan_gbn').value = btn.getAttribute('data-ketGbn') || '';
    });

    // UX kecil saat submit
    document.getElementById('formPesan').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Mengirim...';
    });
</script>

<?php $this->endSection(); ?>