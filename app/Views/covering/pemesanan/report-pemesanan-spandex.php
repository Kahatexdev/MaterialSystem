<?php $this->extend($role . '/pemesanan/header'); ?>
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
        <div class="card-body p-4 rounded-top-4" style="background-color: #344767;">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-filter text-white me-3 fs-4"></i>
                <h4 class="mb-0 fw-bold" style="color: white;">Filter Pemesanan Spandex</h4>
            </div>
        </div>
        <div class="card-body bg-white rounded-bottom-0 p-4">
            <div class="row gy-4">
                <div class="col-md-6">
                    <label for="">Tanggal Awal (Tanggal Pakai)</label>
                    <input type="date" class="form-control" id="tglAwal">
                </div>
                <div class="col-md-6">
                    <label for="">Tanggal Akhir (Tanggal Pakai)</label>
                    <input type="date" class="form-control" id="tglAkhir">
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="btn-group" role="group">
                            <button class="btn text-white px-4" id="btnSearch" style="background-color: #344767;">
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal Pakai</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Global</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Per Area</th>
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
            let tanggal_awal = $('#tglAwal').val().trim();
            let tanggal_akhir = $('#tglAkhir').val().trim();

            // Validasi: Jika semua input kosong, tampilkan alert dan hentikan pencarian
            if (tanggal_awal === '' && tanggal_akhir === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Apa yang mau dicari cuy?',
                });
                return;
            }

            // Validasi 2: Salah satu tanggal doang yang diisi
            if ((tanggal_awal !== '' && tanggal_akhir === '') || (tanggal_awal === '' && tanggal_akhir !== '')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggalnya Belum Diisi',
                    text: 'Isi kedua tanggal kalau mau filter berdasarkan tanggal, jangan setengah-setengah cuy.',
                });
                return;
            }

            $.ajax({
                url: "<?= base_url($role . '/filterPemesananSpandexCovering') ?>",
                type: "GET",
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir
                },
                dataType: "json",
                success: function(response) {
                    dataTable.clear().draw();
                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            var tgl = item;
                            var jenis = "SPANDEX";

                            var link1 = '<a href="<?= base_url($role . '/excelPemesananCovering') ?>?tgl_pakai=' + tgl + '&jenis=' + jenis + '" target="_blank">' +
                                '<i class="fas fa-file-excel"></i>' +
                                '</a>';

                            var link2 = '<a href="<?= base_url($role . '/excelPemesananCoveringPerArea') ?>?tgl_pakai=' + tgl + '&jenis=' + jenis + '" target="_blank">' +
                                '<i class="fas fa-file-excel"></i>' +
                                '</a>';

                            dataTable.row.add([
                                index + 1,
                                tgl,
                                link1,
                                link2
                            ]).draw(false);
                        });

                        $('#btnExport').removeClass('d-none'); // Munculkan tombol Export Excel
                    } else {
                        $('#btnExport').addClass('d-none'); // Sembunyikan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    // console.error("Error:", error);
                }
            });
        }

        $('#btnSearch').click(function() {
            loadData();
        });

        // $('#btnExport').click(function() {
        //     let tanggal_awal = $('#tglAwal').val().trim();
        //     let tanggal_akhir = $('#tglAkhir').val().trim();
        //     window.location.href = "<?= base_url($role . '/excelPemesananSpandexCovering') ?>?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir;
        // });

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