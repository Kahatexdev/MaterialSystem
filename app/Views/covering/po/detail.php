<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Select Option -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="noModelSelect">No Model</label>
                                <select class="form-control" id="noModelSelect">
                                    <option value="">Pilih No Model</option>
                                    <?php foreach ($poDetail as $row) : ?>
                                        <option value="<?= $row['no_model'] ?>"><?= $row['no_model'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk menampilkan detail -->
    <div class="row mt-4" id="detailContainer">
        <!-- Detail akan ditampilkan di sini -->
    </div>

    <!-- Form untuk Covering Buka PO ke Celupan -->
    <div class="row mt-3" id="coveringFormContainer" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Covering Buka PO ke Celupan</h5>
                </div>
                <div class="card-body">
                    <form id="coveringForm">
                        <div id="itemCardsContainer" class="row">
                            <!-- Item cards akan diisi di sini -->
                        </div>


                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-info w-100">Simpan Ke Tabel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3" id="CelupcoveringFormContainer" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="POST" action="<?= base_url($role . '/po/savePOCovering') ?>" id="coveringCelupForm">
                            <table class="table table-flush" id="datatable-basic">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Select</th>
                                        <th>No</th>
                                        <th>No Model</th>
                                        <th>Item Type</th>
                                        <th>Item Type Covering</th>
                                        <th>Kode Warna Covering</th>
                                        <th>Qty Covering</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($coveringData as $index => $item) {
                                        echo "<tr>";
                                        echo "<td><input type='checkbox' name='selected_items[]' value='{$index}'></td>";
                                        echo "<td>" . ($index + 1) . "</td>";
                                        echo "<td>" . htmlspecialchars($item['no_model']) . "</td>";
                                        echo "<td>" . htmlspecialchars($item['item_type']) . "</td>";
                                        echo "<td>" . htmlspecialchars($item['itemTypeCovering']) . "</td>";
                                        echo "<td>" . htmlspecialchars($item['kodeWarnaCovering']) . "</td>";
                                        echo "<td>" . htmlspecialchars($item['qty_covering']) . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-info w-100">Buat PO ke Celupan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script AJAX -->
<script>
    $(document).ready(function() {
        // Event ketika dropdown berubah
        $('#noModelSelect').change(function() {
            var tglPO = "<?= $tgl_po ?>";
            var noModel = $(this).val();

            if (noModel) {
                $.ajax({
                    url: "<?= base_url($role . '/getDetailByNoModel') ?>/" + tglPO + "/" + noModel,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        $('#detailContainer').empty();
                        $('#itemCardsContainer').empty();
                        $('#coveringFormContainer').show();
                        $('#CelupcoveringFormContainer').show();

                        // Buat card untuk setiap item
                        response.forEach(function(item, index) {
                            var cardHtml = `
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-gradient-info text-white">
                                            <h6 class="card-title">Item Type PO : ${item.item_type}</h6>
                                            <h6 class="card-title">Kode Warna : ${item.kode_warna}</h6>
                                            <h6 class="card-title">Kg PO : ${item.kg_po}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <input type="hidden" name="items[${index}][no_model]" value="${noModel}">
                                                    <input type="hidden" name="items[${index}][item_type]" value="${item.item_type}">
                                                    
                                                    <div class="form-group">
                                                        <label>Item Type Covering</label>
                                                        <input type="text" class="form-control" 
                                                            name="items[${index}][itemTypeCovering]" 
                                                            value=""
                                                            required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Kode Warna</label>
                                                        <input type="text" class="form-control" 
                                                            name="items[${index}][kodeWarnaCovering]" 
                                                            value="${item.kode_warna}" 
                                                            required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Qty Covering</label>
                                                        <input type="number" class="form-control" 
                                                            name="items[${index}][qty_covering]" 
                                                            step="0.01" 
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#itemCardsContainer').append(cardHtml);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: " + status + error);
                        $('#detailContainer').html('<div class="alert alert-danger">Gagal memuat data</div>');
                        $('#coveringFormContainer').hide();
                    }
                });
            } else {
                $('#detailContainer').empty();
                $('#coveringFormContainer').hide();
            }
        });

        // Submit form untuk menyimpan ke session
        $('#coveringForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();

            $.ajax({
                url: "<?= base_url($role . '/po/simpanKeSession') ?>",
                method: "POST",
                data: formData,
                success: function(response) {
                    alert('Data berhasil disimpan di session');
                    location.reload(); // Refresh halaman setelah data ditambahkan
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                    alert('Gagal menyimpan data');
                }
            });
        });

        // Submit form untuk menyimpan data covering
        $('#coveringCelupForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();

            $.ajax({
                url: "<?= base_url($role . '/po/savePOCovering') ?>",
                method: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);
                    alert('Data berhasil disimpan');
                    location.reload(); // Refresh halaman setelah data diperbarui atau dihapus
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                    alert('Gagal menyimpan data');
                }
            });
        });
    });
</script>


<?php $this->endSection(); ?>