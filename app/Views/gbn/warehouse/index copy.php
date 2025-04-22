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
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="noModel" placeholder="Masukkan No Model / Cluster">
                </div>
                <div class="col-lg-4 col-sm-12">
                    <input class="form-control" type="text" name="warna" placeholder="Masukkan Kode Warna">
                </div>
                <div class="col-lg-4 col-sm-12 d-flex gap-2">
                    <button class="btn btn-info flex-grow-1" id="filter_data"><i class="fas fa-search"></i> Cari</button>
                    <button class="btn btn-secondary flex-grow-1" id="reset_data"><i class="fas fa-redo"></i> Reset</button>
                    <button type="button" class="btn btn-success flex-grow-1" id="export_excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>

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
                dataType: "json",
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
                            let totalKgs = item.Kgs && item.Kgs > 0 ? item.Kgs : item.KgsStockAwal;
                            let totalKrg = item.Krg && item.Krg > 0 ? item.Krg : item.KrgStockAwal;
                            if (totalKgs == 0 && totalKrg == 0) return;

                            output += `
                            <div class="result-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="badge bg-info">Cluster: ${item.nama_cluster} | No Model: ${item.no_model}</h5>
                                    <span class="badge bg-secondary">Jenis: ${item.item_type}</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <p><strong>Lot Jalur:</strong> ${item.lot_stock || item.lot_awal}</p>
                                        <p><strong>Space:</strong> ${item.kapasitas || 0} KG</p>
                                        <p><strong>Sisa Space:</strong> ${(item.sisa_space || 0).toFixed(2)} KG</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                                        <p><strong>Warna:</strong> ${item.warna}</p>
                                        <p><strong>Total Kgs:</strong> ${(parseFloat(totalKgs) || 0).toFixed(2)} KG | ${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out} Cones | ${totalKrg} KRG</p>
                                    </div>
                                    <div class="col-md-4 d-flex flex-column gap-2">
                                        <button class="btn btn-outline-info btn-sm pindahPalet" data-id="${item.id_stock}" data-cluster="${item.nama_cluster}" data-lot="${item.lot_stock}" data-kgs="${totalKgs}" data-cones="${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out}" data-krg="${totalKrg}">Pindah Palet</button>
                                        <button class="btn btn-outline-info btn-sm pindahOrder" data-id="${item.id_stock}" data-noModel="${item.no_model}" data-cluster="${item.nama_cluster}" data-lot="${item.lot_stock}" data-kgs="${totalKgs}" data-cones="${item.cns_stock_awal && item.cns_stock_awal > 0 ? item.cns_stock_awal : item.cns_in_out}" data-krg="${totalKrg}" data-itemType="${item.item_type}" data-kodeWarna="${item.kode_warna}">Pindah Order</button>
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
        });

        // Reset filter
        $('#reset_data').click(function(e) {
            e.preventDefault();
            $('input[name="noModel"]').val('');
            $('input[name="warna"]').val('');
            $('#result').html('');
        });

        // Export Excel
        $('#export_excel').on('click', function() {
            const noModel = $('input[name="noModel"]').val();
            const warna = $('input[name="warna"]').val();

            const query = `?no_model=${encodeURIComponent(noModel)}&warna=${encodeURIComponent(warna)}`;
            window.location.href = "<?= base_url(session()->get('role') . '/warehouse/exportExcel') ?>" + query;
        });


        $(document).on('click', '.pindahPalet', function() {
            let idStock = $(this).data('id'); // Ambil id_stock dari tombol yang diklik
            let clusterOld = $(this).data('cluster'); // Cluster saat ini
            let lot = $(this).data('lot');
            let kgs = $(this).data('kgs');
            let cones = $(this).data('cones');
            let krg = $(this).data('krg');

            $.ajax({
                url: '<?= base_url(session()->get('role') . '/warehouse/sisaKapasitas') ?>',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let clusterOptions = `<option value="">Pilih Cluster</option>`;

                        response.data.forEach(cluster => {
                            clusterOptions += `<option value="${cluster.nama_cluster}">
                        ${cluster.nama_cluster} (Sisa: ${cluster.sisa_space} KG)
                    </option>`;
                        });

                        Swal.fire({
                            title: 'Pindah Pallet',
                            html: `
                        <div class="text-left">
                            <p class="mb-2">Cluster Saat Ini: <strong>${clusterOld}</strong></p>
                        </div>

                        <label for="clusterSelect" class="font-weight-bold">Pilih Cluster</label>
                        <select id="clusterSelect" name="namaCluster" class="form-control mb-3">
                            ${clusterOptions}
                        </select>

                        <label for="kgs" class="font-weight-bold">Kgs Pindah</label>
                        <input type="number" id="kgs" name="kgs" min="1" max="${kgs}" value="${(parseFloat(kgs) || 0).toFixed(2)}" class="form-control mb-2" placeholder="Jumlah (KG)">

                        <label for="cones" class="font-weight-bold">Cones Pindah</label>
                        <input type="number" id="cones" name="cones" min="1" max="${cones}" value="${cones}" class="form-control mb-2" placeholder="Jumlah (Cones)">

                        <label for="krg" class="font-weight-bold">Krg Pindah</label>
                        <input type="number" id="krg" name="krg" min="1" max="${krg}" value="${krg}" class="form-control mb-3" placeholder="Jumlah (KRG)">
                    `,
                            showCancelButton: true,
                            confirmButtonText: 'Pindah',
                            confirmButtonColor: '#2e7d32',
                            cancelButtonText: 'Batal',
                            cancelButtonColor: '#778899',
                            customClass: {
                                popup: 'swal-wide'
                            },
                            preConfirm: () => {
                                const selectedCluster = Swal.getPopup().querySelector('#clusterSelect').value;
                                const inputKgs = Swal.getPopup().querySelector('#kgs').value;
                                const inputCones = Swal.getPopup().querySelector('#cones').value;
                                const inputKrg = Swal.getPopup().querySelector('#krg').value;

                                if (!selectedCluster) {
                                    Swal.showValidationMessage(`⚠ Silahkan pilih cluster`);
                                } else if (!inputKgs || inputKgs <= 0 || inputKgs > kgs ||
                                    !inputCones || inputCones <= 0 || inputCones > cones ||
                                    !inputKrg || inputKrg <= 0 || inputKrg > krg) {
                                    Swal.showValidationMessage(`⚠ Masukkan jumlah yang valid sesuai stok`);
                                }

                                return {
                                    selectedCluster,
                                    inputKgs,
                                    inputCones,
                                    inputKrg
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // AJAX untuk update cluster di stock
                                $.ajax({
                                    url: '<?= base_url(session()->get('role') . '/warehouse/updateCluster') ?>',
                                    method: 'POST',
                                    data: {
                                        id_stock: idStock,
                                        cluster_old: clusterOld,
                                        nama_cluster: result.value.selectedCluster,
                                        kgs: result.value.inputKgs,
                                        cones: result.value.inputCones,
                                        krg: result.value.inputKrg,
                                        lot: lot
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
            // Ambil data dari tombol
            let idStock = $(this).data('id'); // Ambil id_stock dari tombol yang diklik
            // let noModelOld = $(this).data('nomodel');
            let namaCluster = $(this).data('cluster');
            let lot = $(this).data('lot');
            let kgs = $(this).data('kgs');
            let cones = $(this).data('cones');
            let krg = $(this).data('krg');

            const info = $(this).data();
            console.log(info);
            const noModelOld = info.nomodel;
            const kodeWarna = info.kodewarna;
            // console.log(kodeWarna);
            // Base URL CI4
            const getNoModelUrl = '<?= base_url(session()->get("role") . "/warehouse/getNoModel") ?>';
            const updateNoModelUrl = '<?= base_url(session()->get("role") . "/warehouse/updateNoModel") ?>';

            // Ambil daftar no_model berdasarkan kode_warna
            $.ajax({
                url: getNoModelUrl,
                method: 'GET',
                data: {
                    noModelOld,
                    kodeWarna
                }, // otomatis jadi ?kodeWarna=xxx
                dataType: 'json',
                success: function(res) {
                    if (!res.success || !Array.isArray(res.data) || res.data.length === 0) {
                        return Swal.fire('Error', 'Tidak ada data No Model', 'error');
                    }

                    // Bangun opsi dropdown
                    let noModelOptions = `<option value="">Pilih No Model</option>`;
                    res.data.forEach(item => {
                        noModelOptions += `
                            <option value="${item.no_model}">
                                ${item.no_model} | ${item.item_type} | ${item.kode_warna}
                            </option>`;
                    });

                    Swal.fire({
                        title: 'Pindah Order',
                        html: `
                            <label class="font-weight-bold">No Model Lama</label>
                            <input type="text" class="form-control mb-3" value="${noModelOld}" disabled>

                            <label class="font-weight-bold">Pilih No Model Baru</label>
                            <select id="noModelSelect" class="form-control mb-3">${noModelOptions}</select>

                            <label class="font-weight-bold">Kgs Pindah</label>
                            <input type="number" id="kgs" min="1" max="${kgs}" value="${kgs.toFixed(2)}"
                                class="form-control mb-2" placeholder="Jumlah (KG)">

                            <label class="font-weight-bold">Cones Pindah</label>
                            <input type="number" id="cones" min="1" max="${cones}" value="${cones}"
                                class="form-control mb-2" placeholder="Jumlah (Cones)">

                            <label class="font-weight-bold">Krg Pindah</label>
                            <input type="number" id="krg" min="1" max="${krg}" value="${krg}"
                                class="form-control mb-3" placeholder="Jumlah (KRG)">
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Pindah',
                        confirmButtonColor: '#2e7d32',
                        cancelButtonColor: '#778899',
                        customClass: {
                            popup: 'swal-wide'
                        },
                        preConfirm: () => {
                            const selectedNoModel = $('#noModelSelect').val();
                            const inputKgs = parseFloat($('#kgs').val()) || 0;
                            const inputCones = parseInt($('#cones').val()) || 0;
                            const inputKrg = parseInt($('#krg').val()) || 0;

                            if (!selectedNoModel) {
                                Swal.showValidationMessage('⚠ Silahkan pilih No Model');
                            } else if (
                                inputKgs <= 0 || inputKgs > kgs ||
                                inputCones <= 0 || inputCones > cones ||
                                inputKrg <= 0 || inputKrg > krg
                            ) {
                                Swal.showValidationMessage('⚠ Masukkan jumlah yang valid sesuai stok');
                            }

                            return {
                                selectedNoModel,
                                inputKgs,
                                inputCones,
                                inputKrg
                            };
                        }
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        // Kirim update ke server
                        $.ajax({
                            url: updateNoModelUrl,
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                id_stock: idStock,
                                namaCluster: namaCluster,
                                no_model: result.value.selectedNoModel,
                                kgs: result.value.inputKgs,
                                cones: result.value.inputCones,
                                krg: result.value.inputKrg,
                                lot: lot
                            },
                            success: function(resp) {
                                if (resp.success) {
                                    Swal.fire('Sukses', 'Order berhasil dipindah', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', resp.message || 'Gagal memperbarui No Model', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Terjadi kesalahan saat mengupdate', 'error');
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengambil data', 'error');
                }
            });
        });

    });
</script>

<?php $this->endSection(); ?>