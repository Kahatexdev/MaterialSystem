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
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small id="progressText" class="text-white mt-1 d-block">0%</small>
            </div>
        </div>
    </div>

    <!-- Button Import -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="form-group me-2">
                    <label>No Model</label>
                    <input type="text" class="form-control form-control-sm" id="no_model" placeholder="No Model">
                </div>
                <div class="form-group me-2">
                    <label>Area</label>
                    <input type="text" class="form-control form-control-sm" id="area" placeholder="Area">
                    <!-- kalau mau Select2, tinggal ganti input ini -->
                </div>
                <div class="form-group me-2">
                    <label>Tanggal Pakai</label>
                    <input type="date" class="form-control form-control-sm" id="tgl_pakai">
                </div>
                <div class="flex-grow-1"></div> <!-- spacer -->
                <button class="btn btn-secondary btn-sm align-self-end me-2" id="btnReset">
                    <i class="fa fa-undo"></i> Reset
                </button>

                <button class="btn btn-info btn-sm align-self-end" id="btnFilter">
                    <i class="fa fa-search"></i> Search
                </button>

            </div>
        </div>

        <!-- Tabel Data -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="display non-wrap text-center text-uppercase text-xs font-bolder" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pakai</th>
                                <th>Area</th>
                                <th>No Model</th>
                                <th>Item Type</th>
                                <th>Kode Warna</th>
                                <th>Warna</th>
                                <th>Kg Kebutuhan</th>
                                <th>Jalan Mc</th>
                                <th>Kgs Pesan</th>
                                <th>Cns Pesan</th>
                                <th>Lot Pesan</th>
                                <th>Keterangan Area</th>
                                <th>PO (+)</th>
                                <th>Total Terima</th>
                                <th>Total Retur</th>
                                <th>Sisa Jatah</th>
                                <th>Status Jatah</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                    </table>
                </div>
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
<script src="https://cdn.datatables.net/v/bs5/dt-2.1.3/r-3.0.2/datatables.min.js"></script>

<script>
    let dt;

    function showLoading() {
        $('#loadingOverlay').addClass('active');
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('active');
    }

    function updateProgress(p) {
        $('#progressBar').css('width', p + '%').attr('aria-valuenow', p);
        $('#progressText').text(p + '%');
    }

    function reloadTable() {
        if (!dt) return;
        showLoading();
        updateProgress(30);
        dt.ajax.reload(() => {
            updateProgress(100);
            setTimeout(hideLoading, 300);
        }, false);
    }

    $(function() {
        dt = $('#dataTable').DataTable({
            processing: true,
            serverSide: true, // tetap server-side, tapi tanpa paging
            responsive: false,
            deferRender: true,
            paging: false, // <--- NO PAGING
            info: false, // <--- sembunyikan info "Showing X of Y"
            lengthChange: false, // <--- hilangkan dropdown page length
            order: [],
            ajax: {
                url: "<?= base_url('monitoring/pemesanan/data'); ?>",
                type: "GET",
                data: function(d) {
                    d.tgl_pakai = $('#tgl_pakai').val().trim();
                    d.model = $('#no_model').val().trim();
                    d.area = $('#area').val().trim();
                    // paksa length besar agar server (kalau masih baca length) tetap kirim full
                    d.start = 0;
                    d.length = 1000000;
                },
                dataSrc: function(json) {
                    // nomor urut manual
                    let running = 1;
                    return (json.data || []).map((row) => {
                        row._rownum = running++;
                        return row;
                    });
                },
                beforeSend() {
                    showLoading();
                    updateProgress(50);
                },
                complete() {
                    updateProgress(100);
                    setTimeout(hideLoading, 300);
                }
            },
            columns: [{
                    data: '_rownum'
                },
                {
                    data: 'tgl_pakai'
                },
                {
                    data: 'admin'
                },
                {
                    data: 'no_model'
                },
                {
                    data: 'item_type'
                },
                {
                    data: 'kode_warna'
                },
                {
                    data: 'color'
                },
                {
                    data: 'ttl_kebutuhan_bb',
                    render: d => (parseFloat(d || 0)).toFixed(2)
                },
                {
                    data: 'jl_mc'
                },
                {
                    data: '_ttl_kg_pesan'
                },
                {
                    data: '_ttl_cns_pesan'
                },
                {
                    data: 'lot'
                },
                {
                    data: 'keterangan'
                },
                {
                    data: 'po_tambahan',
                    render: d => (String(d) == '1' ? '✅' : '')
                },
                {
                    data: '_ttl_pengiriman'
                },
                {
                    data: null,
                    defaultContent: ''
                },
                {
                    data: '_sisa_jatah',
                    render: (d, t, row) => {
                        const neg = (parseFloat(row['sisa_jatah'] || 0) < 0);
                        return `<span style="${neg?'color:red;':''}">${d}</span>`;
                    }
                },
                {
                    data: '_status_jatah',
                    render: (d, t, row) => {
                        const neg = (parseFloat(row['sisa_jatah'] || 0) <= 0) || (d === 'Pemesanan Melebihi Jatah');
                        return `<span style="${neg?'color:red;':''}">${d||''}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (d, t, row) => {
                        return `
        <button type="button" class="btn btn-warning update-btn" data-bs-toggle="modal" data-bs-target="#updateListModal"
          data-area="${row.admin}" data-tgl="${row.tgl_pakai}" data-model="${row.no_model}"
          data-item="${row.item_type}" data-kode="${row.kode_warna}" data-color="${row.color}"
          data-po-tambahan="${row.po_tambahan}">
          <i class="fa fa-edit fa-lg"></i>
        </button>
        ${ row.status_kirim !== 'YA' ? `
          <button type="button" class="btn btn-info text-xs send-btn"
            data-area="${row.admin}" data-tgl="${row.tgl_pakai}" data-model="${row.no_model}"
            data-item="${row.item_type}" data-kode="${row.kode_warna}" data-color="${row.color}"
            data-po-tambahan="${row.po_tambahan}">
            <i class="fa fa-paper-plane fa-lg"></i>
          </button>` : `<span style="color:red;"></span>` }
        `;
                    }
                },
            ]
        });


        // Search
        $('#btnFilter').on('click', function() {
            if (!$('#tgl_pakai').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tanggal Pakai harus diisi!'
                });
                return;
            }
            reloadTable();
        });

        // Reset
        $('#btnReset').on('click', function() {
            $('#no_model').val('');
            $('#area').val('');
            $('#tgl_pakai').val('');
            reloadTable(); // akan kembali kosong karena tgl_pakai kosong
        });

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
    // PROSES KIRIM PEMESANAN
    document.addEventListener("click", function(e) {
        if (e.target.matches(".send-btn") || e.target.closest(".send-btn")) {
            const button = e.target.closest(".send-btn");
            if (!button) return; // Jika bukan tombol, keluar
            // Ambil waktu saat ini
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            // Ambil batas waktu dari data attribute tombol
            const batasWaktu = button.getAttribute("data-waktu"); // Contoh: "08:30:00"

            // Ubah batasWaktu yang didapat dari PHP menjadi jam dan menit
            let [batasHour, batasMinute, batasSecond] = batasWaktu.split(':').map(Number);

            if (currentHour > batasHour || (currentHour === batasHour && currentMinute > batasMinute)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Melebihi batas waktu sesuai bahan baku! ',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
                button
                return;
            }

            // Ambil data dari tombol
            const data = {
                area: button.getAttribute("data-area"),
                tgl_pakai: button.getAttribute("data-tgl"),
                no_model: button.getAttribute("data-model"),
                item_type: button.getAttribute("data-item"),
                kode_warna: button.getAttribute("data-kode"),
                color: button.getAttribute("data-color"),
                po_tambahan: button.getAttribute("data-po-tambahan"),
                waktu: batasWaktu,
            };

            // Kirim data ke server menggunakan AJAX
            fetch("http://172.23.44.14/MaterialSystem/public/api/kirimPemesanan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data),
                })
                .then((response) => response.json())
                .then((result) => {
                    if (result.status == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: result.message,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Refresh halaman setelah tombol OK ditekan dengan membawa parameter filter
                                const tglPakai = new URLSearchParams(window.location.search).get('tgl_pakai') || '';
                                const searchPdk = new URLSearchParams(window.location.search).get('searchPdk') || '';
                                const BASE_URL = "<?= base_url(); ?>";
                                const area = button.getAttribute("data-area");
                                window.location.href = `${BASE_URL}user/listPemesanan/${area}?tgl_pakai=${tglPakai}&searchPdk=${searchPdk}`;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'result.message',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Refresh halaman setelah tombol OK ditekan dengan membawa parameter filter
                                const tglPakai = new URLSearchParams(window.location.search).get('tgl_pakai') || '';
                                const searchPdk = new URLSearchParams(window.location.search).get('searchPdk') || '';
                                const BASE_URL = "<?= base_url(); ?>";
                                const area = button.getAttribute("data-area");
                                window.location.href = `${BASE_URL}user/listPemesanan/${area}?tgl_pakai=${tglPakai}&searchPdk=${searchPdk}`;
                            }
                        });
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat mengirim data.");
                });
        }
    });

    // PROSES KIRIM PEMESANAN DENGAN AJAX (id="sendBtn")
    $(document).on('click', '.send-btn', function(e) {
        e.preventDefault();

        // Ambil data dari tombol
        const button = $(this);
        const data = {
            area: button.data('area'),
            tgl_pakai: button.data('tgl'),
            no_model: button.data('model'),
            item_type: button.data('item'),
            kode_warna: button.data('kode'),
            color: button.data('color'),
            po_tambahan: String(button.data('po-tambahan'))
        };

        console.log(data); // Debug data yang akan dikirim
        const baseUrl = "<?= base_url('/api/kirimPemesanan'); ?>";
        Swal.fire({
            title: 'Kirim Pemesanan?',
            text: "Apakah Anda yakin ingin mengirim pemesanan ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                showLoading();

                $.ajax({
                    url: baseUrl,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        hideLoading();
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Pemesanan berhasil dikirim.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Pemesanan gagal dikirim.'
                            });
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengirim data.'
                        });
                    }
                });
            }
        });
    });
</script>

<?php $this->endSection(); ?>