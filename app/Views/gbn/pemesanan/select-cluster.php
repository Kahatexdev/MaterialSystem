<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>

<!-- (Opsional) SweetAlert2 & Select2 jika belum ada di header -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

<style>
    /* ---------- BASE & THEME ---------- */
    .container-fluid {
        padding: clamp(0.75rem, 2vw, 1.5rem);
    }

    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        /* responsive card min-width */
        gap: clamp(0.75rem, 1.8vw, 1.25rem);
        margin-top: 1.5rem;
    }

    .stock-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        overflow: hidden;
        height: 100%;
        background: #fff;
    }

    .stock-card:hover {
        transform: translateY(-4px);
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
        padding: clamp(.85rem, 1.6vw, 1.25rem);
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
        color: #fff;
        font-weight: 600;
        padding: clamp(.85rem, 1.6vw, 1.25rem);
        background: linear-gradient(45deg, #082653, rgb(20, 74, 155));
    }

    .stock-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        flex-wrap: wrap;
        gap: .25rem .5rem;
    }

    .stock-info .label {
        font-weight: 500;
        color: #495057;
    }

    .stock-info .value {
        font-weight: 600;
        color: #212529;
    }

    .divider {
        height: 1px;
        background-color: #e9ecef;
        margin: 0.75rem 0;
    }

    /* ---------- MODAL ---------- */
    .modal-header {
        background-color: #082653;
        color: white;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-header .modal-title {
        line-height: 1.3;
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

    /* ---------- FORM & BUTTON ---------- */
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

    .btn-submit,
    .btn,
    .custom-button {
        padding: clamp(.45rem, .9vw, .6rem) clamp(.7rem, 1.4vw, 1.5rem);
        font-size: clamp(.8rem, 1.6vw, .95rem);
        border-radius: 6px;
    }

    .btn-submit {
        background-color: #082653;
        border-color: #082653;
        font-weight: 500;
    }

    .btn-submit:hover {
        background-color: #061c3e;
        border-color: #061c3e;
    }

    .custom-button {
        background-color: #061c3e;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
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

    /* ---------- EMPTY STATE ---------- */
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

    /* ---------- TYPOGRAPHY ADAPTIVE ---------- */
    h3,
    .fs-hero {
        font-size: clamp(1.1rem, 2.2vw + .5rem, 1.5rem);
    }

    .badge.fs-6 {
        font-size: clamp(.75rem, 1.6vw, .95rem);
    }

    /* ---------- GRID BREAKPOINT TWEAKS ---------- */
    @media (min-width: 576px) {

        #formPengeluaran .col-6,
        #formDetailPinjamOrder .col-6 {
            width: 50%;
        }
    }

    @media (hover: hover) {
        .stock-card:hover {
            transform: translateY(-4px);
        }
    }

    @media (max-width: 576px) {
        .empty-state {
            padding: 2rem;
        }

        .btn-round {
            width: 40px;
            height: 40px;
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
                <h3 class="fw-bold mb-0"><?= $noModel . ' - ' . $itemType . ' - ' . $kodeWarna . ' - ' . $color . ' - ' . $lot; ?></h3>
                <span class="badge bg-gradient-blue fs-6 shadow-sm px-3 py-2"><?= date('d F Y', strtotime($tglPakai)); ?></span>
            </div>

            <!-- Sub Title -->
            <h5 class="text-bold fw-semibold mb-3">📦 Informasi Pemesanan</h5>

            <!-- Grid Cards (2 kolom di hp bila mau: col-6 col-md) -->
            <div class="row g-2">
                <div class="col-6 col-md">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Kg Pesan</h6>
                        <h4 class="fw-semibold mb-0"><?= number_format($KgsPesan, 2) ?> Kg</h4>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Cns Pesan</h6>
                        <h4 class="fw-semibold mb-0"><?= $CnsPesan ?> Cns</h4>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Kg Persiapan</h6>
                        <h4 class="fw-semibold mb-0"><?= $kgPersiapan ? number_format($kgPersiapan, 2) : '0' ?> Kg</h4>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Kg Pengiriman</h6>
                        <h4 class="fw-semibold mb-0"><?= $kgPengiriman ? number_format($kgPengiriman, 2) : '0' ?> Kg</h4>
                    </div>
                </div>
                <div class="col-6 col-md">
                    <div class="p-2 border rounded-3 bg-gradient-light shadow-sm text-center">
                        <h6 class="text-muted mb-1 small">Sisa Kebutuhan</h6>
                        <h4 class="fw-semibold mb-0"><?= $sisaKebutuhan ? number_format($sisaKebutuhan, 2) : '0' ?> Kg</h4>
                    </div>
                </div>
            </div>

            <!-- Button buka modal keterangan -->
            <div class="row g-2">
                <div class="col-12 mt-4">
                    <button type="button" class="btn w-100 p-2 border rounded-3 bg-gradient-light shadow-sm text-center"
                        data-bs-toggle="modal" data-bs-target="#keteranganModal">
                        <h4 class="fw-semibold mb-0">Keterangan Pemesanan</h4>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div class="card-container">
        <?php if (!empty($cluster)): ?>
            <?php foreach ($cluster as $item): ?>
                <div class="stock-card"
                    data-id-stok="<?= esc($item['id_stock'], 'attr'); ?>"
                    data-nama-cluster="<?= esc($item['nama_cluster'], 'attr'); ?>"
                    data-no-model="<?= esc($item['no_model'], 'attr'); ?>"
                    data-item-type="<?= esc($item['item_type'], 'attr'); ?>"
                    data-kode-warna="<?= esc($item['kode_warna'], 'attr'); ?>">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-warehouse me-2 text-white"></i>
                            Cluster <?= esc($item['nama_cluster']); ?>
                            (<?= number_format($item['total_kgs'], 2) . ' Kg / ' . $item['total_krg'] . ' Krg / ' . $item['lot_final']; ?>)
                        </span>
                    </div>
                    <!-- (opsional) bisa tambahkan ringkasan di body -->
                    <!-- <div class="card-body"> ... </div> -->
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="bg-gradient-danger w-100 empty-state">
                <h6 class="text-white">Stock Kosong <?= $noModel; ?>.</h6>
            </div>
        <?php endif; ?>

        <!-- Kartu pintasan Pinjam Order -->
        <div class="card-pinjam-order" data-item-type="<?= $itemType ?>" data-kode-warna="<?= $kodeWarna ?>">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: transparent; border: 0;">
                <span class="d-flex align-items-center">
                    <i class="fas fa-warehouse me-2 text-white"></i>
                    Pinjam Order
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Keterangan Pemesanan (fullscreen di mobile) -->
<div class="modal fade" id="keteranganModal" tabindex="-1" aria-labelledby="keteranganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100">
                    <h5 class="modal-title text-white mb-2" id="keteranganModalLabel">Input Keterangan</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formKeterangan">
                    <div id="formKeteranganContent">
                        <div class="mb-3">
                            <label for="keterangan_gbn" class="form-label">Keterangan</label>
                            <textarea name="keterangan_gbn" id="keterangan_gbn" class="form-control" rows="4" placeholder="Tulis keterangan di sini..."><?= $ketGbn ?></textarea>
                        </div>
                        <input type="hidden" name="id_total_pemesanan" value="<?= $id ?>">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check-circle me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Detail Data Stock (fullscreen di mobile) -->
<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="dataModalLabel">Detail Data Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-section">
                    <h4 class="form-section-title"><strong>Pengeluaran Stock</strong></h4>
                    <form id="pengeluaran" method="post" action="<?= base_url('gbn/simpanPengeluaranJalur/' . $id . '?Area=' . $area . '&KgsPesan=' . $KgsPesan . '&CnsPesan=' . $CnsPesan . '&pinjam='); ?>">
                        <div class="row" id="formPengeluaran"><!-- rendered by JS --></div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary btn-submit">
                                <i class="bi bi-check-circle me-1"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
                <div class="divider"></div>

                <form id="usageForm" method="post">
                    <input type="hidden" id="idStok" name="idStok">
                    <input type="hidden" id="noModel" name="noModel" value="<?= $noModel; ?>">
                    <input type="hidden" id="namaCluster" name="namaCluster" value="<?= $item['nama_cluster'] ?? NULL ?>">
                    <input type="hidden" id="lotFinal" name="lotFinal" value="<?= $item['lot_final'] ?? NULL ?>">
                    <!-- input kuantitas manual dipindah ke setiap kartu item (renderModalContent) -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pinjam Order (fullscreen di mobile) -->
<div class="modal fade" id="pinjamOrderModal" tabindex="-1" aria-labelledby="modelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100">
                    <h5 class="modal-title text-white mb-2">Pinjam Order</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pinjamOrder" method="post" action="<?= base_url('gbn/simpanPengeluaranJalur/' . $id . '?Area=' . $area . '&KgsPesan=' . $KgsPesan . '&CnsPesan=' . $CnsPesan . '&pinjam=YA'); ?>">
                    <div id="formPinjamOrder"><!-- rendered by JS --></div>
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

<!-- (Opsional) Scripts jika belum ada di header -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simpan Keterangan via AJAX
        $('#formKeterangan').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: '<?= base_url($role . '/pemesanan/saveKetGbnInPemesanan') ?>',
                method: 'POST',
                data: formData,
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Keterangan berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false,
                        willClose: () => location.reload()
                    });
                    $('#keteranganModal').modal('hide');
                    $('#formKeterangan')[0].reset();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan keterangan.'
                    });
                    console.error(xhr.responseText);
                }
            });
        });

        // Klik kartu stock -> buka detail
        document.querySelectorAll('.stock-card').forEach(card => {
            card.addEventListener('click', function() {
                const idStok = this.getAttribute('data-id-stok');
                const cluster = this.getAttribute('data-nama-cluster');
                const noModel = this.getAttribute('data-no-model');
                const itemType = this.getAttribute('data-item-type');
                const kodeWarna = this.getAttribute('data-kode-warna');

                document.getElementById('idStok').value = idStok;

                fetch(`<?= base_url('/gbn/pemasukan/getDataByCluster') ?>?id_stok=${idStok}&no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}&cluster=${encodeURIComponent(cluster)}`)
                    .then(resp => {
                        if (!resp.ok) throw new Error('Network error');
                        return resp.json();
                    })
                    .then(data => {
                        const container = document.getElementById('formPengeluaran');
                        container.innerHTML = '';

                        const render = (it) => renderModalContent(it, container);
                        if (Array.isArray(data) && data.length) data.forEach(render);
                        else if (data && typeof data === 'object') render(data);
                        else container.innerHTML = '<div class="col-12"><div class="alert alert-warning">Data tidak ditemukan.</div></div>';

                        new bootstrap.Modal(document.getElementById('dataModal')).show();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data. Silakan coba lagi.',
                            confirmButtonColor: '#082653'
                        });
                    });
            });
        });

        // Toggle input enabled saat centang
        $(document).on('change', '.form-check-input[type="checkbox"]', function() {
            const id = $(this).val();
            $(`#kgs_out_${id}, #cns_out_${id}, #keterangan_${id}`).prop('disabled', !this.checked);

            // ambil elemen keterangan pinjam (type text)
            const inputKet = $(`#keterangan_pinjam_${id}`);
            const sel = $('#noModelSelect option:selected');

            if (this.checked) {
                // ambil data model
                const nm = $('#noModelSelect').val();
                const itemTypePinjam = sel.data('item_type');
                const kodeWarnaPinjam = sel.data('kode_warna');
                const warnaPinjam = sel.data('warna');

                // isi value default
                inputKet.val(`pinjam dari ${nm} / ${itemTypePinjam} / ${kodeWarnaPinjam} / ${warnaPinjam}`);
            } else {
                // kosongkan jika di-uncheck
                inputKet.val('');
            }
        });

        // Submit usageForm (payload ringkas)
        document.getElementById('usageForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const idStok = document.getElementById('idStok').value;
            const qtyKarung = document.querySelectorAll('input[name="id_pemasukan[]"]:checked').length;
            const noModel = document.getElementById('noModel').value;
            const namaCluster = document.getElementById('namaCluster').value;
            const lotFinal = document.getElementById('lotFinal').value;

            const url = new URL(window.location.href);
            const area = url.searchParams.get('Area');
            const KgsPesan = url.searchParams.get('KgsPesan');
            const CnsPesan = url.searchParams.get('CnsPesan');

            const submitBtn = this.querySelector('button[type="submit"]') || document.createElement('button');
            const originalBtnText = submitBtn.innerHTML;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...';
            }

            fetch('<?= base_url('gbn/savePengeluaranJalur') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        idStok,
                        qtyKarung,
                        noModel,
                        namaCluster,
                        lotFinal,
                        area,
                        KgsPesan,
                        CnsPesan
                    })
                })
                .then(r => {
                    if (!r.ok) throw new Error('Network error');
                    return r.json();
                })
                .then(data => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                    bootstrap.Modal.getInstance(document.getElementById('dataModal')).hide();
                    if (data.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                confirmButtonColor: '#082653'
                            })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#082653'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menyimpan data. Silakan coba lagi.',
                        confirmButtonColor: '#082653'
                    });
                });
        });

        // Kartu Pinjam Order
        document.querySelectorAll('.card-pinjam-order').forEach(card => {
            card.addEventListener('click', function() {
                const itemType = this.getAttribute('data-item-type');
                const kodeWarna = this.getAttribute('data-kode-warna');
                const noModel = "<?= $noModel; ?>";

                fetch(`<?= base_url('/gbn/pinjamOrder/getNoModel') ?>?no_model=${encodeURIComponent(noModel)}&item_type=${encodeURIComponent(itemType)}&kode_warna=${encodeURIComponent(kodeWarna)}`)
                    .then(r => {
                        if (!r.ok) throw new Error('Network error');
                        return r.json();
                    })
                    .then(data => {
                        const wrap = document.getElementById('formPinjamOrder');
                        wrap.innerHTML = '';

                        let options = `<option value="">-- Pilih No Model --</option>`;
                        if (Array.isArray(data) && data.length) {
                            data.forEach(it => {
                                options += `
                                <option value="${it.no_model}" data-item_type="${it.item_type}" data-kode_warna="${it.kode_warna}" data-warna="${it.warna}">
                                    ${it.no_model} | ${it.item_type} | ${it.kode_warna} | ${it.warna}
                                </option>`;
                            });
                        }

                        wrap.innerHTML = `
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
                                    <option value="">-- Pilih Cluster --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="lotSelect" class="form-label">Pilih Lot</label>
                                <select id="lotSelect" name="lot" class="form-select mb-3">
                                    <option value="">-- Pilih Lot --</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" id="formDetailPinjamOrder"></div>
                    `;

                        // Init Select2 (dropdown di dalam modal)
                        $('#noModelSelect').select2({
                            placeholder: "Pilih No Model",
                            width: '100%',
                            dropdownParent: $('#pinjamOrderModal')
                        });

                        // On change model -> load clusters
                        $('#noModelSelect').on('change', function() {
                            const sel = $(this).find('option:selected');
                            const nm = $(this).val();
                            if (!nm) {
                                $('#clusterSelect').html('<option value="">-- Pilih Cluster --</option>');
                                return;
                            }

                            const itemTypePinjam = sel.data('item_type');
                            const kodeWarnaPinjam = sel.data('kode_warna');

                            fetch(`<?= base_url('/gbn/pinjamOrder/getCluster') ?>?no_model=${encodeURIComponent(nm)}&item_type=${encodeURIComponent(itemTypePinjam)}&kode_warna=${encodeURIComponent(kodeWarnaPinjam)}`)
                                .then(r => r.json())
                                .then(cl => {
                                    let opts = '<option value="">-- Pilih Cluster --</option>';
                                    if (Array.isArray(cl) && cl.length) cl.forEach(c => {
                                        opts += `<option value="${c.nama_cluster}">${c.nama_cluster}</option>`;
                                    });
                                    $('#clusterSelect').html(opts);
                                })
                                .catch(() => Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal mengambil data cluster.'
                                }));
                        });

                        // On change cluster -> load LOT
                        $('#clusterSelect').on('change', function() {
                            const cluster = $(this).val();
                            const sel = $('#noModelSelect').find('option:selected');
                            const nm = sel.val();
                            const itemTypePinjam = sel.data('item_type');
                            const kodeWarnaPinjam = sel.data('kode_warna');

                            if (!cluster) {
                                $('#lotSelect').html('<option value="">-- Pilih Lot --</option>');
                                return;
                            }

                            fetch(`<?= base_url('/gbn/pinjamOrder/getLot') ?>?no_model=${encodeURIComponent(nm)}&item_type=${encodeURIComponent(itemTypePinjam)}&kode_warna=${encodeURIComponent(kodeWarnaPinjam)}&cluster=${encodeURIComponent(cluster)}`)
                                .then(r => r.json())
                                .then(lots => {
                                    let opts = '<option value="">-- Pilih Lot --</option>';

                                    if (Array.isArray(lots) && lots.length) {

                                        // Untuk menghindari duplikat lot
                                        let lotList = [];

                                        lots.forEach(row => {
                                            if (row.lot_awal && !lotList.includes(row.lot_awal)) {
                                                lotList.push(row.lot_awal);
                                            }
                                            if (row.lot_stock && !lotList.includes(row.lot_stock)) {
                                                lotList.push(row.lot_stock);
                                            }
                                        });

                                        // Render ke option
                                        lotList.forEach(l => {
                                            opts += `<option value="${l}">${l}</option>`;
                                        });
                                    }

                                    $('#lotSelect').html(opts);
                                })
                                .catch(() => Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal mengambil data lot.'
                                }));
                        });

                        // Load detail per cluster
                        function loadDetailPinjamOrder() {
                            const nm = $('#noModelSelect').val();
                            const cl = $('#clusterSelect').val();
                            const lot = $('#lotSelect').val();
                            console.log('Lot:', lot);
                            const sel = $('#noModelSelect option:selected');
                            const itemTypePinjam = sel.data('item_type');
                            const kodeWarnaPinjam = sel.data('kode_warna');
                            const warnaPinjam = sel.data('warna');

                            const target = document.getElementById('formDetailPinjamOrder');
                            target.innerHTML = '';
                            if (!nm || !cl || !lot) return;
                            fetch(`<?= base_url('/gbn/pemasukan/getDataByLot') ?>?no_model=${encodeURIComponent(nm)}&item_type=${encodeURIComponent(itemTypePinjam)}&kode_warna=${encodeURIComponent(kodeWarnaPinjam)}&cluster=${encodeURIComponent(cl)}&lot=${encodeURIComponent(lot)}`)
                                .then(r => r.text())
                                .then(t => {
                                    console.log("RAW RESPONSE:", t);
                                    return JSON.parse(t); // karena kita sudah ambil raw
                                })
                                .then(d => {
                                    target.innerHTML = '';
                                    if (Array.isArray(d) && d.length) {
                                        d.forEach(it => {
                                            const html = `
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5><strong>Pinjam Per Karung</strong></h5>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="id_pemasukan[]" value="${it.id_pemasukan}" id="pemasukan_${it.id_pemasukan}">
                                                            <label class="form-check-label" for="pemasukan_${it.id_pemasukan}">
                                                                <strong>No Karung:</strong> ${it.no_karung}<br>
                                                                <strong>Tanggal Masuk:</strong> ${it.tgl_masuk}<br>
                                                                <strong>Cluster:</strong> ${it.nama_cluster}<br>
                                                                <strong>PDK:</strong> ${it.no_model}<br>
                                                                <strong>Item Type:</strong> ${it.item_type}<br>
                                                                <strong>Kode Warna:</strong> ${it.kode_warna}<br>
                                                                <strong>Warna:</strong> ${it.warna}<br>
                                                                <strong>Lot Celup:</strong> ${it.lot_kirim}<br>
                                                                <strong>Total Kg:</strong> ${it.kgs_kirim} KG<br>
                                                                <strong>Total Cones:</strong> ${it.cones_kirim} CNS
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5><strong>Pinjam Per Kones</strong></h5>
                                                        <div class="row gx-2">
                                                            <div class="col-6">
                                                                <label for="kgs_out_${it.id_pemasukan}" class="form-label small mb-1">Kg Out Manual</label>
                                                                <input type="number" step="0.01" max="${it.kgs_kirim}" min=0 class="form-control form-control-sm"
                                                                        name="kgs_out[${it.id_pemasukan}]" id="kgs_out_${it.id_pemasukan}" placeholder="Kg" disabled>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="cns_out_${it.id_pemasukan}" class="form-label small mb-1">Cones Out Manual</label>
                                                                <input type="number" step="1" max="${it.cones_kirim}" min=0 class="form-control form-control-sm"
                                                                        name="cns_out[${it.id_pemasukan}]" id="cns_out_${it.id_pemasukan}" placeholder="CNS" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="keterangan_${it.id_pemasukan}" class="form-label small">Keterangan Pengeluaran</label>
                                                            <textarea class="form-control form-control-sm" name="keterangan[${it.id_pemasukan}]"
                                                                        id="keterangan_${it.id_pemasukan}" rows="3" placeholder="Masukkan keterangan…" disabled></textarea>
                                                        </div>
                                                        <input type="hidden" name="keterangan_pinjam[${it.id_pemasukan}]" id="keterangan_pinjam_${it.id_pemasukan}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                                            target.insertAdjacentHTML('beforeend', html);
                                        });
                                    } else {
                                        target.innerHTML = '<div class="alert alert-warning">Data stock tidak ditemukan.</div>';
                                    }
                                })
                                .catch(() => Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal mengambil data pemasukan.'
                                }));
                        }

                        // Validasi input numeric
                        $(document).on('input', '[id^="kgs_out_"]', function() {
                            const max = parseFloat($(this).attr('max') || '0');
                            const val = parseFloat($(this).val() || '0');
                            if (val > max) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: `Kg tidak boleh lebih dari ${max} KG`
                                });
                                $(this).val(0);
                            }
                        });
                        $(document).on('input', '[id^="cns_out_"]', function() {
                            const max = parseInt($(this).attr('max') || '0');
                            const val = parseInt($(this).val() || '0');
                            if (val > max) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: `Cones tidak boleh lebih dari ${max} CNS`
                                });
                                $(this).val(0);
                            }
                        });

                        $('#lotSelect').on('change', loadDetailPinjamOrder);

                        new bootstrap.Modal(document.getElementById('pinjamOrderModal')).show();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data Pinjam Order. Silakan coba lagi.',
                            confirmButtonColor: '#082653'
                        });
                    });
            });
        });

        // Render 1 item ke modal data stock
        function renderModalContent(item, container) {
            const html = `
        <div class="card mb-2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Pengeluaran Per Karung</strong></h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="id_pemasukan[]" value="${item.id_pemasukan}" id="pemasukan_${item.id_pemasukan}">
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
                    <div class="col-md-6">
                        <h5><strong>Pengeluaran Per Kones</strong></h5>
                        <div class="row gx-2">
                            <div class="col-6">
                                <label for="kgs_out_${item.id_pemasukan}" class="form-label small mb-1">Kg Out Manual</label>
                                <input type="number" step="0.01" max="${item.kgs_kirim}" min=0 class="form-control form-control-sm"
                                       name="kgs_out[${item.id_pemasukan}]" id="kgs_out_${item.id_pemasukan}" placeholder="Kg" disabled>
                            </div>
                            <div class="col-6">
                                <label for="cns_out_${item.id_pemasukan}" class="form-label small mb-1">Cones Out Manual</label>
                                <input type="number" step="1" max="${item.cones_kirim}" min=0 class="form-control form-control-sm"
                                       name="cns_out[${item.id_pemasukan}]" id="cns_out_${item.id_pemasukan}" placeholder="CNS" disabled>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="keterangan_${item.id_pemasukan}" class="form-label small">Keterangan Pengeluaran</label>
                            <textarea class="form-control form-control-sm" name="keterangan[${item.id_pemasukan}]"
                                      id="keterangan_${item.id_pemasukan}" rows="3" placeholder="Masukkan keterangan…" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
            container.insertAdjacentHTML('beforeend', html);

            window.currentItemType = item.item_type;
            window.currentKodeWarna = item.kode_warna;
        }
    });
</script>

<?php $this->endSection(); ?>