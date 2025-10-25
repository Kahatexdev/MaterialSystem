<?php $this->extend($role . '/poplus/header'); ?>
<?php $this->section('content'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    /* Style umum untuk semua DataTables Buttons */
    .dt-button {
        background-color: #28a745 !important;
        /* hijau */
        color: #ffffff !important;
        /* putih */
        border: 1px solid #1e7e34 !important;
        /* border gelap */
        border-radius: 4px !important;
        padding: 6px 12px !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        text-transform: none !important;
        box-shadow: none !important;
        cursor: pointer !important;
        margin-bottom: 1rem !important;
    }

    /* Hover/focus state */
    .dt-button:hover,
    .dt-button:focus {
        background-color: #218838 !important;
        border-color: #1c7430 !important;
        color: #ffffff !important;
        outline: none !important;
    }

    /* Jika kamu ingin khusus style Excel-button saja */
    .dt-button.buttons-excel {
        background-color: #218838 !important;
        /* biru */
        border-color: #1c7430 !important;
    }
</style>
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

    <!-- Header Card -->
    <div class="card card-frame">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h5 class="mb-0 font-weight-bolder">List PO Tambahan Area</h5>
                </div>
                <div class="col-6 text-end">
                    <button class="btn btn-info ms-2">
                        <a href="<?= base_url($role . '/poplus/form_potambahan') ?>" class="fa fa-list text-white" style="text-decoration: none;"> Open PO(+)</a>
                    </button>
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
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Tanggal PO(+)</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Area</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No Model</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Item Type</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kode Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Warna</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Jatah</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Kg Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cns Po Tambahan</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Sisa Bahan Baku di Mesin</th>
                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($poTambahan as $data):
                            // Cek apakah id_order ini ada di materialOrderIds
                            $isNotApproved = $data['status'] == '';
                        ?>
                            <tr>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['tgl_poplus'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['admin'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['no_model'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['item_type'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['kode_warna'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['color'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= number_format($data['sisa_jatah'], 2) ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= number_format($data['kg_poplus'], 2) ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['cns_poplus'] ?></td>
                                <td style="<?= $isNotApproved ? 'color:red;' : '' ?>"><?= $data['sisa_bb_mc'] ?></td>

                                <!-- <td style="<?= $isNotApproved ? 'color:red;' : '' ?>">
                                    <a href="<?= base_url($role . '/poplus/detail?area=' . $data['admin'] . '&tgl_poplus=' . $data['tgl_poplus'] . '&no_model=' . $data['no_model'] . '&item_type=' . $data['item_type'] . '&kode_warna=' . $data['kode_warna'] . '&warna=' . $data['color'] . '&status=' . $data['status']) ?>" class="btn btn-info btn-sm">
                                        Detail
                                    </a>
                                </td> -->
                                <td>
                                    <button type="button" class="btn btn-warning update-btn" data-bs-toggle="modal" data-bs-target="#updateListModal"
                                        data-area="<?= $data['admin'] ?>" data-tgl="<?= $data['tgl_poplus'] ?>" data-model="<?= $data['no_model'] ?>" data-item="<?= $data['item_type'] ?>" data-kode="<?= $data['kode_warna'] ?>" data-color="<?= $data['color'] ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($poTambahan)) : ?>
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
<!-- modal update list po tambahan -->
<div class="modal fade" id="updateListModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="exampleModalLabel">Update List PO Tambahan</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="updatePoTambahan">
                <div class="modal-body align-items-center">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Area</label>
                                <input type="text" class="form-control" name="area" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Tgl PO(+)</label>
                                <input type="date" class="form-control" name="tgl_pakai">
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">No Model</label>
                                <input type="text" class="form-control" name="no_model" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Item Type</label>
                                <input type="text" class="form-control" name="item_type" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Kode Warna</label>
                                <input type="text" class="form-control" name="kode_warna" readonly>
                            </div>
                            <div class="col-lg-4">
                                <label for="recipient-name" class="col-form-label text-center">Color</label>
                                <input type="text" class="form-control" name="color" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">PO (Kg)</label>
                                <input type="text" class="form-control" name="po_kg">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Terima (Kg)</label>
                                <input type="text" class="form-control" name="terima_kg">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Sisa Jatah (Kg)</label>
                                <input type="text" class="form-control" name="sisa_jatah">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Sisa BB di Mesin (Kg)</label>
                                <input type="text" class="form-control" name="sisa_bb_mc">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">(+) Mesin Cns</label>
                                <input type="text" class="form-control" name="tambahan_mesin">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">(+) Packing Cns</label>
                                <input type="text" class="form-control" name="tambahan_pck">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Loss Aktual</label>
                                <input type="text" class="form-control" name="loss_aktual">
                            </div>
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Loss Tambahan</label>
                                <input type="text" class="form-control" name="loss_tambahan">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <label for="recipient-name" class="col-form-label text-center">Delivery PO(+)</label>
                                <input type="date" class="form-control" name="delivery_po_tambahan">
                            </div>
                            <div class="col-lg-9">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            pageLength: 35,
            order: [],
            dom: 'Bfrtip', // B = Buttons
            buttons: [{
                extend: 'excelHtml5',
                text: 'Download Excel',
                titleAttr: 'Export ke Excel',
                exportOptions: {
                    columns: ':not(:last-child)',
                    modifier: {
                        search: 'applied'
                    }
                }
            }]
        });

    });
</script>
<script>
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
                                <div class="row">
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">Composition(%)</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">GW Aktual</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">Po(Kg)</label>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="recipient-name" class="col-form-label text-center">Sisa Order(Pcs)</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">Bs Mesin(Kg)</label>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="recipient-name" class="col-form-label text-center">Bs Setting(Pcs)</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">PO(+) Mesin (Kg)</label>
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="recipient-name" class="col-form-label text-center">(+)Packing (Pcs)</label>
                                    </div>
                                    <div class="col-lg-1">
                                        <label for="recipient-name" class="col-form-label text-center">(+)Packing (Kg)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="recipient-name" class="col-form-label text-center">Total PO Tambahan (Kg)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="recipient-name" class="col-form-label text-center">Total PO Tambahan (Cns)</label>
                                    </div>
                                </div>
                            </div>
                            `;
                        totalRows++;
                    });
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
</script>
<?php $this->endSection(); ?>