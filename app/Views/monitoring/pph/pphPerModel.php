<?php $this->extend($role . '/pph/header'); ?>
<?php $this->section('content'); ?>
<style>
    #loading {
        display: none;
        /* Sembunyikan awalnya */
        position: fixed;
        /* Tetap di tengah layar */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        /* Biar di atas elemen lain */
        background: rgba(255, 255, 255, 0.7);
        /* Efek transparan */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>
<div class="container-fluid py-4">
    <!-- Filter Data / Form Pencarian -->
    <div class="row my-4">

        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">

            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Material System</p>
                                <h5 class="font-weight-bolder mb-0">PPH: Per Model</h5>
                            </div>
                        </div>
                        <div>

                            <div class="d-flex align-items-center">
                                <input type="text" name="no_model" id="no_model" class="form-control mr-2" placeholder="Masukkan No Model">
                                <button type="button" id="searchModel" class="btn bg-gradient-info text-white ms-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>


                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Tampilkan Tabel Hanya Jika Data Tersedia -->

    <div class="row mt-3">
        <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="row" id="HeaderRow">

                    </div>
                </div>
                <div class="card-body" id="bodyData">

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info text-center text-white" id="info" role="alert">
                Silakan masukkan No Model untuk mencari data.
            </div>
        </div>
    </div>
    <div id="loading" style="display: none;">
        <h3>Sedang Menghitung...</h3>
        <img src="<?= base_url('assets/spinner.gif') ?>" alt="Loading...">
    </div>

    <!-- Notifikasi Flashdata -->
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

    <!-- Script untuk inisialisasi DataTable dan Chart.js -->
    <script src="<?= base_url('assets/js/plugins/chartjs.min.js') ?>"></script>
    <script type="text/javascript">
        let btnSearch = document.getElementById('searchModel');

        btnSearch.onclick = function() {
            let model = document.getElementById('no_model').value;
            let area = <?= json_encode($area) ?>;
            let role = <?= json_encode($role) ?>;
            let loading = document.getElementById('loading');
            let info = document.getElementById('info');

            loading.style.display = 'block'; // T
            info.style.display = 'none'; // Sembunyikan loading setelah selesai

            $.ajax({
                url: "<?= base_url($role . '/getDataModel') ?>",
                type: "GET",
                data: {
                    model: model,
                    area: area
                },
                dataType: "json",
                success: function(response) {
                    fethcData(response, model, area);
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                },
                complete: function() {
                    loading.style.display = 'none'; // Sembunyikan loading setelah selesai
                }
            });
        };

        function fethcData(data, model, area) {
            let qty = parseFloat(data.qty / 24).toFixed(2);
            let sisa = parseFloat(data.sisa / 24).toFixed(2);
            let bruto = parseFloat(data.bruto / 24).toFixed(2);
            let bs_setting = parseFloat(data.bs_setting / 24).toFixed(2);
            let bs_mesin = parseInt(data.bs_mesin).toLocaleString();

            let header = document.getElementById('HeaderRow');

            header.innerHTML = ` 
            <h3>${model}</h3>
    <div class="col-lg-12">
<table class="table table-bordered">
    <tr>
          <td><strong>Area:</strong> ${area}</td>
        <td><strong>Produksi:</strong> ${bruto} dz</td>
    </tr>
    <tr>
     <td><strong>Qty:</strong> ${qty} dz</td>
        <td><strong>Bs Setting:</strong> ${bs_setting} dz</td>
    </tr>
    <tr>
          <td><strong>Sisa:</strong> ${sisa} dz</td>
        <td><strong>Bs Mesin:</strong> ${bs_mesin} gr</td>
    </tr>
  
</table>

    </div>`;

            let body = document.getElementById('bodyData')
            // Ambil kunci utama dalam objek
            let keys = Object.keys(data);

            // Filter untuk mendapatkan bahan baku (exclude qty, sisa, dll.)
            let materials = keys.filter(key => !["qty", "sisa", "bruto", "bs_setting", "bs_mesin"].includes(key));

            // Looping untuk buat baris tabel
            let rows = materials.map((material, index) => `
    <tr>
        <td>${index + 1}</td>
        <td>${data[material].item_type}</td>
        <td>${data[material].kode_warna}</td>
        <td>${data[material].warna}</td>
        <td>${data[material].pph.toFixed(2)} kg</td>
    </tr>
`).join('');

            body.innerHTML = `
    <div class="table-responsive">
        <table class="display text-center text-uppercase text-xs font-bolder" id="dataTable" style="width:100%">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Kode Warna</th>
                    <th class="text-center">Warna</th>
                    <th class="text-center">PPH</th>
                </tr>
            </thead>
            <tbody>
                ${rows} <!-- Data bahan baku masuk sini -->
            </tbody>
        </table>
    </div>`;

            // Inisialisasi DataTables
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true
                });
            });
        }
    </script>
</div>
<?php $this->endSection(); ?>