<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter PO <?= $jenis ?></h5>
            </div>
            <div class="row mt-2">
                <div class="col-md-9">
                    <label for="">Key</label>
                    <input type="text" class="form-control" id="key" placeholder="No Model/Item Type/Kode Warna/Warna">
                </div>
                <div class="col-md-3">
                    <label for="">Aksi</label><br>
                    <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                    <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                    <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <!-- card loading -->
            <div class="card loading" id="loadingCard" style="display: none;">
                <div class="card-body text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover table-striped text-center text-uppercase font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center text-uppercase">No</th>
                            <th rowspan="2" class="text-center text-uppercase">Waktu Input</th>
                            <th rowspan="2" class="text-center text-uppercase">Tanggal PO</th>
                            <th rowspan="2" class="text-center text-uppercase">Foll Up</th>
                            <th rowspan="2" class="text-center text-uppercase">No Model</th>
                            <th rowspan="2" class="text-center text-uppercase">No Order</th>
                            <th rowspan="2" class="text-center text-uppercase">Area</th>
                            <th rowspan="2" class="text-center text-uppercase">Memo</th>
                            <th rowspan="2" class="text-center text-uppercase">Buyer</th>
                            <th rowspan="2" class="text-center text-uppercase">Start MC</th>
                            <th rowspan="2" class="text-center text-uppercase">Delivery Awal</th>
                            <th rowspan="2" class="text-center text-uppercase">Delivery Akhir</th>
                            <th rowspan="2" class="text-center text-uppercase">Order Type</th>
                            <th rowspan="2" class="text-center text-uppercase">Item Type</th>
                            <th rowspan="2" class="text-center text-uppercase">Warna</th>
                            <th rowspan="2" class="text-center text-uppercase">Kode Warna</th>
                            <th rowspan="2" class="text-center text-uppercase">Material Type</th>
                            <th colspan="2" class="text-center text-uppercase">Stock Awal</th>
                            <th colspan="2" class="text-center text-uppercase">Pesan</th>
                            <th colspan="4" class="text-center text-uppercase">PO Tambahan Gbn</th>
                            <th rowspan="2" class="text-center text-uppercase">Admin</th>
                            <th rowspan="2" class="text-center text-uppercase">Note</th>
                        </tr>
                        <tr>
                            <th class="text-center text-uppercase">Kg</th>
                            <th class="text-center text-uppercase">Lot</th>

                            <th class="text-center text-uppercase">Kg</th>
                            <th class="text-center text-uppercase">Loss</th>

                            <th class="text-center text-uppercase">Tgl Terima Po(+) Gbn</th>
                            <th class="text-center text-uppercase">Tgl Po(+) Area</th>
                            <th class="text-center text-uppercase">Delivery Po(+)</th>
                            <th class="text-center text-uppercase">Kg Po(+)</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
            "serverSide": false
        });

        function loadData() {
            let key = $('input[type="text"]').val().trim();
            const jenis = "<?= $jenis ?>";

            // Validasi
            if (key === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Key tidak boleh kosong!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterPoBenang') ?>",
                type: "GET",
                data: {
                    key: key,
                    jenis: jenis
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loadingCard").show(); // Tampilkan loading
                    // disable btn filter
                    $("#btnSearch").prop("disabled", true);
                },
                complete: function() {
                    $("#loadingCard").hide(); // Sembunyikan loading setelah selesai
                    // enable btn filter
                    $("#btnSearch").prop("disabled", false);
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.tgl_input,
                                item.lco_date,
                                item.foll_up,
                                item.no_model,
                                item.no_order,
                                item.area,
                                item.memo,
                                item.buyer,
                                item.start_mc,
                                item.delivery_awal,
                                item.delivery_akhir,
                                item.unit || '',
                                item.item_type,
                                item.color,
                                item.kode_warna,
                                item.material_type,
                                parseFloat(item.kgs_stock || 0).toFixed(2),
                                item.lot_stock || '',
                                parseFloat(item.kg_po).toFixed(2) || '',
                                item.loss + '%' || '',
                                item.tanggal_approve || '',
                                item.tgl_po_plus_area || '',
                                item.delivery_po_plus || '',
                                parseFloat(item.kg_po_plus || 0).toFixed(2) || '',
                                item.admin || '',
                                item.note || '' //note disini 
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        let colCount = $('#dataTable thead th').length;
                        $('#dataTable tbody').html(`
                            <tr>
                                <td colspan="${colCount}" class="text-center text-danger font-weight-bold">
                                    ⚠ Tidak ada data ditemukan
                                </td>
                            </tr>
                        `);

                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let key = $('input[type="text"]').val();
            const jenis = "<?= $jenis ?>";
            window.location.href = "<?= base_url($role . '/warehouse/exportPoBenang') ?>?key=" + key + "&jenis=" + jenis;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });

    $(document).ready(function() {
        // Trigger pencarian saat tombol Enter ditekan di input apa pun
        $('#key').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Hindari form submit default (jika ada form)
                $('#btnSearch').click(); // Trigger tombol Search
            }
        });
    });
</script>


<?php $this->endSection(); ?>