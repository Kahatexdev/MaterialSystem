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

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Data Stock <?= $noModel; ?></h3>
            <span class="badge bg-gradient-info"><?= date('d F Y'); ?></span>
        </div>
    </div>

    <div class="card-container">
        <?php if (!empty($cluster)): ?>
            <?php foreach ($cluster as $item): ?>
                <div class="stock-card" data-id-stok="<?= esc($item['id_stock']); ?>">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-warehouse me-2 text-white"></i>
                            Cluster <?= esc($item['nama_cluster']); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    Cluster <i class="fas fa-inbox text-white"></i>
                </div>
                <p class="empty-state-text">Data pemasukan tidak ditemukan.</p>
            </div>
        <?php endif; ?>
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
                <div class="info-section">
                    <h6 class="info-section-title">Informasi Stock</h6>
                    <div class="row" id="modalContent">
                        <!-- Detail data will be loaded here -->
                    </div>
                </div>

                <div class="form-section">
                    <h6 class="form-section-title">Input Pengeluaran Stock</h6>
                    <form id="usageForm" method="post">
                        <input type="hidden" id="idStok" name="idStok">
                        <input type="hidden" id="noModel" name="noModel" value="<?= $noModel; ?>">
                        <input type="hidden" id="namaCluster" name="namaCluster" value="<?= $item['nama_cluster']; ?>">
                        <input type="hidden" id="lotFinal" name="lotFinal" value="<?= $item['lot_final']; ?>">
                        <div class="row">
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
                        </div>
                    </form>
                </div>
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
                document.getElementById('idStok').value = idStok;


                // Reset form
                document.getElementById('usageForm').reset();

                // Fetch data
                fetch(`<?= base_url(/gbn/pemasukan/getDataByIdStok/${idStok})?>`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        let content = '';

                        if (Array.isArray(data) && data.length > 0) {
                            let item = data[0]; // Get first item if in array
                            renderModalContent(item);
                        } else if (typeof data === 'object' && data !== null) {
                            renderModalContent(data);
                        } else {
                            document.getElementById('modalContent').innerHTML =
                                '<div class="col-12"><div class="alert alert-warning">Data tidak ditemukan.</div></div>';
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

        // Function to render modal content
        // Function to render modal content
        function renderModalContent(item) {
            // Ambil nilai dari URL parameter
            const KgsPesan = new URLSearchParams(window.location.search).get('KgsPesan') || '-';
            const CnsPesan = new URLSearchParams(window.location.search).get('CnsPesan') || '-';

            const content = `
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>PDK: <strong>${item.no_model || '-'}</strong></span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>Item Type: <strong>${item.item_type || '-'}</strong></span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>Kode: <strong>${item.kode_warna || '-'}</strong></span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>Warna: <strong>${item.warna || '-'}</strong></span>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>Stock: <strong>${item.total_kgs || '-'} KG </strong><br>
                <small>KG Pesan: <strong>${KgsPesan} KG</strong></small></span>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-badge">
                <span>Stock Cones: <strong>${item.total_cns || '-'} Cns </strong><br>
                <small>Cones Pesan: <strong>${CnsPesan} Cns</strong></small></span>
            </div>
        </div>
        <input type="hidden" id="idOutCelup" value="${item.id_out_celup}">
    `;

            document.getElementById('modalContent').innerHTML = content;
        }


        // Form submission
        document.getElementById('usageForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submission tradisional

            const idStok = document.getElementById('idStok').value;
            const qtyKGS = document.getElementById('qtyKGS').value;
            const qtyCNS = document.getElementById('qtyCNS').value;
            const qtyKarung = document.getElementById('qtyKarung').value;
            const noModel = document.getElementById('noModel').value;
            const namaCluster = document.getElementById('namaCluster').value;
            const idOutCelup = document.getElementById('idOutCelup').value;
            const lotFinal = document.getElementById('lotFinal').value;
            // get from url ?area=
            // console.log(area);
            const area = new URLSearchParams(window.location.search).get('Area');
            const KgsPesan = new URLSearchParams(window.location.search).get('KgsPesan');
            const CnsPesan = new URLSearchParams(window.location.search).get('CnsPesan');

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
    });
</script>

<?php $this->endSection(); ?>