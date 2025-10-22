<?php $this->extend($role . '/poplus/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    /* Style umum untuk semua DataTables Buttons */
    .dt-button {
        background-color: #28a745 !important;
        /* hijau */
        color: #ffffff !important;
        /* putih */
        border: 1px solid #1e7e34 !important;
        /* border gelap */
        border-radius: 4px !important;
        padding: 6px 12px !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        text-transform: none !important;
        box-shadow: none !important;
        cursor: pointer !important;
        margin-bottom: 1rem !important;
    }

    /* Hover/focus state */
    .dt-button:hover,
    .dt-button:focus {
        background-color: #218838 !important;
        border-color: #1c7430 !important;
        color: #ffffff !important;
        outline: none !important;
    }

    /* Jika kamu ingin khusus style Excel-button saja */
    .dt-button.buttons-excel {
        background-color: #218838 !important;
        /* biru */
        border-color: #1c7430 !important;
    }
</style>
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

    <!-- Header Card -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h5 class="mb-0 font-weight-bolder">List PO Tambahan Area</h5>
                </div>
                <div class="col-6 text-end">
                    <button class="btn btn-info ms-2">
                        <a href="<?= base_url($role . '/poplus/form_potambahan') ?>" class="fa fa-list text-white" style="text-decoration: none;"> Open PO(+)</a>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Jatah</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cns Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Bahan Baku di Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder" colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($poTambahan as $data):
                            // Cek apakah id_order ini ada di materialOrderIds
                            $isNotApproved = $data['status'] == '';
                        ?>
                            <tr>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['tgl_poplus'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['admin'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['no_model'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['item_type'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['kode_warna'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['color'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= number_format($data['sisa_jatah'], 2) ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= number_format($data['kg_poplus'], 2) ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['cns_poplus'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['sisa_bb_mc'] ?></td>

                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>">
                                    <a href="<?= base_url($role . '/poplus/detail?area=' . $data['admin'] . '&tgl_poplus=' . $data['tgl_poplus'] . '&no_model=' . $data['no_model'] . '&item_type=' . $data['item_type'] . '&kode_warna=' . $data['kode_warna'] . '&warna=' . $data['color'] . '&status=' . $data['status']) ?>" class="btn btn-info btn-sm">
                                        Detail
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= base_url($role . '/poplus/editPoTambahan?area=' . $data['admin'] . '&tgl_poplus=' . $data['tgl_poplus'] . '&no_model=' . $data['no_model'] . '&item_type=' . $data['item_type'] . '&kode_warna=' . $data['kode_warna'] . '&warna=' . $data['color'] . '&status=' . $data['status']) ?>" class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($poTambahan)) : ?>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            pageLength: 35,
            order: [],
            dom: 'Bfrtip', // B = Buttons
            buttons: [{
                extend: 'excelHtml5',
                text: 'Download Excel',
                titleAttr: 'Export ke Excel',
                exportOptions: {
                    columns: ':not(:last-child)',
                    modifier: {
                        search: 'applied'
                    }
                }
            }]
        });

    });
</script>
<?php $this->endSection(); ?>