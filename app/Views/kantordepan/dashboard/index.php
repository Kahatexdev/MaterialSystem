<?php $this->extend($role . '/dashboard/header'); ?>
<?php $this->section('content'); ?>

<div class="container-fluid px-4 py-4">
    <!-- Modern Header -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h2 class="mb-1 fw-bold">Katalog Laporan</h2>
                <p class="text-muted mb-0">Akses cepat ke semua report sistem material</p>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-dark px-3 py-2">Kantor Depan</span>
            </div>
        </div>

        <!-- Modern Search Bar -->
        <div class="search-wrapper">
            <i class="ni ni-zoom-split-in search-icon"></i>
            <input
                id="filterReport"
                type="text"
                class="form-control search-input"
                placeholder="Cari laporan...">
        </div>
    </div>

    <?php
    $reports = [
        [
            'key'   => 'pengaduan harian komplien',
            'title' => 'Pengaduan Harian',
            'desc'  => 'Ringkasan pengaduan harian dengan status dan SLA respon',
            'icon'  => 'ni ni-chat-round',
            'color' => 'primary',
            'url'   => base_url('pengaduan/report/harian')
        ],
        [
            'key'   => 'pengaduan bulanan komplien',
            'title' => 'Pengaduan Bulanan',
            'desc'  => 'Tren bulanan per bagian dan kategori',
            'icon'  => 'ni ni-time-alarm',
            'color' => 'info',
            'url'   => base_url('pengaduan/report/bulanan')
        ],
        [
            'key'   => 'pemasukan gudang bahan masuk',
            'title' => 'Pemasukan Material',
            'desc'  => 'Detail pemasukan per area, cluster, dan lot',
            'icon'  => 'ni ni-box-2',
            'color' => 'success',
            'url'   => base_url('report/pemasukan')
        ],
        [
            'key'   => 'pengeluaran kirim area timeline',
            'title' => 'Pengeluaran Area',
            'desc'  => 'Rekap pengiriman per area dan timeline',
            'icon'  => 'ni ni-send',
            'color' => 'warning',
            'url'   => base_url('report/pengeluaran')
        ],
        [
            'key'   => 'stok gudang sisa stock posisi',
            'title' => 'Posisi Stok',
            'desc'  => 'Stok by item, warna, cluster, dan aging',
            'icon'  => 'ni ni-archive-2',
            'color' => 'dark',
            'url'   => base_url('report/stock')
        ],
        [
            'key'   => 'retur barang kembali',
            'title' => 'Retur Material',
            'desc'  => 'Detail retur dengan kategori dan tindak lanjut',
            'icon'  => 'ni ni-curved-next',
            'color' => 'danger',
            'url'   => base_url('report/retur')
        ],
        [
            'key'   => 'po gabungan nylon pck spandex',
            'title' => 'PO Gabungan',
            'desc'  => 'Ringkasan PO per model dan periode',
            'icon'  => 'ni ni-bag-17',
            'color' => 'primary',
            'url'   => base_url('report/po-gabungan')
        ],
        [
            'key'   => 'kapasitas cluster covering celup',
            'title' => 'Kapasitas Cluster',
            'desc'  => 'Utilisasi dan bottleneck per cluster',
            'icon'  => 'ni ni-chart-pie-35',
            'color' => 'info',
            'url'   => base_url('report/kapasitas-cluster')
        ],
        [
            'key'   => 'model item type kode warna',
            'title' => 'Report per Model',
            'desc'  => 'Semua aktivitas terintegrasi per model',
            'icon'  => 'ni ni-collection',
            'color' => 'success',
            'url'   => base_url('report/by-model')
        ],
        [
            'key'   => 'rekap harian dashboard ringkas',
            'title' => 'Rekap Harian',
            'desc'  => 'Snapshot harian lintas modul',
            'icon'  => 'ni ni-notification-70',
            'color' => 'warning',
            'url'   => base_url('report/rekap-harian')
        ],
    ];
    ?>

    <!-- Modern Grid -->
    <div class="row g-4" id="reportGrid">
        <?php foreach ($reports as $r): ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 report-item" data-key="<?= esc($r['key']) ?>">
                <div class="report-card">
                    <div class="report-icon bg-gradient-<?= esc($r['color']) ?>">
                        <i class="<?= esc($r['icon']) ?>"></i>
                    </div>
                    <h6 class="report-title"><?= esc($r['title']) ?></h6>
                    <p class="report-desc"><?= esc($r['desc']) ?></p>
                    <a href="<?= esc($r['url']) ?>" class="report-link">
                        Lihat Laporan
                        <i class="ni ni-bold-right ms-1"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="text-center py-5" style="display: none;">
        <i class="ni ni-folder-17 text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3">Tidak ada laporan yang cocok</p>
    </div>
</div>

<style>
    /* Modern Search Bar */
    .search-wrapper {
        position: relative;
        max-width: 600px;
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #8392ab;
        font-size: 1.1rem;
        z-index: 1;
    }

    .search-input {
        padding: 14px 20px 14px 50px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .search-input:focus {
        border-color: #344767;
        box-shadow: 0 0 0 4px rgba(52, 71, 103, 0.08);
        outline: none;
    }

    /* Modern Report Cards */
    .report-card {
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f0f2f5;
        position: relative;
        overflow: hidden;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        border-color: #e0e0e0;
    }

    .report-card:hover::before {
        opacity: 0.6;
    }

    .report-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .report-icon i {
        font-size: 1.5rem;
        color: #fff;
    }

    .report-title {
        font-size: 17px;
        font-weight: 600;
        color: #344767;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .report-desc {
        font-size: 14px;
        color: #67748e;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .report-link {
        color: #344767;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        margin-top: auto;
    }

    .report-link:hover {
        color: #000;
        transform: translateX(4px);
    }

    .report-link i {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .report-link:hover i {
        transform: translateX(3px);
    }

    /* Responsive */
    @media (max-width: 576px) {
        .search-input {
            padding: 12px 16px 12px 44px;
            font-size: 14px;
        }

        .report-card {
            padding: 24px;
        }

        .report-icon {
            width: 48px;
            height: 48px;
        }

        .report-icon i {
            font-size: 1.3rem;
        }
    }

    /* Fade animation for filtered items */
    .report-item {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    (function() {
        const input = document.getElementById('filterReport');
        const items = document.querySelectorAll('#reportGrid .report-item');
        const noResults = document.getElementById('noResults');

        input?.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            let visibleCount = 0;

            items.forEach(it => {
                const key = (it.dataset.key || '').toLowerCase();
                const isVisible = !q || key.includes(q);
                it.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    })();
</script>

<?php $this->endSection(); ?>