<?php $this->extend($role . '/po/header'); ?>
<?php $this->section('content'); ?>

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

    <div class="card card-frame">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bolder">Form Buka PO Covering</h5>
                <a href="<?= base_url($role . '/po') ?>" class="btn bg-gradient-info">Kembali</a>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="<?= base_url($role . '/po/saveOpenPOCovering') ?>" method="post">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>No PO</label>
                            <input type="text" class="form-control" name="no_model" value="" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <div class="form-group">
                            <label>Tanggal PO (Gudang Benang)</label>
                            <input type="date" class="form-control" name="tgl_po" id="tgl_po" value="" required>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group">
                            <label>Tanggal PO (Covering)</label>
                            <input type="date" class="form-control" name="tgl_po_covering" id="tgl_po_covering" value="" required>
                        </div>
                    </div>
                </div>
                <!-- Card untuk menampilkan detail -->
                <div class="row mt-4" id="detailContainer">
                    <!-- Detail akan ditampilkan di sini -->
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="bentuk_celup">Bentuk Celup</label>
                        <select class="form-control bentuk-celup" name="bentuk_celup">
                            <option value="">Pilih Bentuk Celup</option>
                            <option value="Cones">Cones</option>
                            <option value="Hank">Hank</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_produksi">Untuk Produksi</label>
                        <input type="text" class="form-control jenis-produksi" name="jenis_produksi">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <label for="ket_celup">Keterangan Celup</label>
                        <textarea class="form-control ket-celup" name="ket_celup"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-info w-100 mt-3">Save</button>
        </div>
        </form>
    </div>
</div>

<!-- JS Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.getElementById('tgl_po').addEventListener('change', function() {
        const tglPo = this.value;

        fetch("<?= base_url($role . '/po/getDetailByTglPO') ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'tgl_po=' + encodeURIComponent(tglPo)
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('detailContainer');
                container.innerHTML = ''; // kosongkan sebelum isi baru

                if (data.length > 0) {
                    data.forEach((item, index) => {
                        const card = `
                        <div class="col-md-6" id="card-${index}">
                            <div class="card border-dark">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column">
                                        <strong> ${item.no_model}</strong>
                                        <strong>${item.item_type}</strong>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-white" onclick="hapusCard(${index})">
                                            <i class="fas fa-trash dark"></i>
                                        </button>
                                        <input type="hidden" class="form-control" name="detail[${index}][id_induk]" value="${item.id_induk ?? ''}">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-2">
                                        <label>Item Type</label><label class="text-danger">*</label>
                                        <input type="text" class="form-control" name="detail[${index}][item_type]" value="${item.item_type ?? ''}" required>
                                        <label class="text-danger">*Sesuaikan Item Type</label>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Kode Warna</label>
                                        <input type="text" class="form-control" name="detail[${index}][kode_warna]" value="${item.kode_warna ?? ''}" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Warna</label>
                                        <input type="text" class="form-control" name="detail[${index}][color]" value="${item.color ?? ''}" required>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Total KG PO</label>
                                        <input type="number" step="0.01" class="form-control" name="detail[${index}][kg_total_po]" value="${item.total_kg_po ?? 0}" required readonly>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>KG Celup</label>
                                        <input type="number" step="0.01" class="form-control" name="detail[${index}][kg_po]" required>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                        container.innerHTML += card;
                    });
                } else {
                    container.innerHTML = `<div class="col-12 text-center text-muted">Tidak ada detail PO untuk tanggal ini.</div>`;
                }
            });
    });
</script>
<script>
    function hapusCard(index) {
        const card = document.getElementById(`card-${index}`);
        if (card) {
            card.remove();
        }
    }
</script>

<?php $this->endSection(); ?>