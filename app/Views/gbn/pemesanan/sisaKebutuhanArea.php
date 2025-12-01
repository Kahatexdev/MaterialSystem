<?php $this->extend($role . '/pemesanan/header'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid py-4">
    <style>
        :root {
            --bg: #0f172a;
            --card: #111827;
            --muted: #64748b;
            --text: #e5e7eb;
            --accent: #22d3ee;
            --accent-strong: #06b6d4;
            --success: #22c55e;
            --danger: #ef4444;
            --ring: rgba(34, 211, 238, .35);
            --shadow: 0 10px 30px rgba(2, 8, 23, .45);
            --radius: 16px;
        }

        #loadingOverlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(2, 6, 23, .55);
            backdrop-filter: blur(6px);
        }

        #loadingOverlay.active {
            display: block;
        }

        .loader-wrap {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-card {
            width: 280px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .spin {
            width: 46px;
            height: 46px;
            margin: 0 auto 10px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, .25);
            border-top-color: var(--accent);
            animation: r .9s linear infinite;
        }

        @keyframes r {
            to {
                transform: rotate(360deg)
            }
        }

        .loader-text {
            color: var(--text);
            font-size: 12px;
            opacity: .9;
        }

        .progress {
            height: 6px;
            background: rgba(255, 255, 255, .12);
            border-radius: 999px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress>div {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent), var(--accent-strong));
            transition: width .25s ease;
        }

        .icon-pill {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: radial-gradient(90px 90px at 10% 10%, rgba(34, 211, 238, .25), transparent), #0b1220;
            border: 1px solid rgba(255, 255, 255, .06);
        }

        @media (prefers-reduced-motion: reduce) {

            .btn,
            .progress>div {
                transition: none !important;
            }
        }
    </style>

    <!-- Overlay -->
    <div id="loadingOverlay" aria-hidden="true">
        <div class="loader-wrap">
            <div class="loading-card" role="status" aria-live="polite">
                <div class="spin" aria-hidden="true"></div>
                <div class="loader-text">Memuat data…</div>
                <div class="progress" aria-label="Progress">
                    <div id="progressBar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                </div>
                <small id="progressText" class="d-block mt-1" style="color:var(--accent-strong);">0%</small>
            </div>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize font-weight-bold">Sisa Kebutuhan Area</p>
                            <h5 class="font-weight-bolder mb-0">Data Sisa Kebutuhan Area</h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <!-- No Model -->
                        <div class="col-md-3">
                            <label for="filter_model" class="form-label">No Model</label>
                            <input type="text" id="filter_model" name="filter_model" class="form-control" placeholder="No Model" value="<?= esc($noModel ?? '') ?>" required>
                        </div>
                        <!-- Area -->
                        <div class="col-md-3">
                            <label for="filter_area" class="form-label">Area</label>
                            <select class="form-control" name="filter_area" id="filter_area" required>
                                <option value="">Pilih Area</option>
                                <?php if (!empty($area)) : ?>
                                    <option value="<?= esc($area) ?>" selected><?= esc($area) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Tombol Filter -->
                        <div class="col-md-2">
                            <label class="form-label d-block invisible">Filter</label>
                            <button id="filterButton" type="button" class="btn bg-gradient-info w-100">
                                <i class="fas fa-filter"></i> FILTER
                            </button>
                        </div>

                        <!-- Tombol Refresh -->
                        <div class="col-md-1">
                            <label class="form-label d-block invisible">Refresh</label>
                            <button type="button" class="btn btn-secondary w-100" onclick="window.location.href='<?= base_url($role . '/pemesanan/sisaKebutuhanArea') ?>'">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>

                        <!-- Export -->
                        <div class="col-md-3 text-end">
                            <label class="form-label d-block invisible">Export</label>
                            <button type="button" class="btn btn-success w-100" id="exportExcelBtn">
                                <i class="fas fa-file-excel"></i> EXPORT EXCEL
                            </button>
                        </div>
                    </div>

                    <!-- Tabel -->
                    <div class="table-responsive mt-4">
                        <table class="table align-items-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TANGGAL RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">NO MODEL</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOS</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">ITEM TYPE</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KODE WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">WARNA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">TOTAL KEBUTUHAN AREA</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY PESAN (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY PESAN (CNS)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">PO TAMBAHAN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY KIRIM (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY KIRIM (CNS)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT PAKAI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY RETUR (KG)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">QTY RETUR (CNS)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">LOT RETUR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">KET GBN</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center">SISA (KEBUTUHAN - KIRIM + RETUR)</th>
                                </tr>
                            </thead>
                            <tbody id="sisaKebutuhanTable">
                                <?= view($role . '/pemesanan/partials/_sisa_kebutuhan_rows', [
                                    'dataPemesanan' => $dataPemesanan ?? [],
                                    'area'          => $area ?? '',
                                    'noModel'       => $noModel ?? ''
                                ]) ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modelInput = document.getElementById('filter_model');
    const areaSelect = document.getElementById('filter_area');
    const rolePrefix = '<?= $role ?>';

    // ====== Placeholder yang harus diabaikan ======
    const PLACEHOLDERS = new Set(['belum ada area', 'tidak ada area', 'no area', '-', '']);

    // ====== Helper parsing dari API ======
    function parseAreasPayload(payload) {
        if (payload && typeof payload === 'object' && !Array.isArray(payload)) {
            if (payload.ok && Array.isArray(payload.areas)) return payload.areas.map(String);
        }
        if (Array.isArray(payload)) {
            return payload.map(r => (r && typeof r === 'object' && 'area' in r) ? String(r.area) : '').filter(Boolean);
        }
        return [];
    }

    // ====== State loader ======
    function setAreaLoading() {
        areaSelect.innerHTML = '<option value="">Memuat area…</option>';
        areaSelect.disabled = true;
    }

    function setAreaEmpty() {
        areaSelect.innerHTML = '<option value="">(Tidak ada area untuk model ini)</option>';
        areaSelect.disabled = true;
    }

    // ====== Set dropdown area ======
    function setAreaOptions(areas, keepValue = null) {
        const uniq = [];
        const seen = new Set();
        let placeholder = null;

        for (const a of areas.map(String)) {
            const val = a.trim();
            if (!val) continue;
            if (PLACEHOLDERS.has(val.toLowerCase())) {
                placeholder = val;
                continue;
            }
            if (seen.has(val)) continue;
            seen.add(val);
            uniq.push(val);
        }

        let html = '<option value="">Pilih Area</option>';
        if (placeholder) html += `<option value="" disabled>${placeholder}</option>`;
        uniq.forEach(v => {
            html += `<option value="${v}">${v}</option>`;
        });

        areaSelect.innerHTML = html;
        areaSelect.disabled = uniq.length === 0;

        if (keepValue && uniq.includes(keepValue)) {
            areaSelect.value = keepValue;
        }
    }

    // ====== Ambil data area dari API & controller ======
    async function fetchAreasNow(keepSelected = false) {
        const noModel = modelInput.value.trim();
        if (!noModel) {
            setAreaEmpty();
            return;
        }

        try {
            setAreaLoading();

            const urlAreaByPemesanan = "<?= base_url($role . '/pemesanan/getFilterByPemesanan?no_model=') ?>" + encodeURIComponent(noModel);

            const [resApi, resPemesanan] = await Promise.all([
                fetch(`http://172.23.44.14/CapacityApps/public/api/getFilterArea?no_model=${encodeURIComponent(noModel)}`),
                fetch(urlAreaByPemesanan, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
            ]);

            const payloadApi = await resApi.json();
            const payloadPemesanan = await resPemesanan.json();

            const areasFromApi = parseAreasPayload(payloadApi);
            const areasFromPemesanan = payloadPemesanan.map(a => a.admin);
            const allAreas = [...new Set([...areasFromApi, ...areasFromPemesanan])];

            const currentValue = keepSelected ? areaSelect.value : null;
            setAreaOptions(allAreas, currentValue);

            if (!keepSelected) {
                areaSelect.value = '';
            }

        } catch (e) {
            console.error(e);
            setAreaEmpty();
        }
    }

    // ====== Utility debounce ======
    function debounce(fn, wait) {
        let t;
        return (...a) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...a), wait);
        };
    }

    const fetchAreas = debounce(() => fetchAreasNow(false), 300);

    // ====== Event: saat no model berubah ======
    modelInput.addEventListener('input', fetchAreas);
    modelInput.addEventListener('change', fetchAreas);

    // ====== Saat halaman pertama kali dibuka ======
    document.addEventListener('DOMContentLoaded', async () => {
        const params       = new URLSearchParams(window.location.search);
        const modelFromUrl = params.get('filter_model');
        const areaFromUrl  = params.get('filter_area');

        if (modelFromUrl && !modelInput.value) {
            modelInput.value = modelFromUrl;
        }

        if (modelFromUrl) {
            // isi dropdown area dulu
            await fetchAreasNow(true);

            if (areaFromUrl) {
                const exists = [...areaSelect.options].some(opt => opt.value === areaFromUrl);
                if (!exists) {
                    const opt = new Option(areaFromUrl, areaFromUrl);
                    areaSelect.add(opt);
                }
                areaSelect.value = areaFromUrl;
                console.log(`✅ Area di-set otomatis dari URL: ${areaFromUrl}`);
            }

            // kalau model & area ada, langsung load tabel via AJAX
            if (modelFromUrl && areaFromUrl) {
                fetchTableData(modelFromUrl, areaFromUrl, false); // false = jangan pushState lagi
            }
        }
    });

    window.addEventListener('popstate', async (event) => {
        const state = event.state;

        if (state && (state.filter_model || state.filter_area)) {
            modelInput.value = state.filter_model || '';
            await fetchAreasNow(true); // refresh dropdown area

            if (state.filter_area) {
                const exists = [...areaSelect.options].some(opt => opt.value === state.filter_area);
                if (!exists) {
                    areaSelect.add(new Option(state.filter_area, state.filter_area));
                }
                areaSelect.value = state.filter_area;
            } else {
                areaSelect.value = '';
            }

            if (state.filter_model && state.filter_area) {
                fetchTableData(state.filter_model, state.filter_area, false);
            } else {
                // kalau salah satu kosong, kosongkan tabel
                tableBody.innerHTML = '';
            }
        }
    });

    // ====== Loader helper ======
    function showLoading() {
        document.getElementById('loadingOverlay').classList.add('active');
        const btn = document.getElementById('filterButton');
        if (btn) btn.disabled = true;
        try {
            dataTable.processing(true);
        } catch (e) {}
    }

    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
        const btn = document.getElementById('filterButton');
        if (btn) btn.disabled = false;
        try {
            dataTable.processing(false);
        } catch (e) {}
    }

    function updateProgress(p) {
        const bar = document.getElementById('progressBar');
        const txt = document.getElementById('progressText');
        if (bar) {
            bar.style.width = p + '%';
            bar.setAttribute('aria-valuenow', p);
        }
        if (txt) {
            txt.textContent = p + '%';
        }
    }

    // ====== Tombol Filter ======
    document.getElementById('filterButton').addEventListener('click', function () {
        const filterArea  = areaSelect.value.trim();
        const filterModel = modelInput.value.trim();

        if (!filterArea || !filterModel) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Area dan No Model harus diisi!',
                confirmButtonText: 'OK'
            });
            return;
        }

        fetchTableData(filterModel, filterArea, true);
    });

    // ====== Tombol Export ======
    document.getElementById('exportExcelBtn')?.addEventListener('click', () => {
        const nm = modelInput.value.trim();
        const ar = areaSelect.value.trim();

        if (!nm || !ar) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap',
                text: 'Area dan No Model harus diisi sebelum export!',
            });
            return;
        }

        const u = `<?= base_url($role . '/pemesanan/reportSisaKebutuhanArea') ?>?filter_model=${encodeURIComponent(nm)}&filter_area=${encodeURIComponent(ar)}`;
        window.location.href = u;
    });


    const tableBody = document.getElementById('sisaKebutuhanTable');
    const baseListUrl = '<?= base_url($role . '/pemesanan/sisaKebutuhanArea') ?>';
    const ajaxDataUrl = '<?= base_url($role . '/pemesanan/sisaKebutuhanAreaData') ?>';

    async function fetchTableData(filterModel, filterArea, pushState = true) {
        showLoading();
        updateProgress(25);

        const params = new URLSearchParams({
            filter_model: filterModel,
            filter_area: filterArea
        });

        try {
            const res = await fetch(`${ajaxDataUrl}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();

            tableBody.innerHTML = json.html || '';

            updateProgress(80);

            // update URL tanpa reload (SPA feel)
            if (pushState) {
                window.history.pushState(
                    { filter_model: filterModel, filter_area: filterArea },
                    '',
                    `${baseListUrl}?${params.toString()}`
                );
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal memuat data',
                text: 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.'
            });
        } finally {
            updateProgress(100);
            setTimeout(() => {
                hideLoading();
                updateProgress(0);
            }, 300);
        }
    }

</script>


<?php $this->endSection(); ?>