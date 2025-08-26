<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">

    <!-- Button Filter -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Filter Pemakaian Nylon</h5>
                <button class="btn btn-secondary btn-block" id="btnInfo" style="padding: 5px 12px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#infoModal">
                    <i class="fas fa-info"></i>
                </button>
            </div>
            <div class="row mt-2">
                <div class="col-md-8">
                    <label for="">Buyer</label>
                    <input type="text" class="form-control" name="buyer" id="buyer" placeholder="Buyer">
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Aksi</label><br>
                        <button class="btn btn-info btn-block" id="btnSearch"><i class="fas fa-search"></i></button>
                        <button class="btn btn-danger" id="btnReset"><i class="fas fa-redo-alt"></i></button>
                        <button class="btn btn-primary d-none" id="btnExport"><i class="fas fa-file-excel"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3 font-weight-bolder">Tabel Pemakaian Nylon</h5>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Buyer</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis Material</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Pemakaian Kg</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Pemakaian Cones</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Pemakaian Karung</th>
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
        function loadData() {
            const buyer = $('#buyer').val();

            $.ajax({
                url: "<?= base_url($role . '/warehouse/filterPemakaianNylon') ?>",
                type: "GET",
                data: {
                    buyer: buyer
                },
                dataType: "json",
                success: function(response) {
                    let html = '';
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.buyer || ''}</td>
                                    <td>${item.jenis || ''}</td>
                                    <td>${(parseFloat(item.pemakaian_kgs) || 0).toFixed(2)}</td>
                                    <td>${item.pemakaian_cns || ''}</td>
                                    <td>${item.pemakaian_krg || ''}</td>
                                </tr>
                            `;
                        });
                        $('#dataTable tbody').html(html);
                        $('#btnExport').removeClass('d-none');
                    } else {
                        let colCount = $('#dataTable thead th').length;
                        $('#dataTable tbody').html(`
                            <tr>
                                <td colspan="${colCount}" class="text-center text-danger font-weight-bold">
                                    ⚠ Tidak ada data ditemukan
                                </td>
                            </tr>
                        `);

                        $('#btnExport').addClass('d-none');
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
            const delivery = $('#delivery').val();
            const no_model = $('#no_model').val().trim();
            const kode_warna = $('#kode_warna').val().trim();
            const jenis = 'NYLON';
            const url = "<?= base_url($role . '/warehouse/exportPemakaianNylon') ?>" + "?buyer=" + encodeURIComponent(buyer);

            window.location.href = url;
        });

        dataTable.clear().draw();
    });

    // Fitur Reset
    $('#btnReset').click(function() {
        // Kosongkan input
        $('input[type="text"]').val('');

        // Kosongkan delivery
        $('#delivery').val('');

        // Kosongkan tabel hasil pencarian
        $('#dataTable tbody').html('');

        // Sembunyikan tombol Export Excel
        $('#btnExport').addClass('d-none');
    });
</script>
<script>
    $(document).ready(function() {
        // Trigger pencarian saat tombol Enter ditekan di input apa pun
        $('#buyer').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Hindari form submit default (jika ada form)
                $('#btnSearch').click(); // Trigger tombol Search
            }
        });
    });
</script>

<?php $this->endSection(); ?>