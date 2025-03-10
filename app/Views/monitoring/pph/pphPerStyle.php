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
                            <input type="text" class="form-control" name="nomodel" id="nomodel" placeholder="No Model">
                            <button class="btn btn-filter" id="filter_date_range">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
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
                    <table id="example" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Produksi (Pcs)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Sisa</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">Total Pemakaian</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ps-2">% Pemakaian</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                searching: false, // Nonaktifkan pencarian bawaan
                paging: true,
                ordering: false,
                info: true,
                ajax: {
                    url: "<?= base_url($role . '/tampilPerStyle/' . $area) ?>",
                    type: "POST",
                    data: function(d) {
                        d.nomodel = $('#nomodel').val(); // Kirim data No Model saat tombol filter diklik
                    },
                    dataSrc: function(json) {
                        return json.data; // Pastikan data diambil dari properti 'data'
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Nomor urut
                        }
                    },
                    {
                        data: "machinetypeid" //machinetypeid
                    },
                    {
                        data: "no_model"
                    },
                    {
                        data: "area"
                    },
                    {
                        data: "inisial"
                    },
                    {
                        data: "style_size"
                    },
                    {
                        data: "item_type"
                    },
                    {
                        data: "color"
                    },
                    {
                        data: "kode_warna"
                    },
                    {
                        data: "loss"
                    },
                    {
                        data: "composition"
                    },
                    {
                        data: "gw"
                    },
                    {
                        data: "qty_pcs"
                    },
                    {
                        data: null //qty_produksi
                    },
                    {
                        data: null //sisa
                    },
                    {
                        data: "kgs"
                    },
                    {
                        data: null
                    },
                    {
                        data: null
                    }
                ]
            });

            $('#filter_date_range').click(function() {
                table.ajax.reload(); // Refresh tabel saat tombol filter diklik
            });
        });
    </script>

    <?php $this->endSection(); ?>