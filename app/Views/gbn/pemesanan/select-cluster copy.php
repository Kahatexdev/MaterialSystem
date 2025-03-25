<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<style>
    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background-color: rgb(8, 38, 83);
        color: white;
        font-weight: bold;
    }
</style>

<div class="container my-4">
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: '<?= session()->getFlashdata('error') ?>'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h3>Data Stock <?= $noModel; ?></h3>
        </div>
    </div>

    <div class="card-container">
        <?php if (!empty($cluster)): ?>
            <?php foreach ($cluster as $item): ?>
                <div class="card" data-id-stok="<?= esc($item['id_stock']); ?>">
                    <div class="card-header">
                        <?= esc($item['nama_cluster']); ?>
                    </div>
                    <div class="card-body">
                        <p><strong>KGS Stock Awal:</strong> <?= esc($item['kgs_stock_awal']); ?></p>
                        <p><strong>CNS Stock Awal:</strong> <?= esc($item['cns_stock_awal']); ?></p>
                        <p><strong>KRg Stock Awal:</strong> <?= esc($item['krg_stock_awal']); ?></p>
                        <p><strong>Lot Awal:</strong> <?= esc($item['lot_awal']); ?></p>
                        <p><strong>KGS In/Out:</strong> <?= esc($item['kgs_in_out']); ?></p>
                        <p><strong>CNS In/Out:</strong> <?= esc($item['cns_in_out']); ?></p>
                        <p><strong>KRg In/Out:</strong> <?= esc($item['krg_in_out']); ?></p>
                        <p><strong>Lot Stock:</strong> <?= esc($item['lot_stock']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Data pemasukan tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Detail Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Detail data akan dimuat di sini -->
                </div>
                <hr>
                <!-- Form untuk mengisi qty KGS, qty CNS, dan qty Karung -->
                <form id="usageForm">
                    <div class="mb-3">
                        <label for="qtyKGS" class="form-label">Qty KGS</label>
                        <input type="number" class="form-control" id="qtyKGS" name="qtyKGS" placeholder="Masukkan Qty KGS" required>
                    </div>
                    <div class="mb-3">
                        <label for="qtyCNS" class="form-label">Qty CNS</label>
                        <input type="number" class="form-control" id="qtyCNS" name="qtyCNS" placeholder="Masukkan Qty CNS" required>
                    </div>
                    <div class="mb-3">
                        <label for="qtyKarung" class="form-label">Qty Karung</label>
                        <input type="number" class="form-control" id="qtyKarung" name="qtyKarung" placeholder="Masukkan Qty Karung" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const idStok = this.getAttribute('data-id-stok');
                fetch(`/gbn/pemasukan/getDataByIdStok/${idStok}`)
                    .then(response => response.json())
                    .then(data => {
                        let content = '';

                        if (Array.isArray(data) && data.length > 0) {
                            let item = data[0]; // Ambil item pertama jika dalam array
                            content = `
                            <div class="row mb-2">
    <div class="col-3"><span class="badge bg-info text-wrap">PDK: ${item.no_model}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Item Type: ${item.item_type}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Kode Warna: ${item.kode_warna}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Warna: ${item.warna}</span></div>
    </div>
    <div class="row mb-2">
    <div class="col-3"><span class="badge bg-info text-wrap">KGS Masuk: ${item.kgs_masuk}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">CNS Masuk: ${item.cns_masuk}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Cluster: ${item.nama_cluster}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Lot Awal: ${item.lot_awal}</span></div>
</div>
                        `;
                        } else if (typeof data === 'object') {
                            content = `
                            <div class="row mb-2">
    <div class="col-3"><span class="badge bg-info text-wrap">No Model: ${item.no_model}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Item Type: ${item.item_type}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Kode Warna: ${item.kode_warna}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Warna: ${item.warna}</span></div>
    </div>
    <div class="row mb-2">
    <div class="col-3"><span class="badge bg-info text-wrap">KGS Masuk: ${item.kgs_masuk}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">CNS Masuk: ${item.cns_masuk}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Cluster: ${item.nama_cluster}</span></div>
    <div class="col-3"><span class="badge bg-info text-wrap">Lot Awal: ${item.lot_awal}</span></div>
</div>
                        `;
                        } else {
                            content = 'Data tidak ditemukan.</p>';
                        }

                        document.getElementById('modalContent').innerHTML = content;
                        const modal = new bootstrap.Modal(document.getElementById('dataModal'));
                        modal.show();
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Event listener untuk submit form (sesuaikan aksi sesuai kebutuhan)
        document.getElementById('usageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const qtyKGS = document.getElementById('qtyKGS').value;
            const qtyCNS = document.getElementById('qtyCNS').value;
            const qtyKarung = document.getElementById('qtyKarung').value;

            // Lakukan proses penyimpanan atau pengiriman data qty sesuai dengan kebutuhan
            console.log('Qty KGS:', qtyKGS, 'Qty CNS:', qtyCNS, 'Qty Karung:', qtyKarung);

            // Contoh: kirim data ke server dengan fetch API
            // fetch('/url/untuk/menyimpan', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ qtyKGS, qtyCNS, qtyKarung })
            // }).then(response => response.json())
            //   .then(data => console.log(data))
            //   .catch(error => console.error('Error:', error));
        });
    });
</script>

<?php $this->endSection(); ?>