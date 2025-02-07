<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>

<style>
    :root {
        --primary-color: #2e7d32;
        /* secondary color is abu-abu*/
        --secondary-color: #778899;
        --background-color: #f4f7fa;
        --card-background: #ffffff;
        --text-color: #333333;
    }

    body {
        background-color: var(--background-color);
        color: var(--text-color);
        font-family: 'Arial', sans-serif;
    }

    .container-fluid {
        /* max-width: 1200px; */
        margin: 0 auto;
        padding: 2rem;
    }

    .card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-control {
        border: none;
        border-bottom: 2px solid var(--primary-color);
        border-radius: 0;
        padding: 0.75rem 0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: var(--secondary-color);
    }

    .btn {
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }

    .result-card {
        background-color: var(--card-background);
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .result-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')) : ?>
        <script>
            $(document).ready(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#4a90e2'
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
                    confirmButtonColor: '#4a90e2'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card">
        <h3 class="mb-4">Stock Material Search</h3>
        <form method="post" action="">
            <div class="row g-3">
                <div class="col-lg-5 col-sm-12">
                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model / Cluster">
                </div>
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
                </div>
                <div class="col-lg-3 col-sm-12 d-flex gap-2">
                    <button class="btn btn-info flex-grow-1" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                    <button class="btn btn-secondary flex-grow-1" id="reset_data"><i class="fas fa-redo"></i> Reset</button>
                </div>
            </div>
        </form>
    </div>

    <div id="result"></div>
</div>

<script>
    $(document).ready(function() {
        $('#filter_data').click(function(e) {
            e.preventDefault();
            let noModel = $.trim($('input[name="noModel"]').val());
            let warna = $.trim($('input[name="warna"]').val());

            $.ajax({
                url: "<?= base_url(session()->get('role') . '/warehouse/search') ?>",
                method: "POST",
                dataType: "json", // Pastikan menerima data dalam format JSON
                data: {
                    noModel,
                    warna
                },
                success: function(response) {
                    let output = "";

                    if (response.length === 0) {
                        output = `<div class="alert alert-warning text-center">Data tidak ditemukan</div>`;
                    } else {
                        response.forEach(item => {
                            output += `
                        <div class="result-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="badge bg-info">Cluster: ${item.nama_cluster} | No Model: ${item.no_model}</h5>
                                <span class="badge bg-secondary">Jenis: ${item.item_type}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <p><strong>Lot Jalur:</strong> ${item.lot_stock}</p>
                                    <p><strong>Space:</strong> ${item.kapasitas || 0} KG</p>
                                    <p><strong>Sisa Space:</strong> ${item.sisa_space || 0} KG</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                                    <p><strong>Warna:</strong> ${item.warna}</p>
                                    <p><strong>Total KGs:</strong> ${item.Kgs || 0} KG | <strong>Total KRG:</strong> ${item.Krg || 0}</p>
                                </div>
                                <div class="col-md-4 d-flex flex-column gap-2">
                                    <button class="btn btn-outline-info btn-sm">In/Out</button>
                                    <button class="btn btn-outline-info btn-sm pindahPalet" data-id="${item.id_stock}">Pindah Pallet</button>
                                    <button class="btn btn-outline-info btn-sm pindahOrder" data-id="${item.id_stock}">Pindah Order</button>
                                </div>
                            </div>
                        </div>`;
                        });
                    }

                    $('#result').html(output);
                },
                error: function(xhr, status, error) {
                    $('#result').html(`<div class="alert alert-danger text-center">Terjadi kesalahan: ${error}</div>`);
                }
            });

            $('#reset_data').click(function(e) {
                e.preventDefault();
                $('input[name="noModel"]').val('');
                $('input[name="warna"]').val('');
                $('#result').html('');
            });
        });
        $(document).on('click', '.pindahPalet', function() {
            let idStock = $(this).data('id'); // Ambil id_stock dari tombol yang diklik

            $.ajax({
                url: '<?= base_url(session()->get('role') . '/warehouse/sisaKapasitas') ?>',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let clusterOptions = `<option value="">Pilih Cluster</option>`;

                        response.data.forEach(cluster => {
                            clusterOptions += `<option value="${cluster.nama_cluster}">
                        ${cluster.nama_cluster} (Sisa Space: ${cluster.sisa_space} KG)
                    </option>`;
                        });

                        Swal.fire({
                            title: 'Pindah Pallet',
                            html: `
                            <label for="clusterSelect">Pilih Cluster</label>
                            <select id="clusterSelect" name="namaCluster" class="form-control">
                                ${clusterOptions}
                            </select>
                            
                            <label for="kgs">KGs Pindah Pallet</label>
                            <input type="number" name="kgs" min=1 class="form-control mt-2" placeholder="Jumlah Pindah (KG)">

                            <label for"cones">Cones Pindah Pallet</label>
                            <input type="number" name="cones" min=1 class="form-control mt-2" placeholder="Jumlah Pindah (Cones)">

                            <label for="krg">KRG Pindah Pallet</label>
                            <input type="number" name="krg" min=1 class="form-control mt-2" placeholder="Jumlah Pindah (KRG)">
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Pindah',
                            confirmButtonColor: '#2e7d32',
                            cancelButtonColor: '#778899',
                            preConfirm: () => {
                                const selectedCluster = Swal.getPopup().querySelector('#clusterSelect').value;
                                if (!selectedCluster) {
                                    Swal.showValidationMessage(`Silahkan pilih cluster`);
                                }
                                return selectedCluster;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // AJAX untuk update nama_cluster di stock
                                $.ajax({
                                    url: '<?= base_url(session()->get('role') . '/warehouse/updateCluster') ?>',
                                    method: 'POST',
                                    data: {
                                        id_stock: idStock,
                                        nama_cluster: result.value
                                    },
                                    dataType: 'json',
                                    success: function(updateResponse) {
                                        if (updateResponse.success) {
                                            Swal.fire('Pindah Pallet', 'Berhasil', 'success')
                                                .then(() => location.reload()); // Refresh halaman
                                        } else {
                                            Swal.fire('Error', 'Gagal memperbarui cluster', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Terjadi kesalahan saat mengupdate', 'error');
                                    }
                                });
                            }
                        });

                    } else {
                        Swal.fire('Error', 'Tidak ada data cluster', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            });
        });

        $(document).on('click', '.pindahOrder', function() {
            let idStock = $(this).data('id'); // Ambil id_stock dari tombol yang diklik

            $.ajax({
                url: '<?= base_url(session()->get('role') . '/warehouse/getNoModel') ?>',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let noModelOptions = `<option value="">Pilih No Model</option>`;

                        response.data.forEach(noModel => {
                            noModelOptions += `<option value="${noModel.no_model}">
                        ${noModel.no_model} | ${noModel.item_type} | ${noModel.kode_warna}
                    </option>`;
                        });

                        Swal.fire({
                            title: 'Pindah Order',
                            html: `
                            <label for="noModelSelect">Pilih No Model</label>
                    <select id="noModelSelect" name="noModel" class="form-control">
                        ${noModelOptions}
                    </select>
                    <label for="qty">Jumlah Pindah (KG)</label>
                    <input type="number" name="qty" min=1 class="form-control mt-2" placeholder="Jumlah Pindah (KG)">
                    `,
                            showCancelButton: true,
                            confirmButtonText: 'Pindah',
                            confirmButtonColor: '#2e7d32',
                            cancelButtonColor: '#778899',
                            preConfirm: () => {
                                const selectedNoModel = Swal.getPopup().querySelector('#noModelSelect').value;
                                if (!selectedNoModel) {
                                    Swal.showValidationMessage(`Silahkan pilih No Model`);
                                }
                                return selectedNoModel;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // AJAX untuk update no_model di stock
                                $.ajax({
                                    url: '<?= base_url(session()->get('role') . '/warehouse/updateNoModel') ?>',
                                    method: 'POST',
                                    data: {
                                        id_stock: idStock,
                                        no_model: result.value
                                    },
                                    dataType: 'json',
                                    success: function(updateResponse) {
                                        if (updateResponse.success) {
                                            Swal.fire('Pindah Order', 'Berhasil', 'success')
                                                .then(() => location.reload()); // Refresh halaman
                                        } else {
                                            Swal.fire('Error', 'Gagal memperbarui No Model', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Terjadi kesalahan saat mengupdate', 'error');
                                    }
                                });
                            }
                        });

                    } else {
                        Swal.fire('Error', 'Tidak ada data No Model', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            });
        });
    });
</script>

<?php $this->endSection(); ?>