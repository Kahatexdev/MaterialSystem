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
        <div class="card-header">
            <h2 class="mb-0">Data Pemasukan</h2>
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
                    <!-- Data will be loaded here -->
                </div>
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
                            <p><strong>ID Pemasukan:</strong> ${item.id_pemasukan}</p>
                            <p><strong>No Model:</strong> ${item.no_model}</p>
                            <p><strong>Item Type:</strong> ${item.item_type}</p>
                            <p><strong>Kode Warna:</strong> ${item.kode_warna}</p>
                            <p><strong>Warna:</strong> ${item.warna}</p>
                            <p><strong>KGS Masuk:</strong> ${item.kgs_masuk}</p>
                            <p><strong>CNS Masuk:</strong> ${item.cns_masuk}</p>
                            <p><strong>Tanggal Masuk:</strong> ${item.tgl_masuk}</p>
                            <p><strong>Nama Cluster:</strong> ${item.nama_cluster}</p>
                            <p><strong>Admin:</strong> ${item.admin}</p>
                            <p><strong>Stock Awal KGS:</strong> ${item.kgs_stock_awal}</p>
                            <p><strong>Stock Awal CNS:</strong> ${item.cns_stock_awal}</p>
                            <p><strong>Lot Awal:</strong> ${item.lot_awal}</p>
                        `;
                        } else if (typeof data === 'object') {
                            content = `
                            <p><strong>ID Pemasukan:</strong> ${data.id_pemasukan}</p>
                            <p><strong>No Model:</strong> ${data.no_model}</p>
                            <p><strong>Item Type:</strong> ${data.item_type}</p>
                            <p><strong>Kode Warna:</strong> ${data.kode_warna}</p>
                            <p><strong>Warna:</strong> ${data.warna}</p>
                            <p><strong>KGS Masuk:</strong> ${data.kgs_masuk}</p>
                            <p><strong>CNS Masuk:</strong> ${data.cns_masuk}</p>
                            <p><strong>Tanggal Masuk:</strong> ${data.tgl_masuk}</p>
                            <p><strong>Nama Cluster:</strong> ${data.nama_cluster}</p>
                            <p><strong>Admin:</strong> ${data.admin}</p>
                            <p><strong>Stock Awal KGS:</strong> ${data.kgs_stock_awal}</p>
                            <p><strong>Stock Awal CNS:</strong> ${data.cns_stock_awal}</p>
                            <p><strong>Lot Awal:</strong> ${data.lot_awal}</p>
                        `;
                        } else {
                            content = '<p>Data tidak ditemukan.</p>';
                        }

                        document.getElementById('modalContent').innerHTML = content;
                        const modal = new bootstrap.Modal(document.getElementById('dataModal'));
                        modal.show();
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>

<?php $this->endSection(); ?>