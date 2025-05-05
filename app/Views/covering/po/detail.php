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

    <div class="row mt-3" id="CelupcoveringFormContainer">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="POST" action="<?= base_url($role . '/po/savePOCovering') ?>" id="coveringCelupForm">
                            <div class="form-group">
                                <label>No PO</label>
                                <input type="text" class="form-control"
                                    name="no_po"
                                    value=""
                                    required>
                                <input type="hidden" name="tgl_po" value="<?= $tgl_po ?>">
                            </div>
                            <table class="table table-flush" id="datatable-basic">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Select</th>
                                        <th>No</th>
                                        <th>No Model</th>
                                        <th>Item Type Covering</th>
                                        <th>Kode Warna</th>
                                        <th>Warna</th>
                                        <th>Qty Covering</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    foreach ($coveringData as $index => $item) {
                                        echo "<tr>";
                                        echo "<td><input type='checkbox' name='selected_items[]' value='{$index}'></td>";
                                        echo "<td>" . ($index + 1) . "</td>";
                                        echo "<td>{$item['no_model']}</td>";
                                        echo "<td>{$item['itemTypeCovering']}</td>";
                                        echo "<td>{$item['kodeWarnaCovering']}</td>";
                                        echo "<td>{$item['warnaCovering']}</td>";
                                        echo "<td>{$item['qty_covering']}</td>";
                                        echo "<td>{$item['keterangan']}</td>";
                                        // echo "<input type='hidden' name='items[{$index}][id_po]' value='{$item['id_po']}'>";
                                        echo "<td><a href='" . base_url($role . '/po/deletePOCovering/' . $index) . "' class='btn btn-danger'><i class='fas fa-trash'></i></a></td>";
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
            if (noModel.startsWith("POCOVERING")) {
                noModel = noModel.replace("POCOVERING", "").replace(/_/g, "").trim();
            }

            console.log(noModel);
            if (noModel) {
                $.ajax({
                    url: "<?= base_url($role . '/getDetailByNoModel') ?>/" + tglPO + "/" + noModel,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        // flatten 1 level
                        const items = response.flat();

                        $('#detailContainer').empty();
                        $('#itemCardsContainer').empty();
                        $('#coveringFormContainer').show();
                        $('#CelupcoveringFormContainer').show();

                        items.forEach(function(item, index) {
                            const cardHtml = `
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-gradient-info text-white">
                        <h6 class="card-title">Item Type PO: ${item.item_type}</h6>
                        <h6 class="card-title">Kode Warna: ${item.kode_warna}</h6>
                        <h6 class="card-title">Kg PO: ${item.kg_po}</h6>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="items[${index}][id_po]" value="${item.id_po}">
                        <input type="hidden" name="items[${index}][no_model]" value="${item.no_model}">
                        <div class="form-group">
                            <label>Item Type Covering</label>
                            <input type="text" class="form-control"
                                name="items[${index}][itemTypeCovering]"
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
                            <label>Warna</label>
                            <input type="text" class="form-control"
                                name="items[${index}][warnaCovering]"
                                value="${item.color}"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Qty Covering</label>
                            <input type="number" class="form-control"
                                name="items[${index}][qty_covering]"
                                step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea class="form-control"
                                name="items[${index}][keterangan]">${item.keterangan}</textarea>
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
                    Swal.fire({
                        title: "Sukses!",
                        text: "Data berhasil disimpan di session",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah alert selesai
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                    Swal.fire({
                        title: "Gagal!",
                        text: "Gagal menyimpan data",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        });
    });
</script>


<?php $this->endSection(); ?>