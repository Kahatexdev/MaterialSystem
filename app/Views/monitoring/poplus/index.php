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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cns Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
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
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= number_format($data['kg_poplus'], 2) ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['cns_poplus'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>">
                                    <a href="<?= base_url($role . '/poplus/detail?area=' . $data['admin'] . '&tgl_poplus=' . $data['tgl_poplus'] . '&no_model=' . $data['no_model'] . '&item_type=' . $data['item_type'] . '&kode_warna=' . $data['kode_warna'] . '&warna=' . $data['color'] . '&status=' . $data['status']) ?>" class="btn btn-info btn-sm">
                                        Detail
                                    </a>
                                    <?php if ($isNotApproved): ?>
                                        <button class="btn btn-info btn-sm btn-warning btn-approve" data-id="<?= $data['id_po_tambahan'] ?>" data-tgl="<?= $data['tgl_poplus'] ?>" data-model="<?= $data['no_model'] ?>" data-type="<?= $data['item_type'] ?>"
                                            data-warna="<?= $data['kode_warna'] ?>" data-status="<?= $data['status'] ?>" data-area="<?= $data['admin'] ?>" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            Approve
                                        </button>
                                    <?php else: ?>
                                        <a class="btn btn-success btn-sm" href="<?= base_url($role . '/masterdata/poGabungan/' . $data['jenis']) ?>"> BUKA PO (+)
                                        </a> <!-- Font Awesome centang hijau -->
                                    <?php endif; ?>
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
<div class="modal fade  bd-example-modal-approve" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="modalCancel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Approve</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url($role . '/approvePoPlusArea') ?>" method="post">
                    <input type="hidden" name="id_po_tambahan" id="modal-id">
                    <input type="hidden" name="tgl_poplus" id="modal-tgl">
                    <input type="hidden" name="no_model" id="modal-model">
                    <input type="hidden" name="item_type" id="modal-type">
                    <input type="hidden" name="kode_warna" id="modal-warna">
                    <input type="hidden" name="status" id="modal-status">
                    <input type="hidden" name="area" id="modal-area">
                    Are You Sure Want to Approve ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-gradient-success">Yes</button>
            </div>
            </form>
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

    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-approve');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-id').value = this.getAttribute('data-id');
                document.getElementById('modal-tgl').value = this.getAttribute('data-tgl');
                document.getElementById('modal-model').value = this.getAttribute('data-model');
                document.getElementById('modal-type').value = this.getAttribute('data-type');
                document.getElementById('modal-warna').value = this.getAttribute('data-warna');
                document.getElementById('modal-status').value = this.getAttribute('data-status');
                document.getElementById('modal-area').value = this.getAttribute('data-area');
            });
        });
    });
</script>
<?php $this->endSection(); ?>