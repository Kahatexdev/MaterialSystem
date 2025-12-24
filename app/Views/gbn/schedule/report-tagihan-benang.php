<?php $this->extend($role . '/schedule/header'); ?>
<?php $this->section('content'); ?>

<style>
    .custom-outline {
        color: #344767;
        border-color: #344767;
    }

    .custom-outline:hover {
        background-color: #344767;
        color: white;
    }
</style>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card border-0 rounded-top-4 shadow-lg">
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Data Tagihan Benang</h4>
                <button class="btn btn-secondary btn-block ms-auto" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>

        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6">
                    <label for="noModel">No Model</label>
                    <input type="text" class="form-control" id="noModel" placeholder="Input No Model">
                </div>
                <div class="col-md-6">
                    <label for="kodeWarna">Kode Warna</label>
                    <input type="text" class="form-control" id="kodeWarna" placeholder="Input Kode Warna">
                </div>
                <div class="col-md-6">
                    <label for="deliveryAwal">Delivery Awal</label>
                    <input type="date" class="form-control" id="deliveryAwal">
                </div>
                <div class="col-md-6">
                    <label for="deliveryAkhir">Delivery Akhir</label>
                    <input type="date" class="form-control" id="deliveryAkhir">
                </div>
                <div class="col-md-6">
                    <label for="startMcFrom">Tgl Start MC (Awal)</label>
                    <input type="date" class="form-control" id="startMcFrom">
                </div>
                <div class="col-md-6">
                    <label for="startMcTo">Tgl Start MC (Akhir)</label>
                    <input type="date" class="form-control" id="startMcTo">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="btn-group" role="group">
                            <button class="btn text-white px-4" id="btnSearch" style="background-color: #344767">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <button class="btn btn-outline custom-outline" id="btnReset">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                        <button class="btn btn-info d-none" id="btnExport">
                            <i class="fas fa-file-excel me-2"></i>Ekspor
                        </button>
                    </div>
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
                <table id="dataTable" class="display text-center text-uppercase" style="width:100%">
                    <thead>
                        <tr>
                            <th class="font-weight-bolder">No</th>
                            <th class="font-weight-bolder">No Model</th>
                            <th class="font-weight-bolder">Item Type</th>
                            <th class="font-weight-bolder">Kode Warna</th>
                            <th class="font-weight-bolder">Warna</th>
                            <th class="font-weight-bolder">Area</th>
                            <th class="font-weight-bolder">Start Mc</th>
                            <th class="font-weight-bolder">Delivery Awal</th>
                            <th class="font-weight-bolder">Delivery Akhir</th>
                            <th class="font-weight-bolder">Qty PO</th>
                            <th class="font-weight-bolder">Qty PO(+)</th>
                            <th class="font-weight-bolder">Stock Awal</th>
                            <th class="font-weight-bolder">Stock Opname</th>
                            <th class="font-weight-bolder">Retur Stock</th>
                            <th class="font-weight-bolder">Total Qty Sch</th>
                            <th class="font-weight-bolder">Total Qty Datang Solid</th>
                            <th class="font-weight-bolder">Qty Ganti Retur Solid</th>
                            <th class="font-weight-bolder">Qty Retur Belang</th>
                            <th class="font-weight-bolder">Tagihan Datang Solid</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Info -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="infoModalLabel">Informasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p><strong>Note:</strong> Rumus tagihan datang :</p>

                <div class="mb-3">
                    <p>Apabila qty ganti retur > 0 maka :</p>
                    <div class="border p-2">
                        Tagihan Dtg = (Stock Awal + Stock Opname + Total Qty Datang + Qty Ganti Retur)-Qty PO - Qty PO(+) - Qty Retur
                    </div>
                </div>

                <div class="mb-3">
                    <p>Jika qty ganti retur = 0, maka :</p>
                    <div class="border p-2">
                        Tagihan Dtg = (Stock Awal + Stock Opname + Total Qty Datang)-Qty PO - Qty PO(+)
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTable = $('#dataTable').DataTable({
            paging: true,
            searching: false,
            ordering: true,
            info: true,
            responsive: true,
            processing: true,
            serverSide: false,
            columnDefs: [{
                targets: '_all',
                className: 'text-center'
            }]
        });

        function loadData() {
            let no_model = $('#noModel').val().trim();
            let kode_warna = $('#kodeWarna').val().trim();
            let delivery_awal = $('#deliveryAwal').val().trim();
            let delivery_akhir = $('#deliveryAkhir').val().trim();
            let tanggal_awal = $('#startMcFrom').val().trim();
            let tanggal_akhir = $('#startMcTo').val().trim();

            if (no_model === '' && kode_warna === '' && delivery_awal === '' && delivery_akhir === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih salah satu filter!',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/schedule/filterTagihanBenang') ?>",
                type: "GET",
                data: {
                    no_model: no_model,
                    kode_warna: kode_warna,
                    delivery_awal: delivery_awal,
                    delivery_akhir: delivery_akhir,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                beforeSend: function() {
                    $("#loadingCard").show(); // Tampilkan loading
                    // disable btn filter
                    $("#btnSearch").prop("disabled", true);
                    $('#btnReset').prop('disabled', true);
                    $('#btnExport').prop('disabled', true);
                },
                complete: function() {
                    $("#loadingCard").hide(); // Sembunyikan loading setelah selesai
                    // enable btn filter
                    $("#btnSearch").prop("disabled", false);
                    $('#btnReset').prop('disabled', false);
                    $('#btnExport').prop('disabled', false);
                },
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.no_model ?? '',
                                item.item_type ?? '',
                                item.kode_warna ?? '',
                                item.warna ?? '',
                                item.area ?? '',
                                item.start_mc ?? '',
                                item.delivery_awal ?? '',
                                item.delivery_akhir ?? '',
                                //dua angka di belakang koma
                                formatNumber(item.qty_po),
                                formatNumber(item.po_plus),
                                formatNumber(item.stock_awal),
                                formatNumber(item.stock_opname),
                                formatNumber(item.retur_stock),
                                formatNumber(item.qty_sch),
                                formatNumber(item.qty_datang_solid),
                                formatNumber(item.qty_ganti_retur_solid),
                                formatNumber(item.retur_belang),
                                formatNumber(item.tagihan_datang),
                                //sampai sini
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none');
                    } else {
                        $('#btnExport').addClass('d-none');
                        // Tampilkan alert bahwa hasil pencarian kosong
                        Swal.fire({
                            icon: 'info',
                            title: 'Data Tidak Ditemukan',
                            text: 'Pencarian dengan filter: no_model "' + no_model + '", Kode Warna "' + kode_warna + '", Tgl Start MC Dari "' + tanggal_awal + '", Tgl Start MC Sampai "' + tanggal_akhir + '" menghasilkan 0 data.',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        function formatNumber(num) {
            num = parseFloat(num ?? 0);
            return Number.isInteger(num) ? num.toString() : num.toFixed(2);
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        $('#btnExport').click(function() {
            let no_model = $('#noModel').val().trim();
            let kode_warna = $('#kodeWarna').val().trim();
            let tanggal_awal = $('#startMcFrom').val().trim();
            let tanggal_akhir = $('#startMcTo').val().trim();
            window.location.href = "<?= base_url($role . '/schedule/exportTagihanBenang') ?>?no_model=" + no_model + "&kode_warna=" + kode_warna + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');
        $('input[type="date"]').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });

    $(document).ready(function() {
        // Trigger pencarian saat tombol Enter ditekan di input apa pun
        $('#noModel, #kodeWarna, #deliveryAwal, #deliveryAkhir, #startMcFrom, #startMcTo').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Hindari form submit default (jika ada form)
                $('#btnSearch').click(); // Trigger tombol Search
            }
        });
    });
</script>


<?php $this->endSection(); ?>