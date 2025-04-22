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
    <!-- Modal -->
    <div class="modal fade" id="modalPindahOrder" tabindex="-1" role="dialog" aria-labelledby="modalPindahOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formPindahOrder">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPindahOrderLabel">Pindah Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Cards akan diisi via JS -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="savePindahOrder">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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

    });

    // modal pindah order
    $(document).on('click', '.pindahOrder', function() {
        const idStock = $(this).data('id');
        const $modal = $('#modalPindahOrder');
        const $body = $modal.find('.modal-body');
        $body.html('<p>Loading...</p>');
        $modal.modal('show');

        $.ajax({
                url: `<?= base_url(session()->get('role') . '/warehouse/getPindahOrder') ?>`,
                method: 'POST',
                data: {
                    id_stock: idStock
                },
                dataType: 'json'
            })
            .done(function(res) {
                if (!res.success || !res.data.length) {
                    return $body.html(`<div class="alert alert-warning">${res.message || 'Data tidak ditemukan'}</div>`);
                }

                let html = '<div class="row g-3">';
                res.data.forEach(d => {
                    html += `
              <div class="col-sm-6">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center">
                    <input type="checkbox" class="form-check-input me-2 row-check" 
                           name="pindah[]" value="${d.id_pemasukan}">
                    <strong><h5 class="card-title">${d.nama_cluster} | ${d.no_model}</h5></strong>
                  </div>
                  <div class="card-body">
                    <p class="card-text"><strong>Lot Jalur:</strong> ${d.lot_stock || d.lot_awal}</p>
                    <p class="card-text"><strong>Kode Warna:</strong> ${d.kode_warna}</p>
                    <p class="card-text"><strong>Warna:</strong> ${d.warna}</p>
                    <p class="card-text"><strong>Total Kgs:</strong> ${(parseFloat(d.kgs_kirim) || 0).toFixed(2)} KG | ${d.cones_kirim} Cones | ${d.no_karung} KRG</p>
                  </div>
                </div>
              </div>
            `;
                });
                html += '</div>';
                $body.html(html);
            })
            .fail(function(xhr, status, err) {
                $body.html(`<div class="alert alert-danger">Error: ${err}</div>`);
            });
    });

    // Save handler tetap sama
    $(document).on('click', '#savePindahOrder', function() {
        const selected = $("#formPindahOrder")
            .find("input[name='pindah[]']:checked")
            .map((_, c) => c.value).get();
        if (!selected.length) {
            return alert('Pilih minimal satu kartu!');
        }
        $.post('/stock/save-pindah-order', {
                id_pemasukan: selected
            }, function(res) {
                if (res.success) {
                    alert('Berhasil memindahkan ' + selected.length + ' order.');
                    $('#modalPindahOrder').modal('hide');
                    // reload data utama...
                } else {
                    alert('Gagal: ' + res.message);
                }
            }, 'json')
            .fail((_, __, err) => alert('Error: ' + err));
    });
</script>

<?php $this->endSection(); ?>