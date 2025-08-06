<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<style>
    /* Main container styling */
    .container-fluid {
        padding: 1.5rem;
    }

    /* Card grid styling */
    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-top: 1.5rem;
    }

    /* Individual card styling */
    .stock-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        overflow: hidden;
        height: 100%;
    }

    .stock-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #c9d1d9;
    }

    .stock-card .card-header {
        background-color: #082653;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-bottom: none;
    }

    .stock-card .card-body {
        padding: 1.25rem;
    }

    .card-pinjam-order {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        overflow: hidden;
        height: 100%;
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        border-color: #c9d1d9;
        /* background-color: #082653; */
        color: white;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-bottom: none;
        padding: 1.25rem;
        background: linear-gradient(45deg, #082653, rgb(20, 74, 155));
        color: #fff;
        /* border: 2px dashed #ff5722; */
    }

    /* Stock info styling */
    .stock-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .stock-info .label {
        font-weight: 500;
        color: #495057;
    }

    .stock-info .value {
        font-weight: 600;
        color: #212529;
    }

    /* Divider styling */
    .divider {
        height: 1px;
        background-color: #e9ecef;
        margin: 0.75rem 0;
    }

    /* Modal styling */
    .modal-header {
        background-color: #082653;
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        color: white;
        filter: brightness(0) invert(1);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .info-badge {
        font-size: 0.85rem;
        padding: 0.5rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-weight: 500;
        background-color: #e9f3ff;
        color: #0d6efd;
        border: 1px solid #c9deff;
    }

    .info-section {
        margin-bottom: 1.5rem;
    }

    .info-section-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #082653;
        border-bottom: 2px solid #082653;
        padding-bottom: 0.5rem;
        display: inline-block;
    }

    /* Form styling */
    .form-section {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 8px;
        margin-top: 1.5rem;
    }

    .form-section-title {
        font-weight: 600;
        margin-bottom: 1rem;
        color: #082653;
    }

    .form-control {
        border-radius: 6px;
        padding: 0.6rem 0.75rem;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(8, 38, 83, 0.25);
        border-color: #082653;
    }

    .btn-submit {
        background-color: #082653;
        border-color: #082653;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
    }

    .btn-submit:hover {
        background-color: #061c3e;
        border-color: #061c3e;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 3rem;
        background-color: #f8f9fa;
        border-radius: 10px;
        grid-column: 1 / -1;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #6c757d;
        font-weight: 500;
    }

    .custom-button {
        background-color: #061c3e;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        min-width: 80px;
        justify-content: center;
    }

    .custom-button:hover {
        background-color: rgb(13, 42, 85);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    .custom-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
    }

    .custom-button i {
        font-size: 16px;
    }

    .btn-xs {
        width: 28px;
        height: 28px;
    }

    .btn-round {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: auto;
    }

    .btn-round i {
        font-size: 14px;
    }

    .bg-gradient-blue {
        background: linear-gradient(45deg, #082653, rgb(33, 114, 235));
    }


    /* Responsive Layout */
    @media (min-width: 768px) {
        .card-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .card-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1200px) {
        .card-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    html: '<?= session()->getFlashdata('success') ?>',
                    confirmButtonColor: '#082653'
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
                    html: '<?= session()->getFlashdata('error') ?>',
                    confirmButtonColor: '#082653'
                });
            });
        </script>
    <?php endif; ?>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><?= $noModel . ' - ' . $itemType . ' - ' . $kodeWarna; ?></h3>
                <span class="badge bg-gradient-blue fs-6 shadow-sm px-3 py-2">
                    <?= date('d F Y'); ?>
                </span>
            </div>

            <!-- Sub Title -->
            <h5 class="text-bold fw-semibold mb-3">📦 Informasi Pemesanan</h5>

            <!-- Grid Cards -->
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Kg Pesan</h6>
                        <h4 class="fw-semibold mb-0"><?= number_format($KgsPesan, 2) ?> Kg</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Cns Pesan</h6>
                        <h4 class="fw-semibold mb-0"><?= $CnsPesan ?> Cns</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Kg Persiapan</h6>
                        <h4 class="fw-semibold mb-0"><?= $kgPersiapan ? number_format($kgPersiapan, 2) : '0' ?> Kg</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-container">
        <?php if (!empty($cluster)): ?>
            <?php foreach ($cluster as $item): ?>
                <div class="stock-card" data-id-stok="<?= esc($item['id_stock']); ?>" data-nama-cluster="<?= $item['nama_cluster']; ?>" data-no-model="<?= $item['no_model']; ?>" data-item-type="<?= $item['item_type']; ?>" data-kode-warna="<?= $item['kode_warna']; ?>">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-warehouse me-2 text-white"></i>
                            Cluster <?= esc($item['nama_cluster']); ?> (<?= (number_format($item['total_kgs'], 2) . ' Kg / ' . $item['total_krg'] . ' Krg'); ?>)
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="bg-gradient-danger w-100 empty-state">
                <h6 class="text-white">Stock Kosong <?= $noModel; ?>.</h6>
            </div>
        <?php endif; ?>
        <div class="card-pinjam-order" data-item-type="<?= $itemType ?>" data-kode-warna="<?= $kodeWarna ?>">
            <div class=" card-header d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center">
                    <i class="fas fa-warehouse me-2 text-white"></i>
                    Pinjam Order
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Improved Modal -->
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="dataModalLabel">Detail Data Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-section">
                    <h4 class="form-section-title"><strong>Pengeluaran Stock</strong></h4>
                    <form id="pengeluaran" method="post" action="<?= base_url('gbn/simpanPengeluaranJalur/' . $id . '?Area=' . $area . '&KgsPesan=' . $KgsPesan . '&CnsPesan=' . $CnsPesan . '&pinjam='); ?>">
                        <div class="row" id="formPengeluaran">
                            <!-- Form input pengeluaran stock will be loaded here -->
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-check-circle me-1"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
                <div class="divider"></div>
                <div class="">
                    <!-- <h6 class="form-section-title">Input Pengeluaran Stock</h6> -->
                    <form id="usageForm" method="post">
                        <input type="hidden" id="idStok" name="idStok">
                        <input type="hidden" id="noModel" name="noModel" value="<?= $noModel; ?>">
                        <input type="hidden" id="namaCluster" name="namaCluster" value="<?= $item['nama_cluster'] ?? NULL ?>">
                        <input type="hidden" id="lotFinal" name="lotFinal" value="<?= $item['lot_final'] ?? NULL ?>">
                        <!-- <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="qtyKGS" class="form-label">Qty KGS</label>
                                <div class="input-group">
                                    <input type="number" step=0.1 class="form-control" id="qtyKGS" name="qtyKGS" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="qtyCNS" class="form-label">Qty CNS</label>
                                <div class="input-group">
                                    <input type="number" step=0.1 class="form-control" id="qtyCNS" name="qtyCNS" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="qtyKarung" class="form-label">Qty Karung</label>
                                <div class="input-group">
                                    <input type="number" step=0.1 class="form-control" id="qtyKarung" name="qtyKarung" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-check-circle me-1"></i> Submit
                            </button>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal pinjam order -->
<div class="modal fade" id="pinjamOrderModal" tabindex="-1" aria-labelledby="modelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100">
                    <h5 class="modal-title text-white mb-2">Pinjam Order</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pinjamOrder" method="post" action="<?= base_url('gbn/simpanPengeluaranJalur/' . $id . '?Area=' . $area . '&KgsPesan=' . $KgsPesan . '&CnsPesan=' . $CnsPesan . '&pinjam=YA'); ?>">
                    <div id="formPinjamOrder">
                        <!-- Form input pengeluaran stock will be loaded here -->
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check-circle me-1"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Card click event
        const cards = document.querySelectorAll('.stock-card');
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const idStok = this.getAttribute('data-id-stok');
                const cluster = this.getAttribute('data-nama-cluster');
                const noModel = this.getAttribute('data-no-model');
                const itemType = this.getAttribute('data-item-type');
                const kodeWarna = this.getAttribute('data-kode-warna');
                document.getElementById('idStok').value = idStok;
                // Reset form
                // document.getElementById('usageForm').reset();

                // Fetch data
                fetch(`<?= base_url('/gbn/pemasukan/getDataByCluster') ?>?no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}&cluster=${encodeURIComponent(cluster)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                        console.log(response.json());
                    })
                    .then(data => {
                        let content = '';
                        // Reset konten modal agar tidak terjadi penumpukan data
                        document.getElementById('formPengeluaran').innerHTML = '';
                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                renderModalContent(item);
                            });
                        } // Jika data berupa objek dan tidak kosong
                        else if (typeof data === 'object' && data !== null && Object.keys(data).length > 0) {
                            renderModalContent(data);
                        }
                        // Jika data kosong
                        else {
                            document.getElementById('formPengeluaran').innerHTML = '';
                            document.getElementById('modalContent').innerHTML = `
                                <div class="col-12">
                                    <div class="alert alert-warning">Data tidak ditemukan.</div>
                                </div>
                            `;
                        }

                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('dataModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data. Silakan coba lagi.',
                            confirmButtonColor: '#082653'
                        });
                    });
            });
        });

        $(document).on('change', '.form-check-input[type="checkbox"]', function() {
            const id = $(this).val();
            const enabled = this.checked;

            // toggle disabled prop pada ketiga input
            $(`#kgs_out_${id},
     #cns_out_${id},
     #keterangan_${id}`)
                .prop('disabled', !enabled);
        });

        // Inisialisasi saat page load (opsional jika ada pre-checked)
        $('.form-check-input[type="checkbox"]').trigger('change');
        // Function to render modal content
        function renderModalContent(item) {
            // Buat konten untuk satu item
            const formPengeluaran = `
            <div class="card mb-2">
                <div class="card-body">
                    <div class="row">
                        <!-- Kiri: detail label -->
                        <div class="col-md-6">
                            <h5><strong>Pengeluaran Per Karung</strong></h5>
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="id_pemasukan[]"
                                    value="${item.id_pemasukan}"
                                    id="pemasukan_${item.id_pemasukan}">
                                <label class="form-check-label" for="pemasukan_${item.id_pemasukan}">
                                    <strong>No Karung:</strong> ${item.no_karung}<br>
                                    <strong>Tanggal Masuk:</strong> ${item.tgl_masuk}<br>
                                    <strong>Cluster:</strong> ${item.nama_cluster}<br>
                                    <strong>PDK:</strong> ${item.no_model}<br>
                                    <strong>Item Type:</strong> ${item.item_type}<br>
                                    <strong>Kode Warna:</strong> ${item.kode_warna}<br>
                                    <strong>Warna:</strong> ${item.warna}<br>
                                    <strong>Lot Celup:</strong> ${item.lot_kirim}<br>
                                    <strong>Total Kg:</strong> ${item.kgs_kirim} KG<br>
                                    <strong>Total Cones:</strong> ${item.cones_kirim} CNS
                                </label>
                            </div>
                        </div>

                        <!-- input manual -->
                        <div class="col-md-6">
                            <h5><strong>Pengeluaran Per Kones</strong></h5>
                            <div class="row gx-2">
                                <div class="col-6">
                                    <label for="kgs_out_${item.id_pemasukan}" class="form-label small mb-1">Kg Out Manual</label>
                                    <input type="number"
                                        step="0.01"
                                        class="form-control form-control-sm"
                                        name="kgs_out[${item.id_pemasukan}]"
                                        id="kgs_out_${item.id_pemasukan}"
                                        placeholder="Kg"
                                        disabled>
                                </div>
                                <div class="col-6">
                                    <label for="cns_out_${item.id_pemasukan}" class="form-label small mb-1">Cones Out Manual</label>
                                    <input type="number"
                                        step="1"
                                        class="form-control form-control-sm"
                                        name="cns_out[${item.id_pemasukan}]"
                                        id="cns_out_${item.id_pemasukan}"
                                        placeholder="CNS"
                                        disabled>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label for="keterangan_${item.id_pemasukan}" class="form-label small">Keterangan</label>
                                <textarea class="form-control form-control-sm"
                                    name="keterangan[${item.id_pemasukan}]"
                                    id="keterangan_${item.id_pemasukan}"
                                    rows="3"
                                    placeholder="Masukkan keterangan…"
                                    disabled></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            // Tambahkan konten item ke dalam container
            document.getElementById('formPengeluaran').innerHTML += formPengeluaran;

            // document.getElementById('modalContent').innerHTML = content;

            window.currentItemType = item.item_type;
            window.currentKodeWarna = item.kode_warna;
        }

        // Form submission
        document.getElementById('usageForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submission tradisional

            const idStok = document.getElementById('idStok').value;
            const qtyKGS = document.getElementById('qtyKGS').value;
            const qtyCNS = document.getElementById('qtyCNS').value;
            const qtyKarung = document.querySelectorAll('input[name="id_pemasukan[]"]:checked').length;
            const noModel = document.getElementById('noModel').value;
            const namaCluster = document.getElementById('namaCluster').value;
            const idOutCelup = document.getElementById('idOutCelup').value;
            const lotFinal = document.getElementById('lotFinal').value;
            // get from url ?area=
            // console.log(area);
            const area = new URLSearchParams(window.location.search).get('Area');
            const KgsPesan = new URLSearchParams(window.location.search).get('KgsPesan');
            const CnsPesan = new URLSearchParams(window.location.search).get('CnsPesan');

            const PinjamOrder = document.getElementById('pinjam_order').value;

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';

            // Kirim data menggunakan fetch ke controller saveUsage
            fetch('<?= base_url('gbn/savePengeluaranJalur') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        idStok: idStok,
                        qtyKGS: qtyKGS,
                        qtyCNS: qtyCNS,
                        qtyKarung: qtyKarung,
                        noModel: noModel,
                        namaCluster: namaCluster,
                        idOutCelup: idOutCelup,
                        lotFinal: lotFinal,
                        area: area,
                        KgsPesan: KgsPesan,
                        CnsPesan: CnsPesan
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('dataModal')).hide();

                    // Tampilkan pesan sesuai dengan session flash data dari controller
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            confirmButtonColor: '#082653'
                        }).then(() => {
                            // Opsional: reload halaman atau redirect jika perlu
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#082653'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan data. Silakan coba lagi.',
                        confirmButtonColor: '#082653'
                    });
                });
        });

        // pinjam order
        const pinjamCards = document.querySelectorAll('.card-pinjam-order');
        pinjamCards.forEach(card => {
            card.addEventListener('click', function() {
                const itemType = this.getAttribute('data-item-type');
                const kodeWarna = this.getAttribute('data-kode-warna');
                const noModel = "<?= $noModel; ?>";

                console.log('Klik Pinjam Order:', itemType, kodeWarna);

                // Fetch data Pinjam Order
                fetch(`<?= base_url('/gbn/pinjamOrder/getNoModel') ?>?no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // console.log(data);
                        // Reset konten modal agar tidak terjadi penumpukan data
                        document.getElementById('formPinjamOrder').innerHTML = '';

                        // Siapkan variable untuk option no_model
                        let options = `<option value="">-- Pilih No Model --</option>`;

                        if (Array.isArray(data) && data.length > 0) {
                            data.forEach(item => {
                                // Tambahkan setiap no_model ke option
                                options += `
                                    <option value="${item.no_model}"
                                    data-item_type="${item.item_type}" 
                                    data-kode_warna="${item.kode_warna}">
                                        ${item.no_model} | ${item.item_type} | ${item.kode_warna} | ${item.warna}
                                    </option>
                                `;
                            });
                        }

                        // Masukkan select ke formPinjamOrder
                        document.getElementById('formPinjamOrder').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="noModelSelect" class="form-label">Pilih No Model</label>
                                    <select id="noModelSelect" name="no_model" class="form-select mb-3">
                                        ${options}
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="clusterSelect" class="form-label">Pilih Cluster</label>
                                    <select id="clusterSelect" name="nama_cluster" class="form-select mb-3">
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="formDetailPinjamOrder">
                            </div>
                        `;

                        // Aktifkan Select2
                        $('#noModelSelect').select2({
                            placeholder: "Pilih No Model",
                            width: '100%',
                            dropdownParent: $('#pinjamOrderModal') // biar dropdown tetap dalam modal
                        });

                        // ambil data cluster
                        $('#noModelSelect').on('change', function() {
                            const noModel = $(this).val();

                            if (!noModel) {
                                $('#clusterSelect').html('<option value="">-- Pilih Cluster --</option>').trigger('change');
                                return;
                            }
                            const selectedOption = $(this).find('option:selected');
                            const itemTypePinjam = selectedOption.data('item_type');
                            const kodeWarnaPinjam = selectedOption.data('kode_warna');

                            // AJAX untuk ambil cluster
                            fetch(`<?= base_url('/gbn/pinjamOrder/getCluster') ?>?no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemTypePinjam)}&kode_warna=${encodeURIComponent(kodeWarnaPinjam)}`)
                                .then(response => response.json())
                                .then(clusters => {
                                    let clusterOptions = '<option value="">-- Pilih Cluster --</option>';
                                    if (Array.isArray(clusters) && clusters.length > 0) {
                                        clusters.forEach(cluster => {
                                            clusterOptions += `<option value="${cluster.nama_cluster}">${cluster.nama_cluster}</option>`;
                                        });
                                    }
                                    $('#clusterSelect').html(clusterOptions).trigger('change');
                                })
                                .catch(error => {
                                    console.error('Error fetching clusters:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Gagal mengambil data cluster.'
                                    });
                                });
                        });
                        // ambil data cluster
                        function loadDetailPinjamOrder() {
                            const noModel = $('#noModelSelect').val();
                            const cluster = $('#clusterSelect').val();
                            // Ambil ulang item_type dan kode_warna dari selected option
                            const selectedOption = $('#noModelSelect option:selected');
                            const itemTypePinjam = selectedOption.data('item_type');
                            const kodeWarnaPinjam = selectedOption.data('kode_warna');

                            // Kalau salah satu kosong, clear detail
                            if (!noModel || !cluster) {
                                document.getElementById('formDetailPinjamOrder').innerHTML = '';
                                return;
                            }

                            fetch(`<?= base_url('/gbn/pemasukan/getDataByCluster') ?>?no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemTypePinjam)}&kode_warna=${encodeURIComponent(kodeWarnaPinjam)}&cluster=${encodeURIComponent(cluster)}`)
                                .then(response => response.json())
                                .then(data => {
                                    document.getElementById('formDetailPinjamOrder').innerHTML = '';

                                    if (Array.isArray(data) && data.length > 0) {
                                        data.forEach(item => {
                                            const formPengeluaran = `
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Kiri: detail label -->
                                                        <div class="col-md-6">
                                                            <h5><strong>Pengeluaran Per Karung</strong></h5>
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="checkbox"
                                                                    name="id_pemasukan[]"
                                                                    value="${item.id_pemasukan}"
                                                                    id="pemasukan_${item.id_pemasukan}">
                                                                <label class="form-check-label" for="pemasukan_${item.id_pemasukan}">
                                                                    <strong>No Karung:</strong> ${item.no_karung}<br>
                                                                    <strong>Tanggal Masuk:</strong> ${item.tgl_masuk}<br>
                                                                    <strong>Cluster:</strong> ${item.nama_cluster}<br>
                                                                    <strong>PDK:</strong> ${item.no_model}<br>
                                                                    <strong>Item Type:</strong> ${item.item_type}<br>
                                                                    <strong>Kode Warna:</strong> ${item.kode_warna}<br>
                                                                    <strong>Warna:</strong> ${item.warna}<br>
                                                                    <strong>Lot Celup:</strong> ${item.lot_kirim}<br>
                                                                    <strong>Total Kg:</strong> ${item.kgs_kirim} KG<br>
                                                                    <strong>Total Cones:</strong> ${item.cones_kirim} CNS
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Kanan: input manual -->
                                                        <div class="col-md-6">
                                                            <h5><strong>Pengeluaran Per Kones</strong></h5>
                                                            <div class="row gx-2">
                                                                <div class="col-6">
                                                                    <label for="kgs_out_${item.id_pemasukan}" class="form-label small mb-1">Kg Out Manual</label>
                                                                    <input type="number"
                                                                        step="0.01"
                                                                        class="form-control form-control-sm"
                                                                        name="kgs_out[${item.id_pemasukan}]"
                                                                        id="kgs_out_${item.id_pemasukan}"
                                                                        placeholder="Kg"
                                                                        disabled>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label for="cns_out_${item.id_pemasukan}" class="form-label small mb-1">Cones Out Manual</label>
                                                                    <input type="number"
                                                                        step="1"
                                                                        class="form-control form-control-sm"
                                                                        name="cns_out[${item.id_pemasukan}]"
                                                                        id="cns_out_${item.id_pemasukan}"
                                                                        placeholder="CNS"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <label for="keterangan_${item.id_pemasukan}" class="form-label small">Keterangan</label>
                                                                <textarea class="form-control form-control-sm"
                                                                    name="keterangan[${item.id_pemasukan}]"
                                                                    id="keterangan_${item.id_pemasukan}"
                                                                    rows="3"
                                                                    placeholder="Masukkan keterangan…"
                                                                    disabled></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            `;
                                            document.getElementById('formDetailPinjamOrder').innerHTML += formPengeluaran;
                                        });
                                    } else {
                                        document.getElementById('formDetailPinjamOrder').innerHTML = `
                                            <div class="alert alert-warning">Data stock tidak ditemukan.</div>
                                            `;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching detail pemasukan:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Gagal mengambil data pemasukan.'
                                    });
                                });
                        }

                        $('#clusterSelect').on('change', loadDetailPinjamOrder);
                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('pinjamOrderModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data Pinjam Order. Silakan coba lagi.',
                            confirmButtonColor: '#082653'
                        });
                    });
            });
        });
    });
</script>

<?php $this->endSection(); ?>