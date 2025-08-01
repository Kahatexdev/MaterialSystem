<?php $this->extend($role . '/warehouse/header'); ?>
<?php $this->section('content'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<style>
    /* Auto Complete */
    .ui-state-active {
        background: rgb(230, 153, 233) !important;
        color: #fff !important;
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Form Input Bon</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url($role . '/otherIn/saveOtherIn') ?>" method="post">
                            <div id="kebutuhan-container">
                                <div class="row mb-4">
                                    <div class="col-md-4">

                                        <label>Detail Surat Jalan</label>
                                        <select class="form-control" name="detail_sj" id="detail_sj" required>
                                            <option value="">Pilih Detail Surat Jalan</option>
                                            <option value="COVER MAJALAYA">COVER MAJALAYA</option>
                                            <option value="IMPORT DARI KOREA">IMPORT DARI KOREA</option>
                                            <option value="JS MISTY">JS MISTY</option>
                                            <option value="JS SOLID">JS SOLID</option>
                                            <option value="KHTEX">KHTEX</option>
                                            <option value="PO(+)">PO(+)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>No Surat Jalan</label>
                                        <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" placeholder="No Surat Jalan" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tanggal Datang</label>
                                        <input type="date" class="form-control" id="tgl_datang" name="tgl_datang" required>
                                    </div>
                                </div>
                                <!--  -->
                                <!-- <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">1</button>
                                    </div>
                                </nav> -->
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <!-- Form Items -->
                                        <div class="kebutuhan-item">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label>No Model</label>
                                                    <select class="form-control" id="id_order" name="id_order" required>
                                                        <option value="">Pilih No Model </option>
                                                        <?php foreach ($no_model as $item): ?>
                                                            <option value="<?= $item['id_order'] ?>"><?= $item['no_model'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="text" class="form-control" id="no_model" name="no_model" hidden>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Item Type</label>
                                                    <select class="form-select" name="item_type" id="item_type" required>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Kode Warna</label>
                                                    <select class="form-select" name="kode_warna" id="kode_warna" required>
                                                    </select>
                                                </div>

                                            </div>

                                            <!-- Surat Jalan Section -->
                                            <div class="row g-3 mt-3">
                                                <div class="col-md-3">
                                                    <label>Warna</label>
                                                    <input type="text" class="form-control" name="warna" id="warna" required placeholder="Warna" readonly>
                                                </div>

                                                <div class="col-md-3">
                                                    <label>Harga</label>
                                                    <input type="number" step="0.01" class="form-control" name="harga" id="harga" placeholder="Harga" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Lot</label>
                                                    <input type="text" class="form-control" name="lot" id="lot" placeholder="Lot" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>LMD</label>
                                                    <select class="form-control" name="l_m_d" id="l_m_d" placeholder="L/M/D" required>
                                                        <option value="">Pilih LMD</option>
                                                        <option value="L">L</option>
                                                        <option value="M">M</option>
                                                        <option value="D">D</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <label for="ganti-retur" class="text-center">Ganti Retur</label>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label>
                                                                <input type="hidden" name="ganti_retur" value="0">
                                                                <input type="checkbox" name="ganti_retur" id="ganti_retur" value="1"
                                                                    <?= isset($data['ganti_retur']) && $data['ganti_retur'] == 1 ? 'checked' : '' ?>>
                                                            </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="">Ya</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-5">
                                                <h3>Form Input Data Karung</h3>
                                            </div>

                                            <!-- Out Celup Section -->
                                            <div class="row g-3 mt-3">
                                                <div class="table-responsive">
                                                    <table id="poTable" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th width=100 class="text-center">No Karung</th>
                                                                <th class="text-center">GW Kirim</th>
                                                                <th class="text-center">NW Kirim</th>
                                                                <th class="text-center">Cones Kirim</th>
                                                                <th class="text-center">Cluster</th>
                                                                <th class="text-center">Kapasitas</th>
                                                                <th class="text-center">
                                                                    <button type="button" class="btn btn-info" id="addRow">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><input type="text" class="form-control text-center" name="no_karung[0]" value="1" readonly></td>
                                                                <td><input type="number" step="0.01" class="form-control gw" name="gw[0]" required></td>
                                                                <td><input type="number" step="0.01" class="form-control kgs" name="kgs[0]" required></td>
                                                                <td><input type="number" step="0.01" class="form-control cones" name="cones[0]" required></td>
                                                                <td style="width: 180px;">
                                                                    <select class="form-control cluster" name="cluster[0]" required>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" class="form-control kapasitas" name="kapasitas[0]" data-sisa_kapasitas="" readonly></td>
                                                                <td class="text-center">
                                                                    <!-- <button type="button" class="btn btn-danger removeRow">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button> -->
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <!-- Baris Total -->
                                                        <tfoot>
                                                            <tr>
                                                                <th class="text-center">Total Karung</th>
                                                                <th class="text-center">Total GW</th>
                                                                <th class="text-center">Total NW</th>
                                                                <th class="text-center">Total Cones</th>
                                                                <th></th>
                                                            </tr>
                                                            <tr>
                                                                <td><input type="number" class="form-control" id="total_karung" name="total_karung" placeholder="Total Karung" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_gw" name="total_gw" placeholder="GW" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_kgs" name="total_kgs" placeholder="NW" readonly></td>
                                                                <td><input type="float" class="form-control" id="total_cones" name="total_cones" placeholder="Cones" readonly></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Buttons -->
                                            <!-- <div class="row mt-3">
                                                <div class="col-12 text-center mt-2">
                                                    <button class="btn btn-icon btn-3 btn-outline-info add-more" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-plus"></i></span>
                                                    </button>
                                                    <button class="btn btn-icon btn-3 btn-outline-danger remove-tab" type="button">
                                                        <span class="btn-inner--icon"><i class="fas fa-trash"></i></span>
                                                    </button>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-info w-100">Save</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            $('#id_order').select2();
            $('#id_order').on("select2:select", function() {
                let id_order = $(this).val(); // Ambil value yang dipilih di select2
                let no_model = $('#id_order option:selected').text(); // Ambil teks (No Model) dari opsi yang dipilih

                // Isi nilai No Model ke input
                $('#no_model').val(no_model);

                // Ambil item type berdasarkan id_order
                $.ajax({
                    url: "<?= base_url($role . '/otherIn/getItemTypeForOtherIn/') ?>" + id_order,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        // Kosongkan opsi sebelumnya
                        $('#item_type').empty();

                        // Tambahkan opsi default
                        $('#item_type').append('<option value="">-- Pilih Item Type --</option>');

                        // Iterasi data untuk menambahkan opsi
                        if (data && data.length > 0) {
                            data.forEach(function(item) {
                                $('#item_type').append(
                                    '<option value="' + item.item_type + '">' + item.item_type + '</option>'
                                );
                            });
                        } else {
                            $('#item_type').append('<option value="">Tidak ada item tersedia</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengambil data Item Type.');
                    }
                });
            });

            // Event listener untuk mendapatkan kode warna saat item type dipilih
            $('#item_type').on("change", function() {
                let id_order = $('#id_order').val(); // Pastikan id_order masih sama
                let item_type = $(this).val(); // Ambil nilai item type yang dipilih

                // Ambil kode warna berdasarkan id_order dan item_type
                if (item_type) {
                    $.ajax({
                        url: "<?= base_url($role . '/otherIn/getKodeWarnaForOtherIn') ?>",
                        type: "POST",
                        data: {
                            id_order: id_order,
                            item_type: item_type,
                        },
                        dataType: "json",
                        success: function(data) {
                            console.log(data);
                            // Kosongkan opsi sebelumnya
                            $('#kode_warna').empty();

                            // Tambahkan opsi default
                            $('#kode_warna').append('<option value="">-- Pilih Kode Warna --</option>');

                            // Iterasi data untuk menambahkan opsi
                            if (data && data.length > 0) {
                                data.forEach(function(item) {

                                    $('#kode_warna').append(
                                        '<option value="' + item.kode_warna + '">' + item.kode_warna + '</option>'
                                    );
                                });
                            } else {
                                $('#kode_warna').append('<option value="">Tidak ada kode warna tersedia</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mengambil data Kode Warna.');
                        }
                    });
                } else {
                    // Kosongkan dropdown jika tidak ada item type yang dipilih
                    $('#kode_warna').empty();
                    $('#kode_warna').append('<option value="">-- Pilih Kode Warna --</option>');
                }
            });
            // Event listener untuk mendapatkan kode warna saat item type dipilih
            $('#kode_warna').on("change", function() {
                let id_order = $('#id_order').val(); // Pastikan id_order masih sama
                let item_type = $('#item_type').val(); // Pastikan id_order masih sama
                let kode_warna = $(this).val(); // Ambil nilai item type yang dipilih

                // Ambil kode warna berdasarkan id_order dan item_type
                if (item_type) {
                    $.ajax({
                        url: "<?= base_url($role . '/otherIn/getWarnaForOtherIn') ?>",
                        type: "POST",
                        data: {
                            id_order: id_order,
                            item_type: item_type,
                            kode_warna: kode_warna,
                        },
                        dataType: "json",
                        success: function(data) {
                            $('#warna').val(data.color); // Ambil warna dari respons
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat mengambil data Kode Warna.');
                        }
                    });
                } else {
                    // Kosongkan dropdown jika tidak ada item type yang dipilih
                    $('#warna').empty();
                }
            });
        });


        document.addEventListener('DOMContentLoaded', () => {
            const poTable = $('#poTable');

            // inisialisasi select2 di semua .cluster
            poTable.find('.cluster').select2({
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%'
            });

            // kalau GW/KGS/Cones berubah → total
            poTable.on('input', '.gw, .kgs, .cones', () => calculateTotals());

            // kalau KGS atau cluster berubah → rebuild semua opsi & kapasitas
            poTable.on('input', '.kgs', updateClusterOptions);
            poTable.on('change', '.cluster', updateClusterOptions);

            // remove row
            poTable.on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateRowNumbers();
                calculateTotals();
                updateClusterOptions();
            });
        });

        // tombol “+” baris
        $('#addRow').on('click', function() {
            const $tbody = $('#poTable tbody');
            const idx = $tbody.find('tr').length;
            const $row = $(`
        <tr>
            <td><input type="text" class="form-control text-center" name="no_karung[${idx}]" value="${idx+1}" readonly></td>
            <td><input type="number" step="0.01" class="form-control gw" name="gw[${idx}]" required></td>
            <td><input type="number" step="0.01" class="form-control kgs" name="kgs[${idx}]" required></td>
            <td><input type="number" step="0.01" class="form-control cones" name="cones[${idx}]" required></td>
            <td><select class="form-control cluster" name="cluster[${idx}]" required></select></td>
            <td><input type="text" class="form-control kapasitas" name="kapasitas[${idx}]" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeRow">
                  <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `);
            $tbody.append($row);
            // init select2
            $row.find('.cluster').select2({
                allowClear: true,
                minimumResultsForSearch: 0,
                width: '100%'
            });
            updateRowNumbers();
            calculateTotals();
            updateClusterOptions();
        });

        // ambil data & rebuild semua dropdown + kapasitas
        function updateClusterOptions() {
            // Hitung usage per cluster
            const usage = {};
            $('#poTable tbody tr').each(function() {
                const $tr = $(this);
                const c = $tr.find('.cluster').val();
                const k = parseFloat($tr.find('.kgs').val()) || 0;
                if (c && k > 0) usage[c] = (usage[c] || 0) + k;
            });

            // ALWAYS pass kgs=0 so backend returns semua cluster with sisa>=0
            $.post('<?= base_url($role . "/getcluster") ?>', {
                    kgs: 0
                }, function(resp) {
                    // resp = [ {nama_cluster, sisa_kapasitas}, … ]
                    const clusters = resp.reduce((acc, c) => {
                        acc[c.nama_cluster] = parseFloat(c.sisa_kapasitas);
                        return acc;
                    }, {});

                    // di updateClusterOptions, ganti bagian rebuild setiap select dengan ini:
                    $('#poTable tbody tr').each(function() {
                        const $tr = $(this);
                        const needed = parseFloat($tr.find('.kgs').val()) || 0;
                        const old = $tr.find('.cluster').val();

                        const $sel = $tr.find('.cluster').empty()
                            .append('<option value="">Pilih Cluster</option>');

                        Object.entries(clusters).forEach(([name, avail]) => {
                            const realAvail = avail - (usage[name] || 0);
                            const $opt = $('<option>')
                                .val(name)
                                .text(name)
                                .attr('data-sisa_kapasitas', realAvail.toFixed(2));

                            if (name === old) {
                                // selalu append opsi lama, tapi disable jika benar-benar habis
                                // if (realAvail <= 0) {
                                //     $opt.prop('disabled', true);
                                // }
                                $sel.append($opt);
                            } else if (realAvail < needed) {
                                // kalau tidak cukup untuk needed dan bukan pilihan lama → skip
                                return;
                            } else {
                                // cukup dan bukan pilihan lama
                                $sel.append($opt);
                            }
                        });

                        // re‐init select2 & restore old
                        $sel.trigger('destroy.select2')
                            .select2({
                                allowClear: true,
                                minimumResultsForSearch: 0,
                                width: '100%'
                            })
                            .val(old)
                            .trigger('change.select2');
                    });



                    // apply kapasitas untuk semua yang punya cluster
                    $('#poTable tbody tr').each(function() {
                        const $tr = $(this);
                        const sel = $tr.find('.cluster option:selected');
                        if (sel.val()) {
                            $tr.find('.kapasitas')
                                .val(sel.data('sisa_kapasitas'));
                        } else {
                            $tr.find('.kapasitas').val('');
                        }
                    });
                }, 'json')
                .fail((_, __, err) => console.error('getCluster error:', err));
        }

        function updateRowNumbers() {
            $('#poTable tbody tr').each((i, tr) => {
                $(tr).find("input[name^='no_karung']").val(i + 1);
            });
        }

        function calculateTotals() {
            let gw = 0,
                kgs = 0,
                cones = 0;
            $('#poTable tbody tr').each(function() {
                gw += parseFloat($(this).find('.gw').val()) || 0;
                kgs += parseFloat($(this).find('.kgs').val()) || 0;
                cones += parseFloat($(this).find('.cones').val()) || 0;
            });
            const rows = $('#poTable tbody tr').length;
            $('#total_karung').val(rows);
            $('#total_gw').val(gw.toFixed(2));
            $('#total_kgs').val(kgs.toFixed(2));
            $('#total_cones').val(cones.toFixed(2));
        }
    </script>

    <?php $this->endSection(); ?>