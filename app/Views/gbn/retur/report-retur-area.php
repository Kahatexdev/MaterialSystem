<?php $this->extend($role . '/retur/header'); ?>
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
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Retur Area</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-3">
                    <label for="area">Area</label>
                    <select class="form-select mt-2" name="area" id="area">
                        <option value="">Pilih Area</option>
                        <option value="KK1A">KK1A</option>
                        <option value="KK1B">KK1B</option>
                        <option value="KK2A">KK2A</option>
                        <option value="KK2B">KK2B</option>
                        <option value="KK2C">KK2C</option>
                        <option value="KK5G">KK5G</option>
                        <option value="KK7K">KK7K</option>
                        <option value="KK7L">KK7L</option>
                        <option value="KK8D">KK8D</option>
                        <option value="KK8F">KK8F</option>
                        <option value="KK8J">KK8J</option>
                        <option value="KK9D">KK9D</option>
                        <option value="KK10">KK10</option>
                        <option value="KK11M">KK11M</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="kategoriRetur">Nama Kategori</label>
                    <select class="form-select mt-2" name="kategori_retur" id="kategori_retur">
                        <option value="">Pilih Kategori Retur</option>
                        <?php foreach ($getKategori as $kategori) : ?>
                            <option value="<?= $kategori['nama_kategori'] ?>">
                                <?= $kategori['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Retur Dari</label>
                    <input type="date" class="form-control" id="tgl_retur_dari">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="">Tanggal Retur Sampai</label>
                    <input type="date" class="form-control" id="tgl_retur_sampai">
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
            <div class="table-responsive">
                <table id="dataTable" class="display text-center text-uppercase text-xs font-bolder" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jenis</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Loss</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Karung Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Kirim</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Qty Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cones Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Karung Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">LOT Retur</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kategori</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Gbn</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Waktu Acc</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">User</th>
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
            "searching": false,
            "ordering": true,
            "info": true,
            "responsive": true,
            "processing": true,
            "serverSide": false
        });

        function loadData() {
            let area = $('#area').val().trim();
            let kategori = $('#kategori_retur').val().trim();
            let tanggal_awal = $('#tgl_retur_dari').val().trim();
            let tanggal_akhir = $('#tgl_retur_sampai').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (area === '' && kategori === '' && tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih salah satu filter untuk menampilkan data retur!',
                });
                return;
            }


            $.ajax({
                url: "<?= base_url($role . '/retur/filterReturArea') ?>",
                type: "GET",
                data: {
                    area: area,
                    kategori: kategori,
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            dataTable.row.add([
                                index + 1,
                                item.jenis,
                                item.tgl_retur,
                                item.area_retur,
                                item.no_model,
                                item.item_type,
                                item.kode_warna,
                                item.warna,
                                item.loss,
                                item.total_kgs,
                                item.qty_po_plus !== undefined && item.qty_po_plus !== null ? item.qty_po_plus : 0,
                                item.kg_kirim,
                                item.cns_kirim,
                                item.krg_kirim,
                                item.lot_out,
                                item.kg,
                                item.cns,
                                item.karung,
                                item.lot_retur,
                                item.kategori,
                                item.keterangan_area,
                                item.keterangan_gbn,
                                item.waktu_acc_retur,
                                item.admin,
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
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
            let area = $('#area').val().trim();
            let kategori = $('#kategori_retur').val().trim();
            let tanggal_awal = $('#tgl_retur_dari').val().trim();
            let tanggal_akhir = $('#tgl_retur_sampai').val().trim();
            window.location.href = "<?= base_url($role . '/retur/exportReturArea') ?>?area=" + area + "&kategori=" + kategori + "&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
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
</script>


<?php $this->endSection(); ?>