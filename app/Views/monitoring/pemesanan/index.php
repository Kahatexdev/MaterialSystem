<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<div class="container-fluid py-4">
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

    <style>
        /* Overlay transparan */
        #loadingOverlay {
            display: none;
            position: fixed;
            z-index: 99999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            background: rgba(0, 0, 0, 0.75);
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            width: 260px;
            /* kecilkan modal */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .loader-text {
            margin-top: 8px;
            color: #fff;
            font-weight: 500;
            font-size: 12px;
        }


        #loadingOverlay.active {
            display: block;
            opacity: 1;
        }

        .loader {
            width: 50px;
            height: 50px;
            margin: 0 auto 10px;
            position: relative;
        }

        .loader:after {
            content: "";
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: loader-dual-ring 1.2s linear infinite;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
        }

        @keyframes loader-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>
    <!-- overlay -->
    <div id="loadingOverlay">
        <div class="loader-wrap">
            <div class="loading-card">
                <div class="loader" role="status" aria-hidden="true"></div>
                <div class="loader-text">Memuat data...</div>

                <!-- Progress bar -->
                <div class="progress mt-3" style="height: 6px; border-radius: 6px;">
                    <div id="progressBar"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                        role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>

    <!-- Modal Detail Stok -->
    <div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStockLabel">Detail Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stockForm">
                        <div id="stockData" class="row g-3"></div>
                        <button type="submit" class="btn bg-gradient-info mt-3 text-end">Pilih Stok</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Import -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Judul di kiri -->
                <h5 class="mb-0 font-weight-bolder">Data Pemesanan Area</h5>

                <!-- Wrapper untuk input+button di kanan -->
                <div class="d-flex">
                    <input type="text" class="form-control form-control-sm me-2" id="no_model" placeholder="No Model">
                    <input type="date" class="form-control form-control-sm me-2" id="tgl_pakai">
                    <button class="btn btn-info btn-sm" id="btnFilter">
                        <i class="fa fa-search"></i> Search
                    </button>
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
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Kebutuhan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Jalan Mc</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kgs Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cns Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Lot Pesan</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Keterangan Area</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">PO (+)</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Terima</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Total Retur</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Jatah</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status Jatah</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($dataList as $key => $id) {
                                $ttl_kg_pesan = number_format($id['qty_pesan'] - $id['qty_sisa'], 2);
                                $ttl_cns_pesan = $id['cns_pesan'] - $id['cns_sisa'];
                            ?>
                                <tr>
                                    <td class="text-xs text-start"><?= $no++; ?></td>
                                    <td class="text-xs text-start"><?= $id['tgl_pakai']; ?></td>
                                    <td class="text-xs text-start"><?= $id['admin']; ?></td>
                                    <td class="text-xs text-start"><?= $id['no_model']; ?></td>
                                    <td class="text-xs text-start"><?= $id['item_type']; ?></td>
                                    <td class="text-xs text-start"><?= $id['kode_warna']; ?></td>
                                    <td class="text-xs text-start"><?= $id['color']; ?></td>
                                    <td class="text-xs text-start"><?= number_format($id['ttl_kebutuhan_bb'], 2); ?></td>
                                    <td class="text-xs text-start"><?= $id['jl_mc']; ?></td>
                                    <td class="text-xs text-start"><?= $ttl_kg_pesan; ?></td>
                                    <td class="text-xs text-start"><?= $ttl_cns_pesan; ?></td>
                                    <td class="text-xs text-start"><?= $id['lot']; ?></td>
                                    <td class="text-xs text-start"><?= $id['keterangan']; ?></td>
                                    <td class="text-xs text-start">
                                        <?php if ($id['po_tambahan'] == 1): ?>
                                            <span class="text-success fw-bold">✅</span>
                                        <?php else: ?>
                                            <!-- Biarkan kosong -->
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-xs text-start"><?= number_format($id['ttl_pengiriman'], 2); ?></td>
                                    <td class="text-xs text-start"></td>
                                    <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>"><?= number_format($id['sisa_jatah'], 2); ?></td>
                                    <td class="text-xs text-start" style="<?= $id['sisa_jatah'] < 0 ? 'color: red;' : ''; ?>">
                                        <?php if ($id['sisa_jatah'] > 0) {
                                            if ($ttl_kg_pesan >= $id['sisa_jatah']) { ?>
                                                <span style="color: red;">Pemesanan Melebihi Jatah</span>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <span style="color: red;">Habis Jatah</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-xs text-start">
                                        <button type="button" class="btn btn-warning update-btn" data-bs-toggle="modal" data-bs-target="#updateListModal" data-area="<?= $id['admin']; ?>" data-tgl="<?= $id['tgl_pakai']; ?>" data-model="<?= $id['no_model']; ?>" data-item="<?= $id['item_type']; ?>" data-kode="<?= $id['kode_warna']; ?>" data-color="<?= $id['color']; ?>" data-po-tambahan="<?= $id['po_tambahan']; ?>">
                                            <i class="fa fa-edit fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php if (empty($dataPemesanan)) : ?>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <p>No data available in the table.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- modal update list pemesanan -->
<div class="modal fade" id="updateListModal" tabindex="-1" role="dialog" aria-labelledby="importModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="exampleModalLabel">Update List Pemesanan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="updatePemesanan">
                <div class="modal-body align-items-center">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Area</label>
                                <input type="text" class="form-control" name="area" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Tgl Pakai</label>
                                <input type="text" class="form-control" name="tgl_pakai" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">No Model</label>
                                <input type="text" class="form-control" name="no_model" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <label for="recipient-name" class="col-form-label text-center">Item Type</label>
                                <input type="text" class="form-control" name="item_type" readonly>
                            </div>
                            <div class="col-lg-6">
                                <label for="recipient-name" class="col-form-label text-center">Kode Warna</label>
                                <input type="text" class="form-control" name="kode_warna" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Color</label>
                                <input type="text" class="form-control" name="color" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Lot</label>
                                <input type="text" class="form-control" name="lot">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Keterangan</label>
                                <textarea class="form-control" name="keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer align-items-center">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Style Size</label>
                            </div>
                            <div class="col-lg-1">
                                <label for="recipient-name" class="col-form-label text-center">Jl Mc</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Qty Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Ttl Qty Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center">Berat Cones</label>
                            </div>
                            <div class="col-lg-2">
                                <label for="recipient-name" class="col-form-label text-center"> Ttl Berat Cones</label>
                            </div>
                        </div>
                        <div id="dataPerstyle">
                            <!-- data perstyle muncul di sini -->
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-info remove-row w-100">UPDATE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal update list pemesanan end -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $('#dataTable').DataTable({
        "pageLength": 35,
        "order": []
    });

    function showLoading() {
        $('#loadingOverlay').addClass('active');
        $('#btnSearch').prop('disabled', true);
        // show DataTables processing indicator if available
        try {
            dataTable.processing(true);
        } catch (e) {}
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('active');
        $('#btnSearch').prop('disabled', false);
        try {
            dataTable.processing(false);
        } catch (e) {}
    }

    function updateProgress(percent) {
        $('#progressBar')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent);
        $('#progressText').text(percent + '%');
    }
</script>
<script type="text/javascript">
    //Filter data pemesanan
    document.getElementById('btnFilter').addEventListener('click', function() {
        const noModel = document.getElementById('no_model').value.trim();
        const tglPakai = document.getElementById('tgl_pakai').value.trim();

        if (noModel === '' && tglPakai === '') {
            alert('Isi minimal No Model atau Tanggal Pakai untuk memfilter!');
            return;
        }

        showLoading();
        updateProgress(30);

        // Redirect ke controller dengan parameter
        let url = '<?= base_url($role . '/filter_pemesananarea') ?>?';
        if (noModel) url += 'model=' + encodeURIComponent(noModel) + '&';
        if (tglPakai) url += 'tgl_pakai=' + encodeURIComponent(tglPakai);

        window.location.href = url;

        // animasi progress naik pelan → lalu redirect
        let percent = 80;
        let interval = setInterval(() => {
            percent += 9;
            if (percent >= 99) {
                clearInterval(interval);
                window.location.href = url; // redirect ketika progress sudah 90%
            } else {
                updateProgress(percent);
            }
        }, 100);
    });

    // VIEW MODAL UPDATE PEMESANAN
    // Trigger import modal when import button is clicked
    $(document).on('click', '.update-btn', function() {
        var area = $(this).data('area');
        var tglPakai = $(this).data('tgl');
        var noModel = $(this).data('model');
        var itemType = $(this).data('item');
        var kode_warna = $(this).data('kode');
        var color = $(this).data('color');
        var po_tambahan = $(this).data('po-tambahan');
        const updateListUrl = "<?= base_url("$role/getUpdateListPemesanan") ?>";

        // Kirim data ke server untuk pencarian
        $.ajax({
            url: updateListUrl, // Ganti dengan URL endpoint
            method: 'POST',
            data: {
                area: area,
                tgl_pakai: tglPakai,
                no_model: noModel,
                item_type: itemType,
                kode_warna: kode_warna,
                color: color,
                po_tambahan: po_tambahan
            },
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response); // Debug response dari server
                if (response.status === 'success') {
                    // Ambil semua nilai lot dari response.data
                    let lotValues = response.data.map(item => item.lot);
                    let uniqueLotValues = [...new Set(lotValues)]; // Ambil nilai yang unik menggunakan Set
                    let lotDisplay = uniqueLotValues[0]; // ambil data lot 
                    let keteranganValues = response.data.map(item => item.keterangan);
                    let uniqueKeteranganValues = [...new Set(keteranganValues)]; // Ambil nilai yang unik menggunakan Set
                    let keteranganDisplay = uniqueKeteranganValues[0]; // ambil data lot 

                    // Isi data ke dalam modal
                    $('#updateListModal').find('input[name="area"]').val(area);
                    $('#updateListModal').find('input[name="tgl_pakai"]').val(tglPakai);
                    $('#updateListModal').find('input[name="no_model"]').val(noModel);
                    $('#updateListModal').find('input[name="item_type"]').val(itemType);
                    $('#updateListModal').find('input[name="kode_warna"]').val(kode_warna);
                    $('#updateListModal').find('input[name="color"]').val(color);
                    $('#updateListModal').find('input[name="lot"]').val(lotDisplay);
                    $('#updateListModal').find('textarea[name="keterangan"]').val(keteranganDisplay);

                    // data perstyle
                    var dataPerstyle = '';
                    let ttl_jl_mc = 0;
                    let cns_pesan = 0;
                    let kg_pesan = 0;
                    let totalRows = 0; // Menyimpan jumlah data
                    let sisaKg = 0; // Total jalan_mc
                    let sisaCns = 0; // Total jalan_mc

                    response.data.forEach(function(item, index) {
                        const jalanMc = parseFloat(item.jl_mc) || 0;
                        const totalCones = jalanMc * item.qty_cns;
                        const totalBeratCones = totalCones * item.qty_berat_cns;

                        // Tambahkan nilai ke variabel akumulasi
                        ttl_jl_mc += parseFloat(jalanMc);
                        cns_pesan += parseFloat(totalCones);
                        kg_pesan += parseFloat(totalBeratCones);
                        sisaCns += parseFloat(item.sisa_cones_mc);
                        sisaKg += parseFloat(item.sisa_kgs_mc);

                        dataPerstyle += `
                            <div class="col-lg-12">
                                <div class="row mb-1">
                                <input type="hidden" class="form-control id_material" name="items[${index}][id_total_pemesanan]" value="${item.id_total_pemesanan}" readonly>
                                <input type="hidden" class="form-control id_material" name="items[${index}][id_material]" value="${item.id_material}" readonly>
                                <input type="hidden" class="form-control id_pemesanan" name="items[${index}][id_pemesanan]" value="${item.id_pemesanan}" readonly>
                                <div class="col-lg-3">
                                    <input type="text" class="form-control style" name="items[${index}][style]" value="${item.style_size}" readonly>
                                </div>
                                <div class="col-lg-1">
                                    <input type="number" class="form-control jalan_mc" name="items[${index}][jalan_mc]" value="${item.jl_mc}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" class="form-control qty_cns" name="items[${index}][qty_cns]" value="${item.qty_cns}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_qty_cns" name="items[${index}][ttl_qty_cns]" value="${item.ttl_qty_cones}" readonly>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" step="0.01" class="form-control qty_berat_cns" name="items[${index}][qty_berat_cns]" value="${parseFloat(item.qty_berat_cns).toFixed(2)}">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_berat_cns" name="items[${index}][ttl_berat_cns]" value="${parseFloat(item.ttl_berat_cones).toFixed(2)}" readonly>
                                </div>
                            </div>
                            `;
                        totalRows++;
                    });

                    // Hitung rata-rata jalan_mc
                    // const sisaCnsMc = totalRows > 0 ? (sisaCns / totalRows) : 0;
                    // const sisaKgMc = totalRows > 0 ? (sisaKg / totalRows) : 0;
                    const ttl_cns_pesan = sisaCns > 0 ? cns_pesan - sisaCns : cns_pesan;
                    const ttl_kg_pesan = sisaKg > 0 ? kg_pesan - sisaKg : kg_pesan;
                    dataPerstyle += `
                            <div class="row mt-1">
                                <div class="col-lg-6">
                                    <label for="recipient-name" class="col-form-label text-center">Stock Area</label>
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" class="form-control sisa_cns" name="sisa_cns" value="${sisaCns}">
                                </div>
                                <div class="col-lg-2">
                                    
                                </div>
                                <div class="col-lg-2">
                                    <input type="number" step="0.01" class="form-control sisa_kg" name="sisa_kg" value="${parseFloat(sisaKg).toFixed(2)}">
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-lg-3">
                                    <label for="recipient-name" class="col-form-label text-center">Total</label>
                                </div>
                                <div class="col-lg-1">
                                    <input type="text" class="form-control ttl_jl_mc" name="ttl_jl_mc" value="${ttl_jl_mc}" readonly>
                                </div>
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-2">
                                <input type="text" class="form-control ttl_cns_pesan" name="ttl_cns_pesan" value="${ttl_cns_pesan}" readonly>
                                </div>
                                <div class="col-lg-2">
                                </div>
                                <div class="col-lg-2">
                                    <input type="text" class="form-control ttl_kg_pesan" name="ttl_kg_pesan" value="${parseFloat(ttl_kg_pesan).toFixed(2)}" readonly>
                                </div>
                            </div>
                        `;
                    $('#dataPerstyle').html(dataPerstyle); // Ganti isi elemen di modal

                    // Kalkulasi otomatis
                    function recalculateTotals() {
                        let totalJalanMc = 0;
                        let totalQtyCns = 0;
                        let totalBeratCns = 0;
                        let ttl_jl_mc = 0;
                        let ttl_cns_pesan = 0;
                        let ttl_kg_pesan = 0;

                        $('#dataPerstyle .row.mb-1').each(function() {
                            const jalan_mc = parseFloat($(this).find('.jalan_mc').val()) || 0;
                            const qty_cns = parseFloat($(this).find('.qty_cns').val()) || 0;
                            const qty_berat_cns = parseFloat($(this).find('.qty_berat_cns').val()) || 0;

                            const ttl_qty_cns = jalan_mc * qty_cns;
                            const ttl_berat_cns = ttl_qty_cns * qty_berat_cns;

                            totalJalanMc += jalan_mc;
                            totalQtyCns += ttl_qty_cns;
                            totalBeratCns += ttl_berat_cns;

                            $(this).find('.ttl_qty_cns').val(ttl_qty_cns);
                            $(this).find('.ttl_berat_cns').val(ttl_berat_cns.toFixed(2));
                        });

                        // Hitung nilai akhir dengan mengurangi sisa
                        const sisaCns = parseFloat($('.sisa_cns').val()) || 0;
                        const sisaKg = parseFloat($('.sisa_kg').val()) || 0;
                        if (sisaCns < 0 || sisaKg < 0) {
                            alert('Nilai tidak boleh negatif!');
                            $(this).val(0); // Reset nilai menjadi 0
                        }

                        // Update total dengan pengurangan sisa
                        ttl_jl_mc = totalJalanMc;
                        ttl_cns_pesan = totalQtyCns - sisaCns;
                        ttl_kg_pesan = totalBeratCns - sisaKg;

                        // Perbarui tampilan total
                        $('.ttl_jl_mc').val(ttl_jl_mc);
                        $('.ttl_cns_pesan').val(ttl_cns_pesan);
                        $('.ttl_kg_pesan').val(ttl_kg_pesan.toFixed(2));
                    }

                    $('#dataPerstyle').on('input', '.jalan_mc, .qty_cns, .qty_berat_cns, .sisa_cns, .sisa_kg', recalculateTotals); // Trigger recalculation on input change

                    $('#updateListModal').modal('show'); // Tampilkan modal
                } else {
                    alert('Data tidak ditemukan');
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Terjadi kesalahan saat mengambil data.');
            }
        });
    });
    // END VIEW MODAL UPDATE PEMESANAN

    // PROSES UPDATE PEMESANAN
    document.getElementById('updatePemesanan').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const BASE_URL = "<?= base_url(); ?>";
        const UPDATE_URL = "<?= base_url("$role/updateListPemesanan") ?>";

        // Konversi FormData ke JSON tanpa "[]"
        const payload = {};
        formData.forEach((value, key) => {
            const cleanKey = key.replace(/\[\]$/, ""); // Hapus "[]"
            if (!payload[cleanKey]) payload[cleanKey] = [];
            payload[cleanKey].push(value);
        });
        console.log(payload);

        fetch(UPDATE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
                // credentials: 'include', // Menyertakan cookie/session ID
            })
            .then(async (response) => {
                const resData = await response.json();
                // Ambil area dari payload untuk menentukan URL redirect
                const area = payload.area?.[0] || ''; // Pastikan 'area' ada atau gunakan default
                if (resData.status == "success") {
                    // Tampilkan SweetAlert setelah session berhasil dihapus
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: resData.message,
                        showConfirmButton: false,
                        timer: 1500, // 2 detik
                        timerProgressBar: true
                    }).then(() => {
                        // Redirect ke halaman yang diinginkan
                        window.location.href = `${BASE_URL}monitoring/pemesanan`; // Halaman tujuan setelah sukses
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: resData.message || 'Gagal menyimpan data',
                    }).then(() => {
                        // Redirect ke halaman yang diinginkan
                        window.location.href = `${BASE_URL}monitoring/pemesanan`; // Halaman tujuan setelah sukses
                    });
                    console.error('Response Data:', resData);
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim data',
                });
                console.error('Fetch Error:', error);
            });

    });
    // END PROSES UPDATE PEMESANAN
</script>
<?php $this->endSection(); ?>